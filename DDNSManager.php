<?php

/**
 * DDNS-INWX-Updater
 *
 * Copyright (c) Patrick Mosch
 *
 * @link http://xuad.net
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */
// Load some classes
require_once 'inwx/domrobot.class.php';
require_once 'log/KLogger.php';
require_once 'Helper.php';

/**
 * Update der dynamischen IP
 * 
 * @author    Patrick Mosch
 * @copyright Patrick Mosch 2012
 */
class DDNSManager
{

	/**
	 * Configfile
	 * @var string
	 */
	protected $iniFile = "config.ini";

	/**
	 * Accesskey 
	 * @var string
	 */
	protected $configKey = "";

	/**
	 * Accesskey instantiate 
	 * @var string
	 */
	protected $key = "";

	/**
	 * INWX API-Url
	 * @var type 
	 */
	protected $inwxUrl = "";

	/**
	 * INWX user
	 * @var string
	 */
	protected $inwxUser = "";

	/**
	 * INWX password
	 * @var string
	 */
	protected $inwxPasswd = "";

	/**
	 * INWX domain
	 * @var string
	 */
	protected $inwxDomain = "";

	/**
	 * INWX subdomain
	 * @var string 
	 */
	protected $inwxSubdomain = "";

	/**
	 * Logging
	 * @var KLogger-Object
	 */
	protected $logger;

	/**
	 * Old IP
	 * @var string
	 */
	protected $oldIP = "";

	/**
	 * New IP
	 * @var string
	 */
	protected $newIP = "";

	/**
	 * Initialize the object
	 */
	public function __construct($key)
	{
		// Save accesskey
		$this->key = $key;

		// Load config
		$this->init();
	}

	/**
	 * Load config and initialisize KLogger
	 */
	protected function init()
	{
		// Load configfile
		$ini = parse_ini_file($this->iniFile, TRUE);

		// Load accesskey
		$this->configKey = isset($ini["api"]["api_key"]) ? $this->validateConfigEntry($ini["api"]["api_key"]) : "";

		// Load INWX-API-URL
		$this->inwxUrl = isset($ini["inwx"]["url"]) ? $this->validateConfigEntry($ini["inwx"]["url"]) : "";

		// Load user
		$this->inwxUser = isset($ini["inwx"]["user"]) ? $this->validateConfigEntry($ini["inwx"]["user"]) : "";

		// Load password
		$this->inwxPasswd = isset($ini["inwx"]["passwd"]) ? $this->validateConfigEntry($ini["inwx"]["passwd"]) : "";

		// Load domain
		$this->inwxDomain = isset($ini["inwx"]["domain"]) ? $this->validateConfigEntry($ini["inwx"]["domain"]) : "";

		// Load subsomain
		$this->inwxSubdomain = isset($ini["inwx"]["subdomain"]) ? $this->validateConfigEntry($ini["inwx"]["subdomain"]) : "";

		// Initialisize KLogger
		$filepath = isset($ini["log"]["filepath"]) ? $this->validateConfigEntry($ini["log"]["filepath"]) : "";
		$this->logger = new KLogger($filepath, 2);
	}

	/**
	 * Update domain with new ip
	 * @param type $ip
	 * @return string
	 */
	public function updateIP($ip)
	{
		$this->newIP = $ip;

		// Check access
		if (!$this->checkAccess())
		{
			$this->logger->LogError("unauthorisized access from IP: " . $_SERVER['REMOTE_ADDR']);
			return false;
		}

		// Get old ip from inwx
		$domrobot = new domrobot($this->inwxUrl);
		$domrobot->setDebug(false);
		$domrobot->setLanguage('en');

		$response = $domrobot->login($this->inwxUser, $this->inwxPasswd);

		// After successfully authentificated start update
		if ($response['code'] == 1000)
		{
			// Get subdomain, recordID and current ip
			$object = "nameserver";
			$method = "info";
			$params = array();
			$params['domain'] = $this->inwxDomain;
			$params['name'] = $this->inwxSubdomain;
			$response = $domrobot->call($object, $method, $params);

			if ($response['code'] == 1000)
			{
				if (strlen($this->newIP) > 0)
				{
					// Get the recordID
					// ID is important for the update
					$recordID = $response["resData"]["record"][0]["id"];

					// Get old IP
					$this->oldIP = $response["resData"]["record"][0]["content"];

					// Update-Call
					$object = "nameserver";
					$method = "updateRecord";
					$params = array();
					$params['id'] = $recordID;
					$params['content'] = $this->newIP;
					$response = $domrobot->call($object, $method, $params);

					// After successfully IP update well done
					if ($response['code'] == 1000)
					{
						// Write some information in logfile
						$this->logger->LogInfo("IP Update successfully! | Old IP: " . $this->oldIP . " | New IP: " . $this->newIP);
						return true;
					}
					else
					{
						// Cant update IP
						$this->logger->LogError("IP Update Error : " . $this->newIP);
						return false;
					}
				}
				else
				{
					// New IP doesnt exists
					$this->logger->LogError("New IP false : " . $this->newIP);
					return false;
				}
			}
			else
			{
				// INWX login error
				$this->logger->LogError("Cant login : " . $response);
				return false;
			}
		}
	}

	/**
	 * Check correct accesskey
	 * @param string $key Schlussen
	 * @return boolean Zugriff
	 */
	protected function checkAccess()
	{
		if ($this->key == $this->configKey)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Validate inifile-entry
	 * @param string $entry
	 * @return string
	 */
	protected function validateConfigEntry($entry)
	{
		// Escape string 
		if (isset($entry) && !empty($entry))
		{
			return Helper::htmlencode($entry);
		}
		else
		{
			return "";
		}
	}

}

?>
