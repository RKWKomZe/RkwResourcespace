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

/**
 * Class ImportController
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_Resourcespace
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ImportController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * importRepository
     *
     * @var \RKW\RkwResourcespace\Domain\Repository\ImportRepository
     * @inject
     */
    protected $importRepository = null;

    /**
     * backendUserRepository
     *
     * @var \RKW\RkwResourcespace\Domain\Repository\BackendUserRepository
     * @inject
     */
    protected $backendUserRepository = null;

    /**
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    protected $logger;


    /**
     * action new
     * Link example:
     * ###baseUrl###/index.php?id=###pidOfPlugin###&tx_rkwresourcespace_import[action]=new&tx_rkwresourcespace_import[controller]=Import&tx_rkwresourcespace_import[resourceSpaceImageId]=###resourceSpaceImageId###
     *
     * @param integer $resourceSpaceImageId
     * @param integer $resourceSpaceUserId
     * @param string $resourceSpaceUserName
     * @param string $resourceSpaceUserRealName
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function newAction(
        $resourceSpaceImageId = null,
        $resourceSpaceUserId = null,
        $resourceSpaceUserName = '',
        $resourceSpaceUserRealName = ''
    )
    {
        if ($resourceSpaceImageId) {
            /** @var \RKW\RkwResourcespace\Domain\Model\Import $import */
            $newImport = $this->objectManager->get('RKW\\RkwResourcespace\\Domain\\Model\\Import');
            $newImport->setResourceSpaceImageId(intval($resourceSpaceImageId));
            $newImport->setResourceSpaceUserId(intval($resourceSpaceUserId));
            $newImport->setResourceSpaceUserName(filter_var($resourceSpaceUserName, FILTER_SANITIZE_STRING));
            $newImport->setResourceSpaceUserRealName(filter_var($resourceSpaceUserRealName, FILTER_SANITIZE_STRING));

            $this->forward('create', null, null, array('newImport' => $newImport));
            //===
        }

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
    public function createAction(\RKW\RkwResourcespace\Domain\Model\Import $newImport)
    {
        // check ip, if access is restricted
        if ($this->settings['ipRestriction']) {
            $remoteAddr = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
            if ($_SERVER['HTTP_X_FORWARDED_FOR']) {
                $ips = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                if ($ips[0]) {
                    $remoteAddr = filter_var($ips[0], FILTER_VALIDATE_IP);
                }
            }
            $allowedIps = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->settings['ipRestriction']);
            if (!in_array($remoteAddr, $allowedIps)) {
                $this->getLogger()->log(
                    \TYPO3\CMS\Core\Log\LogLevel::WARNING,
                    sprintf('Access forbidden: Mismatching IP: %s', $remoteAddr)
                );
                $this->addFlashMessage(\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_rkwresourcespace_controller_import.invalidIp', 'rkw_resourcespace'));
                $this->forward('new');
                //===
            }
        }

        /** @var \RKW\RkwResourcespace\Api\ResourceSpace $resourceSpaceApi */
        $resourceSpaceApi = $this->objectManager->get('RKW\\RkwResourcespace\\Api\\ResourceSpace');
        // get resource path (url of image)
        $resourcePathOfImage = $resourceSpaceApi->getResourcePath($newImport->getResourceSpaceImageId());
        // get resource data (like name, file extension etc)
        $resourceData = $resourceSpaceApi->getResourceData($newImport->getResourceSpaceImageId());
        // get resource metadata
        $resourceMetaData = $resourceSpaceApi->getResourceFieldData($newImport->getResourceSpaceImageId());

        /** @var \RKW\RkwResourcespace\Helper\File $fileHelper */
        $fileHelper = $this->objectManager->get('RKW\\RkwResourcespace\\Helper\\File');

        // Workaround: Are these following lines correctly?
        // Problem: Even not existing images will produce an $resourcePathOfImage and $resourceMetaData
        // -> But $resourceData seems correctly to be "false", if there is no image available
        if ($resourceData->file_checksum) {
            $requestMessage = $fileHelper->createFile($resourcePathOfImage, $resourceData, $resourceMetaData, $newImport);
        } else {
            $requestMessage = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_rkwresourcespace_controller_import.noImageFound', 'rkw_resourcespace');
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
        $this->forward('new');
        //===
    }


    /**
     * Returns logger instance
     *
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    protected function getLogger()
    {
        if (!$this->logger instanceof \TYPO3\CMS\Core\Log\Logger) {
            $this->logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Log\\LogManager')->getLogger(__CLASS__);
        }

        return $this->logger;
        //===
    }
}
