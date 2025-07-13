//BANNER SECTION
function BannerView(i) {
    $.post("inc/admin_functions.php", {
        banner_id: i
    }, function(data) {
        $('#BannerViewID').html(data);
    });
    $('#wind2').show(500);
    enableA();
}
function BannerCodeView(i) {
    $.post("inc/admin_functions.php", {
        banner_id: i
    }, function(data) {
        $('#BannerChangeID').val(data);
        $('#BannerChange').val(i);
    });
    $('#status').text();
    $('#wind1').show(500);
    enableA();
}
function BannerSave() {
    $.post("inc/admin_functions.php",
            {
                banner_change_code: $('#BannerChangeID').val(),
                banner_change_id: $('#BannerChange').val()
            }, function(data) {
        if (data === 'yes') {
            alert('\u0423спешно сохранено!');
        } else {
            alert('\u041fроизошла ошибка!');
        }
    });
}
function ShowBannerInfo(id) {
    $.post("inc/admin_functions.php", {
        BannerImmoIDInfo: id
    },
    function(data)
    {
        $('#BannerInfoTable').html(data);
    });
    $('#info_baner_block').show(500);
    enableA();
}
function ChangeBannerInfo(id) {
    $.post("inc/admin_functions.php", {
        BannerImmoIDChange: id,
        BannerImmoChangeOrganization: $('#BannerInfoOrganization').val(),
        BannerImmoChangeContactName: $('#BannerInfoContactName').val(),
        BannerImmoChangeContacts: $('#BannerInfoContacts').val()
    },
    function(data)
    {
        if (data === 'yes') {
            alert('\u0423спешно обновлено!');
        } else {
            alert('\u0412озникла ошибка!');
        }
    });
}
function ChangeTimeToEnd(id) {
    $('#BannerAddDaysID').val(id);
    $.post("inc/admin_functions.php", {
        BannerAddDaysLast: id
    },
    function(data)
    {
        $('#BannerAddDaysLast').text('\u041fериод действия банера: ' + data + " \u0434ней");
    });
    $('#time_baner_block').show(500);
    enableA();
}
function AddDays() {
    $.post("inc/admin_functions.php", {
        BannersAddDaysSubmit: $('#BannerAddDaysID').val(),
        BannersAddDaysPlus: parseInt($('#BannerAddDays').val(), 10)
    },
    function(data)
    {
        if (data === 'yes') {
            alert('\u0412ремя продлено!');
        } else {
            alert('\u0412озникла ошибка!');
        }
    });
    $('#time_baner_block').hide(500);
    $.post("inc/admin_functions.php", {
        BannerAddDaysLast: $('#BannerAddDaysID').val()
    },
    function(data)
    {
        $('#BannerAddDaysLast').text('\u041fериод действия банера: ' + data + " \u0434ней");
    });
    $('#time_baner_block').show(500);
}
function WebcamRedakt(id) {
    $.post('inc/admin_functions.php', {
        WebcamRedakt: id
    },
    function(data) {
        $('#WebcamIDRed').val(id);
        $('#WebcamCodeRed').val(data.code);
        $('#WebcamNameRed').val(data.name);
        $('#edit_camera').slideDown(500);
        enableA();
    }, "json");
}
function WebcamSave() {
    $.post('inc/admin_functions.php', {
        WebcamSaveID: $('#WebcamIDRed').val(),
        WebcamSaveName: $('#WebcamNameRed').val(),
        WebcamSaveCode: $('#WebcamCodeRed').val()
    },
    function(data) {
        if (data === 'yes') {
            $('#web_' + $('#WebcamIDRed').val() + ' td p[class="style_4"]').fadeOut(500, function() {
                $('#web_' + $('#WebcamIDRed').val() + ' td p[class="style_4"]').text($('#WebcamNameRed').val());
            }).fadeIn(500);
        } else {
            alert('\u0412озникла ошибка!');
        }
    });
}
function WebcamIDset(id) {
    $('#change_image').slideDown(500);
    $('#WebcamIMG').val(id);
    enableA();
}
function ChangeWebcamImage(url) {
    $.post('inc/admin_functions.php', {
        ChangeWebcamImageID: $('#WebcamIMG').val(),
        ChangeWebcamImageIMG: url
    },
    function(data) {
        if (data === 'yes') {
            $('#web_' + $('#WebcamIMG').val() + ' td img[class="img_ob"]').fadeOut(500, function() {
                $('#web_' + $('#WebcamIMG').val() + ' td img[class="img_ob"]').attr('src', url);
            }).fadeIn(500);
        } else {
            alert('\u0412озникла ошибка!');
        }
    });
}
function EnableWebBlock(obj) {
    if (window.confirm('Отобразить блок "Веб-камеры" вместо блока "Статьи"?')) {
        $.post("inc/admin_functions.php", {
            EnableWebBlock: 1
        },
        function(data)
        {
            if (data === 'yes') {
                alert('Блок на главной странице изменён!');
                $(obj).hide(500);
            } else {
                alert('Возникла ошибка!');
            }
        });
    }
}