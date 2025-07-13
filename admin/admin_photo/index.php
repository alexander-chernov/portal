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
        $where = array();
        $where_str = '';
        $link = '';

        require_once '../inc/configs.php';
        require_once 'inc/classes.php';
        //require_once 'inc/admin_functions.php';
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
            if (!UserAccess(3)) {
                exit('У вас нет прав заходить в эту категорию!');
            }
        } elseif ($_SESSION['privileges'] == 1) {
            $_SESSION['map_access'] = 1;
        }
        UpdateActivityAdmin();
        require_once '../inc/functions.php';
        CreateTempTables();

        //Меняем категории
        if (isset($_GET['PageType'])) {
            $ShowParamID = filter_var($_GET['PageType'], FILTER_VALIDATE_INT);
        } else {
            $ShowParamID = 1;
        }

        if (!in_array($ShowParamID, array(1, 2, 3))) {
            header($_ENV['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
            header("Location: http://" . _SERVER_ADDRESS . "/page404.php");
        }
        ?>
        <meta name="keywords" content="">
        <meta name="description" content="">
        <link rel="stylesheet" type="text/css" href="../css/style_admin.css">
        <link rel="stylesheet" type="text/css" href="../css/style_wind.css">
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script type="text/javascript" src="../js/wind.js"></script>
        <script type="text/javascript" src="js/script.js"></script>
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
        <p class="topic">Управление разделом: <span style="color: #ff9c00;">Фото Объявления</span></p>
        <div class="center_block">
            <div id="menu_2">
                <a href="./?PageType=1">Объявления</a>
                <a href="./?PageType=2">Банеры</a>
                <a href="./?PageType=3">Рубрики</a>
            </div>

            <?php
            if ($ShowParamID == 1) {
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Фото объявления</span></b><br><br>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img class="img_options" src="../images/delete_team.png" title="Удалить выделенные объявления" alt="">
                            </td>
                            <td style="text-align: right;">
                                <img id="lupa_plus" class="img_options_1" src="../images/lupa_plus.png" title="Развернуть поиск" alt="" onclick="SearchOpen();">
                                <img  id="lupa_minus" class="img_options_1" style="display: none;" src="../images/lupa_minus.png" title="Свернуть поиск" alt="" onclick="SearchClose();">
                            </td>
                        </tr>
                    </table>
                    <div id="parametr_search" style="display: none;">
                        <form method="GET" action="./">
                            <table>
                                <tr>
                                    <td><p class="style_2">По <span style="color: green;">№ объявления</span>:</p></td>
                                    <td><input type="text" name="SearchNum" value=""></td>
                                </tr>
                                <tr>
                                    <td><p class="style_2">По <span style="color: green;">нику пользователя</span>:</p></td>
                                    <td><input type="text" name="SearchName" value=""></td>
                                </tr>
                                <tr>
                                    <td><p class="style_2">По <span style="color: green;">рубрике</span>:</p></td>
                                    <td>
                                        <select name="SearchCategory">
                                            <option value="0">Не важно</option>
                                            <?php
                                            try {
                                                $queue2 = $mysql->prepare('SELECT * FROM k_photodesk_categories ORDER BY k_pdc_name ASC');
                                                $queue2->execute();
                                                $row = $queue2->fetchAll(PDO::FETCH_ASSOC);
                                            } catch (PDOException $e) {
                                                exit();
                                            }
                                            foreach ($row as $value) {
                                                echo '<option val="' . $value['k_pdc_id'] . '">' . $value['k_pdc_name'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <label><input type="checkbox" checked="checked" name="Types[]" value="1">VIP - лента</label>
                                        <label><input type="checkbox" checked="checked" name="Types[]" value="2">Платная лента </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td><input type="submit" name="Search" style="float:left; width: 100%;" value="Найти"></td>
                                    <td><a href="?PageType=1">Очистить поиск</a></td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    <?php
                    $photo_ads = new PhotoAds();
                    $photo_ads->LoadAds(50, $page, $where);
                    $photo_ads->GenerateNavigation($page, $where_str, $link);
                    ?>
                    <table style="width: 100%; text-align: center;">
                        <tr style="background: #7caed3;">
                            <td colspan="2"><p class="style_5">№</p></td>
                            <td><p class="style_5">Фото</p></td>
                            <td><p class="style_5">Рубрика</p></td>
                            <td><p class="style_5">Пользователь</p></td>
                            <td><p class="style_5">Дата</p></td>
                            <td><p class="style_5">Действие</p></td>
                        </tr>
                        <?php
                        for ($i = 1; $i <= count($photo_ads->getID(0)); $i++) {
                            ?>
                            <tr <?php echo 'id="tr_ad_num' . $photo_ads->getID($i) . '"'; ?> style="background: #f0f4f4;">
                                <td style="width: 20px;"><input type="checkbox" value=""></td>
                                <td style="width: 80px;"><p class="style_4"><?php echo $photo_ads->getID($i); ?></p></td>
                                <td style="width: 80px;">
                                    <?php
                                    if (preg_match('/video/', $photo_ads->getPhoto($i))) {
                                        $filename = '../../' . $photo_ads->getPhoto($i);
                                    } else {
                                        $filename = '' . $photo_ads->getPhoto($i);
                                    }
                                    //echo $filename;
                                    if ($photo_ads->getPhoto($i) != "" && file_exists($_SERVER['DOCUMENT_ROOT'].$filename)) {
                                        echo '<img class="img_ob" echo src="' . $photo_ads->getPhoto($i) . '" alt="">';
                                    }
                                    ?>
                                </td>
                                <td style="width: 160px;"><p class="style_4"><?php echo $photo_ads->getCategoryStr($i); ?></p></td>
                                <td><p class="style_4"><?php echo $photo_ads->getUserStr($i); ?></p></td>
                                <td><p class="style_4"><?php echo $photo_ads->getRegDate($i) . '<br>' . $photo_ads->getEndDate($i); ?></p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ChangeBlockEParams(' . $photo_ads->getUser($i) . ');"'; ?>><img src="../images/send_email.png" title="Отправить E-mail" alt=""></a>
                                    <a class="a_1" <?php echo 'onclick="InfoBlock(' . $photo_ads->getID($i) . ');"'; ?> title="Информация по пользователю"><img src="../images/info.png" alt=""></a>
                                    <a class="a_1" <?php echo 'onclick="EditAd(' . $photo_ads->getID($i) . ');"'; ?>><img src="../images/edit.png" title="Редактировать объявление" alt=""></a>
                                    <a class="a_1" <?php echo 'onclick="PhotosPanel(' . $photo_ads->getID($i) . ');"'; ?>><img src="../images/photo.png" title="Редактировать фото объявления" alt=""></a>
                                    <a class="a_1" <?php echo 'onclick="ShowComments(' . $photo_ads->getID($i) . ');"'; ?>><img src="../images/coment.png" title="Посмотреть коментарии объявления" alt=""></a>
                                    <?php
                                    if ($photo_ads->getVIP($i)) {
                                        ?>
                                        <a class="a_1"><img onclick="CommentVIP(this);" src="../images/vip_2.png" title="Убрать из VIP - ленты" alt="<?php echo $photo_ads->getID($i); ?>"></a>
                                        <?php
                                    } else {
                                        ?>
                                        <a class="a_1"><img onclick="CommentVIP(this);" src="../images/vip_1.png" title="Добавить в VIP - ленту" alt="<?php echo $photo_ads->getID($i); ?>"></a>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                    if ($photo_ads->getPaid($i)) {
                                        ?>
                                        <a class="a_1"><img onclick="CommentPaid(this);" src="../images/spec_2.png" title="Убрать из Платной ленты" alt="<?php echo $photo_ads->getID($i); ?>"></a>
                                        <?php
                                    } else {
                                        ?>
                                        <a class="a_1"><img onclick="CommentPaid(this);" src="../images/spec_1.png" title="Добавить в Платную ленту" alt="<?php echo $photo_ads->getID($i); ?>"></a>
                                        <?php
                                    }
                                    ?>
                                    <a class="a_1"><img onclick="PhotoUp(this);" src="../images/up.png" title="Поднять объявление в рубрике" alt="<?php echo $photo_ads->getID($i); ?>"></a>
                                    <a class="a_1"><img <?php echo 'onclick="AddDaysPhoto(' . $photo_ads->getID($i) . ');"'; ?> src = "../images/clock_green_1.png" title = "Продлить объявление" alt = ""></a>
                                    <a class="a_1">
                                        <?php
                                        if ($photo_ads->getOnMain($i) == 1) {
                                            ?>
                                            <img src="../images/not_main.png" title="Убрать с главной страницы" alt="<?php echo $photo_ads->getID($i); ?>" onclick="PhotoMainPage(this);">
                                            <?php
                                        } else {
                                            ?>
                                            <img src="../images/on_main.png" title="Добавить на главную страницу" alt="<?php echo $photo_ads->getID($i); ?>" onclick="PhotoMainPage(this);">
                                            <?php
                                        }
                                        ?>
                                    </a>
                                    <a class="a_1">
                                        <?php
                                        if ($photo_ads->getColorLight($i) == 1) {
                                            ?>
                                            <img src="../images/no_light.png" title="Убрать выделение цветом" alt="<?php echo $photo_ads->getID($i); ?>" onclick="PhotoColor(this);">
                                            <?php
                                        } else {
                                            ?>
                                            <img src="../images/color_light.png" title="Добавить выделение цветом" alt="<?php echo $photo_ads->getID($i); ?>" onclick="PhotoColor(this);">
                                            <?php
                                        }
                                        ?>
                                    </a>
                                    <a class="a_1"><img <?php echo 'onclick="BlockIP(\'' . $photo_ads->getIp($i) . '\');"'; ?> src="../images/block_ip.png" title="Блокировать по IP" alt=""></a>
                                    <a class="a_1"><img <?php echo 'onclick="DeleteAd(' . $photo_ads->getID($i) . ');"'; ?> src="../images/delete.png" title="Удалить объявление" alt=""></a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <?php
                    $photo_ads->GenerateNavigation($page, $where_str, $link);
                    ?>
                </div>

                <div id="AddDayBlock" class="wind">
                    <a class="close" onclick="CloseWindow('AddDayBlock');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Продление объявления</p>
                    <table>
                        <tr>
                            <td>
                                До конца действия объявления осталось
                            </td>
                            <td id="LastDays">
                            </td>
                            <td>
                                дней
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Продлить объявление на
                            </td>
                            <td>
                                <input type="hidden" id="PhotoForAddDays" value="">
                                <input type="text" id="DaysForAddDays" value="">
                            </td>
                            <td>
                                дней
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <button onClick="AddDaysSubmit();">Прибавить</button>
                            </td>
                        </tr>
                    </table>
                </div>

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
                            <td colspan="2"><p class="style_2">Текст:</p></td>
                        </tr>
                        <tr>
                            <td colspan="2"><textarea rows="10" cols="50" id="text_mail"></textarea></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="hidden" value="" id="EmailChange">
                                <button onclick="mailSend();" style="float:right;">Отправить</button>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="info_obiavlenie_block" class="wind">
                    <a class="close" onclick="CloseWindow('info_obiavlenie_block');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Информация по пользователю</p>
                    <table>
                        <tr>
                            <td><p class="style_2">Номер объявления:</p></td>
                            <td><p id="info_num" class="style_4_2"></p></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Контактное лицо:</p></td>
                            <td><p id="info_contact_name" class="style_4_1"></p></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Телефон:</p></td>
                            <td><p id="info_contacts" class="style_4_4"></p></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">E-mail:</p></td>
                            <td><p id="info_email" class="style_4_4"></p></td>
                        </tr>
                    </table>
                </div>

                <div id="edit_obiavlenie" class="wind">
                    <a class="close" onclick="CloseWindow('edit_obiavlenie');">X</a>
                    <br>
                    <br>
                    <table>
                        <tr>
                            <td><p class="style_2">Номер объявления:</p></td>
                            <td><p id="edit_num" class="style_4_2"></p></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Тема объявления:</p></td>
                            <td><input id="edit_theme" type="text" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Рубрика:</p></td>
                            <td>
                                <select id="edit_category">
                                    <?php
                                    $queue = $mysql->prepare('SELECT * FROM k_photodesk_categories ORDER BY k_pdc_name ASC');
                                    $queue->execute();
                                    $result = $queue->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($result as $value) {
                                        echo '<option value="' . $value['k_pdc_id'] . '">' . $value['k_pdc_name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <p class="style_2">Текст объявления:</p><br>
                                <textarea id="edit_text" rows="8" cols="50"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button onclick="SaveAd();" style="float: left;">Изменить</button>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="photo_obiavlenie" class="wind">       <!--Всплывающее окно редактировать фото объявления-->
                    <a class="close" onclick="CloseWindow('photo_obiavlenie');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Все фото объявления</p>
                    <table id="PhotoLoad">
                    </table>
                </div>

                <div id="photo_windows" class="wind">       <!--Всплывающее окно увеличение фото-->
                    <a class="close" onclick="$('#photo_windows').hide(500);">X</a>
                    <br>
                    <img alt="" class="img_windows" src="">
                </div>

                <div id="coment_obiavlenie" class="wind">       <!--Всплывающее окно просмотра коментариев-->
                    <a class="close" onclick="CloseWindow('coment_obiavlenie');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Коментарии объявления</p>
                    <br>
                    <div class="coment_scrol">
                        <table style="width: 100%; text-align: center;" id="CommentsTable">
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
                <div class="block_content_1"><b><span style="color: blue;">Банеры страницы Фото объявления</span></b><br><br>
                    <div class="baners">
                        <table border="1">
                            <tr>
                                <td colspan="2"><p class="style_1">Банеры страницы Фото объявления</p></td>
                            </tr>
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
                                <td><p class="style_2">Левый верхний банер</p></td>
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
                                <td><p class="style_2">Центральный верхний банер</p></td>
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
                                <td><p class="style_2">Правый верхний банер</p></td>
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
                        </table>
                    </div>
                </div>
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
            if ($ShowParamID == 3) {
                $categories = new CatalogCategoriesP();
                $categories->LoadCategories();
                ?>
                <script type="text/javascript">
                    function EditCategory(id) {
                        $.post("inc/admin_functions.php", {
                            EditCategory: id
                        }, function(data)
                        {
                            $('#CategoryName').val(data);
                        });
                        $('#CategoryID').val(id);
                        $('#edit_rub').show(500);
                        enableA();
                    }
                    function EditCategoryName() {
                        $.post("inc/admin_functions.php", {
                            EditCategoryName: $('#CategoryName').val(),
                            EditCategoryID: $('#CategoryID').val()
                        }, function(data)
                        {
                            if (data === 'yes') {
                                $('#category_' + $('#CategoryID').val()).text($('#CategoryName').val());
                                alert('\u0423спешно сохранено!');
                            }
                            if (data === 'no') {
                                alert('\u041fроизошла ошибка!');
                            }
                        });
                    }
                    function AddCategory() {
                        $.post("inc/admin_functions.php", {
                            AddCategory: $('#CategoryNew').val()
                        }, function(data)
                        {
                            if (parseInt(data, 10)) {
                                var table = document.getElementById('categories');
                                var newTr_d = document.createElement("tr");
                                newTr = table.appendChild(newTr_d);
                                newTr.setAttribute('style', 'background: #f0f4f4;');
                                newTr.setAttribute('id', 'tr_' + data);
                                var newTd_d1 = document.createElement("td");
                                newTd1 = newTr_d.appendChild(newTd_d1);
                                newTd1.innerHTML = '<p class="style_4">' + $('#CategoryNew').val() + '</p>';
                                var newTd_d2 = document.createElement("td");
                                newTd2 = newTr_d.appendChild(newTd_d2);
                                newTd2.innerHTML = '<p class="style_4">0</p>';
                                var newTd_d3 = document.createElement("td");
                                newTd3 = newTr_d.appendChild(newTd_d3);
                                newTd3.innerHTML = '<a onclick="EditCategory(' + data + ');" class="a_1"><img alt="" title="\u0420едактировать название рубрики" src="../images/edit.png"></a><a class="a_1" onclick="DeleteCategory(' + data + ')"><img src="../images/delete.png" title="\u0423далить рубрику" alt=""></a>';
                            }
                        });
                    }
                    function DeleteCategory(id) {
                        if (confirm('\u0420убрика и все входящие объявления будут удалены!')) {
                            $.post("inc/admin_functions.php", {
                                DeleteCategory: id
                            }, function(data)
                            {
                                if (data === 'yes') {
                                    $('#tr_' + id).fadeOut(500);
                                }
                            });
                        }
                    }
                </script>
                <div class="block_content_1"><b><span style="color: blue;">Добавление и удаление рубрик Фото Объявлений</span></b><br><br>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img class="img_options" src="../images/add_team.png" title="Добавить рубрику" alt="" onclick="$('#add_new_rub').show(500);
                                        enableA();">
                            </td>
                        </tr>
                    </table>
                    <table id="categories" style="width: 100%; text-align: center;">
                        <tr style="background: #7caed3;">
                            <td><p class="style_5">Наименование рубрики</p></td>
                            <td><p class="style_5">Объявлений в рубрике</p></td>
                            <td><p class="style_5">Действие</p></td>
                        </tr>
                        <?php
                        for ($i = 1; $i <= count($categories->getId(0)); $i++) {
                            ?>
                            <tr <?php echo 'id="tr_' . $categories->getId($i) . '"'; ?> style="background: #f0f4f4;">
                                <td><p <?php echo 'id="category_' . $categories->getId($i) . '"'; ?> class="style_4"><?php echo $categories->getName($i); ?></p></td>
                                <td><p class="style_4"><?php echo $categories->getCount($i); ?></p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="EditCategory(' . $categories->getId($i) . ');"'; ?>><img src="../images/edit.png" title="Редактировать название рубрики" alt=""></a>
                                    <a class="a_1" <?php echo 'onclick="DeleteCategory(' . $categories->getId($i) . ');"'; ?>><img src="../images/delete.png" title="Удалить рубрику" alt=""></a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>

                <div id="edit_rub" class="wind">       <!--Всплывающее окно редактировать рубрику-->
                    <a class="close" onclick="CloseWindow('edit_rub');">X</a>
                    <br>
                    <br>
                    <table>
                        <tr>
                            <td colspan="2"><p class="style_7">Редактировать название рубрики</p></td>
                        </tr>
                        <tr>
                            <td><p class="style_4">Введите новое название:</p></td>
                            <td><input type="text" id="CategoryName" value=""></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="hidden" id="CategoryID" value="0">
                                <button style="float: left;" onclick="EditCategoryName();">Изменить</button>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="add_new_rub" class="wind">       <!--Всплывающее окно добавления НОВОЙ РУБРИКИ-->
                    <a class="close" onclick="CloseWindow('add_new_rub');">X</a>
                    <br>
                    <br>
                    <table>
                        <tr>
                            <td colspan="2"><p class="style_7">Добавить новую рубрику</p></td>
                        </tr>
                        <tr>
                            <td><p class="style_4">Введите название:</p></td>
                            <td><input type="text" id="CategoryNew" value=""></td>
                        </tr>
                        <tr>
                            <td colspan="2"><button style="float: left;" onclick="AddCategory();">Добавить</button></td>
                        </tr>
                    </table>
                </div>
                <?php
            }
            ?>
        </div>

        <div class="temno" id="temno"></div>   <!--Всплывающие окна конец-->
    </body>
</html>