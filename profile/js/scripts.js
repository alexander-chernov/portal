function SearchAddr(obj) {
    if ($(obj).val().length > 1) {
        $.ajax({
            type: "POST",
            url: "inc/ajax.php",
            data: "SearchAddress=" + $(obj).val(),
            success: function(data) {
                $('#select_address').html(data);
                DistrMass($('#final_address').val());
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("\u0412озникла ошибка!");
            }
        });
    } else {
        $('#address_choise').hide(500);
    }
}
function DistrMass(num) {
    $.post("inc/ajax.php", {
        DistrMass: num
    }, function(data)
    {
        if (data.district) {
            $('#district').text(data.district);
        } else {
            $('#district').text('');
        }
        if (data.massive) {
            $('#massive').text(data.massive);
        } else {
            $('#massive').text('');
        }
    }, "json");
    if ($('#address_choise').attr('size') > 1) {
        $('#address_choise').show(500);
    }
    if ($('#address_choise').attr('size') === "1") {
        $('#final_address').val($('#address_choise').val());
    }
}
function ChangeAddr(obj) {
    DistrMass($(obj).attr('ind'));
    $('#final_address').val($(obj).attr('ind'));
    $('#address_input').val($(obj).text());
    $('#address_choise').hide(500);
    if ($('#final_address').val() !== "") {
        $('#address_input').css('background', '#b1e0ff');
    }
}
function convInt(obj) {
    if ($(obj).val().length > 0) {
        $(obj).val(parseInt($(obj).val(), 10));
    } else {
        $(obj).val(0);
    }
    if (isNaN($(obj).val())) {
        $(obj).val(0);
    }
}
function AdSubmit() {
    if ($('#final_address').val() === "" ||
            $('#final_address').val() === 0 ||
            $('input[type="text"][name="price"]').val().trim() === "" ||
            $('input[type="text"][name="price"]').val() === 0 ||
            $('input[type="text"][name="contact_name"]').val().trim() === "" ||
            $('input[type="text"][name="contacts"]').val().trim() === "") {
        return false;
    }
    return true;
}
function RequireInput(obj) {
    if ($(obj).val().trim().length > 0 && $(obj).val() !== 0) {
        $(obj).css('background', '#b1e0ff');
    } else {
        $(obj).css('background', '#ff9999');
    }
}
function SetMainPhoto(obj) {
    $('input[name="priority"]').remove();
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "SetMainPhoto=" + $(obj).closest('div').next('input').val(),
        success: function(data) {
            if (data === 'yes') {
                alert('Главная фотография изменена!');
            }
            if (data === 'not_yet') {
                var input = $('<input>', {
                    'type': 'hidden',
                    'value': $(obj).closest('div').next('input').val(),
                    'name': 'priority'
                });
                input.appendTo($(obj).closest('span'));
                alert('Главная фотография изменена!');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}
function DeleteThisPhoto(obj) {
    if (window.confirm('Вы уверены, что хотите удалить фотографию?')) {
        $.ajax({
            type: "POST",
            url: "inc/ajax.php",
            data: "DeleteThisPhoto=" + $(obj).closest('div').next('input').val(),
            success: function(data) {
                if (data === 'yes') {
                    $(obj).closest('span').hide(500);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("\u0412озникла ошибка!");
            }
        });
    }
}
function SetMainPhoto_p(obj) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "SetMainPhoto_p=" + $(obj).closest('div').find('input').val(),
        success: function(data) {
            if (data === 'yes') {
                alert('Главная фотография изменена!');
            }
            if (data === 'not_yet') {
                var input = $('<input>', {
                    'type': 'hidden',
                    'value': $(obj).closest('div').find('input').val(),
                    'name': 'priority'
                });
                input.appendTo($(obj).closest('div'));
                alert('Главная фотография изменена!');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}
function DeleteThisPhoto_p(obj) {
    if (window.confirm('Вы уверены, что хотите удалить фотографию?')) {
        $.ajax({
            type: "POST",
            url: "inc/ajax.php",
            data: "DeleteThisPhoto_p=" + $(obj).closest('div').find('input').val(),
            success: function(data) {
                if (data === 'yes') {
                    $(obj).closest('div').hide(500);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("\u0412озникла ошибка!");
            }
        });
    }
}
function LoadImmovablePhoto(url) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "LoadImmovablePhotoID=" + $('input[name="Action"]').val() + "&LoadImmovablePhotoURL=" + url,
        success: function() {
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}
function LoadPhotodeskPhoto(url) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "LoadPhotodeskPhotoID=" + $('input[name="PhotoAdId"]').val() + "&LoadPhotodeskPhotoURL=" + url,
        success: function() {
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}
function ChangeAvatarAgent(url) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "ChangeAvatarAgentID=" + $('#agency_id').val() + "&ChangeAvatarAgentURL=" + url,
        success: function() {
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}
function ChangeAvatarExpert(url) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "ChangeAvatarExpertID=" + $('#expert_id').val() + "&ChangeAvatarExpertURL=" + url,
        success: function() {
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}
function DeleteQuestion(id) {
    if (window.confirm('Удалить вопрос и ответ на него?')) {
        $.ajax({
            type: "POST",
            url: "inc/ajax.php",
            data: "DeleteQuestion=" + id,
            success: function(data) {
                if (data === 'yes') {
                    $('.mini_img[alt="' + id + '"]').closest('div').slideUp(500);
                    if ($('p.title_text_reg').text() === 'Новые вопросы') {
                        var new_q = parseInt($('#new_q span').text(), 10) - 1;
                        $('#new_q span').text(new_q);
                    } else {
                        var ans = parseInt($('#answers span').text(), 10) - 1;
                        $('#answers span').text(ans);
                    }
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("\u0412озникла ошибка!");
            }
        });
    }
}
function SaveAnswer(id) {
    if (window.confirm('Сохранить ответ?')) {
        $.ajax({
            type: "POST",
            url: "inc/ajax.php",
            data: "SaveAnswer=" + id + "&SaveAnswerTEXT=" + $('.mini_img[alt="' + id + '"]').closest('div').find('.area_exp').val(),
            success: function(data) {
                if (data === 'yes') {
                    if ($('p.title_text_reg').text() === 'Новые вопросы') {
                        AnswerShake();
                        $('.mini_img[alt="' + id + '"]').closest('div').delay(500).slideUp(500);
                    }
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("\u0412озникла ошибка!");
            }
        });
    }
}
function AnswerShake() {
    $('#answers a').css({'font-weight': 'bold'});
    for (i = 0; i < 3; i++) {
        $('#answers').animate({'margin-left': '5px'}, 75).animate({'margin-left': '-5px'}, 150).animate({'margin-left': '0px'}, 75);
    }
    var ans = parseInt($('#answers span').text(), 10) + 1;
    $('#answers span').text(ans);
    var new_q = parseInt($('#new_q span').text(), 10) - 1;
    $('#new_q span').text(new_q);
}
function ShowMessage(obj) {
    var m_div = $(obj).closest('table').nextAll('.table_add_obiavlenia');
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "ShowMessage=" + $(obj).attr('alt'),
        success: function(data) {
            m_div.fadeOut(500, function() {
                $('.znak_16').text(data);
            }).fadeIn(500);
            if ($(obj).attr('title') === 'Непрочитано') {
                $(obj).fadeOut(500, function() {
                    $(obj).attr({'title': 'Прочитано', 'src': '../images/read.png'});
                }).fadeIn(500);
            }
            jQuery.data(document.body, 'znak_16', {id: $(obj).attr('alt')});
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}
function SendMessage(obj) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: {
            SendMessageID: jQuery.data(document.body, 'znak_16').id,
            SendMessageText: $(obj).closest('tr').find('textarea').val()
        },
        success: function(data) {
            if (data === 'yes') {
                $(obj).closest('tr').find('textarea').val('');
                alert('Сообщение отправлено!');
            }
            if (data === 'no') {
                alert('Не удалось отправить сообщение! Возможно, вы пытаетесь отправить сообщение незарегистрированному пользователю!');
            }
            if (data === 'notext') {
                alert('Сообщение не должно быть пустым!');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}
function DeleteMessage(obj) {
    if (window.confirm('Вы действительно хотите удалить сообщение?')) {
        $.ajax({
            type: "POST",
            url: "inc/ajax.php",
            data: {
                DeleteMessage: $(obj).attr('alt')
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
function ShowMessageOubox(obj) {
    var m_div = $(obj).closest('table').nextAll('.table_add_obiavlenia');
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "ShowMessageOubox=" + $(obj).attr('alt'),
        success: function(data) {
            m_div.fadeOut(500, function() {
                $('.znak_16').text(data);
            }).fadeIn(500);
            jQuery.data(document.body, 'znak_16', {id: $(obj).attr('alt')});
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}
function DeleteMessageOutbox(obj) {
    if (window.confirm('Вы действительно хотите удалить сообщение?')) {
        $.ajax({
            type: "POST",
            url: "inc/ajax.php",
            data: {
                DeleteMessageOutbox: $(obj).attr('alt')
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
function SendMessageOutbox(obj) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: {
            SendMessageOutboxID: jQuery.data(document.body, 'znak_16').id,
            SendMessageOutboxText: $(obj).closest('tr').find('textarea').val()
        },
        success: function(data) {
            if (data === 'yes') {
                $(obj).closest('tr').find('textarea').val('');
                alert('Сообщение отправлено!');
            }
            if (data === 'no') {
                alert('Не удалось отправить сообщение! Возможно, вы пытаетесь отправить сообщение незарегистрированному пользователю!');
            }
            if (data === 'notext') {
                alert('Сообщение не должно быть пустым!');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}