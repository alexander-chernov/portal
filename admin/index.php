<!DOCTYPE HTML>
<?php
define('TOMSKLINE', 1);
session_start();
require_once 'inc/configs.php';
require_once '../inc/functions.php';
if (YourIPBanned()) {
    header($_ENV['SERVER_PROTOCOL'] . " 401 IP BAN", true, 401);
    header("Location: http://" . _SERVER_ADDRESS . "/pageBan.php");
}
try {
    $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
    $mysql->exec('set names utf8');
    $query = $mysql->prepare('SELECT k_ku_id,k_u_privileges FROM k_users WHERE k_ku_login=:login AND k_ku_password=:password');
    $query->execute(array(":login" => $_COOKIE['login'], ":password" => $_COOKIE['password']));
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
?>
<html>
    <head>
        <title>Site Administration</title>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" >
        <link rel="stylesheet" type="text/css" href="styles/autorization.css" >
    </head>
    <body>
        <div id="home">
            <table id="tab1" border="0">
                <tr>
                    <td id="zag">
                        <p>Панель управления</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div id="forma">
                            <table border="0">
                                <tr class="tr_gl">
                                    <td id="title">
                                        <p>Вход в панель управления</p>
                                    </td>
                                    <td>
                                        <div id="logo">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td id="coments">
                                        <p style="width: 50%;">Для входа в панель управления введите логин и пароль.</p>
                                        <div id="coment">
                                            <a href="http://chernov.su" class="coment">Перейти на главную страницу сайта</a>
                                        </div>
                                        <div id="chbe">
                                            <p>Переход на сайт web студии</p>
                                            <a href="http://chernov.su" class="blue">Черное<span class="red">Белое</span>.ru</a>
                                        </div>
                                    </td>
                                    <td colspan="2" bgcolor="#f4f4f4" id="ram2">
                                        <script type="text/javascript">
                                            function FormSubmitCheck() {
                                                if ($('#login_name').val() === "" || $('#pass_value').val() === "") {
                                                    alert('\u0417аполните все необходимые поля!');
                                                    return false;
                                                } else {
                                                    return true;
                                                }
                                            }
                                        </script>
                                        <div id="input">			 <!-- Вход-->
                                            <form id="inputlogin" action="testreg.php" onSubmit="return FormSubmitCheck();
                                                    return false;" method="post">
                                                <table border="0">
                                                    <tr>
                                                        <td id="login">
                                                            <p>Логин</p>
                                                            <div>
                                                                <input type="text" size="15" id="login_name" maxlength="15" name="login_name" >
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <table border="0">
                                                    <tr>
                                                        <td id="pas">
                                                            <p>Пароль</p>
                                                            <div>
                                                                <input type="password" id="pass_value" size="15" maxlength="15" name="pass_value" >
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div id="imginput">
                                                                <input type="image" src="images/autorization/login_button.jpg" name="sub" onClick="return FormSubmitCheck();
                                                                        return false;">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td id="saveme">
                                                            <div style="border:0px !important">
                                                                <label><input  type="checkbox" name="save" value="1" checked="checked"> Запомнить меня</label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </form>
                                        </div>	
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>	
        </div>
    </body>
</html>