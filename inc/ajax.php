<?php

include '../admin/inc/configs.php';
include 'classes.php';

if (isset($_POST['ImmovablePages'])) {
    $_POST['ImmovablePages'] = filter_var($_POST['ImmovablePages'], FILTER_VALIDATE_INT);

    $imm = new MainPageRealty(1);
    if ($_POST['ImmovablePages'] < 1) {
        $_POST['ImmovablePages'] = $imm->max_pages;
    }
    if ($_POST['ImmovablePages'] > $imm->max_pages) {
        $_POST['ImmovablePages'] = 1;
    }
    $immovables = new MainPageRealty($_POST['ImmovablePages']);

    $text = '';
    for ($i = 0; $i < count($immovables->id); $i++) {
        $text .= '<div class="block_white">';
        if ($immovables->photo[$i] && file_exists($_SERVER['DOCUMENT_ROOT'].'/admin/images/addresses/1_' . $immovables->photo[$i])) {
            $text .= '<img class="realty_img" src="/admin/images/addresses/1_' . $immovables->photo[$i].'" alt="' . strip_tags($immovables->text[$i]) . '">';
        } else {
            $text .= '<img class="realty_img" src="/images/s.gif" alt="' . strip_tags($immovables->text[$i]) . '">';
        }
        $text .= '
        <table>
            <tr>
                <td class="left">Тип</td>
                <td class="right">'.$immovables->type[$i].'</td>
            </tr>
            <tr>
                <td class="left">Район</td>
                <td class="right">'.$immovables->district[$i].'</td>
            </tr>
            <tr>
                <td class="left">Площадь</td>
                <td class="right">'.$immovables->square[$i].' м2</td>
            </tr>
            <tr>
                <td class="left">Этаж</td>
                <td class="right">'.$immovables->floor[$i].' этаж</td>
            </tr>
            <tr>
                <td class="left">Комнат</td>
                <td class="right">'.$immovables->rooms[$i].'</td>
            </tr>

            <tr>

                <td colspan="2" class="td-li">
                    <span class="orange_job"></span>
                    <hr size="3" class="orange_hr"></td>

            </tr>
            <tr>
                <td class="left"><span class="price">'.$immovables->price[$i].' т.р.</span></td>
                <td class="right"><a href="realty/?ShowParam=20&id='.$immovables->id[$i].'"><img src="/images/black_arrow.png"></a></td>
            </tr>
        </table>
        </div>';

/*
        if ($i % 2 == 0) {
            $text .= '<div class="block_white">';
        } else {
            $text .= '<div class="block_gray">';
        }
        $text .= '<p class="time_vip_nedvig">Добавлено ' . $immovables->date[$i] . '</p>';
        $text .= $immovables->text[$i];
        $text .= '<div class="icon_vip_nedvig">
            <img class="kompas_map" title="Показать на карте, ' . $immovables->address[$i] . '" src="images/map_1.png" alt="">';
        if ($immovables->photo[$i] && file_exists('../admin/images/addresses/' . $immovables->photo[$i])) {
            $text .= '<img class="photo_map" onmouseover="ShowPhoto(this);" onmouseout="HidePhoto(this);" src="images/photo_1.png" alt="' . $immovables->id[$i] . '">';
        }
        $text .= '</div></div>';
*/
    }


    echo json_encode(array("block_h" => $text, "page_h" => $_POST['ImmovablePages']));
}

