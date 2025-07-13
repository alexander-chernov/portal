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
                $extra = '../index.php';
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

        try {
            if (isset($_COOKIE['login'])) {
                $_SESSION['login'] = $_COOKIE['login'];
                $_SESSION['password'] = $_COOKIE['password'];
            }
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT * FROM k_users WHERE k_ku_login=:login AND k_ku_password=:password');
            $query->execute(array(":login" => $_SESSION['login'], ":password" => $_SESSION['password']));
            $result = $query->fetch(PDO::FETCH_ASSOC);
            if ($query->rowCount() > 0) {
                $_SESSION['id'] = $result['k_ku_id'];
                $_SESSION['privileges'] = $result['k_u_privileges'];
                $_SESSION['owner'] = $result['k_u_owner'];
/*
                if ($_SESSION['owner'] != 1 || $_SESSION['privileges'] != 4) {
                    header($_ENV['SERVER_PROTOCOL'] . " 400 WRONG USER", true, 401);
                    header("Location: http://" . _SERVER_ADDRESS . "/");
                }
*/
            }
        } catch (PDOException $e) {
            exit();
        }
        $user = new User();
        $user->LoadUser($_SESSION['id']);
        $user_packets = new UserPackets($_SESSION['id']);
        $packetsForUser = new PacketsForUser($_SESSION['owner']);
        $user->immo_monthly = $user_packets->current_remain;
        $banners = new BannersAll();
        $agency = new Agency($_SESSION['id']);
//var_dump($agency);
//var_dump($_SESSION);

        $content_page = new PageContent(20);
        ?>
