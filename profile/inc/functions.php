<?php

defined('CHERNOV') or die('Restricted access');

//Рубрика при подаче объявления
if (isset($_GET['ImmoType'])) {
    if (settype($_GET['ImmoType'], "integer")) {
        $Immo_type = $_GET['ImmoType'];
    }
}

//Меняем падеж
function dropBackWords($word) {
    $reg = "/(ый|ой|ая|ия|ий|ое|ые|ому|а|о|у|е|ого|ему|и|ство|ых|ох|я|ют|ат|ок|аю|ы)$/i";
    if (preg_match($reg, $word)) {
        if (preg_match("/(ая)$/i", $word)) {
            $word = preg_replace("/(ая)$/i", 'ую', $word);
        }
        if (preg_match("/(я)$/i", $word)) {
            $word = preg_replace("/(я)$/i", 'ю', $word);
        }
        if (preg_match("/(аю)$/i", $word)) {
            $word = preg_replace("/(аю)$/i", 'ам', $word);
        }
        if (preg_match("/(и|ы)$/i", $word)) {
            $word = preg_replace("/(и|ы)$/i", 'у', $word);
        }
    }
    return $word;
}

//Сохранить контактные данные
if (isset($_POST['SaveUser'])) {
    $user->description = $_POST['UserName'];
    $user->end_date = $_POST['UserSecName'];
    $user->oname = $_POST['UserOName'];
    $user->email = $_POST['UserEmail'];
    $user->SaveUser();
}

//Сохранить пароль
if (isset($_POST['SavePassword'])) {
    if (($_POST['UserPassword'] == $_POST['UserPassword2']) && (strlen($_POST['UserPassword']) > 4)) {
        $password = md5(filter_var($_POST['UserPassword'], FILTER_SANITIZE_STRIPPED));
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('UPDATE k_users SET k_ku_password=:password WHERE k_ku_id=:user');
            $query->execute(array(':password' => $password, ':user' => $_SESSION['id']));
        } catch (PDOException $e) {
            exit();
        }
        $_SESSION['password'] = $password;
    }
}
if (isset($_POST['SaveExpertPassword'])) {
    if (($_POST['UserPassword'] == $_POST['UserPassword2']) && (strlen($_POST['UserPassword']) > 4)) {
        $password = md5(filter_var($_POST['UserPassword'], FILTER_SANITIZE_STRIPPED));
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('UPDATE k_experts SET k_e_password=:password WHERE k_e_id=:user');
            $query->execute(array(':password' => $password, ':user' => $_SESSION['id_e']));
        } catch (PDOException $e) {
            exit();
        }
        $_SESSION['password_e'] = $password;
    }
}
?>
