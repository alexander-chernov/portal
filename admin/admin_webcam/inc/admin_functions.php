<?php

session_start();

require_once '../../inc/configs.php';
require_once 'classes.php';

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

if (isset($_POST['WebcamRedakt'])) {
    $_POST['WebcamRedakt'] = filter_var($_POST['WebcamRedakt'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue0 = $mysql->prepare('SELECT k_w_name AS name, k_w_url AS code FROM k_webcams WHERE k_w_id=:id');
        $queue0->execute(array(":id" => $_POST['WebcamRedakt']));
        $result0 = $queue0->fetch(PDO::FETCH_ASSOC);
        echo json_encode($result0);
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['WebcamSaveID'])) {
    $_POST['WebcamSaveID'] = filter_var($_POST['WebcamSaveID'], FILTER_VALIDATE_INT);
    $_POST['WebcamSaveCode'] = filter_var($_POST['WebcamSaveCode'], FILTER_SANITIZE_URL);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue0 = $mysql->prepare('UPDATE k_webcams SET k_w_name=:name,k_w_url=:url WHERE k_w_id=:id');
        $queue0->execute(array(":id" => $_POST['WebcamSaveID'],
            ":url" => $_POST['WebcamSaveCode'],
            ":name" => $_POST['WebcamSaveName']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['ChangeWebcamImageID'])) {
    $_POST['ChangeWebcamImageID'] = filter_var($_POST['ChangeWebcamImageID'], FILTER_VALIDATE_INT);
    $_POST['ChangeWebcamImageIMG'] = str_replace('../', '', filter_var($_POST['ChangeWebcamImageIMG'], FILTER_SANITIZE_STRIPPED));
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue0 = $mysql->prepare('SELECT k_w_image FROM k_webcams WHERE k_w_id=:id LIMIT 1');
        $queue0->execute(array(":id" => $_POST['ChangeWebcamImageID']));
        $result0 = $queue0->fetch(PDO::FETCH_ASSOC);
        if (file_exists('../../' . $result0['k_w_image']) && $result0['k_w_image'] != 'images/noimage.png') {
            unlink('../../' . $result0['k_w_image']);
        }
        $queue1 = $mysql->prepare('UPDATE k_webcams SET k_w_image=:image WHERE k_w_id=:id');
        $queue1->execute(array(":id" => $_POST['ChangeWebcamImageID'],
            ":image" => $_POST['ChangeWebcamImageIMG']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['EnableWebBlock'])) {
    file_put_contents('../../../inc/blocks.cfg', 'TRUE');
    echo 'yes';
}
?>
