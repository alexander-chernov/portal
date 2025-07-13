<?php
$page = 1;
if (isset($_GET['PageIndex'])) {
    $page = $_GET['PageIndex'];
}

//Выбор страницы для отображения
if (!isset($_SESSION['WhereBuy'])) {
    $_SESSION['WhereBuy'] = '';
}
$buy = new Buys();
$buy->LoadBuys(50, $page, $_SESSION['WhereBuy']);
?>
<div id="admin_nedvigim_5" class="block_content_1"><b><font color="blue">Объявления раздела Куплю</font></b><br><br>
    <table style="width: 100%;">
        <tr>
            <td>
                <img class="img_options" src="../images/delete_team.png" onclick="DeleteSelectedBuys(<?php echo count($buy->buy_id); ?>);" title="Удалить выделенные объявления" alt="">
            </td>
            <td style="text-align: right;">
                <!--ПОИСК-->
                <img id="lupa_plus_kupliu" class="img_options_1" src="../images/lupa_plus.png" title="Развернуть поиск объявлений Куплю" alt=""
                     onclick="document.getElementById('lupa_plus_kupliu').style.display='none';
                         document.getElementById('lupa_minus_kupliu').style.display='block';
                         document.getElementById('parametr_search_kupliu').style.display='block';">
                <img  id="lupa_minus_kupliu" class="img_options_1" style="display: none;" src="../images/lupa_minus.png" title="Свернуть поиск объявлений Куплю" alt=""
                      onclick="document.getElementById('lupa_plus_kupliu').style.display='block';
                          document.getElementById('lupa_minus_kupliu').style.display='none';
                          document.getElementById('parametr_search_kupliu').style.display='none';">
            </td>
        </tr>
    </table>
    <div id="parametr_search_kupliu" style="display: none;">
        <form method="GET" action="index.php">
            <table>
                <tr>
                    <td><p class="style_2">По <font color="green">№ объявления</font>:</p></td>
                    <td><input type="text" name="SearchBuysID" value=""></td>
                </tr>
                <tr>
                    <td><input type="submit" name="SearchBuysSubmit" style="float:left; width: 100%;" value="Найти"></td>
                    <td><input type="submit" name="SearchBuysReset" style="float:left; color: red; width: 100%;" value="Очистить поиск"></td>
                </tr>
            </table>
        </form>
    </div>
    <?php
    $buy->GenerateNavigation($page);
    ?>
    <table style="width: 100%; text-align: center;">
        <tr style="background: #7caed3;">
            <td colspan="2"><p class="style_5">№ Новости</p></td>
            <td><p class="style_5">Пользователь</p></td>
            <td><p class="style_5">Текст</p></td>
            <td><p class="style_5">Дата</p></td>
            <td><p class="style_5">Действие</p></td>
        </tr>
        <?php
        $buy->GenerateTable();
        ?>
    </table>
    <?php
    $buy->GenerateNavigation($page);
    ?>
</div>

<div id="edit_kupliu" class="wind">       <!--Всплывающее окно редактировать объявление раздела куплю-->
    <a class="close" href="#" onclick="CloseWindow('edit_kupliu');">X</a>
    <br>
    <br>
    <p class="style_7">Редактировать объявление раздела Куплю</p>
    <table id="EditBuysTable">
    </table>
</div>