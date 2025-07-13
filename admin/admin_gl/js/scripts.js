function ChangeBlockEParams(id) {
    $.post("inc/admin_functions.php", {
        EmailChange: parseInt(id, 10)
    }, function(data) {
        if (data !== 'no') {
            $('#EmailChange').val(data);
            $('#EmailChange2').text(data);
            $('#send_email').show(500);
            enableA();
        }
    });
}
function mailAdminSend() {
    $.post("inc/admin_functions.php", {
        EmailAdmin: $('#EmailChange').val(),
        mailtheme: $('#mailtheme').val(),
        text_mail: $('#text_mail').val()
    }, function(data) {
        alert(data);
    });
}

function CheckUserAvailability(idname) {
    var new_user_col = document.getElementById(idname);
    $.post("inc/admin_functions.php", {
        user_name: $('#' + idname).val()
    }, function(data) {
        if (data === 'no') //если имя не доступно
        {
            new_user_col.setAttribute("style", "color: red; font-weight: bold;");
            $('input[type="submit"]').attr('disabled', 'disabled');
        }
        else
        {
            new_user_col.setAttribute("style", "color: green;");
            $('input[type="submit"]').removeAttr('disabled');
        }

    });
}
function CheckEmailAvailability(idname) {
    var new_user_col = document.getElementById(idname);
    $.post("inc/admin_functions.php", {
        user_email: $('#' + idname).val()
    }, function(data) {
        if (data === 'no') //если имя не доступно
        {
            new_user_col.setAttribute("style", "color: red; font-weight: bold;");
            $('input[type="submit"]').attr('disabled', 'disabled');
        }
        else
        {
            new_user_col.setAttribute("style", "color: green;");
            $('input[type="submit"]').removeAttr('disabled');
        }

    });
}
function ChangePassCab() {
    if ($('#new_login').val() === "") {
        alert('\u041fоле "Логин" не должно быть пустым!');
        return false;
    }
    if ($('#new_pass').val() !== $('#new_pass2').val()) {
        alert('\u041fароли не совпадают!');
        return false;
    }
    return true;
}
function ChangePassAn() {
    if ($('#new_pass').val() !== $('#new_pass2').val()) {
        $('#new_pass').css('background', '#FF9999');
        $('#new_pass2').css('background', '#FF9999');
    } else {
        $('#new_pass').css('background', '#00FF00');
        $('#new_pass2').css('background', '#00FF00');
    }
}
function ChangeBlockUParams(id) {
    $('#ParamsUChange').val(id);
    $.post("inc/admin_functions.php", {
        user_show: parseInt(id, 10)
    }, function(data) {
        $('#LoginUser').val(data.login);
        $('#NameUser').val(data.name);
        $('#SecNameUser').val(data.secname);
        $('#EmailUser').val(data.email);
        //alert(data.user_status);
        if (data.user_status>0) {
            $("#StatusUser [value='1']").attr("selected", "selected");
        }

    }, "json");
    $('#edit_user').show(500);
    enableA();
}
function IpBlock(id) {
    $.get("inc/admin_functions.php", {
        user_id_ip: id
    }, function(data) {
        $('#IpBlock' + id).animate({
            opacity: 0
        }, 500, function() {
            $('#IpBlock' + id).attr('src', data.src);
            $('#IpBlock' + id).animate({
                opacity: 1
            }, 500, function() {
            });
        });
        $('#IpBlock' + id).attr('title', data.title);
    }, "json");
}
function BlockUser(id) {
    $.get("inc/admin_functions.php", {
        user_id_block: id
    }, function(data) {
        $('#BlockUser' + id).attr('src', data.src);
        $('#BlockUser' + id).attr('title', data.title);
    }, "json");
}
function ChangeUserPref() {
    $.post("inc/admin_functions.php", {
        id_user_ch: parseInt($('#ParamsUChange').val(), 10),
        LoginUser: $('#LoginUser').val(),
        NameUser: $('#NameUser').val(),
        SecNameUser: $('#SecNameUser').val(),
        EmailUser: $('#EmailUser').val(),
        PasswordUser: $('#PasswordUser').val(),
        StatusUser: $('#StatusUser').val()
    }, function(data) {
        alert(data);
    });
}
function DeleteUserTR(id) {
    if (window.confirm('\u0412ы действительно хотите удалить пользователя и все его объявления?')) {
        $.post("inc/admin_functions.php", {
            DeleteUserTR: id
        }, function(data) {
            if (data === 'yes') {
                $('#users_tr_' + id).animate({
                    opacity: 0
                }, 1000, function() {
                    $('#users_tr_' + id).hide(500);
                });
            } else {
                alert('\u0412озникла ошибка!');
            }
        });
    }
}
function ModerCategoriesCheck(i) {
    $.post("inc/admin_functions.php", {
        moder_id: parseInt(i, 10)
    }, function(data) {
        $('#ModerCatSelect').html(data);
    });
}
function ChangeModerParams(id) {
    ModerCategoriesCheck(id);
    $('#ParamsModerChange').val(id);
    $.post("inc/admin_functions.php", {
        moder_show: parseInt(id, 10)
    }, function(data) {
        $('#ModerLogin').val(data.login);
        $('#ModerName').val(data.name);
        $('#ModerSecName').val(data.secname);
        $('#ModerEmail').val(data.email);
    }, "json");
    $('#edit_moderator').show(500);
    enableA();
}
function DeleteModerTR(id) {
    if (window.confirm('\u0412ы действительно хотите удалить модератора?')) {
        $.post("inc/admin_functions.php", {
            DeleteModerTR: id
        }, function(data) {
            if (data === 'yes') {
                $('#moder_tr_' + id).animate({
                    opacity: 0
                }, 1000, function() {
                    $('#moder_tr_' + id).hide(500);
                });
            } else {
                alert('\u0412озникла ошибка!');
            }
        });
    }
}
function ChangeBlockCParams(id) {
    $.post("inc/admin_functions.php", {
        ChangeBlockCParams: parseInt(id, 10)
    }, function(data) {
        $('#ParamsChange').val(data.id);
        $('#LoginAdmin').val(data.login);
        $('#NameAdmin').val(data.fname);
        $('#SecNameAdmin').val(data.lname);
        $('#EmailAdmin').val(data.email);
    }, "json");
    $('#edit_admin').show(500);
    enableA();
}
function ChangeAdminPref() {
    $.post("inc/admin_functions.php", {
        ChangeAdminPrefId: parseInt($('#ParamsChange').val(), 10),
        ChangeAdminPrefLogin: $('#LoginAdmin').val(),
        ChangeAdminPrefName: $('#NameAdmin').val(),
        ChangeAdminPrefSName: $('#SecNameAdmin').val(),
        ChangeAdminPrefEmail: $('#EmailAdmin').val(),
        ChangeAdminPrefPass: $('#PasswordAdmin').val()
    }, function(data) {
        alert(data);
    });
}
function BeforeSave() {
    if ($('#NewAdminPassword').val() !== '') {
        return true;
    }
    return false;
}
function DeleteAdminTR(id) {
    if (window.confirm('\u0412ы действительно хотите удалить администратора?')) {
        $.post("inc/admin_functions.php", {
            DeleteAdminTR: id
        }, function(data) {
            if (data === 'yes') {
                $('#admin_tr_' + id).animate({
                    opacity: 0
                }, 1000, function() {
                    $('#admin_tr_' + id).hide(500);
                });
            } else {
                alert('\u0412озникла ошибка!');
            }
        });
    }
}

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
function RemoveIP(obj) {
    if (window.confirm('\u0412ы действительно хотите снять бан с IP?')) {
        $.post("inc/admin_functions.php", {
            RemoveIP: $(obj).children('img').attr('alt')
        },
        function(data)
        {
            if (data === 'yes') {
                $(obj).parent().parent().hide(500);
            } else {
                alert('\u0412озникла ошибка!');
            }
        });
    }
}
function AddBannedIP() {
    $.post("inc/admin_functions.php", {
        AddBannedIP: $('#NewIPBan').val()
    },
    function(data)
    {
        if (data === 'yes') {
            alert('IP \u0430дрес занесён в бан лист!');
        } else {
            alert('\u0412озникла ошибка!');
        }
    });
}
function showText(obj) {
    $(obj).prev('input').prop('checked', true);
    $.post("inc/admin_functions.php", {
        showTextContent: $(obj).prev('input').val()
    },
    function(data)
    {
        $('#open_page .add_text_2').text($(obj).text());
        $('input[name="ContentPageID"]').val($(obj).prev('input').val());
        tinyMCE.get('elm1').setContent(data);
        $('#open_page').slideDown(500);
    });
}
function SaveContentPage() {
    $.post("inc/admin_functions.php", {
        SaveContentPageID: $('input[name="ContentPageID"]').val(),
        SaveContentPageText: tinyMCE.get('elm1').getContent()
    },
    function(data)
    {
        if (data === 'yes') {
            alert('Содержание страницы обновлено!');
        } else {
            alert('\u0412озникла ошибка!');
        }
    });
}

