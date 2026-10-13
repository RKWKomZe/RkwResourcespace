<?php
namespace RKW\RkwResourcespace\Command;

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
use RKW\RkwResourcespace\Domain\Model\File;
use RKW\RkwResourcespace\Domain\Model\FileMetadata;
use RKW\RkwResourcespace\Domain\Repository\FileMetadataRepository;
use RKW\RkwResourcespace\Domain\Repository\FileRepository;
use RKW\RkwResourcespace\Utility\FileUtility;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Locking\LockFactory;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * Updates metadata of files imported from RKW Media/ResourceSpace.
 *
 * Execute on CLI with: vendor/bin/typo3 rkw_resourcespace:updateMetadata
 * Dry-run example: vendor/bin/typo3 rkw_resourcespace:updateMetadata --dry-run --limit=1 --age=0
 * Update example: vendor/bin/typo3 rkw_resourcespace:updateMetadata --limit=1 --age=0
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwResourcespace
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class MetadataCommand extends Command
{
    public const RKW_MEDIA_FILE_PATH = '/media/images/Medienpool/';

    /** @var string */
    protected static $defaultName = 'rkw_resourcespace:updateMetadata';

    /** @var ResourceSpace */
    protected ResourceSpace $resourceSpaceClient;

    /** @var FileUtility */
    protected FileUtility $fileUtility;

    /** @var FileRepository */
    protected FileRepository $fileRepository;

    /** @var FileMetadataRepository */
    protected FileMetadataRepository $fileMetadataRepository;

    /** @var PersistenceManager */
    protected PersistenceManager $persistenceManager;

    /** @var LockFactory */
    protected LockFactory $lockFactory;

    /** @var ConnectionPool */
    protected ConnectionPool $connectionPool;

    /** @var Logger */
    protected Logger $logger;


    /**
     * Initialize the metadata update command and its dependencies.
     *
     * @param ResourceSpace $resourceSpaceClient
     * @param FileUtility $fileUtility
     * @param FileRepository $fileRepository
     * @param FileMetadataRepository $fileMetadataRepository
     * @param PersistenceManager $persistenceManager
     * @param LockFactory $lockFactory
     * @param ConnectionPool $connectionPool
     */
    public function __construct(
        ResourceSpace $resourceSpaceClient,
        FileUtility $fileUtility,
        FileRepository $fileRepository,
        FileMetadataRepository $fileMetadataRepository,
        PersistenceManager $persistenceManager,
        LockFactory $lockFactory,
        ConnectionPool $connectionPool
    ) {
        $this->resourceSpaceClient = $resourceSpaceClient;
        $this->fileUtility = $fileUtility;
        $this->fileRepository = $fileRepository;
        $this->fileMetadataRepository = $fileMetadataRepository;
        $this->persistenceManager = $persistenceManager;
        $this->lockFactory = $lockFactory;
        $this->connectionPool = $connectionPool;
        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);

        parent::__construct();
    }


    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Refresh metadata for files imported from RKW Media/ResourceSpace.')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Maximum number of files per run.', '10')
            ->addOption(
                'age',
                null,
                InputOption::VALUE_REQUIRED,
                'Minimum age in seconds since the last successful metadata update. 0 updates regardless of age.',
                '86400'
            )
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Fetch metadata without writing changes.');
    }


    /**
     * Update the metadata of the oldest eligible ResourceSpace imports.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     * @throws \TYPO3\CMS\Core\Locking\Exception\LockAcquireException
     * @throws \TYPO3\CMS\Core\Locking\Exception\LockAcquireWouldBlockException
     * @throws \TYPO3\CMS\Core\Locking\Exception\LockCreateException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $limit = max(1, (int)$input->getOption('limit'));
        $minimumAge = max(0, (int)$input->getOption('age'));
        $dryRun = (bool)$input->getOption('dry-run');
        $lock = $this->lockFactory->createLocker(self::$defaultName);

        if (!$lock->acquire()) {
            $io->warning('Another metadata update is still in progress.');
            return Command::SUCCESS;
        }

        try {
            $rows = $this->findFilesToUpdate($limit, $minimumAge);
            if (empty($rows)) {
                $io->success('No files need a metadata update.');
                return Command::SUCCESS;
            }

            $updated = 0;
            $failed = 0;

            foreach ($rows as $row) {
                $fileUid = (int)$row['uid'];
                $resourceSpaceImageId = (int)$row['tx_rkwresourcespace_rkwmedia_image_id'];

                try {
                    $resourceMetadata = $this->resourceSpaceClient->getResourceFieldData($resourceSpaceImageId);
                    if (empty($resourceMetadata)) {
                        throw new \RuntimeException('ResourceSpace returned no metadata.');
                    }

                    if ($dryRun) {
                        $io->writeln(sprintf(
                            '[dry-run] File uid=%d, ResourceSpace id=%d: %d metadata fields fetched.',
                            $fileUid,
                            $resourceSpaceImageId,
                            count($resourceMetadata)
                        ));
                        continue;
                    }

                    $this->updateMetadata($fileUid, $resourceMetadata);
                    $updated++;
                    $message = sprintf(
                        'Metadata updated for file uid=%d (ResourceSpace id=%d).',
                        $fileUid,
                        $resourceSpaceImageId
                    );
                    $io->writeln($message);
                    $this->logger->info($message);
                } catch (\Throwable $exception) {
                    $failed++;
                    $message = sprintf(
                        'Metadata update failed for file uid=%d (ResourceSpace id=%d): %s',
                        $fileUid,
                        $resourceSpaceImageId,
                        str_replace(["\n", "\r"], '', $exception->getMessage())
                    );
                    $io->error($message);
                    $this->logger->error($message);
                }
            }

            if ($dryRun) {
                if ($failed > 0) {
                    $io->error(sprintf('Dry-run finished with %d successful API requests and %d failures.', count($rows) - $failed, $failed));
                    return Command::FAILURE;
                }
                $io->success(sprintf('Dry-run successful for %d files. No data was changed.', count($rows)));
                return Command::SUCCESS;
            }

            if ($failed > 0) {
                $io->warning(sprintf('Updated %d files; %d files failed.', $updated, $failed));
                return Command::FAILURE;
            }

            $io->success(sprintf('Updated metadata for %d files.', $updated));
            return Command::SUCCESS;
        } finally {
            $lock->release();
        }
    }


    /**
     * Find marked imports whose last successful metadata update is old enough.
     *
     * @param int $limit Maximum number of records to return
     * @param int $minimumAge Minimum age of the update timestamp in seconds
     * @return array<int, array<string, mixed>>
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    private function findFilesToUpdate(int $limit, int $minimumAge): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_file');
        $queryBuilder->getRestrictions()->removeAll();

        $conditions = [
            $queryBuilder->expr()->eq(
                'tx_rkwresourcespace_is_import',
                $queryBuilder->createNamedParameter(1, Connection::PARAM_INT)
            ),
            $queryBuilder->expr()->gt(
                'tx_rkwresourcespace_rkwmedia_image_id',
                $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)
            ),
        ];

        if ($minimumAge > 0) {
            $conditions[] = $queryBuilder->expr()->lte(
                'tx_rkwresourcespace_tstamp_import',
                $queryBuilder->createNamedParameter(time() - $minimumAge, Connection::PARAM_INT)
            );
        }

        $statement = $queryBuilder
            ->select('uid', 'tx_rkwresourcespace_rkwmedia_image_id')
            ->from('sys_file')
            ->where(...$conditions)
            ->orderBy('tx_rkwresourcespace_tstamp_import', 'ASC')
            ->addOrderBy('uid', 'ASC')
            ->setMaxResults($limit)
            ->execute();

        return $statement->fetchAllAssociative();
    }


    /**
     * Apply ResourceSpace metadata and set the successful update timestamp.
     *
     * @param int $fileUid UID of the sys_file record
     * @param array<int, \stdClass> $resourceMetadata Metadata returned by ResourceSpace
     * @return void
     * @throws \RuntimeException If the file or metadata record cannot be loaded
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    private function updateMetadata(int $fileUid, array $resourceMetadata): void
    {
        /** @var File|null $file */
        $file = $this->fileRepository->findByUid($fileUid);
        if (!$file || !$file->getMetadata()) {
            throw new \RuntimeException('The file or its metadata record could not be loaded.');
        }

        /** @var FileMetadata|null $fileMetadata */
        $fileMetadata = $this->fileMetadataRepository->findByUid($file->getMetadata()->getUid());
        if (!$fileMetadata) {
            throw new \RuntimeException('The file metadata record could not be loaded.');
        }

        $this->fileUtility->setFileMetadata($fileMetadata, $resourceMetadata);
        $this->fileMetadataRepository->update($fileMetadata);
        $this->persistenceManager->persistAll();

        $connection = $this->connectionPool->getConnectionForTable('sys_file');
        $connection->update(
            'sys_file',
            ['tx_rkwresourcespace_tstamp_import' => time()],
            ['uid' => $fileUid]
        );
    }
}
