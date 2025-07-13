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
        //unset($_SESSION);
    }
} catch (PDOException $e) {
    exit();
}
$banners = new BannersAll();

//Нажатие на картинку
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['submit_x']) && isset($_POST['submit_y'])) {
        if (!empty($_POST['reg_name']) &&
            !empty($_POST['reg_sname']) &&
            !empty($_POST['reg_email']) &&
            !empty($_POST['reg_login']) &&
            !empty($_POST['reg_password']) &&
            !empty($_POST['reg_password2'])
        ) {
            //Если поля заполнены
            $data = base64_decode($_SESSION['captcha_image_code']);
            if (!empty($data)) {
                $captcha_image = imagecreatefromstring($data);
                $x = $_POST['submit_x'];
                $y = $_POST['submit_y'];

                //Проверяем цвет пикселя, на который было произведено нажатие
                $rgb = imagecolorat($captcha_image, $x, $y);
                $color_tran = imagecolorsforindex($captcha_image, $rgb);

                //Проверка, если цвет исключительно красный (, 229, 48, 57))
                $captcha_ok = ($color_tran['red'] == 255 && $color_tran['green'] == 0 && $color_tran['blue'] == 0 && $color_tran['alpha'] == 0);

                //Проверка пройдена
                if ($captcha_ok) {
                    $ad_success = TRUE;
                } else {
                    $comment = "Убедитесь, что вы нажали в розовый кружочек!";
                    $ad_success = FALSE;
                }
            } else {
                $comment = "Картинка не пришла! " . $_SERVER[''];
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
            $_POST['reg_email'] = filter_var($_POST['reg_email'], FILTER_SANITIZE_EMAIL);
            $_POST['reg_phone'] = filter_var($_POST['reg_phone'], FILTER_SANITIZE_STRIPPED);
            $_POST['reg_login'] = filter_var($_POST['reg_login'], FILTER_SANITIZE_STRIPPED);
            $_POST['reg_password'] = md5(filter_var($_POST['reg_password'], FILTER_SANITIZE_STRIPPED));
            $_POST['reg_password2'] = md5(filter_var($_POST['reg_password2'], FILTER_SANITIZE_STRIPPED));
            $_POST['reg_type'] = filter_var($_POST['reg_type'], FILTER_VALIDATE_INT);
            $_POST['reg_agentname'] = filter_var($_POST['reg_agentname'], FILTER_SANITIZE_STRIPPED);
            try {
                //$query = $mysql->prepare('SELECT * FROM k_users WHERE k_ku_login=:login OR k_ku_email=:email OR k_ku_phone=:phone');
                //$query->execute(array(":login" => $_POST['reg_login'],":email" => $_POST['reg_email'],":phone" => $_POST['reg_phone']));
                $query = $mysql->prepare('SELECT * FROM k_users WHERE k_ku_login=:login OR k_ku_email=:email');
                $query->execute(array(":login" => $_POST['reg_login'],":email" => $_POST['reg_email']));
                //var_dump($query->rowCount());
                //die();
                if ($query->rowCount() > 0) {
                    $comment = "Такой пользователь уже есть! Нельзя зарегистрировать пользователя с этими данными.";
                    $ad_success = FALSE;
                } else {
                    $ip = getenv("HTTP_X_FORWARDED_FOR");
                    if (empty($ip) || $ip == 'unknown') {
                        $ip = getenv("REMOTE_ADDR");
                    }
                    $query2 = $mysql->prepare('INSERT INTO k_users
                                (k_ku_login,k_ku_password,k_ku_last_ip,k_ku_autor_date,k_ku_fname,k_ku_lname,k_ku_oname,k_ku_email,k_ku_phone,k_ku_last_date,k_ku_type)
                                VALUES (:login,:password,:ip,NOW(),:fname,:lname,:oname,:email,:phone,NOW(),:type)');
                    $query2->execute(array(":login" => $_POST['reg_login'],
                            ":password" => $_POST['reg_password2'],
                            ":ip" => $ip,
                            ":fname" => $_POST['reg_name'],
                            ":lname" => $_POST['reg_sname'],
                            ":oname" => $_POST['reg_lname'],
                            ":email" => $_POST['reg_email'],
                            ":phone" => $_POST['reg_phone'],
                            ":type" => $_POST['reg_type']
                        ));
                    $id = $mysql->lastInsertId();
                    if ($_POST['reg_type'] == 1) {
                        $query2 = $mysql->prepare('INSERT INTO k_users_agents
                                (k_ua_name,k_ua_avatar,k_ua_phone,k_ua_site,k_ua_user_parent,k_ua_description,k_ua_last_date)
                                VALUES (:name,"","","",:id,"",NOW())');
                        $query2->execute(array(":name" => $_POST['reg_agentname'],
                            ":id" => $id));
                    }
                    $code = md5(time());
                    $query3 = $mysql->prepare('DELETE kuwr.*,ku.*
                                FROM k_users_wait_reg AS kuwr
                                LEFT JOIN k_users AS ku ON (ku.k_ku_id = kuwr.k_wr_user_id)
                                WHERE k_wr_date_code<NOW() - INTERVAL 1 DAY');
                    $query3->execute();
                    $query5 = $mysql->prepare('SELECT k_tpa_price
                                FROM k_tariff_packets_attrs
                                WHERE k_tpa_packet=1 AND k_tpa_owner=:owner');
                    $owner = 0;
                    switch ($_POST['reg_type']) {
                        case 1:
                            $owner = 3;
                            break;
                        case 2:
                            $owner = 4;
                            break;
                        case 3:
                            $owner = 2;
                            break;
                        case 4:
                            $owner = 1;
                            break;
                    }
                    $query5->execute(array(":owner" => $owner));
                    $result5 = $query5->fetch(PDO::FETCH_ASSOC);
                    if ($result5['k_tpa_price'] != 0) {
                        mailCode($_POST['reg_email'], $code, $id, 1);
                    } else {
                        $query4 = $mysql->prepare('INSERT INTO k_users_wait_reg
                                    (k_wr_user_id,k_wr_reg_code,k_wr_date_code)
                                    VALUES (:id,:code,NOW())');
                        $query4->execute(array(":id" => $id, ":code" => $code));
                        mailCode($_POST['reg_email'], $code, $_POST['reg_login'], 0);
                    }

                    unset($_POST);
                    $ad_success = TRUE;
                    //$comment = "<script>alert('Вам отправлено письмо с дальнейшей инструкцией!');</script>";
/*
                    $query4 = $mysql->prepare('INSERT INTO k_users_wait_reg (k_wr_user_id,k_wr_reg_code,k_wr_date_code)
                                VALUES (:id,:code,NOW())');
                    $query4->execute(array(":id" => $id, ":code" => $code));
                    mailCode($_POST['reg_email'], $code, $_POST['reg_login']);
*/
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
                $comment = 'Выброшено исключение: '.  $e->getMessage(). "\n";
                $ad_success = FALSE;
            }
        }
    } else {
        //$comment = "Для завершения регистрации нажмите на яркий кружочек на картинке.";
        $comment = "Что-то пошло не так.";
        $ad_success = FALSE;
    }
} else {
    $comment = "Для завершения регистрации нажмите на яркий кружочек на картинке.";
    $ad_success = FALSE;
}

$content_page_1 = new PageContent(3);
$content_page_2 = new PageContent(4);
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
    <script type="text/javascript" src="js/registration.js"></script>
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
require_once 'inc/header.php';
?>
<div class="center_all_block">
    <div class="left_all_block reg">
        <div class="shapka_bloka">
            <a class="name_shapka">Форма регистрации</a>
        </div>
        <div class="obveden_block">

        <?php
        if (!isset($_GET['do'])) {
        ?>
            <?php if (!$ad_success) { ?>
            <!--<span class="title_text_reg">Форма регистрации</span><br>-->
            <span class="title_text_reg_1">Поля, отмеченные <span style="color: red;">*</span>, обязательны для регистрации.</span><br>
            <span class="title_text_reg_2">
                <?php
                echo $content_page_1->text;
                ?>
            </span>

                <form action="registration.php" id="reg_form" method="post" enctype="multipart/form-data"
                      onsubmit="return true;">
                    <table class="table_registration">
                        <tr>
                            <td><a class="team_reg">Ваша Фамилия:<span style="color: red;">*</span></a></td>
                            <td><?php echo '<input class="inp_reg" name="reg_sname" type="text" value="' . $_POST['reg_sname'] . '">'; ?></td>
                        </tr>
                        <tr>
                            <td><a class="team_reg">Ваше Имя:<span style="color: red;">*</span></a></td>
                            <td><?php echo '<input class="inp_reg" name="reg_name" type="text" value="' . $_POST['reg_name'] . '">'; ?></td>
                        </tr>
                        <tr>
                            <td><a class="team_reg">Ваше Отчество:</a></td>
                            <td><?php echo '<input class="inp_reg" name="reg_lname" type="text" value="' . $_POST['reg_lname'] . '">'; ?></td>
                        </tr>
                        <tr>
                            <td><a class="team_reg">E-mail:<span style="color: red;">*</span></a></td>
                            <td><?php echo '<input class="inp_reg" id="email" onkeyup="EmailAvailable();" name="reg_email" type="text" value="' . $_POST['reg_email'] . '">'; ?></td>
                        </tr>
                        <tr>
                            <td><a class="team_reg">Телефон:<!--<span style="color: red;">*</span>--></a></td>
                            <td><?php
                                echo '<input class="inp_reg" id="phone" onkeyup="PhoneAvailable();" placeholder="79XXXXXXXXX" name="reg_phone" type="text" value="' . $_POST['reg_phone'] . '">';
                                //
                                ?></td>
                        </tr>
                        <tr>
                            <td><a class="team_reg">Логин:<span style="color: red;">*</span></a></td>
                            <td><?php echo '<input class="inp_reg" id="login" onkeyup="LoginAvailable();" name="reg_login" type="text" value="' . $_POST['reg_login'] . '">'; ?></td>
                        </tr>
                        <tr>
                            <td><a class="team_reg">Пароль:<span style="color: red;">*</span></a></td>
                            <td><input class="inp_reg" id="password" name="reg_password" type="password" value=""></td>
                        </tr>
                        <tr>
                            <td><a class="team_reg">Повторите пароль:<span style="color: red;">*</span></a></td>
                            <td><input class="inp_reg" id="password2" name="reg_password2" type="password" value="">
                            </td>
                        </tr>
                        <!--
                        <tr>
                            <th colspan="2">
                                <a class="team_reg">Для пользователей раздела недвижимости</a>
                            </th>
                        </tr>
                        <tr>
                            <td><a class="team_reg">Тип:<span style="color: red;">*</span></a></td>
                            <td>
                                <select name="reg_type" id="user_type" class="inp_reg">
                                    <?php
                                    if (isset($_POST['req_type'])) {
                                        if ($_POST['req_type'] == 1) {
                                            echo '<option selected="selected" value="1">Агентство</option>';
                                        }
                                        if ($_POST['req_type'] == 2) {
                                            echo '<option selected="selected" value="2">Строитель</option>';
                                        }
                                        if ($_POST['req_type'] == 3) {
                                            echo '<option selected="selected" value="3">Посредник</option>';
                                        }
                                        if ($_POST['req_type'] == 4) {
                                            echo '<option selected="selected" value="4">Собственник</option>';
                                        }
                                    }
                                    ?>
                                    <option value="4">Собственник</option>
                                    <option value="2">Строитель</option>
                                    <option value="3">Посредник</option>
                                    <option value="1">Агентство</option>
                                </select>
                            </td>
                        </tr>
                        <tr id="agent" style="display: none;">
                            <td><a class="team_reg">Название агентства:<span style="color: red;">*</span></a></td>
                            <td><input class="inp_reg" name="reg_agentname" disabled="disabled" type="text" value="">
                            </td>
                        </tr>
                        -->

                        <tr>
                            <td colspan="2">
                                <?php
                                if (!empty($comment)) {
                                    echo '<a class="team_reg">' . $comment . '</a>';
                                }
                                ?>
                                <br><input type='image' name='submit' src='profile/inc/captcha.php'
                                           alt='Captcha Security'>
                            </td>
                        </tr>
                    </table>
                </form>
            <?php } else {
            ?>

                <span class="title_text_reg_2">Поздравляем! Вы успешно зарегистрировались на нашем портале. Проверьте, пожалуйста, электронную почту. В ней Вы найдете дальнейшие инструкции по активации своего аккаунта.
                    <br><br>
                <?php
                echo $content_page_1->text;
                ?>
                </span>

            <?php
            }
            ?>

        </div>
        <?php
        }
        if ($_GET['do'] == 'activate') {
        $_GET['username'] = filter_var($_GET['username'], FILTER_SANITIZE_STRIPPED);
        $_GET['code'] = filter_var($_GET['code'], FILTER_SANITIZE_STRIPPED);
        try {
            $query0 = $mysql->prepare('DELETE kuwr.*,ku.*
                                FROM k_users_wait_reg AS kuwr
                                LEFT JOIN k_users AS ku ON (ku.k_ku_id = kuwr.k_wr_user_id)
                                WHERE k_wr_date_code>:date');
            $query0->execute(array(":date" => date('Y-m-d H:i:s', time() + 24 * 60 * 60)));
            $query = $mysql->prepare('SELECT *
                                FROM k_users AS ku
                                LEFT JOIN k_users_wait_reg AS kuwr ON (ku.k_ku_id = kuwr.k_wr_user_id)
                                WHERE k_ku_login=:login AND k_wr_reg_code=:code');
            $query->execute(array(":login" => $_GET['username'], ":code" => $_GET['code']));
            if ($query->rowCount() > 0) {
                $query2 = $mysql->prepare('DELETE kuwr.*
                                    FROM k_users AS ku
                                    LEFT JOIN k_users_wait_reg AS kuwr ON (ku.k_ku_id = kuwr.k_wr_user_id)
                                    WHERE k_ku_login=:login AND k_wr_reg_code=:code');
                $query2->execute(array(":login" => $_GET['username'], ":code" => $_GET['code']));
                $query3 = $mysql->prepare('UPDATE k_users SET k_ku_verified=1 WHERE k_ku_login=:login');
                $query3->execute(array(":login" => $_GET['username']));
                $text = 'Пользователь <b>' . $_GET['username'] . '</b> успешно активирован! Теперь вы можете авторизоваться!';
            } else {
                $text = 'Пользователь или код неверен!';
            }
        } catch (PDOException $e) {
            exit();
        }
        ?>
        <span class="title_text_reg_1">Проверка пользователя</span><br>
        <span class="title_text_reg_2"><?php echo $text; ?></span>
    </div>
    <?php
    }
    ?>
    <?php
    if ($_GET['do'] == 'recover') {
    $_GET['username'] = filter_var($_GET['username'], FILTER_SANITIZE_STRIPPED);
    $_GET['code'] = filter_var($_GET['code'], FILTER_SANITIZE_STRIPPED);
    try {
        $query0 = $mysql->prepare('DELETE * FROM k_users_forget WHERE k_wr_date_code>:date');
        $query0->execute(array(":date" => date('Y-m-d H:i:s', time() + 24 * 60 * 60)));
        $query = $mysql->prepare('SELECT *
                                FROM k_users AS ku
                                LEFT JOIN k_users_forget AS kuf ON (kuf.k_uf_user_id = ku.k_ku_id)
                                WHERE k_ku_login=:login AND k_uf_code=:code');
        $query->execute(array(":login" => $_GET['username'], ":code" => $_GET['code']));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if ($query->rowCount() == 1) {
            $query2 = $mysql->prepare('DELETE kuf.*
                                FROM k_users AS ku
                                LEFT JOIN k_users_forget AS kuf ON (kuf.k_uf_user_id = ku.k_ku_id)
                                WHERE k_ku_login=:login AND k_uf_code=:code');
            $query2->execute(array(":login" => $_GET['username'], ":code" => $_GET['code']));
            $query3 = $mysql->prepare('UPDATE k_users SET k_ku_password=:pass WHERE k_ku_login=:login');
            $query3->execute(array(":login" => $_GET['username'], ":pass" => md5($new_p)));
            $_SESSION['login'] = $result['k_ku_login'];
            $_SESSION['id'] = $result['k_ku_id'];
            $_SESSION['password'] = $result['k_ku_password'];
            $_SESSION['privileges'] = $result['k_u_privileges'];
            $text = 'Код принят! Для изменения пароля перейдите по ссылке: <a href="profile/?PageType=3">изменить пароль</a>';
        } else {
            $text = 'Пользователь или код неверен!';
        }
    } catch (PDOException $e) {
        exit();
    }
    ?>
    <span class="title_text_reg">Проверка пользователя</span><br>
    <span class="title_text_reg_2"><?php echo $text; ?></span>
</div>
<?php
}
?>

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

<div class="temno" id="temno"></div>
<!--Всплывающие окна конец-->
<div class="prozrachno" id="prozrachno" onclick="$('.vhod_block_gl').hide(500);
                                disableP();"></div>
<!--Всплывающие окна прозрачное для закрытия входа-->

<script type="text/javascript">
    function ShowPhoto() {
        $('.photo_map_vsplivaet').show(500);
        $(document).mousemove(function (e) {
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
