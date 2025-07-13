<?php
define('TOMSKLINE', 1);
session_start();


/*
?>
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" >    
        <?php
*/
        //Подключаемся к базе
        require_once 'inc/configs.php';
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('UPDATE k_users SET k_ku_last_date=NOW(),k_u_online=0 WHERE k_ku_id=:id');
            $query->execute(array(":id" => $_SESSION['id']));
            $query2 = $mysql->prepare('UPDATE k_experts SET k_e_online=0 WHERE k_e_id=:id');
            $query2->execute(array(":id" => $_SESSION['id_e']));
        } catch (PDOException $e) {
            exit();
        }

        unset($_SESSION['password']);
        unset($_SESSION['login']);
        unset($_SESSION['id']);
        unset($_SESSION['password_e']);
        unset($_SESSION['login_e']);
        unset($_SESSION['id_e']);
        unset($_SESSION['privileges']);
        setcookie("login", 'false', time() - 999999);
        setcookie("password", 'false', time() - 999999);
        setcookie("login_e", 'false', time() - 999999);
        setcookie("password_e", 'false', time() - 999999);
Header('Location: /');
        //exit("<meta http-equiv='Refresh' content='0; URL=./'>");
/*
        ?>
    </head>
</html>
*/