<!DOCTYPE HTML>
<html lang="ru">
<head>
    <title>Портал Недвижимости</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="keywords" content="">
        <meta name="description" content="">
        <link rel="stylesheet" type="text/css" href="../css/style.css">
        <link rel="stylesheet" type="text/css" href="../css/style_wind.css">
        <link rel="stylesheet" type="text/css" href="css/kabinet_users.css">
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script type="text/javascript" src="../js/wind.js"></script>
        <script type="text/javascript" src="js/scripts.js"></script>
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
    require_once '../inc/header.php';
    ?>
            <div class="center_all_block">
                <div class="left_all_block">
                    <div class="content_left_block">
                        <div class="block_content_1">   <!--Меню кабинета пользователя-->
                            <b class="title_text_reg">Кабинет <b>агентства недвижимости</b></b><br>
                            <?php
                            if ($ShowParamID == 1) {
                                ?>
                                <table>
                                    <tr>
                                        <td><span class="tit_text">№:</span></td>
                                        <td><span class="tit_text"><b><?php echo $agency->id; ?></b></span></td>
                                    </tr>
                                    <tr>
                                        <td><span class="tit_text">Логин:</span></td>
                                        <td><span class="tit_text"><b><?php echo $agency->user; ?></b></span></td>
                                    </tr>
                                    <tr>
                                        <td><span class="tit_text">Наименование:</span></td>
                                        <td><span class="tit_text"><b><?php echo $agency->name; ?></b></span></td>
                                    </tr>
                                </table><br>
                                <p class="conteiner"><a class="style_kabinet_2" href="prof_agent.php?PageType=2">Редактировать информацию</a></p>
                                <p class="conteiner"><a class="style_kabinet_2" href="prof_agent.php?PageType=3">Редактировать аватар</a></p>
                                <p class="conteiner"><a class="style_kabinet_2" href="prof_agent.php?PageType=4">Изменить пароль</a></p>
                                <p class="conteiner"><a class="style_kabinet_2" href="prof_agent.php?PageType=5">Объявления</a></p>
                                <?php
                            }
                            ?>
                            <?php
                            if ($ShowParamID == 2) {
                                ?>
                                <script type="text/javascript" src="js/scripts.js"></script>
                                <script type="text/javascript">
                    $(document).ready(function() {
                        DistrMass($('#final_address').val());
                        if ($('#final_address').val() !== "") {
                            $('#address_input').css('background', '#b1e0ff');
                        }
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
                        $('#submit_agent').mouseover(function() {
                            $('#form_save').removeAttr('onsubmit');
                        });
                        $('#submit_agent').mouseout(function() {
                            $('#form_save').attr('onsubmit', 'return false;');
                        });
                    });
                                </script>
                                <div class="info_expert">
                                    <p class="name_obiavlenia_block">Редактировать информацию</p>
                                    <form id="form_save" action="posts.php" method="post" onsubmit="return false;">
                                        <table>
                                            <tr>
                                                <td><p class="style_wind_3_1">Фамилия:</p></td>
                                                <td><input class="all_inp" name="AgentSName" type="text" value="<?php echo $agency->sname; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td><p class="style_wind_3_1">Имя:</p></td>
                                                <td><input class="all_inp" name="AgentFName" type="text" value="<?php echo $agency->fname; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td><p class="style_wind_3_1">Отчество:</p></td>
                                                <td><input class="all_inp" name="AgentLName" type="text" value="<?php echo $agency->lname; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td><p class="style_wind_3_1">E-mail:</p></td>
                                                <td><input class="all_inp" name="AgentEmail" type="text" value="<?php echo $agency->email; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td><p class="style_wind_3_1">Укажите адрес:</p></td>
                                                <td>
                                                    <input class="all_inp" id="address_input" type="text" value="<?php echo $agency->address_string; ?>">
                                                    <input type="hidden" id="final_address" name="AgentAddress" name="address_choise" value="<?php echo $agency->address; ?>">
                                                    <span id="select_address"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><p class="style_wind_3_1">Название:</p></td>
                                                <td><input class="all_inp" type="text" name="AgentName" value="<?php echo $agency->name; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td><p class="style_wind_3_1">Телефон:</p></td>
                                                <td><input class="all_inp" type="text" name="AgentPhone" value="<?php echo $agency->phone; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td><p class="style_wind_3_1">Сайт:</p></td>
                                                <td><input class="all_inp" type="text" name="AgentSite" value="<?php echo $agency->site; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td><p class="style_wind_3_1">Описание:</p></td>
                                                <td><textarea class="area_exp_1" name="AgentDescription"><?php echo $agency->description; ?></textarea></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <input class="act_2" id="submit_agent" type="submit" name="SaveAgent" value="Сохранить изменения">
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                    <a class="act_2_2" href="prof_agent.php">Назад</a>
                                </div>
                                <?php
                            }
                            ?>
                            <?php
                            if ($ShowParamID == 3) {
                                ?>
                                <script type="text/javascript" src="js/scripts.js"></script>
                                <script type="text/javascript" src="js/ajaxupload.3.5.js"></script>
                                <script type="text/javascript">
                                        $(function() {
                                            var btnUpload = $('#AgentUpload');
                                            var status = $('#statusAgent');
                                            new AjaxUpload(btnUpload, {
                                                action: 'upload-file.php',
                                                name: 'AgentUpload',
                                                onSubmit: function(file, ext) {
                                                    if (!(ext && /^(jpg|png|jpeg|gif)$/.test(ext))) {
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
                                                        ChangeAvatarAgent(response);
                                                    }
                                                }
                                            });
                                        });
                                </script>
                                <div class="info_expert">
                                    <p class="name_obiavlenia_block">Редактировать аватар</p>
                                    <div class="block_profile_avatar">
                                        <?php
                                        if ($agency->avatar && file_exists('../admin/' . $agency->avatar)) {
                                            echo '<img id="avatar" class="profile_avatar" src="../admin/' . $agency->avatar . '" alt="">';
                                        } else {
                                            echo '<img id="avatar" class="profile_avatar" src="../admin/images/noimage.png" alt="">';
                                        }
                                        ?>
                                    </div>
                                    <button class="act_2" id="AgentUpload">Изменить аватар</button><br>
                                    <span id="statusAgent"></span><br>
                                    <input type="hidden" id="agency_id" value="<?php echo $agency->id; ?>">
                                    <a class="act_2_2" href="prof_agent.php">Назад</a>
                                </div>
                                <?php
                            }
                            ?>
                            <?php
                            if ($ShowParamID == 4) {
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
                                <div class="info_expert">
                                    <p class="name_obiavlenia_block">Изменение пароля</p>
                                    <form action="prof_agent.php" method="POST" onsubmit="return PasswordSave();
                                                return false;">
                                        <table>
                                            <tr>
                                                <td><p class="style_wind_3_1">Введите новый пароль:</p></td>
                                                <td><input class="all_inp" type="password" id="UserPassword" name="UserPassword" value=""></td>
                                            </tr>
                                            <tr>
                                                <td><p class="style_wind_3_1">Повторите пароль:</p></td>
                                                <td><input class="all_inp" type="password" id="UserPassword2" onkeyup="PasswordCompare();" name="UserPassword2" value=""></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <input class="act_2" name="SavePassword" type="submit" value="Сохранить изменения">
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                    <a class="act_2_2" href="prof_agent.php">Назад</a>
                                </div>
                                <?php
                            }
                            ?>
                            <?php
                            if ($ShowParamID == 5) {
                                $link = '';
                                $where = '';
                                if (isset($_GET['Category'])) {
                                    $Category = filter_var($_GET['Category'], FILTER_VALIDATE_INT);
                                    $link = '&Category=' . $Category;
                                    $where = ' k_isf_subcategory=' . $Category . ' AND ';
                                }
                                if (isset($_GET['DealType'])) {
                                    $DealType = filter_var($_GET['DealType'], FILTER_VALIDATE_INT);
                                    $link = '&DealType=' . $DealType;
                                    $where = ' k_isf_deal_type=' . $DealType . ' AND ';
                                }
                                if (isset($_GET['PageIndex'])) {
                                    $page = filter_var($_GET['PageIndex'], FILTER_VALIDATE_INT);
                                } else {
                                    $page = 1;
                                }
                                $agent = new ProfileAgent();
                                $agent_ads = new AdSelfAgency($_SESSION['id'], $Category, $DealType, $page);
                                $user_packages = new UserPackages($_SESSION['id']);
                                $immovables_packages = new ImmovablesPackges($_SESSION['id']);
                                ?>
                                <div class="info_expert">
                                    <p class="name_obiavlenia_block">Объявления</p>
                                    <?php
                                    $agent->GenerateAgent($_SESSION['id']);
                                    ?>
                                    <br>
                                    <?php
                                    if (isset($_GET['Category']) || isset($_GET['DealType'])) {
                                        ?>
                                        <div class="info_ag">
                                            <p class="title_text_reg">Все объявления <b>продаю</b></p>
                                            <span>
                                                <a class="add_rfbinet_obiavlenie" style="float: right;" title="Купить дополнительное объявление" href="../payment?pay&realty&additional">
                                                    Купить дополнительное объявление
                                                </a>
                                                <?php
                                                if ($user->immo_monthly > 0 || $user->immo_remain > 0) {
                                                    ?>
                                                    <a class="add_rfbinet_obiavlenie" style="float: right;" title="Добавить объявление" href="./prof_agent?PageType=7">
                                                        Добавить объявление
                                                    </a>
                                                    <?php
                                                }
                                                ?>
                                                <a class="act_2" href="./prof_agent?PageType=1">
                                                    Назад
                                                </a>
                                            </span><br><br>
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
                                                        <a class="no_line" title="Купить пакет" href="../payment?pay&realty&package=2">
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
                                                        <a class="no_line" title="Купить пакет" href="../payment?pay&realty&package=1">
                                                            <img src="../images/dollar.png" alt="">
                                                        </a>
                                                    </td>
                                                </tr>
                                            </table>
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
                                                                <a class="no_line" title="Купить пакет" href="../payment?pay&realty&packet=<?php echo $packetsForUser->id[$i]; ?>">
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
                                            <br>
                                            <table class="table_add_obiavlenia" border="1">
                                                <tr>
                                                    <td colspan="7"><b class="style_kabinet_6">Объявления агентства<span class="style_kabinet_6_1"><?php echo $user->immo_count; ?></span></b></td>
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
                                                for ($i = 0; $i < count($agent_ads->id); $i++) {
                                                    $style = '#ffcccc';
                                                    $state = 'На проверке';
                                                    if ($agent_ads->state[$i] == 1) {
                                                        $style = '#ffffff';
                                                        $state = 'Опубликовано';
                                                    }
                                                    ?>
                                                    <tr style="background: <?php echo $style; ?>;">
                                                        <td><p class="znak_15"><?php echo $agent_ads->id[$i]; ?></p></td>
                                                        <td>
                                                            <?php
                                                            if ($agent_ads->photo_url[$i] && file_exists('../admin/' . $agent_ads->photo_url[$i])) {
                                                                echo '<img class="photo_obiavlenia" src="../admin/' . $agent_ads->photo_url[$i] . '" alt="">';
                                                            } else {
                                                                echo '<img class="photo_obiavlenia" src="../images/noimage.png" alt="">';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><p class="znak_15"><?php echo $agent_ads->description[$i]; ?></p></td>
                                                        <td><p class="znak_15"><?php echo $agent_ads->date_reg[$i]; ?></p></td>
                                                        <td><p class="znak_15"><?php echo $agent_ads->date_end[$i]; ?></p></td>
                                                        <td><p class="znak_15"><?php echo $state; ?></p></td>
                                                        <td>
                                                            <?php
                                                            if (!in_array($agent_ads->id[$i], $agent_ads->special)) {
                                                                ?>
                                                                <a class="no_line" <?php echo 'href="../payment?pay&realty&action=1&id=' . $agent_ads->id[$i] . '"'; ?> title="Закрепление в VIP предложениях">
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
                                                            if ($agent_ads->lock_start[$i] == '') {
                                                                ?>
                                                                <a class="no_line" <?php echo 'href="../payment?pay&realty&action=2&id=' . $agent_ads->id[$i] . '"'; ?> title="Закрепить">
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
                                                            if ($agent_ads->color[$i] != 1) {
                                                                ?>
                                                                <a class="no_line" <?php echo 'href="../payment?pay&realty&action=3&id=' . $agent_ads->id[$i] . '"'; ?> title="Поднять и выделить цветом">
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
                                                            <a class="no_line" <?php echo 'href="./prof_agent?PageType=change&id=' . $agent_ads->id[$i] . '"'; ?> title="Редактировать">
                                                                <img src="../images/edit.png" alt="">
                                                            </a>
                                                            <?php
                                                            if (in_array($agent_ads->id[$i], $immovables_packages->immo)) {
                                                                ?>
                                                                <a onclick="PackageUse(this);" class="no_line" title="Управление пакетами">
                                                                    <img src="../images/packet_plus.png" alt="<?php echo $agent_ads->id[$i]; ?>">
                                                                </a>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <a onclick="PackageUse(this);" class="no_line" title="Управление пакетами">
                                                                    <img src="../images/packet.png" alt="<?php echo $agent_ads->id[$i]; ?>">
                                                                </a>
                                                                <?php
                                                            }
                                                            ?>
                                                            <a class="no_line" title="Удалить объявление">
                                                                <img src="../images/delete_team.png" alt="">  
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                            </table>
                                            <?php
                                            $agent_ads->GenerateNavigation($page, $where, $link);
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
                                </div>
                                <?php
                            }
                            ?>
                            <?php
                            if ($ShowParamID == 7) {
                                ?>
                                <div class="info_expert">   <!--ФОРМЫ ВЫБОРА РУБРИКИ НЕДВИЖИМОСТИ-->
                                    <span>
                                        <a class="act_2" href="prof_agent.php">
                                            &laquo; В кабинет
                                        </a>
                                        <a class="act_2" href="prof_agent.php?PageType=5">
                                            &laquo; К объявлениям
                                        </a>
                                    </span> <br><br>
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
                                                        <a <?php echo 'href="prof_agent.php?PageType=add&ImmoType=' . $value['k_is_id'] . '"'; ?>>
                                                            <label class="razdel_style"><?php echo dropBackWords($value['k_ic_name']) . ' ' . dropBackWords($value['k_is_name']); ?></label>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </table>
                                    </div>
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
                                                                    onSubmit: function(file, ext) {
                                                                        if (!(ext && /^(jpg|png|jpeg|gif)$/.test(ext))) {
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
                                                                            status.text('\u0424ото успешно загружено!');
                                                                            var img = $('<img>', {
                                                                                'src': response,
                                                                                'alt': '',
                                                                                'style': 'width: 100px; display: inline-block; vertical-align: top; margin: 5px;'
                                                                            });
                                                                            var block = $('.team_style:contains("Добавить фотографию")');
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
                                                            })
                                </script>
                                <a class="act_2" href="prof_agent.php">
                                    &laquo; В кабинет
                                </a>
                                <a class="act_2" href="prof_agent.php?PageType=5">
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
                                                <span class="team_style">Добавить фотографию:
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
                                                                if (file_exists(str_replace('images/', '../admin/images/1_', $value['k_ip_url']))) {
                                                                    ?>
                                                                    <span class="ad_f">
                                                                        <?php
                                                                        echo '<img src="' . str_replace('images/', '../admin/images/1_', $value['k_ip_url']) . '" alt="" style="width: 100px; display: inline-block; vertical-align: top; margin: 5px;">';
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
                                                                        echo '<input type="hidden" value="' . str_replace('images/', '../admin/images/1_', $value['k_ip_url']) . '" name="images[]">';
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
                                <?php
                            }
                            ?>
                        </div>
                    </div> 
                </div>

                <div class="right_all_block">
                    <div class="shapka_bloka">
                        <a class="name_shapka">Условия регистрации</a>
                    </div>

                    <div class="ramka_right_block_kabinet">
                        <p class="text_right_block_kabinet">
                            <?php
                            echo $content_page->text;
                            ?>
                        </p>
                    </div>
                </div>
            </div>

            <?php
                require_once '../inc/footer.php';
                ?>
        </div>
        <div class="temno" id="temno"></div>
    </body>
</html>