$(document).ready(function() {
    $('input[name="colours"]').click(function() {
        $('.open_content').slideUp(500);
        $('.open_content[ind="' + $(this).val() + '"]').slideDown(500);
    });
    $('.color_tab input').change(function() {
        var param = $(this).attr('param');
        var color1 = $(this).val();
        if (param === '1') {
            var color2 = $('.color_tab input[param="2"]').val();
            $('.bl_color').css('background', color1);
            $('.bl_color').css('background-image', '-webkit-linear-gradient(top, ' + color1 + ', ' + color2 + ')');
            $('.bl_color').css('background-image', '-moz-linear-gradient(top, ' + color1 + ', ' + color2 + ')');
            $('.bl_color').css('background-image', '-ms-linear-gradient(top, ' + color1 + ', ' + color2 + ')');
            $('.bl_color').css('background-image', '-o-linear-gradient(top, ' + color1 + ', ' + color2 + ')');
        }
        if (param === '2') {
            var color2 = $('.color_tab input[param="1"]').val();
            $('.bl_color').css('background', color2);
            $('.bl_color').css('background-image', '-webkit-linear-gradient(top, ' + color2 + ', ' + color1 + ')');
            $('.bl_color').css('background-image', '-moz-linear-gradient(top, ' + color2 + ', ' + color1 + ')');
            $('.bl_color').css('background-image', '-ms-linear-gradient(top, ' + color2 + ', ' + color1 + ')');
            $('.bl_color').css('background-image', '-o-linear-gradient(top, ' + color2 + ', ' + color1 + ')');
        }
        if (param === '3') {
            $('.bl_color_1').css('background', color1);
        }
        if (param === '4') {
            $('.bl_color_2').css('background', color1);
        }
        if (param === '5') {
            $('.bl_color_3').css('background', color1);
        }
        if (param === '6') {
            $('.bl_color_4').css('background', color1);
        }
        if (param === '7') {
            $('.bl_color_5').css('background', color1);
        }
        if (param === '8') {
            $('.bl_color_6').css('background', color1);
        }
        if (param === '9') {
            $('.bl_color_7').css('background', color1);
        }
    });
});

