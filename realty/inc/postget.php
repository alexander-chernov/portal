<?php

require_once 'functions.php';

if (isset($_GET['ShowParam'])) {
    $ShowParamID = $_GET['ShowParam'];
    array_push($new_url, 'ShowParam=' . $_GET['ShowParam']);
} else {
    if (!isset($_GET['BuySubcategory'])) {
        $ShowParamID = 1;
        array_push($new_url, 'ShowParam=1');
    } else {
        $ShowParamID = 13;
        array_push($new_url, 'ShowParam=13');
    }
    if (isset($_POST['kupluiu'])) {
        $ShowParamID = 13;
        array_push($new_url, 'ShowParam=13');
    }
}

if (isset($_GET['LimitOnPage'])) {
    $limit = $_GET['LimitOnPage'];
    array_push($new_url, 'LimitOnPage=' . $_GET['LimitOnPage']);
} else {
    array_push($new_url, 'LimitOnPage=10');
}
if (isset($_GET['ShowParam'])) {
    $WhereBuy = '';
    $Category = '';
    $WhereNews = '';
    $whereBuys = '';
    switch ($_GET['ShowParam']) {
        case 1: $WhereBuy .= '';
            $Category = '<a   href="index.php?ShowParam=1">Последние объявления</a>';
            break;
        case 2: $WhereBuy .= ' AND k_isf_deal_type=1 ';
            $Category = '<a  href="index.php?ShowParam=2">Продаю</a>';
            break;
        case 3: $WhereBuy .= ' AND k_isf_deal_type=1 AND k_isf_subcategory=1 ';
            $Category = '<a  href="index.php?ShowParam=2">Продаю</a> >> <b><a  href="index.php?ShowParam=3">Квартиры</a></b>';
            break;
        case 4: $WhereBuy .= ' AND k_isf_deal_type=1 AND k_isf_subcategory=2 ';
            $Category = '<a  href="index.php?ShowParam=2">Продаю</a> >> <b><a  href="index.php?ShowParam=4">Дома/дачи</a></b>';
            break;
        case 5: $WhereBuy .= ' AND k_isf_deal_type=1 AND k_isf_subcategory=3 ';
            $Category = '<a  href="index.php?ShowParam=2">Продаю</a> >> <b><a  href="index.php?ShowParam=5">Коммерческая</a></b>';
            break;
        case 6: $WhereBuy .= ' AND k_isf_deal_type=1 AND k_isf_subcategory=4 ';
            $Category = '<a  href="index.php?ShowParam=2">Продаю</a> >> <b><a  href="index.php?ShowParam=6">Гараж/погреб</a></b>';
            break;
        case 7: $WhereBuy .= ' AND k_isf_deal_type=1 AND k_isf_subcategory=5 ';
            $Category = '<a  href="index.php?ShowParam=2">Продаю</a> >> <b><a  href="index.php?ShowParam=7">Земля</a></b>';
            break;
        case 8: $WhereBuy .= ' AND k_isf_deal_type=2 ';
            $Category = '<a  href="index.php?ShowParam=8">Сдаю</a>';
            break;
        case 9: $WhereBuy .= ' AND k_isf_deal_type=2 AND k_isf_subcategory=6 ';
            $Category = '<a  href="index.php?ShowParam=8">Сдаю</a> >> <b><a  href="index.php?ShowParam=9">Квартиры</a></b>';
            break;
        case 10: $WhereBuy .= ' AND k_isf_deal_type=2 AND k_isf_subcategory=7 ';
            $Category = '<a  href="index.php?ShowParam=8">Сдаю</a> >> <b><a  href="index.php?ShowParam=10">Дома/дачи</a></b>';
            break;
        case 11: $WhereBuy .= ' AND k_isf_deal_type=2 AND k_isf_subcategory=8 ';
            $Category = '<a  href="index.php?ShowParam=8">Сдаю</a> >> <b><a  href="index.php?ShowParam=11">Коммерческая</a></b>';
            break;
        case 12: $WhereBuy .= ' AND k_isf_deal_type=2 AND k_isf_subcategory=9 ';
            $Category = '<a  href="index.php?ShowParam=8">Сдаю</a> >> <b><a  href="index.php?ShowParam=12">Гараж/погреб</a></b>';
            break;
        case 13: $whereBuys = '';
            $Category = '';
            break;
        case 14: $WhereNews = '';
            $Category = '';
            break;
        case 18: $id_ad = -1;
            $Category = '<a  href="index.php?ShowParam=18" class="spec_result">Результаты</a>';
            break;
        case 20: $id_ad = $_GET['id'];
            break;
    }

} else {
    $Category = '<a   href="index.php?ShowParam=1" class="spec_result">Последние</a>';
}

