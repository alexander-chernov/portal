<?php

include '../../inc/configs.php';
include 'classes.php';

if (isset($_POST['DoubleAddress1'])) {
    $_POST['DoubleAddress1'] = filter_var($_POST['DoubleAddress1'], FILTER_VALIDATE_INT);
    $_POST['DoubleAddress2'] = filter_var($_POST['DoubleAddress2'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue0 = $mysql->prepare('SELECT k_shn_house_num AS num,
            k_s_name AS name,
            k_shn_street_id AS street,
            k_shn_district_id AS district,
            k_shn_massive_id AS massives
            FROM k_streets_house_nums AS kshn
            LEFT JOIN k_streets AS ks ON (ks.k_s_id = kshn.k_shn_street_id)
            WHERE k_shn_id=:id1 OR k_shn_id=:id2
            ORDER BY k_s_name ASC
            LIMIT 2');
        $queue0->execute(array(":id1" => $_POST['DoubleAddress1'], ":id2" => $_POST['DoubleAddress2']));
        $names = $queue0->fetchAll(PDO::FETCH_ASSOC);
        $queue1 = $mysql->prepare('INSERT INTO k_streets (k_s_name) VALUES (:name)');
        $queue1->execute(array(":name" => ($names[0]['name'] . '###' . $names[1]['name'])));
        $queue2 = $mysql->prepare('INSERT INTO k_streets_house_nums
            (k_shn_house_num,k_shn_street_id,k_shn_district_id,k_shn_massive_id)
            VALUES (:num,:street,:distr,:mass)');
        $queue2->execute(array(":num" => ($names[0]['num'] . '###' . $names[1]['num']),
            ":street" => $mysql->lastInsertId(),
            ":distr" => $names[0]['district'],
            ":mass" => $names[0]['massives']));
        $queue3 = $mysql->prepare('DELETE FROM k_streets_house_nums WHERE k_shn_id=:id');
        $queue3->execute(array(":id" => $_POST['DoubleAddress1']));
        $queue3->execute(array(":id" => $_POST['DoubleAddress2']));
        require_once '../../inc/functions.php';
        CreateTempTables();
        $da = new AllDoubleAddresses(1, '');
        echo '<tr style="background: #7caed3;"><td><p class="style_5">Адрес</p></td><td><p class="style_5">Действие</p></td></tr>';
        for ($i = 0; $i < count($da->id); $i++) {
            echo '<tr style="background: #f0f4f4;" id="do_ad_' . $da->id[$i] . '"><td><p class="style_9_1">' . $da->address[$i] . '</p></td>';
            echo '<td><img onclick="DeleteDoubleAddress(this);" class="img_options" src="../images/delete_team.png" title="Удалить двойной адрес" alt="' . $da->id[$i] . '"></td></tr>';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['DeleteDoubleAddress'])) {
    $_POST['DeleteDoubleAddress'] = filter_var($_POST['DeleteDoubleAddress'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue0 = $mysql->prepare("SELECT kshn.*, ks.*
            FROM k_streets_house_nums AS kshn
            LEFT JOIN k_streets AS ks ON (ks.k_s_id = kshn.k_shn_street_id)
            WHERE k_shn_id=:id");
        $queue0->execute(array(":id" => $_POST['DeleteDoubleAddress']));
        $result0 = $queue0->fetch(PDO::FETCH_ASSOC);
        $street = explode('###', $result0['k_s_name']);
        $house = explode('###', $result0['k_shn_house_num']);
        $queue00 = $mysql->prepare("SELECT k_s_id FROM k_streets WHERE k_s_name=:name");
        $queue00->execute(array(":name" => $street[0]));
        $street_id[] = $queue00->fetch(PDO::FETCH_ASSOC);
        $queue00->execute(array(":name" => $street[1]));
        $street_id[] = $queue00->fetch(PDO::FETCH_ASSOC);
        $queue000 = $mysql->prepare("INSERT INTO k_streets_house_nums
            (k_shn_house_num,k_shn_street_id,k_shn_district_id,k_shn_massive_id)
            VALUES (:num,:street,:distr,:massive)");
        $queue000->execute(array(":num" => $house[0], ":street" => $street_id[0]['k_s_id'], ":distr" => $result0['k_shn_district_id'], ":massive" => $result0['k_shn_massive_id']));
        $queue000->execute(array(":num" => $house[1], ":street" => $street_id[1]['k_s_id'], ":distr" => $result0['k_shn_district_id'], ":massive" => $result0['k_shn_massive_id']));
        $queue = $mysql->prepare("DELETE kshn.*, ks.*
            FROM k_streets_house_nums AS kshn
            LEFT JOIN k_streets AS ks ON (ks.k_s_id = kshn.k_shn_street_id)
            WHERE k_shn_id=:id");
        if ($queue->execute(array(":id" => $_POST['DeleteDoubleAddress']))) {
            echo 'yes';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['DeleteAddress'])) {
    $_POST['DeleteAddress'] = filter_var($_POST['DeleteAddress'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare("DELETE FROM k_streets_house_nums WHERE k_shn_id=:id");
        if ($queue->execute(array(":id" => $_POST['DeleteAddress']))) {
            echo 'yes';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['DeleteStreet'])) {
    $_POST['DeleteStreet'] = filter_var($_POST['DeleteStreet'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare("DELETE FROM k_streets WHERE k_s_id=:id");
        if ($queue->execute(array(":id" => $_POST['DeleteStreet']))) {
            echo 'yes';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['DeleteDistrict'])) {
    $_POST['DeleteDistrict'] = filter_var($_POST['DeleteDistrict'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare("DELETE FROM k_districts WHERE k_d_id=:id");
        if ($queue->execute(array(":id" => $_POST['DeleteDistrict']))) {
            echo 'yes';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['DeleteMassive'])) {
    $_POST['DeleteMassive'] = filter_var($_POST['DeleteMassive'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare("DELETE FROM k_towns_massives WHERE k_tm_id=:id");
        if ($queue->execute(array(":id" => $_POST['DeleteMassive']))) {
            echo 'yes';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['DeleteImg'])) {
    $_POST['DeleteImg'] = filter_var($_POST['DeleteImg'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare("DELETE FROM k_street_house_photos WHERE k_shp_url=:url");
        $queue->execute(array(":url" => str_replace('../images/addresses/1_', '', $_POST['DeleteImg'])));
    } catch (PDOException $e) {
        exit();
    }
    if (unlink('../' . $_POST['DeleteImg'])) {
        unlink('../' . str_replace('../images/addresses/1_', '../images/addresses/', $_POST['DeleteImg']));
        echo 'yes';
    } else {
        echo 'no';
    }
}

if (isset($_POST['LoadPhotosFromAddr'])) {
    $_POST['LoadPhotosFromAddr'] = filter_var($_POST['LoadPhotosFromAddr'], FILTER_VALIDATE_INT);
    $a = new AddressPhotos(0, " WHERE k_shp_parent=" . $_POST['LoadPhotosFromAddr']);
    if (count($a->id) == 0) {
        echo '<div class="block_img_table_add"></div>';
    } else {
        for ($i = 0; $i < count($a->id); $i++) {
            echo '<div class="block_img_table_add">
            <img class="img_table_add" src="../images/addresses/1_' . $a->url[$i] . '">
            <img class="img_options" src="../images/delete_team.png" title="Удалить фото" alt="../images/addresses/1_' . $a->url[$i] . '" onclick="DeleteImg(this);">
            </div>';
        }
    }
}

if (isset($_POST['AddPhotoToAddrID'])) {
    if (!preg_match('/^Cannot/', $_POST['AddPhotoToAddrURL'])) {
        $_POST['AddPhotoToAddrID'] = filter_var($_POST['AddPhotoToAddrID'], FILTER_VALIDATE_INT);
        $_POST['AddPhotoToAddrURL'] = filter_var($_POST['AddPhotoToAddrURL'], FILTER_SANITIZE_STRIPPED);
        $_POST['AddPhotoToAddrURL'] = str_replace('../images/addresses/1_', '', $_POST['AddPhotoToAddrURL']);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare("INSERT INTO k_street_house_photos (k_shp_url,k_shp_parent) VALUES (:url,:parent)");
            $queue->execute(array(":url" => $_POST['AddPhotoToAddrURL'], ":parent" => $_POST['AddPhotoToAddrID']));
        } catch (PDOException $e) {
            exit();
        }
    }
}

if (isset($_POST['CreateNewAddressNum'])) {
    $_POST['CreateNewAddressNum'] = filter_var($_POST['CreateNewAddressNum'], FILTER_SANITIZE_STRIPPED);
    $x = $_POST['x'];
    $y = $_POST['y'];
    $_POST['CreateNewAddressStr'] = filter_var($_POST['CreateNewAddressStr'], FILTER_VALIDATE_INT);
    $_POST['CreateNewAddressDistr'] = filter_var($_POST['CreateNewAddressDistr'], FILTER_VALIDATE_INT);
    $_POST['CreateNewAddressMass'] = filter_var($_POST['CreateNewAddressMass'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue0 = $mysql->prepare("SELECT count(*) AS num FROM k_streets_house_nums
            WHERE k_shn_house_num=:num AND k_shn_street_id=:street");
        $queue0->execute(array(":num" => $_POST['CreateNewAddressNum'],
            ":street" => $_POST['CreateNewAddressStr']));
        $result0 = $queue0->fetch(PDO::FETCH_ASSOC);
        if ($result0['num'] == 0) {
            $queue = $mysql->prepare("INSERT INTO k_streets_house_nums (k_shn_house_num,k_shn_street_id,k_shn_district_id,k_shn_massive_id,centerX,centerY)
                VALUES (:num,:street,:district,:massive,:x,:y)");
            $queue->execute(array(":num" => $_POST['CreateNewAddressNum'],
                ":street" => $_POST['CreateNewAddressStr'],
                ":district" => $_POST['CreateNewAddressDistr'],
                ":massive" => $_POST['CreateNewAddressMass'],
                ":x" => $x,
                ":y" => $y
            ));
            echo 'yes';
        } else {
            echo 'no';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['AllAddressesExDouble'])) {
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare("SELECT k_shn_id,k_s_name,k_shn_house_num
                FROM k_streets_house_nums AS kshn
                LEFT JOIN k_streets AS ks ON (kshn.k_shn_street_id = ks.k_s_id)
                WHERE k_s_name NOT LIKE '%###%'
                ORDER BY k_s_name,k_shn_house_num ASC");
        $queue->execute();
        $result = $queue->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $value) {
            echo '<option value="' . $value['k_shn_id'] . '">' . $value['k_s_name'] . ' ' . $value['k_shn_house_num'] . '</option>';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['AllAddresses'])) {
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare("SELECT k_shn_id,k_s_name,k_shn_house_num
                FROM k_streets_house_nums AS kshn
                LEFT JOIN k_streets AS ks ON (kshn.k_shn_street_id = ks.k_s_id)
                ORDER BY k_s_name,k_shn_house_num ASC");
        $queue->execute();
        $result = $queue->fetchAll(PDO::FETCH_ASSOC);
        echo '<option selected value="0">Выберите адрес</option>';
        foreach ($result as $value) {
            if (preg_match('/(###)/', $value['k_s_name'])) {
                $street = explode('###', $value['k_s_name']);
                $house = explode('###', $value['k_shn_house_num']);
                echo '<option value="' . $value['k_shn_id'] . '">' . $street[0] . ' ' . $house[0] . ' / ' . $street[1] . ' ' . $house[1] . '</option>';
            } else {
                echo '<option value="' . $value['k_shn_id'] . '">' . $value['k_s_name'] . ' ' . $value['k_shn_house_num'] . '</option>';
            }
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['CreateNewStreet'])) {
    $_POST['CreateNewStreet'] = filter_var($_POST['CreateNewStreet'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue0 = $mysql->prepare("SELECT count(*) AS num FROM k_streets WHERE k_s_name=:street");
        $queue0->execute(array(":street" => $_POST['CreateNewStreet']));
        $result0 = $queue0->fetch(PDO::FETCH_ASSOC);
        if ($result0['num'] == 0) {
            $queue = $mysql->prepare("INSERT INTO k_streets (k_s_name) VALUES (:street)");
            $queue->execute(array(":street" => $_POST['CreateNewStreet']));
            echo 'yes';
        } else {
            echo 'no';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['CreateNewDistrict'])) {
    $_POST['CreateNewDistrict'] = filter_var($_POST['CreateNewDistrict'], FILTER_SANITIZE_STRIPPED);
    $x = $_POST['x'];
    $y = $_POST['y'];
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue0 = $mysql->prepare("SELECT count(*) AS num FROM k_districts WHERE k_d_name=:distr");
        $queue0->execute(array(":distr" => $_POST['CreateNewDistrict']));
        $result0 = $queue0->fetch(PDO::FETCH_ASSOC);
        if ($result0['num'] == 0) {
            $queue = $mysql->prepare("INSERT INTO k_districts (k_d_name,centerX,centerY) VALUES (:distr,:x,:y)");
            $queue->execute(array(
                ":distr" => $_POST['CreateNewDistrict'],
                ":x" => $x,
                ":y" => $y
            ));
            echo 'yes';
        } else {
            echo 'no';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['CreateNewMassive'])) {
    $_POST['CreateNewMassive'] = filter_var($_POST['CreateNewMassive'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue0 = $mysql->prepare("SELECT count(*) AS num FROM k_towns_massives WHERE k_tm_name=:mass");
        $queue0->execute(array(":mass" => $_POST['CreateNewMassive']));
        $result0 = $queue0->fetch(PDO::FETCH_ASSOC);
        if ($result0['num'] == 0) {
            $queue = $mysql->prepare("INSERT INTO k_towns_massives (k_tm_name) VALUES (:mass)");
            $queue->execute(array(":mass" => $_POST['CreateNewMassive']));
            echo 'yes';
        } else {
            echo 'no';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['ChangeStreet'])) {
    $_POST['ChangeStreet'] = filter_var($_POST['ChangeStreet'], FILTER_SANITIZE_NUMBER_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue0 = $mysql->prepare("SELECT k_s_name FROM k_streets WHERE k_s_id=:id");
        $queue0->execute(array(":id" => $_POST['ChangeStreet']));
        $result0 = $queue0->fetch(PDO::FETCH_ASSOC);
        echo $result0['k_s_name'];
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['ChangeNameStreetID'])) {
    $_POST['ChangeNameStreetID'] = filter_var($_POST['ChangeNameStreetID'], FILTER_SANITIZE_NUMBER_INT);
    $_POST['ChangeNameStreetName'] = filter_var($_POST['ChangeNameStreetName'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue0 = $mysql->prepare("UPDATE k_streets SET k_s_name=:name WHERE k_s_id=:id");
        $queue0->execute(array(":id" => $_POST['ChangeNameStreetID'], ":name" => $_POST['ChangeNameStreetName']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['ChangeDistrict'])) {
    $_POST['ChangeDistrict'] = filter_var($_POST['ChangeDistrict'], FILTER_SANITIZE_NUMBER_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue0 = $mysql->prepare("SELECT k_d_name FROM k_districts WHERE k_d_id=:id");
        $queue0->execute(array(":id" => $_POST['ChangeDistrict']));
        $result0 = $queue0->fetch(PDO::FETCH_ASSOC);
        echo $result0['k_d_name'];
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['ChangeNameDistrictID'])) {
    $_POST['ChangeNameDistrictID'] = filter_var($_POST['ChangeNameDistrictID'], FILTER_SANITIZE_NUMBER_INT);
    $_POST['ChangeNameDistrictName'] = filter_var($_POST['ChangeNameDistrictName'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue0 = $mysql->prepare("UPDATE k_districts SET k_d_name=:name WHERE k_d_id=:id");
        $queue0->execute(array(":id" => $_POST['ChangeNameDistrictID'], ":name" => $_POST['ChangeNameDistrictName']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['ChangeMassive'])) {
    $_POST['ChangeMassive'] = filter_var($_POST['ChangeMassive'], FILTER_SANITIZE_NUMBER_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue0 = $mysql->prepare("SELECT k_tm_name FROM k_towns_massives WHERE k_tm_id=:id");
        $queue0->execute(array(":id" => $_POST['ChangeMassive']));
        $result0 = $queue0->fetch(PDO::FETCH_ASSOC);
        echo $result0['k_tm_name'];
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['ChangeNameMassiveID'])) {
    $_POST['ChangeNameMassiveID'] = filter_var($_POST['ChangeNameMassiveID'], FILTER_SANITIZE_NUMBER_INT);
    $_POST['ChangeNameMassiveName'] = filter_var($_POST['ChangeNameMassiveName'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue0 = $mysql->prepare("UPDATE k_towns_massives SET k_tm_name=:name WHERE k_tm_id=:id");
        $queue0->execute(array(":id" => $_POST['ChangeNameMassiveID'], ":name" => $_POST['ChangeNameMassiveName']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
?>
