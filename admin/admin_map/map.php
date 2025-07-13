<?php
define('TOMSKLINE', 1);
/**
 * Created by JetBrains PhpStorm.
 * User: Alexander A. Chernov
 * Date: 02.12.13
 * Time: 1:23
 * To change this template use File | Settings | File Templates.
 */
?>
<!DOCTYPE HTML>
<html id="html">
<head>
<title>TOMSK-LINE</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script src="http://api-maps.yandex.ru/2.0/?load=package.full,package.geoObjects,package.editor&lang=ru-RU" type="text/javascript"></script>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>


<script type="text/javascript">

ymaps.ready(init);
function init() {
    var myMap = new ymaps.Map("map", {
        center:[56.496581,84.963502], // Tomsk\
        propagateEvents : true,
        zoom: 15,
        behaviors: ['default', 'scrollZoom']
    });
    myMap.controls
        .add('zoomControl', { left: 5, top: 5 })
    ;
    var objCount = "";
    myGeoObject = [];
    myPlacemark = [];
    myMap.events.add('click', function (e) {
        myMap.balloon.close();
        var coords = e.get('coordPosition');
        ymaps.geocode(coords).then(function (res) {
            var names = [];
            var res_arr = {};
            var arr = {};
            var rrr = {};
            var kind, name;
            res.geoObjects.each(function (obj) {
                arr = obj.properties.get('metaDataProperty');
                kind = arr['GeocoderMetaData'].kind;
                name = obj.properties.get('name');
                res_arr[kind] = name;
                names.push(obj.properties.get('name'));
            });
            myMap.balloon.open(coords, {
                contentHeader:'',
                contentBody: '<p class=address>'+names[2]+'<br>'+names[0]+'</p>',
                contentFooter:'X='+coords[0].toPrecision(8)+', Y='+coords[1].toPrecision(8)
            });


            <?php
            if ($_GET['d']==1) {
                ?>
                $('#create_district_x',top.document).val(coords[0].toPrecision(8));
                $('#create_district_y',top.document).val(coords[1].toPrecision(8));
                <?php
            } elseif ($_GET['d']==2) {
            ?>
            $('#new_organization_house_num_x',top.document).val(coords[0].toPrecision(8));
            $('#new_organization_house_num_y',top.document).val(coords[1].toPrecision(8));

            <?php
            } else {
                ?>
                $('#create_house_num_x',top.document).val(coords[0].toPrecision(8));
                $('#create_house_num_y',top.document).val(coords[1].toPrecision(8));
                <?php
            }
            ?>
        });
    });
    myMap.events.add('contextmenu', function (e) {
        myMap.hint.show(e.get('coordPosition'), '������ ������� ����������');
    });
}
</script>
</head>
<body style="padding: 0px; margin: 0px;" id="body">
    <div id="map" style="clear:left; width:400px; height:300px;float:left;"></div>
</body>
</html>
