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

        if (!in_array($ShowParamID, array(1, 2))) {
            header($_ENV['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
            header("Location: http://" . _SERVER_ADDRESS . "/page404.php");
        }

        if (isset($_POST['NewWebcamSubmit'])) {
            $_POST['NewWebcamCode'] = filter_var($_POST['NewWebcamCode'], FILTER_SANITIZE_URL);
            try {
                $queue0 = $mysql->prepare('INSERT INTO k_webcams (k_w_name,k_w_url) VALUES (:name,:url)');
                $queue0->execute(array(":url" => $_POST['NewWebcamCode'],
                    ":name" => $_POST['NewWebcamName']));
            } catch (PDOException $e) {
                exit();
            }
        }
        ?>
        <meta name="description" content="">
        <link rel="stylesheet" type="text/css" href="../css/style_admin.css">
        <link rel="stylesheet" type="text/css" href="../css/style_wind.css">
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script type="text/javascript" src="../js/wind.js"></script>
        <script type="text/javascript" src="js/scripts.js"></script>
        <script type="text/javascript" src="js/ajaxupload.3.5.js"></script>
        <?php
        if ($ShowParamID == 2) {
            ?>
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
        <?php
        if ($ShowParamID == 1) {
            ?>
            <script type="text/javascript">
                $(function() {
                    var btnUpload = $('#WebcamImage');
                    new AjaxUpload(btnUpload, {
                        action: 'upload-file.php',
                        name: 'WebcamImage',
                        onComplete: function(file, response) {
                            if (response === "error") {
                            } else {
                                ChangeWebcamImage(response);
                            }
                        }
                    });
                });
            </script>
            <?php
        }
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
        <p class="topic">Управление разделом: <span style="color: #ff9c00;">Веб-камеры</span></p>
        <div class="center_block">
            <div id="menu_2">
                <a href="./?PageType=1">Веб-камеры</a>
                <a href="./?PageType=2">Банеры</a>
            </div>

            <?php
            if ($ShowParamID == 1) {
                $where = '';
                $webcams = new Webcams($page, $where);
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Управление Веб-камерами</span></b><br><br>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img class="img_options" src="../images/add_team.png" title="Добавить Веб-камеру" alt="" onclick="$('#add_camera').slideDown(500);
                    enableA();">
                                     <?php
                                     $blocks = @file_get_contents('../../inc/blocks.cfg');
                                     if ($blocks == 'FALSE') {
                                         ?>
                                    <img class="img_options" src="../images/enable.png" title="Отобразить веб-камеры на главной странице" alt="" onclick="EnableWebBlock(this);">
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                    <table style="width: 100%; text-align: center;">
                        <tr style="background: #7caed3;">
                            <td><p class="style_5">Веб-камера</p></td>
                            <td><p class="style_5">Наименование</p></td>
                            <td><p class="style_5">Действие</p></td>
                        </tr>
                        <?php
                        for ($i = 0; $i < count($webcams->id); $i++) {
                            ?>
                            <tr <?php echo 'id="web_' . $webcams->id[$i] . '"'; ?> style="background: #f0f4f4;">
                                <td style="width: 100px;">
                                    <?php
                                    if ($webcams->image[$i]) {
                                        echo '<img class="img_ob" src="../' . $webcams->image[$i] . '" alt="">';
                                    }
                                    ?>
                                </td>
                                <td style="width: 300px;"><p class="style_4"><?php echo $webcams->name[$i]; ?></p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="WebcamRedakt(' . $webcams->id[$i] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать Веб-камеру" alt="">
                                    </a>
                                    <a class="a_1">
                                        <img src="../images/delete.png" title="Удалить Веб-камеру" alt="">
                                    </a>
                                    <a <?php echo 'onclick="WebcamIDset(' . $webcams->id[$i] . ');"'; ?> class="a_1">
                                        <img src="../images/photo.png" title="Загрузить новую фотографию" alt="">
                                    </a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
                <div id="edit_camera" class="wind">       <!--Всплывающее окно редактировать Веб-камеру-->
                    <a class="close" onclick="CloseWindow('edit_camera');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Редактирование Веб-камеры</p>
                    <table>
                        <tr>
                            <td>
                                <p class="style_2">Название Веб-камеры:
                                    <input id="WebcamNameRed" type="text" value="">
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p class="style_2">Код для Веб-камеры:</p>
                                <textarea id="WebcamCodeRed" rows="10" cols="50" name="text"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="hidden" id="WebcamIDRed" value="">
                                <button style="float: left;" onclick="WebcamSave();">Сохранить изменения</button>
                            </td>
                        </tr>
                    </table>
                </div>
                <div id="add_camera" class="wind">       <!--Всплывающее окно добавить Веб-камеру-->
                    <a class="close" onclick="CloseWindow('add_camera');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Форма добавления Веб-камеры</p>
                    <form action="./" method="post">
                        <table>
                            <tr>
                                <td>
                                    <p class="style_2">Введите название Веб-камеры: 
                                        <input type="text" name="NewWebcamName" value="">
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p class="style_2">Вставьте код для Веб-камеры:</p>
                                    <textarea rows="10" cols="50" name="NewWebcamCode"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="submit" name="NewWebcamSubmit" style="float: left;" value="Добавить"></td>
                            </tr>
                        </table>
                    </form>
                </div>
                <div id="change_image" class="wind">
                    <a class="close" onclick="CloseWindow('change_image');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Загрузка фотографии</p>
                    <input type="hidden" id="WebcamIMG" value="">
                    <button id="WebcamImage">Загрузить</button>
                </div>
                <?php
            }
            ?>
            <?php
            if ($ShowParamID == 2) {
                $banner = new BannersAll();
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Банеры страницы Веб-камеры</span></b><br><br>
                    <div class="baners">
                        <table border="1">
                            <tr>
                                <td colspan="2"><p class="style_1">Банеры страницы Веб-камеры</p></td>
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

        </div>
        <div class="futter">
            <a href="http://tikweb.ru">&copy; Copyright 2012 <span style="text-shadow: #fff 1px 0 0px, #fff 0 1px 0px, #fff -1px 0 0px, #fff 0 -1px 0px; font-weight:bold;"><font color="blue">Tik</font><font color="red">WEB</font></span> тел: 8(3822) 94-54-64</a>
        </div>

        <div class="temno" id="temno"></div>   <!--Всплывающие окна конец-->
    </body>
</html>