if (isset($_GET['UserId'])) {
    $WhereBuy .= ' AND k_isf_user_id=' . $_GET['UserId'] . ' ';
    array_push($new_url, 'UserId=' . $_GET['UserId']);
}

if (isset($_GET['SamePrice'])) {
    $_GET['SamePrice'] = filter_var($_GET['SamePrice'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare('SELECT k_isf_subcategory, k_isf_price FROM k_immovables_sell WHERE k_isf_id=:id LIMIT 1');
        $queue->execute(array(':id' => $_GET['SamePrice']));
        $row = $queue->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        exit('');
    }
    $WhereBuy .= ' AND (k_isf_price BETWEEN ' . ($row['k_isf_price'] * 0.9) . ' AND ' . ($row['k_isf_price'] * 1.1) . ') AND k_isf_subcategory = ' . $row['k_isf_subcategory'] . ' AND k_isf_id != ' . $_GET['SamePrice'];
    array_push($new_url, 'SamePrice=' . $_GET['SamePrice']);
}

if (isset($_GET['AllAgents']) && $_GET['AllAgents'] == 1) {
    $WhereBuy .= ' AND k_u_privileges=4 AND k_ua_last_date>NOW() AND k_ua_state=1 ';
    array_push($new_url, 'AllAgents=' . $_GET['AllAgents']);
}

if (isset($_GET['NewsSub'])) {
    $WhereNews = ' AND k_isn_parent=' . $_GET['NewsSub'] . ' ';
    array_push($new_url, 'NewsSub=' . $_GET['NewsSub']);
}

if (isset($_GET['BuySubcategory'])) {
    if (in_array(0,$_GET['BuySubcategory'])) {
        $whereBuys = '';
    } else {
        $whereBuys = ' WHERE ';
        for ($i = 0; $i < count($_GET['BuySubcategory']); $i++) {
            $whereBuys .= ' k_ib_subcategory=' . $_GET['BuySubcategory'][$i];
            array_push($new_url, 'BuySubcategory[]=' . $_GET['BuySubcategory'][$i]);
            if ($i != (count($_GET['BuySubcategory']) - 1)) {
                $whereBuys .= ' OR';
            }
        }
    }
}

if (isset($_POST['submit_x']) && isset($_POST['submit_y'])) {
    if (!empty($_POST['kupluiu']) && !empty($_POST['text_buy']) && !empty($_POST['phone_buy'])) {
        $data = base64_decode($_SESSION['captcha_image_code']);
        $captcha_image = imagecreatefromstring($data);
        $x = $_POST['submit_x'];
        $y = $_POST['submit_y'];

        $rgb = imagecolorat($captcha_image, $x, $y);
        $color_tran = imagecolorsforindex($captcha_image, $rgb);
//229, 48, 57
        $captcha_ok = ($color_tran['red'] == 255 && $color_tran['green'] == 0 && $color_tran['blue'] == 0 && $color_tran['alpha'] == 0);

        if ($captcha_ok) {
            if (!isset($_SESSION['id'])) {
                $id = 0;
            } else {
                $id = $_SESSION['id'];
            }
            $_POST['text_buy'] = filter_var($_POST['text_buy'], FILTER_SANITIZE_STRIPPED);
            $_POST['phone_buy'] = filter_var($_POST['phone_buy'], FILTER_SANITIZE_STRIPPED);
            $_POST['kupluiu'] = filter_var($_POST['kupluiu'], FILTER_VALIDATE_INT);
            try {
                $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
                $mysql->exec('set names utf8');
                $queue = $mysql->prepare('INSERT INTO k_immovables_buy (k_ib_user_id, k_ib_date, k_ib_text, k_ib_phone, k_ib_subcategory) VALUES (:id,NOW(),:text_buy,:phone_buy,:kupluiu)');
                $queue->execute(array(':id' => $id, ':text_buy' => $_POST['text_buy'], ':phone_buy' => $_POST['phone_buy'], ':kupluiu' => $_POST['kupluiu']));
            } catch (PDOException $e) {
                exit('');
            }
            $mysql = NULL;
        } else {
            $results = "Убедитесь, что вы нажали в красный кружочек!";
        }
    } else {
        $results = "Пожалуйста, заполните все обязательные поля!";
    }
}

//ПОИСК
if (isset($_GET['PriceFrom'])) {
    $WhereBuy .= ' AND k_isf_price>=' . $_GET['PriceFrom'] . ' ';
    array_push($new_url, 'PriceFrom=' . $_GET['PriceFrom']);
}
if (isset($_GET['PriceTo'])) {
    $WhereBuy .= ' AND k_isf_price<=' . $_GET['PriceTo'] . ' ';
    array_push($new_url, 'PriceTo=' . $_GET['PriceTo']);
}
if (isset($_GET['PriceFromMet'])) {
    $WhereBuy .= ' AND (k_isf_price/k_isf_area_all)>=' . $_GET['PriceFromMet'];
    array_push($new_url, 'PriceFromMet=' . $_GET['PriceFromMet']);
}
if (isset($_GET['PriceToMet'])) {
    $WhereBuy .= ' AND (k_isf_price/k_isf_area_all)<=' . $_GET['PriceToMet'];
    array_push($new_url, 'PriceToMet=' . $_GET['PriceToMet']);
}
if (isset($_GET['DistrictId'])) {
    $WhereBuy .= ' AND (';
    for ($i = 0; $i < count($_GET['DistrictId']); $i++) {
        $WhereBuy .= ' k_d_id=' . $_GET['DistrictId'][$i] . ' ';
        array_push($new_url, 'DistrictId[]=' . $_GET['DistrictId'][$i]);
        if ($i != (count($_GET['DistrictId']) - 1)) {
            $WhereBuy .= ' OR ';
        }
    }
    $WhereBuy .= ') ';
}
if (isset($_GET['Address'])) {
    $WhereBuy .= ' AND (';
    $defis_cut = str_replace("-", " ", $_GET['Address']);
    $search_string = explode(" ", str_replace("-", " ", $defis_cut));
    $search_num = "";
    $a = 0;
    if (!preg_match('/^[а-Я]/', $search_string[count($search_string) - 1])) {
        $search_num = $search_string[count($search_string) - 1];
        $a = 1;
    }
    for ($i = 0; $i < count($search_string) - $a; $i++) {
        $WhereBuy .= ' k_s_name LIKE "%' . trim($search_string[$i]) . '%" ';
        if ($i != (count($search_string) - $a - 1)) {
            $WhereBuy .= ' OR ';
        }
    }
    $WhereBuy .= ') ';
    if ($a != 0) {
        $WhereBuy .= ' AND k_shn_house_num LIKE "%' . $search_num . '%" ';
    }

    array_push($new_url, 'Address=' . str_replace(" ", "+", $defis_cut));
}
if (isset($_GET['Rooms'])) {
    if (in_array("5+", $_GET['Rooms'])) {
        $WhereBuy .=' AND k_isf_rooms>=5 ';
        array_push($new_url, 'Rooms[]=5%2B');
    } else {
        $WhereBuy .= ' AND (';
        for ($i = 0; $i < count($_GET['Rooms']); $i++) {
            echo $_GET['Rooms'][$i];
            $WhereBuy .= ' k_isf_rooms=' . $_GET['Rooms'][$i] . ' ';
            array_push($new_url, 'Rooms[]=' . $_GET['Rooms'][$i]);
            if ($i != (count($_GET['Rooms']) - 1)) {
                $WhereBuy .= ' OR ';
            }
        }
        $WhereBuy .= ') ';
    }
}
if (isset($_GET['ImmoType'])) {
    $WhereBuy .= ' AND k_isf_immovable_type=' . $_GET['ImmoType'];
    array_push($new_url, 'ImmoType=' . $_GET['ImmoType']);
}
if (isset($_GET['Material'])) {
    $WhereBuy .= ' AND k_isf_material=' . $_GET['Material'];
    array_push($new_url, 'Material=' . $_GET['Material']);
}
if (isset($_GET['NewSec'])) {
    $WhereBuy .= ' AND (';
    for ($i = 0; $i < count($_GET['NewSec']); $i++) {
        $WhereBuy .= ' k_isf_new=' . $_GET['NewSec'][$i] . ' ';
        array_push($new_url, 'NewSec[]=' . $_GET['NewSec'][$i]);
        if ($i != (count($_GET['NewSec']) - 1)) {
            $WhereBuy .= ' OR ';
        }
    }
    $WhereBuy .= ') ';
}
if (isset($_GET['EQ'])) {
    $WhereBuy .= ' AND (';
    for ($i = 0; $i < count($_GET['EQ']); $i++) {
        $WhereBuy .= ' k_isf_eq=' . $_GET['EQ'][$i] . ' ';
        array_push($new_url, 'EQ[]=' . $_GET['EQ'][$i]);
        if ($i != (count($_GET['EQ']) - 1)) {
            $WhereBuy .= ' OR ';
        }
    }
    $WhereBuy .= ') ';
}
if (isset($_GET['AreaAllFrom'])) {
    $WhereBuy .= ' AND k_isf_area_all>=' . $_GET['AreaAllFrom'];
    array_push($new_url, 'AreaAllTo=' . $_GET['AreaAllFrom']);
}
if (isset($_GET['AreaAllTo'])) {
    $WhereBuy .= ' AND k_isf_area_all<=' . $_GET['AreaAllTo'];
    array_push($new_url, 'AreaAllTo=' . $_GET['AreaAllTo']);
}
if (isset($_GET['AreaKitFrom'])) {
    $WhereBuy .= ' AND k_isf_area_kitchen>=' . $_GET['AreaKitFrom'];
    array_push($new_url, 'AreaKitFrom=' . $_GET['AreaKitFrom']);
}
if (isset($_GET['AreaKitTo'])) {
    $WhereBuy .= ' AND k_isf_area_kitchen<=' . $_GET['AreaKitTo'];
    array_push($new_url, 'AreaKitTo=' . $_GET['AreaKitTo']);
}
if (isset($_GET['AreaLandFrom'])) {
    $WhereBuy .= ' AND k_isf_area_land>=' . $_GET['AreaLandFrom'];
    array_push($new_url, 'AreaLandFrom=' . $_GET['AreaLandFrom']);
}
if (isset($_GET['AreaLandTo'])) {
    $WhereBuy .= ' AND k_isf_area_land<=' . $_GET['AreaLandTo'];
    array_push($new_url, 'AreaLandTo=' . $_GET['AreaLandTo']);
}
if (isset($_GET['FloorFrom'])) {
    $WhereBuy .= ' AND k_isf_floor>=' . $_GET['FloorFrom'];
    array_push($new_url, 'FloorFrom=' . $_GET['FloorFrom']);
}
if (isset($_GET['FloorTo'])) {
    $WhereBuy .= ' AND (k_isf_floor<=' . $_GET['FloorTo'] . ' OR k_isf_floor>=41)';
    array_push($new_url, 'FloorTo=' . $_GET['FloorTo']);
}
if (isset($_GET['BaseFloor'])) {
    $WhereBuy .= ' AND k_isf_base_floor=' . $_GET['BaseFloor'];
    array_push($new_url, 'BaseFloor=' . $_GET['BaseFloor']);
}
if (isset($_GET['FloorsFrom'])) {
    $WhereBuy .= ' AND k_isf_floor>=' . $_GET['FloorsFrom'];
    array_push($new_url, 'FloorsFrom=' . $_GET['FloorsFrom']);
}
if (isset($_GET['FloorsTo'])) {
    $WhereBuy .= ' AND k_isf_floor_all<=' . $_GET['FloorsTo'];
    array_push($new_url, 'FloorsTo=' . $_GET['FloorsTo']);
}
if (isset($_GET['Photo'])) {
    if ($_GET['Photo'] == 1) {
        $WhereBuy .= ' AND k_ip_url IS NOT NULL ';
    } else {
        $WhereBuy .= ' AND k_ip_url IS NULL ';
    }
    array_push($new_url, 'Photo=' . $_GET['Photo']);
}
if (isset($_GET['San']) && $_GET['San'] != 0) {
    $WhereBuy .= ' AND k_isf_san=' . $_GET['San'];
    array_push($new_url, 'San=' . $_GET['San']);
}
if (isset($_GET['Balcony']) && $_GET['Balcony'] != 0) {
    $WhereBuy .= ' AND k_isf_balcony=' . $_GET['Balcony'];
    array_push($new_url, 'Balcony=' . $_GET['Balcony']);
}
if (isset($_GET['Adv'][1])) {
    $WhereBuy .= ' AND k_isf_phone_stat=1';
    array_push($new_url, 'Adv[1]=1');
}
if (isset($_GET['Adv'][2])) {
    $WhereBuy .= ' AND k_isf_security=1';
    array_push($new_url, 'Adv[2]=1');
}
if (isset($_GET['Adv'][3])) {
    $WhereBuy .= ' AND k_isf_internet=1';
    array_push($new_url, 'Adv[3]=1');
}
if (isset($_GET['Adv'][4])) {
    $WhereBuy .= ' AND k_isf_balcony_gl=1';
    array_push($new_url, 'Adv[4]=1');
}
if (isset($_GET['Adv'][5])) {
    $WhereBuy .= ' AND k_isf_furniture=1';
    array_push($new_url, 'Adv[5]=1');
}
if (isset($_GET['Adv'][6])) {
    $WhereBuy .= ' AND k_isf_fridge=1';
    array_push($new_url, 'Adv[6]=1');
}
if (isset($_GET['Adv'][7])) {
    $WhereBuy .= ' AND k_isf_washing=1';
    array_push($new_url, 'Adv[7]=1');
}
if (isset($_GET['Adv'][8])) {
    $WhereBuy .= ' AND k_isf_microwave=1';
    array_push($new_url, 'Adv[8]=1');
}
if (isset($_GET['Adv'][9])) {
    $WhereBuy .= ' AND k_isf_tv=1';
    array_push($new_url, 'Adv[9]=1');
}
if (isset($_GET['Adv'][10])) {
    $WhereBuy .= ' AND k_isf_ctv=1';
    array_push($new_url, 'Adv[10]=1');
}
if (isset($_GET['Adv'][11])) {
    $WhereBuy .= ' AND k_isf_stove=1';
    array_push($new_url, 'Adv[11]=1');
}
if (isset($_GET['Adv'][12])) {
    $WhereBuy .= ' AND k_isf_plastic_windows=1';
    array_push($new_url, 'Adv[12]=1');
}
if (isset($_GET['Utilities']) && $_GET['Utilities'] != 0) {
    $WhereBuy .= ' AND k_isf_utilities=' . $_GET['Utilities'];
    array_push($new_url, 'Utilities=' . $_GET['Utilities']);
}
if (isset($_GET['DateFrom'])) {
    $date = explode("/", $_GET['DateFrom']);
    $date_norm = $date[2] . '-' . $date[0] . '-' . $date[1];
    $WhereBuy .= ' AND k_isf_registration_date>="' . $date_norm . '"';
    array_push($new_url, 'DateFrom=' . str_replace("/", "%2F", $_GET['DateFrom']));
}
if (isset($_GET['DateTo'])) {
    $date = explode("/", $_GET['DateTo']);
    $date_norm = $date[2] . '-' . $date[0] . '-' . $date[1];
    $WhereBuy .= ' AND k_isf_registration_date<="' . $date_norm . '"';
    array_push($new_url, 'DateFrom=' . str_replace("/", "%2F", $_GET['DateTo']));
}
?>
