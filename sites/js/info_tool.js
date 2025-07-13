$(document).ready(function() {
    var width = '100px';
    var radius = '0px';
    var background = 'green';
    $('.param_inf input').change(function() {
        if ($(this).prev('span').text().match(/^Цвет/)) {
            $('#tomsk-line-informer').css('background', $(this).val());
            background = $(this).val();
        }
    }).keyup(function() {
        if ($(this).prev('span').text().match(/^Ширина/)) {
            if ($(this).val() === '') {
                width = 100;
            } else {
                width = $(this).val();
            }
            $('#tomsk-line-informer').css('width', width + 'px');
        }
        if ($(this).prev('span').text().match(/^Закругление/)) {
            if ($(this).val() === '') {
                radius = 0;
            } else {
                radius = $(this).val();
            }
            $('#tomsk-line-informer').css('border-radius', radius + 'px');
        }
    });
    $('.td_inf .add_site').on("click", function() {
        $('.instrument_script textarea').val('<script type="text/javascript" src="http://79.136.218.204/kedr.ru/js/informer.js?color=' + background + '?width=' + width + '?radius=' + radius + '"></script><div id="tomsk-line-informer"></div>');
    });
});