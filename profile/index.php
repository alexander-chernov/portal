<?php
define('TOMSKLINE', 1);
session_start();
require_once '../inc/configs.php';
if (isset($_SESSION['login_e'])) {
    header($_ENV['SERVER_PROTOCOL'] . " 400 WRONG USER", true, 401);
    header("Location: http://" . _SERVER_ADDRESS . "/profile/prof_expert.php");
    exit();
}
if (!isset($_SESSION['login'])) {
    if (isset($_COOKIE['login'])) {
        $_SESSION['login'] = $_COOKIE['login'];
        $_SESSION['password'] = $_COOKIE['password'];
    } else {
        $host = $_SERVER['HTTP_HOST'];
        $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $extra = '../warning.php';
        header("Location: http://$host$uri/$extra");
    }
}

//Меняем категории
if (isset($_GET['PageType'])) {
    if (is_int($_GET['PageType'])) {
        $_GET['PageType'] = filter_var($_GET['PageType'], FILTER_VALIDATE_INT);
    } else {
        $_GET['PageType'] = filter_var($_GET['PageType'], FILTER_SANITIZE_STRIPPED);
    }
    $ShowParamID = $_GET['PageType'];
} else {
    $ShowParamID = 1;
}

require_once '../inc/functions.php';
require_once 'inc/classes.php';
require_once 'inc/functions.php';
require_once '../admin/admin_gl/inc/classes.php';

if (YourIPBanned()) {
    header($_ENV['SERVER_PROTOCOL'] . " 401 IP BAN", true, 401);
    header("Location: http://" . _SERVER_ADDRESS . "/pageBan.php");
}

require_once '../admin/inc/functions.php';
CreateTempTables();

try {
    $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
    $mysql->exec('set names utf8');
    $query = $mysql->prepare('SELECT * FROM k_users WHERE k_ku_login=:login AND k_ku_password=:password');
    $query->execute(array(":login" => $_SESSION['login'], ":password" => $_SESSION['password']));
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($query->rowCount() > 0) {
        $_SESSION['id'] = $result['k_ku_id'];
        $_SESSION['privileges'] = $result['k_u_privileges'];
        $_SESSION['owner'] = $result['k_u_owner'];

        if ($_SESSION['owner'] == 1) {
            header($_ENV['SERVER_PROTOCOL'] . " 400 WRONG USER", true, 401);
            header("Location: http://" . _SERVER_ADDRESS . "/profile/prof_agent.php");
            exit();
        }
        /*if ($_SESSION['owner'] != 1 && $_SESSION['owner'] != 2) {
            header($_ENV['SERVER_PROTOCOL'] . " 400 WRONG USER", true, 401);
            header("Location: http://" . _SERVER_ADDRESS . "/warning.php");
            exit();
        }*/
        if ($_SESSION['owner'] == 2 || $_SESSION['owner'] == 3) {
            $user_packets = new UserPackets($_SESSION['id']);
            $packetsForUser = new PacketsForUser($_SESSION['owner']);
        }
    } else {
        unset($_SESSION);
    }

} catch (PDOException $e) {
    echo $e->getMessage();
    exit();
}

?>
<!DOCTYPE HTML>
<html lang="ru">
<head>
    <title>TOMSK-LINE.RU. Профиль.</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <?php

    $user = new User();
    $user->LoadUser($_SESSION['id']);
    if ($_SESSION['owner'] == 2 || $_SESSION['owner'] == 3) {
        $user->immo_monthly = $user_packets->current_remain;
    }
    $banners = new BannersAll();

    $content_page = new PageContent(18);

    $page = 1;
    if (isset($_GET['PageIndex'])) {
        $page = filter_var($_GET['PageIndex'], FILTER_VALIDATE_INT);
    }

    ?>
    <meta name="keywords" content="">
    <meta name="description" content="">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <link rel="stylesheet" type="text/css" href="../css/style_wind.css">
    <link rel="stylesheet" type="text/css" href="../css/show_img.css">

    <link rel="stylesheet" type="text/css" href="css/kabinet_users.css">
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script type="text/javascript" src="../js/wind.js"></script>
    <script type="text/javascript" src="js/scripts.js"></script>
    <!--Отловить размер окна меню-->
    <script type="text/javascript">
        function ResizeMenu() {
            if ($('#show_menu').outerWidth() > 1250) {
                $('#show_menu_1').show(100);
                $('#show_menu_2').hide(100);
            } else {
                $('#show_menu_1').hide(100);
                $('#show_menu_2').show(100);
            }
            var w = Math.round($('.reklama').width() / 2 - 60);
            $("#banner1").width(w);
            $("#banner2").width(w);
            $("#banner3").width(w);
            $("#banner4").width(w);
        }
        $(window).resize(function () {
            ResizeMenu();
        });
        $(window).ready(function () {
            ResizeMenu();
        });
    </script>
    <!--Отловить размер окна меню-->
</head>
<body>
<?php
require_once '../inc/header.php';
?>
<div class="center_all_block">
<div class="left_all_block">
<div class="shapka_bloka">

    <a class="name_shapka">Кабинет пользователя</a>

</div>
<div class="obveden_block">
<?php
if ($ShowParamID == 1) {
    $box = new Messages();
    $box->MessagesInbox();
    $m_in = $box->count;
    $box->MessagesOutbox();
    $m_out = $box->count;
    ?>
    <div id="userform_1" class="block_content_1">   <!--Меню кабинета пользователя-->
        <div id="name_login_kabinet">

            <table>
                <tr>
                    <td><span class="tit_text">Логин:</span></td>
                    <td><span class="tit_text"><b><?php echo $user->login; ?></b></span></td>
                </tr>
                <tr>
                    <td><span class="tit_text">ФИО:</span></td>
                    <td><span
                            class="tit_text"><b><?php echo $user->lname . ' ' . $user->fname . ' ' . $user->oname; ?></b></span>
                    </td>
                </tr>
                <tr>
                    <td><span class="tit_text">Тип:</span></td>
                    <td>
                        <span class="tit_text">
                            <b>
                                <?php
                                if ($_SESSION['owner'] == 2) {
                                    echo 'Застройщик';
                                }
                                if ($_SESSION['owner'] == 3) {
                                    echo 'Посредник';
                                }
                                if ($_SESSION['owner'] == 4) {
                                    echo 'Собственник';
                                }
                                ?>
                            </b>
                        </span>
                    </td>
                </tr>

            </table>
            <br>

            <p class="conteiner"><a class="style_kabinet_2" href="./?PageType=2">Изменить контактную информацию</a></p>

            <p class="conteiner"><a class="style_kabinet_2" href="./?PageType=3">Изменить пароль</a></p>

            <div class="obiavlenia_block">
                <p class="name_obiavlenia_block">Мои объявления</p>

                <p class="conteiner"><a class="style_kabinet_2" href="./?PageType=4">Объявления раздела Недвижимость</a><span
                        class="style_spec"><?php echo $user->immo_count; ?></span></p>

                <p class="conteiner"><a class="style_kabinet_2" href="./?PageType=5">Объявления раздела Фото
                        объявления</a><span class="style_spec"><?php echo $user->photo_count; ?></span></p>

                <p class="conteiner"><a class="style_kabinet_2" href="./?PageType=6">Объявления раздела Работа</a><span
                        class="style_spec"><?php echo $user->job_count; ?></span></p>
                <!--<p class="conteiner"><a class="style_kabinet_2" href="./?PageType=22">Организации</a><span
                        class="style_spec"><?php echo $user->organizations_count; ?></span></p>-->
            </div>
            <div class="obiavlenia_block">
                <p class="name_obiavlenia_block">Личные сообщения</p>

                <p class="conteiner"><a class="style_kabinet_2" href="./?PageType=20">Входящие</a><span
                        class="style_spec"><?php echo $m_in; ?></span></p>

                <p class="conteiner"><a class="style_kabinet_2" href="./?PageType=21">Исходящие</a><span
                        class="style_spec"><?php echo $m_out; ?></span></p>
            </div>
        </div>
    </div>
<?php
}
?>

<?php
if ($ShowParamID == 2) {
    ?>
    <div class="block_content_1">
        <div>
            <p class="style_kabinet_2_1">Редактировать контактную информацию</p><br>

            <form action="./" method="POST">
                <table>
                    <tr>
                        <td><p class="style_wind_3_1">Изменить фамилию:</p></td>
                        <td><input class="all_inp" type="text" name="UserSecName" value="<?php echo $user->lname; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td><p class="style_wind_3_1">Изменить имя:</p></td>
                        <td><input class="all_inp" type="text" name="UserName" value="<?php echo $user->fname; ?>"></td>
                    </tr>
                    <tr>
                        <td><p class="style_wind_3_1">Изменить отчество:</p></td>
                        <td><input class="all_inp" type="text" name="UserOName" value="<?php echo $user->oname; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td><p class="style_wind_3_1">Изменить E-mail:</p></td>
                        <td><input class="all_inp" type="text" name="UserEmail" value="<?php echo $user->email; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input class="act_2" name="SaveUser" type="submit" value="Сохранить изменения">
                        </td>
                    </tr>
                </table>
            </form>
            <a class="act_2_2" href="./?PageType=1">&laquo; В кабинет</a>
        </div>
    </div>
<?php
}
?>

