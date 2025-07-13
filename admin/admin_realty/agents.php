<?php
$page = 1;
if (isset($_GET['PageIndex'])) {
    $page = $_GET['PageIndex'];
}

//Выбор страницы для отображения
if (!isset($_SESSION['WhereAg'])) {
    $_SESSION['WhereAg'] = '';
}
$agents = new Agents();
$agents->LoadAgents(50, $page, $_SESSION['WhereAg']);
?>
<script type="text/javascript" src="../js/ajaxupload.3.5.js"></script>
<script type="text/javascript">
    $(function(){
        var btnUpload=$('#AgentUpload');
        var status=$('#status');
        new AjaxUpload(btnUpload, {
            action: 'upload-file.php',
            name: 'uploadfile',
            onSubmit: function(file, ext){
                if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
                    // extension is not allowed 
                    status.text('Only JPG, PNG or GIF files are allowed');
                    return false;
                }
                status.text('Uploading...');
            },
            onComplete: function(file, response){
                status.text('');
                if(response==="error"){
                    $('<li></li>').appendTo('#files').text(file);
                } else{
                    $('#AgentAvatarShow').attr('src',response);
                    AvatarChange(response);
                }
            }
        });
		
    });
</script>
<div id="admin_nedvigim_3" class="block_content_1"><b><font color="blue">Агенства недвижимости</font></b><br><br>
    <table style="width: 100%;">
        <tr>
            <td>
                <img class="img_options" src="../images/add_team.png" title="Добавить Агентство" alt="" onclick="AddAgentWindow();">
                <img class="img_options" src="../images/delete_team.png" onclick="CheckedAgentVal(<?php echo count($agents->agent_id); ?>,1);" title="Удалить выделенные Агентства" alt="">
                <img class="img_options" src="../images/aktive_team.png" onclick="CheckedAgentVal(<?php echo count($agents->agent_id); ?>,2);" title="Активировать выделенные Агентства" alt="">
                <img class="img_options" src="../images/deactivate_team.png" onclick="CheckedAgentVal(<?php echo count($agents->agent_id); ?>,3);" title="Скрыть выделенные Агентства" alt="">
                <img class="img_options" src="../images/check_all.png" onclick="CheckedAllAgents(<?php echo count($agents->agent_id); ?>);" id="CheckButton" title="Выделить все Агентства" alt="">
            </td>
            <td style="text-align: right;">
                <img id="lupa_plus_agenstvo" class="img_options_1" src="../images/lupa_plus.png" title="Развернуть поиск Агентств" alt="" onclick="$('#lupa_plus_agenstvo').css('display','none'); $('#lupa_minus_agenstvo').css('display','block'); $('#parametr_search_agenstvo').css('display','block');">
                <img  id="lupa_minus_agenstvo" class="img_options_1" style="display: none;" src="../images/lupa_minus.png" title="Свернуть поиск Агентств" alt="" onclick="$('#lupa_plus_agenstvo').css('display','block'); $('#lupa_minus_agenstvo').css('display','none'); $('#parametr_search_agenstvo').css('display','none');">
            </td>
        </tr>
    </table>
    <div id="parametr_search_agenstvo" style="display: none;">
        <form action="index.php" method="GET">
            <table>
                <tr>
                    <td><p class="style_2">По <font color="green">№ Агентства</font>:</p></td>
                    <td><input type="text" name="AgentSearchID" value=""></td>
                </tr>
                <tr>
                    <td><p class="style_2">По <font color="green">названию Агентства</font>:</p></td>
                    <td><input type="text" name="AgentSearchName" value=""></td>
                </tr>
                <tr>
                    <td>
                        <p class="style_2">По <font color="green">адресу Агентства (улица)</font>:</p>
                    </td>
                    <td>
                        <input type="text" name="AgentSearchAddress" value="">
                    </td>
                </tr>
                <tr>
                    <td>
                        <p class="style_2">По <font color="green">статусу</font>:</p>
                    </td>
                    <td>
                        <select name="AgentSearchState">
                            <option value="no">Не указан</option>
                            <option value="1">Активные</option>
                            <option value="0">Скрытые</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" name="AgentSearchSubmit" style="width: 100%;" value="Найти">
                    </td>
                    <td>
                        <input type="submit" name="AgentSearchReset" style="width: 100%; color: red;" value="Сбросить поиск">
                    </td>    
                </tr>
            </table>
        </form>
    </div>
    <?php
    $agents->GenerateNavigation($page);
    ?>
    <table style="width: 100%; text-align: center;">
        <tr style="background: #7caed3;">
            <td colspan="2"><p class="style_5">№ Агентства</p></td>
            <td><p class="style_5">Аватар</p></td>
            <td><p class="style_5">Название</p></td>
            <td><p class="style_5">Пользователь</p></td>
            <td><p class="style_5">Дата</p></td>
            <td><p class="style_5">Статус</p></td>
            <td><p class="style_5">Время</p></td>
            <td><p class="style_5">Действие</p></td>
        </tr>
        <?php
        $agents->GenerateTable();
        ?>
    </table>
    <?php
    $agents->GenerateNavigation($page);
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

