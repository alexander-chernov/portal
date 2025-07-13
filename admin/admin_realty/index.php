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
        <meta name="keywords" content="">
        <meta name="description" content="">
        <?php
        require_once '../inc/configs.php';

        //Проверка авторизации
        if (!isset($_SESSION['login'])) {
            if (isset($_COOKIE['login'])) {
                $_SESSION['login'] = $_COOKIE['login'];
                $_SESSION['password'] = $_COOKIE['password'];
            } else {
                exit('Вы не авторизованы для данной страницы');
            }
        }

        //Выбор страницы для отображения
        if (isset($_GET['PageType'])) {
            $PageType = filter_var($_GET['PageType'], FILTER_VALIDATE_INT);
        } else {
            $PageType = 1;
        }
        if (isset($_GET['PageIndex'])) {
            $page = filter_var($_GET['PageIndex'], FILTER_VALIDATE_INT);
        } else {
            $page = 1;
        }

        if (!in_array($PageType, array(1, 2, 4, 3, 5, 6))) {
            header($_ENV['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
            header("Location: http://" . _SERVER_ADDRESS . "/page404.php");
        }

        require_once '../inc/db.php';

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
            if (!UserAccess(2)) {
                exit('У вас нет прав заходить в эту категорию!');
            }
        } elseif ($_SESSION['privileges'] == 1) {
            $_SESSION['map_access'] = 1;
        }
        UpdateActivityAdmin();
        require_once '../inc/functions.php';
        CreateTempTables();
        ?>
        <link rel="stylesheet" type="text/css" href="../css/style_admin.css">
        <link rel="stylesheet" type="text/css" href="../css/style_wind.css">
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script type="text/javascript" src="../js/wind.js"></script>
        <script type="text/javascript" src="js/scripts.js"></script>
        <script type="text/javascript" src="js/ajaxupload.3.5.js"></script>
        <?php
        if ($PageType == 2) {
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
        if ($PageType == 3) {
            ?>
            <script type="text/javascript">
                $(function() {
                    var btnUpload = $('#AgentUpload');
                    var status = $('#status2');
                    new AjaxUpload(btnUpload, {
                        action: 'upload-file.php',
                        name: 'uploadfile',
                        onSubmit: function(file, ext) {
                            status.html('<img src="../images/animate.gif" alt="">');
                        },
                        onComplete: function(file, response) {
                            status.text('');
                            if (response === "error") {
                                status.text('\u0412озникла ошибка!');
                            } else {
                                $('#AgentAvatarShow').attr('src', response);
                                AvatarChange(response);
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
                            </p></td>
                    </tr>
                    <tr>
                        <td><p class="autho_1">Логин:</p></td>
                        <td><p class="autho_2"><?php echo $_SESSION['login']; ?></p></td>
                    </tr>
                </table>
            </div>
        </div>
        <p class="topic">Управление разделом: <span style="color: #ff9c00;">Недвижимость</span></p>
        <div class="center_block">
            <div id="menu_2">
                <a href="./?PageType=1">Объявления</a>
                <a href="./?PageType=2">Банеры</a>
                <a href="./?PageType=3">Агентства</a>
                <a href="./?PageType=4">Новости</a>
                <a href="./?PageType=5">Куплю</a>
                <a href="./?PageType=6">Видео</a>
            </div>
            <?php
            if ($PageType == 1) {
                if (!isset($_SESSION['Where'])) {
                    $_SESSION['Where'] = '';
                }
                $immovable = new Realty();
                $immovable->LoadRealty(50, $page, $_SESSION['Where']);
                ?>
                <div id="admin_nedvigim_1" class="block_content_1"><b><span style="color: blue;">Объявления Недвижимости</span></b><br><br>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img class="img_options" onclick="CheckedAdsVal(<?php echo count($immovable->id); ?>, 1);" src="../images/delete_team.png" title="Удалить выделенные объявления" alt="">
                                <img class="img_options" onclick="CheckedAdsVal(<?php echo count($immovable->id); ?>, 2);" src="../images/aktive_team.png" title="Активировать выделенные объявления" alt="">
                                <img class="img_options" onclick="CheckedAdsVal(<?php echo count($immovable->id); ?>, 3);" src="../images/deactivate_team.png" title="Скрыть выделенные объявления" alt="">
                                <img class="img_options" onclick="CheckedAllImmo(<?php echo count($immovable->id); ?>);" src="../images/check_all.png" id="CheckButton" title="Выделить все Объявления" alt="">
                            </td>
                            <td style="text-align: right;">
                                <img id="lupa_plus" class="img_options_1" src="../images/lupa_plus.png" title="Развернуть поиск" alt="" onclick="SearchOpen();">
                                <img  id="lupa_minus" class="img_options_1" style="display: none;" src="../images/lupa_minus.png" title="Свернуть поиск" alt="" onclick="SearchClose();">
                            </td>
                        </tr>
                    </table>
                    <div id="parametr_search" style="display: none;">
                        <form action="./" method="GET">
                            <table>
                                <tr>
                                    <td>
                                        <p class="style_2">По <span style="color: green;">ID</span>:</p>
                                    </td>
                                    <td>
                                        <input name="ImmoSearchID" type="text" value="">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p class="style_2">По <span style="color: green;">нику пользователя</span>:</p>
                                    </td>
                                    <td>
                                        <input name="ImmoSearchNick" type="text" value="">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p class="style_2">По <span style="color: green;">рубрике</span>:</p>
                                    </td>
                                    <td>
                                        <?php
                                        $query = 'SELECT * FROM k_immovables_subcategories as kis LEFT JOIN k_immovables_categories as kic ON (kis.k_is_parent = kic.k_ic_id) WHERE k_is_parent=1 OR k_is_parent=2';
                                        $result = mysql_query($query);
                                        echo '<select name="ImmoSearchSubCategory">';
                                        echo '<option value="0">Не указано</option>';
                                        while ($row = mysql_fetch_array($result)) {
                                            echo '<option value="' . $row['k_is_id'] . '">' . $row['k_ic_name'] . ' ' . $row['k_is_name'] . '</option>';
                                        }
                                        echo '</select>';
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p class="style_2">По <span style="color: green;">статусу</span>:</p>
                                    </td>
                                    <td>
                                        <select name="ImmoSearchState">
                                            <option value="no">Не указан</option>
                                            <option value="1">Активные</option>
                                            <option value="0">Скрытые</option>
                                            <option value="2">Ожидают подтверждения</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <label><input name="ImmoSearchUserType1" type="checkbox" checked="checked" value="1">Объявления Агентств</label>
                                        <label><input name="ImmoSearchUserType2" type="checkbox" checked="checked" value="4">Объявления Пользователей</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p class="style_2">По <span style="color: green;">адресу</span>:</p>
                                    </td>
                                    <td>
                                        <input name="ImmoSearchAddress" type="text" value="">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="submit" name="ImmoSearchSubmit" style="width: 100%;" value="Найти">
                                    </td>
                                    <td>
                                        <input type="submit" name="ImmoSearchReset" style="width: 100%; color: red;" value="Отменить поиск">
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    <?php
                    $immovable->GenerateNavigation($page);
                    ?>
                    <table style="width: 100%; text-align: center;">
                        <tr style="background: #7caed3;">
                            <td colspan="2"><p class="style_5">№</p></td>
                            <td><p class="style_5">Фото</p></td>
                            <td><p class="style_5">Рубрика</p></td>
                            <td><p class="style_5">Пользователь</p></td>
                            <td><p class="style_5">Дата</p></td>
                            <td><p class="style_5">Статус</p></td>
                            <td><p class="style_5">Время</p></td>
                            <td><p class="style_5">Действие</p></td>
                        </tr>
                        <?php
                        $immovable->BuildTable();
                        ?>
                    </table>
                    <?php
                    $immovable->GenerateNavigation($page);
                    ?>
                </div>

                <div id="info_obiavlenie_block" class="wind">       <!--Всплывающее окно Информации по объявлению-->
                    <a class="close" onclick="CloseWindow('info_obiavlenie_block');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Информация по объявлению</p>
                    <table>
                        <tr>
                            <td><p class="style_2">Номер объявления:</p></td>
                            <td><p class="style_4_2" id="ImmoAdNum"></p></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Рубрика:</p></td>
                            <td><p class="style_4_4" id="ImmoAdIT"></p></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Ник пользователя:</p></td>
                            <td><p class="style_4_4" id="ImmoAdUser"></p></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Дата размещения:</p></td>
                            <td><p class="style_4_4" id="ImmoAdDate"></p></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Статус:</p></td>
                            <td><p class="style_4_4" id="ImmoAdState"></p></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Время действия объявления:</p></td>
                            <td><p class="style_4_4" id="ImmoAdDays"></p></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Контактное лицо:</p></td>
                            <td><p class="style_4_1" id="ImmoAdContact"></p></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Телефон:</p></td>
                            <td><p class="style_4_4" id="ImmoAdPhone"></p></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Адрес:</p></td>
                            <td><p class="style_4_4" id="ImmoAdAddress"></p></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">E-mail:</p></td>
                            <td><p class="style_4_4" id="ImmoAdEmail"></p></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Тип:</p></td>
                            <td><p class="style_4_1" id="ImmoAdUT"></p></td>
                        </tr>
                    </table>
                </div>

                <div id="photo_obiavlenie" class="wind">       <!--Всплывающее окно редактировать фото объявления-->
                </div>

                <div id="photo_windows" class="wind">       <!--Всплывающее окно увеличение фото-->
                    <a class="close" onclick="$('#photo_windows').hide(500);">X</a>
                    <br>
                    <img class="img_windows" id="ImmoImageShow" src="" alt="">
                </div>

                <div id="edit_obiavlenie" class="wind">
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
                                <input type="hidden" id="ImmoForAddDays" value="">
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
                <?php
            }
            ?>
            <?php
            if ($PageType == 2) {
                $banner = new BannersAll();
                ?>
                <div id="admin_nedvigim_2" class="block_content_1"><b><span style="color: blue;">Банеры страницы Недвижимость</span></b><br><br>
                    <div class="baners">
                        <table border="1">
                            <tr>
                                <td colspan="4"><p class="style_1">Банеры страницы Недвижимость</p></td>
                            </tr>
                            <tr style="background: #7caed3;">
                                <td><p class="style_5">Страница</p></td>
                                <td colspan="3"><p class="style_5">Главный банер</p></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Недвижимость</p></td>
                                <td colspan="3">
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
                            <tr style="background: #7caed3;">
                                <td><p class="style_5">Раздел</p></td>
                                <td><p class="style_5">Левый банер</p></td>
                                <td><p class="style_5">Центральный банер</p></td>
                                <td><p class="style_5">Правый банер</p></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Последние объявления</p></td>
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
                                <td><p class="style_2">ПРОДАЮ</p></td>
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
                            <tr>
                                <td><p class="style_2">Продаю: Квартиры</p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[7] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[7] . ');" title="Оставшееся время: ' . $banner->banner_end_days[7] . ' дней">';
                                    if ($banner->banner_end_days[7] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[7] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[7] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[8] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[8] . ');" title="Оставшееся время: ' . $banner->banner_end_days[8] . ' дней">';
                                    if ($banner->banner_end_days[8] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[8] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[8] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[9] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[9] . ');" title="Оставшееся время: ' . $banner->banner_end_days[9] . ' дней">';
                                    if ($banner->banner_end_days[9] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[9] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[9] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Продаю: Дома/дачи</p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[10] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[10] . ');" title="Оставшееся время: ' . $banner->banner_end_days[10] . ' дней">';
                                    if ($banner->banner_end_days[10] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[10] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[10] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[11] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[11] . ');" title="Оставшееся время: ' . $banner->banner_end_days[11] . ' дней">';
                                    if ($banner->banner_end_days[11] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[11] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[11] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[12] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[12] . ');" title="Оставшееся время: ' . $banner->banner_end_days[12] . ' дней">';
                                    if ($banner->banner_end_days[12] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[12] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[12] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Продаю: Нежилое</p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[13] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[13] . ');" title="Оставшееся время: ' . $banner->banner_end_days[13] . ' дней">';
                                    if ($banner->banner_end_days[13] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[13] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[13] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[14] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[14] . ');" title="Оставшееся время: ' . $banner->banner_end_days[14] . ' дней">';
                                    if ($banner->banner_end_days[14] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[14] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[14] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[15] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[15] . ');" title="Оставшееся время: ' . $banner->banner_end_days[15] . ' дней">';
                                    if ($banner->banner_end_days[15] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[15] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[15] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Продаю: Гараж/погреб</p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[16] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[16] . ');" title="Оставшееся время: ' . $banner->banner_end_days[16] . ' дней">';
                                    if ($banner->banner_end_days[16] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[16] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[16] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[17] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[17] . ');" title="Оставшееся время: ' . $banner->banner_end_days[17] . ' дней">';
                                    if ($banner->banner_end_days[17] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[17] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[17] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[18] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[18] . ');" title="Оставшееся время: ' . $banner->banner_end_days[18] . ' дней">';
                                    if ($banner->banner_end_days[18] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[18] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[18] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Продаю: Земля</p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[19] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[19] . ');" title="Оставшееся время: ' . $banner->banner_end_days[19] . ' дней">';
                                    if ($banner->banner_end_days[19] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[19] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[19] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[20] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[20] . ');" title="Оставшееся время: ' . $banner->banner_end_days[20] . ' дней">';
                                    if ($banner->banner_end_days[20] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[20] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[20] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[21] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[21] . ');" title="Оставшееся время: ' . $banner->banner_end_days[21] . ' дней">';
                                    if ($banner->banner_end_days[21] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[21] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[21] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><p class="style_2">СДАЮ</p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[22] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[22] . ');" title="Оставшееся время: ' . $banner->banner_end_days[22] . ' дней">';
                                    if ($banner->banner_end_days[22] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[22] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[22] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[23] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[23] . ');" title="Оставшееся время: ' . $banner->banner_end_days[23] . ' дней">';
                                    if ($banner->banner_end_days[23] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[23] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[23] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[24] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[24] . ');" title="Оставшееся время: ' . $banner->banner_end_days[24] . ' дней">';
                                    if ($banner->banner_end_days[24] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[24] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[24] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Сдаю: Квартиры</p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[25] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[25] . ');" title="Оставшееся время: ' . $banner->banner_end_days[25] . ' дней">';
                                    if ($banner->banner_end_days[25] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[25] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[25] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[26] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[26] . ');" title="Оставшееся время: ' . $banner->banner_end_days[26] . ' дней">';
                                    if ($banner->banner_end_days[26] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[26] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[26] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[27] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[27] . ');" title="Оставшееся время: ' . $banner->banner_end_days[27] . ' дней">';
                                    if ($banner->banner_end_days[27] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[27] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[27] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Сдаю: Дома/дачи</p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[28] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[28] . ');" title="Оставшееся время: ' . $banner->banner_end_days[28] . ' дней">';
                                    if ($banner->banner_end_days[28] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[28] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[28] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[29] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[29] . ');" title="Оставшееся время: ' . $banner->banner_end_days[29] . ' дней">';
                                    if ($banner->banner_end_days[29] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[29] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[29] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[30] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[30] . ');" title="Оставшееся время: ' . $banner->banner_end_days[30] . ' дней">';
                                    if ($banner->banner_end_days[30] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[30] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[30] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Сдаю: Нежилое</p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[31] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[31] . ');" title="Оставшееся время: ' . $banner->banner_end_days[31] . ' дней">';
                                    if ($banner->banner_end_days[31] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[31] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[31] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[32] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[32] . ');" title="Оставшееся время: ' . $banner->banner_end_days[32] . ' дней">';
                                    if ($banner->banner_end_days[32] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[32] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[32] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[33] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[33] . ');" title="Оставшееся время: ' . $banner->banner_end_days[33] . ' дней">';
                                    if ($banner->banner_end_days[33] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[33] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[33] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Сдаю: Гараж/погреб</p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[34] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[34] . ');" title="Оставшееся время: ' . $banner->banner_end_days[34] . ' дней">';
                                    if ($banner->banner_end_days[34] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[34] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[34] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[35] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[35] . ');" title="Оставшееся время: ' . $banner->banner_end_days[35] . ' дней">';
                                    if ($banner->banner_end_days[35] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[35] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[35] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[36] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[36] . ');" title="Оставшееся время: ' . $banner->banner_end_days[36] . ' дней">';
                                    if ($banner->banner_end_days[36] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[36] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[36] . ');"'; ?>>
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
            if ($PageType == 3) {
                if (!isset($_SESSION['WhereAg'])) {
                    $_SESSION['WhereAg'] = '';
                }
                $agents = new Agents();
                $agents->LoadAgents(50, $page, $_SESSION['WhereAg']);
                ?>
                <div id="admin_nedvigim_3" class="block_content_1"><b><span style="color: blue;">Агенства недвижимости</span></b><br><br>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img class="img_options" src="../images/add_team.png" title="Добавить Агентство" alt="" onclick="AddAgentWindow();">
                                <img class="img_options" src="../images/delete_team.png" onclick="CheckedAgentVal(<?php echo count($agents->agent_id); ?>, 1);" title="Удалить выделенные Агентства" alt="">
                                <img class="img_options" src="../images/aktive_team.png" onclick="CheckedAgentVal(<?php echo count($agents->agent_id); ?>, 2);" title="Активировать выделенные Агентства" alt="">
                                <img class="img_options" src="../images/deactivate_team.png" onclick="CheckedAgentVal(<?php echo count($agents->agent_id); ?>, 3);" title="Скрыть выделенные Агентства" alt="">
                                <img class="img_options" src="../images/check_all.png" onclick="CheckedAllAgents(<?php echo count($agents->agent_id); ?>);" id="CheckButton" title="Выделить все Агентства" alt="">
                            </td>
                            <td style="text-align: right;">
                                <img id="lupa_plus" class="img_options_1" src="../images/lupa_plus.png" title="Развернуть поиск" alt="" onclick="SearchOpen();">
                                <img  id="lupa_minus" class="img_options_1" style="display: none;" src="../images/lupa_minus.png" title="Свернуть поиск" alt="" onclick="SearchClose();">
                            </td>
                        </tr>
                    </table>
                    <div id="parametr_search" style="display: none;">
                        <form action="./" method="GET">
                            <table>
                                <tr>
                                    <td><p class="style_2">По <span style="color: green;">№ Агентства</span>:</p></td>
                                    <td><input type="text" name="AgentSearchID" value=""></td>
                                </tr>
                                <tr>
                                    <td><p class="style_2">По <span style="color: green;">названию Агентства</span>:</p></td>
                                    <td><input type="text" name="AgentSearchName" value=""></td>
                                </tr>
                                <tr>
                                    <td>
                                        <p class="style_2">По <span style="color: green;">адресу Агентства (улица)</span>:</p>
                                    </td>
                                    <td>
                                        <input type="text" name="AgentSearchAddress" value="">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p class="style_2">По <span style="color: green;">статусу</span>:</p>
                                    </td>
                                    <td>
                                        <select name="AgentSearchState">
                                            <option value="no">Не указан</option>
                                            <option value="1">Активные</option>
                                            <option value="0">Скрытые</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="submit" name="AgentSearchSubmit" style="width: 100%;" value="Найти">
                                    </td>
                                    <td>
                                        <input type="submit" name="AgentSearchReset" style="width: 100%; color: red;" value="Сбросить поиск">
                                    </td>    
                                </tr>
                            </table>
                        </form>
                    </div>
                    <?php
                    $agents->GenerateNavigation($page);
                    ?>
                    <table style="width: 100%; text-align: center;">
                        <tr style="background: #7caed3;">
                            <td colspan="2"><p class="style_5">№ Агентства</p></td>
                            <td><p class="style_5">Аватар</p></td>
                            <td><p class="style_5">Название</p></td>
                            <td><p class="style_5">Пользователь</p></td>
                            <td><p class="style_5">Дата</p></td>
                            <td><p class="style_5">Статус</p></td>
                            <td><p class="style_5">Время</p></td>
                            <td><p class="style_5">Действие</p></td>
                        </tr>
                        <?php
                        $agents->GenerateTable();
                        ?>
                    </table>
                    <?php
                    $agents->GenerateNavigation($page);
                    ?>
                </div>



                <div id="info_agentstvo" class="wind">       <!--Всплывающее окно Информации по Агентству-->
                    <a class="close" onclick="CloseWindow('info_agentstvo');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Информация по Агентству</p>
                    <table id="InfoAgentsTable">
                    </table>
                </div>

                <div id="edit_agentstvo" class="wind_1">       <!--Всплывающее окно Редактировать Агентство-->
                    <a class="close" onclick="document.getElementById('edit_agentstvo').style.display = 'none';
                                disableA();">X</a>
                    <br>
                    <br>
                    <p class="style_7">Редактируем Агентство</p>
                    <table id="AgentEditTable">
                    </table>
                </div>

                <div id="avatar_agentstvo" class="wind">       <!--Всплывающее окно редактировать Аватарку Агентства-->
                    <a class="close" onclick="CloseWindow('avatar_agentstvo');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Аватарка Агентства</p>
                    <button id="AgentUpload" style="width: 100%;">Загрузить</button>
                    <span id="status2"></span>
                    <input type="hidden" id="AgentHiddenID" value="1">
                    <table id="AgentAvatarTable">
                    </table>
                </div>

                <div id="pass_agentstvo" class="wind">       <!--Всплывающее окно изменения пароля для Агентство-->
                    <a class="close" onclick="document.getElementById('pass_agentstvo').style.display = 'none';
                                disableA();">X</a>
                    <br>
                    <br>
                    <p class="style_7">Изменение пароля Агентства</p>
                    <table>
                        <tr>
                            <td><p class="style_2">Пароль:</p></td>
                            <td><input type="password" id="AgentPassLine1" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Повторите пароль:</p></td>
                            <td><input type="password" id="AgentPassLine2" onkeyup="LinesCompare();" value=""></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button style="float:left;" onclick="ChangeAgentPassword();">Изменить</button>
                                <input type="hidden" id="AgentPasswordID" value="1">
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="add_agentstvo" class="wind_1">       <!--Всплывающее окно Добавить Агентство-->
                    <a class="close" onclick="CloseWindow('add_agentstvo');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Добавление пользователя как Агентство</p>
                    <table style="width: 500px;">
                        <tr>
                            <td><p class="style_2">Ник пользователя:</p></td>
                            <td><input type="text" id="AddAgentLogin" value="" style="width: 100%;"></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Пароль:</p></td>
                            <td><input type="password" id="AddAgentPassword1" value="" style="width: 100%;"></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Повторите пароль:</p></td>
                            <td><input type="password" id="AddAgentPassword2" onkeyup="PasswordCompareAgent();" value="" style="width: 100%;"></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Контактное лицо:</p></td>
                            <td>
                                <input type="text" value="" placeholder="Имя" id="AddAgentFName" style="width: 30%;">
                                <input type="text" value="" placeholder="Фамилия" id="AddAgentLName" style="width: 30%;">
                                <input type="text" value="" placeholder="Отчество" id="AddAgentOName" style="width: 30%;">
                            </td>
                        </tr>
                        <tr>
                            <td><p class="style_2">E-mail:</p></td>
                            <td><input type="text" id="AddAgentEmail" value="" style="width: 100%;"></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Введите адрес:</p></td>
                            <td><input type="text" id="AgentEditAddress" onkeyup="SearchAddress();" value="" style="width: 100%;"></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Выберите из списка:</p></td>
                            <td id="AgentAddressResult">
                                <select id="ImmoAddressChosen" style="width: 100%;" name="ImmoAddressChosen"><option selected value="0"></option></select>
                            </td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Название Агентства:</p></td>
                            <td><input type="text" value="" id="AddAgentName" style="width: 100%;"></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Телефон:</p></td>
                            <td><input type="text" id="AddAgentPhone" value="" style="width: 100%;"></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Сайт Агентства:</p></td>
                            <td><input type="text" value="" id="AddAgentSite" style="width: 100%;"></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Время действия Агентства (дни):</p></td>
                            <td><input type="text" value="" id="AddAgentDays" style="width: 100%;"></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <p class="style_2">Описание Агентства:</p>
                                <textarea rows="12" cols="55" id="AddAgentDescription" style="width: 100%;"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button style="float:left; width: 100%;" onclick="AddAgentIntoTable();">Добавить</button>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
            }
            ?>
            <?php
            if ($PageType == 4) {
                if (!isset($_SESSION['WhereNews'])) {
                    $_SESSION['WhereNews'] = '';
                }
                $news = new News();
                $news->LoadNews(50, $page, $_SESSION['WhereNews']);
                ?>
                <script type="text/javascript" src="../js/ajaxupload.3.5.js"></script>
                <script type="text/javascript" >
                                    $(function() {
                                        var btnUpload = $('#NewsUpload');
                                        var status = $('#statusNews');
                                        new AjaxUpload(btnUpload, {
                                            action: 'upload-file2.php',
                                            name: 'uploadfile',
                                            onSubmit: function(file, ext) {
                                                if (!(ext && /^(jpg|png|jpeg|gif)$/.test(ext))) {
                                                    // extension is not allowed 
                                                    status.text('Only JPG, PNG or GIF files are allowed');
                                                    return false;
                                                }
                                                status.text('Uploading...');
                                            },
                                            onComplete: function(file, response) {
                                                status.text('');
                                                if (response === "error") {
                                                    $('<li></li>').appendTo('#filesNews').text(file);
                                                } else {
                                                    $('#NewsUploadImage').attr('src', response);
                                                    NewsAvatarChange(response);
                                                }
                                            }
                                        });

                                    });
                </script>
                <div id="admin_nedvigim_4" class="block_content_1"><b><span style="color: blue;">Новости недвижимости</span></b><br><br>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img class="img_options" src="../images/add_team.png" title="Добавить Новость" alt="" onclick="NewNewsAddShow();">
                                <img class="img_options" src="../images/delete_team.png" title="Удалить выделенные Новости" onclick="DeleteSelectedNews(<?php echo count($news->news_id); ?>);" alt="">
                                <img class="img_options" src="../images/check_all.png" id="CheckButton" title="Выделить все Новости" alt="" onclick="CheckedAllNews(<?php echo count($news->news_id); ?>);">
                            </td>
                            <td style="text-align: right;">
                                <!--ПОИСК-->
                                <img id="lupa_plus" class="img_options_1" src="../images/lupa_plus.png" title="Развернуть поиск" alt="" onclick="SearchOpen();">
                                <img  id="lupa_minus" class="img_options_1" style="display: none;" src="../images/lupa_minus.png" title="Свернуть поиск" alt="" onclick="SearchClose();">
                                <img class="img_options_1" src="../images/new.png" title="Добавить новую рубрику Новостей" alt="" onclick="SubcategoryAddShow();">
                                <img class="img_options_1" src="../images/new_down.png" title="Удалить рубрику Новостей" alt="" onclick="DeleteSubcategoryShow();">
                                <img class="img_options_1" src="../images/edit_new.png" title="Изменить название рубрики Новостей" alt="" onclick="ChangeSubcategory();">
                            </td>
                        </tr>
                    </table>
                    <div id="parametr_search" style="display: none;">
                        <form action="./" method="get">
                            <table>
                                <tr>
                                    <td><p class="style_2">По <span style="color: green;">№ Новости</span>:</p></td>
                                    <td><input type="text" name="SearchNewsID" value=""></td>
                                </tr>
                                <tr>
                                    <td><p class="style_2">По <span style="color: green;">заголовку Новости</span>:</p></td>
                                    <td><input type="text" name="SearchNewsHeader" value=""></td>
                                </tr>
                                <tr>
                                    <td><p class="style_2">По <span style="color: green;">рубрике Новостей</span>:</p></td>
                                    <td><select name="SelectSearchNews">
                                            <option value="no">Не указано</option>
                                            <?php
                                            $query = 'SELECT * FROM k_immovables_subcategories WHERE k_is_parent=4 ORDER BY k_is_name ASC';
                                            $result = mysql_query($query);
                                            while ($row = mysql_fetch_array($result)) {
                                                echo '<option value="' . $row['k_is_id'] . '">' . $row['k_is_name'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="submit" name="SearchNewsSubmit" style="float:left;" value="Найти">
                                        <input type="submit" name="SearchNewsCancel" style="float:left;" value="Сбросить поиск">
                                    <td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    <?php
                    $news->GenerateNavigation($page);
                    ?>
                    <table style="width: 100%; text-align: center;">
                        <tr style="background: #7caed3;">
                            <td colspan="2"><p class="style_5">№ Новости</p></td>
                            <td><p class="style_5">Аватар</p></td>
                            <td><p class="style_5">Заголовок</p></td>
                            <td><p class="style_5">Рубрика</p></td>
                            <td><p class="style_5">Дата</p></td>
                            <td><p class="style_5">Действие</p></td>
                        </tr>
                        <?php
                        $news->GenerateTable();
                        ?>
                    </table>
                    <?php
                    $news->GenerateNavigation($page);
                    ?>
                </div>

                <div id="info_news" class="wind">       <!--Всплывающее окно Информации о Новости-->
                    <a class="close" onclick="CloseWindow('info_news');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Информация по Новости</p>
                    <table style="width: 600px;" border="1" id="NewsTable">
                    </table>
                </div>

                <div id="edit_news" class="wind">       <!--Всплывающее окно Информации о Новости-->
                    <a class="close" onclick="CloseWindow('edit_news');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Редактирование Новости</p>
                    <table id="NewsEditTable">
                    </table>
                </div>

                <div id="avatar_news" class="wind">       <!--Всплывающее окно редактировать Аватарку Новости-->
                    <a class="close" onclick="CloseWindow('avatar_news');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Аватарка Новости</p>
                    <button id="NewsUpload" style="width: 100%;">Загрузить</button>
                    <span id="statusNews" ></span>
                    <ul id="filesNews" ></ul>
                    <table id="AvatarEditTable">
                    </table> 
                </div>


                <div id="edit_rubrik" class="wind">       <!--Всплывающее окно Редактировать название рубрику новостей-->
                    <a class="close" onclick="CloseWindow('edit_rubrik');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Редактирование названия рубрики Новостей</p>
                    <table id="SubcategoryEditTable">
                    </table>
                </div> 

                <div id="down_rubrik" class="wind">       <!--Всплывающее окно удалить рубрику новостей-->
                    <a class="close" onclick="CloseWindow('down_rubrik');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Удаление рубрики Новостей</p>
                    <table id="DeleteSubcategoryTable">
                    </table>
                </div>

                <div id="new_rubrik" class="wind">       <!--Всплывающее окно добавить новую рубрику новостей-->
                    <a class="close" onclick="CloseWindow('new_rubrik');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Добавление новой рубрики Новостей</p>
                    <table>
                        <tr>
                            <td><p class="style_2">Введите название рубрики:</p></td>
                        </tr>
                        <tr>
                            <td><input style="width: 300px;" type="text" id="SubcategoryNewStr" value=""></td>
                        </tr>
                        <tr>
                            <td><button style="float:left; width: 100%;" onclick="AddNewSubcategorySubmit();">Добавить рубрику</button></td>
                        </tr>
                    </table>
                </div>

                <div id="add_new" class="wind_1">       <!--Всплывающее окно добавить новость-->
                    <a class="close" onclick="CloseWindow('add_new');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Добавление Новости</p>
                    <table id="NewsAddTable">
                    </table>
                </div>
                <?php
            }
            ?>
            <?php
            if ($PageType == 5) {
                if (!isset($_SESSION['WhereBuy'])) {
                    $_SESSION['WhereBuy'] = '';
                }
                $buy = new Buys();
                $buy->LoadBuys(50, $page, $_SESSION['WhereBuy']);
                ?>
                <div id="admin_nedvigim_5" class="block_content_1"><b><font color="blue">Объявления раздела Куплю</font></b><br><br>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img class="img_options" src="../images/delete_team.png" onclick="DeleteSelectedBuys(<?php echo count($buy->buy_id); ?>);" title="Удалить выделенные объявления" alt="">
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
                                    <td><p class="style_2">По <font color="green">№ объявления</font>:</p></td>
                                    <td><input type="text" name="SearchBuysID" value=""></td>
                                </tr>
                                <tr>
                                    <td><input type="submit" name="SearchBuysSubmit" style="float:left; width: 100%;" value="Найти"></td>
                                    <td><input type="submit" name="SearchBuysReset" style="float:left; color: red; width: 100%;" value="Очистить поиск"></td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    <?php
                    $buy->GenerateNavigation($page);
                    ?>
                    <table style="width: 100%; text-align: center;">
                        <tr style="background: #7caed3;">
                            <td colspan="2"><p class="style_5">№ Новости</p></td>
                            <td><p class="style_5">Пользователь</p></td>
                            <td><p class="style_5">Текст</p></td>
                            <td><p class="style_5">Дата</p></td>
                            <td><p class="style_5">Действие</p></td>
                        </tr>
                        <?php
                        $buy->GenerateTable();
                        ?>
                    </table>
                    <?php
                    $buy->GenerateNavigation($page);
                    ?>
                </div>

                <div id="edit_kupliu" class="wind">       <!--Всплывающее окно редактировать объявление раздела куплю-->
                    <a class="close" onclick="CloseWindow('edit_kupliu');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Редактировать объявление раздела Куплю</p>
                    <table id="EditBuysTable">
                    </table>
                </div>
                <?php
            }
            ?>
            <?php
            if ($PageType == 6) {
                $videos = new VideoRealty(50, $page, '');
                ?>
                <div id="admin_nedvigim_5" class="block_content_1"><b><font color="blue">Видео</font></b><br><br>
                    <?php
                    $videos->GenerateNavigation($page, 50);
                    ?>
                    <table style="width: 100%; text-align: center;">
                        <tr style="background: #7caed3;">
                            <td><p class="style_5">ID</p></td>
                            <td><p class="style_5">URL</p></td>
                            <td><p class="style_5">Статус</p></td>
                            <td><p class="style_5">ID объявления</p></td>
                            <td><p class="style_5">Действие</p></td>
                        </tr>
                        <?php
                        for ($i = 0; $i < count($videos->id); $i++) {
                            ?>
                            <tr style="background: #f5dac7;">
                                <td style="width: 80px;"><p class="style_4"><?php echo $videos->id[$i]; ?></p></td>
                                <td><p class="style_4"><?php echo $videos->url[$i]; ?></p></td>
                                <td>
                                    <?php
                                    if ($videos->state[$i]) {
                                        echo '<p class="style_4_1">Активно</p>';
                                    } else {
                                        echo '<p class="style_4_2">Скрыто</p>';
                                    }
                                    ?>
                                </td>
                                <td><p class="style_4"><?php echo $videos->immo[$i]; ?></p></td>
                                <td>
                                    <a class="a_1"><img src="../images/delete.png" title="Удалить видео" alt=""></a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <?php
                    $videos->GenerateNavigation($page, 50);
                    ?>
                </div>
                <?php
            }
            ?>
        </div>

        <div id="send_email" class="wind">       <!--Всплывающее окно отправки почты-->
            <a class="close" onclick="CloseWindow('send_email');">X</a>
            <br>
            <br>
            <p class="style_7">Отправить Письмо</p>
            <table>
                <tr>
                    <td><p class="style_2">Тема:</p></td>
                    <td><input type="text" id="ImmoEmailTheme" name="ImmoEmailTheme" value=""></td>
                </tr>
                <tr>
                    <td><p class="style_2">Email:</p></td>
                    <td><p class="style_2" id="EmailToShow"></p></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p class="style_2">Текст:</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <textarea rows="10" cols="50" id="ImmoEmailText" name="ImmoEmailText"></textarea>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="hidden" name="ImmoEmailEmail" id="ImmoEmailEmail" value="">
                        <button style="float:right;" onClick="">Отправить</button>
                    </td>
                </tr>
            </table>
        </div>

        <div class="temno" id="temno"></div>   <!-- Всплывающие окна конец -->
    </body>
</html>