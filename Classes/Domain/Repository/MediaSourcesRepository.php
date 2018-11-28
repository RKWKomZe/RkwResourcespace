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

/**
 * Class MediaSourcesRepository
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_Resourcespace
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class MediaSourcesRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * Find one by partial string
     *
     * @param string $search
     * @return object|\RKW\RkwResourcespace\Domain\Model\MediaSources
     */
    public function findOneByNameLike($search)
    {
        $query = $this->createQuery();

        /*
        // -> does not work
        $query->matching(
            $query->like('name', '%' . $search . '%')
        );
        */

        $query->statement('
			SELECT *
			FROM
				tx_rkwbasics_domain_model_mediasources
			WHERE
				name LIKE "%' . $search . '%"
		');

        return $query->execute()->getFirst();
        //===
    }
}

?>