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
    $('#wind1').show();
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
    $('#info_baner_block').show();
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
    $('#time_baner_block').show();
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
    $('#time_baner_block').show();
}
function ShowNewCategory() {
    $('#add_new_rub').show();
    enableA();
}
function ChangeCategoryName(obj) {
    $.ajax({
        type: "POST",
        url: "inc/admin_functions.php",
        data: "ChangeCategoryName=" + $(obj).children('img').attr('alt'),
        success: function(data) {
            $('#edit_rub input').val(data);
            $('#edit_rub').show();
            enableA();
            jQuery.data(document.body, 'edit_rub', {
                id: $(obj).children('img').attr('alt'),
                name: data
            });
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}
function SaveCategoryName() {
    $.ajax({
        type: "POST",
        url: "inc/admin_functions.php",
        data: {
            SaveCategoryName: $('#edit_rub input').val(),
            SaveCategoryNameID: jQuery.data(document.body, 'edit_rub').id
        },
        success: function(data) {
            if (data === 'yes') {
                $('p:contains("' + jQuery.data(document.body, 'edit_rub').name + '")').text($('#edit_rub input').val());
                alert('Название рубрики успешно изменено!');
                jQuery.data(document.body, 'edit_rub').name = $('#edit_rub input').val();
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}
function AddNewCategory() {
    var tr = $('<tr>', {'style': 'background: #f0f4f4;'});
    var td1 = $('<td>');
    var td2 = $('<td>');
    var td3 = $('<td>');
    var p1 = $('<p>', {'class': 'style_4'});
    var p2 = $('<p>', {'class': 'style_4'});
    var space = $('<span>');
    var a1 = $('<a>', {'class': 'a_1', 'onclick': 'ChangeCategoryName(this);'});
    var a2 = $('<a>', {'class': 'a_1', 'onclick': 'DeleteCategory(this);'});
    $.ajax({
        type: "POST",
        url: "inc/admin_functions.php",
        data: {
            AddNewCategory: $('#add_new_rub input').val()
        },
        success: function(data) {
            var img1 = $('<img>', {'src': '../images/edit.png', 'title': 'Редактировать название рубрики', 'alt': data});
            var img2 = $('<img>', {'src': '../images/delete.png', 'title': 'Удалить рубрику', 'alt': data});
            tr.appendTo($('#CategoriesTable'));
            td1.appendTo(tr);
            td2.appendTo(tr);
            td3.appendTo(tr);
            p1.appendTo(td1);
            p2.appendTo(td2);
            a1.appendTo(td3);
            space.appendTo(td3);
            space.text(' ');
            a2.appendTo(td3);
            img1.appendTo(a1);
            img2.appendTo(a2);
            p1.text($('#add_new_rub input').val());
            p2.text('0');
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}
function DeleteCategory(obj) {
    if (window.confirm('Вы действительно хотите удалить рубрику и все входящие в неё подрубрики?')) {
        $.ajax({
            type: "POST",
            url: "inc/admin_functions.php",
            data: {
                DeleteCategory: $(obj).children('img').attr('alt')
            },
            success: function(data) {
                if (data === 'yes') {
                    $(obj).closest('tr').hide(500);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("\u0412озникла ошибка!");
            }
        });
    }
}
function ChangeSubcategoryName(obj) {
    $.ajax({
        type: "POST",
        url: "inc/admin_functions.php",
        data: "ChangeSubcategoryName=" + $(obj).children('img').attr('alt'),
        success: function(data) {
            $('#edit_pod_rub input').val(data);
            $('#edit_pod_rub').show();
            enableA();
            jQuery.data(document.body, 'edit_pod_rub', {
                id: $(obj).children('img').attr('alt'),
                name: data
            });
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}
function SaveSubcategoryName() {
    $.ajax({
        type: "POST",
        url: "inc/admin_functions.php",
        data: {
            SaveSubcategoryName: $('#edit_pod_rub input').val(),
            SaveSubcategoryNameID: jQuery.data(document.body, 'edit_pod_rub').id
        },
        success: function(data) {
            if (data === 'yes') {
                $('p:contains("' + jQuery.data(document.body, 'edit_pod_rub').name + '")').text($('#edit_pod_rub input').val());
                alert('Название рубрики успешно изменено!');
                jQuery.data(document.body, 'edit_pod_rub').name = $('#edit_pod_rub input').val();
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}
function AddNewSubcategory() {
    var tr = $('<tr>', {'style': 'background: #f0f4f4;'});
    var td1 = $('<td>');
    var td2 = $('<td>');
    var td3 = $('<td>');
    var td4 = $('<td>');
    var p1 = $('<p>', {'class': 'style_4'});
    var p2 = $('<p>', {'class': 'style_4'});
    var p3 = $('<p>', {'class': 'style_4'});
    var space = $('<span>');
    var a1 = $('<a>', {'class': 'a_1', 'onclick': 'ChangeSubcategoryName(this);'});
    var a2 = $('<a>', {'class': 'a_1', 'onclick': 'DeleteSubcategory(this);'});
    $.ajax({
        type: "POST",
        url: "inc/admin_functions.php",
        data: {
            AddNewSubcategory: $('#add_new_pod_rub input').val(),
            AddNewSubcategoryCAT: $('#add_new_pod_rub select').val()
        },
        success: function(data) {
            var img1 = $('<img>', {'src': '../images/edit.png', 'title': 'Редактировать название подрубрики', 'alt': data});
            var img2 = $('<img>', {'src': '../images/delete.png', 'title': 'Удалить подрубрику', 'alt': data});
            tr.appendTo($('#SubcategoriesTable'));
            td1.appendTo(tr);
            td2.appendTo(tr);
            td3.appendTo(tr);
            td4.appendTo(tr);
            p1.appendTo(td1);
            p2.appendTo(td2);
            p3.appendTo(td3);
            a1.appendTo(td4);
            space.appendTo(td4);
            space.text(' ');
            a2.appendTo(td4);
            img1.appendTo(a1);
            img2.appendTo(a2);
            p1.text($('#add_new_pod_rub input').val());
            p2.text($('#add_new_pod_rub select option:selected').text());
            p3.text('0');
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}
function DeleteSubcategory(obj) {
    if (window.confirm('Вы действительно хотите удалить подрубрику?')) {
        $.ajax({
            type: "POST",
            url: "inc/admin_functions.php",
            data: {
                DeleteSubcategory: $(obj).children('img').attr('alt')
            },
            success: function(data) {
                if (data === 'yes') {
                    $(obj).closest('tr').hide(500);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("\u0412озникла ошибка!");
            }
        });
    }
}
function ShowNewSubcategory() {
    $('#add_new_pod_rub').show();
    enableA();
}
function ShowEmailWindow(obj) {
    $.ajax({
        type: "POST",
        url: "inc/admin_functions.php",
        data: {
            ShowEmailWindow: $(obj).children('img').attr('alt')
        },
        success: function(data) {
            $('#email_address').html(data);
            $('#send_email').show();
            enableA();
            jQuery.data(document.body, 'email_address', {id: $(obj).children('img').attr('alt')});
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}
function SendEmail() {
    var theme = $('#send_email input').val();
    var text = $('#send_email textarea').val();
    $.ajax({
        type: "POST",
        url: "inc/admin_functions.php",
        data: {
            SendEmailText: text,
            SendEmailTheme: theme,
            SendEmailID: jQuery.data(document.body, 'email_address').id
        },
        success: function(data) {
            if (data === 'yes') {
                alert('Письмо успешно отправлено!');
            } else {
                alert('Возникла ошибка!');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}
function ShowSiteChange(obj) {
    $.post("inc/admin_functions.php",
            {
                ShowSiteChange: $(obj).children('img').attr('alt')
            }, function(data)
    {
        $('#site_name').val(data.name);
        $('#site_url').val(data.url);
        $('#site_description').val(data.description);
        $('#site_avatar').attr('src', data.avatar);
        $('#site_contact_name').val(data.c_name);
        $('#site_contact_phone').val(data.c_phone);
        $('#site_email').val(data.email);
        $('#edit_sites').show();
        enableA();
        jQuery.data(document.body, 'edit_sites', {id: $(obj).children('img').attr('alt')});
    }, "json");
}
function SiteChangeSubmit() {
    $.ajax({
        type: "POST",
        url: "inc/admin_functions.php",
        data: {
            SiteChangeName: $('#site_name').val(),
            SiteChangeURL: $('#site_url').val(),
            SiteChangeDescr: $('#site_description').val(),
            SiteChangeCName: $('#site_contact_name').val(),
            SiteChangeCPhone: $('#site_contact_phone').val(),
            SiteChangeEmail: $('#site_email').val(),
            SiteChangeID: jQuery.data(document.body, 'edit_sites').id
        },
        success: function(data) {
            if (data === 'yes') {
                alert('Успешно сохранено!');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}
function ChangeSiteState(obj) {
    $.ajax({
        type: "POST",
        url: "inc/admin_functions.php",
        data: {
            ChangeSiteState: $(obj).children('img').attr('alt')
        },
        success: function(data) {
            if (data === 'state0') {
                var p = $(obj).closest('tr').find('p:contains("Размещен")');
                $(obj).children('img').fadeOut(250, function() {
                    $(obj).children('img').attr({'src': '../images/disable_1.png', 'title': 'Разместить сайт'});
                }).fadeIn(250);
                p.fadeOut(250, function() {
                    p.attr('class', 'style_4_2');
                    p.text('Скрыт');
                }).fadeIn(250);
            }
            if (data === 'state1') {
                var p = $(obj).closest('tr').find('p:contains("Скрыт")');
                $(obj).children('img').fadeOut(250, function() {
                    $(obj).children('img').attr({'src': '../images/enable.png', 'title': 'Скрыть сайт'});
                }).fadeIn(250);
                p.fadeOut(250, function() {
                    p.attr('class', 'style_4_1');
                    p.text('Размещен');
                }).fadeIn(250);
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}
function ShowNewSiteWindow() {
    $('#add_sites').show();
    enableA();
}
function ShowSitePhotos(obj) {
    $.ajax({
        type: "POST",
        url: "inc/admin_functions.php",
        data: {
            ShowSitePhotos: $(obj).children('img').attr('alt')
        },
        success: function(data) {
            $('#photo_sites img.img_ob').attr('src', data);
            $('#photo_sites').show();
            enableA();
            jQuery.data(document.body, 'photo_sites', {id: $(obj).children('img').attr('alt')});
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}
function CreateNewSite() {
    $.ajax({
        type: "POST",
        url: "inc/admin_functions.php",
        data: {
            CreateNewSiteName: $('#newsite_name').val(),
            CreateNewSiteURL: $('#newsite_url').val(),
            CreateNewSiteDescr: $('#newsite_descr').val(),
            CreateNewSiteCName: $('#newsite_cname').val(),
            CreateNewSiteCPhone: $('#newsite_cphone').val(),
            CreateNewSiteEmail: $('#newsite_email').val()
        },
        success: function(data) {
            if (data === 'enter') {
                alert('Заполните обязательные поля!');
            } else {
                $('#SiteTable').html(data);
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}
function DeleteSite(obj) {
    if (window.confirm('Вы действительно хотите удалить сайт?')) {
        $.ajax({
            type: "POST",
            url: "inc/admin_functions.php",
            data: {
                DeleteSite: $(obj).children('img').attr('alt')
            },
            success: function(data) {
                if (data === 'yes') {
                    $(obj).closest('tr').hide(500);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("\u0412озникла ошибка!");
            }
        });
    }
}
function ChangeAvatarSite(url) {
    $.ajax({
        type: "POST",
        url: "inc/admin_functions.php",
        data: "ChangeAvatarSiteID=" + jQuery.data(document.body, 'photo_sites').id + "&ChangeAvatarSiteURL=" + url,
        success: function() {
            $('.img_ob[alt="' + jQuery.data(document.body, 'photo_sites').id + '"]').attr('src', url);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}
function ShowSiteCategories(obj) {
    $.ajax({
        type: "POST",
        url: "inc/admin_functions.php",
        data: {
            ShowSiteCategories: $(obj).children('img').attr('alt')
        },
        success: function(data) {
            $('#t_c').html(data);
            $('#site_categories').show();
            enableA();
            jQuery.data(document.body, 'site_categories', {id: $(obj).children('img').attr('alt')});
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}
function DeleteSiteCategory(obj) {
    if (window.confirm('Вы действительно хотите открепить сайт от подрубрики?')) {
        $.ajax({
            type: "POST",
            url: "inc/admin_functions.php",
            data: {
                DeleteSiteCategory: $(obj).children('img').attr('alt'),
                DeleteSiteCategoryP: jQuery.data(document.body, 'site_categories').id
            },
            success: function(data) {
                if (data === 'yes') {
                    $(obj).closest('tr').hide(500);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("\u0412озникла ошибка!");
            }
        });
    }
}
function ReloadSC() {
    $.ajax({
        type: "POST",
        url: "inc/admin_functions.php",
        data: {
            ReloadSC: $('#cat_add').val()
        },
        success: function(data) {
            $('#sub_cat_add').html(data);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}
function AddSubcategoryToSite() {
    $.ajax({
        type: "POST",
        url: "inc/admin_functions.php",
        data: {
            AddSubcategoryToSite: $('#sub_cat_add').val(),
            AddSubcategoryToSiteP: jQuery.data(document.body, 'site_categories').id
        },
        success: function(data) {
            $('#t_c').html(data);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}