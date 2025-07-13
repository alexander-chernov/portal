<?php
define('TOMSKLINE', 1);
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="ru">
    <head>
        <title>Портал Недвижимости: Эксперты</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="keywords" content="">
        <meta name="description" content="">
        <link rel="stylesheet" type="text/css" href="../css/style.css">
        <link rel="stylesheet" type="text/css" href="css/expert.css">
        <link rel="stylesheet" type="text/css" href="../css/style_wind.css">
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script type="text/javascript" src="../js/wind.js"></script>
        <script type="text/javascript">
            function AddComment() {
                if ($('[name="your_name"]').val() === '' || $('[name="your_question"]').val() === '') {
                    alert('\u0417аполните все поля!');
                    return false;
                } else {
                    return true;
                }
            }
        </script>
        <?php
        require_once '../inc/configs.php';
        require_once '../inc/functions.php';

        $where = '';
        $link = '';
        $limit = 10;
        $page = 1;
        $Question = 0;
        $AdId = 0;
        $results = '';

        require_once 'inc/classes.php';
        require_once 'inc/functions.php';
        require_once '../admin/admin_expert/inc/classes.php';
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

        $banners = new BannersAll(0);

        if (!isset($_GET['PageType'])) {
            $ShowParamID = 1;
        } else {
            $ShowParamID = filter_var($_GET['PageType'], FILTER_VALIDATE_INT);
        }

        if (!in_array($ShowParamID, array(1, 2))) {
            header($_ENV['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
            header("Location: http://" . _SERVER_ADDRESS . "/page404.php");
        }
        
        $content_page = new PageContent(11);
        $content_page_1 = new PageContent(2);
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
    require_once '../inc/header.php';
    ?>
            <div class="all_expert_block">

                <?php
                $left_block = new LeftBlock();
                $left_block->LoadBlock();
                ?>
                <div class="left_expert_block">
                    <div class="kriteri_expert">
                        <div class="shapka_bloka">
                            <p class="style_shapka_1">Эксперты</p>
                            <!--<p class="style_shapka_3" title="Всего экспертов"><?php echo $left_block->getAllCount(); ?></p>-->
                        </div>
                        <div class="obverden_bl">
                            <div>
                                <span><a class="style_menu_left_1" href="./?PageType=1">Все эксперты</a><span class="style_menu_left_3"><?php echo $left_block->getAllCount(); ?></span></span><br>
                                <?php
                                for ($i = 1; $i <= count($left_block->getID(0)); $i++) {
                                    echo '<span><a class="style_menu_left_2" href="./?PageType=1&Category=' . $left_block->getID($i) . '">' . $left_block->getName($i) . '</a><span class="style_menu_left_3">' . $left_block->getCount($i) . '</span></span><br>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="center_expert_block">
                    <?php
                    if ($ShowParamID == 1) {
                        $expert = new ExpertT();
                        $expert->LoadExperts($limit, $page, $where);
                        if (isset($_GET['Category'])) {
                            try {
                                $query = $mysql->prepare('SELECT k_ec_name FROM k_experts_categories WHERE k_ec_id=:id');
                                $query->execute(array(":id" => $_GET['Category']));
                                $resultCat = $query->fetch(PDO::FETCH_ASSOC);
                                if ($query->rowCount() == 0) {
                                    header($_ENV['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
                                    header("Location: http://" . _SERVER_ADDRESS . "/page404.php");
                                }
                            } catch (PDOException $e) {
                                exit();
                            }
                        }
                        ?>
                        <div class="block_content_1" <?php if(!isset($_GET['Howto'])) echo 'style="display: none;"'; ?>>
                            <div class="all_redactor">
                                <?php
                                echo $content_page->text;
                                ?>
                            </div>
                        </div>
                        <div class="block_content_1" <?php if(isset($_GET['Howto'])) echo 'style="display: none;"'; ?>>
                            <div class="men_cont">
                                <p class="title_exp">Все эксперты <?php if (isset($_GET['Category'])) echo $resultCat['k_ec_name']; ?>
                                    <!--<a class="title_nambe"><?php echo $expert->find_col; ?></a>-->
                                    <span id="visible_param_1" class="visible_content">Показать по
                                        <a <?php if ($limit == 10) echo 'style="color: #f0938a;"'; ?> href="./?Limit=10">10</a>
                                        <a <?php if ($limit == 30) echo 'style="color: #f0938a;"'; ?> href="./?Limit=30">30</a>
                                        <a <?php if ($limit == 50) echo 'style="color: #f0938a;"'; ?> href="./?Limit=50">50</a>
                                    </span>
                                    <span id="visible_param_1" class="visible_content" style="padding-right: 30px;">Показать&nbsp;&nbsp;
                                        <a <?php if (!isset($_GET['Online'])) echo 'style="color: ' . $result['k_c_value'] . ';"'; ?> href="./?PageType=1<?php echo str_replace('&Online=1', '', $link); ?>" title="Отобразить все">Все</a>
                                        <a <?php if (isset($_GET['Online'])) echo 'style="color: ' . $result['k_c_value'] . ';"'; ?> href="./?Online=1<?php echo $link; ?>" title="Отобразить тех кто в сети">ONLINE</a>
                                    </span>
                                </p>
                            </div>
                            <?php
                            $expert->GenerateNavigation($page, $limit, $where);
                            for ($i = 0; $i < count($expert->id); $i++) {
                                //if ($i % 2 == 0) {
                                    $class = 'class="block_expert_1"';
                                /*} else {
                                    $class = 'class="block_expert_2"';
                                }*/
                                ?>
                                <div <?php echo $class; ?>>
                                    <div class="img_block_expert">
                                        <?php
                                        if ($expert->avatar[$i]) {
                                            echo '<img class="img_expert" src="../admin/' . $expert->avatar[$i] . '" alt="">';
                                        } else {
                                            echo '<img class="img_expert" src="../images/noimage.png" alt="">';
                                        }
                                        ?>
                                    </div>
                                    <div class="block_text_expert">
                                        <a class="team_text_expert" <?php echo 'href="./?PageType=2&AdId=' . $expert->id[$i] . '"'; ?>><?php echo $expert->header[$i]; ?></a>
                                        <p class="text_expert"><?php echo $expert->description[$i]; ?></p>
                                        <a class="site_firm" <?php echo 'href="' . $expert->site[$i] . '"'; ?> title="Сайт эксперта"><?php echo $expert->site[$i]; ?></a>
                                    </div>
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <tr>
                                            <td style="width: 109px;">
                                                <?php
                                                if ($expert->online[$i] == '1') {
                                                    ?>
                                                    <p class="online" title="Сейчас на сайте">Эксперт ONLINE</p>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <p class="ofline" title="Отсутствует на сайте">Эксперт OFFLINE</p>
                                                    <?php
                                                }
                                                ?>
                                            </td>
                                            <td style="text-align: right;"><a class="but_exp" <?php echo 'href="./?PageType=2&Question=1&AdId=' . $expert->id[$i] . '"'; ?>>Задать вопрос</a></td>
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
                    if ($ShowParamID == 2) {
                        $expert = new ExpertT();
                        $expert->LoadExperts(0, 0, $where);
                        try {
                            $query = $mysql->prepare('UPDATE k_experts SET k_e_watches=:watches WHERE k_e_id=:id');
                            $expert->watches[0] +=1;
                            $query->execute(array(":watches" => $expert->watches[0], ":id" => $expert->id[0]));
                        } catch (PDOException $e) {
                            
                        }
                        ?>
                        <div class="block_content_1">   <!--Информация по Эксперту-->
                            <div class="block_expert_opisanie">
                                <div class="bl_im_exp">
                                    <?php
                                    if ($expert->avatar[0]) {
                                        echo '<img class="img_expert_opisanie" src="../admin/' . $expert->avatar[0] . '" alt="">';
                                    } else {
                                        echo '<img class="img_expert_opisanie" src="../admin/images/noimage.png" alt="">';
                                    }
                                    ?>
                                </div>
                                <div class="block_text_opisanie">
                                    <a class="team_text_expert"><?php echo $expert->header[0]; ?></a>
                                    <p class="text_expert"><?php echo $expert->brief[0]; ?></p><br>
                                    <table class="tab_exp">
                                        <tr>
                                            <td><a class="exp_1">Адрес:</a></td>
                                            <td colspan="2"><a <?php echo 'href="../map/?f=' . $expert->address_str[0] . '"'; ?> class="exp_2"><?php echo $expert->address_str[0]; ?></a></td>
                                        </tr>
                                        <tr>
                                            <td><a class="exp_1">Телефон:</a></td>
                                            <td colspan="2"><a class="exp_2"><?php echo $expert->phone[0]; ?></a></td>
                                        </tr>
                                        <tr>
                                            <td><a class="exp_1">Сайт:</a></td>
                                            <td colspan="2"><a <?php echo 'href="' . $expert->site[0] . '"'; ?> class="exp_3"><?php echo $expert->site[0]; ?></a></td>
                                        </tr>
                                        <tr>
                                            <td><a class="exp_1">E-mail:</a></td>
                                            <td colspan="2"><a class="exp_2"><?php echo $expert->email[0]; ?></a></td>
                                        </tr>
                                        <tr>
                                            <td><a class="exp_2">Просмотров:</a></td>
                                            <td colspan="2"><a class="exp_2"><?php echo $expert->watches[0]; ?></a></td>
                                        </tr>
                                        <!--<tr>
                                            <td colspan="3">
                                                <a class="exp_3" onclick="ShowFormspVopros(2);"><img src="images/karandash.png" alt="">Задать вопрос</a>
                                                <a class="exp_6" onclick="ShowFormspVopros(1);"><img src="images/karandash_1.png" alt="">Посмотреть ответы</a>
                                            </td>
                                        </tr>-->
                                    </table>
                                </div>
                                <div style="text-align: right;">
                                    <?php
                                    if ($Question == 1) {
                                        echo '<a class="but_exp" onclick="ShowFormspVopros(this);">К ответам</a>';
                                    } else {
                                        echo '<a class="but_exp" onclick="ShowFormspVopros(this);">Задать вопрос</a>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <p class="text_expert_1"><?php echo $expert->theme[0]; ?><br>
                                <?php echo $expert->description[0]; ?>
                            </p>
                            <div class="vopros_otvet" <?php
                            if ($Question == 0) {
                                echo 'style="display: block;"';
                            } else {
                                echo 'style="display: none;"';
                            }
                            ?> >
                                     <?php
                                     $ans_ques = new QuestionAnswer();
                                     $ans_ques->Load($expert->id[0]);
                                     for ($i = 0; $i < count($ans_ques->ans_id); $i++) {
                                         if ($ans_ques->ans_id[$i]) {
                                             ?>
                                        <div class="expert_otvet">
                                            <a class="exp_4"><span style="color: #838181; font-weight:normal;"><?php echo $ans_ques->name[$i]; ?>&nbsp;<?php echo $ans_ques->date[$i]; ?></span></a>
                                            <div class="vopr">
                                                <p class="text_expert"><?php echo $ans_ques->text_q[$i]; ?></p>
                                            </div>
                                            <div class="otv">
                                                <b>Ответ эксперта:</b>
                                                <p class="text_expert_otv"><?php echo $ans_ques->text_a[$i]; ?></p>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>

                            <div class="vopros_otvet" <?php
                            if ($Question == 0) {
                                echo 'style="display: none;"';
                            } else {
                                echo 'style="display: block;"';
                            }
                            ?> >
                                <div class="expert_vopr">
                                    <form action="./" method="post" enctype="multipart/form-data" onsubmit="return AddComment();">
                                        <table style="width: 100%; border-collapse: collapse; text-align: left;">
                                            <!--<tr>
                                                <td colspan="2"><span class="team_text_expert">Задать вопрос</span></td>
                                            </tr>-->
                                            <!--<tr>
                                                <td colspan="2"><a class="exp_5">Поля отмеенные звездочкой * обязательны для заполнения</a></td>
                                            </tr>-->
                                            <tr>
                                                <td style="width: 50%">
                                                    <p class="exp_1">Ваше имя: <span style="color: red;">*</span><input class="s_inp_1" name="your_name" type="text" <?php echo 'value="' . $_POST['your_name'] . '"'; ?>></p>
                                                </td>
                                                <!--<td style="width: 10%;"><input class="s_inp_1" name="your_name" type="text" <?php echo 'value="' . $_POST['your_name'] . '"'; ?>></td>-->
                                                <td style="width: 50%;">
                                                    <p class="exp_1">E-mail: <input class="s_inp_1" name="your_email" type="text" <?php echo 'value="' . $_POST['your_email'] . '"'; ?>></p>
                                                </td>
                                                <!--<td style="width: 10%;"><input class="s_inp_1" name="your_email" type="text" <?php echo 'value="' . $_POST['your_email'] . '"'; ?>></td>-->
                                            </tr>
                                            <tr>
                                                <td colspan="2"><a class="exp_1">Вопрос: <span style="color: red;">*</span></a></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <div class="left_bl_vopros">
                                                        <textarea class="ar_vopr" rows="10" cols="53" name="your_question"><?php echo $_POST['your_question']; ?></textarea>
                                                    </div>
                                                    <div class="right_bl_vopros">
                                                        <div style="width: auto; padding-left: 5px; padding-right: 5px;">
                                                            <p style="padding: 0px; margin: 0px; padding-bottom: 10px;"><i style="font-size: 80%;">Нажмите на кружок на картинке чтобы отправить вопрос.</i></p>
                                                            <input type="hidden" name="number" <?php echo 'value="' . $AdId . '"'; ?>>
                                                            <input type='image' name='submit' src='inc/captcha.php' alt='Captcha Security'>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <!--<tr>
                                                <td><a class="exp_1">E-mail:</a></td>
                                                <td><input class="s_inp_1" name="your_email" type="text" <?php echo 'value="' . $_POST['your_email'] . '"'; ?>></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><a class="exp_1">Вопрос: <span style="color: red;">*</span></a><br>
                                                    <textarea class="ar_vopr" rows="10" cols="53" name="your_question"><?php echo $_POST['your_question']; ?></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <i style="font-size: 80%;">Чтобы убедиться, что вы не робот, для отправки нажмите красный кружок на картинке</i><br>
                                                    <input type="hidden" name="number" <?php echo 'value="' . $AdId . '"'; ?>>
                                                    <input type='image' name='submit' src='inc/captcha.php' alt='Captcha Security'>
                                                </td>
                                            </tr>-->
                                        </table>
                                    </form>

                                    <?php
                                    if (!empty($results)) {
                                        echo "<div style='color:#990000; margin-bottom: 20px;'>" . $results . "</div>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>


                <div class="right_expert_block">
                    <div class="kriteri_expert">
                        <div class="shapka_bloka">
                            <p class="style_shapka_1">Консультации экспертов</p>
                        </div>
                        <div class="obverden_bl_right">
                            <!--<div class="add_expert_new_block">
                                <a class="add_expert_new" onclick="$('.block_content_1').toggle();">Стать экспертом</a>
                            </div>-->
                            <p class="text_konsultacii">
                                <?php
                                echo $content_page_1->text;
                                ?>
                            </p>
                        </div>
                    </div>
                </div>

            </div>
    <?php
        require_once '../inc/footer.php';
        ?>
            <!--ВСПЛЫВАЮЩИЕ ОКНА-->

            <div class="temno" id="temno"></div>   <!--Всплывающие окна конец-->
            <div class="prozrachno" id="prozrachno" onclick="$('.vhod_block_gl').hide(500);
                disableP();"></div>   <!--Всплывающие окна прозрачное для закрытия входа-->
        </div>
    </body>
</html>