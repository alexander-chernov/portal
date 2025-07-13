function ShowUpPrice() {
    $.post("inc/admin_functions.php",{
        LoadUpPrice:1
    } ,
    function(data)
    {
        $('#UpPrice').html(data);
    })
    $('#UpPrice').css('display','block');
    enableA();
}
function SaveUpPrice() {
    $.post("inc/admin_functions.php",{
        SaveUp1d: parseInt($('#day1').val(),10),
        SaveUp2d: parseInt($('#day2').val(),10),
        SaveUp3d: parseInt($('#day3').val(),10),
        SaveUp1p: parseInt($('#price1').val(),10),
        SaveUp2p: parseInt($('#price2').val(),10),
        SaveUp3p: parseInt($('#price3').val(),10)
    } ,
    function()
    {
        ShowUpPrice();
        alert('\u041eбновлено!');
    })
}
function CalcPriceUp() {
    $.post("inc/admin_functions.php",{
        DaysPriceUp: parseInt($('#day_calc').val(),10)
    } , function(data) {
        $('#price_calc').text(data+' \u0440ублей');
    })
}
function UpImmo() {
    $.post("inc/admin_functions.php",{
        ImmoUpDays: parseInt($('#DaysForUp').val(),10),
        ImmoUpID: $('#ImmoForUp').val()
    } , function(data) {
        alert(data);
    })
    $('#UpBlock').css('display','none');
    UpImmoBlock($('#ImmoForUp').val());
}
function UpImmoBlock(id) {
    $('#ImmoForUp').val(id);
    $.post("inc/admin_functions.php",{
        ImmoIdToDays: $('#ImmoForUp').val()
    } , function(data) {
        $('#LastUp').text(data);
    })
    $('#UpBlock').css('display','block');
    enableA();
}
function DownImmo() {
    $.post("inc/admin_functions.php",{
        ImmoDownId: $('#ImmoForUp').val()
    } , function(data) {
        alert(data);
    })
    $('#UpBlock').css('display','none');
    UpImmoBlock($('#ImmoForUp').val());
}