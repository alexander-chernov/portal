<?php

include '../../inc/configs.php';
require_once '../../inc/db.php';
require_once 'classes.php';
require_once '../../inc/functions.php';

require_once($_SERVER['DOCUMENT_ROOT'].'/inc/class.phpmailer.php');

if (isset($_POST['SearchAddress'])) {
    $search_string = explode(" ", $_POST['SearchAddress']);
    $search_addr = "";
    $search_num = "";
    $a = 0;
    if (!preg_match('/^[а-Я]/', $search_string[count($search_string) - 1])) {
        $search_num = $search_string[count($search_string) - 1];
        $a = 1;
    }
    for ($i = 0; $i < count($search_string) - $a; $i++) {
        $search_addr .= $search_string[$i] . ' ';
    }
    $search_addr = trim($search_addr);

    if ($search_num == "") {
        $query = 'SELECT * FROM k_streets_house_nums as kshn
        LEFT JOIN k_streets as ks ON (ks.k_s_id = kshn.k_shn_street_id)
        WHERE k_s_name LIKE "%' . $search_addr . '%" ORDER BY k_shn_house_num ASC';
    } else {
        $query = 'SELECT * FROM k_streets_house_nums as kshn
        LEFT JOIN k_streets as ks ON (ks.k_s_id = kshn.k_shn_street_id)
        WHERE k_s_name LIKE "%' . $search_addr . '%" AND k_shn_house_num LIKE "%' . $search_num . '%"
        ORDER BY k_shn_house_num ASC';
    }
    $result = mysql_query($query);
    $print = '<select name="ImmoAddressChosen" id="ImmoAddressChosen" style="width: 100%;">';
    $result_streets = array();
    $result_nums = array();
    $n = 0;
    while ($row = mysql_fetch_array($result)) {
        $result_streets[$n] = $row['k_s_name'];
        $result_nums[$n] = $row['k_shn_house_num'];
        $print .= '<option value="' . $row['k_shn_id'] . '">' . $row['k_s_name'] . ' ' . $row['k_shn_house_num'] . '</option>';
        $n++;
    }
    $print .= '</select>';
    echo $print;
}

if (isset($_POST['AdsID'])) {
    $immovable = new Realty();
    $immovable->LoadRealty(0, 0, ' WHERE k_isf_id=' . $_POST['AdsID']);
    echo $immovable->GenerateWindowChange($_POST['AdsID']);
}

if (isset($_POST['SaveInDBParam'])) {
    if (mysql_query($_POST['SaveInDBParam'])) {
        echo 'Информация обновлена!';
    } else {
        echo 'Обнаружена ошибка!';
    }
}

if (isset($_POST['PhotoID'])) {
    $immovable = new Realty();
    echo $immovable->GeneratePhotos($_POST['PhotoID']);
}

if (isset($_POST['PhotoDelID'])) {
    $query = 'SELECT k_ip_url FROM k_immovables_photos WHERE k_ip_id=' . $_POST['PhotoDelID'];
    $result = mysql_query($query);
    $row = mysql_fetch_assoc($result);
    $path = $row['k_ip_url'];
    $query = 'DELETE FROM k_immovables_photos WHERE k_ip_id=' . $_POST['PhotoDelID'];
    if (mysql_query($query)) {
        if (unlink('../../' . $path)) {
            echo 'Фото удалено!';
        } else {
            echo 'Ошибка удаления файла!';
        }
    } else {
        echo 'Ошибка обращения к базе!';
    }
}

if (isset($_POST['SpecialID'])) {
    if ($_POST['ActionType'] == 1) {
        $query = 'INSERT INTO k_immovables_special (k_is_immovable_id) VALUES (' . $_POST['SpecialID'] . ')';
        if (mysql_query($query)) {
            echo "yes";
        } else {
            echo "error";
        }
    }
    if ($_POST['ActionType'] == 2) {
        $query = 'DELETE FROM k_immovables_special WHERE k_is_immovable_id=' . $_POST['SpecialID'];
        if (mysql_query($query)) {
            echo "no";
        } else {
            echo "error";
        }
    }
}

