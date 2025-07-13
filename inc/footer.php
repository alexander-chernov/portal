<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alexander A. Chernov
 * Date: 28.09.13
 * Time: 13:34
 * To change this template use File | Settings | File Templates.
 */
if ($_SERVER['PHP_SELF']!='/index.php'){
?>
<div class="clear"></div>
<div class="reklama">
    <div class="reklama_1">
        <?php
        if ($banners->banner_end_days[4] > 0) {
            echo str_replace('../images/banners/', '/admin/images/banners/', $banners->banner_code[4]);
        } else {
            echo '<img src="/images/s.gif" id="banner3" height="70">';
        }
        ?>
    </div>
    <div class="reklama_2">
        <?php
        if ($banners->banner_end_days[5] > 0) {
            echo str_replace('../images/banners/', '/admin/images/banners/', $banners->banner_code[5]);
        } else {
            echo '<img src="/images/s.gif" id="banner4" height="70">';
        }
        ?>
    </div>
</div>
<?php
}
?>
<div class="futter">
    <div class="futter_menu">
        <h1 onclick="location.href='/realty/'">Недвижимость</h1>
        <a href="/profile/?PageType=4">Купить</a>
        <a href="/profile/?PageType=4">Продать</a>
        <a href="/profile/?PageType=4">Обменять</a>
        <a href="/profile/?PageType=4">Снять</a>
        <a href="/profile/?PageType=4">Сдать</a>
    </div>
    <div class="futter_menu">
        <h1 onclick="location.href='/photoboard/'">Объявления</h1>
        <a href="/profile/?PageType=5">Купить</a>
        <a href="/profile/?PageType=5">Продать</a>
    </div>
    <div class="futter_menu">
        <h1 onclick="location.href('/job/'">Работа</h1>
        <a href="/job/">Вакансии</a>
        <a href="/job/">Резюме</a>
    </div>
    <div class="futter_menu">
        <h1 onclick="location.href='/catalog/'">Каталог</h1>
        <a href="/catalog/">Организации</a>
        <!--<a>Сайты</a>-->
    </div>
    <!--<div class="futter_menu">
        <h1 onclick="location.href='/expert/'">Вопросы экспертам</h1>
        <a href="/expert/?Howto=1">Стать</a>
    </div>-->
    <div class="futter_menu">
        <h1 onclick="location.href='/blog/'">Достопримечательности</h1>
        <!--<a href="/blog/?Howto=1">Написать</a>-->
    </div>
    <div class="futter_menu">
        <h1 onclick="location.href='/map/'">Карта</h1>
    </div>
</div>
<div class="clear"></div>
<div class="futter_black_line"></div>
<div class="clear"></div>
<div class="futter_dark_gray_line">
    <span class="copyright">
        Все права защищены 2014 &laquo;Г.А.Р.&raquo;
    </span>
    <span class="mini_footer">
        <a href="/other.php?user">Пользовательское соглашение</a> |
        <a href="/other.php?ad">Отдел рекламы</a> |
        <a href="/other.php?support">Служба поддержки</a> |
        <a href="/other.php?contacts">Контактные данные</a>
    </span>
    <span class="adv">
        <a href="/other.php?ad">Реклама на сайте</a>
    </span>
</div>