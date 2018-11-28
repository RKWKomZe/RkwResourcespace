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
 * Class BackendUser
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_Resourcespace
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class BackendUser extends \TYPO3\CMS\Extbase\Domain\Model\BackendUser
{
	/**
	 * @var string
	 */
	protected $lang = '';

	/**
	 * Gets the lang of the user
	 *
	 * @return string
	 */
	public function getLang() {
		return $this->lang;
	}
}
