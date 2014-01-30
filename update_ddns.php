<?php

/**
 * DDNS-INWX-Updater
 *
 * Copyright (c) Patrick Mosch
 *
 * @link http://xuad.net
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */
// Set utf-8 header
header('Content-type: text/plain; charset=utf-8');

// Load classes
require_once 'DDNSManager.php';
require_once 'Helper.php';

// Some variables declaration
$key = "";
$ip = "";

// Read accesskey
if(isset($_GET['key']))
{
	$key = Helper::htmlencode($_GET['key']);
}

// Read ip
if(isset($_GET['ip']))
{
	$ip = Helper::htmlencode($_GET['ip']);
}

// Initialize ddnsmanager
$ddns = new DDNSManager($key);

// Update ip
$ddns->updateIP($ip);