<?php
if ($_SERVER["SCRIPT_NAME"] == "/kedr.ru/admin/admin_realty/news.php") {
    exit();
}
$page = 1;
if (isset($_GET['PageIndex'])) {
    $page = $_GET['PageIndex'];
}

//Выбор страницы для отображения
if (!isset($_SESSION['WhereNews'])) {
    $_SESSION['WhereNews'] = '';
}
$news = new News();
$news->LoadNews(50, $page, $_SESSION['WhereNews']);
?>
<script type="text/javascript" src="../js/ajaxupload.3.5.js"></script>
<script type="text/javascript" >
    $(function(){
        var btnUpload=$('#NewsUpload');
        var status=$('#statusNews');
        new AjaxUpload(btnUpload, {
            action: 'upload-file2.php',
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
                    $('<li></li>').appendTo('#filesNews').text(file);
                } else{
                    $('#NewsUploadImage').attr('src',response);
                    NewsAvatarChange(response);
                }
            }
        });
		
    });
</script>
<div id="admin_nedvigim_4" class="block_content_1"><b><font color="blue">Новости недвижимости</font></b><br><br>
    <table style="width: 100%;">
        <tr>
            <td>
                <img class="img_options" src="../images/add_team.png" title="Добавить Новость" alt="" onclick="NewNewsAddShow();">
                <img class="img_options" src="../images/delete_team.png" title="Удалить выделенные Новости" onclick="DeleteSelectedNews(<?php echo count($news->news_id); ?>);" alt="">
                <img class="img_options" src="../images/check_all.png" id="CheckButton" title="Выделить все Новости" alt="" onclick="CheckedAllNews(<?php echo count($news->news_id); ?>);">
            </td>
            <td style="text-align: right;">
                <!--ПОИСК-->
                <img id="lupa_plus_new" class="img_options_1" src="../images/lupa_plus.png" title="Развернуть поиск Новостей" alt=""
                     onclick="document.getElementById('lupa_plus_new').style.display='none';
                         document.getElementById('lupa_minus_new').style.display='block';
                         document.getElementById('parametr_search_new').style.display='block';">
                <img  id="lupa_minus_new" class="img_options_1" style="display: none;" src="../images/lupa_minus.png" title="Свернуть поиск Новостей" alt=""
                      onclick="document.getElementById('lupa_plus_new').style.display='block';
                          document.getElementById('lupa_minus_new').style.display='none';
                          document.getElementById('parametr_search_new').style.display='none';">
                <img class="img_options_1" src="../images/new.png" title="Добавить новую рубрику Новостей" alt="" onclick="SubcategoryAddShow();">
                <img class="img_options_1" src="../images/new_down.png" title="Удалить рубрику Новостей" alt="" onclick="DeleteSubcategoryShow();">
                <img class="img_options_1" src="../images/edit_new.png" title="Изменить название рубрики Новостей" alt="" onclick="ChangeSubcategory();">
            </td>
        </tr>
    </table>
    <div id="parametr_search_new" style="display: none;">
        <form>
            <table>
                <tr>
                    <td><p class="style_2">По <font color="green">№ Новости</font>:</p></td>
                    <td><input type="text" name="SearchNewsID" value=""></td>
                </tr>
                <tr>
                    <td><p class="style_2">По <font color="green">заголовку Новости</font>:</p></td>
                    <td><input type="text" name="SearchNewsHeader" value=""></td>
                </tr>
                <tr>
                    <td><p class="style_2">По <font color="green">рубрике Новостей</font>:</p></td>
                    <td><select name="SelectSearchNews">
                            <option value="no">Не указано</option>
                            <?php
                            $query = 'SELECT * FROM k_immovables_subcategories WHERE k_is_parent=4 ORDER BY k_is_name ASC';
                            $result = mysql_query($query);
                            while ($row = mysql_fetch_array($result)) {
                                echo '<option value="' . $row['k_is_id'] . '">' . $row['k_is_name'] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" name="SearchNewsSubmit" style="float:left;" value="Найти">
                        <input type="submit" name="SearchNewsCancel" style="float:left;" value="Сбросить поиск">
                    <td>
                </tr>
            </table>
        </form>
    </div>
    <?php
    $news->GenerateNavigation($page);
    ?>
    <table style="width: 100%; text-align: center;">
        <tr style="background: #7caed3;">
            <td colspan="2"><p class="style_5">№ Новости</p></td>
            <td><p class="style_5">Аватар</p></td>
            <td><p class="style_5">Заголовок</p></td>
            <td><p class="style_5">Рубрика</p></td>
            <td><p class="style_5">Дата</p></td>
            <td><p class="style_5">Действие</p></td>
        </tr>
        <?php
        $news->GenerateTable();
        ?>
    </table>
    <?php
    $news->GenerateNavigation($page);
    ?>
</div>

<div id="info_news" class="wind">       <!--Всплывающее окно Информации о Новости-->
    <a class="close" href="#" onclick="CloseWindow('info_news');">X</a>
    <br>
    <br>
    <p class="style_7">Информация по Новости</p>
    <table style="width: 600px;" border="1" id="NewsTable">
    </table>
</div>

<div id="edit_news" class="wind">       <!--Всплывающее окно Информации о Новости-->
    <a class="close" href="#" onclick="CloseWindow('edit_news')">X</a>
    <br>
    <br>
    <p class="style_7">Редактирование Новости</p>
    <table id="NewsEditTable">
    </table>
</div>

<div id="avatar_news" class="wind">       <!--Всплывающее окно редактировать Аватарку Новости-->
    <a class="close" href="#" onclick="CloseWindow('avatar_news');">X</a>
    <br>
    <br>
    <p class="style_7">Аватарка Новости</p>
    <table id="AvatarEditTable">
        <button id="NewsUpload" style="width: 100%;">Загрузить</button>
        <span id="statusNews" ></span>
        <ul id="filesNews" ></ul>
    </table> 
</div>


<div id="edit_rubrik" class="wind">       <!--Всплывающее окно Редактировать название рубрику новостей-->
    <a class="close" href="#" onclick="CloseWindow('edit_rubrik');">X</a>
    <br>
    <br>
    <p class="style_7">Редактирование названия рубрики Новостей</p>
    <table id="SubcategoryEditTable">
    </table>
</div> 

<div id="down_rubrik" class="wind">       <!--Всплывающее окно удалить рубрику новостей-->
    <a class="close" href="#" onclick="CloseWindow('down_rubrik');">X</a>
    <br>
    <br>
    <p class="style_7">Удаление рубрики Новостей</p>
    <table id="DeleteSubcategoryTable">
    </table>
</div>

<div id="new_rubrik" class="wind">       <!--Всплывающее окно добавить новую рубрику новостей-->
    <a class="close" href="#" onclick="CloseWindow('new_rubrik');">X</a>
    <br>
    <br>
    <p class="style_7">Добавление новой рубрики Новостей</p>
    <table>
        <tr>
            <td><p class="style_2">Введите название рубрики:</p></td>
        </tr>
        <tr>
            <td><input style="width: 300px;" type="text" id="SubcategoryNewStr" value=""></td>
        </tr>
        <tr>
            <td><button style="float:left; width: 100%;" onclick="AddNewSubcategorySubmit();">Добавить рубрику</button></td>
        </tr>
    </table>
</div>

<div id="add_new" class="wind_1">       <!--Всплывающее окно добавить новость-->
    <a class="close" href="#" onclick="CloseWindow('add_new');">X</a>
    <br>
    <br>
    <p class="style_7">Добавление Новости</p>
    <table id="NewsAddTable">
    </table>
</div>