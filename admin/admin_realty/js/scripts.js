function ImmoSaveInDB(param, immo_id) {
    var query;
    switch (param) {
        case 1:
            if (parseInt($('#ImmoChangePrice').val().length) > 0) {
                query = 'UPDATE k_immovables_sell SET';
                query += ' k_isf_immovable_type=' + $('#ImmoChangeType').val();
                query += ', k_isf_new=' + $('#ImmoChangeClass').val();
                query += ', k_isf_material=' + $('#ImmoChangeMaterial').val();
                query += ', k_isf_floor=' + $('#ImmoChangeFloor').val();
                query += ', k_isf_floor_all=' + $('#ImmoChangeFloorAll').val();
                query += ', k_isf_eq=' + $('#ImmoChangeEQ').val();
                query += ', k_isf_rooms=' + parseInt($('#ImmoChangeRooms').val(), 10);
                query += ', k_isf_area_all=' + parseFloat($('#ImmoChangeAreaAll').val());
                query += ', k_isf_area_live=' + parseFloat($('#ImmoChangeAreaLive').val());
                query += ', k_isf_area_kitchen=' + parseFloat($('#ImmoChangeAreaKitchen').val());
                query += ', k_isf_san=' + $('#ImmoChangeSan').val();
                query += ', k_isf_balcony=' + $('#ImmoChangeBalcony').val();
                query += ', k_isf_phone_stat=' + CheckOrNot('ImmoChangePhoneStat');
                query += ', k_isf_price=' + parseInt($('#ImmoChangePrice').val(), 10);
                query += ', k_isf_quickly=' + CheckOrNot('ImmoChangeQuickly');
                query += ', k_isf_merch=' + CheckOrNot('ImmoChangeMerch');
                query += ', k_isf_exchange=' + CheckOrNot('ImmoChangeExchange');
                query += ', k_isf_credit=' + CheckOrNot('ImmoChangeCredit');
                query += ', k_isf_documents=' + CheckOrNot('ImmoChangeDoc');
                query += ', k_isf_owned=' + CheckOrNot('ImmoChangeOwned');
                query += ', k_isf_privat=' + CheckOrNot('ImmoChangePrivat');
                query += ', k_isf_description="' + $('#ImmoChangeDescr').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ', k_isf_contact_name="' + $('#ImmoChangeContName').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ', k_isf_contacts="' + $('#ImmoChangeContacts').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ' WHERE k_isf_id=' + immo_id;
                $.post("inc/admin_functions.php", {
                    SaveInDBParam: query
                },
                function(data)
                {
                    alert(data);
                });
            } else {
                alert('\u0412ведите цену!');
            }
            break;
        case 2:
            if (parseInt($('#ImmoChangePrice').val().length) > 0) {
                query = 'UPDATE k_immovables_sell SET';
                query += ' k_isf_immovable_type=' + $('#ImmoChangeType').val();
                query += ', k_isf_new=' + $('#ImmoChangeClass').val();
                query += ', k_isf_material=' + $('#ImmoChangeMaterial').val();
                query += ', k_isf_floor_all=' + $('#ImmoChangeFloorAll').val();
                query += ', k_isf_eq=' + $('#ImmoChangeEQ').val();
                query += ', k_isf_rooms=' + parseInt($('#ImmoChangeRooms').val(), 10);
                query += ', k_isf_area_all=' + parseFloat($('#ImmoChangeAreaAll').val());
                query += ', k_isf_area_live=' + parseFloat($('#ImmoChangeAreaLive').val());
                query += ', k_isf_area_land=' + parseFloat($('#ImmoChangeAreaLand').val());
                query += ', k_isf_phone_stat=' + CheckOrNot('ImmoChangePhoneStat');
                query += ', k_isf_price=' + parseInt($('#ImmoChangePrice').val(), 10);
                query += ', k_isf_quickly=' + CheckOrNot('ImmoChangeQuickly');
                query += ', k_isf_merch=' + CheckOrNot('ImmoChangeMerch');
                query += ', k_isf_exchange=' + CheckOrNot('ImmoChangeExchange');
                query += ', k_isf_credit=' + CheckOrNot('ImmoChangeCredit');
                query += ', k_isf_documents=' + CheckOrNot('ImmoChangeDoc');
                query += ', k_isf_owned=' + CheckOrNot('ImmoChangeOwned');
                query += ', k_isf_privat=' + CheckOrNot('ImmoChangePrivat');
                query += ', k_isf_description="' + $('#ImmoChangeDescr').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ', k_isf_contact_name="' + $('#ImmoChangeContName').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ', k_isf_contacts="' + $('#ImmoChangeContacts').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ' WHERE k_isf_id=' + immo_id;
                $.post("inc/admin_functions.php", {
                    SaveInDBParam: query
                },
                function(data)
                {
                    alert(data);
                });
            } else {
                alert('\u0412ведите цену!');
            }
            break;
        case 3:
            if (parseInt($('#ImmoChangePrice').val().length) > 0) {
                query = 'UPDATE k_immovables_sell SET';
                query += ' k_isf_immovable_type=' + $('#ImmoChangeType').val();
                query += ', k_isf_new=' + $('#ImmoChangeClass').val();
                query += ', k_isf_material=' + $('#ImmoChangeMaterial').val();
                query += ', k_isf_floor=' + $('#ImmoChangeFloor').val();
                query += ', k_isf_floor_all=' + $('#ImmoChangeFloorAll').val();
                query += ', k_isf_eq=' + $('#ImmoChangeEQ').val();
                query += ', k_isf_area_all=' + parseFloat($('#ImmoChangeAreaAll').val());
                query += ', k_isf_area_live=' + parseFloat($('#ImmoChangeAreaLive').val());
                query += ', k_isf_area_kitchen=' + parseFloat($('#ImmoChangeAreaKitchen').val());
                query += ', k_isf_san=' + $('#ImmoChangeSan').val();
                query += ', k_isf_balcony=' + $('#ImmoChangeBalcony').val();
                query += ', k_isf_phone_stat=' + CheckOrNot('ImmoChangePhoneStat');
                query += ', k_isf_security=' + CheckOrNot('ImmoChangeSecurity');
                query += ', k_isf_internet=' + CheckOrNot('ImmoChangeInternet');
                query += ', k_isf_price=' + parseInt($('#ImmoChangePrice').val(), 10);
                query += ', k_isf_quickly=' + CheckOrNot('ImmoChangeQuickly');
                query += ', k_isf_merch=' + CheckOrNot('ImmoChangeMerch');
                query += ', k_isf_exchange=' + CheckOrNot('ImmoChangeExchange');
                query += ', k_isf_credit=' + CheckOrNot('ImmoChangeCredit');
                query += ', k_isf_documents=' + CheckOrNot('ImmoChangeDoc');
                query += ', k_isf_owned=' + CheckOrNot('ImmoChangeOwned');
                query += ', k_isf_description="' + $('#ImmoChangeDescr').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ', k_isf_contact_name="' + $('#ImmoChangeContName').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ', k_isf_contacts="' + $('#ImmoChangeContacts').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ' WHERE k_isf_id=' + immo_id;
                $.post("inc/admin_functions.php", {
                    SaveInDBParam: query
                },
                function(data)
                {
                    alert(data);
                });
            } else {
                alert('\u0412ведите цену!');
            }
            break;
        case 4:
            if (parseInt($('#ImmoChangePrice').val().length) > 0) {
                query = 'UPDATE k_immovables_sell SET';
                query += ' k_isf_immovable_type=' + $('#ImmoChangeType').val();
                query += ', k_isf_new=' + $('#ImmoChangeClass').val();
                query += ', k_isf_floor=' + $('#ImmoChangeFloor').val();
                query += ', k_isf_floor_all=' + $('#ImmoChangeFloorAll').val();
                query += ', k_isf_area_all=' + parseFloat($('#ImmoChangeAreaAll').val());
                query += ', k_isf_price=' + parseInt($('#ImmoChangePrice').val(), 10);
                query += ', k_isf_quickly=' + CheckOrNot('ImmoChangeQuickly');
                query += ', k_isf_merch=' + CheckOrNot('ImmoChangeMerch');
                query += ', k_isf_exchange=' + CheckOrNot('ImmoChangeExchange');
                query += ', k_isf_credit=' + CheckOrNot('ImmoChangeCredit');
                query += ', k_isf_documents=' + CheckOrNot('ImmoChangeDoc');
                query += ', k_isf_owned=' + CheckOrNot('ImmoChangeOwned');
                query += ', k_isf_privat=' + CheckOrNot('ImmoChangePrivat');
                query += ', k_isf_description="' + $('#ImmoChangeDescr').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ', k_isf_contact_name="' + $('#ImmoChangeContName').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ', k_isf_contacts="' + $('#ImmoChangeContacts').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ' WHERE k_isf_id=' + immo_id;
                $.post("inc/admin_functions.php", {
                    SaveInDBParam: query
                },
                function(data)
                {
                    alert(data);
                });
            } else {
                alert('\u0412ведите цену!');
            }
            break;
        case 5:
            if (parseInt($('#ImmoChangePrice').val().length) > 0) {
                query = 'UPDATE k_immovables_sell SET';
                query += ' k_isf_immovable_type=' + $('#ImmoChangeType').val();
                query += ', k_isf_area_land=' + parseFloat($('#ImmoChangeAreaLand').val());
                query += ', k_isf_price=' + parseInt($('#ImmoChangePrice').val(), 10);
                query += ', k_isf_quickly=' + CheckOrNot('ImmoChangeQuickly');
                query += ', k_isf_merch=' + CheckOrNot('ImmoChangeMerch');
                query += ', k_isf_exchange=' + CheckOrNot('ImmoChangeExchange');
                query += ', k_isf_credit=' + CheckOrNot('ImmoChangeCredit');
                query += ', k_isf_documents=' + CheckOrNot('ImmoChangeDoc');
                query += ', k_isf_owned=' + CheckOrNot('ImmoChangeOwned');
                query += ', k_isf_privat=' + CheckOrNot('ImmoChangePrivat');
                query += ', k_isf_description="' + $('#ImmoChangeDescr').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ', k_isf_contact_name="' + $('#ImmoChangeContName').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ', k_isf_contacts="' + $('#ImmoChangeContacts').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ' WHERE k_isf_id=' + immo_id;
                $.post("inc/admin_functions.php", {
                    SaveInDBParam: query
                },
                function(data)
                {
                    alert(data);
                });
            } else {
                alert('\u0412ведите цену!');
            }
            break;
        case 6:
            if (parseInt($('#ImmoChangePrice').val().length) > 0) {
                query = 'UPDATE k_immovables_sell SET';
                query += ' k_isf_immovable_type=' + $('#ImmoChangeType').val();
                query += ', k_isf_floor=' + $('#ImmoChangeFloor').val();
                query += ', k_isf_floor_all=' + $('#ImmoChangeFloorAll').val();
                query += ', k_isf_rooms=' + parseInt($('#ImmoChangeRooms').val(), 10);
                query += ', k_isf_area_all=' + parseFloat($('#ImmoChangeAreaAll').val());
                query += ', k_isf_area_live=' + parseFloat($('#ImmoChangeAreaLive').val());
                query += ', k_isf_area_kitchen=' + parseFloat($('#ImmoChangeAreaKitchen').val());
                query += ', k_isf_san=' + $('#ImmoChangeSan').val();
                query += ', k_isf_balcony=' + $('#ImmoChangeBalcony').val();
                query += ', k_isf_internet=' + CheckOrNot('ImmoChangeInternet');
                query += ', k_isf_balcony_gl=' + CheckOrNot('ImmoChangeBalconyGl');
                query += ', k_isf_furniture=' + CheckOrNot('ImmoChangeFurniture');
                query += ', k_isf_fridge=' + CheckOrNot('ImmoChangeFridge');
                query += ', k_isf_washing=' + CheckOrNot('ImmoChangeWashing');
                query += ', k_isf_microwave=' + CheckOrNot('ImmoChangeMicrowave');
                query += ', k_isf_tv=' + CheckOrNot('ImmoChangeTV');
                query += ', k_isf_ctv=' + CheckOrNot('ImmoChangeCTV');
                query += ', k_isf_stove=' + CheckOrNot('ImmoChangeStove');
                query += ', k_isf_plastic_windows=' + CheckOrNot('ImmoChangePlastic');
                query += ', k_isf_utilities=' + $('#ImmoChangeUtil').val();
                query += ', k_isf_price=' + parseInt($('#ImmoChangePrice').val(), 10);
                query += ', k_isf_quickly=' + CheckOrNot('ImmoChangeQuickly');
                query += ', k_isf_merch=' + CheckOrNot('ImmoChangeMerch');
                query += ', k_isf_description="' + $('#ImmoChangeDescr').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ', k_isf_contact_name="' + $('#ImmoChangeContName').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ', k_isf_contacts="' + $('#ImmoChangeContacts').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ' WHERE k_isf_id=' + immo_id;
                $.post("inc/admin_functions.php", {
                    SaveInDBParam: query
                },
                function(data)
                {
                    alert(data);
                });
            } else {
                alert('\u0412ведите цену!');
            }
            break;
        case 7:
            if (parseInt($('#ImmoChangePrice').val().length) > 0) {
                query = 'UPDATE k_immovables_sell SET';
                query += ' k_isf_immovable_type=' + $('#ImmoChangeType').val();
                query += ', k_isf_material=' + $('#ImmoChangeMaterial').val();
                query += ', k_isf_floor_all=' + $('#ImmoChangeFloorAll').val();
                query += ', k_isf_rooms=' + parseInt($('#ImmoChangeRooms').val(), 10);
                query += ', k_isf_area_all=' + parseFloat($('#ImmoChangeAreaAll').val());
                query += ', k_isf_area_live=' + parseFloat($('#ImmoChangeAreaLive').val());
                query += ', k_isf_area_land=' + parseFloat($('#ImmoChangeAreaLand').val());
                query += ', k_isf_phone_stat=' + CheckOrNot('ImmoChangePhoneStat');
                query += ', k_isf_furniture=' + CheckOrNot('ImmoChangeFurniture');
                query += ', k_isf_fridge=' + CheckOrNot('ImmoChangeFridge');
                query += ', k_isf_washing=' + CheckOrNot('ImmoChangeWashing');
                query += ', k_isf_microwave=' + CheckOrNot('ImmoChangeMicrowave');
                query += ', k_isf_tv=' + CheckOrNot('ImmoChangeTV');
                query += ', k_isf_ctv=' + CheckOrNot('ImmoChangeCTV');
                query += ', k_isf_utilities=' + $('#ImmoChangeUtil').val();
                query += ', k_isf_price=' + parseInt($('#ImmoChangePrice').val(), 10);
                query += ', k_isf_quickly=' + CheckOrNot('ImmoChangeQuickly');
                query += ', k_isf_merch=' + CheckOrNot('ImmoChangeMerch');
                query += ', k_isf_description="' + $('#ImmoChangeDescr').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ', k_isf_contact_name="' + $('#ImmoChangeContName').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ', k_isf_contacts="' + $('#ImmoChangeContacts').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ' WHERE k_isf_id=' + immo_id;
                $.post("inc/admin_functions.php", {
                    SaveInDBParam: query
                },
                function(data)
                {
                    alert(data);
                });
            } else {
                alert('\u0412ведите цену!');
            }
            break;
        case 8:
            if (parseInt($('#ImmoChangePrice').val().length) > 0) {
                query = 'UPDATE k_immovables_sell SET';
                query += ' k_isf_immovable_type=' + $('#ImmoChangeType').val();
                query += ', k_isf_new=' + $('#ImmoChangeClass').val();
                query += ', k_isf_material=' + $('#ImmoChangeMaterial').val();
                query += ', k_isf_floor=' + $('#ImmoChangeFloor').val();
                query += ', k_isf_floor_all=' + $('#ImmoChangeFloorAll').val();
                query += ', k_isf_area_all=' + parseFloat($('#ImmoChangeAreaAll').val());
                query += ', k_isf_phone_stat=' + CheckOrNot('ImmoChangePhoneStat');
                query += ', k_isf_security=' + CheckOrNot('ImmoChangeSecurity');
                query += ', k_isf_internet=' + CheckOrNot('ImmoChangeInternet');
                query += ', k_isf_utilities=' + $('#ImmoChangeUtil').val();
                query += ', k_isf_price=' + parseInt($('#ImmoChangePrice').val(), 10);
                query += ', k_isf_quickly=' + CheckOrNot('ImmoChangeQuickly');
                query += ', k_isf_merch=' + CheckOrNot('ImmoChangeMerch');
                query += ', k_isf_description="' + $('#ImmoChangeDescr').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ', k_isf_contact_name="' + $('#ImmoChangeContName').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ', k_isf_contacts="' + $('#ImmoChangeContacts').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ' WHERE k_isf_id=' + immo_id;
                $.post("inc/admin_functions.php", {
                    SaveInDBParam: query
                },
                function(data)
                {
                    alert(data);
                });
            } else {
                alert('\u0412ведите цену!');
            }
            break;
        case 9:
            if (parseInt($('#ImmoChangePrice').val().length) > 0) {
                query = 'UPDATE k_immovables_sell SET';
                query += ' k_isf_immovable_type=' + $('#ImmoChangeType').val();
                query += ', k_isf_floor=' + $('#ImmoChangeFloor').val();
                query += ', k_isf_floor_all=' + $('#ImmoChangeFloorAll').val();
                query += ', k_isf_area_all=' + parseFloat($('#ImmoChangeAreaAll').val());
                query += ', k_isf_utilities=' + $('#ImmoChangeUtil').val();
                query += ', k_isf_price=' + parseInt($('#ImmoChangePrice').val(), 10);
                query += ', k_isf_quickly=' + CheckOrNot('ImmoChangeQuickly');
                query += ', k_isf_merch=' + CheckOrNot('ImmoChangeMerch');
                query += ', k_isf_description="' + $('#ImmoChangeDescr').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ', k_isf_contact_name="' + $('#ImmoChangeContName').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ', k_isf_contacts="' + $('#ImmoChangeContacts').val().replace(/'/g, "\\'").replace(/\"/g, '\\"') + '"';
                query += ' WHERE k_isf_id=' + immo_id;
                $.post("inc/admin_functions.php", {
                    SaveInDBParam: query
                },
                function(data)
                {
                    alert(data);
                });
            } else {
                alert('\u0412ведите цену!');
            }
            break;
    }
}
function ImmoEmail(email) {
    $('#send_email').show(500);
    $('#ImmoEmailEmail').val(email);
    $('#EmailToShow').text(email);
    enableA();
}
function RealtyMainPage(obj) {
    $.post("inc/admin_functions.php", {
        RealtyMainPage: $(obj).attr('alt')
    }
    , function(data)
    {
        if (data === 'yes') {
            if ($(obj).attr('src') === '../images/not_main.png') {
                $(obj).fadeOut(500, function() {
                    $(obj).attr('src', '../images/on_main.png');
                    $(obj).attr('title', 'Добавить на главную страницу');
                }).fadeIn(500);
            } else {
                $(obj).fadeOut(500, function() {
                    $(obj).attr('src', '../images/not_main.png');
                    $(obj).attr('title', 'Убрать с главной страницы');
                }).fadeIn(500);
            }
        } else {
            alert('Возникла ошибка!');
        }
    });
}
function RealtyColor(obj) {
    $.post("inc/admin_functions.php", {
        RealtyColor: $(obj).attr('alt')
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
function ImmoInfo(num, it, user, date, state, days, contact, phone, address, email, ut) {
    $('#info_obiavlenie_block').show(500);
    $('#ImmoAdNum').text(num);
    $('#ImmoAdIT').text(it);
    $('#ImmoAdUser').text(user);
    $('#ImmoAdDate').text(date);
    $('#ImmoAdState').text(state);
    $('#ImmoAdDays').text(days + ' \u0434ней');
    $('#ImmoAdContact').text(contact);
    $('#ImmoAdPhone').text(phone);
    $('#ImmoAdAddress').text(address);
    $('#ImmoAdEmail').text(email);
    $('#ImmoAdUT').text(ut);
    enableA();
}
function ImmoChangeAd(i) {
    $.post("inc/admin_functions.php", {
        AdsID: i
    },
    function(data)
    {
        $('#edit_obiavlenie').html(data);
    });
    $('#edit_obiavlenie').show(500);
    enableA();
}
function CheckOrNot(id) {
    if ($('#' + id).is(':checked')) {
        return 1;
    } else {
        return 0;
    }
}
function AddressSelectChange() {
    $.post("inc/admin_functions.php", {
        AddressSelectChange: $('#ImmoAddressChosen').val()
    },
    function(data)
    {
        $('#ImmoChangeDistrict').text(data.district);
        $('#ImmoChangeMassive').text(data.mass);
    }, "json");
}
function PhotoEdit(id) {
    $.post("inc/admin_functions.php", {
        PhotoID: id
    },
    function(data)
    {
        $('#photo_obiavlenie').html(data);
    });
    $('#photo_obiavlenie').show(500);
    enableA();
}
function PhotoShow(url) {
    $('#ImmoImageShow').attr('src', url);
    document.getElementById('photo_windows').style.display = 'block';
}
function DeletePhoto(id) {
    if (confirm('\u0412ы уверены?')) {
        $.post("inc/admin_functions.php", {
            PhotoDelID: id
        },
        function(data)
        {
            alert(data);
        });
        $('#photo_id_' + id).hide(500);
    }
}
function SpecialAction(id, type) {
    $.post("inc/admin_functions.php", {
        SpecialID: id,
        ActionType: type
    },
    function(data)
    {
        if (data.strip() === 'yes') {
            $('#special_' + id).attr('title', '\u0423брать из спец предложения');
            $('#special_' + id).attr('onClick', 'SpecialAction(' + id + ',2);');
            $('#special_' + id).animate({
                opacity: 0
            }, 500, function() {
                $('#special_' + id).attr('src', '../images/spec_2.png');
                $('#special_' + id).animate({
                    opacity: 1
                }, 500, function() {
                });
            });
        }
        if (data.strip() === 'no') {
            $('#special_' + id).attr('title', '\u0414обавить в спец предложения');
            $('#special_' + id).attr('onClick', 'SpecialAction(' + id + ',1);');
            $('#special_' + id).animate({
                opacity: 0
            }, 500, function() {
                $('#special_' + id).attr('src', '../images/spec_1.png');
                $('#special_' + id).animate({
                    opacity: 1
                }, 500, function() {
                });
            });
        }
        if (data.strip() === 'error') {
            alert('\u0412озникла непредвиденная ошибка!');
        }
    });
}
function AddDaysImmo(id) {
    $('#ImmoForAddDays').val(id);
    $.post("inc/admin_functions.php", {
        ImmoAddDays: $('#ImmoForAddDays').val()
    }, function(data) {
        $('#LastDays').text(data);
    });
    $('#AddDayBlock').show(500);
    enableA();
}
function AddDaysSubmit() {
    $.post("inc/admin_functions.php", {
        ImmoAddDaysID: $('#ImmoForAddDays').val(),
        ImmoAddDaysSubmit: parseInt($('#DaysForAddDays').val(), 10)
    }, function(data) {
        alert(data);
    });
    AddDaysImmo($('#ImmoForAddDays').val());
}
function DisEnAd(id, act) {
    $.post("inc/admin_functions.php", {
        ImmoDisEnID: id,
        ImmoDisEnAct: act
    }, function(data) {
        if (data.strip() === 'yes') {
            if (act === 1) {
                $('#endis_' + id).attr('title', '\u0421крыть объявление');
                $('#endis_' + id).attr('onClick', 'DisEnAd(' + id + ',0);');
                $('#endis_' + id).animate({
                    opacity: 0
                }, 500, function() {
                    $('#endis_' + id).attr('src', '../images/disable_1.png');
                    $('#endis_' + id).animate({
                        opacity: 1
                    }, 500, function() {
                    });
                });
                $('#state_immo_' + id).animate({
                    opacity: 0
                }, 500, function() {
                    $('#state_immo_' + id).attr('class', 'style_4_1');
                    $('#state_immo_' + id).text('\u0410ктивно');
                    $('#state_immo_' + id).animate({
                        opacity: 1
                    }, 500, function() {
                    });
                });
            } else {
                $('#endis_' + id).attr('title', '\u041fоказать объявление');
                $('#endis_' + id).attr('onClick', 'DisEnAd(' + id + ',1);');
                $('#endis_' + id).animate({
                    opacity: 0
                }, 500, function() {
                    $('#endis_' + id).attr('src', '../images/enable.png');
                    $('#endis_' + id).animate({
                        opacity: 1
                    }, 500, function() {
                    });
                });
                $('#state_immo_' + id).animate({
                    opacity: 0
                }, 500, function() {
                    $('#state_immo_' + id).attr('class', 'style_4_2');
                    $('#state_immo_' + id).text('\u0421крыто');
                    $('#state_immo_' + id).animate({
                        opacity: 1
                    }, 500, function() {
                    });
                });
            }
        }
    });
}
function BlockIP(ip) {
    $.post("inc/admin_functions.php", {
        IPUserBan: ip
    }, function(data) {
        alert(data);
    });
}
function DeleteAd(id) {
    if (confirm("\u042dтим действием вы полностью удалите объявление и все данные, связанные с ним. Вы уверены?")) {
        $.post("inc/admin_functions.php", {
            ImmoIDDelSubmit: id
        }, function(data) {
            if (data === 'yes') {
                $('#immo_tr_' + id).fadeOut(500);
            }
        });
    }
}
function SearchWidget(act) {
    if (act === 1) {
        $('#lupa_plus').hide(500);
        $('#lupa_minus').show(500);
        $('#parametr_search').show(500);
    }
    if (act === 2) {
        $('#lupa_plus').show(500);
        $('#lupa_minus').hide(500);
        $('#parametr_search').hide(500);
    }
}
function CheckedAdsVal(max, act) {
    for (i = 0; i < max; i++) {
        if (CheckOrNot('CheckedAds_' + i)) {
            if (act === 1) {
                if (confirm('\u0412ы уверены?')) {
                    $.post("inc/admin_functions.php", {
                        ImmoIDDelSubmit: i
                    }, function() {
                    });
                }
            }
            if (act === 2) {
                DisEnAd($('#CheckedAds_' + i).val(), 1);
            }
            if (act === 3) {
                DisEnAd($('#CheckedAds_' + i).val(), 0);
            }
        }
    }
    alert(query);
}

function CheckedAllImmo(max) {
    for (i = 0; i < max; i++) {
        if (CheckOrNot('CheckedAds_' + i)) {
            $('#CheckedAds_' + i).prop('checked', false);
            $('#CheckButton').attr('title', '\u0412ыделить все Объявления');
        } else {
            $('#CheckedAds_' + i).prop('checked', true);
            $('#CheckButton').attr('title', '\u0421нять выделения с Объявлений');
        }
    }
}

function SendEmail() {
    $.post("inc/admin_functions.php", {
        ImmoEmailEmail: $('#ImmoEmailEmail').val(),
        ImmoEmailTheme: $('#ImmoEmailTheme').val(),
        ImmoEmailText: $('#ImmoEmailText').val()
    }, function(data) {
        if (data === 'yes') {
            alert('\u0421ообщение успешно отправлено!');
        }
    });
}
function UpImmoBlock(obj) {
    $.post("inc/admin_functions.php", {
        ImmoIdToDays: $(obj).attr('alt')
    }, function(data) {
        if (data === 'yes') {
            alert('\u041eбъявление успешно поднято!');
        } else {
            alert('\u0412озникла ошибка!');
        }
    });
}
function ImmoEmail(email) {
    $('#send_email').show(500);
    $('#EmailToShow').text(email);
    $('#ImmoEmailEmail').val(email);
    enableA();
}
function AgentsInfo(id) {
    $.post("inc/admin_functions.php", {
        AgentsInfoID: id
    },
    function(data)
    {
        $('#InfoAgentsTable').html(data);
    });
    $('#info_agentstvo').show(500);
    enableA();
}
function AgentEdit(id) {
    $.post("inc/admin_functions.php", {
        AgentsEditID: id
    },
    function(data)
    {
        $('#AgentEditTable').html(data);
    });
    $('#edit_agentstvo').show(500);
    enableA();
}
function SearchAddress() {
    if (($('#AgentEditAddress').val()).length > 2) {
        $.post("inc/admin_functions.php", {
            SearchAddress: $('#AgentEditAddress').val()
        },
        function(data)
        {
            $('#AgentAddressResult').html(data);
        });
    } else {
        $('#AgentAddressResult').html('<select id="ImmoAddressChosen" style="width: 100%;" name="ImmoAddressChosen"><option selected value="0"></option></select>');
    }
}
function AgentEditSubmit(id) {
    $.post("inc/admin_functions.php", {
        AgentEditName: $('#AgentEditName').val(),
        AgentEditPhone: $('#AgentEditPhone').val(),
        AgentEditEmail: $('#AgentEditEmail').val(),
        AgentEditSite: $('#AgentEditSite').val(),
        AgentEditDescr: $('#AgentEditDescr').val(),
        AgentEditFName: $('#AgentEditFName').val(),
        AgentEditLName: $('#AgentEditLName').val(),
        AgentEditOName: $('#AgentEditOName').val(),
        ImmoAddressChosen: $('#ImmoAddressChosen').val(),
        AgentEditSubmitID: id
    },
    function(data)
    {
        alert(data);
    });
}
function AgentAvatarLoad(id) {
    $.post("inc/admin_functions.php", {
        AgentAvatarLoadID: id
    },
    function(data)
    {
        $('#AgentAvatarTable').html(data);
    });
    $('#AgentHiddenID').val(id);
    $('#avatar_agentstvo').show(500);
    enableA();
}
function AvatarChange(filename) {
    $.post("inc/admin_functions.php", {
        AgentAvatarChangeID: $('#AgentHiddenID').val(),
        AgentAvatarChangeURL: filename
    },
    function() {
    });
    $('#AgentTableImage_' + $('#AgentHiddenID').val()).attr('src', filename);
    $('#AgentAvatarShow').show(500);
    $('#AgentTableImage_' + $('#AgentHiddenID').val()).show(500);
}
function DeleteAgentAvatar() {
    $.post("inc/admin_functions.php", {
        AgentAvatarDeleteID: $('#AgentHiddenID').val()
    },
    function() {
        $('#AgentAvatarShow').hide(500);
        $('#AgentTableImage_' + $('#AgentHiddenID').val()).hide(500);
    });
}
function ChangePasswordWindow(id) {
    $('#AgentPasswordID').val(id);
    $('#pass_agentstvo').show(500);
    enableA();
}
function LinesCompare() {
    if ($('#AgentPassLine2').val() === $('#AgentPassLine1').val()) {
        $('#AgentPassLine2').css('background-color', '#77FF77');
    } else {
        $('#AgentPassLine2').css('background-color', '#FF7777');
    }
}
function ChangeAgentPassword() {
    if ($('#AgentPassLine2').val() === $('#AgentPassLine1').val()) {
        $.post("inc/admin_functions.php", {
            PasswordChangeID: $('#AgentPasswordID').val(),
            PasswordChangePass: $('#AgentPassLine2').val()
        },
        function(data) {
            alert(data);
        });
    } else {
        alert('\u041fароли не совпадают!');
    }
}
function AgentInRegister(id, type) {
    $.post("inc/admin_functions.php", {
        AgentRegisterID: id,
        AgentRegisterAct: type
    },
    function(data)
    {
        if (data.strip() === 'yes') {
            $('#AgentRegister_' + id).attr('src', '../images/down.png');
            $('#AgentRegister_' + id).attr('title', '\u041eтменить поднятие');
            $('#AgentRegister_' + id).attr('onClick', 'AgentInRegister(' + id + ',2);');
        }
        if (data.strip() === 'no') {
            $('#AgentRegister_' + id).attr('src', '../images/up.png');
            $('#AgentRegister_' + id).attr('title', '\u041fоднять Агентство');
            $('#AgentRegister_' + id).attr('onClick', 'AgentInRegister(' + id + ',1);');
        }
        if (data.strip() === 'error') {
            alert('\u0412озникла непредвиденная ошибка!');
        }
    });
}
//ImmoDisEnID
function DisEnAgent(id, act) {
    $.post("inc/admin_functions.php", {
        AgentDisEnID: id,
        AgentDisEnAct: act
    }, function(data) {
        if (data.strip() === 'yes') {
            if (act === 1) {
                $('#AgentStateInTable_' + id).fadeOut(500, function() {
                    $('#AgentStateInTable_' + id).attr('class', 'style_4_1');
                    $('#AgentStateInTable_' + id).text('\u0410ктивно');
                }).fadeIn(500);
                $('#AgentIDState_' + id).fadeOut(500, function() {
                    $('#AgentIDState_' + id).attr('src', '../images/disable_1.png');
                    $('#AgentIDState_' + id).attr('title', '\u0421крыть Агентство');
                    $('#AgentIDState_' + id).attr('onClick', 'DisEnAgent(' + id + ',0);');
                }).fadeIn(500);
            } else {
                $('#AgentIDState_' + id).fadeOut(500, function() {
                    $('#AgentIDState_' + id).attr('src', '../images/enable.png');
                    $('#AgentIDState_' + id).attr('title', '\u041fоказать Агентство');
                    $('#AgentIDState_' + id).attr('onClick', 'DisEnAgent(' + id + ',1);');
                }).fadeIn(500);
                $('#AgentStateInTable_' + id).fadeOut(500, function() {
                    $('#AgentStateInTable_' + id).attr('class', 'style_4_2');
                    $('#AgentStateInTable_' + id).text('\u0421крыто');
                }).fadeIn(500);
            }
        }
    });
}
function BlockIP(ip) {
    $.post("inc/admin_functions.php", {
        IPUserBan: ip
    }, function(data) {
        alert(data);
    });
}
function DeleteAgent(id) {
    if (confirm("\u042dтим действием вы полностью удалите агентство и все данные, связанные с ним. Вы уверены?")) {
        $.post("inc/admin_functions.php", {
            AgentIDDelSubmit: id
        }, function(data) {
            alert(data);
            $('#AgentRowID_' + id).hide(500);
        });
    }
}
function AddAgentWindow() {
    $('#add_agentstvo').show(500);
    enableA();
}
function AddAgentIntoTable() {
    if ($('#AddAgentPassword2').val() === $('#AddAgentPassword1').val()) {
        $.post("inc/admin_functions.php", {
            AddAgentLogin: $('#AddAgentLogin').val(),
            AddAgentPassword2: $('#AddAgentPassword2').val(),
            AddAgentFName: $('#AddAgentFName').val(),
            AddAgentLName: $('#AddAgentLName').val(),
            AddAgentOName: $('#AddAgentOName').val(),
            AddAgentEmail: $('#AddAgentEmail').val(),
            ImmoAddressChosen: $('#ImmoAddressChosen').val(),
            AddAgentName: $('#AddAgentName').val(),
            AddAgentPhone: $('#AddAgentPhone').val(),
            AddAgentSite: $('#AddAgentSite').val(),
            AddAgentDays: parseInt($('#AddAgentDays').val(), 10),
            AddAgentDescription: $('#AddAgentDescription').val()
        }, function(data) {
            alert(data);
            CloseWindow('add_agentstvo');

        });
    } else {
        alert('\u041fароли не совпадают!');
    }
}
function PasswordCompareAgent() {
    if ($('#AddAgentPassword2').val() === $('#AddAgentPassword1').val()) {
        $('#AddAgentPassword2').css('background-color', '#77FF77');
    } else {
        $('#AddAgentPassword2').css('background-color', '#FF7777');
    }
}
function CheckOrNot(id) {
    if ($('#' + id).is(':checked')) {
        return 1;
    } else {
        return 0;
    }
}
function CheckedAgentVal(max, act) {
    if (confirm('\u0412ы уверены?')) {
        for (i = 0; i < max; i++) {
            if (CheckOrNot('CheckedAgents_' + i)) {
                if (act === 1) {
                    $.post("inc/admin_functions.php", {
                        AgentIDDelSubmit: $('#CheckedAgents_' + i).val()
                    }, function() {
                    });
                    $('#AgentRowID_' + $('#CheckedAgents_' + i).val()).hide(500);
                }
                if (act === 2) {
                    DisEnAgent($('#CheckedAgents_' + i).val(), 1);
                }
                if (act === 3) {
                    DisEnAgent($('#CheckedAgents_' + i).val(), 0);
                }
            }
        }
    }
}
function CheckedAllAgents(max) {
    for (i = 0; i < max; i++) {
        if (CheckOrNot('CheckedAgents_' + i)) {
            $('#CheckedAgents_' + i).prop('checked', false);
            $('#CheckButton').attr('title', '\u0412ыделить все Агентства');
        } else {
            $('#CheckedAgents_' + i).prop('checked', true);
            $('#CheckButton').attr('title', '\u0421нять выделения с Агентств');
        }
    }
}
function ShowBuys(id) {
    $.post("inc/admin_functions.php", {
        BuysID: id
    },
    function(data)
    {
        $('#EditBuysTable').html(data);
    });
    $('#edit_kupliu').show(500);
    enableA();
}

function EditBuysSubmit(id) {
    $.post("inc/admin_functions.php", {
        BuysEditID: id,
        BuysTextEdit: $('#BuysTextEdit').val()
    },
    function(data)
    {
        if (data.strip() === 'yes') {
            alert('\u0423спешно сохранено!');
        } else {
            alert('\u0412озникла ошибка!');
        }
    });
}

function DeleteBuysSubmit(id) {
    if (confirm("\u042dтим действием вы удалите объявление. Вы уверены?")) {
        $.post("inc/admin_functions.php", {
            BuysDeleteID: id
        },
        function(data) {
            if (data.strip() === 'yes') {
                alert('\u041eбъявление успешно удалено!');
                $('#RowBuys_' + id).hide(500);
            } else {
                alert('\u041dе удалось совершить операцию!');
            }
        });
    }
}

function DeleteSelectedBuys(max) {
    if (confirm('\u0412ы уверены?')) {
        for (i = 0; i < max; i++) {
            if (CheckOrNot('CheckBuys_' + i)) {
                $.post("inc/admin_functions.php", {
                    BuysDeleteID: $('#CheckBuys_' + i).val()
                },
                function() {
                });
                $('#RowBuys_' + $('#CheckBuys_' + i).val()).hide(500);
            }
        }
    }
}

function ShowNews(id) {
    $.post("inc/admin_functions.php", {
        NewsID: id
    },
    function(data)
    {
        $('#NewsTable').html(data);
    });
    $('#info_news').show(500);
    enableA();
}

function EditNews(id) {
    $.post("inc/admin_functions.php", {
        NewsIDEdit: id
    },
    function(data)
    {
        $('#NewsEditTable').html(data);
    });
    $('#edit_news').show(500);
    enableA();
}

function NewsEditSubmit() {
    $.post("inc/admin_functions.php", {
        NewsIDEditSub: $('#NewsIDEditSub').val(),
        NewsHeaderEdit: $('#NewsHeaderEdit').val(),
        NewsTextEdit: $('#NewsTextEdit').val(),
        NewsSubcategoriesEdit: $('#NewsSubcategoriesEdit').val()
    },
    function(data)
    {
        if (data.strip() === 'yes') {
            alert('\u0418нформация обновлена!');
            $('#NewsHeaderTable_' + $('#NewsIDEditSub').val()).text($('#NewsHeaderEdit').val());
            $('#NewsSubcategoryTable_' + $('#NewsIDEditSub').val()).text($("#NewsSubcategoriesEdit option[value='" + $('#NewsSubcategoriesEdit').val() + "']").text());
        }
    });
}

function NewsEditAvatar(id) {
    $.post("inc/admin_functions.php", {
        NewsIDAvatar: id
    },
    function(data)
    {
        $('#AvatarEditTable').html(data);
    });
    $('#avatar_news').show(500);
    enableA();
}

function NewsAvatarChange(filename) {
    $.post("inc/admin_functions.php", {
        NewsAvatarChangeID: $('#AvatarNewsIDCh').val(),
        NewsAvatarChangeURL: filename
    },
    function() {
    });
    $('#NewsImage_' + $('#AvatarNewsIDCh').val()).attr('src', filename);
    $('#AvatarEditTable').show(500);
    $('#NewsImage_' + $('#AvatarNewsIDCh').val()).show(500);
}

function DeleteNewsAvatar() {
    $.post("inc/admin_functions.php", {
        NewsAvatarDeleteID: $('#AvatarNewsIDCh').val()
    },
    function() {
        $('#AvatarEditTable').hide(500);
        $('#NewsImage_' + $('#AvatarNewsIDCh').val()).hide(500);
    });
}

function DeleteNewsSubmit(id) {
    if (confirm("\u042dтим действием вы удалите новость. Вы уверены?")) {
        $.post("inc/admin_functions.php", {
            NewsDeleteID: id
        },
        function(data) {
            if (data.strip() === 'yes') {
                alert('\u041dовость успешно удалена!');
                $('#NewsRow_' + id).hide(500);
            } else {
                alert('\u041dе удалось совершить операцию!');
            }
        });
    }
}

function ChangeSubcategory() {
    $.post("inc/admin_functions.php", {
        SubcategoryEdit: 1
    },
    function(data) {
        $('#SubcategoryEditTable').html(data);
    });
    $('#edit_rubrik').show(500);
    enableA();
}

function SubcategoryChangeName() {
    $.post("inc/admin_functions.php", {
        SubcategoryChangeName: $('#SubcategoryNewName').val(),
        SubcategoryChangeNameID: $('#SubcategorySelect').val()
    },
    function(data) {
        if (data.strip() === 'yes') {
            $("#SubcategorySelect option[value='" + $('#SubcategorySelect').val() + "']").text($('#SubcategoryNewName').val());
            alert('\u041dазвание рубрики изменено!');
        } else {
            alert('\u0412озникла ошибка!');
        }
    });
}

function DeleteSubcategoryShow() {
    $.post("inc/admin_functions.php", {
        SubcategoryDeleteShow: 1
    },
    function(data) {
        $('#DeleteSubcategoryTable').html(data);
    });
    $('#down_rubrik').show(500);
    enableA();
}

function SubcategoryDeleteSubmit() {
    if (confirm("\u042dтим действием вы удалите рубрику и все новости, входящие в неё. Вы уверены?")) {
        $.post("inc/admin_functions.php", {
            SubcategoryDeleteSubmit: $('#SubcategorySelectDel').val()
        },
        function(data) {
            if (data.strip() === 'yes') {
                $("#SubcategorySelectDel option[value='" + $('#SubcategorySelectDel').val() + "']").remove();
                alert('\u0423даление прошло успешно! Обновите страницу!');
            }
        });
    }
}

function SubcategoryAddShow() {
    $('#new_rubrik').show(500);
    enableA();
}

function AddNewSubcategorySubmit() {
    $.post("inc/admin_functions.php", {
        SubcategoryNewStr: $('#SubcategoryNewStr').val()
    },
    function(data) {
        if (data.strip() === 'yes') {
            alert('\u041dовая рубрика добавлена!');
        }
        if (data.strip() === 'no') {
            alert('\u041fроизошла ошибка!');
        }
        if (data.strip() === 'have') {
            alert('\u0420убрика с таким именем уже существует!');
        }
    });
}

function NewNewsAddShow() {
    $.post("inc/admin_functions.php", {
        NewNewsAddShow: 1
    },
    function(data) {
        $('#NewsAddTable').html(data);
    });
    $('#add_new').show(500);
    enableA();
}

function NewsNewAddSubmit() {
    $.post("inc/admin_functions.php", {
        NewsNewHeaderAdd: $('#NewsNewHeaderAdd').val(),
        NewsNewTextAdd: $('#NewsNewTextAdd').val(),
        SubcategorySelectNewsAdd: $('#SubcategorySelectNewsAdd').val()
    },
    function(data) {
        if (data.strip() === 'yes') {
            alert('\u041dовость успешно добавлена! Обновите страницу!');
            $('#add_new').hide(500);
            disableA();
        } else {
            alert('\u041fроизошла ошибка!');
        }
    });
}

function CheckedAllNews(max) {
    for (i = 0; i < max; i++) {
        if (CheckOrNot('NewsCheck_' + i)) {
            $('#NewsCheck_' + i).prop('checked', false);
            $('#CheckButton').attr('title', '\u0412ыделить все Новости');
        } else {
            $('#NewsCheck_' + i).prop('checked', true);
            $('#CheckButton').attr('title', '\u0421нять выделения с Новостей');
        }
    }
}

function DeleteSelectedNews(max) {
    if (confirm('\u0412ы уверены?')) {
        for (i = 0; i < max; i++) {
            if (CheckOrNot('NewsCheck_' + i)) {
                $.post("inc/admin_functions.php", {
                    NewsDeleteID: $('#NewsCheck_' + i).val()
                },
                function() {
                });
                $('#NewsRow_' + $('#NewsCheck_' + i).val()).hide(500);
            }
        }
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
function RealtyLock(obj) {
    $.post("inc/admin_functions.php", {
        RealtyLock: $(obj).attr('alt')
    }
    , function(data)
    {
        if (data === 'yes') {
            if ($(obj).attr('src') === '../images/unlock.png') {
                $(obj).fadeOut(500, function() {
                    $(obj).attr('src', '../images/lock.png');
                    $(obj).attr('title', 'Закрепить');
                }).fadeIn(500);
            } else {
                $(obj).fadeOut(500, function() {
                    $(obj).attr('src', '../images/unlock.png');
                    $(obj).attr('title', 'Открепить');
                }).fadeIn(500);
            }
        } else {
            alert('Возникла ошибка!');
        }
    });
}