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
 * Class Import
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_Resourcespace
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Import extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * resourceSpaceImageId
     *
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     * @var int
     */
    protected $resourceSpaceImageId;

    /**
     * resourceSpaceUserId
     *
     * @var int
     */
    protected $resourceSpaceUserId;

    /**
     * resourceSpaceUserName
     *
     * @var string
     */
    protected $resourceSpaceUserName;

    /**
     * resourceSpaceUserRealName
     *
     * @var string
     */
    protected $resourceSpaceUserRealName;

    /**
     * the saved file
     *
     * @var \RKW\RkwResourcespace\Domain\Model\FileReference
     */
    protected $file;

    /**
     * backendUser
     *
     * @var \RKW\RkwResourcespace\Domain\Model\BackendUser
     */
    protected $backendUser;

    /**
     * Returns the resourceSpaceImageId
     *
     * @return int $resourceSpaceImageId
     */
    public function getResourceSpaceImageId()
    {
        return $this->resourceSpaceImageId;
    }

    /**
     * Sets the resourceSpaceImageId
     *
     * @param int $resourceSpaceImageId
     * @return void
     */
    public function setResourceSpaceImageId($resourceSpaceImageId)
    {
        $this->resourceSpaceImageId = $resourceSpaceImageId;
    }

    /**
     * Returns the resourceSpaceUserId
     *
     * @return int $resourceSpaceUserId
     */
    public function getResourceSpaceUserId()
    {
        return $this->resourceSpaceUserId;
    }

    /**
     * Sets the resourceSpaceUserId
     *
     * @param int $resourceSpaceUserId
     * @return void
     */
    public function setResourceSpaceUserId($resourceSpaceUserId)
    {
        $this->resourceSpaceUserId = $resourceSpaceUserId;
    }

    /**
     * Returns the resourceSpaceUserName
     *
     * @return string $resourceSpaceUserName
     */
    public function getResourceSpaceUserName()
    {
        return $this->resourceSpaceUserName;
    }

    /**
     * Sets the resourceSpaceUserName
     *
     * @param string $resourceSpaceUserName
     * @return void
     */
    public function setResourceSpaceUserName($resourceSpaceUserName)
    {
        $this->resourceSpaceUserName = $resourceSpaceUserName;
    }

    /**
     * Returns the resourceSpaceUserRealName
     *
     * @return string $resourceSpaceUserRealName
     */
    public function getResourceSpaceUserRealName()
    {
        return $this->resourceSpaceUserRealName;
    }

    /**
     * Sets the resourceSpaceUserRealName
     *
     * @param string $resourceSpaceUserRealName
     * @return void
     */
    public function setResourceSpaceUserRealName($resourceSpaceUserRealName)
    {
        $this->resourceSpaceUserRealName = $resourceSpaceUserRealName;
    }

    /**
     * Return the file
     *
     * @return \RKW\RkwResourcespace\Domain\Model\FileReference $file
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set the file
     *
     * @param \RKW\RkwResourcespace\Domain\Model\FileReference $file
     * @return void
     */
    public function setFile(\RKW\RkwResourcespace\Domain\Model\FileReference $file)
    {
        $this->file = $file;
    }

    /**
     * backendUser
     *
     * @param \RKW\RkwResourcespace\Domain\Model\BackendUser $backendUser
     * @return void
     */
    public function setBackendUser(\RKW\RkwResourcespace\Domain\Model\BackendUser $backendUser)
    {
        $this->backendUser = $backendUser;
    }

    /**
     * backendUser
     *
     * @return \RKW\RkwResourcespace\Domain\Model\BackendUser
     */
    public function getBackendUser()
    {
        return $this->backendUser;
    }
}
