function ShowBuys(id) {
    $.post("inc/admin_functions.php",{
        BuysID: id
    } ,
    function(data)
    {
        $('#EditBuysTable').html(data);
    })
    $('#edit_kupliu').css('display','block');
    enableA();
}

function EditBuysSubmit(id) {
    $.post("inc/admin_functions.php",{
        BuysEditID: id,
        BuysTextEdit: $('#BuysTextEdit').val()
    } ,
    function(data)
    {
        if(data.strip() == 'yes') {
            alert('\u0423спешно сохранено!')
        } else {
            alert('\u0412озникла ошибка!');
        }
    })
}

function DeleteBuysSubmit(id) {
    if(confirm("\u042dтим действием вы удалите объявление. Вы уверены?")) {
        $.post("inc/admin_functions.php",{
            BuysDeleteID: id
        } ,
        function(data) {
            if(data.strip() == 'yes') {
                alert('\u041eбъявление успешно удалено!');
                $('#RowBuys_'+id).css('display','none');
            } else {
                alert('\u041dе удалось совершить операцию!');
            }
        })
    }
}

function DeleteSelectedBuys(max) {
    if(confirm('\u0412ы уверены?')) {
        for(i=0;i<max;i++) {
            if (CheckOrNot('CheckBuys_'+i)) {
                $.post("inc/admin_functions.php",{
                    BuysDeleteID: $('#CheckBuys_'+i).val()
                } ,
                function() {})
                $('#RowBuys_'+$('#CheckBuys_'+i).val()).css('display','none');
            }
        }
    }
}