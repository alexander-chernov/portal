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
            if (!UserAccess(6)) {
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
        switch ($ShowParamID) {
            case 1: $typej = 1;
                break;
            case 3: $typej = 2;
                break;
        }
        $where = array(array('k_j_type'), array(':type'), array(''), array($typej));

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
        <p class="topic">Управление разделом: <span style="color: #ff9c00;">Работа</span></p>
        <div class="center_block">
            <div id="menu_2">
                <a href="./?PageType=1">Предложения о работе</a>
                <a href="./?PageType=2">Банеры</a>
                <a href="./?PageType=3">Ищу работу</a>
            </div>

            <?php
            if ($ShowParamID == 1) {
                $job_ads = new JobAds();
                $job_ads->LoadAds(30, $page, $where);
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Управление объявлениями Требуются</span></b><br><br>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img class="img_options" src="../images/delete_team.png" title="Удалить выделенные объявления" alt="">
                                <img class="img_options" src="../images/aktive_team.png" title="Активировать выделенные объявления" alt="">
                                <img class="img_options" src="../images/deactivate_team.png" title="Скрыть выделенные объявления" alt="">
                            </td>
                            <td style="text-align: right;">
                                <img id="lupa_plus" class="img_options_1" src="../images/lupa_plus.png" title="Развернуть поиск" alt="" onclick="SearchOpen();">
                                <img  id="lupa_minus" class="img_options_1" style="display: none;" src="../images/lupa_minus.png" title="Свернуть поиск" alt="" onclick="SearchClose();">
                            </td>
                        </tr>
                    </table>
                    <div id="parametr_search" class="searc_ramka" style="display: none;">
                        <form action="./" method="GET">
                            <table>
                                <tr>
                                    <td><p class="style_2">По <span style="color: green;">№ объявления</span>:</p></td>
                                    <td><input type="text" name="SearchNum" value=""></td>
                                </tr>
                                <tr>
                                    <td><p class="style_2">По <span style="color: green;">Статусу</span>:</p></td>
                                    <td>
                                        <select name="SearchState">
                                            <option selected>Выберите статус</option>
                                            <option value="1">Активные</option>
                                            <option value="2">Скрытые</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="hidden" name="PageType" <?php echo 'value="' . $ShowParamID . '"'; ?>>
                                        <input name="Search" type="submit" style="float:left;" value="Найти">
                                    <td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    <?php
                    $job_ads->GenerateNavigation($page, $where_str, $link);
                    ?>
                    <table style="width: 100%; text-align: center;">
                        <tr style="background: #7caed3;">
                            <td colspan="2"><p class="style_5">№ объявления</p></td>
                            <td><p class="style_5">Пользователь</p></td>
                            <td><p class="style_5">Наименование объявления</p></td>
                            <td><p class="style_5">Дата</p></td>
                            <td><p class="style_5">Статус</p></td>
                            <td><p class="style_5">Действие</p></td>
                        </tr>
                        <?php
                        for ($i = 0; $i < count($job_ads->id); $i++) {
                            ?>
                            <tr style="background: #f0f4f4;" <?php echo 'id="job_tr_' . $job_ads->id[$i] . '"'; ?>>
                                <td style="width: 20px;"><input type="checkbox" value=""></td>
                                <td style="width: 80px;"><p class="style_4"><?php echo $job_ads->id[$i]; ?></p></td>
                                <td>
                                    <p class="style_4">
                                        <?php
                                        if ($job_ads->user_id[$i]) {
                                            echo $job_ads->user_login[$i];
                                        } else {
                                            echo 'Гость';
                                        }
                                        ?>
                                    </p>
                                </td>
                                <td style="width: 250px;">
                                    <p class="style_4">
                                        <?php
                                        if ($job_ads->type[$i] == 1) {
                                            echo 'Требуется';
                                        }
                                        if ($job_ads->type[$i] == 2) {
                                            echo 'Ищу работу';
                                        }
                                        ?>
                                        <br>
                                        <span class="name_job_team">
                                            <?php
                                            echo $job_ads->post[$i];
                                            ?>
                                        </span>
                                    </p>
                                </td>
                                <td>
                                    <p class="style_4">
                                        <?php
                                        echo $job_ads->date_reg[$i];
                                        ?>
                                    </p>
                                </td>
                                <td>
                                    <?php
                                    if ($job_ads->state[$i]) {
                                        echo '<p class="style_4_1">Активно</p>';
                                    } else {
                                        echo '<p class="style_4_2">Скрыто</p>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="SendEmail(\'' . $job_ads->email[$i] . '\')"'; ?>><img src="../images/send_email.png" title="Отправить E-mail Владельцу объявления" alt=""></a>
                                    <a class="a_1" <?php echo 'onclick="ShowInfo(' . $job_ads->id[$i] . ');"'; ?> title="Информация по объявлению"><img src="../images/info.png" alt=""></a>
                                    <a class="a_1">
                                        <?php
                                        if ($job_ads->state[$i] == 0) {
                                            echo '<img src="../images/enable.png" title="Показать объявление" onclick="DisEn(' . $job_ads->id[$i] . ', this);" alt="">';
                                        } else {
                                            echo '<img src="../images/disable_1.png" title="Скрыть объявление" onclick="DisEn(' . $job_ads->id[$i] . ', this);" alt="">';
                                        }
                                        ?>
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="UpJob(' . $job_ads->id[$i] . ');"'; ?>><img src="../images/up.png" title="Поднять объявление" alt=""></a>
                                    <a class="a_1" <?php echo 'onclick="DeleteJob(' . $job_ads->id[$i] . ');"'; ?>><img src="../images/delete.png" title="Удалить объявление" alt=""></a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <?php
                    $job_ads->GenerateNavigation($page, $where_str, $link);
                    ?>
                </div>
                <?php
            }
            ?>

            <?php
            if ($ShowParamID == 2) {
                $banner = new BannersAll();
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Банеры страницы Работа</span></b><br><br>
                    <div class="baners">
                        <table border="1">
                            <tr>
                                <td colspan="2"><p class="style_1">Банеры страницы Работа</p></td>
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
                $job_ads = new JobAds();
                $job_ads->LoadAds(30, $page, $where);
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Управление объявлениями Ищу</span></b><br><br>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img class="img_options" src="../images/delete_team.png" title="Удалить выделенные объявления" alt="">
                                <img class="img_options" src="../images/aktive_team.png" title="Активировать выделенные объявления" alt="">
                                <img class="img_options" src="../images/deactivate_team.png" title="Скрыть выделенные объявления" alt="">
                            </td>
                            <td style="text-align: right;">
                                <img id="lupa_plus" class="img_options_1" src="../images/lupa_plus.png" title="Развернуть поиск" alt="" onclick="SearchOpen();">
                                <img id="lupa_minus" class="img_options_1" style="display: none;" src="../images/lupa_minus.png" title="Свернуть поиск" alt="" onclick="SearchClose();">
                            </td>
                        </tr>
                    </table>
                    <div id="parametr_search" class="searc_ramka" style="display: none;">
                        <form action="./" method="GET">
                            <table>
                                <tr>
                                    <td><p class="style_2">По <span style="color: green;">№ объявления</span>:</p></td>
                                    <td><input type="text" name="SearchNum" value=""></td>
                                </tr>
                                <tr>
                                    <td><p class="style_2">По <span style="color: green;">Статусу</span>:</p></td>
                                    <td>
                                        <select name="SearchState">
                                            <option selected>Выберите статус</option>
                                            <option value="1">Активные</option>
                                            <option value="2">Скрытые</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="hidden" name="PageType" <?php echo 'value="' . $ShowParamID . '"'; ?>>
                                        <input name="Search" type="submit" style="float:left;" value="Найти">
                                    <td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    <?php
                    $job_ads->GenerateNavigation($page, $where_str, $link);
                    ?>
                    <table style="width: 100%; text-align: center;">
                        <tr style="background: #7caed3;">
                            <td colspan="2"><p class="style_5">№ объявления</p></td>
                            <td><p class="style_5">Пользователь</p></td>
                            <td><p class="style_5">Наименование объявления</p></td>
                            <td><p class="style_5">Дата</p></td>
                            <td><p class="style_5">Статус</p></td>
                            <td><p class="style_5">Действие</p></td>
                        </tr>
                        <?php
                        for ($i = 0; $i < count($job_ads->id); $i++) {
                            ?>
                            <tr style="background: #f0f4f4;" <?php echo 'id="job_tr_' . $job_ads->id[$i] . '"'; ?>>
                                <td style="width: 20px;"><input type="checkbox" value=""></td>
                                <td style="width: 80px;"><p class="style_4"><?php echo $job_ads->id[$i]; ?></p></td>
                                <td>
                                    <p class="style_4">
                                        <?php
                                        if ($job_ads->user_id[$i]) {
                                            echo $job_ads->user_login[$i];
                                        } else {
                                            echo 'Гость';
                                        }
                                        ?>
                                    </p>
                                </td>
                                <td style="width: 250px;">
                                    <p class="style_4">
                                        <?php
                                        if ($job_ads->type[$i] == 1) {
                                            echo 'Требуется';
                                        }
                                        if ($job_ads->type[$i] == 2) {
                                            echo 'Ищу работу';
                                        }
                                        ?>
                                        <br>
                                        <span class="name_job_team">
                                            <?php
                                            echo $job_ads->post[$i];
                                            ?>
                                        </span>
                                    </p>
                                </td>
                                <td>
                                    <p class="style_4">
                                        <?php
                                        echo $job_ads->date_reg[$i];
                                        ?>
                                    </p>
                                </td>
                                <td>
                                    <?php
                                    if ($job_ads->state[$i]) {
                                        echo '<p class="style_4_1">Активно</p>';
                                    } else {
                                        echo '<p class="style_4_2">Скрыто</p>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="SendEmail(\'' . $job_ads->email[$i] . '\')"'; ?>><img src="../images/send_email.png" title="Отправить E-mail Владельцу объявления" alt=""></a>
                                    <a class="a_1" <?php echo 'onclick="ShowInfo(' . $job_ads->id[$i] . ');"'; ?> title="Информация по объявлению"><img src="../images/info.png" alt=""></a>
                                    <a class="a_1">
                                        <?php
                                        if ($job_ads->state[$i] == 0) {
                                            echo '<img src="../images/enable.png" title="Показать объявление" onclick="DisEn(' . $job_ads->id[$i] . ', this);" alt="">';
                                        } else {
                                            echo '<img src="../images/disable_1.png" title="Скрыть объявление" onclick="DisEn(' . $job_ads->id[$i] . ', this);" alt="">';
                                        }
                                        ?>
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="UpJob(' . $job_ads->id[$i] . ');"'; ?>><img src="../images/up.png" title="Поднять объявление" alt=""></a>
                                    <a class="a_1" <?php echo 'onclick="DeleteJob(' . $job_ads->id[$i] . ');"'; ?>><img src="../images/delete.png" title="Удалить объявление" alt=""></a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <?php
                    $job_ads->GenerateNavigation($page, $where_str, $link);
                    ?>
                </div>
                <?php
            }
            ?>
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
                    <td><p id="EmailChange" class="style_7"></p></td>
                </tr>
                <tr>
                    <td colspan="2"><p class="style_2">Текст:</p></td>
                </tr>
                <tr>
                    <td colspan="2"><textarea rows="10" cols="50" id="text_mail"></textarea></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <button onclick="mailSend();" style="float:right;">Отправить</button>
                    </td>
                </tr>
            </table>
        </div>

        <div id="info_job_treb" class="wind">       <!--Всплывающее окно информации по объявлению Работодателя-->
            <a class="close" onclick="CloseWindow('info_job_treb');">X</a>
            <br>
            <br>
            <p class="style_7">Информация по объявлению Работодателя</p>
            <hr>
            <table style="text-align: left;">
                <tr>
                    <td colspan="2"><b><span style="color: #000;">Условия</span></b></td>
                </tr>
                <tr>
                    <td><span>Зарплата:</span></td>
                    <td>
                        <span style="color: #5370ce;" id="salary_min"></span>
                        <span style="color: #5370ce;" id="salary_max"></span>
                    </td>
                </tr>
                <tr>
                    <td><span>Возраст:</span></td>
                    <td>
                        <span style="color: #5370ce;" id="age_min"></span>
                        <span style="color: #5370ce;" id="age_max"></span>
                    </td>
                </tr>
                <tr>
                    <td><span>Пол:</span></td>
                    <td>
                        <span style="color: #5370ce;" id="sex"></span>
                    </td>
                </tr>
                <tr style="border-bottom: 1px solid #ccc;">
                    <td><span>Образование:</span></td>
                    <td>
                        <span style="color: #5370ce;" id="education_t"></span>
                    </td>
                </tr>
                <tr style="border-bottom: 1px solid #ccc;">
                    <td><span>Образование (подробно):</span></td>
                    <td>
                        <span style="color: #5370ce;" id="education"></span>
                    </td>
                </tr>
                <tr>
                    <td><span>Опыт работы:</span></td>
                    <td>
                        <span style="color: #5370ce;" id="experience"></span>
                    </td>
                </tr>
                <tr>
                    <td><span>График работы:</span></td>
                    <td>
                        <span style="color: #5370ce;" id="schedule"></span>
                    </td>
                </tr>
            </table>
            <hr>
            <table style="text-align: left;">
                <tr>
                    <td>
                        <b><span style="color: #000;">Требования</span></b>
                        <div style=" width: 550px;">
                            <p class="admin_treb_uslovia" id="req_text">
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
            <hr>
            <table style="text-align: left;">
                <tr>
                    <td colspan="2"><b><span style="color: #000;">Контактная информация</span></b></td>
                </tr>
                <tr>
                    <td><span>Организация:</span></td>
                    <td>
                        <span style="color: #5370ce;" id="organization"></span>
                    </td>
                </tr>
                <tr>
                    <td><span>Контактное лицо:</span></td>
                    <td>
                        <span style="color: #5370ce;" id="contact_name"></span>
                    </td>
                </tr>
                <tr>
                    <td><span>Телефон:</span></td>
                    <td>
                        <span style="color: #5370ce;" id="contact_phone"></span>
                    </td>
                </tr>
                <tr>
                    <td><span>E-mail:</span></td>
                    <td>
                        <span style="color: #5370ce;" id="email"></span>
                    </td>
                </tr>
            </table>
            <hr>
        </div>

        <div id="info_job_soiskat" class="wind">       <!--Всплывающее окно информации по объявлению Соискателя-->
            <a class="close" onclick="document.getElementById('info_job_soiskat').style.display = 'none';
                    disableA();">X</a>
            <br>
            <br>
            <p class="style_7">Информация по объявлению Соискателя</p>
            <hr>
            <table style="text-align: left;">
                <tr>
                    <td colspan="2"><b><span style="color: #000;">Личные данные</span></b></td>
                </tr>
                <tr>
                    <td><span>Искомая должность:</span></td>
                    <td><span style="color: #5370ce;">от 2500 руб/мес</span></td>
                </tr>
                <tr>
                    <td><span>Заработная плата от:</span></td>
                    <td><span style="color: #5370ce;">от 2500 руб/мес</span></td>
                </tr>
                <tr>
                    <td><span>Пол:</span></td>
                    <td><span style="color: #5370ce;">от 2500 руб/мес</span></td>
                </tr>
                <tr>
                    <td><span>Возраст:</span></td>
                    <td><span style="color: #5370ce;">от 2500 руб/мес</span></td>
                </tr>
                <tr>
                    <td><span>Семейное положение:</span></td>
                    <td><span style="color: #5370ce;">от 2500 руб/мес</span></td>
                </tr>
            </table>
            <hr>
            <table style="text-align: left;">
                <tr>
                    <td>
                        <b><span style="color: #000;">Образование</span></b>
                        <div style=" width: 550px;">
                            <p class="admin_treb_uslovia">
                                Учебно-консалтинговый центр Газ. Нефть,
                                Квалификация – оператор по добыче нефти и газа 
                                5 (пятого) разряда,
                                Удостоверение №190/1-11-02 от 24 июня 2011 г.,
                                Протокол № 190/1-11 от 14 июня 2011 г.
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
            <hr>
            <table style="text-align: left;">
                <tr>
                    <td>
                        <b><span style="color: #000;">Опыт работы</span></b>
                        <div style=" width: 550px;">
                            <p class="admin_treb_uslovia">
                                Опыта работы нет
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
            <hr>
            <table style="text-align: left;">
                <tr>
                    <td>
                        <b><span style="color: #000;">О себе</span></b>
                        <div style=" width: 550px;">
                            <p class="admin_treb_uslovia">
                                Трудолюбивый, добропорядочный
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
            <hr>
            <table style="text-align: left;">
                <tr>
                    <td colspan="2"><b><span style="color: #000;">Контактная информация</span></b></td>
                </tr>
                <tr>
                    <td><span>Телефон:</span></td>
                    <td><span style="color: #5370ce;">от 2500 руб/мес</span></td>
                </tr>
                <tr>
                    <td><span>E-mail:</span></td>
                    <td><span style="color: #5370ce;">от 2500 руб/мес</span></td>
                </tr>
                <tr>
                    <td><span>Объявление №:</span></td>
                    <td><span style="color: #5370ce;">от 2500 руб/мес</span></td>
                </tr>
                <tr>
                    <td><span>Добавлено:</span></td>
                    <td><span style="color: #5370ce;">от 2500 руб/мес</span></td>
                </tr>
                <tr>
                    <td><span>Опубликовано:</span></td>
                    <td><span style="color: #5370ce;">от 2500 руб/мес</span></td>
                </tr>
                <tr>
                    <td><span>Истекает:</span></td>
                    <td><span style="color: #5370ce;">от 2500 руб/мес</span></td>
                </tr>
            </table>
            <hr>
        </div>

        <div class="temno" id="temno"></div>   <!--Всплывающие окна конец-->
    </body>
</html>