$(function() {
    $('body').mouseover(function() {
        $('.photo_map_vsplivaet').hide(500);
    });
});
function ShowPhoto(obj) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "ShowPhoto=" + $(obj).attr('alt'),
        success: function(data) {
            if (data) {
                if ($('.photo_map_vsplivaet img').css('display') !== 'block') {
                    $('.photo_map_vsplivaet img').attr('src', data);
                    $('.photo_map_vsplivaet').show();
                }
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
    $(document).mousemove(function(e) {
        var x = e.pageX;
        var y = e.pageY;
        $('.photo_map_vsplivaet').css('left', x + 2);
        $('.photo_map_vsplivaet').css('top', y + 2);
    });
}
function HidePhoto(obj) {
    $(obj).attr('src', 'images/photo_1.png');
    if ($('.photo_map_vsplivaet img').css('display') === 'block') {
        $('.photo_map_vsplivaet').hide();
    }
}