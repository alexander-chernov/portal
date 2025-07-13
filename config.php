<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alexander A. Chernov
 * Date: 14.09.13
 * Time: 13:45
 * To change this template use File | Settings | File Templates.
 */
//error_reporting(E_ALL);
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;
mb_internal_encoding("UTF-8");
define('YANDEX_GEO_LINK',"http://geocode-maps.yandex.ru/1.x/?format=json&geocode=");
define('YANDEX_GEO_PEOPLEMAP_LINK',"http://psearch-maps.yandex.ru/1.x/?format=json&text=");
$hash = base64_encode(md5(md5('TriPorosenka')));
define('DOUBLEGIS_API_KEY',$hash);
$url = 'http://catalog.api.2gis.ru/geo/search?version=1.3&key='.$hash.'&q=';
define('DOUBLEGIS_GEO_LINK',$url);
//for people map
define('YANDEX_API_KEY','API_KEY');
$perPage = 1000;
$perPageAjax = 5;
$perPageMap = 100;
session_start();
//$mysqli = new mysqli('localhost', 'project', '4eTLDcKmdKpQYqhy','k_kedr');
if ($mysqli->connect_errno) {
    echo "Не удалось подключиться к MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
if (!$mysqli->set_charset("utf8")) {
    printf("Ошибка при загрузке набора символов utf8: %s<br>\n", $mysqli->error);
} else {
    printf("Текущий набор символов: %s<br>\n", $mysqli->character_set_name());
}
require_once "functions.php";
?>