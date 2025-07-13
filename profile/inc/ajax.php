<?php

define('TOMSKLINE', 1);
session_start();
require_once '../../inc/configs.php';
require_once '../../inc/functions.php';
try {
    $mysql2 = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
    $mysql2->exec('set names utf8');
} catch (PDOException $e) {
    exit();
}

//Поиск адреса
if (isset($_POST['SearchAddress'])) {
    $_POST['SearchAddress'] = WrongLanguage(1, filter_var($_POST['SearchAddress'], FILTER_SANITIZE_STRIPPED));
    $_POST['SearchAddress'] = str_replace('-', ' ', $_POST['SearchAddress']);
    $search_string = explode(" ", $_POST['SearchAddress']);
    $search_num = "";
    $a = 0;
    if (preg_match("(^[0-9])", $search_string[count($search_string) - 1]) && count($search_string) > 1) {
        $search_num = $search_string[count($search_string) - 1];
        $a = 1;
    }
    try {
        $cant_find = FALSE;
        $queue = 'SELECT * FROM k_streets_house_nums as kshn
                LEFT JOIN k_streets as ks ON (ks.k_s_id = kshn.k_shn_street_id)
                WHERE k_s_name LIKE CONCAT ("%", :addr, "%")';
        if ($search_num != "") {
            $queue .= ' AND k_shn_house_num LIKE CONCAT ("' . $search_num . '", "%") ';
        }
        $queue .= 'ORDER BY k_shn_house_num ASC LIMIT 10';
        $query = $mysql2->prepare($queue);
        $query->execute(array(':addr' => RussianRules(str_replace($search_num, '', $_POST['SearchAddress']))));
        if ($query->rowCount() > 0) {
            $row = $query->fetchAll(PDO::FETCH_ASSOC);
        } elseif (count($search_string) > 1) {
            $queue = 'SELECT fin.* FROM (SELECT * FROM k_streets_house_nums as kshn
                LEFT JOIN k_streets as ks ON (ks.k_s_id = kshn.k_shn_street_id)
                WHERE k_s_name LIKE CONCAT ("%", :addr, "%")) AS fin
                WHERE k_s_name LIKE CONCAT ("%", :addr2, "%")';
            if ($search_num != "") {
                $queue .= ' AND k_shn_house_num LIKE CONCAT ("' . $search_num . '", "%") ';
            }
            $queue .= 'ORDER BY k_shn_house_num ASC LIMIT 10';
            $query = $mysql2->prepare($queue);
            $query->execute(array(":addr" => RussianRules($search_string[0]), ":addr2" => RussianRules($search_string[1])));
            if ($query->rowCount() > 0) {
                $row = $query->fetchAll(PDO::FETCH_ASSOC);
            }
        } else {
            $cant_find = TRUE;
        }
        if ($cant_find) {
            for ($i = 0; $i < count($search_string); $i++) {
                $query = $mysql2->prepare('SELECT * FROM k_streets_house_nums as kshn
                        LEFT JOIN k_streets as ks ON (ks.k_s_id = kshn.k_shn_street_id)
                        WHERE k_s_name LIKE CONCAT ("%", :addr, "%") ORDER BY k_shn_house_num ASC
                        LIMIT 10');
                $query->execute(array(':addr' => RussianRules($search_string[$i])));
                $row = $query->fetchAll(PDO::FETCH_ASSOC);
            }
        }
    } catch (PDOException $e) {
        exit();
    }

    if (count($row) == 0) {
        echo '';
    } else {
        $print = '<div id="address_choise"><table style="width: 100%;">';
        foreach ($row as $value) {
            $street = array();
            $house = array();
            $print .= '<tr><td class="addr_link"><a ind="' . $value['k_shn_id'] . '" onclick="ChangeAddr(this);">';
            if (preg_match('/(###)/', $value['k_s_name'])) {
                $street = explode('###', $value['k_s_name']);
                $house = explode('###', $value['k_shn_house_num']);
                $print .= $street[0] . ' ' . $house[0] . ' / ' . $street[1] . ' ' . $house[1];
            } else {
                $print .= $value['k_s_name'] . ' ' . $value['k_shn_house_num'];
            }
            $print .= '</a></td></tr>';
        }
        $print .='</table></div>';
        echo $print;
    }
}

