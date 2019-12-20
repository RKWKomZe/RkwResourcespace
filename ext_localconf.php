<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function($extKey)
	{

        //=================================================================
        // Configure Plugin
        //=================================================================
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


        //=================================================================
        // Register Logger
        //=================================================================
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
