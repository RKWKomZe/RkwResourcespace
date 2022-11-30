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
 * Class FileReference
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_Resourcespace
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class FileReference extends \TYPO3\CMS\Extbase\Domain\Model\FileReference
{
    /**
     * fieldname
     *
     * @var string
     */
    protected $fieldname;

    /**
     * uidLocal
     *
     * @var integer
     */
    protected $uidLocal;

    /**
     * tableLocal
     *
     * @var string
     */
    protected $tableLocal;

    /**
     * tablenames
     *
     * @var string
     */
    protected $tablenames;

    /**
     * @var \RKW\RkwResourcespace\Domain\Model\File
     */
    protected $file;

    /**
     * Returns the fieldname
     *
     * @return integer $fieldname
     */
    public function getFieldname()
    {
        return $this->fieldname;
    }

    /**
     * Sets the fieldname
     *
     * @param integer $fieldname
     * @return void
     */
    public function setFieldname($fieldname)
    {
        $this->fieldname = $fieldname;
    }

    /**
     * Returns the uidLocal
     *
     * @return integer $uidLocal
     */
    public function getUidLocal()
    {
        return $this->uidLocal;
    }

    /**
     * Sets the uidLocal
     *
     * @param integer $uidLocal
     * @return void
     */
    public function setUidLocal($uidLocal)
    {
        $this->uidLocal = $uidLocal;
    }

    /**
     * Returns the tableLocal
     *
     * @return integer $tableLocal
     */
    public function getTableLocal()
    {
        return $this->tableLocal;
    }

    /**
     * Sets the tableLocal
     *
     * @param integer $tableLocal
     * @return void
     */
    public function setTableLocal($tableLocal)
    {
        $this->tableLocal = $tableLocal;
    }

    /**
     * Returns the tablenames
     *
     * @return integer $tablenames
     */
    public function getTablenames()
    {
        return $this->tablenames;
    }

    /**
     * Sets the tablenames
     *
     * @param integer $tablenames
     * @return void
     */
    public function setTablenames($tablenames)
    {
        $this->tablenames = $tablenames;
    }

    /**
     * Set file
     *
     * @param \RKW\RkwResourcespace\Domain\Model\File $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * Get file
     *
     * @return \RKW\RkwResourcespace\Domain\Model\File
     */
    public function getFile()
    {
        return $this->file;
    }
}
