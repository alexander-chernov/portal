<?php
define('TOMSKLINE', 1);
session_start();
?>
<!DOCTYPE HTML>
<html lang="ru">
    <head>
        <title>Портал Недвижимости</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <?php
        if (!isset($_SESSION['login_e'])) {
            if (isset($_COOKIE['login_e'])) {
                $_SESSION['login_e'] = $_COOKIE['login_e'];
                $_SESSION['password_e'] = $_COOKIE['password_e'];
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

        require_once '../inc/configs.php';
        require_once '../inc/functions.php';
        require_once 'inc/classes.php';
        require_once 'inc/functions.php';
        require_once '../admin/admin_gl/inc/classes.php';
        if (YourIPBanned()) {
            header($_ENV['SERVER_PROTOCOL'] . " 401 IP BAN", true, 401);
            header("Location: http://" . _SERVER_ADDRESS . "/pageBan.php");
        }

        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT * FROM k_experts WHERE k_e_email=:login AND k_e_password=:password');
            $query->execute(array(":login" => $_SESSION['login_e'], ":password" => $_SESSION['password_e']));
            $result = $query->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $_SESSION['id_e'] = $result['k_e_id'];
                $_SESSION['login'] = $_SESSION['login_e'];
            } else {
                header($_ENV['SERVER_PROTOCOL'] . " 400 WRONG USER", true, 401);
                header("Location: http://" . _SERVER_ADDRESS . "/");
                exit();
            }
        } catch (PDOException $e) {
            unset($e);
            exit();
        }

        $banners = new BannersAll();
        $expert = new Expert($_SESSION['login_e']);
        if (!$expert->id) {
            header($_ENV['SERVER_PROTOCOL'] . " 400 WRONG USER", true, 401);
            header("Location: http://" . _SERVER_ADDRESS . "/");
            exit();
        }
        if (isset($_GET['PageIndex'])) {
            $page = filter_var($_GET['PageIndex'], FILTER_VALIDATE_INT);
        } else {
            $page = 1;
        }
        $expertQA = new ExpertQuestions($_SESSION['id_e'], $page);
        
        $content_page = new PageContent(19);
        ?>
        <meta name="keywords" content="">
        <meta name="description" content="">
        <link rel="stylesheet" type="text/css" href="../css/style.css">
        <link rel="stylesheet" type="text/css" href="../css/style_wind.css">
        <link rel="stylesheet" type="text/css" href="css/kabinet_users.css">
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script type="text/javascript" src="../js/wind.js"></script>
        <script type="text/javascript" src="js/scripts.js"></script>
        <?php
        if ($ShowParamID == 5) {
            ?>
            <script type="text/javascript">
                $(document).ready(function() {
                    $('.mini_img').click(function() {
                        if ($(this).attr('title') === 'Удалить вопрос') {
                            DeleteQuestion($(this).attr('alt'));
                        }
                        if ($(this).attr('title') === 'Сохранить изменения') {
                            SaveAnswer($(this).attr('alt'));
                        }
                    });
                });
            </script>
            <?php
        }
        ?>
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
            <div class="center_all_block">
                <div class="left_all_block">
                    <div class="content_left_block">
                        <div class="block_content_1">   <!--Меню кабинета пользователя-->
                            <b class="title_text_reg">Кабинет <b>эксперта</b></b><br>
                            <?php
                            if ($ShowParamID == 1) {
                                ?>
                                <table>
                                    <tr>
                                        <td><span class="tit_text">№:</span></td>
                                        <td><span class="tit_text"><b><?php echo $_SESSION['id_e']; ?></b></span></td>
                                    </tr>
                                    <tr>
                                        <td><span class="tit_text">Логин:</span></td>
                                        <td><span class="tit_text"><b><?php echo $_SESSION['login_e']; ?></b></span></td>
                                    </tr>
                                    <tr>
                                        <td><span class="tit_text">Заголовок:</span></td>
                                        <td><span class="tit_text"><b><?php echo $expert->header; ?></b></span></td>
                                    </tr>
                                </table><br>
                                <p class="conteiner"><a class="style_kabinet_2" href="prof_expert.php?PageType=2">Редактировать информацию</a></p>
                                <p class="conteiner"><a class="style_kabinet_2" href="prof_expert.php?PageType=3">Редактировать аватар</a></p>
                                <p class="conteiner"><a class="style_kabinet_2" href="prof_expert.php?PageType=4">Изменить пароль</a></p>
                                <p class="conteiner"><a class="style_kabinet_2" href="prof_expert.php?PageType=5">Мои вопросы / ответы</a></p>
                                <?php
                            }
                            ?>
                            <?php
                            if ($ShowParamID == 2) {
                                $expert_categories = new ExpertCategories();
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
                                        $('#submit_expert').mouseover(function() {
                                            $('#form_save').removeAttr('onsubmit');
                                        });
                                        $('#submit_expert').mouseout(function() {
                                            $('#form_save').attr('onsubmit', 'return false;');
                                        });
                                    });
                                </script>
                                <div class="info_expert">
                                    <p class="name_obiavlenia_block">Редактировать информацию</p>
                                    <form action="posts.php" method="post" onsubmit="return false;" id="form_save">
                                        <table>
                                            <tr>
                                                <td><p class="style_wind_3_1">Заголовок:</p></td>
                                                <td><input class="all_inp" name="ExpertHeader" type="text" id="UserPassword" value="<?php echo $expert->header; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td><p class="style_wind_3_1">Тема эксперта:</p></td>
                                                <td><input class="all_inp" name="ExpertTheme" type="text" id="UserPassword" value="<?php echo $expert->theme; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td><p class="style_wind_3_1">Краткое описание:</p></td>
                                                <td><textarea class="area_exp_1" name="ExpertBrief"><?php echo $expert->brief; ?></textarea></td>
                                            </tr>
                                            <tr>
                                                <td><p class="style_wind_3_1">Описание:</p></td>
                                                <td><textarea class="area_exp_1" name="ExpertDescription"><?php echo $expert->description; ?></textarea></td>
                                            </tr>
                                            <tr>
                                                <td><p class="style_wind_3_1">Укажите адрес:</p></td>
                                                <td>
                                                    <input class="all_inp" id="address_input" type="text" value="<?php echo $expert->address_string; ?>">
                                                    <input type="hidden" id="final_address" name="ExpertAddress" name="address_choise" value="<?php echo $expert->address; ?>">
                                                    <span id="select_address"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><p class="style_wind_3_1">Телефон:</p></td>
                                                <td><input class="all_inp" type="text" name="ExpertPhone" value="<?php echo $expert->phone; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td><p class="style_wind_3_1">Сайт:</p></td>
                                                <td><input class="all_inp" type="text" name="ExpertSite" value="<?php echo $expert->site; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td><p class="style_wind_3_1">Рубрика эксперта:</p></td>
                                                <td>
                                                    <ul class="access_list">
                                                        <?php
                                                        for ($i = 0; $i < count($expert_categories->id); $i++) {
                                                            if (in_array($expert_categories->id[$i], $expert->categories)) {
                                                                echo '<li><label><input type="checkbox" checked="" name="ExpertCat[]" value="' . $expert_categories->id[$i] . '">' . $expert_categories->name[$i] . '</label></li>';
                                                            } else {
                                                                echo '<li><label><input type="checkbox" name="ExpertCat[]" value="' . $expert_categories->id[$i] . '">' . $expert_categories->name[$i] . '</label></li>';
                                                            }
                                                        }
                                                        ?>
                                                    </ul>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <input id="submit_expert" class="act_2" name="SaveExpert" type="submit" value="Сохранить изменения">
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                    <a class="act_2_2" href="prof_expert.php">Назад</a>
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
                                        var btnUpload = $('#ExpertUpload');
                                        var status = $('#statusExpert');
                                        new AjaxUpload(btnUpload, {
                                            action: 'upload-file.php',
                                            name: 'ExpertUpload',
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
                                                    ChangeAvatarExpert(response);
                                                }
                                            }
                                        });
                                    });
                                </script>
                                <div class="info_expert">
                                    <p class="name_obiavlenia_block">Редактировать аватар</p>
                                    <div class="block_profile_avatar">
                                        <?php
                                        if ($expert->avatar && file_exists('../admin/' . $expert->avatar)) {
                                            echo '<img id="avatar" class="profile_avatar" src="../admin/' . $expert->avatar . '" alt="">';
                                        } else {
                                            echo '<img id="avatar" class="profile_avatar" src="../admin/images/noimage.png" alt="">';
                                        }
                                        ?>
                                    </div>
                                    <button class="act_2" id="ExpertUpload">Изменить аватар</button><br>
                                    <span id="statusExpert"></span><br>
                                    <input type="hidden" id="expert_id" value="<?php echo $expert->id; ?>">
                                    <a class="act_2_2" href="prof_expert.php">Назад</a>
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
                                    <form action="prof_expert.php" method="POST" onsubmit="return PasswordSave();
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
                                                    <input class="act_2" type="submit" name="SaveExpertPassword" value="Сохранить изменения">
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                    <a class="act_2_2" href="prof_expert.php">Назад</a>
                                </div>
                                <?php
                            }
                            ?>
                            <?php
                            if ($ShowParamID == 5) {
                                ?>
                                <div class="info_expert">
                                    <p class="name_obiavlenia_block">Мои вопросы / ответы</p>
                                    <p class="conteiner" id="new_q"><a class="style_kabinet_2" href="prof_expert.php?PageType=5&QA=1">Новые вопросы</a><span class="kab_nambe"><?php echo $expertQA->need_answer; ?></span></p>
                                    <p class="conteiner" id="answers"><a class="style_kabinet_2" href="prof_expert.php?PageType=5&QA=2">Ответы</a><span class="kab_nambe"><?php echo $expertQA->have_answer; ?></span></p>
                                    <?php
                                    if ($_GET['QA'] == 1) {
                                        ?>
                                        <div class="info_expert">
                                            <p class="title_text_reg">Новые вопросы</p>
                                            <?php
                                            for ($i = 0; $i < count($expertQA->id); $i++) {
                                                if (!$expertQA->text_a[$i]) {
                                                    ?>
                                                    <div class="expert_otvet">
                                                        <a class="exp_4"><img src="images/karandash.png" alt="">Вопрос задает:<span class="n_exp"><?php echo $expertQA->name[$i]; ?></span><span class="n_exp"><?php echo $expertQA->date[$i]; ?></span>
                                                            <span style="padding-left: 30px;">
                                                                <img class="mini_img" title="Удалить вопрос" src="../images/delete_team.png" alt="<?php echo $expertQA->id[$i]; ?>">
                                                                <img class="mini_img" title="Сохранить изменения" src="../images/save_team.png" alt="<?php echo $expertQA->id[$i]; ?>">
                                                            </span>
                                                        </a>
                                                        <div class="vopr">
                                                            <p class="text_expert"><?php echo $expertQA->text_q[$i]; ?></p>
                                                        </div>
                                                        <div class="otv">
                                                            <b>Ответ эксперта:</b>
                                                            <textarea class="area_exp"></textarea>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                    if ($_GET['QA'] == 2) {
                                        ?>
                                        <div id="vopr_exp_2" class="info_expert">
                                            <p class="title_text_reg">Ответы</p>
                                            <?php
                                            for ($i = 0; $i < count($expertQA->id); $i++) {
                                                if ($expertQA->text_a[$i]) {
                                                    ?>
                                                    <div class="expert_otvet">
                                                        <a class="exp_4"><img src="images/karandash_1.png" alt="">Вопрос задает:<span class="n_exp"><?php echo $expertQA->name[$i]; ?></span><span class="n_exp"><?php echo $expertQA->date[$i]; ?></span>
                                                            <span style="padding-left: 30px;">
                                                                <img class="mini_img" title="Удалить вопрос" src="../images/delete_team.png" alt="<?php echo $expertQA->id[$i]; ?>">
                                                                <img class="mini_img" title="Сохранить изменения" src="../images/save_team.png" alt="<?php echo $expertQA->id[$i]; ?>">
                                                            </span>
                                                        </a>
                                                        <div class="vopr">
                                                            <p class="text_expert"><?php echo $expertQA->text_q[$i]; ?></p>
                                                        </div>
                                                        <div class="otv">
                                                            <b>Ответ эксперта:</b>
                                                            <textarea class="area_exp"><?php echo $expertQA->text_a[$i]; ?></textarea>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <a class="act_2_2" href="prof_expert.php">Назад</a>
                                </div>
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
    </body>
</html>
