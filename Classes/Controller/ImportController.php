<?php
namespace RKW\RkwResourcespace\Controller;

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

use RKW\RkwResourcespace\Api\ResourceSpace;
use RKW\RkwResourcespace\Domain\Model\Import;
use RKW\RkwResourcespace\Domain\Repository\BackendUserRepository;
use RKW\RkwResourcespace\Domain\Repository\FileMetadataRepository;
use RKW\RkwResourcespace\Domain\Repository\FileRepository;
use RKW\RkwResourcespace\Domain\Repository\ImportRepository;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class ImportController
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_Resourcespace
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ImportController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var \RKW\RkwResourcespace\Domain\Repository\ImportRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected ?ImportRepository $importRepository = null;


    /**
     * @var \RKW\RkwResourcespace\Domain\Repository\BackendUserRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected ?BackendUserRepository $backendUserRepository = null;

    /**
     * @var \TYPO3\CMS\Core\Log\Logger|null
     */
    protected ?Logger $logger = null;


    /**
     * @param \RKW\RkwResourcespace\Domain\Repository\ImportRepository $importRepository
     */
    public function injectImportRepository(ImportRepository $importRepository)
    {
        $this->importRepository = $importRepository;
    }


    /**
     * @param \RKW\RkwResourcespace\Domain\Repository\BackendUserRepository $backendUserRepository
     */
    public function injectBackendUserRepository(BackendUserRepository $backendUserRepository)
    {
        $this->backendUserRepository = $backendUserRepository;
    }


    /**
     * action new
     * Link example:
     * ###baseUrl###/index.php?id=###pidOfPlugin###&tx_rkwresourcespace_import[action]=new&tx_rkwresourcespace_import[controller]=Import&tx_rkwresourcespace_import[resourceSpaceImageId]=###resourceSpaceImageId###
     *
     * @param int $resourceSpaceImageId
     * @param int $resourceSpaceUserId
     * @param string $resourceSpaceUserName
     * @param string $resourceSpaceUserRealName
     * @param int $returnMessageCode
     * @return void
     * @throws StopActionException
     */
    public function newAction(
        int $resourceSpaceImageId = 0,
        int $resourceSpaceUserId = 0,
        string $resourceSpaceUserName = '',
        string $resourceSpaceUserRealName = '',
        int $returnMessageCode = 0
    ): void {

        if (
            $resourceSpaceImageId
            && !$returnMessageCode
        ) {
            /** @var \RKW\RkwResourcespace\Domain\Model\Import $import */
            $newImport = $this->objectManager->get(\RKW\RkwResourcespace\Domain\Model\Import::class);
            $newImport->setResourceSpaceImageId($resourceSpaceImageId);
            $newImport->setResourceSpaceUserId($resourceSpaceUserId);
            $newImport->setResourceSpaceUserName(filter_var($resourceSpaceUserName, FILTER_SANITIZE_STRING));
            $newImport->setResourceSpaceUserRealName(filter_var($resourceSpaceUserRealName, FILTER_SANITIZE_STRING));

            $this->forward('create', null, null, array('newImport' => $newImport));
            //===
        }

        // for metadata update link
        $this->view->assign('resourceSpaceImageId', $resourceSpaceImageId);
        $this->view->assign('returnMessageCode', $returnMessageCode);

        // else: show view with upload form (if allowed by settings.enableFormUpload)

    }


    /**
     * action create
     *
     * @param \RKW\RkwResourcespace\Domain\Model\Import $newImport
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Core\Resource\Exception\ExistingTargetFileNameException
     */
    public function createAction(Import $newImport): void
    {
        $this->checkIpIfAccessIsRestricted();

        $returnMessageCode = 0;

        /** @var \RKW\RkwResourcespace\Api\ResourceSpace $resourceSpaceApi */
        $resourceSpaceApi = $this->objectManager->get(ResourceSpace::class);

        // get resource data (like name, file extension etc)
        $resourceData = $resourceSpaceApi->getResourceData($newImport->getResourceSpaceImageId());

        // get resource path (url of image)
        $resourcePathOfImage = $resourceSpaceApi->getResourcePath($newImport->getResourceSpaceImageId(), $resourceData->file_extension);

        // get resource metadata
        $resourceMetaData = $resourceSpaceApi->getResourceFieldData($newImport->getResourceSpaceImageId());

        /** @var \RKW\RkwResourcespace\Utility\FileUtility $fileUtility */
        $fileUtility = $this->objectManager->get(\RKW\RkwResourcespace\Utility\FileUtility::class);

        // Workaround: Are these following lines correctly?
        // Problem: Even not existing images will produce an $resourcePathOfImage and $resourceMetaData
        // -> But $resourceData seems correctly to be "false", if there is no image available
        if ($resourceData->file_checksum) {
            $requestMessageArray = $fileUtility->createFile($resourcePathOfImage, $resourceData, $resourceMetaData, $newImport);
            $requestMessage = $requestMessageArray['message'];
            $returnMessageCode = $requestMessageArray['code'];
        } else {
            $requestMessage = LocalizationUtility::translate('tx_rkwresourcespace_controller_import.noImageFound', 'rkw_resourcespace');
        }

        $this->addFlashMessage($requestMessage);

        // log in db, if enabled
        if ($this->settings['logActivitiesInDb']) {
            // only log something if we have a relation to a file
            if ($newImport->getFile()) {
                // set BackendUser (only if logged in)
                if (is_object($GLOBALS['BE_USER']) && !empty($GLOBALS['BE_USER']->user['uid'])) {
                    /** @var \RKW\RkwResourcespace\Domain\Model\BackendUser $backendUser */
                    $backendUser = $this->backendUserRepository->findByIdentifier($GLOBALS['BE_USER']->user['uid']);
                    if ($backendUser) {
                        $newImport->setBackendUser($backendUser);
                    }
                }
                // add to repo
                $this->importRepository->add($newImport);
            }
        }

        $this->forward(
            'new',
            null,
            null,
            [
                'returnMessageCode' => $returnMessageCode,
                'resourceSpaceImageId' => $newImport->getResourceSpaceImageId(),
            ]
        );
    }


    /**
     * action overrideMetadata
     *
     * @param int $resourceSpaceImageId
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Core\Resource\Exception\ExistingTargetFileNameException
     */
    protected function overrideMetadataAction(int $resourceSpaceImageId): void
    {
        $this->checkIpIfAccessIsRestricted();

        // 1. Get data from api
        /** @var \RKW\RkwResourcespace\Api\ResourceSpace $resourceSpaceApi */
        $resourceSpaceApi = $this->objectManager->get(ResourceSpace::class);

        // get resource data (like name, file extension etc)
        $resourceData = $resourceSpaceApi->getResourceData($resourceSpaceImageId);

        // get resource metadata
        $resourceMetaData = $resourceSpaceApi->getResourceFieldData($resourceSpaceImageId);


        // 2. Initialize repos and grab file from TYPO3 DB
        /** @var \RKW\RkwResourcespace\Domain\Repository\FileRepository $fileRepository */
        $fileRepository = $this->objectManager->get(FileRepository::class);

        /** @var \RKW\RkwResourcespace\Domain\Repository\FileMetadataRepository $fileMetadataRepository */
        $fileMetadataRepository = $this->objectManager->get(FileMetadataRepository::class);

        /** @var \RKW\RkwResourcespace\Domain\Model\File $fileFromDb */
        $fileFromDb = $fileRepository->findByBeginningOfName($resourceData->ref)->getFirst();


        if (
            $fileFromDb
            && strpos($fileFromDb->getIdentifier(), $this->settings['uploadDestination']) === 0
        ) {

            // 3. Do update

            // convert to type ResourceSpace->metadata
            /** @var \RKW\RkwResourcespace\Domain\Model\FileMetadata $fileMetadata */
            $fileMetadata = $fileMetadataRepository->findByUid($fileFromDb->getMetadata()->getUid());

            /** @var \RKW\RkwResourcespace\Utility\FileUtility $fileUtility */
            $fileUtility = $this->objectManager->get(\RKW\RkwResourcespace\Utility\FileUtility::class);

            // override
            $fileUtility->setFileMetadata($fileMetadata, $resourceMetaData);

            // finally: Update
            $fileMetadataRepository->update($fileMetadata);

            $this->addFlashMessage(
                LocalizationUtility::translate(
                    'tx_rkwresourcespace_controller_import.metadataOverrideSuccess',
                    'rkw_resourcespace'
                )
            );

        } else {
            $this->addFlashMessage(
                LocalizationUtility::translate(
                    'tx_rkwresourcespace_controller_import.metadataOverrideFail',
                    'rkw_resourcespace'
                ),
                '',
                AbstractMessage::ERROR
            );
        }

        $this->redirect('new', null, null, ['resourceSpaceImageId' => $resourceSpaceImageId]);

    }


    /**
     * Returns logger instance
     *
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    protected function getLogger(): Logger
    {
        if (!$this->logger instanceof \TYPO3\CMS\Core\Log\Logger) {
            $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        }

        return $this->logger;
    }

    /**
     * @return void
     * @throws StopActionException
     */
    protected function checkIpIfAccessIsRestricted(): void
    {
// check ip, if access is restricted
        if ($this->settings['ipRestriction']) {
            $remoteAddr = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
            if ($_SERVER['HTTP_X_FORWARDED_FOR']) {
                $ips = GeneralUtility::trimExplode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                if ($ips[0]) {
                    $remoteAddr = filter_var($ips[0], FILTER_VALIDATE_IP);
                }
            }

            $allowedIps = GeneralUtility::trimExplode(',', $this->settings['ipRestriction']);
            if (!in_array($remoteAddr, $allowedIps)) {
                $this->getLogger()->log(
                    \TYPO3\CMS\Core\Log\LogLevel::WARNING,
                    sprintf('Access forbidden: Mismatching IP: %s', $remoteAddr)
                );
                $this->addFlashMessage(LocalizationUtility::translate('tx_rkwresourcespace_controller_import.invalidIp', 'rkw_resourcespace'));
                $this->forward('new');
                //===
            }
        }
    }
}
