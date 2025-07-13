function disableA()
{
    $('#temno').hide();
}
function enableA()
{
    $('#temno').show();
}
function CloseWindow(wid) {
    $('#' + wid).hide(500);
    disableA();
}
function disableP()
{
    $('#prozrachno').hide();
}
function enableP()
{
    $('#prozrachno').show();
}
function ShowFormsFilter(i) {
    $('.visible_filter_all').slideUp(500);
    $('#filter_' + i).slideDown(500);
}
function ShowFormsUserads(i) {
    $('#user_form_1').hide();
    $('#user_form_2').hide();
    $('#user_form_' + i).show();
    if (i==1) {
        $('#photo1').css('background','#ebede8');
        $('#photo2').css('background','#fff');
    }
    if (i==2) {
        $('#photo1').css('background','#fff');
        $('#photo2').css('background','#ebede8');
    }

}
function ShowFormspVopros(obj) {
    if ($(obj).text() === 'К ответам') {
        $(obj).text('Задать вопрос');
    } else {
        $(obj).text('К ответам');
    }
    $('.vopros_otvet').toggle();
}
function FormSubmitCheck() {
    if ($('#login_name').val() === "" || $('#pass_value').val() === "") {
        alert('\u0417аполните все необходимые поля!');
        return false;
    } else {
        return true;
    }
}