function SaveGradient() {
    $.post("inc/admin_functions.php", {
        SaveGradient1: $('.color_tab input[param="1"]').val(),
        SaveGradient2: $('.color_tab input[param="2"]').val()
    },
    function(data)
    {
        if (data === 'yes') {
            alert('Успешно сохранено!');
        } else {
            alert('\u0412озникла ошибка!');
        }
    });
}

function SaveColors(obj) {
    $.post("inc/admin_functions.php", {
        SaveColorsC: $(obj).closest('div').find('input').val(),
        SaveColorsID: $(obj).closest('div').find('input').attr('param')
    },
    function(data)
    {
        if (data === 'yes') {
            alert('Успешно сохранено!');
        } else {
            alert('\u0412озникла ошибка!');
        }
    });
}

function SaveTariffPacket(obj) {
    if (window.confirm('Вы действительно хотите сохранить изменения?')) {
        $.post("inc/admin_functions.php", {
            SaveTariffPacketID: $(obj).children('img').attr('alt'),
            SaveTariffPacketLockDays: $(obj).closest('tr').find('input[name="lock_days"]').val(),
            SaveTariffPacketUp: $(obj).closest('tr').find('input[name="up"]').val(),
            SaveTariffPacketColor: $(obj).closest('tr').find('select[name="color"]').val(),
            SaveTariffPacketVIP: $(obj).closest('tr').find('select[name="vip"]').val(),
            SaveTariffPacketPrice: $(obj).closest('tr').find('input[name="price"]').val()
        },
        function(data)
        {
            if (data === 'yes') {
                alert('Успешно сохранено!');
            } else {
                alert('\u0412озникла ошибка!');
            }
        });
    }
}

