<?php

session_start();

require_once '../../inc/configs.php';
require_once '../../inc/functions.php';
require_once 'classes.php';

if (isset($_POST['expert_show'])) {
    $_POST['expert_show'] = filter_var($_POST['expert_show'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_e_email as email, k_e_brief as brief, k_e_address as address, k_e_phone as phone, k_e_site as site, k_e_theme as theme, k_e_header as header, k_e_description as descr FROM k_experts WHERE k_e_id=:id LIMIT 1');
        $query->execute(array(':id' => $_POST['expert_show']));
        $row = $query->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        exit();
    }
    echo json_encode($row);
}

if (isset($_POST['SaveChangeExID'])) {
    $_POST['SaveChangeExID'] = filter_var($_POST['SaveChangeExID'], FILTER_VALIDATE_INT);
    $_POST['SaveChangeExEmail'] = filter_var($_POST['SaveChangeExEmail'], FILTER_SANITIZE_EMAIL);
    $_POST['SaveChangeExTheme'] = filter_var($_POST['SaveChangeExTheme'], FILTER_SANITIZE_STRIPPED);
    $_POST['SaveChangeExBrief'] = filter_var($_POST['SaveChangeExBrief'], FILTER_SANITIZE_STRIPPED);
    $_POST['SaveChangeExHeader'] = filter_var($_POST['SaveChangeExHeader'], FILTER_SANITIZE_STRIPPED);
    $_POST['SaveChangeExDescription'] = filter_var($_POST['SaveChangeExDescription'], FILTER_SANITIZE_STRIPPED);
    $_POST['SaveChangeExAddress'] = filter_var($_POST['SaveChangeExAddress'], FILTER_VALIDATE_INT);
    $_POST['SaveChangeExPhone'] = filter_var($_POST['SaveChangeExPhone'], FILTER_SANITIZE_STRIPPED);
    $_POST['SaveChangeExSite'] = CorrectURL($_POST['SaveChangeExSite']);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('UPDATE k_experts SET k_e_email=:email, k_e_theme=:theme, k_e_brief=:brief, k_e_header=:header,
            k_e_description=:descr, k_e_address=:addr, k_e_phone=:phone, k_e_site=:site WHERE k_e_id=:id');
        $query->execute(array(":email" => $_POST['SaveChangeExEmail'],
            ":theme" => $_POST['SaveChangeExTheme'],
            ":brief" => $_POST['SaveChangeExBrief'],
            ":header" => $_POST['SaveChangeExHeader'],
            ":descr" => $_POST['SaveChangeExDescription'],
            ":addr" => $_POST['SaveChangeExAddress'],
            ":phone" => $_POST['SaveChangeExPhone'],
            ":site" => $_POST['SaveChangeExSite'],
            ":id" => $_POST['SaveChangeExID']));
        $query2 = $mysql->prepare('DELETE FROM k_experts_categories_links WHERE k_ecl_expert_id=:id');
        $query2->execute(array(":id" => $_POST['SaveChangeExID']));
        $query3 = $mysql->prepare('INSERT INTO k_experts_categories_links (k_ecl_expert_id,k_ecl_category_id) VALUES (:id,:cat)');
        for ($i = 0; $i < count($_POST['SaveChangeCategories']); $i++) {
            $query3->execute(array(":id" => $_POST['SaveChangeExID'], ":cat" => $_POST['SaveChangeCategories'][$i]));
        }
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['expert_id'])) {
    $ex = new TableExpertsBuild();
    $ex->LoadCategories();
    echo $ex->CompareCategories($_POST['expert_id']);
}

if (isset($_POST['EmailChange'])) {
    $_POST['EmailChange'] = filter_var($_POST['EmailChange'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_e_email FROM k_experts WHERE k_e_id=:id LIMIT 1');
        $query->execute(array(':id' => $_POST['EmailChange']));
        $row = $query->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        exit();
    }
    echo $row['k_e_email'];
}

if (isset($_POST['ShowExpertInfo'])) {
    $_POST['ShowExpertInfo'] = filter_var($_POST['ShowExpertInfo'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_e_date as rdate, k_e_end_date as ldate, k_e_theme as theme, k_e_phone as phone, k_e_email as email, k_e_site as site, k_shn_house_num as hnum, k_s_name as street
            FROM k_experts AS ke
            LEFT JOIN k_streets_house_nums AS kshn ON (kshn.k_shn_id = ke.k_e_address)
            LEFT JOIN k_streets AS kstr ON (kstr.k_s_id = kshn.k_shn_street_id)
            WHERE k_e_id=:id');
        $query->execute(array(':id' => $_POST['ShowExpertInfo']));
        $row = $query->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        exit();
    }
    echo json_encode($row);
}

if (isset($_POST['PassSave'])) {
    $_POST['PassSave'] = filter_var($_POST['PassSave'], FILTER_VALIDATE_INT);
    $_POST['NewPass'] = filter_var($_POST['NewPass'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('UPDATE k_experts SET k_e_password=:pass WHERE k_e_id=:id');
        $query->execute(array(':id' => $_POST['PassSave'], ':pass' => md5($_POST['NewPass'])));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['UpExpert'])) {
    $_POST['UpExpert'] = filter_var($_POST['UpExpert'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('UPDATE k_experts SET k_e_up_date=NOW() WHERE k_e_id=:id');
        $query->execute(array(':id' => $_POST['UpExpert']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['ChangeState'])) {
    $_POST['ChangeState'] = filter_var($_POST['ChangeState'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query0 = $mysql->prepare('SELECT k_e_active FROM k_experts WHERE k_e_id=:id');
        $query0->execute(array(':id' => $_POST['ChangeState']));
        $result0 = $query0->fetch(PDO::FETCH_ASSOC);
        $new_state = 0;
        if ($result0['k_e_active'] == 0) {
            $new_state = 1;
        }
        $query = $mysql->prepare('UPDATE k_experts SET k_e_active=:state WHERE k_e_id=:id');
        $query->execute(array(':id' => $_POST['ChangeState'], ":state" => $new_state));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['OnMainPage'])) {
    $_POST['OnMainPage'] = filter_var($_POST['OnMainPage'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query0 = $mysql->prepare('SELECT k_e_main_page FROM k_experts WHERE k_e_id=:id');
        $query0->execute(array(':id' => $_POST['OnMainPage']));
        $result0 = $query0->fetch(PDO::FETCH_ASSOC);
        $new_state = 0;
        if ($result0['k_e_main_page'] == 0) {
            $new_state = 1;
        }
        $query = $mysql->prepare('UPDATE k_experts SET k_e_main_page=:state WHERE k_e_id=:id');
        $query->execute(array(':id' => $_POST['OnMainPage'], ":state" => $new_state));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['DeleteExpert'])) {
    $_POST['DeleteExpert'] = filter_var($_POST['DeleteExpert'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query0 = $mysql->prepare('SELECT k_e_image FROM k_experts WHERE k_e_id=:id');
        $query0->execute(array(':id' => $_POST['DeleteExpert']));
        $result0 = $query0->fetch(PDO::FETCH_ASSOC);
        if ($result0['k_e_image'] != 'images/noimage.png') {
            if (file_exists('../../' . $result0['k_e_image'])) {
                unlink('../../' . $result0['k_e_image']);
            }
        }
        $query1 = $mysql->prepare('DELETE FROM k_experts WHERE k_e_id=:id');
        $query1->execute(array(':id' => $_POST['DeleteExpert']));
        $query2 = $mysql->prepare('DELETE keq.*, kea.*
            FROM k_experts_questions AS keq
            LEFT JOIN k_experts_answers AS kea ON (keq.k_eq_id = kea.k_ea_question_id)
            WHERE k_eq_expert_id=:id');
        $query2->execute(array(':id' => $_POST['DeleteExpert']));
        $query3 = $mysql->prepare('DELETE FROM k_experts_categories_links WHERE k_ecl_expert_id=:id');
        $query3->execute(array(':id' => $_POST['DeleteExpert']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
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
if (isset($_POST['CategoriesRedakt'])) {
    $_POST['CategoriesRedakt'] = filter_var($_POST['CategoriesRedakt'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_ec_name FROM k_experts_categories WHERE k_ec_id=:id');
        $query->execute(array(":id" => $_POST['CategoriesRedakt']));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        echo $result['k_ec_name'];
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['CategoriesRedaktSubmitID'])) {
    $_POST['CategoriesRedaktSubmitID'] = filter_var($_POST['CategoriesRedaktSubmitID'], FILTER_VALIDATE_INT);
    $_POST['CategoriesRedaktSubmitText'] = filter_var($_POST['CategoriesRedaktSubmitText'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('UPDATE k_experts_categories SET k_ec_name=:name WHERE k_ec_id=:id');
        $query->execute(array(":name" => $_POST['CategoriesRedaktSubmitText'], ":id" => $_POST['CategoriesRedaktSubmitID']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['DeleteCategoryExp'])) {
    $_POST['DeleteCategoryExp'] = filter_var($_POST['DeleteCategoryExp'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('DELETE FROM k_experts_categories WHERE k_ec_id=:id');
        $query->execute(array(":id" => $_POST['DeleteCategoryExp']));
        $query2 = $mysql->prepare('DELETE FROM k_experts_categories_links WHERE k_ecl_category_id=:id');
        $query2->execute(array(":id" => $_POST['DeleteCategoryExp']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
?>