//Массив и Район
if (isset($_POST['DistrMass']) && filter_input(INPUT_POST, 'DistrMass', FILTER_VALIDATE_INT)) {
    $query = $mysql2->prepare('SELECT k_tm_name AS massive, k_d_name AS district FROM k_streets_house_nums AS kshn
      LEFT JOIN k_districts AS kd ON (kd.k_d_id = kshn.k_shn_district_id)
      LEFT JOIN k_towns_massives AS ktm ON (ktm.k_tm_id = kshn.k_shn_massive_id)
      WHERE k_shn_id=:id LIMIT 1');
    $query->execute(array(':id' => $_POST['DistrMass']));
    $row = $query->fetch(PDO::FETCH_ASSOC);
    echo json_encode($row);
}

if (isset($_POST['SetMainPhoto'])) {
    $_POST['SetMainPhoto'] = str_replace('../admin/images/1_', 'images/', filter_var($_POST['SetMainPhoto'], FILTER_SANITIZE_STRIPPED));
    $query = $mysql2->prepare('SELECT * FROM k_immovables_photos WHERE k_ip_url=:url LIMIT 1');
    $query->execute(array(':url' => $_POST['SetMainPhoto']));
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if ($query->rowCount() > 0) {
        $query2 = $mysql2->prepare('UPDATE k_immovables_photos SET k_ip_priority=0 WHERE k_ip_immo_id=:id');
        $query2->execute(array(':id' => $result['k_ip_immo_id']));
        $query3 = $mysql2->prepare('UPDATE k_immovables_photos SET k_ip_priority=1 WHERE k_ip_id=:id');
        $query3->execute(array(':id' => $result['k_ip_id']));
        echo 'yes';
    } else {
        echo 'not_yet';
    }
}

if (isset($_POST['DeleteThisPhoto'])) {
    $link_to_photo = filter_var($_POST['DeleteThisPhoto'], FILTER_SANITIZE_STRIPPED);
    /*
    $_POST['DeleteThisPhoto'] = str_replace('../admin/images/1_', 'images/', $link_to_photo);
    if (file_exists('../' . $link_to_photo)) {
        unlink('../' . $link_to_photo);
        unlink(str_replace('images/', 'images/1_', '../' . $link_to_photo));
    }
    $query = $mysql2->prepare('DELETE FROM k_immovables_photos WHERE k_ip_url=:url');
    $query->execute(array(':url' => $_POST['DeleteThisPhoto']));
    echo 'yes';
    */
    $_POST['DeleteThisPhoto'] = str_replace('../video/', 'video/', str_replace('../admin/images/1_', 'images/', $link_to_photo));
        $query = $mysql2->prepare('SELECT * FROM k_immovables_photos WHERE k_ip_url=:url');
        $query->execute(array(':url' => $_POST['DeleteThisPhoto']));
        if ($query->rowCount() > 0) {
            if (file_exists('../' . $link_to_photo)) {
                unlink('../' . $link_to_photo);
                if (strpos($link_to_photo, 'video') === FALSE) {
                    unlink(str_replace('images/1_', 'images/', '../' . $link_to_photo));
                } else {
                    $format = end(explode('.', $link_to_photo));
                    $link_to_photo = str_replace($format, 'flv', $link_to_photo);
                    unlink('../' . $link_to_photo);
                }
            }
            $query = $mysql2->prepare('DELETE FROM k_immovables_photos WHERE k_ip_url=:url');
            $query->execute(array(':url' => $_POST['DeleteThisPhoto']));
            echo 'yes';
        }
}

if (isset($_POST['SetMainPhoto_p'])) {
    $_POST['SetMainPhoto_p'] = str_replace('../admin/images/photo/1_', 'images/photo/', filter_var($_POST['SetMainPhoto_p'], FILTER_SANITIZE_STRIPPED));
    $query = $mysql2->prepare('SELECT * FROM k_photodesk_photos WHERE k_pdp_link=:url LIMIT 1');
    $query->execute(array(':url' => $_POST['SetMainPhoto_p']));
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if ($query->rowCount() > 0) {
        $query2 = $mysql2->prepare('UPDATE k_photodesk_photos SET k_pdp_priority=0 WHERE k_pdp_ad_id=:id');
        $query2->execute(array(':id' => $result['k_pdp_ad_id']));
        $query3 = $mysql2->prepare('UPDATE k_photodesk_photos SET k_pdp_priority=1 WHERE k_pdp_id=:id');
        $query3->execute(array(':id' => $result['k_pdp_id']));
        echo 'yes';
    } else {
        echo 'not_yet';
    }
}

if (isset($_POST['DeleteThisPhoto_p'])) {
    $link_to_photo = filter_var($_POST['DeleteThisPhoto_p'], FILTER_SANITIZE_STRIPPED);
    /*
    $_POST['DeleteThisPhoto_p'] = str_replace('../admin/images/photo/1_', 'images/photo/', $link_to_photo);
    if (file_exists('../' . $link_to_photo)) {
        unlink('../' . $link_to_photo);
        unlink(str_replace('images/photo/', 'images/photo/1_', '../' . $link_to_photo));
    }
    $query = $mysql2->prepare('DELETE FROM k_photodesk_photos WHERE k_pdp_link=:url');
    $query->execute(array(':url' => $_POST['DeleteThisPhoto_p']));
    echo 'yes';
    */
    $_POST['DeleteThisPhoto_p'] = str_replace('../video/', 'video/', str_replace('../admin/images/photo/1_', 'images/photo/', $link_to_photo));
        $query = $mysql2->prepare('SELECT * FROM k_photodesk_photos WHERE k_pdp_link=:url');
        $query->execute(array(':url' => $_POST['DeleteThisPhoto_p']));
        if ($query->rowCount() > 0) {
            if (file_exists('../' . $link_to_photo)) {
                unlink('../' . $link_to_photo);
                if (strpos($link_to_photo, 'video') === FALSE) {
                    unlink(str_replace('images/photo/1_', 'images/photo/', '../' . $link_to_photo));
                } else {
                    $format = end(explode('.', $link_to_photo));
                    $link_to_photo = str_replace($format, 'flv', $link_to_photo);
                    unlink('../' . $link_to_photo);
                }
            }
            $query = $mysql2->prepare('DELETE FROM k_photodesk_photos WHERE k_pdp_link=:url');
            $query->execute(array(':url' => $_POST['DeleteThisPhoto_p']));
            echo 'yes';
        }
}
if (isset($_POST['LoadImmovablePhotoID'])) {
    $_POST['LoadImmovablePhotoID'] = filter_var($_POST['LoadImmovablePhotoID'], FILTER_VALIDATE_INT);
    $_POST['LoadImmovablePhotoURL'] = str_replace('../admin/images/1_', 'images/', filter_var($_POST['LoadImmovablePhotoURL'], FILTER_SANITIZE_STRIPPED));
    $_POST['LoadImmovablePhotoURL'] = str_replace('../video/', 'video/', filter_var($_POST['LoadImmovablePhotoURL'], FILTER_SANITIZE_STRIPPED));

    if ($_POST['LoadImmovablePhotoID']) {
        $query = $mysql2->prepare('INSERT INTO k_immovables_photos (k_ip_url,k_ip_immo_id,k_ip_priority) VALUES (:url,:ad_id,0)');
        $query->execute(array(':url' => $_POST['LoadImmovablePhotoURL'], ':ad_id' => $_POST['LoadImmovablePhotoID']));
    }
}
if (isset($_POST['LoadPhotodeskPhotoID'])) {
    $_POST['LoadPhotodeskPhotoID'] = filter_var($_POST['LoadPhotodeskPhotoID'], FILTER_VALIDATE_INT);
    $_POST['LoadPhotodeskPhotoURL'] = str_replace('../admin/images/photo/1_', 'images/photo/', filter_var($_POST['LoadPhotodeskPhotoURL'], FILTER_SANITIZE_STRIPPED));
    $_POST['LoadPhotodeskPhotoURL'] = str_replace('../video/', 'video/', filter_var($_POST['LoadPhotodeskPhotoURL'], FILTER_SANITIZE_STRIPPED));

    if ($_POST['LoadPhotodeskPhotoID']) {
        $query = $mysql2->prepare('INSERT INTO k_photodesk_photos (k_pdp_link,k_pdp_ad_id,k_pdp_priority) VALUES (:url,:ad_id,0)');
        $query->execute(array(':url' => $_POST['LoadPhotodeskPhotoURL'], ':ad_id' => $_POST['LoadPhotodeskPhotoID']));
    }
}

if (isset($_POST['ChangeAvatarAgentID'])) {
    $_POST['ChangeAvatarAgentID'] = filter_var($_POST['ChangeAvatarAgentID'], FILTER_VALIDATE_INT);
    $_POST['ChangeAvatarAgentURL'] = str_replace('../admin/', '', filter_var($_POST['ChangeAvatarAgentURL'], FILTER_SANITIZE_STRIPPED));
    $query = $mysql2->prepare('SELECT k_ua_avatar FROM k_users_agents WHERE k_ua_id=:id LIMIT 1');
    $query->execute(array(':id' => $_POST['ChangeAvatarAgentID']));
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if (file_exists('../../admin/' . $result['k_ua_avatar'])) {
        unlink('../../admin/' . $result['k_ua_avatar']);
    }
    $query2 = $mysql2->prepare('UPDATE k_users_agents SET k_ua_avatar=:ava WHERE k_ua_id=:id LIMIT 1');
    $query2->execute(array(':id' => $_POST['ChangeAvatarAgentID'], ":ava" => $_POST['ChangeAvatarAgentURL']));
}

if (isset($_POST['ChangeAvatarExpertID'])) {
    $_POST['ChangeAvatarExpertID'] = filter_var($_POST['ChangeAvatarExpertID'], FILTER_VALIDATE_INT);
    $_POST['ChangeAvatarExpertURL'] = str_replace('../admin/', '', filter_var($_POST['ChangeAvatarExpertURL'], FILTER_SANITIZE_STRIPPED));
    $query = $mysql2->prepare('SELECT k_e_image FROM k_experts WHERE k_e_id=:id LIMIT 1');
    $query->execute(array(':id' => $_POST['ChangeAvatarExpertID']));
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if (file_exists('../../admin/' . $result['k_e_image'])) {
        unlink('../../admin/' . $result['k_e_image']);
    }
    $query2 = $mysql2->prepare('UPDATE k_experts SET k_e_image=:ava WHERE k_e_id=:id LIMIT 1');
    $query2->execute(array(':id' => $_POST['ChangeAvatarExpertID'], ":ava" => $_POST['ChangeAvatarExpertURL']));
}

if (isset($_POST['DeleteQuestion'])) {
    $_POST['DeleteQuestion'] = filter_var($_POST['DeleteQuestion'], FILTER_VALIDATE_INT);
    if (isset($_SESSION['id_e'])) {
        $query = $mysql2->prepare('DELETE keq.*, kea.*
            FROM k_experts_questions AS keq
            LEFT JOIN k_experts_answers AS kea ON (keq.k_eq_id = kea.k_ea_question_id)
            WHERE k_eq_id=:id AND k_eq_expert_id=:expert');
        $query->execute(array(':id' => $_POST['DeleteQuestion'], ':expert' => $_SESSION['id_e']));
        echo 'yes';
    }
}

if (isset($_POST['SaveAnswer'])) {
    $_POST['SaveAnswer'] = filter_var($_POST['SaveAnswer'], FILTER_VALIDATE_INT);
    $_POST['SaveAnswerTEXT'] = filter_var($_POST['SaveAnswerTEXT'], FILTER_SANITIZE_STRIPPED);
    if (isset($_SESSION['id_e'])) {
        $query = $mysql2->prepare('SELECT k_ea_id
            FROM k_experts_questions AS keq
            LEFT JOIN k_experts_answers AS kea ON (keq.k_eq_id = kea.k_ea_question_id)
            WHERE k_eq_expert_id=:expert AND k_ea_question_id=:id
            LIMIT 1');
        $query->execute(array(':id' => $_POST['SaveAnswer'], ':expert' => $_SESSION['id_e']));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if ($result['k_ea_id']) {
            $query2 = $mysql2->prepare('UPDATE k_experts_answers SET k_ea_text=:text WHERE k_ea_id=:id');
            $query2->execute(array(':id' => $result['k_ea_id'], ':text' => $_POST['SaveAnswerTEXT']));
        } else {
            $query2 = $mysql2->prepare('INSERT INTO k_experts_answers
                (k_ea_text,k_ea_question_id)
                VALUES (:text,:id)');
            $query2->execute(array(':id' => $_POST['SaveAnswer'], ':text' => $_POST['SaveAnswerTEXT']));
        }
        echo 'yes';
    }
}

if (isset($_POST['ShowMessage'])) {
    $_POST['ShowMessage'] = filter_var($_POST['ShowMessage'], FILTER_VALIDATE_INT);
    $query = $mysql2->prepare('SELECT k_um_text,k_um_read
        FROM k_user_messages WHERE k_um_id=:id AND k_um_user_id=:uid');
    $query->execute(array(':id' => $_POST['ShowMessage'], ':uid' => $_SESSION['id']));
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if ($result['k_um_read'] == 0) {
        $query2 = $mysql2->prepare('UPDATE k_user_messages SET k_um_read=1 WHERE k_um_id=:id AND k_um_user_id=:uid');
        $query2->execute(array(':id' => $_POST['ShowMessage'], ':uid' => $_SESSION['id']));
    }
    echo $result['k_um_text'];
}

if (isset($_POST['SendMessageID'])) {
    $_POST['SendMessageID'] = filter_var($_POST['SendMessageID'], FILTER_VALIDATE_INT);
    $_POST['SendMessageText'] = filter_var($_POST['SendMessageText'], FILTER_SANITIZE_STRIPPED);
    if ($_POST['SendMessageText'] != '') {
        $query = $mysql2->prepare('SELECT k_um_sender_id FROM k_user_messages WHERE k_um_id=:id AND k_um_user_id=:uid');
        $query->execute(array(':id' => $_POST['SendMessageID'], ':uid' => $_SESSION['id']));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if ($result['k_um_sender_id'] != 0 && $query->rowCount() > 0) {
            $query2 = $mysql2->prepare('INSERT INTO k_user_messages
            (k_um_text,k_um_user_id,k_um_sender_id,k_um_date)
            VALUES (:text,:uid,:sid,NOW())');
            $query2->execute(array(':text' => $_POST['SendMessageText'], ':sid' => $_SESSION['id'], ':uid' => $result['k_um_sender_id']));
            echo 'yes';
        } else {
            echo 'no';
        }
    } else {
        echo 'notext';
    }
}

if (isset($_POST['DeleteMessage'])) {
    $_POST['DeleteMessage'] = filter_var($_POST['DeleteMessage'], FILTER_VALIDATE_INT);
    $query = $mysql2->prepare('DELETE FROM k_user_messages WHERE k_um_id=:id AND k_um_user_id=:uid');
    $query->execute(array(':id' => $_POST['DeleteMessage'], ':uid' => $_SESSION['id']));
    echo 'yes';
}
if (isset($_POST['DeleteMessageOutbox'])) {
    $_POST['DeleteMessageOutbox'] = filter_var($_POST['DeleteMessageOutbox'], FILTER_VALIDATE_INT);
    $query = $mysql2->prepare('DELETE FROM k_user_messages WHERE k_um_id=:id AND k_um_sender_id=:uid');
    $query->execute(array(':id' => $_POST['DeleteMessageOutbox'], ':uid' => $_SESSION['id']));
    echo 'yes';
}
if (isset($_POST['ShowMessageOubox'])) {
    $_POST['ShowMessageOubox'] = filter_var($_POST['ShowMessageOubox'], FILTER_VALIDATE_INT);
    $query = $mysql2->prepare('SELECT k_um_text,k_um_read
        FROM k_user_messages WHERE k_um_id=:id AND k_um_sender_id=:uid');
    $query->execute(array(':id' => $_POST['ShowMessageOubox'], ':uid' => $_SESSION['id']));
    $result = $query->fetch(PDO::FETCH_ASSOC);
    echo $result['k_um_text'];
}
if (isset($_POST['SendMessageOutboxID'])) {
    $_POST['SendMessageOutboxID'] = filter_var($_POST['SendMessageOutboxID'], FILTER_VALIDATE_INT);
    $_POST['SendMessageOutboxText'] = filter_var($_POST['SendMessageOutboxText'], FILTER_SANITIZE_STRIPPED);
    if ($_POST['SendMessageOutboxText'] != '') {
        $query = $mysql2->prepare('SELECT k_um_user_id FROM k_user_messages WHERE k_um_id=:id AND k_um_sender_id=:uid');
        $query->execute(array(':id' => $_POST['SendMessageOutboxID'], ':uid' => $_SESSION['id']));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if ($result['k_um_user_id'] != 0 && $query->rowCount() > 0) {
            $query2 = $mysql2->prepare('INSERT INTO k_user_messages
            (k_um_text,k_um_user_id,k_um_sender_id,k_um_date)
            VALUES (:text,:uid,:sid,NOW())');
            $query2->execute(array(':text' => $_POST['SendMessageOutboxText'], ':sid' => $_SESSION['id'], ':uid' => $result['k_um_user_id']));
            echo 'yes';
        } else {
            echo 'no';
        }
    } else {
        echo 'notext';
    }
}
if (isset($_POST['PackageUseID'])) {
    $_POST['PackageUseID'] = filter_var($_POST['PackageUseID'], FILTER_VALIDATE_INT);
    $query = $mysql2->prepare('SELECT kip.*
                    FROM k_immovables_sell AS kis
                    LEFT JOIN k_immovables_packets AS kip ON (kip.k_ip_immo_id = kis.k_isf_id)
                    WHERE k_isf_user_id=:id AND k_ip_immo_id=:immo
                    ORDER BY k_ip_packet ASC');
    $query->execute(array(':immo' => $_POST['PackageUseID'], ':id' => $_SESSION['id']));
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    $json_array = array();
    if ($result[0]['k_ip_packet'] == 1) {
        $json_array["tslock"] = $result[0]["k_ip_lock"];
        $json_array["tsup"] = $result[0]["k_ip_up"];
        $json_array["tscolor"] = $result[0]["k_ip_color"];
        $json_array["tsvip"] = $result[0]["k_ip_vip"];
    } else {
        $json_array["tslock"] = "0";
        $json_array["tsup"] = "0";
        $json_array["tscolor"] = "0";
        $json_array["tsvip"] = "0";
    }
    if ($result[0]['k_ip_packet'] == 2) {
        $json_array["qslock"] = $result[0]["k_ip_lock"];
        $json_array["qsup"] = $result[0]["k_ip_up"];
        $json_array["qscolor"] = $result[0]["k_ip_color"];
        $json_array["qsvip"] = $result[0]["k_ip_vip"];
    } elseif ($result[1]['k_ip_packet'] == 2) {
        $json_array["qslock"] = $result[1]["k_ip_lock"];
        $json_array["qsup"] = $result[1]["k_ip_up"];
        $json_array["qscolor"] = $result[1]["k_ip_color"];
        $json_array["qsvip"] = $result[1]["k_ip_vip"];
    } else {
        $json_array["qslock"] = "0";
        $json_array["qsup"] = "0";
        $json_array["qscolor"] = "0";
        $json_array["qsvip"] = "0";
    }
    $up = new UserPackages($_SESSION['id']);
    $json_array["qsremain"] = $up->num[1];
    $json_array["tsremain"] = $up->num[0];
    echo json_encode($json_array);
}

if (isset($_POST['ImmoIDDelSubmit'])) {
    $query_0 = $mysql2->prepare('SELECT * FROM k_immovables_sell WHERE k_isf_id=:id AND k_isf_user_id=:user');
    $query_0->execute(array(':user' => $_SESSION['id'], ':id' => $_POST['ImmoIDDelSubmit']));
    $result_0 = $query_0->fetch(PDO::FETCH_ASSOC);
    if ($query_0->rowCount() > 0) {
        $query = $mysql2->prepare('DELETE FROM k_immovables_sell WHERE k_isf_id=:id AND k_isf_user_id=:user');
        $query->execute(array(':user' => $_SESSION['id'], ':id' => $_POST['ImmoIDDelSubmit']));
        $query1 = $mysql2->prepare('SELECT k_ip_url FROM k_immovables_photos WHERE k_ip_immo_id=:id');
        $query1->execute(array(':id' => $_POST['ImmoIDDelSubmit']));
        $result1 = $query1->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result1 as $row2) {
            if (preg_match('/video/', $row2['k_ip_url'])) {
                unlink('../../' . $row2['k_ip_url']);
                unlink('../../' . str_replace('.flv', '.jpg', $row2['k_ip_url']));
            } else {
                unlink('../../admin/' . $row2['k_ip_url']);
            }
        }
        $query3 = $mysql2->prepare('DELETE FROM k_immovables_special WHERE k_ip_immo_id=:id');
        $query3->execute(array(':id' => $_POST['ImmoIDDelSubmit']));
        echo 'yes';
    } else {
        echo 'no';
    }
}

if (isset($_POST['DeletePhotoAd'])) {
    $id = filter_var($_POST['DeletePhotoAd'], FILTER_VALIDATE_INT);
    try {
        $queue0 = $mysql2->prepare('SELECT * FROM k_photodesk WHERE k_pd_id=:id AND k_pd_user_id=:user');
        $queue0->execute(array(':id' => $id, ':user' => $_SESSION['id']));
        if ($queue0->rowCount() > 0) {
            $queue1 = $mysql2->prepare('DELETE FROM k_photodesk WHERE k_pd_id=:id AND k_pd_user_id=:user');
            $queue1->execute(array(':id' => $id, ':user' => $_SESSION['id']));
            $queue2 = $mysql2->prepare('DELETE FROM k_photodesk_comments WHERE k_pc_photodesk_id=:id');
            $queue2->execute(array(':id' => $id));
            $queue3 = $mysql2->prepare('SELECT k_pdp_link FROM k_photodesk_photos WHERE k_pdp_ad_id=:id');
            $queue3->execute(array(':id' => $id));
            $result = $queue3->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $value) {
                unlink('../../admin/' . $value['k_pdp_link']);
                unlink('../../admin/1_' . $value['k_pdp_link']);
            }
            $queue4 = $mysql2->prepare('DELETE FROM k_photodesk_photos WHERE k_pdp_ad_id=:id');
            $queue4->execute(array(':id' => $id));
            echo 'yes';
        } else {
            echo 'no';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['DeleteJob'])) {
    $_POST['DeleteJob'] = filter_var($_POST['DeleteJob'], FILTER_VALIDATE_INT);
    try {
        $queue0 = $mysql2->prepare('SELECT * FROM k_job WHERE k_j_id=:id AND k_j_user_id=:user');
        $queue0->execute(array(":id" => $_POST['DeleteJob'], ':user' => $_SESSION['id']));
        if ($queue0->rowCount() > 0) {
            $queue = $mysql2->prepare('DELETE FROM k_job WHERE k_j_id=:id AND k_j_user_id=:user');
            $queue->execute(array(":id" => $_POST['DeleteJob'], ':user' => $_SESSION['id']));
            $queue1 = $mysql2->prepare('DELETE FROM k_job_organizations WHERE k_jo_job_id=:id');
            $queue1->execute(array(":id" => $_POST['DeleteJob']));
            $queue2_0 = $mysql2->prepare('SELECT k_jp_avatar FROM k_job_person WHERE k_jp_job_id=:id');
            $queue2_0->execute(array(":id" => $_POST['DeleteJob']));
            $result2_0 = $queue2_0->fetch(PDO::FETCH_ASSOC);
            if (file_exists('../../admin/' . $result2_0['k_jp_avatar'])) {
                unlink('../../admin/' . $result2_0['k_jp_avatar']);
                unlink('../../admin/1_' . $result2_0['k_jp_avatar']);
            }
            $queue2 = $mysql2->prepare('DELETE FROM k_job_person WHERE k_jp_job_id=:id');
            $queue2->execute(array(":id" => $_POST['DeleteJob']));
            echo 'yes';
        } else {
            echo 'no';
        }
    } catch (PDOException $e) {
        echo 'no';
        exit();
    }
}

if (isset($_POST['UpdateJob'])) {
    $_POST['UpdateJob'] = filter_var($_POST['UpdateJob'], FILTER_VALIDATE_INT);
    try {
        $queue0 = $mysql2->prepare('SELECT * FROM k_job WHERE k_j_id=:id AND k_j_user_id=:user');
        $queue0->execute(array(":id" => $_POST['UpdateJob'], ':user' => $_SESSION['id']));
        if ($queue0->rowCount() > 0) {
            $queue = $mysql2->prepare('UPDATE k_job SET k_j_date_end=NOW() + INTERVAL 2 MONTH WHERE k_j_id=:id AND k_j_user_id=:user');
            $queue->execute(array(":id" => $_POST['UpdateJob'], ':user' => $_SESSION['id']));
            $queue1 = $mysql2->prepare('SELECT k_j_date_end FROM k_job WHERE k_j_id=:id AND k_j_user_id=:user');
            $queue1->execute(array(":id" => $_POST['UpdateJob'], ':user' => $_SESSION['id']));
            $result1 = $queue1->fetch(PDO::FETCH_ASSOC);
            echo $result1['k_j_date_end'];
        } else {
            echo 'no';
        }
    } catch (PDOException $e) {
        echo 'no';
        exit();
    }
}

if (isset($_POST['AddPackageToImmoID'])) {
    $_POST['AddPackageToImmoID'] = filter_var($_POST['AddPackageToImmoID'], FILTER_VALIDATE_INT);
    $_POST['AddPackageToImmoPackage'] = filter_var($_POST['AddPackageToImmoPackage'], FILTER_VALIDATE_INT);
    if (in_array($_POST['AddPackageToImmoPackage'], array(1, 2))) {
        $query = $mysql2->prepare('SELECT * FROM k_immovables_sell WHERE k_isf_id=:id AND k_isf_user_id=:uid');
        $query->execute(array(':id' => $_POST['AddPackageToImmoID'], ':uid' => $_SESSION['id']));
        if ($query->rowCount() > 0) {
            $up = new UserPackages($_SESSION['id']);
            if ($up->num[$_POST['AddPackageToImmoPackage'] - 1] > 0) {
                $query2 = $mysql2->prepare('SELECT kip.*
                    FROM k_immovables_sell AS kis
                    LEFT JOIN k_immovables_packets AS kip ON (kip.k_ip_immo_id = kis.k_isf_id)
                    WHERE k_isf_user_id=:id AND k_ip_immo_id=:immo AND k_ip_packet=:packet
                    ORDER BY k_ip_packet ASC');
                $query2->execute(array(':immo' => $_POST['AddPackageToImmoID'],
                    ':id' => $_SESSION['id'],
                    ':packet' => $_POST['AddPackageToImmoPackage']));
                $result2 = $query2->fetch(PDO::FETCH_ASSOC);
                $query3 = $mysql2->prepare('SELECT * FROM k_tariff_packages WHERE k_tp_id=:id');
                $query3->execute(array(':id' => $_POST['AddPackageToImmoPackage']));
                $result3 = $query3->fetch(PDO::FETCH_ASSOC);
                if ($result3['k_tp_lock_days'] != 0) {
                    $result3['k_tp_lock_days'] = 1;
                }
                if ($query2->rowCount() > 0) {
                    $query4 = $mysql2->prepare('UPDATE k_immovables_packets
                            SET k_ip_lock=:ld, k_ip_up=:up, k_ip_color=:color, k_ip_vip=:vip
                            WHERE k_ip_id=:id');
                    $query4->execute(array(':ld' => ($result3['k_tp_lock_days'] + $result2['k_ip_lock']),
                        ':up' => ($result3['k_tp_up'] + $result2['k_ip_up']),
                        ':color' => ($result3['k_tp_color'] + $result2['k_ip_color']),
                        ':vip' => ($result3['k_tp_vip'] + $result2['k_ip_vip']),
                        ':id' => $result2['k_ip_id']));
                } else {
                    $query4 = $mysql2->prepare('INSERT INTO k_immovables_packets
                            (k_ip_immo_id,k_ip_packet,k_ip_lock,k_ip_up,k_ip_color,k_ip_vip)
                            VALUES (:immo,:packet,:lock,:up,:color,:vip)');
                    $query4->execute(array(':immo' => $_POST['AddPackageToImmoID'],
                        ':packet' => $_POST['AddPackageToImmoPackage'],
                        ':lock' => $result3['k_tp_lock_days'],
                        ':up' => $result3['k_tp_up'],
                        ':color' => $result3['k_tp_color'],
                        ':vip' => $result3['k_tp_vip']));
                }
                if ($up->num[$_POST['AddPackageToImmoPackage'] - 1] == 1) {
                    $query4 = $mysql2->prepare('DELETE FROM k_users_packages WHERE k_up_id=:id');
                    $query4->execute(array(":id" => $up->id[$_POST['AddPackageToImmoPackage'] - 1]));
                } else {
                    $query4 = $mysql2->prepare('UPDATE k_users_packages SET k_up_num=:num WHERE k_up_id=:id');
                    $query4->execute(array(":id" => $up->id[$_POST['AddPackageToImmoPackage'] - 1],
                        ":num" => ($up->num[$_POST['AddPackageToImmoPackage'] - 1] - 1)));
                }
                echo 'yes';
            } else {
                echo 'remain';
            }
        } else {
            echo 'wrong';
        }
    } else {
        echo 'packet';
    }
}

?>
