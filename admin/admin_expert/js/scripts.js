function ExpertCategoriesCheck(i) {
    $.post("inc/admin_functions.php", {
        expert_id: parseInt(i, 10)
    }, function(data)
    {
        $('#ExpertCatSelect').html(data);
    });
}
function ChangeExpertParams(id) {
    ExpertCategoriesCheck(id);
    $('#ChangeExID').val(id);
    $.post("inc/admin_functions.php", {
        expert_show: parseInt(id, 10)
    }, function(data)
    {
        $('#ChangeExEmail').val(data.email);
        $('#ChangeExBrief').val(data.brief);
        $('#ChangeExAddress option[value="' + data.address + '"]').prop('selected', true);
        $('#ChangeExPhone').val(data.phone);
        $('#ChangeExSite').val(data.site);
        $('#ChangeExTheme').val(data.theme);
        $('#ChangeExHeader').val(data.header);
        $('#ChangeExDescription').val(data.descr);
    }, "json");
    $('#edit_expert').show(500);
    enableA();
}

function SaveExpertParams() {
    var checked = [];
    $('input[name="ex[]"]:checked').each(function() {
        checked.push($(this).val());
    });
    $.post("inc/admin_functions.php", {
        SaveChangeExID: $('#ChangeExID').val(),
        SaveChangeExEmail: $('#ChangeExEmail').val(),
        SaveChangeExTheme: $('#ChangeExTheme').val(),
        SaveChangeExBrief: $('#ChangeExBrief').val(),
        SaveChangeExHeader: $('#ChangeExHeader').val(),
        SaveChangeExDescription: $('#ChangeExDescription').val(),
        SaveChangeExAddress: $('#ChangeExAddress').val(),
        SaveChangeExPhone: $('#ChangeExPhone').val(),
        SaveChangeExSite: $('#ChangeExSite').val(),
        SaveChangeCategories: checked
    }, function(data) {
        if (data === 'yes') {
            alert('\u0423спешно сохранено!');
        } else {
            alert('\u0412озникла ошибка!');
        }
    });
}

