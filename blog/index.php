<?php
define('TOMSKLINE', 1);
session_start();
        require_once '../inc/configs.php';
        require_once '../inc/functions.php';
        require_once '../admin/admin_blog/inc/classes.php';
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
            if ($result) {
                $_SESSION['id'] = $result['k_ku_id'];
                $_SESSION['privileges'] = $result['k_u_privileges'];
            }
        } catch (PDOException $e) {
            unset($e);
            exit();
        }

        //Меняем категории
        if (isset($_GET['PageType'])) {
            $ShowParamID = filter_var($_GET['PageType'], FILTER_VALIDATE_INT);
        } else {
            $ShowParamID = 1;
        }
        //Листаем страницы
        if (isset($_GET['PageIndex'])) {
            $page = filter_var($_GET['PageIndex'], FILTER_VALIDATE_INT);
        } else {
            $page = 1;
        }

        $banners = new BannersAll(0);
        $content_page = new PageContent(12);
        ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
    <head>
        <title>TOMSK-LINE.RU. Достопримечательности.</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="keywords" content="">
        <meta name="description" content="">
        <link rel="stylesheet" type="text/css" href="../css/style.css">
        <link rel="stylesheet" type="text/css" href="css/blog.css">
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
            $(window).resize(function() {
                ResizeMenu();
            });
            $(window).ready(function() {
                ResizeMenu();
            });
        </script>
        <!--Отловить размер окна меню-->
        <script type="text/javascript" src="js/ajaxupload.3.5.js"></script>
        <script type="text/javascript">
            $(function() {
                var btnUpload = $('#load_image');
                var status = $('#load_image_s');
                new AjaxUpload(btnUpload, {
                    action: 'upload-file.php',
                    name: 'BlogImage',
                    onSubmit: function(file, ext) {
                        if (!(ext && /^(jpg|png|jpeg|gif)$/.test(ext))) {
                            status.text('Разрешена загрузка JPG, PNG или GIF');
                            return false;
                        }
                        status.html('<img src="../images/animate.gif" alt="">');
                    },
                    onComplete: function(file, response) {
                        status.text('');
                        if (response === "error") {
                            status.text('\u0412озникла ошибка!');
                        } else {
                            status.html('<img alt="" src="' + response + '">');
                            $('#blog_image').val(response);
                        }
                    }
                });
            });
        </script>

    </head>
    <body>

    <?php
    require_once '../inc/header.php';
    ?>
            <div class="centr_block_article">
                <div class="left_block_artikle">
                    <div class="content_left">
                        <div class="shapka_bloka">
                            <a class="style_shapka_1">Статьи</a>
                        </div>
                        <div class="block_menu_artikle">
                            <?php
                            $categories = new BlogCategories();
                            ?>
                            <p class="all_artikle"><a href="./">Все статьи</a><span class="style_menu_left_3"><?php echo $categories->all; ?></span></p>
                            <?php
                            for ($i = 0; $i < count($categories->id); $i++) {
                                ?>
                                <p class="element_artikle"><a <?php echo 'href="./?categorie=' . $categories->id[$i] . '"'; ?>><?php echo $categories->name[$i]; ?></a><span class="style_menu_left_3"><?php echo $categories->count[$i]; ?></span></p>
                                <?php
                            }
                            ?>
                            <div  <?php if(!isset($_GET['Howto'])) echo 'style="display: none;"'; ?>>
                                <div class="all_redactor">
                                    <?php
                                    echo $content_page->text;
                                    ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!--
                <div class="content_left">
                    <div class="shapka_bloka">
                        <a class="name_shapka">Написать статью</a>
                    </div>
                    <div class="block_menu_artikle">
                        <p>
                            <?php
                            echo $content_page_2->text;
                            ?>
                        </p>
                    </div>
                </div>
                -->
            </div>
            <div class="right_block_artikle" <?php if (!isset($_GET['Howto'])) echo 'style="display: none;"'; ?>>
                <div class="all_redactor">
                    <?php
                    echo $content_page->text;
                    ?>
                </div>
                <!-- TinyMCE -->
                <script type="text/javascript" src="/admin/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
                <script type="text/javascript">
                    tinyMCE.init({
                        // General options
                        mode: "textareas",
                        theme: "advanced",
                        skin: "o2k7",
                        width: "100%",
                        height: "600px",
                        plugins: "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups,autosave",
                        // Theme options
                        theme_advanced_buttons1: "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect",
                        theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
                        theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
                        theme_advanced_toolbar_location: "top",
                        theme_advanced_toolbar_align: "left",
                        theme_advanced_statusbar_location: "bottom",
                        theme_advanced_resizing: true,
                        theme_advanced_resizing_use_cookie: false,
                        // Example word content CSS (should be your site CSS) this one removes paragraph margins
                        content_css: "css/word.css",
                        // Drop lists for link/image/media/template dialogs
                        template_external_list_url: "lists/template_list.js",
                        external_link_list_url: "lists/link_list.js",
                        external_image_list_url: "lists/image_list.js",
                        media_external_list_url: "lists/media_list.js"

                    });
                </script>
                <!-- /TinyMCE -->

                <div>
                    <form id="WriteBlog" action="index.php" method="post" onsubmit="return false;">
                        <table style="width: 100%;">
                            <tr>
                                <th>
                                    Написать статью
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <label>Имя статьи<span style="color: red;">*</span> <input type="text" name="writeblog_name" value="" /></label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Краткое описание<span style="color: red;">*</span></label><br>
                                    <textarea cols="50" rows="5" name="writeblog_brief" style="width: 90%;"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Текст статьи<span style="color: red;">*</span></label><br>
                                    <textarea cols="50" rows="20" name="writeblog_text" style="width: 90%;"></textarea>

                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Изображение</label>
                                    <button id="load_image">Добавить изображение</button><br>
                                    <span id="load_image_s"></span>
                                    <input id="blog_image" type="hidden" name="writeblog_image" value="">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Категория<span style="color: red;">*</span></label>
                                    <select name="writeblog_category">
                                        <?php
                                        for ($i = 0; $i < count($categories->id); $i++) {
                                            echo '<option value="' . $categories->id[$i] . '">' . $categories->name[$i] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <button onclick="WriteBlog();">Отправить статью</button>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
                <div class="right_block_artikle" <?php if(isset($_GET['Howto'])) echo 'style="display: none;"'; ?>>
                    <?php
                    if ($ShowParamID == 1) {
                        $where = '';
                        $category = 'Все статьи';
                        if (isset($_GET['categorie'])) {
                            $_GET['categorie'] = filter_var($_GET['categorie'], FILTER_VALIDATE_INT);
                            $where = ' AND k_b_category=' . $_GET['categorie'] . ' ';
                            for ($i = 0; $i < count($categories->id); $i++) {
                                if ($categories->id[$i] == $_GET['categorie']) {
                                    $category = $categories->name[$i];
                                }
                            }
                        }
                        if (isset($_GET['Limit'])) {
                            $limit = filter_var($_GET['Limit'], FILTER_VALIDATE_INT);
                        } else {
                            $limit = 10;
                        }
                        $link = '&Limit=' . $limit;
                        $blog = new Blog($page, $where, 0, $limit);
                        ?>
                        <div class="krit_block">
                            <p class="name_rubrik_artikle"><?php echo $category; ?><span class="title_nambe"><?php echo $blog->all; ?></span>
                                <span id="visible_param_1" class="visible_content">Показать по
                                    <a <?php if ($limit == 10) echo 'style="color: #E23321;"'; ?> href="./?Limit=10">10</a>
                                    <a <?php if ($limit == 30) echo 'style="color: #E23321;"'; ?> href="./?Limit=30">30</a>
                                    <a <?php if ($limit == 50) echo 'style="color: #E23321;"'; ?> href="./?Limit=50">50</a>
                                </span>
                            </p>
                            <!--<div class="param_art">
                                <span id="visible_param_1" class="visible_content">Показывать по
                                    <a <?php if ($limit == 10) echo 'style="color: #198f18;"'; ?> href="./?Limit=10">10</a>
                                    <a <?php if ($limit == 30) echo 'style="color: #198f18;"'; ?> href="./?Limit=30">30</a>
                                    <a <?php if ($limit == 50) echo 'style="color: #198f18;"'; ?> href="./?Limit=50">50</a>
                                </span>
                            </div>-->
                            <?php
                            $blog->GenerateNavigation($page, $where, $link, $limit);
                            ?>
                            <?php
                            for ($i = 0; $i < count($blog->id); $i++) {
                                //if ($i % 2 == 0) {
                                    echo '<div class="artikle_content_2">';
                                /*
                                } else {
                                    echo '<div class="artikle_content_1">';
                                }
                                */
                                ?>
                                <div class="block_artikle_img">
                                    <a <?php echo 'href="./?PageType=2&ID=' . $blog->id[$i] . '"'; ?>>
                                        <?php
                                        if ($blog->image[$i] && file_exists('../admin/' . $blog->image[$i])) {
                                            echo '<img class="img_artikle_content" title="' . $blog->name[$i] . '" src="../admin/' . $blog->image[$i] . '" alt="">';
                                        } else {
                                            echo '<img class="img_artikle_content" title="' . $blog->name[$i] . '" src="../images/noimage.png" alt="">';
                                        }
                                        ?>
                                    </a>
                                </div>
                                <div class="block_artikle_text">
                                    <div class="all_artikle_text">
                                        <p class="dannie_artikle"><span><?php echo $blog->date[$i]; ?>.</span><span class="sp_otst">Просмотров: <?php echo $blog->views[$i]; ?>.</span><span class="sp_otst">Статью добавил:<a><?php echo $blog->user[$i]; ?></a></span></p>
                                        <p class="name_artikle">
                                            <a <?php echo 'href="./?PageType=2&ID=' . $blog->id[$i] . '"'; ?>><?php echo $blog->name[$i]; ?></a>
                                            <!--<span class="nabe_artikle">№ <?php echo $blog->id[$i]; ?></span>-->
                                        </p>
                                        <p class="text_artikle"><?php echo $blog->brief[$i]; ?></p>
                                    </div>
                                </div>
                                <?php
                                echo '</div>';
                            }
                            ?>
                            <?php
                            $blog->GenerateNavigation($page, $where, $link, $limit);
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                    <?php
                    if ($ShowParamID == 2) {
                        $where = '';
                        $_GET['ID'] = filter_var($_GET['ID'], FILTER_VALIDATE_INT);
                        $where = ' AND k_b_id=' . $_GET['ID'] . ' ';
                        $blog = new Blog(1, $where);
                        try {
                                                    $query = $mysql->prepare('UPDATE k_blog SET k_b_views=:watches WHERE k_b_id=:id');
                                                    $blog->views[0] +=1;
                                                    $query->execute(array(":watches" => $blog->views[0], ":id" => $blog->id[0]));
                                                } catch (PDOException $e) {

                                                }
                        ?>
                        <div class="krit_block">
                            <div class="artikle_content_1">
                                <div class="block_artikle_img">
                                    <?php
                                    if ($blog->image[0] && file_exists('../admin/' . $blog->image[0])) {
                                        echo '<img class="img_artikle_content_1" src="../admin/' . $blog->image[0] . '" alt="">';
                                    } else {
                                        echo '<img class="img_artikle_content_1" src="../images/noimage.png" alt="">';
                                    }
                                    ?>
                                </div>
                                <div class="block_artikle_text">
                                    <div class="all_artikle_text">
                                        <p class="name_artikle_1"><?php echo $blog->name[0]; ?><!--<span class="nabe_artikle">№ <?php echo $blog->id[0]; ?></span>--></p>
                                        <p class="dannie_artikle"><span><?php echo $blog->date[0]; ?>.</span><span>Просмотров: <?php echo $blog->views[0]; ?>.</span><span>Статью добавил:<a><?php echo $blog->user[0]; ?></a></span></p>
                                        <p class="text_artikle"><?php echo $blog->brief[0]; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="artikle_content_3">
                                <div class="redaktor_artikle">
                                    <?php
                                    echo str_replace('../images/blog/', '../admin/images/blog/', $blog->text[0]);
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
    <?php
        require_once '../inc/footer.php';
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
