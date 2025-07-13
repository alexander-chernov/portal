<?php

session_start();
defined('TOMSKLINE') or die('Restricted access');
require_once 'classes.php';

//Страницы
if (isset($_GET['limit'])) {
    $limit = filter_var($_GET['limit'], FILTER_VALIDATE_INT);
}

if (isset($_GET['Id'])) {
    $ID = filter_var($_GET['Id'], FILTER_VALIDATE_INT);
}

function plural_form($n, $form1, $form2) {
    $n = abs($n) % 100;
    $n1 = $n % 10;
    if ($n > 10 && $n < 20)
        return $form1;
    if ($n1 > 1 && $n1 < 5)
        return $form2;
    if ($n1 == 1)
        return $form1;
    return $form2;
}

?>
