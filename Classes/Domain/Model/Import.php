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
 * @copyright RKW Kompetenzzentrum
 * @package RKW_Resourcespace
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Import extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     * @var int
     */
    protected int $resourceSpaceImageId = 0;


    /**
     * @var int
     */
    protected int $resourceSpaceUserId = 0;


    /**
     * @var string
     */
    protected string $resourceSpaceUserName = '';


    /**
     * @var string
     */
    protected string $resourceSpaceUserRealName = '';


    /**
     * @var \RKW\RkwResourcespace\Domain\Model\FileReference|null
     */
    protected ?FileReference $file = null;


    /**
     * @var \RKW\RkwResourcespace\Domain\Model\BackendUser|null
     */
    protected ?BackendUser $backendUser = null;


    /**
     * Returns the resourceSpaceImageId
     *
     * @return int $resourceSpaceImageId
     */
    public function getResourceSpaceImageId(): int
    {
        return $this->resourceSpaceImageId;
    }


    /**
     * Sets the resourceSpaceImageId
     *
     * @param int $resourceSpaceImageId
     * @return void
     */
    public function setResourceSpaceImageId(int $resourceSpaceImageId): void
    {
        $this->resourceSpaceImageId = $resourceSpaceImageId;
    }


    /**
     * Returns the resourceSpaceUserId
     *
     * @return int $resourceSpaceUserId
     */
    public function getResourceSpaceUserId(): int
    {
        return $this->resourceSpaceUserId;
    }


    /**
     * Sets the resourceSpaceUserId
     *
     * @param int $resourceSpaceUserId
     * @return void
     */
    public function setResourceSpaceUserId(int $resourceSpaceUserId): void
    {
        $this->resourceSpaceUserId = $resourceSpaceUserId;
    }


    /**
     * Returns the resourceSpaceUserName
     *
     * @return string $resourceSpaceUserName
     */
    public function getResourceSpaceUserName(): string
    {
        return $this->resourceSpaceUserName;
    }


    /**
     * Sets the resourceSpaceUserName
     *
     * @param string $resourceSpaceUserName
     * @return void
     */
    public function setResourceSpaceUserName(string $resourceSpaceUserName): void
    {
        $this->resourceSpaceUserName = $resourceSpaceUserName;
    }


    /**
     * Returns the resourceSpaceUserRealName
     *
     * @return string $resourceSpaceUserRealName
     */
    public function getResourceSpaceUserRealName(): string
    {
        return $this->resourceSpaceUserRealName;
    }


    /**
     * Sets the resourceSpaceUserRealName
     *
     * @param string $resourceSpaceUserRealName
     * @return void
     */
    public function setResourceSpaceUserRealName(string $resourceSpaceUserRealName): void
    {
        $this->resourceSpaceUserRealName = $resourceSpaceUserRealName;
    }


    /**
     * Return the file
     *
     * @return \RKW\RkwResourcespace\Domain\Model\FileReference $file
     */
    public function getFile():? FileReference
    {
        return $this->file;
    }


    /**
     * Set the file
     *
     * @param \RKW\RkwResourcespace\Domain\Model\FileReference $file
     * @return void
     */
    public function setFile(FileReference $file): void
    {
        $this->file = $file;
    }


    /**
     * backendUser
     *
     * @param \RKW\RkwResourcespace\Domain\Model\BackendUser $backendUser
     * @return void
     */
    public function setBackendUser(BackendUser $backendUser): void
    {
        $this->backendUser = $backendUser;
    }


    /**
     * backendUser
     *
     * @return \RKW\RkwResourcespace\Domain\Model\BackendUser
     */
    public function getBackendUser():? BackendUser
    {
        return $this->backendUser;
    }
}