function SaveTariffPriceForAd(obj) {
    if (window.confirm('Вы действительно хотите сохранить изменения?')) {
        $.post("inc/admin_functions.php", {
            SaveTariffPriceForAdID: $(obj).children('img').attr('alt'),
            SaveTariffPriceForAdDays: $(obj).closest('tr').find('input[name="days"]').val(),
            SaveTariffPriceForAdPrice: $(obj).closest('tr').find('input[name="price"]').val()
        },
        function(data)
        {
            if (data === 'yes') {
                alert('Успешно сохранено!');
            } else {
                alert('\u0412озникла ошибка!');
            }
        });
    }
}
function SaveTariffOthers(obj) {
    if (window.confirm('Вы действительно хотите сохранить изменения?')) {
        $.post("inc/admin_functions.php", {
            SaveTariffOthersID: $(obj).children('img').attr('alt'),
            SaveTariffOthersPrice: $(obj).closest('tr').find('input[name="price"]').val()
        },
        function(data)
        {
            if (data === 'yes') {
                alert('Успешно сохранено!');
            } else {
                alert('\u0412озникла ошибка!');
            }
        });
    }
}
function SaveTariffVideo(obj) {
    if (window.confirm('Вы действительно хотите сохранить изменения?')) {
        $.post("inc/admin_functions.php", {
            SaveTariffVideoID: $(obj).children('img').attr('alt'),
            SaveTariffVideoPrice: $(obj).closest('tr').find('input[name="price"]').val(),
            SaveTariffVideoDuration: $(obj).closest('tr').find('input[name="duration"]').val()
        },
        function(data)
        {
            if (data === 'yes') {
                alert('Успешно сохранено!');
            } else {
                alert('\u0412озникла ошибка!');
            }
        });
    }
}
$(document).ready(function() {
    $('#packets input').on('change', function() {
        var input = $(this);
        if (window.confirm('Вы изменили значение. Хотите применить изменения?')) {
            $.post("inc/admin_functions.php", {
                SaveNewPacketID: $(this).attr('ind'),
                SaveNewPacketType: $(this).attr('attr'),
                SaveNewPacketValue: $(this).val()
            },
            function(data)
            {
                if (data === 'yes') {
                    input.css('background', '#aaffaa');
                } else {
                    input.css('background', '#ffaaaa');
                }
            });
        }
    });
    $('#packets select').on('change', function() {
        var select = $(this);
        if (window.confirm('Вы изменили значение. Хотите применить изменения?')) {
            $.post("inc/admin_functions.php", {
                SaveNewPacketID: $(this).attr('ind'),
                SaveNewPacketType: $(this).attr('attr'),
                SaveNewPacketValue: $(this).val()
            },
            function(data)
            {
                if (data === 'yes') {
                    select.css('background', '#aaffaa');
                } else {
                    select.css('background', '#ffaaaa');
                }
            });
        }
    });
});