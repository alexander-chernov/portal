function ShowNews(id) {
    $.post("inc/admin_functions.php",{
        NewsID: id
    } ,
    function(data)
    {
        $('#NewsTable').html(data);
    })
    $('#info_news').css('display','block');
    enableA();
}

function EditNews(id) {
    $.post("inc/admin_functions.php",{
        NewsIDEdit: id
    } ,
    function(data)
    {
        $('#NewsEditTable').html(data);
    })
    $('#edit_news').css('display','block');
    enableA();
}

function NewsEditSubmit() {
    $.post("inc/admin_functions.php",{
        NewsIDEditSub: $('#NewsIDEditSub').val(),
        NewsHeaderEdit: $('#NewsHeaderEdit').val(),
        NewsTextEdit: $('#NewsTextEdit').val(),
        NewsSubcategoriesEdit: $('#NewsSubcategoriesEdit').val()
    } ,
    function(data)
    {
        if(data.strip() == 'yes') {
            alert('\u0418нформация обновлена!');
            $('#NewsHeaderTable_'+$('#NewsIDEditSub').val()).text($('#NewsHeaderEdit').val());
            $('#NewsSubcategoryTable_'+$('#NewsIDEditSub').val()).text($("#NewsSubcategoriesEdit option[value='"+$('#NewsSubcategoriesEdit').val()+"']").text());
        }
    })
}

function NewsEditAvatar(id) {
    $.post("inc/admin_functions.php",{
        NewsIDAvatar: id
    } ,
    function(data)
    {
        $('#AvatarEditTable').html(data);
    })
    $('#avatar_news').css('display','block');
    enableA();
}

function NewsAvatarChange(filename) {
    $.post("inc/admin_functions.php",{
        NewsAvatarChangeID: $('#AvatarNewsIDCh').val(),
        NewsAvatarChangeURL: filename
    } ,
    function() {})
    $('#NewsImage_'+$('#AvatarNewsIDCh').val()).attr('src',filename);
    $('#AvatarEditTable').css('display','block');
    $('#NewsImage_'+$('#AvatarNewsIDCh').val()).css('display','block');
}

function DeleteNewsAvatar() {
    $.post("inc/admin_functions.php",{
        NewsAvatarDeleteID: $('#AvatarNewsIDCh').val()
    } ,
    function() {
        $('#AvatarEditTable').css('display','none');
        $('#NewsImage_'+$('#AvatarNewsIDCh').val()).css('display','none');
    })
}

function DeleteNewsSubmit(id) {
    if(confirm("\u042dтим действием вы удалите новость. Вы уверены?")) {
        $.post("inc/admin_functions.php",{
            NewsDeleteID: id
        } ,
        function(data) {
            if(data.strip() == 'yes') {
                alert('\u041dовость успешно удалена!');
                $('#NewsRow_'+id).css('display','none');
            } else {
                alert('\u041dе удалось совершить операцию!');
            }
        })
    }
}

function ChangeSubcategory() {
    $.post("inc/admin_functions.php",{
        SubcategoryEdit: 1
    } ,
    function(data) {
        $('#SubcategoryEditTable').html(data);
    })
    $('#edit_rubrik').css('display','block');
    enableA();
}

function SubcategoryChangeName() {
    $.post("inc/admin_functions.php",{
        SubcategoryChangeName: $('#SubcategoryNewName').val(),
        SubcategoryChangeNameID: $('#SubcategorySelect').val()
    } ,
    function(data) {
        if(data.strip() == 'yes') {
            $("#SubcategorySelect option[value='"+$('#SubcategorySelect').val()+"']").text($('#SubcategoryNewName').val());
            alert('\u041dазвание рубрики изменено!')
        } else {
            alert('\u0412озникла ошибка!');
        }
    })
}

function DeleteSubcategoryShow() {
    $.post("inc/admin_functions.php",{
        SubcategoryDeleteShow: 1
    } ,
    function(data) {
        $('#DeleteSubcategoryTable').html(data);
    })
    $('#down_rubrik').css('display','block');
    enableA();
}

function SubcategoryDeleteSubmit() {
    if(confirm("\u042dтим действием вы удалите рубрику и все новости, входящие в неё. Вы уверены?")) {
        $.post("inc/admin_functions.php",{
            SubcategoryDeleteSubmit: $('#SubcategorySelectDel').val()
        } ,
        function(data) {
            if(data.strip() == 'yes') {
                $("#SubcategorySelectDel option[value='"+$('#SubcategorySelectDel').val()+"']").remove();
                alert('\u0423даление прошло успешно! Обновите страницу!');
            }
        })
    }
}

function SubcategoryAddShow() {
    $('#new_rubrik').css('display','block');
    enableA();
}

function AddNewSubcategorySubmit() {
    $.post("inc/admin_functions.php",{
        SubcategoryNewStr: $('#SubcategoryNewStr').val()
    } ,
    function(data) {
        if(data.strip() == 'yes') {
            alert('\u041dовая рубрика добавлена!');
        }
        if(data.strip() == 'no') {
            alert('\u041fроизошла ошибка!');
        }
        if(data.strip() == 'have') {
            alert('\u0420убрика с таким именем уже существует!');
        }
    })
}

function NewNewsAddShow() {
    $.post("inc/admin_functions.php",{
        NewNewsAddShow: 1
    } ,
    function(data) {
        $('#NewsAddTable').html(data);
    })
    $('#add_new').css('display','block');
    enableA();
}

function NewsNewAddSubmit() {
    $.post("inc/admin_functions.php",{
        NewsNewHeaderAdd: $('#NewsNewHeaderAdd').val(),
        NewsNewTextAdd: $('#NewsNewTextAdd').val(),
        SubcategorySelectNewsAdd: $('#SubcategorySelectNewsAdd').val()
    } ,
    function(data) {
        if (data.strip() == 'yes') {
            alert('\u041dовость успешно добавлена! Обновите страницу!');
            $('#add_new').css('display','none');
            disableA();
        } else {
            alert('\u041fроизошла ошибка!');
        }
    })
}

function CheckedAllNews(max) {
    for(i=0;i<max;i++) {
        if (CheckOrNot('NewsCheck_'+i)){
            $('#NewsCheck_'+i).attr('checked','');
            $('#CheckButton').attr('title','\u0412ыделить все Новости');
        } else {
            $('#NewsCheck_'+i).attr('checked','checked');
            $('#CheckButton').attr('title','\u0421нять выделения с Новостей');
        }
    }
}

function DeleteSelectedNews(max) {
    if(confirm('\u0412ы уверены?')) {
        for(i=0;i<max;i++) {
            if (CheckOrNot('NewsCheck_'+i)) {
                $.post("inc/admin_functions.php",{
                    NewsDeleteID: $('#NewsCheck_'+i).val()
                } ,
                function() {})
                $('#NewsRow_'+$('#NewsCheck_'+i).val()).css('display','none');
            }
        }
    }
}