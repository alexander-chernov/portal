<?php

session_start();

require_once '../../inc/configs.php';
require_once 'classes.php';
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/class.phpmailer.php');

//////////////////////////////////// БЛОК ФУНКЦИЙ ////////////////////////////////////
/**
 * Сохранить изменения у пользователя
 * @param Int $id ID для поиска в базе
 * @param String $login Смена логина
 * @param String $password Смена пароля
 * @param String $fname Смена имени
 * @param String $lname Смена фамилии
 * @param String $email Смена E-mail
 * @return boolean Удалось или нет
 */
function SaveChangesUser($id, $login, $password, $fname, $lname, $email,$status=2) {
    $login = filter_var($login, FILTER_SANITIZE_STRIPPED);
    $password = filter_var($password, FILTER_SANITIZE_STRIPPED);
    $fname = filter_var($fname, FILTER_SANITIZE_STRIPPED);
    $lname = filter_var($lname, FILTER_SANITIZE_STRIPPED);
    $email = filter_var($email, FILTER_SANITIZE_STRIPPED);
    if ($status==1 || $status=0) {
        $status = filter_var($status, FILTER_VALIDATE_INT);
    }
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT * FROM k_users WHERE k_ku_login=:login or k_ku_email=:email');
        $query->execute(array(':login' => $login, ':email' => $email));
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        exit();
    }
    foreach ($result as $row) {
        if ($row['k_ku_id'] != $id) {
            echo 'Логин или E-mail уже занят!';
        }
    }
    if (!empty($password)) {
        if ($status==1 || $status=0) {
            $query = $mysql->prepare('UPDATE k_users SET k_ku_login=:login, k_ku_password=:password, k_ku_fname=:fname, k_ku_lname=:lname, k_ku_email=:email,k_ku_verified=:status WHERE k_ku_id=:id');
            $query->execute(array(':login' => $login, ':password' => md5($password), ':fname' => $fname, ':lname' => $lname, ':email' => $email, ':id' => $id,':status'=>$status));
        } else {
            $query = $mysql->prepare('UPDATE k_users SET k_ku_login=:login, k_ku_password=:password, k_ku_fname=:fname, k_ku_lname=:lname, k_ku_email=:email WHERE k_ku_id=:id');
            $query->execute(array(':login' => $login, ':password' => md5($password), ':fname' => $fname, ':lname' => $lname, ':email' => $email, ':id' => $id));
        }
    } else {
        if ($status==1 || $status=0) {
            $query = $mysql->prepare('UPDATE k_users SET k_ku_login=:login, k_ku_fname=:fname, k_ku_lname=:lname, k_ku_email=:email,k_ku_verified=:status WHERE k_ku_id=:id');
            $query->execute(array(':login' => $login, ':fname' => $fname, ':lname' => $lname, ':email' => $email, ':id' => $id,':status'=>$status));
        } else {
            $query = $mysql->prepare('UPDATE k_users SET k_ku_login=:login, k_ku_fname=:fname, k_ku_lname=:lname, k_ku_email=:email WHERE k_ku_id=:id');
            $query->execute(array(':login' => $login, ':fname' => $fname, ':lname' => $lname, ':email' => $email, ':id' => $id));
        }
    }
    if ($id == $_SESSION['id']) {
        $_SESSION['login'] = $login;
        if (!empty($password)) {
            $_SESSION['password'] = md5($password);
        }
    }
    echo 'Успешно сохранено';
}

/**
 * Создание нового администратора
 * @param String $login Логин
 * @param String $password Пароль
 * @param String $fname Имя
 * @param String $lname Фамилия
 * @param String $email Email
 * @return boolean Удалось или нет
 */
function NewAdminAdd($login, $password, $fname, $lname, $email) {
    $login = filter_var($login, FILTER_SANITIZE_STRIPPED);
    $password = filter_var($password, FILTER_SANITIZE_STRIPPED);
    $fname = filter_var($fname, FILTER_SANITIZE_STRIPPED);
    $lname = filter_var($lname, FILTER_SANITIZE_STRIPPED);
    $email = filter_var($email, FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT * FROM k_users WHERE k_ku_login=:login or k_ku_email=:email');
        $query->execute(array(':login' => $login, ':email' => $email));
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        exit();
    }
    if (count($result) > 0) {
        return false;
    } else {
        $query = $mysql->prepare('INSERT INTO k_users (k_ku_login, k_ku_password, k_ku_autor_date, k_ku_verified, k_ku_fname, k_ku_lname, k_ku_email, k_ku_last_date,k_u_privileges) VALUES (:login, :password, NOW(), 1, :fname, :lname, :email, NOW(),1)');
        $query->execute(array(':login' => $login, ':password' => md5($password), /*':date' => date("Y-m-d H:i:s"),*/ ':fname' => $fname, ':lname' => $lname, ':email' => $email/*, ':last_date' => date("Y-m-d H:i:s")*/));
        return true;
    }
}

