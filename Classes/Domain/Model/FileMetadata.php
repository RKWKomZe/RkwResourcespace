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

use Madj2k\CopyrightGuardian\Domain\Model\MediaSource;

/**
 * Class FileMetadata
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_Resourcespace
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class FileMetadata extends \Madj2k\CoreExtended\Domain\Model\FileMetadata
{

    /**
     * @var string
     */
    protected string $alternative = '';


    /**
     * @var string
     */
    protected string $txCopyrightguardianCreator = '';


    /**
     * @var \Madj2k\CopyrightGuardian\Domain\Model\MediaSource|null
     */
    protected ?MediaSource $txCopyrightguardianSource = null;


    /**
     * @return string
     */
    public function getAlternative(): string
    {
        return $this->alternative;
    }


    /**
     * @param string $alternative
     * @return void
     */
    public function setAlternative(string $alternative): void
    {
        $this->alternative = $alternative;
    }


    /**
     * Returns the txCopyrightguardianCreator
     *
     * @return string
     */
    public function getTxCopyrightguardianCreator(): string
    {
        return $this->txCopyrightguardianCreator;
    }


    /**
     * Sets the txCopyrightguardianCreator
     *
     * @param string $txCopyrightguardianCreator
     * @return void
     */
    public function setTxCopyrightguardianCreator(string $txCopyrightguardianCreator)
    {
        $this->txCopyrightguardianCreator = $txCopyrightguardianCreator;
    }


    /**
     * Returns the txCopyrightguardianSource
     *
     * @return \Madj2k\CopyrightGuardian\Domain\Model\MediaSource|null
     */
    public function getTxCopyrightguardianSource(): ?MediaSource
    {
        return $this->txCopyrightguardianSource;
    }


    /**
     * Sets the txCopyrightguardianSource
     *
     * @param \Madj2k\CopyrightGuardian\Domain\Model\MediaSource $txCopyrightguardianSource
     * @return void
     */
    public function setTxCopyrightguardianSource(MediaSource $txCopyrightguardianSource)
    {
        $this->txCopyrightguardianSource = $txCopyrightguardianSource;
    }

}
