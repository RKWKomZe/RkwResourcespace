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
 * Class FileMetadata
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_Resourcespace
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class FileMetadata extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * caption
     *
     * @var string
     */
    protected $caption;

    /**
     * contentCreationDate
     *
     * @var int
     */
    protected $contentCreationDate;

    /**
     * creator
     *
     * @var string
     */
    protected $creator;

    /**
     * keywords
     *
     * @var string
     */
    protected $keywords;

    /**
     * publisher
     *
     * @var string
     */
    protected $publisher;

    /**
     * source
     *
     * @var string
     */
    protected $source;

    /**
     * text
     *
     * @var string
     */
    protected $text;

    /**
     * description
     *
     * @var string
     */
    protected $description;

    /**
     * title
     *
     * @var string
     */
    protected $title;

    /**
     * @var \RKW\RkwResourcespace\Domain\Model\File
     */
    protected $file;

    /**
     * Returns the caption
     *
     * @return string $caption
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Sets the caption
     *
     * @param string $caption
     * @return void
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
    }

    /**
     * Returns the contentCreationDate
     *
     * @return int $contentCreationDate
     */
    public function getContentCreationDate()
    {
        return $this->contentCreationDate;
    }

    /**
     * Sets the contentCreationDate
     *
     * @param int $contentCreationDate
     * @return void
     */
    public function setContentCreationDate($contentCreationDate)
    {
        $this->contentCreationDate = $contentCreationDate;
    }

    /**
     * Returns the creator
     *
     * @return string $creator
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Sets the creator
     *
     * @param string $creator
     * @return void
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;
    }

    /**
     * Returns the keywords
     *
     * @return string $keywords
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Sets the keywords
     *
     * @param string $keywords
     * @return void
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * Returns the publisher
     *
     * @return string $publisher
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * Sets the publisher
     *
     * @param string $publisher
     * @return void
     */
    public function setPublisher($publisher)
    {
        $this->publisher = $publisher;
    }

    /**
     * Returns the source
     *
     * @return string $source
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Sets the source
     *
     * @param string $source
     * @return void
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * Returns the text
     *
     * @return string $text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Sets the text
     *
     * @param string $text
     * @return void
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
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


    /**
     * txRkwbasicsPublisher
     *
     * @var string
     */
    protected $txRkwbasicsPublisher = '';

    /**
     * txRkwbasicsSource
     *
     * @var \Madj2k\CoreExtended\Domain\Model\MediaSources
     */
    protected $txRkwbasicsSource = null;

    /**
     * Returns the txRkwbasicsPublisher
     *
     * @return string $txRkwbasicsPublisher
     */
    public function getTxRkwbasicsPublisher()
    {
        return $this->txRkwbasicsPublisher;
    }

    /**
     * Sets the txRkwbasicsPublisher
     *
     * @param string $txRkwbasicsPublisher
     * @return void
     */
    public function setTxRkwbasicsPublisher($txRkwbasicsPublisher)
    {
        $this->txRkwbasicsPublisher = $txRkwbasicsPublisher;
    }

    /**
     * Returns the txRkwbasicsSource
     *
     * @return \Madj2k\CoreExtended\Domain\Model\MediaSources $txRkwbasicsSource
     */
    public function getTxRkwbasicsSource()
    {
        return $this->txRkwbasicsSource;
    }

    /**
     * Sets the txRkwbasicsSource
     *
     * @param \Madj2k\CoreExtended\Domain\Model\MediaSources $txRkwbasicsSource
     * @return void
     */
    public function setTxRkwbasicsSource(\Madj2k\CoreExtended\Domain\Model\MediaSources $txRkwbasicsSource)
    {
        $this->txRkwbasicsSource = $txRkwbasicsSource;
    }
}
