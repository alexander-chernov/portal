<?php

session_start();

define('TOMSKLINE', 1);
include '../../inc/configs.php';

if (isset($_POST['writeblog_name'])) {
    $_POST['writeblog_name'] = strip_tags($_POST['writeblog_name']);
    $_POST['writeblog_brief'] = strip_tags($_POST['writeblog_brief']);
    $_POST['writeblog_text'] = strip_tags($_POST['writeblog_text']);
    $_POST['writeblog_category'] = filter_var($_POST['writeblog_category'], FILTER_SANITIZE_NUMBER_INT);
    $_POST['writeblog_image'] = str_replace('../admin/', '', strip_tags($_POST['writeblog_image']));
    $user = 'Гость';
    if (isset($_SESSION['login'])) {
        $user = $_SESSION['login'];
    }
    if (strlen($_POST['writeblog_name']) > 3 &&
            strlen($_POST['writeblog_brief']) > 3 &&
            strlen($_POST['writeblog_text']) > 3) {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('INSERT INTO k_blog
            (k_b_category,k_b_image,k_b_name,k_b_brief,k_b_text,k_b_user,k_b_date)
            VALUES (:cat, :image,:name,:brief,:text,:user,NOW())');
            $query->execute(array(":cat" => $_POST['writeblog_category'],
                ":image" => $_POST['writeblog_image'],
                ":name" => $_POST['writeblog_name'],
                ":brief" => $_POST['writeblog_brief'],
                ":text" => $_POST['writeblog_text'],
                ":user" => $user));
            echo 'yes';
        } catch (PDOException $e) {
            exit();
        }
    } else {
        echo 'no';
    }
}
?>