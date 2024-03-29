<?php
namespace RKW\RkwResourcespace\Domain\Repository;

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

use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Class FileRepository
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_Resourcespace
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class FileRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * Find by name using wildcard
     *
     * @param string $name
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findByBeginningOfName(string $name): QueryResultInterface
    {
        $query = $this->createQuery();

        return $query->matching(
            $query->logicalOr(
                $query->like('name', $name . '\_%'),
                $query->like('name', $name . '-%')
            )
        )->execute();
    }
}
