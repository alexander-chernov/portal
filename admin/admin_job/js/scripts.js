function SendEmail(email) {
    $('#EmailChange').text(email);
    $('#send_email').show(500);
    enableA();
}
function mailSend() {
    $.post("inc/admin_functions.php", {
        EmailAdmin: $('#EmailChange').text(),
        mailtheme: $('#mailtheme').val(),
        text_mail: $('#text_mail').val()
    }, function(data) {
        alert(data);
    });
}
function ShowInfo(id) {
    $.post("inc/admin_functions.php", {
        ShowInfo: id
    }, function(data) {
        $('#salary_min').text(data.salary_min);
        $('#salary_max').text(data.salary_max);
        $('#age_min').text(data.age_min);
        $('#age_max').text(data.age_max);
        $('#sex').text(data.sex);
        $('#education_t').text(data.education_t);
        $('#education').text(data.education);
        $('#experience').text(data.experience);
        $('#schedule').text(data.schedule);
        $('#req_text').text(data.req_text);
        $('#organization').text(data.organization);
        $('#contact_name').text(data.contact_name);
        $('#contact_phone').text(data.contact_phone);
        $('#email').text(data.email);
        $('#info_job_treb').show(500);
        enableA();
    }, "json");
}
function DisEn(id, obj) {
    $.post("inc/admin_functions.php", {
        DisEn: id
    }, function(data) {
        if (data === 'show') {
            $('#job_tr_' + id + ' [class="style_4_2"]').text('\u0410ктивно');
            $('#job_tr_' + id + ' [class="style_4_2"]').attr('class', 'style_4_1');
            $(obj).attr('src', '../images/disable_1.png');
            $(obj).attr('title', '\u0421крыть объявление');
        }
        if (data === 'hide') {
            $('#job_tr_' + id + ' [class="style_4_1"]').text('\u0421крыто');
            $('#job_tr_' + id + ' [class="style_4_1"]').attr('class', 'style_4_2');
            $(obj).attr('src', '../images/enable.png');
            $(obj).attr('title', '\u041fоказать объявление');
        }
    });
}
function UpJob(id) {
    $.post("inc/admin_functions.php", {
        UpJob: id
    }, function(data) {
        if (data === 'yes') {
            alert('\u041eбъявление успешно поднято!');
        }
    });
}
function DeleteJob(id) {
    if (confirm('\u0412ы уверены!')) {
        $.post("inc/admin_functions.php", {
            DeleteJob: id
        }, function(data) {
            if (data === 'yes') {
                $('#job_tr_' + id).hide(500);
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