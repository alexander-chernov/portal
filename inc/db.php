<?php
define('TOMSKLINE', 1);
require_once 'configs.php';
$db = mysql_connect($database_address, $database_login, $database_password)
        or die('Не удалось соединиться: ' . mysql_error());
mysql_select_db($database_name)
        or die('Не удалось выбрать базу данных');
mysql_query("SET NAMES " . $database_charset);

?>
