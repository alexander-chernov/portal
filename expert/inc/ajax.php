<?php

define('TOMSKLINE', 1);
session_start();
require_once '../../inc/configs.php';
try {
    $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
    $mysql->exec('set names utf8');
} catch (PDOException $e) {
    exit();
}

if (isset($_POST['NewExpertLogin'])) {
    require_once '../../admin/inc/functions.php';
    require_once '../../admin/admin_gl/inc/tariffs.php';
    $money = new TarrifOthers();
    $cost = $money->price[0];
    $_POST['NewExpertLogin'] = filter_var($_POST['NewExpertLogin'], FILTER_SANITIZE_EMAIL);
    $_POST['NewExpertPassword'] = md5(filter_var($_POST['NewExpertPassword'], FILTER_SANITIZE_STRIPPED));
    $_POST['NewExpertPassword2'] = md5(filter_var($_POST['NewExpertPassword2'], FILTER_SANITIZE_STRIPPED));
    $_POST['NewExpertTheme'] = filter_var($_POST['NewExpertTheme'], FILTER_SANITIZE_STRIPPED);
    $_POST['NewExpertBrief'] = filter_var($_POST['NewExpertBrief'], FILTER_SANITIZE_STRIPPED);
    $_POST['NewExpertHeader'] = filter_var($_POST['NewExpertHeader'], FILTER_SANITIZE_STRIPPED);
    $_POST['NewExpertDescription'] = filter_var($_POST['NewExpertDescription'], FILTER_SANITIZE_STRIPPED);
    $_POST['NewExpertPhone'] = filter_var($_POST['NewExpertPhone'], FILTER_SANITIZE_STRIPPED);
    $_POST['NewExpertSite'] = CorrectURL($_POST['NewExpertSite']);
    $_POST['NewExpertAddress'] = filter_var($_POST['NewExpertAddress'], FILTER_VALIDATE_INT);
    if (!empty($_POST['NewExpertLogin']) && $_POST['NewExpertPassword'] == $_POST['NewExpertPassword2']) {
        try {
            $query0 = $mysql->prepare('SELECT * FROM k_experts WHERE k_e_email=:email');
            $query0->execute(array(":email" => $_POST['NewExpertLogin']));
            if ($query0->rowCount() == 0) {
                $query = $mysql->prepare('INSERT INTO k_experts
                            (k_e_email,k_e_password,k_e_theme,k_e_brief,k_e_header,k_e_description,k_e_phone,k_e_site,k_e_address,k_e_verified,k_e_date,k_e_last_date,k_e_active)
                            VALUES (:email,:pass,:theme,:brief,:header,:descr,:phone,:site,:addr,1,NOW(),NOW(),0)');
                $query->execute(array(":email" => $_POST['NewExpertLogin'],
                    ":pass" => $_POST['NewExpertPassword2'],
                    ":theme" => $_POST['NewExpertTheme'],
                    ":brief" => $_POST['NewExpertBrief'],
                    ":header" => $_POST['NewExpertHeader'],
                    ":descr" => $_POST['NewExpertDescription'],
                    ":phone" => $_POST['NewExpertPhone'],
                    ":site" => $_POST['NewExpertSite'],
                    ":addr" => $_POST['NewExpertAddress']));
                $id = $mysql->lastInsertId();
                $query2 = $mysql->prepare('INSERT INTO k_experts_categories_links (k_ecl_expert_id,k_ecl_category_id) VALUES (:id,:cat)');
                for ($i = 0; $i < count($_POST['NewExpertCat']); $i++) {
                    $query2->execute(array(":id" => $id, ":cat" => $_POST['NewExpertCat'][$i]));
                }
                mb_internal_encoding("UTF-8");
                if ($cost > 0) {
                    $message = "Уважаемый пользователь, для завершения регистрации вам необходимо дня оплатить регистрацию, используя ссылку:" . PHP_EOL;
                    $message .= "http://" . _SERVER_ADDRESS . "/payment.php?pay&expert&registration=" . $id . PHP_EOL;
                    mb_send_mail($_POST['NewExpertLogin'], "Регистрация на портале " . date("Y-m-d"), $message, "From: \""._SERVER_ADDRESS."\"" . PHP_EOL);
                } else {
                    $message = "Уважаемый пользователь, для завершения регистрации вам необходимо перейти по ссылке:" . PHP_EOL;
                    $message .= "http://" . _SERVER_ADDRESS . "/registration.php?do=activate&expert=" . $id . PHP_EOL;
                    mb_send_mail($_POST['NewExpertLogin'], "Регистрация на портале " . date("Y-m-d"), $message, "From: \""._SERVER_ADDRESS."\"" . PHP_EOL);
                }
                echo 'yes';
            } else {
                echo 'exist';
            }
        } catch (PDOException $e) {
            exit();
        }
    } else {
        echo 'empty';
    }
}
?>