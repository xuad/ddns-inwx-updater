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

// Read accesskey
$key = Helper::htmlencode($_GET['key']);

// Read ip
$ip = Helper::htmlencode($_GET['ip']);

// Initialize manager
$ddns = new DDNSManager($key);

// Update ID IP
$ddns->updateIP($ip);
?>
