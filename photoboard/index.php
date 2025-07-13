<?php
define('TOMSKLINE', 1);
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="ru">
    <head>
        <title>TOMSK-LINE.RU. Фото-объявления.</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="keywords" content="">
        <meta name="description" content="">
        <link rel="stylesheet" type="text/css" href="../css/style.css">
        <link rel="stylesheet" type="text/css" href="../css/search.css">
        <link rel="stylesheet" type="text/css" href="css/photo_board.css">
        <link rel="stylesheet" type="text/css" href="../css/style_wind.css">
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script type="text/javascript" src="../js/wind.js"></script>
        <script type="text/javascript" src="js/scripts.js"></script>
        <?php
        $ShowParamID = 1;
        $Category = 0;
        $Ad_num = 0;
        $VIPlimit = 7;
        $limit = 14;
        $page = 1;
        $where = '';
        $link = '';

        if ($ShowParamID == 1) {
            $cat_arr = array();
            $vars_cat = array();
            $cond_arr = array();
            $values_arr = array();
        }
        require_once '../inc/configs.php';
        require_once 'inc/classes.php';
        require_once 'inc/functions.php';
        require_once '../inc/functions.php';
        require_once '../admin/admin_photo/inc/classes.php';
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
            if ($result) {
                $_SESSION['id'] = $result['k_ku_id'];
                $_SESSION['privileges'] = $result['k_u_privileges'];
            }
        } catch (PDOException $e) {
            exit();
        }

        $banners = new BannersAll(0);

        if (empty($ShowParamID)) {
            $ShowParamID = 1;
        }

        if (!in_array($ShowParamID, array(1, 2, 13))) {
            header($_ENV['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
            header("Location: http://" . _SERVER_ADDRESS . "/page404.php");
        }

        if (isset($_GET['Word'])) {
            $_GET['Word'] = preg_replace('/\\s+/', ' ', $_GET['Word']);
            $search_array = explode(' ', $_GET['Word']);
            $where = ' WHERE (';
            $link .= '&Word=';
            $reg = "/(ый|ой|ая|ия|ий|ое|ые|ому|а|о|у|е|ого|ему|и|ство|ых|ох|я|ют|ат|ок)$/i";
            foreach ($search_array as $value) {
                if (strlen($value) > 2) {
                    $value = preg_replace($reg, '', $value);
                }
                $where .= ' k_pd_text LIKE "%' . $value . '%" OR k_pd_theme LIKE "%' . $value . '%" OR ';
                $link .= $value . '+';
            }
            $link = substr($link, 0, strlen($link) - 1);
            $where = substr($where, 0, strlen($where) - 3);
            $where .= ') ';
        }

        if (isset($_GET['search_string'])) {
            require_once '../inc/search.php';
            require_once '../realty/inc/classes.php';
            require_once '../admin/admin_catalog/inc/classes.php';
            require_once '../job/inc/classes.php';
            $on_map = new SearchOnMap($_GET['search_string']);
            $in_realty = new Ads();
            $in_realty->LoadAds(2, 1, " AND (k_isf_description LIKE '%" . $_GET['search_string'] . "%' " . WhereAddress($_GET['search_string']) . ') ', 0);
            $in_photo = new PhotoAdsTable();
            $in_photo->LoadAds(6, 1, array(), WhereWordsPhoto($_GET['search_string']));
            $in_vacancy = new JobAdsSite();
            $in_vacancy->LoadAds(0, 1, array(array('k_j_type'), array(':type'), array(''), array(1)), WhereWordsJob($_GET['search_string']));
            $in_resume = new JobAdsSite();
            $in_resume->LoadAds(0, 1, array(array('k_j_type'), array(':type'), array(''), array(2)), WhereWordsJob($_GET['search_string']));
            $in_blog = new Blog(1, WhereWordsBlog($_GET['search_string']), 0, 2);
            $in_sites = new Sites(1, WhereWordsSites($_GET['search_string']), 0);
            $in_catalog = new Organizations(1, WhereWordsCatalog($_GET['search_string']), 0, 0);
        }
        ?>
        <!--Отловить размер окна меню-->
        <script type="text/javascript">
            function ResizeMenu()
            {
                //$(".text_inp_ser").val($('#show_menu').outerWidth());
                if ($('#show_menu').outerWidth() > 1250) {
                    $('#show_menu_1').show(100);
                    $('#show_menu_2').hide(100);
                    $('.block_vip_content').css('min-width','130px');
                    $('.img_vip_gl').css('width','120px');
                    $('.img_vip_gl').css('height','120px');
                    $('.shapka_bloka_spec').css('width','100%');

                } else  {
                    $('#show_menu_1').hide(100);
                    $('#show_menu_2').show(100);
                    $('.block_vip_content').css('min-width','120px');
                    $('.img_vip_gl').css('width','110px');
                    $('.img_vip_gl').css('height','110px');
                    $('.shapka_bloka_spec').css('width','870px');
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

            <div class="all_photo_block">

                <div class="left_photo_block">
                    <?php
                    $statistics = new StatisticsTable();
                    $statistics->LoadStat();
                    ?>
                    <!--
                    <div class="kriteri_photo">
                        <div class="shapka_bloka">
                            <p class="style_shapka_1">Статистика</p>
                        </div>
                        <div class="obverden_bl">
                        </div>
                    </div>
                    -->
                    <div class="kriteri_photo">
                        <div class="shapka_bloka">
                            <p class="style_shapka_1">Фото объявления</p>
                            <p class="style_shapka_3" title="Всего объявлений"><?php echo $statistics->GetAll(); ?></p>
                        </div>
                        <div class="obveden_block">

                            <span><a class="style_menu_left_1" href="./">Все рубрики</a><span class="style_menu_left_3"><?php echo $statistics->GetAll(); ?></span></span><br>
                            <div class="inline_menu">
                            <?php
                            $slv = new StatisticsLeftBlock();
                            for ($i = 0; $i < count($slv->id); $i++) {
                                echo '<span><a class="style_menu_left_1" href="./?PageType=1&Category=' . $slv->id[$i] . '">' . $slv->name[$i] . '</a><span class="style_menu_left_3">' . $slv->count[$i] . '</span></span><br>';
                            }
                            ?>
                            </div>
                            <span><a class="style_menu_left_1" href="./?PageType=1&Today=1">Добавлено сегодня</a><span class="style_menu_left_3"><?php echo $statistics->GetToday(); ?></span></span><br>
                            <span><a class="style_menu_left_1" href="./?PageType=1&Yesterday=1">Добавлено вчера</a><span class="style_menu_left_3"><?php echo $statistics->GetYesterday(); ?></span></span><br>
                            <!--
                            <form action="./" method="GET">
                                <div class="search_photo">
                                    <input class="s_inp_1" type="text" name="Word" value="">
                                    <input type="submit" name="Search" class="search_photo_1" value="Поиск"><br>
                                </div>
                            </form>
                            -->
                        </div>
                    </div>
                </div>

                <div class="center_photo_block">
                    <?php
                    if (!isset($_GET['search_string'])) {
                        if ($ShowParamID == 1) {
                            if (!isset($limit)) {
                                $limit = 14;
                            }
                            $where_arr = array($cat_arr, $vars_cat, $cond_arr, $values_arr);
                            $photo_ads = new PhotoAdsTable();
                            $photo_ads->LoadAds($limit, $page, $where_arr, $where);
                            if ($Category == 0 && !isset($_GET['UserId'])) {
                                $cat = 'Все рубрики';
                            } else {
                                try {
                                    $queue = $mysql->prepare('SELECT k_pdc_name FROM k_photodesk_categories WHERE k_pdc_id=:id');
                                    $queue->execute(array(':id' => $Category));
                                    $result = $queue->fetch(PDO::FETCH_ASSOC);
                                    $cat = $result['k_pdc_name'];
                                } catch (PDOException $e) {
                                    exit();
                                }
                            }
                            ?>

                            <div id="rubrika_1" class="block_content_1">
                                <!--Все рубрики-->
                                <?php
                                if (!isset($_GET['UserId']) && !isset($_GET['Word'])) {
                                    $cond_arr[count($cond_arr) - 1] = ' AND ';
                                    array_push($cat_arr, 'k_pd_paid');
                                    array_push($vars_cat, ':paid');
                                    array_push($cond_arr, '');
                                    array_push($values_arr, '1');
                                    $where_paid = array($cat_arr, $vars_cat, $cond_arr, $values_arr);
                                    $photo_paid = new PhotoAdsTable();
                                    $photo_paid->LoadAds($VIPlimit, 0, $where_paid, '');
                                    ?>
                                    <div class="shapka_bloka_spec"> <!--VIP блок-->
                                        <p class="vip_text">Платная лента<!--<span class="style_spec"><?php echo $photo_paid->all_ads; ?></span>--></p>
                                        <div>
                                            <?php
                                            //var_dump($limit);
                                            $print = '';
                                            for ($i = 1; $i <= count($photo_paid->getID(0)); $i++) {
                                                /*
                                                ?>
                                                <div class="vip_img">
                                                    <p class="vip_text_1"> 
                                                        <a class="l_txt" title="За сегодня просмотров">+<?php echo $photo_paid->getViewsDays($i); ?></a>
                                                        <a class="r_txt" title="Всего просмотров"><?php echo $photo_paid->getViews($i); ?></a>
                                                    </p>
                                                    <div class="over_img_photodoska">
                                                        <?php
                                                        echo '<a href="./?PageType=2&PhotoNum=' . $photo_paid->getID($i) . '">';
                                                        if ($photo_paid->getPhoto($i) && file_exists('../admin/' . str_replace('photo/', 'photo/1_', $photo_paid->getPhoto($i)))) {
                                                            echo '<img class="vip_img_1" src="../admin/' . str_replace('photo/', 'photo/1_', $photo_paid->getPhoto($i)) . '" alt="">';
                                                        } else {
                                                            echo '<img class="vip_img_1" src="../images/noimage.png" alt="">';
                                                        }
                                                        echo '</a>';
                                                        ?>
                                                    </div>
                                                    <p class="vip_text_2" title="Товар"><?php echo $photo_paid->getTheme($i); ?></p>
                                                    <p class="vip_text_2" title="Цена в руб"><?php echo $photo_paid->getPrice($i); ?> руб.</p>
                                                </div>
                                                <?php
                                                */
                                                echo '<div class="block_vip_content">
                                                <div class="block_vip_content_wrap">
                                                    <a href="/photoboard/?PageType=2&PhotoNum=' . $photo_paid->getID($i) . '">';
                                                //echo $photo_paid->getPhoto($i);
                                                if ($photo_paid->getPhoto($i) && file_exists($_SERVER['DOCUMENT_ROOT'].'' . str_replace('photo/', 'photo/', $photo_paid->getPhoto($i)))) {

                                                    echo '<img class="img_vip_gl" src="' . str_replace('photo/', 'photo/', $photo_paid->getPhoto($i)) . '" alt="">';
                                                } else {
                                                    echo '<img class="img_vip_gl" src="/admin/images/noimage.png" alt="">';
                                                }
                                                echo '</a>
                                                </div>
                                                <div class="block_vip_content_wrap block_vip_content_padd">
                                                    <p class="text_photo_gl text_photo_gl_p">' . $photo_paid->getTheme($i) . '</p>
                                                    <hr size=3 color="#444">
                                                    <p class="text_photo_cena_gl text_photo_cena_gl_p">' . $photo_paid->getPrice($i) . ' руб.</p>
                                                    </div>
                                                </div>';
                                                //echo $print;
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                                <div class="shapka_bloka_spec">
                                    <?php
                                    if (!isset($_GET['UserId'])) {
                                        ?>
                                        <p class="free_text">Бесплатная лента<!--<span class="style_spec"><?php echo $photo_ads->all_ads; ?></span>-->

                                            <?php
                                            $show = '<span id="visible_param_1" class="visible_content">Показать по
                                            <a href="./?LimitOnPage=14' . $link . '">14</a>
                                            <a href="./?LimitOnPage=28' . $link . '">28</a>
                                            <a href="./?LimitOnPage=56' . $link . '">56</a></span>';
                                            echo str_replace('href="./?LimitOnPage=' . $limit, ' style="color: #f0938a;" href="./?LimitOnPage=' . $limit, $show);
                                            ?> 
                                        </p>
                                        <?php
                                    } else {
                                        ?>
                                        <div style="padding-bottom: 15px;">
                                            <p class="name_team_2">Другие объявления пользователя<b> <?php echo $photo_ads->getUserStr(1); ?></b></p>

                                        </div>
                                        <?php
                                    }
                                    if (!$where_nav) {
                                        $where_nav = $where;
                                    }
                                    ?>
                                    <?php
                                    //$photo_ads->GenerateNavigation($page, $where_nav, $link, $limit);
                                    ?>
                                    <div >
                                        <?php
                                        $print = '';
                                        for ($i = 1; $i <= count($photo_ads->getID(0)); $i++) {
                                            /*
                                            ?>
                                            <div <?php echo $photo_ads->color[$i] == 1 ? 'class="free_img_color"' : 'class="free_img"'; ?>>
                                                <p class="free_text_1">
                                                    <a class="l_txt" title="За сегодня просмотров">+<?php echo $photo_ads->getViewsDays($i); ?></a>
                                                    <a class="r_txt" title="Всего просмотров"><?php echo $photo_ads->getViews($i); ?></a>
                                                </p>
                                                <div class="over_img_photodoska_2">
                                                    <?php
                                                    echo '<a href="./?PageType=2&PhotoNum=' . $photo_ads->getID($i) . '">';
                                                    if ($photo_ads->getPhoto($i) && file_exists('../admin/' . str_replace('photo/', 'photo/1_', $photo_ads->getPhoto($i)))) {
                                                        echo '<img class="free_img_1" src="../admin/' . str_replace('photo/', 'photo/1_', $photo_ads->getPhoto($i)) . '" alt="">';
                                                    } else {
                                                        echo '<img class="free_img_1" src="../images/noimage.png" alt="">';
                                                    }
                                                    echo '</a>';
                                                    ?>
                                                </div>
                                                <p class="free_text_2" title="Товар"><?php echo $photo_ads->getTheme($i); ?></p>
                                                <p class="free_text_2" title="Цена в руб"><?php echo $photo_ads->getPrice($i); ?> руб.</p>
                                            </div>
                                            <?php
                                            */
                                            echo '<div class="block_vip_content">
                                                <div class="block_vip_content_wrap">
                                                    <a href="/photoboard/?PageType=2&PhotoNum=' . $photo_ads->getID($i) . '">';
                                                if ($photo_ads->getPhoto($i) && file_exists($_SERVER['DOCUMENT_ROOT'].'' . str_replace('photo/', 'photo/', $photo_ads->getPhoto($i)))) {
                                                    echo '<img class="img_vip_gl" src="' . str_replace('photo/', 'photo/', $photo_ads->getPhoto($i)) . '" alt="">';
                                                } else {
                                                    echo '<img class="img_vip_gl" src="/admin/images/noimage.png" alt="">';
                                                }
                                            echo '</a>
                                                </div>
                                                <div class="block_vip_content_wrap block_vip_content_padd">
                                                    <p class="text_photo_gl text_photo_gl_p">' . $photo_ads->getTheme($i) . '</p>
                                                    <hr size=3 color="#444">
                                                    <p class="text_photo_cena_gl text_photo_cena_gl_p">' . $photo_ads->getPrice($i) . ' руб.</p>
                                                    </div>
                                                </div>';
                                            //echo $print;
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php
                                $photo_ads->GenerateNavigation($page, $where, $link, $limit);
                                ?>
                            </div>

                            <?php
                        }
                        ?>

                        <?php
                        if ($ShowParamID == 2) {
                            $ad = new PhotoAdsTable();
                            $ad->LoadAds(0, 0, array(array('k_pd_id'), array(':id_ad'), array(''), array($Ad_num)), '');
                            $ad->ViewsAdd($_SESSION['id']);
                            $user = new UserPhoto();
                            $user->LoadUser($Ad_num);
                            $comments = new Comments();
                            $comments->LoadComments($Ad_num);
                            ?>
                            <div class="block_content_1 once">   <!--Описание объявления фотодоски-->
                                <div class="menu_photo_board">
                                    <a class="photo_menu" id="photo1" onclick="ShowFormsUserads(1);" title="Венуться к объявлению">Объявление № <?php echo $ad->getID(1); ?></a>
                                    <a class="photo_menu" style="background:#fff" id="photo2" onclick="ShowFormsUserads(2);" title="Отправить личное сообщение">Отправить сообщение</a>
                                </div>
                                <div id="user_form_1" style="display: block;">
                                    <div class="obvodka_photo">
                                        <table class="kartochka_photo">
                                            <tr>
                                                <td><p class="txt_table_left"><?php echo $ad->getRegDate(1); ?></p></td>
                                                <td><p class="name_team"><?php echo $ad->getTheme(1); ?></p></td>
                                            </tr>
                                            <tr>
                                                <td><p class="txt_table_left">Рубрика:</p></td>
                                                <td><p class="txt_table_right"><?php echo $ad->getCategoryStr(1); ?></p></td>
                                            </tr>
                                            <tr>
                                                <td><p class="txt_table_left">Последний визит:</p></td>
                                                <td><p class="txt_table_right"><?php echo $user->GetLastDay(); ?></p></td>
                                            </tr>
                                            <tr>
                                                <td><p class="txt_table_left_up" title="Другие объявления пользователя">Объявлений:</p></td>
                                                <td><p class="txt_table_right"><?php echo $user->GetCount(); ?></p></td>
                                            </tr>
                                            <tr>
                                                <td><p class="txt_table_left">Пользователь:</p></td>
                                                <td><p class="txt_table_right"><?php echo $user->GetLogin(); ?></p></td>
                                            </tr>
                                            <tr>
                                                <td><p class="txt_table_left">E-mail:</p></td>
                                                <td><p class="txt_table_right"><a class="name_team_4" href="mailto:<?=$user->GetEmail()?>"><?=$user->GetEmail()?></a></p></td>
                                            </tr>
                                        </table>
                                        <!--<p class="name_team"><?php echo $ad->getTheme(1); ?></p>
                                        <span class="name_team_1"><?php echo $ad->getRegDate(1); ?> Рубрика: <b><?php echo $ad->getCategoryStr(1); ?></b></span><br>
                                        <div class="user_team">
                                            <div class="user_opisanie">
                                                <span class="name_team_2">Последний визит: <span><?php echo $user->GetLastDay(); ?></span></span><br>
                                                <a class="name_team_4" title="Другие объявления пользователя" <?php echo 'href="./?PageType=1&UserId=' . $user->GetID() . '"'; ?>>Объявлений <span style="color: #adb3aa; padding-left: 5px;"><?php echo $user->GetCount(); ?></span></a><br>
                                                <span class="name_team_4" title="Пользователь" onclick="ShowFormspPhoto(8);"><span><?php echo $user->GetLogin(); ?></span></span><br>
                                                <span class="name_team_3" title="E-mail"><?php echo $user->GetEmail(); ?></span><br>
                                            </div>
                                        </div>-->
                                        <div class="block_text_team">
                                            <p class="gl_text_team">
                                                <?php
                                                echo $ad->getText(1);
                                                ?>
                                            </p>
                                        </div>
                                        <div class="block_cena">
                                            <p class="text_cena">Цена:
                                                <span class="otstup_text_phot"></span><b><?php echo $ad->getPrice(1); ?> руб.</b>
                                            </p>
                                        </div>
                                        <div class="block_telefon">
                                            <p class="text_telefon">Телефон:
                                                <b><?php echo $ad->getPhone(1); ?></b>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="block_photo">
                                        <p class="dop_phot">Дополнительные фото</p>
                                        <?php
                                        foreach ($user->GetPhotos(0) as $value) {
                                            //echo $value;
                                            echo '<img class="text_photo" onclick="ShowImage(this);" src="' . str_replace('photo/', 'photo/', $value) . '" alt="">';
                                        }
                                        ?>
                                    </div>
                                    <form action="./" method="post" enctype="multipart/form-data">
                                        <div class="block_koment">
                                            <input type="hidden" name="PhotoNum" <?php echo 'value="' . $Ad_num . '"'; ?> readonly>
                                            <p class="text_title_koment">Комментарий:<span>*</span></p>
                                            <div class="t_ar_phot">
                                                <textarea rows="8" cols="67" name="PhotoComment"><?php
                                if (isset($results)) {
                                    echo $_POST['PhotoComment'];
                                }
                                        ?></textarea>
                                            </div>
                                            <div class="kapcha">
                                                <p class="com_capcha"><i>Нажмите на яркий кружочек, чтобы отправить комментарий.</i></p>
                                                <?php
                                                if (isset($results)) {
                                                    echo $results;
                                                }
                                                ?>
                                                <input type='image' name='submit' src='inc/captcha.php' alt='Captcha Security'>
                                            </div>
                                        </div>
                                    </form>
                                    <?php
                                    for ($i = 1; $i <= count($comments->GetComments(0)); $i++) {
                                        ?>
                                        <div class="block_koment_1">
                                            <span class="name_team_1">
                                                <?php
                                                if ($comments->GetLogin($i)) {
                                                    echo $comments->GetLogin($i);
                                                } else {
                                                    echo 'Гость';
                                                }
                                                ?>
                                            </span>
                                            <span class="name_team_1"><?php echo $comments->GetDate($i); ?></span>
                                            <br>
                                            <p class="text_team"><?php echo $comments->GetComments($i); ?></p>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div id="user_form_2" class="bl_lichn_mess">
                                    <p class="text_title_koment">Личное сообщение:<span>*</span></p>
                                    <form action="./" method="post" enctype="multipart/form-data">
                                        <div class="t_ar_phot">
                                            <textarea rows="8" cols="67" name="PrivateMessage"><?php
                            if (isset($resultsP)) {
                                echo $_POST['PrivateMessage'];
                            }
                                    ?></textarea>
                                            <input type="hidden" name="PhotoNumPrivate" <?php echo 'value="' . $Ad_num . '"'; ?> readonly>
                                        </div>
                                        <div class="kapcha">
                                            <p class="com_capcha"><i>Нажмите на яркий кружочек, чтобы отправить личное сообщение.</i></p>
                                            <?php
                                            if (isset($resultsP)) {
                                                echo $resultsP;
                                            }
                                            ?>
                                            <input type="image" name="submit" src="inc/captcha.php" alt="Captcha Security">
                                        </div>
                                    </form>
                                </div>
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
                        if (count($in_catalog->id) != 0) {
                            ?>
                            <div class="push_all_search">
                                <p class="name_push">Найдено <b>в предприятиях</b><span><?php echo count($in_catalog->id); ?></span></p>
                                <?php
                                if (count($in_catalog->id) > 2) {
                                    $count = 2;
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
                                        <a class="open_map">Показать адреса на карте<span class="nambe_of"><?php echo count($org_addr->id); ?></span></a>
                                        <a class="element_style_4">Просмотров<span class="nambe_of"><?php echo $in_catalog->watches[$i]; ?></span></a><br><br>
                                        <table style="margin-left: 15px;">
                                            <tr>
                                                <td>
                                                    <?php
                                                    if ($in_catalog->site[$i] != 'http://') {
                                                        ?>
                                                        <a class="element_style_1">Сайт:</a>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($in_catalog->site[$i] != 'http://') {
                                                        echo '<a href="' . $in_catalog->site[$i] . '" class="element_style_3">' . $in_catalog->site[$i] . '</a>';
                                                    }
                                                    ?>
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
                                                            echo '<a href="../map/?f=' . $org_addr->address_str[$n] . '" class="open_map">';
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
                                    echo '<p class="push_all"><a href="../map/?f=' . $on_map->address_str[$i] . '">' . $on_map->address_str[$i] . '</a><img class="push_img" src="../images/photo_1.png" alt=""></p>';
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
                <?php /*
                <div class="right_photo_block">
                    <?php
                    $where_vip = array(array('k_pd_vip'), array(':vip'), array(''), array('1'));
                    $photo_vip = new PhotoAdsTable();
                    $photo_vip->LoadAds(10, 1, $where_vip, '');
                    ?>
                    <div class="kriteri_photo_1">
                        <div class="shapka_bloka">
                            <p class="style_shapka_1">VIP - лента</p>
                            <p class="style_shapka_3" title="Всего объявлений"><?php echo $photo_vip->all_ads; ?></p>
                        </div>
                        <div class="obveden_block">
                            <?php
                            for ($i = 1; $i <= count($photo_vip->getID(0)); $i++) {
                                ?>

                                <div class="vip_div">
                                    <div class="over_img_photodoska_3">
                                        <?php
                                        echo '<a href="./?PageType=2&PhotoNum=' . $photo_vip->getID($i) . '">';
                                        if ($photo_vip->getPhoto($i) && file_exists('../admin/' . str_replace('photo/', 'photo/1_', $photo_vip->getPhoto($i)))) {
                                            echo '<img class="vip_div_img" src="../admin/' . str_replace('photo/', 'photo/1_', $photo_vip->getPhoto($i)) . '" alt="">';
                                        } else {
                                            echo '<img class="vip_div_img" src="../images/noimage.png" alt="">';
                                        }
                                        echo '</a>';
                                        ?>
                                    </div>
                                    <p class="free_text_2" title="Товар"><?php echo $photo_vip->getTheme($i); ?></p>
                                    <div class="vip_div_money">
                                        <p class="vip_div_money_text"><?php echo $photo_vip->getPrice($i); ?> руб.</p>
                                    </div>
                                </div>

                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                */ ?>
            </div>

            <div id="wind_poto" class="wind_nedvigimost">
                <a class="close_2" onclick="CloseWindow('wind_poto');">X</a>
                <div class="bl_phot_list">
                    <img src="" alt="">
                </div>
            </div>

    <?php
    require_once '../inc/footer.php';
    ?>
            <!--ВСПЛЫВАЮЩИЕ ОКНА-->

            <div class="temno" id="temno"></div>   <!--Всплывающие окна конец-->
            <div class="prozrachno" id="prozrachno" onclick="$('.vhod_block_gl').hide(500);
                disableP();"></div>   <!--Всплывающие окна прозрачное для закрытия входа-->
        </div>
    </body>
</html>