<?php
date_default_timezone_set('Europe/Istanbul');

$_scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$_host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('BASE_URL', $_scheme . '://' . $_host . '/berber-sistemi');
