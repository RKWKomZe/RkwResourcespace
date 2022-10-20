<?php
return [
	'ctrl' => [
		'title'	=> 'LLL:EXT:rkw_resourcespace/Resources/Private/Language/locallang_db.xlf:tx_rkwresourcespace_domain_model_import',
		'label' => 'uid',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => [
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		],
		'searchFields' => 'resource_space_image_id,resource_space_user_id,resource_space_user_name,resource_space_user_real_name,file,backend_user',
		'iconfile' => 'EXT:rkw_resourcespace/Resources/Public/Icons/tx_rkwresourcespace_domain_model_import.gif'
	],
	'interface' => [
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, resource_space_image_id, resource_space_user_id, resource_space_user_name, resource_space_user_real_name, file, backend_user',
	],
	'types' => [
		'1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, resource_space_image_id, resource_space_user_id, resource_space_user_name, resource_space_user_real_name, file, backend_user, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
	],
	'columns' => [
		'sys_language_uid' => [
			'exclude' => true,
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'special' => 'languages',
				'items' => [
					[
						'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
						-1,
						'flags-multiple'
					]
				],
				'default' => 0,
			],
		],
		'l10n_parent' => [
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => true,
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['', 0],
				],
				'foreign_table' => 'tx_rkwresourcespace_domain_model_import',
				'foreign_table_where' => 'AND tx_rkwresourcespace_domain_model_import.pid=###CURRENT_PID### AND tx_rkwresourcespace_domain_model_import.sys_language_uid IN (-1,0)',
			],
		],
		'l10n_diffsource' => [
			'config' => [
				'type' => 'passthrough',
			],
		],
		'hidden' => [
			'exclude' => true,
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'LLL:EXT:lang/locallang_core.xlf:labels.enabled'
					]
				],
			],
		],
		'starttime' => [
			'exclude' => true,
			//'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
			'config' => [
                'type'    => 'input',
                'renderType' => 'inputDateTime',
                'size'    => 13,
                'eval'    => 'datetime',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
			]
		],
		'endtime' => [
			'exclude' => true,
			//'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
			'config' => [
                'type'    => 'input',
                'renderType' => 'inputDateTime',
                'size'    => 13,
                'eval'    => 'datetime',
                'default' => 0,
                'range'   => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038),
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
			],
		],
		'resource_space_image_id' => [
			'exclude' => false,
			'label' => 'LLL:EXT:rkw_resourcespace/Resources/Private/Language/locallang_db.xlf:tx_rkwresourcespace_domain_model_import.resource_space_image_id',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim, int'
			],
		],
		'resource_space_user_id' => [
			'exclude' => false,
			'label' => 'LLL:EXT:rkw_resourcespace/Resources/Private/Language/locallang_db.xlf:tx_rkwresourcespace_domain_model_import.resource_space_user_id',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim, int'
			],
		],
		'resource_space_user_name' => [
			'exclude' => false,
			'label' => 'LLL:EXT:rkw_resourcespace/Resources/Private/Language/locallang_db.xlf:tx_rkwresourcespace_domain_model_import.resource_space_user_name',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			],
		],
		'resource_space_user_real_name' => [
			'exclude' => false,
			'label' => 'LLL:EXT:rkw_resourcespace/Resources/Private/Language/locallang_db.xlf:tx_rkwresourcespace_domain_model_import.resource_space_user_real_name',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			],
		],
		'file' => [
			'exclude' => 0,
			'label' => 'LLL:EXT:rkw_resourcespace/Resources/Private/Language/locallang_db.xlf:tx_rkwresourcespace_domain_model_import.file',
			'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
				'image',
				['maxitems' => 1],
				'jpg, png, gif'
			),
		],
		'backend_user' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:rkw_resourcespace/Resources/Private/Language/locallang_db.xlf:tx_rkwresourcespace_domain_model_import.backend_user',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectMultipleSideBySide',
				'foreign_table' => 'be_users',
				'foreign_table_where' => 'AND be_users.deleted = 0 AND be_users.email != "" ORDER BY be_users.username',
				'maxitems'      => 1,
				'minitems'      => 1,
				'size'          => 1,
			),
		),
	],
];