<div id="info_agentstvo" class="wind">       <!--Всплывающее окно Информации по Агентству-->
    <a class="close" href="#" onclick="CloseWindow('info_agentstvo');">X</a>
    <br>
    <br>
    <p class="style_7">Информация по Агентству</p>
    <table id="InfoAgentsTable">
    </table>
</div>

<div id="edit_agentstvo" class="wind_1">       <!--Всплывающее окно Редактировать Агентство-->
    <a class="close" href="#" onclick="document.getElementById('edit_agentstvo').style.display='none'; disableA();">X</a>
    <br>
    <br>
    <p class="style_7">Редактируем Агентство</p>
    <table id="AgentEditTable">
    </table>
</div>

<div id="avatar_agentstvo" class="wind">       <!--Всплывающее окно редактировать Аватарку Агентства-->
    <a class="close" href="#" onclick="document.getElementById('avatar_agentstvo').style.display='none'; disableA();">X</a>
    <br>
    <br>
    <p class="style_7">Аватарка Агентства</p>
    <input type="hidden" id="AgentHiddenID" value="1">
    <table id="AgentAvatarTable">
        <button id="AgentUpload" style="width: 100%;">Загрузить</button>
        <span id="status" ></span>
        <ul id="files" ></ul>
    </table>
</div>

<div id="pass_agentstvo" class="wind">       <!--Всплывающее окно изменения пароля для Агентство-->
    <a class="close" href="#" onclick="document.getElementById('pass_agentstvo').style.display='none'; disableA();">X</a>
    <br>
    <br>
    <p class="style_7">Изменение пароля Агентства</p>
    <table>
        <tr>
            <td><p class="style_2">Пароль:</p></td>
            <td><input type="password" id="AgentPassLine1" value=""></td>
        </tr>
        <tr>
            <td><p class="style_2">Повторите пароль:</p></td>
            <td><input type="password" id="AgentPassLine2" onkeyup="LinesCompare();" value=""></td>
        </tr>
        <tr>
            <td colspan="2">
                <button style="float:left;" onclick="ChangeAgentPassword();">Изменить</button>
                <input type="hidden" id="AgentPasswordID" value="1">
            </td>
        </tr>
    </table>
</div>

<div id="add_agentstvo" class="wind_1">       <!--Всплывающее окно Добавить Агентство-->
    <a class="close" href="#" onclick="CloseWindow('add_agentstvo');">X</a>
    <br>
    <br>
    <p class="style_7">Добавление пользователя как Агентство</p>
    <table style="width: 500px;">
        <tr>
            <td><p class="style_2">Ник пользователя:</p></td>
            <td><input type="text" id="AddAgentLogin" value="" style="width: 100%;"></td>
        </tr>
        <tr>
            <td><p class="style_2">Пароль:</p></td>
            <td><input type="password" id="AddAgentPassword1" value="" style="width: 100%;"></td>
        </tr>
        <tr>
            <td><p class="style_2">Повторите пароль:</p></td>
            <td><input type="password" id="AddAgentPassword2" onkeyup="PasswordCompareAgent();" value="" style="width: 100%;"></td>
        </tr>
        <tr>
            <td><p class="style_2">Контактное лицо:</p></td>
            <td>
                <input type="text" value="" placeholder="Имя" id="AddAgentFName" style="width: 30%;">
                <input type="text" value="" placeholder="Фамилия" id="AddAgentLName" style="width: 30%;">
                <input type="text" value="" placeholder="Отчество" id="AddAgentOName" style="width: 30%;">
            </td>
        </tr>
        <tr>
            <td><p class="style_2">E-mail:</p></td>
            <td><input type="text" id="AddAgentEmail" value="" style="width: 100%;"></td>
        </tr>
        <tr>
            <td><p class="style_2">Введите адрес:</p></td>
            <td><input type="text" id="AgentEditAddress" onkeyup="SearchAddress();" value="" style="width: 100%;"></td>
        </tr>
        <tr>
            <td><p class="style_2">Выберите из списка:</p></td>
            <td id="AgentAddressResult">
                <select id="ImmoAddressChosen" style="width: 100%;" name="ImmoAddressChosen"><option selected value="0"></option></select>
                </select>
            </td>
        </tr>
        <tr>
            <td><p class="style_2">Название Агентства:</p></td>
            <td><input type="text" value="" id="AddAgentName" style="width: 100%;"></td>
        </tr>
        <tr>
            <td><p class="style_2">Телефон:</p></td>
            <td><input type="text" id="AddAgentPhone" value="" style="width: 100%;"></td>
        </tr>
        <tr>
            <td><p class="style_2">Сайт Агентства:</p></td>
            <td><input type="text" value="" id="AddAgentSite" style="width: 100%;"></td>
        </tr>
        <tr>
            <td><p class="style_2">Время действия Агентства:</p></td>
            <td><input type="text" value="" id="AddAgentDays" style="width: 100%;"></td>
        </tr>
        <tr>
            <td colspan="2">
                <p class="style_2">Описание Агентства:</p>
                <textarea rows="12" cols="55" id="AddAgentDescription" style="width: 100%;"></textarea>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <button style="float:left; width: 100%;" onclick="AddAgentIntoTable();">Добавить</button>
            </td>
        </tr>
    </table>
</div>