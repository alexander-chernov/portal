<?php

include '../../admin/inc/configs.php';
include 'classes.php';

if (isset($_POST['ShowPhoto'])) {
    $_POST['ShowPhoto'] = filter_var($_POST['ShowPhoto'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_shp_url
            FROM k_street_house_photos
            WHERE k_shp_parent=:id');
        $query->execute(array(":id" => $_POST['ShowPhoto']));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if ($result['k_shp_url']) {
            echo '../admin/images/addresses/' . $result['k_shp_url'];
        } else {
            echo '../images/noimage.png';
        }
    } catch (PDOException $e) {
        exit();
    }
}
?>
