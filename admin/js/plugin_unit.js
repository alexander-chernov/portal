$('#menu_2 a').click(function(){
    var index = $("#menu_2 a").index(this);
    $('#content_all_block').animate({
        "marginTop" : -index*720 + "px"
        });
    return false;
});