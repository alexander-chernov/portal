<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alexander A. Chernov
 * Date: 15.09.13
 * Time: 15:32
 * To change this template use File | Settings | File Templates.
 */
require_once "config.php";
$page = (intval($_GET['page'])>0)?intval($_GET['page']):0;
$limit = $page*$perPage;
$sql = "SELECT
                h.k_shn_id,
                t.k_t_name town,
                s.k_s_name street,
                h.k_shn_house_num house
        FROM k_towns t
        LEFT JOIN k_streets s ON s.k_s_town=t.k_t_id
        LEFT JOIN k_streets_house_nums h ON h.k_shn_street_id = s.k_s_id
        WHERE yandex_status=0
        ORDER BY s.k_s_name ASC
        limit ".$limit.",".$perPage;
//echo $sql;

$res = $mysqli->query($sql);
$res->data_seek(0);
while ($row = $res->fetch_assoc()) {
    $centerX = 'null';
    $centerY = 'null';
    $upperX = 'null';
    $upperY = 'null';
    $lowerX = 'null';
    $lowerY = 'null';
    if (preg_match('/\((.+)\)/', $row['street'], $m)) {
		//var_dump($m);
		$t = 1;
		$street = $m[1].', '.str_replace($m[0],"",$row['street']);
		//echo $street.'<br>';
    } else {
		$street = $row['street'];	
		$t = 0;
    }
    $geoName = 'город+' . $row['town'] . ",+" . str_replace(' ', '+', $street) . ",+дом+" . str_replace(" стр","c",$row['house']);
    $geoKode = YANDEX_GEO_LINK . $geoName;
	echo $geoKode.'<br>';
/*
	if ($t==1) {
		die();
	}
*/
    if ($curl = curl_init()) {
        curl_setopt($curl, CURLOPT_URL, $geoKode);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $out = curl_exec($curl);
        curl_close($curl);
    }
    $ya_coord = json_decode($out, TRUE);
    $home = 0;
    foreach ($ya_coord['response']['GeoObjectCollection']['featureMember'] as $place) {

        if (!empty($place['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['Locality']['Thoroughfare'])) {
            $home = $place['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['Locality']['Thoroughfare']['Premise']['PremiseNumber'];
        } elseif (!empty($place['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['Locality']['DependentLocality']['Thoroughfare'])) {
            $home = $place['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['Locality']['DependentLocality']['Thoroughfare']['Premise']['PremiseNumber'];
        } elseif (!empty($place['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['Locality']['Thoroughfare'])){
            $home = $place['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['Locality']['Thoroughfare']['Premise']['PremiseNumber'];
        } elseif (!empty($place['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['SubAdministrativeArea']['Locality']['Thoroughfare'])){
            $home = $place['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['SubAdministrativeArea']['Locality']['Thoroughfare']['Premise']['PremiseNumber'];
        } elseif (!empty($place['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['Locality']['DependentLocality']['Thoroughfare'])) {
            $home = $place['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['Locality']['DependentLocality']['Thoroughfare']['Premise']['PremiseNumber'];
        } elseif (!empty($place['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['Locality']['DependentLocality']['Thoroughfare'])) {
            $home = $place['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['Locality']['DependentLocality']['Thoroughfare']['Premise']['PremiseNumber'];
        } else {
            echo '<b>';
            var_dump($place['GeoObject']);
            echo '</b><br>';
        }

        $house = array();
        $house[] = str_replace(" стр","c",$row['house']);
        $house[] = str_replace(" стр","/",$row['house']);
        $house[] = $row['house'];
        echo '<br>ID='.$row['k_shn_id']." : ".$geoName . " : ".var_export($house,true)." : ".$home."<br>\n";

        if (in_array($home,$house)) {
            $yandex_status = 1;
            list($centerY,$centerX) = explode(" ",$place['GeoObject']['Point']['pos']);
            list($lowerY,$lowerX) = explode(" ",$place['GeoObject']['boundedBy']['Envelope']['lowerCorner']);
            list($upperY,$upperX) = explode(" ",$place['GeoObject']['boundedBy']['Envelope']['upperCorner']);
            echo "X=".floatval($centerX)." Y=".floatval($centerY)."<br>"."Corners: ".$upperX.",".$upperY.",".$lowerX.",".$lowerY.'<br>';
        } else {
            $yandex_status = 2;
            echo "Нет такого адреса<br>";
            list($centerY,$centerX) = explode(" ",$place['GeoObject']['Point']['pos']);
            list($lowerY,$lowerX) = explode(" ",$place['GeoObject']['boundedBy']['Envelope']['lowerCorner']);
            list($upperY,$upperX) = explode(" ",$place['GeoObject']['boundedBy']['Envelope']['upperCorner']);
            echo "X=".floatval($centerX)." Y=".floatval($centerY)."<br>"."Corners: ".$upperX.",".$upperY.",".$lowerX.",".$lowerY.'<br>';
        }
		echo '<br><br>';
    }

    $sqlUpdate = "UPDATE k_streets_house_nums SET
        centerX=".(($centerX>0)?floatval($centerX):$centerX).",
        centerY=".(($centerY>0)?floatval($centerY):$centerY).",
        upperX=".(($upperX>0)?floatval($upperX):$upperX).",
        upperY=".(($upperY>0)?floatval($upperY):$upperY).",
        lowerX=".(($lowerX>0)?floatval($lowerX):$lowerX).",
        lowerY=".(($lowerY>0)?floatval($lowerY):$lowerY).",
        yandex_status=".$yandex_status."
        WHERE k_shn_id=".$row['k_shn_id'];
    //echo $sqlUpdate.'<br>';
    $mysqli->query($sqlUpdate);
}
echo "<br><a href='?page=".($page-1)."'> <<< prev <<< </a>&nbsp;|&nbsp;<a href='?page=".($page+1)."'> >>> next >>> </a>";
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);
echo '<br><br>Page generated in ' . $total_time . ' seconds.' . "\n";
$mysqli->close();
?>