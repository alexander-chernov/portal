<?php
define('TOMSKLINE', 1);
session_start();
$title = '';
$page = 0;
$page_2 = 0;
if (isset($_GET['user'])) {
    $title = 'Пользовательское соглашение';
    $page = 7;
    $page_2 = 14;
}
if (isset($_GET['ad'])) {
    $title = 'Отдел рекламы';
    $page = 8;
    $page_2 = 15;
}
if (isset($_GET['contacts'])) {
    $title = 'Контактная информация';
    $page = 26;
    $page_2 = 27;
}
        require_once 'inc/configs.php';
        require_once 'inc/functions.php';
        require_once 'admin/admin_gl/inc/classes.php';
        if (YourIPBanned()) {
            header($_ENV['SERVER_PROTOCOL'] . " 401 IP BAN", true, 401);
            header("Location: http://" . _SERVER_ADDRESS . "/pageBan.php");
        }
        try {
            if (isset($_COOKIE['login'])) {
                $_SESSION['login'] = $_COOKIE['login'];
                $_SESSION['password'] = $_COOKIE['password'];
            }
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT k_ku_id,k_u_privileges FROM k_users WHERE k_ku_login=:login AND k_ku_password=:password');
            $query->execute(array(":login" => $_SESSION['login'], ":password" => $_SESSION['password']));
            $result = $query->fetch(PDO::FETCH_ASSOC);
            if ($query->rowCount() > 0) {
                $_SESSION['id'] = $result['k_ku_id'];
                $_SESSION['privileges'] = $result['k_u_privileges'];
            } else {
                unset($_SESSION);
            }
        } catch (PDOException $e) {
            exit();
        }
        $banners = new BannersAll();

        $content_page = new PageContent($page);
        $content_page_2 = new PageContent($page_2);
        ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <title><?php echo $title; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="keywords" content="">
        <meta name="description" content="">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel="stylesheet" type="text/css" href="css/style_wind.css">
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script type="text/javascript" src="js/wind.js"></script>
        <script type="text/javascript" src="js/registration.js"></script>
        <!--Отловить размер окна меню-->
        <script type="text/javascript">
            function ResizeMenu()
            {
                if ($('#show_menu').outerWidth() > 1250) {
                    $('#show_menu_1').show(100);
                    $('#show_menu_2').hide(100);
                } else  {
                    $('#show_menu_1').hide(100);
                    $('#show_menu_2').show(100);
                }
                var w = Math.round($('.reklama').width()/2-60);
                $("#banner1").width(w);
                $("#banner2").width(w);
                $("#banner3").width(w);
                $("#banner4").width(w);
            }
            $(window).resize(function() {
                ResizeMenu();
            });
            $(window).ready(function() {
                ResizeMenu();
            });
        </script>
        <!--Отловить размер окна меню-->
        <?php
        //ColorsOnPage();
        ?>
    </head>
    <body>
    <?php
    require_once 'inc/header.php';
    ?>
            <div class="center_all_block">
                <div class="left_all_block">
                    <div class="content_left_block">
                        <?php
                        echo $content_page->text;
                        ?>
                    </div>
                </div>
                <div class="right_all_block">
                    <div class="shapka_bloka">
                        <a class="name_shapka">Какое-то описание</a>
                    </div>
                    <?php
                    echo $content_page_2->text;
                    ?>
                </div>
            </div>

    <?php
    require_once 'inc/footer.php';
    ?>


            <!--ВСПЛЫВАЮЩИЕ ОКНА-->       

            <div class="temno" id="temno"></div>   <!--Всплывающие окна конец-->
            <div class="prozrachno" id="prozrachno" onclick="$('.vhod_block_gl').hide(500);
                    disableP();"></div>   <!--Всплывающие окна прозрачное для закрытия входа-->
        </div>
    </body>
</html>