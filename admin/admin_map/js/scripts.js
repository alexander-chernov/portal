function DoubleAddress() {
    if (window.confirm('\u0412ы действительно хотите создать двойной адрес?')) {
        $.ajax({
            type: "POST",
            url: "inc/ajax.php",
            data: "DoubleAddress1=" + $('#sum_address1').val() + "&DoubleAddress2=" + $('#sum_address2').val(),
            success: function(data) {
                $('#double_table').html(data);
                alert('\u0410дрес успешно добавлен!');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("\u0412озникла ошибка!");
            }
        });
    }
}

function DeleteDoubleAddress(obj) {
    if (window.confirm('\u0412ы действительно хотите удалить двойной адрес?')) {
        $.ajax({
            type: "POST",
            url: "inc/ajax.php",
            data: "DeleteDoubleAddress=" + $(obj).attr('alt'),
            success: function(data) {
                if (data === 'yes') {
                    $('#do_ad_' + $(obj).attr('alt')).hide(500);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("\u0412озникла ошибка!");
            }
        });
    }
}

function DeleteAddress(obj) {
    if (window.confirm('\u0412ы действительно хотите удалить адрес?')) {
        $.ajax({
            type: "POST",
            url: "inc/ajax.php",
            data: "DeleteAddress=" + $(obj).attr('alt'),
            success: function(data) {
                if (data === 'yes') {
                    $('#do_ad_' + $(obj).attr('alt')).hide(500);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("\u0412озникла ошибка!");
            }
        });
    }
}

function DeleteStreet(obj) {
    if (window.confirm('\u0412ы действительно хотите удалить улицу?')) {
        $.ajax({
            type: "POST",
            url: "inc/ajax.php",
            data: "DeleteStreet=" + $(obj).attr('alt'),
            success: function(data) {
                if (data === 'yes') {
                    $('#do_ad_' + $(obj).attr('alt')).hide(500);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("\u0412озникла ошибка!");
            }
        });
    }
}

function DeleteDistrict(obj) {
    if (window.confirm('\u0412ы действительно хотите удалить район?')) {
        $.ajax({
            type: "POST",
            url: "inc/ajax.php",
            data: "DeleteDistrict=" + $(obj).attr('alt'),
            success: function(data) {
                if (data === 'yes') {
                    $('#do_ad_' + $(obj).attr('alt')).hide(500);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("\u0412озникла ошибка!");
            }
        });
    }
}

function DeleteMassive(obj) {
    if (window.confirm('\u0412ы действительно хотите удалить жилмассив?')) {
        $.ajax({
            type: "POST",
            url: "inc/ajax.php",
            data: "DeleteMassive=" + $(obj).attr('alt'),
            success: function(data) {
                if (data === 'yes') {
                    $('#do_ad_' + $(obj).attr('alt')).hide(500);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("\u0412озникла ошибка!");
            }
        });
    }
}

function DeleteImg(obj) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "DeleteImg=" + $(obj).attr('alt'),
        success: function(data) {
            if (data === 'yes') {
                $(obj).hide(500);
                $("img[src='" + $(obj).attr('alt') + "']").css('opacity', '0.2');
                $("input[value='" + $(obj).attr('alt') + "']").prop('disabled', true);
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function LoadPhotosFromAddr(id) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "LoadPhotosFromAddr=" + id,
        success: function(data) {
            document.getElementById('PhotoForAddress').disabled = false;
            $('#PhotoForAddress').css('opacity', '1');
            $('#LoadPhotos').html(data);
            $('#addr_ph_id').val(id);
            $('#add_s').show(500);
            enableA();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function AddPhotoToAddr(url) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "AddPhotoToAddrID=" + $('#addr_ph_id').val() + "&AddPhotoToAddrURL=" + url,
        success: function() {
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function CreateNewAddress() {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "CreateNewAddressNum=" + $('#create_house_num').val() +
                "&CreateNewAddressStr=" + $('#create_street :selected').val() +
                "&CreateNewAddressDistr=" + $('#create_distr :selected').val() +
                "&CreateNewAddressMass=" + $('#create_massive :selected').val()+
                "&x=" + $('#create_house_num_x').val()+
                "&y=" + $('#create_house_num_y').val()
        ,
        success: function(data) {
            if (data === 'yes') {
                alert('\u0410дрес успешно добавлен!');
            } else {
                alert('\u0412озникла ошибка!');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function AllAddressesExDouble(id) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "AllAddressesExDouble=1",
        success: function(data) {
            $('#' + id).html(data);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function AllAddresses(id) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "AllAddresses=1",
        success: function(data) {
            $('#' + id).html(data);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function CreateNewStreet() {
    if ($('#create_new_street').val().length > 3) {
        $.ajax({
            type: "POST",
            url: "inc/ajax.php",
            data: "CreateNewStreet=" + $('#create_new_street').val(),
            success: function(data) {
                if (data === 'yes') {
                    $('#create_new_street').css('background', '#aaffaa');
                    alert('Улица успешно добавлена!');
                } else {
                    $('#create_new_street').css('background', '#ffaaaa');
                    alert('Запрещено!');
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("\u0412озникла ошибка!");
            }
        });
    } else {
        $('#create_new_street').css('background', '#ffaaaa');
        alert('\u041dазвание улицы должно быть более 5 символов!');
    }
}

function CreateNewDistrict() {
    if ($('#create_new_district').val().length > 3) {
        $.ajax({
            type: "POST",
            url: "inc/ajax.php",
            data: "CreateNewDistrict=" + $('#create_new_district').val()+'&x='+$('#create_district_x').val()+'&y='+$('#create_district_y').val(),
            success: function(data) {
                if (data === 'yes') {
                    $('#create_new_district').css('background', '#aaffaa');
                    alert('Район успешно добавлен!');
                } else {
                    $('#create_new_district').css('background', '#ffaaaa');
                    alert('Запрещено!');
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("\u0412озникла ошибка!");
            }
        });
    } else {
        $('#create_new_district').css('background', '#ffaaaa');
        alert('\u041dазвание района должно быть более 5 символов!');
    }
}

function CreateNewMassive() {
    if ($('#create_new_massive').val().length > 3) {
        $.ajax({
            type: "POST",
            url: "inc/ajax.php",
            data: "CreateNewMassive=" + $('#create_new_massive').val(),
            success: function(data) {
                if (data === 'yes') {
                    $('#create_new_massive').css('background', '#aaffaa');
                    alert('Массив успешно добавлен!');
                } else {
                    $('#create_new_massive').css('background', '#ffaaaa');
                    alert('Запрещено!');
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("\u0412озникла ошибка!");
            }
        });
    } else {
        $('#create_new_massive').css('background', '#ffaaaa');
        alert('\u041dазвание массива должно быть более 5 символов!');
    }
}

function ShowAddDoubleWindow() {

    $('#add_double_w').slideDown(500);
    enableA();

}

function ShowAddWindow(i) {
    if (i==1) {
        $('#admin_map_2').attr('src', "map.php");
    }
    if (i==2){
        $('#admin_map').attr('src', "map.php?d=1");
    }

    $('#add_w').slideDown(500);
    enableA();

}

function ChangeStreet(obj) {
    var id = $(obj).attr('alt');
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "ChangeStreet=" + id,
        success: function(data) {
            $('#change_name_street').val(data);
            $('#change_id_street').val(id);
            $('#change_w').slideDown(500);
            enableA();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function ChangeNameStreet() {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: {
            'ChangeNameStreetID': $('#change_id_street').val(),
            'ChangeNameStreetName': $('#change_name_street').val()
        },
        success: function(data) {
            if (data === 'yes') {
                alert('Успешно сохранено!');
            } else {
                alert('Возникла ошибка!');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function ChangeDistrict(obj) {
    var id = $(obj).attr('alt');
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "ChangeDistrict=" + id,
        success: function(data) {
            $('#change_name_district').val(data);
            $('#change_id_district').val(id);
            $('#change_a_w').slideDown(500);
            enableA();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function ChangeNameDistrict() {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: {
            'ChangeNameDistrictID': $('#change_id_district').val(),
            'ChangeNameDistrictName': $('#change_name_district').val()
        },
        success: function(data) {
            if (data === 'yes') {
                alert('Успешно сохранено!');
            } else {
                alert('Возникла ошибка!');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function ChangeMassive(obj) {
    var id = $(obj).attr('alt');
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "ChangeMassive=" + id,
        success: function(data) {
            $('#change_name_massive').val(data);
            $('#change_id_massive').val(id);
            $('#change_m_w').slideDown(500);
            enableA();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function ChangeNameMassive() {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: {
            'ChangeNameMassiveID': $('#change_id_massive').val(),
            'ChangeNameMassiveName': $('#change_name_massive').val()
        },
        success: function(data) {
            if (data === 'yes') {
                alert('Успешно сохранено!');
            } else {
                alert('Возникла ошибка!');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}