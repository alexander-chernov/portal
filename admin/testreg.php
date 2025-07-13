<?php
define('TOMSKLINE', 1);
session_start();
        if (isset($_POST['login_name'])) {
            $login = $_POST['login_name'];
            if ($login == '') {
                unset($login);
            }
        }
        if (isset($_POST['pass_value'])) {
            $password = $_POST['pass_value'];
            if ($password == '') {
                unset($password);
            }
        }

        if (empty($login) or empty($password)) {
            exit("Вы ввели не всю информацию, вернитесь назад и заполните все поля!");
        }

        unset($_SESSION['password']);
        unset($_SESSION['login']);
        unset($_SESSION['id']);
        unset($_SESSION['privileges']);
        //Подключаемся к базе
        require_once 'inc/configs.php';
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
        } catch (PDOException $e) {
            exit();
        }
        //Удаляем лишние символы, которые могут быть восприняты скриптами или тегами
        $login = trim(filter_var($login, FILTER_SANITIZE_STRIPPED));
        $password = trim(filter_var($password, FILTER_SANITIZE_STRIPPED));
        //Проверка на подбор паролей
        $ip = getenv("HTTP_X_FORWARDED_FOR");
        if (empty($ip) || $ip == 'unknown') {
            $ip = getenv("REMOTE_ADDR");
        }//извлекаем ip

        try {
            //удаляем ip-адреса ошибавшихся при входе пользователей через 15 минут.
            $query = $mysql->prepare('DELETE FROM k_error_logon WHERE UNIX_TIMESTAMP() - UNIX_TIMESTAMP(k_el_date) > 900');
            $query->execute();
            //извлекаем из базы количество неудачных попыток входа за последние 15 у пользователя с данным ip
            $query1 = $mysql->prepare('SELECT k_el_col FROM k_error_logon WHERE k_el_ip=:ip');
            $query1->execute(array(":ip" => $ip));
            $row = $query1->fetch(PDO::FETCH_ASSOC);
            if ($row['k_el_col']) {
                $col_from_ip = $row['k_el_col'];
            } else {
                $col_from_ip = 0;
            }
            if ($col_from_ip > 2) {
                //если ошибок больше двух, т.е три, то выдаем сообщение.
                header($_ENV['SERVER_PROTOCOL'] . " 403 Restricted access", true, 403);
                header("Location: http://" . _SERVER_ADDRESS . "/admin/failed.php");
            }
            //Ищем пользователя в базе
            $query2 = $mysql->prepare('SELECT * FROM k_users WHERE k_ku_login=:login AND (k_u_privileges=1 or k_u_privileges=2)');
            $query2->execute(array(":login" => $login));
            $myrow = $query2->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        if (empty($myrow['k_ku_password'])) {
            //Если пользователя с введенным логином не существует
            header($_ENV['SERVER_PROTOCOL'] . " 403 Restricted access", true, 403);
            header("Location: http://" . _SERVER_ADDRESS . "/admin/failed.php");
        } else {
            //Если существует, то сверяем пароли
            if ($myrow['k_ku_password'] == md5($password)) {
                //Если пароли совпадают, то запускаем пользователю сессию.
                $_SESSION['login'] = $myrow['k_ku_login'];
                $_SESSION['id'] = $myrow['k_ku_id'];
                $_SESSION['password'] = $myrow['k_ku_password'];
                $_SESSION['privileges'] = $myrow['k_u_privileges'];
                try {
                    $query3 = $mysql->prepare('DELETE FROM k_error_logon WHERE k_el_ip=:ip');
                    $query3->execute(array(":ip" => $ip));
                    $query4 = $mysql->prepare('UPDATE k_users SET k_ku_last_ip=:ip,k_ku_last_date=NOW(),k_u_online=1 WHERE k_ku_id=:id');
                    $query4->execute(array(":ip" => $ip, ":id" => $_SESSION['id']));
                } catch (PDOException $e) {
                    exit();
                }
                if ($_POST['save'] != NULL) {
                    //Если пользователь хочет, чтобы его данные сохранились для последующего входа, то сохраняем в куках его браузера
                    setcookie("login", 'false', time() - 999999);
                    setcookie("password", 'false', time() - 999999);
                    setcookie("login", $login, time() + 9999999);
                    setcookie("password", md5($password), time() + 9999999);
                }
                $host = $_SERVER['HTTP_HOST'];
                $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
                $extra = 'admin_gl/';
                header("Location: http://$host$uri/$extra");
            } else {
                if ($col_from_ip == 0) {
                    try {
                        $query5 = $mysql->prepare('INSERT INTO k_error_logon (k_el_ip,k_el_date) VALUES (:ip,NOW())');
                        $query5->execute(array(":ip" => $ip));
                    } catch (PDOException $e) {
                        exit();
                    }
                } else {
                    try {
                        $query5 = $mysql->prepare('UPDATE k_error_logon SET k_el_col=:col WHERE k_el_ip=:ip');
                        $query5->execute(array(":ip" => $ip, ":col" => ($col_from_ip + 1)));
                    } catch (PDOException $e) {
                        exit();
                    }
                }
                try {
                    $query6 = $mysql->prepare('INSERT INTO k_warning_messages (k_wm_message,k_wm_date) VALUES (:ip,NOW())');
                    $query6->execute(array(":ip" => "К вам пытались зайти с IP $ip. Вы можете заблокировать данный IP во избежание дальнейших попыток взлома."));
                } catch (PDOException $e) {
                    exit();
                }
                header($_ENV['SERVER_PROTOCOL'] . " 403 Restricted access", true, 403);
                header("Location: http://" . _SERVER_ADDRESS . "/admin/failed.php");
            }
        }
        /*
        ?>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" >

</head>
</html>
        */