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
                'Import' => 'new, create, overrideMetadata'
            ],
            // non-cacheable actions
            [
                'Import' => 'new, create, overrideMetadata'
            ]
        );

        //=================================================================
        // Add XClasses for extending existing classes
        // ATTENTION: deactivated due to faulty mapping in TYPO3 9.5
        //=================================================================
        /*
        // for TYPO3 12+
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\Madj2k\CoreExtended\Domain\Model\File::class] = [
            'className' => \RKW\RkwResourcespace\Domain\Model\File::class
        ];

        // for TYPO3 9.5 - 11.5 only, not required for TYPO3 12
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)
            ->registerImplementation(
                \Madj2k\CoreExtended\Domain\Model\File::class,
                \RKW\RkwResourcespace\Domain\Model\File::class
            );

        // for TYPO3 12+
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\Madj2k\CoreExtended\Domain\Model\FileReference::class] = [
            'className' => \RKW\RkwResourcespace\Domain\Model\FileReference::class
        ];

        // for TYPO3 9.5 - 11.5 only, not required for TYPO3 12
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)
            ->registerImplementation(
                \Madj2k\CoreExtended\Domain\Model\FileReference::class,
                \RKW\RkwResourcespace\Domain\Model\FileReference::class
            );

        // for TYPO3 12+
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\Madj2k\CoreExtended\Domain\Model\FileMetadata::class] = [
            'className' => \RKW\RkwResourcespace\Domain\Model\FileMetadata::class
        ];

        // for TYPO3 9.5 - 11.5 only, not required for TYPO3 12
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)
            ->registerImplementation(
                \Madj2k\CoreExtended\Domain\Model\FileMetadata::class,
                \RKW\RkwResourcespace\Domain\Model\FileMetadata::class
            );


        // for TYPO3 12+
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\Madj2k\CoreExtended\Domain\Model\MediaSources::class] = [
            'className' => \RKW\RkwResourcespace\Domain\Model\MediaSource::class
        ];

        // for TYPO3 9.5 - 11.5 only, not required for TYPO3 12
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)
            ->registerImplementation(
                \Madj2k\CoreExtended\Domain\Model\MediaSources::class,
                \RKW\RkwResourcespace\Domain\Model\MediaSource::class
            );

        // for TYPO3 12+
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\Madj2k\CoreExtended\Domain\Model\BackendUser::class] = [
            'className' => \RKW\RkwResourcespace\Domain\Model\BackendUser::class
        ];

        // for TYPO3 9.5 - 11.5 only, not required for TYPO3 12
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)
            ->registerImplementation(
                \Madj2k\CoreExtended\Domain\Model\BackendUser::class,
                \RKW\RkwResourcespace\Domain\Model\BackendUser::class
            );
        */

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
					'logFile' => \TYPO3\CMS\Core\Core\Environment::getVarPath()  . '/log/tx_rkwresourcespace.log'
				)
			),
		);
    },
    'rkw_resourcespace'
);
