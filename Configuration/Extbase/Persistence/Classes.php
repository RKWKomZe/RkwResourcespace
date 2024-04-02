<?php
declare(strict_types = 1);

return [
    \RKW\RkwResourcespace\Domain\Model\File::class => [
        'tableName' => 'sys_file',
        'identifier' => 'identifier'
    ],
    \RKW\RkwResourcespace\Domain\Model\FileReference::class => [
        'tableName' => 'sys_file_reference',
        'properties' => [
            'file' => [
                'fieldName' => 'uid_local'
            ],
        ],
    ],
    \RKW\RkwResourcespace\Domain\Model\FileMetadata::class => [
        'tableName' => 'sys_file_metadata',
        'identifier' => 'identifier',
        'properties' => [
            'file' => [
                'fieldName' => 'file'
            ],
            'keywords' => [
                'fieldName' => 'keywords'
            ],
        ],
    ],
    \RKW\RkwResourcespace\Domain\Model\MediaSources::class => [
        'tableName' => 'tx_coreextended_domain_model_mediasources',
    ],
    \RKW\RkwResourcespace\Domain\Model\BackendUser::class => [
        'tableName' => 'be_users',
        'properties' => [
            'allowedLanguages' => [
                'fieldName' => 'allowed_languages'
            ],
            'fileMountPoints' => [
                'fieldName' => 'file_mountpoints'
            ],
            'dbMountPoints' => [
                'fieldName' => 'db_mountpoints'
            ],
            'backendUserGroups' => [
                'fieldName' => 'usergroup'
            ],
        ],
    ],
];
