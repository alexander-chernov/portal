<?php

session_start();

require_once '../../inc/configs.php';
require_once 'classes.php';
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/class.phpmailer.php');

//Листаем страницы
if (isset($_GET['PageIndex'])) {
    $page = filter_var($_GET['PageIndex'], FILTER_VALIDATE_INT);
}

//Отправка почты
if (isset($_POST['EmailAdmin'])) {
    $_POST['EmailAdmin'] = filter_var($_POST['EmailAdmin'], FILTER_SANITIZE_EMAIL);
    $_POST['mailtheme'] = filter_var($_POST['mailtheme'], FILTER_SANITIZE_STRIPPED);
    $_POST['text_mail'] = filter_var($_POST['text_mail'], FILTER_SANITIZE_STRIPPED);
    $mail             = new PHPMailer();
    $mail->CharSet = 'UTF-8';
    $mail->IsSMTP(); // telling the class to use SMTP
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

if (isset($_POST['ShowInfo'])) {
    $_POST['ShowInfo'] = filter_var($_POST['ShowInfo'], FILTER_VALIDATE_INT);
    $info_job = new JobAds();
    $info_job->LoadAds(0, 0, array(array('k_j_id'), array(':j_id'), array(''), array($_POST['ShowInfo'])));
    if ($info_job->salary_min[0]) {
        $info_job->salary_min[0] = 'от ' . $info_job->salary_min[0] . ' ' . $info_job->currency_str[0] . '/мес';
    } else {
        $info_job->salary_min[0] = '';
    }
    if ($info_job->salary_max[0]) {
        $info_job->salary_max[0] = 'до ' . $info_job->salary_max[0] . ' ' . $info_job->currency_str[0] . '/мес';
    } else {
        $info_job->salary_max[0] = '';
    }
    if ($info_job->age_min[0]) {
        $info_job->age_min[0] = 'от ' . $info_job->age_min[0] . ' лет';
    } else {
        $info_job->age_min[0] = '';
    }
    if ($info_job->age_max[0]) {
        $info_job->age_max[0] = 'до ' . $info_job->age_max[0] . ' лет';
    } else {
        $info_job->age_max[0] = '';
    }
    switch ($info_job->sex[0]) {
        case 0: $info_job->sex[0] = 'Не указан';
            break;
        case 1: $info_job->sex[0] = 'Мужской';
            break;
        case 2: $info_job->sex[0] = 'Женский';
            break;
    }
    if($info_job->education_t[0] == 0) {
        $info_job->education_t_str[0] = 'Не указано';
    }
    echo json_encode(array("salary_min" => $info_job->salary_min[0],
        "salary_max" => $info_job->salary_max[0],
        "age_min" => $info_job->age_min[0],
        "age_max" => $info_job->age_max[0],
        "sex" => $info_job->sex[0],
        "education_t" => $info_job->education_t_str[0],
        "education" => $info_job->education[0],
        "experience" => $info_job->exp[0],
        "schedule" => $info_job->schedule[0],
        "req_text" => $info_job->text[0],
        "organization" => $info_job->organization_name[0],
        "contact_name" => $info_job->contact_name[0],
        "contact_phone" => $info_job->contact_phone[0],
        "email" => $info_job->email[0]
    ));
}

if (isset($_POST['DisEn'])) {
    $_POST['DisEn'] = filter_var($_POST['DisEn'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare('SELECT k_j_state FROM k_job WHERE k_j_id=:id');
        $queue->execute(array(":id" => $_POST['DisEn']));
        $result = $queue->fetch(PDO::FETCH_ASSOC);
        if ($result['k_j_state'] == 1) {
            $output = 'hide';
            $newstate = 0;
        } else {
            $newstate = 1;
            $output = 'show';
        }
        $queue2 = $mysql->prepare('UPDATE k_job SET k_j_state=:state WHERE k_j_id=:id');
        $queue2->execute(array(":id" => $_POST['DisEn'], ":state" => $newstate));
        echo $output;
    } catch (PDOException $e) {
        echo 'no';
        exit();
    }
}

if (isset($_POST['UpJob'])) {
    $_POST['UpJob'] = filter_var($_POST['UpJob'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare('UPDATE k_job SET k_j_up_date=NOW() WHERE k_j_id=:id');
        $queue->execute(array(":id" => $_POST['UpJob']));
        echo 'yes';
    } catch (PDOException $e) {
        echo 'no';
        exit();
    }
}

if (isset($_POST['DeleteJob'])) {
    $_POST['DeleteJob'] = filter_var($_POST['DeleteJob'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare('DELETE FROM k_job WHERE k_j_id=:id');
        $queue->execute(array(":id" => $_POST['DeleteJob']));
        echo 'yes';
    } catch (PDOException $e) {
        echo 'no';
        exit();
    }
}

if (isset($_GET['Search'])) {
    $_GET['SearchNum'] = filter_var($_GET['SearchNum'], FILTER_VALIDATE_INT);
    $_GET['SearchState'] = filter_var($_GET['SearchState'], FILTER_VALIDATE_INT);
    $columns = array();
    $vars = array();
    $conditions = array();
    $values = array();
    $link = '';
    $where_str = '';
    if (!empty($_GET['SearchNum'])) {
        array_push($columns, 'k_j_id');
        array_push($vars, ':var1');
        array_push($values, $_GET['SearchNum']);
        $link .= '&SearchNum=' . $_GET['SearchNum'];
    }
    if (!empty($_GET['SearchState'])) {
        array_push($columns, 'k_j_state');
        array_push($vars, ':var2');
        if ($_GET['SearchState'] == 1) {
            array_push($values, 1);
            $link .= '&SearchState=1';
        }
        if ($_GET['SearchState'] == 2) {
            array_push($values, 0);
            $link .= '&SearchState=0';
        }
    }
    if (!empty($_GET['PageType'])) {
        array_push($columns, 'k_j_type');
        array_push($vars, ':type');
        array_push($values, $typej);
        $link .= '&PageType=' . $_GET['PageType'];
    }
    for ($i = 1; $i < count($columns); $i++) {
        array_push($conditions, 'AND');
    }
    array_push($conditions, '');
    if (count($columns) > 0) {
        $where_str .= 'WHERE ';
        for ($i = 0; $i < count($columns); $i++) {
            $where_str .= ' ' . $columns[$i] . '="' . $values[$i] . '" ' . $conditions[$i];
        }
    }
    $where = array($columns, $vars, $conditions, $values);
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
        $query = $mysql->prepare('UPDATE k_all_banners SET k_ab_end_date=NOW() + INTERVAL :date DAY WHERE k_ab_id=:id');
        $query->execute(array(":date" => $_POST['BannersAddDaysPlus'], ":id" => $_POST['BannersAddDaysSubmit']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
?>