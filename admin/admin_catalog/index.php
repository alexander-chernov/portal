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
        require_once 'inc/banners.php';
        require_once 'inc/classes.php';
        //require_once '../../../admin/admin_map/inc/classes.php';

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
            if (!UserAccess(7)) {
                exit('У вас нет прав заходить в эту категорию!');
            }
        } elseif ($_SESSION['privileges'] == 1) {
            $_SESSION['map_access'] = 1;
        }
        UpdateActivityAdmin();
        require_once '../inc/functions.php';
        CreateTempTables();

        if (isset($_GET['Search4'])) {
            $ShowParamID = 4;
        }
        if (isset($_GET['Search5'])) {
            $ShowParamID = 5;
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

        if (!in_array($ShowParamID, array(1, 2, 3, 4, 5))) {
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
                            status.html('<img src="../images/ajax-loader.gif" alt="" width="30">');
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
                            </p></td>
                    </tr>
                    <tr>
                        <td><p class="autho_1">Логин:</p></td>
                        <td><p class="autho_2"><?php echo $_SESSION['login']; ?></p></td>
                    </tr>
                </table>
            </div>
        </div>
        <p class="topic">Управление разделом: <span style="color: #ff9c00;">Каталог</span></p>
        <div class="center_block">
            <div id="menu_2">
                <a href="./?PageType=1">Организации</a>
                <a href="./?PageType=2">Банеры</a>
                <!--<a href="./?PageType=3">Каталог</a>
                <a href="./?PageType=4">Рубрика</a>
                <a href="./?PageType=5">Подрубрика</a>-->
            </div>

            <?php
            if ($ShowParamID == 1) {
                $where = '';
                if (isset($_POST['new_organization_post'])) {
                    $_POST['new_organization_name'] = filter_var($_POST['new_organization_name'], FILTER_SANITIZE_STRIPPED);
                    $_POST['new_organization_descr'] = filter_var($_POST['new_organization_descr'], FILTER_SANITIZE_STRIPPED);
                    $_POST['new_organization_email'] = filter_var($_POST['new_organization_email'], FILTER_SANITIZE_STRIPPED);
                    $_POST['new_organization_site'] = filter_var($_POST['new_organization_site'], FILTER_SANITIZE_STRIPPED);
                    $_POST['new_organization_category'] = filter_var($_POST['new_organization_category'], FILTER_SANITIZE_STRIPPED);
                    $_POST['new_organization_subcategory'] = filter_var($_POST['new_organization_subcategory'], FILTER_SANITIZE_STRIPPED);
                    $_POST['new_organization_phone1'] = filter_var($_POST['new_organization_phone1'], FILTER_SANITIZE_STRIPPED);
                    $_POST['new_organization_phone2'] = filter_var($_POST['new_organization_phone2'], FILTER_SANITIZE_STRIPPED);
                    $_POST['new_organization_phone3'] = filter_var($_POST['new_organization_phone3'], FILTER_SANITIZE_STRIPPED);
                    $_POST['new_organization_phone4'] = filter_var($_POST['new_organization_phone4'], FILTER_SANITIZE_STRIPPED);
                    $_POST['new_organization_street'] = filter_var($_POST['new_organization_street'], FILTER_SANITIZE_STRIPPED);
                    $_POST['new_organization_street_name'] = filter_var($_POST['new_organization_street_name'], FILTER_SANITIZE_STRIPPED);
                    $_POST['new_organization_house_num'] = filter_var($_POST['new_organization_house_num'], FILTER_SANITIZE_STRIPPED);
                    $x = $_POST['new_organization_house_num_x'];
                    $y = $_POST['new_organization_house_num_y'];
                    if (!preg_match('/^http:\/\//', $_POST['new_organization_site'])) {
                        $_POST['new_organization_site'] = 'http://' . $_POST['new_organization_site'];
                    }
                    try {
                        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
                        $mysql->exec('set names utf8');
                        $sql = "INSERT INTO base_org (name,description,address,category,subcategory,site,email,phone1,phone2,phone3,phone4,street,street_id,house_num,centerX,centerY
                                                ) VALUES (
                                                        :name,
                                                        :descr,
                                                        :address,
                                                        :category,
                                                        :subcategory,
                                                        :site,
                                                        :email,
                                                        :phone1,
                                                        :phone2,
                                                        :phone3,
                                                        :phone4,
                                                        :street_name,
                                                        :street_id,
                                                        :house_num,
                                                        :x,
                                                        :y
                                                )";
                        $queue1 = $mysql->prepare($sql);
                        $address = $_POST['new_organization_house_num'].', '.$_POST['new_organization_house_num'];
                        $exec_arr = array(
                                                    ":name" => $_POST['new_organization_name'],
                                                    ":descr" => $_POST['new_organization_descr'],
                                                    ":address" => $address,
                                                    ":category" => $_POST['new_organization_category'],
                                                    ":subcategory" => $_POST['new_organization_subcategory'],
                                                    ":site" => $_POST['new_organization_site'],
                                                    ":email" => $_POST['new_organization_email'],
                                                    ":phone1" => $_POST['new_organization_phone1'],
                                                    ":phone2" => $_POST['new_organization_phone2'],
                                                    ":phone3" => $_POST['new_organization_phone3'],
                                                    ":phone4" => $_POST['new_organization_phone4'],
                                                    ":street_name" => $_POST['new_organization_street_name'],
                                                    ":street_id" => $_POST['new_organization_street'],
                                                    ":house_num" => $_POST['new_organization_house_num'],
                                                    ":x" => $x,
                                                    ":y" => $y
                                                );
                        if ($queue1->execute($exec_arr)) {
                        } else {
                            echo 'Error';
                        }
                    } catch (PDOException $e) {
                        //echo $e->getMessage(); //Boring error messages from anything else!
                        exit();
                    }
                }

                if (isset($_REQUEST)) {
                    $link_ar = array();
                    if (!empty($_GET['org_name'])) {
                        $link_ar['org_name'] = filter_var($_GET['org_name'], FILTER_SANITIZE_STRIPPED);
                        if (empty($where)) {
                            $where .= " WHERE name LIKE '%" . $link_ar['org_name'] . "%' ";
                        } else {
                            $where .= " AND name LIKE '%" . $link_ar['org_name'] . "%' ";
                        }
                    }
                    if ($_GET['category'] != 0) {
                        $link_ar['category'] = filter_var($_GET['category'], FILTER_VALIDATE_INT);
                        if (empty($where)) {
                            $where .= " WHERE category=" . $link_ar['category'] . " ";
                        } else {
                            $where .= " AND category=" . $link_ar['category'] . " ";
                        }
                    }
                    if ($_GET['big_subcategory'] != 0) {
                        $link_ar['big_subcategory'] = filter_var($_GET['big_subcategory'], FILTER_VALIDATE_INT);
                        if (empty($where)) {
                            $where .= " WHERE subcategory=" . $link_ar['big_subcategory'] . " ";
                        } else {
                            $where .= " AND subcategory=" . $link_ar['big_subcategory'] . " ";
                        }
                    }
                    $link_ar_url = array();
                    foreach ($link_ar as $key => $value) {
                        $link_ar_url[] = urlencode($key) . '=' . urlencode($value);
                    }
                    $link = '&' . join('&', $link_ar_url);
                }
                $organizations = new Organizations($page, $where, 50, 1);
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Управление организациями Каталога</span></b><br><br>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img class="img_options" src="../images/add_team.png" title="Добавить организацию" alt="" onclick="ShowAddOrganizationWindow();">
                                <img class="img_options" src="../images/delete_team.png" title="Удалить выделенные организации" alt="">
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
                                    <td><p class="style_2">По <span style="color: green;">Названию организации</span>:</p></td>
                                    <td><input type="text" name="org_name" value=""></td>
                                </tr>
                                <tr>
                                    <td><p class="style_2">По <span style="color: green;">Каталогу</span>:</p></td>
                                    <td>
                                        <select name="category">
                                            <option value="0">Не важно</option>
                                            <?php
                                            $categories = new CatalogCategories();
                                            for ($i = 0; $i < count($categories->id); $i++) {
                                                echo '<option value="' . $categories->id[$i] . '">' . $categories->name[$i] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><p class="style_2">По <span style="color: green;">Рубрике каталога</span>:</p></td>
                                    <td>
                                        <select name="big_subcategory">
                                            <option value="0">Не важно</option>
                                            <?php
                                            $sub = new CatalogSubCategories();
                                            for ($i = 0; $i < count($sub->id_sub); $i++) {
                                                echo '<option value="' . $sub->id_sub[$i] . '">' . $sub->name_sub[$i] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="submit" name="Search1" style="float:left;" value="Найти">
                                    <td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    <?php
                    $organizations->GenerateNavigation($page, $where, $link);
                    ?>
                    <table style="width: 100%; text-align: center;">
                        <tr style="background: #7caed3;">
                            <td colspan="2"><p class="style_5">Наименование организации</p></td>
                            <td><p class="style_5">Каталог</p></td>
                            <td><p class="style_5">Рубрика</p></td>
                            <td><p class="style_5">Действие</p></td>
                        </tr>
                        <?php
                        for ($i = 0; $i < count($organizations->id); $i++) {
                            ?>
                            <tr <?php echo 'id="otntd_' . $organizations->id[$i] . '"'; ?> style="background: #f0f4f4;">
                                <td style="width: 20px;"><input type="checkbox" value=""></td>
                                <td style="width: 400px;"><p class="style_9_1" title="<?=$organizations->watches[$i]?>"><?php echo $organizations->name[$i]; ?></p></td>
                                <td ><p class="style_9_1"><?php echo $organizations->category[$i]; ?></p></td>
                                <td ><p class="style_9_1"><?php echo $organizations->subcategory[$i]; ?></p></td>
                                <!--
                                <td style="width: 250px;"><p class="style_9_1">
                                        <?php
                                        //var_dump($organizations->category[$i]);
                                        //var_dump($organizations->subcategory[$i]);
                                        /*
                                        foreach ($organizations->category[$i] as $value) {
                                            echo $value . '<br>';
                                        }
                                        */
                                        echo $organizations->category[$i].'/'.$organizations->subcategory[$i].'<br>';
                                        ?>
                                    </p></td>
                                    -->
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowInfo(' . $organizations->id[$i] . ');"'; ?> title="Информация по организации"><img src="../images/info.png" alt=""></a>
                                    <!--<a class="a_1" <?php echo 'onclick="ChangeOrganization(' . $organizations->id[$i] . ');"'; ?>><img src="../images/edit.png" title="Редактировать организауию" alt=""></a>
                                    <a class="a_1" <?php echo 'onclick="AddNewOrganizationAddress(' . $organizations->id[$i] . ');"'; ?>><img src="../images/add_tool.png" title="Добавить Адрес" alt=""></a>-->
                                    <a class="a_1" <?php echo 'onclick="DeleteAllOrganization(' . $organizations->id[$i] . ');"'; ?>><img src="../images/delete.png" title="Удалить организацию" alt=""></a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <?php
                    //$all = new AllAddresses($page, $where);
                    $streets = new AllStreets(0, '');

                    $organizations->GenerateNavigation($page, $where, $link);
                    ?>
                </div>

                <div id="info_company" class="wind">       <!--Всплывающее окно Информации о организации-->
                </div>

                <div id="edit_company" class="wind_1">       <!--Всплывающее окно редактировать организацию-->
                </div>

                <div id="red_adres" class="wind_green">       <!--Всплывающее окно редактировать адрес-->
                </div>

                <div id="add_company" class="wind_1">       <!--Всплывающее окно Добавить организацию в каталог-->
                    <a class="close" onclick="CloseWindow('add_company');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Добавление организации в каталог</p>
                    <form action="./" method="post">
                        <table width=100% cellpadding=0 sellspacing=0 border="0">
                            <tr>
                                <td>

                        <table style="text-align: left;">
                            <tr>
                                <td><p class="style_2">Наименование организации:</p></td>
                                <td><input onkeyup="CanBeCreated();" id="new_organization_name" name="new_organization_name" style="width: 300px;" type="text" value=""></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Каталог:</p></td>
                                <td>
                                    <select name="new_organization_category" style="width: 300px;">
                                        <option value="0">Не важно</option>
                                        <?php
                                        $categories = new CatalogCategories();
                                        for ($i = 0; $i < count($categories->id); $i++) {
                                            echo '<option value="' . $categories->id[$i] . '">' . $categories->name[$i] . '</option>';
                                        }
                                        ?>
                                    </select>

                                </td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Рубрика:</p></td>
                                <td>
                                    <select name="new_organization_subcategory" style="width: 300px;">
                                        <option value="0">Не важно</option>
                                        <?php
                                        $sub = new CatalogSubCategories();
                                        for ($i = 0; $i < count($sub->id_sub); $i++) {
                                            echo '<option value="' . $sub->id_sub[$i] . '">' . $sub->name_sub[$i] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
<!--                            <tr>
                                <td><p class="style_2">Адрес организации:</p></td>
                                <td><input name="new_organization_address" style="width: 300px;" type="text" value=""></td>
                            </tr>
-->
                            <tr>
                                <td><p class="style_2">E-mail организации:</p></td>
                                <td><input name="new_organization_email" style="width: 300px;" type="text" value=""></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Сайт организации:</p></td>
                                <td><input name="new_organization_site" style="width: 300px;" type="text" value=""></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Телефон - 1:</p></td>
                                <td><input name="new_organization_phone1" style="width: 300px;" type="text" value=""></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Телефон - 2</p></td>
                                <td><input name="new_organization_phone2" style="width: 300px;" type="text" value=""></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Телефон - 3:</p></td>
                                <td><input name="new_organization_phone3" style="width: 300px;" type="text" value=""></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Телефон - 4:</p></td>
                                <td><input name="new_organization_phone4" style="width: 300px;" type="text" value=""></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Улица:</p></td>
                                <td>
                                    <select name="new_organization_street" id="shown_street_name" style="width: 300px;"
                                            onchange="$('#hidden_street_name').val($('#shown_street_name :selected').text())"
                                            onkeyup="$('#hidden_street_name').val($('#shown_street_name :selected').text())"
                                            onkeydown="$('#hidden_street_name').val($('#shown_street_name :selected').text())"
                                        >
                                        <option value="0">Не важно</option>
                                        <?php
                                        for ($i = 0; $i < count($streets->id); $i++) {
                                            echo '<option value="' . $streets->id[$i] . '">' . $streets->name[$i] . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <input name="new_organization_street_name" id="hidden_street_name" type="hidden" value="">
                                </td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Номер дома:</p></td>
                                <td><input name="new_organization_house_num" style="width: 300px;" type="text" value=""></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">X:</p></td>
                                <td><input name="new_organization_house_num_x" id="new_organization_house_num_x" style="width: 300px;" type="text" value=""></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Y:</p></td>
                                <td><input name="new_organization_house_num_y" id="new_organization_house_num_y" style="width: 300px;" type="text" value=""></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Описание организации:</p></td>
                                <td><textarea name="new_organization_descr" style="width: 300px;" rows="5" cols="10"></textarea></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input disabled="disabled" type="submit" id="new_organization_post" name="new_organization_post" style="float:left;" value="Добавить">
                                </td>
                            </tr>
                        </table>

                                </td>
                                <td>
                                    <div style="width: 400px; height: 300px; border: 1px solid #559228;">
                                        <iframe id="admin_map"  width="400" height="300" style="clear:left; width:400px; height:300px;float:left;border: 0px;"></iframe>
                                    </div>
                                </td>
                            </tr>
                        </table>

                    </form>
                </div>

                <div id="add_adres" class="wind_1">       <!--Всплывающее окно Добавить дополнительный адрес организации-->
                    <?php
                    $a = new AllAddresses();
                    ?>
                    <a class="close" onclick="CloseWindow('add_adres');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Добавление адреса</p>
                    <table style="text-align: left;">
                        <?php
                        echo '<tr><td><p class="style_2">Адрес:</p></td>
                            <td colspan="2">
                            <select id="ANOAA">';
                        for ($i = 0; $i < count($a->id); $i++) {
                            if ($_POST['ChangeAddress'] == $a->id[$i]) {
                                echo '<option selected value="' . $a->id[$i] . '">' . $a->address[$i] . '</option>';
                            } else {
                                echo '<option value="' . $a->id[$i] . '">' . $a->address[$i] . '</option>';
                            }
                        }
                        echo '</select></td></tr>';
                        ?>
                        <tr>
                            <td colspan="2">
                                <input type="hidden" name="ANOAS" id="ANOAS" value="">
                                <button onclick="AddNewOrganizationAddressSubmit();">Добавить</button>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
            }
            ?>

            <?php
            if ($ShowParamID == 2) {
                $banner = new BannersAll(0);
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Банеры страницы Каталог</span></b><br><br>
                    <div class="baners">
                        <table border="1">
                            <tr>
                                <td colspan="2"><p class="style_1">Банеры страницы Каталог</p></td>
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
                        <table id="Banners_8" border="1">
                            <tr>
                                <td>
                                    <p class="style_1">Банеры страницы Каталог (Левая сторона)</p>
                                </td>
                                <td>
                                    <a class="a_1" onclick="AddBanner(8);">
                                        <img src="../images/add_team.png" alt="">
                                    </a>
                                </td>
                            </tr>
                            <?php
                            for ($i = 0; $i < count($banner->banner_id); $i++) {
                                if ($banner->banner_type[$i] == 8) {
                                    ?>
                                    <tr>
                                        <td><p class="style_2"><?php echo $banner->banner_organization[$i]; ?></p></td>
                                        <td>
                                            <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[$i] . ');"'; ?> title="Информация">
                                                <img src="../images/info.png" alt="">
                                            </a>
                                            <?php
                                            echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[$i] . ');" title="Оставшееся время: ' . $banner->banner_end_days[$i] . ' дней">';
                                            if ($banner->banner_end_days[$i] < 5) {
                                                echo '<img src="../images/clock_red_1.png" alt="">';
                                            } else {
                                                echo '<img src="../images/clock_green_1.png" alt="">';
                                            }
                                            echo '</a>';
                                            ?>
                                            <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[$i] . ');"'; ?> title="Просмотр">
                                                <img src="../images/photo_baner.png" alt="">
                                            </a>
                                            <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[$i] . ');"'; ?>>
                                                <img src="../images/edit.png" title="Редактировать" alt="">
                                            </a>
                                            <a class="a_1" <?php echo 'onclick="DeleteBanner(' . $banner->banner_id[$i] . ',this);"'; ?>>
                                                <img src="../images/delete.png" title="Удалить" alt="">
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </table>
                        <table id="Banners_9" border="1">
                            <tr>
                                <td>
                                    <p class="style_1">Банеры страницы Каталог (Правая сторона)</p>
                                </td>
                                <td>
                                    <a class="a_1" onclick="AddBanner(9);">
                                        <img src="../images/add_team.png" alt="">
                                    </a>
                                </td>
                            </tr>
                            <?php
                            for ($i = 0; $i < count($banner->banner_id); $i++) {
                                if ($banner->banner_type[$i] == 9) {
                                    ?>
                                    <tr>
                                        <td><p class="style_2"><?php echo $banner->banner_organization[$i]; ?></p></td>
                                        <td>
                                            <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[$i] . ');"'; ?> title="Информация">
                                                <img src="../images/info.png" alt="">
                                            </a>
                                            <?php
                                            echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[$i] . ');" title="Оставшееся время: ' . $banner->banner_end_days[$i] . ' дней">';
                                            if ($banner->banner_end_days[$i] < 5) {
                                                echo '<img src="../images/clock_red_1.png" alt="">';
                                            } else {
                                                echo '<img src="../images/clock_green_1.png" alt="">';
                                            }
                                            echo '</a>';
                                            ?>
                                            <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[$i] . ');"'; ?> title="Просмотр">
                                                <img src="../images/photo_baner.png" alt="">
                                            </a>
                                            <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[$i] . ');"'; ?>>
                                                <img src="../images/edit.png" title="Редактировать" alt="">
                                            </a>
                                            <a class="a_1" <?php echo 'onclick="DeleteBanner(' . $banner->banner_id[$i] . ',this);"'; ?>>
                                                <img src="../images/delete.png" title="Удалить" alt="">
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
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
                        <tr>
                            <td>
                                <button>Изменить</button>
                            </td>
                        </tr>
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
                $categories = new CatalogCategories();
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Управление Каталогом</span></b><br><br>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img class="img_options" src="../images/add_team.png" title="Добавить Каталог" alt="" onclick="$('#add_catalog').show(500);
                    enableA();">
                                <img class="img_options" src="../images/delete_team.png" title="Удалить выделенные Каталоги" alt="">
                            </td>
                        </tr>
                    </table>
                    <table style="width: 100%; text-align: center;">
                        <tr style="background: #7caed3;">
                            <td colspan="2"><p class="style_5">Наименование Каталога</p></td>
                            <td><p class="style_5">Действие</p></td>
                        </tr>
                        <?php
                        for ($i = 0; $i < count($categories->id); $i++) {
                            ?>
                            <tr style="background: #f0f4f4;" <?php echo 'id="tr_id_' . $categories->id[$i] . '"'; ?>>
                                <td style="width: 20px;"><input <?php echo 'id="categorie_id_' . $categories->id[$i] . '"'; ?> name="categorie[]" type="checkbox" value=""></td>
                                <td style="width: 500px;">
                                    <p class="style_9">
                                        <?php
                                        echo $categories->name[$i];
                                        ?>
                                    </p>
                                </td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ChangeCategory(' . $categories->id[$i] . ');"'; ?>><img src="../images/edit.png" title="Редактировать Каталог" alt=""></a>
                                    <a class="a_1" <?php echo 'onclick="DeleteCategory(' . $categories->id[$i] . ');"'; ?>><img src="../images/delete.png" title="Удалить категорию" alt=""></a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>

                <div id="edit_catalog" class="wind">       <!--Всплывающее окно редактирования каталога-->
                    <a class="close" onclick="CloseWindow('edit_catalog');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Редактирование Каталога</p>
                    <table style="text-align: left;">
                        <tr>
                            <td><span>Изменить название каталога:</span></td>
                        </tr>
                        <tr>
                            <td>
                                <input style="width: 250px;" type="text" id="CategoryName" value="">
                            </td>  
                        </tr>
                        <tr>
                            <td>
                                <input type="hidden" id="CategoryID" value="">
                                <button onclick="SaveCategory();">
                                    Изменить
                                </button>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="add_catalog" class="wind">       <!--Всплывающее окно добавления каталога-->
                    <a class="close" onclick="CloseWindow('add_catalog');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Добавление Каталога</p>
                    <table style="text-align: left;">
                        <tr>
                            <td><span>Введите название каталога:</span></td>
                        </tr>
                        <tr>
                            <td><input style="width: 250px;" id="NewCategory" type="text" value=""></td>  
                        </tr>
                        <tr>
                            <td><button onclick="AddCategory();">Добавить</button></td>
                        </tr>
                    </table>
                </div>
                <?php
            }
            ?>

            <?php
            if ($ShowParamID == 4) {
                $where = '';
                if (isset($_GET['Search4'])) {
                    if (!empty($_GET['big_subcategory'])) {
                        if (empty($where)) {
                            $where .= " WHERE k_cbs_name LIKE '%" . filter_var($_GET['big_subcategory'], FILTER_SANITIZE_STRIPPED) . "%' ";
                        } else {
                            $where .= " AND k_cbs_name LIKE '%" . filter_var($_GET['big_subcategory'], FILTER_SANITIZE_STRIPPED) . "%' ";
                        }
                    }
                    if ($_GET['category'] != 0) {
                        if (empty($where)) {
                            $where .= " WHERE k_cc_id=" . filter_var($_GET['category'], FILTER_VALIDATE_INT) . " ";
                        } else {
                            $where .= " AND k_cc_id=" . filter_var($_GET['category'], FILTER_VALIDATE_INT) . " ";
                        }
                    }
                }
                $subcategory = new CatalogSubCategories($where);
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Управление рубрикой каталога</span></b><br><br>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img class="img_options" src="../images/add_team.png" title="Добавить Рубрику в Каталог" alt="" onclick="ShowAddWindow();">
                                <img class="img_options" src="../images/delete_team.png" title="Удалить выделенные Рубрики" alt="">
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
                                    <td><p class="style_2">По <span style="color: green;">Наименованию Рубрики</span>:</p></td>
                                    <td><input type="text" name="big_subcategory" value=""></td>
                                </tr>
                                <tr>
                                    <td><p class="style_2">По <span style="color: green;">Каталогу</span>:</p></td>
                                    <td>
                                        <select name="category">
                                            <option value="0">Не важно</option>
                                            <?php
                                            $categories = new CatalogCategories();
                                            for ($i = 0; $i < count($categories->id); $i++) {
                                                echo '<option value="' . $categories->id[$i] . '">' . $categories->name[$i] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><input type="submit" name="Search4" style="float:left;" value="Найти"><td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    <table style="width: 100%; text-align: center;">
                        <tr style="background: #7caed3;">
                            <td colspan="2"><p class="style_5">Наименование рубрики</p></td>
                            <td><p class="style_5">Принадлежит каталогу</p></td>
                            <td><p class="style_5">Действие</p></td>
                        </tr>
                        <?php
                        for ($i = 0; $i < count($subcategory->id_sub); $i++) {
                            ?>
                            <tr <?php echo 'id="TrId_' . $subcategory->id_sub[$i] . '"'; ?> style="background: #f0f4f4;">
                                <td style="width: 20px;"><input type="checkbox" value=""></td>
                                <td style="width: 250px;"><p class="style_9_1" <?php echo 'id="SubId_' . $subcategory->id_sub[$i] . '"'; ?>><?php echo $subcategory->name_sub[$i]; ?></p></td>
                                <td style="width: 250px;"><p class="style_9" <?php echo 'id="CatId_' . $subcategory->id_sub[$i] . '"'; ?>><?php echo $subcategory->name[$i]; ?></p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="EditSubcategory(' . $subcategory->id_sub[$i] . ');"'; ?>><img src="../images/edit.png" title="Редактировать Рубрику" alt=""></a>
                                    <a class="a_1" <?php echo 'onclick="DeleteSubcategory(' . $subcategory->id_sub[$i] . ');"'; ?>><img src="../images/delete.png" title="Удалить Рубрику" alt=""></a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>

                <div id="add_catalog_rubrik" class="wind">       <!--Всплывающее окно добавления рубрики в каталог-->
                    <a class="close" onclick="CloseWindow('add_catalog_rubrik');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Добавление рубрики</p>
                    <table style="text-align: left;">
                        <tr>
                            <td><p class="style_2">Выбериет каталог рубрики:</p></td>
                            <td>
                                <select id="AddCat">
                                    <?php
                                    for ($i = 0; $i < count($categories->id); $i++) {
                                        echo '<option value="' . $categories->id[$i] . '">' . $categories->name[$i] . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Введите название рубрики:</p></td>
                            <td><input style="width: 280px;" id="AddSubCat" type="text" value=""></td>  
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button onclick="AddSubcategory();">Добавить</button>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
            }
            ?>

            <?php
            if ($ShowParamID == 5) {
                $where = '';
                $link = '';
                if (isset($_REQUEST)) {
                    $link_ar = array();
                    if (!empty($_GET['subcategory'])) {
                        $link_ar['subcategory'] = filter_var($_GET['subcategory'], FILTER_SANITIZE_STRIPPED);
                        if (empty($where)) {
                            $where .= " WHERE k_cs_name LIKE '%" . $link_ar['subcategory'] . "%' ";
                        } else {
                            $where .= " AND k_cs_name LIKE '%" . $link_ar['subcategory'] . "%' ";
                        }
                    }
                    if ($_GET['category'] != 0) {
                        $link_ar['category'] = filter_var($_GET['category'], FILTER_VALIDATE_INT);
                        if (empty($where)) {
                            $where .= " WHERE k_cc_id=" . $link_ar['category'] . " ";
                        } else {
                            $where .= " AND k_cc_id=" . $link_ar['category'] . " ";
                        }
                    }
                    if ($_GET['big_subcategory'] != 0) {
                        $link_ar['big_subcategory'] = filter_var($_GET['big_subcategory'], FILTER_VALIDATE_INT);
                        if (empty($where)) {
                            $where .= " WHERE k_cbs_id=" . $link_ar['big_subcategory'] . " ";
                        } else {
                            $where .= " AND k_cbs_id=" . $link_ar['big_subcategory'] . " ";
                        }
                    }
                    $link_ar_url = array();
                    foreach ($link_ar as $key => $value) {
                        $link_ar_url[] = urlencode($key) . '=' . urlencode($value);
                    }
                    $link = '&' . join('&', $link_ar_url);
                }
                $subsub = new SubSubcategories($page, $where);
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Управление подрубрикой каталога</span></b><br><br>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img class="img_options" src="../images/add_team.png" title="Добавить Подрубрику в Каталог" alt="" onclick="AddSubSub();">
                                <img class="img_options" src="../images/delete_team.png" title="Удалить выделенные Подрубрики" alt="">
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
                                    <td><p class="style_2">По <span style="color: green;">Наименованию Подрубрики</span>:</p></td>
                                    <td><input type="text" name="subcategory" value=""></td>
                                </tr>
                                <tr>
                                    <td><p class="style_2">По <span style="color: green;">Каталогу</span>:</p></td>
                                    <td>
                                        <select name="category">
                                            <option value="0">Не важно</option>
                                            <?php
                                            $categories = new CatalogCategories();
                                            for ($i = 0; $i < count($categories->id); $i++) {
                                                echo '<option value="' . $categories->id[$i] . '">' . $categories->name[$i] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><p class="style_2">По <span style="color: green;">Рубрике каталога</span>:</p></td>
                                    <td>
                                        <select name="big_subcategory">
                                            <option value="0">Не важно</option>
                                            <?php
                                            $sub = new CatalogSubCategories();
                                            for ($i = 0; $i < count($sub->id_sub); $i++) {
                                                echo '<option value="' . $sub->id_sub[$i] . '">' . $sub->name_sub[$i] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="submit" name="Search5" style="float:left;" value="Найти">
                                    <td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    <?php
                    $subsub->GenerateNavigation($page, $where, $link);
                    ?>
                    <table style="width: 100%; text-align: center;">
                        <tr style="background: #7caed3;">
                            <td colspan="2"><p class="style_5">Наименование подрубрики</p></td>
                            <td><p class="style_5">Принадлежит каталогу</p></td>
                            <td><p class="style_5">Размещена в рубрике</p></td>
                            <td><p class="style_5">Действие</p></td>
                        </tr>
                        <?php
                        for ($i = 0; $i < count($subsub->id_ss); $i++) {
                            ?>
                            <tr <?php echo 'id="sstid_' . $subsub->id_ss[$i] . '"'; ?> style="background: #f0f4f4;">
                                <td style="width: 20px;"><input type="checkbox" value=""></td>
                                <td style="width: 250px;"><p class="style_9_1"><?php echo $subsub->name_ss[$i]; ?></p></td>
                                <td style="width: 250px;"><p class="style_9"><?php echo $subsub->name[$i]; ?></p></td>
                                <td style="width: 250px;"><p class="style_9_1"><?php echo $subsub->name_sub[$i]; ?></p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="RedaktSubSub(' . $subsub->id_ss[$i] . ');"'; ?>><img src="../images/edit.png" title="Редактировать Подрубрику" alt=""></a>
                                    <a class="a_1" <?php echo 'onclick="DeleteSubSub(' . $subsub->id_ss[$i] . ');"'; ?>><img src="../images/delete.png" title="Удалить Подрубрику" alt=""></a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <?php
                    $subsub->GenerateNavigation($page, $where, $link);
                    ?>
                </div>

                <div id="add_catalog_podrubrik" class="wind">       <!--Всплывающее окно добавления подрубрики в каталог-->
                    <a class="close" onclick="document.getElementById('add_catalog_podrubrik').style.display = 'none';
                    disableA();">X</a>
                    <br>
                    <br>
                    <p class="style_7">Добавление подрубрики</p>
                    <table style="text-align: left;">
                        <tr>
                            <td><p class="style_2">Выбериет рубрику для подрубрики:</p></td>
                            <td>
                                <select id="AddSubSubPar">
                                    <?php
                                    for ($i = 0; $i < count($sub->id_sub); $i++) {
                                        echo '<option value="' . $sub->id_sub[$i] . '">' . $sub->name_sub[$i] . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Введите название подрубрики:</p></td>
                            <td><input style="width: 280px;" type="text" id="AddSubSubName" value=""></td>  
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button onclick="AddSubSubPost();">Добавить</button>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="edit_catalog_podrubrik" class="wind">       <!--Всплывающее окно Редактирования подрубрики-->
                    <a class="close" onclick="CloseWindow('edit_catalog_podrubrik');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Редактирование подрубрики</p>
                    <?php
                    $subcategory = new CatalogSubCategories('');
                    ?>
                    <table style="text-align: left;">
                        <tr>
                            <td><p class="style_2">Изменить каталог подрубрики:</p></td>
                            <td>
                                <select id="srss">
                                    <?php
                                    for ($i = 0; $i < count($subcategory->id_sub); $i++) {
                                        echo '<option value="' . $subcategory->id_sub[$i] . '">' . $subcategory->name_sub[$i] . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Изменить название подрубрики:</p></td>
                            <td><input id="irss" style="width: 280px;" type="text" value=""></td>  
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="hidden" value="" id="hrss">
                                <button onclick="SaveSubSubRedakt();">Изменить</button>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
            }
            ?>
        </div>

        <div id="edit_catalog_rubrik" class="wind">       <!--Всплывающее окно редактирования рубрики каталога-->
            <a class="close" onclick="CloseWindow('edit_catalog_rubrik');">X</a>
            <br>
            <br>
            <p class="style_7">Редактирование рубрики</p>
            <table style="text-align: left;" id="TableSubEdit">
            </table>
        </div>

        <div class="temno" id="temno"></div>   <!--Всплывающие окна конец-->
    </body>
</html>