function ExpertReg(obj) {
    $.post($(obj).closest('form').attr('action'), $(obj).closest('form').serialize(), function(data) {
        alert(data)
        if (data === 'yes') {
            $(obj).closest('form').find('input').val('');
            $(obj).closest('form').find('textarea').val('');
            alert('Вам на E-mail отправлено письмо с дальнейшей инструкцией!')
        }
        if (data === 'empty') {
            alert('Заполните обязательные поля!');
        }
        if (data === 'exist') {
            alert('Пользователь с таким E-mail уже существует!');
        }
    });
}