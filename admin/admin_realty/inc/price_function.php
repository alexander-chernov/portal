<?php
if ($_SERVER["SCRIPT_NAME"] == "/kedr.ru/admin/admin_realty/inc/price_function.php") {
    exit();
}
function GetPrice($days) {
    $query = 'SELECT * FROM k_price_for_up ORDER BY k_pfu_id ASC';
    $result = mysql_query($query);
    $k = array();
    $s = array();
    $a = array();
    $b = array();
    $n = 0;
    while ($row = mysql_fetch_array($result)) {
        $k[$n] = $row['k_pfu_days'];
        $s[$n] = $row['k_pfu_price'];
        $n++;
    }

//Решение
    $b[0] = (pow($s[1] * $k[0], 2) - pow($s[0] * $k[1], 2)) / ($k[0] * $k[1] * ($k[0] - $k[1]));
    $a[0] = (pow($s[0], 2) - $k[0] * $b[0]) / pow($k[0], 2);
    $b[1] = (pow($s[2] * $k[0], 2) - pow($s[0] * $k[2], 2)) / ($k[0] * $k[2] * ($k[0] - $k[2]));
    $a[1] = (pow($s[0], 2) - $k[0] * $b[1]) / pow($k[0], 2);
    $b[2] = (pow($s[2] * $k[1], 2) - pow($s[1] * $k[2], 2)) / ($k[1] * $k[2] * ($k[1] - $k[2]));
    $a[2] = (pow($s[1], 2) - $k[1] * $b[2]) / pow($k[1], 2);
    $a[3] = ($a[0] + $a[1] + $a[2]) / 3;
    $b[3] = ($b[0] + $b[1] + $b[2]) / 3;

    return round(sqrt(abs($a[3] * pow($days, 2) + $b[3] * $days)), 0);
}

?>
