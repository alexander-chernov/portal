<?php

session_start();

require_once '../../inc/configs.php';
require_once '../../inc/functions.php';
require_once 'classes.php';
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/class.phpmailer.php');

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
if (isset($_POST['ChangeCategoryName'])) {
    $_POST['ChangeCategoryName'] = filter_var($_POST['ChangeCategoryName'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_sc_name FROM k_site_categories WHERE k_sc_id=:id LIMIT 1');
        $query->execute(array(":id" => $_POST['ChangeCategoryName']));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        echo $result['k_sc_name'];
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['SaveCategoryNameID'])) {
    $_POST['SaveCategoryNameID'] = filter_var($_POST['SaveCategoryNameID'], FILTER_VALIDATE_INT);
    $_POST['SaveCategoryName'] = filter_var($_POST['SaveCategoryName'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('UPDATE k_site_categories SET k_sc_name=:name WHERE k_sc_id=:id');
        $query->execute(array(":id" => $_POST['SaveCategoryNameID'], ":name" => $_POST['SaveCategoryName']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['AddNewCategory'])) {
    $_POST['AddNewCategory'] = filter_var($_POST['AddNewCategory'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('INSERT INTO k_site_categories (k_sc_name) VALUES (:name)');
        $query->execute(array(":name" => $_POST['AddNewCategory']));
        echo $mysql->lastInsertId();
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['DeleteCategory'])) {
    $_POST['DeleteCategory'] = filter_var($_POST['DeleteCategory'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('DELETE ksc.*, kss.*, ksl.*
            FROM k_site_categories AS ksc
            LEFT JOIN k_sites_subcategories AS kss ON (kss.k_ss_category = ksc.k_sc_id)
            LEFT JOIN k_sites_links AS ksl ON (ksl.k_sl_sub_id = kss.k_ss_id)
            WHERE k_sc_id=:id');
        $query->execute(array(":id" => $_POST['DeleteCategory']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['ChangeSubcategoryName'])) {
    $_POST['ChangeSubcategoryName'] = filter_var($_POST['ChangeSubcategoryName'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_ss_name FROM k_sites_subcategories WHERE k_ss_id=:id LIMIT 1');
        $query->execute(array(":id" => $_POST['ChangeSubcategoryName']));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        echo $result['k_ss_name'];
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['SaveSubcategoryNameID'])) {
    $_POST['SaveSubcategoryNameID'] = filter_var($_POST['SaveSubcategoryNameID'], FILTER_VALIDATE_INT);
    $_POST['SaveSubcategoryName'] = filter_var($_POST['SaveSubcategoryName'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('UPDATE k_sites_subcategories SET k_ss_name=:name WHERE k_ss_id=:id');
        $query->execute(array(":id" => $_POST['SaveSubcategoryNameID'], ":name" => $_POST['SaveSubcategoryName']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['AddNewSubcategory'])) {
    $_POST['AddNewSubcategory'] = filter_var($_POST['AddNewSubcategory'], FILTER_SANITIZE_STRIPPED);
    $_POST['AddNewSubcategoryCAT'] = filter_var($_POST['AddNewSubcategoryCAT'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('INSERT INTO k_sites_subcategories (k_ss_name,k_ss_category) VALUES (:name,:cat)');
        $query->execute(array(":name" => $_POST['AddNewSubcategory'], ":cat" => $_POST['AddNewSubcategoryCAT']));
        echo $mysql->lastInsertId();
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['DeleteSubcategory'])) {
    $_POST['DeleteSubcategory'] = filter_var($_POST['DeleteSubcategory'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('DELETE kss.*, ksl.*
            FROM k_sites_subcategories AS kss
            LEFT JOIN k_sites_links AS ksl ON (ksl.k_sl_sub_id = kss.k_ss_id)
            WHERE k_ss_id=:id');
        $query->execute(array(":id" => $_POST['DeleteSubcategory']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['ShowEmailWindow'])) {
    $_POST['ShowEmailWindow'] = filter_var($_POST['ShowEmailWindow'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_s_email FROM k_sites WHERE k_s_id=:id LIMIT 1');
        $query->execute(array(":id" => $_POST['ShowEmailWindow']));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        echo $result['k_s_email'];
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['SendEmailID'])) {
    $_POST['SendEmailText'] = filter_var($_POST['SendEmailText'], FILTER_SANITIZE_STRIPPED);
    $_POST['SendEmailTheme'] = filter_var($_POST['SendEmailTheme'], FILTER_SANITIZE_STRIPPED);
    $_POST['SendEmailID'] = filter_var($_POST['SendEmailID'], FILTER_VALIDATE_INT);
    //try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_s_email FROM k_sites WHERE k_s_id=:id LIMIT 1');
        $query->execute(array(":id" => $_POST['SendEmailID']));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $message = $_POST['SendEmailText'] . "\n";
        $theme = 'Сообщение от администратора '.strtoupper(_SERVER_ADDRESS).': ' . $_POST['SendEmailTheme'] . ' - ' . date("Y-m-d H:m:s");


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
            $mail->Username   = "TOMSK-LINE.ru@gmail.com ";  // GMAIL username
            $mail->Password   = "Qwer1@34";            // GMAIL password
            */

            $mail->SetFrom('noreply@'._SERVER_ADDRESS, _SERVER_ADDRESS);
            $mail->AddAddress($result['k_s_email'], '');
            $mail->Subject    =  'Message from '.strtoupper(_SERVER_ADDRESS).' Administrator - ' . date("Y-m-d H:i:s");
            $mail->AltBody    = $message;
            $mail->MsgHTML($message);
            $mail->Send();
            echo 'Письмо отправлено!';
        } catch (phpmailerException $e) {
            echo $e->errorMessage(); //Pretty error messages from PHPMailer
        } catch (Exception $e) {
            echo $e->getMessage(); //Boring error messages from anything else!
        }
        /*
        if (mb_send_mail($result['k_s_email'], $theme, $message, "From: \"TOMSK-LINE.RU\"\n")) {
            echo 'yes';
        }
        */
    /*
    } catch (PDOException $e) {
        exit();
    }
    */
}
if (isset($_POST['ShowSiteChange'])) {
    $_POST['ShowSiteChange'] = filter_var($_POST['ShowSiteChange'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_s_name AS name, k_s_url AS url,
            k_s_description AS description, k_s_image AS avatar, k_s_contact_name AS c_name,
            k_s_contact_phone AS c_phone, k_s_email AS email
            FROM k_sites
            WHERE k_s_id=:id LIMIT 1');
        $query->execute(array(":id" => $_POST['ShowSiteChange']));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if ($result['avatar'] && file_exists('../../' . $result['avatar'])) {
            $result['avatar'] = '../' . $result['avatar'];
        } else {
            $result['avatar'] = '../images/noimage.png';
        }
        echo json_encode($result);
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['SiteChangeID'])) {
    $_POST['SiteChangeID'] = filter_var($_POST['SiteChangeID'], FILTER_VALIDATE_INT);
    $_POST['SiteChangeName'] = filter_var($_POST['SiteChangeName'], FILTER_SANITIZE_STRIPPED);
    $_POST['SiteChangeURL'] = CorrectURL($_POST['SiteChangeURL']);
    $_POST['SiteChangeDescr'] = filter_var($_POST['SiteChangeDescr'], FILTER_SANITIZE_STRIPPED);
    $_POST['SiteChangeCName'] = filter_var($_POST['SiteChangeCName'], FILTER_SANITIZE_STRIPPED);
    $_POST['SiteChangeCPhone'] = filter_var($_POST['SiteChangeCPhone'], FILTER_SANITIZE_STRIPPED);
    $_POST['SiteChangeEmail'] = filter_var($_POST['SiteChangeEmail'], FILTER_SANITIZE_EMAIL);
    if ($_POST['SiteChangeName'] != '' && $_POST['SiteChangeURL'] != 'http://') {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('UPDATE k_sites
                SET k_s_name=:name,k_s_url=:url,k_s_description=:descr,
                k_s_contact_name=:cname,k_s_contact_phone=:cphone,
                k_s_email=:email
                WHERE k_s_id=:id');
            $query->execute(array(":id" => $_POST['SiteChangeID'],
                ":name" => $_POST['SiteChangeName'],
                ":url" => $_POST['SiteChangeURL'],
                ":descr" => $_POST['SiteChangeDescr'],
                ":cname" => $_POST['SiteChangeCName'],
                ":cphone" => $_POST['SiteChangeCPhone'],
                ":email" => $_POST['SiteChangeEmail']));
            echo 'yes';
        } catch (PDOException $e) {
            exit();
        }
    } else {
        echo 'enter';
    }
}
if (isset($_POST['ChangeSiteState'])) {
    $_POST['ChangeSiteState'] = filter_var($_POST['ChangeSiteState'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_s_state FROM k_sites
            WHERE k_s_id=:id LIMIT 1');
        $query->execute(array(":id" => $_POST['ChangeSiteState']));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $query2 = $mysql->prepare('UPDATE k_sites SET k_s_state=:state WHERE k_s_id=:id LIMIT 1');
        $new_state = 0;
        if ($result['k_s_state'] == 0) {
            $new_state = 1;
            $query2->execute(array(":id" => $_POST['ChangeSiteState'], ":state" => $new_state));
            echo 'state1';
        } else {
            $query2->execute(array(":id" => $_POST['ChangeSiteState'], ":state" => $new_state));
            echo 'state0';
        }
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['CreateNewSiteName'])) {
    $_POST['CreateNewSiteName'] = filter_var($_POST['CreateNewSiteName'], FILTER_SANITIZE_STRIPPED);
    $_POST['CreateNewSiteURL'] = CorrectURL($_POST['CreateNewSiteURL']);
    $_POST['CreateNewSiteDescr'] = filter_var($_POST['CreateNewSiteDescr'], FILTER_SANITIZE_STRIPPED);
    $_POST['CreateNewSiteCName'] = filter_var($_POST['CreateNewSiteCName'], FILTER_SANITIZE_STRIPPED);
    $_POST['CreateNewSiteCPhone'] = filter_var($_POST['CreateNewSiteCPhone'], FILTER_SANITIZE_STRIPPED);
    $_POST['CreateNewSiteEmail'] = filter_var($_POST['CreateNewSiteEmail'], FILTER_SANITIZE_EMAIL);
    if ($_POST['CreateNewSiteName'] != '' && $_POST['CreateNewSiteURL'] != 'http://') {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('INSERT INTO k_sites
                (k_s_name,k_s_url,k_s_description,k_s_contact_name,k_s_contact_phone,k_s_email,k_s_date)
                VALUES (:name,:url,:descr,:cname,:cphone,:email,NOW())');
            $query->execute(array(":name" => $_POST['CreateNewSiteName'],
                ":url" => $_POST['CreateNewSiteURL'],
                ":descr" => $_POST['CreateNewSiteDescr'],
                ":cname" => $_POST['CreateNewSiteCName'],
                ":cphone" => $_POST['CreateNewSiteCPhone'],
                ":email" => $_POST['CreateNewSiteEmail']));
            $sites = new Sites(1, '', 50);
            $sites->Refresh();
        } catch (PDOException $e) {
            exit();
        }
    } else {
        echo 'enter';
    }
}
if (isset($_POST['DeleteSite'])) {
    $_POST['DeleteSite'] = filter_var($_POST['DeleteSite'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_s_image FROM k_sites WHERE k_s_id=:id');
        $query->execute(array(":id" => $_POST['DeleteSite']));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if (file_exists('../../' . $result['k_s_image'])) {
            unlink('../../' . $result['k_s_image']);
        }
        $query2 = $mysql->prepare('DELETE ksl.*,ks.*
          FROM k_sites AS ks
          LEFT JOIN k_sites_links AS ksl ON (ksl.k_sl_site_id = ks.k_s_id)
          WHERE k_s_id=:id');
        $query2->execute(array(":id" => $_POST['DeleteSite']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['ChangeAvatarSiteID'])) {
    $_POST['ChangeAvatarSiteID'] = filter_var($_POST['ChangeAvatarSiteID'], FILTER_VALIDATE_INT);
    $_POST['ChangeAvatarSiteURL'] = str_replace('../', '', filter_var($_POST['ChangeAvatarSiteURL'], FILTER_SANITIZE_STRIPPED));
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_s_image FROM k_sites WHERE k_s_id=:id');
        $query->execute(array(':id' => $_POST['ChangeAvatarSiteID']));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if (file_exists('../../' . $result['k_s_image'])) {
            unlink('../../' . $result['k_s_image']);
        }
        $query2 = $mysql->prepare('UPDATE k_sites SET k_s_image=:ava WHERE k_s_id=:id LIMIT 1');
        $query2->execute(array(':id' => $_POST['ChangeAvatarSiteID'], ":ava" => $_POST['ChangeAvatarSiteURL']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['ShowSitePhotos'])) {
    $_POST['ShowSitePhotos'] = filter_var($_POST['ShowSitePhotos'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_s_image FROM k_sites WHERE k_s_id=:id LIMIT 1');
        $query->execute(array(":id" => $_POST['ShowSitePhotos']));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if ($result['k_s_image'] && file_exists('../../' . $result['k_s_image'])) {
            $result['k_s_image'] = '../' . $result['k_s_image'];
        } else {
            $result['k_s_image'] = '../images/noimage.png';
        }
        echo $result['k_s_image'];
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['ShowSiteCategories'])) {
    $_POST['ShowSiteCategories'] = filter_var($_POST['ShowSiteCategories'], FILTER_VALIDATE_INT);
    try {
        $sc = new SitesSubcategories(0, ' AND k_sl_site_id=' . $_POST['ShowSiteCategories'] . ' ');
        for ($i = 0; $i < count($sc->id); $i++) {
            echo '<tr><td><p class="style_2">' . $sc->name[$i] . '</p></td>
                <td><a class="a_1" onclick="DeleteSiteCategory(this);">
                <img src="../images/delete.png" alt="' . $sc->id[$i] . '"></a></td></tr>';
        }
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['DeleteSiteCategoryP'])) {
    $_POST['DeleteSiteCategoryP'] = filter_var($_POST['DeleteSiteCategoryP'], FILTER_VALIDATE_INT);
    $_POST['DeleteSiteCategory'] = filter_var($_POST['DeleteSiteCategory'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('DELETE FROM k_sites_links WHERE k_sl_site_id=:sid AND k_sl_sub_id=:scid');
        $query->execute(array(":sid" => $_POST['DeleteSiteCategoryP'], ":scid" => $_POST['DeleteSiteCategory']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['ReloadSC'])) {
    $_POST['ReloadSC'] = filter_var($_POST['ReloadSC'], FILTER_VALIDATE_INT);
    try {
        $sc = new SitesSubcategories(0, ' AND k_ss_category=' . $_POST['ReloadSC'] . ' ');
        for ($i = 0; $i < count($sc->id); $i++) {
            echo '<option value="' . $sc->id[$i] . '">' . $sc->name[$i] . '</option>';
        }
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['AddSubcategoryToSiteP'])) {
    $_POST['AddSubcategoryToSiteP'] = filter_var($_POST['AddSubcategoryToSiteP'], FILTER_VALIDATE_INT);
    $_POST['AddSubcategoryToSite'] = filter_var($_POST['AddSubcategoryToSite'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT * FROM k_sites_links WHERE k_sl_site_id=:sid AND k_sl_sub_id=:scid');
        $query->execute(array(":sid" => $_POST['AddSubcategoryToSiteP'], ":scid" => $_POST['AddSubcategoryToSite']));
        if ($query->rowCount() == 0) {
            $query2 = $mysql->prepare('INSERT INTO k_sites_links (k_sl_site_id,k_sl_sub_id) VALUES (:sid,:scid)');
            $query2->execute(array(":sid" => $_POST['AddSubcategoryToSiteP'], ":scid" => $_POST['AddSubcategoryToSite']));
            $sc = new SitesSubcategories(0, ' AND k_sl_site_id=' . $_POST['AddSubcategoryToSiteP'] . ' ');
            for ($i = 0; $i < count($sc->id); $i++) {
                echo '<tr><td><p class="style_2">' . $sc->name[$i] . '</p></td>
                <td><a class="a_1" onclick="DeleteSiteCategory(this);">
                <img src="../images/delete.png" alt="' . $sc->id[$i] . '"></a></td></tr>';
            }
        }
    } catch (PDOException $e) {
        exit();
    }
}
?>