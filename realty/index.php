<?php
define('TOMSKLINE', 1);
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="ru">
    <head>
        <title>TOMSK-LINE.RU. Недвижимость.</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="keywords" content="">
        <meta name="description" content="">
        <link rel="stylesheet" type="text/css" href="../css/style.css">
        <link rel="stylesheet" type="text/css" href="../css/search.css">
        <link rel="stylesheet" type="text/css" href="css/nedvigimost.css">
        <link rel="stylesheet" type="text/css" href="../css/style_wind.css">
        <link rel="stylesheet" type="text/css" href="tcal.css">
        <link rel="stylesheet" type="text/css" href="../css/show_img.css">
        <script type="text/javascript" src="js/tcal.js"></script> 
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script type="text/javascript" src="../js/wind.js"></script>
        <script type="text/javascript" src="../js/show_img.js"></script>
        <script type="text/javascript" src="js/immovable.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#menu_2 ul').each(function(index) {
                    $(this).prev().addClass('collapsible').click(function() {
                        if ($(this).next().css('display') == 'none') {
                            $(this).next().slideDown(200, function () {
                                $(this).prev().removeClass('collapsed').addClass('expanded');
                            });
                        }else {
                            $(this).next().slideUp(200, function () {
                                $(this).prev().removeClass('expanded').addClass('collapsed');
                                $(this).find('ul').each(function() {
                                    $(this).hide().prev().removeClass('expanded').addClass('collapsed');
                                });
                            });
                        }
                        return false;
                    });
                });
            });
            function ResizeMenu()
            {
                //$(".text_inp_ser").val($('#show_menu').outerWidth());
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
        $whereBuys = '';
        $WhereBuy = '';
        $WhereNews = '';
        $Category = '';
        $results = '';
        $ShowParamID = 1;
        $limit = 10;
        $id_ad = 0;
        $new_url = array();

        require_once '../inc/configs.php';
        require_once 'inc/classes.php';
        require_once '../admin/admin_realty/inc/classes.php';
        require_once 'inc/postget.php';
        require_once '../inc/functions.php';
        if (YourIPBanned()) {
            header($_ENV['SERVER_PROTOCOL'] . " 401 IP BAN", true, 401);
            header("Location: http://" . _SERVER_ADDRESS . "/pageBan.php");
        }
        ImmovablePreload();
        $ful_link = '';
        if (count($new_url) > 0) {
            for ($i = 0; $i < count($new_url); $i++) {
                $ful_link .= $new_url[$i];
                if ($i != count($new_url) - 1) {
                    $ful_link .= '&';
                }
            }
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
            unset($e);
            exit();
        }
        require_once '../admin/inc/functions.php';
        CreateTempTables();

        $statistics = new Statistics();
        $statistics->Statistics();
        $banners = new BannersAll(0);

        //НОМЕР СТРАНИЦЫ
        if (isset($_GET['PageIndex'])) {
            $page = filter_var($_GET['PageIndex'], FILTER_VALIDATE_INT);
        } else {
            $page = 1;
        }

        //РУБРИКА
        if (!isset($Category)) {
            $Category = '<a class="style_menu_left" href="index.php?ShowParam=1">Последние объявления</a>';
        }
        if (!isset($ShowParamID)) {
            $ShowParamID = 1;
        }

        //ПОИСКОВЫЙ ФИЛЬТР
        if (!isset($WhereBuy)) {
            $WhereBuy = '';
        }
        if (!isset($WhereNews)) {
            $WhereNews = '';
        }

        //ЗАГРУЗКА ОБЪЯВЛЕНИЙ
        $ads = new Ads();
        if (($ShowParamID >= 1 && $ShowParamID <= 12) || $ShowParamID == 20 || $ShowParamID == 18) {
            $ads->LoadAds($limit, $page, $WhereBuy, $id_ad);
            if ((count($ads->id) == 0 && $page != 1) || !in_array($ShowParamID, array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20))) {
                header($_ENV['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
                header("Location: http://" . _SERVER_ADDRESS . "/page404.php");
            }
            //echo count($ads->id);
            //die();
        }
        if ($ShowParamID == 13) {
            $buy = new BuyIm();
            $buy->LoadBuys($limit, $page, $whereBuys);
        }
        if ($ShowParamID == 14) {
            $news = new NewsIm();
            $news->LoadNews($limit, $page, $WhereNews);
        }
        $agent = new Agent();
        $agent->LoadAgents(20, 1, '');


        if (isset($_GET['search_string'])) {
            require_once '../inc/search.php';
            //require_once '../realty/inc/classes.php';
            require_once '../admin/admin_catalog/inc/classes.php';
            require_once '../photoboard/inc/classes.php';
            require_once '../job/inc/classes.php';
            $on_map = new SearchOnMap($_GET['search_string']);
            $in_realty = new Ads();
            $in_realty->LoadAds(2, 1, " AND (k_isf_description LIKE '%" . $_GET['search_string'] . "%' " . WhereAddress($_GET['search_string']) . ') ', 0);
            $in_photo = new PhotoAdsTable();
            $in_photo->LoadAds(5, 1, array(), WhereWordsPhoto($_GET['search_string']));
            $in_vacancy = new JobAdsSite();
            $in_vacancy->LoadAds(0, 1, array(array('k_j_type'), array(':type'), array(''), array(1)), WhereWordsJob($_GET['search_string']));
            $in_resume = new JobAdsSite();
            $in_resume->LoadAds(0, 1, array(array('k_j_type'), array(':type'), array(''), array(2)), WhereWordsJob($_GET['search_string']));
            $in_blog = new Blog(1, WhereWordsBlog($_GET['search_string']), 0, 2);
            $in_sites = new Sites(1, WhereWordsSites($_GET['search_string']), 0);
            $in_catalog = new Organizations(1, WhereWordsCatalog($_GET['search_string']), 0, 0);
        }
        ?>
        <?php
        if (isset($_GET['PrintVersion'])) {
            ?>
            <script type="text/javascript">
                $(document).ready(function() {
                    window.print();
                });
            </script>
            <?php
        }
        ?>
    </head>
    <body>
    <?php
    if (!isset($_GET['PrintVersion'])) {
        ?>

        <?php
        require_once '../inc/header.php';
        ?>

            <div class="all_nedvigimost">
                <div class="left_nedvigimost">
                    <?php
                    $statistics->GenerateMenu();
                    $agent->GenerateAgentRegister();
                    ?>
                </div>
                <div class="center_nedvigimost">
                    <?php
                    if (!isset($_GET['search_string'])) {
                        $ads->GenerateSpecial();
                        ?>
                        <?php if ($ShowParamID != 20 && $ShowParamID != 21) { ?>
                            <div id="filter_2" style="display: none;">
                                <!--Загружается пустой блок для скрыть фильтр-->
                            </div>
                            <!-- СТАРАЯ КНОПКА ПОКАЗАТЬ ФИЛЬТР ПОИСКА
                        <div class="add_obiavlenie">
                            <?php if ($ShowParamID >= 2 && $ShowParamID <= 12) { ?>
                                                                                                            <a id="visible_param" class="style_filter" onClick="ShowFormsFilter(1);">Задать параметры поиска</a>
                            <?php } ?>

                            </div>-->
                        <?php } ?>
                        <?php
                        if ($ShowParamID != 20 && $ShowParamID != 13) {
                            ?>
                            <div class="visible_filter_all" id="filter_1"> 
                                <?php
                                $districts = $ads->LoadDistricts();
                                $eq = $ads->LoadEQ();
                                $mat = $ads->LoadMaterail();
                                ?>
                                <form action="index.php" method="GET" id="SearchForm" onsubmit="return SearchSubmit();
                                            return false;">
                                    <div class="parametr_filter">
                                        <span class="parametr_1">
                                            <b>Цена тыс.руб:</b>
                                            <input class="input_1 price_1" name="PriceFrom" type="text" value="<?php
                                                    if (isset($_GET['PriceFrom'])) {
                                                        echo $_GET['PriceFrom'];
                                                    }
                                                            ?>"
                                                   > &ndash;
                                            <input class="input_1 price_1" name="PriceTo" type="text" value="<?php
                                           if (isset($_GET['PriceTo'])) {
                                               echo $_GET['PriceTo'];
                                           }
                                ?>"
                                                   > 
                                        <!--</span>
                                        <span class="parametr_1">-->
                                            <?php if ($ShowParamID < 8) { ?>
                                                <b>Цена за кв/м</b>
                                                <input class="input_1 price_1" name="PriceFromMet" type="text" value="<?php
                                    if (isset($_GET['PriceFromMet'])) {
                                        echo $_GET['PriceFromMet'];
                                    }
                                                ?>"
                                                       > &ndash;
                                                <input class="input_1 price_1" name="PriceToMet" type="text" value="<?php
                                           if (isset($_GET['PriceToMet'])) {
                                               echo $_GET['PriceToMet'];
                                           }
                                                ?>"
                                                       >
                                                   <?php } ?>
                                    </span>
                                    </div>
                                    <div class="parametr_filter">
                                        <span class="parametr_1">
                                            <b>Район:</b>
                                            <a class="add_rai" onclick="$('.raion_up').slideToggle(500);">Выберите район:</a>
                                            <span class="distr_to_search"></span>
                                        </span>
                                        <div class="raion_up">
                                            <a class="cl_up" onclick="$('.raion_up').slideToggle(500);">х</a>
                                            <?php
                                            for ($i = 0; $i < count($districts[0]); $i++) {
                                                if (isset($_GET['DistrictId'])) {
                                                    $checked = '';
                                                    if (in_array($districts[0][$i], $_GET['DistrictId'])) {
                                                        $checked = 'checked';
                                                    }
                                                }
                                                echo '<label><input name="DistrictId[]" type="checkbox" value="' . $districts[0][$i] . '" ' . $checked . '>' . $districts[1][$i] . '</label>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="parametr_filter">
                                        <span class="parametr_1">
                                            <b>Адрес:</b>
                                            <input class="input_2 address_2" name="Address" type="text" value="<?php
                                    if (isset($_GET['Address'])) {
                                        echo $_GET['Address'];
                                    }
                                            ?>"
                                                   >
                                        </span>
                                    </div>
                                    <?php
                                    $rooms = array(3, 4, 9, 10);
                                    if (in_array($ShowParamID, $rooms)) {
                                        ?>
                                        <div class="parametr_filter_close">
                                            <span class="parametr_1">
                                                <b>Колличество комнат:</b>
                                                <label><input type="checkbox" name="Rooms[]" value="1"
                                                    <?php
                                                    if (isset($_GET['Rooms'])) {
                                                        if (in_array("1", $_GET['Rooms'])) {
                                                            echo 'checked';
                                                        }
                                                    }
                                                    ?>>1</label>
                                                <label><input type="checkbox" name="Rooms[]" value="2"
                                                    <?php
                                                    if (isset($_GET['Rooms'])) {
                                                        if (in_array("2", $_GET['Rooms'])) {
                                                            echo 'checked';
                                                        }
                                                    }
                                                    ?>>2</label>
                                                <label><input type="checkbox" name="Rooms[]" value="3"
                                                    <?php
                                                    if (isset($_GET['Rooms'])) {
                                                        if (in_array("3", $_GET['Rooms'])) {
                                                            echo 'checked';
                                                        }
                                                    }
                                                    ?>>3</label>
                                                <label><input type="checkbox" name="Rooms[]" value="4"
                                                    <?php
                                                    if (isset($_GET['Rooms'])) {
                                                        if (in_array("4", $_GET['Rooms'])) {
                                                            echo 'checked';
                                                        }
                                                    }
                                                    ?>>4</label>
                                                <label><input type="checkbox" name="Rooms[]" value="5+"
                                                    <?php
                                                    if (isset($_GET['Rooms'])) {
                                                        if (in_array("5+", $_GET['Rooms'])) {
                                                            echo 'checked';
                                                        }
                                                    }
                                                    ?>>5+</label>
                                            </span>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <?php
                                    if ($ShowParamID != 2 && $ShowParamID != 8) {
                                        $id_immo = 1;
                                        if ($ShowParamID == 3 || $ShowParamID == 9) {
                                            $id_immo = 1;
                                        }
                                        if ($ShowParamID == 4 || $ShowParamID == 10) {
                                            $id_immo = 2;
                                        }
                                        if ($ShowParamID == 5 || $ShowParamID == 11) {
                                            $id_immo = 3;
                                        }
                                        if ($ShowParamID == 6 || $ShowParamID == 12) {
                                            $id_immo = 4;
                                        }
                                        if ($ShowParamID == 7) {
                                            $id_immo = 5;
                                        }
                                        $immo_types = $ads->LoadImmoType($id_immo);
                                        ?>
                                        <div class="parametr_filter_close">
                                            <span class="parametr_1">
                                                <b>Тип:</b>
                                                <select class="input_1 select_1" name="ImmoType">
                                                    <option value="0">Не важно</option>
                                                    <?php
                                                    for ($i = 0; $i < count($immo_types[0]); $i++) {
                                                        $selected = '';
                                                        if (isset($_GET['ImmoType'])) {
                                                            if ($immo_types[0][$i] == $_GET['ImmoType']) {
                                                                $selected = 'selected';
                                                            }
                                                        }
                                                        echo '<option value="' . $immo_types[0][$i] . '" ' . $selected . '>' . $immo_types[1][$i] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </span>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <?php
                                    $material = array(3, 4, 5, 10, 11);
                                    if (in_array($ShowParamID, $material)) {
                                        ?>
                                        <div class="parametr_filter_close">
                                            <span class="parametr_1">
                                                <b>Материал:</b>
                                                <select class="input_1" name="Material">
                                                    <option value="0">Не важно</option>
                                                    <?php
                                                    for ($i = 0; $i < count($mat[0]); $i++) {
                                                        $selected = '';
                                                        if (isset($_GET['Material'])) {
                                                            if ($immo_types[0][$i] == $_GET['Material']) {
                                                                $selected = 'selected';
                                                            }
                                                        }
                                                        echo '<option value="' . $mat[0][$i] . '" ' . $selected . '>' . $mat[1][$i] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </span>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <?php
                                    $new_type = array(3, 4, 5, 6, 11);
                                    if (in_array($ShowParamID, $new_type)) {
                                        ?>
                                        <div class="parametr_filter_close">
                                            <span class="parametr_1">
                                                <b>Вид:</b>
                                                <label><input type="checkbox" name="NewSec[]" value="1"
                                                    <?php
                                                    if (isset($_GET['NewSec'])){
                                                        if (in_array("1", $_GET['NewSec'])) {
                                                            echo 'checked';
                                                        }
                                                    }
                                                    ?>>Новостройки</label>
                                                <label><input type="checkbox" name="NewSec[]" value="2"
                                                    <?php
                                                    if (isset($_GET['NewSec'])){
                                                        if (in_array("2", $_GET['NewSec'])) {
                                                            echo 'checked';
                                                        }
                                                    }
                                                    ?>>Вторичное</label>
                                            </span>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <?php
                                    $eqs = array(3, 4, 5);
                                    if (in_array($ShowParamID, $eqs)) {
                                        ?>
                                        <div class="parametr_filter_close">
                                            <span class="parametr_1">
                                                <b>Отделка:</b>
                                                <span class="inl_span">
                                                    <?php
                                                for ($i = 0; $i < count($eq[0]); $i++) {
                                                    $checked = '';
                                                    if (isset($_GET['EQ'])) {
                                                        if (in_array($immo_types[0][$i], $_GET['EQ'])) {
                                                            $checked = 'checked';
                                                        }
                                                    }
                                                    echo '<label><input type="checkbox" name="EQ[]" value="' . $eq[0][$i] . '" ' . $checked . '>' . $eq[1][$i] . '</label>';
                                                }
                                                ?>
                                                </span>
                                            </span>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                    $area_all = array(3, 4, 5, 6, 9, 10, 11, 12);
                                    if (in_array($ShowParamID, $area_all)) {
                                        ?>
                                        <div class="parametr_filter_close">
                                            <span class="parametr_1">
                                                <b>Общая площадь кв/м:</b>
                                                <input class="input_1" name="AreaAllFrom" type="text" value="<?php
                            if (isset($_GET['AreaAllFrom'])) {
                                echo $_GET['AreaAllFrom'];
                            }
                                        ?>"
                                                       > &ndash;
                                                <input class="input_1" name="AreaAllTo" type="text" value="<?php
                                           if (isset($_GET['AreaAllTo'])) {
                                               echo $_GET['AreaAllTo'];
                                           }
                                        ?>"
                                                       >
                                            </span>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                    $area_kit = array(3, 9);
                                    if (in_array($ShowParamID, $area_kit)) {
                                        ?>
                                        <div class="parametr_filter_close">
                                            <span class="parametr_1">
                                                <b>Площадь кухни кв/м:</b>
                                                <input class="input_1" name="AreaKitFrom" type="text" value="<?php
                            if (isset($_GET['AreaKitFrom'])) {
                                echo $_GET['AreaKitFrom'];
                            }
                                        ?>"
                                                       > &ndash;
                                                <input class="input_1" name="AreaKitTo" type="text" value="<?php
                                           if (isset($_GET['AreaKitTo'])) {
                                               echo $_GET['AreaKitTo'];
                                           }
                                        ?>"
                                                       >
                                            </span>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <?php
                                    $area_land = array(4, 7, 10);
                                    if (in_array($ShowParamID, $area_land)) {
                                        ?>
                                        <div class="parametr_filter_close">
                                            <span class="parametr_1">
                                                <b>Площадь участка сот:</b>
                                                <input class="input_1" name="AreaLandFrom" type="text" value="<?php
                            if (isset($_GET['AreaLandFrom'])) {
                                echo $_GET['AreaLandFrom'];
                            }
                                        ?>"
                                                       > &ndash;
                                                <input class="input_1" name="AreaLandTo" type="text" value="<?php
                                           if (isset($_GET['AreaLandTo'])) {
                                               echo $_GET['AreaLandTo'];
                                           }
                                        ?>"
                                                       >
                                            </span>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <?php
                                    $floor = array(3, 5, 6, 9, 11);
                                    if (in_array($ShowParamID, $floor)) {
                                        ?>
                                        <div class="parametr_filter_close">
                                            <span class="parametr_1">
                                                <b>Этаж:</b>
                                                <input class="input_1" name="FloorFrom" type="text" value="<?php
                            if (isset($_GET['FloorFrom'])) {
                                echo $_GET['FloorFrom'];
                            }
                                        ?>"
                                                       > &ndash;
                                                <input class="input_1" name="FloorTo" type="text" value="<?php
                                           if (isset($_GET['FloorTo'])) {
                                               echo $_GET['FloorTo'];
                                           }
                                        ?>"
                                                       >
                                                       <?php
                                                       if ($ShowParamID == 5) {
                                                           ?>
                                                    <label><input type="checkbox" name="BaseFloor" value="1"
                                                        <?php
                                                        if (isset($_GET['BaseFloor'])) {
                                                            echo 'checked';
                                                        }
                                                        ?>>Цоколь</label>
                                                        <?php
                                                    }
                                                    ?>
                                            </span>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <?php
                                    $floor_all = array(3, 4, 5, 6, 9, 10, 11, 12);
                                    if (in_array($ShowParamID, $floor_all)) {
                                        ?>
                                        <div class="parametr_filter_close">
                                            <span class="parametr_1">
                                                <b>Этажей:</b>
                                                <input class="input_1" name="FloorsFrom" type="text" value="<?php
                            if (isset($_GET['FloorsFrom'])) {
                                echo $_GET['FloorsFrom'];
                            }
                                        ?>"> &ndash;
                                                <input class="input_1" name="FloorsTo" type="text" value="<?php
                                           if (isset($_GET['FloorsTo'])) {
                                               echo $_GET['FloorsTo'];
                                           }
                                        ?>">
                                            </span>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <div class="parametr_filter_close">
                                        <span class="parametr_1">
                                            <b>Фото:</b>
                                            <label><input type="radio" name="Photo" value="1">С фото</label>
                                            <label><input type="radio" name="Photo" value="2">Без фото</label>
                                        </span>
                                    </div>

                                    <?php
                                    $san = array(3, 9);
                                    if (in_array($ShowParamID, $san)) {
                                        ?>
                                        <div class="parametr_filter_close">
                                            <span class="parametr_1">
                                                <b>Санузел:</b>
                                                <select class="input_1" name="San">
                                                    <option value="0">Не важно</option>
                                                    <option value="1"
                                                    <?php
                                                    if (isset($_GET['San']) && $_GET['San'] == 1) {
                                                        echo 'selected';
                                                    }
                                                    ?>>Совмещённый</option>
                                                    <option value="2"
                                                    <?php
                                                    if (isset($_GET['San']) && $_GET['San'] == 2) {
                                                        echo 'selected';
                                                    }
                                                    ?>>Раздельный</option>
                                                </select>
                                            </span>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <?php
                                    $balcony = array(3, 9);
                                    if (in_array($ShowParamID, $balcony)) {
                                        ?>
                                        <div class="parametr_filter_close">
                                            <span class="parametr_1">
                                                <b>Балкон:</b>
                                                <select class="input_1" name="Balcony">
                                                    <option value="0">Не важно</option>
                                                    <option value="1"
                                                    <?php
                                                    if (isset($_GET['Balcony']) && $_GET['Balcony'] == 1) {
                                                        echo 'selected';
                                                    }
                                                    ?>>Балкон</option>
                                                    <option value="2"
                                                    <?php
                                                    if (isset($_GET['Balcony']) && $_GET['Balcony'] == 2) {
                                                        echo 'selected';
                                                    }
                                                    ?>>Лоджия</option>
                                                    <option value="3"
                                                    <?php
                                                    if (isset($_GET['Balcony']) && $_GET['Balcony'] == 3) {
                                                        echo 'selected';
                                                    }
                                                    ?>>Нет</option>
                                                </select>
                                            </span>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <?php
                                    $advanced = array(3, 4, 5, 9, 10, 11);
                                    if (in_array($ShowParamID, $advanced)) {
                                        ?>
                                        <div class="parametr_filter_close">
                                            <span class="parametr_1">
                                                <b>Дополнительно:</b>
                                                <span class="inl_span">
                                                <?php
                                                $phone_stat = array(3, 4, 5, 10, 11);
                                                if (in_array($ShowParamID, $phone_stat)) {
                                                    ?>
                                                    <label><input type="checkbox" name="Adv[1]" value="1"
                                                        <?php
                                                        if (!empty($_GET['ADV'][1])) {
                                                            echo 'checked';
                                                        }
                                                        ?>>Телефон</label>
                                                        <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    $security = array(5, 11);
                                                    if (in_array($ShowParamID, $security)) {
                                                        ?>
                                                    <label><input type="checkbox" name="Adv[2]" value="1"
                                                        <?php
                                                        if (!empty($_GET['ADV'][2])) {
                                                            echo 'checked';
                                                        }
                                                        ?>>Охрана</label>
                                                        <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    $internet = array(5, 11);
                                                    if (in_array($ShowParamID, $internet)) {
                                                        ?>
                                                    <label><input type="checkbox" name="Adv[3]" value="1"
                                                        <?php
                                                        if (!empty($_GET['ADV'][3])) {
                                                            echo 'checked';
                                                        }
                                                        ?>>Интернет</label>
                                                        <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    if ($ShowParamID == 9) {
                                                        ?>
                                                    <label><input type="checkbox" name="Adv[4]" value="1"
                                                        <?php
                                                        if (!empty($_GET['ADV'][4])) {
                                                            echo 'checked';
                                                        }
                                                        ?>>Балкон застеклён</label>
                                                        <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    $furniture = array(9, 10);
                                                    if (in_array($ShowParamID, $furniture)) {
                                                        ?>
                                                    <label><input type="checkbox" name="Adv[5]" value="1"
                                                        <?php
                                                        if (!empty($_GET['ADV'][5])) {
                                                            echo 'checked';
                                                        }
                                                        ?>>Мебель</label>
                                                        <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    $fridge = array(9, 10);
                                                    if (in_array($ShowParamID, $fridge)) {
                                                        ?>
                                                    <label><input type="checkbox" name="Adv[6]" value="1"
                                                        <?php
                                                        if (!empty($_GET['ADV'][6])) {
                                                            echo 'checked';
                                                        }
                                                        ?>>Холодильник</label>
                                                        <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    $washing = array(9, 10);
                                                    if (in_array($ShowParamID, $washing)) {
                                                        ?>
                                                    <label><input type="checkbox" name="Adv[7]" value="1"
                                                        <?php
                                                        if (!empty($_GET['ADV'][7])) {
                                                            echo 'checked';
                                                        }
                                                        ?>>Стиральная машина</label>
                                                        <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    $microwave = array(9, 10);
                                                    if (in_array($ShowParamID, $microwave)) {
                                                        ?>
                                                    <label><input type="checkbox" name="Adv[8]" value="1"
                                                        <?php
                                                        if (!empty($_GET['ADV'][8])) {
                                                            echo 'checked';
                                                        }
                                                        ?>>Микроволновая печь</label>
                                                        <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    $tv = array(9, 10);
                                                    if (in_array($ShowParamID, $tv)) {
                                                        ?>
                                                    <label><input type="checkbox" name="Adv[9]" value="1"
                                                        <?php
                                                        if (!empty($_GET['ADV'][9])) {
                                                            echo 'checked';
                                                        }
                                                        ?>>Телевизор</label>
                                                        <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    $ctv = array(9, 10);
                                                    if (in_array($ShowParamID, $ctv)) {
                                                        ?>
                                                    <label><input type="checkbox" name="Adv[10]" value="1"
                                                        <?php
                                                        if (!empty($_GET['ADV'][10])) {
                                                            echo 'checked';
                                                        }
                                                        ?>>Кабельное телевидение</label>
                                                        <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    if ($ShowParamID == 9) {
                                                        ?>
                                                    <label><input type="checkbox" name="Adv[11]" value="1"
                                                        <?php
                                                        if (!empty($_GET['ADV'][11])) {
                                                            echo 'checked';
                                                        }
                                                        ?>>Кухонная плита</label>
                                                        <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    if ($ShowParamID == 9) {
                                                        ?>
                                                    <label><input type="checkbox" name="Adv[12]" value="1"
                                                        <?php
                                                        if (!empty($_GET['ADV'][12])) {
                                                            echo 'checked';
                                                        }
                                                        ?>>Пластиковые окна</label>
                                                        <?php
                                                    }
                                                    ?>
                                            </span>
                                            </span>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <?php
                                    $utilities = array(9, 10, 11, 12);
                                    if (in_array($ShowParamID, $utilities)) {
                                        ?>
                                        <div class="parametr_filter_close">
                                            <span class="parametr_1">
                                                <b>Коммунальные услуги:</b>
                                                <select class="input_1" name="Utilities">
                                                    <option value="0">Не важно</option>
                                                    <option value="1"
                                                    <?php
                                                    if (isset($_GET['Utilities']) && $_GET['Utilities'] == 1) {
                                                        echo 'selected';
                                                    }
                                                    ?>>Входят в стоимость</option>
                                                    <option value="2"
                                                    <?php
                                                    if (isset($_GET['Utilities']) && $_GET['Utilities'] == 2) {
                                                        echo 'selected';
                                                    }
                                                    ?>>Не входят в стоимость</option>
                                                </select>
                                            </span>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <div class="parametr_filter_close">
                                        <span class="parametr_1">
                                            <b>По дате:</b>
                                            <input type="text" name="DateFrom" class="tcal" value="<?php
                            if (isset($_GET['DateFrom'])) {
                                echo $_GET['DateFrom'];
                            }
                                    ?>"
                                                   > &ndash;
                                            <input type="text" name="DateTo" class="tcal" value="<?php
                                           if (isset($_GET['DateTo'])) {
                                               echo $_GET['DateTo'];
                                           }
                                    ?>"
                                                   >
                                        </span>
                                    </div>
                                    <div class="parametr_filter_but">
                                        <input type="hidden" name="ShowParam" value="<?php echo $ShowParamID; ?>">
                                        <input class="style_filter_but" type="submit" name="Search" value="Поиск">
                                        <input class="style_filter_but" type="reset" name="Reset" value="Сброс">
                                        <input class="style_filter_but" type="button" name="" value="Расширить" onclick="FilterToggle(this);">
                                    </div>
                                </form>
                                <!--<div class="parametr_filter" style="text-align: right;">
                                    <p class="filter_tit">
                                        <b class="filter_but_2" onClick="ShowFormsFilter(2);">
                                            Свернуть параметры поиска
                                            <img style="padding-right: 10px;" src="images/refresh.png" alt="">
                                        </b>
                                    <p>
                                </div>-->
                            </div>
                            <?php
                        }
                        ?>
                        <?php
                        if (($ShowParamID >= 1 && $ShowParamID <= 12) || $ShowParamID == 18) {
                            echo '<div class="block_content_1">';
                            $ful_link_sub = str_replace('&LimitOnPage=' . $limit, '', $ful_link);

                            $show = '<span id="visible_param_1" class="visible_content">Показать по
                        <a href="index.php?LimitOnPage=10&' . $ful_link_sub . '">10</a>
                        <a href="index.php?LimitOnPage=30&' . $ful_link_sub . '">30</a>
                        <a href="index.php?LimitOnPage=50&' . $ful_link_sub . '">50</a>
                        </span>';
                            if ($ShowParamID == 18) {
                                $WhereBuy .= ' AND kisp.k_is_id IS NOT NULL ';
                            }
                            echo str_replace('>' . $limit, ' style="color: #f0938a;" >' . $limit, $show);
                            echo '<span class="style_shapka_1_spec">' . $Category . '</span><a class="style_shapka_3_spec">' . $ads->total . '</a><br>';

                            $ads->GenerateNavigation($page, $limit, $WhereBuy, $ful_link_sub . '&LimitOnPage=' . $limit);
                            $ads->GenerateTable();
                            echo '</div>';
                            $ads->GenerateNavigation($page, $limit, $WhereBuy, $ful_link_sub . '&LimitOnPage=' . $limit);
                        }
                        if ($ShowParamID == 13) {
                            ?>
                            <div class="block_content_1"><span class="style_shapka_1_spec">Купить</span><a class="style_shapka_3_spec"><?php echo $buy->total; ?></a><br>
                                <?php
                                $buy->GenerateNavigation($page, $limit, $whereBuys, $ful_link);
                                $buy->GenerateTable();
                                $buy->GenerateNavigation($page, $limit, $whereBuys, $ful_link);
                                ?>
                                <div class="block_koment">
                                    <p class="text_title_koment">Подать объявление</p>
                                    <script type="text/javascript">
                                        function BuyAdd() {
                                            if ($('#text_buy').val() === '' || $('#phone_buy').val() === '') {
                                                alert('\u0417аполните все поля!');
                                                return false;поднять и
                                            } else {
                                                return true;
                                            }
                                        }
                                    </script>
                                    <form action="index.php" method="post" enctype="multipart/form-data" onsubmit="return BuyAdd();">
                                        <table>
                                            <tr>
                                                <td><label class="label_realty"><input id="buy_sub_check" <?php if ($_GET['BuySubcategory'] == 1) echo 'checked="checked"'; ?> type="radio" name="kupluiu" value="1">Квартиру</label></td>
                                                <td><label class="label_realty"><input type="radio" <?php if ($_GET['BuySubcategory'] == 2) echo 'checked="checked"'; ?> name="kupluiu" value="2">Дом/дачу</label></td>
                                                <td><label class="label_realty"><input type="radio" <?php if ($_GET['BuySubcategory'] == 3) echo 'checked="checked"'; ?> name="kupluiu" value="3">Нежилое</label></td>
                                                <td><label class="label_realty"><input type="radio" <?php if ($_GET['BuySubcategory'] == 4) echo 'checked="checked"'; ?> name="kupluiu" value="4">Гараж/погреб</label></td>
                                                <td><label class="label_realty"><input type="radio" <?php if ($_GET['BuySubcategory'] == 5) echo 'checked="checked"'; ?> name="kupluiu" value="5">Землю</label></td>
                                            </tr>
                                        </table>
                                        <table>
                                            <tr>
                                                <td>
                                                    <textarea id="text_buy" rows="10" cols="50" name="text_buy" style="width: 100%; resize: none;"><?php
                        if (!empty($_POST['text_buy'])) {
                            echo $_POST['text_buy'];
                        }
                                ?></textarea>
                                                </td>
                                            </tr>
                                        </table>
                                        <table>
                                            <tr>
                                                <td class="label_realty">Ваш телефон:<span style="color: red;">*</span><br></td>
                                                <td><input type="text" name="phone_buy" id="phone_buy" value="<?php
                                                if (!empty($_POST['phone_buy'])) {
                                                    echo $_POST['phone_buy'];
                                                }
                                ?>"></td>
                                            </tr>
                                        </table>
                                        <table>
                                            <tr>
                                                <td>
                                                    <i style="font-size: 80%;">Чтобы убедиться, что вы не робот, для отправки нажмите розовый кружок на картинке</i><br>
                                                    <input type='image' name='submit' src='inc/captcha.php' alt='Captcha Security'>
                                                </td>
                                            </tr>
                                        </table>
                                    </form>

                                    <?php
                                    if (!empty($results)) {
                                        echo "<div style='color:#990000; margin-bottom: 20px;'>" . $results . "</div>";
                                    }
                                    ?>

                                </div>
                            </div>
                            <?php
                        }
                        if ($ShowParamID == 14) {
                            echo '<div class="block_content_1"><span class="style_shapka_1_spec">Новости</span><a class="style_shapka_3_spec">' . count($news->news_id) . '</a><br><br>';

                            $news->GenerateNavigation($page, $limit, $WhereNews,$ful_link_sub . '&LimitOnPage=' . $limit);
                            $news->GenerateTable();
                            $news->GenerateNavigation($page, $limit, $WhereNews,$ful_link_sub . '&LimitOnPage=' . $limit);
                            echo '</div>';
                        }
                        if ($ShowParamID == 20) {
                            $ads->GenerateAd($id_ad);
                        }
                        if ($ShowParamID == 19) {
                            $agent->GenerateTable();
                        }
                        if ($ShowParamID == 21) {
                            $agent->GenerateAgent($_GET['Aid']);
                        }
                        ?>
                        <?php
                    }
                    ?>

                    <?php
                    if (isset($_GET['search_string'])) {
                        ?>
                        <div class="block_content_1">

                            <?php
                            if ($in_realty->total != 0) {
                                ?>
                                <div class="push_all_search">
                                    <p class="name_push">Найдено в недвижимости<span><?php echo $in_realty->total; ?></span></p>
                                    <?php
                                    $in_realty->GenerateTable();
                                    if ($in_realty->total > 0) {
                                        ?>
                                        <p class="push_all"><a href="">Показать все предложения</a><span><?php echo $in_realty->total; ?></span></p>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                            ?>

                            <?php
                            if (count($in_sites->id) != 0) {
                                ?>
                                <div class="push_all_search">
                                    <p class="name_push">Найдено в сайтах<span><?php echo count($in_sites->id); ?></span></p>
                                    <?php
                                    for ($i = 0; $i < count($in_sites->id); $i++) {
                                        $sc_for = new SitesSubcategories(0, ' AND k_sl_site_id=' . $in_sites->id[$i] . ' ');
                                        if ($i % 2 == 0) {
                                            $class = 'artikle_content_1';
                                        } else {
                                            $class = 'artikle_content_2';
                                        }
                                        ?>
                                        <div class="<?php echo $class; ?>">
                                            <div class="block_artikle_img">
                                                <a target="_blank" href="<?php echo $in_sites->url[$i]; ?>">
                                                    <?php
                                                    if ($in_sites->avatar[$i] && file_exists('../admin/' . $in_sites->avatar[$i])) {
                                                        echo '<img class="img_artikle_content" src="../admin/' . $in_sites->avatar[$i] . '" alt="">';
                                                    } else {
                                                        echo '<img class="img_artikle_content" src="../images/noimage.png" alt="">';
                                                    }
                                                    ?>
                                                </a>
                                            </div>
                                            <div class="block_artikle_text">
                                                <div class="all_artikle_text">
                                                    <p class="name_artikle">
                                                        <a target="_blank" href="<?php echo $in_sites->url[$i]; ?>"><?php echo $in_sites->name[$i]; ?></a>
                                                        <span class="nabe_artikle">№ <?php echo $in_sites->id[$i]; ?></span>
                                                    </p>
                                                    <p class="dannie_artikle">
                                                        <span><?php echo $in_sites->date[$i]; ?></span>
                                                        <?php
                                                        for ($n = 0; $n < count($sc_for->id); $n++) {
                                                            echo '<span class="sp_otst" title="' . $sc_for->name_parent[$n] . '">' . $sc_for->name[$n] . '</span>';
                                                        }
                                                        ?>
                                                    </p>
                                                    <p class="text_artikle"><?php echo $in_sites->description[$i]; ?></p>
                                                    <a class="name_sites" target="_blank" href="<?php echo $in_sites->url[$i]; ?>"><?php echo $in_sites->url[$i]; ?></a>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <p class="push_all"><a href="">Показать все сайты</a><span><?php echo count($in_sites->id); ?></span></p>
                                </div>
                                <?php
                            }
                            ?>

                            <?php
                            if (count($in_catalog->id) != 0) {
                                ?>
                                <div class="push_all_search">
                                    <p class="name_push">Найдено в предприятиях<span><?php echo count($in_catalog->id); ?></span></p>
                                    <?php
                                    if (count($in_catalog->id) > 2) {
                                        $count = 2;
                                    } else {
                                        $count = count($in_catalog->id);
                                    }
                                    for ($i = 0; $i < $count; $i++) {
                                        if ($i % 2 == 0) {
                                            echo '<div class="element_catalog">';
                                        } else {
                                            echo '<div class="element_catalog_1">';
                                        }
                                        $org_addr = new OrganizationAddresses($in_catalog->id[$i]);
                                        ?>
                                        <div class="fuul_text_element">
                                            <a class="name_text_element"><?php echo $in_catalog->name[$i]; ?></a><br>
                                            <a class="open_map">Показать адреса на карте<span class="nambe_of"><?php echo count($org_addr->id); ?></span></a>
                                            <a class="element_style_4">Просмотров<span class="nambe_of"><?php echo $in_catalog->watches[$i]; ?></span></a><br><br>
                                            <table style="margin-left: 15px;">
                                                <tr>
                                                    <td>
                                                        <?php
                                                        if ($in_catalog->site[$i] != 'http://') {
                                                            ?>
                                                            <a class="element_style_1">Сайт:</a>
                                                            <?php
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if ($in_catalog->site[$i] != 'http://') {
                                                            echo '<a href="' . $in_catalog->site[$i] . '" class="element_style_3">' . $in_catalog->site[$i] . '</a>';
                                                        }
                                                        ?>
                                                        <?php
                                                        if ($in_catalog->email[$i]) {
                                                            ?>
                                                            <a class="element_style_4" onclick="$('#send_email_element').show(500);
                                                                                enableA();">Написать письмо</a>
                                                               <?php
                                                           }
                                                           ?>
                                                    </td>
                                                </tr>
                                            </table>
                                            <table style="margin-left: 15px;">
                                                <?php
                                                for ($n = 0; $n < count($org_addr->id); $n++) {
                                                    ?>
                                                    <?php
                                                    if ($org_addr->fid[$n]) {
                                                        ?>
                                                        <tr>
                                                            <td><a class="element_style_1">Адрес:</a></td>
                                                            <td>
                                                                <?php
                                                                echo '<a href="../map/?f=' . $org_addr->address_str[$n] . '" class="open_map">';
                                                                ?>
                                                                <?php
                                                                echo $org_addr->address_str[$n];
                                                                if ($org_addr->address_advanced[$n])
                                                                    echo ' - ' . $org_addr->address_advanced[$n];
                                                                echo '</a>';
                                                                ?>
                                                                <span class="visible_photo_catalog">
                                                                    <?php
                                                                    echo '<img class="map_photo" src="../images/photo_1.png" onmouseover="ShowPhoto(this);" alt="' . $org_addr->address[$n] . '">';
                                                                    ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    for ($m = 0; $m < count($org_addr->phones); $m++) {
                                                        if ($org_addr->phones_numb[$m][0] == $org_addr->id[$n] && $org_addr->phones_numb[$m][1]) {
                                                            ?>
                                                            <tr>
                                                                <td><a class="element_style_1"><?php echo $org_addr->phones_types[$m][2]; ?>:</a></td>
                                                                <td><a class="element_style_2"><?php echo $org_addr->phones_numb[$m][1]; ?></a></td>
                                                            </tr>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td></td>
                                                        <td>
                                                            <?php
                                                            $holidays = array();
                                                            $work_days = array();
                                                            $index = 0;
                                                            $first = TRUE;
                                                            for ($m = 0; $m < count($org_addr->days); $m++) {
                                                                if ($org_addr->days[$m][0] == $org_addr->id[$n]) {
                                                                    if (intval($org_addr->days[$m][2]) == 0) {
                                                                        $holidays[] = $org_addr->days[$m][1];
                                                                    } else {
                                                                        if ($first) {
                                                                            $work_days[$index] = '<img src="../images/clock_green_1.png" alt="">
                                                                        <a class="dat">' . $org_addr->days[$m][1] . '</a>';
                                                                            if ($org_addr->hours_s[$m][1] == '00:00' && $org_addr->hours_e[$m][1] == '00:00') {
                                                                                $work_days[$index] .= '<a class="ser">круглосуточно</a>';
                                                                            } else {
                                                                                $work_days[$index] .= '<a class="ser">с</a><a class="dat">' . $org_addr->hours_s[$m][1] . '</a>
                                                                        <a class="ser">до</a><a class="dat">' . $org_addr->hours_e[$m][1] . '</a>';
                                                                            }
                                                                            if ($org_addr->hours_b_s[$m][1] == '00:00' && $org_addr->hours_b_e[$m][1] == '00:00') {
                                                                                $work_days[$index] .= '<a class="ser">без перерыва</a>';
                                                                            } else {
                                                                                $work_days[$index] .= '<a class="dat">перерыв</a>
                                                                            <a class="ser">с</a><a class="dat">' . $org_addr->hours_b_s[$m][1] . '</a>
                                                                            <a class="ser">до</a><a class="dat">' . $org_addr->hours_b_e[$m][1] . '</a>';
                                                                            }
                                                                            $work_days[$index] .= '<br>';
                                                                            $index++;
                                                                            $first = FALSE;
                                                                        } else {
                                                                            if ($org_addr->hours_s[$m][1] == $org_addr->hours_s[$m - 1][1] &&
                                                                                    $org_addr->hours_e[$m][1] == $org_addr->hours_e[$m - 1][1] &&
                                                                                    $org_addr->hours_b_s[$m][1] == $org_addr->hours_b_s[$m - 1][1] &&
                                                                                    $org_addr->hours_b_e[$m][1] == $org_addr->hours_b_e[$m - 1][1]) {
                                                                                if (preg_match('/\-/', $work_days[$index - 1])) {
                                                                                    $work_days[$index - 1] = str_replace($org_addr->days[$m - 1][1], $org_addr->days[$m][1], $work_days[$index - 1]);
                                                                                } else {
                                                                                    $work_days[$index - 1] = str_replace($org_addr->days[$m - 1][1], $org_addr->days[$m - 1][1] . '-' . $org_addr->days[$m][1], $work_days[$index - 1]);
                                                                                }
                                                                            } else {
                                                                                $work_days[$index] = '<img src="../images/clock_green_1.png" alt="">
                                                                            <a class="dat">' . $org_addr->days[$m][1] . '</a>';
                                                                                if ($org_addr->hours_s[$m][1] == '00:00' && $org_addr->hours_e[$m][1] == '00:00') {
                                                                                    $work_days[$index] .= '<a class="ser">круглосуточно</a>';
                                                                                } else {
                                                                                    $work_days[$index] .= '<a class="ser">с</a><a class="dat">' . $org_addr->hours_s[$m][1] . '</a>
                                                                                <a class="ser">до</a><a class="dat">' . $org_addr->hours_e[$m][1] . '</a>';
                                                                                }
                                                                                if ($org_addr->hours_b_s[$m][1] == '00:00' && $org_addr->hours_b_e[$m][1] == '00:00') {
                                                                                    $work_days[$index] .= '<a class="ser">без перерыва</a>';
                                                                                } else {
                                                                                    $work_days[$index] .= '<a class="dat">перерыв</a>
                                                                                <a class="ser">с</a><a class="dat">' . $org_addr->hours_b_s[$m][1] . '</a>
                                                                                <a class="ser">до</a><a class="dat">' . $org_addr->hours_b_e[$m][1] . '</a>';
                                                                                }
                                                                                $work_days[$index] .= '<br>';
                                                                                $index++;
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                            if (count($work_days) > 0) {
                                                                for ($m = 0; $m < count($work_days); $m++) {
                                                                    echo $work_days[$m];
                                                                }
                                                            }
                                                            ?>
                                                            <?php
                                                            if (count($holidays) > 0) {
                                                                echo '<img src="../images/clock_red_1.png" alt="">';
                                                                for ($m = 0; $m < count($holidays); $m++) {
                                                                    ?>
                                                                    <a class="red_a"><?php echo $holidays[$m]; ?></a>
                                                                    <?php
                                                                }
                                                                ?>
                                                                <a class="dat">выходной</a>
                                                                <?php
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                            </table>
                                        </div>
                                        <?php
                                        echo '</div>';
                                        ?>
                                        <?php
                                    }
                                    ?>
                                    <p class="push_all"><a href="">Показать все предприятия</a><span><?php echo count($in_catalog->id); ?></span></p>
                                </div>
                                <?php
                            }
                            ?>

                            <?php
                            if ($on_map->count != 0) {
                                ?>
                                <div class="push_all_search">
                                    <p class="name_push">Найдено <b>на карте</b><span><?php echo $on_map->count; ?></span></p>
                                    <?php
                                    if ($on_map->count > 5) {
                                        $count = 5;
                                    } else {
                                        $count = $on_map->count;
                                    }
                                    for ($i = 0; $i < $count; $i++) {
                                        echo '<p class="push_all"><a href="../map/?f=' . $on_map->address_str[$i] . '">' . $on_map->address_str[$i] . '</a><img class="push_img" src="../images/photo_1.png" alt=""></p>';
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                            ?>

                            <?php
                            if ($in_photo->all_ads != 0) {
                                ?>
                                <div class="push_all_search">
                                    <p class="name_push">Найдено <b>в фото объявлениях</b><span><?php echo $in_photo->all_ads; ?></span></p>
                                    <?php
                                    for ($i = 1; $i <= count($in_photo->getID(0)); $i++) {
                                        ?>
                                        <div class="free_img">
                                            <p class="free_text_1">
                                                <a class="l_txt" title="За сегодня просмотров">+<?php echo $in_photo->getViewsDays($i); ?></a>
                                                <a class="r_txt" title="Всего просмотров"><?php echo $in_photo->getViews($i); ?></a>
                                            </p>
                                            <div class="over_img_photodoska_2">
                                                <?php
                                                echo '<a href="../photoboard/?PageType=2&PhotoNum=' . $in_photo->getID($i) . '">';
                                                if ($in_photo->getPhoto($i) && file_exists('../admin/' . str_replace('photo/', 'photo/1_', $in_photo->getPhoto($i)))) {
                                                    echo '<img class="free_img_1" src="../admin/' . str_replace('photo/', 'photo/1_', $in_photo->getPhoto($i)) . '" alt="">';
                                                } else {
                                                    echo '<img class="free_img_1" src="../images/noimage.png" alt="">';
                                                }
                                                echo '</a>';
                                                ?>
                                            </div>
                                            <p class="free_text_2" title="Товар"><?php echo $in_photo->getTheme($i); ?></p>
                                            <p class="free_text_2" title="Цена в руб"><?php echo $in_photo->getPrice($i); ?> руб.</p>
                                        </div>
                                        <?php
                                    }
                                    if (count($in_photo->getID(0)) > 0) {
                                        ?>
                                        <p class="push_all"><a href="">Показать все фото объявления</a><span><?php echo $in_photo->all_ads; ?></span></p>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                            ?>

                            <?php
                            if (count($in_vacancy->id) != 0) {
                                ?>
                                <div class="push_all_search">
                                    <?php
                                    if (count($in_vacancy->id) > 5) {
                                        $count = 5;
                                    } else {
                                        $count = count($in_vacancy->id);
                                    }
                                    ?>
                                    <p class="name_push">Найдено <b>в вакансиях</b><span><?php echo $count; ?></span></p>
                                    <?php
                                    for ($i = 0; $i < $count; $i++) {
                                        ?>
                                        <div class="obiavlenie_job">
                                            <table class="tab_job_elem">
                                                <tr class="tab_job_elem_tr_1">
                                                    <td class="tab_job_elem_td">
                                                        <p class="treb_text_5">
                                                            <?php
                                                            echo $in_vacancy->date_reg[$i];
                                                            ?>
                                                        </p>
                                                    </td>
                                                    <td><p class="treb_text"><a <?php echo 'href="./?PageType=4&Id=' . $in_vacancy->id[$i] . '"'; ?> class="treb_text_1"><?php echo $in_vacancy->post[$i]; ?></a></p></td>
                                                </tr>
                                                <tr class="tab_job_elem_tr_2">
                                                    <td><p class="treb_text_5">График:</p></td>
                                                    <td>
                                                        <p class="treb_text_6">
                                                            <?php
                                                            if ($in_vacancy->schedule[$i]) {
                                                                echo '<a class="treb_text_4">' . $in_vacancy->schedule[$i] . '</a>';
                                                            }
                                                            ?>
                                                        </p> 
                                                    </td>
                                                </tr>
                                                <tr class="tab_job_elem_tr_2">
                                                    <td><p class="treb_text_5">Возраст:</p></td>
                                                    <td>
                                                        <p class="treb_text_6">
                                                            <?php
                                                            if ($in_vacancy->age_min[$i]) {
                                                                echo '<a class="treb_text_4">от ' . $in_vacancy->age_min[$i] . ' ' . plural_form($in_vacancy->age_min[$i], 'год', 'лет') . '</a> ';
                                                            } elseif ($in_vacancy->age_max[$i]) {
                                                                echo '<a class="treb_text_4">до ' . $in_vacancy->age_max[$i] . ' ' . plural_form($in_vacancy->age_max[$i], 'год', 'лет') . '</a>';
                                                            }
                                                            if ($in_vacancy->age_max[$i] && $in_vacancy->age_min[$i]) {
                                                                echo '<a class="treb_text_4">до ' . $in_vacancy->age_max[$i] . ' лет' . '</a>';
                                                            }
                                                            ?>
                                                        </p> 
                                                    </td>
                                                </tr>
                                                <tr class="tab_job_elem_tr_2">
                                                    <td><p class="treb_text_5">Зарплата:</p></td>
                                                    <td>
                                                        <p class="treb_text_6">
                                                            <?php
                                                            if ($in_vacancy->salary_min[$i]) {
                                                                echo '<a class="treb_text_4">от ' . $in_vacancy->salary_min[$i] . ' ' . $in_vacancy->currency_str[$i] . '/месяц</a>';
                                                            } elseif ($in_vacancy->salary_max[$i]) {
                                                                echo '<a class="treb_text_4">до ' . $in_vacancy->salary_max[$i] . ' ' . $in_vacancy->currency_str[$i] . '/месяц' . '</a>';
                                                            }
                                                            if ($in_vacancy->salary_max[$i] && $in_vacancy->salary_min[$i]) {
                                                                echo '<a class="treb_text_4">до ' . $in_vacancy->salary_max[$i] . ' ' . $in_vacancy->currency_str[$i] . '/месяц' . '</a>';
                                                            }
                                                            ?>
                                                        </p> 
                                                    </td>
                                                </tr>
                                                <tr class="tab_job_elem_tr_2">
                                                    <td><p class="treb_text_5">Компания:</p></td>
                                                    <td>
                                                        <p class="treb_text_6">
                                                            <?php
                                                            if ($in_vacancy->organization_name[$i]) {
                                                                echo '<a class="treb_text_4">' . $in_vacancy->organization_name[$i] . '</a>';
                                                            }
                                                            ?>
                                                        </p> 
                                                    </td>
                                                </tr>
                                                <tr class="tab_job_elem_tr_2">
                                                    <td><p class="treb_text_5">Телефон:</p></td>
                                                    <td>
                                                        <p class="treb_text_6">
                                                            <?php
                                                            if ($in_vacancy->contact_phone[$i]) {
                                                                echo '<span class="treb_text_4">' . $in_vacancy->contact_phone[$i] . '</span>';
                                                            }
                                                            ?>
                                                        </p> 
                                                    </td>
                                                </tr>
                                                <tr class="tab_job_elem_tr_2">
                                                    <td><p class="treb_text_5">Разместил:</p></td>
                                                    <td>
                                                        <p class="treb_text_6">
                                                            <?php
                                                            if ($in_vacancy->contact_name[$i]) {
                                                                echo '<span class="treb_text_4">' . $in_vacancy->contact_name[$i] . '</span>';
                                                            }
                                                            ?>
                                                        </p> 
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <?php
                                    }
                                    if ($count > 0) {
                                        ?>
                                        <p class="push_all"><a href="">Показать все вакансии</a><span><?php echo $count; ?></span></p>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                            ?>

                            <?php
                            if (count($in_resume->id) != 0) {
                                ?>
                                <div class="push_all_search">
                                    <?php
                                    if (count($in_resume->id) > 5) {
                                        $count = 5;
                                    } else {
                                        $count = count($in_resume->id);
                                    }
                                    ?>
                                    <p class="name_push">Найдено <b>в резюме</b><span><?php echo $count; ?></span></p>
                                    <?php
                                    for ($i = 0; $i < count($in_resume->id); $i++) {
                                        ?>
                                        <div class="obiavlenie_job_1">
                                            <?php
                                            echo '<a href="./?PageType=5&Id=' . $in_resume->id[$i] . '">';
                                            if ($in_resume->avatar[$i] && file_exists(str_replace('job/', 'job/1_', '../admin/' . $in_resume->avatar[$i]))) {
                                                echo '<img class="img_search_job" src="../admin/' . str_replace('job/', 'job/1_', $in_resume->avatar[$i]) . '" alt="">';
                                            } else {
                                                echo '<img class="img_search_job" src="../images/noimage.png" alt="">';
                                            }
                                            echo '</a>';
                                            ?>
                                            <div class="fuul_text_job">
                                                <table class="tab_job_elem">
                                                    <tr class="tab_job_elem_tr_1">
                                                        <td class="tab_job_elem_td">
                                                            <p class="treb_text_5">
                                                                <?php
                                                                echo $in_resume->date_reg[$i];
                                                                ?>
                                                            </p>
                                                        </td>
                                                        <td><p class="treb_text"><a <?php echo 'href="../job/?PageType=5&Id=' . $in_resume->id[$i] . '"'; ?> class="treb_text_1"><?php echo $in_resume->post[$i]; ?></a></p></td>
                                                    </tr>
                                                    <tr class="tab_job_elem_tr_2">
                                                        <td><p class="treb_text_5">График:</p></td>
                                                        <td>
                                                            <p class="treb_text_6">
                                                                <?php
                                                                if ($in_resume->schedule[$i]) {
                                                                    echo '<a class="treb_text_4">' . $in_resume->schedule[$i] . '</a>';
                                                                }
                                                                ?>
                                                            </p> 
                                                        </td>
                                                    </tr>
                                                    <tr class="tab_job_elem_tr_2">
                                                        <td><p class="treb_text_5">Возраст:</p></td>
                                                        <td>
                                                            <p class="treb_text_6">
                                                                <?php
                                                                if ($in_resume->age_min[$i]) {
                                                                    echo '<a class="treb_text_4">' . $in_resume->age_min[$i] . ' ' . plural_form($in_resume->age_min[$i], 'год', 'лет') . '</a>';
                                                                }
                                                                ?>
                                                            </p> 
                                                        </td>
                                                    </tr>
                                                    <tr class="tab_job_elem_tr_2">
                                                        <td><p class="treb_text_5">Зарплата:</p></td>
                                                        <td>
                                                            <p class="treb_text_6">
                                                                <?php
                                                                if ($in_resume->salary_min[$i]) {
                                                                    echo '<a class="treb_text_4">от ' . $in_resume->salary_min[$i] . ' ' . $in_resume->currency_str[$i] . '/месяц</a>';
                                                                } elseif ($in_resume->salary_max[$i]) {
                                                                    echo '<a class="treb_text_4">до ' . $in_resume->salary_max[$i] . ' ' . $in_resume->currency_str[$i] . '/месяц' . '</a>';
                                                                }
                                                                if ($in_resume->salary_max[$i] && $in_resume->salary_min[$i]) {
                                                                    echo '<a class="treb_text_4">до ' . $in_resume->salary_max[$i] . ' ' . $in_resume->currency_str[$i] . '/месяц' . '</a>';
                                                                }
                                                                ?>
                                                            </p> 
                                                        </td>
                                                    </tr>
                                                    <tr class="tab_job_elem_tr_2">
                                                        <td><p class="treb_text_5">Телефон:</p></td>
                                                        <td>
                                                            <p class="treb_text_6">
                                                                <?php
                                                                if ($in_resume->contact_phone[$i]) {
                                                                    echo '<span class="treb_text_4">' . $in_resume->contact_phone[$i] . '</span>';
                                                                }
                                                                ?>
                                                            </p> 
                                                        </td>
                                                    </tr>
                                                    <tr class="tab_job_elem_tr_2">
                                                        <td><p class="treb_text_5">Разместил:</p></td>
                                                        <td>
                                                            <p class="treb_text_6">
                                                                <?php
                                                                if ($in_resume->contact_name[$i]) {
                                                                    echo '<span class="treb_text_4">' . $in_resume->contact_name[$i] . '</span>';
                                                                }
                                                                ?>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    if ($count > 0) {
                                        ?>
                                        <p class="push_all"><a href="">Показать все резюме</a><span><?php echo $count; ?></span></p>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                            ?>

                            <?php
                            if ($in_blog->all != 0) {
                                ?>
                                <div class="push_all_search">
                                    <p class="name_push">Найдено <b>в статьях</b><span><?php echo $in_blog->all; ?></span></p>
                                    <?php
                                    for ($i = 0; $i < count($in_blog->id); $i++) {
                                        if ($i % 2 == 0) {
                                            $class = 'artikle_content_1';
                                        } else {
                                            $class = 'artikle_content_2';
                                        }
                                        ?>
                                        <div class="<?php echo $class; ?>">
                                            <div class="block_artikle_img">
                                                <a <?php echo 'href="../blog/?PageType=2&ID=' . $in_blog->id[$i] . '"'; ?>>
                                                    <?php
                                                    if ($in_blog->image[$i] && file_exists('../admin/' . $in_blog->image[$i])) {
                                                        echo '<img class="img_artikle_content" title="' . $in_blog->name[$i] . '" src="../admin/' . $in_blog->image[$i] . '" alt="">';
                                                    } else {
                                                        echo '<img class="img_artikle_content" title="' . $in_blog->name[$i] . '" src="../images/noimage.png" alt="">';
                                                    }
                                                    ?>
                                                </a>
                                            </div>
                                            <div class="block_artikle_text">
                                                <div class="all_artikle_text">
                                                    <p class="name_artikle">
                                                        <a <?php echo 'href="../blog/?PageType=2&ID=' . $in_blog->id[$i] . '"'; ?>><?php echo $in_blog->name[$i]; ?></a>
                                                        <span class="nabe_artikle">№ <?php echo $in_blog->id[$i]; ?></span>
                                                    </p>
                                                    <p class="dannie_artikle"><span><?php echo $in_blog->date[$i]; ?>.</span><span class="sp_otst">Просмотров: <?php echo $in_blog->views[$i]; ?>.</span><span class="sp_otst">Статью добавил:<a><?php echo $in_blog->user[$i]; ?></a></span></p>
                                                    <p class="text_artikle"><?php echo $in_blog->brief[$i]; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <p class="push_all"><a href="">Показать все статьи</a><span><?php echo $in_blog->all; ?></span></p>
                                </div>
                                <?php
                            }
                            ?>

                            <?php
                            if ($on_map->count == 0 &&
                                    $in_realty->total == 0 &&
                                    $in_photo->all_ads == 0 &&
                                    count($in_vacancy->id) == 0 &&
                                    count($in_resume->id) == 0 &&
                                    $in_blog->all == 0 &&
                                    count($in_sites->id) == 0 &&
                                    count($in_catalog->id) == 0) {
                                ?>
                                <div>
                                    Ничего не найдено!
                                </div>
                                <?php
                            }
                            ?>
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

            <div id="wind_poto_karti" class="wind_karta">       <!--Всплывающее окно просмотра картинок карты-->
                <a class="close_2" onclick="document.getElementById('wind_poto_karti').style.display = 'none';
                    disableA();">X</a>
                <div class="block_listing">
                    <img id="photo_karta_1" class="im_2" src="images/karta_1.png" alt="">
                    <img id="photo_karta_2" class="im_2" src="images/karta_2.png" alt="">
                    <img id="photo_karta_3" class="im_2" src="images/karta_3.png" alt="">
                    <input type="hidden" id="count_im" value="1">
                </div>
                <div class="listings">
                    <a onclick="prevImage(3);"><img class="left_list" src="images/left_4.png" alt="Листать влево"></a>
                    <a  onclick="changeImage(1);"><img class="mini_listing_img" src="images/karta_1.png" alt=""></a>
                    <a  onclick="changeImage(2);"><img class="mini_listing_img" src="images/karta_2.png" alt=""></a>
                    <a  onclick="changeImage(3);"><img class="mini_listing_img" src="images/karta_3.png" alt=""></a>
                    <a onclick="nextImage(3);"><img class="right_list" src="images/right_4.png" alt="Листать вправо"></a>
                </div>
            </div>
        </div>
        <?php
    } else {
        ?>
        <div class="gl_block_gl">
            <div class="all_nedvigimost">
                <div class="center_nedvigimost">
                    <?php
                    $ads_print = new Ads();
                    $ads_print->LoadThisAds($_GET['ToPrint']);
                    $ads_print->GenerateTable();
                    ?>
                </div>
            </div>
        </div>
        <?php
    }
    ?>


        <div class="photo_map_vsplivaet">
            <img src="" alt="">
        </div>
        <div class="temno" id="temno"></div>
        <div class="prozrachno" id="prozrachno" onclick="$('.vhod_block_gl').hide(500);
            disableP();"></div>
    </body>
</html>