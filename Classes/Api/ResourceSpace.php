<?php
namespace RKW\RkwResourcespace\Api;

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

use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use Madj2k\CoreExtended\Utility\GeneralUtility;

/**
 * Class ResourceSpace
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_Resourcespace
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ResourceSpace implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * @var string
     */
    protected string $apiBaseUrl = '';


    /**
     * @var string
     */
    protected string $apiPrivateKey = '';


    /**
     * @var string
     */
    protected string $apiUser = '';


    /**
     * @var resource A stream context resource
     */
    protected $streamContext = null;


    /**
     * @var \TYPO3\CMS\Core\Log\Logger|null
     */
    protected ?Logger $logger = null;


    /**
     * initializeObject
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function initializeObject(): void
    {
        $settingsDefault = $this->getSettings();

        // Set the private API key for the user (from the user account page) and the user we're accessing the system as.
        $this->apiBaseUrl = filter_var($settingsDefault['resourceSpaceApi']['baseUrl'], FILTER_SANITIZE_STRING);
        $this->apiPrivateKey = filter_var($settingsDefault['resourceSpaceApi']['privateKey'], FILTER_SANITIZE_STRING);
        $this->apiUser = filter_var($settingsDefault['resourceSpaceApi']['user'], FILTER_SANITIZE_STRING);

        // login header
        $opts = array(
            'http' => array(
                'method' => 'GET',
            ),
        );

        // optional: proxy configuration
        if ($settingsDefault['resourceSpaceApi']['proxy']) {

            $optsProxy = array(
                'http' => array(
                    'proxy'           => $settingsDefault['resourceSpaceApi']['proxy'],
                    'request_fulluri' => true,
                ),
            );

            if ($settingsDefault['resourceSpaceApi']['proxyUsername']) {
                $auth = base64_encode(
                    $settingsDefault['resourceSpaceApi']['proxyUsername'] . ':'
                    . $settingsDefault['resourceSpaceApi']['proxyPassword']
                );
                $optsProxy['http']['header'] = 'Proxy-Authorization: Basic ' . $auth;
            }
            $opts = array_merge_recursive($opts, $optsProxy);
        }

        $this->streamContext = stream_context_create($opts);
    }


    /**
     * Returns TYPO3 settings
     *
     * @param string $which Which type of settings will be loaded
     * @return array
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    protected static function getSettings(string $which = ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS): array
    {
        return GeneralUtility::getTypoScriptConfiguration('Rkwresourcespace', $which);
    }


    /**
     * getResourcePath
     * (returns image-url)
     *
     * @param int $resourceSpaceImageId
     * @param string $file_extension
     * @return string
     */
    public function getResourcePath(int $resourceSpaceImageId, string $file_extension = 'jpg'): string
    {
        // create search query
        // Hint: without that empty "param2" - "param8" arguments the query will fail
        $query = "user=" . $this->apiUser . "&function=get_resource_path&param1="
            . $resourceSpaceImageId . "&param2=0&param3=&param4=1&param5=" . $file_extension . "&param6=&param7&param8=";
        $sign = hash("sha256", $this->apiPrivateKey . $query);

        try {
            return json_decode(
                file_get_contents(
                    $this->apiBaseUrl . "?" . $query . "&sign=" . $sign,
                    false,
                    $this->streamContext
                )
            );

        } catch (\Exception $e) {
            $this->getLogger()->log(
                \TYPO3\CMS\Core\Log\LogLevel::ERROR,
                sprintf('Error while try to get image resource path: %s', $e->getMessage())
            );
        }

        return '';
    }


    /**
     * getResourceData
     * (returns basic file information like name, file extension etc)
     *
     * @param int $resourceSpaceImageId
     * @return \stdClass
     */
    public function getResourceData(int $resourceSpaceImageId): \stdClass
    {
        // create search query
        $query = "user=" . $this->apiUser . "&function=get_resource_data&param1=" . $resourceSpaceImageId;
        $sign = hash("sha256", $this->apiPrivateKey . $query);


        try {
            $data = json_decode(
                file_get_contents(
                    $this->apiBaseUrl . "?" . $query . "&sign=" . $sign,
                    false,
                    $this->streamContext
                )
            );

            // fix for foxy
            if (!$data->file_checksum) {
                $data->file_checksum = sha1($data->ref);
            }

            return $data;

        } catch (\Exception $e) {
            $this->getLogger()->log(
                \TYPO3\CMS\Core\Log\LogLevel::ERROR,
                sprintf('Error while try to get image resource data: %s', $e->getMessage())
            );
        }

        return new \stdClass();
    }


    /**
     * getResourceFieldData
     * (returns metadata)
     *
     * @param int $resourceSpaceImageId
     * @return array
     */
    public function getResourceFieldData(int $resourceSpaceImageId): array
    {
        // create search query
        $query = "user=" . $this->apiUser . "&function=get_resource_field_data&param1=" . $resourceSpaceImageId;
        $sign = hash("sha256", $this->apiPrivateKey . $query);

        try {
            return json_decode(
                file_get_contents(
                    $this->apiBaseUrl . "?" . $query . "&sign=" . $sign,
                    false,
                    $this->streamContext)
            );

        } catch (\Exception $e) {

            $this->getLogger()->log(
                \TYPO3\CMS\Core\Log\LogLevel::ERROR,
                sprintf('Error while try to get image resource field data: %s', $e->getMessage())
            );
        }

        return [];
    }



    /**
     * Returns logger instance
     *
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    protected function getLogger(): Logger
    {
        if (!$this->logger instanceof \TYPO3\CMS\Core\Log\Logger) {
            $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        }

        return $this->logger;
    }
}
