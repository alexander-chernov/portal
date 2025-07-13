<?php

defined('TOMSKLINE') or die('Restricted access');
require_once ($_SERVER['DOCUMENT_ROOT'].'/admin/inc/configs.php');

$currentCookieParams = session_get_cookie_params();
$rootDomain = 'nashtomsk.ru';
session_set_cookie_params(
    $currentCookieParams["lifetime"],
    $currentCookieParams["path"],
    $rootDomain,
    $currentCookieParams["secure"],
    $currentCookieParams["httponly"]
);

session_start();
