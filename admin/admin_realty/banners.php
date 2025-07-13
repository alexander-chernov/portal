<?php
$banner = new BannersImmo();
$banner->LoadImmoBanners();
?>
<script type="text/javascript" src="js/banners.js"></script>
<div id="admin_nedvigim_2" class="block_content_1"><b><font color="blue">Банеры страницы Недвижимость</font></b><br><br>
    <div class="baners">
        <table border="1">
            <tr>
                <td colspan="4"><p class="style_1">Банеры страницы Недвижимость</p></td>
            </tr>
            <tr style="background: #7caed3;">
                <td><p class="style_5">Страница</p></td>
                <td colspan="3"><p class="style_5">Главный банер</p></td>
            </tr>
            <tr>
                <td><p class="style_2">Недвижимость</p></td>
                <td colspan="3">
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[1]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[1] . ',' . $banner->banner_end_days[1] . ');" title="Оставшееся время: ' . $banner->banner_end_days[1] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[1] . ',' . $banner->banner_end_days[1] . ');" title="Оставшееся время: ' . $banner->banner_end_days[1] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[1]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[1]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
            </tr>
            <tr style="background: #7caed3;">
                <td><p class="style_5">Раздел</p></td>
                <td><p class="style_5">Левый банер</p></td>
                <td><p class="style_5">Центральный банер</p></td>
                <td><p class="style_5">Правый банер</p></td>
            </tr>
            <tr>
                <td><p class="style_2">Последние объявления</p></td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[2]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[2] . ',' . $banner->banner_end_days[2] . ');" title="Оставшееся время: ' . $banner->banner_end_days[2] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[2] . ',' . $banner->banner_end_days[2] . ');" title="Оставшееся время: ' . $banner->banner_end_days[2] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[2]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[2]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[3]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[3] . ',' . $banner->banner_end_days[3] . ');" title="Оставшееся время: ' . $banner->banner_end_days[3] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[3] . ',' . $banner->banner_end_days[3] . ');" title="Оставшееся время: ' . $banner->banner_end_days[3] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[3]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[3]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[4]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[4] . ',' . $banner->banner_end_days[4] . ');" title="Оставшееся время: ' . $banner->banner_end_days[4] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[4] . ',' . $banner->banner_end_days[4] . ');" title="Оставшееся время: ' . $banner->banner_end_days[4] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[4]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[4]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
            </tr>
            <tr>
                <td><p class="style_2">ПРОДАЮ</p></td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[5]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[5] . ',' . $banner->banner_end_days[5] . ');" title="Оставшееся время: ' . $banner->banner_end_days[5] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[5] . ',' . $banner->banner_end_days[5] . ');" title="Оставшееся время: ' . $banner->banner_end_days[5] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[5]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[5]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[6]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[6] . ',' . $banner->banner_end_days[6] . ');" title="Оставшееся время: ' . $banner->banner_end_days[6] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[6] . ',' . $banner->banner_end_days[6] . ');" title="Оставшееся время: ' . $banner->banner_end_days[6] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[6]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[6]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[7]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[7] . ',' . $banner->banner_end_days[7] . ');" title="Оставшееся время: ' . $banner->banner_end_days[7] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[7] . ',' . $banner->banner_end_days[7] . ');" title="Оставшееся время: ' . $banner->banner_end_days[7] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[7]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[7]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
            </tr>
            <tr>
                <td><p class="style_2">Продаю: Квартиры</p></td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[8]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[8] . ',' . $banner->banner_end_days[8] . ');" title="Оставшееся время: ' . $banner->banner_end_days[8] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[8] . ',' . $banner->banner_end_days[8] . ');" title="Оставшееся время: ' . $banner->banner_end_days[8] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[8]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[8]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[9]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[9] . ',' . $banner->banner_end_days[9] . ');" title="Оставшееся время: ' . $banner->banner_end_days[9] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[9] . ',' . $banner->banner_end_days[9] . ');" title="Оставшееся время: ' . $banner->banner_end_days[9] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[9]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[9]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[10]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[10] . ',' . $banner->banner_end_days[10] . ');" title="Оставшееся время: ' . $banner->banner_end_days[10] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[10] . ',' . $banner->banner_end_days[10] . ');" title="Оставшееся время: ' . $banner->banner_end_days[10] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[10]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[10]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
            </tr>
            <tr>
                <td><p class="style_2">Продаю: Дома/дачи</p></td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[11]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[11] . ',' . $banner->banner_end_days[11] . ');" title="Оставшееся время: ' . $banner->banner_end_days[11] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[11] . ',' . $banner->banner_end_days[11] . ');" title="Оставшееся время: ' . $banner->banner_end_days[11] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[11]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[11]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[12]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[12] . ',' . $banner->banner_end_days[12] . ');" title="Оставшееся время: ' . $banner->banner_end_days[12] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[12] . ',' . $banner->banner_end_days[12] . ');" title="Оставшееся время: ' . $banner->banner_end_days[12] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[12]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[12]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[13]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[13] . ',' . $banner->banner_end_days[13] . ');" title="Оставшееся время: ' . $banner->banner_end_days[13] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[13] . ',' . $banner->banner_end_days[13] . ');" title="Оставшееся время: ' . $banner->banner_end_days[13] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[13]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[13]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
            </tr>
            <tr>
                <td><p class="style_2">Продаю: Нежилое</p></td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[14]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[14] . ',' . $banner->banner_end_days[14] . ');" title="Оставшееся время: ' . $banner->banner_end_days[14] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[14] . ',' . $banner->banner_end_days[14] . ');" title="Оставшееся время: ' . $banner->banner_end_days[14] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[14]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[14]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[15]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[15] . ',' . $banner->banner_end_days[15] . ');" title="Оставшееся время: ' . $banner->banner_end_days[15] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[15] . ',' . $banner->banner_end_days[15] . ');" title="Оставшееся время: ' . $banner->banner_end_days[15] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[15]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[15]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[16]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[16] . ',' . $banner->banner_end_days[16] . ');" title="Оставшееся время: ' . $banner->banner_end_days[16] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[16] . ',' . $banner->banner_end_days[16] . ');" title="Оставшееся время: ' . $banner->banner_end_days[16] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[16]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[16]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
            </tr>
            <tr>
                <td><p class="style_2">Продаю: Гараж/погреб</p></td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[17]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[17] . ',' . $banner->banner_end_days[17] . ');" title="Оставшееся время: ' . $banner->banner_end_days[17] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[17] . ',' . $banner->banner_end_days[17] . ');" title="Оставшееся время: ' . $banner->banner_end_days[17] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[17]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[17]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[18]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[18] . ',' . $banner->banner_end_days[18] . ');" title="Оставшееся время: ' . $banner->banner_end_days[18] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[18] . ',' . $banner->banner_end_days[18] . ');" title="Оставшееся время: ' . $banner->banner_end_days[18] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[18]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[18]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[19]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[19] . ',' . $banner->banner_end_days[19] . ');" title="Оставшееся время: ' . $banner->banner_end_days[19] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[19] . ',' . $banner->banner_end_days[19] . ');" title="Оставшееся время: ' . $banner->banner_end_days[19] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[19]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[19]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
            </tr>
            <tr>
                <td><p class="style_2">Продаю: Земля</p></td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[20]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[20] . ',' . $banner->banner_end_days[20] . ');" title="Оставшееся время: ' . $banner->banner_end_days[20] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[20] . ',' . $banner->banner_end_days[20] . ');" title="Оставшееся время: ' . $banner->banner_end_days[20] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[20]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[20]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[21]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[21] . ',' . $banner->banner_end_days[21] . ');" title="Оставшееся время: ' . $banner->banner_end_days[21] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[21] . ',' . $banner->banner_end_days[21] . ');" title="Оставшееся время: ' . $banner->banner_end_days[21] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[21]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[21]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[22]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[22] . ',' . $banner->banner_end_days[22] . ');" title="Оставшееся время: ' . $banner->banner_end_days[22] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[22] . ',' . $banner->banner_end_days[22] . ');" title="Оставшееся время: ' . $banner->banner_end_days[22] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[22]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[22]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
            </tr>
            <tr>
                <td><p class="style_2">СДАЮ</p></td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[23]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[23] . ',' . $banner->banner_end_days[23] . ');" title="Оставшееся время: ' . $banner->banner_end_days[23] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[23] . ',' . $banner->banner_end_days[23] . ');" title="Оставшееся время: ' . $banner->banner_end_days[23] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[23]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[23]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[24]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[24] . ',' . $banner->banner_end_days[24] . ');" title="Оставшееся время: ' . $banner->banner_end_days[24] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[24] . ',' . $banner->banner_end_days[24] . ');" title="Оставшееся время: ' . $banner->banner_end_days[24] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[24]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[24]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[25]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[25] . ',' . $banner->banner_end_days[25] . ');" title="Оставшееся время: ' . $banner->banner_end_days[25] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[25] . ',' . $banner->banner_end_days[25] . ');" title="Оставшееся время: ' . $banner->banner_end_days[25] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[25]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[25]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
            </tr>
            <tr>
                <td><p class="style_2">Сдаю: Квартиры</p></td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[26]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[26] . ',' . $banner->banner_end_days[26] . ');" title="Оставшееся время: ' . $banner->banner_end_days[26] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[26] . ',' . $banner->banner_end_days[26] . ');" title="Оставшееся время: ' . $banner->banner_end_days[26] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[26]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[26]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[27]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[27] . ',' . $banner->banner_end_days[27] . ');" title="Оставшееся время: ' . $banner->banner_end_days[27] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[27] . ',' . $banner->banner_end_days[27] . ');" title="Оставшееся время: ' . $banner->banner_end_days[27] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[27]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[27]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[28]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[28] . ',' . $banner->banner_end_days[28] . ');" title="Оставшееся время: ' . $banner->banner_end_days[28] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[28] . ',' . $banner->banner_end_days[28] . ');" title="Оставшееся время: ' . $banner->banner_end_days[28] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[28]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[28]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
            </tr>
            <tr>
                <td><p class="style_2">Сдаю: Дома/дачи</p></td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[29]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[29] . ',' . $banner->banner_end_days[29] . ');" title="Оставшееся время: ' . $banner->banner_end_days[29] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[29] . ',' . $banner->banner_end_days[29] . ');" title="Оставшееся время: ' . $banner->banner_end_days[29] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[29]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[29]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[30]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[30] . ',' . $banner->banner_end_days[30] . ');" title="Оставшееся время: ' . $banner->banner_end_days[30] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[30] . ',' . $banner->banner_end_days[30] . ');" title="Оставшееся время: ' . $banner->banner_end_days[30] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[30]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[30]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[31]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[31] . ',' . $banner->banner_end_days[31] . ');" title="Оставшееся время: ' . $banner->banner_end_days[31] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[31] . ',' . $banner->banner_end_days[31] . ');" title="Оставшееся время: ' . $banner->banner_end_days[31] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[31]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[31]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
            </tr>
            <tr>
                <td><p class="style_2">Сдаю: Нежилое</p></td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[32]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[32] . ',' . $banner->banner_end_days[32] . ');" title="Оставшееся время: ' . $banner->banner_end_days[32] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[32] . ',' . $banner->banner_end_days[32] . ');" title="Оставшееся время: ' . $banner->banner_end_days[32] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[32]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[32]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[33]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[33] . ',' . $banner->banner_end_days[33] . ');" title="Оставшееся время: ' . $banner->banner_end_days[33] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[33] . ',' . $banner->banner_end_days[33] . ');" title="Оставшееся время: ' . $banner->banner_end_days[33] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[33]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[33]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[34]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[34] . ',' . $banner->banner_end_days[34] . ');" title="Оставшееся время: ' . $banner->banner_end_days[34] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[34] . ',' . $banner->banner_end_days[34] . ');" title="Оставшееся время: ' . $banner->banner_end_days[34] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[34]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[34]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
            </tr>
            <tr>
                <td><p class="style_2">Сдаю: Гараж/погреб</p></td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[35]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[35] . ',' . $banner->banner_end_days[35] . ');" title="Оставшееся время: ' . $banner->banner_end_days[35] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[35] . ',' . $banner->banner_end_days[35] . ');" title="Оставшееся время: ' . $banner->banner_end_days[35] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[35]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[35]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[36]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[36] . ',' . $banner->banner_end_days[36] . ');" title="Оставшееся время: ' . $banner->banner_end_days[36] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[36] . ',' . $banner->banner_end_days[36] . ');" title="Оставшееся время: ' . $banner->banner_end_days[36] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[36]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[36]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
                <td>
                    <a class="a_1" href="#" onclick="ShowBannerInfo(<?php echo $banner->banner_immo_id[37]; ?>);" title="Информация">
                        <img src="../images/info.png" alt="">
                    </a>
                    <?php
                    if ($banner->banner_end_days[1] < 5) {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[37] . ',' . $banner->banner_end_days[37] . ');" title="Оставшееся время: ' . $banner->banner_end_days[37] . ' дней">
                            <img src="../images/clock_red_1.png" alt="">
                            </a>';
                    } else {
                        echo '<a class="a_1" href="#" onclick="ChangeTimeToEnd(' . $banner->banner_immo_id[37] . ',' . $banner->banner_end_days[37] . ');" title="Оставшееся время: ' . $banner->banner_end_days[37] . ' дней">
                            <img src="../images/clock_green_1.png" alt="">
                            </a>';
                    }
                    ?>
                    <a class="a_1" href="#" onclick="BannerCodeEdit(<?php echo $banner->banner_immo_id[37]; ?>);">
                        <img src="../images/edit.png" title="Редактировать" alt="">
                    </a>
                    <a class="a_1" href="#" onclick="ViewBanner(<?php echo $banner->banner_immo_id[37]; ?>);" title="Просмотр">
                        <img src="../images/photo_baner.png" alt="">
                    </a>
                </td>
            </tr>
        </table>
    </div>
