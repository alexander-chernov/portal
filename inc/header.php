<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Alexander A. Chernov
 * Date: 28.09.13
 * Time: 13:34
 * To change this template use File | Settings | File Templates.
 */
?>
<div class="gl_block_gl" xmlns="http://www.w3.org/1999/html">
            <div class="top_block">                     <!--Шапка портала начало-->
                <div class="info_reg"> <!--Блок ЗАРЕГИСТРИРОВАННОГО ПОЛЬЗОВАТЕЛЯ-->
                    <?php
                    if (isset($_SESSION['login'])) {
                        UpdateActivityUser();
                        ?>
                        <a class="inf_text_1" title="Новые сообщения" href="/profile/?PageType=20"><?php echo NewMessages(); ?></a>
                        <a class="inf_text_3" href="/profile/"><?php echo $_SESSION['login']; ?></a>
                        <a class="inf_text_2 inf_text_2_dop" href="/exit.php">Выход</a>
                        <?php
                    }
                    ?>
                    <?php
                    if (!isset($_SESSION['login'])) {
                        ?>
                        <div class="vhod_block_gl" id="vhod_block_gl">
                            <svg class="strelka">
                            <polygon points="0,15 15,0 30,15" fill="rgb(255,255,255)" stroke="rgba(0,0,0,0.1)"/>
                            </svg>
                            <form method="post" action="/testreg.php">
                                <div>
                                    <input name="login_name" id="login_name" class="input_vhod_gl" type="text" placeholder="ЛОГИН" value="">
                                    <input name="pass_value" id="pass_value" class="input_vhod_gl" type="password" placeholder="ПАРОЛЬ" value="">
                                    <a href="/singin.php" class="forgot">Напомнить пароль</a>
                                    <input type="submit" class="act" onclick="return FormSubmitCheck(); return false;" value="Войти">
                                    <label class="rememb"><input type="checkbox" name="save" value="1" class="rem_check">Запомнить меня</label>

                                </div>
                            </form>
                        </div>
                        <a class="inf_text_2" href="/registration.php">Регистрация</a>
                        <a id="signin" class="inf_text_2 inf_text_2_dop" onclick="enableP();ShowLogin();">Вход</a>
                        <?php
                    }
                    ?>
                </div>
                <div class="left_shapka">
                    <div class="vizitka">
                        <!--<a href="/"><img src="/images/s.gif" alt="На главную" width="96" height="85" class="img_border"></a>-->
                        <a href="/"><img src="/images/nt.png" alt="На главную" width="280" height="81"></a>
                        <span class="locality"><a class="inf_text_4" onclick="$('.locality_block').toggle(500)" href="#">ТОМСК
                                <svg class="locality_strelka">
                                <polygon points="0,0 12,0 6,6" fill="rgb(256,0,31)" stroke="rgba(0,0,0,0.2)"/>
                                </svg>
                            </a></span>
                        <div class="locality_block">
                            <a class="inf_text_5" onclick="$('.locality_block').hide(500)"  href="#">ТОМСК</a>
                        </div>
                    </div>

                    <div class="block_baner">
                        <?php
                        if (preg_match("/map/i",$_SERVER['REQUEST_URI'])){
                        ?>
                            <form autocomplete="off" id='searchForm' action="/map/">
                                <div class="search_panel">
                                    <span class="search_text">Введите, что хотите найти, например:
                                    <i>мостовая автосан</i></span>
                                    <input type="text" value="Поиск" onfocus="changeValue()" id="searchLine" name="search" class="text_inp_ser map" autocomplete="off">
                                    <input type="hidden" name="changeCount" value="0" id="changeVar">
                                </div>
                            </form>
                        <?php
                        } else {
                        ?>
                        <form action="/catalog/" method="GET">
                            <div class="search_panel">
                                <span class="search_text">Введите, что хотите найти, например:
                                <i>гостинка недорого</i></span>
                                <input class="text_inp_ser" type="text" name="search_string" value="<?=$_GET['search_string']?>" onfocus="changeValue()" id="searchLine" autocomplete="off">
                                <input class="but_img_ser" type="submit" value="">
                            </div>
                        </form>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            <?php /*
                <div class="right_shapka">
                    <div class="info_block">
                        <div class="info_pogoda">
                            <?php
                            if (empty($_SESSION['weather'])){
                                //$weather = json_decode(file_get_contents('http://pogodavtomske.ru/informer/tomskru.json'), true);
                                $_SESSION['weather'] = $weather;
                            } else {
                                $weather = $_SESSION['weather'];
                            }
                            if ($weather) {
                                $currWeather=array(
                                    1 => array('img' => 'd.sun.png', 'desc' => 'ясно')
                                    ,2 => array('img' => 'd.sun.c1.png', 'desc' => 'малооблачно, без осадков')
                                    ,3 => array('img' => 'd.sun.c2.png', 'desc' => 'переменная облачность, без осадков')
                                    ,4 => array('img' => 'd.sun.c2.r2.png', 'desc' => 'переменная облачность, возможен дождь')
                                    ,5 => array('img' => 'd.sun.c2.r1.s1.png', 'desc' => 'переменная облачность, возможен снег с дождем')
                                    ,6 => array('img' => 'd.sun.c2.r1.h1.png', 'desc' => 'переменная облачность, возможен град с дождем')
                                    ,7 => array('img' => 'd.sun.c2.st.png', 'desc' => 'переменная облачность, возможна гроза')
                                    ,8 => array('img' => 'd.sun.c4.png', 'desc' => 'пасмурно, без осадков')
                                    ,9 => array('img' => 'd.sun.c4.r2.png', 'desc' => 'пасмурно, возможен дождь')
                                    ,10 => array('img' => 'd.sun.c4.r1.s1.png', 'desc' => 'пасмурно, возможен снег с дождем')
                                    ,11 => array('img' => 'd.sun.c4.r1.h1.png', 'desc' => 'пасмурно, возможен град с дождем')
                                    ,12 => array('img' => 'd.sun.c4.r3.png', 'desc' => 'пасмурно, дождь')
                                    ,13 => array('img' => 'd.sun.c4.r2.h2.png', 'desc' => 'пасмурно, град с дождем')
                                    ,14 => array('img' => 'd.sun.c4.r2.s2.png', 'desc' => 'пасмурно, снег с дождем')
                                    ,15 => array('img' => 'd.sun.c4.st.png', 'desc' => 'пасмурно, гроза')
                                    ,16 => array('img' => 'd.sun.c4.s3.png', 'desc' => 'пасмурно, снег')
                                    ,17 => array('img' => 'd.sun.c2.s3.png', 'desc' => 'переменная облачность, снег')
                                    ,18 => array('img' => 'd.sun.c2.s2.png', 'desc' => 'переменная облачность, возможен снег')
                                    ,19 => array('img' => 'd.sun.c4.s3.png', 'desc' => 'пасмурно, возможен снег')
                                );
                                $forecastWeather=array(
                                    1 => $currWeather[12]
                                    ,2 => $currWeather[16]
                                    ,3 => $currWeather[13]
                                    ,4 => $currWeather[10]
                                    ,5 => $currWeather[8]
                                    ,6 => $currWeather[3]
                                    ,7 => $currWeather[1]
                                    ,8 => $currWeather[7]
                                    ,9 => $currWeather[4]
                                    ,10 => $currWeather[4]
                                    ,11 => $currWeather[9]
                                    ,12 => $currWeather[18]
                                    ,13 => $currWeather[19]
                                    ,14 => $currWeather[5]
                                    ,15 => $currWeather[14]
                                    ,16 => $currWeather[8]
                                    ,17 => $currWeather[16]
                                );
                                foreach ($weather as $key => $value) {
                                    if ($key) {
                                        $arrPnt='forecastWeather';
                                    } else {
                                        $arrPnt='currWeather';
                                    }
                                    if (isset(${$arrPnt}[$value['weather']])) {
                                        $idx[$key]=${$arrPnt}[$value['weather']];
                                        //$idx[$key]=${$arrPnt}[5];
                                    } else {
                                        $idx[$key]=array('img' => 'd.sun.c3.png', 'desc' => 'пасмурно');
                                    }
                                }
                                $today_day = date ('N');
                                switch ($today_day) {
                                    case 1:
                                        $day1_txt = 'пн';
                                        $day2_txt = 'вт';
                                        break;
                                    case 2:
                                        $day1_txt = 'вт';
                                        $day2_txt = 'ср';
                                        break;
                                    case 3:
                                        $day1_txt = 'ср';
                                        $day2_txt = 'чт';
                                        break;
                                    case 4:
                                        $day1_txt = 'чт';
                                        $day2_txt = 'пт';
                                        break;
                                    case 5:
                                        $day1_txt = 'пт';
                                        $day2_txt = 'сб';
                                        break;
                                    case 6:
                                        $day1_txt = 'сб';
                                        $day2_txt = 'вс';
                                        break;
                                    case 7:
                                        $day1_txt = 'вс';
                                        $day2_txt = 'пн';
                                        break;
                                    default:
                                        $day1_txt = '';
                                        $day2_txt = '';
                                }

                                $out='
                                    <div class="temperature">
                                        <div class="temp-current"><img class="img_weather" src="/images/weather/icons/'.$idx[0]['img'].'" width="36" height="36" alt="'.$idx[0]['desc'].'" title="'.$idx[0]['desc'].'" /><span class="desc">сейчас '.$day1_txt.'</span><br /><span class="temp">'.($weather[0]['temperature']>0?'+':'').$weather[0]['temperature'].' &deg;C</span></div>
                                        <div class="temp-current"><img class="img_weather" src="/images/weather/icons/'.$idx[1]['img'].'" width="36" height="36" alt="'.$idx[1]['desc'].'" title="'.$idx[1]['desc'].'" /><span class="desc">завтра '.$day2_txt.'</span><br /><span class="temp">'.($weather[1]['temperature']>0?'+':'').$weather[1]['temperature'].'&deg;C</span></div>
                                    </div>';
                                echo $out;
                            }

                            ?>
                        </div>
                        <?php

                        $dollar = 'R01235';
                        $today = date('d/m/Y');
                        $yestoday = date('d/m/Y',mktime(0,0,0,date("m"),date("d")-1,date("Y")));
                        $yestoday2 = date('d/m/Y',mktime(0,0,0,date("m"),date("d")-2,date("Y")));
                        $yestoday3 = date('d/m/Y',mktime(0,0,0,date("m"),date("d")-3,date("Y")));
                        if (empty($_SESSION['dollar_money']) || empty($money['yestoday']) || empty($money['today'])){
                            $money = cbr($dollar,$yestoday,$today);
                            $_SESSION['dollar_money'] = $money;
                        } else {
                            $money = $_SESSION['dollar_money'];
                        }
                        $dollar_yestoday = $money['yestoday'];
                        $dollar_today = $money['today'];
                        if (empty($dollar_today) || empty($dollar_yestoday)) {
                            $money = cbr($dollar,$yestoday2,$yestoday);
                            $_SESSION['dollar_money'] = $money;
                            $dollar_yestoday = $money['yestoday'];
                            $dollar_today = $money['today'];
                        }
                        if (empty($dollar_today) || empty($dollar_yestoday)) {
                            $money = cbr($dollar,$yestoday3,$yestoday2);
                            $_SESSION['dollar_money'] = $money;
                            $dollar_yestoday = $money['yestoday'];
                            $dollar_today = $money['today'];
                        }
                        $euro = 'R01239';
                        if (empty($_SESSION['dollar_money'])){
                            $money = cbr($euro,$yestoday,$today);
                            $_SESSION['euro_money'] = $money;
                        } else {
                            $money = $_SESSION['euro_money'];
                        }
                        $euro_yestoday = $money['yestoday'];
                        $euro_today = $money['today'];
                        if (empty($euro_yestoday) || empty($euro_today)) {
                            $money = cbr($euro,$yestoday2,$yestoday);
                            $_SESSION['euro_money'] = $money;
                            $euro_yestoday = $money['yestoday'];
                            $euro_today = $money['today'];
                        }
                        if (empty($euro_yestoday) || empty($euro_today)) {
                            $money = cbr($euro,$yestoday3,$yestoday2);
                            $_SESSION['euro_money'] = $money;
                            $euro_yestoday = $money['yestoday'];
                            $euro_today = $money['today'];
                        }
                        $raznicaDollar = $dollar_today - $dollar_yestoday;
                        $raznicaEuro = $euro_today - $euro_yestoday;

                        $result_url = str_replace('?','',str_replace($_SERVER['QUERY_STRING'],'',$_SERVER['REQUEST_URI']));

                        ?>
                        <div class="info_valuta">
                            <!--<img style="width: 100%;" src="images/valuta.jpg" alt="">-->
                            <div class="currency">
                                <div class="item"><strong>$</strong><?=number_format($dollar_today,4,'.','')?><sup class="<?=($raznicaDollar>0?'green':'red')?>"><?=($raznicaDollar>0?'+':'')?><?=number_format($raznicaDollar,4,'.','')?></sup></div>
                                <div class="item"><strong>€</strong><?=number_format($euro_today,4,'.','')?><sup class="<?=($raznicaEuro>0?'green':'red')?>"><?=($raznicaEuro>0?'+':'')?><?=number_format($raznicaEuro,4,'.','')?></sup></div>
                            </div>
                        </div>
                    </div>
                </div>
                */ ?>
            </div>
            <div class="clear"></div>
            <div class="gl_menu" id="show_menu">                   <!--Меню начало-->
                <a class="<?=($result_url=='/'?'gl_menu_active':'gl_menu_a')?>" href="/">Главная</a>
                <a class="<?=($result_url=='/realty/'?'gl_menu_active':'gl_menu_a')?>" href="/realty/">Недвижимость</a>
                <a class="<?=($result_url=='/photoboard/'?'gl_menu_active':'gl_menu_a')?>" href="/photoboard/">Объявления</a>
                <a class="<?=($result_url=='/job/'?'gl_menu_active':'gl_menu_a')?>" href="/job/">Работа</a>
                <a class="<?=($result_url=='/catalog/'?'gl_menu_active':'gl_menu_a')?>" href="/catalog/">Каталог</a>

                <div id="show_menu_1">
                    <a class="<?=($result_url=='/blog/'?'gl_menu_active':'gl_menu_a')?>" href="/blog/">Достопримечательности</a>
                    <!--<a class="<?=($result_url=='/expert/'?'gl_menu_active':'gl_menu_a')?>" href="/expert/">Эксперты</a>
                    <a class="<?=($result_url=='/webcam/'?'gl_menu_active':'gl_menu_a')?>" href="/webcam/">Веб-камеры</a>
                    <a class="<?=($result_url=='/sites/'?'gl_menu_active':'gl_menu_a')?>" href="/sites/">Сайты</a>-->
                </div>
                <a class="<?=($result_url=='/map/'?'gl_menu_active':'gl_menu_a')?>" href="/map/">Карта</a>

                <div id="show_menu_2" onmouseover="$('.elem_open_menu').show();" onmouseout="$('.elem_open_menu').hide();">
                    <a class="gl_menu_a" onmouseover="$('.conteiner_img_menu').show();" onmouseout="$('.conteiner_img_menu').hide();">Еще</a>
                    <div class="conteiner_img_menu">
                        <svg class="menu_strelka">
                            <!--<polygon points="0,15 15,0 30,15" fill="rgb(204,204,204)" stroke="rgba(204,204,204,0.2)"/>-->
                            <polygon points="0,0 30,0 15,15" fill="rgb(34,34,34)" stroke="rgba(34,34,34,0.2)"/>
                        </svg>
                    </div>
                    <div class="elem_open_menu">
                        <!--<a class="el_op_men_1" href="/expert/" >Эксперты</a>-->
                        <a class="el_op_men_1" href="/blog/" >Достопримечательности</a>
                        <!--<a class="el_op_men_1" href="/webcam/" >Веб-камеры</a>
                        <a class="el_op_men_1" href="/sites/" >Сайты</a>-->
                    </div>

                </div>
<?php

if (preg_match('/map/i',$_SERVER['REQUEST_URI'],$matches)) {
    $hide = ' style="display:none" ';
}
?>
                <div id="show_menu_3" onmouseover="$('.elem_open_menu_1').show();" onmouseout="$('.elem_open_menu_1').hide();" <?=$hide?>>
                    <?php
                    $menu = true;
                    if ($result_url=='/realty/') {
                        $lint_text = 'В НЕДВИЖИМОСТЬ';
                        $link = '/profile/?PageType=4';
                        $style = 'gl_but_men gl_but_men2';
                        //$menu = false;
                    } elseif ($result_url=='/photoboard/') {
                        $lint_text =  'В ФОТООБЪЯВЛЕНИЯ';
                        $link = '/profile/?PageType=5';
                        $style = 'gl_but_men gl_but_men2';
                        //$menu = false;
                    } elseif ($result_url=='/expert/') {
                        $lint_text =  'СТАТЬ ЭКСПЕРТОМ';
                        $style = 'gl_but_men gl_but_men2';
                        $link = '/expert/?Howto=1';
                        //$menu = false;
                    } elseif ($result_url=='/blog/') {
                        $lint_text =  'НАПИСАТЬ СТАТЬЮ';
                        $style = 'gl_but_men gl_but_men2';
                        $link = '/blog/?Howto=1';
                        //$menu = false;
                    } elseif ($result_url=='/sites/') {
                        $lint_text =  'САЙТ';
                        $style = 'gl_but_men';
                        $link = '/profile/?PageType=2';
                        //$menu = false;
                    } elseif ($result_url=='/job/') {
                        $lint_text =  'В РАБОТУ';
                        $style = 'gl_but_men gl_but_men2';
                        $link = '/profile/?PageType=6';
                        //$menu = false;
                    } elseif ($result_url=='/catalog/') {
                        $lint_text =  'В КАТАЛОГ';
                        $style = 'gl_but_men';
                        $link = '/catalog/?Howto=1';
                        //$menu = false;
                    } else {
                        $lint_text =  'Добавить';
                        $style = 'gl_but_men';
                        $link = '#';
                    }
                    ?>
                    <a class="<?=$style?>" href="<?=$link?>"><?=$lint_text?></a>
                    <div class="conteiner_img_menu2">
                        <svg class="menu_strelka">
                            <polygon points="0,0 30,0 15,15" fill="rgb(256,0,31)" stroke="rgba(256,0,31,0.2)"/>
                        </svg>
                    </div>

                    <?php
                    if ($menu) {
                    ?>

                    <div class="elem_open_menu_1">
                        <?php if ($result_url!='/realty/') { ?><a class="el_op_men_1" href="/profile/?PageType=4">В недвижимость</a> <?php } ?>
                        <?php if ($result_url!='/photoboard/') { ?><a class="el_op_men_1" href="/profile/?PageType=5">В фотообъявления</a> <?php } ?>
                        <!--<?php if ($result_url!='/expert/') { ?><a class="el_op_men_1" href="/expert/?Howto=1">Стать Экспертом</a> <?php } ?>
                        <?php if ($result_url!='/blog/') { ?><a class="el_op_men_1" href="/blog/?Howto=1">Написать статью</a> <?php } ?>-->
                        <?php if ($result_url!='/job/') { ?><a class="el_op_men_1" href="/profile/?PageType=6">В работу</a> <?php } ?>
                        <?php if ($result_url!='/catalog/') { ?><a class="el_op_men_2" href="/catalog/?Howto=1">Добавить в каталог</a> <?php } ?>
                    </div>

                    <?php
                    }
                    ?>
                </div>
            </div>                            <!--Меню конец-->
            <div class="clear"></div>
            <div class="reklama">
                <div class="reklama_1">
                    <?php
                    if ($banners->banner_end_days[1] > 0) {
                        echo str_replace('../images/banners/', '/admin/images/banners/', $banners->banner_code[1]);
                    } else {
                        echo '<img src="/images/s.gif" id="banner1" height="70">';
                    }
                    ?>
                </div>
                <div class="reklama_2">
                    <?php
                    if ($banners->banner_end_days[2] > 0) {
                        echo str_replace('../images/banners/', '/admin/images/banners/', $banners->banner_code[2]);
                    } else {
                        echo '<img src="/images/s.gif" id="banner2" height="70">';
                    }
                    ?>
                </div>
            </div>
            <div class="clear"></div>
