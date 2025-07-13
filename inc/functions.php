<?php

require_once 'configs.php';
require_once('class.phpmailer.php');

//----------- Секция пользователей -----------

/**
 * Функция для отправки кода на почту
 * @param String $e E-mail
 * @param String $c Код
 * @param String $u Логин
 */
function mailCode($e1, $c, $u, $p=0) {
    $e1 = filter_var($e1, FILTER_SANITIZE_EMAIL);
    $c = filter_var($c, FILTER_SANITIZE_STRIPPED);
    $u = filter_var($u, FILTER_SANITIZE_STRIPPED);
    $p = filter_var($p, FILTER_VALIDATE_INT);
    mb_internal_encoding("UTF-8");


    if ($p == 1) {
        $message = "Уважаемый пользователь, для завершения регистрации вам необходимо в течение 1 дня оплатить регистрацию, используя ссылку:" . PHP_EOL;
        $message .= "http://" . _SERVER_ADDRESS . "/payment.php?pay&user&registration=" . $u . PHP_EOL;
        //mb_send_mail($e, "Регистрация на портале " . date("Y-m-d"), $message, "From: \"TOMSK-LINE.RU\"" . PHP_EOL);
        //echo $message;
    } else {
        $message = "Уважаемый пользователь, для завершения регистрации вам необходимо в течение 1 дня перейти по ссылке:" . PHP_EOL;
        $message .= "http://" . _SERVER_ADDRESS . "/registration.php?do=activate&username=" . $u . "&code=" . $c . PHP_EOL;
        //mb_send_mail($e, "Регистрация на портале " . date("Y-m-d"), $message, "From: \"TOMSK-LINE.RU\"" . PHP_EOL);
        //echo $message;
    }
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
        $mail->SMTPAuth   = false;                  // enable SMTP authentication
        $mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
        $mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
        $mail->Port       = 465;                   // set the SMTP port for the GMAIL server
        $mail->Username   = "tomsk.line.ru@gmail.com ";  // GMAIL username
        $mail->Password   = "Qwer1@34";            // GMAIL password
*/
        $mail->SetFrom('noreply@'._SERVER_ADDRESS, _SERVER_ADDRESS);
        $mail->AddAddress($e1, '');
        $mail->Subject    = _SERVER_ADDRESS." TOMSK-LINE Portal Registration " . date("Y-m-d");
        $mail->AltBody    = $message;
        $mail->MsgHTML($message);
        $mail->Send();
    } catch (phpmailerException $e) {
        echo $e->errorMessage(); //Pretty error messages from PHPMailer
    } catch (Exception $e) {
        echo $e->getMessage(); //Boring error messages from anything else!
    }

    return $message;
}

/**
 * Функция для восстановления пароля по почте
 * @param String $e E-mail
 * @param String $c Код
 * @param String $u Логин
 */
function mailForget($e1, $c, $u) {
    $e1 = filter_var($e1, FILTER_SANITIZE_EMAIL);
    $c = filter_var($c, FILTER_SANITIZE_STRIPPED);
    $u = filter_var($u, FILTER_SANITIZE_STRIPPED);
    $message = "Для восстановления пароля вам необходимо перейти по ссылке:" . PHP_EOL;
    $message .= "http://" . _SERVER_ADDRESS . "/registration.php?do=recover&username=" . $u . "&code=" . $c . PHP_EOL;
    $message .= "Ссылка будет действительна в течение 1 дня.";

    //mb_send_mail($e, "Восстановление пароля на портале " . date("Y-m-d"), $message, "From: \"UP70.RU\"" . PHP_EOL);
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

        $mail->SMTPAuth   = false;                  // enable SMTP authentication
        $mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
        $mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
        $mail->Port       = 465;                   // set the SMTP port for the GMAIL server
        $mail->Username   = "tomsk.line.ru@gmail.com ";  // GMAIL username
        $mail->Password   = "Qwer1@34";            // GMAIL password
        */

        $mail->SetFrom('noreply@'._SERVER_ADDRESS, _SERVER_ADDRESS);
        $mail->AddAddress($e1, '');
        $mail->Subject    = _SERVER_ADDRESS." Password reminder " . date("Y-m-d");
        $mail->AltBody    = $message;
        $mail->MsgHTML($message);
        $mail->Send();
    } catch (phpmailerException $e) {
        echo $e->errorMessage(); //Pretty error messages from PHPMailer
    } catch (Exception $e) {
        echo $e->getMessage(); //Boring error messages from anything else!
    }

}

/**
 * Забыл пароль
 * @param String $login Логин
 * @return boolean Ссылка отправлена / не отправлена
 */
