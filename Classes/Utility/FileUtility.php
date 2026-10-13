<?php
namespace RKW\RkwResourcespace\Utility;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Madj2k\CoreExtended\Utility\GeneralUtility;
use Madj2k\DrSerp\Utility\SlugUtility;
use Psr\Log\LogLevel as LogLevelAlias;
use RKW\RkwResourcespace\Domain\Model\File;
use RKW\RkwResourcespace\Domain\Model\FileMetadata;
use RKW\RkwResourcespace\Domain\Model\FileReference;
use RKW\RkwResourcespace\Domain\Model\Import;
use RKW\RkwResourcespace\Domain\Model\MediaSource;
use RKW\RkwResourcespace\Domain\Repository\FileRepository;
use RKW\RkwResourcespace\Domain\Repository\FileMetadataRepository;
use RKW\RkwResourcespace\Domain\Repository\MediaSourceRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Resource\Exception\InsufficientFolderAccessPermissionsException;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\Utility\CommandUtility;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Generic\Exception;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class File
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_Resourcespace
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class FileUtility implements SingletonInterface
{
    /**
     * @var string
     */
    protected string $fileName = '';

    /**
     * @var string
     */
    protected string $tempName = '';

    /**
     * @var string
     */
    protected string $newTempName = '';

    /**
     * @var array
     */
    protected array $settingsDefault = [];

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager|null
     */
    protected ?ObjectManager $objectManager = null;

    /**
     * @var Logger|null
     */
    protected ?Logger $logger = null;


    /**
     * initializeObject
     *
     * @return void
     * @throws InvalidConfigurationTypeException
     */
    public function initializeObject(): void
    {
        $this->settingsDefault = $this->getSettings();
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        // login header for etracker
        $opts = array(
            'http' => array(
                'method' => 'GET',
            ),
        );

        // optional: proxy configuration
        if ($this->settingsDefault['resourceSpaceApi']['proxy']) {

            $optsProxy = array(
                'http' => array(
                    'proxy'           => $this->settingsDefault['resourceSpaceApi']['proxy'],
                    'request_fulluri' => true,
                ),
            );

            if ($this->settingsDefault['resourceSpaceApi']['proxyUsername']) {
                $auth = base64_encode(
                    $this->settingsDefault['resourceSpaceApi']['proxyUsername'] . ':' .
                    $this->settingsDefault['resourceSpaceApi']['proxyPassword']
                );
                $optsProxy['http']['header'] = 'Proxy-Authorization: Basic ' . $auth;
            }
            $opts = array_merge_recursive($opts, $optsProxy);
        }

        // we need to set the default context here,
        // because get_headers() is not working with a stream-context as parameter
        stream_context_set_default($opts);
    }


    /**
     * createFile
     *
     * @param string $imageUrl
     * @param \StdClass $resourceData
     * @param array $resourceMetaData
     * @param Import $import
     * @param string $fieldName
     * @return array
     * @throws \TYPO3\CMS\Core\Resource\Exception\ExistingTargetFileNameException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @throws Exception
     * @throws InsufficientFolderAccessPermissionsException
     */
    public function createFile(
        string $imageUrl,
        \StdClass $resourceData,
        array $resourceMetaData,
        Import  $import,
        string $fieldName = 'file'
    ): array {

        // check for disallowed file extensions
        if ($this->checkForDisallowedFileExtension($imageUrl)) {
            // Log
            $this->getLogger()->log(
                LogLevelAlias::INFO,
                sprintf('File %s has an disallowed file extension:', $imageUrl)
            );

            // return message
            // 500 - disallowed file extension
            return [
                'code' => 500,
                'message' => LocalizationUtility::translate(
                    'tx_rkwresourcespace_helper_file.forbiddenFileExtension',
                    'rkw_resourcespace',
                    [strtoupper($this->settingsDefault['upload']['disallowedFileExtension'])])
            ];

        }

        // save image to the system (use file_checksum as temp file name)
        // first: Check if URL can deliver something
        $headers = get_headers($imageUrl);
        $finalImageUrl = null;
        if (strpos($headers[0], '200 OK')) {
            $finalImageUrl = $imageUrl;
        } else {

            // this is to handle a bug in ResourceSpace:
            // .jpg is returned as an extension even if the file is stored as .jpeg
            if (strpos($imageUrl, '.jpg')) {

                $imageUrl = str_replace('.jpg', '.jpeg', $imageUrl);
                if (
                    ($headers = get_headers($imageUrl))
                    && strpos($headers[0], '200 OK')
                ) {
                    $finalImageUrl = $imageUrl;
                }
            }
        }

        if (!$finalImageUrl) {
            $this->getLogger()->log(
                LogLevelAlias::ERROR,
                sprintf('Cannot copy the file %s. Delivered Header: %s', $imageUrl, $headers[0])
            );

            // return message
            // 501 - file copy failed
            return [
                'code' => 501,
                'message' => LocalizationUtility::translate(
                    'tx_rkwresourcespace_helper_file.fileCopyFailed',
                    'rkw_resourcespace'
                )
            ];
        }

        // copy image
        try {
            copy($finalImageUrl, $this->settingsDefault['localBufferDestination'] . $resourceData->file_checksum);
        } catch (\Exception $e) {
            $this->getLogger()->log(
                LogLevelAlias::ERROR,
                sprintf('Error while try to copy the image: %s', $e->getMessage())
            );
        }

        /** @var ResourceStorage $storage */
        $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
        $storage = $resourceFactory->getStorageObject($this->settingsDefault['upload']['sysFileStorageUid']);

        if ($storage) {

            /** @var FileRepository $fileRepository */
            $fileRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(FileRepository::class);

            /** @var FileMetadataRepository $fileMetadataRepository */
            $fileMetadataRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(FileMetadataRepository::class);

            // a) create temp- & fileName
            $this->createTempAndFileName($resourceData);

            // b) Check if file(-name) already exists!
            // important: use the sanitizeFileName method to get a comparable name (with converted äüöß e.g.)
            $sanitizedFileName = $storage->sanitizeFileName($this->fileName);

            /** @var File $fileFromDb */
            //$fileFromDb = $fileRepository->findByName($sanitizedFileName)->getFirst();
            $fileFromDb = $fileRepository->findByBeginningOfName($resourceData->ref)->getFirst();

            // check if a file exists AND if the file path is the same, we have defined in TS!
            if (
                $fileFromDb
                && str_starts_with($fileFromDb->getIdentifier(), $this->settingsDefault['uploadDestination'])
            ) {
                // Log
                $this->getLogger()->log(
                    LogLevelAlias::INFO,
                    sprintf('Resource %s already exists.', $fileFromDb->getIdentifier())
                );

                // return message
                // 300 - file already exists
                return [
                    'code' => 300,
                    'message' => LocalizationUtility::translate(
                        'tx_rkwresourcespace_helper_file.fileAlreadyExists',
                        'rkw_resourcespace'
                    )
                ];

            }

            // c) create file
            // process image (if enabled)
            if ($this->settingsDefault['upload']['processing']) {
                // resize image if maxWidth is defined
                try {
                    $imageSize = getimagesize($this->tempName);

                    if (
                        $this->settingsDefault['upload']['processing']['maxWidth']
                        && $imageSize[0] > $this->settingsDefault['upload']['processing']['maxWidth']
                    ) {
                        // resize image
                        $newTmpName = $this->resizeImage();
                        if ($newTmpName) {
                            $this->tempName = $newTmpName;
                        }
                    }
                } catch (\Exception $e) {
                    $this->getLogger()->log(
                        LogLevelAlias::ERROR,
                        sprintf('Error while try to get the image size: %s', $e->getMessage())
                    );

                    // return message
                    // 503 - error image size
                    return [
                        'code' => 503,
                        'message' => LocalizationUtility::translate(
                            'tx_rkwresourcespace_helper_file.errorImageSize',
                            'rkw_resourcespace'
                        )
                    ];

                }
            }

           // try {
                /** @var \TYPO3\CMS\Core\Resource\File $newFileObject */
                $newFileObject = $storage->addFile(
                    $this->tempName,
                    $storage->getFolder($this->settingsDefault['uploadDestination']),
                    $this->fileName
                );

                /** @var File $newFile */
                // Important: Get Extbase Model instead of \TYPO3\CMS\Core\Resource\File
                $newFile = $fileRepository->findByUid($newFileObject->getProperty('uid'));

                // d) fetch & fill metadata
                /** @var FileMetadata $fileMetadata */
                $fileMetadata = $fileMetadataRepository->findByFile($newFile)->getFirst();
                $fileMetadata->setFile($newFile);
                $this->setFileMetadata($fileMetadata, $resourceMetaData);
                $fileMetadataRepository->update($fileMetadata);

                // Mark new imports immediately so the recurring metadata command
                // can pick them up without relying on the one-time upgrade wizard.
                $this->markAsResourceSpaceImport(
                    (int)$newFileObject->getProperty('uid'),
                    (int)$resourceData->ref
                );

                // e) Optional: Create fileReference (Add a file to Import-Object
                // (will be saved in controller, if db-logging is enabled))
                // -> Otherwise we don't need a reference yet (we're just adding the image to the typo3 system)
                /** @var FileReference $newFileReference */
                $newFileReference = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(FileReference::class);
                $dataMapper = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(DataMapper::class);
                $newFileReference->setFile($newFile);
                $newFileReference->setFieldname($fieldName);
                $newFileReference->setTableLocal(
                    trim(strip_tags($dataMapper->getDataMap(get_class($newFile))->getTableName()))
                );
                $newFileReference->setTablenames(
                    trim(strip_tags(($dataMapper->getDataMap(get_class($import))->getTableName()))
                ));
                $import->setFile($newFileReference);

                // return message
                // 200 - success: File created
                return [
                    'code' => 200,
                    'message' => LocalizationUtility::translate(
                        'tx_rkwresourcespace_helper_file.fileCreated',
                        'rkw_resourcespace'
                    )
                ];
        } else {
            $this->getLogger()->log(
                LogLevelAlias::ERROR,
                'SysFileStorage not found or is misconfigured by typoscript. Please define a correct storage uid for file uploads.'
            );

            // return message
            // 504 - misconfiguration
            return [
                'code' => 504,
                'message' => LocalizationUtility::translate(
                    'tx_rkwresourcespace_helper_file.errorMisconfiguration',
                    'rkw_resourcespace'
                )
            ];
        }
    }


    /**
     * createTempAndFileName
     *
     * @param \StdClass $resourceData
     * @return void
     */
    protected function createTempAndFileName(\StdClass $resourceData): void
    {
        $this->tempName = $this->settingsDefault['localBufferDestination'] . $resourceData->file_checksum;
        // further tempName for optional file processing
        $this->newTempName = $this->tempName . '_new';
        // if enabled and set: Use in TS-defined image format.
        // Else: Use the image format which is delivered by ResourceSpace
        if (
            $this->settingsDefault['upload']['processing']
            && $this->settingsDefault['upload']['processing']['forceFormat']
        ) {
            $fileExtension = $this->settingsDefault['upload']['processing']['forceFormat'];
        } else {
            $fileExtension = $resourceData->file_extension;
        }
        $this->fileName = $resourceData->ref . "_" .
            str_replace(
            ' ',
            '_',
            $this->handleUmlauts(strtolower($resourceData->field8))
            ) . '.' . $fileExtension;
    }


    /**
     * setFileMetadata
     * this function is filtering metadata from the resourceSpace-api-request
     *
     * @param FileMetadata $newFileMetadata
     * @param array $resourceMetaData
     * @return void
     * @throws IllegalObjectTypeException
     */
    public function setFileMetadata(FileMetadata $newFileMetadata, array $resourceMetaData): void
    {
        foreach ($resourceMetaData as $metaDataEntry) {

            switch ($metaDataEntry->resource_type_field) {
                // 'source'
                case 76:
                    /** @var MediaSourceRepository $mediaSourceRepository */
                    $mediaSourceRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(MediaSourceRepository::class);
                    $mediaSource = $mediaSourceRepository->findOneByNameLike($metaDataEntry->value);
                    if ($mediaSource) {
                        // use existing
                        $newFileMetadata->setTxCopyrightguardianSource($mediaSource);
                    } else {
                        // create & add new
                        /** @var MediaSource $newMediaSource */
                        $newMediaSource = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(MediaSource::class);
                        $newMediaSource->setName($metaDataEntry->value);
                        $mediaSourceRepository->add($newMediaSource);
                        // set new
                        $newFileMetadata->setTxCopyrightguardianSource($newMediaSource);
                    }
                    break;
                // 'credit'
                case 10:
                    $newFileMetadata->setTxCopyrightguardianCreator(trim(strip_tags($metaDataEntry->value)));
                    break;
                // 'title'
                case 8:
                    $newFileMetadata->setTitle(trim(strip_tags($metaDataEntry->value)));
                    break;
                // 'caption'
                case 18:
                    $newFileMetadata->setCaption(trim(strip_tags($metaDataEntry->value)));
                    $newFileMetadata->setAlternative(trim(strip_tags($metaDataEntry->value)));
                    break;
                // 'keywords'
                case 1:
                    $newFileMetadata->setKeywords(trim(strip_tags($metaDataEntry->value)));
                    break;
                // date
                case 12:
                    $newFileMetadata->setContentCreationDate(strtotime($metaDataEntry->value));
                    break;
                // text
                case 72:
                    $newFileMetadata->setText(trim(strip_tags($metaDataEntry->value)));
                    break;
                // additional text
                case 25:
                    $newFileMetadata->setDescription(trim(strip_tags($metaDataEntry->value)));
                    break;
            }
        }
    }


    /**
     * Mark a newly created file as a ResourceSpace import.
     *
     * @param int $fileUid
     * @param int $resourceSpaceImageId
     * @return void
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function markAsResourceSpaceImport(int $fileUid, int $resourceSpaceImageId): void
    {
        $connection = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            ConnectionPool::class
        )->getConnectionForTable('sys_file');

        $connection->update(
            'sys_file',
            [
                'tx_rkwresourcespace_is_import' => 1,
                'tx_rkwresourcespace_tstamp_import' => time(),
                'tx_rkwresourcespace_rkwmedia_image_id' => $resourceSpaceImageId,
            ],
            ['uid' => $fileUid]
        );
    }


    /**
     * resizeImage
     * As result it's returns the path of the new tmpFile
     * Note: The new processed tmpFile will created in the system - but not saved as TYPO3 sys_file (this would be the next
     * step)!
     * Show for more imagick examples for TYPO3
     * https://hotexamples.com/examples/typo3.cms.core.utility/GeneralUtility/imageMagickCommand/php-generalutility-imagemagickcommand-method-examples.html
     *
     * @return string|null
     */
    protected function resizeImage():? string
    {
        if (file_exists($this->tempName)) {
            $parameterArray = array();
            // a) "-sample" will to a resize to width of x
            $parameterArray[] = "-sample " . $this->settingsDefault['upload']['processing']['maxWidth'];
            // b) "-resample" will adjust the dpi
            $parameterArray[] = "-density 72";
            // c) "-colorspace" will overwrite the color profile
            $parameterArray[] = "+profile '*'";
            $parameterArray[] = "-colorspace " . $GLOBALS['TYPO3_CONF_VARS']['GFX']['colorspace'];
            // d) the current and the new filename
            $parameterArray[] = $this->tempName . "[0] " . $this->newTempName;
            // do it!
            $cmd = CommandUtility::imageMagickCommand('convert', implode(' ', $parameterArray));
            CommandUtility::exec($cmd);

            $this->getLogger()->log(
                LogLevelAlias::INFO,
                sprintf('Executed ImageMagick-Commmand: %s', $cmd)
            );
        }

        if (file_exists($this->newTempName)) {
            return $this->newTempName;
        }

        $this->getLogger()->log(
            LogLevelAlias::ERROR,
            sprintf('Resizing of image failed. New temporary file %s not found (or could not be created)!', $this->newTempName)
        );

        return null;
    }


    /**
     * handleUmlauts
     *
     * because "iconv("UTF-8", "ASCII//TRANSLIT", $sanitizedFileName)" does not work (produce "?" symbols)
     *
     * @param string $str
     * @return string
     */
    protected function handleUmlauts(string $str): string
    {
        return SlugUtility::slugify($str);
    }


    /**
     * checkForDisallowedFileExtension
     *
     * if set via TS: check for disallowed file extension
     * returns TRUE, if
     *
     * @param string $fileName
     * @return bool
     */
    protected function checkForDisallowedFileExtension(string $fileName): bool
    {
        if ($this->settingsDefault['upload']['disallowedFileExtension']) {

            $fileExtensionList = GeneralUtility::trimExplode(',', $this->settingsDefault['upload']['disallowedFileExtension']);
            foreach ($fileExtensionList as $fileExtension) {
                if (str_ends_with($fileName, $fileExtension)) {
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * Returns TYPO3 settings
     *
     * @param string $which Which type of settings will be loaded
     * @return array
     * @throws InvalidConfigurationTypeException
     */
    protected static function getSettings(string $which = ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS): array
    {
        return GeneralUtility::getTypoScriptConfiguration('Rkwresourcespace', $which);
    }


    /**
     * Returns logger instance
     *
     * @return Logger
     */
    protected function getLogger(): Logger
    {
        if (!$this->logger instanceof Logger) {
            $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        }

        return $this->logger;
    }
}
