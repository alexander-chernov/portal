<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alexander A. Chernov
 * Date: 28.09.13
 * Time: 13:34
 * To change this template use File | Settings | File Templates.
 */
//include_once "map/core/indexinit.php";
error_reporting('E_ALL');
define('TOMSKLINE', 1);
session_start();
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>TOMSK-LINE.RU. Главная.</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="keywords" content="">
    <meta name="description" content="">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel="stylesheet" type="text/css" href="css/style_wind.css">
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script type="text/javascript" src="js/wind.js"></script>
        <script type="text/javascript" src="js/scripts.js"></script>
        <script type="text/javascript">
            function ResizeMenu() {
                if ($('#show_menu').outerWidth() <= 1200) {
                    $('.rubrika_foto').css('padding-left','10px');
                    $('.rubrika_nedvig').css('padding-left','25px');
                    $('#shapka_realty').css('padding-left','15px');
                } else if ($('#show_menu').outerWidth() > 1200 && $('#show_menu').outerWidth() < 1280) {
                    $('.rubrika_foto').css('padding-left','10px');
                    $('.rubrika_nedvig').css('padding-left','20px');
                    $('#shapka_realty').css('padding-left','25px');
                } else {
                    $('.rubrika_foto').css('padding-left','10px');
                    $('.rubrika_nedvig').css('padding-left','15px');
                    $('#shapka_realty').css('padding-left','35px');
                }
                if ($('#show_menu').outerWidth() > 1250) {
                    $('#show_menu_1').show(100);
                    $('#show_menu_2').hide(100);
                    $('.rubrik').css('padding-left','0px');
                    $('#shapka_ads').css('padding-left','33px');
                } else  {
                    $('#show_menu_1').hide(100);
                    $('#show_menu_2').show(100);
                    $('.rubrik').css('padding-left','10px');
                    $('.rubrik3').css('padding-left','0px');
                    $('#shapka_ads').css('padding-left','33px');
                }
                $('#searchLine').val($('#show_menu').outerWidth());
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
    </head>
    <body onload="<?php /* OnLoadFunctions(true); */ ?>"><?php

        require_once 'admin/inc/configs.php';
        require_once 'admin/admin_gl/inc/classes.php';
        require_once 'inc/classes.php';
        require_once 'inc/functions.php';

        if (YourIPBanned()) {
            header($_ENV['SERVER_PROTOCOL'] . " 401 IP BAN", true, 401);
            header("Location: http://" . _SERVER_ADDRESS . "/pageBan.php");
        }
        $banners = new BannersAll();
        $immovables = new MainPageRealty(1);
        $photoboard = new MainPagePhotoboard(1);
        $experts = new MainPageExperts(1);
        $job = new MainPageJob(1, 1);
        $blog = new MainPageBlog(1);
        $catalog = new MainPageCatalog(1, 1);
        $stat = new StatiscticsMainPage();

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

        $content_page = new PageContent(1);

        require_once 'inc/header.php';
?>
            <div class="rubrik rubrik1">
                <div class="rubrika_nedvig" id="realty">
                    <div class="shapka_bloka">
                        <a href="/realty/" class="name_shapka" id="shapka_realty">Недвижимость</a>
                        <?php
                        if ($immovables->max_pages > 1) {
                            ?>
                            <span class="add_right">
                                <a ><img src="/images/black_arrow_left.png" onmouseover="$(this).attr('src', 'images/red_arrow_left.png');" onmouseout="$(this).attr('src', 'images/black_arrow_left.png');" onclick="ImmovablePages(this);" alt="1" class="left_arrow"></a>
                                <a ><img src="/images/black_arrow.png" id="right_arrow_realty" onmouseover="$(this).attr('src', 'images/red_arrow.png');" onmouseout="$(this).attr('src', 'images/black_arrow.png');" onclick="ImmovablePages(this);" alt="1" class="right_arrow"></a>
                            </span>
                            <script language="javascript">
                                $(window).load(function() {
                                    intId = setInterval(function() {
                                        ImmovablePages(
                                            $("#right_arrow_realty")
                                        )
                                    },35000);
                                });
                            </script>

                        <?php
                        }
                        ?>
                        <a class="add_rubrika_1 realty_right" title="Всего объявлений">Все предложения <span>всего <?php echo $stat->immo_all; ?></span></a>
                        <!--<a class="add_rubrika" title="Последние добавления за сутки">+<?php echo $stat->immo_today; ?></a>-->
                    </div>

                    <div class="block_all_obiavl" id="realty_main">
                        <?php
                        if ($immovables->max_pages > 1) {
                            /*
                            ?>
                            <img class="top_button" onmouseover="$(this).attr('src', 'images/top_button_2.png');" onmouseout="$(this).attr('src', 'images/top_button_1.png');" onclick="ImmovablePages(this);" src="images/top_button_1.png" alt="1">
                            <img class="bottom_button" onmouseover="$(this).attr('src', 'images/botton_button_2.png');" onmouseout="$(this).attr('src', 'images/botton_button_1.png');" onclick="ImmovablePages(this);" src="images/botton_button_1.png" alt="1">
                            <?php
                            */
                        }
                        ?>
                                <div id="vip_nedvig" class="vip_nedvig">
                                    <?php
                                    for ($i = 0; $i < count($immovables->id); $i++) {
                                        echo '<div class="block_white">';
                                        echo '<a href="realty/?ShowParam=20&id='.$immovables->id[$i].'">';

                                        if ($immovables->photo[$i] && file_exists($_SERVER['DOCUMENT_ROOT'].'/admin/images/addresses/' . $immovables->photo[$i])) {
                                            echo '<img class="realty_img" src="/admin/images/addresses/' . $immovables->photo[$i].'" alt="' . strip_tags($immovables->text[$i]) . '">';
                                        } else {
                                            echo '<img class="realty_img" src="/images/s.gif" alt="' . strip_tags($immovables->text[$i]) . '">';
                                        }
                                        echo '</a>';
                                        ?>
                                        <table>
                                            <tr>
                                                <td class="left">Тип</td>
                                                <td class="right"><?=$immovables->type[$i]?></td>
                                            </tr>
                                            <tr>
                                                <td class="left">Район</td>
                                                <td class="right"><?=$immovables->district[$i]?></td>
                                            </tr>
                                            <tr>
                                                <td class="left">Площадь</td>
                                                <td class="right"><?=$immovables->square[$i]?> м2</td>
                                            </tr>
                                            <tr>
                                                <td class="left">Этаж</td>
                                                <td class="right"><?=$immovables->floor[$i]?> этаж</td>
                                            </tr>
                                            <tr>
                                                <td class="left">Комнат</td>
                                                <td class="right"><?=$immovables->rooms[$i]?></td>
                                            </tr>

                                            <tr>
                                                <td colspan="2" class="td-li">
                                                    <!--<li type="square"></li>-->
                                                    <span class="orange_job"></span>
                                                    <hr size="3" class="orange_hr"></td>
                                            </tr>
                                            <tr>
                                                <td class="left"><span class="price"><?=$immovables->price[$i]?> т.р.</span></td>
                                                <td class="right"><a href="realty/?ShowParam=20&id=<?=$immovables->id[$i]?>"><img src="/images/black_arrow.png"
                                                  onmouseover="$(this).attr('src', 'images/red_arrow.png');" onmouseout="$(this).attr('src', 'images/black_arrow.png');"
                                                ></a></td>
                                            </tr>
                                        </table>
                                    <!--
                                        <p class="time_vip_nedvig"></p>
                                        <?php echo $immovables->text[$i]; ?>
                                        <div class="icon_vip_nedvig">
                                            <a class="a_1" <?php echo 'href="map/?fid=map_buildings.' . $immovables->fid[$i] . '"'; ?>>
                                                <img class="kompas_map" <?php echo 'title="Показать на карте, ' . $immovables->address[$i] . '"'; ?> onmouseover="$(this).attr('src', 'images/map_2.png');" onmouseout="$(this).attr('src', 'images/map_1.png');" src="images/map_1.png" alt="">
                                            </a>
                                            <?php
                                            ?>
                                        </div>
                                    -->
                                    </div>
                                    <?php
                                    }
                                    ?>

                                </div>


                    </div>
                </div>
            </div>

            <div class="rubrik rubrik2">
                <div class="rubrika_foto">
                    <div class="shapka_bloka">
                        <a href="/photoboard/" class="name_shapka" id="shapka_ads">Объявления</a>
                        <?php
                        if ($photoboard->max_pages > 1) {
                            ?>
                            <span class="add_right">
                            <a ><img src="/images/black_arrow_left.png" onmouseover="$(this).attr('src', 'images/red_arrow_left.png');" onmouseout="$(this).attr('src', 'images/black_arrow_left.png');" onclick="PhotoPages(this);" alt="1" class="left_arrow"></a>
                            <a ><img src="/images/black_arrow.png" id="right_arrow_ads" onmouseover="$(this).attr('src', 'images/red_arrow.png');" onmouseout="$(this).attr('src', 'images/black_arrow.png');" onclick="PhotoPages(this);" alt="1" class="right_arrow"></a>
                            </span>
                            <script language="javascript">
                                $(window).load(function() {
                                    intId = setInterval(function() {
                                        PhotoPages(
                                            $("#right_arrow_ads")
                                        )
                                    },47000);
                                });
                            </script>

                        <?php
                        }
                        ?>
                        <a class="add_rubrika_1 realty_right" title="Всего объявлений">Все объявления <span>всего <?php echo $stat->photo_all; ?></span></a>
                    </div>

                <div id="PhotoBlock" class="block_all_obiavl block_all_obiavl_h360">
                    <?php

                    if ($photoboard->max_pages >= 0) {
                        /*
                        ?>
                        <img class="right_button" onmouseover="$(this).attr('src', 'images/right_button_2.png');" onmouseout="$(this).attr('src', 'images/right_button_1.png');" onclick="PhotoPages(this);" src="images/right_button_1.png" alt="1">
                        <img class="left_button" onmouseover="$(this).attr('src', 'images/left_button_2.png');" onmouseout="$(this).attr('src', 'images/left_button_1.png');" onclick="PhotoPages(this);" src="images/left_button_1.png" alt="1">
                    <?php
                        */
                    }
                    ?>
                    <div class="vip_photo">
                        <div class="block_all_obiavl" id="vip_photo_obiavl">

                                <?php
                                echo $photoboard->Refresh();

                                ?>


                        </div>
                    </div>
                    <div id="photo_circles" class="listing_img_show">
                        <?php

                        if ($photoboard->max_pages > 1) {
                            $maximum = $photoboard->max_pages;
                            if ($maximum > $photoboard->perMainPage) {
                                $maximum = $photoboard->perMainPage;
                            }
                            echo '<div class="krug_active"></div>';
                            for ($i = 1; $i < $maximum; $i++) {
                                echo '<div class="krug"></div>';
                            }
                            ?>
                        <?php
                        }
                        ?>
                    </div>

                </div>
            </div>
<!--
            <div class="rubrika_expert">
                <div class="shapka_bloka">
                    <a href="expert/" class="name_shapka">Вопрос эксперту</a>
                    <a class="add_rubrika_1" title="Всего экспертов"><?php echo $stat->experts_all; ?></a>
                    <a class="add_rubrika" title="Последние добавления за сутки">+<?php echo $stat->experts_today; ?></a>
                </div>
                <div class="vip_expert">
                    <div class="block_all_obiavl">
                        <?php
                        if ($experts->max_pages > 1) {
                            ?>
                            <img class="top_button_exp" onmouseover="$(this).attr('src', 'images/top_button_2.png');" onmouseout="$(this).attr('src', 'images/top_button_1.png');" onclick="ExpertPages(this);" src="images/top_button_1.png" alt="1">
                            <img class="bottom_button_exp" onmouseover="$(this).attr('src', 'images/botton_button_2.png');" onmouseout="$(this).attr('src', 'images/botton_button_1.png');" onclick="ExpertPages(this);" src="images/botton_button_1.png" alt="1">
                            <?php
                        }
                        ?>
                        <div class="vip_expert_obiavl">
                            <div id="vip_expert" style="width: auto;">
                                <?php
                                echo $experts->Refresh();
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rubrika_kamera">
                <div class="shapka_bloka">
                    <a href="blog/" class="name_shapka">Статьи</a>
                    <a class="add_rubrika_1" title="Всего статей"><?php echo $stat->blog_all; ?></a>
                    <a class="add_rubrika" title="Последние добавления за сутки">+<?php echo $stat->experts_today; ?></a>
                </div>
                <div class="vip_expert">
                    <div class="block_all_obiavl">
                        <?php
                        if ($blog->max_pages > 1) {
                            ?>
                            <img class="top_button_exp" onmouseover="$(this).attr('src', 'images/top_button_2.png');" onmouseout="$(this).attr('src', 'images/top_button_1.png');" onclick="BlogPages(this);" src="images/top_button_1.png" alt="1">
                            <img class="bottom_button_exp" onmouseover="$(this).attr('src', 'images/botton_button_2.png');" onmouseout="$(this).attr('src', 'images/botton_button_1.png');" onclick="BlogPages(this);" src="images/botton_button_1.png" alt="1">
                            <?php
                        }
                        ?>
                        <div class="vip_expert_obiavl">
                            <div id="vip_blog" style="width: auto;">
                                <?php
                                echo $blog->Refresh();
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            -->
        </div>
        <div class="clear"></div>
        <div class="reklama">
            <div class="reklama_1">
                <?php
                if ($banners->banner_end_days[4] > 0) {
                    echo str_replace('../images/banners/', 'admin/images/banners/', $banners->banner_code[4]);
                } else {
                    echo '<img src="/images/s.gif" id="banner3" height="70">';
                }
                ?>
            </div>
            <div class="reklama_2">
                <?php
                if ($banners->banner_end_days[5] > 0) {
                    echo str_replace('../images/banners/', 'admin/images/banners/', $banners->banner_code[5]);
                } else {
                    echo '<img src="/images/s.gif" id="banner4" height="70">';
                }
                ?>
            </div>
        </div>

        <div class="rubrik rubrik3">
            <div class="rubrika_rabota_main">
                <div class="shapka_bloka">

                    <a href="javascript:;" onclick="JobSwitch(this);" id="job_seek" class="menu_rab_active">Вакансии</a>
                    <a href="javascript:;" onclick="JobSwitch(this);" id="job_need" class="menu_rab">Резюме</a>


                    <!--<a class="add_rubrika_1" title="Всего объявлений"><?php echo $stat->job_all; ?></a>
                    <a class="add_rubrika" title="Последние добавления за сутки">+<?php echo $stat->job_today; ?></a>-->
                </div>
                <div id="JobBlock" class="block_all_obiavl block_all_obiavl_h360 job_block">
                    <div id="job_circles" class="listing_img_show">
                        <?php
                        if ($job->max_pages > 1) {
                            $maximum = $job->max_pages;
                            if ($maximum > 6) {
                                $maximum = 6;
                            }
                            echo '<div class="krug_active"></div>';
                            for ($i = 1; $i < $maximum; $i++) {
                                echo '<div class="krug"></div>';
                            }
                        }
                        ?>
                    </div>
                    <?php
                    if ($job->max_pages >= 1) {
                        /*
                        ?>
                        <img class="right_button" onmouseover="$(this).attr('src', 'images/right_button_2.png');" onmouseout="$(this).attr('src', 'images/right_button_1.png');" onclick="JobPages(this);" src="images/right_button_1.png" alt="1">
                        <img class="left_button" onmouseover="$(this).attr('src', 'images/left_button_2.png');" onmouseout="$(this).attr('src', 'images/left_button_1.png');" onclick="JobPages(this);" src="images/left_button_1.png" alt="1">
                        <?php
                        */
                    }
                    ?>
                    <div class="vip_rabota">
                        <div class="vip_rabota_obiavl">
                            <a class="add_rubrika_1">
                                Свежие вакансии и резюме
                                <span>Всего <?=$stat->job_all?></span>
                            </a>
                            <div id="vip_rabota">
                                <?php
                                echo $job->Refresh();
                                ?>
                            </div>
                            <div class="job_line_bot"><hr color="#444" size="1"></div>
                            <div class="job_bottom">
                                <div class="left_bottom"><a href="/job/">Все вакансии</a></div>
                                <div class="center_bottom">
                                <?php
                                if ($job->max_pages >= 1) {
                                    ?>
                                    <a ><img src="/images/black_arrow_left.png" onmouseover="$(this).attr('src', 'images/red_arrow_left.png');" onmouseout="$(this).attr('src', 'images/black_arrow_left.png');" onclick="JobPages(this);" alt="1" class="left_arrow"></a>
                                    <a ><img src="/images/black_arrow.png" id="right_arrow_job" onmouseover="$(this).attr('src', 'images/red_arrow.png');" onmouseout="$(this).attr('src', 'images/black_arrow.png');" onclick="JobPages(this);" alt="1" class="right_arrow"></a>
                                    <script language="javascript">
                                        $(window).load(function() {
                                            intId = setInterval(function() {
                                                JobPages(
                                                    $("#right_arrow_job")
                                                )
                                            },52000);
                                        });
                                    </script>

                                    <?php
                                }
                                ?>
                                </div>
                                <div class="right_bottom"><a href="/job/">Все резюме</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rubrika_karta">
                <div class="shapka_bloka">
                    <a href="map/" class="name_shapka1">События на карте</a>
                </div>
                <div class="block_all_obiavl block_all_obiavl_h360">
                    <a class="add_rubrika_1">
                        Вы всегда можете все события за сегодня на интерактивной карте вашего города!
                    </a>
                    <div class="vip_karta"> 
                        <div class="block_map_gl">
                            <div class="mapview">
                                <iframe src="http://nashtomsk.ru/map/map_frame.php?frame=1" scrolling=no class="iframe_view"></iframe>
                            </div>

                            <!--
                            <div class="fon_karta_gl" style="z-index:9998"></div>
                            <div class="text_karta_gl" style="z-index:9999">
                                <?php
                                //echo $content_page->text;
                                ?>
                            </div>
                            -->
                        </div> 
                    </div>

                </div>
            </div>
            <div class="rubrika_katalog">
                <div class="shapka_bloka">
                    <a href="/blog/" class="name_shapka1">Достопримечательности</a>
                    <!--<a class="add_rubrika_1" title="Всего организаций"><?php echo $stat->catalog_all; ?></a>-->
                </div>
                <!--
                <div id="CatalogBlock" class="block_all_obiavl">
                    <div id="catalog_circles" class="listing_img_show">
                        <?php
                        if ($catalog->max_pages > 1) {
                            $maximum = $catalog->max_pages;
                            if ($maximum > 6) {
                                $maximum = 6;
                            }
                            echo '<div class="krug_active"></div>';
                            for ($i = 1; $i < $maximum; $i++) {
                                echo '<div class="krug"></div>';
                            }
                        }
                        ?>
                    </div>
                    <?php
                    if ($catalog->max_pages > 1) {
                        ?>
                        <img class="right_button" onmouseover="$(this).attr('src', 'images/right_button_2.png');" onmouseout="$(this).attr('src', 'images/right_button_1.png');" onclick="CatalogPages(this);" src="images/right_button_1.png" alt="1">
                        <img class="left_button" onmouseover="$(this).attr('src', 'images/left_button_2.png');" onmouseout="$(this).attr('src', 'images/left_button_1.png');" onclick="CatalogPages(this);" src="images/left_button_1.png" alt="1">
                        <?php
                    }
                    ?>
                    <div class="vip_katalog">
                        <div class="vip_rabota_obiavl vip_catalog_obiavl">
                            <div class="block_menu_rab">
                                <a onclick="CatalogSwitch(this);" id="catalog_c" class="menu_rab_active">Организации</a>
                                <a onclick="CatalogSwitch(this);" id="catalog_s" class="menu_rab">Сайты</a>
                            </div>
                            <div id="vip_catalog">
                                <?php
                                echo $catalog->Refresh();
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                -->
                <div class="vip_expert">
                    <div class="block_all_obiavl block_all_obiavl_h360">
                        <?php
                        if ($blog->max_pages > 1) {
                            ?>
                            <img class="top_button_exp" onmouseover="$(this).attr('src', 'images/top_button_2.png');" onmouseout="$(this).attr('src', 'images/top_button_1.png');" onclick="BlogPages(this);" src="images/top_button_1.png" alt="1">
                            <img class="bottom_button_exp" onmouseover="$(this).attr('src', 'images/botton_button_2.png');" onmouseout="$(this).attr('src', 'images/botton_button_1.png');" onclick="BlogPages(this);" src="images/botton_button_1.png" alt="1">
                        <?php
                        }
                        ?>
                        <a class="add_rubrika_1">
                            Новые материалы вам понравятся. Пишите отзывы!
                        </a>
                        <div class="vip_expert_obiavl transparent" id="BlogPage">

                            <div id="vip_blog" style="width: auto;">
                                <?php
                                echo $blog->Refresh();
                                ?>
                            </div>
                        </div>
                        <div class="job_line_bot"><hr color="#444" size="1"></div>
                        <div class="job_bottom">
                            <div class="left_bottom"><a href="/blog/">Все достопримечательности</a></div>
                            <div class="right_bottom">
                            <?php
                            if ($job->max_pages >= 1) {
                                ?>
                                <a ><img src="/images/black_arrow_left.png" onmouseover="$(this).attr('src', 'images/red_arrow_left.png');" onmouseout="$(this).attr('src', 'images/black_arrow_left.png');" onclick="BlogPages(this);" alt="1" class="left_arrow"></a>
                                <a ><img src="/images/black_arrow.png" id="right_arrow_blog" onmouseover="$(this).attr('src', 'images/red_arrow.png');" onmouseout="$(this).attr('src', 'images/black_arrow.png');" onclick="BlogPages(this);" alt="1" class="right_arrow"></a>
                                <script language="javascript">
                                    $(window).load(function() {
                                        intId = setInterval(function() {
                                            BlogPages(
                                                $("#right_arrow_blog")
                                            )
                                        },65000);
                                    });
                                </script>

                                <?php
                            }
                            ?>
                            </div>

                        </div>

                    </div>
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
        <div class="photo_map_vsplivaet">
            <img src="" alt="">
        </div>
        <!--<script type="text/javascript">Resize();</script>-->
    </body>
</html>