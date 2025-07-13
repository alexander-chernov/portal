function Registration() {
    if ($('#password').val() === $('#password2').val() && $('#password2').val().length > 3) {
        return true;
    }
    return false;
}
function LoginAvailable() {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "LoginAvailable=" + $('#login').val(),
        success: function(data) {
            if (data === 'yes') {
                if ($('#login').val().length > 3) {
                    $('#reg_form').attr('onsubmit', 'return Registration();');
                    $('#login').css('background', '#ddffdd');
                } else {
                    $('#reg_form').attr('onsubmit', 'return false;');
                    $('#login').css('background', '#ffdddd');
                }
            } else {
                $('#reg_form').attr('onsubmit', 'return false;');
                $('#login').css('background', '#ffdddd');
            }
        }
    });
}
function EmailAvailable() {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "EmailAvailable=" + $('#email').val(),
        success: function(data) {
            if (data === 'yes') {
                if ($('#email').val().length > 3) {
                    $('#reg_form').attr('onsubmit', 'return Registration();');
                    $('#email').css('background', '#ddffdd');
                } else {
                    $('#reg_form').attr('onsubmit', 'return false;');
                    $('#email').css('background', '#ffdddd');
                }
            } else {
                $('#reg_form').attr('onsubmit', 'return false;');
                $('#email').css('background', '#ffdddd');
            }
        }
    });
}
function PhoneAvailable() {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: "PhoneAvailable=" + $('#phone').val(),
        success: function(data) {
            if (data === 'yes') {
                if ($('#phone').val().length === 11) {
                    $('#reg_form').attr('onsubmit', 'return Registration();');
                    $('#phone').css('background', '#ddffdd');
                } else {
                    $('#reg_form').attr('onsubmit', 'return false;');
                    $('#phone').css('background', '#ffdddd');
                }
            } else {
                $('#reg_form').attr('onsubmit', 'return false;');
                $('#phone').css('background', '#ffdddd');
            }
        }
    });
}