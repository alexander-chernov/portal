<?php
define('TOMSKLINE', 1);
session_start();
        require_once '../inc/configs.php';
        require_once '../inc/functions.php';
        require_once '../admin/inc/functions.php';
        require_once '../admin/admin_sites/inc/classes.php';
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
            }
        } catch (PDOException $e) {
            exit();
        }

        //Меняем категории
        if (isset($_GET['PageType'])) {
            $ShowParamID = filter_var($_GET['PageType'], FILTER_VALIDATE_INT);
        } else {
            $ShowParamID = 1;
        }
        //Листаем страницы
        if (isset($_GET['PageIndex'])) {
            $page = filter_var($_GET['PageIndex'], FILTER_VALIDATE_INT);
        } else {
            $page = 1;
        }
        //Ограничение по количеству
        if (isset($_GET['Limit'])) {
            $limit = filter_var($_GET['Limit'], FILTER_VALIDATE_INT);
        } else {
            $limit = 10;
        }

        if (isset($_POST['submit_x']) && isset($_POST['submit_y'])) {
            $ShowParamID = 2;
            if (!empty($_POST['new_site_name']) && !empty($_POST['new_site_url'])) {
                $data = base64_decode($_SESSION['captcha_image_code']);
                $captcha_image = imagecreatefromstring($data);
                $x = $_POST['submit_x'];
                $y = $_POST['submit_y'];

                $rgb = imagecolorat($captcha_image, $x, $y);
                $color_tran = imagecolorsforindex($captcha_image, $rgb);
//229, 48, 57
                $captcha_ok = ($color_tran['red'] == 255 && $color_tran['green'] == 0 && $color_tran['blue'] == 0 && $color_tran['alpha'] == 0);

                if ($captcha_ok) {
                    $_POST['new_site_name'] = filter_var($_POST['new_site_name'], FILTER_SANITIZE_STRIPPED);
                    $_POST['new_site_url'] = CorrectURL($_POST['new_site_url']);
                    $_POST['new_site_description'] = filter_var($_POST['new_site_description'], FILTER_SANITIZE_STRIPPED);
                    $_POST['new_site_cname'] = filter_var($_POST['new_site_cname'], FILTER_SANITIZE_STRIPPED);
                    $_POST['new_site_cphone'] = filter_var($_POST['new_site_cphone'], FILTER_SANITIZE_STRIPPED);
                    $_POST['new_site_email'] = filter_var($_POST['new_site_email'], FILTER_SANITIZE_EMAIL);
                    $image = str_replace('../admin/', '', filter_var($_POST['new_site_image'], FILTER_SANITIZE_STRIPPED));
                    try {
                        $queue = $mysql->prepare('INSERT INTO k_sites
                            (k_s_name,k_s_url,k_s_description,k_s_contact_name,k_s_contact_phone,k_s_email,k_s_date,k_s_image)
                            VALUES (:name,:url,:descr,:cname,:cphone,:email,NOW(),:image)');
                        $queue->execute(array(":name" => $_POST['new_site_name'],
                            ":url" => $_POST['new_site_url'],
                            ":descr" => $_POST['new_site_description'],
                            ":cname" => $_POST['new_site_cname'],
                            ":cphone" => $_POST['new_site_cphone'],
                            ":email" => $_POST['new_site_email'],
                            ":image" => $image));
                        $id = $mysql->lastInsertId();
                        $queue2 = $mysql->prepare('INSERT INTO k_sites_links (k_sl_site_id,k_sl_sub_id) VALUES (:sid,:cat)');
                        foreach ($_POST['new_site_cats'] as $value) {
                            $queue2->execute(array(":sid" => $id, ":cat" => $value));
                        }
                        $ShowParamID = 1;
                    } catch (PDOException $e) {
                        exit('');
                    }
                } else {
                    $results = '<span style="color: red;">Убедитесь, что вы нажали в розовый кружочек!</span>';
                }
            } else {
                $results = '<span style="color: red;">Пожалуйста, заполните все обязательные поля!</span>';
            }
        } else {
            $results = 'Чтобы убедиться, что вы не робот, для отправки нажмите красный кружок на картинке';
        }

        $banners = new BannersAll(0);
        ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
    <head>
        <title>TOMSK-LINE.RU. Сайты</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="keywords" content="">
        <meta name="description" content="">
        <link rel="stylesheet" type="text/css" href="../css/style.css">
        <link rel="stylesheet" type="text/css" href="css/sites.css">
        <link rel="stylesheet" type="text/css" href="../css/style_wind.css">
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script type="text/javascript" src="../js/wind.js"></script>
        <!--Отловить размер окна меню-->
        <script type="text/javascript">
            function ResizeMenu()
            {
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
            $(window).resize(function(){ResizeMenu();});
            $(window).ready(function(){ResizeMenu();});
        </script>
        <!--Отловить размер окна меню-->
        <?php
        //ColorsOnPage();
        ?>
    </head>
    <body>
    <?php
    require_once '../inc/header.php';
    ?>

            <script type="text/javascript">
                        function open_obj(obj) {
                            $(obj).next('div').toggle(500);
                        }
            </script>
            <div class="centr_block_article">
                <div class="left_block_artikle">
                    <div class="content_left">
                        <div class="shapka_bloka">
                            <a class="name_shapka">Сайты</a>
                        </div>
                        <div class="block_menu_sites">
                            <div style="width: auto; padding-top:15px; padding-bottom: 15px;">
                                <a class="add_site" href="./?PageType=2">Добавить сайт</a>
                            </div>
                            <?php
                            $s_cat = new SitesCategories();
                            for ($i = 0; $i < count($s_cat->id); $i++) {
                                echo '<p class="team_sites" onclick="open_obj(this);"><a>' . $s_cat->name[$i] . '</a>
                                    <span class="style_menu_left_3">' . $s_cat->count[$i] . '</span></p>';
                                $ss_menu = new SitesSubcategories($s_cat->id[$i], '');
                                if (count($ss_menu->id) > 0) {
                                    echo '<div class="menu_team">';
                                    for ($n = 0; $n < count($ss_menu->id); $n++) {
                                        echo '<p><a href="./?Category=' . $ss_menu->id[$n] . '">' . $ss_menu->name[$n] . '</a>
                                            <span class="style_menu_left_3">' . $ss_menu->count[$n] . '</span></p>';
                                    }
                                    echo '</div>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="right_block_artikle">
                    <?php
                    if ($ShowParamID == 1) {
                        $where = ' WHERE k_s_state=1 ';
                        if (isset($_GET['Category'])) {
                            $_GET['Category'] = filter_var($_GET['Category'], FILTER_VALIDATE_INT);
                            $link_ar['Category'] = filter_var($_GET['Category'], FILTER_VALIDATE_INT);
                            $where .= ' AND k_sl_sub_id=' . $_GET['Category'] . ' ';
                            $s_c = new SitesSubcategories(0, ' AND k_ss_id=' . $_GET['Category'] . ' ');
                            $header = $s_c->name_parent[0] . '&nbsp<b>' . $s_c->name[0] . '</b>';
                        } else {
                            $header = 'Все сайты';
                        }
                        if (!empty($_GET['Limit'])) {
                            $link_ar['Limit'] = filter_var($_GET['Limit'], FILTER_VALIDATE_INT);
                        } else {
                            $link_ar['Limit'] = 10;
                        }
                        $link_ar_url = array();
                        $link_ar_limit = array();
                        foreach ($link_ar as $key => $value) {
                            $link_ar_url[] = urlencode($key) . '=' . urlencode($value);
                            if ($key != 'Limit') {
                                $link_ar_limit[] = urlencode($key) . '=' . urlencode($value);
                            }
                        }
                        $link = '&' . join('&', $link_ar_url);
                        $link_l = '&' . join('&', $link_ar_limit);
                        $sites = new Sites($page, $where, $limit);
                        ?>
                        <p class="name_rubrik_artikle"><?php echo $header; ?>
                            <span id="visible_param_1" class="visible_content">Показывать по

                                <a <?php if ($link_ar['Limit'] == 10) echo 'style="color: #198f18;"'; ?> href="./?Limit=10<?php echo $link_l; ?>">10</a>
                                <a <?php if ($link_ar['Limit'] == 30) echo 'style="color: #198f18;"'; ?> href="./?Limit=30<?php echo $link_l; ?>">30</a>
                                <a <?php if ($link_ar['Limit'] == 50) echo 'style="color: #198f18;"'; ?> href="./?Limit=50<?php echo $link_l; ?>">50</a>
                            </span>
                        </p>
                        <?php
                        $sites->GenerateNavigation($page, $where, $link, $limit);
                        ?>
                        <?php
                        for ($i = 0; $i < count($sites->id); $i++) {
                            $sc_for = new SitesSubcategories(0, ' AND k_sl_site_id=' . $sites->id[$i] . ' ');
                            if ($i % 2 == 0) {
                                $class = 'artikle_content_1';
                            } else {
                                $class = 'artikle_content_2';
                            }
                            ?>
                            <div class="<?php echo $class; ?>">
                                <div class="block_artikle_img">
                                    <a target="_blank" href="<?php echo $sites->url[$i]; ?>">
                                        <?php
                                        if ($sites->avatar[$i] && file_exists('../admin/' . $sites->avatar[$i])) {
                                            echo '<img class="img_artikle_content" src="../admin/' . $sites->avatar[$i] . '" alt="">';
                                        } else {
                                            echo '<img class="img_artikle_content" src="../images/noimage.png" alt="">';
                                        }
                                        ?>
                                    </a>
                                </div>
                                <div class="block_artikle_text">
                                    <div class="all_artikle_text">
                                        <p class="dannie_artikle">
                                            <span><?php echo $sites->date[$i]; ?></span>
                                            <?php
                                            for ($n = 0; $n < count($sc_for->id); $n++) {
                                                echo '<span class="sp_otst" title="' . $sc_for->name_parent[$n] . '">' . $sc_for->name[$n] . '</span>';
                                            }
                                            ?>
                                        </p>
                                        <p class="name_artikle">
                                            <a target="_blank" href="<?php echo $sites->url[$i]; ?>"><?php echo $sites->name[$i]; ?></a>
                                            <!--<span class="nabe_artikle">№ <?php echo $sites->id[$i]; ?></span>-->
                                        </p>
                                        <p class="text_artikle"><?php echo $sites->description[$i]; ?></p>
                                        <a class="name_sites" target="_blank" href="<?php echo $sites->url[$i]; ?>"><?php echo $sites->url[$i]; ?></a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <?php
                        $sites->GenerateNavigation($page, $where, $link, $limit);
                        ?>
                        <?php
                    }
                    ?>
                    <?php
                    if ($ShowParamID == 2) {
                        ?>
                        <script type="text/javascript" src="js/ajaxupload.3.5.js"></script>
                        <script type="text/javascript">
                            $(function() {
                                var btnUpload = $('#site_image');
                                var status = $('#statusS');
                                new AjaxUpload(btnUpload, {
                                    action: 'upload-file.php',
                                    name: 'site_image',
                                    onSubmit: function(file, ext) {
                                        status.html('<img src="../admin/images/ajax-loader.gif" alt="" width="30">');
                                    },
                                    onComplete: function(file, response) {
                                        status.text('');
                                        if (response === "error") {
                                            status.text('\u0412озникла ошибка!');
                                        } else {
                                            $('#your_image').attr('src', response);
                                            $('#new_site_image').val(response);
                                        }
                                    }
                                });
                            });
                        </script>
                        <div class="artikle_content_3">
                            <form action="./" method="post" enctype="multipart/form-data">
                                <div class="redaktor_artikle">
                                    <p class="name_rubrik_artikle">Добавить&nbsp<b>сайт</b></p>
                                    <p class="text_com">Поля отмеченные <span style="color: red;">*</span> объязательны для заполнения.</p>
                                    <p class="text_redakt_sites">Для размещения сайта в каталог <span style="font-weight: bold; font-family: Tahoma;"><?=strtoupper(_SERVER_ADDRESS)?></span> необходимо заполнить анкету.</p>
                                    <div style="padding-top: 20px; width: auto;">
                                        <p class="text_redakt_sites">Название сайта&nbsp<span style="color: red;">*</span><span class="primer">например: Агенство недвижимости ООО"Загородом"</span></p>
                                        <input class="inp_sites" name="new_site_name" type="text" value="<?php echo $_POST['new_site_name']; ?>">
                                        <p class="text_redakt_sites">Адрес сайта&nbsp<span style="color: red;">*</span><span class="primer">например: www.zagorodom.ru</span></p>
                                        <input class="inp_sites" name="new_site_url" type="text" value="<?php echo $_POST['new_site_url']; ?>">
                                        <p class="text_redakt_sites">Описание<span class="primer">краткое описание о сайте.</span></p>
                                        <textarea class="inp_sites" name="new_site_description" rows="9"><?php echo $_POST['new_site_description']; ?></textarea>
                                        <div>
                                            <?php
                                            if (isset($_POST['new_site_image'])) {
                                                echo '<img id="your_image" src="' . $_POST['new_site_image'] . '" alt="">';
                                            } else {
                                                echo '<img class="img_artikle_content" id="your_image" src="../admin/images/noimage.png" alt="">';
                                            }
                                            ?>
                                            <br>
                                            <button class="add_site" id="site_image">Загрузить</button><br>
                                            <span id="statusS"></span>
                                            <input type="hidden" id="new_site_image" name="new_site_image" <?php echo 'value="' . $_POST['new_site_image'] . '"'; ?>>
                                        </div>
                                        <p class="text_redakt_sites">Рубрика<span class="primer">отметьте галочкой нужную рубрику.</span></p>
                                        <div class="check_block">
                                            <?php
                                            for ($i = 0; $i < count($s_cat->id); $i++) {
                                                echo '<div class="check_elem">';
                                                echo '<p class="name_r">' . $s_cat->name[$i] . '</p>';
                                                $ss_menu = new SitesSubcategories($s_cat->id[$i], '');
                                                if (count($ss_menu->id) > 0) {
                                                    for ($n = 0; $n < count($ss_menu->id); $n++) {
                                                        echo '<p class="name_r"><label>
                                                        <input type="checkbox" name="new_site_cats[]" ';
                                                        if (in_array($ss_menu->id[$n], $_POST['new_site_cats'])) {
                                                            echo 'checked="checked"';
                                                        }
                                                        echo ' value="' . $ss_menu->id[$n] . '">
                                                        ' . $ss_menu->name[$n] . '</label></p>';
                                                    }
                                                    echo '</div>';
                                                }
                                            }
                                            ?>
                                        </div>
                                        <p class="text_redakt_sites">Контактное лицо<span class="primer">ФИО</span></p>
                                        <input class="inp_sites" name="new_site_cname" type="text" value="<?php echo $_POST['new_site_cname']; ?>">
                                        <p class="text_redakt_sites">Телефон<span class="primer">для связи</span></p>
                                        <input class="inp_sites" name="new_site_cphone" type="text" value="<?php echo $_POST['new_site_cphone']; ?>">
                                        <p class="text_redakt_sites">E-mail<span class="primer">для связи</span></p>
                                        <input class="inp_sites" name="new_site_email" type="text" value="<?php echo $_POST['new_site_email']; ?>">
                                    </div>
                                    <i style="font-size: 80%;">
                                        <?php
                                        echo $results;
                                        ?>
                                    </i>
                                    <br>
                                    <input type='image' name='submit' src='inc/captcha.php' alt='Captcha Security'>
                                </div>
                            </form>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>

    <?php
    require_once '../inc/footer.php';
    ?>

            <!--ВСПЛЫВАЮЩИЕ ОКНА-->       

            <div class="temno" id="temno"></div>   <!--Всплывающие окна конец-->
            <div class="prozrachno" id="prozrachno" onclick="$('.vhod_block_gl').hide(500);
                            disableP();"></div>   <!--Всплывающие окна прозрачное для закрытия входа-->

            <script type="text/javascript">
                        function ShowPhoto() {
                            $('.photo_map_vsplivaet').show(500);
                            $(document).mousemove(function(e) {
                                var x = e.pageX;
                                var y = e.pageY;
                                $('.photo_map_vsplivaet').css('left', x);
                                $('.photo_map_vsplivaet').css('top', y);
                            });
                        }
                        function HidePhoto() {
                            $('.photo_map_vsplivaet').hide(500);
                        }
            </script>
            <div class="photo_map_vsplivaet">
                <img src="images/kottedg.jpg" alt="">
            </div>
        </div>
    </body>
</html>