if (isset($_POST['ShowPhoto'])) {
    $_POST['ShowPhoto'] = filter_var($_POST['ShowPhoto'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT k_shp_url
            FROM k_immovables_sell AS kis
            LEFT JOIN k_street_house_photos AS kshp ON (kshp.k_shp_parent = kis.k_isf_address)
            WHERE k_isf_id=:id');
        $query->execute(array(":id" => $_POST['ShowPhoto']));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if ($result['k_shp_url']) {
            echo 'admin/images/addresses/1_' . $result['k_shp_url'];
        } else {
            echo 'images/noimage.png';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['PhotoPages'])) {
    $_POST['PhotoPages'] = filter_var($_POST['PhotoPages'], FILTER_VALIDATE_INT);
    $photo = new MainPagePhotoboard(1);
    if ($_POST['PhotoPages'] < 1) {
        $_POST['PhotoPages'] = $photo->max_pages;
    }
    if ($_POST['PhotoPages'] > $photo->max_pages) {
        $_POST['PhotoPages'] = 1;
    }
    $photoboard = new MainPagePhotoboard($_POST['PhotoPages']);

    echo json_encode(array("block_h" => $photoboard->AjaxRefresh(), "page_h" => $_POST['PhotoPages']));
}

if (isset($_POST['ExpertPages'])) {
    $_POST['ExpertPages'] = filter_var($_POST['ExpertPages'], FILTER_VALIDATE_INT);
    $exp = new MainPageExperts(1);
    if ($_POST['ExpertPages'] < 1) {
        $_POST['ExpertPages'] = $exp->max_pages;
    }
    if ($_POST['ExpertPages'] > $exp->max_pages) {
        $_POST['ExpertPages'] = 1;
    }
    $experts = new MainPageExperts($_POST['ExpertPages']);

    echo json_encode(array("block_h" => $experts->AjaxRefresh(), "page_h" => $_POST['ExpertPages']));
}

if (isset($_POST['BlogPages'])) {
    $_POST['BlogPages'] = filter_var($_POST['BlogPages'], FILTER_VALIDATE_INT);
    //$bl = new MainPageBlog(1);
    $blocks = file_get_contents('blocks.cfg');
        if ($blocks == 'TRUE') {
            $bl = new MainPageWebcams(1);
        } else {
            $bl = new MainPageBlog(1);
        }
    if ($_POST['BlogPages'] < 1) {
        $_POST['BlogPages'] = $bl->max_pages;
    }
    if ($_POST['BlogPages'] > $bl->max_pages) {
        $_POST['BlogPages'] = 1;
    }
    //$blog = new MainPageBlog($_POST['BlogPages']);
    if ($blocks == 'TRUE') {
        $blog = new MainPageWebcams($_POST['BlogPages']);
    } else {
        $blog = new MainPageBlog($_POST['BlogPages']);
    }

    echo json_encode(array("block_h" => $blog->AjaxRefresh(), "page_h" => $_POST['BlogPages']));
}

if (isset($_POST['JobPages'])) {
    $_POST['JobPages'] = filter_var($_POST['JobPages'], FILTER_VALIDATE_INT);
    $_POST['JobPagesType'] = filter_var($_POST['JobPagesType'], FILTER_VALIDATE_INT);
    $job = new MainPageJob(1, $_POST['JobPagesType']);
    if ($_POST['JobPages'] < 1) {
        $_POST['JobPages'] = $job->max_pages;
    }
    if ($_POST['JobPages'] > $job->max_pages) {
        $_POST['JobPages'] = 1;
    }
    $jobs = new MainPageJob($_POST['JobPages'], $_POST['JobPagesType']);

    echo json_encode(array("block_h" => $jobs->Refresh(), "page_h" => $_POST['JobPages']));
}

if (isset($_POST['CatalogPages'])) {
    $_POST['CatalogPages'] = filter_var($_POST['CatalogPages'], FILTER_VALIDATE_INT);
    $_POST['catalogPagesType'] = filter_var($_POST['catalogPagesType'], FILTER_VALIDATE_INT);
    $catalog = new MainPageCatalog(1, $_POST['catalogPagesType']);
    if ($_POST['CatalogPages'] < 1) {
        $_POST['CatalogPages'] = $catalog->max_pages;
    }
    if ($_POST['CatalogPages'] > $catalog->max_pages) {
        $_POST['CatalogPages'] = 1;
    }
    $catalogs = new MainPageCatalog($_POST['CatalogPages'], $_POST['catalogPagesType']);

    echo json_encode(array("block_h" => $catalogs->Refresh(), "page_h" => $_POST['CatalogPages']));
}

if (isset($_POST['LoginAvailable'])) {
    $_POST['LoginAvailable'] = filter_var($_POST['LoginAvailable'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT * FROM k_users WHERE k_ku_login=:login');
        $query->execute(array(":login" => $_POST['LoginAvailable']));
        if ($query->rowCount() > 0) {
            echo 'no';
        } else {
            echo 'yes';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['EmailAvailable'])) {
    $_POST['EmailAvailable'] = filter_var($_POST['EmailAvailable'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT * FROM k_users WHERE k_ku_email=:email');
        $query->execute(array(":email" => $_POST['EmailAvailable']));
        if ($query->rowCount() > 0) {
            echo 'no';
        } else {
            echo 'yes';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['PhoneAvailable'])) {
    $_POST['PhoneAvailable'] = filter_var($_POST['PhoneAvailable'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT * FROM k_users WHERE k_ku_phone=:phone');
        $query->execute(array(":phone" => $_POST['PhoneAvailable']));
        if ($query->rowCount() > 0) {
            echo 'no';
        } else {
            echo 'yes';
        }
    } catch (PDOException $e) {
        exit();
    }
}
?>
