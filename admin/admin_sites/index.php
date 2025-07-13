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
        require_once '../../inc/functions.php';
        require_once './inc/classes.php';
        //require_once 'inc/admin_functions.php';
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
        //Листаем страницы
        if (isset($_GET['PageIndex'])) {
            $page = filter_var($_GET['PageIndex'], FILTER_VALIDATE_INT);
        } else {
            $page = 1;
        }

        if (!in_array($ShowParamID, array(1, 2, 3, 4))) {
            header($_ENV['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
            header("Location: http://" . _SERVER_ADDRESS . "/page404.php");
        }

        $id_sc = 0;
        if (isset($_GET['SearchS'])) {
            $ShowParamID = 4;
            $id_sc = filter_var($_GET['CategoryS'], FILTER_VALIDATE_INT);
        }
        ?>
        <meta name="keywords" content="">
        <meta name="description" content="">
        <link rel="stylesheet" type="text/css" href="../css/style_admin.css">
        <link rel="stylesheet" type="text/css" href="../css/style_wind.css">
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script type="text/javascript" src="../js/wind.js"></script>
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
        <?php
        if ($ShowParamID == 1) {
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
                            status.html('<img src="../images/animate.gif" alt="">');
                        },
                        onComplete: function(file, response) {
                            status.text('');
                            if (response === "error") {
                                status.text('\u0412озникла ошибка!');
                            } else {
                                $('#photo_sites img.img_ob').attr('src', response);
                                ChangeAvatarSite(response);
                            }
                        }
                    });
                });
            </script>
            <?php
        }
        ?>
        <script type="text/javascript" src="js/scripts.js"></script>
    </head>
    <body>
        <div class="top_block">
            <a class="menu" href="../admin_gl/">Главная</a> <!--visible Клас для скрытия кнопок-->
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
        <p class="topic">Управление разделом: <font color="#ff9c00">Сайты</font></p>
        <div class="center_block">
            <div id="menu_2">
                <a href="./?PageType=1">Сайты</a>
                <a href="./?PageType=2">Банеры</a>
                <a href="./?PageType=3">Рубрики</a>
                <a href="./?PageType=4">Подрубрики</a>
            </div>

            <?php
            if ($ShowParamID == 1) {
                $where = '';
                $link = '';
                $sites = new Sites($page, $where, 50);
                $sc = new SitesCategories();
                $ssc = new SitesSubcategories($sc->id[0], '');
                ?>
                <div class="block_content_1"><b><font color="blue">Управление сайтами</font></b><br><br>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img class="img_options" src="../images/add_team.png" title="Добавить сайт" alt="" onclick="ShowNewSiteWindow();">
                                <img class="img_options" src="../images/delete_team.png" title="Удалить выделенный сайт" alt="">
                                <img class="img_options" src="../images/aktive_team.png" title="Разместить сайт" alt="">
                                <img class="img_options" src="../images/deactivate_team.png" title="Скрыть сайт" alt="">
                            </td>
                            <td style="text-align: right;">
                                <img id="lupa_plus" class="img_options_1" src="../images/lupa_plus.png" title="Развернуть поиск" alt=""
                                     onclick="document.getElementById('lupa_plus').style.display = 'none';
                    document.getElementById('lupa_minus').style.display = 'block';
                    document.getElementById('parametr_search').style.display = 'block';">
                                <img  id="lupa_minus" class="img_options_1" style="display: none;" src="../images/lupa_minus.png" title="Свернуть поиск" alt=""
                                      onclick="document.getElementById('lupa_plus').style.display = 'block';
                    document.getElementById('lupa_minus').style.display = 'none';
                    document.getElementById('parametr_search').style.display = 'none';">
                            </td>
                        </tr>
                    </table>
                    <div id="parametr_search" style="display: none;">
                        <form>
                            <table>
                                <tr>
                                    <td><p class="style_2">По <font color="green">№ сайта</font>:</p></td>
                                    <td><input type="text" value=""></td>
                                </tr>
                                <tr>
                                    <td><p class="style_2">По <font color="green">адресу сайта</font>:</p></td>
                                    <td><input type="text" value=""></td>
                                </tr>
                                <tr>
                                    <td><p class="style_2">По <font color="green">Статусу</font>:</p></td>
                                    <td><select>
                                            <option>Выберите статус</option>
                                            <option>Размещенные</option>
                                            <option>Скрытые</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><button style="float:left;">Найти</button><td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    <?php
                    $sites->GenerateNavigation($page, $where, $link, 50);
                    ?>
                    <table style="width: 100%; text-align: center;" id="SiteTable">
                        <?php
                        $sites->Refresh();
                        ?>
                    </table>
                    <?php
                    $sites->GenerateNavigation($page, $where, $link, 50);
                    ?>
                </div>

                <div id="send_email" class="wind">       <!--Всплывающее окно отправки почты-->
                    <a class="close" onclick="CloseWindow('send_email');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Отправить Письмо</p>
                    <table>
                        <tr>
                            <td>
                                <p class="style_2">Кому:</p>
                            </td>
                            <td>
                                <p class="style_2" id="email_address"></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p class="style_2">Тема:</p>
                            </td>
                            <td>
                                <input type="text" value="">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <p class="style_2">Текст:</p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <textarea rows="10" cols="50" name="text"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button style="float:right;" onclick="SendEmail();">Отправить</button>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="edit_sites" class="wind_1">       <!--Всплывающее окно редактировать сайт-->
                    <a class="close" onclick="CloseWindow('edit_sites');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Редактирование сайт</p>
                    <table>
                        <tr>
                            <td><p class="style_2">Название сайта:</p></td>
                            <td><input id="site_name" type="text" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Адрес сайта URL:</p></td>
                            <td><input id="site_url" type="text" value=""></td>
                        </tr>
                        <tr>
                            <td colspan="2"><p class="style_2">Краткое описание:</p>
                                <textarea id="site_description" style="float: left;" rows="5" cols="55"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Фото сайта:</p></td>
                            <td>
                                <img id="site_avatar" class="img_ob" src="" alt=""><br>
                            </td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Контактное лицо:</p></td>
                            <td><input id="site_contact_name" type="text" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Телефон для связи:</p></td>
                            <td><input id="site_contact_phone" type="text" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">E-mail:</p></td>
                            <td><input id="site_email" type="text" value=""></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button style="float:left; cursor: pointer;" onclick="SiteChangeSubmit();">Сохранить изменения</button>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="add_sites" class="wind_1">       <!--Всплывающее окно Добавить сайт-->
                    <a class="close" onclick="CloseWindow('add_sites');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Добавление сайта</p>
                    <table>
                        <tr>
                            <td><p class="style_2">Название сайта:</p></td>
                            <td><input id="newsite_name" type="text" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Адрес сайта URL:</p></td>
                            <td><input id="newsite_url" type="text" value=""></td>
                        </tr>
                        <tr>
                            <td colspan="2"><p class="style_2">Краткое описание:</p>
                                <textarea id="newsite_descr" style="float: left;" rows="5" cols="55"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Контактное лицо:</p></td>
                            <td><input id="newsite_cname" type="text" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Телефон для связи:</p></td>
                            <td><input id="newsite_cphone" type="text" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">E-mail:</p></td>
                            <td><input id="newsite_email" type="text" value=""></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button style="float:left; cursor: pointer;" onclick="CreateNewSite();">Добавить сайт</button>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="photo_sites" class="wind">       <!--Всплывающее окно редактировать фото сайта-->
                    <a class="close" onclick="CloseWindow('photo_sites');">X</a>
                    <br>
                    <br>
                    <p class="style_7">
                        Редактировать фото сайта
                    </p>
                    <table>
                        <tr style="background: #f0f4f4;">
                            <td colspan="2">
                                <img class="img_ob" src="images/expert_1.jpg">
                            </td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Изменить Аватар:</p></td>
                            <td>
                                <button id="site_image">Загрузить изображение</button><br>
                                <span id="statusS"></span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><button style="float:left;">Сохранить</button></td>
                        </tr>
                    </table>
                </div>

                <div id="site_categories" class="wind">
                    <a class="close" onclick="CloseWindow('site_categories');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Редактировать категории сайта</p>
                    <table id="t_c">
                    </table>
                    <table>
                        <tr>
                            <td>
                                <select id="cat_add" onchange="ReloadSC();">
                                    <?php
                                    for ($i = 0; $i < count($sc->id); $i++) {
                                        echo '<option value="' . $sc->id[$i] . '">' . $sc->name[$i] . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <select id="sub_cat_add">
                                    <?php
                                    for ($i = 0; $i < count($ssc->id); $i++) {
                                        echo '<option value="' . $ssc->id[$i] . '">' . $ssc->name[$i] . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <a class="a_1" onclick="AddSubcategoryToSite();"><img src="../images/add_tool.png" alt=""></a>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
            }
            ?>

            <?php
            if ($ShowParamID == 2) {
                $banner = new BannersAll();
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Банеры страницы "Сайты"</span></b><br>
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
                $site_categories = new SitesCategories();
                ?>
                <div class="block_content_1"><b><font color="blue">Добавление и удаление рубрики</font></b><br><br>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img class="img_options" src="../images/add_team.png" title="Добавить рубрику" alt="" onclick="ShowNewCategory();">
                            </td>
                        </tr>
                    </table>
                    <table style="width: 100%; text-align: center;" id="CategoriesTable">
                        <tr style="background: #7caed3;">
                            <td><p class="style_5">Наименование рубрики</p></td>
                            <td><p class="style_5">Сайтов в рубрике</p></td>
                            <td><p class="style_5">Действие</p></td>
                        </tr>
                        <?php
                        for ($i = 0; $i < count($site_categories->id); $i++) {
                            ?>
                            <tr style="background: #f0f4f4;">
                                <td><p class="style_4"><?php echo $site_categories->name[$i]; ?></p></td>
                                <td><p class="style_4"><?php echo $site_categories->count[$i]; ?></p></td>
                                <td>
                                    <a class="a_1" onclick="ChangeCategoryName(this);">
                                        <img src="../images/edit.png" title="Редактировать название рубрики" alt="<?php echo $site_categories->id[$i]; ?>">
                                    </a>
                                    <a class="a_1" onclick="DeleteCategory(this);">
                                        <img src="../images/delete.png" title="Удалить рубрику" alt="<?php echo $site_categories->id[$i]; ?>">
                                    </a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
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
                            <td><input type="text" value=""></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button style="float: left;" onclick="AddNewCategory();">Добавить</button>
                            </td>
                        </tr>
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
                            <td><input type="text" value=""></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button style="float: left;" onclick="SaveCategoryName();">Изменить</button>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
            }
            ?>

            <?php
            if ($ShowParamID == 4) {
                $site_subcategories = new SitesSubcategories($id_sc, '');
                $site_categories = new SitesCategories();
                ?>
                <div class="block_content_1"><b><font color="blue">Добавление и удаление подрубрики</font></b><br><br>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img class="img_options" src="../images/add_team.png" title="Добавить подрубрику" alt="" onclick="ShowNewSubcategory();">
                                <form action="./" method="get">
                                    <p class="search_p">Показать в рубрике:
                                        <select name="CategoryS">
                                            <option value="0">Все</option>
                                            <?php
                                            for ($i = 0; $i < count($site_categories->id); $i++) {
                                                echo '<option value="' . $site_categories->id[$i] . '">' . $site_categories->name[$i] . '</option>';
                                            }
                                            ?>
                                        </select>
                                        <input type="submit" name="SearchS" value="Поиск">
                                    </p>
                                </form>
                            </td>
                        </tr>
                    </table>
                    <table style="width: 100%; text-align: center;" id="SubcategoriesTable">
                        <tr style="background: #7caed3;">
                            <td><p class="style_5">Наименование подрубрики</p></td>
                            <td><p class="style_5">Принадлежит рубрике</p></td>
                            <td><p class="style_5">Сайтов в рубрике</p></td>
                            <td><p class="style_5">Действие</p></td>
                        </tr>
                        <?php
                        for ($i = 0; $i < count($site_subcategories->id); $i++) {
                            ?>
                            <tr style="background: #f0f4f4;">
                                <td><p class="style_4"><?php echo $site_subcategories->name[$i]; ?></p></td>
                                <td><p class="style_4"><?php echo $site_subcategories->name_parent[$i]; ?></p></td>
                                <td><p class="style_4"><?php echo $site_subcategories->count[$i]; ?></p></td>
                                <td>
                                    <a class="a_1" onclick="ChangeSubcategoryName(this);">
                                        <img src="../images/edit.png" title="Редактировать название подрубрики" alt="<?php echo $site_subcategories->id[$i]; ?>">
                                    </a>
                                    <a class="a_1" onclick="DeleteSubcategory(this);">
                                        <img src="../images/delete.png" title="Удалить подрубрику" alt="<?php echo $site_subcategories->id[$i]; ?>">
                                    </a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>

                <div id="edit_pod_rub" class="wind">       <!--Всплывающее окно редактировать подрубрику-->
                    <a class="close" onclick="CloseWindow('edit_pod_rub');">X</a>
                    <br>
                    <br>
                    <table>
                        <tr>
                            <td colspan="2"><p class="style_7">Редактировать название подрубрики</p></td>
                        </tr>
                        <tr>
                            <td><p class="style_4">Введите новое название:</p></td>
                            <td><input type="text" value=""></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button style="float: left;" onclick="SaveSubcategoryName();">Изменить</button>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="add_new_pod_rub" class="wind">       <!--Всплывающее окно добавления НОВОЙ ПОДРУБРИКИ-->
                    <a class="close" onclick="CloseWindow('add_new_pod_rub');">X</a>
                    <br>
                    <br>
                    <table>
                        <tr>
                            <td colspan="2"><p class="style_7">Добавить новую подрубрику</p></td>
                        </tr>
                        <tr>
                            <td><p class="style_4">Введите название:</p></td>
                            <td><input type="text" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_4">Выберите рубрику:</p></td>
                            <td>
                                <select>
                                    <?php
                                    for ($i = 0; $i < count($site_categories->id); $i++) {
                                        echo '<option value="' . $site_categories->id[$i] . '">' . $site_categories->name[$i] . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button style="float: left;" onclick="AddNewSubcategory();">Добавить</button>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
            }
            ?>
        </div>

        <div class="temno" id="temno"></div>
    </body>
</html>