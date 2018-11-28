<?php

namespace RKW\RkwResourcespace\Domain\Model;

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
 * Class File
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_Resourcespace
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class File extends \TYPO3\CMS\Extbase\Domain\Model\File
{
    /**
     * identifier
     *
     * @var string
     */
    protected $identifier;

    /**
     * metadata
     *
     * @cascade persist
     * @var \RKW\RkwResourcespace\Domain\Model\FileMetadata
     */
    protected $metadata;

    /**
     * Returns the identifier
     *
     * @return integer $identifier
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Sets the identifier
     *
     * @param integer $identifier
     * @return void
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * Return the metadata
     *
     * @return \RKW\RkwResourcespace\Domain\Model\FileMetadata $metadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Set the fileMetadata
     *
     * @param \RKW\RkwResourcespace\Domain\Model\FileMetadata $metadata
     * @return void
     */
    public function setMetadata(\RKW\RkwResourcespace\Domain\Model\FileMetadata $metadata)
    {
        $this->metadata = $metadata;
    }
}
