function WriteBlog() {
    $.post('inc/ajax.php', $('#WriteBlog').serialize(), function(data) {
        if (data === 'yes') {
            $('#WriteBlog input').val('');
            $('#WriteBlog textarea').val('');
            $('#load_image_s').html('');
            alert('Статья добавлена на модерацию!');
        }
        if (data === 'no') {
            alert('Заполните все обязательные поля!');
        }
    });
}