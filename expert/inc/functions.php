<?php

defined('TOMSKLINE') or die('Restricted access');

if (isset($_GET['PageIndex'])) {
    $page = filter_var($_GET['PageIndex'], FILTER_VALIDATE_INT);
}

if (isset($_GET['Category'])) {
    $_GET['Category'] = filter_var($_GET['Category'], FILTER_VALIDATE_INT);
    $where = ' AND k_ecl_category_id=' . $_GET['Category'] . ' ';
    $link .= '&Category='.$_GET['Category'];
}

if (isset($_GET['AdId'])) {
    $AdId = filter_var($_GET['AdId'], FILTER_VALIDATE_INT);
    $where = ' AND k_e_id=' . $AdId . ' ';
}

if (isset($_GET['Question'])) {
    $Question = 1;
}

if (isset($_GET['Online'])) {
    $where = ' AND k_e_online=1 ';
    $link .= '&Online=1';
}

if (isset($_GET['Limit'])) {
    $limit = filter_var($_GET['Limit'], FILTER_VALIDATE_INT);
}

if (isset($_POST['submit_x']) && isset($_POST['submit_y'])) {
    $AdId = filter_var($_POST['number'], FILTER_VALIDATE_INT);
    $where = ' AND k_e_id=' . $AdId . ' ';
    $ShowParamID = 2;
    $Question = 1;
    if (!empty($_POST['your_name']) && !empty($_POST['your_question'])) {
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
            $_POST['your_name'] = filter_var($_POST['your_name'], FILTER_SANITIZE_STRIPPED);
            $_POST['your_question'] = filter_var($_POST['your_question'], FILTER_SANITIZE_STRIPPED);
            $_POST['your_email'] = filter_var($_POST['your_email'], FILTER_SANITIZE_EMAIL);
            try {
                $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
                $mysql->exec('set names utf8');
                $queue = $mysql->prepare('INSERT INTO k_experts_questions (k_eq_expert_id,k_eq_name,k_eq_email,k_eq_text,k_eq_datetime) VALUES (:e_id,:name,:email,:text,NOW())');
                $queue->execute(array(':e_id' => $AdId, ':name' => $_POST['your_name'], ':email' => $_POST['your_email'], ":text" => $_POST['your_question']));
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
?>
