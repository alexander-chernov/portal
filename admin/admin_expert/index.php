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
        $where = '';
        $link = '';

        require_once '../inc/configs.php';
        require_once 'inc/classes.php';
        require_once '../admin_map/inc/classes.php';
        require_once 'inc/admin_functions.php';

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
            if (!UserAccess(4)) {
                exit('У вас нет прав заходить в эту категорию!');
            }
        } elseif ($_SESSION['privileges'] == 1) {
            $_SESSION['map_access'] = 1;
        }
        UpdateActivityAdmin();
        CreateTempTables();

        if (!in_array($ShowParamID, array(1, 2, 3))) {
            header($_ENV['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
            header("Location: http://" . _SERVER_ADDRESS . "/page404.php");
        }

        if (isset($_POST['NewExpertSubmit'])) {
            $_POST['NewExpertLogin'] = filter_var($_POST['NewExpertLogin'], FILTER_SANITIZE_EMAIL);
            $_POST['NewExpertPassword'] = md5(filter_var($_POST['NewExpertPassword'], FILTER_SANITIZE_STRIPPED));
            $_POST['NewExpertPassword2'] = md5(filter_var($_POST['NewExpertPassword2'], FILTER_SANITIZE_STRIPPED));
            $_POST['NewExpertTheme'] = filter_var($_POST['NewExpertTheme'], FILTER_SANITIZE_STRIPPED);
            $_POST['NewExpertBrief'] = filter_var($_POST['NewExpertBrief'], FILTER_SANITIZE_STRIPPED);
            $_POST['NewExpertHeader'] = filter_var($_POST['NewExpertHeader'], FILTER_SANITIZE_STRIPPED);
            $_POST['NewExpertDescription'] = filter_var($_POST['NewExpertDescription'], FILTER_SANITIZE_STRIPPED);
            $_POST['NewExpertPhone'] = filter_var($_POST['NewExpertPhone'], FILTER_SANITIZE_STRIPPED);
            $_POST['NewExpertSite'] = CorrectURL($_POST['NewExpertSite']);
            $_POST['NewExpertAddress'] = filter_var($_POST['NewExpertAddress'], FILTER_VALIDATE_INT);
            if (!empty($_POST['NewExpertLogin']) && $_POST['NewExpertPassword'] == $_POST['NewExpertPassword2']) {
                try {
                    $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
                    $mysql->exec('set names utf8');
                    $query0 = $mysql->prepare('SELECT * FROM k_experts WHERE k_e_email=:email');
                    $query0->execute(array(":email" => $_POST['NewExpertLogin']));
                    if ($query0->rowCount() == 0) {
                        $query = $mysql->prepare('INSERT INTO k_experts
                            (k_e_email,k_e_password,k_e_theme,k_e_brief,k_e_header,k_e_description,k_e_phone,k_e_site,k_e_address,k_e_verified,k_e_date,k_e_last_date)
                            VALUES (:email,:pass,:theme,:brief,:header,:descr,:phone,:site,:addr,1,NOW(),NOW())');
                        $query->execute(array(":email" => $_POST['NewExpertLogin'],
                            ":pass" => $_POST['NewExpertPassword2'],
                            ":theme" => $_POST['NewExpertTheme'],
                            ":brief" => $_POST['NewExpertBrief'],
                            ":header" => $_POST['NewExpertHeader'],
                            ":descr" => $_POST['NewExpertDescription'],
                            ":phone" => $_POST['NewExpertPhone'],
                            ":site" => $_POST['NewExpertSite'],
                            ":addr" => $_POST['NewExpertAddress']));
                        $id = $mysql->lastInsertId();
                        $query2 = $mysql->prepare('INSERT INTO k_experts_categories_links (k_ecl_expert_id,k_ecl_category_id) VALUES (:id,:cat)');
                        for ($i = 0; $i < count($_POST['NewExpertCat']); $i++) {
                            $query2->execute(array(":id" => $id, ":cat" => $_POST['NewExpertCat'][$i]));
                        }
                    }
                } catch (PDOException $e) {
                    exit();
                }
            }
        }
        ?>
        <meta name="keywords" content="">
        <meta name="description" content="">
        <link rel="stylesheet" type="text/css" href="../css/style_admin.css">
        <link rel="stylesheet" type="text/css" href="../css/style_wind.css">
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
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
        <script type="text/javascript" src="../js/wind.js"></script>
        <script type="text/javascript" src="js/scripts.js"></script>
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
        <p class="topic">Управление разделом: <span style="color: #ff9c00;">Эксперты</span></p>
        <div class="center_block">
            <div id="menu_2">
                <a href="./?PageType=1">Эксперты</a>
                <a href="./?PageType=2">Банеры</a>
                <a href="./?PageType=3">Рубрики</a>
            </div>

            <?php
            if ($ShowParamID == 1) {
                $expert = new TableExpertsBuild();
                $expert->LoadExperts($page, $where);
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Управление экспертами</span></b><br><br>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img class="img_options" src="../images/add_team.png" title="Добавить Эксперта" alt="" onclick="$('#add_expert').show(500);
                    enableA();">
                                <img class="img_options" src="../images/delete_team.png" title="Удалить выделенного Эксперта" alt="">
                                <img class="img_options" src="../images/aktive_team.png" title="Активировать выделенные Эксперта" alt="">
                                <img class="img_options" src="../images/deactivate_team.png" title="Скрыть выделенные Эксперта" alt="">
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
                                    <td><p class="style_2">По <span style="color: green;">№ эксперта</span>:</p></td>
                                    <td><input type="text" value=""></td>
                                </tr>
                                <tr>
                                    <td><p class="style_2">По <span style="color: green;">нику эксперта</span>:</p></td>
                                    <td><input type="text" value=""></td>
                                </tr>
                                <tr>
                                    <td><p class="style_2">По <span style="color: green;">Статусу</span>:</p></td>
                                    <td><select>
                                            <option>Выберите статус</option>
                                            <option>Активные</option>
                                            <option>Скрытые</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><p class="style_2">По <span style="color: green;">рубрике эксперта</span>:</p></td>
                                    <td><select>
                                            <option>Выберите рубрику</option>
                                            <option>Авто</option>
                                            <option>Активный отдых</option>
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
                    echo $expert->GenerateNavigation($page, $where, $link);
                    ?>
                    <table style="width: 100%; text-align: center;">
                        <tr style="background: #7caed3;">
                            <td colspan="2"><p class="style_5">№ Эксперта</p></td>
                            <td><p class="style_5">Аватар</p></td>
                            <td><p class="style_5">Пользователь</p></td>
                            <td><p class="style_5">Дата</p></td>
                            <td><p class="style_5">Статус</p></td>
                            <td><p class="style_5">Действие</p></td>
                        </tr>
                        <?php
                        for ($a = 0; $a < count($expert->email); $a++) {
                            ?>
                            <tr style="background: #f0f4f4;">
                                <td style="width: 20px;"><input type="checkbox" value=""></td>
                                <td style="width: 80px;"><p class="style_4"><?php echo $expert->id[$a]; ?></p></td>
                                <td style="width: 80px;">
                                    <?php
                                    if ($expert->avatar[$a]) {
                                        echo '<img class="img_ob" src="../' . $expert->avatar[$a] . '" alt="">';
                                    } else {
                                        echo '<img class="img_ob" src="../images/noimage.png" alt="">';
                                    }
                                    ?>
                                </td>
                                <td><p class="style_4"><?php echo $expert->email[$a]; ?></p></td>
                                <td><p class="style_4"><?php echo $expert->regdate[$a]; ?></p></td>
                                <td>
                                    <?php
                                    if ($expert->state[$a] == 1) {
                                        echo '<p class="style_4_1">Активно</p>';
                                    } else {
                                        echo '<p class="style_4_2">Скрыто</p>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ChangeBlockEParams(' . $expert->id[$a] . ');"'; ?>><img src="../images/send_email.png" title="Отправить E-mail эксперту" alt=""></a>
                                    <a class="a_1" <?php echo 'onclick="ShowExpertInfo(' . $expert->id[$a] . ');"'; ?> title="Информация по эксперту"><img src="../images/info.png" alt=""></a>
                                    <a class="a_1" <?php echo 'onclick="ChangeExpertParams(' . $expert->id[$a] . ');"'; ?>><img src="../images/edit.png" title="Редактировать эксперта" alt=""></a>
                                    <!--<a class="a_1" onclick="ChangeAvatarWindow(this);">
                                        <img src="../images/photo.png" title="Редактировать аватар эксперта" alt="">
                                    </a>-->
                                    <a class="a_1" <?php echo 'onclick="PasswordEdit(' . $expert->id[$a] . ')";'; ?>><img src="../images/pass.png" title="Изменить пароль для входа" alt=""></a>
                                    <a class="a_1">
                                        <?php
                                        if($expert->state[$a] == 1) {
                                            ?>
                                        <img src="../images/disable_1.png" title="Скрыть эксперта" alt="<?php echo $expert->id[$a]; ?>" onclick="ChangeState(this);">
                                        <?php
                                        } else {
                                            ?>
                                        <img src="../images/enable.png" title="Отобразить эксперта" alt="<?php echo $expert->id[$a]; ?>" onclick="ChangeState(this);">
                                        <?php
                                        }
                                        ?>
                                    </a>
                                    <a class="a_1">
                                        <?php
                                        if($expert->on_main[$a] == 1) {
                                            ?>
                                        <img src="../images/not_main.png" title="Убрать с главной страницы" alt="<?php echo $expert->id[$a]; ?>" onclick="OnMainPage(this);">
                                        <?php
                                        } else {
                                            ?>
                                        <img src="../images/on_main.png" title="Добавить на главную страницу" alt="<?php echo $expert->id[$a]; ?>" onclick="OnMainPage(this);">
                                        <?php
                                        }
                                        ?>
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="UpExpert(' . $expert->id[$a] . ');"'; ?>><img src="../images/up.png" title="Поднять эксперта" alt=""></a>
                                    <a class="a_1"><img src="../images/delete.png" title="Удалить эксперта" alt="<?php echo $expert->id[$a]; ?>" onclick="DeleteExpert(this);"></a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <?php
                    echo $expert->GenerateNavigation($page, $where, $link);
                    ?>
                </div>

                <div id="add_expert" class="wind_1">       <!--Всплывающее окно Добавить Эксперта-->
                    <a class="close" onclick="CloseWindow('add_expert');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Добавление пользователя как Эксперт</p>
                    <?php
                    $addresses = new AllAddresses(0, '');
                    ?>
                    <form action="./" method="post">
                        <table>
                            <tr>
                                <td><p class="style_2">E-mail (логин):</p></td>
                                <td><input name="NewExpertLogin" type="text" value=""></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Пароль:</p></td>
                                <td><input name="NewExpertPassword" type="password" value=""></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Повторите пароль:</p></td>
                                <td><input name="NewExpertPassword2" type="password" value=""></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Тема эксперта:</p></td>
                                <td><input name="NewExpertTheme" type="text" value=""></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Краткое описание:</p></td>
                                <td><textarea name="NewExpertBrief" rows="10" cols="50" style="width: 200px; height: 90px; resize: none;"></textarea></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Подзаголовок:</p></td>
                                <td><input name="NewExpertHeader" type="text" value=""></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Описание:</p></td>
                                <td><textarea name="NewExpertDescription" rows="10" cols="50" style="width: 200px; height: 90px; resize: none;"></textarea></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Адрес:</p></td>
                                <td>
                                    <select name="NewExpertAddress">
                                        <?php
                                        for ($i = 0; $i < count($addresses->id); $i++) {
                                            echo '<option value="' . $addresses->id[$i] . '">' . $addresses->address[$i] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Телефон:</p></td>
                                <td><input name="NewExpertPhone" type="text" value=""></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Сайт:</p></td>
                                <td><input name="NewExpertSite" type="text" value=""></td>
                            </tr>
                            <tr>
                                <?php
                                $ex_c = new TableExpertsBuild();
                                $ex_c->LoadCategories();
                                ?>
                                <td><p class="style_2">Рубрика эксперта:</p></td>
                                <td>
                                    <?php
                                    for ($i = 0; $i < count($ex_c->all_cat_id); $i++) {
                                        echo '<label style="display: inline-block;"><input name="NewExpertCat[]" type="checkbox" value="' . $ex_c->all_cat_id[$i] . '">' . $ex_c->all_cat[$i] . '</label>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input name="NewExpertSubmit" type="submit" style="float:left;" value="Добавить">
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>

                <div id="edit_expert" class="wind">       <!--Всплывающее окно редактирования Эксперта-->
                    <a class="close" onclick="CloseWindow('edit_expert');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Редактировать Эксперта:</p>
                    <table>
                        <tr>
                            <td><p class="style_2">E-mail (логин):</p></td>
                            <td><input type="text" name="ChangeExEmail" id="ChangeExEmail" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Тема эксперта:</p></td>
                            <td><input type="text" name="ChangeExTheme" id="ChangeExTheme" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Краткое описание:</p></td>
                            <td><textarea rows="10" cols="50" name="ChangeExBrief" id="ChangeExBrief" style="width: 200px; height: 90px; resize: none;"></textarea></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Подзаголовок:</p></td>
                            <td><input type="text" name="ChangeExHeader" id="ChangeExHeader" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Описание:</p></td>
                            <td><textarea rows="10" cols="50" name="ChangeExDescription" id="ChangeExDescription"  style="width: 200px; height: 90px; resize: none;"></textarea></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Адрес:</p></td>
                            <td>
                                <select name="ChangeExAddress" id ="ChangeExAddress">
                                    <?php
                                    for ($i = 0; $i < count($addresses->id); $i++) {
                                        echo '<option value="' . $addresses->id[$i] . '">' . $addresses->address[$i] . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Телефон:</p></td>
                            <td><input type="text" name="ChangeExPhone" id="ChangeExPhone" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Сайт:</p></td>
                            <td><input type="text" name="ChangeExSite" id="ChangeExSite" value=""></td>
                        </tr>
                        <tr>
                            <td id="ExpertCatSelect" colspan="2">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="hidden" name="ChangeExID" id="ChangeExID" value="">
                                <input type="button" name="ChangeExpert" style="float:right;" value="Изменить" onclick="SaveExpertParams();">
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
                                <button onclick="mailAdminSend();" style="float:right;">Отправить</button>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="info_expert" class="wind">       <!--Всплывающее окно Информации по Эксперту-->
                    <a class="close" onclick="CloseWindow('info_expert');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Информация по эксперту</p>
                    <table>
                        <tr>
                            <td><p class="style_2">№ Эксперта:</p></td>
                            <td><p class="style_4_2" id="InfoID"></p></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Дата размещения:</p></td>
                            <td><p class="style_4_4" id="IndoDate"></p></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Время действия эксперта:</p></td>
                            <td><p class="style_4_4" id="InfoDaysLast"></p></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Тема:</p></td>
                            <td><p class="style_4_1" id="InfoTheme"></p></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Телефон:</p></td>
                            <td><p class="style_4_4" id="InfoPhone"></p></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Адрес:</p></td>
                            <td><p class="style_4_4" id="InfoAddress"></p></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">E-mail:</p></td>
                            <td><p class="style_4_4" id="InfoEmail"></p></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Сайт Эксперта:</p></td>
                            <td><p class="style_4_4" id="InfoSite"></p></td>
                        </tr>
                    </table>
                </div>

                <div id="pass_expert" class="wind">       <!--Всплывающее окно изменения логина и пароля для Эксперта-->
                    <a class="close" onclick="CloseWindow('pass_expert');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Изменение пароля Эксперта</p>
                    <table>
                        <tr>
                            <td><p class="style_2">Пароль:</p></td>
                            <td><input type="password" id="Pass1" value=""></td>
                        </tr>
                        <tr>
                            <td><p class="style_2">Повторите пароль:</p></td>
                            <td><input type="password" id="Pass2" onkeyup="PasswordCorrect();" value=""></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="hidden" value="" id="PasswID">
                                <button style="float: left;" onclick="PassSave();">Изменить</button>
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
                <div class="block_content_1"><b><span style="color: blue;">Банеры страницы Эксперты</span></b><br><br>
                    <div class="baners">
                        <table border="1">
                            <tr>
                                <td colspan="2"><p class="style_1">Банеры страницы Эксперты</p></td>
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
                $categories = new TableExpertsBuild();
                $categories->LoadCategories();
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Добавление и удаление рубрик Экспертов</span></b><br><br>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img class="img_options" src="../images/add_team.png" title="Добавить рубрику" alt="" onclick="document.getElementById('add_new_rub').style.display = 'block';
                    enableA();">
                                <img class="img_options" src="../images/delete_team.png" title="Удалить выделенные рубрики" alt="">
                            </td>
                        </tr>
                    </table>
                    <table style="width: 100%; text-align: center;">
                        <tr style="background: #7caed3;">
                            <td><p class="style_5">Наименование рубрики</p></td>
                            <td><p class="style_5">Экспертов в рубрике</p></td>
                            <td><p class="style_5">Действие</p></td>
                        </tr>
                        <?php
                        for ($i = 0; $i < count($categories->all_cat_id); $i++) {
                            ?>
                            <tr style="background: #f0f4f4;">
                                <td><p class="style_4"><?php echo $categories->all_cat[$i]; ?></p></td>
                                <td><p class="style_4"><?php echo $categories->all_cat_count[$i]; ?></p></td>
                                <td>
                                    <a class="a_1" onclick="CategoriesRedakt(this);">
                                        <img src="../images/edit.png" title="Редактировать название рубрики" alt="<?php echo $categories->all_cat_id[$i]; ?>">
                                    </a>
                                    <a class="a_1" onclick="DeleteCategoryExp(this);">
                                        <img src="../images/delete.png" title="Удалить рубрику" alt="<?php echo $categories->all_cat_id[$i]; ?>">
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
        </div>


        <div id="avatar_expert" class="wind">       <!--Всплывающее окно редактировать Аватарку Эксперта-->
            <a class="close" onclick="CloseWindow('avatar_expert');">X</a>
            <br>
            <br>
            <p class="style_7">Аватарка Эксперта</p>
            <table>
                <tr style="background: #f0f4f4;">
                    <td style="width: 80px;"><img class="img_ob" src="images/expert_1.jpg" alt=""></td>
                    <td style="width: 150px;"><a><img src="../images/delete.png" title="Удалить фото" alt=""></a></td>
                </tr>
                <tr>
                    <td><p class="style_2">Изменить Аватар:</p></td>
                    <td><input type="file" name=""></td>
                </tr>
                <tr>
                    <td colspan="2"><button style="float:left;">Сохранить</button></td>
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
                    <td><input type="text" value=""></td>
                </tr>
                <tr>
                    <td colspan="2"><button style="float: left;">Добавить</button></td>
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
                        <button onclick="CategoriesRedaktSubmit();" style="float: left;">Изменить</button>
                    </td>
                </tr>
            </table>
        </div>

        <div class="temno" id="temno"></div>   <!--Всплывающие окна конец-->
    </body>
</html>