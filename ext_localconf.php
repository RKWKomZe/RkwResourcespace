<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function($extKey)
	{

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'RKW.RkwResourcespace',
            'Import',
            [
                'Import' => 'new, create'
            ],
            // non-cacheable actions
            [
                'Import' => 'new, create'
            ]
        );

		// wizards
        /*
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
			'mod {
				wizards.newContentElement.wizardItems.plugins {
					elements {
						import {
							icon = ' . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($extKey) . 'Resources/Public/Icons/user_plugin_import.svg
							title = LLL:EXT:rkw_resourcespace/Resources/Private/Language/locallang_db.xlf:tx_rkw_resourcespace_domain_model_import
							description = LLL:EXT:rkw_resourcespace/Resources/Private/Language/locallang_db.xlf:tx_rkw_resourcespace_domain_model_import.description
							tt_content_defValues {
								CType = list
								list_type = rkwresourcespace_import
							}
						}
					}
					show = *
				}
		   }'
		);
        */

		// set logger
		$GLOBALS['TYPO3_CONF_VARS']['LOG']['RKW']['RkwResourcespace']['writerConfiguration'] = array(

			// configuration for WARNING severity, including all
			// levels with higher severity (ERROR, CRITICAL, EMERGENCY)
			\TYPO3\CMS\Core\Log\LogLevel::DEBUG => array(
				// add a FileWriter
				'TYPO3\\CMS\\Core\\Log\\Writer\\FileWriter' => array(
					// configuration for the writer
					'logFile' => 'typo3temp/logs/tx_rkwresourcespace.log'
				)
			),
		);
    },
    $_EXTKEY
);
