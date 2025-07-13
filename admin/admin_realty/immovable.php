<?php
$page = 1;
if (isset($_GET['PageIndex'])) {
    $page = $_GET['PageIndex'];
}

//Выбор страницы для отображения
if (!isset($_SESSION['Where'])) {
    $_SESSION['Where'] = '';
}
$immovable = new Realty();
$immovable->LoadRealty(50, $page, $_SESSION['Where']);
?>
<script type="text/javascript" src="js/ImmoChange.js"></script>
<script type="text/javascript" src="js/Immovable.js"></script>
<div id="admin_nedvigim_1" class="block_content_1"><b><font color="blue">Объявления Недвижимости</font></b><br><br>
    <table style="width: 100%;">
        <tr>
            <td>
                <img class="img_options" onclick="CheckedAdsVal(<?php echo count($immovable->id); ?>,1);" src="../images/delete_team.png" title="Удалить выделенные объявления" alt="">
                <img class="img_options" onclick="CheckedAdsVal(<?php echo count($immovable->id); ?>,2);" src="../images/aktive_team.png" title="Активировать выделенные объявления" alt="">
                <img class="img_options" onclick="CheckedAdsVal(<?php echo count($immovable->id); ?>,3);" src="../images/deactivate_team.png" title="Скрыть выделенные объявления" alt="">
                <img class="img_options" onclick="CheckedAllImmo(<?php echo count($immovable->id); ?>);" src="../images/check_all.png" id="CheckButton" title="Выделить все Объявления" alt="">
            </td>
            <td style="text-align: right;">
                <img id="lupa_plus" class="img_options_1" src="../images/lupa_plus.png" title="Развернуть поиск" alt="" onclick="SearchWidget(1);">
                <img  id="lupa_minus" class="img_options_1" style="display: none;" src="../images/lupa_minus.png" title="Свернуть поиск" alt="" onclick="SearchWidget(2);">
            </td>
        </tr>
    </table>
    <div id="parametr_search" style="display: none;">
        <form action="index.php" method="GET">
            <table>
                <tr>
                    <td>
                        <p class="style_2">По <font color="green">ID</font>:</p>
                    </td>
                    <td>
                        <input name="ImmoSearchID" type="text" value="">
                    </td>
                </tr>
                <tr>
                    <td>
                        <p class="style_2">По <font color="green">нику пользователя</font>:</p>
                    </td>
                    <td>
                        <input name="ImmoSearchNick" type="text" value="">
                    </td>
                </tr>
                <tr>
                    <td>
                        <p class="style_2">По <font color="green">рубрике</font>:</p>
                    </td>
                    <td>
                        <?php
                        $query = 'SELECT * FROM k_immovables_subcategories as kis LEFT JOIN k_immovables_categories as kic ON (kis.k_is_parent = kic.k_ic_id)';
                        $result = mysql_query($query);
                        echo '<select name="ImmoSearchSubCategory">';
                        echo '<option value="0">Не указано</option>';
                        while ($row = mysql_fetch_array($result)) {
                            echo '<option value="' . $row['k_is_id'] . '">' . $row['k_ic_name'] . ' ' . $row['k_is_name'] . '</option>';
                        }
                        echo '</select>';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p class="style_2">По <font color="green">статусу</font>:</p>
                    </td>
                    <td>
                        <select name="ImmoSearchState">
                            <option value="no">Не указан</option>
                            <option value="1">Активные</option>
                            <option value="0">Скрытые</option>
                            <option value="2">Ожидают подтверждения</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input name="ImmoSearchUserType1" type="checkbox" checked="" value="1">
                        <label>Объявления Агентств</label>
                        <input name="ImmoSearchUserType2" type="checkbox" checked="" value="4">
                        <label>Объявления Пользователей</label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p class="style_2">По <font color="green">адресу</font>:</p>
                    </td>
                    <td>
                        <input name="ImmoSearchAddress" type="text" value="">
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" name="ImmoSearchSubmit" style="width: 100%;" value="Найти">
                    </td>
                    <td>
                        <input type="submit" name="ImmoSearchReset" style="width: 100%; color: red;" value="Отменить поиск">
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <?php
    $immovable->GenerateNavigation($page);
    ?>
    <table style="width: 100%; text-align: center;">
        <tr style="background: #7caed3;">
            <td colspan="2"><p class="style_5">№</p></td>
            <td><p class="style_5">Фото</p></td>
            <td><p class="style_5">Рубрика</p></td>
            <td><p class="style_5">Пользователь</p></td>
            <td><p class="style_5">Дата</p></td>
            <td><p class="style_5">Статус</p></td>
            <td><p class="style_5">Время</p></td>
            <td><p class="style_5">Действие</p></td>
        </tr>
        <?php
        $immovable->BuildTable();
        ?>
    </table>
    <?php
    $immovable->GenerateNavigation($page);
    ?>
</div>