<?php
if ($ShowParamID == 3) {
    ?>
    <script type="text/javascript">
        function PasswordCompare() {
            if ($('#UserPassword').val() === $('#UserPassword2').val()) {
                $('#UserPassword').css('background', '#00ff00');
                $('#UserPassword2').css('background', '#00ff00');
            } else {
                $('#UserPassword').css('background', '#ff9999');
                $('#UserPassword2').css('background', '#ff9999');
            }
        }
        function PasswordSave() {
            if ($('#UserPassword').val().length < 5) {
                alert('\u041fароль слишком короткий!');
                return false;
            }
            if ($('#UserPassword').val() === $('#UserPassword2').val()) {
                return true;
            } else {
                alert('\u041fароли не совпадают!');
            }
            return false;
        }
    </script>
    <div class="block_content_1">
        <p class="style_kabinet_2_1">Изменение пароля</p><br>

        <form action="./" method="POST" onsubmit="return PasswordSave();
                    return false;">
            <table>
                <tr>
                    <td><p class="style_wind_3_1">Введите новый пароль:</p></td>
                    <td><input class="all_inp" type="password" id="UserPassword" name="UserPassword" value=""></td>
                </tr>
                <tr>
                    <td><p class="style_wind_3_1">Повторите пароль:</p></td>
                    <td><input class="all_inp" type="password" id="UserPassword2" onkeyup="PasswordCompare();"
                               name="UserPassword2" value=""></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input class="act_2" name="SavePassword" type="submit" value="Сохранить изменения">
                    </td>
                </tr>
            </table>
        </form>
        <a class="act_2_2" href="./?PageType=1">
            &laquo; В кабинет
        </a>
    </div>
<?php
}
if ($ShowParamID == 4) {
    $user_ads = new AdSelf($_SESSION['id'], $page);
    $user_packages = new UserPackages($_SESSION['id']);
    $immovables_packages = new ImmovablesPackges($_SESSION['id']);
    ?>
    <div class="block_content_1">
        <p class="style_kabinet_2_1">Все объявления недвижимости</p><br>
                                <span>
                                    <a class="add_rfbinet_obiavlenie" style="float: right;" title="Купить дополнительное объявление" href="../payment.php?pay&realty&additional">
                                        Купить дополнительное объявление
                                    </a>
                                    <?php
                                    //echo 'monthly: '.$user->immo_monthly.'<br>';
                                    //echo 'remain: '.$user->immo_remain.'<br>';
                                    if ($user->immo_monthly > 0 || $user->immo_remain > 0) {
                                        ?>
                                        <a class="add_rfbinet_obiavlenie" style="float: right;" title="Добавить объявление" href="./?PageType=7">
                                            Добавить объявление
                                        </a>
                                        <?php
                                    }
                                    ?>
                                    <a class="act_2" href="./?PageType=1">
                                        Назад
                                    </a>
                                </span>
        <br><br>
        <table class="table_add_obiavlenia" border="1">
            <tr>
                <td colspan="3"><b class="style_kabinet_6">Приобретённые платные пакеты объявлений</b></td>
            </tr>
            <tr>
                <td class="td_name_table"><p class="style_kabinet_3">Пакет</p></td>
                <td class="td_name_table"><p class="style_kabinet_3">Остаток</p></td>
                <td class="td_name_table"><p class="style_kabinet_3">Действие</p></td>
            </tr>
            <tr>
                <td><p class="znak_15">Быстрая продажа</p></td>
                <td>
                    <p class="znak_15">
                        <?php
                        echo $user_packages->num[1];
                        ?>
                    </p>
                </td>
                <td>
                    <a class="no_line" title="Купить пакет" href="../payment.php?pay&realty&package=2">
                        <img src="../images/dollar.png" alt="">
                    </a>
                </td>
            </tr>
            <tr>
                <td><p class="znak_15">Турбо продажа</p></td>
                <td>
                    <p class="znak_15">
                        <?php
                        if ($user_packages->num[0] != '') {
                            echo $user_packages->num[0];
                        } else {
                            echo '0';
                        }
                        ?>
                    </p>
                </td>
                <td>
                    <a class="no_line" title="Купить пакет" href="../payment.php?pay&realty&package=1">
                        <img src="../images/dollar.png" alt="">
                    </a>
                </td>
            </tr>
        </table>
        <?php
        if ($_SESSION['owner'] == 2 || $_SESSION['owner'] == 3) {
            ?>
            <br>
            <table class="table_add_obiavlenia" border="1">
                <tr>
                    <td colspan="3"><b class="style_kabinet_6">Платные пакеты пользователя</b></td>
                </tr>
                <tr>
                    <td class="td_name_table"><p class="style_kabinet_3">Объявлений в месяц</p></td>
                    <td class="td_name_table"><p class="style_kabinet_3">Цена</p></td>
                    <td class="td_name_table"><p class="style_kabinet_3">Действие</p></td>
                </tr>
                <?php
                for ($i = 0; $i < count($packetsForUser->id); $i++) {
                    ?>
                    <tr>
                        <td>
                            <p class="znak_15">
                                <?php
                                echo $packetsForUser->total[$i];
                                ?>
                            </p>
                        </td>
                        <td>
                            <p class="znak_15">
                                <?php
                                echo $packetsForUser->price[$i];
                                ?>
                            </p>
                        </td>
                        <td>
                            <?php
                            if ($packetsForUser->price[$i] > 0) {
                                ?>
                                <a class="no_line" title="Купить пакет" href="../payment.php?pay&realty&packet=<?php echo $packetsForUser->id[$i]; ?>">
                                    <img src="../images/dollar.png" alt="">
                                </a>
                                <?php
                            } else {
                                ?>
                                <a class="no_line" title="Использовать пакет">
                                    <img src="../images/enable.png" alt="">
                                </a>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <br>
            <table class="table_add_obiavlenia" border="1">
                <tr>
                    <td colspan="3"><b class="style_kabinet_6">Приобретённые платные пакеты пользователя</b></td>
                </tr>
                <tr>
                    <td class="td_name_table"><p class="style_kabinet_3">Пакет (объявлений/цена)</p></td>
                    <td class="td_name_table"><p class="style_kabinet_3">Остаток</p></td>
                    <td class="td_name_table"><p class="style_kabinet_3">Период действия пакета</p></td>
                </tr>
                <?php
                for ($i = 0; $i < count($user_packets->id); $i++) {
                    ?>
                    <tr <?php if ($i == 0 && $user_packets->current_remain > 0) echo 'style="background: #aaffaa;"'; ?>>
                        <td>
                            <p class="znak_15">
                                <?php
                                echo $user_packets->total[$i] . ' / ' . $user_packets->price[$i] . ' рублей';
                                ?>
                            </p>
                        </td>
                        <td>
                            <p class="znak_15">
                                <?php
                                echo $user_packets->remain[$i] . ' / ' . $user_packets->total[$i];
                                ?>
                            </p>
                        </td>
                        <td>
                            <p class="znak_15">
                                <?php
                                echo $user_packets->start_date[$i];
                                ?>
                            </p>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <?php
        }
        ?>
        <br>
        <table class="table_add_obiavlenia" border="1">
            <tr>

                <td colspan="7"><b class="style_kabinet_6">Мои объявления<span
                            class="style_kabinet_6_1"><?php echo $user->immo_count; ?></span></b></td>
            </tr>
            <tr>
                <td colspan="7">
                    <b class="style_kabinet_6">
                        Осталось
                        <span class="style_kabinet_6_1">
                        <?php echo $user->immo_monthly . ' + ' . $user->immo_remain; ?>
                        </span>
                    </b>
                </td>
            </tr>
            <tr>
            <td class="td_name_table"><p class="style_kabinet_3">№</p></td>
                <td class="td_name_table"><p class="style_kabinet_3">Фото</p></td>
                <td class="td_name_table"><p class="style_kabinet_3">Текст объявления</p></td>
                <td class="td_name_table"><p class="style_kabinet_3">Дата размещения</p></td>
                <td class="td_name_table"><p class="style_kabinet_3">Дата завершения</p></td>
                <td class="td_name_table"><p class="style_kabinet_3">Статус</p></td>
                <td class="td_name_table"><p class="style_kabinet_3">Действие</p></td>
            </tr>
            <?php
            for ($i = 0; $i < count($user_ads->id); $i++) {
                $style = '#ffcccc';
                $state = 'На проверке';
                if ($user_ads->state[$i] == 1) {
                    $style = '#ffffff';
                    $state = 'Опубликовано';
                }
                ?>
                <tr style="background: <?php echo $style; ?>;">
                    <td><p class="znak_15"><?php echo $user_ads->id[$i]; ?></p></td>
                    <td>
                        <?php
                        //echo $user_ads->photo_url[$i];
                        $file_name = str_replace('images/', '../admin/images/', $user_ads->photo_url[$i]);
                        $file_name = str_replace('video/', '../video/', $file_name);
                        if ($user_ads->photo_url[$i] && file_exists($_SERVER['DOCUMENT_ROOT'].$file_name)) {
                            echo '<img class="photo_obiavlenia" src="' . $file_name . '" alt="">';
                        } else {
                            echo '<img class="photo_obiavlenia" src="../images/noimage.png" alt="">';
                        }
                        ?>
                    </td>
                    <td><p class="znak_15"><?php echo $user_ads->description[$i]; ?></p></td>
                    <td><p class="znak_15"><?php echo $user_ads->date_reg[$i]; ?></p></td>
                    <td><p class="znak_15"><?php echo $user_ads->date_end[$i]; ?></p></td>
                    <td><p class="znak_15"><?php echo $state; ?></p></td>
                    <td>
                        <?php
                        if (!in_array($user_ads->id[$i], $user_ads->special)) {
                            ?>
                            <a class="no_line" <?php echo 'href="../payment.php?pay&realty&action=1&id=' . $user_ads->id[$i] . '"'; ?> title="Закрепление в VIP предложениях">
                                <img src="../admin/images/spec_1.png" alt="">
                            </a>
                            <?php
                        } else {
                            ?>
                            <a class="no_line" title="Убрать из VIP предложений">
                                <img src="../admin/images/spec_2.png" alt="">
                            </a>
                            <?php
                        }
                        ?>
                        <?php
                        if ($user_ads->lock_start[$i] == '') {
                            ?>
                            <a class="no_line" <?php echo 'href="../payment.php?pay&realty&action=2&id=' . $user_ads->id[$i] . '"'; ?> title="Закрепить">
                                <img src="../admin/images/lock.png" alt="">
                            </a>
                            <?php
                        } else {
                            ?>
                            <a class="no_line" title="Открепить">
                                <img src="../admin/images/unlock.png" alt="">
                            </a>
                            <?php
                        }
                        ?>
                        <?php
                        if ($user_ads->color[$i] != 1) {
                            ?>
                            <a class="no_line" <?php echo 'href="../payment.php?pay&realty&action=3&id=' . $user_ads->id[$i] . '"'; ?> title="Поднять и выделить цветом">
                                <img src="../images/down_1.png" alt="">
                            </a>
                            <?php
                        } else {
                            ?>
                            <a class="no_line" title="Убрать выделение цветом">
                                <img src="../admin/images/no_light.png" alt="">
                            </a>
                            <?php
                        }
                        ?>

                        <a class="no_line" <?php echo 'href="./?PageType=change&id=' . $user_ads->id[$i] . '"'; ?>
                           title="Редактировать">
                            <img src="../images/edit.png" alt="">
                        </a>
                        <?php
                        if (in_array($user_ads->id[$i], $immovables_packages->immo)) {
                            ?>
                            <a onclick="PackageUse(this);" class="no_line" title="Управление пакетами">
                                <img src="../images/packet_plus.png" alt="<?php echo $user_ads->id[$i]; ?>">
                            </a>
                            <?php
                        } else {
                            ?>
                            <a onclick="PackageUse(this);" class="no_line" title="Управление пакетами">
                                <img src="../images/packet.png" alt="<?php echo $user_ads->id[$i]; ?>">
                            </a>
                            <?php
                        }
                        ?>
                        <a onclick="DeleteAd(this);" class="no_line" title="Удалить объявление">
                            <img src="../images/delete_team.png" alt="<?php echo $user_ads->id[$i]; ?>">
                    </td>
                </tr>
            <?php
            }
            ?>
        </table>
        <?php
                                        $user_ads->GenerateNavigation($page, '', '');
                                        ?>
    </div>
    <div id="add_package" class="wind">
                                    <a class="close" onclick="CloseWindow('add_package');">X</a>
                                    <br>
                                    <br>
                                    <p class="style_7">Управление пакетами объявления</p>
                                    <table border="1">
                                        <tr>
                                            <td>
                                                <p class="style_2">Пакет</p>
                                            </td>
                                            <td>
                                                <p class="style_2">Закрепление</p>
                                            </td>
                                            <td>
                                                <p class="style_2">Поднятие</p>
                                            </td>
                                            <td>
                                                <p class="style_2">Выделение цветом</p>
                                            </td>
                                            <td>
                                                <p class="style_2">VIP</p>
                                            </td>
                                            <td>
                                                <p class="style_2">Действие</p>
                                            </td>
                                        </tr>
                                        <tr id="quick_sell">
                                            <td>
                                                <p class="style_2">Быстрая продажа</p>
                                            </td>
                                            <td>
                                                <p class="style_2"></p>
                                            </td>
                                            <td>
                                                <p class="style_2"></p>
                                            </td>
                                            <td>
                                                <p class="style_2"></p>
                                            </td>
                                            <td>
                                                <p class="style_2"></p>
                                            </td>
                                            <td>
                                                <a onclick="AddPackageToImmo(2);" class="no_line" title="">
                                                    <img src="../admin/images/add_team.png" alt="">
                                                </a>
                                            </td>
                                        </tr>
                                        <tr id="turbo_sell">
                                            <td>
                                                <p class="style_2">Турбо продажа</p>
                                            </td>
                                            <td>
                                                <p class="style_2"></p>
                                            </td>
                                            <td>
                                                <p class="style_2"></p>
                                            </td>
                                            <td>
                                                <p class="style_2"></p>
                                            </td>
                                            <td>
                                                <p class="style_2"></p>
                                            </td>
                                            <td>
                                                <a onclick="AddPackageToImmo(1);" class="no_line" title="">
                                                    <img src="../admin/images/add_team.png" alt="">
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

<?php
}
?>

<?php
if ($ShowParamID == 'add' || $ShowParamID == 'change') {
    ?>

    <script type="text/javascript" src="js/ajaxupload.3.5.js"></script>
    <script type="text/javascript">
                        $(function() {
                            var btnUpload = $('#PhotoUpload');
                            var status = $('#statusNews');
                            new AjaxUpload(btnUpload, {
                                action: 'upload-file.php',
                                name: 'uploadfile',
                                onChange: function(file, extension) {
                                    $(document).on('change', 'input[name="uploadfile"]', function(e) {
                                        var files = e.target.files;
                                        var size = files[0].size / (1024 * 1024);
                                        if (size > 20) {
                                            alert('Максимальный размер файла 80Мб!');
                                            return false;
                                        }
                                    });
                                },
                                onSubmit: function(file, ext) {
                                    if (!(ext && /^(jpg|png|jpeg|gif|avi|mp4|3gp|flv|mov)$/.test(ext))) {
                                        // extension is not allowed
                                        status.text('Разрешена загрузка только: jpg, png, jpeg, gif, avi, mp4, 3gp, flv.');
                                        return false;
                                    }
                                    status.html('<span style="padding: 0px 10px 5px 10px;">Файл загружается на сервер...</span>');
                                    $('#video_load').fadeIn(500);
                                    enableA();
                                },
                                onComplete: function(file, response) {
                                    /*
                                    alert(response);
                                    if (response.toLowerCase().match('/jpg|jpeg/')) {
                                        alert('match');
                                    }
                                    if (response.toLowerCase().match('/admin/')) {
                                        alert('match2');
                                    }
*/
                                    status.text('');
                                    if (response === "error") {
                                        $('#video_load').fadeOut(500);
                                        disableA();
                                        return false;
                                    }
                                    if (response === 'duration') {
                                        $('#video_load').fadeOut(500);
                                        disableA();
                                        alert('Видео превышает максимальную длительность!');
                                    }
                                    if (response.toLowerCase().match('/jpg|jpeg/')
                                        || response.toLowerCase().match('/admin/')) {

                                        $('#video_load').fadeOut(500);
                                        disableA();
                                        status.text('Успешно загружено!');
                                        var img = $('<img>', {
                                            'src': response,
                                            'alt': '',
                                            'style': 'width: 100px; display: inline-block; vertical-align: top; margin: 5px;'
                                        });
                                        var block = $('.team_style:contains("Добавить фотографию или видео")');
                                        var span = $('<span>', {'class': 'ad_f'});
                                        span.appendTo(block);
                                        img.appendTo(span);
                                        var div = $('<div>', {'class': 'img_block'});
                                        var a_main = $('<a>', {'title': 'Сделать Главной', 'class': 'no_line', 'onclick': 'SetMainPhoto(this);'});
                                        var img_main = $('<img>', {'alt': '', 'src': '../images/prioritet.png'});
                                        var a_del = $('<a>', {'title': 'Удалить фото', 'class': 'no_line', 'onclick': 'DeleteThisPhoto(this);'});
                                        var img_del = $('<img>', {'alt': '', 'src': '../images/delete.png'});
                                        div.insertAfter(img);
                                        a_main.appendTo(div);
                                        img_main.appendTo(a_main);
                                        a_del.insertAfter(a_main);
                                        img_del.appendTo(a_del);
                                        $('<input>', {'type': 'hidden', 'value': response, 'name': 'images[]'}).appendTo(span);
                                        LoadImmovablePhoto(response);
                                    }
                                }
                            });
                        });
                        $(document).ready(function() {
                            DistrMass($('#final_address').val());
                            if ($('#final_address').val() !== "") {
                                $('#address_input').css('background', '#b1e0ff');
                            }
                            RequireInput($('input[name="price"]'));
                            RequireInput($('input[name="contact_name"]'));
                            RequireInput($('input[name="contacts"]'));
                            $('#address_input').keyup(function(e) {
                                if (e.keyCode === 37 || e.keyCode === 38 || e.keyCode === 39 || e.keyCode === 40 || e.keyCode === 13) {
                                    if (e.keyCode === 40) {
                                        if ($('#address_choise td[class="addr_link_2"]').length === 0) {
                                            $('#address_choise td[class="addr_link"]').first().attr('class', 'addr_link_2');
                                        } else {
                                            index = $('#address_choise td').index($('#address_choise td[class="addr_link_2"]'));
                                            $('#address_choise td').attr('class', 'addr_link');
                                            $('#address_choise td:eq(' + (index + 1) + ')').attr('class', 'addr_link_2');
                                        }
                                    }
                                    if (e.keyCode === 38) {
                                        if ($('#address_choise td[class="addr_link_2"]').length === 0) {
                                            $('#address_choise td[class="addr_link"]').first().attr('class', 'addr_link_2');
                                        } else {
                                            index = $('#address_choise td').index($('#address_choise td[class="addr_link_2"]'));
                                            $('#address_choise td').attr('class', 'addr_link');
                                            $('#address_choise td:eq(' + (index - 1) + ')').attr('class', 'addr_link_2');
                                        }
                                    }
                                    if (e.keyCode === 13) {
                                        if ($('#address_choise td[class="addr_link_2"]').length !== 0) {
                                            ChangeAddr($('#address_choise td[class="addr_link_2"] a'));
                                        }
                                    }
                                } else {
                                    SearchAddr($('#address_input'));
                                }
                            });
                        });
                        $(document).ready(function() {
                            $('#submit_image').mouseover(function() {
                                $('#form_save').removeAttr('onsubmit');
                            });
                            $('#submit_image').mouseout(function() {
                                $('#form_save').attr('onsubmit', 'return false;');
                            });
                        });
    </script>
    <div class="block_content_1">   <!--ФОРМЫ ДОБАВЛЕНИЯ и РЕДАКТИРОВАНИЯ (Продам Квартиру/Комнату)-->
    <a class="act_2" href="./?PageType=1">
        &laquo; В кабинет
    </a>
    <a class="act_2" href="./?PageType=4">
        &laquo; К объявлениям
    </a>
    <?php
    if ($ShowParamID != 'change') {
        ?>
        <a class="act_2" href="./?PageType=7">
            &laquo; Назад
        </a>
    <?php
    }
    ?>
    <br><br>
    <?php
    if (($ShowParamID == 'add' && ($user->immo_monthly > 0 || $user->immo_remain > 0)) || $ShowParamID == 'change') {
        ?>
        <form id="form_save" action="posts.php" method="post" enctype="multipart/form-data" onsubmit="return false;" autocomplete="off">
            <div class="style_form">
                <?php
                if ($ShowParamID == 'change') {
                    if (isset($_GET['id'])) {
                        $_GET['id'] = filter_var($_GET['id'], FILTER_VALIDATE_INT);
                    } else {
                        $_GET['id'] = filter_var($_GET['Action'], FILTER_VALIDATE_INT);
                    }
                    if ($_GET['id']) {
                        try {
                            $q = $mysql->prepare('SELECT kise.*, kshn.k_shn_house_num, ks.k_s_name
                            FROM k_immovables_sell AS kise
                            LEFT JOIN k_streets_house_nums AS kshn ON (kshn.k_shn_id = kise.k_isf_address)
                            LEFT JOIN k_streets AS ks ON (ks.k_s_id = kshn.k_shn_street_id)
                            WHERE k_isf_id=:id AND k_isf_user_id=:user
                            LIMIT 1');
                            $q->execute(array(':id' => $_GET['id'], ":user" => $_SESSION['id']));
                            $r = $q->fetch(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                            exit();
                        }
                        $Immo_type = $r['k_isf_subcategory'];
                        $immovable_type = $r['k_isf_immovable_type'];
                        $address_choise = $r['k_isf_address'];
                        if (preg_match('/(###)/', $r['k_s_name'])) {
                            $street = explode('###', $r['k_s_name']);
                            $house = explode('###', $r['k_shn_house_num']);
                            $address_input = $street[0] . ' ' . $house[0] . ' / ' . $street[1] . ' ' . $house[1];
                        } else {
                            $address_input = $r['k_s_name'] . ' ' . $r['k_shn_house_num'];
                        }
                        $newsec = $r['k_isf_new'];
                        $material = $r['k_isf_material'];
                        $rooms = $r['k_isf_rooms'];
                        $floor = $r['k_isf_floor'];
                        $floor_all = $r['k_isf_floor_all'];
                        $eq = $r['k_isf_eq'];
                        $area_all = $r['k_isf_area_all'];
                        $area_live = $r['k_isf_area_live'];
                        $area_land = $r['k_isf_area_land'];
                        $area_kit = $r['k_isf_area_kitchen'];
                        $san = $r['k_isf_san'];
                        $balcony = $r['k_isf_balcony'];
                        $utils = $r['k_isf_utilities'];
                        $price = $r['k_isf_price'];
                        $description = $r['k_isf_description'];
                        $contact_name = $r['k_isf_contact_name'];
                        $contacts = $r['k_isf_contacts'];
                        $Adv = array();
                        if ($r['k_isf_phone_stat']) {
                            array_push($Adv, 'phone_stat');
                        }
                        if ($r['k_isf_security']) {
                            array_push($Adv, 'security');
                        }
                        if ($r['k_isf_internet']) {
                            array_push($Adv, 'internet');
                        }
                        if ($r['k_isf_balcony_gl']) {
                            array_push($Adv, 'balcony_gl');
                        }
                        if ($r['k_isf_furniture']) {
                            array_push($Adv, 'furniture');
                        }
                        if ($r['k_isf_fridge']) {
                            array_push($Adv, 'fridge');
                        }
                        if ($r['k_isf_washing']) {
                            array_push($Adv, 'washing');
                        }
                        if ($r['k_isf_microwave']) {
                            array_push($Adv, 'microwave');
                        }
                        if ($r['k_isf_tv']) {
                            array_push($Adv, 'tv');
                        }
                        if ($r['k_isf_ctv']) {
                            array_push($Adv, 'ctv');
                        }
                        if ($r['k_isf_stove']) {
                            array_push($Adv, 'stove');
                        }
                        if ($r['k_isf_plastic_windows']) {
                            array_push($Adv, 'plastic_windows');
                        }
                        $Params = array();
                        if ($r['k_isf_quickly'])
                            array_push($Params, 'quickly');
                        if ($r['k_isf_exchange'])
                            array_push($Params, 'exchange');
                        if ($r['k_isf_merch'])
                            array_push($Params, 'merch');
                        if ($r['k_isf_privat'])
                            array_push($Params, 'privat');
                        if ($r['k_isf_owned'])
                            array_push($Params, 'owned');
                        if ($r['k_isf_credit'])
                            array_push($Params, 'credit');
                        if ($r['k_isf_documents'])
                            array_push($Params, 'documents');
                    }
                }
                try {
                    $query = $mysql->prepare('SELECT kis.*, kic.k_ic_name FROM k_immovables_subcategories as kis
                    LEFT JOIN k_immovables_categories as kic ON (kic.k_ic_id = kis.k_is_parent)
                    WHERE (k_is_parent=1 OR k_is_parent=2) AND k_is_id=:id
                    LIMIT 1');
                    $query->execute(array(':id' => $Immo_type));
                    $row = $query->fetch(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    exit();
                }
                $Immo_str = $row['k_is_name'];
                ?>
                <p class="style_kabinet_4"><?php echo dropBackWords($row['k_ic_name']) . ' ' . dropBackWords($Immo_str); ?></p>
                <input type="hidden" name="dealtype" value="<?php echo $row['k_is_parent']; ?>">
                <div class="line_top_bottom">
                    <span class="team_style">Тип:
                        <select class="all_inp" name="immovable_type">
                            <option value="0">Не выбрано</option>
                            <?php
                            try {
                                $query1 = $mysql->prepare('SELECT k_is_id FROM k_immovables_subcategories WHERE k_is_parent=1 AND k_is_name=:name LIMIT 1');
                                $query1->execute(array(':name' => $Immo_str));
                                $row1 = $query1->fetch(PDO::FETCH_ASSOC);
                                $query2 = $mysql->prepare('SELECT * FROM k_immovables_sell_types WHERE k_isft_sub_id=:subid ORDER BY k_isft_name ASC');
                                $query2->execute(array(':subid' => $row1['k_is_id']));
                                $row2 = $query2->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                exit();
                            }
                            foreach ($row2 as $value) {
                                echo '<option';
                                if (isset($_GET['immovable_type'])) {
                                    $_GET['immovable_type'] = filter_var($_GET['immovable_type'], FILTER_VALIDATE_INT);
                                    if ($value['k_isft_id'] == $_GET['immovable_type']) {
                                        echo ' selected="" ';
                                    }
                                } else {
                                    if ($immovable_type == $value['k_isft_id']) {
                                        echo ' selected="" ';
                                    }
                                }
                                echo ' value="' . $value['k_isft_id'] . '">' . $value['k_isft_name'] . '</option>';
                            }
                            ?>
                        </select>
                    </span>
                </div>
                <div class="line_top_bottom">
                    <span class="team_style" style="display: inline-block; vertical-align: top;">Адрес:<span style="color: red; font-weight: bold;">*</span></span>
                    <div style="display: inline-block; vertical-align: top;">
                        <input class="team_style_input" style="background: #ff9999;" name="address_input" type="text" id="address_input" value="<?php echo isset($_GET['address_input']) ? $_GET['address_input'] : $address_input; ?>"><br>
                        <input type="hidden" id="final_address" name="address_choise" value="<?php echo isset($_GET['address_choise']) ? $_GET['address_choise'] : $address_choise; ?>">
                        <span id="select_address"></span>
                    </div>
                </div>
                <div class="line_top_bottom">
                    <span class="team_style">Район:
                        <span id="district"></span>
                    </span>
                </div>
                <div class="line_top_bottom">
                    <span class="team_style">Жилмассив:
                        <span id="massive"></span>
                    </span>
                </div>
                <?php
                if (in_array($Immo_type, array(1, 2, 3, 4, 8))) {
                    ?>
                    <div class="line_top_bottom">
                        <span class="team_style">Вид:
                            <select class="all_inp" name="newsec">
                                <?php
                                echo '<option ';
                                if (isset($_GET['newsec'])) {
                                    $_GET['newsec'] = filter_var($_GET['newsec'], FILTER_VALIDATE_INT);
                                    if ($_GET['newsec'] == 0) {
                                        echo 'selected=""';
                                    }
                                } else {
                                    if ($newsec == 0) {
                                        echo 'selected=""';
                                    }
                                }
                                echo ' value="0">Не выбрано</option>';

                                echo '<option ';
                                if (isset($_GET['newsec'])) {
                                    if ($_GET['newsec'] == 1) {
                                        echo 'selected=""';
                                    }
                                } else {
                                    if ($newsec == 1) {
                                        echo 'selected=""';
                                    }
                                }
                                echo ' value="1">Новостройка</option>';

                                echo '<option ';
                                if (isset($_GET['newsec'])) {
                                    if ($_GET['newsec'] == 2) {
                                        echo 'selected=""';
                                    }
                                } else {
                                    if ($newsec == 2) {
                                        echo 'selected=""';
                                    }
                                }
                                echo ' value="2">Вторичное</option>';
                                ?>
                            </select>
                        </span>
                    </div>
                    <?php
                }
                ?>
                <?php
                if (in_array($Immo_type, array(1, 2, 3, 7, 8))) {
                    try {
                        $query3 = $mysql->prepare('SELECT * FROM k_immovables_sell_material ORDER BY k_isfm_name ASC');
                        $query3->execute();
                        $row3 = $query3->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        exit();
                    }
                    ?>
                    <div class="line_top_bottom">
                        <span class="team_style">Материал:
                            <select class="all_inp" name="material">
                                <option value="0">Не выбрано</option>
                                <?php
                                foreach ($row3 as $value) {
                                    echo '<option ';
                                    if (isset($_GET['material'])) {
                                        $_GET['material'] = filter_var($_GET['material'], FILTER_VALIDATE_INT);
                                        if ($_GET['material'] == $value['k_isfm_id']) {
                                            echo ' selected="" ';
                                        }
                                    } else {
                                        if ($material == $value['k_isfm_id']) {
                                            echo ' selected="" ';
                                        }
                                    }
                                    echo ' value="' . $value['k_isfm_id'] . ' ">' . $value['k_isfm_name'] . '</option>';
                                }
                                ?>
                            </select>
                        </span>
                    </div>
                    <?php
                }
                ?>
                <?php
                if (in_array($Immo_type, array(1, 3, 4, 6, 8, 9))) {
                    ?>
                    <div class="line_top_bottom">
                        <span class="team_style">Этаж:
                            <select class="all_inp" name="floor">
                                <option value="0">Не выбрано</option>
                                <?php
                                for ($i = 1; $i <= 40; $i++) {
                                    echo '<option ';
                                    if (isset($_GET['floor'])) {
                                        $_GET['floor'] = filter_var($_GET['floor'], FILTER_VALIDATE_INT);
                                        if ($_GET['floor'] == $i) {
                                            echo 'selected=""';
                                        }
                                    } else {
                                        if ($floor == $i) {
                                            echo 'selected=""';
                                        }
                                    }
                                    echo ' value="' . $i . ' ">' . $i . '</option>';
                                }

                                echo '<option ';
                                if (isset($_GET['floor'])) {
                                    if ($_GET['floor'] == 41) {
                                        echo 'selected=""';
                                    }
                                } else {
                                    if ($floor == 41) {
                                        echo 'selected=""';
                                    }
                                }
                                echo ' value="2">Мансарда с окнами</option>';

                                echo '<option ';
                                if (isset($_GET['floor'])) {
                                    if ($_GET['floor'] == 42) {
                                        echo 'selected=""';
                                    }
                                } else {
                                    if ($floor == 42) {
                                        echo 'selected=""';
                                    }
                                }
                                echo ' value="2">Мансарда без окон</option>';

                                echo '<option ';
                                if (isset($_GET['floor'])) {
                                    if ($_GET['floor'] == 43) {
                                        echo 'selected=""';
                                    }
                                } else {
                                    if ($floor == 43) {
                                        echo 'selected=""';
                                    }
                                }
                                echo ' value="2">Цоколь с окнами</option>';

                                echo '<option ';
                                if (isset($_GET['floor'])) {
                                    if ($_GET['floor'] == 44) {
                                        echo 'selected=""';
                                    }
                                } else {
                                    if ($floor == 44) {
                                        echo 'selected=""';
                                    }
                                }
                                echo ' value="2">Цоколь без окон</option>';
                                ?>
                            </select>
                        </span>
                    </div>
                    <?php
                }
                ?>
                <?php
                if (in_array($Immo_type, array(1, 2, 3, 4, 6, 7, 8, 9))) {
                    ?>
                    <div class="line_top_bottom">
                        <span class="team_style">Количество этажей здания:
                            <select class="all_inp" name="floor_all">
                                <option value="0">Не выбрано</option>
                                <?php
                                for ($i = 1; $i <= 40; $i++) {
                                    echo '<option ';
                                    if (isset($_GET['floor_all'])) {
                                        $_GET['floor_all'] = filter_var($_GET['floor_all'], FILTER_VALIDATE_INT);
                                        if ($_GET['floor_all'] == $i) {
                                            echo 'selected=""';
                                        }
                                    } else {
                                        if ($floor_all == $i) {
                                            echo 'selected=""';
                                        }
                                    }
                                    echo ' value="' . $i . ' ">' . $i . '</option>';
                                }
                                ?>
                            </select>
                        </span>
                    </div>
                    <?php
                }
                ?>
                <?php
                if (in_array($Immo_type, array(1, 2, 3))) {
                    ?>
                    <div class="line_top_bottom">
                        <span class="team_style">Отделка:
                            <select class="all_inp" name="eqs">
                                <option value="0">Не выбрано</option>
                                <?php
                                try {
                                    $query4 = $mysql->prepare('SELECT * FROM k_immovables_sell_eq ORDER BY k_isfe_name ASC');
                                    $query4->execute();
                                    $row4 = $query4->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    exit();
                                }
                                foreach ($row4 as $value) {
                                    echo '<option ';
                                    if (isset($_GET['eqs'])) {
                                        echo $value['k_isfe_id'];
                                        $_GET['eqs'] = filter_var($_GET['eqs'], FILTER_VALIDATE_INT);
                                        if ($_GET['eqs'] == $value['k_isfe_id']) {
                                            echo ' selected="" ';
                                        }
                                    } else {
                                        if ($eq == $value['k_isfe_id']) {
                                            echo ' selected="" ';
                                        }
                                    }
                                    echo ' value="' . $value['k_isfe_id'] . ' ">' . $value['k_isfe_name'] . '</option>';
                                }
                                ?>
                            </select>
                        </span>
                    </div>
                    <?php
                }
                ?>
                <?php
                if (in_array($Immo_type, array(1, 2, 6, 7))) {
                    ?>
                    <div class="line_top_bottom">
                        <span class="team_style">Колличество комнат:
                            <?php
                            if (isset($_GET['rooms'])) {
                                $_GET['rooms'] = filter_var($_GET['rooms'], FILTER_VALIDATE_INT);
                                echo '<input class="all_inp" name="rooms" type="text" onkeyup="convInt(this);" value="' . $_GET['rooms'] . '">';
                            } else {
                                echo '<input class="all_inp" name="rooms" type="text" onkeyup="convInt(this);" value="' . ($rooms + 0) . '">';
                            }
                            ?>
                        </span>
                    </div>
                    <?php
                }
                ?>
                <?php
                if (in_array($Immo_type, array(1, 2, 3, 4, 6, 7, 8, 9))) {
                    ?>
                    <div class="line_top_bottom">
                        <span class="team_style">Общая площадь (кв.м):
                            <?php
                            if (isset($_GET['area_all'])) {
                                $_GET['area_all'] = filter_var($_GET['area_all'], FILTER_VALIDATE_INT);
                                echo '<input class="all_inp" name="area_all" type="text" onkeyup="convInt(this);" value="' . $_GET['rooms'] . '">';
                            } else {
                                echo '<input class="all_inp" name="area_all" type="text" onkeyup="convInt(this);" value="' . ($area_all + 0) . '">';
                            }
                            ?>
                        </span>
                    </div>
                    <?php
                }
                ?>
                <?php
                if (in_array($Immo_type, array(1, 2, 6, 7))) {
                    ?>
                    <div class="line_top_bottom">
                        <span class="team_style">Жилая площадь (кв.м):
                            <?php
                            if (isset($_GET['area_live'])) {
                                $_GET['area_live'] = filter_var($_GET['area_live'], FILTER_VALIDATE_INT);
                                echo '<input class="all_inp" name="area_live" type="text" onkeyup="convInt(this);" value="' . $_GET['area_live'] . '">';
                            } else {
                                echo '<input class="all_inp" name="area_live" type="text" onkeyup="convInt(this);" value="' . ($area_live + 0) . '">';
                            }
                            ?>
                        </span>
                    </div>
                    <?php
                }
                ?>
                <?php
                if (in_array($Immo_type, array(1, 6))) {
                    ?>
                    <div class="line_top_bottom">
                        <span class="team_style">Площадь кухни (кв.м):
                            <?php
                            if (isset($_GET['area_kitchen'])) {
                                $_GET['area_kitchen'] = filter_var($_GET['area_kitchen'], FILTER_VALIDATE_INT);
                                echo '<input class="all_inp" name="area_kitchen" type="text" onkeyup="convInt(this);" value="' . $_GET['area_kitchen'] . '">';
                            } else {
                                echo '<input class="all_inp" name="area_kitchen" type="text" onkeyup="convInt(this);" value="' . ($area_kit + 0) . '">';
                            }
                            ?>
                        </span>
                    </div>
                    <?php
                }
                ?>
                <?php
                if (in_array($Immo_type, array(2, 5, 7))) {
                    ?>
                    <div class="line_top_bottom">
                        <span class="team_style">Площадь участка в сот.:
                            <?php
                            if (isset($_GET['area_land'])) {
                                $_GET['area_land'] = filter_var($_GET['area_land'], FILTER_VALIDATE_INT);
                                echo '<input class="all_inp" name="area_land" type="text" onkeyup="convInt(this);" value="' . $_GET['area_land'] . '">';
                            } else {
                                echo '<input class="all_inp" name="area_land" type="text" onkeyup="convInt(this);" value="' . ($area_land + 0) . '">';
                            }
                            ?>
                        </span>
                    </div>
                    <?php
                }
                ?>
                <?php
                if (in_array($Immo_type, array(1, 6))) {
                    ?>
                    <div class="line_top_bottom">
                        <span class="team_style">Санузел:
                            <select class="all_inp" name="san">
                                <?php
                                echo '<option ';
                                if (isset($_GET['san'])) {
                                    $_GET['san'] = filter_var($_GET['san'], FILTER_VALIDATE_INT);
                                    if ($_GET['san'] == 0) {
                                        echo 'selected=""';
                                    }
                                } else {
                                    if ($san == 0) {
                                        echo 'selected=""';
                                    }
                                }
                                echo '  value="0">Не выбрано</option>';
                                echo '<option ';
                                if (isset($_GET['san'])) {
                                    $_GET['san'] = filter_var($_GET['san'], FILTER_VALIDATE_INT);
                                    if ($_GET['san'] == 1) {
                                        echo 'selected=""';
                                    }
                                } else {
                                    if ($san == 1) {
                                        echo 'selected=""';
                                    }
                                }
                                echo '  value="1">Совмещенный</option>';
                                echo '<option ';
                                if (isset($_GET['san'])) {
                                    $_GET['san'] = filter_var($_GET['san'], FILTER_VALIDATE_INT);
                                    if ($_GET['san'] == 2) {
                                        echo 'selected=""';
                                    }
                                } else {
                                    if ($san == 2) {
                                        echo 'selected=""';
                                    }
                                }
                                echo '  value="2">Раздельный</option>';
                                ?>
                            </select>
                        </span>
                    </div>
                    <div class="line_top_bottom">
                        <span class="team_style">Балкон/лоджия:
                            <select class="all_inp" name="balcony">
                                <?php
                                echo '<option ';
                                if (isset($_GET['balcony'])) {
                                    $_GET['balcony'] = filter_var($_GET['balcony'], FILTER_VALIDATE_INT);
                                    if ($_GET['balcony'] == 0) {
                                        echo 'selected=""';
                                    }
                                } else {
                                    if ($balcony == 0) {
                                        echo 'selected=""';
                                    }
                                }
                                echo '  value="0">Не выбрано</option>';
                                echo '<option ';
                                if (isset($_GET['balcony'])) {
                                    $_GET['balcony'] = filter_var($_GET['balcony'], FILTER_VALIDATE_INT);
                                    if ($_GET['balcony'] == 1) {
                                        echo 'selected=""';
                                    }
                                } else {
                                    if ($balcony == 1) {
                                        echo 'selected=""';
                                    }
                                }
                                echo '  value="1">Балкон</option>';
                                echo '<option ';
                                if (isset($_GET['balcony'])) {
                                    $_GET['balcony'] = filter_var($_GET['balcony'], FILTER_VALIDATE_INT);
                                    if ($_GET['balcony'] == 2) {
                                        echo 'selected=""';
                                    }
                                } else {
                                    if ($balcony == 2) {
                                        echo 'selected=""';
                                    }
                                }
                                echo '  value="2">Лоджия</option>';
                                echo '<option ';
                                if (isset($_GET['balcony'])) {
                                    $_GET['balcony'] = filter_var($_GET['balcony'], FILTER_VALIDATE_INT);
                                    if ($_GET['balcony'] == 3) {
                                        echo 'selected=""';
                                    }
                                } else {
                                    if ($balcony == 3) {
                                        echo 'selected=""';
                                    }
                                }
                                echo '  value="3">Нет</option>';
                                ?>
                            </select>
                        </span>
                    </div>
                    <?php
                }
                ?>
                <?php
                if (in_array($Immo_type, array(1, 2, 3, 6, 7, 8))) {
                    ?>
                    <div class="line_top_bottom">
                        <span class="team_style">Дополнительно:<br>
                            <?php
                            if (in_array($Immo_type, array(1, 2, 3, 7, 8))) {
                                echo '<label><input ';
                                if (isset($_GET['Adv']) || isset($_GET['immovable_type'])) {
                                    if (in_array('phone_stat', $_GET['Adv'])) {
                                        echo 'checked="checked"';
                                    }
                                } else {
                                    if (in_array('phone_stat', $Adv)) {
                                        echo 'checked="checked"';
                                    }
                                }
                                echo ' type="checkbox" name="Adv[]" value="phone_stat">Телефон</label><br>';
                            }
                            ?>
                            <?php
                            if (in_array($Immo_type, array(3, 8))) {
                                echo '<label><input ';
                                if (isset($_GET['Adv']) || isset($_GET['immovable_type'])) {
                                    if (in_array('security', $_GET['Adv'])) {
                                        echo 'checked="checked"';
                                    }
                                } else {
                                    if (in_array('security', $Adv)) {
                                        echo 'checked="checked"';
                                    }
                                }
                                echo ' type="checkbox" name="Adv[]" value="security">Охрана</label><br>';
                            }
                            ?>
                            <?php
                            if (in_array($Immo_type, array(3, 6, 8))) {
                                echo '<label><input ';
                                if (isset($_GET['Adv']) || isset($_GET['immovable_type'])) {
                                    if (in_array('internet', $_GET['Adv'])) {
                                        echo 'checked="checked"';
                                    }
                                } else {
                                    if (in_array('internet', $Adv)) {
                                        echo 'checked="checked"';
                                    }
                                }
                                echo ' type="checkbox" name="Adv[]" value="internet">Интернет</label><br>';
                            }
                            ?>
                            <?php
                            if ($Immo_type == 6) {
                                echo '<label><input ';
                                if (isset($_GET['Adv']) || isset($_GET['immovable_type'])) {
                                    if (in_array('balcony_gl', $_GET['Adv'])) {
                                        echo 'checked="checked"';
                                    }
                                } else {
                                    if (in_array('balcony_gl', $Adv)) {
                                        echo 'checked="checked"';
                                    }
                                }
                                echo ' type="checkbox" name="Adv[]" value="balcony_gl">Балкон застеклён</label><br>';
                            }
                            ?>
                            <?php
                            if (in_array($Immo_type, array(6, 7))) {
                                ?>
                                <label><input type="checkbox" <?php if (in_array('furniture', $Adv)) echo 'checked="checked"'; ?> name="Adv[]" value="furniture">Мебель</label><br>
                                <label><input type="checkbox" <?php if (in_array('fridge', $Adv)) echo 'checked="checked"'; ?> name="Adv[]" value="fridge">Холодильник</label><br>
                                <label><input type="checkbox" <?php if (in_array('washing', $Adv)) echo 'checked="checked"'; ?> name="Adv[]" value="washing">Стиральная машина</label><br>
                                <label><input type="checkbox" <?php if (in_array('microwave', $Adv)) echo 'checked="checked"'; ?> name="Adv[]" value="microwave">Микроволновая печь</label><br>
                                <label><input type="checkbox" <?php if (in_array('tv', $Adv)) echo 'checked="checked"'; ?> name="Adv[]" value="tv">Телевизор</label><br>
                                <label><input type="checkbox" <?php if (in_array('ctv', $Adv)) echo 'checked="checked"'; ?> name="Adv[]" value="ctv">Кабельное телевидение</label><br>
                                <?php
                            }
                            ?>
                            <?php
                            if ($Immo_type == 6) {
                                ?>
                                <label><input type="checkbox" <?php if (in_array('stove', $Adv)) echo 'checked="checked"'; ?> name="Adv[]" value="stove">Кухонная печь</label><br>
                                <label><input type="checkbox" <?php if (in_array('plastic_windows', $Adv)) echo 'checked="checked"'; ?> name="Adv[]" value="plastic_windows">Пластиковые окна</label><br>
                                <?php
                            }
                            ?>
                        </span>
                    </div>
                    <?php
                }
                ?>
                <?php
                if (in_array($Immo_type, array(6, 7, 8, 9))) {
                    ?>
                    <div class="line_top_bottom">
                        <span class="team_style">Коммунальные услуги:
                            <select class="all_inp" name="utils">
                                <?php
                                if (isset($_GET['utils'])) {
                                    $_GET['utils'] = filter_var($_GET['utils'], FILTER_VALIDATE_INT);
                                    if ($_GET['utils'] == 0) {
                                        echo '<option selected="" value="0">Не выбрано</option>';
                                    }
                                    if ($_GET['utils'] == 1) {
                                        echo '<option selected="" value="1">Включены в стоимость</option>';
                                    }
                                    if ($_GET['utils'] == 2) {
                                        echo '<option selected="" value="2">Не включены в стоимость</option>';
                                    }
                                } else {
                                    ?>
                                    <option <?php if ($utils == 0) echo 'selected=""'; ?> value="0">Не выбрано</option>
                                    <option <?php if ($utils == 1) echo 'selected=""'; ?> value="1">Включены в стоимость</option>
                                    <option <?php if ($utils == 2) echo 'selected=""'; ?> value="2">Не включены в стоимость</option>
                                    <?php
                                }
                                ?>
                            </select>
                        </span>
                    </div>
                    <?php
                }
                ?>
                <div class="line_top_bottom">
                    <span class="team_style">Цена (тыс.руб.<?php if ($row['k_is_parent'] == 2) echo '/месяц'; ?>):<span style="color: red; font-weight: bold;">*</span>
                        <?php
                        if (isset($_GET['price'])) {
                            $_GET['price'] = filter_var($_GET['price'], FILTER_VALIDATE_INT);
                            echo '<input class="team_style_input_1" type="text" style="background: #ff9999;" name="price" onkeyup="convInt(this); RequireInput(this);" value="' . $_GET['price'] . '">';
                        } else {
                            echo '<input class="team_style_input_1" type="text" style="background: #ff9999;" name="price" onkeyup="convInt(this); RequireInput(this);" value="' . ($price + 0) . '">';
                        }
                        ?>
                    </span>
                </div>
                <div class="line_top_bottom">
                    <span class="team_style">
                        <label><input <?php if (in_array('quickly', $Params)) echo 'checked="checked"'; ?> type="checkbox" name="Params[]" value="quickly">Срочно</label><br>
                        <?php
                        if (in_array($Immo_type, array(1, 2, 3, 4, 5))) {
                            ?>
                            <label><input <?php if (in_array('exchange', $Params)) echo 'checked="checked"'; ?> type="checkbox" name="Params[]" value="exchange">Обмен</label><br>
                            <label><input <?php if (in_array('credit', $Params)) echo 'checked="checked"'; ?> type="checkbox" name="Params[]" value="credit">Ипотека</label><br>
                            <label><input <?php if (in_array('documents', $Params)) echo 'checked="checked"'; ?> type="checkbox" name="Params[]" value="documents">Документы готовы</label><br>
                            <label><input <?php if (in_array('owned', $Params)) echo 'checked="checked"'; ?> type="checkbox" name="Params[]" value="owned">В собственности</label><br>
                            <?php
                        }
                        ?>
                        <?php
                        if (in_array($Immo_type, array(1, 2, 4, 5))) {
                            ?>
                            <label><input <?php if (in_array('privat', $Params)) echo 'checked="checked"'; ?> type="checkbox" name="Params[]" value="privat">Приватизирована</label><br>
                            <?php
                        }
                        ?>
                        <label><input <?php if (in_array('merch', $Params)) echo 'checked="checked"'; ?> type="checkbox" name="Params[]" value="merch">Возможен торг</label><br>
                    </span>
                </div>
                <div class="line_top_bottom_3">
                    <span class="team_style">Текст объявления:<br>
                        <?php
                        if (isset($_GET['description'])) {
                            $_GET['description'] = filter_var($_GET['description'], FILTER_SANITIZE_STRIPPED);
                            echo '<textarea rows="5" cols="80" name="description" style="resize: vertical;">' . $_GET['description'] . '</textarea>';
                        } else {
                            ?>
                            <textarea rows="5" cols="80" name="description" style="resize: vertical;"><?php echo $description; ?></textarea>
                            <?php
                        }
                        ?>
                    </span>
                </div>
                <div class="line_top_bottom">
                    <span class="team_style">Добавить фотографию или видео:
                        <button class="act_3" id="PhotoUpload">Загрузить</button><br>
                        <span id="statusNews"></span><br>
                        <?php
                        if (isset($_GET['images'])) {
                            for ($k = 0; $k < count($_GET['images']); $k++) {
                                if (file_exists($_GET['images'][$k])) {
                                    ?>
                                    <span>
                                        <?php
                                        echo '<img src="' . $_GET['images'][$k] . '" alt="" style="width: 100px; display: inline-block; vertical-align: top; margin: 5px;">';
                                        ?>
                                        <div class="img_block">
                                            <a title="Сделать Главной" onclick="SetMainPhoto(this);" class="no_line">
                                                <img alt="" src="../images/prioritet.png">
                                            </a>
                                            <a title="Удалить фото" onclick="DeleteThisPhoto(this);" class="no_line">
                                                <img alt="" src="../images/delete.png">
                                            </a>
                                        </div>
                                        <?php
                                        echo '<input type="hidden" value="' . $_GET['images'][$k] . '" name="images[]">';
                                        ?>
                                    </span>
                                    <?php
                                }
                            }
                        } else {
                            try {
                                $query_p = $mysql->prepare('SELECT * FROM k_immovables_photos WHERE k_ip_immo_id=:id ORDER BY k_ip_priority DESC');
                                $query_p->execute(array(":id" => $_GET['id']));
                                $result_p = $query_p->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($result_p as $value) {
                                    //echo $value['k_ip_url'];
                                    $file_name = str_replace('images/', '../admin/images/', $value['k_ip_url']);
                                    $file_name = str_replace('video/', '../video/', $file_name);
                                    //echo $file_name;
                                    if (file_exists($_SERVER['DOCUMENT_ROOT'].$file_name) || file_exists($_SERVER['DOCUMENT_ROOT'].$value['k_ip_url'])) {
                                        ?>
                                        <span class="ad_f">
                                            <?php
                                            echo '<img src="' . $file_name . '" alt="" style="width: 100px; display: inline-block; vertical-align: top; margin: 5px;">';
                                            ?>
                                            <div class="img_block">
                                                <a title="Сделать Главной" onclick="SetMainPhoto(this);" class="no_line">
                                                    <img alt="" src="../images/prioritet.png">
                                                </a>
                                                <a title="Удалить фото" onclick="DeleteThisPhoto(this);" class="no_line">
                                                    <img alt="" src="../images/delete.png">
                                                </a>
                                            </div>
                                            <?php
                                            echo '<input type="hidden" value="' . $file_name . '" name="images[]">';
                                            ?>
                                        </span>
                                        <?php
                                    }
                                }
                            } catch (PDOException $e) {
                                exit();
                            }
                        }
                        ?>
                    </span>
                </div>
                <div class="line_top_bottom_2">
                    <span class="team_style">Контактное лицо:<span style="color: red; font-weight: bold;">*</span>
                        <?php
                        if (isset($_GET['contact_name'])) {
                            $_GET['contact_name'] = filter_var($_GET['contact_name'], FILTER_SANITIZE_STRIPPED);
                            echo '<input class="team_style_input_1" style="background: #ff9999;" type="text" name="contact_name" onkeyup="RequireInput(this);" value="' . $_GET['contact_name'] . '">';
                        } else {
                            ?>
                            <input class="team_style_input_1" style="background: #ff9999;" type="text" name="contact_name" onkeyup="RequireInput(this);" value="<?php echo $contact_name; ?>">
                            <?php
                        }
                        ?>
                        Контакты:<span style="color: red; font-weight: bold;">*</span>
                        <?php
                        if (isset($_GET['contacts'])) {
                            $_GET['contacts'] = filter_var($_GET['contacts'], FILTER_SANITIZE_STRIPPED);
                            echo '<input class="team_style_input_1" style="background: #ff9999;" type="text" name="contacts" onkeyup="RequireInput(this);" value="' . $_GET['contacts'] . '">';
                        } else {
                            ?>
                            <input class="team_style_input_1" style="background: #ff9999;" type="text" name="contacts" onkeyup="RequireInput(this);" value="<?php echo $contacts; ?>">
                            <?php
                        }
                        ?>
                    </span><br>
                </div>
                <?php
                if (!empty($_GET['comment'])) {
                    echo '<span style="color: red;">' . $_GET['comment'] . '</span><br>';
                }
                ?>
                <input type="hidden" name="Action" value="<?php echo $_GET['id'] + 0; ?>">
                <input type="hidden" name="ImmoType" value="<?php echo $Immo_type; ?>">
                <input type="hidden" name="PageType" value="<?php echo $ShowParamID; ?>">
                <input id="submit_image" type='image' name='submit' src='inc/captcha.php' alt='Captcha Security'>
            </div>
        </form>
        <?php
    } else {
        echo 'У вас не осталось объявлений в этом месяце!';
    }
    ?>
</div>
<?php
}
?>
<?php
if ($ShowParamID == 7) {
?>
<div class="block_content_1">   <!--ФОРМЫ ВЫБОРА РУБРИКИ НЕДВИЖИМОСТИ-->
    <span>
        <a class="act_2" href="./?PageType=1">
            &laquo; В кабинет
        </a>
        <a class="act_2" href="./?PageType=4">
            &laquo; К объявлениям
        </a>
    </span><br><br>
    <?php
    if ($user->immo_monthly > 0 || $user->immo_remain > 0) {
        ?>
        <div class="style_form">
            <p class="style_kabinet_4">Выберите рубрику для объявления</p>
            <table style="width: 80%; text-align: left;">
                <?php
                try {
                    $query5 = $mysql->prepare('SELECT kis.*, kic.k_ic_name FROM k_immovables_subcategories as kis
                    LEFT JOIN k_immovables_categories as kic ON (kic.k_ic_id = kis.k_is_parent)
                    WHERE k_is_parent=1 OR k_is_parent=2
                    ORDER BY k_is_parent, k_is_name ASC');
                    $query5->execute();
                    $row5 = $query5->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    exit();
                }
                foreach ($row5 as $value) {
                    ?>
                    <tr>
                        <td>
                            <a <?php echo 'href="./?PageType=add&ImmoType=' . $value['k_is_id'] . '"'; ?>>
                                <label class="razdel_style"><?php echo dropBackWords($value['k_ic_name']) . ' ' . dropBackWords($value['k_is_name']); ?></label>
                            </a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
        <?php
    } else {
        echo 'У вас не осталось объявлений в этом месяце!';
    }
    ?>
</div>
<?php
}
?>

<?php
if ($ShowParamID == 5) {
$user_ads = new AdPhotos($_SESSION['id'], '');
?>
<div class="block_content_1">
    <p class="style_kabinet_2_1">Все <b>фото объявления</b></p><br>
    <span>
        <a class="add_rfbinet_obiavlenie" style="float: right;" title="Добавить фото объявление" href="./?PageType=13">
            Добавить фото объявление
        </a>
        <a class="act_2" href="./?PageType=1">
            Назад
        </a>
    </span>
    <br><br>
    <table class="table_add_obiavlenia" border="1">
        <tr>
            <td colspan="7"><b class="style_kabinet_6">Фото объявления<span class="style_kabinet_6_1"><?php echo $user->photo_count; ?></span></b></td>
        </tr>
        <tr>
            <td class="td_name_table"><p class="style_kabinet_3">№</p></td>
            <td class="td_name_table"><p class="style_kabinet_3">Фото</p></td>
            <td class="td_name_table"><p class="style_kabinet_3">Наименование объявления</p></td>
            <td class="td_name_table"><p class="style_kabinet_3">Дата размещения</p></td>
            <td class="td_name_table"><p class="style_kabinet_3">Дата завершения</p></td>
            <td class="td_name_table"><p class="style_kabinet_3">Рубрика</p></td>
            <td class="td_name_table"><p class="style_kabinet_3">Действие</p></td>
        </tr>
        <?php
        for ($i = 0; $i < count($user_ads->id); $i++) {
            ?>
            <tr>
                <td><p class="znak_15"><?php echo $user_ads->id[$i]; ?></p></td>
                <td>
                    <?php

                    if ($user_ads->photo_url[$i] && file_exists($_SERVER['DOCUMENT_ROOT'].$user_ads->photo_url[$i])) {
                        echo '<img class="photo_obiavlenia" src="' . $user_ads->photo_url[$i] . '" alt="">';
                    } else {
                        echo '<img class="photo_obiavlenia" src="../images/noimage.png" alt="">';
                    }
                    ?>
                </td>
                <td><p class="znak_15"><?php echo $user_ads->theme[$i]; ?></p></td>
                <td><p class="znak_15"><?php echo $user_ads->date_reg[$i]; ?></p></td>
                <td><p class="znak_15"><?php echo $user_ads->date_end[$i]; ?></p></td>
                <td><p class="znak_15"><?php echo $user_ads->category_name[$i]; ?></td>
                <td>
                    <a <?php echo 'href="./?PageType=14&ID=' . $user_ads->id[$i] . '"'; ?>><img class="icon_f" title="Редактировать" src="../images/edit.png" alt=""></a>
                    <?php
                    /*
                      <img class="icon_f" title="Добавить в платную ленту" src="../images/plat_lenta.png" alt="">
                      <img class="icon_f" title="Добавить в VIP-ленту" src="../images/vip.png" alt="">
                     */
                    ?>
                    <img class="icon_f" onclick="DeletePhotoAd(this);" title="Удалить объявление" src="../images/delete_team.png" alt="<?php echo $user_ads->id[$i]; ?>">
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
if ($ShowParamID == 13 || $ShowParamID == 14) {
if ($ShowParamID == 14) {
    if (isset($_GET['ID'])) {
        $_GET['ID'] = filter_var($_GET['ID'], FILTER_VALIDATE_INT);
    }
    $user_ads = new AdPhotos($_SESSION['id'], ' AND k_pd_id=' . $_GET['ID'] . ' ');
    $user_ads->LoadPhotos($user_ads->id[0]);
} else {
    $user_ads = new AdPhotos(0, '');
}
$photo_categories = new PhotoCategories();
?>
<script type="text/javascript" src="js/ajaxupload.3.5.js"></script>
<script type="text/javascript">
                        $(function() {
                            var btnUpload = $('#LoadPhotoButton');
                            var status = $('#statusNews');
                            new AjaxUpload(btnUpload, {
                                action: 'upload-file.php',
                                name: 'photo_load',
                                onSubmit: function(file, ext) {
                                    if (!(ext && /^(jpg|png|jpeg|gif|avi|mp4|3gp|flv|mov)$/.test(ext))) {
                                        // extension is not allowed
                                        status.text('Разрешена загрузка только: jpg, png, jpeg, gif, avi, mp4, 3gp, flv.');
                                        return false;
                                    }
                                    status.text('\u0417агружаются данные...');
                                    $('#video_load').fadeIn(500);
                                    enableA();
                                },
                                onComplete: function(file, response) {
                                    status.text('');
                                    //alert(response);
                                    if (response === "error") {
                                        $('#video_load').fadeOut(500);
                                        disableA();
                                        return false;
                                    }
                                    if (response === 'duration') {
                                        $('#video_load').fadeOut(500);
                                        disableA();
                                        alert('Видео превышает максимальную длительность!');
                                    }
                                    if (response.toLowerCase().match('/gif|jpg|jpeg|png/')) {
                                        //alert(response);
                                        $('#video_load').fadeOut(500);
                                        disableA();
                                        status.text('Успешно загружено!');
                                        var img = $('<img>', {
                                            'src': response,
                                            'alt': '',
                                            'class': 'im_f'
                                        });
                                        var block = $('.gl_bl_img_fot');
                                        var span = $('<div>', {'class': 'bl_img_fot'});
                                        span.appendTo(block);
                                        img.appendTo(span);
                                        var a_main = $('<a>', {'title': 'Сделать Главной', 'onclick': 'SetMainPhoto_p(this);'});
                                        var img_main = $('<img>', {'alt': '', 'src': '../images/prioritet.png', 'class': 'icon_f'});
                                        var a_del = $('<a>', {'title': 'Удалить фото', 'onclick': 'DeleteThisPhoto_p(this);'});
                                        var img_del = $('<img>', {'alt': '', 'src': '../images/delete.png', 'class': 'icon_f'});
                                        a_main.insertAfter(img);
                                        img_main.appendTo(a_main);
                                        a_del.insertAfter(a_main);
                                        img_del.appendTo(a_del);
                                        $('<input>', {'type': 'hidden', 'value': response, 'name': 'images[]'}).appendTo(span);
                                        LoadPhotodeskPhoto(response);
                                    }
                                }
                            });
                        });
</script>
<div class="block_content_1">
    <div>
        <?php
        if ($ShowParamID == 14) {
            ?>
            <p class="style_kabinet_2_1">Редактировать <b>объявление</b></p><br>
            <?php
        }
        ?>
        <?php
        if ($ShowParamID == 13) {
            ?>
            <p class="style_kabinet_2_1">Добавить <b>объявление</b></p><br>
            <?php
        }
        ?>
        <form action="posts.php" method="POST">
            <table style="width: 100%;">
                <?php
                if ($ShowParamID == 14) {
                    ?>
                    <tr>
                        <td><p class="tit_text">№:</p></td>
                        <td><p class="tit_text"><b><?php echo $_GET['ID']; ?></b></p></td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td><p class="tit_text">Наименование:</p></td>
                    <td>
                        <?php
                        echo '<input class="all_inp" name="PhotoAdTheme" type="text" value="' . $user_ads->theme[0] . '">';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><p class="tit_text">Рубрика:</p></td>
                    <td>
                        <select class="all_inp" name="PhotoAdCategory">
                            <?php
                            for ($i = 0; $i < count($photo_categories->id); $i++) {
                                if ($user_ads->category[0] == $photo_categories->id[$i]) {
                                    echo '<option selected="" value="' . $photo_categories->id[$i] . '">' . $photo_categories->name[$i] . '</option>';
                                } else {
                                    echo '<option value="' . $photo_categories->id[$i] . '">' . $photo_categories->name[$i] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><p class="tit_text">Текст объявления:</p></td>
                    <td><textarea class="area_exp_1" name="PhotoAdText"><?php echo $user_ads->description[0]; ?></textarea></td>
                </tr>
                <tr>
                    <td><p class="tit_text">Контактный телефон:</p></td>
                    <td>
                        <?php
                        echo '<input class="all_inp" name="PhotoAdPhone" type="text" value="' . $user_ads->phone[0] . '">';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><p class="tit_text">Цена:</p></td>
                    <td>
                        <?php
                        echo '<input class="all_inp" name="PhotoAdPrice" onchange="convInt(this);" type="text" value="' . (int) $user_ads->price[0] . '">';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="button" id="LoadPhotoButton" class="act_2" value="Загрузить файл"><br>
                        <span id="statusNews"></span>
                    </td>
                    <td>
                        <div class="gl_bl_img_fot">
                            <?php
                            //var_dump($user_ads->all_photos);
                            for ($i = 0; $i < count($user_ads->all_photos); $i++) {
                                $file_name = str_replace('images/', '../admin/images/', $value['k_ip_url']);
                                $file_name = str_replace('video/', '../video/', $file_name);
                                if ($user_ads->all_photos[$i][1] && (file_exists('../admin/' . $user_ads->all_photos[$i][1]) || file_exists('../' . $user_ads->all_photos[$i][1]))) {
                                    ?>
                                    <div class="bl_img_fot">
                                        <?php
                                        echo '<img class="im_f" src="' . str_replace('video/', '../video/', str_replace('images/photo/', '../admin/images/photo/', $user_ads->all_photos[$i][1])) . '" alt="">';
                                        ?>
                                        <a onclick="SetMainPhoto_p(this);" title="Сделать главной фотографией"><img class="icon_f" src="../images/prioritet.png" alt=""></a>
                                        <a onclick="DeleteThisPhoto_p(this);" title="Удалить фото"><img class="icon_f" src="../images/delete.png" alt=""></a>
                                        <?php
                                        echo '<input type="hidden" value="' . str_replace('images/photo/', '../admin/images/photo/1_', $user_ads->all_photos[$i][1]) . '" name="images[]">';
                                        ?>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <?php
                        echo '<input name="PhotoAdId" type="hidden" value="' . $user_ads->id[0] . '">';
                        ?>
                        <input class="act_2" name="SavePhotoAds" type="submit" value="Сохранить">
                        <a class="act_2" href="./?PageType=5">&laquo; К объявлениям</a>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<?php
}
?>

<?php
if ($ShowParamID == 6) {
$user_ads = new AdJob($_SESSION['id'], '');
?>
<div class="block_content_1">
    <p class="style_kabinet_2_1">Все <b>объявления о работе</b></p><br>
    <span>
        <a class="add_rfbinet_obiavlenie" style="float: right;" title="Подать вакансию" href="./?PageType=15">
            Подать вакансию
        </a>
        <a class="add_rfbinet_obiavlenie" style="float: right; margin-right: 5px;" title="Подать резюме" href="./?PageType=17">
            Подать резюме
        </a>
        <a class="act_2" href="./?PageType=1">
            Назад
        </a>
    </span>
    <br><br>
    <table class="table_add_obiavlenia" border="1">
        <tr>
            <td colspan="6"><b class="style_kabinet_6">Работа<span class="style_kabinet_6_1"><?php echo $user->job_count; ?></span></b></td>
        </tr>
        <tr>
            <td class="td_name_table"><p class="style_kabinet_3">№</p></td>
            <td class="td_name_table"><p class="style_kabinet_3">Тема объявления</p></td>
            <td class="td_name_table"><p class="style_kabinet_3">Дата размещения</p></td>
            <td class="td_name_table"><p class="style_kabinet_3">Дата завершения</p></td>
            <td class="td_name_table"><p class="style_kabinet_3">Тема</p></td>
            <td class="td_name_table"><p class="style_kabinet_3">Действие</p></td>
        </tr>
        <?php
        for ($i = 0; $i < count($user_ads->id); $i++) {
            ?>
            <tr <?php if ($user_ads->state[$i] == 0) echo 'style="background: #ffaaaa;"'; ?>>
                <td><p class="znak_15"><?php echo $user_ads->id[$i]; ?></p></td>
                <td><p class="znak_15"><?php echo $user_ads->post[$i]; ?></p></td>
                <td><p class="znak_15"><?php echo $user_ads->date_reg[$i]; ?></p></td>
                <td><p class="znak_15"><?php echo $user_ads->date_end[$i]; ?></p></td>
                <td><p class="znak_15">
                        <?php
                        if ($user_ads->type[$i] == 1) {
                            echo 'Вакансия';
                        }
                        if ($user_ads->type[$i] == 2) {
                            echo 'Резюме';
                        }
                        ?>
                </td>
                <td>
                    <a <?php echo 'href="./?PageType=16&ID=' . $user_ads->id[$i] . '"'; ?>><img class="icon_f" title="Редактировать" src="../images/edit.png" alt=""></a>
                    <img class="icon_f" onclick="UpdateJob(this);" title="Продлить объявление" src="../images/refresh.png" alt="<?php echo $user_ads->id[$i]; ?>">
                    <img class="icon_f" onclick="DeleteJob(this);" title="Удалить объявление" src="../images/delete_team.png" alt="<?php echo $user_ads->id[$i]; ?>">
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
if ($ShowParamID == 15 || $ShowParamID == 16 || $ShowParamID == 17) {
if ($ShowParamID == 16) {
    if (isset($_GET['ID'])) {
        $_GET['ID'] = filter_var($_GET['ID'], FILTER_VALIDATE_INT);
    }
    $user_ads = new AdJob($_SESSION['id'], ' AND k_j_id=' . $_GET['ID'] . ' ');
} else {
    $user_ads = new AdJob(0, '');
}
if (isset($_GET['JobPost'])) {
    $user_ads->post[0] = filter_var($_GET['JobPost'], FILTER_SANITIZE_STRIPPED);
}
if (isset($_GET['JobType'])) {
    $user_ads->type[0] = filter_var($_GET['JobType'], FILTER_VALIDATE_INT);
}
if (isset($_GET['JobID'])) {
    $user_ads->id[0] = filter_var($_GET['JobID'], FILTER_VALIDATE_INT);
}
if (isset($_GET['JobSalaryMin'])) {
    $user_ads->salary_min[0] = filter_var($_GET['JobSalaryMin'], FILTER_VALIDATE_INT);
}
if (isset($_GET['JobSalaryMax'])) {
    $user_ads->salary_max[0] = filter_var($_GET['JobSalaryMax'], FILTER_VALIDATE_INT);
}
if (isset($_GET['JobAgeMin'])) {
    $user_ads->age_min[0] = filter_var($_GET['JobAgeMin'], FILTER_VALIDATE_INT);
}
if (isset($_GET['JobAgeMax'])) {
    $user_ads->age_max[0] = filter_var($_GET['JobAgeMax'], FILTER_VALIDATE_INT);
}
if (isset($_GET['JobCurrency'])) {
    $user_ads->currency[0] = filter_var($_GET['JobCurrency'], FILTER_VALIDATE_INT);
}
if (isset($_GET['JobSex'])) {
    $user_ads->sex[0] = filter_var($_GET['JobSex'], FILTER_VALIDATE_INT);
}
if (isset($_GET['JobEducation'])) {
    $user_ads->education[0] = filter_var($_GET['JobEducation'], FILTER_SANITIZE_STRIPPED);
}
if (isset($_GET['JobEducation_T'])) {
    $user_ads->education_t[0] = filter_var($_GET['JobEducation_T'], FILTER_VALIDATE_INT);
}
if (isset($_GET['JobExperience'])) {
    $user_ads->experience[0] = filter_var($_GET['JobExperience'], FILTER_SANITIZE_STRIPPED);
}
if (isset($_GET['JobSchedule'])) {
    $user_ads->schedule[0] = filter_var($_GET['JobSchedule'], FILTER_SANITIZE_STRIPPED);
}
if (isset($_GET['JobText'])) {
    $user_ads->text[0] = filter_var($_GET['JobText'], FILTER_SANITIZE_STRIPPED);
}
if (isset($_GET['JobOrganization'])) {
    $user_ads->organization[0] = filter_var($_GET['JobOrganization'], FILTER_SANITIZE_STRIPPED);
}
if (isset($_GET['JobContactName'])) {
    $user_ads->contact_name[0] = filter_var($_GET['JobContactName'], FILTER_SANITIZE_STRIPPED);
}
if (isset($_GET['JobMarital'])) {
    $user_ads->marital[0] = filter_var($_GET['JobMarital'], FILTER_SANITIZE_STRIPPED);
}
if (isset($_GET['JobContactPhone'])) {
    $user_ads->contact_phone[0] = filter_var($_GET['JobContactPhone'], FILTER_SANITIZE_STRIPPED);
}
if (isset($_GET['JobEmail'])) {
    $user_ads->email[0] = filter_var($_GET['JobEmail'], FILTER_SANITIZE_EMAIL);
}
if (isset($_GET['JobAvatar'])) {
    $user_ads->avatar[0] = filter_var($_GET['JobAvatar'], FILTER_SANITIZE_STRIPPED);
}
?>
<script type="text/javascript" src="js/ajaxupload.3.5.js"></script>
<script type="text/javascript">
                        $(function() {
                            var btnUpload = $('#JobUpload');
                            var status = $('#statusJob');
                            new AjaxUpload(btnUpload, {
                                action: 'upload-file.php',
                                name: 'JobUpload',
                                onSubmit: function(file, ext) {
                                    if (!(ext && /^(jpg|png|jpeg|gif|avi|mp4|3gp|flv)$/.test(ext))) {
                                        // extension is not allowed
                                        status.text('Only JPG, PNG or GIF files are allowed');
                                        return false;
                                    }
                                    status.text('\u0417агружаются данные...');
                                },
                                onComplete: function(file, response) {
                                    status.text('');
                                    if (response === "error") {
                                        return false;
                                    } else {
                                        status.text('Аватар успешно загружен!');
                                        $('#avatar').attr('src', response);
                                        $('#avatar_val').val(response);
                                    }
                                }
                            });
                        });
</script>
<div class="block_content_1">
    <div>
        <p class="style_kabinet_2_1">
            <?php
            if ($ShowParamID == 15) {
                echo 'Подать <b>вакансию</b>';
            }
            if ($ShowParamID == 17) {
                echo 'Подать <b>резюме</b>';
            }
            if ($ShowParamID == 16) {
                echo 'Редактировать ';
                if ($user_ads->type[0] == 1) {
                    echo '<b>вакансию</b>';
                }
                if ($user_ads->type[0] == 2) {
                    echo '<b>резюме</b>';
                }
            }
            ?>
        </p><br>
        <?php
        if (isset($_GET['comment'])) {
            echo '<b style="color: red;">' . $_GET['comment'] . '</b><br>';
        }
        ?>
        <form action="posts.php" method="POST">
            <table style="width: 100%;">
                <tr>
                    <td><p class="tit_text">Искомая должность:<b style="color: red; padding-left: 5px;">*</b></p></td>
                    <td><input class="all_inp" type="text" name="JobPost" value="<?php echo $user_ads->post[0]; ?>"></td>
                </tr>
                <tr>
                    <td><p class="tit_text">Зарплата:</p></td>
                    <td>
                        <p class="tit_text">
                            от <input class="all_inp_с" name="JobSalaryMin" type="text" value="<?php echo $user_ads->salary_min[0]; ?>"> до <input class="all_inp_с" name="JobSalaryMax" type="text" value="<?php echo $user_ads->salary_max[0]; ?>">
                            <select class="all_inp" name="JobCurrency">
                                <option <?php if ($user_ads->currency[0] == 1) echo 'selected=""'; ?> value="1">руб</option>
                                <option <?php if ($user_ads->currency[0] == 2) echo 'selected=""'; ?> value="2">$</option>
                                <option <?php if ($user_ads->currency[0] == 3) echo 'selected=""'; ?> value="3">€</option>
                            </select>
                            в месяц
                        </p>
                    </td>
                </tr>
                <tr>
                    <td><p class="tit_text">Возраст:</p></td>
                    <td>
                        <p class="tit_text">
                            <?php
                            if ($ShowParamID == 15 || $user_ads->type[0] == 1) {
                                ?>
                                от <input class="all_inp_с" name="JobAgeMin" type="text" value="<?php echo $user_ads->age_min[0]; ?>"> до <input class="all_inp_с" name="JobAgeMax" type="text" value="<?php echo $user_ads->age_max[0]; ?>">
                                <?php
                            }
                            ?>
                            <?php
                            if ($ShowParamID == 17 || $user_ads->type[0] == 2) {
                                ?>
                                <input class="all_inp_с" name="JobAgeMin" type="text" value="<?php echo $user_ads->age_min[0]; ?>">
                                <?php
                            }
                            ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td><p class="tit_text">Выберите пол:</p></td>
                    <td>
                        <p class="tit_text">
                            <?php
                            if ($user_ads->sex[0] == 0) {
                                ?>
                                <label><input type="radio" checked="" name="JobSex" value="0">Не важно</label>
                                <?php
                            } else {
                                ?>
                                <label><input type="radio" name="JobSex" value="0">Не важно</label>
                                <?php
                            }
                            ?>
                            <?php
                            if ($user_ads->sex[0] == 1) {
                                ?>
                                <label><input type="radio" checked="" name="JobSex" value="1">Мужской</label>
                                <?php
                            } else {
                                ?>
                                <label><input type="radio" name="JobSex" value="1">Мужской</label>
                                <?php
                            }
                            ?>
                            <?php
                            if ($user_ads->sex[0] == 2) {
                                ?>
                                <label><input type="radio" checked="" name="JobSex" value="2">Женский</label>
                                <?php
                            } else {
                                ?>
                                <label><input type="radio" name="JobSex" value="2">Женский</label>
                                <?php
                            }
                            ?>
                        </p>
                    </td>
                </tr>
                <?php
                $educations = new EducationTypes();
                ?>
                <tr>
                    <td><p class="tit_text">Образование:</p></td>
                    <td>
                        <select name="JobEducation_T">
                            <option value="0">Не указано</option>
                            <?php
                            for ($i = 0; $i < count($educations->id); $i++) {
                                if ($user_ads->education_t[0] == $educations->id[$i]) {
                                    echo '<option selected="selected" value="' . $educations->id[$i] . '">' . $educations->name[$i] . '</option>';
                                } else {
                                    echo '<option value="' . $educations->id[$i] . '">' . $educations->name[$i] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><p class="tit_text">Образование (подробно):</p></td>
                    <td><textarea name="JobEducation" class="area_exp_1"><?php echo $user_ads->education[0]; ?></textarea></td>
                </tr>
                <tr>
                    <td><p class="tit_text">Опыт работы:</p></td>
                    <td><textarea name="JobExperience" class="area_exp_1"><?php echo $user_ads->experience[0]; ?></textarea></td>
                </tr>
                <tr>
                    <td><p class="tit_text">График работы:</p></td>
                    <td>
                        <select class="all_inp" name="JobSchedule">
                            <option value="" <?php
                            if ($user_ads->schedule[0] == '') {
                                echo 'selected="selected"';
                            }
                            ?>>Не указан</option>
                            <option value="Полный день" <?php
                            if ($user_ads->schedule[0] == 'Полный день') {
                                echo 'selected="selected"';
                            }
                            ?>>Полный день</option>
                            <option value="Частичная занятость" <?php
                            if ($user_ads->schedule[0] == 'Частичная занятость') {
                                echo 'selected="selected"';
                            }
                            ?>>Частичная занятость</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><p class="tit_text">Требования:</p></td>
                    <td><textarea name="JobText" class="area_exp_1"><?php echo $user_ads->text[0]; ?></textarea></td>
                </tr>
                <?php
                if ($user_ads->type[0] == 1 || $ShowParamID == 15) {
                    ?>
                    <tr>
                        <td><p class="tit_text">Организация:</p></td>
                        <td><input class="all_inp" name="JobOrganization" type="text" value="<?php echo $user_ads->organization[0]; ?>"></td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td><p class="tit_text">Контактное лицо:</p></td>
                    <td><input class="all_inp" name="JobContactName" type="text" value="<?php echo $user_ads->contact_name[0]; ?>"></td>
                </tr>
                <?php
                if ($ShowParamID == 17 || $user_ads->type[0] == 2) {
                    ?>
                    <tr>
                        <td><p class="tit_text">Семейное положение:</p></td>
                        <td>
                            <select class="all_inp" name="JobMarital">
                                <option value="" <?php
                                if ($user_ads->marital[0] == '') {
                                    echo 'selected="selected"';
                                }
                                ?>>Не указан</option>
                                <option value="Холост / Не замужем" <?php
                                if ($user_ads->marital[0] == 'Холост / Не замужем') {
                                    echo 'selected="selected"';
                                }
                                ?>>Холост / Не замужем</option>
                                <option value="Женат / Замужем" <?php
                                if ($user_ads->marital[0] == 'Женат / Замужем') {
                                    echo 'selected="selected"';
                                }
                                ?>>Женат / Замужем</option>
                            </select>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td><p class="tit_text">Контактный телефон:<b style="color: red; padding-left: 5px;">*</b></p></td>
                    <td><input class="all_inp" type="text" name="JobContactPhone" value="<?php echo $user_ads->contact_phone[0]; ?>"></td>
                </tr>
                <tr>
                    <td><p class="tit_text">E-mail:<b style="color: red; padding-left: 5px;">*</b></p></td>
                    <td><input class="all_inp" type="text" name="JobEmail" value="<?php echo $user_ads->email[0]; ?>"></td>
                </tr>
                <?php
                if ($ShowParamID == 17 || $user_ads->type[0] == 2) {
                    ?>
                    <tr>
                        <td>
                            <p class="tit_text">Аватар:</p>
                        </td>
                        <td>
                            <?php
                            if ($user_ads->avatar[0] && file_exists($_SERVER['DOCUMENT_ROOT'] . $user_ads->avatar[0])) {
                                echo '<img id="avatar" style="width: 120px;" src="' . $user_ads->avatar[0] . '"><br>';
                            } else {
                                ?>
                                <img id="avatar" style="width: 120px;" src="../admin/images/noimage.png"><br>
                                <?php
                            }
                            ?>
                            <input type="hidden" name="JobAvatar" id="avatar_val" value="<?php echo $user_ads->avatar[0]; ?>">
                            <input class="act_2" type="button" id="JobUpload" value="Загрузить аватар"><br>
                            <span id="statusJob"></span>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td colspan="2">
                        <?php
                        if ($ShowParamID == 15) {
                            ?>
                            <input type="hidden" name="JobType" value="<?php echo $ShowParamID; ?>">
                            <input class="act_2" name="SaveJob" type="submit" value="Подать вакансию">
                            <?php
                        }
                        ?>
                        <?php
                        if ($ShowParamID == 16) {
                            ?>
                            <input type="hidden" name="JobID" value="<?php echo $_GET['ID']; ?>">
                            <input type="hidden" name="JobType" value="<?php echo $user_ads->type[0]; ?>">
                            <input class="act_2" name="SaveJob" type="submit" value="Сохранить">
                            <?php
                        }
                        ?>
                        <?php
                        if ($ShowParamID == 17) {
                            ?>
                            <input type="hidden" name="JobType" value="<?php echo $ShowParamID; ?>">
                            <input class="act_2" name="SaveJob" type="submit" value="Подать резюме">
                            <?php
                        }
                        ?>
                        <a class="act_2" href="./?PageType=6">&laquo; К объявлениям</a>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<?php
}
?>
<?php
if ($ShowParamID == 20) {
$inbox = new Messages();
$inbox->MessagesInbox();
?>
<div class="my_messages">
    <p class="style_kabinet_2_1">Ваши <b>входящие сообщения</b></p><br>
    <span><a class="act_2" href="./?PageType=1">Назад</a></span><br><br>
    <table class="table_add_obiavlenia" border="1">
        <tr>
            <td colspan="4"><b class="style_kabinet_6">Входящие сообщения<span class="style_kabinet_6_1"><?php echo $inbox->count; ?></span></b></td>
        </tr>
        <tr>
            <td class="td_name_table"><p class="style_kabinet_3">Текст</p></td>
            <td class="td_name_table"><p class="style_kabinet_3">От</p></td>
            <td class="td_name_table"><p class="style_kabinet_3">Дата</p></td>
            <td class="td_name_table"><p class="style_kabinet_3">Действие</p></td>
        </tr>
        <?php
        for ($i = 0; $i < count($inbox->id); $i++) {
            ?>
            <tr>
                <td><p class="znak_15"><?php echo mb_substr($inbox->text[$i], 0, 50, 'utf-8'); ?>...</p></td>
                <td><p class="znak_15"><?php echo $inbox->sender_login[$i]; ?></p></td>
                <td><p class="znak_15"><?php echo $inbox->date[$i]; ?></p></td>
                <td>
                    <?php
                    if ($inbox->read[$i] == 0) {
                        echo '<img class="icon_f" title="Непрочитано" onclick="ShowMessage(this);" src="../images/unread.png" alt="' . $inbox->id[$i] . '">';
                    }
                    if ($inbox->read[$i] == 1) {
                        echo '<img class="icon_f" title="Прочитано" onclick="ShowMessage(this);" src="../images/read.png" alt="' . $inbox->id[$i] . '">';
                    }
                    ?>
                    <img class="icon_f" onclick="DeleteMessage(this);" title="Удалить сообщение" src="../images/delete_team.png" alt="<?php echo $inbox->id[$i]; ?>">
                </td>
            </tr>
            <?php
        }
        ?>
    </table><br><br>

    <table class="table_add_obiavlenia" border="1" style="display: none;">
        <tr>
            <td style="width: 120px;"><p class="znak_15">Сообщение:</p></td>
            <td><p class="znak_16"></p></td>
        </tr>
        <tr>
            <td style="width: 120px;">
                <button class="prof_but" onclick="SendMessage(this);">Ответить</button>
            </td>
            <td>
                <textarea name="" class="area_lich"></textarea>
            </td>
        </tr>
    </table>

</div>
<?php
}
?>

<?php
if ($ShowParamID == 21) {
$outbox = new Messages();
$outbox->MessagesOutbox();
?>
<div class="my_messages">
    <p class="style_kabinet_2_1">Ваши <b>исходящие сообщения</b></p><br>
    <span><a class="act_2" href="./?PageType=1">Назад</a></span><br><br>
    <table class="table_add_obiavlenia" border="1">
        <tr>
            <td colspan="4"><b class="style_kabinet_6">Исходящие сообщения<span class="style_kabinet_6_1"><?php echo $outbox->count; ?></span></b></td>
        </tr>
        <tr>
            <td class="td_name_table"><p class="style_kabinet_3">Текст</p></td>
            <td class="td_name_table"><p class="style_kabinet_3">Кому</p></td>
            <td class="td_name_table"><p class="style_kabinet_3">Дата</p></td>
            <td class="td_name_table"><p class="style_kabinet_3">Действие</p></td>
        </tr>
        <?php
        for ($i = 0; $i < count($outbox->id); $i++) {
            ?>
            <tr>
                <td><p class="znak_15"><?php echo mb_substr($outbox->text[$i], 0, 50, 'utf-8'); ?>...</p></td>
                <td><p class="znak_15"><?php echo $outbox->user_login[$i]; ?></p></td>
                <td><p class="znak_15"><?php echo $outbox->date[$i]; ?></p></td>
                <td>
                    <?php
                    if ($outbox->read[$i] == 0) {
                        echo '<img class="icon_f" title="Непрочитано" onclick="ShowMessageOubox(this);" src="../images/unread.png" alt="' . $outbox->id[$i] . '">';
                    }
                    if ($outbox->read[$i] == 1) {
                        echo '<img class="icon_f" title="Прочитано" onclick="ShowMessageOubox(this);" src="../images/read.png" alt="' . $outbox->id[$i] . '">';
                    }
                    ?>
                    <img class="icon_f" onclick="DeleteMessageOutbox(this);" title="Удалить сообщение" src="../images/delete_team.png" alt="<?php echo $outbox->id[$i]; ?>">
                </td>
            </tr>
            <?php
        }
        ?>
    </table><br><br>
    <table class="table_add_obiavlenia" border="1" style="display: none;">
        <tr>
            <td style="width: 120px;"><p class="znak_15">Сообщение:</p></td>
            <td><p class="znak_16"></p></td>
        </tr>
        <tr>
            <td style="width: 120px;">
                <button class="prof_but" onclick="SendMessageOutbox(this);">Написать</button>
            </td>
            <td><textarea name="" class="area_lich"></textarea></td>
        </tr>
    </table>
</div>
<?php
}
?>
<?php
if ($ShowParamID == 22) {
require_once '../admin/admin_catalog/inc/classes.php';
$user_ads = new Organizations(1, ' WHERE c_cf_user="' . $_SESSION['login'] . '" ', 0, 0);
?>
<div class="block_content_1">
    <p class="style_kabinet_2_1">Все <b>организации</b></p><br>
    <span>
        <a class="add_rfbinet_obiavlenie" style="float: right;" title="Добавить организацию" href="./?PageType=23">
            Добавить организацию
        </a>
        <a class="act_2" href="./?PageType=1">
            Назад
        </a>
    </span>
    <br><br>
    <table class="table_add_obiavlenia" border="1">
        <tr>
            <td colspan="7"><b class="style_kabinet_6">Организации<span class="style_kabinet_6_1"><?php echo $user->photo_count; ?></span></b></td>
        </tr>
        <tr>
            <td class="td_name_table"><p class="style_kabinet_3">№</p></td>
            <td class="td_name_table"><p class="style_kabinet_3">Название организации</p></td>
            <td class="td_name_table"><p class="style_kabinet_3">E-mail</p></td>
            <td class="td_name_table"><p class="style_kabinet_3">Сайт</p></td>
            <td class="td_name_table"><p class="style_kabinet_3">Дейсвие</p></td>
        </tr>
        <?php
        for ($i = 0; $i < count($user_ads->id); $i++) {
            ?>
            <tr>
                <td><p class="znak_15"><?php echo $user_ads->id[$i]; ?></p></td>
                <td><p class="znak_15"><?php echo $user_ads->name[$i]; ?></p></td>
                <td><p class="znak_15"><?php echo $user_ads->email[$i]; ?></p></td>
                <td><p class="znak_15"><?php echo $user_ads->site[$i]; ?></p></td>
                <td>
                    <a <?php echo 'href="./?PageType=23&ID=' . $user_ads->id[$i] . '"'; ?>><img class="icon_f" title="Редактировать" src="../images/edit.png" alt=""></a>
                    <img class="icon_f" <?php echo 'onclick="DeleteAd(' . $user_ads->id[$i] . ');"'; ?> title="Удалить объявление" src="../images/delete_team.png" alt="">
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
if ($ShowParamID == 23) {
require_once '../admin/admin_catalog/inc/classes.php';
$_GET['ID'] = filter_var($_GET['ID'], FILTER_VALIDATE_INT);
$user_ads = new Organizations(1, ' WHERE c_cf_user="' . $_SESSION['login'] . '" AND k_cf_id=' . $_GET['ID'] . ' ', 0, 0);
if (count($user_ads->id) > 0) {
    $user_ads->LoadOne($_GET['ID']);
} else {
    exit();
}
$categories = new CatalogCategories();
?>
<div class="block_content_1">
    <p class="style_kabinet_2_1">Редактирование <b>организации</b></p><br>
    <form action="posts.php" method="POST">
        <table style="width: 100%;">
            <tr>
                <td><p class="tit_text">Наименование:</p></td>
                <td>
                    <input class="all_inp" name="CatalogName" type="text" value="<?php echo $user_ads->name[0]; ?>">
                </td>
            </tr>
            <tr>
                <td><p class="tit_text">E-mail:</p></td>
                <td>
                    <input class="all_inp" name="CatalogEmail" type="text" value="<?php echo $user_ads->email[0]; ?>">
                </td>
            </tr>
            <tr>
                <td><p class="tit_text">Сайт:</p></td>
                <td>
                    <input class="all_inp" name="CatalogSite" type="text" value="<?php echo $user_ads->site[0]; ?>">
                </td>
            </tr>
            <tr>
                <td><p class="tit_text">Описание:</p></td>
                <td><textarea class="area_exp_1" name="CatalogDescr"><?php echo $user_ads->descr[1]; ?></textarea></td>
            </tr>
            <?php
            for ($a = 0; $a < count($user_ads->category[$user_ads->id[0]]); $a++) {
                $subs = new CatalogSubCategories(' WHERE k_cc_id=' . $user_ads->category[$user_ads->id[0]][$a] . ' ');
                $subsub = new SubSubcategories(1, ' WHERE k_cbs_parent=' . $user_ads->big_sub[$user_ads->id[0]][$a] . ' ', 'unlimit');
                ?>
                <tr>
                    <td><p class="tit_text">Категории:</p></td>
                    <td>
                        <select class="all_inp">
                            <?php
                            for ($i = 0; $i < count($categories->id); $i++) {
                                if ($user_ads->category[$user_ads->id[0]][$a] == $categories->id[$i]) {
                                    echo '<option selected="" value="' . $categories->id[$i] . '">' . $categories->name[$i] . '</option>';
                                } else {
                                    echo '<option value="' . $categories->id[$i] . '">' . $categories->name[$i] . '</option>';
                                }
                            }
                            ?>
                        </select>
                        <select class="all_inp">
                            <?php
                            for ($i = 0; $i < count($subs->id_sub); $i++) {
                                if ($user_ads->big_sub[$user_ads->id[0]][$a] == $subs->id_sub[$i]) {
                                    echo '<option selected="" value="' . $subs->id_sub[$i] . '">' . $subs->name_sub[$i] . '</option>';
                                } else {
                                    echo '<option value="' . $subs->id_sub[$i] . '">' . $subs->name_sub[$i] . '</option>';
                                }
                            }
                            ?>
                        </select>
                        <select class="all_inp">
                            <?php
                            for ($i = 0; $i < count($subsub->id_ss); $i++) {
                                if ($user_ads->sub[$user_ads->id[0]][$a] == $subsub->id_ss[$i]) {
                                    echo '<option selected="" value="' . $subsub->id_ss[$i] . '">' . $subsub->name_ss[$i] . '</option>';
                                } else {
                                    echo '<option value="' . $subsub->id_ss[$i] . '">' . $subsub->name_ss[$i] . '</option>';
                                }
                            }
                            ?>
                        </select>
                        <img class="icon_f" title="Удалить подкатегорию" src="../images/delete_team.png" alt="<?php echo $user_ads->sub[$user_ads->id[0]][$a]; ?>">
                    </td>
                </tr>
                <?php
            }
            ?>
            <tr>
                <td colspan="2">
                    <?php
                    echo '<input name="CatalogId" type="hidden" value="' . $user_ads->id[0] . '">';
                    ?>
                    <input class="act_2" name="SaveCatalog" type="submit" value="Сохранить">
                    <a class="act_2" href="./?PageType=22">&laquo; К списку</a>
                </td>
            </tr>
        </table>
    </form>
</div>
<?php
}
?>
</div>
</div>
<div class="right_all_block">
<div class="shapka_bloka">
<a class="name_shapka">Условия регистрации</a>
</div>
<p class="text_right_block_kabinet">
<?php
echo $content_page->text;
?>
</p>
</div>
</div>

<?php
require_once '../inc/footer.php';
?>
</div>
</body>
</html>