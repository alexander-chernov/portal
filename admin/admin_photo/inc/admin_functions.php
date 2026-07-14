<?php

//error_reporting('E_ALL');
//define('TOMSKLINE', 1);

require_once '../../inc/configs.php';
require_once 'classes.php';
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/class.phpmailer.php');

//Листаем страницы
if (isset($_GET['PageIndex'])) {
    $page = filter_var($_GET['PageIndex'], FILTER_VALIDATE_INT);
}
//Меняем категории
if (isset($_GET['PageType'])) {
    $ShowParamID = filter_var($_GET['PageType'], FILTER_VALIDATE_INT);
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
        $mail->Host       = "smtp.email.com"; // SMTP server (example: smtp.email.com)
        /*
        $mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
                                                   // 1 = errors and messages
                                                   // 2 = messages only
        $mail->SMTPAuth   = true;                  // enable SMTP authentication
        $mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
        $mail->Host       = "smtp.email.com";      // sets email as the SMTP server
        $mail->Port       = 465;                   // set the SMTP port for the email server (example: 465)
        $mail->Username   = "email@email.ru ";     // email username (example: email@email.ru)
        $mail->Password   = "password";            // email password (example: password)
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
/*    if (mb_send_mail($_POST['EmailAdmin'], $_POST['mailtheme'] . ' ' . date("Y-m-d H:i:s"), $_POST['text_mail'], "From: \"TOMSK-LINE.RU\"\n")) {
        echo 'Письмо отправлено!';
    } else {
        echo 'Возникла ошибка при отправке!';
    } */
}

//Показать окно отправки E-mail
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

