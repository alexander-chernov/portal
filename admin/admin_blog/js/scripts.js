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
function BlogChange(id) {
    $.post("inc/admin_functions.php", {
        BlogChange: id
    },
    function(data)
    {
        $('#edit_blog_table').html(data);
    });
    $('#edit_blog').show(500);
    enableA();
}
function ChangeImageBlog(url) {
    $.post("inc/admin_functions.php", {
        ChangeImageBlogID: $('#BlogID').val(),
        ChangeImageBlogURL: url
    },
    function(data)
    {
        if (data === 'yes') {
            $('#blog_tr_' + $('#BlogID').val() + ' img[class="img_ob"]').attr('src', url);
        }
    });
}
function SaveBlogChange() {
    $.post("inc/admin_functions.php", {
        SaveBlogChangeID: $('#BlogID').val(),
        SaveBlogChangeNAME: $('#BlogName').val(),
        SaveBlogChangeBRIEF: $('#BlogBrief').val(),
        SaveBlogChangeCAT: $('#BlogCategoryChange').val()
    },
    function(data)
    {
        if (data === 'yes') {
            alert('\u0423спешно сохранено!');
        } else {
            alert('\u0412озникла ошибка!');
        }
    });
}
function BlogMainPage(obj) {
    $.post("inc/admin_functions.php", {
        BlogMainPage: $(obj).attr('alt')
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
function BlogCategoryNameChange(id) {
    $.post("inc/admin_functions.php", {
        BlogCategoryNameChange: id
    },
    function(data)
    {
        $('#BlogCategoryName').val(data);
    });
    $('#BlogCategoryID').val(id);
    $('#edit_rub').show();
    enableA();
}
function BlogCategorySave() {
    $.post("inc/admin_functions.php", {
        BlogCategorySaveID: $('#BlogCategoryID').val(),
        BlogCategorySaveNAME: $('#BlogCategoryName').val()
    },
    function(data)
    {
        if (data === 'yes') {
            $('#blog_org_' + $('#BlogCategoryID').val()).text($('#BlogCategoryName').val());
            alert('\u0423спешно сохранено!');
        } else {
            alert('\u0412озникла ошибка!');
        }
    });
}
function DeleteBlog(id) {
    if (window.confirm('\u0412ы действительно хотите удалить статью и все вложения в неё?')) {
        $.post("inc/admin_functions.php", {
            DeleteBlog: id
        },
        function(data)
        {
            if (data === 'yes') {
                $('#blog_tr_' + id).hide();
            } else {
                alert('\u0412озникла ошибка!');
            }
        });
    }
}
function DeleteCategory(id) {
    if (window.confirm('\u0412ы действительно хотите удалить категорию и все статьи, входящие в неё?')) {
        $.post("inc/admin_functions.php", {
            DeleteCategory: id
        },
        function(data)
        {
            if (data === 'yes') {
                $('#blog_org_' + id).parent().parent().hide();
            } else {
                alert('\u0412озникла ошибка!');
            }
        });
    }
}
function EnableBlogBlock(obj) {
    if (window.confirm('Отобразить блок "Статьи" вместо блока "Веб-камеры"?')) {
        $.post("inc/admin_functions.php", {
            EnableBlogBlock: 1
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

function BlogState(obj) {
    $.post("inc/admin_functions.php", {
        BlogState: $(obj).attr('alt')
    }, function(data) {
        if (data === 'show') {
            $(obj).fadeOut(500, function() {
                $(obj).closest('tr').find('.style_4_2').text('\u0410ктивно');
                $(obj).closest('tr').find('.style_4_2').attr('class', 'style_4_1');
                $(obj).attr('src', '../images/disable_1.png');
                $(obj).attr('title', 'Скрыть статью');
            }).fadeIn(500);
        }
        if (data === 'hide') {
            $(obj).fadeOut(500, function() {
                $(obj).closest('tr').find('.style_4_1').text('\u0421крыто');
                $(obj).closest('tr').find('.style_4_1').attr('class', 'style_4_2');
                $(obj).attr('src', '../images/enable.png');
                $(obj).attr('title', 'Показать статью');
            }).fadeIn(500);
        }
    });
}