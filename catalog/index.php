<?php
define('TOMSKLINE', 1);
session_start();
        require_once '../inc/configs.php';
        require_once 'inc/classes.php';
        require_once 'inc/functions.php';
        require_once '../inc/functions.php';
        require_once '../admin/admin_catalog/inc/classes.php';
        require_once '../admin/admin_catalog/inc/banners.php';
        if (YourIPBanned()) {
            header($_ENV['SERVER_PROTOCOL'] . " 401 IP BAN", true, 401);
            header("Location: http://" . _SERVER_ADDRESS . "/pageBan.php");
        }

        try {
            if (isset($_COOKIE['login'])) {
                $_SESSION['login'] = $_COOKIE['login'];
                $_SESSION['password'] = $_COOKIE['password'];
            }
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT k_ku_id,k_u_privileges FROM k_users WHERE k_ku_login=:login AND k_ku_password=:password');
            $query->execute(array(":login" => $_SESSION['login'], ":password" => $_SESSION['password']));
            $result = $query->fetch(PDO::FETCH_ASSOC);
            if ($query->rowCount() > 0) {
                $_SESSION['id'] = $result['k_ku_id'];
                $_SESSION['privileges'] = $result['k_u_privileges'];
            } else {
                unset($_SESSION);
            }        } catch (PDOException $e) {
            unset($e);
            exit();
        }
        require_once '../admin/inc/functions.php';
        CreateTempTables();

        if (!isset($_GET['PageType'])) {
            $ShowParamID = 3;
        } else {
            $ShowParamID = filter_var($_GET['PageType'], FILTER_VALIDATE_INT);
        }
        if (!isset($_GET['PageIndex'])) {
            $page = 1;
        } else {
            $page = filter_var($_GET['PageIndex'], FILTER_VALIDATE_INT);
        }

        $banners = new BannersAll(0);

        if (!in_array($ShowParamID, array(1, 2, 3, 4))) {
            header($_ENV['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
            header("Location: http://" . _SERVER_ADDRESS . "/page404.php");
        }

        if (isset($_GET['search_string'])) {
            require_once '../inc/search.php';
            require_once '../realty/inc/classes.php';
            require_once '../photoboard/inc/classes.php';
            require_once '../job/inc/classes.php';
            $on_map = new SearchOnMap($_GET['search_string']);
            $in_realty = new Ads();
            $in_realty->LoadAds(2, 1, " AND (k_isf_description LIKE '%" . $_GET['search_string'] . "%' " . WhereAddress($_GET['search_string']) . ') ', 0);
            $in_photo = new PhotoAdsTable();
            $in_photo->LoadAds(5, 1, array(), WhereWordsPhoto($_GET['search_string']));
            $in_vacancy = new JobAdsSite();
            $in_vacancy->LoadAds(0, 1, array(array('k_j_type'), array(':type'), array(''), array(1)), WhereWordsJob($_GET['search_string']));
            $in_resume = new JobAdsSite();
            $in_resume->LoadAds(0, 1, array(array('k_j_type'), array(':type'), array(''), array(2)), WhereWordsJob($_GET['search_string']));
            $in_blog = new Blog(1, WhereWordsBlog($_GET['search_string']), 0, 2);
            $in_sites = new Sites(1, WhereWordsSites($_GET['search_string']), 0);
            $in_catalog = new Organizations(1, WhereWordsCatalog($_GET['search_string']), 0, 1);
        }
        
        $content_page = new PageContent(13);
        ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="ru">
<head>
    <title>TOMSK-LINE.RU. Каталог.</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="keywords" content="">
        <meta name="description" content="">
        <link rel="stylesheet" type="text/css" href="../css/style.css">
        <link rel="stylesheet" type="text/css" href="css/catalog.css">
        <link rel="stylesheet" type="text/css" href="../css/style_wind.css">
        <link rel="stylesheet" type="text/css" href="../css/show_img.css">
        <link rel="stylesheet" type="text/css" href="../css/search.css">
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script type="text/javascript" src="../js/wind.js"></script>
        <script type="text/javascript" src="../js/show_img.js"></script>
        <script type="text/javascript" src="js/scripts.js"></script>
        <!--Отловить размер окна меню-->
        <script type="text/javascript">
            function ResizeMenu()
            {
                //$(".text_inp_ser").val($('#show_menu').outerWidth());
                if ($('#show_menu').outerWidth() > 1250) {
                    $('#show_menu_1').show(100);
                    $('#show_menu_2').hide(100);
                } else  {
                    $('#show_menu_1').hide(100);
                    $('#show_menu_2').show(100);
                }
                var w = Math.round($('.reklama').width()/2-60);
                $("#banner1").width(w);
                $("#banner2").width(w);
                $("#banner3").width(w);
                $("#banner4").width(w);
            }
            $(window).resize(function() {
                ResizeMenu();
            });
            $(window).ready(function() {
                ResizeMenu();
            });
        </script>
        <!--Отловить размер окна меню-->
    </head>
    <body>
    <?php
    require_once '../inc/header.php';
    ?>

            <div class="all_catalog_block">

                <div class="left_catalog_block">
                    <?php
                    for ($i = 0; $i < count($banners->banner_id); $i++) {
                        if ($banners->banner_type[$i] == 8 && $banners->banner_end_days[$i] > 0) {
                            ?>
                            <div class="reklama_catalog">
                                <?php
                                echo str_replace('../images/banners/', '../admin/images/banners/', $banners->banner_code[$i]);
                                ?>
                            </div>
                            <br>
                            <?php
                        }
                    }
                    ?>
                </div>
                <div class="center_catalog_block" <?php if (!isset($_GET['Howto'])) echo 'style="display: none;"'; ?>>
                    <div class="all_redactor">
                        <?php
                        echo $content_page->text;
                        ?>
                    </div>
                </div>
                <div class="center_catalog_block" <?php if (isset($_GET['Howto'])) echo 'style="display: none;"'; ?>>
                    <?php
                    if (!isset($_GET['search_string'])) {
                        ?>
                        <?php
                        if ($ShowParamID == 3) {
                            $categories = new CatalogCategories();
                            //var_dump($categories);
                            $in_all = (int) (count($categories->id) / 4);
                            $ost = count($categories->id) % 4;

                            $shift = 0;
                            //<span class="nambe_of">1567</span>
                            echo '<div class="rubrik_catalog_1">';
                            for ($n = (0 + $shift); $n < ($in_all + $shift); $n++) {
                                //href="?PageType=2&Categorie=' . urlencode($categories->id[$n]) . '"
                                echo '<div style="margin-bottom: 10px;"><a href="?PageType=2&Categorie=' . urlencode($categories->id[$n]) . '" class="rubrika_catalog_text">' . $categories->name[$n] . '</a>
                                        <p class="next_ru">';
                                $where = " WHERE category= '". $categories->id[$n]."' ";
                                $mlimit = 20;
                                $subcategories = new SubSubCategories(1, $where, ($mlimit+2));
                                if (count($subcategories->id) > $mlimit) {
                                    $max = $mlimit;
                                } else {
                                    $max = count($subcategories->id);
                                }
                                for ($k = 0; $k < $max; $k++) {
                                    echo (($k!=0)?',</a> ':'').'<a href="?PageType=1&ss=' . urlencode($subcategories->id_ss[$k]) . '">' . trim($subcategories->name_ss[$k]) . '';
                                }
                                echo '</a>';
                                if (count($subcategories->id) > $mlimit) {
                                    echo "<a href=\"?PageType=2&Categorie='" . urlencode($categories->id[$n]) . "'\">, ...</a>";
                                }
                                echo '</p></div>';
                            }
                            $shift += $in_all;
                            if ($ost > 0) {
                                //href="?PageType=2&Categorie=' . urlencode($categories->id[$n]) . '"
                                echo '<div style="margin-bottom: 10px;">
                                    <a href="?PageType=2&Categorie=' . urlencode($categories->id[$n]) . '" class="rubrika_catalog_text">' . $categories->name[$shift] . '</a>
                                    <p class="next_ru">';
                                $where = " WHERE category= '". $categories->id[$n]."' ";
                                $mlimit = 20;
                                $subcategories = new SubSubCategories(1, $where, ($mlimit+2));
                                if (count($subcategories->id) > $mlimit) {
                                    $max = $mlimit;
                                } else {
                                    $max = count($subcategories->id);
                                }
                                for ($k = 0; $k < $max; $k++) {
                                    echo (($k!=0)?',</a> ':'').'<a href="?PageType=1&ss=' . urlencode($subcategories->id_ss[$k]) . '">' . trim($subcategories->name_ss[$k]) . '';
                                }
                                echo '</a>';
                                if (count($subcategories->id) > $mlimit) {
                                    echo "<a href=\"?PageType=2&Categorie='" . urlencode($categories->id[$n]) . "'\">, ...</a>";
                                }
                                echo '</p></div>';
                                $shift++;
                            }
                            echo '</div>';

                            echo '<div class="rubrik_catalog_2">';
                            for ($n = (0 + $shift); $n < ($in_all + $shift); $n++) {
                                //href="?PageType=2&Categorie=' . urlencode($categories->id[$n]) . '"
                                echo '<div style="margin-bottom: 10px;"><a href="?PageType=2&Categorie=' . urlencode($categories->id[$n]) . '"  class="rubrika_catalog_text">' . $categories->name[$n] . '</a>
                                    <p class="next_ru">';
                                $where = " WHERE category= '". $categories->id[$n]."' ";
                                $mlimit = 20;
                                $subcategories = new SubSubCategories(1, $where, ($mlimit+2));
                                if (count($subcategories->id) > $mlimit) {
                                    $max = $mlimit;
                                } else {
                                    $max = count($subcategories->id);
                                }
                                for ($k = 0; $k < $max; $k++) {
                                    echo (($k!=0)?',</a> ':'').'<a href="?PageType=1&ss=' . urlencode($subcategories->id_ss[$k]) . '&Categorie=' . urlencode($categories->id[$n]) . '">' . trim($subcategories->name_ss[$k]) . '';
                                }
                                echo '</a>';
                                if (count($subcategories->id) > $mlimit) {
                                    echo "<a href=\"?PageType=2&Categorie='" . urlencode($categories->id[$n]) . "'\">, ...</a>";
                                }
                                echo '</p></div>';
                            }
                            $shift += $in_all;
                            if ($ost > 0) {
                                //href="?PageType=2&Categorie=' . urlencode($categories->id[$n]) . '"
                                echo '<div style="margin-bottom: 10px;">
                                    <a href="?PageType=2&Categorie=' . urlencode($categories->id[$n]) . '" class="rubrika_catalog_text">' . $categories->name[$shift] . '</a>
                                    <p class="next_ru">';
                                $where = " WHERE category= '". $categories->id[$n]."' ";
                                $mlimit = 20;
                                $subcategories = new SubSubCategories(1, $where, ($mlimit+2));
                                if (count($subcategories->id) > $mlimit) {
                                    $max = $mlimit;
                                } else {
                                    $max = count($subcategories->id);
                                }
                                for ($k = 0; $k < $max; $k++) {
                                    echo (($k!=0)?',</a> ':'').'<a href="?PageType=1&ss=' . urlencode($subcategories->id_ss[$k]) . '&Categorie=' . urlencode($categories->id[$n]) . '">' . trim($subcategories->name_ss[$k]) . '';
                                }
                                echo '</a>';
                                if (count($subcategories->id) > $mlimit) {
                                    echo "<a href=\"?PageType=2&Categorie='" . urlencode($categories->id[$n]) . "'\">, ...</a>";
                                }
                                echo '</p></div>';
                                $shift++;
                            }
                            echo '</div>';

                            echo '<div class="rubrik_catalog_3">';
                            for ($n = (0 + $shift); $n < ($in_all + $shift); $n++) {
                                //href="?PageType=2&Categorie=' . urlencode($categories->id[$n]) . '"
                                echo '<div style="margin-bottom: 10px;"><a href="?PageType=2&Categorie=' . urlencode($categories->id[$n]) . '" class="rubrika_catalog_text">' . $categories->name[$n] . '</a>
                                    <p class="next_ru">';
                                $where = " WHERE category= '". $categories->id[$n]."' ";
                                $mlimit = 20;
                                $subcategories = new SubSubCategories(1, $where, ($mlimit+2));
                                if (count($subcategories->id) > $mlimit) {
                                    $max = $mlimit;
                                } else {
                                    $max = count($subcategories->id);
                                }
                                for ($k = 0; $k < $max; $k++) {
                                    echo (($k!=0)?',</a> ':'').'<a href="?PageType=1&ss=' . urlencode($subcategories->id_ss[$k]) . '&Categorie=' . urlencode($categories->id[$n]) . '">' . trim($subcategories->name_ss[$k]) . '';
                                }
                                echo '</a>';
                                if (count($subcategories->id) > $mlimit) {
                                    echo "<a href=\"?PageType=2&Categorie='" . urlencode($categories->id[$n]) . "'\">, ...</a>";
                                }
                                echo '</p></div>';
                            }
                            $shift += $in_all;
                            if ($ost > 0) {
                                //href="?PageType=2&Categorie=' . urlencode($categories->id[$n]) . '"
                                echo '<div style="margin-bottom: 10px;">
                                    <a href="?PageType=2&Categorie=' . urlencode($categories->id[$n]) . '" class="rubrika_catalog_text">' . $categories->name[$shift] . '</a>
                                    <p class="next_ru">';
                                $where = " WHERE category= '". $categories->id[$n]."' ";
                                $mlimit = 20;
                                $subcategories = new SubSubCategories(1, $where, ($mlimit+2));
                                if (count($subcategories->id) > $mlimit) {
                                    $max = $mlimit;
                                } else {
                                    $max = count($subcategories->id);
                                }
                                for ($k = 0; $k < $max; $k++) {
                                    echo (($k!=0)?',</a> ':'').'<a href="?PageType=1&ss=' . urlencode($subcategories->id_ss[$k]) . '&Categorie=' . urlencode($categories->id[$n]) . '">' . trim($subcategories->name_ss[$k]) . ' ';
                                }
                                echo '</a>';
                                if (count($subcategories->id) > $mlimit) {
                                    echo "<a href=\"?PageType=2&Categorie='" . urlencode($categories->id[$n]) . "'\">, ...</a>";
                                }
                                echo '</p></div>';
                                $shift++;
                            }
                            echo '</div>';

                            echo '<div class="rubrik_catalog_4">';
                            for ($n = (0 + $shift); $n < ($in_all + $shift); $n++) {
                                //href="?PageType=2&Categorie=' . urlencode($categories->id[$n]) . '"
                                echo '<div style="margin-bottom: 10px;"><a href="?PageType=2&Categorie=' . urlencode($categories->id[$n]) . '" class="rubrika_catalog_text">' . $categories->name[$n] . '</a>
                                    <p class="next_ru">';
                                $where = " WHERE category= '". $categories->id[$n]."' ";
                                $mlimit = 20;
                                $subcategories = new SubSubCategories(1, $where, ($mlimit+2));
                                if (count($subcategories->id) > $mlimit) {
                                    $max = $mlimit;
                                } else {
                                    $max = count($subcategories->id);
                                }
                                for ($k = 0; $k < $max; $k++) {
                                    echo (($k!=0)?',</a> ':'').'<a href="?PageType=1&ss=' . urlencode($subcategories->id_ss[$k]) . '&Categorie=' . urlencode($categories->id[$n]) . '">' . trim($subcategories->name_ss[$k]) . '';
                                }
                                echo '</a>';
                                if (count($subcategories->id) > $mlimit) {
                                    echo "<a href=\"?PageType=2&Categorie='" . urlencode($categories->id[$n]) . "'\">, ...</a>";
                                }
                                echo '</p></div>';
                            }
                            $shift += $in_all;
                            if ($ost > 0) {
                                //href="?PageType=2&Categorie=' . urlencode($categories->id[$n]) . '"
                                echo '<div style="margin-bottom: 10px;">
                                    <a href="?PageType=2&Categorie=' . urlencode($categories->id[$n]) . '" class="rubrika_catalog_text">' . $categories->name[$shift] . '</a>
                                    <p class="next_ru">';
                                $where = " WHERE category= '". $categories->id[$n]."' ";
                                $mlimit = 20;
                                $subcategories = new SubSubCategories(1, $where, ($mlimit+2));
                                if (count($subcategories->id) > $mlimit) {
                                    $max = $mlimit;
                                } else {
                                    $max = count($subcategories->id);
                                }
                                for ($k = 0; $k < $max; $k++) {
                                    echo (($k!=0)?',</a> ':'').'<a href="?PageType=1&ss=' . urlencode($subcategories->id_ss[$k]) . '&Categorie=' . urlencode($categories->id[$n]) . '">' . trim($subcategories->name_ss[$k]) . '';
                                }
                                echo '</a>';
                                if (count($subcategories->id) > $mlimit) {
                                    echo "<a href=\"?PageType=2&Categorie='" . urlencode($categories->id[$n]) . "'\">, ...</a>";
                                }
                                echo '</p></div>';
                                $shift++;
                            }
                            echo '</div>';
                            ?>
                            <?php
                        }
                        ?>
                        <?php
                        if ($ShowParamID == 2 || $ShowParamID == 1) {
                            $where = " WHERE category=" . $mysql->quote($_GET['Categorie'])." ";
                            $categories = new CatalogCategories();
                            $subcategories = new CatalogSubCategories($where);
                            ?>
                            <div class="block_content_1">   <!--Подрубрики-->
                                <div class="all_podrubrik_catalog">
                                    <a href="/catalog/" class="name_rubrik"><?php echo $categories->name[array_search($_GET['Categorie'], $categories->id)]; ?></a>
                                    <?php
                                    for ($i = 0; $i < count($subcategories->id_sub); $i++) {
                                        $subsub = new SubSubcategories(1, ' WHERE subcategory=' . $mysql->quote($subcategories->id_sub[$i]), 'unlimit');
                                        //var_dump($subsub);
                                        ?>
                                        <div class="podrubrik_catalog">
                                            <!--<a class="name_podrubrik"><?php echo $subcategories->name_sub[$i]; ?></a>-->
                                            <?php
                                            //var_dump($subsub->id_ss);
                                            $kt = count($subcategories->id_sub)-1;
                                            for ($n = 0; $n < count($subsub->id_ss); $n++) {
                                                echo '<a href="?PageType=1&ss=' . urlencode($subsub->id_ss[$n]) . '&Categorie=' . urlencode($_GET['Categorie']) . '" class="podrubrika_catalog_text">' . $subsub->name_ss[$n] . '<span class="nambe_of">' . $subsub->CountOrgInSubSub($subsub->id_ss[$n]) . '</span>'.(($i!=$kt)?',</a>':'');
                                            }

                                            ?>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                        }
                        ?>

                        <?php
                        if ($ShowParamID == 1) {
                            if (!isset($_GET['limit'])) {
                                $limit = 10;
                            } else {
                                $limit = filter_var($_GET['limit'], FILTER_VALIDATE_INT);
                            }
                            $where = ' WHERE 1 = 1 ';
                            if (isset($_GET['ss'])) {
                                $where .= ' AND subcategory=' . $mysql->quote($_GET['ss']);
                                $link = '&ss=' . urlencode($_GET['ss']) . '&limit=' . $limit;
                                $link2 = '&ss=' . urlencode($_GET['ss']);
                            }
                            if (isset($_GET['Categorie'])) {
                                $where .= ' AND category=' . $mysql->quote($_GET['Categorie']);
                                $link .= '&Categorie=' . urlencode($_GET['Categorie']) ;
                                $link2 .= '&Categorie=' . urlencode($_GET['Categorie']);
                            }
                            if (isset($_GET['id_org'])) {
                                $where = ' WHERE id=' . filter_var($_GET['id_org'], FILTER_VALIDATE_INT);
                                $link = '&id_org=' . filter_var($_GET['id_org'], FILTER_VALIDATE_INT) . '&limit=' . $limit;
                                $link2 = '&id_org=' . filter_var($_GET['id_org'], FILTER_VALIDATE_INT);
                            }
                            $organizations = new Organizations($page, $where, $limit, 2);

                            //var_dump($organizations);
                            ?>
                            <div class="block_content_1 no_padding">
                                <a href="/catalog/" class="name_rubrik nobottom"><?php echo urldecode($_GET['ss']); ?></a>
                                   <!--Переход на просмотр подрубрики-->
                                <?php
                                $organizations->GenerateNavigation($page, $where, $link, $limit);
                                for ($i = 0; $i < count($organizations->id); $i++) {
                                    //if ($i % 2 == 0) {
                                        echo '<div class="element_catalog">';
                                    /*
                                    } else {
                                        echo '<div class="element_catalog_1">';
                                    }
                                    */
                                    $org_addr = new OrganizationAddresses($organizations->id[$i]);
                                    ?>
                                    <div class="fuul_text_element">
                                        <a class="name_text_element"><?php echo $organizations->name[$i]; ?></a><br>
                                        <a class="element_style_4">Адрес: <?php echo $organizations->watches[$i]; ?>
                                            <?php
                                            $tel = array();
                                            if ($organizations->phone1[$i]) {
                                                $tel[] = $organizations->phone1[$i];
                                            }
                                            if ($organizations->phone2[$i]) {
                                                $tel[] = $organizations->phone2[$i];
                                            }
                                            if ($organizations->phone3[$i]) {
                                                $tel[] = $organizations->phone3[$i];
                                            }
                                            if ($organizations->phone4[$i]) {
                                                $tel[] = $organizations->phone4[$i];
                                            }
                                            if (count($tel)>0) {
                                                echo '<br>тел.: '.implode('<br>',$tel);
                                            }
                                            ?>
                                        </a><br>
                                        <a href="/map/?f=<?php echo $organizations->watches[$i]; ?>" target=_blank class="open_map">Показать адрес на карте</a>
                                        <br>
                                        <table style="margin-left: 15px;">
                                            <tr>
                                                <td>
                                                    <?php
                                                    if ($organizations->site[$i]) {
                                                        ?>
                                                        <a class="element_style_1">Сайт:</a>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($organizations->site[$i]) {
                                                        echo '<a href="' . $organizations->site[$i] . '" class="element_style_3">' . $organizations->site[$i] . '</a>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($organizations->email[$i]) {
                                                        ?>
                                                        <a class="element_style_4" onclick="$('#send_email_element').show(500);
                                enableA();">Написать письмо</a>
                                                           <?php
                                                       }
                                                       ?>
                                                </td>
                                            </tr>
                                        </table>
                                        <table style="margin-left: 15px;">
                                            <?php
                                            for ($n = 0; $n < count($org_addr->id); $n++) {
                                                ?>
                                                <?php
                                                if ($org_addr->fid[$n]) {
                                                    ?>
                                                    <tr>
                                                        <td><a class="element_style_1">Адрес:</a></td>
                                                        <td>
                                                            <?php
                                                            echo '<a href="/map/?f=' . $org_addr->address_str[$n] . '" target=_blank class="open_map">';
                                                            ?>
                                                            <?php
                                                            echo $org_addr->address_str[$n];
                                                            if ($org_addr->address_advanced[$n])
                                                                echo ' - ' . $org_addr->address_advanced[$n];
                                                            echo '</a>';
                                                            ?>
                                                            <span class="visible_photo_catalog">
                                                                <?php
                                                                echo '<img class="map_photo" src="../images/photo_1.png" onmouseover="ShowPhoto(this);" alt="' . $org_addr->address[$n] . '">';
                                                                ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                <?php
                                                for ($m = 0; $m < count($org_addr->phones); $m++) {
                                                    if ($org_addr->phones_numb[$m][0] == $org_addr->id[$n] && $org_addr->phones_numb[$m][1]) {
                                                        ?>
                                                        <tr>
                                                            <td><a class="element_style_1"><?php echo $org_addr->phones_types[$m][2]; ?>:</a></td>
                                                            <td><a class="element_style_2"><?php echo $org_addr->phones_numb[$m][1]; ?></a></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                                <tr>
                                                    <td></td>
                                                    <td>
                                                        <?php
                                                        $holidays = array();
                                                        $work_days = array();
                                                        $index = 0;
                                                        $first = TRUE;
                                                        for ($m = 0; $m < count($org_addr->days); $m++) {
                                                            if ($org_addr->days[$m][0] == $org_addr->id[$n]) {
                                                                if (intval($org_addr->days[$m][2]) == 0) {
                                                                    $holidays[] = $org_addr->days[$m][1];
                                                                } else {
                                                                    if ($first) {
                                                                        $work_days[$index] = '<img src="../images/clock_green_1.png" alt="">
                                                                        <a class="dat">' . $org_addr->days[$m][1] . '</a>';
                                                                        if ($org_addr->hours_s[$m][1] == '00:00' && $org_addr->hours_e[$m][1] == '00:00') {
                                                                            $work_days[$index] .= '<a class="ser">круглосуточно</a>';
                                                                        } else {
                                                                            $work_days[$index] .= '<a class="ser">с</a><a class="dat">' . $org_addr->hours_s[$m][1] . '</a>
                                                                        <a class="ser">до</a><a class="dat">' . $org_addr->hours_e[$m][1] . '</a>';
                                                                        }
                                                                        if ($org_addr->hours_b_s[$m][1] == '00:00' && $org_addr->hours_b_e[$m][1] == '00:00') {
                                                                            $work_days[$index] .= '<a class="ser">без перерыва</a>';
                                                                        } else {
                                                                            $work_days[$index] .= '<a class="dat">перерыв</a>
                                                                            <a class="ser">с</a><a class="dat">' . $org_addr->hours_b_s[$m][1] . '</a>
                                                                            <a class="ser">до</a><a class="dat">' . $org_addr->hours_b_e[$m][1] . '</a>';
                                                                        }
                                                                        $work_days[$index] .= '<br>';
                                                                        $index++;
                                                                        $first = FALSE;
                                                                    } else {
                                                                        if ($org_addr->hours_s[$m][1] == $org_addr->hours_s[$m - 1][1] &&
                                                                                $org_addr->hours_e[$m][1] == $org_addr->hours_e[$m - 1][1] &&
                                                                                $org_addr->hours_b_s[$m][1] == $org_addr->hours_b_s[$m - 1][1] &&
                                                                                $org_addr->hours_b_e[$m][1] == $org_addr->hours_b_e[$m - 1][1]) {
                                                                            if (preg_match('/\-/', $work_days[$index - 1])) {
                                                                                $work_days[$index - 1] = str_replace($org_addr->days[$m - 1][1], $org_addr->days[$m][1], $work_days[$index - 1]);
                                                                            } else {
                                                                                $work_days[$index - 1] = str_replace($org_addr->days[$m - 1][1], $org_addr->days[$m - 1][1] . '-' . $org_addr->days[$m][1], $work_days[$index - 1]);
                                                                            }
                                                                        } else {
                                                                            $work_days[$index] = '<img src="../images/clock_green_1.png" alt="">
                                                                            <a class="dat">' . $org_addr->days[$m][1] . '</a>';
                                                                            if ($org_addr->hours_s[$m][1] == '00:00' && $org_addr->hours_e[$m][1] == '00:00') {
                                                                                $work_days[$index] .= '<a class="ser">круглосуточно</a>';
                                                                            } else {
                                                                                $work_days[$index] .= '<a class="ser">с</a><a class="dat">' . $org_addr->hours_s[$m][1] . '</a>
                                                                                <a class="ser">до</a><a class="dat">' . $org_addr->hours_e[$m][1] . '</a>';
                                                                            }
                                                                            if ($org_addr->hours_b_s[$m][1] == '00:00' && $org_addr->hours_b_e[$m][1] == '00:00') {
                                                                                $work_days[$index] .= '<a class="ser">без перерыва</a>';
                                                                            } else {
                                                                                $work_days[$index] .= '<a class="dat">перерыв</a>
                                                                                <a class="ser">с</a><a class="dat">' . $org_addr->hours_b_s[$m][1] . '</a>
                                                                                <a class="ser">до</a><a class="dat">' . $org_addr->hours_b_e[$m][1] . '</a>';
                                                                            }
                                                                            $work_days[$index] .= '<br>';
                                                                            $index++;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        if (count($work_days) > 0) {
                                                            for ($m = 0; $m < count($work_days); $m++) {
                                                                echo $work_days[$m];
                                                            }
                                                        }
                                                        ?>
                                                        <?php
                                                        if (count($holidays) > 0) {
                                                            echo '<img src="../images/clock_red_1.png" alt="">';
                                                            for ($m = 0; $m < count($holidays); $m++) {
                                                                ?>
                                                                <a class="red_a"><?php echo $holidays[$m]; ?></a>
                                                                <?php
                                                            }
                                                            ?>
                                                            <a class="dat">выходной</a>
                                                            <?php
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </table>
                                    </div>
                                    <?php
                                    echo '</div>';
                                    ?>
                                    <?php
                                }
                                ?>
                                <?php
                                $organizations->GenerateNavigation($page, $where, $link, $limit);
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                        <?php
                    }
                    ?>

                    <?php
                    if (isset($_GET['search_string'])) {
                        ?>

                        <?php
                        if (count($in_sites->id) != 0) {
                            ?>
                            <div class="push_all_search">
                                <p class="name_push">Найдено <b>в сайтах</b><span><?php echo count($in_sites->id); ?></span></p>
                                <?php
                                for ($i = 0; $i < count($in_sites->id); $i++) {
                                    $sc_for = new SitesSubcategories(0, ' AND k_sl_site_id=' . $in_sites->id[$i] . ' ');
                                    if ($i % 2 == 0) {
                                        $class = 'artikle_content_1';
                                    } else {
                                        $class = 'artikle_content_2';
                                    }
                                    ?>
                                    <div class="<?php echo $class; ?>">
                                        <div class="block_artikle_img">
                                            <a target="_blank" href="<?php echo $in_sites->url[$i]; ?>">
                                                <?php
                                                if ($in_sites->avatar[$i] && file_exists('../admin/' . $in_sites->avatar[$i])) {
                                                    echo '<img class="img_artikle_content" src="../admin/' . $in_sites->avatar[$i] . '" alt="">';
                                                } else {
                                                    echo '<img class="img_artikle_content" src="../images/noimage.png" alt="">';
                                                }
                                                ?>
                                            </a>
                                        </div>
                                        <div class="block_artikle_text">
                                            <div class="all_artikle_text">
                                                <p class="name_artikle">
                                                    <a target="_blank" href="<?php echo $in_sites->url[$i]; ?>"><?php echo $in_sites->name[$i]; ?></a>
                                                    <span class="nabe_artikle">№ <?php echo $in_sites->id[$i]; ?></span>
                                                </p>
                                                <p class="dannie_artikle">
                                                    <span><?php echo $in_sites->date[$i]; ?></span>
                                                    <?php
                                                    for ($n = 0; $n < count($sc_for->id); $n++) {
                                                        echo '<span class="sp_otst" title="' . $sc_for->name_parent[$n] . '">' . $sc_for->name[$n] . '</span>';
                                                    }
                                                    ?>
                                                </p>
                                                <p class="text_artikle"><?php echo $in_sites->description[$i]; ?></p>
                                                <a class="name_sites" target="_blank" href="<?php echo $in_sites->url[$i]; ?>"><?php echo $in_sites->url[$i]; ?></a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                                <p class="push_all"><a href="">Показать все сайты</a><span><?php echo count($in_sites->id); ?></span></p>
                            </div>
                            <?php
                        }
                        ?>

                        <?php
                        //var_dump(count($in_catalog->id));
                        if (count($in_catalog->id) != 0) {
                            ?>
                            <div class="push_all_search">
                                <p class="name_push">Найдено <b>в предприятиях</b><span><?php echo count($in_catalog->id); ?></span></p>
                                <?php

                                if (count($in_catalog->id) > 5) {
                                    $count = 5;
                                } else {
                                    $count = count($in_catalog->id);
                                }
                                for ($i = 0; $i < $count; $i++) {
                                    if ($i % 2 == 0) {
                                        echo '<div class="element_catalog">';
                                    } else {
                                        echo '<div class="element_catalog_1">';
                                    }
                                    $org_addr = new OrganizationAddresses($in_catalog->id[$i]);
                                    ?>
                                    <div class="fuul_text_element">
                                        <a class="name_text_element"><?php echo $in_catalog->name[$i]; ?></a><br>
                                        <!--<a class="open_map">Показать адреса на карте<span class="nambe_of"><?php echo count($org_addr->id); ?></span></a>
                                        <a class="element_style_4">Просмотров<span class="nambe_of"><?php echo $in_catalog->watches[$i]; ?></span></a><br><br>-->
                                        <a class="element_style_4" href="/map/">Адрес: <?php echo $in_catalog->watches[$i]; ?>
                                            <?php
                                            $tel = array();
                                            if ($in_catalog->phone1[$i]) {
                                                $tel[] = $in_catalog->phone1[$i];
                                            }
                                            if ($in_catalog->phone2[$i]) {
                                                $tel[] = $in_catalog->phone2[$i];
                                            }
                                            if ($in_catalog->phone3[$i]) {
                                                $tel[] = $in_catalog->phone3[$i];
                                            }
                                            if ($in_catalog->phone4[$i]) {
                                                $tel[] = $in_catalog->phone4[$i];
                                            }
                                            if (count($tel)>0) {
                                                echo '<br>тел.: '.implode('<br>',$tel);
                                            }
                                            ?>
                                        </a><br>
                                        <a href="/map/?f=<?php echo $in_catalog->watches[$i]; ?>" target=_blank class="open_map">Показать адрес на карте</a>

                                        <table style="margin-left: 15px;">
                                            <tr>
                                                <td>
                                                    <?php
                                                    if ($in_catalog->site[$i]) {
                                                        ?>
                                                        <a class="element_style_1">Сайт:</a>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($in_catalog->site[$i]) {
                                                        echo '<a href="' . $in_catalog->site[$i] . '" class="element_style_3">' . $in_catalog->site[$i] . '</a>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($in_catalog->email[$i]) {
                                                        ?>
                                                        <a class="element_style_4" onclick="$('#send_email_element').show(500);
                                                            enableA();">Написать письмо</a>
                                                           <?php
                                                       }
                                                       ?>
                                                </td>
                                            </tr>
                                        </table>
                                        <!--
                                        <table style="margin-left: 15px;">
                                            <?php
                                            for ($n = 0; $n < count($org_addr->id); $n++) {
                                                ?>
                                                <?php
                                                if ($org_addr->fid[$n]) {
                                                    ?>
                                                    <tr>
                                                        <td><a class="element_style_1">Адрес:</a></td>
                                                        <td>
                                                            <?php
                                                            echo '<a href="/map/?f=' . $org_addr->address_str[$n] . '" class="open_map">';
                                                            ?>
                                                            <?php
                                                            echo $org_addr->address_str[$n];
                                                            if ($org_addr->address_advanced[$n])
                                                                echo ' - ' . $org_addr->address_advanced[$n];
                                                            echo '</a>';
                                                            ?>
                                                            <span class="visible_photo_catalog">
                                                                <?php
                                                                echo '<img class="map_photo" src="../images/photo_1.png" onmouseover="ShowPhoto(this);" alt="' . $org_addr->address[$n] . '">';
                                                                ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                <?php
                                                for ($m = 0; $m < count($org_addr->phones); $m++) {
                                                    if ($org_addr->phones_numb[$m][0] == $org_addr->id[$n] && $org_addr->phones_numb[$m][1]) {
                                                        ?>
                                                        <tr>
                                                            <td><a class="element_style_1"><?php echo $org_addr->phones_types[$m][2]; ?>:</a></td>
                                                            <td><a class="element_style_2"><?php echo $org_addr->phones_numb[$m][1]; ?></a></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                                <tr>
                                                    <td></td>
                                                    <td>
                                                        <?php
                                                        $holidays = array();
                                                        $work_days = array();
                                                        $index = 0;
                                                        $first = TRUE;
                                                        for ($m = 0; $m < count($org_addr->days); $m++) {
                                                            if ($org_addr->days[$m][0] == $org_addr->id[$n]) {
                                                                if (intval($org_addr->days[$m][2]) == 0) {
                                                                    $holidays[] = $org_addr->days[$m][1];
                                                                } else {
                                                                    if ($first) {
                                                                        $work_days[$index] = '<img src="../images/clock_green_1.png" alt="">
                                                                        <a class="dat">' . $org_addr->days[$m][1] . '</a>';
                                                                        if ($org_addr->hours_s[$m][1] == '00:00' && $org_addr->hours_e[$m][1] == '00:00') {
                                                                            $work_days[$index] .= '<a class="ser">круглосуточно</a>';
                                                                        } else {
                                                                            $work_days[$index] .= '<a class="ser">с</a><a class="dat">' . $org_addr->hours_s[$m][1] . '</a>
                                                                        <a class="ser">до</a><a class="dat">' . $org_addr->hours_e[$m][1] . '</a>';
                                                                        }
                                                                        if ($org_addr->hours_b_s[$m][1] == '00:00' && $org_addr->hours_b_e[$m][1] == '00:00') {
                                                                            $work_days[$index] .= '<a class="ser">без перерыва</a>';
                                                                        } else {
                                                                            $work_days[$index] .= '<a class="dat">перерыв</a>
                                                                            <a class="ser">с</a><a class="dat">' . $org_addr->hours_b_s[$m][1] . '</a>
                                                                            <a class="ser">до</a><a class="dat">' . $org_addr->hours_b_e[$m][1] . '</a>';
                                                                        }
                                                                        $work_days[$index] .= '<br>';
                                                                        $index++;
                                                                        $first = FALSE;
                                                                    } else {
                                                                        if ($org_addr->hours_s[$m][1] == $org_addr->hours_s[$m - 1][1] &&
                                                                                $org_addr->hours_e[$m][1] == $org_addr->hours_e[$m - 1][1] &&
                                                                                $org_addr->hours_b_s[$m][1] == $org_addr->hours_b_s[$m - 1][1] &&
                                                                                $org_addr->hours_b_e[$m][1] == $org_addr->hours_b_e[$m - 1][1]) {
                                                                            if (preg_match('/\-/', $work_days[$index - 1])) {
                                                                                $work_days[$index - 1] = str_replace($org_addr->days[$m - 1][1], $org_addr->days[$m][1], $work_days[$index - 1]);
                                                                            } else {
                                                                                $work_days[$index - 1] = str_replace($org_addr->days[$m - 1][1], $org_addr->days[$m - 1][1] . '-' . $org_addr->days[$m][1], $work_days[$index - 1]);
                                                                            }
                                                                        } else {
                                                                            $work_days[$index] = '<img src="../images/clock_green_1.png" alt="">
                                                                            <a class="dat">' . $org_addr->days[$m][1] . '</a>';
                                                                            if ($org_addr->hours_s[$m][1] == '00:00' && $org_addr->hours_e[$m][1] == '00:00') {
                                                                                $work_days[$index] .= '<a class="ser">круглосуточно</a>';
                                                                            } else {
                                                                                $work_days[$index] .= '<a class="ser">с</a><a class="dat">' . $org_addr->hours_s[$m][1] . '</a>
                                                                                <a class="ser">до</a><a class="dat">' . $org_addr->hours_e[$m][1] . '</a>';
                                                                            }
                                                                            if ($org_addr->hours_b_s[$m][1] == '00:00' && $org_addr->hours_b_e[$m][1] == '00:00') {
                                                                                $work_days[$index] .= '<a class="ser">без перерыва</a>';
                                                                            } else {
                                                                                $work_days[$index] .= '<a class="dat">перерыв</a>
                                                                                <a class="ser">с</a><a class="dat">' . $org_addr->hours_b_s[$m][1] . '</a>
                                                                                <a class="ser">до</a><a class="dat">' . $org_addr->hours_b_e[$m][1] . '</a>';
                                                                            }
                                                                            $work_days[$index] .= '<br>';
                                                                            $index++;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        if (count($work_days) > 0) {
                                                            for ($m = 0; $m < count($work_days); $m++) {
                                                                echo $work_days[$m];
                                                            }
                                                        }
                                                        ?>
                                                        <?php
                                                        if (count($holidays) > 0) {
                                                            echo '<img src="../images/clock_red_1.png" alt="">';
                                                            for ($m = 0; $m < count($holidays); $m++) {
                                                                ?>
                                                                <a class="red_a"><?php echo $holidays[$m]; ?></a>
                                                                <?php
                                                            }
                                                            ?>
                                                            <a class="dat">выходной</a>
                                                            <?php
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </table>
                                        -->
                                    </div>
                                    <?php
                                    echo '</div>';
                                    ?>
                                    <?php
                                }
                                ?>
                                <p class="push_all"><a href="">Показать все предприятия</a><span><?php echo count($in_catalog->id); ?></span></p>
                            </div>
                            <?php
                        }
                        ?>

                        <?php
                        if ($on_map->count != 0) {
                            ?>
                            <div class="push_all_search">
                                <p class="name_push">Найдено <b>на карте</b><span><?php echo $on_map->count; ?></span></p>
                                <?php
                                if ($on_map->count > 5) {
                                    $count = 5;
                                } else {
                                    $count = $on_map->count;
                                }
                                for ($i = 0; $i < $count; $i++) {
                                    echo '<p class="push_all"><a href="/map/?f=' . $on_map->address_str[$i] . '">' . $on_map->address_str[$i] . '</a><img class="push_img" src="../images/photo_1.png" alt=""></p>';
                                }
                                ?>
                            </div>
                            <?php
                        }
                        ?>

                        <?php
                        if ($in_realty->total != 0) {
                            ?>
                            <div class="push_all_search">
                                <p class="name_push">Найдено <b>в недвижимости</b><span><?php echo $in_realty->total; ?></span></p>
                                <?php
                                $in_realty->GenerateTable();
                                if ($in_realty->total > 0) {
                                    ?>
                                    <p class="push_all"><a href="">Показать все предложения</a><span><?php echo $in_realty->total; ?></span></p>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                        }
                        ?>

                        <?php
                        if ($in_photo->all_ads != 0) {
                            ?>
                            <div class="push_all_search">
                                <p class="name_push">Найдено <b>в фото объявлениях</b><span><?php echo $in_photo->all_ads; ?></span></p>
                                <?php
                                for ($i = 1; $i <= count($in_photo->getID(0)); $i++) {
                                    ?>
                                    <div class="free_img">
                                        <p class="free_text_1">
                                            <a class="l_txt" title="За сегодня просмотров">+<?php echo $in_photo->getViewsDays($i); ?></a>
                                            <a class="r_txt" title="Всего просмотров"><?php echo $in_photo->getViews($i); ?></a>
                                        </p>
                                        <div class="over_img_photodoska_2">
                                            <?php
                                            echo '<a href="../photoboard/?PageType=2&PhotoNum=' . $in_photo->getID($i) . '">';
                                            if ($in_photo->getPhoto($i) && file_exists('../admin/' . str_replace('photo/', 'photo/1_', $in_photo->getPhoto($i)))) {
                                                echo '<img class="free_img_1" src="../admin/' . str_replace('photo/', 'photo/1_', $in_photo->getPhoto($i)) . '" alt="">';
                                            } else {
                                                echo '<img class="free_img_1" src="../images/noimage.png" alt="">';
                                            }
                                            echo '</a>';
                                            ?>
                                        </div>
                                        <p class="free_text_2" title="Товар"><?php echo $in_photo->getTheme($i); ?></p>
                                        <p class="free_text_2" title="Цена в руб"><?php echo $in_photo->getPrice($i); ?> руб.</p>
                                    </div>
                                    <?php
                                }
                                if (count($in_photo->getID(0)) > 0) {
                                    ?>
                                    <p class="push_all"><a href="">Показать все фото объявления</a><span><?php echo $in_photo->all_ads; ?></span></p>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                        }
                        ?>

                        <?php
                        if (count($in_vacancy->id) != 0) {
                            ?>
                            <div class="push_all_search">
                                <?php
                                if (count($in_vacancy->id) > 5) {
                                    $count = 5;
                                } else {
                                    $count = count($in_vacancy->id);
                                }
                                ?>
                                <p class="name_push">Найдено <b>в вакансиях</b><span><?php echo $count; ?></span></p>
                                <?php
                                for ($i = 0; $i < $count; $i++) {
                                    ?>
                                    <div class="obiavlenie_job">
                                        <table class="tab_job_elem">
                                            <tr class="tab_job_elem_tr_1">
                                                <td class="tab_job_elem_td">
                                                    <p class="treb_text_5">
                                                        <?php
                                                        echo $in_vacancy->date_reg[$i];
                                                        ?>
                                                    </p>
                                                </td>
                                                <td><p class="treb_text"><a <?php echo 'href="./?PageType=4&Id=' . $in_vacancy->id[$i] . '"'; ?> class="treb_text_1"><?php echo $in_vacancy->post[$i]; ?></a></p></td>
                                            </tr>
                                            <tr class="tab_job_elem_tr_2">
                                                <td><p class="treb_text_5">График:</p></td>
                                                <td>
                                                    <p class="treb_text_6">
                                                        <?php
                                                        if ($in_vacancy->schedule[$i]) {
                                                            echo '<a class="treb_text_4">' . $in_vacancy->schedule[$i] . '</a>';
                                                        }
                                                        ?>
                                                    </p> 
                                                </td>
                                            </tr>
                                            <tr class="tab_job_elem_tr_2">
                                                <td><p class="treb_text_5">Возраст:</p></td>
                                                <td>
                                                    <p class="treb_text_6">
                                                        <?php
                                                        if ($in_vacancy->age_min[$i]) {
                                                            echo '<a class="treb_text_4">от ' . $in_vacancy->age_min[$i] . ' ' . plural_form($in_vacancy->age_min[$i], 'год', 'лет') . '</a> ';
                                                        } elseif ($in_vacancy->age_max[$i]) {
                                                            echo '<a class="treb_text_4">до ' . $in_vacancy->age_max[$i] . ' ' . plural_form($in_vacancy->age_max[$i], 'год', 'лет') . '</a>';
                                                        }
                                                        if ($in_vacancy->age_max[$i] && $in_vacancy->age_min[$i]) {
                                                            echo '<a class="treb_text_4">до ' . $in_vacancy->age_max[$i] . ' лет' . '</a>';
                                                        }
                                                        ?>
                                                    </p> 
                                                </td>
                                            </tr>
                                            <tr class="tab_job_elem_tr_2">
                                                <td><p class="treb_text_5">Зарплата:</p></td>
                                                <td>
                                                    <p class="treb_text_6">
                                                        <?php
                                                        if ($in_vacancy->salary_min[$i]) {
                                                            echo '<a class="treb_text_4">от ' . $in_vacancy->salary_min[$i] . ' ' . $in_vacancy->currency_str[$i] . '/месяц</a>';
                                                        } elseif ($in_vacancy->salary_max[$i]) {
                                                            echo '<a class="treb_text_4">до ' . $in_vacancy->salary_max[$i] . ' ' . $in_vacancy->currency_str[$i] . '/месяц' . '</a>';
                                                        }
                                                        if ($in_vacancy->salary_max[$i] && $in_vacancy->salary_min[$i]) {
                                                            echo '<a class="treb_text_4">до ' . $in_vacancy->salary_max[$i] . ' ' . $in_vacancy->currency_str[$i] . '/месяц' . '</a>';
                                                        }
                                                        ?>
                                                    </p> 
                                                </td>
                                            </tr>
                                            <tr class="tab_job_elem_tr_2">
                                                <td><p class="treb_text_5">Компания:</p></td>
                                                <td>
                                                    <p class="treb_text_6">
                                                        <?php
                                                        if ($in_vacancy->organization_name[$i]) {
                                                            echo '<a class="treb_text_4">' . $in_vacancy->organization_name[$i] . '</a>';
                                                        }
                                                        ?>
                                                    </p> 
                                                </td>
                                            </tr>
                                            <tr class="tab_job_elem_tr_2">
                                                <td><p class="treb_text_5">Телефон:</p></td>
                                                <td>
                                                    <p class="treb_text_6">
                                                        <?php
                                                        if ($in_vacancy->contact_phone[$i]) {
                                                            echo '<span class="treb_text_4">' . $in_vacancy->contact_phone[$i] . '</span>';
                                                        }
                                                        ?>
                                                    </p> 
                                                </td>
                                            </tr>
                                            <tr class="tab_job_elem_tr_2">
                                                <td><p class="treb_text_5">Разместил:</p></td>
                                                <td>
                                                    <p class="treb_text_6">
                                                        <?php
                                                        if ($in_vacancy->contact_name[$i]) {
                                                            echo '<span class="treb_text_4">' . $in_vacancy->contact_name[$i] . '</span>';
                                                        }
                                                        ?>
                                                    </p> 
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <?php
                                }
                                if ($count > 0) {
                                    ?>
                                    <p class="push_all"><a href="">Показать все вакансии</a><span><?php echo $count; ?></span></p>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                        }
                        ?>

                        <?php
                        if (count($in_resume->id) != 0) {
                            ?>
                            <div class="push_all_search">
                                <?php
                                if (count($in_resume->id) > 5) {
                                    $count = 5;
                                } else {
                                    $count = count($in_resume->id);
                                }
                                ?>
                                <p class="name_push">Найдено <b>в резюме</b><span><?php echo $count; ?></span></p>
                                <?php
                                for ($i = 0; $i < count($in_resume->id); $i++) {
                                    ?>
                                    <div class="obiavlenie_job_1">
                                        <?php
                                        echo '<a href="./?PageType=5&Id=' . $in_resume->id[$i] . '">';
                                        if ($in_resume->avatar[$i] && file_exists(str_replace('job/', 'job/1_', '../admin/' . $in_resume->avatar[$i]))) {
                                            echo '<img class="img_search_job" src="../admin/' . str_replace('job/', 'job/1_', $in_resume->avatar[$i]) . '" alt="">';
                                        } else {
                                            echo '<img class="img_search_job" src="../images/noimage.png" alt="">';
                                        }
                                        echo '</a>';
                                        ?>
                                        <div class="fuul_text_job">
                                            <table class="tab_job_elem">
                                                <tr class="tab_job_elem_tr_1">
                                                    <td class="tab_job_elem_td">
                                                        <p class="treb_text_5">
                                                            <?php
                                                            echo $in_resume->date_reg[$i];
                                                            ?>
                                                        </p>
                                                    </td>
                                                    <td><p class="treb_text"><a <?php echo 'href="../job/?PageType=5&Id=' . $in_resume->id[$i] . '"'; ?> class="treb_text_1"><?php echo $in_resume->post[$i]; ?></a></p></td>
                                                </tr>
                                                <tr class="tab_job_elem_tr_2">
                                                    <td><p class="treb_text_5">График:</p></td>
                                                    <td>
                                                        <p class="treb_text_6">
                                                            <?php
                                                            if ($in_resume->schedule[$i]) {
                                                                echo '<a class="treb_text_4">' . $in_resume->schedule[$i] . '</a>';
                                                            }
                                                            ?>
                                                        </p> 
                                                    </td>
                                                </tr>
                                                <tr class="tab_job_elem_tr_2">
                                                    <td><p class="treb_text_5">Возраст:</p></td>
                                                    <td>
                                                        <p class="treb_text_6">
                                                            <?php
                                                            if ($in_resume->age_min[$i]) {
                                                                echo '<a class="treb_text_4">' . $in_resume->age_min[$i] . ' ' . plural_form($in_resume->age_min[$i], 'год', 'лет') . '</a>';
                                                            }
                                                            ?>
                                                        </p> 
                                                    </td>
                                                </tr>
                                                <tr class="tab_job_elem_tr_2">
                                                    <td><p class="treb_text_5">Зарплата:</p></td>
                                                    <td>
                                                        <p class="treb_text_6">
                                                            <?php
                                                            if ($in_resume->salary_min[$i]) {
                                                                echo '<a class="treb_text_4">от ' . $in_resume->salary_min[$i] . ' ' . $in_resume->currency_str[$i] . '/месяц</a>';
                                                            } elseif ($in_resume->salary_max[$i]) {
                                                                echo '<a class="treb_text_4">до ' . $in_resume->salary_max[$i] . ' ' . $in_resume->currency_str[$i] . '/месяц' . '</a>';
                                                            }
                                                            if ($in_resume->salary_max[$i] && $in_resume->salary_min[$i]) {
                                                                echo '<a class="treb_text_4">до ' . $in_resume->salary_max[$i] . ' ' . $in_resume->currency_str[$i] . '/месяц' . '</a>';
                                                            }
                                                            ?>
                                                        </p> 
                                                    </td>
                                                </tr>
                                                <tr class="tab_job_elem_tr_2">
                                                    <td><p class="treb_text_5">Телефон:</p></td>
                                                    <td>
                                                        <p class="treb_text_6">
                                                            <?php
                                                            if ($in_resume->contact_phone[$i]) {
                                                                echo '<span class="treb_text_4">' . $in_resume->contact_phone[$i] . '</span>';
                                                            }
                                                            ?>
                                                        </p> 
                                                    </td>
                                                </tr>
                                                <tr class="tab_job_elem_tr_2">
                                                    <td><p class="treb_text_5">Разместил:</p></td>
                                                    <td>
                                                        <p class="treb_text_6">
                                                            <?php
                                                            if ($in_resume->contact_name[$i]) {
                                                                echo '<span class="treb_text_4">' . $in_resume->contact_name[$i] . '</span>';
                                                            }
                                                            ?>
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <?php
                                }
                                if ($count > 0) {
                                    ?>
                                    <p class="push_all"><a href="">Показать все резюме</a><span><?php echo $count; ?></span></p>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                        }
                        ?>

                        <?php
                        if ($in_blog->all != 0) {
                            ?>
                            <div class="push_all_search">
                                <p class="name_push">Найдено <b>в статьях</b><span><?php echo $in_blog->all; ?></span></p>
                                <?php
                                for ($i = 0; $i < count($in_blog->id); $i++) {
                                    if ($i % 2 == 0) {
                                        $class = 'artikle_content_1';
                                    } else {
                                        $class = 'artikle_content_2';
                                    }
                                    ?>
                                    <div class="<?php echo $class; ?>">
                                        <div class="block_artikle_img">
                                            <a <?php echo 'href="../blog/?PageType=2&ID=' . $in_blog->id[$i] . '"'; ?>>
                                                <?php
                                                if ($in_blog->image[$i] && file_exists('../admin/' . $in_blog->image[$i])) {
                                                    echo '<img class="img_artikle_content" title="' . $in_blog->name[$i] . '" src="../admin/' . $in_blog->image[$i] . '" alt="">';
                                                } else {
                                                    echo '<img class="img_artikle_content" title="' . $in_blog->name[$i] . '" src="../images/noimage.png" alt="">';
                                                }
                                                ?>
                                            </a>
                                        </div>
                                        <div class="block_artikle_text">
                                            <div class="all_artikle_text">
                                                <p class="name_artikle">
                                                    <a <?php echo 'href="../blog/?PageType=2&ID=' . $in_blog->id[$i] . '"'; ?>><?php echo $in_blog->name[$i]; ?></a>
                                                    <span class="nabe_artikle">№ <?php echo $in_blog->id[$i]; ?></span>
                                                </p>
                                                <p class="dannie_artikle"><span><?php echo $in_blog->date[$i]; ?>.</span><span class="sp_otst">Просмотров: <?php echo $in_blog->views[$i]; ?>.</span><span class="sp_otst">Статью добавил:<a><?php echo $in_blog->user[$i]; ?></a></span></p>
                                                <p class="text_artikle"><?php echo $in_blog->brief[$i]; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                                <p class="push_all"><a href="">Показать все статьи</a><span><?php echo $in_blog->all; ?></span></p>
                            </div>
                            <?php
                        }
                        ?>

                        <?php
                        if ($on_map->count == 0 &&
                                $in_realty->total == 0 &&
                                $in_photo->all_ads == 0 &&
                                count($in_vacancy->id) == 0 &&
                                count($in_resume->id) == 0 &&
                                $in_blog->all == 0 &&
                                count($in_sites->id) == 0 &&
                                count($in_catalog->id) == 0) {
                            ?>
                            <div>
                                Ничего не найдено!
                            </div>
                            <?php
                        }
                        ?>

                        <?php
                    }
                    ?>

                </div>

                <div class="right_catalog_block">
                    <?php
                    for ($i = 0; $i < count($banners->banner_id); $i++) {
                        if ($banners->banner_type[$i] == 9 && $banners->banner_end_days[$i] > 0) {
                            ?>
                            <div class="reklama_catalog">
                                <?php
                                echo str_replace('../images/banners/', '../admin/images/banners/', $banners->banner_code[$i]);
                                ?>
                            </div>
                            <br>
                            <?php
                        }
                    }
                    ?>
                </div>

            </div>

    <?php
    require_once '../inc/footer.php';
    ?>

            <!--ВСПЛЫВАЮЩИЕ ОКНА-->

            <div id="send_email_element" class="wind">       <!--Всплывающее окно формы отправки письма-->
                <a class="close" onclick="document.getElementById('send_email_element').style.display = 'none';
                disableA();">X</a>
                <br>
                <p class="style_wind_3">Написать письмо</p>
                <p class="style_wind_1">Поля помеченные * обязательны для заполнения</p>
                <table>
                    <tr>
                        <td><p class="style_wind_3_1">Получатель:</p></td>
                        <td><p class="name_text_element_1">Авангард, акционерный коммерческий банк, кредитно-кассовый офис в Томске</p></td>
                    </tr>
                    <tr>
                        <td><p class="style_wind_3_1">E-mail отправителя:<font color="red">*</font></p></td>
                    <td><input class="catalog_inp" type="text" value=""></td>
                    </tr>
                    <tr>
                        <td><p class="style_wind_3_1">Тема:<font color="red">*</font></p></td>
                    <td><input class="catalog_inp" type="text" value=""></td>
                    </tr>
                    <tr>
                        <td colspan="2"><p class="style_wind_3_1">Сообщение:<font color="red">*</font></p><br>
                    <textarea rows="10" cols="57" name="text"></textarea></td>
                    </tr>
                    <tr>
                        <td colspan="2"><button class="act_2" style="float: left;">Отправить письмо</button></td>
                    </tr>
                </table>
            </div>

            <div id="wind_poto_karti" class="wind_karta">       <!--Всплывающее окно просмотра картинок карты-->
                <a class="close_2" onclick="document.getElementById('wind_poto_karti').style.display = 'none';
                disableA();">X</a>
                <div class="block_listing">
                    <img id="photo_karta_1" class="im_2" src="../images/karta_1.png" alt="">
                    <img id="photo_karta_2" class="im_2" src="../images/karta_2.png" alt="">
                    <img id="photo_karta_3" class="im_2" src="../images/karta_3.png" alt="">
                    <input type="hidden" id="count_im" value="1">
                </div>
                <div class="listings">
                    <a onclick="prevImage(3);"><img class="left_list" src="../images/left_4.png" alt="Листать влево"></a>
                    <a onclick="changeImage(1);"><img class="mini_listing_img" src="../images/karta_1.png" alt=""></a>
                    <a onclick="changeImage(2);"><img class="mini_listing_img" src="../images/karta_2.png" alt=""></a>
                    <a onclick="changeImage(3);"><img class="mini_listing_img" src="../images/karta_3.png" alt=""></a>
                    <a onclick="nextImage(3);"><img class="right_list" src="../images/right_4.png" alt="Листать вправо"></a>
                </div>
            </div>

            <div class="temno" id="temno"></div>
            <div class="prozrachno" id="prozrachno" onclick="$('.vhod_block_gl').hide(500);
                disableP();"></div>   <!--Всплывающие окна прозрачное для закрытия входа-->
            <div class="photo_map_vsplivaet">
                <img src="" alt="">
            </div>
        </div>
    </body>
</html>