/**
 * Создание нового пользователя
 * @param String $login Логин
 * @param String $password Пароль
 * @param String $fname Имя
 * @param String $lname Фамилия
 * @param String $email E-mail
 * @return boolean Удалось или нет
 */
function NewUserAdd($login, $password, $fname, $lname, $email) {
    $login = filter_var($login, FILTER_SANITIZE_STRIPPED);
    $password = filter_var($password, FILTER_SANITIZE_STRIPPED);
    $fname = filter_var($fname, FILTER_SANITIZE_STRIPPED);
    $lname = filter_var($lname, FILTER_SANITIZE_STRIPPED);
    $email = filter_var($email, FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT * FROM k_users WHERE k_ku_login=:login or k_ku_email=:email');
        $query->execute(array(':login' => $login, ':email' => $email));
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        exit();
    }
    if (count($result) > 0) {
        return false;
    } else {
        $query = $mysql->prepare('INSERT INTO k_users (k_ku_login, k_ku_password, k_ku_autor_date, k_ku_verified, k_ku_fname, k_ku_lname, k_ku_email, k_ku_last_date,k_u_privileges) VALUES (:login, :password, NOW(), 1, :fname, :lname, :email, NOW(),3)');
        $query->execute(array(':login' => $login, ':password' => md5($password), /*':date' => date("Y-m-d H:i:s"), */':fname' => $fname, ':lname' => $lname, ':email' => $email/*, ':last_date' => date("Y-m-d H:i:s")*/));
        return true;
    }
}

/**
 * Добавление нового модератора
 * @param String $login Логин
 * @param String $password Пароль
 * @param String $fname Имя
 * @param String $lname Фамилия
 * @param String $email E-mail
 * @return boolean Удалось или нет
 */
function NewModerAdd($login, $password, $fname, $lname, $email) {
    $login = filter_var($login, FILTER_SANITIZE_STRIPPED);
    $password = filter_var($password, FILTER_SANITIZE_STRIPPED);
    $fname = filter_var($fname, FILTER_SANITIZE_STRIPPED);
    $lname = filter_var($lname, FILTER_SANITIZE_STRIPPED);
    $email = filter_var($email, FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT * FROM k_users WHERE k_ku_login=:login or k_ku_email=:email');
        $query->execute(array(':login' => $login, ':email' => $email));
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        exit();
    }
    if (count($result) > 0) {
        return false;
    } else {
        $query = $mysql->prepare('INSERT INTO k_users (k_ku_login, k_ku_password, k_ku_autor_date, k_ku_verified, k_ku_fname, k_ku_lname, k_ku_email, k_ku_last_date,k_u_privileges) VALUES (:login, :password, NOW(), 1, :fname, :lname, :email, NOW(),2)');
        $query->execute(array(':login' => $login, ':password' => md5($password), /*':date' => date("Y-m-d H:i:s"), */':fname' => $fname, ':lname' => $lname, ':email' => $email/*, ':last_date' => date("Y-m-d H:i:s")*/));
        return true;
    }
}

/**
 * Добавление нового эксперта
 * @param String $login Логин (E-mail)
 * @param String $password Пароль
 * @return boolean Удалось или нет
 */
function NewExpertAdd($login, $password) {
    $login = filter_var($login, FILTER_SANITIZE_STRIPPED);
    $password = filter_var($password, FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT * FROM k_experts WHERE k_e_email=:login');
        $query->execute(array(':login' => $login));
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        exit();
    }
    if (count($result) == 0) {
        $query = $mysql->prepare('INSERT INTO k_experts (k_e_brief, k_e_email, k_e_password, k_e_description, k_e_verified, k_e_date) VALUES ("", :login, :password, "", 1, NOW())');
        $query->execute(array(':login' => $login, ':password' => md5($password)));
        return true;
    } else {
        return false;
    }
}

