<?php

include '../../admin/inc/configs.php';
include 'classes.php';

if (isset($_POST['ShowPhoto'])) {
    $_POST['ShowPhoto'] = filter_var($_POST['ShowPhoto'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_shp_url
            FROM k_immovables_sell AS kis
            LEFT JOIN k_street_house_photos AS kshp ON (kshp.k_shp_parent = kis.k_isf_address)
            WHERE k_isf_id=:id');
        $query->execute(array(":id" => $_POST['ShowPhoto']));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if (file_exists('../../admin/images/addresses/' . $result['k_shp_url']) && $result['k_shp_url']) {
            echo '../admin/images/addresses/' . $result['k_shp_url'];
        } else {
            echo '../admin/images/noimage.png';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['ShowVideo'])) {
    $_POST['ShowVideo'] = filter_var($_POST['ShowVideo'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_ip_url FROM k_immovables_photos WHERE k_ip_id=:id');
        $query->execute(array(":id" => $_POST['ShowVideo']));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $format = end(explode('.', $result['k_ip_url']));
        $filename = str_replace($format, 'flv', $result['k_ip_url']);
        if (file_exists('../../' . $filename) && $filename) {
            echo '<object id="videoplayer463" type="application/x-shockwave-flash" data="http://' . _SERVER_ADDRESS . '/player/uppod.swf" width="640" height="360">
            <param name="bgcolor" value="#ffffff" /><param name="allowFullScreen" value="true" />
            <param name="allowScriptAccess" value="always" />
            <param name="movie" value="http://' . _SERVER_ADDRESS . '/player/uppod.swf" />
            <param name="flashvars" value="comment=&amp;st=http://' . _SERVER_ADDRESS . '/styles/video177-788.txt&amp;file=http://' . _SERVER_ADDRESS . '/' . $filename . '" />
            </object>';
        } else {
            echo '../admin/images/noimage.png';
        }
    } catch (PDOException $e) {
        exit();
    }
}

?>
