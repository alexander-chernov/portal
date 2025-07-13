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
        require_once '../inc/functions.php';
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
            if (!UserAccess(8)) {
                exit('У вас нет прав заходить в эту категорию!');
            }
        } elseif ($_SESSION['privileges'] == 1) {
            $_SESSION['map_access'] = 1;
        }
        UpdateActivityAdmin();

        include 'inc/classes.php';

        //Меняем категории
        if (isset($_GET['PageType'])) {
            $ShowParamID = filter_var($_GET['PageType'], FILTER_VALIDATE_INT);
        } else {
            $ShowParamID = 1;
        }

        CreateTempTables();

        $where = '';
        $link = '';
        $page = 1;
        //double_s_distr
        if (isset($_GET['double_s_str']) || isset($_GET['double_s_num'])) {
            $_GET['double_s_str'] = filter_var($_GET['double_s_str'], FILTER_SANITIZE_STRIPPED);
            $_GET['double_s_num'] = filter_var($_GET['double_s_num'], FILTER_SANITIZE_STRIPPED);
            if (trim($_GET['double_s_str']) != '') {
                $where .= " AND k_s_name LIKE '%" . $_GET['double_s_str'] . "%' ";
                $link .= '&double_s_str=' . $_GET['double_s_str'];
            }
            if (trim($_GET['double_s_num']) != '') {
                $where .= " AND k_shn_house_num LIKE '%" . $_GET['double_s_num'] . "%' ";
                $link .= '&double_s_num=' . $_GET['double_s_num'];
            }
        }
        if (isset($_GET['double_s_distr'])) {
            $_GET['double_s_distr'] = filter_var($_GET['double_s_distr'], FILTER_SANITIZE_STRIPPED);
            if (trim($_GET['double_s_distr']) != '') {
                $where .= " AND k_d_name LIKE '%" . $_GET['double_s_distr'] . "%' ";
                $link .= '&double_s_distr=' . $_GET['double_s_distr'];
            }
        }
        if (isset($_GET['double_s_mass'])) {
            $_GET['double_s_mass'] = filter_var($_GET['double_s_mass'], FILTER_SANITIZE_STRIPPED);
            if (trim($_GET['double_s_mass']) != '') {
                $where .= " AND k_tm_name LIKE '%" . $_GET['double_s_mass'] . "%' ";
                $link .= '&double_s_mass=' . $_GET['double_s_mass'];
            }
        }
        if (isset($_GET['double_s_photo'])) {
            $_GET['double_s_photo'] = filter_var($_GET['double_s_photo'], FILTER_SANITIZE_STRIPPED);
            if (trim($_GET['double_s_photo']) == 1) {
                $where .= " AND k_shp_id IS NOT NULL ";
                $link .= '&double_s_photo=' . $_GET['double_s_photo'];
            }
            if (trim($_GET['double_s_photo']) == 0) {
                $where .= " AND k_shp_id IS NULL ";
                $link .= '&double_s_photo=' . $_GET['double_s_photo'];
            }
        }
        //Листаем страницы
        if (isset($_GET['PageIndex'])) {
            $page = filter_var($_GET['PageIndex'], FILTER_VALIDATE_INT);
        }
        ?>
        <meta name="keywords" content="">
        <meta name="description" content="">
        <link rel="stylesheet" type="text/css" href="../css/style_admin.css">
        <link rel="stylesheet" type="text/css" href="../css/style_wind.css">
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script type="text/javascript" src="../js/wind.js"></script>
        <script type="text/javascript" src="js/scripts.js"></script>
        <?php
        if ($ShowParamID == 3) {
            ?>
            <script type="text/javascript" src="js/ajaxupload.3.5.js"></script>
            <script type="text/javascript">
                $(function() {
                    document.getElementById('PhotoForAddress').disabled = true;
                    $('#PhotoForAddress').css('opacity', '0.5');
                    var btnUpload = $('#PhotoForAddress');
                    var status = $('#status');
                    new AjaxUpload(btnUpload, {
                        action: 'upload-file.php',
                        name: 'PhotoForAddress',
                        onSubmit: function(file, ext) {
                            if (!(ext && /^(jpg|png|jpeg|gif)$/.test(ext))) {
                                // extension is not allowed 
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
                                AddPhotoToAddr(response);
                                LoadPhotosFromAddr($('#addr_ph_id').val());
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
        <p class="topic">Управление разделом: <span style="color: #ff9c00;">Карта</span></p>
        <div class="center_block">
            <div id="menu_2">
                <a href="./?PageType=1">Управление двойными адресами</a>
                <a href="./?PageType=3">Управление адресами</a>
                <a href="./?PageType=4">Управление улицами</a>
                <a href="./?PageType=5">Управление районами</a>
                <a href="./?PageType=6">Управление жилмассивами</a>
                <a href="../../map/?view=editor">Перейти на карту</a>
            </div>
            <div class="block_content_1">
                <?php
                if ($ShowParamID == 1) {
                    $all_addr = new AllAddressesExDouble();
                    $all_d_addr = new AllDoubleAddresses($page, $where);
                    ?>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img class="img_options" src="../images/add_team.png" title="Добавить двойной адрес" alt="" onclick="ShowAddDoubleWindow();">
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
                                    <td><p class="style_2">По <span style="color: green;">улице</span>:</p></td>
                                    <td><input type="text" name="double_s_str" value=""></td>
                                </tr>
                                <tr>
                                    <td><p class="style_2">По <span style="color: green;">номеру дома</span>:</p></td>
                                    <td><input type="text" name="double_s_num" value=""></td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="hidden" value="1" name="PageType">
                                        <input type="submit" style="float:left;" value="Найти">
                                    <td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    <div class="open_content">
                        <b><span style="color: blue;">Управление двойными адресами</span></b><br><br>
                        <?php
                        $all_d_addr->GenerateNavigation($page, $where, $link);
                        ?>
                        <table id="double_table" style="width: 100%; text-align: center;">
                            <tr style="background: #7caed3;">
                                <td><p class="style_5">Адрес</p></td>
                                <td><p class="style_5">Действие</p></td>
                            </tr>
                            <?php
                            for ($i = 0; $i < count($all_d_addr->id); $i++) {
                                echo '<tr style="background: #f0f4f4;" id="do_ad_' . $all_d_addr->id[$i] . '"><td><p class="style_9_1">' . $all_d_addr->address[$i] . '</p></td>';
                                echo '<td><img onclick="DeleteDoubleAddress(this);" class="img_options" src="../images/delete_team.png" title="Удалить двойной адрес" alt="' . $all_d_addr->id[$i] . '"></td></tr>';
                            }
                            ?>
                        </table>
                        <?php
                        $all_d_addr->GenerateNavigation($page, $where, $link);
                        ?>
                    </div>
                    <div id="add_double_w" class="wind_1">
                        <a class="close" onclick="CloseWindow('add_double_w');">X</a>
                        <br>
                        <br>
                        <p class="style_7">Добавление двойного адреса</p>
                        <table>
                            <tr>
                                <td><p class="add_text_4">Выберите адрес здания:</p></td>
                                <td>
                                    <select id="sum_address1" class="contacts_inp_1">
                                        <?php
                                        for ($i = 0; $i < count($all_addr->id); $i++) {
                                            echo '<option value="' . $all_addr->id[$i] . '">' . $all_addr->address[$i] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <p class="add_text_4">+</p>
                                </td>
                                <td>
                                    <select id="sum_address2" class="contacts_inp_1">
                                        <?php
                                        for ($i = 0; $i < count($all_addr->id); $i++) {
                                            echo '<option value="' . $all_addr->id[$i] . '">' . $all_addr->address[$i] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <button onclick="DoubleAddress();" class="all_up">Объединить</button>
                                    <a class="a_1" onclick="AllAddressesExDouble('sum_address1');
                                            AllAddressesExDouble('sum_address2');"><img src="../images/update.png" alt=""></a>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php
                }
                ?>
                <?php
                if ($ShowParamID == 3) {
                    $all = new AllAddresses($page, $where);
                    $districts = new AllDistricts(0, '');
                    $massives = new AllMassives(0, '');
                    $streets = new AllStreets(0, '');
                    ?>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img class="img_options" src="../images/add_team.png" title="Добавить адрес" alt="" onclick="ShowAddWindow(1);">
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
                                    <td><p class="style_2">По <span style="color: green;">улице</span>:</p></td>
                                    <td><input type="text" name="double_s_str" value=""></td>
                                </tr>
                                <tr>
                                    <td><p class="style_2">По <span style="color: green;">номеру дома</span>:</p></td>
                                    <td><input type="text" name="double_s_num" value=""></td>
                                </tr>
                                <tr>
                                    <td><p class="style_2">Без фотографии:</p></td>
                                    <td>
                                        <label><input type="radio" name="double_s_photo" value="1">С фотографией</label>
                                        <label><input type="radio" name="double_s_photo" value="0">Без фотографии</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="hidden" value="3" name="PageType">
                                        <input type="submit" style="float:left;" value="Найти">
                                    <td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    <div class="open_content">
                        <b><span style="color: blue;">Управление адресами</span></b><br><br>
                        <?php
                        $all->GenerateNavigation($page, $where, $link);
                        ?>
                        <table id="double_table" style="width: 100%; text-align: center;">
                            <tr style="background: #7caed3;">
                                <td><p class="style_5">Адрес</p></td>
                                <td><p class="style_5">Действие</p></td>
                            </tr>
                            <?php
                            for ($i = 0; $i < count($all->id); $i++) {
                                $url = '../images/photo_n.png';
                                if ($all->with_photo[$i] != '') {
                                    $url = '../images/photo.png';
                                }
                                echo '<tr style="background: #f0f4f4;" id="do_ad_' . $all->id[$i] . '"><td><p class="style_9_1">' . $all->address[$i] . '</p></td>';
                                echo '<td>';
                                if ($all->id[$i] != 0) {
                                    echo '<img class="img_options" src="' . $url . '" title="Редактировать" alt="" onclick="LoadPhotosFromAddr(' . $all->id[$i] . ');">';
                                    echo '<img onclick="DeleteAddress(this);" class="img_options" src="../images/delete_team.png" title="Удалить адрес" alt="' . $all->id[$i] . '"></td></tr>';
                                }
                            }
                            ?>
                        </table>
                        <?php
                        $all->GenerateNavigation($page, $where, $link);
                        ?>
                    </div>

                    <div id="add_w" class="wind_1">
                        <a class="close" onclick="CloseWindow('add_w');">X</a>
                        <br>
                        <br>
                        <p class="style_7">Добавление адреса</p>
                        <table width=100% cellpadding=0 sellspacing=0 border="0">
                            <tr>
                                <td>
                            <table>
                                <tr>
                                    <td colspan="2">
                                        <p class="add_text_4">Введите номер дома<br>(включая дроби, буквы, строения и прочее):</p>
                                    <!--</td>
                                    <td>-->
                                        <input type="text" id="create_house_num" class="contacts_inp_1">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <p class="add_text_4">Выберите улицу здания:</p>
                                    <!--</td>
                                    <td>-->
                                        <select id="create_street" class="contacts_inp_1">
                                            <?php
                                            for ($i = 0; $i < count($streets->id); $i++) {
                                                echo '<option value="' . $streets->id[$i] . '">' . $streets->name[$i] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <p class="add_text_4">Выберите район здания:</p>
                                    <!--</td>
                                    <td>-->
                                        <select id="create_distr" class="contacts_inp_1">
                                            <option value="">Район не выбран</option>
                                            <?php
                                            for ($i = 0; $i < count($districts->id); $i++) {
                                                echo '<option value="' . $districts->id[$i] . '">' . $districts->name[$i] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <p class="add_text_4">Выберите массив здания:</p>
                                    <!--</td>
                                    <td>-->
                                        <select id="create_massive" class="contacts_inp_1">
                                            <option value="">Массив не выбран</option>
                                            <?php
                                            for ($i = 0; $i < count($massives->id); $i++) {
                                                echo '<option value="' . $massives->id[$i] . '">' . $massives->name[$i] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p class="add_text_4">X:</p>
                                    </td>
                                    <td>
                                        <input type="text" id="create_house_num_x" class="contacts_inp_1" value="">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p class="add_text_4">Y:</p>
                                    </td>
                                    <td>
                                        <input type="text" id="create_house_num_y" class="contacts_inp_1" value="">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <button onclick="CreateNewAddress();" class="all_up">Добавить</button>
                                    </td>
                                </tr>
                            </table>
                            </td>
                            <td>
                                <div style="width: 400px; height: 300px; border: 1px solid #559228;">
                                    <iframe id="admin_map_2" src="map.php" width="400" height="300" style="clear:left; width:400px; height:300px;float:left;border: 0px;"></iframe>
                                </div>
                            </td>
                            </tr>
                        </table>
                    </div>

                    <div id="add_s" class="wind_1">
                        <a class="close" onclick="CloseWindow('add_s');">X</a>
                        <br>
                        <br>
                        <p class="style_7">Добавление фотографии</p>
                        <table>
                            <tr id="address_photos">
                                <td>
                                    <input type="hidden" id="addr_ph_id" value="">
                                    <button id="PhotoForAddress" class="all_up">Добавить фото</button><br>
                                    <span id="status"></span>
                                </td>
                                <td>
                                    <div id="LoadPhotos" class="add_img_content">
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php
                }
                ?>
                <?php
                if ($ShowParamID == 4) {
                    $streets = new AllStreets($page, $where);
                    ?>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img class="img_options" src="../images/add_team.png" title="Добавить улицу" alt="" onclick="ShowAddWindow(0);">
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
                                    <td><p class="style_2">По <span style="color: green;">улице</span>:</p></td>
                                    <td><input type="text" name="double_s_str" value=""></td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="hidden" value="4" name="PageType">
                                        <input type="submit" style="float:left;" value="Найти">
                                    <td>
                                </tr>
                            </table>
                        </form>
                    </div>

                    <div class="open_content">
                        <b><span style="color: blue;">Управление улицами</span></b><br><br>
                        <?php
                        $streets->GenerateNavigation($page, $where, $link);
                        ?>
                        <table id="double_table" style="width: 100%; text-align: center;">
                            <tr style="background: #7caed3;">
                                <td><p class="style_5">Адрес</p></td>
                                <td><p class="style_5">Действие</p></td>
                            </tr>
                            <?php
                            for ($i = 0; $i < count($streets->id); $i++) {
                                echo '<tr style="background: #f0f4f4;" id="do_ad_' . $streets->id[$i] . '"><td><p class="style_9_1">' . $streets->name[$i] . '</p></td>';
                                echo '<td><img onclick="ChangeStreet(this);" class="img_options" src="../images/edit.png" title="Редактировать улицу" alt="' . $streets->id[$i] . '">
                                <img onclick="DeleteStreet(this);" class="img_options" src="../images/delete_team.png" title="Удалить улицу" alt="' . $streets->id[$i] . '"></td></tr>';
                            }
                            ?>
                        </table>
                        <?php
                        $streets->GenerateNavigation($page, $where, $link);
                        ?>
                    </div>

                    <div id="add_w" class="wind_1">
                        <a class="close" onclick="CloseWindow('add_w');">X</a>
                        <br>
                        <br>
                        <p class="style_7">Добавление улицы</p>
                        <table>
                            <tr>
                                <td>
                                    <p class="add_text_4">Введите название улицы:</p></td>
                                <td>
                                    <input type="text" id="create_new_street" class="contacts_inp_1">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <i>Формат: название тип<br>Пример: Ленина проспект, Суворова улица</i>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button onclick="CreateNewStreet();" class="all_up">Добавить</button>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div id="change_w" class="wind_1">
                        <a class="close" onclick="CloseWindow('change_w');">X</a>
                        <br>
                        <br>
                        <p class="style_7">Редактирование улицы</p>
                        <table>
                            <tr>
                                <td>
                                    <input type="hidden" id="change_id_street" value="" />
                                    <input type="text" id="change_name_street" class="contacts_inp_1" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button onclick="ChangeNameStreet();" class="all_up">Редактировать</button>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php
                }
                ?>
                <?php
                if ($ShowParamID == 5) {
                    $districts = new AllDistricts($page, $where);
                    ?>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img class="img_options" src="../images/add_team.png" title="Добавить район" alt="" onclick="ShowAddWindow(2);">
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
                                    <td><p class="style_2">По <span style="color: green;">району</span>:</p></td>
                                    <td><input type="text" name="double_s_distr" value=""></td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="hidden" value="5" name="PageType">
                                        <input type="submit" style="float:left;" value="Найти">
                                    <td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    <div class="open_content">
                        <b><span style="color: blue;">Управление районами</span></b><br><br>
                        <?php
                        $districts->GenerateNavigation($page, $where, $link);
                        ?>
                        <table id="double_table" style="width: 100%; text-align: center;">
                            <tr style="background: #7caed3;">
                                <td><p class="style_5">Адрес</p></td>
                                <td><p class="style_5">Действие</p></td>
                            </tr>
                            <?php
                            for ($i = 0; $i < count($districts->id); $i++) {
                                echo '<tr style="background: #f0f4f4;" id="do_ad_' . $districts->id[$i] . '"><td><p class="style_9_1">' . $districts->name[$i] . '</p></td>';
                                echo '<td><img onclick="ChangeDistrict(this);" class="img_options" src="../images/edit.png" title="Редактировать район" alt="' . $districts->id[$i] . '">
                                <img onclick="DeleteDistrict(this);" class="img_options" src="../images/delete_team.png" title="Удалить район" alt="' . $districts->id[$i] . '"></td></tr>';
                            }
                            ?>
                        </table>
                        <?php
                        $districts->GenerateNavigation($page, $where, $link);
                        ?>
                    </div>
                    <div id="add_w" class="wind_1">
                        <a class="close" onclick="CloseWindow('add_w');">X</a>
                        <br>
                        <br>
                        <p class="style_7">Добавление района</p>
                        <table width=100% cellpadding=0 sellspacing=0 border="0">
                            <tr>
                                <td>
                            <table>
                                <tr>
                                    <td>
                                        <p class="add_text_4">Введите название района:</p></td>
                                    <td>
                                        <input type="text" id="create_new_district" class="contacts_inp_1">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <i>Формат: название<br>Пример: Октябрьский, Ленинский</i>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p class="add_text_4">X:</p></td>
                                    <td>
                                        <input type="text" id="create_district_x" class="contacts_inp_1" value="">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p class="add_text_4">Y:</p></td>
                                    <td>
                                        <input type="text" id="create_district_y" class="contacts_inp_1" value="">
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="2">
                                        <button onclick="CreateNewDistrict();" class="all_up">Добавить</button>
                                    </td>
                                </tr>
                            </table>
                                </td>
                                <td>
                                    <div style="width: 400px; height: 300px; border: 1px solid #559228;">
                                        <iframe id="admin_map" src="map.php?d=1" width="400" height="300" style="clear:left; width:400px; height:300px;float:left;border: 0px;"></iframe>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div id="change_a_w" class="wind_1">
                        <a class="close" onclick="CloseWindow('change_a_w');">X</a>
                        <br>
                        <br>
                        <p class="style_7">Редактирование района</p>
                        <table>
                            <tr>
                                <td>
                                    <input type="hidden" id="change_id_district" value="" />
                                    <input type="text" id="change_name_district" class="contacts_inp_1" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button onclick="ChangeNameDistrict();" class="all_up">Редактировать</button>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php
                }
                ?>
                <?php
                if ($ShowParamID == 6) {
                    $massives = new AllMassives($page, $where);
                    ?>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img class="img_options" src="../images/add_team.png" title="Добавить жилмассив" alt="" onclick="ShowAddWindow(0);">
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
                                    <td><p class="style_2">По <span style="color: green;">жилмассиву</span>:</p></td>
                                    <td><input type="text" name="double_s_mass" value=""></td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="hidden" value="6" name="PageType">
                                        <input type="submit" style="float:left;" value="Найти">
                                    <td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    <div class="open_content">
                        <b><span style="color: blue;">Управление жилмассивами</span></b><br><br>
                        <?php
                        $massives->GenerateNavigation($page, $where, $link);
                        ?>
                        <table id="double_table" style="width: 100%; text-align: center;">
                            <tr style="background: #7caed3;">
                                <td><p class="style_5">Адрес</p></td>
                                <td><p class="style_5">Действие</p></td>
                            </tr>
                            <?php
                            for ($i = 0; $i < count($massives->id); $i++) {
                                echo '<tr style="background: #f0f4f4;" id="do_ad_' . $massives->id[$i] . '"><td><p class="style_9_1">' . $massives->name[$i] . '</p></td>';
                                echo '<td><img onclick="ChangeMassive(this);" class="img_options" src="../images/edit.png" title="Редактировать район" alt="' . $massives->id[$i] . '">
                                <img onclick="DeleteMassive(this);" class="img_options" src="../images/delete_team.png" title="Удалить район" alt="' . $massives->id[$i] . '"></td></tr>';
                            }
                            ?>
                        </table>
                        <?php
                        $massives->GenerateNavigation($page, $where, $link);
                        ?>
                    </div>
                    <div id="add_w" class="wind_1">
                        <a class="close" onclick="CloseWindow('add_w');">X</a>
                        <br>
                        <br>
                        <p class="style_7">Добавление жилмассива</p>
                        <table>
                            <tr>
                                <td>
                                    <p class="add_text_4">Введите название жилмассива:</p></td>
                                <td>
                                    <input type="text" id="create_new_massive" class="contacts_inp_1">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <i>Формат: название<br>Пример: Академгородок, Зеленые горки</i>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button onclick="CreateNewMassive();" class="all_up">Добавить</button>
                                </td>
                            </tr>
                        </table>
                    </div>
                <div id="change_m_w" class="wind_1">
                        <a class="close" onclick="CloseWindow('change_m_w');">X</a>
                        <br>
                        <br>
                        <p class="style_7">Редактирование жилмассива</p>
                        <table>
                            <tr>
                                <td>
                                    <input type="hidden" id="change_id_massive" value="" />
                                    <input type="text" id="change_name_massive" class="contacts_inp_1" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button onclick="ChangeNameMassive();" class="all_up">Редактировать</button>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <div class="temno" id="temno"></div>
    </body>
</html>