if (isset($_POST['RealtyMainPage'])) {
    $_POST['RealtyMainPage'] = filter_var($_POST['RealtyMainPage'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query0 = $mysql->prepare('SELECT k_isf_main_page FROM k_immovables_sell WHERE k_isf_id=:id');
        $query0->execute(array(':id' => $_POST['RealtyMainPage']));
        $result0 = $query0->fetch(PDO::FETCH_ASSOC);
        $new_state = 0;
        if ($result0['k_isf_main_page'] == 0) {
            $new_state = 1;
        }
        $query = $mysql->prepare('UPDATE k_immovables_sell SET k_isf_main_page=:state WHERE k_isf_id=:id');
        $query->execute(array(':id' => $_POST['RealtyMainPage'], ":state" => $new_state));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['RealtyColor'])) {
    $_POST['RealtyColor'] = filter_var($_POST['RealtyColor'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query0 = $mysql->prepare('SELECT k_isf_color_light FROM k_immovables_sell WHERE k_isf_id=:id');
        $query0->execute(array(':id' => $_POST['RealtyColor']));
        $result0 = $query0->fetch(PDO::FETCH_ASSOC);
        $new_state = 0;
        if ($result0['k_isf_color_light'] == 0) {
            $new_state = 1;
        }
        $query = $mysql->prepare('UPDATE k_immovables_sell SET k_isf_color_light=:state WHERE k_isf_id=:id');
        $query->execute(array(':id' => $_POST['RealtyColor'], ":state" => $new_state));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['RealtyLock'])) {
    $_POST['RealtyLock'] = filter_var($_POST['RealtyLock'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query1 = $mysql->prepare('SELECT * FROM k_immovables_locked WHERE k_il_ad_id=:id');
        $query1->execute(array(':id' => $_POST['RealtyLock']));
        $result1 = $query1->fetch(PDO::FETCH_ASSOC);
        if ($query1->rowCount() == 0) {
            $query2 = $mysql->prepare('INSERT INTO k_immovables_locked (k_il_ad_id,k_il_date_start,k_il_date_stop) VALUES (:id,NOW(),NOW() + INTERVAL 7 DAY)');
        } else {
            $query2 = $mysql->prepare('DELETE FROM k_immovables_locked WHERE k_il_id=:id');
        }
        $query2->execute(array(':id' => $_POST['RealtyLock']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['ImmoUpDays'])) {
    $query = 'SELECT * FROM k_immovables_up WHERE k_iu_immo_id=' . $_POST['ImmoUpID'] . ' LIMIT 1';
    $result = mysql_query($query);
    $row = mysql_fetch_assoc($result);
    if (mysql_num_rows($result)) {
        //$plus = strtotime($row['k_iu_end_date']) + $_POST['ImmoUpDays'] * 60 * 60 * 24;
        //$plus_str = date('Y-m-d H:i:s', $plus);
        if (mysql_query('UPDATE k_immovables_up SET k_iu_end_date=NOW() + INTERVAL '.$_POST['ImmoUpDays'].' DAY WHERE k_iu_id=' . $row['k_iu_id'])) {
            echo 'Успешно добавлено ' . $_POST['ImmoUpDays'] . ' дней!';
        }
    } else {
        //$plus = time() + $_POST['ImmoUpDays'] * 60 * 60 * 24;
        //$plus_str = date('Y-m-d H:i:s', $plus);
        if (mysql_query('UPDATE k_immovables_sell SET k_isf_up=1 WHERE k_isf_id=' . $_POST['ImmoUpID'])) {
            if (mysql_query('INSERT INTO k_immovables_up (k_iu_immo_id,k_iu_end_date) VALUES (' . $_POST['ImmoUpID'] . ',NOW() + INTERVAL '.$_POST['ImmoUpDays'].' DAY)')) {
                echo 'Успешно поднято на ' . $_POST['ImmoUpDays'] . ' дней!';
            }
        }
    }
}

if (isset($_POST['ImmoDownId'])) {
    mysql_query('DELETE FROM k_immovables_up WHERE k_iu_immo_id=' . $_POST['ImmoDownId']);
    mysql_query('UPDATE k_immovables_sell SET k_isf_up=0 WHERE k_isf_id=' . $_POST['ImmoDownId']);
    echo 'Поднятие объявления отменено!';
}

if (isset($_POST['ImmoIdToDays'])) {
    $_POST['ImmoIdToDays'] = filter_var($_POST['ImmoIdToDays'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare('UPDATE k_immovables_sell SET k_isf_up_date=NOW() WHERE k_isf_id=:id');
        $queue->execute(array(":id" => $_POST['ImmoIdToDays']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['ImmoAddDays'])) {
    $query = 'SELECT k_isf_end_date FROM k_immovables_sell WHERE k_isf_id=' . $_POST['ImmoAddDays'] . ' LIMIT 1';
    $result = mysql_query($query);
    $row = mysql_fetch_assoc($result);
    $between = round((strtotime($row['k_isf_end_date']) - time()) / 86400, 0);
    echo $between;
}

if (isset($_POST['ImmoAddDaysID'])) {
    $query = 'SELECT k_isf_end_date FROM  k_immovables_sell WHERE k_isf_id=' . $_POST['ImmoAddDaysID'] . ' LIMIT 1';
    $result = mysql_query($query);
    $row = mysql_fetch_array($result);
    //$plus = strtotime($row['k_isf_end_date']) + $_POST['ImmoAddDaysSubmit'] * 24 * 60 * 60;
    //$plus_date = date('Y-m-d H:i:s', $plus);
    if (mysql_query("UPDATE k_immovables_sell SET k_isf_end_date=NOW() + INTERVAL ".$_POST['ImmoAddDaysSubmit']." DAY  WHERE k_isf_id=" . $_POST['ImmoAddDaysID'])) {
        echo 'Успешно продлено до ' . $plus_date . '!';
    } else {
        echo 'Произошла ошибка!';
    }
}

if (isset($_POST['ImmoDisEnID'])) {
    if (mysql_query('UPDATE k_immovables_sell SET k_isf_state=' . $_POST['ImmoDisEnAct'] . ' WHERE k_isf_id=' . $_POST['ImmoDisEnID'])) {
        echo 'yes';
    } else {
        echo 'no';
    }
}

if (isset($_POST['IPUserBan'])) {
    $query = 'SELECT k_ubil_ip FROM k_users_ban_ip_list WHERE k_ubil_ip="' . $_POST['IPUserBan'] . '"';
    $result = mysql_query($query);
    if (mysql_num_rows($result) > 0) {
        echo 'IP уже есть в списке!';
    } else {
        if (mysql_query('INSERT INTO k_users_ban_ip_list (k_ubil_ip) VALUES ("' . trim($_POST['IPUserBan']) . '")')) {
            echo 'IP добавлен в BAN-лист!';
        } else {
            echo 'Произошла ошибка!';
        }
    }
}

if (isset($_POST['ImmoIDDelSubmit'])) {
    $query = 'DELETE FROM k_immovables_sell WHERE k_isf_id=' . $_POST['ImmoIDDelSubmit'];
    mysql_query($query);
    $query1 = 'SELECT k_ip_url FROM k_immovables_photos WHERE k_ip_immo_id=' . $_POST['ImmoIDDelSubmit'];
    $result1 = mysql_query($query1);
    while ($row2 = mysql_fetch_array($result1)) {
        if (preg_match('/(video)/', $row2['k_ip_url'])) {
            unlink('../../../' . $row2['k_ip_url']);
        } else {
            unlink('../../' . $row2['k_ip_url']);
        }
    }
    $query3 = 'DELETE FROM k_immovables_special WHERE k_ip_immo_id=' . $_POST['ImmoIDDelSubmit'];
    mysql_query($query3);
    echo 'yes';
}

if (isset($_GET['ImmoSearchSubmit'])) {
    $query = "WHERE ";
    $first = FALSE;
    if ($_GET['ImmoSearchID'] != "") {
        $query .= ' k_isf_id=' . $_GET['ImmoSearchID'];
        $first = TRUE;
    }
    if ($_GET['ImmoSearchNick'] != "") {
        if ($first) {
            $query .= ' AND';
        }
        $query .= ' k_ku_login LIKE "%' . $_GET['ImmoSearchNick'] . '%"';
        $first = TRUE;
    }
    if ($_GET['ImmoSearchSubCategory'] != 0) {
        if ($first) {
            $query .= ' AND';
        }
        $query .= ' k_isf_subcategory=' . $_GET['ImmoSearchSubCategory'];
        $first = TRUE;
    }
    if ($_GET['ImmoSearchState'] != "no") {
        if ($first) {
            $query .= ' AND';
        }
        $query .= ' k_isf_state=' . $_GET['ImmoSearchState'];
        $first = TRUE;
    }
    if ($_GET['ImmoSearchState'] != "no") {
        if ($first) {
            $query .= ' AND';
        }
        $query .= ' k_isf_state=' . $_GET['ImmoSearchState'];
        $first = TRUE;
    }
    if (isset($_GET['ImmoSearchUserType1']) && !isset($_GET['ImmoSearchUserType2'])) {
        if ($first) {
            $query .= ' AND';
        }
        $query .= ' k_isf_owner=' . $_GET['ImmoSearchUserType1'];
        $first = TRUE;
    }
    if (!isset($_GET['ImmoSearchUserType1']) && isset($_GET['ImmoSearchUserType2'])) {
        if ($first) {
            $query .= ' AND';
        }
        $query .= ' k_isf_owner=' . $_GET['ImmoSearchUserType2'];
        $first = TRUE;
    }
    if ($_GET['ImmoSearchAddress'] != "") {
        if ($first) {
            $query .= ' AND';
        }
        $query .= ' k_s_name LIKE "%' . $_GET['ImmoSearchAddress'] . '%"';
        $first = TRUE;
    }
    $_SESSION['Where'] = $query;
}

if (isset($_GET['ImmoSearchReset'])) {
    unset($_SESSION['Where']);
}

if (isset($_POST['AgentsInfoID'])) {
    $_POST['AgentsInfoID'] = filter_var($_POST['AgentsInfoID'], FILTER_VALIDATE_INT);
    $agents = new Agents();
    $agents->LoadAgents(0, 0, ' AND k_ku_id=' . $_POST['AgentsInfoID'] . ' ');
    $agents->GenerateInfoTable();
}

if (isset($_POST['AgentsEditID'])) {
    $_POST['AgentsEditID'] = filter_var($_POST['AgentsEditID'], FILTER_VALIDATE_INT);
    $agents = new Agents();
    $agents->LoadAgents(0, 0, ' AND k_ku_id=' . $_POST['AgentsEditID'] . ' ');
    $agents->GenerateEditTable();
}

if (isset($_POST['AgentAvatarLoadID'])) {
    $agents = new Agents();
    $agents->LoadAgents(0, 0, '');
    $agents->GenerateAvatarTable($_POST['AgentAvatarLoadID']);
}

if (isset($_POST['AgentEditName'])) {
    $_POST['AgentEditFName'] = filter_var($_POST['AgentEditFName'], FILTER_SANITIZE_STRIPPED);
    $_POST['AgentEditLName'] = filter_var($_POST['AgentEditLName'], FILTER_SANITIZE_STRIPPED);
    $_POST['AgentEditOName'] = filter_var($_POST['AgentEditOName'], FILTER_SANITIZE_STRIPPED);
    $_POST['AgentEditEmail'] = filter_var($_POST['AgentEditEmail'], FILTER_SANITIZE_EMAIL);
    $_POST['AgentEditSubmitID'] = filter_var($_POST['AgentEditSubmitID'], FILTER_VALIDATE_INT);
    $_POST['AgentEditName'] = filter_var($_POST['AgentEditName'], FILTER_SANITIZE_STRIPPED);
    $_POST['AgentEditPhone'] = filter_var($_POST['AgentEditPhone'], FILTER_SANITIZE_STRIPPED);
    $_POST['AgentEditSite'] = CorrectURL($_POST['AgentEditSite']);
    $_POST['AgentEditDescr'] = filter_var($_POST['AgentEditDescr'], FILTER_SANITIZE_STRIPPED);
    $_POST['ImmoAddressChosen'] = filter_var($_POST['ImmoAddressChosen'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('UPDATE k_users SET k_ku_fname=:fname, k_ku_lname=:lname, k_ku_oname=:oname, k_ku_email=:email WHERE k_ku_id=:id');
        $query->execute(array(":fname" => $_POST['AgentEditFName'],
            ":lname" => $_POST['AgentEditLName'],
            ":oname" => $_POST['AgentEditOName'],
            ":email" => $_POST['AgentEditEmail'],
            ":id" => $_POST['AgentEditSubmitID']));
        if ($_POST['ImmoAddressChosen'] == 0) {
            $query1 = $mysql->prepare('UPDATE k_users_agents SET k_ua_name=:name, k_ua_phone=:phone, k_ua_site=:site, k_ua_description=:descr WHERE k_ua_user_parent=:id');
            $query1->execute(array(":name" => $_POST['AgentEditName'],
                ":phone" => $_POST['AgentEditPhone'],
                ":site" => $_POST['AgentEditSite'],
                ":descr" => $_POST['AgentEditDescr'],
                ":id" => $_POST['AgentEditSubmitID']));
        } else {
            $query1 = $mysql->prepare('UPDATE k_users_agents SET k_ua_name=:name, k_ua_phone=:phone, k_ua_address=:addr, k_ua_site=:site, k_ua_description=:descr WHERE k_ua_user_parent=:id');
            $query1->execute(array(":name" => $_POST['AgentEditName'],
                ":addr" => $_POST['ImmoAddressChosen'],
                ":phone" => $_POST['AgentEditPhone'],
                ":site" => $_POST['AgentEditSite'],
                ":descr" => $_POST['AgentEditDescr'],
                ":id" => $_POST['AgentEditSubmitID']));
        }
        echo 'Обновление пользователя прошло успешно!';
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['AgentAvatarChangeID'])) {
    $query = 'SELECT k_ua_avatar FROM k_users_agents WHERE k_ua_user_parent=' . $_POST['AgentAvatarChangeID'] . ' LIMIT 1';
    $result = mysql_query($query);
    $row = mysql_fetch_assoc($result);
    unlink('../../' . $row['k_ua_avatar']);
    $query = 'UPDATE k_users_agents SET k_ua_avatar="' . substr($_POST['AgentAvatarChangeURL'], 3) . '" WHERE k_ua_user_parent=' . $_POST['AgentAvatarChangeID'];
    mysql_query($query);
}

if (isset($_POST['AgentAvatarDeleteID'])) {
    $query = 'SELECT k_ua_avatar FROM k_users_agents WHERE k_ua_user_parent=' . $_POST['AgentAvatarDeleteID'] . ' LIMIT 1';
    $result = mysql_query($query);
    $row = mysql_fetch_assoc($result);
    unlink('../../' . $row['k_ua_avatar']);
    $query = 'UPDATE k_users_agents SET k_ua_avatar="" WHERE k_ua_user_parent=' . $_POST['AgentAvatarDeleteID'];
    mysql_query($query);
}

if (isset($_POST['PasswordChangeID'])) {
    $query = 'UPDATE k_users SET k_ku_password="' . md5($_POST['PasswordChangePass']) . '" WHERE k_ku_id=' . $_POST['PasswordChangeID'];
    if (mysql_query($query)) {
        echo 'Пароль успешно изменён!';
    } else {
        echo 'Возникла ошибка!';
    }
}

if (isset($_POST['AgentRegisterID'])) {
    if ($_POST['AgentRegisterAct'] == 1) {
        $query = 'INSERT INTO k_users_agents_register (k_uar_user_id) VALUES (' . $_POST['AgentRegisterID'] . ')';
        if (mysql_query($query)) {
            echo "yes";
        } else {
            echo "error";
        }
    }
    if ($_POST['AgentRegisterAct'] == 2) {
        $query = 'DELETE FROM k_users_agents_register WHERE k_uar_user_id=' . $_POST['AgentRegisterID'];
        if (mysql_query($query)) {
            echo "no";
        } else {
            echo "error";
        }
    }
}

if (isset($_POST['AgentDisEnID'])) {
    $sql = "SELECT count(*) FROM k_users_agents WHERE k_ua_user_parent=" . $_POST['AgentDisEnID'];
    $res = $mysqli->query($sql);
    $res->data_seek(0);
    $total = $res->fetch_row();
    if ($total>1) {
        $sqlUpd = 'UPDATE k_users_agents SET k_ua_state=' . $_POST['AgentDisEnAct'] . ' WHERE k_ua_user_parent=' . $_POST['AgentDisEnID'];
        $resIns = $mysqli->query($sqlUpd);
        if ($resIns) {
            echo 'yes';
        } else {
            echo 'no';
        }
    } else {
        echo 'error';
    }
}

if (isset($_POST['AgentIDDelSubmit'])) {
    $query = 'SELECT k_isf_id FROM k_immovables_sell WHERE k_isf_user_id=' . $_POST['AgentIDDelSubmit'];
    $result = mysql_query($query);
    $ids = array();
    $n = 0;
    while ($row3 = mysql_fetch_array($result)) {
        $ids[$n] = $row3['k_isf_id'];
        $n++;
    }
    for ($n = 0; $n < count($ids); $n++) {
        $query = 'DELETE FROM k_immovables_sell WHERE k_isf_id=' . $ids[$n];
        mysql_query($query);
        $query1 = 'SELECT k_ip_url FROM k_immovables_photos WHERE k_ip_immo_id=' . $ids[$n];
        //echo $query1;
        $result1 = mysql_query($query1);
        while ($row2 = mysql_fetch_array($result1)) {
            unlink('../../' . $row2['k_ip_url']);
        }
        mysql_query($query2);
        $query3 = 'DELETE FROM k_immovables_special WHERE k_is_immovable_id=' . $ids[$n];
        echo $query3;
        mysql_query($query3);
    }
    $query = 'DELETE FROM k_users WHERE k_ku_id=' . $_POST['AgentIDDelSubmit'];
    mysql_query($query);
    $query = 'DELETE FROM k_users_agents WHERE k_ua_user_parent=' . $_POST['AgentIDDelSubmit'];
    mysql_query($query);
    echo 'Агент и все его объявления удалены!';
}

if (isset($_POST['AddAgentLogin'])) {
    $query = 'SELECT * FROM k_users WHERE k_ku_login="' . addslashes($_POST['AddAgentLogin']) . '" OR k_ku_email="' . addslashes($_POST['AddAgentEmail']) . '"';
    $result = mysql_query($query);
    if (mysql_num_rows($result) > 0) {
        echo 'Невозможно создать агентство! Логин или пароль уже занят!';
    } else {
        //$last_date = date('Y-m-d H:i:s', time() + $_POST['AddAgentDays'] * 24 * 60 * 60);
        $query = 'INSERT INTO k_users (k_ku_login,k_ku_password,k_ku_autor_date,k_ku_verified,k_ku_fname,k_ku_lname,k_ku_oname,k_ku_email,k_ku_last_date,k_u_privileges)
            VALUES("' . addslashes($_POST['AddAgentLogin']) . '","' . md5($_POST['AddAgentPassword2']) . '",NOW(),1,"' . addslashes($_POST['AddAgentFName']) . '","' . addslashes($_POST['AddAgentLName']) . '","' . addslashes($_POST['AddAgentOName']) . '","' . addslashes($_POST['AddAgentEmail']) . '",NOW(),4)';
        if (mysql_query($query)) {
            $id = mysql_insert_id();
            $query = 'INSERT INTO k_users_agents (k_ua_name,k_ua_phone,k_ua_site,k_ua_last_date,k_ua_user_parent,k_ua_description,k_ua_address)
                VALUES ("' . addslashes($_POST['AddAgentName']) . '","' . addslashes($_POST['AddAgentPhone']) . '","' . addslashes($_POST['AddAgentSite']) . '",NOW()+INTERVAL '.$_POST['AddAgentDays'].' DAY,' . $id . ',"' . addslashes($_POST['AddAgentDescription']) . '",' . $_POST['ImmoAddressChosen'] . ')';
            if (mysql_query($query)) {
                echo 'Агентство добавлено! Чтобы оно появилось в списке, обновите страницу!';

            }
        }
    }
}

if (isset($_GET['AgentSearchSubmit'])) {
    $PageType = 3;
    if (trim($_GET['AgentSearchID']) != '') {
        $query .= ' AND k_ku_id=' . $_GET['AgentSearchID'];
    }
    if ($_GET['AgentSearchName'] != "") {
        $query .= ' AND k_ua_name LIKE "%' . $_GET['AgentSearchName'] . '%"';
    }
    if ($_GET['AgentSearchAddress'] != "") {
        $query .= ' AND k_s_name LIKE "%' . $_GET['AgentSearchAddress'] . '%"';
    }
    if ($_GET['AgentSearchState'] != "no") {
        $query .= ' AND k_ua_state=' . $_GET['AgentSearchState'];
    }
    $_SESSION['WhereAg'] = $query;
}

if (isset($_GET['AgentSearchReset'])) {
    $PageType = 3;
    $_SESSION['WhereAg'] = '';
}

if (isset($_POST['NewsID'])) {
    $new = new News();
    $new->LoadNews(0, 0, '');
    $new->GenerateNewsShow($_POST['NewsID']);
}

if (isset($_POST['NewsIDEdit'])) {
    $new = new News();
    $new->LoadNews(0, 0, '');
    $new->GenerateEditTable($_POST['NewsIDEdit']);
}

if (isset($_POST['NewsIDEditSub'])) {
    $query = 'UPDATE k_immovables_subcategories_news SET k_isn_header="' . addslashes($_POST['NewsHeaderEdit']) . '", k_isn_text="' . addslashes($_POST['NewsTextEdit']) . '", k_isn_parent=' . $_POST['NewsSubcategoriesEdit'] . ' WHERE k_isn_id=' . $_POST['NewsIDEditSub'];
    if (mysql_query($query)) {
        echo 'yes';
    } else {
        echo 'no';
    }
}

if (isset($_POST['NewsIDAvatar'])) {
    $new = new News();
    $new->LoadNews(0, 0, '');
    $new->GenerateAvatarEdit($_POST['NewsIDAvatar']);
}

if (isset($_POST['NewsAvatarChangeID'])) {
    $query = 'SELECT k_isn_image FROM k_immovables_subcategories_news WHERE k_isn_id=' . $_POST['NewsAvatarChangeID'] . ' LIMIT 1';
    $result = mysql_query($query);
    $row = mysql_fetch_assoc($result);
    unlink('../../' . $row['k_isn_image']);
    $query = 'UPDATE k_immovables_subcategories_news SET k_isn_image="' . substr($_POST['NewsAvatarChangeURL'], 3) . '" WHERE k_isn_id=' . $_POST['NewsAvatarChangeID'];
    mysql_query($query);
}

if (isset($_POST['NewsAvatarDeleteID'])) {
    $query = 'SELECT k_isn_image FROM k_immovables_subcategories_news WHERE k_isn_id=' . $_POST['NewsAvatarDeleteID'] . ' LIMIT 1';
    $result = mysql_query($query);
    $row = mysql_fetch_assoc($result);
    unlink('../../' . $row['k_isn_image']);
    $query = 'UPDATE k_immovables_subcategories_news SET k_isn_image="" WHERE k_isn_id=' . $_POST['NewsAvatarDeleteID'];
    mysql_query($query);
}

if (isset($_POST['NewsDeleteID'])) {
    $query = 'SELECT k_isn_image FROM k_immovables_subcategories_news WHERE k_isn_id=' . $_POST['NewsDeleteID'] . ' LIMIT 1';
    $result = mysql_query($query);
    $row = mysql_fetch_assoc($result);
    unlink('../../' . $row['k_isn_image']);
    if (mysql_query('DELETE FROM k_immovables_subcategories_news WHERE k_isn_id=' . $_POST['NewsDeleteID'])) {
        echo 'yes';
    } else {
        echo 'error';
    }
}

if (isset($_POST['SubcategoryEdit'])) {
    $query = 'SELECT * FROM k_immovables_subcategories WHERE k_is_parent=4 ORDER BY k_is_name ASC';
    $result = mysql_query($query);
    $sel = '<select id="SubcategorySelect" style="width: 100%;">';
    while ($row = mysql_fetch_array($result)) {
        $sel .= '<option value="' . $row['k_is_id'] . '">' . $row['k_is_name'] . '</option>';
    }
    $sel .= '</select>';
    echo '<tr>
        <td><p class="style_2">Выберите рубрику:</p></td>
        <td>' . $sel . '<br></td></tr>
        <tr>
        <td><p class="style_2">Введите новое<br> название рубрики:</p></td>
        <td><input type="text" id="SubcategoryNewName" value=""></td>
        </tr>
        <tr>
        <td colspan="2">
        <button style="float:left; width: 100%;" onclick="SubcategoryChangeName();">Изменить</button>
        </td>
        </tr>';
}

if (isset($_POST['SubcategoryChangeNameID'])) {
    $query = 'UPDATE k_immovables_subcategories SET k_is_name="' . addslashes($_POST['SubcategoryChangeName']) . '" WHERE k_is_id=' . $_POST['SubcategoryChangeNameID'];
    if (mysql_query($query)) {
        echo 'yes';
    } else {
        echo 'no';
    }
}

if (isset($_POST['SubcategoryDeleteShow'])) {
    $query = 'SELECT * FROM k_immovables_subcategories WHERE k_is_parent=4 ORDER BY k_is_name ASC';
    $result = mysql_query($query);
    $sel = '<select id="SubcategorySelectDel" style="width: 100%;">';
    while ($row = mysql_fetch_array($result)) {
        $sel .= '<option value="' . $row['k_is_id'] . '">' . $row['k_is_name'] . '</option>';
    }
    $sel .= '</select>';
    echo '<tr>
        <td><p class="style_2">Выберите рубрику:</p></td>
        </tr>
        <tr><td>' . $sel . '<br></td></tr>
        <tr><td><button style="float:left; width: 100%;" onclick="SubcategoryDeleteSubmit();">Удалить</button></td></tr>';
}

if (isset($_POST['SubcategoryDeleteSubmit'])) {
    $query = 'SELECT k_isn_image FROM k_immovables_subcategories_news WHERE k_isn_parent=' . $_POST['SubcategoryDeleteSubmit'];
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result)) {
        unlink('../../' . $row['k_isn_image']);
    }
    if (mysql_query('DELETE FROM k_immovables_subcategories_news WHERE k_isn_parent=' . $_POST['SubcategoryDeleteSubmit'])) {
        if (mysql_query('DELETE FROM k_immovables_subcategories WHERE k_is_id=' . $_POST['SubcategoryDeleteSubmit'])) {
            echo 'yes';
        } else {
            echo 'no';
        }
    } else {
        echo 'no';
    }
}

if (isset($_POST['SubcategoryNewStr'])) {
    $query = 'SELECT k_is_name FROM k_immovables_subcategories WHERE k_is_parent=4 AND k_is_name="' . addslashes($_POST['SubcategoryNewStr']) . '"';
    $result = mysql_query($query);
    if (mysql_num_rows($result) > 0) {
        echo 'have';
    } else {
        if (mysql_query('INSERT INTO k_immovables_subcategories (k_is_name,k_is_parent) VALUES ("' . addslashes($_POST['SubcategoryNewStr']) . '",4)')) {
            echo 'yes';
        } else {
            echo 'no';
        }
    }
}

if (isset($_POST['NewNewsAddShow'])) {
    $query = 'SELECT * FROM k_immovables_subcategories WHERE k_is_parent=4 ORDER BY k_is_name ASC';
    $result = mysql_query($query);
    $sel = '<select id="SubcategorySelectNewsAdd" style="width: 100%;">';
    while ($row = mysql_fetch_array($result)) {
        $sel .= '<option value="' . $row['k_is_id'] . '">' . $row['k_is_name'] . '</option>';
    }
    $sel .= '</select>';
    echo '<tr>
      <td><p class="style_2">Выбрать рубрику:</p></td>
      <td>' . $sel . '<br></td></tr>
      <tr>
      <td><p class="style_2">Заголовок Новости:</p></td>
      <td><input type="text" id="NewsNewHeaderAdd" style="width: 100%;" value=""></td>
      </tr>
      <tr>
      <td colspan="2">
      <p class="style_2">Текст Новости:</p>
      <textarea rows="12" cols="55" id="NewsNewTextAdd" style="width: 100%; resize: none;" name="text"></textarea>
      </td>
      </tr>
      <tr>
      <td colspan="2">
      <button style="float:left; width: 100%;" onclick="NewsNewAddSubmit();">Создать</button>
      </td>
      </tr>';
}

if (isset($_POST['NewsNewHeaderAdd'])) {
    $query = 'INSERT INTO k_immovables_subcategories_news (k_isn_header,k_isn_text,k_isn_parent,k_isn_date) VALUES ("' . addslashes($_POST['NewsNewHeaderAdd']) . '","' . addslashes($_POST['NewsNewTextAdd']) . '",' . $_POST['SubcategorySelectNewsAdd'] . ',NOW())';
    if (mysql_query($query)) {
        echo 'yes';
    } else {
        echo 'no';
    }
}

if (isset($_GET['SearchNewsSubmit'])) {
    $PageType = 4;
    if (trim($_GET['SearchNewsID']) != '') {
        $query .= ' AND k_isn_id=' . $_GET['SearchNewsID'];
    }
    if (trim($_GET['SearchNewsHeader']) != "") {
        $query .= ' AND k_isn_header LIKE "%' . $_GET['SearchNewsHeader'] . '%"';
    }
    if ($_GET['SelectSearchNews'] != "no") {
        $query .= ' AND k_isn_parent=' . $_GET['SelectSearchNews'];
    }
    $_SESSION['WhereNews'] = $query;
}

if (isset($_GET['SearchNewsCancel'])) {
    $PageType = 4;
    unset($_SESSION['WhereNews']);
}

if (isset($_POST['BuysID'])) {
    $buys = new Buys();
    $buys->LoadBuys(0, 0, '');
    $buys->GenerateEdit($_POST['BuysID']);
}

if (isset($_POST['BuysEditID'])) {
    $_POST['BuysTextEdit'] = filter_var($_POST['BuysTextEdit'], FILTER_SANITIZE_STRIPPED);
    $_POST['BuysEditID'] = filter_var($_POST['BuysEditID'], FILTER_VALIDATE_INT);
    $query = 'UPDATE k_immovables_buy SET k_ib_text="' . addslashes($_POST['BuysTextEdit']) . '" WHERE k_ib_id=' . $_POST['BuysEditID'];
    if (mysql_query($query)) {
        echo 'yes';
    } else {
        echo 'no';
    }
}

if (isset($_POST['BuysDeleteID'])) {
    $query = 'DELETE FROM k_immovables_buy WHERE k_ib_id=' . $_POST['BuysDeleteID'];
    if (mysql_query($query)) {
        echo 'yes';
    } else {
        echo 'no';
    }
}

if (isset($_GET['SearchBuysSubmit'])) {
    $PageType = 5;
    if (trim($_GET['SearchBuysID']) != '') {
        $query .= ' WHERE k_ib_id=' . $_GET['SearchBuysID'];
    }
    $_SESSION['WhereBuy'] = $query;
}

if (isset($_GET['SearchBuysReset'])) {
    $PageType = 5;
    $_SESSION['WhereBuy'] = '';
}

if (isset($_POST['ImmoEmailEmail'])) {
    $_POST['ImmoEmailEmail'] = filter_var($_POST['ImmoEmailEmail'], FILTER_SANITIZE_EMAIL);
    $_POST['ImmoEmailTheme'] = filter_var($_POST['ImmoEmailTheme'], FILTER_SANITIZE_STRIPPED);
    $_POST['ImmoEmailText'] = filter_var($_POST['ImmoEmailText'], FILTER_SANITIZE_STRIPPED);
    $message = "Текст: " . $_POST["ImmoEmailText"] . "\n";

    $mail             = new PHPMailer();
    $mail->CharSet = 'UTF-8';
    $mail->IsSMTP(); // telling the class to use SMTP
    try {
        //$mail->Host       = "tomsk-line.ru"; // SMTP server
        $mail->Host       = "192.168.151.141"; // SMTP server
        //$mail->Host       = "localhost"; // SMTP server
        /*
        $mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
                                                   // 1 = errors and messages
                                                   // 2 = messages only
        $mail->SMTPAuth   = true;                  // enable SMTP authentication
        $mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
        $mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
        $mail->Port       = 465;                   // set the SMTP port for the GMAIL server
        $mail->Username   = "TOMSK-LINE.ru@gmail.com ";  // GMAIL username
        $mail->Password   = "Qwer1@34";            // GMAIL password
        */

        $mail->SetFrom('noreply@'._SERVER_ADDRESS, _SERVER_ADDRESS);
        $mail->AddAddress($_POST['ImmoEmailEmail'], '');
        $mail->Subject    =  'Message from '.strtoupper(_SERVER_ADDRESS).' Administrator - ' . date("Y-m-d H:i:s");
        $mail->AltBody    = $message;
        $mail->MsgHTML($message);
        $mail->Send();
        echo 'Письмо отправлено!';
    } catch (phpmailerException $e) {
        echo $e->errorMessage(); //Pretty error messages from PHPMailer
    } catch (Exception $e) {
        echo $e->getMessage(); //Boring error messages from anything else!
    }
/*
    if (mb_send_mail($_POST['ImmoEmailEmail'], 'Сообщение от администратора TOMSK-LINE.RU - ' . date("Y-m-d H:m:s"), $message, "From: \"TOMSK-LINE.ru\"\n")) {
        echo 'yes';
    }
*/
}

if (isset($_POST['AddressSelectChange'])) {
    $_POST['AddressSelectChange'] = filter_var($_POST['AddressSelectChange'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare("SELECT k_tm_name AS mass,k_d_name AS district
            FROM k_streets_house_nums AS kshn
            LEFT JOIN k_towns_massives AS ktm ON (ktm.k_tm_id = kshn.k_shn_massive_id)
            LEFT JOIN k_districts AS kd ON (kd.k_d_id = kshn.k_shn_district_id)
            WHERE k_shn_id=:id LIMIT 1");
        $queue->execute(array(":id" => $_POST['AddressSelectChange']));
        $result = $queue->fetch(PDO::FETCH_ASSOC);
        if (!$result['district']) {
            $result['district'] = '';
        }
        if (!$result['mass']) {
            $result['mass'] = '';
        }
        echo json_encode($result);
    } catch (PDOException $e) {
        exit();
    }
}

//BANNER SECTION
if (isset($_POST['banner_id'])) {
    $banners = new BannersAll($_POST['banner_id']);
    echo $banners->banner_code[0];
}
if (isset($_POST['banner_change_id'])) {
    $_POST['banner_change_id'] = filter_var($_POST['banner_change_id'], FILTER_VALIDATE_INT);
    $code = $_POST['banner_change_code'];
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('UPDATE k_all_banners SET k_ab_code=:code WHERE k_ab_id=:id');
        $query->execute(array(":code" => $code, ":id" => $_POST['banner_change_id']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['BannerImmoIDInfo'])) {
    $_POST['BannerImmoIDInfo'] = filter_var($_POST['BannerImmoIDInfo'], FILTER_VALIDATE_INT);
    $banner = new BannersAll($_POST['BannerImmoIDInfo']);
    echo '<tr>
        <td><p class="style_2">Организация:</p></td>
        <td><input id="BannerInfoOrganization" type="text" value="' . $banner->banner_organization[0] . '"></td>
        </tr>
        <tr>
        <td><p class="style_2">Имя:</p></td>
        <td><input id="BannerInfoContactName" type="text" value="' . $banner->banner_contact_name[0] . '"></td>
        </tr>
        <tr>
        <td><p class="style_2">Контакт:</p></td>
        <td><input id="BannerInfoContacts" type="text" value="' . $banner->banner_contacts[0] . '"></td>
        </tr>
        <tr>
        <td colspan="2"><button onclick="ChangeBannerInfo(' . $_POST['BannerImmoIDInfo'] . ');" style="float:right;">Изменить</button></td>
        </tr>';
}
if (isset($_POST['BannerImmoIDChange'])) {
    $_POST['BannerImmoIDChange'] = filter_var($_POST['BannerImmoIDChange'], FILTER_VALIDATE_INT);
    $_POST['BannerImmoChangeOrganization'] = filter_var($_POST['BannerImmoChangeOrganization'], FILTER_SANITIZE_STRIPPED);
    $_POST['BannerImmoChangeContactName'] = filter_var($_POST['BannerImmoChangeContactName'], FILTER_SANITIZE_STRIPPED);
    $_POST['BannerImmoChangeContacts'] = filter_var($_POST['BannerImmoChangeContacts'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('UPDATE k_all_banners SET k_ab_organization=:org, k_ab_contact_name=:name, k_ab_contacts=:contacts WHERE k_ab_id=:id');
        $query->execute(array(":org" => $_POST['BannerImmoChangeOrganization'],
            ":name" => $_POST['BannerImmoChangeContactName'],
            ":contacts" => $_POST['BannerImmoChangeContacts'],
            ":id" => $_POST['BannerImmoIDChange']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
if (isset($_POST['BannerAddDaysLast'])) {
    $banners = new BannersAll($_POST['BannerAddDaysLast']);
    echo $banners->banner_end_days[0];
}
if (isset($_POST['BannersAddDaysSubmit'])) {
    $_POST['BannersAddDaysSubmit'] = filter_var($_POST['BannersAddDaysSubmit'], FILTER_VALIDATE_INT);
    //$end_date = date('Y-m-d H:i:s', time() + $_POST['BannersAddDaysPlus'] * 24 * 60 * 60);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('UPDATE k_all_banners SET k_ab_end_date=NOW() + INTERVAL :date DAY WHERE k_ab_id=:id');
        $query->execute(array(":date" => $_POST['BannersAddDaysPlus'], ":id" => $_POST['BannersAddDaysSubmit']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
?>