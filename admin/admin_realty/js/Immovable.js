function ImmoEmail(email) {
    $('#send_email').css('display', 'block');
    $('#ImmoEmailEmail').val(email);
    enableA();
}
function ImmoInfo(num, it, user, date, state, days, contact, phone, address, email, ut) {
    $('#info_obiavlenie_block').css('display', 'block');
    $('#ImmoAdNum').text(num);
    $('#ImmoAdIT').text(it);
    $('#ImmoAdUser').text(user);
    $('#ImmoAdDate').text(date);
    $('#ImmoAdState').text(state);
    $('#ImmoAdDays').text(days+' дней');
    $('#ImmoAdContact').text(contact);
    $('#ImmoAdPhone').text(phone);
    $('#ImmoAdAddress').text(address);
    $('#ImmoAdEmail').text(email);
    $('#ImmoAdUT').text(ut);
    enableA();
}
function ImmoChangeAd(i) {
    $.post("inc/admin_functions.php",{
        AdsID:i
    } ,
    function(data)
    {
        $('#edit_obiavlenie').html(data);
    })
    $('#edit_obiavlenie').css('display', 'block');
    enableA();
}
function SearchAddress() {
    if (($('#ImmoAddressSearch').val()).length > 2) {
        $.post("inc/admin_functions.php",{
            SearchAddress:$('#ImmoAddressSearch').val()
        } ,
        function(data)
        {
            $('#ImmoAddressResult').html(data);
        })
    } else {
        $('#ImmoAddressResult').html('<select id="ImmoAddressChosen" style="width: 100%;" name="ImmoAddressChosen"></select>');
    }
}
function CheckOrNot(id) {
    if ($('#'+id).is(':checked')) {
        return 1;
    } else {
        return 0;
    }
}
function PhotoEdit(id) {
    $.post("inc/admin_functions.php",{
        PhotoID:id
    } ,
    function(data)
    {
        $('#photo_obiavlenie').html(data);
    })
    $('#photo_obiavlenie').css('display', 'block');
    enableA();
}
function PhotoShow(url) {
    $('#ImmoImageShow').attr('src','../'+url);
    document.getElementById('photo_windows').style.display='block';
}
function DeletePhoto(id) {
    if(confirm('\u0412ы уверены?')) {
        $.post("inc/admin_functions.php",{
            PhotoDelID:id
        } ,
        function(data)
        {
            alert(data);
        })
        $('#photo_id_'+id).css('display', 'none');
    }
}
function SpecialAction(id, type) {
    $.post("inc/admin_functions.php",{
        SpecialID:id, 
        ActionType:type
    } ,
    function(data)
    {
        if (data.strip() == 'yes') {
            $('#special_'+id).attr('src', '../images/spec_2.png');
            $('#special_'+id).attr('title', '\u0423брать из спец предложения');
            $('#special_'+id).attr('onClick', 'SpecialAction('+id+',2);');
        }
        if (data.strip() == 'no') {
            $('#special_'+id).attr('src', '../images/spec_1.png');
            $('#special_'+id).attr('title', '\u0414обавить в спец предложения');
            $('#special_'+id).attr('onClick', 'SpecialAction('+id+',1);');
        }
        if (data.strip() == 'error') {
            alert('\u0412озникла непредвиденная ошибка!');
        }
    })
}
function AddDaysImmo(id) {
    $('#ImmoForAddDays').val(id);
    $.post("inc/admin_functions.php",{
        ImmoAddDays: $('#ImmoForAddDays').val()
    } , function(data) {
        $('#LastDays').text(data);
    })
    $('#AddDayBlock').css('display', 'block');
    enableA();
}
function AddDaysSubmit() {
    $.post("inc/admin_functions.php",{
        ImmoAddDaysID: $('#ImmoForAddDays').val(),
        ImmoAddDaysSubmit: parseInt($('#DaysForAddDays').val(),10)
    } , function(data) {
        alert(data);
    })
    AddDaysImmo($('#ImmoForAddDays').val());
}
function DisEnAd(id,act) {
    $.post("inc/admin_functions.php",{
        ImmoDisEnID: id,
        ImmoDisEnAct: act
    } , function(data) {
        if(data.strip() == 'yes') {
            if(act == 1) {
                $('#endis_'+id).attr('src','../images/disable_1.png');
                $('#endis_'+id).attr('title','\u0421крыть объявление');
                $('#endis_'+id).attr('onClick','DisEnAd(' + id + ',0);');
                $('#state_immo_'+id).attr('class','style_4_1');
                $('#state_immo_'+id).text('\u0410ктивно');
            } else {
                $('#endis_'+id).attr('src','../images/enable.png');
                $('#endis_'+id).attr('title','\u041fоказать объявление');
                $('#endis_'+id).attr('onClick','DisEnAd(' + id + ',1);');
                $('#state_immo_'+id).attr('class','style_4_2');
                $('#state_immo_'+id).text('\u0421крыто');
            }
        }
    })
}
function BlockIP(ip) {
    $.post("inc/admin_functions.php",{
        IPUserBan: ip
    } , function(data) {
        alert(data);
    })
}
function DeleteAd(id) {
    if(confirm("\u042dтим действием вы полностью удалите объявление и все данные, связанные с ним. Вы уверены?")) {
        $.post("inc/admin_functions.php",{
            ImmoIDDelSubmit: id
        } , function(data) {
            alert(data);
        })
    }
}
function SearchWidget(act) {
    if(act == 1) {
        $('#lupa_plus').css('display','none');
        $('#lupa_minus').css('display','block');
        $('#parametr_search').css('display','block');
    }
    if(act==2) {
        $('#lupa_plus').css('display','block');
        $('#lupa_minus').css('display','none');
        $('#parametr_search').css('display','none');
    }
}
function CheckedAdsVal(max,act) {
    for(i=0;i<max;i++) {
        if (CheckOrNot('CheckedAds_'+i)) {
            if(act==1) {
                if(confirm('\u0412ы уверены?')) {
                    $.post("inc/admin_functions.php",{
                        ImmoIDDelSubmit: i
                    } , function() {})
                }
            }
            if (act==2) {
                DisEnAd($('#CheckedAds_'+i).val(),1);
            }
            if (act==3) {
                DisEnAd($('#CheckedAds_'+i).val(),0);
            }
        }
    }
    alert(query);
}

function CheckedAllImmo(max) {
    for(i=0;i<max;i++) {
        if (CheckOrNot('CheckedAds_'+i)){
            $('#CheckedAds_'+i).attr('checked','');
            $('#CheckButton').attr('title','\u0412ыделить все Объявления');
        } else {
            $('#CheckedAds_'+i).attr('checked','checked');
            $('#CheckButton').attr('title','\u0421нять выделения с Объявлений');
        }
    }
}