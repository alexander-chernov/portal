<?php
define('TOMSKLINE', 1);
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="ru">
    <head>
        <title>TOMSK-LINE.RU. Web-камеры</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <?php
        require_once '../inc/configs.php';
        require_once 'inc/classes.php';
        require_once 'inc/functions.php';
        require_once '../inc/functions.php';
        require_once '../admin/admin_webcam/inc/classes.php';
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
            unset($e);
            exit();
        }

        if (!isset($_GET['PageType'])) {
            $ShowParamID = 1;
        } else {
            $ShowParamID = filter_var($_GET['PageType'], FILTER_VALIDATE_INT);
        }
        if (!isset($_GET['PageIndex'])) {
            $page = 1;
        } else {
            $page = filter_var($_GET['PageIndex'], FILTER_VALIDATE_INT);
        }
        if (!isset($_GET['limit'])) {
            $limit = 20;
        } else {
            $limit = filter_var($_GET['limit'], FILTER_VALIDATE_INT);
        }

        $banners = new BannersAll(0);
        $webcams = new WebcamsSite($page, $limit);
        ?>
        <meta name="keywords" content="">
        <meta name="description" content="">
        <link rel="stylesheet" type="text/css" href="../css/style.css">
        <link rel="stylesheet" type="text/css" href="css/webcam.css">
        <link rel="stylesheet" type="text/css" href="../css/style_wind.css">
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script type="text/javascript" src="../js/wind.js"></script>
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
            $(window).resize(function(){ResizeMenu();});
            $(window).ready(function(){ResizeMenu();});
        </script>
        <!--Отловить размер окна меню-->
        <?php
       //ColorsOnPage();
       ?>
    </head>
    <body>
    <?php
    require_once '../inc/header.php';
    ?>

            <div class="all_webcam_block">

                <div id="webcam_1" class="block_content_1">   <!--Все вебкамеры-->
                    <div class="kriteri_webcam">
                        <div class="shapka_bloka">
                            <p class="style_shapka_4">Веб-камеры г.Томска</p>
                            <p class="style_shapka_3" title="Всего объявлений"><?php echo $webcams->all_cams; ?></p>
                        </div>
                    <div class="obveden_block">

                        <?php
                        $webcams->GenerateNavigation($page, $limit);
                        ?>

                        <div class="cameri">
                            <div class="visible_content">
                                <span class="visible_content_text">Показывать по  
                                    <a href="?limit=20">20</a>
                                    <a href="?limit=30">30</a> 
                                    <a href="?limit=50">50</a>
                                </span>
                            </div>
                            <?php
                            for ($i = 0; $i < count($webcams->id); $i++) {
                                ?>
                                <div class="cam_block">
                                    <?php
                                    echo '<a href="' . $webcams->url[$i] . '">';
                                    if ($webcams->image[$i] && file_exists('../admin/' . $webcams->image[$i])) {
                                        echo '<img class="img_webcam" src="../admin/' . $webcams->image[$i] . '" alt="">';
                                    } else {
                                        echo '<img class="img_webcam" src="../images/noimage.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <p class="text_webcam"><?php echo $webcams->name[$i]; ?></p>
                                </div>
                                <?php
                            }
                            ?>
                        </div>


                        <?php
                        $webcams->GenerateNavigation($page, $limit);
                        ?>
                    </div>
                </div>
                </div>
            </div>

    <?php
    require_once '../inc/footer.php';
    ?>

            <!--ВСПЛЫВАЮЩИЕ ОКНА -->

            <div class="temno" id="temno"></div>   <!--Всплывающие окна конец-->
            <div class="prozrachno" id="prozrachno" onclick="$('.vhod_block_gl').hide(500);
                                disableP();"></div>   <!--Всплывающие окна прозрачное для закрытия входа-->
        </div>
    </body>
</html>