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

        //Нажатие на картинку
        if (isset($_POST['submit_x']) && isset($_POST['submit_y'])) {
            if (!empty($_POST['reg_name']) &&
                    !empty($_POST['reg_sname']) &&
                    !empty($_POST['reg_email']) &&
                    !empty($_POST['reg_phone']) &&
                    !empty($_POST['reg_login']) &&
                    !empty($_POST['reg_password']) &&
                    !empty($_POST['reg_password2'])) {
                //Если поля заполнены
                $data = base64_decode($_SESSION['captcha_image_code']);
                $captcha_image = imagecreatefromstring($data);
                $x = $_POST['submit_x'];
                $y = $_POST['submit_y'];

                //Проверяем цвет пикселя, на который было произведено нажатие
                $rgb = imagecolorat($captcha_image, $x, $y);
                $color_tran = imagecolorsforindex($captcha_image, $rgb);

                //Проверка, если цвет исключительно красный

                $captcha_ok = ($color_tran['red'] == 255 && $color_tran['green'] == 0 && $color_tran['blue'] == 0 && $color_tran['alpha'] == 0);

                //Проверка пройдена
                if ($captcha_ok) {
                    $ad_success = TRUE;
                } else {
                    $comment = "Убедитесь, что вы нажали в розовый кружочек!";
                    $ad_success = FALSE;
                }
            } else {
                $comment = "Пожалуйста, заполните все обязательные поля!";
                $ad_success = FALSE;
            }
            if ($ad_success) {
                $_POST['reg_name'] = filter_var($_POST['reg_name'], FILTER_SANITIZE_STRIPPED);
                $_POST['reg_sname'] = filter_var($_POST['reg_sname'], FILTER_SANITIZE_STRIPPED);
                $_POST['reg_lname'] = filter_var($_POST['reg_lname'], FILTER_SANITIZE_STRIPPED);
                $_POST['reg_email'] = filter_var($_POST['reg_email'], FILTER_SANITIZE_STRIPPED);
                $_POST['reg_phone'] = filter_var($_POST['reg_phone'], FILTER_SANITIZE_STRIPPED);
                $_POST['reg_login'] = filter_var($_POST['reg_login'], FILTER_SANITIZE_STRIPPED);
                $_POST['reg_password'] = md5(filter_var($_POST['reg_password'], FILTER_SANITIZE_STRIPPED));
                $_POST['reg_password2'] = md5(filter_var($_POST['reg_password2'], FILTER_SANITIZE_STRIPPED));
                try {
                    $query = $mysql->prepare('SELECT * FROM k_users WHERE k_ku_login=:login OR k_ku_email=:email OR k_ku_phone=:phone');
                    $query->execute(array(":login" => $_POST['reg_login'],
                        ":email" => $_POST['reg_email'],
                        ":phone" => $_POST['reg_phone']));
                    if ($query->rowCount() > 0) {
                        $comment = "Введённые данные используются другим пользователем!";
                    } else {
                        $ip = getenv("HTTP_X_FORWARDED_FOR");
                        if (empty($ip) || $ip == 'unknown') {
                            $ip = getenv("REMOTE_ADDR");
                        }
                        $query2 = $mysql->prepare('INSERT INTO k_users
                            (k_ku_login,k_ku_password,k_ku_last_ip,k_ku_autor_date,k_ku_fname,k_ku_lname,k_ku_oname,k_ku_email,k_ku_phone,k_ku_last_date)
                            VALUES (:login,:password,:ip,NOW(),:fname,:lname,:oname,:email,:phone,NOW())');
                        $query2->execute(array(":login" => $_POST['reg_login'],
                            ":password" => $_POST['reg_password2'],
                            ":ip" => $ip,
                            ":fname" => $_POST['reg_name'],
                            ":lname" => $_POST['reg_sname'],
                            ":oname" => $_POST['reg_lname'],
                            ":email" => $_POST['reg_email'],
                            ":phone" => $_POST['reg_phone']));
                        $id = $mysql->lastInsertId();
                        $code = md5(time());
                        $query3 = $mysql->prepare('DELETE kuwr.*,ku.*
                            FROM k_users_wait_reg AS kuwr
                            LEFT JOIN k_users AS ku ON (ku.k_ku_id = kuwr.k_wr_user_id)
                            WHERE k_wr_date_code>:date');
                        $query3->execute(array(":date" => date('Y-m-d H:i:s', time() + 24 * 60 * 60)));
                        $query4 = $mysql->prepare('INSERT INTO k_users_wait_reg (k_wr_user_id,k_wr_reg_code,k_wr_date_code)
                            VALUES (:id,:code,NOW())');
                        $query4->execute(array(":id" => $id, ":code" => $code));
                        mailCode($_POST['reg_email'], $code, $_POST['reg_login']);
                        $_POST['reg_name'] = '';
                        $_POST['reg_sname'] = '';
                        $_POST['reg_lname'] = '';
                        $_POST['reg_email'] = '';
                        $_POST['reg_phone'] = '';
                        $_POST['reg_login'] = '';
                        $_POST['reg_password'] = '';
                        $_POST['reg_password2'] = '';
                    }
                } catch (PDOException $e) {
                    exit();
                }
            }
        } else {
            $comment = "Для завершения регистрации нажмите на яркий кружочек на картинке.";
        }
        ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
    <head>
        <title>Ошибка авторизации</title>
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
    <?php
    /*

        <div class="gl_block_gl">
            <div class="top_block">                     <!--Шапка портала начало-->
                <div class="info_reg"> <!--Блок ЗАРЕГИСТРИРОВАННОГО ПОЛЬЗОВАТЕЛЯ-->
                    <?php
                    if (isset($_SESSION['login'])) {
                        UpdateActivityUser();
                        ?>
                        <a class="inf_text_1" title="Новые сообщения" href="profile/?PageType=20"><?php echo NewMessages(); ?></a>
                        <a class="inf_text_3" href="profile/"><?php echo $_SESSION['login']; ?></a>
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
            <div class="gl_menu">                   <!--Меню начало-->
                <a class="gl_menu_a" href="./">Главная</a>
                <a class="gl_menu_a" href="realty/">Недвижимость</a>
                <a class="gl_menu_a" href="photoboard/">Фото Объявления</a>
                <a class="gl_menu_a" href="expert/">Эксперты</a>
                <a class="gl_menu_a" href="blog/">Статьи</a>
                <a class="gl_menu_a" href="webcam/">Веб-камеры</a>
                <a class="gl_menu_a" href="job/">Работа</a>
                <a class="gl_menu_a" href="catalog/">Каталог</a>
                <a class="gl_menu_a" href="map/">Карта</a>
                <div class="block_add_object" onmouseover="$('.menu_object').show()" onmouseout="$('.menu_object').hide()">
                    <a class="add_object">ПОДАТЬ ОБЪЯВЛЕНИЕ</a>
                    <div>
                        <div class="menu_object">
                            <div class="line_menu_object">
                                <a href="">Недвижимость</a>
                            </div>
                            <div class="line_menu_object">
                                <a href="">Фото объявление</a>
                            </div>
                            <div class="line_menu_object">
                                <a href="">Стать Экспертом</a>
                            </div>
                            <div class="line_menu_object">
                                <a href="">Работа</a>
                            </div>
                            <div class="line_menu_object">
                                <a href="">Каталог</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>                                  <!--Меню конец-->
*/
?>
            <div class="center_all_block">
                <h1>Ошибка авторизации</h1>
                <?php
                    if(isset($_GET['error'])) {
                        ?>
                                <p>Отказано в доступе!</p>
                                <p>Вы ввели логин или пароль неверно более трёх раз. Вам закрыт доступ в течение 15 минут.</p>
                    <?php
                    } else {
                    ?>
                                <p>Логин или пароль неверен!</p>
                                <p>Возможно Ваш аккаунт еще не активирован. Дождитесь письма на Ваш Email с инструкциями по активации аккаунта. Если Вы ввели логин или пароль неверно более трёх раз, Вам будет закрыт доступ в течение 15 минут.</p>
                    <?php
                    }
                    ?>
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
