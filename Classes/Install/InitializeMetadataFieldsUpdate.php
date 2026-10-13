<?php
declare(strict_types=1);
namespace RKW\RkwResourcespace\Install;

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

use Doctrine\DBAL\Exception\InvalidFieldNameException;
use RKW\RkwResourcespace\Command\MetadataCommand;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

/**
 * Marks existing ResourceSpace files for recurring metadata updates.
 */
class InitializeMetadataFieldsUpdate implements UpgradeWizardInterface
{
    /**
     * Return the unique upgrade wizard identifier.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'rkwResourcespaceInitializeMetadataFields';
    }


    /**
     * Return the upgrade wizard title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return 'RKW ResourceSpace: Initialize metadata update fields';
    }


    /**
     * Return the upgrade wizard description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Mark existing RKW Media files and derive their ResourceSpace ID from the file name.';
    }


    /**
     * Mark existing ResourceSpace files and derive their external image ID.
     *
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executeUpdate(): bool
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('sys_file');

        // ResourceSpace imports historically used both "123_name" and "123-name".
        // MariaDB converts the leading numeric part to the corresponding image ID.
        $connection->executeStatement(
            'UPDATE sys_file
                SET tx_rkwresourcespace_is_import = 1,
                    tx_rkwresourcespace_tstamp_import = :now,
                    tx_rkwresourcespace_rkwmedia_image_id = CAST(name AS UNSIGNED)
              WHERE identifier LIKE :path
                AND name REGEXP :fileNamePattern',
            [
                'now' => time(),
                'path' => MetadataCommand::RKW_MEDIA_FILE_PATH . '%',
                'fileNamePattern' => '^[0-9]+[-_]',
            ]
        );

        return true;
    }


    /**
     * Check whether matching files still need their import fields initialized.
     *
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function updateNecessary(): bool
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_file');
        $queryBuilder->getRestrictions()->removeAll();

        try {
            $statement = $queryBuilder
                ->selectLiteral('1')
                ->from('sys_file')
                ->where(
                    $queryBuilder->expr()->like(
                        'identifier',
                        $queryBuilder->createNamedParameter(
                            MetadataCommand::RKW_MEDIA_FILE_PATH . '%',
                            Connection::PARAM_STR
                        )
                    ),
                    $queryBuilder->expr()->comparison(
                        'name',
                        'REGEXP',
                        $queryBuilder->createNamedParameter('^[0-9]+[-_]', Connection::PARAM_STR)
                    ),
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->eq(
                            'tx_rkwresourcespace_is_import',
                            $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)
                        ),
                        $queryBuilder->expr()->eq(
                            'tx_rkwresourcespace_tstamp_import',
                            $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)
                        )
                    )
                )
                ->setMaxResults(1)
                ->execute();

            return (bool)$statement->fetchOne();
        } catch (InvalidFieldNameException $exception) {
            return false;
        }
    }


    /**
     * Return upgrade wizards that must run before this wizard.
     *
     * @return array<int, string>
     */
    public function getPrerequisites(): array
    {
        return [];
    }
}
