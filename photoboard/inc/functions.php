<?php

defined('CHERNOV') or die('Restricted access');

//Меняем категории
if (isset($_GET['PageType'])) {
    $ShowParamID = filter_var($_GET['PageType'], FILTER_VALIDATE_INT);
}
if (isset($_GET['Category'])) {
    $_GET['Category'] = filter_var($_GET['Category'], FILTER_VALIDATE_INT);
    $Category = $_GET['Category'];
    array_push($cat_arr, 'k_pd_category');
    array_push($vars_cat, ':cat');
    array_push($cond_arr, '');
    array_push($values_arr, $Category);
    $link .= '&Category=' . $_GET['Category'];
}
if (isset($_GET['PhotoNum'])) {
    $_GET['PhotoNum'] = filter_var($_GET['PhotoNum'], FILTER_VALIDATE_INT);
    $Ad_num = $_GET['PhotoNum'];
}
if (isset($_GET['UserId'])) {
    $_GET['UserId'] = filter_var($_GET['UserId'], FILTER_VALIDATE_INT);
    array_push($cat_arr, 'k_pd_user_id');
    array_push($vars_cat, ':user_id');
    array_push($cond_arr, '');
    array_push($values_arr, $_GET['UserId']);
    $link .= '&UserId=' . $_GET['UserId'];
    $where_nav = ' WHERE k_pd_user_id=' . $_GET['UserId'] . ' ';
}
if (isset($_GET['Today'])) {
    $where = ' WHERE k_pd_reg_date > "' . date('Y-m-d 00:00:00', time()) . '"';
    $link .= '&Today=1';
}
if (isset($_GET['Yesterday'])) {
    $where = ' WHERE k_pd_reg_date > "' . date('Y-m-d 00:00:00', time() - 24 * 60 * 60) . '" AND k_pd_reg_date < "' . date('Y-m-d 00:00:00', time()) . '" ';
    $link .= '&Yesterday=1';
}
if (isset($_GET['LimitOnPage'])) {
    $limit = filter_var($_GET['LimitOnPage'], FILTER_VALIDATE_INT);
}
if (isset($_GET['PageIndex'])) {
    $page = filter_var($_GET['PageIndex'], FILTER_VALIDATE_INT);
}

if (isset($_POST['submit_x']) && isset($_POST['submit_y']) && isset($_POST['PhotoNum'])) {
    $Ad_num = filter_var($_POST['PhotoNum'], FILTER_VALIDATE_INT);
    $ShowParamID = 2;
    if (!empty($_POST['PhotoNum']) && !empty($_POST['PhotoComment'])) {
        $data = base64_decode($_SESSION['captcha_image_code']);
        $captcha_image = imagecreatefromstring($data);
        $x = $_POST['submit_x'];
        $y = $_POST['submit_y'];

        $rgb = imagecolorat($captcha_image, $x, $y);
        $color_tran = imagecolorsforindex($captcha_image, $rgb);

        $captcha_ok = ($color_tran['red'] == 255 && $color_tran['green'] == 0 && $color_tran['blue'] == 0 && $color_tran['alpha'] == 0);


        if ($captcha_ok) {
            if (!isset($_SESSION['id'])) {
                $id = 0;
            } else {
                $id = $_SESSION['id'];
            }
            $_POST['PhotoComment'] = filter_var($_POST['PhotoComment'], FILTER_SANITIZE_STRIPPED);
            $_POST['PhotoNum'] = filter_var($_POST['PhotoNum'], FILTER_VALIDATE_INT);
            try {
                $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
                $mysql->exec('set names utf8');
                $queue = $mysql->prepare('INSERT INTO k_photodesk_comments (k_pc_text,k_pc_photodesk_id,k_pc_date,k_pc_user_id) VALUES (:text,:p_id,NOW(),:u_id)');
                $queue->execute(array(':text' => $_POST['PhotoComment'], ':p_id' => $_POST['PhotoNum'], ':u_id' => $id));
            } catch (PDOException $e) {
                exit('');
            }
            $mysql = NULL;
        } else {
            $results = "Убедитесь, что вы нажали в розовый кружочек!";
        }
    } else {
        $results = "Пожалуйста, заполните все обязательные поля!";
    }
}

if (isset($_POST['submit_x']) && isset($_POST['submit_y']) && isset($_POST['PhotoNumPrivate'])) {
    $Ad_num = filter_var($_POST['PhotoNumPrivate'], FILTER_VALIDATE_INT);
    $ShowParamID = 2;
    if (!empty($_POST['PhotoNumPrivate']) && !empty($_POST['PrivateMessage'])) {
        $data = base64_decode($_SESSION['captcha_image_code']);
        $captcha_image = imagecreatefromstring($data);
        $x = $_POST['submit_x'];
        $y = $_POST['submit_y'];

        $rgb = imagecolorat($captcha_image, $x, $y);
        $color_tran = imagecolorsforindex($captcha_image, $rgb);

        $captcha_ok = ($color_tran['red'] == 255 && $color_tran['green'] == 0 && $color_tran['blue'] == 0 && $color_tran['alpha'] == 0);


        if ($captcha_ok) {
            if (!isset($_SESSION['id'])) {
                $id = 0;
            } else {
                $id = $_SESSION['id'];
            }
            $_POST['PrivateMessage'] = filter_var($_POST['PrivateMessage'], FILTER_SANITIZE_STRIPPED);
            $_POST['PhotoNumPrivate'] = filter_var($_POST['PhotoNumPrivate'], FILTER_VALIDATE_INT);
            try {
                $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
                $mysql->exec('set names utf8');
                $queue0 = $mysql->prepare('SELECT k_pd_user_id FROM k_photodesk WHERE k_pd_id=:id LIMIT 1');
                $queue0->execute(array(':id' => $_POST['PhotoNumPrivate']));
                $result0 = $queue0->fetch(PDO::FETCH_ASSOC);
                $queue = $mysql->prepare('INSERT INTO k_user_messages
                    (k_um_text,k_um_user_id,k_um_sender_id,k_um_date)
                    VALUES (:text,:uid,:sid,NOW())');
                $queue->execute(array(':text' => $_POST['PrivateMessage'], ':uid' => $result0['k_pd_user_id'], ':sid' => $id));
            } catch (PDOException $e) {
                exit('');
            }
            $mysql = NULL;
        } else {
            $resultsP = "Убедитесь, что вы нажали в розовый кружочек!";
        }
    } else {
        $resultsP = "Пожалуйста, заполните все обязательные поля!";
    }
}
?>