//Показать окно информации
if (isset($_POST['InfoBlock'])) {
    $_POST['InfoBlock'] = filter_var($_POST['InfoBlock'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT ku.k_ku_fname as fname, ku.k_ku_lname as lname, ku.k_ku_oname as oname, ku.k_ku_email as email,
            kp.k_pd_phone as contacts
            FROM k_photodesk AS kp
            LEFT JOIN k_users AS ku ON (ku.k_ku_id = kp.k_pd_user_id)
            WHERE k_pd_id=:id
            LIMIT 1');
        $query->execute(array(':id' => $_POST['InfoBlock']));
        $row = $query->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        exit();
    }
    echo json_encode($row);
}

//Редактирование объявления
if (isset($_POST['EditAd'])) {
    $_POST['EditAd'] = filter_var($_POST['EditAd'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_pd_theme as theme,k_pd_category as category,k_pd_text as text
            FROM k_photodesk
            WHERE k_pd_id=:id
            LIMIT 1');
        $query->execute(array(':id' => $_POST['EditAd']));
        $row = $query->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        exit();
    }
    echo json_encode($row);
}

//Сохранить объявления
if (isset($_POST['SaveAdId'])) {
    $_POST['SaveAdId'] = filter_var($_POST['SaveAdId'], FILTER_VALIDATE_INT);
    $_POST['SaveAdTheme'] = filter_var($_POST['SaveAdTheme'], FILTER_SANITIZE_STRIPPED);
    $_POST['SaveAdCategory'] = filter_var($_POST['SaveAdCategory'], FILTER_VALIDATE_INT);
    $_POST['SaveAdText'] = filter_var($_POST['SaveAdText'], FILTER_SANITIZE_STRIPPED, FILTER_FLAG_NO_ENCODE_QUOTES);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('UPDATE k_photodesk SET k_pd_theme=:theme, k_pd_category=:category, k_pd_text=:text WHERE k_pd_id=:id');
        $query->execute(array(':id' => $_POST['SaveAdId'],
            ':theme' => $_POST['SaveAdTheme'],
            ':category' => $_POST['SaveAdCategory'],
            ':text' => $_POST['SaveAdText']));
        echo 'Успешно выполнено!';
    } catch (PDOException $e) {
        exit();
    }
}

//Сохранить объявления
if (isset($_POST['PhotosPanel'])) {
    $_POST['PhotosPanel'] = filter_var($_POST['PhotosPanel'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_pdp_id as id,k_pdp_link as link FROM k_photodesk_photos WHERE k_pdp_ad_id=:id');
        $query->execute(array(':id' => $_POST['PhotosPanel']));
        $row = $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        exit();
    }
    echo json_encode($row);
}

//Удалить фото
if (isset($_POST['DeletePhoto'])) {
    $_POST['DeletePhoto'] = filter_var($_POST['DeletePhoto'], FILTER_SANITIZE_STRIPPED);
    if (preg_match('/(video)/', $_POST['DeletePhoto'])) {
        $del_img = '../../../' . $_POST['DeletePhoto'];
    } else {
        $del_img = '../../' . $_POST['DeletePhoto'];
    }
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('DELETE FROM k_photodesk_photos WHERE k_pdp_link=:link');
        $query->execute(array(':link' => $_POST['DeletePhoto']));
        unlink($del_img);
        $format = end(explode('.', $del_img));
        $del_img = str_replace($format, 'flv', $del_img);
        unlink($del_img);
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['PhotoMainPage'])) {
    $_POST['PhotoMainPage'] = filter_var($_POST['PhotoMainPage'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query0 = $mysql->prepare('SELECT k_pd_main_page FROM k_photodesk WHERE k_pd_id=:id');
        $query0->execute(array(':id' => $_POST['PhotoMainPage']));
        $result0 = $query0->fetch(PDO::FETCH_ASSOC);
        $new_state = 0;
        if ($result0['k_pd_main_page'] == 0) {
            $new_state = 1;
        }
        $query = $mysql->prepare('UPDATE k_photodesk SET k_pd_main_page=:state WHERE k_pd_id=:id');
        $query->execute(array(':id' => $_POST['PhotoMainPage'], ":state" => $new_state));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}

//Показать комментарии
if (isset($_POST['ShowComments'])) {
    $com = new PhotoComments();
    $com->LoadComments($_POST['ShowComments']);
    $print = '<tr style="background: #7caed3;">
        <td><p class="style_5">Пользователь</p></td>
        <td><p class="style_5">Текст</p></td>
        <td><p class="style_5">Дата</p></td>
        <td><p class="style_5">Действие</p></td>
        </tr>';
    for ($i = 1; $i <= count($com->getId(0)); $i++) {
        $print .= '<tr id="com_n' . $com->getId($i) . '" style="background: #f0f4f4;">';
        if ($com->getUserId($i)) {
            $print .= '<td><p class="style_4">' . $com->getUserStr($i) . '</p></td>';
        } else {
            $print .= '<td><p class="style_4">Гость</p></td>';
        }
        $print .= '<td><textarea rows="5" cols="40" style="resize: none;">' . $com->getText($i) . '</textarea></td>';
        $print .= '<td><p class="style_4">' . $com->getDate($i) . '</p></td>';
        $print .= '<td><a><img src="../images/delete.png" onclick="DeleteComment(' . $com->getId($i) . ')" title="Удалить коментарий" alt=""></a></td></tr>';
    }
    echo $print;
    $com = NULL;
}

//Удалить комментарий
if (isset($_POST['DeleteComment'])) {
    $com = new PhotoComments();
    if ($com->DeleteComment($_POST['DeleteComment'])) {
        echo 'yes';
    } else {
        echo 'no';
    }
    $com = NULL;
}

//VIP лента
if (isset($_POST['CommentVIP'])) {
    $com = new PhotoAds();
    if ($com->PhotoVIP($_POST['CommentVIP'], $_POST['CommentAct'])) {
        echo 'yes';
    } else {
        echo 'no';
    }
    $com = NULL;
}

//Платная лента
if (isset($_POST['CommentPaid'])) {
    $com = new PhotoAds();
    if ($com->PhotoPaid($_POST['CommentPaid'], $_POST['CommentPAct'])) {
        echo 'yes';
    } else {
        echo 'no';
    }
    $com = NULL;
}

//Поднять объявление
if (isset($_POST['PhotoUp'])) {
    $com = new PhotoAds();
    if ($com->PhotoUp($_POST['PhotoUp'])) {
        echo 'yes';
    } else {
        echo 'no';
    }
    $com = NULL;
}

//Поднять объявление
if (isset($_POST['BlockIP'])) {
    $com = new PhotoAds();
    $res = $com->BlockIP($_POST['BlockIP']);
    echo $res;
    $com = NULL;
}

//Удалить объявление
if (isset($_POST['DeleteAd'])) {
    $com = new PhotoAds();
    if ($com->DeleteAd($_POST['DeleteAd'])) {
        echo 'yes';
    } else {
        echo 'no';
    }
    $com = NULL;
}

//Поиск объявлений
if (isset($_GET['Search'])) {
    $_GET['SearchNum'] = filter_var($_GET['SearchNum'], FILTER_VALIDATE_INT);
    $_GET['SearchName'] = filter_var($_GET['SearchName'], FILTER_SANITIZE_STRIPPED);
    $_GET['SearchCategory'] = filter_var($_GET['SearchCategory'], FILTER_VALIDATE_INT);
    for ($i = 0; $i < count($_GET['Types']); $i++) {
        $_GET['Types'][$i] = filter_var($_GET['Types'][$i], FILTER_VALIDATE_INT);
    }
    $columns = array();
    $vars = array();
    $conditions = array();
    $values = array();
    $link = '';
    $where_str = '';
    if (!empty($_GET['SearchNum'])) {
        array_push($columns, 'k_pd_id');
        array_push($vars, ':var1');
        array_push($values, $_GET['SearchNum']);
        $link .= '&SearchNum=' . $_GET['SearchNum'];
    }
    if (!empty($_GET['SearchName'])) {
        array_push($columns, 'k_ku_login');
        array_push($vars, ':var2');
        array_push($values, $_GET['SearchName']);
        $link .= '&SearchName=' . $_GET['SearchName'];
    }
    if ($_GET['SearchCategory'] != 0) {
        array_push($columns, 'k_pd_category');
        array_push($vars, ':var3');
        array_push($values, $_GET['SearchCategory']);
        $link .= '&SearchCategory=' . $_GET['SearchCategory'];
    }
    if (in_array(1, $_GET['Types'])) {
        if (in_array(2, $_GET['Types'])) {
            array_push($conditions, 'OR');
            array_push($columns, '(k_pd_vip');
        } else {
            array_push($columns, 'k_pd_vip');
        }
        array_push($vars, ':adv1');
        array_push($values, '1');
        $link .= '&Types[]=1';
    }
    if (in_array(2, $_GET['Types'])) {
        array_push($columns, 'k_pd_paid');
        array_push($vars, ':adv2');
        array_push($values, '1');
        if (in_array(1, $_GET['Types'])) {
            array_push($conditions, ')');
        }
        $link .= '&Types[]=2';
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

if (isset($_POST['EditCategory'])) {
    $_POST['EditCategory'] = filter_var($_POST['EditCategory'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_pdc_name FROM k_photodesk_categories WHERE k_pdc_id=:id');
        $query->execute(array(':id' => $_POST['EditCategory']));
        $result = $query->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        exit();
    }
    echo $result['k_pdc_name'];
}

if (isset($_POST['EditCategoryName'])) {
    $_POST['EditCategoryID'] = filter_var($_POST['EditCategoryID'], FILTER_VALIDATE_INT);
    $_POST['EditCategoryName'] = filter_var($_POST['EditCategoryName'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('UPDATE k_photodesk_categories SET k_pdc_name=:name WHERE k_pdc_id=:id');
        $query->execute(array(':id' => $_POST['EditCategoryID'], ':name' => $_POST['EditCategoryName']));
        if ($query) {
            echo 'yes';
        } else {
            echo 'no';
        }
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['AddCategory'])) {
    $_POST['AddCategory'] = filter_var($_POST['AddCategory'], FILTER_SANITIZE_STRIPPED);
    if ($_POST['AddCategory']) {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query0 = $mysql->prepare('SELECT count(*) as num FROM k_photodesk_categories WHERE k_pdc_name=:name');
            $query0->execute(array(':name' => $_POST['AddCategory']));
            $result = $query0->fetch(PDO::FETCH_ASSOC);
            if ($result['num'] == 0) {
                $query = $mysql->prepare('INSERT INTO k_photodesk_categories (k_pdc_name) VALUES (:name)');
                $query->execute(array(':name' => $_POST['AddCategory']));
                echo $mysql->lastInsertId();
            } else {
                echo 'no';
            }
        } catch (PDOException $e) {
            exit();
        }
    } else {
        echo 'no';
    }
}

if (isset($_POST['DeleteCategory'])) {
    $cat_del = new CatalogCategoriesP();
    $cat_del->DeleteCategory($_POST['DeleteCategory']);
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
        $query = $mysql->prepare('UPDATE k_all_banners SET k_ab_end_date=NOW()+INTERVAL :date DAY WHERE k_ab_id=:id');
        $query->execute(array(":date" => $_POST['BannersAddDaysPlus'], ":id" => $_POST['BannersAddDaysSubmit']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['PhotoColor'])) {
    $_POST['PhotoColor'] = filter_var($_POST['PhotoColor'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query0 = $mysql->prepare('SELECT k_pd_color_light FROM k_photodesk WHERE k_pd_id=:id');
        $query0->execute(array(':id' => $_POST['PhotoColor']));
        $result0 = $query0->fetch(PDO::FETCH_ASSOC);
        $new_state = 0;
        if ($result0['k_pd_color_light'] == 0) {
            $new_state = 1;
        }
        $query = $mysql->prepare('UPDATE k_photodesk SET k_pd_color_light=:state WHERE k_pd_id=:id');
        $query->execute(array(':id' => $_POST['PhotoColor'], ":state" => $new_state));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['PhotoAddDays'])) {
    $_POST['PhotoAddDays'] = filter_var($_POST['PhotoAddDays'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query0 = $mysql->prepare('SELECT k_pd_end_date FROM k_photodesk WHERE k_pd_id=:id LIMIT 1');
        $query0->execute(array(':id' => $_POST['PhotoAddDays']));
        $result0 = $query0->fetch(PDO::FETCH_ASSOC);
        $between = round((strtotime($result0['k_pd_end_date']) - time()) / 86400, 0);
        echo $between;
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['PhotoAddDaysID'])) {
    $_POST['PhotoAddDaysID'] = filter_var($_POST['PhotoAddDaysID'], FILTER_VALIDATE_INT);
    $_POST['PhotoAddDaysSubmit'] = filter_var($_POST['PhotoAddDaysSubmit'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query0 = $mysql->prepare('SELECT k_pd_end_date FROM k_photodesk WHERE k_pd_id=:id LIMIT 1');
        $query0->execute(array(':id' => $_POST['PhotoAddDaysID']));
        $result0 = $query0->fetch(PDO::FETCH_ASSOC);
        //$plus = strtotime($result0['k_pd_end_date']) + $_POST['PhotoAddDaysSubmit'] * 24 * 60 * 60;
        //$plus_date = date('Y-m-d H:i:s', $plus);
        $query1 = $mysql->prepare('UPDATE k_photodesk SET k_pd_end_date=NOW()+INTERVAL :date DAY WHERE k_pd_id=:id LIMIT 1');
        if ($query1->execute(array(':id' => $_POST['PhotoAddDaysID'], ':date' => $_POST['PhotoAddDaysSubmit']))) {
            echo 'Успешно продлено до ' . $plus_date . '!';
        } else {
            echo 'Произошла ошибка!';
        }
    } catch (PDOException $e) {
        exit();
    }
}
?>
