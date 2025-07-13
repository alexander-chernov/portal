function ShowSearch(action) {
    if (action === 1) {
        d1 = 'none';
        d2 = 'block';
    }
    if (action === 2) {
        d2 = 'none';
        d1 = 'block';
    }
    $('#lupa_plus').css('display', d1);
    $('#lupa_minus').css('display', d2);
    $('#parametr_search').css('display', d2);
}
function mailSend() {
    $.post("inc/admin_functions.php", {
        EmailAdmin: $('#EmailChange').val(),
        mailtheme: $('#mailtheme').val(),
        text_mail: $('#text_mail').val()
    }, function(data)
    {
        alert(data);
    });
}
function ChangeBlockEParams(id) {
    $.post("inc/admin_functions.php", {
        EmailChange: parseInt(id, 10)
    },
    function(data)
    {
        if (data !== 'no') {
            $('#EmailChange').val(data);
            $('#EmailChange2').text(data);
            $('#send_email').show(500);
            enableA();
        }
    });
}
function InfoBlock(id) {
    $('#info_num').text(id);
    $.post("inc/admin_functions.php", {
        InfoBlock: parseInt(id, 10)
    }, function(data)
    {
        $('#info_contact_name').text(data.fname + " " + data.lname + " " + data.oname);
        $('#info_contacts').text(data.contacts);
        $('#info_email').text(data.email);
    }, "json");
    $('#info_obiavlenie_block').show(500);
    enableA();
}
function EditAd(id) {
    $('#edit_num').text(id);
    $.post("inc/admin_functions.php", {
        EditAd: parseInt(id, 10)
    },
    function(data)
    {
        $('#edit_theme').val(data.theme);
        $('select#edit_category option[value="' + data.category + '"]').attr('selected', 'selected');
        $('#edit_text').val(data.text);
    }, "json");
    $('#edit_obiavlenie').show(500);
    enableA();
}
function SaveAd() {
    $.post("inc/admin_functions.php", {
        SaveAdId: parseInt($('#edit_num').text(), 10),
        SaveAdTheme: $('#edit_theme').val(),
        SaveAdCategory: $('#edit_category').val(),
        SaveAdText: $('#edit_text').val()
    },
    function(data)
    {
        alert(data);
    });
}
function DeletePhoto(url) {
    $.post("inc/admin_functions.php", {
        DeletePhoto: url
    },
    function(data)
    {
        if (data === 'yes') {
            $('#PhotoLoad tr img[src$="' + url + '"]').hide(500, function() {
                $(this).closest('tr').remove();
            });
        }
    });
}
function ZoomPhoto(obj) {
    $('#photo_windows').show(500);
    $('.img_windows').attr('src', $(obj).attr('src'));
}
function ShowComments(id) {
    $.post("inc/admin_functions.php", {
        ShowComments: id
    },
    function(data)
    {
        $('#CommentsTable').html(data);
    });
    $('#coment_obiavlenie').show(500);
    enableA();
}
function DeleteComment(id) {
    $.post("inc/admin_functions.php", {
        DeleteComment: id
    },
    function(data)
    {
        if (data === 'yes') {
            alert('\u041aомментарий успешно удалён!');
            $('#com_n' + id).fadeOut(500);
        }
        if (data === 'no') {
            alert('\u0412озникла ошибка при удалении!');
        }
    });
}
function CommentVIP(obj) {
    if ($(obj).attr('src') === '../images/vip_2.png') {
        act = 0;
    }
    if ($(obj).attr('src') === '../images/vip_1.png') {
        act = 1;
    }
    $.post("inc/admin_functions.php", {
        CommentVIP: $(obj).attr('alt'),
        CommentAct: act
    },
    function(data)
    {
        if (data === 'yes') {
            $(obj).fadeOut(500, function() {
                if (act === 1) {
                    $(obj).attr('src', '../images/vip_2.png');
                    $(obj).attr('title', '\u0423брать из VIP - ленты');
                }
                if (act === 0) {
                    $(obj).attr('src', '../images/vip_1.png');
                    $(obj).attr('title', '\u0414обавить в VIP - ленту');
                }
            }).fadeIn(500);
        }
    });
}
function PhotoMainPage(obj) {
    $.post("inc/admin_functions.php", {
        PhotoMainPage: $(obj).attr('alt')
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
function CommentPaid(obj) {
    if ($(obj).attr('src') === '../images/spec_2.png') {
        act = 0;
    }
    if ($(obj).attr('src') === '../images/spec_1.png') {
        act = 1;
    }
    $.post("inc/admin_functions.php", {
        CommentPaid: $(obj).attr('alt'),
        CommentPAct: act
    },
    function(data)
    {
        if (data === 'yes') {
            $(obj).fadeOut(500, function() {
                if (act === 1) {
                    $(obj).attr('src', '../images/spec_2.png');
                    $(obj).attr('title', '\u0423брать из Платной ленты');
                }
                if (act === 0) {
                    $(obj).attr('src', '../images/spec_1.png');
                    $(obj).attr('title', '\u0414обавить в Платную ленту');
                }
            }).fadeIn(500);
        }
    });
}
function PhotoUp(obj) {
    $.post("inc/admin_functions.php", {
        PhotoUp: $(obj).attr('alt')
    },
    function(data)
    {
        if (data === 'yes') {
            alert('\u041eбъявление успешно поднято!');
        }
        if (data === 'no') {
            alert('\u041fроизошла ошибка!');
        }
    });
}
function BlockIP(ip) {
    $.post("inc/admin_functions.php", {
        BlockIP: ip
    },
    function(data)
    {
        if (data === '1') {
            alert('IP \u0434обавлен в базу!');
        }
        if (data === '2') {
            alert('IP \u0443же забанен!');
        }
        if (data === '0') {
            alert('\u041fроизошла ошибка!');
        }
    });
}
function DeleteAd(id) {
    if (window.confirm('Вы действительно хотите удалить объявление?')) {
        $.post("inc/admin_functions.php", {
            DeleteAd: id
        },
        function(data)
        {
            if (data === 'yes') {
                $('#tr_ad_num' + id).hide(500);
            }
        });
    }
}
function PhotosPanel(id) {
    $.post("inc/admin_functions.php", {
        PhotosPanel: parseInt(id, 10)
    },
    function(data)
    {
        var table = document.getElementById('PhotoLoad');
        table.innerHTML = '';
        for (i = 0; i < data.length; i++) {
            new_tr_e = document.createElement('tr');
            new_tr = table.appendChild(new_tr_e);
            if (i % 2 === 0) {
                new_tr.setAttribute('style', 'background: #f0f4f4;');
            }
            new_td_e = document.createElement('td');
            new_td = new_tr.appendChild(new_td_e);
            new_td.setAttribute('style', 'width: 80px;');
            new_img_e = document.createElement('img');
            new_img = new_td.appendChild(new_img_e);
            if (data[i].link.match(/video/)) {
                new_img.setAttribute('src', '../../' + data[i].link);
            } else {
                new_img.setAttribute('src', '../' + data[i].link);
            }
            new_img.setAttribute('class', 'img_ob');
            new_img.setAttribute('alt', '');
            new_img.setAttribute('onclick', 'ZoomPhoto(this)');
            new_td_e2 = document.createElement('td');
            new_td2 = new_tr.appendChild(new_td_e2);
            new_td2.setAttribute('style', 'width: 150px;');
            new_img_e2 = document.createElement('img');
            new_img2 = new_td2.appendChild(new_img_e2);
            new_img2.setAttribute('src', '../images/delete.png');
            new_img2.setAttribute('title', '\u0423далить фото');
            new_img2.setAttribute('alt', '');
            new_img2.setAttribute('style', 'cursor: pointer;');
            new_img2.setAttribute('onclick', 'DeletePhoto(\'' + data[i].link + '\')');
        }
    }, "json");
    $('#photo_obiavlenie').show(500);
    enableA();
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
function PhotoColor(obj) {
    $.post("inc/admin_functions.php", {
        PhotoColor: $(obj).attr('alt')
    }
    , function(data)
    {
        if (data === 'yes') {
            if ($(obj).attr('src') === '../images/no_light.png') {
                $(obj).fadeOut(500, function() {
                    $(obj).attr('src', '../images/color_light.png');
                    $(obj).attr('title', 'Добавить выделение цветом');
                }).fadeIn(500);
            } else {
                $(obj).fadeOut(500, function() {
                    $(obj).attr('src', '../images/no_light.png');
                    $(obj).attr('title', 'Убрать выделение цветом');
                }).fadeIn(500);
            }
        } else {
            alert('Возникла ошибка!');
        }
    });
}
function AddDaysPhoto(id) {
    $('#PhotoForAddDays').val(id);
    $.post("inc/admin_functions.php", {
        PhotoAddDays: $('#PhotoForAddDays').val()
    }, function(data) {
        $('#LastDays').text(data);
    });
    $('#AddDayBlock').show(500);
    enableA();
}
function AddDaysSubmit() {
    $.post("inc/admin_functions.php", {
        PhotoAddDaysID: $('#PhotoForAddDays').val(),
        PhotoAddDaysSubmit: parseInt($('#DaysForAddDays').val(), 10)
    }, function(data) {
        alert(data);
    });
    AddDaysPhoto($('#PhotoForAddDays').val());
}