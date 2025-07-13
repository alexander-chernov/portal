$(function() {
    $('body').mouseover(function() {
        $('.photo_map_vsplivaet').hide(500);
    });
});
function ShowPhoto(obj) {
    if ($(obj).attr('src') === '../images/photo_1.png') {
        $(obj).attr('src', '../images/photo_2.png');
        $.ajax({
            type: "POST",
            url: "inc/ajax.php",
            data: "ShowPhoto=" + $(obj).attr('alt'),
            success: function(data) {
                if ($('.photo_map_vsplivaet img').css('display') !== 'block') {
                    $('.photo_map_vsplivaet img').attr('src', data);
                    $('.photo_map_vsplivaet').show();
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("\u0412озникла ошибка!");
            }
        });
    }
    $(document).mousemove(function(e) {
        var x = e.pageX;
        var y = e.pageY;
        $('.photo_map_vsplivaet').css('left', x + 2);
        $('.photo_map_vsplivaet').css('top', y + 2);
    });
}
function HidePhoto(obj) {
    $(obj).attr('src', '../images/photo_1.png');
    if ($('.photo_map_vsplivaet').css('display') === 'block') {
        $('.photo_map_vsplivaet').hide();
    }
}

function ShowImage(num) {
    changeImage2(num);
    $('#wind_poto_nedvigimost').show(500);
    enableA();
}

function CloseWindow(id) {
    $('#' + id).hide(500);
    disableA();
}

function SearchSubmit() {
    $('#SearchForm input').each(function() {
        if ($(this).val() === '')
            $(this).prop('disabled', true);
        if (($(this).attr('type') === 'checkbox') && !($(this).is(':checked')))
            $(this).prop('disabled', true);
    });
    $('#SearchForm select').each(function() {
        if ($(this).val() === '0')
            $(this).prop('disabled', true);
    });
    return true;
}
$(document).ready(function() {
    $('.raion_up label input').click(function() {
        $('.distr_to_search').html('');
        $('input[name="DistrictId[]"]').each(function() {
            if ($(this).is(':checked')) {
                var span = $('<span>', {text: $(this).parent().text()});
                span.appendTo($('.distr_to_search'));
            }
        });
    });
});
function FilterToggle(obj) {
    $('.parametr_filter_close').slideToggle(500);
    if ($(obj).val() === 'Расширить') {
        $(obj).val('Свернуть');
        $(obj).attr('type', 'button');
    } else {
        $(obj).val('Расширить');
        $(obj).attr('type', 'reset');
    }
}