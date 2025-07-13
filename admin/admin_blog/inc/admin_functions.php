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
if (isset($_POST['BlogChange'])) {
    $_POST['BlogChange'] = filter_var($_POST['BlogChange'], FILTER_VALIDATE_INT);
    $blog = new Blog(1, ' AND k_b_id=' . $_POST['BlogChange'] . ' ', 1);
    $bc = new BlogCategories();
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT * FROM k_blog WHERE k_b_id=:id');
        $query->execute(array(":id" => $_POST['BlogChange']));
        echo '<tr>
            <td><p class="style_2">№ Статьи:</p></td>
            <td><p class="style_4_2">' . $blog->id[0] . '</p></td>
            </tr>
            <tr><td><p class="style_2">Рубрика Статьи:</p></td>
            <td>
            <select id="BlogCategoryChange">';
        for ($i = 0; $i < count($bc->id); $i++) {
            if ($bc->id[$i] == $blog->category[0]) {
                echo '<option selected value="' . $bc->id[$i] . '">' . $bc->name[$i] . '</option>';
            } else {
                echo '<option value="' . $bc->id[$i] . '">' . $bc->name[$i] . '</option>';
            }
        }
        echo '</select>
            </td></tr>
            <tr><td><p class="style_2">Главное фото статьи:</p></td>
            <td><div>';
        if ($blog->image[0] && file_exists('../../' . $blog->image[0])) {
            echo '<img id="BlogChangeImage" class="img_blog" src="../' . $blog->image[0] . '" alt=""><br>';
        } else {
            echo '<img id="BlogChangeImage" class="img_blog" src="../images/noimage.png" alt=""><br>';
        }
        echo '</div>
            </td></tr>
            <tr>
            <td><p class="style_2">Наименование статьи:</p></td>
            <td><input type="text" id="BlogName" value="' . $blog->name[0] . '"></td>
            </tr>
            <tr>
            <td colspan="2">
            <p class="style_2">Краткое описание:</p>
            <textarea rows="12" id="BlogBrief" cols="55" name="text">' . $blog->brief[0] . '</textarea>
            <input type="hidden" id="BlogID" value="' . $blog->id[0] . '">
            </td>
            </tr>';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['ChangeImageBlogID'])) {
    $_POST['ChangeImageBlogID'] = filter_var($_POST['ChangeImageBlogID'], FILTER_VALIDATE_INT);
    $_POST['ChangeImageBlogURL'] = str_replace('../', '', filter_var($_POST['ChangeImageBlogURL'], FILTER_SANITIZE_STRIPPED));
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_b_image FROM k_blog WHERE k_b_id=:id');
        $query->execute(array(":id" => $_POST['ChangeImageBlogID']));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        unlink('../../' . $result['k_b_image']);
        $query1 = $mysql->prepare('UPDATE k_blog SET k_b_image=:url WHERE k_b_id=:id');
        $query1->execute(array(":id" => $_POST['ChangeImageBlogID'], ":url" => $_POST['ChangeImageBlogURL']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['SaveBlogChangeID'])) {
    $_POST['SaveBlogChangeID'] = filter_var($_POST['SaveBlogChangeID'], FILTER_VALIDATE_INT);
    $_POST['SaveBlogChangeNAME'] = filter_var($_POST['SaveBlogChangeNAME'], FILTER_SANITIZE_STRIPPED);
    $_POST['SaveBlogChangeBRIEF'] = filter_var($_POST['SaveBlogChangeBRIEF'], FILTER_SANITIZE_STRIPPED);
    $_POST['SaveBlogChangeCAT'] = filter_var($_POST['SaveBlogChangeCAT'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('UPDATE k_blog
            SET k_b_name=:name, k_b_brief=:brief, k_b_category=:cat, k_b_date=NOW(), k_b_user=:user
            WHERE k_b_id=:id');
        $query->execute(array(":id" => $_POST['SaveBlogChangeID'],
            ":name" => $_POST['SaveBlogChangeNAME'],
            ":brief" => $_POST['SaveBlogChangeBRIEF'],
            ":cat" => $_POST['SaveBlogChangeCAT'],
            ":user" => $_SESSION['login']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['BlogCategoryNameChange'])) {
    $_POST['BlogCategoryNameChange'] = filter_var($_POST['BlogCategoryNameChange'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_bc_name FROM k_blog_categories WHERE k_bc_id=:id');
        $query->execute(array(":id" => $_POST['BlogCategoryNameChange']));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        echo $result['k_bc_name'];
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['BlogCategorySaveID'])) {
    $_POST['BlogCategorySaveID'] = filter_var($_POST['BlogCategorySaveID'], FILTER_VALIDATE_INT);
    $_POST['BlogCategorySaveNAME'] = filter_var($_POST['BlogCategorySaveNAME'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('UPDATE k_blog_categories SET k_bc_name=:name WHERE k_bc_id=:id');
        $query->execute(array(":id" => $_POST['BlogCategorySaveID'], ":name" => $_POST['BlogCategorySaveNAME']));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['DeleteBlog'])) {
    $_POST['DeleteBlog'] = filter_var($_POST['DeleteBlog'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT * FROM k_blog WHERE k_b_id=:id');
        $query->execute(array(":id" => $_POST['DeleteBlog']));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $match = array();
        $img2 = array();
        preg_match_all('/<img[^>]+>/i', $result['k_b_text'], $match);
        foreach ($match[0] as $img) {
            preg_match_all('/(src)=("[^"]*")/i', $img, $img2);
            unlink('../' . str_replace('"', '', $img2[2][0]));
        }
        $query2 = $mysql->prepare('DELETE FROM k_blog WHERE k_b_id=:id');
        $query2->execute(array(":id" => $_POST['DeleteBlog']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['DeleteCategory'])) {
    $_POST['DeleteCategory'] = filter_var($_POST['DeleteCategory'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT kb.*
            FROM k_blog_categories AS kbc
            LEFT JOIN k_blog AS kb ON (kbc.k_bc_id = kb.k_b_category)
            WHERE k_bc_id=:id');
        $query->execute(array(":id" => $_POST['DeleteCategory']));
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $value) {
            $match = array();
            $img2 = array();
            preg_match_all('/<img[^>]+>/i', $value['k_b_text'], $match);
            foreach ($match[0] as $img) {
                preg_match_all('/(src)=("[^"]*")/i', $img, $img2);
                unlink('../' . str_replace('"', '', $img2[2][0]));
            }
        }
        $query2 = $mysql->prepare('DELETE kbc.*, kb.*
            FROM k_blog_categories AS kbc
            LEFT JOIN k_blog AS kb ON (kbc.k_bc_id = kb.k_b_category)
            WHERE k_bc_id=:id');
        $query2->execute(array(":id" => $_POST['DeleteCategory']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['BlogMainPage'])) {
    $_POST['BlogMainPage'] = filter_var($_POST['BlogMainPage'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query0 = $mysql->prepare('SELECT k_b_main_page FROM k_blog WHERE k_b_id=:id');
        $query0->execute(array(':id' => $_POST['BlogMainPage']));
        $result0 = $query0->fetch(PDO::FETCH_ASSOC);
        $new_state = 0;
        if ($result0['k_b_main_page'] == 0) {
            $new_state = 1;
        }
        $query = $mysql->prepare('UPDATE k_blog SET k_b_main_page=:state WHERE k_b_id=:id');
        $query->execute(array(':id' => $_POST['BlogMainPage'], ":state" => $new_state));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['EnableBlogBlock'])) {
    file_put_contents('../../../inc/blocks.cfg', 'FALSE');
    echo 'yes';
}

if (isset($_POST['BlogState'])) {
    $_POST['BlogState'] = filter_var($_POST['BlogState'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare('SELECT k_b_state FROM k_blog WHERE k_b_id=:id');
        $queue->execute(array(":id" => $_POST['BlogState']));
        $result = $queue->fetch(PDO::FETCH_ASSOC);
        if ($result['k_b_state'] == 1) {
            $output = 'hide';
            $newstate = 0;
        } else {
            $newstate = 1;
            $output = 'show';
        }
        $queue2 = $mysql->prepare('UPDATE k_blog SET k_b_state=:state WHERE k_b_id=:id');
        $queue2->execute(array(":id" => $_POST['BlogState'], ":state" => $newstate));
        echo $output;
    } catch (PDOException $e) {
        echo 'no';
        exit();
    }
}
?>