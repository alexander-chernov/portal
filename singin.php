<?php
define('TOMSKLINE', 1);
session_start();
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
        if (isset($_POST['forget'])) {
            userForgetPass($_POST['email']);
            $comment = 'Письмо было выслано на ваш E-mail.';
        }

        $content_page_1 = new PageContent(5);
        $content_page_2 = new PageContent(6);
        ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <title>Портал Недвижимости</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="keywords" content="">
        <meta name="description" content="">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel="stylesheet" type="text/css" href="css/style_wind.css">
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script type="text/javascript" src="js/wind.js"></script>
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
    </head>
    <body>
    <?php
    require_once 'inc/header.php';
    ?>
<?php /*
        <div class="gl_block_gl">
            <div class="top_block">                     <!--Шапка портала начало-->
                <div class="info_reg"> <!--Блок ЗАРЕГИСТРИРОВАННОГО ПОЛЬЗОВАТЕЛЯ-->
                    <?php
                    if (isset($_SESSION['login'])) {
                        UpdateActivityUser();
                        ?>
                        <a class="inf_text_1" title="Новые сообщения" href="profile/?PageType=20"><?php echo NewMessages(); ?></a>
                        <a class="inf_text_3" href="profile/index.php"><?php echo $_SESSION['login']; ?></a>
                        <a class="inf_text_2" href="exit.php">Выход</a>
                        <?php
                    }
                    ?>
                    <?php
                    if (!isset($_SESSION['login'])) {
                        ?>
                        <a class="inf_text_2" href="registration.php">Регистрация</a>
                        <a class="inf_text_2" onclick="$('.vhod_block_gl').show(500);
                    enableP();">Вход</a>
                        <div class="vhod_block_gl">
                            <svg class="strelka">
                                <polygon points="0,15 15,0 30,15" fill="rgb(255,255,255)" width="1" stroke="rgba(0,0,0,0.2)"/>
                            </svg>
                            <form method="post" action="testreg.php">
                                <div>
                                    <input name="login_name" id="login_name" class="input_vhod_gl" type="text" placeholder="Логин" value="">
                                    <input name="pass_value" id="pass_value" class="input_vhod_gl" type="password" placeholder="Пароль" value="">
                                    <a href="singin.php" class="forgot">Напомнить пароль</a>
                                    <label class="rememb"><input type="checkbox" name="save" value="1">Запомнить меня</label>
                                    <input type="submit" class="act" onclick="return FormSubmitCheck();
                    return false;" value="Войти">
                                </div>
                            </form>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <div class="left_shapka">
                    <div class="vizitka">
                        <img src="images/logo_top.png" alt="">
                    </div>
                    <div class="block_baner">
                        <?php
                        if ($banners->banner_end_days[0] > 0) {
                            echo str_replace('../images/banners/', 'admin/images/banners/', $banners->banner_code[0]);
                        }
                        ?>
                    </div>
                    <form action="catalog/" method="GET">
                        <div class="search_panel">
                            <input class="text_inp_ser" type="text" name="search_string" value="">
                            <input class="but_img_ser" type="submit" value="">
                        </div>
                    </form>
                </div>
                <div class="right_shapka">
                    <div class="info_block">
                        <div class="info_pogoda">
                            <embed height="90" width="195" name="plugin" wmode="transparent"
                                   src="http://www.meteonova.ru/flashinformer/pinf.swf?city=29430&cs=1" 
                                   type="application/x-shockwave-flash"/>
                        </div>
                        <div class="info_valuta">
                            <img style="width: 100%;" src="images/valuta.jpg" alt="">
                        </div>
                    </div>
                </div>
            </div>                                  <!--Шапка портала конец-->
            <div class="gl_menu" id="show_menu">                   <!--Меню начало-->
                <a class="gl_menu_a" href="./">Главная</a>
                <a class="gl_menu_a" href="realty/">Недвижимость</a>
                <a class="gl_menu_a" href="photoboard/">Фото Объявления</a>
                <a class="gl_menu_a" href="expert/">Эксперты</a>
                <div id="show_menu_1">
                    <a class="gl_menu_a" href="blog/">Статьи</a>
                    <a class="gl_menu_a" href="webcam/">Веб-камеры</a>
                    <a class="gl_menu_a" href="sites/">Сайты</a>
                </div>
                <a class="gl_menu_a" href="job/">Работа</a>
                <a class="gl_menu_a" href="catalog/">Каталог</a>
                <a class="gl_menu_a" href="map/">Карта</a>
                <div id="show_menu_2" onmouseover="$('.elem_open_menu').show()" onmouseout="$('.elem_open_menu').hide()">
                    <a class="gl_menu_a">Еще</a>
                    <div class="conteiner_img_menu"></div>
                    <div class="elem_open_menu">
                        <img class="img_str_m" src="images/str_m.png" alt="">
                        <a class="el_op_men_1" href="webcam/">Веб-камеры</a>
                        <img class="img_podcherk" src="images/line_menu.png" alt="">
                        <a class="el_op_men_1" href="blog/">Статьи</a>
                        <img class="img_podcherk" src="images/line_menu.png" alt="">
                        <a class="el_op_men_2" href="sites/">Сайты</a>
                    </div>
                </div>
                <div id="show_menu_3" onmouseover="$('.elem_open_menu_1').show()" onmouseout="$('.elem_open_menu_1').hide()">
                    <a class="gl_but_men">ПОДАТЬ ОБЪЯВЛЕНИЕ</a>
                    <div class="conteiner_img_menu"></div>
                    <div class="elem_open_menu_1">
                        <img class="img_str_m" src="images/str_m.png" alt="">
                        <a class="el_op_men_1" href="profile/?PageType=4">В недвижимость</a>
                        <img class="img_podcherk" src="images/line_menu.png" alt="">
                        <a class="el_op_men_1" href="profile/?PageType=5">В фотообъявления</a>
                        <img class="img_podcherk" src="images/line_menu.png" alt="">
                        <a class="el_op_men_2" href="expert/?Howto=1">Стать Экспертом</a>
                        <img class="img_podcherk" src="images/line_menu.png" alt="">
                        <a class="el_op_men_2" href="blog/?Howto=1">Написать статью</a>
                        <img class="img_podcherk" src="images/line_menu.png" alt="">
                        <a class="el_op_men_2" href="profile/?PageType=6">В работу</a>
                        <img class="img_podcherk" src="images/line_menu.png" alt="">
                        <a class="el_op_men_2" href="catalog/?Howto=1">Добавить в каталог</a>
                    </div>
                </div>
            </div>                                  <!--Меню конец-->
*/ ?>
            <div class="center_all_block">
                <div class="left_all_block">

                        <div class="shapka_bloka">
                            <a class="name_shapka">Забыли пароль?</a>
                        </div>
                        <div class="obveden_block">

                        <span class="title_text_reg">Забыли пароль ?</span><br>
                        <span class="title_text_reg_1">Введите вашу почту и нажмите отправить, вам будет направлено письмо с вашими данными. Пожалуйста проверьте почту.</span><br>
                        <span class="title_text_reg_2">
                            <?php
                            echo $content_page_1->text;
                            ?>
                        </span>
                        <form action="singin.php" method="post">
                            <table class="table_registration">
                                <tr>
                                    <td><a class="team_reg">Введите ваш E-mail:</a></td>
                                    <td><input class="inp_reg" name="email" type="text" value=""></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><input type="submit" class="act" name="forget" value="Отправить"></td>
                                </tr>
                            </table>
                        </form>
                        <?php
                        echo '<span class="title_text_reg_2">' . $comment . '</span>';
                        ?>
                        </div>

                </div>
                <div class="right_all_block">
                    <div class="shapka_bloka">
                        <a class="name_shapka">Какое-то описание</a>
                    </div>
                    <div class="obveden_block">
                    <p>
                        <?php
                        echo $content_page_2->text;
                        ?>
                    </p>
                    </div>
                </div>
            </div>


    <?php
    require_once 'inc/footer.php';
    ?>

            <!--ВСПЛЫВАЮЩИЕ ОКНА-->       

            <div class="temno" id="temno"></div>   <!--Всплывающие окна конец-->
            <div class="prozrachno" id="prozrachno" onclick="$('.vhod_block_gl').hide(500);
                disableP();"></div>   <!--Всплывающие окна прозрачное для закрытия входа-->

            <script type="text/javascript">
            function ShowPhoto() {
                $('.photo_map_vsplivaet').show(500);
                $(document).mousemove(function(e) {
                    var x = e.pageX;
                    var y = e.pageY;
                    $('.photo_map_vsplivaet').css('left', x);
                    $('.photo_map_vsplivaet').css('top', y);
                });
            }
            function HidePhoto() {
                $('.photo_map_vsplivaet').hide(500);
            }
            </script>
            <div class="photo_map_vsplivaet">
                <img src="images/kottedg.jpg" alt="">
            </div>
        </div>
    </body>
</html>