//////////////////////////////////// БЛОК ISSET ////////////////////////////////////
/**
 * Меняем информацию об админе
 */
if (isset($_POST['ChangeAdminPrefId'])) {
    SaveChangesUser($_POST['ChangeAdminPrefId'], $_POST['ChangeAdminPrefLogin'], $_POST['ChangeAdminPrefPass'], $_POST['ChangeAdminPrefName'], $_POST['ChangeAdminPrefSName'], $_POST['ChangeAdminPrefEmail']);
}

/**
 * Меняем информацию о пользователе
 */
if (isset($_POST['id_user_ch'])) {
    SaveChangesUser($_POST['id_user_ch'], $_POST['LoginUser'], $_POST['PasswordUser'], $_POST['NameUser'], $_POST['SecNameUser'], $_POST['EmailUser'], $_POST['StatusUser']);
}

//Ище IP по ID пользователя и блокуруем/разблокируем
if (isset($_GET['user_id_ip'])) {
    $_GET['user_id_ip'] = filter_var($_GET['user_id_ip'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_ku_last_ip FROM k_users WHERE k_ku_id=:id LIMIT 1');
        $query->execute(array(':id' => $_GET['user_id_ip']));
        $row = $query->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        exit();
    }
    $ip = $row['k_ku_last_ip'];
    $query2 = $mysql->prepare('SELECT * FROM k_users_ban_ip_list WHERE k_ubil_ip=:ip');
    $query2->execute(array(':ip' => $ip));
    $result2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    if (count($result2) > 0) {
        $query = $mysql->prepare('DELETE FROM k_users_ban_ip_list WHERE k_ubil_ip=:ip');
        $query->execute(array(':ip' => $ip));
        $output = array("src" => "../images/block_ip.png", "title" => "Заблокировать IP: " . $ip);
        echo json_encode($output);
    } else {
        $ban_days = 30;
        $query = $mysql->prepare('INSERT INTO k_users_ban_ip_list (k_ubil_ip,k_ubil_end_date) VALUES (:ip,NOW()+INTERVAL '.$ban_days.' DAY )');
        $query->execute(array(':ip' => $ip/*, ":date" => date('Y-m-d H:i:s', time() + 30 * 24 * 60 * 60)*/));
        $output = array("src" => "../images/unblock_ip.png", "title" => "Разблокировать IP: " . $ip);
        echo json_encode($output);
    }
}

//Ище пользователя по ID для установки/снятия бана
if (isset($_GET['user_id_block'])) {
    $_GET['user_id_block'] = filter_var($_GET['user_id_block'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT * FROM k_users WHERE k_ku_banned_forever=1 AND k_ku_id=:id');
        $query->execute(array(':id' => $_GET['user_id_block']));
        $result2 = $query->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        exit();
    }
    if (count($result2) > 0) {
        $query = $mysql->prepare('UPDATE k_users SET k_ku_banned_forever=0 WHERE k_ku_id=:id');
        $query->execute(array(':id' => $_GET['user_id_block']));
        $output = array("src" => "../images/disable.png", "title" => "Отключить пользователя");
        echo json_encode($output);
    } else {
        $query = $mysql->prepare('UPDATE k_users SET k_ku_banned_forever=1 WHERE k_ku_id=:id');
        $query->execute(array(':id' => $_GET['user_id_block']));
        $output = array("src" => "../images/enable.png", "title" => "Включить пользователя");
        echo json_encode($output);
    }
}

if (isset($_POST['DeleteUserTR'])) {
    //Удалить пользователя
    $_POST['DeleteUserTR'] = filter_var($_POST['DeleteUserTR'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('DELETE FROM k_users WHERE k_ku_id=:id AND (k_u_privileges=3 OR k_u_privileges=4)');
        $query->execute(array(':id' => $_POST['DeleteUserTR']));
    } catch (PDOException $e) {
        exit();
    }
    echo 'yes';
}

if (isset($_POST['DeleteModerTR'])) {
    //Удалить пользователя
    $_POST['DeleteModerTR'] = filter_var($_POST['DeleteModerTR'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('DELETE FROM k_users WHERE k_ku_id=:id AND k_u_privileges=2');
        $query->execute(array(':id' => $_POST['DeleteModerTR']));
    } catch (PDOException $e) {
        exit();
    }
    echo 'yes';
}

if (isset($_POST['DeleteAdminTR'])) {
    //Удалить пользователя
    $_POST['DeleteAdminTR'] = filter_var($_POST['DeleteAdminTR'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('DELETE FROM k_users WHERE k_ku_id=:id AND k_u_privileges=1');
        $query->execute(array(':id' => $_POST['DeleteAdminTR']));
    } catch (PDOException $e) {
        exit();
    }
    echo 'yes';
}

//Отправка почты админу
if (isset($_POST['EmailAdmin'])) {

    $mail             = new PHPMailer();
    $mail->IsSMTP(); // telling the class to use SMTP
    $mail->CharSet = 'UTF-8';
    try {
        //$mail->Host       = "tomsk-line.ru"; // SMTP server
        $mail->Host       = "192.168.151.141"; // SMTP server
        //$mail->Host       = "localhost"; // SMTP server
        /*
        $mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
                                                   // 1 = errors and messages
                                                   // 2 = messages only
        $mail->SMTPAuth   = true;                  // enable SMTP authentication
        $mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
        $mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
        $mail->Port       = 465;                   // set the SMTP port for the GMAIL server
        $mail->Username   = "tomsk.line.ru@gmail.com ";  // GMAIL username
        $mail->Password   = "Qwer1@34";            // GMAIL password
        */

        $mail->SetFrom('noreply@'._SERVER_ADDRESS, _SERVER_ADDRESS);
        $mail->AddAddress($_POST['EmailAdmin'], '');
        $mail->Subject    = $_POST['mailtheme'] . ' ' . date("Y-m-d H:i:s");
        $mail->AltBody    = $_POST['text_mail'];
        $mail->MsgHTML($_POST['text_mail']);
        $mail->Send();
        echo 'Письмо отправлено!';
    } catch (phpmailerException $e) {
        echo $e->errorMessage(); //Pretty error messages from PHPMailer
    } catch (Exception $e) {
        echo $e->getMessage(); //Boring error messages from anything else!
    }
    /*
    if (mb_send_mail($_POST['EmailAdmin'], $_POST['mailtheme'] . ' ' . date("Y-m-d H:i:s"), $_POST['text_mail'], "From: \"TOMSK-LINE.RU\"\n")) {
        echo 'Письмо отправлено!';
    } else {
        echo 'Возникла ошибка при отправке!';
    }
    */
}

//Создаём нового администратора
if (isset($_POST['NewAdminSubmit'])) {
    NewAdminAdd($_POST['NewAdminLogin'], $_POST['NewAdminPassword'], $_POST['NewAdminFName'], $_POST['NewAdminLName'], $_POST['NewAdminEmail']);
    $ShowParamID = 3;
}

//Смена логина и пароля в личном кабинете
if (isset($_POST['ChangePassCab'])) {
    if ($_POST['new_pass'] == $_POST['new_pass2']) {
        $login = filter_var($_POST['new_login'], FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_var($_POST['new_pass'], FILTER_SANITIZE_SPECIAL_CHARS);
        $password = md5($password);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
        } catch (PDOException $e) {
            exit();
        }
        if ($_SESSION['login'] != $login) {
            $query = $mysql->prepare('SELECT * FROM k_users WHERE k_ku_login=:login');
            $query->execute(array(':login' => $login));
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            if (count($result) > 0) {
                exit("Логин уже занят!");
            }
        }
        $_SESSION['login'] = $login;
        if ($_POST['new_pass'] == "") {
            $query = $mysql->prepare('SELECT * FROM k_users WHERE k_ku_login=:login');
            $query->execute(array(':login' => $login, ':id' => $_SESSION['id']));
        } else {
            $query = $mysql->prepare('UPDATE k_users SET k_ku_login=:login, k_ku_password=:password WHERE k_ku_id=:id');
            $query->execute(array(':login' => $login, ':password' => $password, ':id' => $_SESSION['id']));
            $_SESSION['password'] = $password;
        }
        $res = "Информация обновлена!";
    } else {
        $res = "Пароли не совпадают!";
    }
    echo $res;
    $ShowParamID = 7;
}

//Редактирование модератора
if (isset($_POST['ModeratorChange'])) {
    SaveChangesUser($_POST['id_moder_ch'], $_POST['ModerLogin'], $_POST['ModerPassword'], $_POST['ModerName'], $_POST['ModerSecName'], $_POST['ModerEmail']);
    $_POST['id_moder_ch'] = filter_var($_POST['id_moder_ch'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('DELETE FROM k_users_categories_links WHERE k_ucl_user_id=:id');
        $query->execute(array(':id' => $_POST['id_moder_ch']));
    } catch (PDOException $e) {
        exit();
    }
    if (isset($_POST['mod'])) {
        for ($i = 0; $i < count($_POST['mod']); $i++) {
            $query = $mysql->prepare('INSERT INTO k_users_categories_links (k_ucl_user_id,k_ucl_cat_id) VALUES (:id,:mod)');
            $query->execute(array(':id' => $_POST['id_moder_ch'], ':mod' => $_POST['mod'][$i]));
        }
    }
    $ShowParamID = 4;
}

//Создать нового модератора
if (isset($_POST['NewModeratorCreate'])) {
    NewModerAdd($_POST['NewModerLogin'], $_POST['NewModerPassword'], $_POST['NewModerName'], $_POST['NewModerSecName'], $_POST['NewModerEmail']);
    $_POST['NewModerLogin'] = filter_var($_POST['NewModerLogin'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_ku_id FROM k_users WHERE k_ku_login=:login LIMIT 1');
        $query->execute(array(':login' => $_POST['NewModerLogin']));
        $row1 = $query->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        exit();
    }
    if (isset($_POST['moda'])) {
        for ($i = 0; $i < count($_POST['moda']); $i++) {
            $query = $mysql->prepare('INSERT INTO k_users_categories_links (k_ucl_user_id,k_ucl_cat_id) VALUES (:id,:mod)');
            $query->execute(array(':id' => $row1['k_ku_id'], ':mod' => $_POST['moda'][$i]));
        }
    }
    $ShowParamID = 4;
}

//Создание нового пользователя
if (isset($_POST['UserAdd'])) {
    if (NewUserAdd($_POST['UserLoginAdd'], $_POST['UserPasswordAdd'], $_POST['UserNameAdd'], $_POST['UserSecNameAdd'], $_POST['UserEmailAdd'])) {
        echo 'Пользователь успешно создан!';
    } else {
        echo 'Логин или E-mail уже занят!';
    }
    $ShowParamID = 6;
}

//Проверка доступности логина function CheckUserAvailability(idname)
if (isset($_POST['user_name'])) {
    $user_name = filter_var($_POST['user_name'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_ku_login FROM k_users WHERE k_ku_login=:login');
        $query->execute(array(':login' => $user_name));
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        exit();
    }
    if (count($result) > 0) {
        echo 'no';
    } else {
        echo 'yes';
    }
}

//Проверка доступности E-mail function CheckEmailAvailability(idname)
if (isset($_POST['user_email'])) {
    $user_email = filter_var($_POST['user_email'], FILTER_SANITIZE_EMAIL);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_ku_email FROM k_users WHERE k_ku_email=:email');
        $query->execute(array(':email' => $user_email));
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        exit();
    }
    if (count($result) > 0) {
        echo 'no';
    } else {
        echo 'yes';
    }
}

//Проверка категорий модератора function ModerCategoriesCheck(i)
if (isset($_POST['moder_id'])) {
    if (settype($_POST['moder_id'], "integer")) {
        $mod = new TableModeratorBuild();
        $mod->CategoriesLoad();
        echo $mod->ListGenerate($_POST['moder_id']);
    } else {
        echo 'Возникла ошибка данных!';
    }
}

//Показать окно редактирования модератора function ChangeModerParams(id)
if (isset($_POST['moder_show'])) {
    $_POST['moder_show'] = filter_var($_POST['moder_show'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_ku_login as login, k_ku_fname as name, k_ku_lname as secname, k_ku_email as email FROM k_users WHERE k_ku_id=:id AND k_u_privileges=2 LIMIT 1');
        $query->execute(array(':id' => $_POST['moder_show']));
        $row = $query->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        exit();
    }
    echo json_encode($row);
}

//Проверка категорий эксперта function ExpertCategoriesCheck(i)
if (isset($_POST['expert_id'])) {
    $ex = new TableExpertsBuild();
    $ex->LoadCategories();
    echo $ex->CompareCategories($_POST['expert_id']);
}

//Показать окно редактирования пользователя function ChangeBlockUParams(id)
if (isset($_POST['user_show'])) {
    $_POST['user_show'] = filter_var($_POST['user_show'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_ku_login as login, k_ku_fname as name, k_ku_lname as secname, k_ku_email as email, k_ku_verified as user_status FROM k_users WHERE k_ku_id=:id AND (k_u_privileges=3 OR k_u_privileges=4) LIMIT 1');
        $query->execute(array(':id' => $_POST['user_show']));
        $row = $query->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        exit();
    }
    echo json_encode($row);
}

//Показать окно редактирования администратора function ChangeBlockCParams(id)
if (isset($_POST['ChangeBlockCParams'])) {
    $_POST['ChangeBlockCParams'] = filter_var($_POST['ChangeBlockCParams'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_ku_id as id, k_ku_login as login, k_ku_fname as fname, k_ku_lname as lname, k_ku_email as email FROM k_users WHERE k_ku_id=:id LIMIT 1');
        $query->execute(array(':id' => $_POST['ChangeBlockCParams']));
        $row = $query->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        exit();
    }
    echo json_encode($row);
}

//Показать окно отправки E-mail function ChangeBlockEParams(id)
if (isset($_POST['EmailChange'])) {
    $_POST['EmailChange'] = filter_var($_POST['EmailChange'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_ku_email FROM k_users WHERE k_ku_id=:id LIMIT 1');
        $query->execute(array(':id' => $_POST['EmailChange']));
        $row = $query->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        exit();
    }
    echo $row['k_ku_email'];
}

//BANNER SECTION
if (isset($_POST['banner_id'])) {
    $banners = new BannersAll($_POST['banner_id']);
    echo $banners->banner_code[0];
}
if (isset($_POST['banner_change_id'])) {
    $_POST['banner_change_id'] = filter_var($_POST['banner_change_id'], FILTER_VALIDATE_INT);
    $code = $_POST['banner_change_code'];
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('UPDATE k_all_banners SET k_ab_code=:code WHERE k_ab_id=:id');
        $query->execute(array(":code" => $code, ":id" => $_POST['banner_change_id']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['BannerImmoIDInfo'])) {
    $_POST['BannerImmoIDInfo'] = filter_var($_POST['BannerImmoIDInfo'], FILTER_VALIDATE_INT);
    $banner = new BannersAll($_POST['BannerImmoIDInfo']);
    echo '<tr>
        <td><p class="style_2">Организация:</p></td>
        <td><input id="BannerInfoOrganization" type="text" value="' . $banner->banner_organization[0] . '"></td>
        </tr>
        <tr>
        <td><p class="style_2">Имя:</p></td>
        <td><input id="BannerInfoContactName" type="text" value="' . $banner->banner_contact_name[0] . '"></td>
        </tr>
        <tr>
        <td><p class="style_2">Контакт:</p></td>
        <td><input id="BannerInfoContacts" type="text" value="' . $banner->banner_contacts[0] . '"></td>
        </tr>
        <tr>
        <td colspan="2"><button onclick="ChangeBannerInfo(' . $_POST['BannerImmoIDInfo'] . ');" style="float:right;">Изменить</button></td>
        </tr>';
}
if (isset($_POST['BannerImmoIDChange'])) {
    $_POST['BannerImmoIDChange'] = filter_var($_POST['BannerImmoIDChange'], FILTER_VALIDATE_INT);
    $_POST['BannerImmoChangeOrganization'] = filter_var($_POST['BannerImmoChangeOrganization'], FILTER_SANITIZE_STRIPPED);
    $_POST['BannerImmoChangeContactName'] = filter_var($_POST['BannerImmoChangeContactName'], FILTER_SANITIZE_STRIPPED);
    $_POST['BannerImmoChangeContacts'] = filter_var($_POST['BannerImmoChangeContacts'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('UPDATE k_all_banners SET k_ab_organization=:org, k_ab_contact_name=:name, k_ab_contacts=:contacts WHERE k_ab_id=:id');
        $query->execute(array(":org" => $_POST['BannerImmoChangeOrganization'],
            ":name" => $_POST['BannerImmoChangeContactName'],
            ":contacts" => $_POST['BannerImmoChangeContacts'],
            ":id" => $_POST['BannerImmoIDChange']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['BannerAddDaysLast'])) {
    $banners = new BannersAll($_POST['BannerAddDaysLast']);
    echo $banners->banner_end_days[0];
}
if (isset($_POST['BannersAddDaysSubmit'])) {
    $_POST['BannersAddDaysSubmit'] = filter_var($_POST['BannersAddDaysSubmit'], FILTER_VALIDATE_INT);
    //$end_date = date('Y-m-d H:i:s', time() + $_POST['BannersAddDaysPlus'] * 24 * 60 * 60);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('UPDATE k_all_banners SET k_ab_end_date=NOW() + INTERVAL :date DAY  WHERE k_ab_id=:id');
        $query->execute(array(":date" => $_POST['BannersAddDaysPlus'], ":id" => $_POST['BannersAddDaysSubmit']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['RemoveIP'])) {
    $_POST['RemoveIP'] = filter_var($_POST['RemoveIP'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('DELETE FROM k_users_ban_ip_list WHERE k_ubil_id=:id');
        $query->execute(array(":id" => $_POST['RemoveIP']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['AddBannedIP'])) {
    $_POST['AddBannedIP'] = filter_var($_POST['AddBannedIP'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query0 = $mysql->prepare('SELECT * FROM k_users_ban_ip_list WHERE k_ubil_ip=:ip');
        $query0->execute(array(":ip" => $_POST['AddBannedIP']));
        if ($query0->rowCount() == 0) {
            $query = $mysql->prepare('INSERT INTO k_users_ban_ip_list (k_ubil_ip) VALUES (:ip)');
            $query->execute(array(":ip" => $_POST['AddBannedIP']));
            echo 'yes';
        }
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['showTextContent'])) {
    $_POST['showTextContent'] = filter_var($_POST['showTextContent'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query0 = $mysql->prepare('SELECT k_cp_text FROM k_content_pages WHERE k_cp_id=:id LIMIT 1');
        $query0->execute(array(":id" => $_POST['showTextContent']));
        $result = $query0->fetch(PDO::FETCH_ASSOC);
        echo $result['k_cp_text'];
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['SaveContentPageID'])) {
    $_POST['SaveContentPageID'] = filter_var($_POST['SaveContentPageID'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query0 = $mysql->prepare('UPDATE k_content_pages SET k_cp_text=:text WHERE k_cp_id=:id');
        $query0->execute(array(":id" => $_POST['SaveContentPageID'], ":text" => $_POST['SaveContentPageText']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['SaveGradient1'])) {
    $_POST['SaveGradient1'] = filter_var($_POST['SaveGradient1'], FILTER_SANITIZE_STRIPPED);
    $_POST['SaveGradient2'] = filter_var($_POST['SaveGradient2'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query0 = $mysql->prepare('UPDATE k_colours SET k_c_value=:val WHERE k_c_id=:id');
        $query0->execute(array(":id" => 1, ":val" => $_POST['SaveGradient1']));
        $query0->execute(array(":id" => 2, ":val" => $_POST['SaveGradient2']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['SaveColorsC'])) {
    $_POST['SaveColorsC'] = filter_var($_POST['SaveColorsC'], FILTER_SANITIZE_STRIPPED);
    $_POST['SaveColorsID'] = filter_var($_POST['SaveColorsID'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query0 = $mysql->prepare('UPDATE k_colours SET k_c_value=:val WHERE k_c_id=:id');
        $query0->execute(array(":id" => $_POST['SaveColorsID'], ":val" => $_POST['SaveColorsC']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['SaveTariffPacketID'])) {
    $_POST['SaveTariffPacketID'] = filter_var($_POST['SaveTariffPacketID'], FILTER_VALIDATE_INT);
    $_POST['SaveTariffPacketLockDays'] = filter_var($_POST['SaveTariffPacketLockDays'], FILTER_VALIDATE_INT);
    $_POST['SaveTariffPacketUp'] = filter_var($_POST['SaveTariffPacketUp'], FILTER_VALIDATE_INT);
    $_POST['SaveTariffPacketColor'] = filter_var($_POST['SaveTariffPacketColor'], FILTER_VALIDATE_INT);
    $_POST['SaveTariffPacketVIP'] = filter_var($_POST['SaveTariffPacketVIP'], FILTER_VALIDATE_INT);
    $_POST['SaveTariffPacketPrice'] = filter_var($_POST['SaveTariffPacketPrice'], FILTER_VALIDATE_FLOAT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query0 = $mysql->prepare('UPDATE k_tariff_packages
            SET k_tp_lock_days=:lock_days, k_tp_up=:up, k_tp_color=:color, k_tp_vip=:vip, k_tp_price=:price
            WHERE k_tp_id=:id');
        $query0->execute(array(":id" => $_POST['SaveTariffPacketID'],
            ":lock_days" => $_POST['SaveTariffPacketLockDays'],
            ":up" => $_POST['SaveTariffPacketUp'],
            ":color" => $_POST['SaveTariffPacketColor'],
            ":vip" => $_POST['SaveTariffPacketVIP'],
            ":price" => $_POST['SaveTariffPacketPrice']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['SaveTariffPriceForAdID'])) {
    $_POST['SaveTariffPriceForAdID'] = filter_var($_POST['SaveTariffPriceForAdID'], FILTER_VALIDATE_INT);
    $_POST['SaveTariffPriceForAdDays'] = filter_var($_POST['SaveTariffPriceForAdDays'], FILTER_VALIDATE_INT);
    $_POST['SaveTariffPriceForAdPrice'] = filter_var($_POST['SaveTariffPriceForAdPrice'], FILTER_VALIDATE_FLOAT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query0 = $mysql->prepare('UPDATE k_tariff_prices_for_ad
            SET k_tpfa_days=:days, k_tpfa_price=:price
            WHERE k_tpfa_id=:id');
        $query0->execute(array(":id" => $_POST['SaveTariffPriceForAdID'],
            ":days" => $_POST['SaveTariffPriceForAdDays'],
            ":price" => $_POST['SaveTariffPriceForAdPrice']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['SaveTariffOthersID'])) {
    $_POST['SaveTariffOthersID'] = filter_var($_POST['SaveTariffOthersID'], FILTER_VALIDATE_INT);
    $_POST['SaveTariffOthersPrice'] = filter_var($_POST['SaveTariffOthersPrice'], FILTER_VALIDATE_FLOAT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query0 = $mysql->prepare('UPDATE k_tariff_other
            SET k_to_price=:price
            WHERE k_to_id=:id');
        $query0->execute(array(":id" => $_POST['SaveTariffOthersID'],
            ":price" => $_POST['SaveTariffOthersPrice']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['SaveTariffVideoID'])) {
    $_POST['SaveTariffVideoID'] = filter_var($_POST['SaveTariffVideoID'], FILTER_VALIDATE_INT);
    $_POST['SaveTariffVideoPrice'] = filter_var($_POST['SaveTariffVideoPrice'], FILTER_VALIDATE_FLOAT);
    $_POST['SaveTariffVideoDuration'] = filter_var($_POST['SaveTariffVideoDuration'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query0 = $mysql->prepare('UPDATE k_tariff_video
            SET k_tv_price=:price, k_tv_duration=:dur
            WHERE k_tv_id=:id');
        $query0->execute(array(":id" => $_POST['SaveTariffVideoID'],
            ":price" => $_POST['SaveTariffVideoPrice'],
            ":dur" => $_POST['SaveTariffVideoDuration']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['SaveNewPacketID'])) {
    $_POST['SaveNewPacketID'] = filter_var($_POST['SaveNewPacketID'], FILTER_VALIDATE_INT);
    $_POST['SaveNewPacketType'] = filter_var($_POST['SaveNewPacketType'], FILTER_SANITIZE_STRIPPED);
    $_POST['SaveNewPacketValue'] = filter_var($_POST['SaveNewPacketValue'], FILTER_VALIDATE_FLOAT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        if ($_POST['SaveNewPacketType'] == 'price') {
            $query0 = $mysql->prepare('UPDATE k_tariff_packets_attrs
                SET k_tpa_price=:val WHERE k_tpa_id=:id');
        }
        if ($_POST['SaveNewPacketType'] == 'attr') {
            $query0 = $mysql->prepare('UPDATE k_tariff_packets_attrs
                SET k_tpa_int=:val WHERE k_tpa_id=:id');
        }
        $query0->execute(array(":id" => $_POST['SaveNewPacketID'],
            ":val" => $_POST['SaveNewPacketValue']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
?>