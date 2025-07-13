function ShowBannerInfo(id) {
    $.post("inc/admin_functions.php",{
        BannerImmoIDInfo: id
    } ,
    function(data)
    {
        $('#BannerInfoTable').html(data);
    })
    $('#info_baner_block').css('display','block');
    enableA();
}

function ChangeBannerInfo(id) {
    $.post("inc/admin_functions.php",{
        BannerImmoIDChange: id,
        BannerImmoChangeOrganization: $('#BannerInfoOrganization').val(),
        BannerImmoChangeContactName: $('#BannerInfoContactName').val(),
        BannerImmoChangeContacts: $('#BannerInfoContacts').val()
    } ,
    function(data)
    {
        alert(data);
    })
}

function ChangeTimeToEnd(id) {
    $('#BannerAddDaysID').val(id);
    $.post("inc/admin_functions.php",{
        BannerAddDaysLast: id
    } ,
    function(data)
    {
        $('#BannerAddDaysLast').text('Период действия банера: '+data+" дней");
    })
    $('#time_baner_block').css('display','block');
    enableA();
}

function AddDays() {
    $.post("inc/admin_functions.php",{
        BannersAddDaysSubmit: $('#BannerAddDaysID').val(),
        BannersAddDaysPlus: parseInt($('#BannerAddDays').val(),10)
    } ,
    function(data)
    {
        alert(data);
    })
    $('#time_baner_block').css('display','none');
    $.post("inc/admin_functions.php",{
        BannerAddDaysLast: $('#BannerAddDaysID').val()
    } ,
    function(data)
    {
        $('#BannerAddDaysLast').text('Период действия банера: '+data+" дней");
    })
    $('#time_baner_block').css('display','block');
}

function BannerCodeEdit(id) {
    $.post("inc/admin_functions.php",{
        BannerEditCodeID: id
    } ,
    function(data)
    {
        $('#BannerCodeEdit').val(data);
    })
    $('#wind1').find('button').attr('name',id);
    $('#wind1').css('display','block');
    enableA();
}

function BannerCodeEditSubmit() {
    $.post("inc/admin_functions.php",{
        BannerEditCodeSubmit: $('#BannerCodeEdit').val(),
        BannerEditCodeSubmitID: $('#wind1').find('button').attr('name')
    } ,
    function(data)
    {
        alert(data);
    })
}

function ViewBanner(id) {
    $.post("inc/admin_functions.php",{
        BannerViewID: id
    } ,
    function(data)
    {
        $('#ViewBannerID').html(data);
    })
    $('#wind2').css('display','block');
    enableA();
}