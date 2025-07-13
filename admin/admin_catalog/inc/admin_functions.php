<?php

session_start();

require_once '../../inc/configs.php';
require_once 'classes.php';
require_once 'banners.php';

if (isset($_POST['ChangeCategory'])) {
    $_POST['ChangeCategory'] = filter_var($_POST['ChangeCategory'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare('SELECT k_cc_name FROM k_catalog_categories WHERE k_cc_id=:id');
        $queue->execute(array(":id" => $_POST['ChangeCategory']));
        $result = $queue->fetch(PDO::FETCH_ASSOC);
        echo $result['k_cc_name'];
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['SaveCategory'])) {
    $_POST['SaveCategory'] = filter_var($_POST['SaveCategory'], FILTER_SANITIZE_STRIPPED);
    $_POST['SaveID'] = filter_var($_POST['SaveID'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare('UPDATE k_catalog_categories SET k_cc_name=:name WHERE k_cc_id=:id');
        if ($queue->execute(array(":name" => $_POST['SaveCategory'], ":id" => $_POST['SaveID']))) {
            echo 'yes';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['DeleteCategory'])) {
    $_POST['DeleteCategory'] = filter_var($_POST['DeleteCategory'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare('DELETE kcc.*, kcbs.*, kcs.*, kcfp.*
            FROM k_catalog_categories AS kcc
            LEFT JOIN k_catalog_big_subcategories AS kcbs ON (kcbs.k_cbs_parent = kcc.k_cc_id)
            LEFT JOIN k_catalog_subcategories AS kcs ON (kcs.k_cs_parent = kcbs.k_cbs_id)
            LEFT JOIN k_catalog_firms_parents AS kcfp ON (kcfp.k_cfp_parent_id = kcs.k_cs_id)
            WHERE k_cc_id = :id');
        if ($queue->execute(array(":id" => $_POST['DeleteCategory']))) {
            echo 'yes';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['AddCategory'])) {
    $_POST['AddCategory'] = filter_var($_POST['AddCategory'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare('INSERT INTO k_catalog_categories (k_cc_name) VALUES (:name)');
        if ($queue->execute(array(":name" => $_POST['AddCategory']))) {
            echo 'yes';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['EditSubcategory'])) {
    $s = new CatalogSubCategories();
    $s->EditWindow($_POST['EditSubcategory']);
}

if (isset($_POST['SaveSubcategory'])) {
    $_POST['SaveSubcategoryName'] = filter_var($_POST['SaveSubcategoryName'], FILTER_SANITIZE_STRIPPED);
    $_POST['SaveSubcategory'] = filter_var($_POST['SaveSubcategory'], FILTER_VALIDATE_INT);
    $_POST['SaveSubcategoryCat'] = filter_var($_POST['SaveSubcategoryCat'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare('UPDATE k_catalog_big_subcategories SET k_cbs_name=:name, k_cbs_parent=:parent WHERE k_cbs_id=:id');
        if ($queue->execute(array(":name" => $_POST['SaveSubcategoryName'], ":id" => $_POST['SaveSubcategory'], ":parent" => $_POST['SaveSubcategoryCat']))) {
            echo 'yes';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['DeleteSubcategory'])) {
    $_POST['DeleteSubcategory'] = filter_var($_POST['DeleteSubcategory'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare('DELETE kcbs.*, kcs.*, kcfp.*
            FROM k_catalog_big_subcategories AS kcbs
            LEFT JOIN k_catalog_subcategories AS kcs ON (kcs.k_cs_parent = kcbs.k_cbs_id)
            LEFT JOIN k_catalog_firms_parents AS kcfp ON (kcfp.k_cfp_parent_id = kcs.k_cs_id)
            WHERE k_cbs_id=:id');
        if ($queue->execute(array(":id" => $_POST['DeleteSubcategory']))) {
            echo 'yes';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['AddSubcategory'])) {
    $_POST['AddSubcategory'] = filter_var($_POST['AddSubcategory'], FILTER_SANITIZE_STRIPPED);
    $_POST['AddSubcategoryPar'] = filter_var($_POST['AddSubcategoryPar'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare('INSERT INTO k_catalog_big_subcategories (k_cbs_name,k_cbs_parent) VALUES (:name,:parent)');
        if ($queue->execute(array(":name" => $_POST['AddSubcategory'], ":parent" => $_POST['AddSubcategoryPar']))) {
            echo 'yes';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['AddSubSubPost'])) {
    $_POST['AddSubSubPostPar'] = filter_var($_POST['AddSubSubPostPar'], FILTER_SANITIZE_STRIPPED);
    $_POST['AddSubSubPost'] = filter_var($_POST['AddSubSubPost'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare('INSERT INTO k_catalog_subcategories (k_cs_name,k_cs_parent) VALUES (:name,:parent)');
        if ($queue->execute(array(":name" => $_POST['AddSubSubPostPar'], ":parent" => $_POST['AddSubSubPost']))) {
            echo 'yes';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['DeleteSubSub'])) {
    $_POST['DeleteSubSub'] = filter_var($_POST['DeleteSubSub'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare('DELETE FROM k_catalog_subcategories WHERE k_cs_id=:id');
        if ($queue->execute(array(":id" => $_POST['DeleteSubSub']))) {
            echo 'yes';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['RedaktSubSub'])) {
    $_POST['RedaktSubSub'] = filter_var($_POST['RedaktSubSub'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare('SELECT k_cs_name as name,k_cs_parent as parent FROM k_catalog_subcategories WHERE k_cs_id=:id');
        $queue->execute(array(":id" => $_POST['RedaktSubSub']));
        $result = $queue->fetch(PDO::FETCH_ASSOC);
        echo json_encode($result);
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['SaveSubSubRedaktID'])) {
    $_POST['SaveSubSubRedaktID'] = filter_var($_POST['SaveSubSubRedaktID'], FILTER_VALIDATE_INT);
    $_POST['SaveSubSubRedaktPar'] = filter_var($_POST['SaveSubSubRedaktPar'], FILTER_VALIDATE_INT);
    $_POST['SaveSubSubRedaktName'] = filter_var($_POST['SaveSubSubRedaktName'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare('UPDATE k_catalog_subcategories SET k_cs_name=:name,k_cs_parent=:parent WHERE k_cs_id=:id');
        $queue->execute(array(":id" => $_POST['SaveSubSubRedaktID'], ":parent" => $_POST['SaveSubSubRedaktPar'], ":name" => $_POST['SaveSubSubRedaktName']));
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
if (isset($_POST['AddBanner'])) {
    $_POST['AddBanner'] = filter_var($_POST['AddBanner'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('INSERT INTO k_all_banners (k_ab_code,k_ab_type,k_ab_end_date) VALUES ("",:type,NOW())');
        $query->execute(array(":type" => $_POST['AddBanner']));
        $last = $mysql->lastInsertId();
        echo '<tr>
            <td><p class="style_2"></p></td>
            <td>
            <a class="a_1" onclick="ShowBannerInfo(' . $last . ');" title="Информация">
            <img src="../images/info.png" alt="">
            </a>
            <a class="a_1" onclick="ChangeTimeToEnd(' . $last . ');" title="Оставшееся время: ' . $last . ' дней">
            <img src="../images/clock_red_1.png" alt="">
            </a>
            <a class="a_1" onclick="BannerView(' . $last . ');" title="Просмотр">
            <img src="../images/photo_baner.png" alt="">
            </a>
            <a class="a_1" onclick="BannerCodeView(' . $last . ');">
            <img src="../images/edit.png" title="Редактировать" alt="">
            </a>
            <a class="a_1" onclick="DeleteBanner(' . $last . ',this);">
            <img src="../images/delete.png" title="Удалить" alt="">
            </a>
            </td>
            </tr>';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['DeleteBanner'])) {
    $_POST['DeleteBanner'] = filter_var($_POST['DeleteBanner'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('DELETE FROM k_all_banners WHERE k_ab_id=:id');
        $query->execute(array(":id" => $_POST['DeleteBanner']));
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
        $query0 = $mysql->prepare('SELECT k_cf_state FROM k_catalog_firms WHERE k_cf_id=:id');
        $query0->execute(array(':id' => $_POST['ChangeState']));
        $result0 = $query0->fetch(PDO::FETCH_ASSOC);
        $new_state = 0;
        if ($result0['k_cf_state'] == 0) {
            $new_state = 1;
        }
        $query = $mysql->prepare('UPDATE k_catalog_firms SET k_cf_state=:state WHERE k_cf_id=:id');
        $query->execute(array(':id' => $_POST['ChangeState'], ":state" => $new_state));

        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
?>