<?php

define('TOMSKLINE', 1);
session_start();
setcookie('login', '', time() - 9999999, '/');
setcookie('password', '', time() - 9999999, '/');
if (empty($_SESSION['login']) or empty($_SESSION['password'])) {
    exit("Доступ на эту страницу разрешен только зарегистрированным пользователям.");
}

//Подключаемся к базе
require_once 'inc/configs.php';
try {
    $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
    $mysql->exec('set names utf8');
    $query = $mysql->prepare('UPDATE k_users SET k_ku_last_date=NOW(),k_u_online=0 WHERE k_ku_id=:id');
    $query->execute(array(":id" => $_SESSION['id']));
} catch (PDOException $e) {
    exit();
}

unset($_SESSION['password']);
unset($_SESSION['login']);
unset($_SESSION['id']);
unset($_SESSION['privileges']);

$host = $_SERVER['HTTP_HOST'];
$uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
header("Location: http://$host$uri/");
?>