</div>

<div id="info_baner_block" class="wind">       <!--Всплывающее окно редактирования Информации о владельце банера-->
    <a class="close" href="#" onclick="CloseWindow('info_baner_block');">X</a>
    <br>
    <br>
    <p class="style_7">Редактируем Владельца банера:</p>
    <table id="BannerInfoTable">
    </table>
</div>

<div id="time_baner_block" class="wind">       <!--Всплывающее окно редактирования Времени банера-->
    <a class="close" href="#" onclick="CloseWindow('time_baner_block');">X</a>
    <br>
    <br>
    <p id="BannerAddDaysLast" class="style_7">Период действия банера:</p><br>
    <table>
        <tr>
            <td><p class="style_2">Продлить на:</p></td>
            <td>
                <input id="BannerAddDays" type="text" value="">дней
            </td>
            <td>
                <input type="hidden" id="BannerAddDaysID" value="">
                <button onclick="AddDays();" style="float:left;">Продлить</button>
            </td>
        </tr>
    </table>
</div>

<div id="wind1" class="wind">       <!--Всплывающее окна редактирования банера-->
    <a class="close" href="#" onclick="CloseWindow('wind1');">X</a>
    <br>
    <br>
    <p class="style_4">Вставьте код банера:</p>
    <textarea rows="10" cols="50" id="BannerCodeEdit"></textarea><br>
    <button name="0" style="float:right;" onclick="BannerCodeEditSubmit();">Сохранить</button>
</div>

<div id="wind2" class="wind">       <!--Всплывающее окно просмотра банера-->
    <a class="close" href="#" onclick="CloseWindow('wind2');">X</a>
    <br>
    <br>
    <p class="style_7">Наименование банера</p>
    <div id="ViewBannerID"></div>
</div>