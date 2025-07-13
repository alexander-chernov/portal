$(document).ready(function() {
    var div = $('#tomsk-line-informer');
    var script = div.prev('script');
    var attrs = script.attr('src').split(/\?/);
    var width = '100px';
    var radius = '0px';
    var background = 'green';
    for (var i = 1; i < attrs.length; i++) {
        var param = attrs[i].split(/\=/);
        if (param[0].match(/^color/)) {
            if (param[1].match(/[0-9]/) && !param[1].match(/\#/)) {
                background = '#' + param[1];
            } else {
                background = param[1];
            }
        }
        if (param[0].match(/^width/)) {
            width = param[1];
            if(width.match(/[0-9]$/)) {
                width += 'px';
            }
        }
        if (param[0].match(/^radius/)) {
            radius = param[1];
            if(radius.match(/[0-9]$/)) {
                radius += 'px';
            }
        }
    }
    div.attr({
        'title': 'Информационный портал Недвижимость Томска'
    }).css({
        'width': width,
        'padding': '3px 10px 3px 7px',
        'background': background,
        'border-radius': radius,
        'cursor': 'pointer',
        'box-shadow': 'inset 0px 0px 15px rgba(0,0,0,0.3)'
    });
    var img = $('<img>').attr({
        'src': '/images/informer.png',
        'alt': 'TOMSK-LINE.ru'
    }).css({
        'width': '100%',
        'vertical-align': 'top'
    });
    img.appendTo(div);
    div.on("mouseover",function(){
        $(this).css('opacity', '0.8');
    });
    div.on("mouseout",function(){
        $(this).css('opacity', '1');
    });
});