function userForgetPass($email) {
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT *
            FROM k_users
            WHERE k_ku_banned_forever=0 AND k_ku_verified=1 AND k_ku_email=:email
            LIMIT 1');
        $query->execute(array(":email" => $email));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if ($query->rowCount() == 1) {
            $code = md5(time());
            $query2 = $mysql->prepare('DELETE FROM k_users_forget WHERE k_uf_date>:date');
            $query2->execute(array(":date" => date('Y-m-d H:i:s', time() + 24 * 60 * 60)));
            $query3 = $mysql->prepare('INSERT INTO k_users_forget (k_uf_user_id,k_uf_code,k_uf_date) VALUES (:id,:code,NOW())');
            $query3->execute(array(":id" => $result['k_ku_id'], ":code" => $code));
            mailForget($email, $code, $result['k_ku_login']);
        }
    } catch (PDOException $e) {
        exit();
    }
}

function WrongLanguage($mode, $text) {
    $mode = filter_var($mode, FILTER_VALIDATE_INT);
    $text = filter_var($text, FILTER_SANITIZE_STRIPPED);
    $LangEn = array("&", "q", "w", "e", "r", "t", "y", "u", "i", "o", "p", "[", "]",
        "a", "s", "d", "f", "g", "h", "j", "k", "l", ";", "'",
        "z", "x", "c", "v", "b", "n", "m", ",", ".", "/",
        "Q", "W", "E", "R", "T", "Y", "U", "I", "O", "P", "[", "]",
        "A", "S", "D", "F", "G", "H", "J", "K", "L", ";", "'",
        "Z", "X", "C", "V", "B", "N", "M", ",", "/");
    $LangRu = array("?", "й", "ц", "у", "к", "е", "н", "г", "ш", "щ", "з", "х", "ъ",
        "ф", "ы", "в", "а", "п", "р", "о", "л", "д", "ж", "э",
        "я", "ч", "с", "м", "и", "т", "ь", "б", "ю", ".",
        "Й", "Ц", "У", "К", "Е", "Н", "Г", "Ш", "Щ", "З", "Х", "Ъ",
        "Ф", "Ы", "В", "А", "П", "Р", "О", "Л", "Д", "Ж", "Э",
        "Я", "Ч", "С", "М", "И", "Т", "Ь", "Б", "Ю", ".");
    switch ($mode) {
        case 1: return str_replace($LangEn, $LangRu, $text);
            break;
        case 2: return str_replace($LangRu, $LangEn, $text);
            break;
    }
}

function RussianRules($word) {
    $reg = "/(ый|ой|ая|ия|ий|ое|ые|ому|а|о|у|е|ого|ему|и|ство|ых|ох|я|ют|ат|ок)$/i";
    $word = trim(preg_replace($reg, '', $word));
    return $word;
}

function UpdateActivityUser() {
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('UPDATE k_users SET k_ku_last_date=NOW(),k_u_online=1,k_ku_session_id=:s_id WHERE k_ku_id=:id');
        $query->execute(array(":id" => $_SESSION['id'], ":s_id" => session_id()));
        $query_2 = $mysql->prepare('UPDATE k_experts SET k_e_last_date=NOW(),k_e_online=1 WHERE k_e_id=:id');
        $query_2->execute(array(":id" => $_SESSION['id_e']));
        $query1 = $mysql->prepare('UPDATE k_users SET k_u_online=0,k_ku_session_id="" WHERE k_ku_last_date<:date');
        $query1->execute(array(":date" => date('Y-m-d G:i:s', (time() - 15 * 60))));
        $query1_2 = $mysql->prepare('UPDATE k_experts SET k_e_online=0 WHERE k_e_last_date<:date');
        $query1_2->execute(array(":date" => date('Y-m-d G:i:s', (time() - 15 * 60))));
    } catch (PDOException $e) {
        exit();
    }
}

function ImmovablePreload() {
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $mysql->exec('DELETE FROM k_immovables_locked WHERE k_il_date_stop<NOW()');
    } catch (PDOException $e) {
        exit();
    }
}

function NewMessages() {
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT count(*) AS max FROM k_user_messages WHERE k_um_read=0 AND k_um_user_id=:id');
        $query->execute(array(":id" => $_SESSION['id']));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result['max'];
    } catch (PDOException $e) {
        exit();
    }
}

function YourIPBanned() {
    $ip = getenv("HTTP_X_FORWARDED_FOR");
    if (empty($ip) || $ip == 'unknown') {
        $ip = getenv("REMOTE_ADDR");
    }
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT * FROM k_users_ban_ip_list WHERE k_ubil_ip=:ip');
        $query->execute(array(":ip" => $ip));
        if ($query->rowCount() == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    } catch (PDOException $e) {
        exit();
    }
}

