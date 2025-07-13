<?php

require_once 'configs.php';

function UserAccess($cat) {
    $cat = filter_var($cat, FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_ucl_cat_id FROM k_users_categories_links WHERE k_ucl_user_id=:id');
        $query->execute(array(":id" => $_SESSION['id']));
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $access = array();
        foreach ($result as $value) {
            array_push($access, $value['k_ucl_cat_id']);
        }
        if (in_array(8, $access)) {
            $_SESSION['map_access'] = 1;
        }
        if (in_array($cat, $access)) {
            return TRUE;
        } else {
            return FALSE;
        }
    } catch (PDOException $e) {
        exit();
    }
    return FALSE;
}

function UpdateActivityAdmin() {
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('UPDATE k_users SET k_ku_last_date=NOW(),k_u_online=1,k_ku_session_id=:s_id WHERE k_ku_id=:id');
        $query->execute(array(":id" => $_SESSION['id'], ":s_id" => session_id()));
        $query1 = $mysql->prepare('UPDATE k_users SET k_u_online=0,k_ku_session_id="" WHERE k_ku_last_date<:date');
        $query1->execute(array(":date" => date('Y-m-d G:i:s', (time() - 15 * 60))));
    } catch (PDOException $e) {
        exit();
    }
}

?>
