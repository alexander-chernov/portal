function ShowInfo(id) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "ShowInfoOrganization=" + id,
        success: function(data) {
            $('#info_company').html(data);
            $('#info_company').show(500);
            enableA();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function ChangeOrganization(id) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "ChangeOrganization=" + id,
        success: function(data) {
            $('#edit_company').html(data);
            $('#edit_company').show(500);
            enableA();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function ChangeAddress(id_ad, id, id_dop) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "ChangeAddress=" + id_ad + "&ChangeAddressID=" + id + "&ChangeAddressDop=" + id_dop,
        success: function(data) {
            $('#red_adres').html(data);
            $('#red_adres').slideDown(500);
            enableA();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function ChangeWorkDay(obj) {
    var type = 0;
    if ($(obj).prop("checked") === true) {
        type = 1;
    } else {
        type = 0;
    }
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "ChangeWorkDay=" + $(obj).val() + "&ChangeWorkDayT=" + type,
        success: function() {
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function ChangeTime(id, pos, obj) {
    $('#bl' + id + pos).html('<a class="a_1" onclick="SaveTime(' + id + ',this,\'' + $(obj).attr('id') + '\',' + pos + ');"><img src="../images/enable.png" title="\u0421охранить изменения" alt=""></a>');
    $(obj).css('background', '#ffff00');
}

function SaveTime(id, obj1, obj2, pos) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "SaveTimeID=" + id + "&SaveTimeType=" + pos + "&SaveTimeVal=" + $('#' + obj2).val(),
        success: function(data) {
            if (data === 'yes') {
                $(obj1).hide(500);
                $('#' + obj2).css('background', '#66ff66');
            } else {
                $('#' + obj2).css('background', '#ff6666');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function ChangePhoneNumbField(id, obj) {
    $('#phone_s_' + id).html('<a class="a_1" onclick="ChangePhoneNumb(' + id + ');"><img src="../images/enable.png" title="\u0421охранить изменения" alt=""></a>');
    $(obj).css('background', '#ffff00');
}

function ChangePhoneNumb(id) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "ChangePhoneNumb=" + id + "&ChangePhoneNumbVal=" + $('#phone_' + id).val(),
        success: function(data) {
            if (data === 'yes') {
                $('#phone_s_' + id).hide(500);
                $('#phone_' + id).css('background', '#66ff66');
            } else {
                $('#phone_' + id).css('background', '#ff6666');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function ChangePhoneType(id, obj) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "ChangePhoneType=" + id + "&ChangePhoneTypeT=" + $(obj).val(),
        success: function(data) {
            if (data === 'yes') {
                alert('\u0422ип изменён!');
            } else {
                alert('\u0412озникла ошибка!');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function DeletePhone(id) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "DeletePhone=" + id,
        success: function(data) {
            if (data === 'yes') {
                $('#phone_line_' + id).hide(500);
            } else {
                alert('\u0412озникла ошибка!');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function AddPhone(parent) {
    $.post("inc/ajax.php", {
        AddPhone: parent
    }, function(data)
    {
        if (data) {
            var table_p = document.getElementById('phones_table');
            var tr1 = document.createElement('tr');
            tr1_d = table_p.appendChild(tr1);
            tr1_d.setAttribute('id', 'phone_line_' + data);
            var td1_1 = document.createElement('td');
            td1_1_d = tr1.appendChild(td1_1);
            var sel1 = document.createElement('select');
            sel1_d = td1_1.appendChild(sel1);
            sel1_d.setAttribute('onchange', 'ChangePhoneType(' + data + ',this);');
            sel1.innerHTML = '<option selected value="1">\u0422елефон</option><option value="2">Факс</option><option value="3">Единая служба</option>';
            var td1_2 = document.createElement('td');
            td1_2_d = tr1.appendChild(td1_2);
            var inp1 = document.createElement('input');
            inp1_d = td1_2.appendChild(inp1);
            inp1_d.setAttribute('id', 'phone_' + data);
            inp1_d.setAttribute('type', 'text');
            inp1_d.setAttribute('value', '');
            inp1_d.setAttribute('onkeyup', 'ChangePhoneNumbField(' + data + ',this)');
            var span1 = document.createElement('span');
            span1_d = td1_2.appendChild(span1);
            span1_d.setAttribute('id', 'phone_s_' + data);
            var td1_3 = document.createElement('td');
            td1_3_d = tr1.appendChild(td1_3);
            var a1 = document.createElement('a');
            a1_d = td1_3.appendChild(a1);
            a1_d.setAttribute('onclick', 'DeletePhone(' + data + ')');
            a1_d.setAttribute('class', 'a_1');
            var img1 = document.createElement('img');
            img1_d = a1.appendChild(img1);
            img1_d.setAttribute('src', '../images/delete.png');
            img1_d.setAttribute('title', '\u0423далить телефон');
            img1_d.setAttribute('alt', '');
        } else {
            alert('\u0412озникла ошибка!');
        }
    });
}

function DeleteFromCategory(id) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "DeleteFromCategory=" + id,
        success: function(data) {
            if (data === 'yes') {
                $('#cat_block_' + id).hide(500);
            } else {
                alert('\u0412озникла ошибка!');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function OpenCategoryForm(id) {
    $.post("inc/ajax.php",
            {
                OpenCategoryFormID: id
            }, function(data)
    {
        $('#cat_td_' + id).html(data.categories);
        $('#big_sub_td_' + id).html(data.bigsub);
        $('#sub_td_' + id).html(data.sub);
        $('#SaveGal_' + id).css('visibility', 'visible');
    }, "json");
}

function OnCategoryChange(id) {
    $.post("inc/ajax.php",
            {
                OnCategoryChange: $('#category_select_change_' + id).val(),
                OnCategoryChangeID: id
            }, function(data)
    {
        if (data.error !== 1) {
            $('#big_sub_td_' + id).html(data.bigsub);
            $('#sub_td_' + id).html(data.sub);
        } else {
            $('#big_sub_td_' + id).html('');
            $('#sub_td_' + id).html('');
            alert('\u041dевозможно выбрать данную категорию! \u0412 категории отсутствуют рубрики или подрубрики!');
        }
    }, "json");
}

function OnBigSChange(id) {
    $.post("inc/ajax.php",
            {
                OnBigSChange: $('#bs_select_change_' + id).val(),
                OnBigSChangeID: id
            }, function(data)
    {
        if (data.error !== 1) {
            $('#sub_td_' + id).html(data.sub);
        } else {
            $('#sub_td_' + id).html('');
            alert('\u041dевозможно выбрать данную рубрику! \u0412 рубрике отсутствуют подрубрики!');
        }
    }, "json");
}

function SaveChangedCategory(id, reopen) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "SaveChangedCategoryS=" + $('#sub_select_change_' + id).val() +
                "&SaveChangedCategoryID=" + id,
        success: function(data) {
            if (data === 'yes') {
                ChangeOrganization(reopen);
            } else {
                alert('\u0412озникла ошибка!');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function CreateNewCategorySelect(id) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "CreateNewCategorySelect=" + id,
        success: function(data) {
            if (data) {
                ChangeOrganization(id);
            } else {
                alert('\u0412озникла ошибка!');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function ShowAddOrganizationWindow() {
    $('#admin_map').attr('src', "/admin/admin_map/map.php?d=2");

    $('#add_company').slideDown(500);
    enableA();
}

function AddNewOrganizationAddress(id) {
    $('#ANOAS').val(id);
    document.getElementById('add_adres').style.display = 'block';
    enableA();
}

function AddNewOrganizationAddressSubmit() {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "ANOAA=" + $('#ANOAA').val() + "&ANOAS=" + $('#ANOAS').val(),
        success: function(data) {
            if (data === 'yes') {
                alert('\u0410дрес успешно добавлен!');
                CloseWindow('add_adres');
            } else {
                alert('\u0412озникла ошибка!');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function DeleteAddressFromOrganization(id) {
    if (window.confirm('\u0412ы действительно хотите удалить адрес из организации?')) {
        $.ajax({
            type: "POST",
            url: "inc/ajax.php",
            data: "DAFO=" + id,
            success: function(data) {
                if (data === 'yes') {
                    $('#ATB_id_' + id).hide(500);
                    alert('\u0410дрес успешно удалён!');
                } else {
                    alert('\u0412озникла ошибка!');
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("\u0412озникла ошибка!");
            }
        });
    }
}

function DeleteAllOrganization(id) {
    if (window.confirm('\u0412ы действительно хотите удалить организацию и все её привязки?')) {
        $.ajax({
            type: "POST",
            url: "inc/ajax.php",
            data: "DeleteAllOrganization=" + id,
            success: function(data) {
                if (data === 'yes') {
                    $('#otntd_' + id).hide(500);
                } else {
                    alert('\u0412озникла ошибка!');
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("\u0412озникла ошибка!");
            }
        });
    }
}

function SaveNewAddressCH(id, id_org) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "SaveNewAddressCH=" + $('#toac').val() + "&SaveNewAddressCHID=" + id,
        success: function(data) {
            if (data) {
                ChangeOrganization(id_org);
                alert('\u0423спешно сохранено!');
            } else {
                alert('\u0412озникла ошибка!');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function SaveNewAddressAdvCH(id, id_org) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "SaveNewAddressAdvCH=" + $('#toaca').val() + "&SaveNewAddressAdvCHID=" + id,
        success: function(data) {
            if (data) {
                ChangeOrganization(id_org);
                alert('\u0423спешно сохранено!');
            } else {
                alert('\u0412озникла ошибка!');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function SaveAllOrgParams(id) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "SaveAllOrgParamsName=" + $('#org_p_name').val() +
                "&SaveAllOrgParamsID=" + id +
                "&SaveAllOrgParamsSite=" + $('#org_p_site').val() +
                "&SaveAllOrgParamsEmail=" + $('#org_p_email').val() +
                "&SaveAllOrgParamsDescr=" + $('#org_p_descr').val(),
        success: function(data) {
            if (data) {
                alert('\u0423спешно сохранено!');
            } else {
                alert('\u0412озникла ошибка!');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("\u0412озникла ошибка!");
        }
    });
}

function CanBeCreated() {
    if ($('#new_organization_name').val().length > 5) {
        $.ajax({
            type: "POST",
            url: "inc/ajax.php",
            data: "CanBeCreated=" + $('#new_organization_name').val(),
            success: function(data) {
                if (data === 'yes') {
                    $('#new_organization_post').prop('disabled', false);
                    $('#new_organization_name').css('background', '#aaffaa');
                } else {
                    $('#new_organization_post').prop('disabled', true);
                    $('#new_organization_name').css('background', '#ffaaaa');
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    } else {
        $('#new_organization_post').prop('disabled', true);
        $('#new_organization_name').css('background', '#ffaaaa');
    }
}

function ChangeCategory(id) {
    $.post('inc/admin_functions.php', {
        ChangeCategory: id
    },
    function(data) {
        if (data) {
            $('#CategoryName').val(data);
            $('#CategoryID').val(id);
        } else {
            alert('\u0412озникла ошибка!');
        }
    });
    $('#edit_catalog').show(500);
    enableA();
}

function SaveCategory() {
    $.post('inc/admin_functions.php', {
        SaveCategory: $('#CategoryName').val(),
        SaveID: $('#CategoryID').val()
    },
    function(data) {
        if (data === 'yes') {
            alert('\u0423спешно сохранено!');
        } else {
            alert('\u0412озникла ошибка!');
        }
    });
}

function DeleteCategory(id) {
    if (confirm('\u0411удут удалены все входящие рубрики и подрубрики!')) {
        $.post('inc/admin_functions.php', {
            DeleteCategory: id
        },
        function(data) {
            if (data === 'yes') {
                $('#tr_id_' + id).hide(500);
                alert('\u0423спешно удалено!');
            } else {
                alert('\u0412озникла ошибка!');
            }
        });
    }
}

function AddCategory() {
    $.post('inc/admin_functions.php', {
        AddCategory: $('#NewCategory').val()
    },
    function(data) {
        if (data === 'yes') {
            alert('\u0423спешно сохранено!');
        } else {
            alert('\u0412озникла ошибка!');
        }
    });
}

function EditSubcategory(id) {
    $.post('inc/admin_functions.php', {
        EditSubcategory: id
    },
    function(data) {
        $('#TableSubEdit').html(data);
    });
    $('#edit_catalog_rubrik').show(500);
    enableA();
}

function SaveSubcategory() {
    $.post('inc/admin_functions.php', {
        SaveSubcategory: $('#SaveId').val(),
        SaveSubcategoryName: $('#EditSubcategory').val(),
        SaveSubcategoryCat: $('#EditCategory').val()
    },
    function(data) {
        if (data === 'yes') {
            $('#SubId_' + $('#SaveId').val()).text($('#EditSubcategory').val());
            $('#CatId_' + $('#SaveId').val()).text($('#EditCategory option:selected').text());
            alert('\u0423спешно сохранено!');
        } else {
            alert('\u0412озникла ошибка!');
        }
    });
}

function DeleteSubcategory(id) {
    if (confirm('\u0412ы уверены, что хотите удалить рубрику со всеми входящими в неё подрубриками?')) {
        $.post('inc/admin_functions.php', {
            DeleteSubcategory: id
        },
        function(data) {
            if (data === 'yes') {
                $('#TrId_' + id).hide(500);
                alert('\u0423спешно удалено!');
            } else {
                alert('\u0412озникла ошибка!');
            }
        });
    }
}

function ShowAddWindow() {
    $('#add_catalog_rubrik').show(500);
    enableA();
}

function AddSubcategory() {
    $.post('inc/admin_functions.php', {
        AddSubcategory: $('#AddSubCat').val(),
        AddSubcategoryPar: $('#AddCat').val()
    },
    function(data) {
        if (data === 'yes') {
            alert('\u0423спешно добавлено!');
        } else {
            alert('\u0412озникла ошибка!');
        }
    });
}

function AddSubSub() {
    $('#add_catalog_podrubrik').show(500);
    enableA();
}

function AddSubSubPost() {
    $.post('inc/admin_functions.php', {
        AddSubSubPost: $('#AddSubSubPar').val(),
        AddSubSubPostPar: $('#AddSubSubName').val()
    },
    function(data) {
        if (data === 'yes') {
            alert('\u0423спешно добавлено!');
        } else {
            alert('\u0412озникла ошибка!');
        }
    });
}

function DeleteSubSub(id) {
    $.post('inc/admin_functions.php', {
        DeleteSubSub: id
    },
    function(data) {
        if (data === 'yes') {
            $('#sstid_' + id).hide(500);
            alert('\u0423спешно удалено!');
        } else {
            alert('\u0412озникла ошибка!');
        }
    });
}

function RedaktSubSub(id) {
    $.post('inc/admin_functions.php', {
        RedaktSubSub: id
    },
    function(data) {
        $('#srss option[value="' + data.parent + '"]').prop('selected', true);
        $('#irss').val(data.name);
        $('#hrss').val(id);
        $('#edit_catalog_podrubrik').slideDown(500);
        enableA();
    }, "json");
}

function SaveSubSubRedakt() {
    $.post('inc/admin_functions.php', {
        SaveSubSubRedaktID: $('#hrss').val(),
        SaveSubSubRedaktPar: $('#srss').val(),
        SaveSubSubRedaktName: $('#irss').val()
    },
    function(data) {
        if (data === 'yes') {
            alert('\u041eбновления внесены!');
            CloseWindow('edit_catalog_podrubrik');
        }
    });
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
function AddBanner(type) {
    $.post('inc/admin_functions.php', {
        AddBanner: type
    },
    function(data) {
        $('#Banners_' + type).html($('#Banners_' + type).html() + data);
    });
}
function DeleteBanner(id, obj) {
    if (window.confirm('Вы действительно хотите удалить баннер?')) {
        $.post('inc/admin_functions.php', {
            DeleteBanner: id
        },
        function(data) {
            if (data === 'yes') {
                $(obj).parent().parent().hide(500);
            }
        });
    }
}