function ColorsOnPage() {
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('SELECT * FROM k_colours ORDER BY k_c_id ASC');
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        exit();
    }
    $color = $result[2]['k_c_value'];
    $color1 = $result[0]['k_c_value'];
    $color2 = $result[1]['k_c_value'];
    $color_back = $result[3]['k_c_value'];
    $color_words = $result[4]['k_c_value'];
    $color_back_2 = $result[5]['k_c_value'];
    $color_ads = $result[6]['k_c_value'];
    $color_headers = $result[7]['k_c_value'];
    $color_lights = $result[8]['k_c_value'];
    echo "<style>
        .shapka_bloka a, .copyright, .futter_menu a, .style_shapka_4, .style_shapka_3,
        .style_shapka_1 {color: $color;}
        .bl_color_1 {background: $color;}
        .add_rubrika, .style_shapka_2 {border-right: 1px solid $color; color: $color;}
        .vopros_exp, .menu_rab_active, .gl_but_men, .search_panel, .shapka_bloka, .inf_text_1, .futter, .add_site,
        .down_1, .style_filter_but, .button_agent, .search_photo_1, .but_j_ser, .add_expert_new, .but_exp, .bl_color {
        background: $color2;color: $color;
        background-image: -webkit-linear-gradient(top, $color1, $color2);
        background-image: -moz-linear-gradient(top, $color1, $color2);
        background-image: -ms-linear-gradient(top, $color1, $color2);
        background-image: -o-linear-gradient(top, $color1, $color2);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='$color1', endColorstr='$color2');}
        .gl_menu, .bl_color_2 {background: $color_back;}
        .vip_nedvig, .block_gray, .vip_photo, .vip_expert, .vip_rabota,
        .vip_karta, .vip_rabota, .block_gray_rab_1, .block_gray_rab_2, .bl_color_4 {background: $color_back_2;}
         .obveden_block, .obveden_block_agent, .obverden_bl, .obverden_bl_1,
         .obverden_bl_right, .block_menu_artikle, .block_menu_sites {border: 5px solid $color_back; border-bottom: 15px solid $color_back;}
        .gl_menu_a, .gl_menu_active {color: $color_words;}
        .bl_color_3 {background: $color_words;}
        .bl_color_5, .visible_filter_all, .obiavlenie, .block_expert_1,
        .artikle_content_1, .obiavlenie_job, .obiavlenie_job_1 {background: $color_ads;}
        .bl_color_6 {background: $color_headers;}
        .bl_color_7, .obiavlenie_color, .free_img_color {background: $color_lights;}
        .shapka_bloka_spec, .style_shapka_1_spec, .style_shapka_1_spec a, .green_text, .active_listing,
        .style_listing:hover, .vip_text, .free_text, .title_exp, .name_rubrik_artikle, .name_sites,
        .rubrika_catalog_text, .name_rubrik, .name_push {color: $color_headers;}
        </style>";
}
class PageContent {

    public $text = '';

    public function PageContent($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT k_cp_text FROM k_content_pages WHERE k_cp_id=:id LIMIT 1');
            $query->execute(array(":id" => $id));
            $result = $query->fetch(PDO::FETCH_ASSOC);
            $result['k_cp_text'] = str_replace('../', '../admin/', strip_tags($result['k_cp_text'],'<p><br><ol><ul><option>'));
            $this->text = $result['k_cp_text'];
        } catch (PDOException $e) {
            exit();
        }
    }

}
function cbr($valuta_code, $date1, $date2){
    /*
    $file = $_SERVER['DOCUMENT_ROOT'].'/currency.db';
    $fp = fopen($file,'w+');
    $contents = fread($fp, filesize($filename));
    fclose($handle);
    */
    $xml = array();
    if ($valuta_code
        && $date1
        && $date2){
        //$url = 'http://www.cbr.ru/scripts/XML_dynamic.asp?date_req1='.$date1.'&date_req2='.$date2.'&VAL_NM_RQ='.$valuta_code;
        $xml = simplexml_load_file($url);
        $i = 0;
        foreach ($xml as $key => $value) {
            if ($i == 0) {
                $money_yestoday = floatval(str_replace(',','.',$value->Value));
            } else {
                $money_today = floatval(str_replace(',','.',$value->Value));
            }
            $i++;
        }
        $res = array('yestoday'=>$money_yestoday,'today'=>$money_today);
    } else {
        $res = false;
    }
    //var_dump ($res);
    return $res;
}