<div id="send_email" class="wind">       <!--Всплывающее окно отправки почты-->
    <a class="close" href="#" onclick="CloseWindow('send_email');">X</a>
    <br>
    <br>
    <p class="style_7">Отправить Письмо</p>
    <table>
        <tr>
            <td><p class="style_2">Тема:</p></td>
            <td><input type="text" id="ImmoEmailTheme" name="ImmoEmailTheme" value=""></td>
        </tr>
        <tr>
            <td colspan="2">
                <p class="style_2">Текст:</p>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <textarea rows="10" cols="50" id="ImmoEmailText" name="ImmoEmailText"></textarea>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="hidden" name="ImmoEmailEmail" id="ImmoEmailEmail" value="">
                <button style="float:right;" onClick="">Отправить</button>
            </td>
        </tr>
    </table>
</div>

<div id="info_obiavlenie_block" class="wind">       <!--Всплывающее окно Информации по объявлению-->
    <a class="close" href="#" onclick="CloseWindow('info_obiavlenie_block');">X</a>
    <br>
    <br>
    <p class="style_7">Информация по объявлению</p>
    <table>
        <tr>
            <td><p class="style_2">Номер объявления:</p></td>
            <td><p class="style_4_2" id="ImmoAdNum"></p></td>
        </tr>
        <tr>
            <td><p class="style_2">Рубрика:</p></td>
            <td><p class="style_4_4" id="ImmoAdIT"></p></td>
        </tr>
        <tr>
            <td><p class="style_2">Ник пользователя:</p></td>
            <td><p class="style_4_4" id="ImmoAdUser"></p></td>
        </tr>
        <tr>
            <td><p class="style_2">Дата размещения:</p></td>
            <td><p class="style_4_4" id="ImmoAdDate"></p></td>
        </tr>
        <tr>
            <td><p class="style_2">Статус:</p></td>
            <td><p class="style_4_4" id="ImmoAdState"></p></td>
        </tr>
        <tr>
            <td><p class="style_2">Время действия объявления:</p></td>
            <td><p class="style_4_4" id="ImmoAdDays"></p></td>
        </tr>
        <tr>
            <td><p class="style_2">Контактное лицо:</p></td>
            <td><p class="style_4_1" id="ImmoAdContact"></p></td>
        </tr>
        <tr>
            <td><p class="style_2">Телефон:</p></td>
            <td><p class="style_4_4" id="ImmoAdPhone"></p></td>
        </tr>
        <tr>
            <td><p class="style_2">Адрес:</p></td>
            <td><p class="style_4_4" id="ImmoAdAddress"></p></td>
        </tr>
        <tr>
            <td><p class="style_2">E-mail:</p></td>
            <td><p class="style_4_4" id="ImmoAdEmail"></p></td>
        </tr>
        <tr>
            <td><p class="style_2">Тип:</p></td>
            <td><p class="style_4_1" id="ImmoAdUT"></p></td>
        </tr>
    </table>
</div>

<div id="photo_obiavlenie" class="wind">       <!--Всплывающее окно редактировать фото объявления-->
</div>

<div id="photo_windows" class="wind">       <!--Всплывающее окно увеличение фото-->
    <a class="close" href="#" onclick="$('#photo_windows').css('display','none');">X</a>
    <br>
    <img class="img_windows" id="ImmoImageShow" src="">
</div>

<div id="edit_obiavlenie" class="wind">
</div>

<div id="UpBlock" class="wind">
    <a class="close" href="#" onclick="CloseWindow('UpBlock');">X</a>
    <br>
    <br>
    <p class="style_7">Поднятие объявления</p>
    <table>
        <tr>
            <td>
                До конца действия поднятия осталось
            </td>
            <td id="LastUp">
            </td>
            <td>
                дней
            </td>
        </tr>
        <tr>
            <td>
                Поднять объявление на
            </td>
            <td>
                <input type="hidden" id="ImmoForUp" value="">
                <input type="text" id="DaysForUp" value="">
            </td>
            <td>
                дней
            </td>
        </tr>
        <tr>
            <td>
                <button onClick="UpImmo()">Прибавить</button>
            </td>
            <td>
                <button onClick="DownImmo()">Опустить</button>
            </td>
            <td>
            </td>
        </tr>
    </table>
</div>

<div id="AddDayBlock" class="wind">
    <a class="close" href="#" onclick="CloseWindow('AddDayBlock');">X</a>
    <br>
    <br>
    <p class="style_7">Продление объявления</p>
    <table>
        <tr>
            <td>
                До конца действия объявления осталось
            </td>
            <td id="LastDays">
            </td>
            <td>
                дней
            </td>
        </tr>
        <tr>
            <td>
                Продлить объявление на
            </td>
            <td>
                <input type="hidden" id="ImmoForAddDays" value="">
                <input type="text" id="DaysForAddDays" value="">
            </td>
            <td>
                дней
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <button onClick="AddDaysSubmit();">Прибавить</button>
            </td>
        </tr>
    </table>
</div>