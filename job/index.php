<?php
define('TOMSKLINE', 1);
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="ru">
    <head>
        <title>TOMSK-LINE.RU. Работа.</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <?php
        $ShowParamID = 1;
        $page1 = 1;
        $page2 = 1;
        $limit = 20;
        $ID = 0;
        require_once '../inc/configs.php';
        require_once 'inc/classes.php';
        if (!isset($_GET['search_string'])) {
            require_once 'inc/functions.php';
        }
        require_once '../inc/functions.php';
        require_once '../admin/admin_job/inc/classes.php';
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
            unset($e);
            exit();
        }

        $banners = new BannersAll(0);

        if (!isset($_GET['PageType'])) {
            $ShowParamID = 1;
        } else {
            $ShowParamID = filter_var($_GET['PageType'], FILTER_VALIDATE_INT);
        }
        if (!isset($_GET['PageIndex'])) {
            $page1 = 1;
        } else {
            $page1 = filter_var($_GET['PageIndex'], FILTER_VALIDATE_INT);
        }

        if (isset($_GET['search_string'])) {
            require_once '../inc/search.php';
            require_once '../realty/inc/classes.php';
            require_once '../admin/admin_catalog/inc/classes.php';
            require_once '../photoboard/inc/classes.php';
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

        $whereJob = '';
        if (isset($_GET['VacancySearch'])) {
            $_GET['VSMin'] = filter_var($_GET['VSMin'], FILTER_VALIDATE_INT);
            $_GET['VSMax'] = filter_var($_GET['VSMax'], FILTER_VALIDATE_INT);
            $_GET['VCurrency'] = filter_var($_GET['VCurrency'], FILTER_VALIDATE_INT);
            $_GET['VPost'] = RussianRules(filter_var($_GET['VPost'], FILTER_SANITIZE_STRING));
            $_GET['VOrg'] = filter_var($_GET['VOrg'], FILTER_SANITIZE_STRING);
            if (isset($_GET['VSched']) && $_GET['VSched'] != '0') {
                $whereJob .= ' AND k_j_schedule="' . filter_var($_GET['VSched'], FILTER_SANITIZE_STRIPPED) . '" ';
            }
            if (!empty($_GET['VSMin']) && empty($_GET['VSMax'])) {
                $whereJob .= ' AND ((k_j_salary_min>=' . $_GET['VSMin'] . ' AND k_j_salary_max>=' . $_GET['VSMin'] . ') OR
                    (k_j_salary_min>=' . $_GET['VSMin'] . ' AND k_j_salary_max=0)) ';
            }
            if (!empty($_GET['VSMax']) && empty($_GET['VSMin'])) {
                $whereJob .= ' AND ((k_j_salary_max<' . $_GET['VSMax'] . ' AND k_j_salary_min<' . $_GET['VSMax'] . ') OR
                    (k_j_salary_max=0 AND k_j_salary_min<=' . $_GET['VSMax'] . ')) ';
            }
            if (!empty($_GET['VSMax']) && !empty($_GET['VSMin'])) {
                $whereJob .= ' AND ((k_j_salary_min>=' . $_GET['VSMin'] . ' AND  k_j_salary_max>=' . $_GET['VSMin'] . ') OR
                    (k_j_salary_min<=' . $_GET['VSMax'] . ' AND  k_j_salary_max>=' . $_GET['VSMax'] . ')) ';
            }
            if (!empty($_GET['VCurrency']) && $_GET['VCurrency'] != 0) {
                $whereJob .= ' AND k_j_currency=' . $_GET['VCurrency'] . ' ';
            }
            if (!empty($_GET['VPost'])) {
                $whereJob .= ' AND k_j_post LIKE "%' . $_GET['VPost'] . '%" ';
            }
            if (!empty($_GET['VOrg'])) {
                $whereJob .= ' AND k_jo_name LIKE "%' . $_GET['VOrg'] . '%" ';
            }
            $ShowParamID = 2;
        }

        if (isset($_GET['ResumeSearch'])) {
            $_GET['SSMin'] = filter_var($_GET['SSMin'], FILTER_VALIDATE_INT);
            $_GET['SSMax'] = filter_var($_GET['SSMax'], FILTER_VALIDATE_INT);
            $_GET['SAMin'] = filter_var($_GET['SAMin'], FILTER_VALIDATE_INT);
            $_GET['SAMax'] = filter_var($_GET['SAMax'], FILTER_VALIDATE_INT);
            $_GET['SCurrency'] = filter_var($_GET['SCurrency'], FILTER_VALIDATE_INT);
            $_GET['SPost'] = RussianRules(filter_var($_GET['SPost'], FILTER_SANITIZE_STRING));
            if (!empty($_GET['SSMin']) && empty($_GET['SSMax'])) {
                $whereJob .= ' AND ((k_j_salary_min>=' . $_GET['SSMin'] . ' AND k_j_salary_max>=' . $_GET['SSMin'] . ') OR
                    (k_j_salary_min>=' . $_GET['SSMin'] . ' AND k_j_salary_max=0)) ';
            }
            if (!empty($_GET['SSMax']) && empty($_GET['SSMin'])) {
                $whereJob .= ' AND ((k_j_salary_max<' . $_GET['SSMax'] . ' AND k_j_salary_min<' . $_GET['SSMax'] . ') OR
                    (k_j_salary_max=0 AND k_j_salary_min<=' . $_GET['SSMax'] . ')) ';
            }
            if (!empty($_GET['SSMax']) && !empty($_GET['SSMin'])) {
                $whereJob .= ' AND ((k_j_salary_min>=' . $_GET['SSMin'] . ' AND  k_j_salary_max>=' . $_GET['SSMin'] . ') OR
                    (k_j_salary_min<=' . $_GET['SSMax'] . ' AND  k_j_salary_max>=' . $_GET['SSMax'] . ')) ';
            }
            if (!empty($_GET['SAMin']) && empty($_GET['SAMax'])) {
                $whereJob .= ' AND ((k_j_age_min>=' . $_GET['SAMin'] . ' AND k_j_age_max>=' . $_GET['SAMin'] . ') OR
                    (k_j_age_min>=' . $_GET['SAMin'] . ' AND k_j_age_max=0)) ';
            }
            if (!empty($_GET['SAMax']) && empty($_GET['SAMin'])) {
                $whereJob .= ' AND ((k_j_age_max<' . $_GET['SAMax'] . ' AND k_j_age_min<' . $_GET['SAMax'] . ') OR
                    (k_j_age_max=0 AND k_j_age_min<=' . $_GET['SAMax'] . ')) ';
            }
            if (!empty($_GET['SAMax']) && !empty($_GET['SAMin'])) {
                $whereJob .= ' AND ((k_j_age_min>=' . $_GET['SAMin'] . ' AND  k_j_age_max>=' . $_GET['SAMin'] . ') OR
                    (k_j_age_min<=' . $_GET['SAMax'] . ' AND  k_j_age_max>=' . $_GET['SAMax'] . ')) ';
            }
            if (!empty($_GET['SCurrency']) && $_GET['SCurrency'] != 0) {
                $whereJob .= ' AND k_j_currency=' . $_GET['SCurrency'] . ' ';
            }
            if (!empty($_GET['SPost'])) {
                $whereJob .= ' AND k_j_post LIKE "%' . $_GET['SPost'] . '%" ';
            }
            if (isset($_GET['SMarital']) && $_GET['SMarital'] != '') {
                $whereJob .= ' AND k_jp_marital="' . filter_var($_GET['SMarital'], FILTER_SANITIZE_STRIPPED) . '" ';
            }
            if (isset($_GET['SEducation']) && $_GET['SEducation'] != '0') {
                $whereJob .= ' AND k_j_education_type="' . filter_var($_GET['SEducation'], FILTER_VALIDATE_INT) . '" ';
            }
            if (isset($_GET['SSex']) && $_GET['SSex'] != 0) {
                $whereJob .= ' AND k_j_sex="' . filter_var($_GET['SSex'], FILTER_VALIDATE_INT) . '" ';
            }
            if (isset($_GET['SPhoto']) && $_GET['SPhoto'] != 0) {
                $whereJob .= ' AND k_jp_avatar<>"" ';
            }
            $ShowParamID = 3;
        }
        ?>
        <meta name="keywords" content="">
        <meta name="description" content="">
        <link rel="stylesheet" type="text/css" href="../css/style.css">
        <link rel="stylesheet" type="text/css" href="../css/search.css">
        <link rel="stylesheet" type="text/css" href="css/job.css">
        <link rel="stylesheet" type="text/css" href="../css/style_wind.css">
        <link rel="stylesheet" type="text/css" href="../css/show_img.css">
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script type="text/javascript" src="../js/wind.js"></script>
        <script type="text/javascript">
            function EmailShow(theme, dest) {
                $('#EmailTheme').val(theme);
                $('#EmailDest').text(dest);
                $('#send_email_job').show(500);
                enableA();
            }
        </script>
        <!--Отловить размер окна меню-->
        <script type="text/javascript">
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

            <div class="all_job_block">
                <?php
                if (!isset($_GET['search_string'])) {
                    $perPage = 2;
                    $page1 = $_GET['page1']>0?$_GET['page1']:1;
                    $page2 = $_GET['page2']>0?$_GET['page2']:1;
                    if ($ShowParamID == 1) {

                        $job_req = new JobAdsSite();
                        $job_req->LoadStat();
                        $job_req->LoadAds($perPage, $page1, array(array('k_j_type'), array(':type'), array(''), array(1)), '');

                        $job_search = new JobAdsSite();
                        $job_search->LoadAds($perPage, $page2, array(array('k_j_type'), array(':type'), array(''), array(2)), '');

                        ?>
                        <div class="block_content_1">   <!--Вакансии Требуются и ИЩУ-->
                            <div class="need_job">
                                <div class="kriteri_job">
                                    <div class="shapka_bloka">
                                        <a class="style_shapka_1" href="?PageType=2">Вакансии</a>
                                        <p class="style_shapka_3" title="Всего объявлений"><?php /* echo $job_req->all_req; */ ?></p>
                                    </div>
                                <div class="obveden_block">
                                    <div class="obiavlenie_job noborder">
                                        <form action="./" method="GET">
                                            <table class="job_search_tab">
                                                <tr>
                                                    <td style="width: 130px;"><p class="treb_text">Зарплата:</p></td>
                                                    <td>
                                                        <p class="treb_text">
                                                            <input class="inp_j" type="text" name="VSMin" value=""><input name="VSMax" class="inp_j" type="text" value="">
                                                            <select class="sel_job" name="VCurrency">
                                                                <option value="0">-</option>
                                                                <option value="1">руб</option>
                                                                <option value="2">$</option>
                                                                <option value="3">€</option>
                                                            </select>
                                                        </p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><p class="treb_text">Должность:</p></td>
                                                    <td><input class="inp_j_1" name="VPost" type="text" value=""></td>
                                                </tr>
                                                <tr>
                                                    <td><p class="treb_text">Организация:</p></td>
                                                    <td><input class="inp_j_1" name="VOrg" type="text" value=""></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" style="text-align: right; padding-top: 10px;">
                                                        <a class="but_j_ser" href="?PageType=2">Все вакансии</a>
                                                        <input type="submit" name="VacancySearch" class="but_j_ser" value="Искать">
                                                    </td>
                                                </tr>
                                            </table>
                                        </form>
                                    </div>
                                    <?php

                                    $pages = intval($job_req->all_req / $perPage);
                                    if ($job_req->all_search % $perPage != 0) {
                                        $pages++;
                                    }
                                    if ($pages > 1) {
                                        echo '<div class="listing">';
                                        if ($page == $pages || $page > 1) {
                                            echo '<a class="style_listing" href="./?page1=' . ($page - 1) . '&' . $new_url . '">Предыдущая</a>';
                                        }



                                        if ($pages <= 11) {
                                            for ($i = 1; $i <= $pages; $i++) {
                                                if ($i == $page) {
                                                    echo '<a class="active_listing" href="./?page1=' . $i . '&' . $new_url . '">' . $i . '</a>';
                                                } else {
                                                    echo '<a class="style_listing" href="./?page1=' . $i . '&' . $new_url . '">' . $i . '</a>';
                                                }
                                            }
                                        } else {
                                            if ($page <= 6) {
                                                for ($i = 1; $i <= $page + 2; $i++) {
                                                    if ($i == $page) {
                                                        echo '<a class="active_listing" href="./?page1=' . $i . '&' . $new_url . '">' . $i . '</a>';
                                                    } else {
                                                        echo '<a class="style_listing" href="./?page1=' . $i . '&' . $new_url . '">' . $i . '</a>';
                                                    }
                                                }
                                                echo '<span style="color: #000000; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                                                for ($i = $pages - 2; $i <= $pages; $i++) {
                                                    echo '<a class="style_listing" href="./?page1=' . $i . '&' . $new_url . '">' . $i . '</a>';
                                                }
                                            }
                                            if ($page > 6 && $page <= ($pages - 6)) {
                                                for ($i = 1; $i < 4; $i++) {
                                                    echo '<a class="style_listing" href="./?page1=' . $i . '&' . $new_url . '">' . $i . '</a>';
                                                }
                                                echo '<span style="color: #000000; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                                                for ($i = $page - 2; $i <= $page + 2; $i++) {
                                                    if ($i == $page) {
                                                        echo '<a class="active_listing" href="./?page1=' . $i . '&' . $new_url . '">' . $i . '</a>';
                                                    } else {
                                                        echo '<a class="style_listing" href="./?page1=' . $i . '&' . $new_url . '">' . $i . '</a>';
                                                    }
                                                }
                                                echo '<span style="color: #000000; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                                                for ($i = $pages - 2; $i <= $pages; $i++) {
                                                    echo '<a class="style_listing" href="./?page1=' . $i . '&' . $new_url . '">' . $i . '</a>';
                                                }
                                            }
                                            if ($page > ($pages - 6)) {
                                                for ($i = 1; $i < 4; $i++) {
                                                    echo '<a class="style_listing" href="./?page1=' . $i . '&' . $new_url . '">' . $i . '</a>';
                                                }
                                                echo '<span style="color: #000000; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                                                for ($i = $pages - 5; $i <= $pages; $i++) {
                                                    if ($i == $page) {
                                                        echo '<a class="active_listing" href="./?page1=' . $i . '&' . $new_url . '">' . $i . '</a>';
                                                    } else {
                                                        echo '<a class="style_listing" href="./?page1=' . $i . '&' . $new_url . '">' . $i . '</a>';
                                                    }
                                                }
                                            }
                                        }

                                        if ($page == 1 || $page < $pages) {
                                            echo '<a class="style_listing" href="./?page1=' . ($page + 1) . '&' . $new_url . '">Следующая</a>';
                                        }

                                        echo '</div>';
                                    }


                                    for ($i = 0; $i < count($job_req->id); $i++) {
                                        ?>
                                        <div class="obiavlenie_job">
                                            <table class="tab_job_elem">
                                                <tr class="tab_job_elem_tr_1">
                                                    <td class="tab_job_elem_td">
                                                        <p class="treb_text_5">
                                                            <?php
                                                            echo $job_req->date_reg[$i];
                                                            ?>
                                                        </p>
                                                    </td>
                                                    <td><p class="treb_text"><a <?php echo 'href="./?PageType=4&Id=' . $job_req->id[$i] . '"'; ?> class="treb_text_1"><?php echo $job_req->post[$i]; ?></a></p></td>
                                                </tr>
                                                <tr class="tab_job_elem_tr_2">
                                                    <td><p class="treb_text_5">График:</p></td>
                                                    <td>
                                                        <p class="treb_text_6">
                                                            <?php
                                                            if ($job_req->schedule[$i]) {
                                                                echo '<a class="treb_text_4">' . $job_req->schedule[$i] . '</a>';
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
                                                            if ($job_req->age_min[$i]) {
                                                                echo '<a class="treb_text_4">от ' . $job_req->age_min[$i] . ' ' . plural_form($job_req->age_min[$i], 'год', 'лет') . '</a> ';
                                                            } elseif ($job_req->age_max[$i]) {
                                                                echo '<a class="treb_text_4">до ' . $job_req->age_max[$i] . ' ' . plural_form($job_req->age_max[$i], 'год', 'лет') . '</a>';
                                                            }
                                                            if ($job_req->age_max[$i] && $job_req->age_min[$i]) {
                                                                echo '<a class="treb_text_4">до ' . $job_req->age_max[$i] . ' лет' . '</a>';
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
                                                            if ($job_req->salary_min[$i]) {
                                                                echo '<a class="treb_text_4">от ' . $job_req->salary_min[$i] . ' ' . $job_req->currency_str[$i] . '/месяц</a>';
                                                            } elseif ($job_req->salary_max[$i]) {
                                                                echo '<a class="treb_text_4">до ' . $job_req->salary_max[$i] . ' ' . $job_req->currency_str[$i] . '/месяц' . '</a>';
                                                            }
                                                            if ($job_req->salary_max[$i] && $job_req->salary_min[$i]) {
                                                                echo '<a class="treb_text_4">до ' . $job_req->salary_max[$i] . ' ' . $job_req->currency_str[$i] . '/месяц' . '</a>';
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
                                                            if ($job_req->organization_name[$i]) {
                                                                echo '<a class="treb_text_4">' . $job_req->organization_name[$i] . '</a>';
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
                                                            if ($job_req->contact_phone[$i]) {
                                                                echo '<span class="treb_text_4">' . $job_req->contact_phone[$i] . '</span>';
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
                                                            if ($job_req->contact_name[$i]) {
                                                                echo '<span class="treb_text_4">' . $job_req->contact_name[$i] . '</span>';
                                                            }
                                                            ?>
                                                        </p> 
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                </div>
                            </div>

                            <div class="search_job">
                                <div class="kriteri_job">
                                    <div class="shapka_bloka">
                                        <a class="style_shapka_1" href="?PageType=3">РЕЗЮМЕ</a>
                                        <p class="style_shapka_3" title="Всего объявлений"><?php /* echo $job_req->all_search; */ ?></p>
                                    </div>
                                <div class="obveden_block">
                                    <div class="obiavlenie_job_1 noborder">
                                        <form action="./" method="GET">
                                            <table class="job_search_tab">
                                                <tr>
                                                    <td style="width: 130px;"><p class="treb_text">Зарплата:</p></td>
                                                    <td>
                                                        <p class="treb_text">
                                                            <input class="inp_j" name="SSMin" type="text" value="">
                                                            <input class="inp_j" name="SSMax" type="text" value="">
                                                            <select class="sel_job" name="SCurrency">
                                                                <option value="0">-</option>
                                                                <option value="1">руб</option>
                                                                <option value="2">$</option>
                                                                <option value="3">€</option>
                                                            </select>
                                                        </p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><p class="treb_text">Должность:</p></td>
                                                    <td><input class="inp_j_1" name="SPost" type="text" value=""></td>
                                                </tr>
                                                <tr>
                                                    <td><p class="treb_text">Возраст:</p></td>
                                                    <td><p class="treb_text">
                                                            <input class="inp_j_2" name="SAMin" type="text" value="">
                                                        <input class="inp_j_2 inp_j_2_r" name="SAMax" type="text" value=""></p></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" style="text-align: right; padding-top: 10px;">
                                                        <a class="but_j_ser" href="?PageType=3">Все резюме</a>
                                                        <input type="submit" name="ResumeSearch" class="but_j_ser" value="Искать">
                                                    </td>
                                                </tr>
                                            </table>
                                        </form>
                                    </div>
                                    <?php


                                    $pages = intval($job_req->all_search / $perPage);
                                    if ($job_req->all_search % $perPage != 0) {
                                        $pages++;
                                    }
                                    if ($pages > 1) {
                                        echo '<div class="listing">';
                                        if ($page == $pages || $page > 1) {
                                            echo '<a class="style_listing" href="./?page2=' . ($page - 1) . '&' . $new_url . '">Предыдущая</a>';
                                        }



                                        if ($pages <= 11) {
                                            for ($i = 1; $i <= $pages; $i++) {
                                                if ($i == $page) {
                                                    echo '<a class="active_listing" href="./?page2=' . $i . '&' . $new_url . '">' . $i . '</a>';
                                                } else {
                                                    echo '<a class="style_listing" href="./?page2=' . $i . '&' . $new_url . '">' . $i . '</a>';
                                                }
                                            }
                                        } else {
                                            if ($page <= 6) {
                                                for ($i = 1; $i <= $page + 2; $i++) {
                                                    if ($i == $page) {
                                                        echo '<a class="active_listing" href="./?page2=' . $i . '&' . $new_url . '">' . $i . '</a>';
                                                    } else {
                                                        echo '<a class="style_listing" href="./?page2=' . $i . '&' . $new_url . '">' . $i . '</a>';
                                                    }
                                                }
                                                echo '<span style="color: #000000; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                                                for ($i = $pages - 2; $i <= $pages; $i++) {
                                                    echo '<a class="style_listing" href="./?page2=' . $i . '&' . $new_url . '">' . $i . '</a>';
                                                }
                                            }
                                            if ($page > 6 && $page <= ($pages - 6)) {
                                                for ($i = 1; $i < 4; $i++) {
                                                    echo '<a class="style_listing" href="./?page2=' . $i . '&' . $new_url . '">' . $i . '</a>';
                                                }
                                                echo '<span style="color: #000000; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                                                for ($i = $page - 2; $i <= $page + 2; $i++) {
                                                    if ($i == $page) {
                                                        echo '<a class="active_listing" href="./?page2=' . $i . '&' . $new_url . '">' . $i . '</a>';
                                                    } else {
                                                        echo '<a class="style_listing" href="./?page2=' . $i . '&' . $new_url . '">' . $i . '</a>';
                                                    }
                                                }
                                                echo '<span style="color: #000000; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                                                for ($i = $pages - 2; $i <= $pages; $i++) {
                                                    echo '<a class="style_listing" href="./?page2=' . $i . '&' . $new_url . '">' . $i . '</a>';
                                                }
                                            }
                                            if ($page > ($pages - 6)) {
                                                for ($i = 1; $i < 4; $i++) {
                                                    echo '<a class="style_listing" href="./?page2=' . $i . '&' . $new_url . '">' . $i . '</a>';
                                                }
                                                echo '<span style="color: #000000; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                                                for ($i = $pages - 5; $i <= $pages; $i++) {
                                                    if ($i == $page) {
                                                        echo '<a class="active_listing" href="./?page2=' . $i . '&' . $new_url . '">' . $i . '</a>';
                                                    } else {
                                                        echo '<a class="style_listing" href="./?page2=' . $i . '&' . $new_url . '">' . $i . '</a>';
                                                    }
                                                }
                                            }
                                        }

                                        if ($page == 1 || $page < $pages) {
                                            echo '<a class="style_listing" href="./?page2=' . ($page + 1) . '&' . $new_url . '">Следующая</a>';
                                        }

                                        echo '</div>';
                                    }

                                    for ($i = 0; $i < count($job_search->id); $i++) {
                                        ?>
                                        <div class="obiavlenie_job_1">
                                            <?php
                                            echo '<a href="./?PageType=5&Id=' . $job_search->id[$i] . '">';
                                            if ($job_search->avatar[$i] && file_exists(str_replace('job/', 'job/1_', '../admin/' . $job_search->avatar[$i]))) {
                                                echo '<img class="img_search_job" src="../admin/' . str_replace('job/', 'job/1_', $job_search->avatar[$i]) . '" alt="">';
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
                                                                echo $job_search->date_reg[$i];
                                                                ?>
                                                            </p>
                                                        </td>
                                                        <td><p class="treb_text"><a <?php echo 'href="./?PageType=5&Id=' . $job_search->id[$i] . '"'; ?> class="treb_text_1"><?php echo $job_search->post[$i]; ?></a></p></td>
                                                    </tr>
                                                    <tr class="tab_job_elem_tr_2">
                                                        <td><p class="treb_text_5">График:</p></td>
                                                        <td>
                                                            <p class="treb_text_6">
                                                                <?php
                                                                if ($job_search->schedule[$i]) {
                                                                    echo '<a class="treb_text_4">' . $job_search->schedule[$i] . '</a>';
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
                                                                if ($job_search->age_min[$i]) {
                                                                    echo '<a class="treb_text_4">' . $job_search->age_min[$i] . ' ' . plural_form($job_search->age_min[$i], 'год', 'лет') . '</a>';
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
                                                                if ($job_search->salary_min[$i]) {
                                                                    echo '<a class="treb_text_4">от ' . $job_search->salary_min[$i] . ' ' . $job_search->currency_str[$i] . '/месяц</a>';
                                                                } elseif ($job_search->salary_max[$i]) {
                                                                    echo '<a class="treb_text_4">до ' . $job_search->salary_max[$i] . ' ' . $job_search->currency_str[$i] . '/месяц' . '</a>';
                                                                }
                                                                if ($job_search->salary_max[$i] && $job_search->salary_min[$i]) {
                                                                    echo '<a class="treb_text_4">до ' . $job_search->salary_max[$i] . ' ' . $job_search->currency_str[$i] . '/месяц' . '</a>';
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
                                                                if ($job_search->contact_phone[$i]) {
                                                                    echo '<span class="treb_text_4">' . $job_search->contact_phone[$i] . '</span>';
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
                                                                if ($job_search->contact_name[$i]) {
                                                                    echo '<span class="treb_text_4">' . $job_search->contact_name[$i] . '</span>';
                                                                }
                                                                ?>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="otst_job_gl"></div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <?php
                    if ($ShowParamID == 2) {
                        $job_req = new JobAdsSite();
                        $job_req->LoadAds($limit, $page1, array(array('k_j_type'), array(':type'), array(''), array(1)), $whereJob);
                        $job_req->LoadStat();
                        ?>
                        <div class="block_content_1">   <!-- Блок Вакансии Требуются -->
                            <div class="kriteri_job">
                                <div class="shapka_bloka">
                                    <p class="style_shapka_1">Требуются</p>
                                    <p class="style_shapka_3" title="Всего объявлений"><?php echo $job_req->all_req; ?></p>
                                </div> 
                                <div class="visible_content">
                                    <p class="add_form"><a href="../profile/?PageType=15" class="add_job_b">Подать Вакансию</a></p>
                                    <div class="obiavlenie_job noborder">
                                        <form action="./" method="GET">
                                            <table class="job_search_tab">
                                                <tr>
                                                    <td style="width: 130px;"><p class="treb_text">Зарплата:</p></td>
                                                    <td>
                                                        <p class="treb_text">
                                                            <input class="inp_j" name="VSMin" type="text" value=""> - <input class="inp_j" name="VSMax" type="text" value="">
                                                            <select class="sel_job" name="VCurrency">
                                                                <option value="0">-</option>
                                                                <option value="1">руб</option>
                                                                <option value="2">$</option>
                                                                <option value="3">€</option>
                                                            </select>
                                                        </p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><p class="treb_text">Должность:</p></td>
                                                    <td><input class="inp_j_1" name="VPost" type="text" value=""></td>
                                                </tr>
                                                <tr>
                                                    <td><p class="treb_text">Организация:</p></td>
                                                    <td><input class="inp_j_1" name="VOrg" type="text" value=""></td>
                                                </tr>
                                                <tr>
                                                    <td><p class="treb_text">График работы:</p></td>
                                                    <td>
                                                        <select class="sel_job_1" name="VSched">
                                                            <option value="0">Неважно</option>
                                                            <option value="Полный день">Полный день</option>
                                                            <option value="Частичная занятость">Частичная занятость</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" style="text-align: left; padding-top: 10px;">
                                                        <input type="submit" name="VacancySearch" class="but_j_ser" value="Искать">
                                                    </td>
                                                </tr>
                                            </table>
                                        </form>
                                    </div>
                                    <span class="visible_content_text">Показать по
                                        <a <?php if ($limit == 20) echo 'style="color: #f0938a;"'; ?> <?php echo 'href="?limit=20&PageType=' . $ShowParamID . '"'; ?>>20</a>
                                        <a <?php if ($limit == 30) echo 'style="color: #f0938a;"'; ?> <?php echo 'href="?limit=30&PageType=' . $ShowParamID . '"'; ?>>30</a>
                                        <a <?php if ($limit == 50) echo 'style="color: #f0938a;"'; ?> <?php echo 'href="?limit=50&PageType=' . $ShowParamID . '"'; ?>>50</a>
                                    </span>
                                </div>
                                <?php
                                $job_req->GenerateNavigation($page1, ' WHERE k_j_type=1 ', $ShowParamID, $limit);
                                ?>
                                <?php
                                for ($i = 0; $i < count($job_req->id); $i++) {
                                    ?>
                                    <div class="obiavlenie_job">
                                        <table class="tab_job_elem">
                                            <tr class="tab_job_elem_tr_1">
                                                <td class="tab_job_elem_td">
                                                    <p class="treb_text_5">
                                                        <?php
                                                        echo $job_req->date_reg[$i];
                                                        ?>
                                                    </p>
                                                </td>
                                                <td><p class="treb_text"><a <?php echo 'href="./?PageType=4&Id=' . $job_req->id[$i] . '"'; ?> class="treb_text_1"><?php echo $job_req->post[$i]; ?></a></p></td>
                                            </tr>
                                            <tr class="tab_job_elem_tr_2">
                                                <td><p class="treb_text_5">График:</p></td>
                                                <td>
                                                    <p class="treb_text_6">
                                                        <?php
                                                        if ($job_req->schedule[$i]) {
                                                            echo '<a class="treb_text_4">' . $job_req->schedule[$i] . '</a>';
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
                                                        if ($job_req->age_min[$i]) {
                                                            echo '<a class="treb_text_4">от ' . $job_req->age_min[$i] . ' ' . plural_form($job_req->age_min[$i], 'год', 'лет') . '</a> ';
                                                        } elseif ($job_req->age_max[$i]) {
                                                            echo '<a class="treb_text_4">до ' . $job_req->age_max[$i] . ' ' . plural_form($job_req->age_max[$i], 'год', 'лет') . '</a>';
                                                        }
                                                        if ($job_req->age_max[$i] && $job_req->age_min[$i]) {
                                                            echo '<a class="treb_text_4">до ' . $job_req->age_max[$i] . ' лет' . '</a>';
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
                                                        if ($job_req->salary_min[$i]) {
                                                            echo '<a class="treb_text_4">от ' . $job_req->salary_min[$i] . ' ' . $job_req->currency_str[$i] . '/месяц</a>';
                                                        } elseif ($job_req->salary_max[$i]) {
                                                            echo '<a class="treb_text_4">до ' . $job_req->salary_max[$i] . ' ' . $job_req->currency_str[$i] . '/месяц' . '</a>';
                                                        }
                                                        if ($job_req->salary_max[$i] && $job_req->salary_min[$i]) {
                                                            echo '<a class="treb_text_4">до ' . $job_req->salary_max[$i] . ' ' . $job_req->currency_str[$i] . '/месяц' . '</a>';
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
                                                        if ($job_req->organization_name[$i]) {
                                                            echo '<a class="treb_text_4">' . $job_req->organization_name[$i] . '</a>';
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
                                                        if ($job_req->contact_phone[$i]) {
                                                            echo '<span class="treb_text_4">' . $job_req->contact_phone[$i] . '</span>';
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
                                                        if ($job_req->contact_name[$i]) {
                                                            echo '<span class="treb_text_4">' . $job_req->contact_name[$i] . '</span>';
                                                        }
                                                        ?>
                                                    </p> 
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <?php
                                }
                                $job_req->GenerateNavigation($page1, ' WHERE k_j_type=1 ', $ShowParamID, $limit);
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                    <?php
                    if ($ShowParamID == 3) {
                        $job_search = new JobAdsSite();
                        $job_search->LoadAds($limit, $page1, array(array('k_j_type'), array(':type'), array(''), array(2)), $whereJob);
                        $job_search->LoadStat();
                        ?>
                        <div class="block_content_1">   <!--Блок Вакансии Ищу-->
                            <div class="kriteri_job">
                                <div class="shapka_bloka">
                                    <p class="style_shapka_1">Ищу</p>
                                    <p class="style_shapka_3" title="Всего объявлений">52</p>
                                </div>
                                <div class="visible_content">
                                    <p class="add_form"><a href="../profile/?PageType=15" class="add_job_b">Подать Резюме</a></p>
                                    <div class="obiavlenie_job_1 noborder">
                                        <form action="./" method="GET">
                                            <table class="job_search_tab">
                                                <tr>
                                                    <td style="width: 130px;"><p class="treb_text">Зарплата:</p></td>
                                                    <td>
                                                        <p class="treb_text">
                                                            <input class="inp_j" name="SSMin" type="text" value=""> - <input class="inp_j" name="SSMax" type="text" value="">
                                                            <select class="sel_job" name="SCurrency">
                                                                <option value="0">-</option>
                                                                <option value="1">руб</option>
                                                                <option value="2">$</option>
                                                                <option value="3">€</option>
                                                            </select>
                                                        </p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><p class="treb_text">Возраст:</p></td>
                                                    <td><p class="treb_text">
                                                            <input class="inp_j_2" name="SAMin" type="text" value=""> - <input class="inp_j_2" name="SAMax" type="text" value=""></p></td>
                                                </tr>
                                                <tr>
                                                    <td><p class="treb_text">Должность:</p></td>
                                                    <td><input class="inp_j_1" name="SPost" type="text" value=""></td>
                                                </tr>
                                                <?php
                                                $educations = new EducationTypes();
                                                ?>
                                                <tr>
                                                    <td><p class="treb_text">Образование:</p></td>
                                                    <td>
                                                        <select class="sel_job_1" name="SEducation">
                                                            <option value="0">Не указано</option>
                                                            <?php
                                                            for ($i = 0; $i < count($educations->id); $i++) {
                                                                echo '<option value="' . $educations->id[$i] . '">' . $educations->name[$i] . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><p class="treb_text">Сем. положение:</p></td>
                                                    <td>
                                                        <select class="sel_job_1" name="SMarital">
                                                            <option value="">Не указано</option>
                                                            <option value="Холост / Не замужем">Холост / Не замужем</option>
                                                            <option value="Женат / Замужем">Женат / Замужем</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><p class="treb_text">Пол:</p></td>
                                                    <td>
                                                        <p class="treb_text">
                                                            <label><input checked="checked" type="radio" name="SSex" value="0">Неважно</label>
                                                            <label><input type="radio" name="SSex" value="1">Мужской</label>
                                                            <label><input type="radio" name="SSex" value="2">Женский</label>
                                                        </p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><p class="treb_text">Фото:</p></td>
                                                    <td>
                                                        <p class="treb_text">
                                                            <label><input type="radio" checked="checked" name="SPhoto" value="0">Неважно</label>
                                                            <label><input type="radio" name="SPhoto" value="1">С фото</label>
                                                        </p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" style="text-align: left; padding-top: 10px;">
                                                        <input type="submit" name="ResumeSearch" class="but_j_ser" value="Искать">
                                                    </td>
                                                </tr>
                                            </table>
                                        </form>
                                    </div>
                                    <span class="visible_content_text">Показать по
                                        <a <?php if ($limit == 20) echo 'style="color: #f0938a;"'; ?> <?php echo 'href="?limit=20&PageType=' . $ShowParamID . '"'; ?>>20</a>
                                        <a <?php if ($limit == 30) echo 'style="color: #f0938a;"'; ?> <?php echo 'href="?limit=30&PageType=' . $ShowParamID . '"'; ?>>30</a>
                                        <a <?php if ($limit == 50) echo 'style="color: #f0938a;"'; ?> <?php echo 'href="?limit=50&PageType=' . $ShowParamID . '"'; ?>>50</a>
                                    </span>
                                </div>
                                <?php
                                $job_search->GenerateNavigation($page1, ' WHERE k_j_type=2 ', $ShowParamID, $limit);
                                ?>
                                <?php
                                for ($i = 0; $i < count($job_search->id); $i++) {
                                    ?>
                                    <div class="obiavlenie_job_1">
                                        <?php
                                        echo '<a href="./?PageType=5&Id=' . $job_search->id[$i] . '">';
                                        if ($job_search->avatar[$i] && file_exists(str_replace('job/', 'job/1_', '../admin/' . $job_search->avatar[$i]))) {
                                            echo '<img class="img_search_job" src="' . str_replace('job/', 'job/1_', '../admin/' . $job_search->avatar[$i]) . '" alt="">';
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
                                                            echo $job_search->date_reg[$i];
                                                            ?>
                                                        </p>
                                                    </td>
                                                    <td><p class="treb_text"><a <?php echo 'href="./?PageType=5&Id=' . $job_search->id[$i] . '"'; ?> class="treb_text_1"><?php echo $job_search->post[$i]; ?></a></p></td>
                                                </tr>
                                                <tr class="tab_job_elem_tr_2">
                                                    <td><p class="treb_text_5">График:</p></td>
                                                    <td>
                                                        <p class="treb_text_6">
                                                            <?php
                                                            if ($job_search->schedule[$i]) {
                                                                echo '<a class="treb_text_4">' . $job_search->schedule[$i] . '</a>';
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
                                                            if ($job_search->age_min[$i]) {
                                                                echo '<a class="treb_text_4">' . $job_search->age_min[$i] . ' ' . plural_form($job_search->age_min[$i], 'год', 'лет') . '</a>';
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
                                                            if ($job_search->salary_min[$i]) {
                                                                echo '<a class="treb_text_4">от ' . $job_search->salary_min[$i] . ' ' . $job_search->currency_str[$i] . '/месяц</a>';
                                                            } elseif ($job_search->salary_max[$i]) {
                                                                echo '<a class="treb_text_4">до ' . $job_search->salary_max[$i] . ' ' . $job_search->currency_str[$i] . '/месяц' . '</a>';
                                                            }
                                                            if ($job_search->salary_max[$i] && $job_search->salary_min[$i]) {
                                                                echo '<a class="treb_text_4">до ' . $job_search->salary_max[$i] . ' ' . $job_search->currency_str[$i] . '/месяц' . '</a>';
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
                                                            if ($job_search->contact_phone[$i]) {
                                                                echo '<span class="treb_text_4">' . $job_search->contact_phone[$i] . '</span>';
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
                                                            if ($job_search->contact_name[$i]) {
                                                                echo '<span class="treb_text_4">' . $job_search->contact_name[$i] . '</span>';
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
                                $job_search->GenerateNavigation($page1, ' WHERE k_j_type=2 ', $ShowParamID, $limit);
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                    <?php
                    if ($ShowParamID == 4) {
                        $job_req = new JobAdsSite();
                        $job_req->LoadAds($limit, $page1, array(array('k_j_type', 'k_j_id'), array(':type', ':id'), array('AND', ''), array(1, $ID)), '');
                        $job_req->AddViews($ID);
                        $job_req->views[0]++;
                        ?>
                        <div class="block_content_1"><br><br><b class="title_rabotodatel"><span><?php echo $job_req->post[0]; ?></span></b>
                            <br><br>
                            <div class="rabotodatel">
                                <table style="border-collapse: collapse;">
                                    <tr>
                                        <td colspan="2">
                                            <a onclick="window.print();" class="but_j_ser">Распечатать</a><br><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b class="text_uslovia">Условия</b></td>
                                    </tr>
                                    <?php
                                    if ($job_req->salary_min[0]) {
                                        echo '<tr><td><span class="table_l_t">Зарплата:</span></td><td><span class="table_text_job">от ' . $job_req->salary_min[0] . ' ' . $job_req->currency_str[0] . '/месяц ';
                                    } elseif ($job_req->salary_max[0]) {
                                        echo '<td><span class="table_l_t">Зарплата:</span></td><td><span>до ' . $job_req->salary_max[0] . ' ' . $job_req->currency_str[0] . '/месяц' . '</span></td></tr>';
                                    }
                                    if ($job_req->salary_max[0] && $job_req->salary_min[0]) {
                                        echo 'до ' . $job_req->salary_max[0] . ' ' . $job_req->currency_str[0] . '/месяц</span></td></tr>';
                                    } else {
                                        echo '</td></tr>';
                                    }
                                    if ($job_req->age_min[0]) {
                                        echo '<tr><td><span class="table_l_t">Возраст:</span></td><td><span class="table_text_job">от ' . $job_req->age_min[0] . ' ' . plural_form($job_req->age_min[$i], 'года', 'лет') . ' ';
                                    } elseif ($job_req->age_max[0]) {
                                        echo '<td><span class="table_l_t">Возраст:</span></td><td><span>до ' . $job_req->age_max[0] . ' ' . plural_form($job_req->age_max[$i], 'года', 'лет') . '</span></td></tr>';
                                    }
                                    if ($job_req->age_max[0] && $job_req->age_min[0]) {
                                        echo '<span>до ' . $job_req->age_max[0] . ' </span></td></tr>';
                                    } else {
                                        echo '</td></tr>';
                                    }
                                    ?>
                                    <tr>
                                        <td><span class="table_l_t">Пол:</span></td>
                                        <td>
                                            <span class="table_text_job">
                                                <?php
                                                switch ($job_req->sex[0]) {
                                                    case 0: echo 'Неважно';
                                                        break;
                                                    case 1: echo 'Мужской';
                                                        break;
                                                    case 2: echo 'Женский';
                                                        break;
                                                }
                                                ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="table_l_t">Образование:</span></td>
                                        <td>
                                            <span class="table_text_job">
                                                <?php
                                                echo $job_req->education[0] ? $job_req->education[0] : 'Не имеет значения';
                                                ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="table_l_t">Опыт работы:</span></td>
                                        <td>
                                            <span class="table_text_job">
                                                <?php
                                                echo $job_req->exp[0] ? $job_req->exp[0] : 'Не имеет значения';
                                                ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php
                                    if ($job_req->schedule[0]) {
                                        ?>
                                        <tr>
                                            <td><span class="table_l_t">График работы:</span></td>
                                            <td>
                                                <span class="table_text_job">
                                                    <?php
                                                    echo $job_req->schedule[0];
                                                    ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                                <table style="border-collapse: collapse;">
                                    <tr>
                                        <td>
                                            <br>
                                            <b class="text_uslovia">Требования</b><br>
                                            <p class="treb_uslovia_2">
                                                <?php
                                                echo $job_req->text[0];
                                                ?>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                                <table style="border-collapse: collapse;">
                                    <tr>
                                        <td colspan="2"><br><b class="text_uslovia">Контактная информация</b><br></td>
                                    </tr>
                                    <?php
                                    if ($job_req->organization_name[0]) {
                                        ?>
                                        <tr>
                                            <td><span>Организация:</span></td>
                                            <td>
                                                <span class="table_text_job">
                                                    <?php
                                                    echo $job_req->organization_name[0];
                                                    ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                    if ($job_req->contact_name[0]) {
                                        ?>
                                        <tr>
                                            <td><span class="table_l_t">Контактное лицо:</span></td>
                                            <td>
                                                <span class="table_text_job">
                                                    <?php
                                                    echo $job_req->contact_name[0];
                                                    ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                    if ($job_req->contact_phone[0]) {
                                        ?>
                                        <tr>
                                            <td><span class="table_l_t">Телефон:</span></td>
                                            <td colspan="2">
                                                <span class="table_text_job">
                                                    <?php
                                                    echo $job_req->contact_phone[0];
                                                    ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                    if ($job_req->email[0]) {
                                        ?>
                                        <tr>
                                            <td><span class="table_l_t">E-mail:</span></td>
                                            <td colspan="2">
                                                <a class="table_ser" <?php echo 'onclick="EmailShow(\'' . $job_req->post[0] . '\',\'' . $job_req->email[0] . '\');"'; ?>>Написать письмо</a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                                <br>
                                <span class="table_l_t">Объявление № <?php echo $job_req->id[0]; ?>. Просмотров: <?php echo $job_req->views[0]; ?>. Опубликовал <a class="treb_text_1"><?php echo $job_req->user_login[0] ? $job_req->user_login[0] : 'Гость'; ?></a></span>
                                <table style="border-collapse: collapse;">
                                    <tr>
                                        <td><span class="table_l_t">Опубликовано:</span></td>
                                        <td><span class="table_ser"><?php echo $job_req->date_reg[0]; ?></span></td>
                                    </tr>
                                    <tr>
                                        <td><span class="table_l_t">Истекает:</span></td>
                                        <td><span class="table_ser"><?php echo $job_req->date_end[0]; ?></span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                    <?php
                    if ($ShowParamID == 5) {
                        $job_search = new JobAdsSite();
                        $job_search->LoadAds($limit, $page1, array(array('k_j_type', 'k_j_id'), array(':type', ':id'), array('AND', ''), array(2, $ID)), '');
                        $job_search->AddViews($ID);
                        $job_search->views[0]++;
                        ?>
                        <script type="text/javascript">
            function ImageEnlarge(obj) {
                $('.im_job').attr('src', $(obj).attr('src'));
                $('#wind_poto_soiskatel').show(500);
                enableA();
            }
                        </script>
                        <div class="block_content_1">
                            <br><br>
                            <div class="soiskatel">
                                <div class="soiskatel_bl_im">
                                    <?php
                                    if ($job_search->avatar[0]) {
                                        echo '<img class="soiskatel_img" src="../admin/' . $job_search->avatar[0] . '" alt="" onclick="ImageEnlarge(this);">';
                                    }
                                    ?>
                                    <a onclick="window.print();" class="but_j_ser">Распечатать</a>
                                </div>
                                <div class="soiskatel_bl_cont">
                                    <table style="display: inline-block; vertical-align: top;">
                                        <tr>
                                            <td colspan="2">
                                                <b class="title_rabotodatel">
                                                    <span>
                                                        <?php
                                                        echo $job_search->contact_name[0];
                                                        ?>
                                                    </span>
                                                </b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b class="text_uslovia">Личные данные</b></td>
                                        </tr>
                                        <tr>
                                            <td><span class="table_l_t">Искомая должность:</span></td>
                                            <td><span class="table_text_job"><?php echo $job_search->post[0]; ?></span></td>
                                        </tr>
                                        <?php
                                        if ($job_search->salary_min[0]) {
                                            echo '<tr><td><span class="table_l_t">Зарплата:</span></td><td><span class="table_text_job">от ' . $job_search->salary_min[0] . ' ' . $job_search->currency_str[0] . '/месяц ';
                                        } elseif ($job_search->salary_max[0]) {
                                            echo '<td><span class="table_l_t">Зарплата:</span></td><td><span class="table_text_job">до ' . $job_search->salary_max[0] . ' ' . $job_search->currency_str[0] . '/месяц' . '</span></td></tr>';
                                        }
                                        if ($job_search->salary_max[0] && $job_search->salary_min[0]) {
                                            echo 'до ' . $job_search->salary_max[0] . ' ' . $job_search->currency_str[0] . '/месяц</span></td></tr>';
                                        } else {
                                            echo '</td></tr>';
                                        }
                                        if ($job_search->age_min[0]) {
                                            echo '<tr><td><span class="table_l_t">Возраст:</span></td><td><span class="table_text_job">' . $job_search->age_min[0] . ' ' . plural_form($job_search->age_min[$i], 'год', 'лет') . '</td></tr>';
                                        }
                                        ?>
                                        <tr>
                                            <td><span class="table_l_t">Пол:</span></td>
                                            <td>
                                                <span class="table_text_job">
                                                    <?php
                                                    switch ($job_search->sex[0]) {
                                                        case 0: echo 'Неважно';
                                                            break;
                                                        case 1: echo 'Мужской';
                                                            break;
                                                        case 2: echo 'Женский';
                                                            break;
                                                    }
                                                    ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php
                                        if ($job_search->marital[0]) {
                                            ?>
                                            <tr>
                                                <td><span class="table_l_t">Семейное положение:</span></td>
                                                <td>
                                                    <span class="table_text_job">
                                                        <?php
                                                        echo $job_search->marital[0];
                                                        ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                    if ($job_search->education[0]) {
                                        ?>
                                        <table>
                                            <tr>
                                                <td>
                                                    <br>
                                                    <b class="text_uslovia">Образование</b><br>
                                                    <p class="treb_uslovia_2">
                                                        <?php
                                                        if ($job_search->education_t[0] != 0) {
                                                            echo $job_search->education_t_str[0] . ' / ';
                                                        }
                                                        echo $job_search->education[0];
                                                        ?>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                    if ($job_search->exp[0]) {
                                        ?>
                                        <table>
                                            <tr>
                                                <td>
                                                    <br>
                                                    <b class="text_uslovia">Опыт работы</b><br>
                                                    <p class="treb_uslovia_2">
                                                        <?php
                                                        echo $job_search->exp[0];
                                                        ?>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                    if ($job_search->text[0]) {
                                        ?>
                                        <table>
                                            <tr>
                                                <td>
                                                    <br>
                                                    <b class="text_uslovia">О себе</b><br>
                                                    <p class="treb_uslovia_2">
                                                        <?php
                                                        echo $job_search->text[0];
                                                        ?>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                        <?php
                                    }
                                    ?>
                                    <table>
                                        <tr>
                                            <td colspan="3"><br><b class="text_uslovia">Контактная информация</b><br></td>
                                        </tr>
                                        <tr>
                                            <td><span class="table_l_t">Телефон:</span></td>
                                            <td colspan="2">
                                                <span class="table_text_job">
                                                    <?php
                                                    echo $job_search->contact_phone[0];
                                                    ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><span class="table_l_t">E-mail:</span></td>
                                            <td colspan="2">
                                                <a class="table_ser" <?php echo 'onclick="EmailShow(\'' . $job_search->post[0] . '\',\'' . $job_search->email[0] . '\');"'; ?>>Написать письмо</a>
                                            </td>
                                        </tr>
                                    </table>
                                    <br>
                                    <span class="table_l_t">Объявление № <?php echo $job_search->id[0]; ?>. Просмотров: <?php echo $job_search->views[0]; ?>. Опубликовал <a class="treb_text_1"><?php echo $job_req->user_login[0] ? $job_req->user_login[0] : 'Гость'; ?></a></span>
                                    <table>
                                        <tr>
                                            <td><span class="table_l_t">Опубликовано:</span></td>
                                            <td><span class="table_ser"><?php echo $job_search->date_reg[0]; ?></span></td>
                                        </tr>
                                        <tr>
                                            <td><span class="table_l_t">Истекает:</span></td>
                                            <td><span class="table_ser"><?php echo $job_search->date_end[0]; ?></span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php
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
                        if ($in_realty->total != 0) {
                            ?>
                            <div class="push_all_search">
                                <p class="name_push">Найдено <b>в недвижимости</b><span><?php echo $in_realty->total; ?></span></p>
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
                                <p class="name_push">Найдено <b>в сайтах</b><span><?php echo count($in_sites->id); ?></span></p>
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
                                <p class="name_push">Найдено <b>в предприятиях</b><span><?php echo count($in_catalog->id); ?></span></p>
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
                                    echo '<p class="push_all"><a href="/map/?f=' . $on_map->address_str[$i] . '">' . $on_map->address_str[$i] . '</a><img class="push_img" src="../images/photo_1.png" alt=""></p>';
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

    <?php
    require_once '../inc/footer.php';
    ?>

            <!--ВСПЛЫВАЮЩИЕ ОКНА-->

            <div id="send_email_job" class="wind">       <!--Всплывающее окно формы отправки письма-->
                <a class="close" onclick="CloseWindow('send_email_job');">X</a>
                <br>
                <p class="style_wind_3">Написать письмо</p>
                <p class="style_wind_1">Поля помеченные * обязательны для заполнения</p>
                <table>
                    <tr>
                        <td><p class="style_wind_3_1">Получатель:</p></td>
                        <td><span style="color: #5370ce" id="EmailDest"></span></td>
                    </tr>
                    <tr>
                        <td><p class="style_wind_3_1">E-mail отправителя:<span style="color: red">*</span></p></td>
                        <td><input class="job_inp" type="text" value=""></td>
                    </tr>
                    <tr>
                        <td><p class="style_wind_3_1">Тема:<span style="color: red">*</span></p></td>
                        <td><input class="job_inp" id="EmailTheme" type="text" value=""></td>
                    </tr>
                    <tr>
                        <td colspan="2"><p class="style_wind_3_1">Сообщение:<span style="color: red">*</span></p><br>
                            <textarea rows="10" cols="57" name="text"></textarea></td>
                    </tr>
                    <tr>
                        <td colspan="2"><button class="act_2" style="float: left;">Отправить письмо</button></td>
                    </tr>
                </table>
            </div>

            <div id="wind_poto_soiskatel" class="wind_job">
                <a class="close_2" onclick="CloseWindow('wind_poto_soiskatel');">X</a>
                <div class="block_listing_job">
                    <img class="im_job" src="" alt="">
                </div>
            </div>

            <div class="temno" id="temno"></div>   <!--Всплывающие окна конец-->
            <div class="prozrachno" id="prozrachno" onclick="$('.vhod_block_gl').hide(500);
                disableP();"></div>   <!--Всплывающие окна прозрачное для закрытия входа-->
        </div>
    </body>
</html>