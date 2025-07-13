<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
define('TOMSKLINE', 1);
session_start();
?>
<!DOCTYPE HTML>
<html lang="ru">
    <head>
        <title>Система управления</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <?php
        if (!isset($_SESSION['login'])) {
            if (isset($_COOKIE['login'])) {
                $_SESSION['login'] = $_COOKIE['login'];
                $_SESSION['password'] = $_COOKIE['password'];
            } else {
                exit('Вы не авторизованы для данной страницы');
            }
        }
        $page = 1;
        require_once '../inc/configs.php';
        require_once 'inc/classes.php';
        require_once 'inc/admin_functions.php';
        require_once '../../inc/functions.php';
        if (YourIPBanned()) {
            header($_ENV['SERVER_PROTOCOL'] . " 401 IP BAN", true, 401);
            header("Location: http://" . _SERVER_ADDRESS . "/pageBan.php");
        }

        //Подключаемся к БД
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
        } catch (PDOException $e) {
            unset($e);
            exit();
        }

        //Определяем переменные сессии
        $query = $mysql->prepare('SELECT k_ku_id,k_u_privileges FROM k_users WHERE k_ku_login=:login AND k_ku_password=:password');
        $query->execute(array(":login" => $_SESSION['login'], ":password" => $_SESSION['password']));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if ($query->rowCount() == 0) {
            exit('Пользователь не найден! Повторите вход!');
        }
        $_SESSION['id'] = $result['k_ku_id'];
        $_SESSION['privileges'] = $result['k_u_privileges'];

        require_once '../inc/user_access.php';
        if ($_SESSION['privileges'] != 1) {
            if (!UserAccess(1)) {
                exit('У вас нет прав заходить в эту категорию!');
            }
        } elseif ($_SESSION['privileges'] == 1) {
            $_SESSION['map_access'] = 1;
        }
        UpdateActivityAdmin();
        require_once '../inc/functions.php';
        CreateTempTables();

        //Листаем страницы
        if (isset($_GET['PageIndex'])) {
            $page = filter_var($_GET['PageIndex'], FILTER_VALIDATE_INT);
        } else {
            $page = 1;
        }
        //Меняем категории
        if (isset($_GET['PageType'])) {
            $ShowParamID = filter_var($_GET['PageType'], FILTER_VALIDATE_INT);
        } else {
            $ShowParamID = 1;
        }

        if (!in_array($ShowParamID, array(1, 2, 3, 4, 6, 7, 8, 9, 10, 11, 12))) {
            header($_ENV['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
            header("Location: http://" . _SERVER_ADDRESS . "/page404.php");
        }

        //"Статистика"
        if ($ShowParamID == 1) {
            $statistics = new MainStatistics();
        }

        //"Администраторы"
        if ($ShowParamID == 3) {
            $admins = new TableBuild();
            $admins->AdminLoad();
            $admins->LoadTable();
        }

        //"Модераторы"
        if ($ShowParamID == 4) {
            $moderators = new TableModeratorBuild();
            $moderators->ModeratorLoad();
            $moderators->LoadTable();
            $moder_categories = $moderators->CategoriesLoad();
        }

        //"Пользователи"
        if ($ShowParamID == 6) {
            $users = new TableUsersBuild();
            $users->UsersLoad($page);
            $users->BanIPList();
            $users->LoadTable();
        }
        ?>
        <meta name="keywords" content="">
        <meta name="description" content="">
        <link rel="stylesheet" type="text/css" href="../css/style_admin.css">
        <link rel="stylesheet" type="text/css" href="../css/style_wind.css">
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <?php
        if ($ShowParamID == 2) {
            ?>
            <script type="text/javascript" src="js/ajaxupload.3.5.js"></script>
            <script type="text/javascript">
                $(function() {
                    var btnUpload = $('#BannerLoad');
                    var status = $('#status');
                    new AjaxUpload(btnUpload, {
                        action: 'upload-file.php',
                        name: 'BannerLoad',
                        onSubmit: function(file, ext) {
                            status.html('<img src="../images/animate.gif" alt="">');
                        },
                        onComplete: function(file, response) {
                            status.text('');
                            if (response === "error") {
                                status.text('\u0412озникла ошибка!');
                            } else {
                                $('#BannerChangeID').val(response);
                            }
                        }
                    });
                });
            </script>
            <?php
        }
        ?>
        <script type="text/javascript" src="js/scripts.js"></script>
        <script type="text/javascript" src="../js/wind.js"></script>
        <?php
        if ($ShowParamID == 10) {
            ?>
            <!-- TinyMCE -->
            <script type="text/javascript" src="../tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
            <!--<script type="text/javascript" src="../tinymce/tinymce.min.js"></script>-->
            <script type="text/javascript">
                tinyMCE.init({
                    // General options
                    mode: "textareas",
                    theme: "advanced",
                    skin: "default",
                    width: "100%",
                    height: "600px",
                    tools: "inserttable",
                    language: "ru",
                    plugins: "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups,autosave",
                    // Theme options
                    theme_advanced_buttons1: "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect",
                    theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
                    theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
                    theme_advanced_toolbar_location: "top",
                    theme_advanced_toolbar_align: "left",
                    theme_advanced_statusbar_location: "bottom",
                    theme_advanced_resizing: true,
                    theme_advanced_resizing_use_cookie: false,
                    // Example word content CSS (should be your site CSS) this one removes paragraph margins
                    content_css: "css/word.css"
                });
            </script>
            <!-- /TinyMCE -->
            <script type="text/javascript" src="js/ajaxupload.3.5.js"></script>
            <script type="text/javascript">
                $(function() {
                    var btnUpload = $('#TextRedaktor');
                    var status = $('#status_t');
                    new AjaxUpload(btnUpload, {
                        action: 'upload-file.php',
                        name: 'TextRedaktor',
                        onSubmit: function(file, ext) {
                            if (!(ext && /^(jpg|png|jpeg|gif)$/.test(ext))) {
                                status.text('Only JPG, PNG or GIF files are allowed');
                                return false;
                            }
                            status.html('<img src="../images/animate.gif" alt="">');
                        },
                        onComplete: function(file, response) {
                            status.text('');
                            if (response === "error") {
                                status.text('\u0412озникла ошибка!');
                            } else {
                                status.html('\u0414ля вставки изображения используйте ссылку: <b>' + response + '</b>');
                            }
                        }
                    });
                });
            </script>
            <?php
        }
        ?>
        <?php
        ColorsOnPage();
        ?>
    </head>
    <body>
        <div class="top_block">
            <a class="menu" href="../admin_gl/">Главная</a>
            <a class="menu" href="../admin_realty/">Недвижимость</a>
            <a class="menu" href="../admin_photo/">Фото Объявления</a>
            <!--<a class="menu" href="../admin_expert/">Эксперты</a>-->
            <a class="menu" href="../admin_blog/">Статьи</a>
            <!--<a class="menu" href="../admin_webcam/">Веб-камеры</a>-->
            <a class="menu" href="../admin_job/">Работа</a>
            <a class="menu" href="../admin_catalog/">Каталог</a>
            <!--<a class="menu" href="../admin_sites/">Сайты</a>-->
            <a class="menu" href="../admin_map/">Карта</a>
            <div class="authorization">
                <table>
                    <tr>
                        <td colspan="2"><a  class="autho_3" href="../exit.php">Выход</a></td>
                    </tr>
                    <tr>
                        <td><p class="autho_1">Права:</p></td>
                        <td><p class="autho_2">
                                <?php
                                if ($_SESSION['privileges'] == 2) {
                                    echo 'Модератор';
                                }
                                if ($_SESSION['privileges'] == 1) {
                                    echo 'Администратор';
                                }
                                ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td><p class="autho_1">Логин:</p></td>
                        <td><p class="autho_2"><?php echo $_SESSION['login']; ?></p></td>
                    </tr>
                </table>
            </div>
        </div>
        <p class="topic">Управление: <span style="color: #ff9c00;">Главной страницей</span></p>
        <div class="center_block">
            <div id="menu_2">
                <a href="./?PageType=1">Статистика</a>
                <a href="./?PageType=2">Банеры</a>
                <a href="./?PageType=3">Администраторы</a>
                <a href="./?PageType=4">Модераторы</a>
                <a href="./?PageType=6">Пользователи</a>
                <a href="./?PageType=7">Личный кабинет</a>
                <a href="./?PageType=8">Warnings</a>
                <a href="./?PageType=9">Баны по IP</a>
                <a href="./?PageType=10">Редактор страниц</a>
                <a href="./?PageType=11">Редактор цвета</a>
                <a href="./?PageType=12">Управление тарифами</a>
            </div>
            <?php
            if ($ShowParamID == 12) {
                require_once './inc/tariffs.php';
                $tariffs = new TarrifPackages();
                $tariff_4ad = new TarrifPricesForAd();
                $packets = new TarrifPackets();
                $videos = new TarrifVideo();
                $others = new TarrifOthers();
                ?>
                <!--БЛОК ТАРИФОВ-->
                <div class="block_content_1"><b><span style="color: blue;">Управление тарифами</span></b><br>
                    <table id="packets" class="tab_tarif">
                        <tr class="gl_tr_tarif">
                            <td>Пакет / Услуги</td>
                            <td>Собственник</td>
                            <td>Посредник</td>
                            <td>Агенства</td>
                            <td>Застройщик</td>
                        </tr>
                        <tr class="dop_tr_tarif">
                            <td class="max_td_1">Стоимость регистрации / подключения</td>
                            <td><span><input type="text" ind="1" attr="price" value="<?php echo $packets->price[0]; ?>"> руб.</span></td>
                            <td><span><input type="text" ind="2" attr="price" value="<?php echo $packets->price[1]; ?>"> руб.</span></td>
                            <td><span><input type="text" ind="3" attr="price" value="<?php echo $packets->price[2]; ?>"> руб.</span></td>
                            <td><span><input type="text" ind="4" attr="price" value="<?php echo $packets->price[3]; ?>"> руб.</span></td>
                        </tr>
                        <tr class="dop_tr_tarif" style="background: #eeeeee;">
                            <td class="max_td_1" style="color: gray;">Возможность регистрации без приобретения платных пакетов</td>
                            <td>
                                <select ind="5" attr="attr">
                                    <?php
                                    if ($packets->attr[4] == 1) {
                                        echo '<option selected="selected" value="1">Да</option><option value="0">Нет</option>';
                                    } else {
                                        echo '<option value="1">Да</option><option selected="selected" value="0">Нет</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <select ind="6" attr="attr">
                                    <?php
                                    if ($packets->attr[5] == 1) {
                                        echo '<option selected="selected" value="1">Да</option><option value="0">Нет</option>';
                                    } else {
                                        echo '<option value="1">Да</option><option selected="selected" value="0">Нет</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <select ind="7" attr="attr">
                                    <?php
                                    if ($packets->attr[6] == 1) {
                                        echo '<option selected="selected" value="1">Да</option><option value="0">Нет</option>';
                                    } else {
                                        echo '<option value="1">Да</option><option selected="selected" value="0">Нет</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <select ind="8" attr="attr">
                                    <?php
                                    if ($packets->attr[7] == 1) {
                                        echo '<option selected="selected" value="1">Да</option><option value="0">Нет</option>';
                                    } else {
                                        echo '<option value="1">Да</option><option selected="selected" value="0">Нет</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr class="dop_tr_tarif">
                            <td class="max_td_1">Цена за одно дополнительное объявление</td>
                            <td><span><input type="text" ind="9" attr="price" value="<?php echo $packets->price[8]; ?>"> руб.</span></td>
                            <td><span><input type="text" ind="10" attr="price" value="<?php echo $packets->price[9]; ?>"> руб.</span></td>
                            <td><span><input type="text" ind="11" attr="price" value="<?php echo $packets->price[10]; ?>"> руб.</span></td>
                            <td><span><input type="text" ind="12" attr="price" value="<?php echo $packets->price[11]; ?>"> руб.</span></td>
                        </tr>
                        <tr class="dop_tr_tarif">
                            <td class="max_td_1">Всего объявлений в месяц</td>
                            <td><span><input type="text" ind="13" attr="attr" value="<?php echo $packets->attr[12]; ?>"></span></td>
                            <td>
                                <div class="ful_bl">
                                    <input class="bl_inp" ind="14" attr="attr" type="text" value="<?php echo $packets->attr[13]; ?>">
                                    /<input class="bl_inp" ind="15" attr="attr" type="text" value="<?php echo $packets->attr[14]; ?>">
                                    /<input class="bl_inp" ind="16" attr="attr" type="text" value="<?php echo $packets->attr[15]; ?>">
                                    /<input class="bl_inp" ind="17" attr="attr" type="text" value="<?php echo $packets->attr[16]; ?>">
                                </div>
                            </td>
                            <td>
                                <div class="ful_bl">
                                    <input class="bl_inp" ind="18" attr="attr" type="text" value="<?php echo $packets->attr[17]; ?>">
                                    /<input class="bl_inp" ind="19" attr="attr" type="text" value="<?php echo $packets->attr[18]; ?>">
                                    /<input class="bl_inp" ind="20" attr="attr" type="text" value="<?php echo $packets->attr[19]; ?>">
                                    /<input class="bl_inp" ind="21" attr="attr" type="text" value="<?php echo $packets->attr[20]; ?>">
                                </div>
                            </td>
                            <td>
                                <div class="ful_bl">
                                    <input class="bl_inp" ind="22" attr="attr" type="text" value="<?php echo $packets->attr[21]; ?>">
                                    /<input class="bl_inp" ind="23" attr="attr" type="text" value="<?php echo $packets->attr[22]; ?>">
                                    /<input class="bl_inp" ind="24" attr="attr" type="text" value="<?php echo $packets->attr[23]; ?>">
                                    /<input class="bl_inp" ind="25" attr="attr" type="text" value="<?php echo $packets->attr[24]; ?>">
                                </div>
                            </td>
                        </tr>
                        <tr class="dop_tr_tarif">
                            <td class="max_td_1">Стоимость в месяц</td>
                            <td><span><input type="text" ind="13" attr="price" value="<?php echo $packets->price[12]; ?>"> руб.</span></td>
                            <td>
                                <div class="ful_bl">
                                    <input class="bl_inp" ind="14" attr="price" type="text" value="<?php echo $packets->price[13]; ?>">
                                    /<input class="bl_inp" ind="15" attr="price" type="text" value="<?php echo $packets->price[14]; ?>">
                                    /<input class="bl_inp" ind="16" attr="price" type="text" value="<?php echo $packets->price[15]; ?>">
                                    /<input class="bl_inp" ind="17" attr="price" type="text" value="<?php echo $packets->price[16]; ?>">
                                </div>
                            </td>
                            <td>
                                <div class="ful_bl">
                                    <input class="bl_inp" ind="18" attr="price" type="text" value="<?php echo $packets->price[17]; ?>">
                                    /<input class="bl_inp" ind="19" attr="price" type="text" value="<?php echo $packets->price[18]; ?>">
                                    /<input class="bl_inp" ind="20" attr="price" type="text" value="<?php echo $packets->price[19]; ?>">
                                    /<input class="bl_inp" ind="21" attr="price" type="text" value="<?php echo $packets->price[20]; ?>">
                                </div>
                            </td>
                            <td>
                                <div class="ful_bl">
                                    <input class="bl_inp" ind="22" attr="price" type="text" value="<?php echo $packets->price[21]; ?>">
                                    /<input class="bl_inp" ind="23" attr="price" type="text" value="<?php echo $packets->price[22]; ?>">
                                    /<input class="bl_inp" ind="24" attr="price" type="text" value="<?php echo $packets->price[23]; ?>">
                                    /<input class="bl_inp" ind="25" attr="price" type="text" value="<?php echo $packets->price[24]; ?>">
                                </div>
                            </td>
                        </tr>
                        <tr class="dop_tr_tarif">
                            <td class="max_td_1">Пакет турбо продаж (количество объявлений)</td>
                            <td></td>
                            <td>
                                <div class="ful_bl">
                                    <input class="bl_inp" ind="26" attr="attr" type="text" value="<?php echo $packets->attr[25]; ?>">
                                    /<input class="bl_inp" ind="27" attr="attr" type="text" value="<?php echo $packets->attr[26]; ?>">
                                    /<input class="bl_inp" ind="28" attr="attr" type="text" value="<?php echo $packets->attr[27]; ?>">
                                    /<input class="bl_inp" ind="29" attr="attr" type="text" value="<?php echo $packets->attr[28]; ?>">
                                </div>
                            </td>
                            <td>
                                <div class="ful_bl">
                                    <input class="bl_inp" ind="30" attr="attr" type="text" value="<?php echo $packets->attr[29]; ?>">
                                    /<input class="bl_inp" ind="31" attr="attr" type="text" value="<?php echo $packets->attr[30]; ?>">
                                    /<input class="bl_inp" ind="32" attr="attr" type="text" value="<?php echo $packets->attr[31]; ?>">
                                    /<input class="bl_inp" ind="33" attr="attr" type="text" value="<?php echo $packets->attr[32]; ?>">
                                </div>
                            </td>
                            <td>
                                <div class="ful_bl">
                                    <input class="bl_inp" ind="34" attr="attr" type="text" value="<?php echo $packets->attr[33]; ?>">
                                    /<input class="bl_inp" ind="35" attr="attr" type="text" value="<?php echo $packets->attr[34]; ?>">
                                    /<input class="bl_inp" ind="36" attr="attr" type="text" value="<?php echo $packets->attr[35]; ?>">
                                    /<input class="bl_inp" ind="37" attr="attr" type="text" value="<?php echo $packets->attr[36]; ?>">
                                </div>
                            </td>
                        </tr>
                        <tr class="dop_tr_tarif">
                            <td class="max_td_1">Пакет быстрая продажа (количество объявлений)</td>
                            <td></td>
                            <td>
                                <div class="ful_bl">
                                    <input class="bl_inp" ind="38" attr="attr" type="text" value="<?php echo $packets->attr[37]; ?>">
                                    /<input class="bl_inp" ind="39" attr="attr" type="text" value="<?php echo $packets->attr[38]; ?>">
                                    /<input class="bl_inp" ind="40" attr="attr" type="text" value="<?php echo $packets->attr[39]; ?>">
                                    /<input class="bl_inp" ind="41" attr="attr" type="text" value="<?php echo $packets->attr[40]; ?>">
                                </div>
                            </td>
                            <td>
                                <div class="ful_bl">
                                    <input class="bl_inp" ind="42" attr="attr" type="text" value="<?php echo $packets->attr[41]; ?>">
                                    /<input class="bl_inp" ind="43" attr="attr" type="text" value="<?php echo $packets->attr[42]; ?>">
                                    /<input class="bl_inp" ind="44" attr="attr" type="text" value="<?php echo $packets->attr[43]; ?>">
                                    /<input class="bl_inp" ind="45" attr="attr" type="text" value="<?php echo $packets->attr[44]; ?>">
                                </div>
                            </td>
                            <td>
                                <div class="ful_bl">
                                    <input class="bl_inp" ind="46" attr="attr" type="text" value="<?php echo $packets->attr[45]; ?>">
                                    /<input class="bl_inp" ind="47" attr="attr" type="text" value="<?php echo $packets->attr[46]; ?>">
                                    /<input class="bl_inp" ind="48" attr="attr" type="text" value="<?php echo $packets->attr[47]; ?>">
                                    /<input class="bl_inp" ind="49" attr="attr" type="text" value="<?php echo $packets->attr[48]; ?>">
                                </div>
                            </td>
                        </tr>
                        <tr class="dop_tr_tarif">
                            <td class="max_td_1">SMS уведомления включены / стоимость в месяц</td>
                            <td>
                                <select ind="50" attr="attr">
                                    <?php
                                    if ($packets->attr[49] == 1) {
                                        echo '<option selected="selected" value="1">Да</option><option value="0">Нет</option>';
                                    } else {
                                        echo '<option value="1">Да</option><option selected="selected" value="0">Нет</option>';
                                    }
                                    ?>
                                </select><br>
                                <span><input type="text" ind="50" attr="price" value="<?php echo $packets->price[49]; ?>"> руб.</span>
                            </td>
                            <td>
                                <select ind="51" attr="attr">
                                    <?php
                                    if ($packets->attr[50] == 1) {
                                        echo '<option selected="selected" value="1">Да</option><option value="0">Нет</option>';
                                    } else {
                                        echo '<option value="1">Да</option><option selected="selected" value="0">Нет</option>';
                                    }
                                    ?>
                                </select><br>
                                <span><input type="text" ind="51" attr="price" value="<?php echo $packets->price[50]; ?>"> руб.</span>
                            </td>
                            <td>
                                <select ind="52" attr="attr">
                                    <?php
                                    if ($packets->attr[51] == 1) {
                                        echo '<option selected="selected" value="1">Да</option><option value="0">Нет</option>';
                                    } else {
                                        echo '<option value="1">Да</option><option selected="selected" value="0">Нет</option>';
                                    }
                                    ?>
                                </select><br>
                                <span><input type="text" ind="52" attr="price" value="<?php echo $packets->price[51]; ?>"> руб.</span>
                            </td>
                            <td>
                                <select ind="53" attr="attr">
                                    <?php
                                    if ($packets->attr[52] == 1) {
                                        echo '<option selected="selected" value="1">Да</option><option value="0">Нет</option>';
                                    } else {
                                        echo '<option value="1">Да</option><option selected="selected" value="0">Нет</option>';
                                    }
                                    ?>
                                </select><br>
                                <span><input type="text" ind="53" attr="price" value="<?php echo $packets->price[52]; ?>"> руб.</span>
                            </td>
                        </tr>
                    </table>
                    <table class="tab_tarif">
                        <tr class="gl_tr_tarif">
                            <td style="font-size: 20px;" colspan="4">Стоимость пакетов для одного объявления</td>
                        </tr>
                        <?php
                        for ($i = 0; $i < count($tariffs->id); $i++) {
                            ?>
                            <tr class="dop_tr_tarif">
                                <td class="max_td_1"><?php echo $tariffs->name[$i]; ?></td>
                                <td>
                                    <span>
                                        <a class="txt_otst">Закрепление</a>&nbsp;<input name="lock_days" type="text" value="<?php echo $tariffs->lock_days[$i]; ?>"> дней
                                    </span><br>
                                    <span>
                                        <a class="txt_otst">Поднятий</a>&nbsp;<input name="up" type="text" value="<?php echo $tariffs->up[$i]; ?>"> раз
                                    </span><br>
                                    <span>
                                        <a class="txt_otst">Выделение цветом</a>
                                        <select name="color">
                                            <?php
                                            if ($tariffs->color[$i] == 1) {
                                                echo '<option selected="selected" value="1">Да</option><option value="0">Нет</option>';
                                            } else {
                                                echo '<option value="1">Да</option><option selected="selected" value="0">Нет</option>';
                                            }
                                            ?>
                                        </select>
                                    </span><br>
                                    <span>
                                        <a class="txt_otst">В VIP</a>
                                        <select name="vip">
                                            <?php
                                            if ($tariffs->vip[$i] == 1) {
                                                echo '<option selected="selected" value="1">Да</option><option value="0">Нет</option>';
                                            } else {
                                                echo '<option value="1">Да</option><option selected="selected" value="0">Нет</option>';
                                            }
                                            ?>
                                        </select>
                                    </span><br>
                                </td>
                                <td><span><input type="text" name="price" value="<?php echo $tariffs->price[$i]; ?>"> руб.</span></td>
                                <td>
                                    <a class="a_1" onclick="SaveTariffPacket(this);">
                                        <img src="../images/enable.png" alt="<?php echo $tariffs->id[$i]; ?>">
                                    </a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <table class="tab_tarif">
                        <tr class="gl_tr_tarif">
                            <td style="font-size: 20px;" colspan="4">Стоимость платных услуг для одного объявления</td>
                        </tr>
                        <?php
                        for ($i = 0; $i < count($tariff_4ad->id); $i++) {
                            ?>
                            <tr class="dop_tr_tarif">
                                <td class="max_td_1"><?php echo $tariff_4ad->name[$i]; ?></td>
                                <td>
                                    <input type="text" name="days" value="<?php echo $tariff_4ad->days[$i]; ?>"> дней</span>
                                </td>
                                <td><span><input type="text" name="price" value="<?php echo $tariff_4ad->price[$i]; ?>"> руб.</span></td>
                                <td>
                                    <a class="a_1" onclick="SaveTariffPriceForAd(this);">
                                        <img src="../images/enable.png" alt="<?php echo $tariff_4ad->id[$i]; ?>">
                                    </a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <table class="tab_tarif">
                        <tr class="gl_tr_tarif">
                            <td style="font-size: 20px;" colspan="4">Стоимость размещения видео</td>
                        </tr>
                        <?php
                        for ($i = 0; $i < count($videos->id); $i++) {
                            ?>
                            <tr class="dop_tr_tarif">
                                <td style="color: royalblue;"><?php echo $videos->name[$i]; ?></td>
                                <td><span><input type="text" name="price" value="<?php echo $videos->price[$i]; ?>"> руб.</span></td>
                                <td><span><input type="text" name="duration" value="<?php echo $videos->duration[$i]; ?>"> сек.</span></td>
                                <td>
                                    <a class="a_1" onclick="SaveTariffVideo(this);">
                                        <img src="../images/enable.png" alt="<?php echo $videos->id[$i]; ?>">
                                    </a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <table class="tab_tarif">
                        <tr class="gl_tr_tarif">
                            <td style="font-size: 20px;" colspan="4">Разное</td>
                        </tr>
                        <?php
                        for ($i = 0; $i < count($others->id); $i++) {
                            ?>
                            <tr class="dop_tr_tarif">
                                <td style="color: royalblue;"><?php echo $others->name[$i]; ?></td>
                                <td><span><input type="text" name="price" value="<?php echo $others->price[$i]; ?>"> руб.</span></td>
                                <td>
                                    <a class="a_1" onclick="SaveTariffOthers(this);">
                                        <img src="../images/enable.png" alt="<?php echo $others->id[$i]; ?>">
                                    </a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
                <?php
            }
            ?>
            <?php
            if ($ShowParamID == 10) {
                ?>
                <!--БЛОК ТЕКСТОВЫХ РЕДАКТОРОВ НАЧАЛО-->
                <div class="block_content_1"><b><span style="color: blue;">Текстовый редактор</span></b><br>
                    <p class="add_text">Выберите страницу</p>
                    <p class="text_radio"><input name="p[]" value="1" type="radio"><label onclick="showText(this);">Главная</label></p>
                    <p class="text_radio"><input name="p[]" value="2" type="radio"><label onclick="showText(this);">Эксперты</label></p>
                    <p class="text_radio"><input name="p[]" value="3" type="radio"><label onclick="showText(this);">Регистрации (левый блок)</label></p>
                    <p class="text_radio"><input name="p[]" value="4" type="radio"><label onclick="showText(this);">Регистрации (правый блок)</label></p>
                    <p class="text_radio"><input name="p[]" value="5" type="radio"><label onclick="showText(this);">Забыли пароль? (левый блок)</label></p>
                    <p class="text_radio"><input name="p[]" value="6" type="radio"><label onclick="showText(this);">Забыли пароль? (правый блок)</label></p>
                    <p class="text_radio"><input name="p[]" value="7" type="radio"><label onclick="showText(this);">Пользовательское соглашение</label></p>
                    <p class="text_radio"><input name="p[]" value="14" type="radio"><label onclick="showText(this);">Пользовательское соглашение (правый блок)</label></p>
                    <p class="text_radio"><input name="p[]" value="8" type="radio"><label onclick="showText(this);">Отдел рекламы</label></p>
                    <p class="text_radio"><input name="p[]" value="15" type="radio"><label onclick="showText(this);">Отдел рекламы (правый блок)</label></p>
                    <p class="text_radio"><input name="p[]" value="9" type="radio"><label onclick="showText(this);">Служба поддержки</label></p>
                    <p class="text_radio"><input name="p[]" value="16" type="radio"><label onclick="showText(this);">Служба поддержки (правый блок)</label></p>
                    <p class="text_radio"><input name="p[]" value="26" type="radio"><label onclick="showText(this);">Контактная информация</label></p>
                    <p class="text_radio"><input name="p[]" value="27" type="radio"><label onclick="showText(this);">Контактная информация (правый блок)</label></p>
                    <p class="text_radio"><input name="p[]" value="10" type="radio"><label onclick="showText(this);">Помощь</label></p>
                    <p class="text_radio"><input name="p[]" value="11" type="radio"><label onclick="showText(this);">Стать экспертом</label></p>
                    <p class="text_radio"><input name="p[]" value="12" type="radio"><label onclick="showText(this);">Статьи (написать статью)</label></p>
                    <p class="text_radio"><input name="p[]" value="17" type="radio"><label onclick="showText(this);">Статьи (левый блок)</label></p>
                    <p class="text_radio"><input name="p[]" value="13" type="radio"><label onclick="showText(this);">Добавить в каталог</label></p>
                    <p class="text_radio"><input name="p[]" value="18" type="radio"><label onclick="showText(this);">Кабинет пользователя (правый блок)</label></p>
                    <p class="text_radio"><input name="p[]" value="19" type="radio"><label onclick="showText(this);">Кабинет эксперта (правый блок)</label></p>
                    <p class="text_radio"><input name="p[]" value="20" type="radio"><label onclick="showText(this);">Кабинет агентства (правый блок)</label></p>
                    <p class="text_radio"><input name="p[]" value="21" type="radio"><label onclick="showText(this);">Страница оплаты</label></p>
                    <p class="text_radio"><input name="p[]" value="22" type="radio"><label onclick="showText(this);">Страница успешной оплаты</label></p>
                    <p class="text_radio"><input name="p[]" value="23" type="radio"><label onclick="showText(this);">Страница ошибки оплаты</label></p>
                    <p class="text_radio"><input name="p[]" value="24" type="radio"><label onclick="showText(this);">Страница ошибки системы оплаты</label></p>
                    <p class="text_radio"><input name="p[]" value="25" type="radio"><label onclick="showText(this);">Страница отмены платежа</label></p>
                    <div id="open_page" class="open_content" style="display: none;">
                        <p class="add_text_2">Главная</p>
                        <div class="text_red_pages">
                            <div class="bl_redaktirovat">
                                <textarea id="elm1" name="elm1" rows="15" cols="80" style="width: 100%"></textarea>
                                <input type="hidden" name="ContentPageID" value="">
                            </div>
                            <button onclick="SaveContentPage();">Сохранить</button>
                            <button id="TextRedaktor">Загрузить изображение</button>
                            <div id="status_t"></div>
                        </div>
                    </div>
                </div>
                <!--БЛОК ТЕКСТОВЫХ РЕДАКТОРОВ КОНЕЦ-->
                <?php
            }
            ?>
            <?php
            if ($ShowParamID == 11) {
                try {
                    $query = $mysql->prepare('SELECT * FROM k_colours ORDER BY k_c_id ASC');
                    $query->execute();
                    $result = $query->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    exit();
                }
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Редатировать цвет, градиент (блоков, текста)</span></b><br>
                    <p class="add_text">Выберите действие</p>
                    <p class="text_radio"><label><input name="colours" value="1" type="radio">Цвет поиска, шапки блоков, кнопок</label></p>
                    <p class="text_radio"><label><input name="colours" value="2" type="radio">Цвет текста шапки блокови кнопок</label></p>
                    <p class="text_radio"><label><input name="colours" value="3" type="radio">Цвет блока меню</label></p>
                    <p class="text_radio"><label><input name="colours" value="4" type="radio">Цвет текста меню</label></p>
                    <p class="text_radio"><label><input name="colours" value="5" type="radio">Цвет рамки блоков</label></p>
                    <p class="text_radio"><label><input name="colours" value="6" type="radio">Цвет блоков объявлений</label></p>
                    <p class="text_radio"><label><input name="colours" value="7" type="radio">Цвет заголовков страниц</label></p>
                    <p class="text_radio"><label><input name="colours" value="8" type="radio">Цвет выделенных блоков</label></p>
                    <div class="open_content" ind="1">
                        <p class="add_text_2">Цвет поиска, шапки блоков, кнопок</p>
                        <div class="text_red_pages">
                            <table class="color_tab">
                                <tr>
                                    <td>
                                        <div class="bl_color"></div>
                                    </td>
                                    <td>
                                        <p>Верхний цвет:</p>
                                        <p>Нижний цвет:</p>
                                    </td>
                                    <td>
                                        <p><input type="color" param="1" placeholder="#3cce0b" value="<?php echo $result[0]['k_c_value']; ?>"></p>
                                        <p><input type="color" param="2" placeholder="#028600" value="<?php echo $result[1]['k_c_value']; ?>"></p>
                                    </td>
                                </tr>
                            </table>
                            <button onclick="SaveGradient();">Сохранить</button>
                        </div>
                    </div>
                    <div class="open_content" ind="2">
                        <p class="add_text_2">Цвет текста шапки блоков, кнопок</p>
                        <div class="text_red_pages">
                            <table class="color_tab">
                                <tr>
                                    <td>
                                        <div class="bl_color_1"></div>
                                    </td>
                                    <td>
                                        <p>Цвет:</p>
                                    </td>
                                    <td>
                                        <p><input type="color" param="3" placeholder="#ffffff" value="<?php echo $result[2]['k_c_value']; ?>"></p>
                                    </td>
                                </tr>
                            </table>
                            <button onclick="SaveColors(this);">Сохранить</button>
                        </div>
                    </div>
                    <div class="open_content" ind="3">
                        <p class="add_text_2">Цвет блока меню</p>
                        <div class="text_red_pages">
                            <table class="color_tab">
                                <tr>
                                    <td>
                                        <div class="bl_color_2"></div>
                                    </td>
                                    <td>
                                        <p>Цвет:</p>
                                    </td>
                                    <td>
                                        <p><input type="color" param="4" placeholder="#e2eadf" value="<?php echo $result[3]['k_c_value']; ?>"></p>
                                    </td>
                                </tr>
                            </table>
                            <button onclick="SaveColors(this);">Сохранить</button>
                        </div>
                    </div>
                    <div class="open_content" ind="4">
                        <p class="add_text_2">Цвет текста меню</p>
                        <div class="text_red_pages">
                            <table class="color_tab">
                                <tr>
                                    <td>
                                        <div class="bl_color_3"></div>
                                    </td>
                                    <td>
                                        <p>Цвет:</p>
                                    </td>
                                    <td>
                                        <p><input type="color" param="5" placeholder="#000000" value="<?php echo $result[4]['k_c_value']; ?>"></p>
                                    </td>
                                </tr>
                            </table>
                            <button onclick="SaveColors(this);">Сохранить</button>
                        </div>
                    </div>
                    <div class="open_content" ind="5">
                        <p class="add_text_2">Цвет рамки блоков</p>
                        <div class="text_red_pages">
                            <table class="color_tab">
                                <tr>
                                    <td>
                                        <div class="bl_color_4"></div>
                                    </td>
                                    <td>
                                        <p>Цвет:</p>
                                    </td>
                                    <td>
                                        <p><input type="color" param="6" placeholder="#ced9c8" value="<?php echo $result[5]['k_c_value']; ?>"></p>
                                    </td>
                                </tr>
                            </table>
                            <button onclick="SaveColors(this);">Сохранить</button>
                        </div>
                    </div>
                    <div class="open_content" ind="6">
                        <p class="add_text_2">Цвет блоков объявлений</p>
                        <div class="text_red_pages">
                            <table class="color_tab">
                                <tr>
                                    <td>
                                        <div class="bl_color_5"></div>
                                    </td>
                                    <td>
                                        <p>Цвет:</p>
                                    </td>
                                    <td>
                                        <p><input type="color" param="7" placeholder="#ebede8" value="<?php echo $result[6]['k_c_value']; ?>"></p>
                                    </td>
                                </tr>
                            </table>
                            <button onclick="SaveColors(this);">Сохранить</button>
                        </div>
                    </div>
                    <div class="open_content" ind="7">
                        <p class="add_text_2">Цвет заголовков страниц</p>
                        <div class="text_red_pages">
                            <table class="color_tab">
                                <tr>
                                    <td>
                                        <div class="bl_color_6"></div>
                                    </td>
                                    <td>
                                        <p>Цвет:</p>
                                    </td>
                                    <td>
                                        <p><input type="color" param="8" placeholder="#198f18" value="<?php echo $result[7]['k_c_value']; ?>"></p>
                                    </td>
                                </tr>
                            </table>
                            <button onclick="SaveColors(this);">Сохранить</button>
                        </div>
                    </div>
                    <div class="open_content" ind="8">
                        <p class="add_text_2">Цвет выделенных блоков</p>
                        <div class="text_red_pages">
                            <table class="color_tab">
                                <tr>
                                    <td>
                                        <div class="bl_color_7"></div>
                                    </td>
                                    <td>
                                        <p>Цвет:</p>
                                    </td>
                                    <td>
                                        <p><input type="color" param="9" placeholder="#198f18" value="<?php echo $result[8]['k_c_value']; ?>"></p>
                                    </td>
                                </tr>
                            </table>
                            <button onclick="SaveColors(this);">Сохранить</button>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
            <?php
            if ($ShowParamID == 1) {
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Статистика</span></b><br>
                    <div class="statistik_last_login">
                        <table>
                            <tr>
                                <td colspan="2"><p class="style_1">Последний активность в системе</p></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Дата и время</p></td>
                                <td><p class="style_3"><?php echo $statistics->last_visit_date; ?></p></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">IP-адрес</p></td>
                                <td><p class="style_3"><?php echo $statistics->last_visit_ip; ?></p></td>
                            </tr>
                        </table>
                    </div>
                    <div class="statistik_users">
                        <table>
                            <tr>
                                <td colspan="2"><p class="style_1">Пользователи</p></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Всего пользователей</p></td>
                                <td><p class="style_3"><?php echo $statistics->user_all; ?></p></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Активных пользователей</p></td>
                                <td><p class="style_3"><?php echo $statistics->user_active; ?></p></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Неактивных пользователей</p></td>
                                <td><p class="style_3"><?php echo $statistics->user_inactive; ?></p></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Пользователей Экспертов</p></td>
                                <td><p class="style_3"><?php echo $statistics->user_experts; ?></p></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Администраторов в системе</p></td>
                                <td><p class="style_3"><?php echo $statistics->admin_active; ?></p></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Модераторов в системе</p></td>
                                <td><p class="style_3"><?php echo $statistics->moderator_active; ?></p></td>
                            </tr>
                        </table>
                    </div>
                    <div class="statistik_listings">
                        <table>
                            <tr>
                                <td colspan="3"><p class="style_1">Объявления</p></td>
                            </tr>
                            <tr>
                                <td colspan="2"><p class="style_2">Всего объявлений</p></td>
                                <td><p class="style_3"><?php echo $statistics->ads_all; ?></p></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Объявления недвижимости</p></td>
                                <td><a class="style_4" href="../../realty/">[Смотреть]</a></td>
                                <td><p class="style_3"><?php echo $statistics->immo_ads; ?></p></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Фото Объявления</p></td>
                                <td><a class="style_4" href="../../photoboard/">[Смотреть]</a></td>
                                <td><p class="style_3"><?php echo $statistics->ads_photo; ?></p></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Объявления раздела Работа</p></td>
                                <td><a class="style_4" href="../../job/">[Смотреть]</a></td>
                                <td><p class="style_3"><?php echo $statistics->ads_job; ?></p></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Организаций в Каталоге</p></td>
                                <td><a class="style_4" href="../../catalog/">[Смотреть]</a></td>
                                <td><p class="style_3"><?php echo $statistics->ads_catalog; ?></p></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Веб-камеры</p></td>
                                <td><a class="style_4" href="../../webcam/">[Смотреть]</a></td>
                                <td><p class="style_3"><?php echo $statistics->ads_webcams; ?></p></td>
                            </tr>
                        </table>
                    </div>
                    <div class="statistik_latest_users">
                        <table>
                            <tr>
                                <td colspan="2">
                                    <p class="style_1">Последние зарегистрированные пользователи</p>
                                </td>
                            </tr>
                            <?php
                            $dates = $statistics->last_5_users_date;
                            $logins = $statistics->last_5_users_login;
                            for ($n = 0; $n < count($statistics->last_5_users_login); $n++) {
                                echo '<tr>
                                        <td><p class="style_2">' . $dates[$n] . '</p></td>
                                        <td><p class="style_4">' . $logins[$n] . '</a></td>
                                        </tr>';
                            }
                            ?>
                        </table>
                    </div>
                </div>
                <?php
            }
            ?>

            <?php
            if ($ShowParamID == 2) {
                $banner = new BannersAll();
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Банеры Главной страницы</span></b><br>
                    <div class="baners">
                        <table border="1">
                            <tr>
                                <td><p class="style_2">Главный верхний банер</p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[0] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[0] . ');" title="Оставшееся время: ' . $banner->banner_end_days[0] . ' дней">';
                                    if ($banner->banner_end_days[0] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[0] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[0] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Банер 1</p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[1] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[1] . ');" title="Оставшееся время: ' . $banner->banner_end_days[1] . ' дней">';
                                    if ($banner->banner_end_days[1] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[1] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[1] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Банер 2</p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[2] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[2] . ');" title="Оставшееся время: ' . $banner->banner_end_days[2] . ' дней">';
                                    if ($banner->banner_end_days[2] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[2] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[2] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>  
                            </tr>
                            <tr>
                                <td><p class="style_2">Банер 3</p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[3] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[3] . ');" title="Оставшееся время: ' . $banner->banner_end_days[3] . ' дней">';
                                    if ($banner->banner_end_days[3] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[3] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[3] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Банер 4</p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[4] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[4] . ');" title="Оставшееся время: ' . $banner->banner_end_days[4] . ' дней">';
                                    if ($banner->banner_end_days[4] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[4] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[4] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td> 
                            </tr>
                            <tr>
                                <td><p class="style_2">Банер 5</p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[5] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[5] . ');" title="Оставшееся время: ' . $banner->banner_end_days[5] . ' дней">';
                                    if ($banner->banner_end_days[5] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[5] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[5] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Банер 6</p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[6] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[6] . ');" title="Оставшееся время: ' . $banner->banner_end_days[6] . ' дней">';
                                    if ($banner->banner_end_days[6] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[6] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[6] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php
            }
            ?>

            <?php
            if ($ShowParamID == 3 && $_SESSION['privileges'] == 1) {
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Управление Администраторами</span></b><br>
                    <div class="users_admin">
                        <table border="1">
                            <tr style="background: #7caed3;">
                                <td><p class="style_5">ID</p></td>
                                <td><p class="style_5">Логин</p></td>
                                <td><p class="style_5">Контактное лицо</p></td>
                                <td><p class="style_5">E-mail</p></td>
                                <td><p class="style_5">Зарегистрирован</p></td>
                                <td><p class="style_5">Действие</p></td>
                            </tr>
                            <?php
                            for ($i = 0; $i < count($admins->login); $i++) {
                                echo '<tr id="admin_tr_' . $admins->id[$i] . '" style="background: #f0f4f4;">
                                <td><p class="style_6">' . $admins->id[$i] . '</p></td>
                                <td><p class="style_7">' . $admins->login[$i] . '</p></td>
                                <td><p class="style_6">' . $admins->fname[$i] . ' ' . $admins->lname[$i] . '</p></td>
                                <td><p class="style_6">' . $admins->email[$i] . '</p></td>
                                <td><p class="style_6">' . $admins->reg_date[$i] . '</p></td>
                                <td><a class="a_1" onclick="ChangeBlockCParams(' . $admins->id[$i] . ');"><img src="../images/edit.png" title="Редактировать" alt=""></a>
                                <a class="a_1" onclick="ChangeBlockEParams(' . $admins->id[$i] . ');"><img src="../images/send_email.png" title="Отправить E-mail" alt=""></a>
                                <a class="a_1" onclick="DeleteAdminTR(' . $admins->id[$i] . ');"><img src="../images/delete.png" title="Удалить" alt=""></a></td>
                                </tr>';
                            }
                            ?>
                            <tr>
                                <td colspan="6"><a class="style_8" onclick="$('#add_admin').show(500);
                                            enableA();">+1</a></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php
            }
            ?>

            <?php
            if ($ShowParamID == 4 && $_SESSION['privileges'] == 1) {
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Управление Модераторами</span></b><br>
                    <div class="users_moderatori">
                        <table border="1">
                            <tr style="background: #7caed3;">
                                <td><p class="style_5">ID</p></td>
                                <td><p class="style_5">Логин</p></td>
                                <td><p class="style_5">Контактное лицо</p></td>
                                <td><p class="style_5">E-mail</p></td>
                                <td><p class="style_5">Зарегистрирован</p></td>
                                <td><p class="style_5">Действие</p></td>
                            </tr>
                            <?php
                            for ($a = 0; $a < count($moderators->login); $a++) {
                                echo '<tr id="moder_tr_' . $moderators->id[$a] . '" style="background: #f0f4f4;">
                                <td><p class="style_6">' . $moderators->id[$a] . '</p></td>
                                <td><p class="style_7">' . $moderators->login[$a] . '</p></td>
                                <td><p class="style_6">' . $moderators->fname[$a] . ' ' . $moderators->lname[$a] . '</p></td>
                                <td><p class="style_6">' . $moderators->email[$a] . '</p></td>
                                <td><p class="style_6">' . $moderators->reg_date[$a] . '</p></td>
                                <td>
                                    <a class="a_1" onclick="ChangeModerParams(' . $moderators->id[$a] . ');"><img src="../images/edit.png" title="Редактировать" alt=""></a>
                                    <a class="a_1" onclick="ChangeBlockEParams(' . $moderators->id[$a] . ');"><img src="../images/send_email.png" title="Отправить E-mail" alt=""></a>
                                    <a class="a_1" onclick="DeleteModerTR(' . $moderators->id[$a] . ');"><img src="../images/delete.png" title="Удалить" alt=""></a>
                                </td>
                            </tr>';
                            }
                            ?>
                            <tr>
                                <td colspan="6"><a class="style_8" onclick="$('#add_moderator').show(500);
                                            enableA();">+1</a></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php
            }
            ?>

            <?php
            if ($ShowParamID == 6) {
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Управление Пользователями</span></b><br>
                    <div class="users_users">
                        <?php
                        $users->GenerateNavigation($page);
                        ?>
                        <table border="1">
                            <tr style="background: #7caed3;">
                                <td><p class="style_5">ID</p></td>
                                <td><p class="style_5">Логин</p></td>
                                <td><p class="style_5">Контактное лицо</p></td>
                                <td><p class="style_5">E-mail</p></td>
                                <td><p class="style_5">Status</p></td>
                                <td><p class="style_5">Зарегистрирован</p></td>
                                <td><p class="style_5">Действие</p></td>
                            </tr>
                            <?php
                            for ($i = 0; $i < count($users->id); $i++) {
                                echo '<tr id="users_tr_' . $users->id[$i] . '" style="background: #f0f4f4;">
                                    <td><p class="style_6">' . $users->id[$i] . '</p></td>
                                    <td><p class="style_7">' . $users->login[$i] . '</p></td>
                                    <td><p class="style_6">' . $users->fname[$i] . ' ' . $users->lname[$i] . '</p></td>
                                    <td><p class="style_6">' . $users->email[$i] . '</p></td>
                                    <td><p class="style_6">' . ($users->status[$i]==1?'verified':'not verified') . '</p></td>
                                    <td><p class="style_6">' . $users->reg_date[$i] . '</p></td>
                                    <td>
                                    <a class="a_1" onclick="ChangeBlockUParams(' . $users->id[$i] . ');"><img src="../images/edit.png" title="Редактировать" alt=""></a>
                                    <a class="a_1" onclick="ChangeBlockEParams(' . $users->id[$i] . ');"><img src="../images/send_email.png" title="Отправить E-mail" alt=""></a>';
                                echo '<a class="a_1" onclick="IpBlock(' . $users->id[$i] . ');">';
                                if ($users->CheckBanIP($users->last_ip[$i])) {
                                    echo '<img id="IpBlock' . $users->id[$i] . '" src="../images/unblock_ip.png" title="Разблокировать IP: ' . $users->last_ip[$i] . '" alt="">';
                                } else {
                                    echo '<img id="IpBlock' . $users->id[$i] . '" src="../images/block_ip.png" title="Заблокировать IP: ' . $users->last_ip[$i] . '" alt="">';
                                }
                                echo '</a>';
                                echo '<a class="a_1" onclick="BlockUser(' . $users->id[$i] . ');">';
                                if ($users->banned[$i] == 0) {
                                    echo '<img id="BlockUser' . $users->id[$i] . '" src="../images/disable.png" title="Отключить пользователя" alt="">';
                                } else {
                                    echo '<img id="BlockUser' . $users->id[$i] . '" src="../images/enable.png" title="Включить пользователя" alt="">';
                                }
                                echo '</a>';
                                echo '<a class="a_1" onclick="DeleteUserTR(' . $users->id[$i] . ');"><img src="../images/delete.png" title="Удалить" alt=""></a>
                                    </td></tr>';
                            }
                            ?>
                            <tr>
                                <td colspan="7"><a class="style_8" onclick="$('#add_user').show(500);
                                            enableA();">+1</a></td>
                            </tr>
                        </table>
                        <?php
                        $users->GenerateNavigation($page);
                        ?>
                    </div>
                </div>
                <?php
            }
            ?>

            <?php
            if ($ShowParamID == 7 && $_SESSION['privileges'] == 1) {
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Настройки личного кабинета</span></b><br>
                    <form action="./?PageType=7" method="POST" onSubmit="return ChangePassCab();
                                return false;">
                        <table>
                            <tr>
                                <td colspan="2"><p class="style_1">Изменение Логина и пароля</p></td>
                            </tr>
                            <tr>
                                <td><p class="style_6">Введите новый Логин:</p></td>
                                <td><input type="text" id="new_login" name="new_login" onKeyUp="CheckUserAvailability('new_login');" value="<?php echo $_SESSION['login']; ?>"></td>
                            </tr>
                            <tr>
                                <td><p class="style_6">Введите новый Пароль:</p></td>
                                <td><input type="password" name="new_pass" id="new_pass" value=""></td>
                            </tr>
                            <tr>
                                <td><p class="style_6">Повторите Пароль:</p></td>
                                <td><input type="password" onkeyup="ChangePassAn();" name="new_pass2" id="new_pass2" value=""></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="submit" name="ChangePassCab" value="Сохранить">
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
                <?php
            }
            ?>

            <?php
            if ($ShowParamID == 8) {
                $warnings = new WarningMessages();
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Warning Messages</span></b><br>
                    <div class="baners">
                        <table border="1">
                            <tr>
                                <td><p class="style_1">Сообщение</p></td>
                                <td><p class="style_1">Дата</p></td>
                            </tr>
                            <?php
                            for ($i = 0; $i < count($warnings->message); $i++) {
                                ?>
                                <tr>
                                    <td><p class="style_2"><?php echo $warnings->message[$i]; ?></p></td>
                                    <td><p class="style_2"><?php echo $warnings->date[$i]; ?></p></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </table>
                    </div>
                </div>
                <?php
            }
            ?>

            <?php
            if ($ShowParamID == 9) {
                $ipbans = new IPBans();
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Баны по IP адресу</span></b><br>
                    <a class="a_1" onclick="$('#add_ip_ban').slideDown(500);
                                enableA();"><img src="../images/add_team.png" title="Добавить бан по IP" alt=""></a>
                    <div class="baners">
                        <table border="1">
                            <tr>
                                <td><p class="style_1">IP</p></td>
                                <td><p class="style_1">Дата окончания</p></td>
                                <td><p class="style_1">Действие</p></td>
                            </tr>
                            <?php
                            for ($i = 0; $i < count($ipbans->id); $i++) {
                                ?>
                                <tr>
                                    <td><p class="style_2"><?php echo $ipbans->ip[$i]; ?></p></td>
                                    <td><p class="style_2"><?php echo $ipbans->date[$i]; ?></p></td>
                                    <td>
                                        <a onclick="RemoveIP(this);" class="a_1">
                                            <?php
                                            echo '<img src="../images/delete.png" title="Удалить" alt="' . $ipbans->id[$i] . '">';
                                            ?>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </table>
                    </div>
                </div>
                <div id="add_ip_ban" class="wind">       <!--Всплывающее окно Регистрации Пользователя-->
                    <a class="close" onclick="CloseWindow('add_ip_ban');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Добавление IP в чёрный список:</p>
                    <table>
                        <tr>
                            <td>
                                <p class="style_2">IP:</p>
                            </td>
                            <td>
                                <input type="text" placeholder="127.0.0.1" id="NewIPBan" value="">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="button" onclick="AddBannedIP();" style="float:right;" value="Добавить">
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
            }
            ?>
        </div>

        <?php
        if ($ShowParamID == 2) {
            ?>
            <div id="wind1" class="wind">       <!--Всплывающее окна редактирования банера-->
                <a class="close" onclick="CloseWindow('wind1');">X</a>
                <br>
                <br>
                <p class="style_4">Вставьте код банера или загрузите его:</p>
                <input type="hidden" name="BannerChange" id="BannerChange" value="">
                <textarea id="BannerChangeID" rows="4" cols="50" name="BannerChangeID"></textarea>
                <button onclick="BannerSave();" style="float:right;">Сохранить</button>
                <button id="BannerLoad" style="float:right;">Загрузить</button>
                <div id="status"></div>
            </div>

            <div id="wind2" class="wind">
                <a class="close" onclick="CloseWindow('wind2');">X</a>
                <br>
                <br>
                <p class="style_7">Внешний вид</p>
                <div id="BannerViewID"></div>
            </div>

            <div id="info_baner_block" class="wind">       <!--Всплывающее окно редактирования Информации о владельце банера-->
                <a class="close" onclick="CloseWindow('info_baner_block');">X</a>
                <br>
                <br>
                <p class="style_7">Редактируем Владельца банера:</p>
                <table id="BannerInfoTable">
                </table>
            </div>

            <div id="time_baner_block" class="wind">       <!--Всплывающее окно редактирования Времени банера-->
                <a class="close" onclick="CloseWindow('time_baner_block');">X</a>
                <br>
                <br>
                <p id="BannerAddDaysLast" class="style_7">Период действия банера:</p><br>
                <table>
                    <tr>
                        <td><p class="style_2">Оставшееся время:</p></td>
                        <td>
                            <input id="BannerAddDays" type="text" value="">дней
                        </td>
                        <td>
                            <input type="hidden" id="BannerAddDaysID" value="">
                            <button onclick="AddDays();" style="float:left;">Установить</button>
                        </td>
                    </tr>
                </table>
            </div>
            <?php
        }
        ?>

        <?php
        if ($ShowParamID == 3 && $_SESSION['privileges'] == 1) {
            ?>
            <div id="edit_admin" class="wind">       <!--Всплывающее окно редактирования Администратора-->
                <a class="close" onclick="CloseWindow('edit_admin');">X</a>
                <br>
                <br>
                <p class="style_7">Редактировать Администратора:</p>
                <table>
                    <tr>
                        <td><p class="style_2">Логин:</p></td>
                        <td><input name="LoginAdmin" id="LoginAdmin" onKeyUp="CheckUserAvailability('LoginAdmin');" type="text" value=""></td>
                    </tr>
                    <tr>
                        <td><p class="style_2">Пароль:</p></td>
                        <td><input name="PasswordAdmin" id="PasswordAdmin" type="password" value=""></td>
                    </tr>
                    <tr>
                        <td><p class="style_2">E-mail:</p></td>
                        <td><input name="EmailAdmin" id="EmailAdmin" onKeyUp="CheckEmailAvailability('EmailAdmin');" type="text" value=""></td>
                    </tr>
                    <tr>
                        <td><p class="style_2">Имя:</p></td>
                        <td><input name="NameAdmin" id="NameAdmin" type="text" value=""></td>
                    </tr>
                    <tr>
                        <td><p class="style_2">Фамилия:</p></td>
                        <td><input name="SecNameAdmin" id="SecNameAdmin" type="text" value=""></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="id_admin" value="" id="ParamsChange">
                            <button name="ChangeAdminPref" onclick="ChangeAdminPref();" style="float:right;">Изменить</button>
                        </td>
                    </tr>
                </table>
            </div>

            <div id="add_admin" class="wind">       <!--Всплывающее окно Регистрации администратора-->
                <a class="close" onclick="CloseWindow('add_admin');">X</a>
                <br>
                <br>
                <p class="style_7">Добавление нового Администратора:</p>
                <form method="POST" action="./?PageType=3" onsubmit="return BeforeSave();">
                    <table>
                        <tr>
                            <td><p class="style_2">Логин:</p></td>
                            <td>
                                <input type="text" name="NewAdminLogin" id="NewAdminLogin" value="" onKeyUp="CheckUserAvailability('NewAdminLogin');">
                            </td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Пароль:</p></td>
                            <td><input type="password" id="NewAdminPassword" name="NewAdminPassword" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">E-mail:</p></td>
                            <td><input type="text" id="NewAdminEmail" name="NewAdminEmail" onKeyUp="CheckEmailAvailability('NewAdminEmail');" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Имя:</p></td>
                            <td><input type="text" name="NewAdminFName" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Фамилия:</p></td>
                            <td><input type="text" name="NewAdminLName" value=""></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="submit" style="float:right;" value="Добавить" name="NewAdminSubmit">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <?php
        }
        ?>

        <?php
        if ($ShowParamID == 4 && $_SESSION['privileges'] == 1) {
            ?>
            <div id="edit_moderator" class="wind">       <!--Всплывающее окно Редактировать Модератора-->
                <a class="close" onclick="CloseWindow('edit_moderator');">X</a>
                <br>
                <br>
                <p class="style_7">Редактирование Модератора:</p>
                <form action="./" method="POST">
                    <table>
                        <tr>
                            <td><p class="style_2">Логин:</p></td>
                            <td><input type="text" name="ModerLogin" id="ModerLogin" onKeyUp="CheckUserAvailability('ModerLogin');" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Пароль:</p></td>
                            <td><input type="password" name="ModerPassword" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">E-mail:</p></td>
                            <td><input type="text" name="ModerEmail" id="ModerEmail" onKeyUp="CheckEmailAvailability('ModerEmail');" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Имя:</p></td>
                            <td><input type="text" name="ModerName" id="ModerName" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Фамилия:</p></td>
                            <td><input type="text" name="ModerSecName" id="ModerSecName" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Права:</p></td>
                            <td id="ModerCatSelect">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="hidden" name="id_moder_ch" value="" id="ParamsModerChange">
                                <input type="submit" name="ModeratorChange" style="float:right;" value="Изменить">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <div id="add_moderator" class="wind">       <!--Всплывающее окно Регистрации Модератора-->
                <a class="close" onclick="CloseWindow('add_moderator');">X</a>
                <br>
                <br>
                <p class="style_7">Добавление нового Модератора:</p>
                <form action="./?PageType=4" method="POST">
                    <table>
                        <tr>
                            <td><p class="style_2">Логин:</p></td>
                            <td><input type="text" id="NewModerLogin" name="NewModerLogin" onKeyUp="CheckUserAvailability('NewModerLogin');" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Пароль:</p></td>
                            <td><input type="password" name="NewModerPassword" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">E-mail:</p></td>
                            <td><input type="text" id="NewModerEmail" name="NewModerEmail" onKeyUp="CheckEmailAvailability('NewModerEmail');" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Имя:</p></td>
                            <td><input type="text" name="NewModerName" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Фамилия:</p></td>
                            <td><input type="text" name="NewModerSecName" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Права:</p></td>
                            <td>
                                <ul class="access_list">
                                    <?php
                                    for ($a = 1; $a <= count($moder_categories[1]); $a++) {
                                        ?>
                                        <li>
                                            <label><input type="checkbox" name="moda[]" <?php if ($moder_categories[1][$a] == 1) echo 'checked="checked"'; ?> value="<?php echo $moder_categories[1][$a]; ?>"><?php echo $moder_categories[0][$a]; ?></label>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="submit" name="NewModeratorCreate" style="float:right;" value="Добавить">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <?php
        }
        ?>

        <?php
        if ($ShowParamID == 6) {
            ?>
            <div id="edit_user" class="wind">       <!--Всплывающее окно редактирования пользователя-->
                <a class="close" onclick="CloseWindow('edit_user');">X</a>
                <br>
                <br>
                <p class="style_7">Редактировать Пользователя:</p>
                <table>
                    <tr>
                        <td><p class="style_2">Логин:</p></td>
                        <td><input type="text" name="LoginUser" id="LoginUser" onKeyUp="CheckUserAvailability('LoginUser');" value=""></td>
                    </tr>
                    <tr>
                        <td><p class="style_2">Пароль:</p></td>
                        <td><input name="PasswordUser" id="PasswordUser" type="password" value=""></td>
                    </tr>
                    <tr>
                        <td><p class="style_2">E-mail:</p></td>
                        <td><input type="text" name="EmailUser" id="EmailUser" onKeyUp="CheckEmailAvailability('EmailUser');" value=""></td>
                    </tr>
                    <tr>
                        <td><p class="style_2">Имя:</p></td>
                        <td><input type="text" name="NameUser" id="NameUser" value=""></td>
                    </tr>
                    <tr>
                        <td><p class="style_2">Фамилия:</p></td>
                        <td><input type="text" name="SecNameUser" id="SecNameUser" value=""></td>
                    </tr>
                    <tr>
                        <td><p class="style_2">Статус:</p></td>
                        <td>
                            <select ind="5" attr="attr" id="StatusUser">
                                <option value="1">Verified</option><option selected="selected" value="0">Not verified</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="id_user_ch" value="" id="ParamsUChange">
                            <button name="ChangeUserPref" onclick="ChangeUserPref();" style="float:right;">Изменить</button>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="add_user" class="wind">       <!--Всплывающее окно Регистрации Пользователя-->
                <a class="close" onclick="CloseWindow('add_user');">X</a>
                <br>
                <br>
                <p class="style_7">Добавление нового Пользователя:</p>
                <form method="POST" action="./?PageType=6">
                    <table>
                        <tr>
                            <td><p class="style_2">Логин:</p></td>
                            <td><input type="text" name="UserLoginAdd" id="UserLoginAdd" onKeyUp="CheckUserAvailability('UserLoginAdd');" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Пароль:</p></td>
                            <td><input type="password" name="UserPasswordAdd" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">E-mail:</p></td>
                            <td><input type="text" id="UserEmailAdd" name="UserEmailAdd" onKeyUp="CheckEmailAvailability('UserEmailAdd');" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Имя:</p></td>
                            <td><input type="text" name="UserNameAdd" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Фамилия:</p></td>
                            <td><input type="text" name="UserSecNameAdd" value=""></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="submit" name="UserAdd" style="float:right;" value="Добавить">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <?php
        }
        ?>

        <div id="send_email" class="wind">
            <a class="close" onclick="CloseWindow('send_email');">X</a>
            <br>
            <br>
            <p class="style_7">Отправить Письмо</p>
            <table>
                <tr>
                    <td><p class="style_2">Тема:</p></td>
                    <td><input id="mailtheme" type="text" value=""></td>
                </tr>
                <tr>
                    <td><p class="style_2">E-mail:</p></td>
                    <td><p id="EmailChange2" class="style_7"></p></td>
                </tr>
                <tr>
                    <td><p class="style_2">Status:</p></td>
                    <td><p id="StatusChange2" class="style_7"></p></td>
                </tr>
                <tr>
                    <td colspan="2"><p class="style_2">Текст:</p></td>
                </tr>
                <tr>
                    <td colspan="2"><textarea rows="10" cols="50" id="text_mail"></textarea></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="hidden" value="" id="EmailChange">
                        <button onclick="mailAdminSend();" style="float:right;">Отправить</button>
                    </td>
                </tr>
            </table>
        </div>
        <div class="temno" id="temno"></div>
    </body>
</html>