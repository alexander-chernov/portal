<?php

require_once 'configs.php';
$db = mysql_connect(_DB_addr, _DB_login, _DB_pass)
        or die('Не удалось соединиться: ' . mysql_error());
mysql_select_db(_DB_name)
        or die('Не удалось выбрать базу данных');
mysql_query("SET NAMES UTF8");

$mysqli = new mysqli(_DB_addr, _DB_login, _DB_pass, _DB_name);
if ($mysqli->connect_errno) {
    echo "Не удалось подключиться к MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$sql = 'set names utf8';
$res = $mysqli->query($sql);

?>
