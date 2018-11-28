<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function($extKey)
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'RKW.RkwResourcespace',
            'Import',
            'RKW ResourceSpace: Import'
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($extKey, 'Configuration/TypoScript', 'RKW ResourceSpace');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_rkwresourcespace_domain_model_import', 'EXT:rkw_resourcespace/Resources/Private/Language/locallang_csh_tx_rkwresourcespace_domain_model_import.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_rkwresourcespace_domain_model_import');

    },
    $_EXTKEY
);
