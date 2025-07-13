function ImmoEmail(email) {
    $('#send_email').css('display', 'block');
    $('#ImmoEmailEmail').val(email);
    enableA();
}
function AgentsInfo(id) {
    $.post("inc/admin_functions.php",{
        AgentsInfoID: id
    } ,
    function(data)
    {
        $('#InfoAgentsTable').html(data);
    })
    $('#info_agentstvo').css('display','block');
    enableA();
}
function AgentEdit(id) {
    $.post("inc/admin_functions.php",{
        AgentsEditID: id
    } ,
    function(data)
    {
        $('#AgentEditTable').html(data);
    })
    $('#edit_agentstvo').css('display','block');
    enableA();
}
function SearchAddress() {
    if (($('#AgentEditAddress').val()).length > 2) {
        $.post("inc/admin_functions.php",{
            SearchAddress:$('#AgentEditAddress').val()
        } ,
        function(data)
        {
            $('#AgentAddressResult').html(data);
        })
    } else {
        $('#AgentAddressResult').html('<select id="ImmoAddressChosen" style="width: 100%;" name="ImmoAddressChosen"><option selected value="0"></option></select>');
    }
}
function AgentEditSubmit(id) {
    $.post("inc/admin_functions.php",{
        AgentEditName:$('#AgentEditName').val(),
        AgentEditPhone:$('#AgentEditPhone').val(),
        AgentEditEmail:$('#AgentEditEmail').val(),
        AgentEditSite:$('#AgentEditSite').val(),
        AgentEditDescr:$('#AgentEditDescr').val(),
        AgentEditFName:$('#AgentEditFName').val(),
        AgentEditLName:$('#AgentEditLName').val(),
        AgentEditOName:$('#AgentEditOName').val(),
        ImmoAddressChosen:$('#ImmoAddressChosen').val(),
        AgentEditSubmitID: id
    } ,
    function(data)
    {
        alert(data);
    })
}
function AgentAvatarLoad(id) {
    $.post("inc/admin_functions.php",{
        AgentAvatarLoadID: id
    } ,
    function(data)
    {
        $('#AgentAvatarTable').html(data);
    })
    $('#AgentHiddenID').val(id);
    $('#avatar_agentstvo').css('display','block');
    enableA();
}
function AvatarChange(filename) {
    $.post("inc/admin_functions.php",{
        AgentAvatarChangeID: $('#AgentHiddenID').val(),
        AgentAvatarChangeURL: filename
    } ,
    function() {})
    $('#AgentTableImage_'+$('#AgentHiddenID').val()).attr('src',filename);
    $('#AgentAvatarShow').css('display','block');
    $('#AgentTableImage_'+$('#AgentHiddenID').val()).css('display','block');
}
function DeleteAgentAvatar() {
    $.post("inc/admin_functions.php",{
        AgentAvatarDeleteID: $('#AgentHiddenID').val()
    } ,
    function() {
        $('#AgentAvatarShow').css('display','none');
        $('#AgentTableImage_'+$('#AgentHiddenID').val()).css('display','none');
    })
}
function ChangePasswordWindow(id) {
    $('#AgentPasswordID').val(id);
    $('#pass_agentstvo').css('display','block');
    enableA();
}
function LinesCompare() {
    if($('#AgentPassLine2').val() == $('#AgentPassLine1').val()) {
        $('#AgentPassLine2').css('background-color','#77FF77');
    } else {
        $('#AgentPassLine2').css('background-color','#FF7777');
    }
}
function ChangeAgentPassword() {
    if($('#AgentPassLine2').val() == $('#AgentPassLine1').val()) {
        $.post("inc/admin_functions.php",{
            PasswordChangeID: $('#AgentPasswordID').val(),
            PasswordChangePass: $('#AgentPassLine2').val()
        } ,
        function(data) {
            alert(data);
        })
    } else {
        alert('\u041fароли не совпадают!');
    }
}
function AgentInRegister(id, type) {
    $.post("inc/admin_functions.php",{
        AgentRegisterID:id, 
        AgentRegisterAct:type
    } ,
    function(data)
    {
        if (data.strip() == 'yes') {
            $('#AgentRegister_'+id).attr('src', '../images/down.png');
            $('#AgentRegister_'+id).attr('title', '\u041eтменить поднятие');
            $('#AgentRegister_'+id).attr('onClick', 'AgentInRegister('+id+',2);');
        }
        if (data.strip() == 'no') {
            $('#AgentRegister_'+id).attr('src', '../images/up.png');
            $('#AgentRegister_'+id).attr('title', '\u041fоднять Агентство');
            $('#AgentRegister_'+id).attr('onClick', 'AgentInRegister('+id+',1);');
        }
        if (data.strip() == 'error') {
            alert('\u0412озникла непредвиденная ошибка!');
        }
    })
}
//ImmoDisEnID
function DisEnAgent(id,act) {
    $.post("inc/admin_functions.php",{
        AgentDisEnID: id,
        AgentDisEnAct: act
    } , function(data) {
        if(data.strip() == 'yes') {
            if(act == 1) {
                $('#AgentIDState_'+id).attr('src','../images/disable_1.png');
                $('#AgentIDState_'+id).attr('title','\u0421крыть Агентство');
                $('#AgentIDState_'+id).attr('onClick','DisEnAgent(' + id + ',0);');
                $('#AgentStateInTable_'+id).attr('class','style_4_1');
                $('#AgentStateInTable_'+id).text('\u0410ктивно');
            } else {
                $('#AgentIDState_'+id).attr('src','../images/enable.png');
                $('#AgentIDState_'+id).attr('title','\u041fоказать Агентство');
                $('#AgentIDState_'+id).attr('onClick','DisEnAgent(' + id + ',1);');
                $('#AgentStateInTable_'+id).attr('class','style_4_2');
                $('#AgentStateInTable_'+id).text('\u0421крыто');
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
function DeleteAgent(id) {
    if(confirm("\u042dтим действием вы полностью удалите агентство и все данные, связанные с ним. Вы уверены?")) {
        $.post("inc/admin_functions.php",{
            AgentIDDelSubmit: id
        } , function(data) {
            alert(data);
            $('#AgentRowID_'+id).css('display','none');
        })
    }
}
function AddAgentWindow() {
    $('#add_agentstvo').css('display','block');
    enableA();
}
function AddAgentIntoTable() {
    if($('#AddAgentPassword2').val() == $('#AddAgentPassword1').val()) {
        $.post("inc/admin_functions.php",{
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
            AddAgentDays: parseInt($('#AddAgentDays').val(),10),
            AddAgentDescription: $('#AddAgentDescription').val()
        } , function(data) {
            alert(data);
        })
    } else {
        alert('\u041fароли не совпадают!');
    }
}
function PasswordCompareAgent() {
    if($('#AddAgentPassword2').val() == $('#AddAgentPassword1').val()) {
        $('#AddAgentPassword2').css('background-color','#77FF77');
    } else {
        $('#AddAgentPassword2').css('background-color','#FF7777');
    }
}
function CheckOrNot(id) {
    if ($('#'+id).is(':checked')) {
        return 1;
    } else {
        return 0;
    }
}
function CheckedAgentVal(max,act) {
    if(confirm('\u0412ы уверены?')) {
        for(i=0;i<max;i++) {
            if (CheckOrNot('CheckedAgents_'+i)) {
                if(act==1) {
                    $.post("inc/admin_functions.php",{
                        AgentIDDelSubmit: $('#CheckedAgents_'+i).val()
                    } , function() {})
                    $('#AgentRowID_'+$('#CheckedAgents_'+i).val()).css('display','none');
                }
                if (act==2) {
                    DisEnAgent($('#CheckedAgents_'+i).val(),1);
                }
                if (act==3) {
                    DisEnAgent($('#CheckedAgents_'+i).val(),0);
                }
            }
        }
    }
}
function CheckedAllAgents(max) {
    for(i=0;i<max;i++) {
        if (CheckOrNot('CheckedAgents_'+i)){
            $('#CheckedAgents_'+i).attr('checked','');
            $('#CheckButton').attr('title','\u0412ыделить все Агентства');
        } else {
            $('#CheckedAgents_'+i).attr('checked','checked');
            $('#CheckButton').attr('title','\u0421нять выделения с Агентств');
        }
    }
}