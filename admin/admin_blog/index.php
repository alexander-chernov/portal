<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
define('TOMSKLINE', 1);
session_start();
?>
<!DOCTYPE HTML>
<html lang="ru">
    <head>
        <title>Система управления</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <?php
        if (!isset($_SESSION['login'])) {
            if (isset($_COOKIE['login'])) {
                $_SESSION['login'] = $_COOKIE['login'];
                $_SESSION['password'] = $_COOKIE['password'];
            } else {
                exit('Вы не авторизованы для данной страницы');
            }
        }

        require_once '../inc/configs.php';
        require_once 'inc/classes.php';
        require_once '../inc/functions.php';
        require_once '../../inc/functions.php';
        if (YourIPBanned()) {
            header($_ENV['SERVER_PROTOCOL'] . " 401 IP BAN", true, 401);
            header("Location: http://" . _SERVER_ADDRESS . "/pageBan.php");
        }

        //Подключаемся к БД
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT k_ku_id,k_u_privileges FROM k_users WHERE k_ku_login=:login AND k_ku_password=:password');
            $query->execute(array(":login" => $_SESSION['login'], ":password" => $_SESSION['password']));
            $result = $query->fetch(PDO::FETCH_ASSOC);
            if ($query->rowCount() == 0) {
                exit('Пользователь не найден! Повторите вход!');
            }
        } catch (PDOException $e) {
            exit();
        }
        //Определяем переменные сессии
        $_SESSION['id'] = $result['k_ku_id'];
        $_SESSION['privileges'] = $result['k_u_privileges'];

        require_once '../inc/user_access.php';
        if ($_SESSION['privileges'] != 1) {
            if (!UserAccess(4)) {
                exit('У вас нет прав заходить в эту категорию!');
            }
        } elseif ($_SESSION['privileges'] == 1) {
            $_SESSION['map_access'] = 1;
        }
        UpdateActivityAdmin();
        CreateTempTables();

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

        if (!in_array($ShowParamID, array(1, 2, 3, 4))) {
            header($_ENV['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
            header("Location: http://" . _SERVER_ADDRESS . "/page404.php");
        }
        if (isset($_POST['saveBlog'])) {
            $_POST['BlogSID'] = filter_var($_POST['BlogSID'], FILTER_VALIDATE_INT);
            try {
                $query = $mysql->prepare('UPDATE k_blog SET k_b_text=:text, k_b_date=NOW(), k_b_user=:user WHERE k_b_id=:id');
                $query->execute(array(":id" => $_POST['BlogSID'], ":text" => $_POST['elm1'], ":user" => $_SESSION['login']));
            } catch (PDOException $e) {
                exit();
            }
        }
        if (isset($_POST['SaveN'])) {
            $_POST['BlogCategorieN'] = filter_var($_POST['BlogCategorieN'], FILTER_VALIDATE_INT);
            $_POST['BlogNameN'] = filter_var($_POST['BlogNameN'], FILTER_SANITIZE_STRIPPED);
            $_POST['BlogBriefN'] = filter_var($_POST['BlogBriefN'], FILTER_SANITIZE_STRIPPED);
            try {
                $query = $mysql->prepare('INSERT INTO k_blog
                    (k_b_category,k_b_name,k_b_brief,k_b_text,k_b_user,k_b_date)
                    VALUES (:category,:name,:brief,"",:user,NOW())');
                $query->execute(array(":category" => $_POST['BlogCategorieN'],
                    ":name" => $_POST['BlogNameN'],
                    ":brief" => $_POST['BlogBriefN'],
                    ":user" => $_SESSION['login']));
            } catch (PDOException $e) {
                exit();
            }
        }
        ?>
        <meta name="keywords" content="">
        <meta name="description" content="">
        <link rel="stylesheet" type="text/css" href="../css/style_admin.css">
        <link rel="stylesheet" type="text/css" href="../css/style_wind.css">
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script type="text/javascript" src="../js/wind.js"></script>
        <script type="text/javascript" src="js/scripts.js"></script>
        <script type="text/javascript" src="js/ajaxupload.3.5.js"></script>
        <?php
        if ($ShowParamID == 1) {
            ?>
            <script type="text/javascript">
                $(function() {
                    var btnUpload = $('#BlogImage');
                    var status = $('#status_b');
                    new AjaxUpload(btnUpload, {
                        action: 'upload-file.php',
                        name: 'BlogImage',
                        onSubmit: function(file, ext) {
                            if (!(ext && /^(jpg|png|jpeg|gif)$/.test(ext))) {
                                status.text('Only JPG, PNG or GIF files are allowed');
                                return false;
                            }
                            status.html('<img src="../images/animate.gif" alt="">');
                        },
                        onComplete: function(file, response) {
                            status.text('');
                            if (response === "error") {
                                status.text('\u0412озникла ошибка!');
                            } else {
                                $('#BlogChangeImage').attr('src', response);
                                ChangeImageBlog(response);
                            }
                        }
                    });
                });
            </script>
            <?php
        }
        ?>
        <?php
        if ($ShowParamID == 2) {
            ?>
            <script type="text/javascript">
                $(function() {
                    var btnUpload = $('#BannerLoad');
                    var status = $('#status');
                    new AjaxUpload(btnUpload, {
                        action: 'upload-file.php',
                        name: 'BannerLoad',
                        onSubmit: function(file, ext) {
                            status.html('<img src="../images/animate.gif" alt="">');
                        },
                        onComplete: function(file, response) {
                            status.text('');
                            if (response === "error") {
                                status.text('\u0412озникла ошибка!');
                            } else {
                                $('#BannerChangeID').val(response);
                            }
                        }
                    });
                });
            </script>
            <?php
        }
        ?>
        <?php
        if ($ShowParamID == 4) {
            ?>
            <!-- TinyMCE -->
            <script type="text/javascript" src="../tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
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
            <script type="text/javascript">
                $(function() {
                    var btnUpload = $('#TextRedaktor');
                    var status = $('#status_t');
                    new AjaxUpload(btnUpload, {
                        action: 'upload-file.php',
                        name: 'TextRedaktor',
                        onSubmit: function(file, ext) {
                            if (!(ext && /^(jpg|png|jpeg|gif)$/.test(ext))) {
                                status.text('Only JPG, PNG or GIF files are allowed');
                                return false;
                            }
                            status.html('<img src="../images/animate.gif" alt="">');
                        },
                        onComplete: function(file, response) {
                            status.text('');
                            if (response === "error") {
                                status.text('\u0412озникла ошибка!');
                            } else {
                                status.html('\u0414ля вставки изображения используйте ссылку: <b>' + response + '</b>');
                            }
                        }
                    });
                });
            </script>
            <?php
        }
        ?>
    </head>
    <body>
        <div class="top_block">
            <a class="menu" href="../admin_gl/">Главная</a>
            <a class="menu" href="../admin_realty/">Недвижимость</a>
            <a class="menu" href="../admin_photo/">Фото Объявления</a>
            <!--<a class="menu" href="../admin_expert/">Эксперты</a>-->
            <a class="menu" href="../admin_blog/">Статьи</a>
            <!--<a class="menu" href="../admin_webcam/">Веб-камеры</a>-->
            <a class="menu" href="../admin_job/">Работа</a>
            <a class="menu" href="../admin_catalog/">Каталог</a>
            <!--<a class="menu" href="../admin_sites/">Сайты</a>-->
            <a class="menu" href="../admin_map/">Карта</a>
            <div class="authorization">
                <table>
                    <tr>
                        <td colspan="2"><a  class="autho_3" href="../exit.php">Выход</a></td>
                    </tr>
                    <tr>
                        <td><p class="autho_1">Права:</p></td>
                        <td><p class="autho_2">
                                <?php
                                if ($_SESSION['privileges'] == 2) {
                                    echo 'Модератор';
                                }
                                if ($_SESSION['privileges'] == 1) {
                                    echo 'Администратор';
                                }
                                ?>
                            </p></td>
                    </tr>
                    <tr>
                        <td><p class="autho_1">Логин:</p></td>
                        <td><p class="autho_2"><?php echo $_SESSION['login']; ?></p></td>
                    </tr>
                </table>
            </div>
        </div>
        <p class="topic">Управление разделом: <span style="color: #ff9c00;">Статьи</span></p>
        <div class="center_block">
            <div id="menu_2">
                <a href="./?PageType=1">Статьи</a>
                <a href="./?PageType=2">Банеры</a>
                <a href="./?PageType=3">Рубрики</a>
            </div>

            <?php
            if ($ShowParamID == 1) {
                $where = '';
                $link = '';
                if (isset($_REQUEST)) {
                    $link_ar = array();
                    if (!empty($_GET['b_name'])) {
                        $link_ar['b_name'] = filter_var($_GET['b_name'], FILTER_SANITIZE_STRIPPED);
                        $where .= " AND k_b_name LIKE '%" . $link_ar['b_name'] . "%' ";
                    }
                    if ($_GET['id'] != 0) {
                        $link_ar['id'] = filter_var($_GET['id'], FILTER_VALIDATE_INT);
                        $where .= " AND k_b_id=" . $link_ar['id'] . " ";
                    }
                    if ($_GET['b_cat'] != 0) {
                        $link_ar['b_cat'] = filter_var($_GET['b_cat'], FILTER_VALIDATE_INT);
                        $where .= " AND k_b_category=" . $link_ar['b_cat'] . " ";
                    }
                    $link_ar_url = array();
                    foreach ($link_ar as $key => $value) {
                        $link_ar_url[] = urlencode($key) . '=' . urlencode($value);
                    }
                    $link = '&' . join('&', $link_ar_url);
                }
                $blog = new Blog($page, $where, 1);
                $categories = new BlogCategories();
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Управление статьями</span></b><br><br>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img class="img_options" src="../images/add_team.png" title="Добавить Статью" alt="" onclick="$('#add_blog').show();
                                            enableA();">
                                     <?php
                                     $blocks = @file_get_contents('../../inc/blocks.cfg');
                                     if ($blocks == 'TRUE') {
                                         ?>
                                        <img class="img_options" src="../images/enable.png" title="Отобразить статьи на главной странице" alt="" onclick="EnableBlogBlock(this);">
                                        <?php
                                    }
                                    ?>
                            </td>
                            <td style="text-align: right;">
                                <img id="lupa_plus" class="img_options_1" src="../images/lupa_plus.png" title="Развернуть поиск" alt="" onclick="SearchOpen();">
                                <img  id="lupa_minus" class="img_options_1" style="display: none;" src="../images/lupa_minus.png" title="Свернуть поиск" alt="" onclick="SearchClose();">
                            </td>
                        </tr>
                    </table>
                    <div id="parametr_search" style="display: none;">
                        <form action="./" method="get">
                            <table>
                                <tr>
                                    <td><p class="style_2">По <span style="color: green;">№ статьи</span>:</p></td>
                                    <td><input type="text" name="id" value=""></td>
                                </tr>
                                <tr>
                                    <td><p class="style_2">По <span style="color: green;">названию статьи</span>:</p></td>
                                    <td><input type="text" name="b_name" value=""></td>
                                </tr>
                                <tr>
                                    <td><p class="style_2">По <span style="color: green;">рубрике статьи</span>:</p></td>
                                    <td>
                                        <select name="b_cat">
                                            <?php
                                            for ($i = 0; $i < count($categories->id); $i++) {
                                                echo '<option value="' . $categories->id[$i] . '">' . $categories->name[$i] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><input type="submit" name="search" style="float:left;" value="Найти"><td>
                                </tr>
                            </table>
                        </form>
                    </div>

                    <?php
                    $blog->GenerateNavigation($page, $where, $link);
                    ?>

                    <table style="width: 100%; text-align: center;">
                        <tr style="background: #7caed3;">
                            <td><p class="style_5">№ Статьи</p></td>
                            <td><p class="style_5">Фото</p></td>
                            <td><p class="style_5">Название статьи</p></td>
                            <td><p class="style_5">Статус</p></td>
                            <td><p class="style_5">Рубрика</p></td>
                            <td><p class="style_5">Модератор</p></td>
                            <td><p class="style_5">Дата</p></td>
                            <td><p class="style_5">Действие</p></td>
                        </tr>
                        <?php
                        for ($i = 0; $i < count($blog->id); $i++) {
                            ?>
                            <tr <?php echo 'id="blog_tr_' . $blog->id[$i] . '"'; ?> style="background: #f0f4f4;">
                                <td style="width: 80px;"><p class="style_4"><?php echo $blog->id[$i]; ?></p></td>
                                <td style="width: 80px;">
                                    <?php
                                    if ($blog->image[$i] && file_exists('../' . $blog->image[$i])) {
                                        echo '<img class="img_ob" src="../' . $blog->image[$i] . '" alt="">';
                                    } else {
                                        echo '<img class="img_ob" src="../images/noimage.png" alt="">';
                                    }
                                    ?>
                                </td>
                                <td style="width: 160px;"><p class="style_4"><?php echo $blog->name[$i]; ?></p></td>
                                <td>
                                    <?php
                                    if ($blog->state[$i]) {
                                        echo '<p class="style_4_1">Активно</p>';
                                    } else {
                                        echo '<p class="style_4_2">Скрыто</p>';
                                    }
                                    ?>
                                </td>
                                <td style="width: 160px;"><p class="style_4"><?php echo $blog->category_name[$i]; ?></p></td>
                                <td><p class="style_4"><?php echo $blog->user[$i]; ?></p></td>
                                <td><p class="style_4"><?php echo $blog->date[$i]; ?></p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="BlogChange(' . $blog->id[$i] . ')";'; ?>><img src="../images/edit.png" title="Редактировать статью" alt=""></a>
                                    <a class="a_1" <?php echo 'href="./?PageType=4&ID=' . $blog->id[$i] . '"'; ?>><img src="../images/edit_t.png" title="Текстовый редактор статьи" alt=""></a>
                                    <a class="a_1">
                                        <?php
                                        if ($blog->state[$i] == 0) {
                                            echo '<img src="../images/enable.png" title="Показать статью" onclick="BlogState(this);" alt="' . $blog->id[$i] . '">';
                                        } else {
                                            echo '<img src="../images/disable_1.png" title="Скрыть статью" onclick="BlogState(this);" alt="' . $blog->id[$i] . '">';
                                        }
                                        ?>
                                    </a>
                                    <a class="a_1">
                                        <?php
                                        if ($blog->on_main[$i] == 1) {
                                            ?>
                                            <img src="../images/not_main.png" title="Убрать с главной страницы" alt="<?php echo $blog->id[$i]; ?>" onclick="BlogMainPage(this);">
                                            <?php
                                        } else {
                                            ?>
                                            <img src="../images/on_main.png" title="Добавить на главную страницу" alt="<?php echo $blog->id[$i]; ?>" onclick="BlogMainPage(this);">
                                            <?php
                                        }
                                        ?>
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="DeleteBlog(' . $blog->id[$i] . ')";'; ?>><img src="../images/delete.png" title="Удалить статью" alt=""></a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>

                    <?php
                    $blog->GenerateNavigation($page, $where, $link);
                    ?>

                </div>

                <div id="add_blog" class="wind_1">       <!--Всплывающее окно Добавить Статью-->
                    <a class="close" onclick="CloseWindow('add_blog');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Добавление новой статьи</p>
                    <form action="./" method="post">
                        <table>
                            <tr>
                                <td><p class="style_2">Рубрика статьи:</p></td>
                                <td>
                                    <select name="BlogCategorieN">
                                        <?php
                                        for ($i = 0; $i < count($categories->id); $i++) {
                                            echo '<option value="' . $categories->id[$i] . '">' . $categories->name[$i] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Наименование статьи:</p></td>
                                <td><input type="text" name="BlogNameN" value=""></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <p class="style_2">Краткое описание:</p>
                                    <textarea rows="12" cols="55" name="BlogBriefN"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="submit" name="SaveN" style="float:left;" value="Добавить статью">
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>

                <div id="edit_blog" class="wind_1">       <!--Всплывающее окно редактировать Статью-->
                    <a class="close" onclick="CloseWindow('edit_blog');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Редактировать Статью</p>
                    <table id="edit_blog_table">
                    </table>
                    <button onclick="SaveBlogChange();" style="float:left;">Сохранить изменения</button>
                    <button id="BlogImage">Изменить изображение</button>
                    <div id="status_b"></div>
                </div>
                <?php
            }
            ?>

            <?php
            if ($ShowParamID == 2) {
                $banner = new BannersAll();
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Банеры страницы Статьи</span></b><br><br>
                    <div class="baners">
                        <table border="1">
                            <tr>
                                <td colspan="2"><p class="style_1">Банеры страницы Статьи</p></td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Главный верхний банер</p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[0] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[0] . ');" title="Оставшееся время: ' . $banner->banner_end_days[0] . ' дней">';
                                    if ($banner->banner_end_days[0] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[0] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[0] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Левый верхний банер</p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[1] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[1] . ');" title="Оставшееся время: ' . $banner->banner_end_days[1] . ' дней">';
                                    if ($banner->banner_end_days[1] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[1] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[1] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><p class="style_2">Центральный верхний банер</p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[2] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[2] . ');" title="Оставшееся время: ' . $banner->banner_end_days[2] . ' дней">';
                                    if ($banner->banner_end_days[2] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[2] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[2] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>   
                            </tr>
                            <tr>
                                <td><p class="style_2">Правый верхний банер</p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="ShowBannerInfo(' . $banner->banner_id[3] . ');"'; ?> title="Информация">
                                        <img src="../images/info.png" alt="">
                                    </a>
                                    <?php
                                    echo '<a class="a_1" onclick="ChangeTimeToEnd(' . $banner->banner_id[3] . ');" title="Оставшееся время: ' . $banner->banner_end_days[3] . ' дней">';
                                    if ($banner->banner_end_days[3] < 5) {
                                        echo '<img src="../images/clock_red_1.png" alt="">';
                                    } else {
                                        echo '<img src="../images/clock_green_1.png" alt="">';
                                    }
                                    echo '</a>';
                                    ?>
                                    <a class="a_1" <?php echo 'onclick="BannerView(' . $banner->banner_id[3] . ');"'; ?> title="Просмотр">
                                        <img src="../images/photo_baner.png" alt="">
                                    </a>
                                    <a class="a_1" <?php echo 'onclick="BannerCodeView(' . $banner->banner_id[3] . ');"'; ?>>
                                        <img src="../images/edit.png" title="Редактировать" alt="">
                                    </a>
                                </td>   
                            </tr>
                        </table>
                    </div>
                </div>

                <div id="wind1" class="wind">       <!--Всплывающее окна редактирования банера-->
                    <a class="close" onclick="CloseWindow('wind1');">X</a>
                    <br>
                    <br>
                    <p class="style_4">Вставьте код банера или загрузите его:</p>
                    <input type="hidden" name="BannerChange" id="BannerChange" value="">
                    <textarea id="BannerChangeID" rows="4" cols="50" name="BannerChangeID"></textarea>
                    <button onclick="BannerSave();" style="float:right;">Сохранить</button>
                    <button id="BannerLoad" style="float:right;">Загрузить</button>
                    <div id="status"></div>
                </div>

                <div id="wind2" class="wind">
                    <a class="close" onclick="CloseWindow('wind2');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Внешний вид</p>
                    <div id="BannerViewID"></div>
                </div>

                <div id="info_baner_block" class="wind">       <!--Всплывающее окно редактирования Информации о владельце банера-->
                    <a class="close" onclick="CloseWindow('info_baner_block');">X</a>
                    <br>
                    <br>
                    <p class="style_7">Редактируем Владельца банера:</p>
                    <table id="BannerInfoTable">
                    </table>
                </div>

                <div id="time_baner_block" class="wind">       <!--Всплывающее окно редактирования Времени банера-->
                    <a class="close" onclick="CloseWindow('time_baner_block');">X</a>
                    <br>
                    <br>
                    <p id="BannerAddDaysLast" class="style_7">Период действия банера:</p><br>
                    <table>
                        <tr>
                            <td><p class="style_2">Оставшееся время:</p></td>
                            <td>
                                <input id="BannerAddDays" type="text" value="">дней
                            </td>
                            <td>
                                <input type="hidden" id="BannerAddDaysID" value="">
                                <button onclick="AddDays();" style="float:left;">Установить</button>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
            }
            ?>

            <?php
            if ($ShowParamID == 3) {
                $categories = new BlogCategories();
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Добавление и удаление рубрик Статей</span></b><br><br>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <img class="img_options" src="../images/add_team.png" title="Добавить рубрику" alt="" onclick="document.getElementById('add_new_rub').style.display = 'block';
                                            enableA();">
                            </td>
                        </tr>
                    </table>
                    <table style="width: 100%; text-align: center;">
                        <tr style="background: #7caed3;">
                            <td><p class="style_5">Наименование рубрики</p></td>
                            <td><p class="style_5">Статей в рубрике</p></td>
                            <td><p class="style_5">Действие</p></td>
                        </tr>
                        <?php
                        for ($i = 0; $i < count($categories->id); $i++) {
                            ?>
                            <tr style="background: #f0f4f4;">
                                <td><p <?php echo 'id="blog_org_' . $categories->id[$i] . '"'; ?> class="style_4"><?php echo $categories->name[$i]; ?></p></td>
                                <td><p class="style_4"><?php echo $categories->count[$i]; ?></p></td>
                                <td>
                                    <a class="a_1" <?php echo 'onclick="BlogCategoryNameChange(' . $categories->id[$i] . ');"'; ?>><img src="../images/edit.png" title="Редактировать название рубрики" alt=""></a>
                                    <a class="a_1" <?php echo 'onclick="DeleteCategory(' . $categories->id[$i] . ');"'; ?>><img src="../images/delete.png" title="Удалить рубрику" alt=""></a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>

                <div id="add_new_rub" class="wind">       <!--Всплывающее окно добавления НОВОЙ РУБРИКИ-->
                    <a class="close" onclick="CloseWindow('add_new_rub');">X</a>
                    <br>
                    <br>
                    <table>
                        <tr>
                            <td colspan="2"><p class="style_7">Добавить новую рубрику</p></td>
                        </tr>
                        <tr>
                            <td><p class="style_4">Введите название:</p></td>
                            <td><input type="text" value=""></td>
                        </tr>
                        <tr>
                            <td colspan="2"><button style="float: left;">Добавить</button></td>
                        </tr>
                    </table>
                </div>

                <div id="edit_rub" class="wind">       <!--Всплывающее окно редактировать рубрику-->
                    <a class="close" onclick="CloseWindow('edit_rub');">X</a>
                    <br>
                    <br>
                    <table>
                        <tr>
                            <td colspan="2"><p class="style_7">Редактировать название рубрики</p></td>
                        </tr>
                        <tr>
                            <td><p class="style_4">Введите новое название:</p></td>
                            <td>
                                <input type="hidden" id="BlogCategoryID" value="">
                                <input type="text" id="BlogCategoryName" value="">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button onclick="BlogCategorySave();" style="float: left;">Изменить</button>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
            }
            ?>

            <?php
            if ($ShowParamID == 4) {
                $_GET['ID'] = filter_var($_GET['ID'], FILTER_VALIDATE_INT);
                $blog = new Blog(1, ' AND k_b_id=' . $_GET['ID'] . ' ', 1);
                ?>
                <div class="block_content_1"><b><span style="color: blue;">Редактирование текста статьи</span></b><br><br>
                    <form action="./" method="post">
                        <div>
                            <textarea id="elm1" name="elm1" rows="15" cols="80" style="width: 100%"><?php echo $blog->text[0]; ?></textarea>
                            <?php echo '<input type="hidden" name="BlogSID" value="' . $_GET['ID'] . '">'; ?>
                            <input type="submit" name="saveBlog" value="Сохранить">
                            <button id="TextRedaktor">Загрузить изображение</button>
                            <a href="./">Назад</a>
                            <div id="status_t"></div>
                        </div>
                    </form>
                </div>
                <?php
            }
            ?>
        </div>


        <div class="temno" id="temno"></div>   <!--Всплывающие окна конец-->
    </body>
</html>