function ChangeBlockEParams(id) {
    $.post("inc/admin_functions.php", {
        EmailChange: parseInt(id, 10)
    }, function(data)
    {
        if (data !== 'no') {
            $('#EmailChange').val(data);
            $('#EmailChange2').text(data);
            $('#send_email').show(500);
            enableA();
        }
    });
}
function ShowExpertInfo(id) {
    $.post("inc/admin_functions.php", {
        ShowExpertInfo: id
    }
    , function(data)
    {
        $('#InfoID').text(id);
        $('#IndoDate').text(data.rdate);
        $('#InfoDaysLast').text(data.ldate);
        $('#InfoTheme').text(data.theme);
        $('#InfoPhone').text(data.phone);
        $('#InfoAddress').text(data.street + " " + data.hnum);
        $('#InfoEmail').text(data.email);
        $('#InfoSite').text(data.site);
    }, "json");
    $('#info_expert').show(500);
    enableA();
}
function UpExpert(id) {
    $.post("inc/admin_functions.php", {
        UpExpert: id
    }
    , function(data)
    {
        if (data === 'yes') {
            alert('Эксперт успешно поднят!');
        } else {
            alert('Возникла ошибка!');
        }
    });
}
function PasswordEdit(id) {
    $('#PasswID').val(id);
    $('#pass_expert').show(500);
    enableA();
}
function StringComp(str1, str2) {
    if (str1 === str2) {
        return true;
    }
    return false;
}
function PasswordCorrect() {
    if (StringComp($('#Pass1').val(), $('#Pass2').val())) {
        $('#Pass1').css('background', '#AAFFAA');
        $('#Pass2').css('background', '#AAFFAA');
    } else {
        $('#Pass1').css('background', '#FFAAAA');
        $('#Pass2').css('background', '#FFAAAA');
    }
}
function PassSave() {
    if ($('#Pass1').val() === $('#Pass2').val()) {
        $.post("inc/admin_functions.php", {
            PassSave: $('#PasswID').val(),
            NewPass: $('#Pass2').val()
        },
        function(data)
        {
            if (data === 'yes') {
                alert('\u041fароль успешно обновлен!');
            } else {
                alert('\u041fроизошла ошибка!');
            }
        });
    } else {
        alert('\u041fароли не совпадают!');
    }
}
function ChangeState(obj) {
    $.post("inc/admin_functions.php", {
        ChangeState: $(obj).attr('alt')
    }
    , function(data)
    {
        if (data === 'yes') {
            var p = $(obj).closest('tr').find('.style_4_1,.style_4_2');
            if ($(obj).attr('src') === '../images/disable_1.png') {
                $(obj).fadeOut(500, function() {
                    $(obj).attr('src', '../images/enable.png');
                }).fadeIn(500);
                p.fadeOut(500, function() {
                    p.text('Скрыто');
                    p.attr('class', 'style_4_2');
                }).fadeIn(500);
            } else {
                $(obj).fadeOut(500, function() {
                    $(obj).attr('src', '../images/disable_1.png');
                }).fadeIn(500);
                p.fadeOut(500, function() {
                    p.text('Активно');
                    p.attr('class', 'style_4_1');
                }).fadeIn(500);
            }
        } else {
            alert('Возникла ошибка!');
        }
    });
}
function OnMainPage(obj) {
    $.post("inc/admin_functions.php", {
        OnMainPage: $(obj).attr('alt')
    }
    , function(data)
    {
        if (data === 'yes') {
            if ($(obj).attr('src') === '../images/not_main.png') {
                $(obj).fadeOut(500, function() {
                    $(obj).attr('src', '../images/on_main.png');
                }).fadeIn(500);
            } else {
                $(obj).fadeOut(500, function() {
                    $(obj).attr('src', '../images/not_main.png');
                }).fadeIn(500);
            }
        } else {
            alert('Возникла ошибка!');
        }
    });
}

function DeleteExpert(obj) {
    if (window.confirm('Вы действительно хотите удалить эксперта и все его ответы?')) {
        $.post("inc/admin_functions.php", {
            DeleteExpert: $(obj).attr('alt')
        }
        , function(data)
        {
            alert(data)
            if (data === 'yes') {
                $(obj).closest('tr').hide(500);
            } else {
                alert('Возникла ошибка!');
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
function CategoriesRedakt(obj) {
    $.post("inc/admin_functions.php", {
        CategoriesRedakt: $(obj).children('img').attr('alt')
    },
    function(data)
    {
        $('#edit_rub input').val(data);
        jQuery.data(document.body, 'edit_rub', {id: $(obj).children('img').attr('alt'), name: data});
    });
    $('#edit_rub').show(500);
    enableA();
}
function CategoriesRedaktSubmit() {
    $.post("inc/admin_functions.php", {
        CategoriesRedaktSubmitID: jQuery.data(document.body, 'edit_rub').id,
        CategoriesRedaktSubmitText: $('#edit_rub input').val()
    },
    function(data)
    {
        if (data === 'yes') {
            $('p:contains("' + jQuery.data(document.body, 'edit_rub').name + '")').first().text($('#edit_rub input').val());
        } else {
            alert('\u0412озникла ошибка!');
        }
    });
}
function DeleteCategoryExp(obj) {
    if (window.confirm('Вы действительно хотите удалить рубрику?')) {
        $.post("inc/admin_functions.php", {
            DeleteCategoryExp: $(obj).children('img').attr('alt')
        },
        function(data)
        {
            if (data === 'yes') {
                $(obj).closest('tr').hide(500);
            } else {
                alert('\u0412озникла ошибка!');
            }
        });
    }
}
function ChangeAvatarWindow(obj) {
    $('#avatar_expert').show(500);
    enableA();
}