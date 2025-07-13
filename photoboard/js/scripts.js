function ShowImage(obj) {
    var url = $(obj).attr('src').replace('photo/1_', 'photo/');
    $('#wind_poto img').attr('src', url);
    $('#wind_poto').show();
    enableA();
}