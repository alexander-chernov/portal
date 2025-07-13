<?php

define('TOMSKLINE', 1);
session_start();

require_once '../inc/configs.php';
require_once 'inc/classes.php';
require_once '../admin/inc/functions.php';

//Нажатие на картинку
if (isset($_POST['submit_x']) && isset($_POST['submit_y'])) {
    if (!empty($_POST['ImmoType']) && !empty($_POST['dealtype']) && !empty($_POST['address_choise']) && !empty($_POST['price']) && $_POST['price'] != 0 && !empty($_POST['contact_name']) && !empty($_POST['contacts'])) {
        //Если поля заполнены
        $data = base64_decode($_SESSION['captcha_image_code']);
        $captcha_image = imagecreatefromstring($data);
        $x = $_POST['submit_x'];
        $y = $_POST['submit_y'];

        //Проверяем цвет пикселя, на который было произведено нажатие
        $rgb = imagecolorat($captcha_image, $x, $y);
        $color_tran = imagecolorsforindex($captcha_image, $rgb);

        //Проверка, если цвет исключительно красный
        $captcha_ok = ($color_tran['red'] == 255 && $color_tran['green'] == 0 && $color_tran['blue'] == 0 && $color_tran['alpha'] == 0);


        //Проверка пройдена
        if ($captcha_ok) {
            $ad_create = new AdCreate();
            $ad_create->AdsCreate($_POST['dealtype'], $_POST['ImmoType'], $_SESSION['owner'], $_POST['rooms'], $_POST['immovable_type'], $_POST['address_choise'], $_POST['contacts'], $_POST['contact_name'], $_POST['floor'], $_POST['floor_all'], $_POST['area_all'], $_POST['area_live'], $_POST['area_land'], $_POST['area_kitchen'], $_POST['price'], $_POST['newsec'], $_POST['eqs'], $_POST['material'], $_POST['description'], $_POST['Params'], $_POST['san'], $_POST['balcony'], $_POST['Adv'], $_POST['utils'], $_SESSION['id'], $_POST['images']);
            $ad_create->SaveAd($_POST['Action'], $_POST['priority']);
            header($_ENV['SERVER_PROTOCOL'] . " 200 OK", true, 200);
            if ($_SESSION['privileges'] == 4) {
                header("Location: http://" . _SERVER_ADDRESS . "/profile/prof_agent.php?PageType=5");
            } else {
                header("Location: http://" . _SERVER_ADDRESS . "/profile/index.php?PageType=4");
            }
        } else {
            $comment = "Убедитесь, что вы нажали в розовый кружочек!";
            $ShowParamID = filter_var($_POST['PageType'], FILTER_SANITIZE_STRIPPED);
            $ID = filter_var($_POST['Action'], FILTER_VALIDATE_INT);
            $link_ar_url = array();
            foreach ($_POST as $key => $value) {
                if ($key == 'submit_x' || $key == 'submit_y') {
                    
                } else {
                    if ($key == 'Adv') {
                        $key = 'Adv[]';
                        for ($i = 0; $i < count($value); $i++) {
                            $link_ar_url[] = $key . '=' . urlencode($value[$i]);
                        }
                    } else {
                        $link_ar_url[] = $key . '=' . urlencode($value);
                    }
                    if ($key == 'images') {
                        $key = 'images[]';
                        for ($i = 0; $i < count($value); $i++) {
                            $link_ar_url[] = $key . '=' . urlencode($value[$i]);
                        }
                    } else {
                        $link_ar_url[] = $key . '=' . urlencode($value);
                    }
                }
            }
            $link = '&' . join('&', $link_ar_url);
            header($_ENV['SERVER_PROTOCOL'] . " 201 Missed", true, 200);
            if ($_SESSION['privileges'] == 4) {
                header("Location: http://" . _SERVER_ADDRESS . "/profile/prof_agent.php?PageType=" . $ShowParamID . "&id=" . $ID . "&comment=" . $comment . $link);
            } else {
                header("Location: http://" . _SERVER_ADDRESS . "/profile/index.php?PageType=" . $ShowParamID . "&id=" . $ID . "&comment=" . $comment . $link);
            }
        }
    } else {
        $comment = "Пожалуйста, заполните все обязательные поля!";
        $ShowParamID = filter_var($_POST['PageType'], FILTER_SANITIZE_STRIPPED);
        $ID = filter_var($_POST['Action'], FILTER_VALIDATE_INT);
        header($_ENV['SERVER_PROTOCOL'] . " 202 Empty form", true, 200);
        if ($_SESSION['privileges'] == 4) {
            header("Location: http://" . _SERVER_ADDRESS . "/profile/prof_agent.php?PageType=" . $ShowParamID . "&id=" . $ID . "&comment=" . $comment . $link);
        } else {
            header("Location: http://" . _SERVER_ADDRESS . "/profile/index.php?PageType=" . $ShowParamID . "&id=" . $ID . "&comment=" . $comment . $link);
        }
    }
}

if (isset($_POST['SavePhotoAds'])) {
    $_POST['PhotoAdTheme'] = filter_var($_POST['PhotoAdTheme'], FILTER_SANITIZE_STRIPPED);
    $_POST['PhotoAdText'] = filter_var($_POST['PhotoAdText'], FILTER_SANITIZE_STRIPPED);
    $_POST['PhotoAdPhone'] = filter_var($_POST['PhotoAdPhone'], FILTER_SANITIZE_STRIPPED);
    $_POST['PhotoAdCategory'] = filter_var($_POST['PhotoAdCategory'], FILTER_VALIDATE_INT);
    $_POST['PhotoAdPrice'] = filter_var($_POST['PhotoAdPrice'], FILTER_VALIDATE_INT);
    $_POST['PhotoAdId'] = filter_var($_POST['PhotoAdId'], FILTER_VALIDATE_INT);
    $_POST['priority'] = str_replace('../admin/images/photo/1_', 'images/photo/', filter_var($_POST['priority'], FILTER_SANITIZE_STRIPPED));
    ;
    $images = array();
    foreach ($_POST['images'] as $value) {
        if (preg_match('/(jpg|png|jpeg|gif)$/', $value)) {
            $images[] = str_replace('../admin/images/photo/1_', 'images/photo/', filter_var($value, FILTER_SANITIZE_STRIPPED));
        } else {
            header($_ENV['SERVER_PROTOCOL'] . " 202 ERROR", true, 200);
            header("Location: http://" . _SERVER_ADDRESS . "/profile/index.php?PageType=13");
        }
    }
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        if (!$_POST['PhotoAdId'] || $_POST['PhotoAdId'] == 0) {
            $query = $mysql->prepare('INSERT INTO k_photodesk
                (k_pd_theme,k_pd_category,k_pd_user_id,k_pd_text,k_pd_phone,k_pd_price,k_pd_reg_date,k_pd_last_visit,k_pd_end_date)
                VALUES (:theme,:category,:user,:text,:phone,:price,NOW(),NOW(),(NOW() + INTERVAL 10 DAY))');
            $query->execute(array(':theme' => $_POST['PhotoAdTheme'],
                ':category' => $_POST['PhotoAdCategory'],
                ":user" => $_SESSION['id'],
                ":text" => $_POST['PhotoAdText'],
                ":phone" => $_POST['PhotoAdPhone'],
                ":price" => $_POST['PhotoAdPrice']));
            $id = $mysql->lastInsertId();
        } else {
            $id = $_POST['PhotoAdId'];
            $query = $mysql->prepare('UPDATE k_photodesk
                SET k_pd_theme=:theme,k_pd_category=:category,k_pd_user_id=:user,
                k_pd_text=:text,k_pd_phone=:phone,k_pd_price=:price
                WHERE k_pd_id=:id');
            $query->execute(array(':theme' => $_POST['PhotoAdTheme'],
                ':category' => $_POST['PhotoAdCategory'],
                ":user" => $_SESSION['id'],
                ":text" => $_POST['PhotoAdText'],
                ":phone" => $_POST['PhotoAdPhone'],
                ":price" => $_POST['PhotoAdPrice'],
                ":id" => $id));
        }
        $query2 = $mysql->prepare('SELECT * FROM k_photodesk_photos WHERE k_pdp_link=:url');
        $query3 = $mysql->prepare('INSERT INTO k_photodesk_photos (k_pdp_link,k_pdp_ad_id,k_pdp_priority) VALUES (:url,:ad_id,0)');
        for ($i = 0; $i < count($images); $i++) {
            $query2->execute(array(":url" => $images[$i]));
            if ($query2->rowCount() == 0) {
                $query3->execute(array(":url" => $images[$i], ":ad_id" => $id));
            }
            if ($_POST['priority'] == $images[$i]) {
                $query4 = $mysql->prepare('UPDATE k_photodesk_photos SET k_pdp_priority=0 WHERE k_pdp_ad_id=:id');
                $query4->execute(array(":id" => $id));
                $query5 = $mysql->prepare('UPDATE k_photodesk_photos SET k_pdp_priority=1 WHERE k_pdp_link=:url');
                $query5->execute(array(":url" => $_POST['priority']));
            }
        }
        header($_ENV['SERVER_PROTOCOL'] . " 202 ERROR", true, 200);
        header("Location: http://" . _SERVER_ADDRESS . "/profile/index.php?PageType=5");
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['SaveJob'])) {
    $_POST['JobType'] = filter_var($_POST['JobType'], FILTER_VALIDATE_INT);
    $_POST['JobID'] = filter_var($_POST['JobID'], FILTER_VALIDATE_INT);
    $_POST['JobPost'] = filter_var($_POST['JobPost'], FILTER_SANITIZE_STRIPPED);
    $_POST['JobSalaryMin'] = filter_var($_POST['JobSalaryMin'], FILTER_VALIDATE_INT);
    $_POST['JobSalaryMax'] = filter_var($_POST['JobSalaryMax'], FILTER_VALIDATE_INT);
    $_POST['JobAgeMin'] = filter_var($_POST['JobAgeMin'], FILTER_VALIDATE_INT);
    $_POST['JobAgeMax'] = filter_var($_POST['JobAgeMax'], FILTER_VALIDATE_INT);
    $_POST['JobCurrency'] = filter_var($_POST['JobCurrency'], FILTER_VALIDATE_INT);
    $_POST['JobSex'] = filter_var($_POST['JobSex'], FILTER_VALIDATE_INT);
    $_POST['JobEducation'] = filter_var($_POST['JobEducation'], FILTER_SANITIZE_STRIPPED);
    $_POST['JobExperience'] = filter_var($_POST['JobExperience'], FILTER_SANITIZE_STRIPPED);
    $_POST['JobSchedule'] = filter_var($_POST['JobSchedule'], FILTER_SANITIZE_STRIPPED);
    $_POST['JobText'] = filter_var($_POST['JobText'], FILTER_SANITIZE_STRIPPED);
    $_POST['JobOrganization'] = filter_var($_POST['JobOrganization'], FILTER_SANITIZE_STRIPPED);
    $_POST['JobContactName'] = filter_var($_POST['JobContactName'], FILTER_SANITIZE_STRIPPED);
    $_POST['JobMarital'] = filter_var($_POST['JobMarital'], FILTER_SANITIZE_STRIPPED);
    $_POST['JobContactPhone'] = filter_var($_POST['JobContactPhone'], FILTER_SANITIZE_STRIPPED);
    $_POST['JobEmail'] = filter_var($_POST['JobEmail'], FILTER_SANITIZE_EMAIL);
    $avatar = $_POST['JobAvatar'];
    $_POST['JobAvatar'] = str_replace('../admin/images/job/1_', 'images/job/', filter_var($_POST['JobAvatar'], FILTER_SANITIZE_STRIPPED));
    if ($_POST['JobType'] == 1 || $_POST['JobType'] == 2) {
        $ShowParamID = 16;
        $type = $_POST['JobType'];
    } else {
        $ShowParamID = $_POST['JobType'];
        if ($_POST['JobType'] == 15) {
            $type = 1;
        }
        if ($_POST['JobType'] == 17) {
            $type = 2;
        }
    }
    if (empty($_POST['JobPost']) || empty($_POST['JobContactPhone']) || empty($_POST['JobEmail'])) {
        $link_ar_url = array();
        foreach ($_POST as $key => $value) {
            if ($key == 'JobAvatar') {
                $link_ar_url[] = $key . '=' . urlencode($avatar);
            } else {
                $link_ar_url[] = $key . '=' . urlencode($value);
            }
        }
        $link = '&' . join('&', $link_ar_url);
        $comment = urlencode('Заполните все обязательные поля!');
        header($_ENV['SERVER_PROTOCOL'] . " 201 Missed", true, 200);
        header("Location: http://" . _SERVER_ADDRESS . "/profile/index.php?PageType=" . $ShowParamID . "&ID=" . $_POST['JobID'] . "&comment=" . $comment . $link);
        exit();
    }
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        if ($_POST['JobType'] == 15) {
            $query = $mysql->prepare('INSERT INTO k_job
                (k_j_user_id,k_j_type,k_j_salary_min,k_j_salary_max,k_j_currency,k_j_sex,k_j_schedule,k_j_text,k_j_date_reg,k_j_date_end,k_j_up_date,
                k_j_post,k_j_age_min,k_j_age_max,k_j_education,k_j_exp,k_j_contact_name,k_j_contact_phone,k_j_email)
                VALUES (:user,:type,:s_min,:s_max,:currency,:sex,:schedule,:text,NOW(),DATE_ADD(CURDATE(),INTERVAL 2 MONTH),NOW(),
                :post,:a_min,:a_max,:education,:exp,:c_name,:c_phone,:email)');
            $query->execute(array(":user" => $_SESSION['id'],
                ":type" => $type,
                ":s_min" => $_POST['JobSalaryMin'],
                ":s_max" => $_POST['JobSalaryMax'],
                ":currency" => $_POST['JobCurrency'],
                ":sex" => $_POST['JobSex'],
                ":schedule" => $_POST['JobSchedule'],
                ":text" => $_POST['JobText'],
                ":post" => $_POST['JobPost'],
                ":a_min" => $_POST['JobAgeMin'],
                ":a_max" => $_POST['JobAgeMax'],
                ":education" => $_POST['JobEducation'],
                ":exp" => $_POST['JobExperience'],
                ":c_name" => $_POST['JobContactName'],
                ":c_phone" => $_POST['JobContactPhone'],
                ":email" => $_POST['JobEmail']));
            $query2 = $mysql->prepare('INSERT INTO k_job_organizations
                (k_jo_job_id,k_jo_name) VALUES (:id,:name)');
            $query2->execute(array(":id" => $mysql->lastInsertId(), ":name" => $_POST['JobOrganization']));
        }
        if ($_POST['JobType'] == 17) {
            $query = $mysql->prepare('INSERT INTO k_job
                (k_j_user_id,k_j_type,k_j_salary_min,k_j_salary_max,k_j_currency,k_j_sex,k_j_schedule,k_j_text,k_j_date_reg,k_j_date_end,k_j_up_date,
                k_j_post,k_j_age_min,k_j_education,k_j_exp,k_j_contact_name,k_j_contact_phone,k_j_email)
                VALUES (:user,:type,:s_min,:s_max,:currency,:sex,:schedule,:text,NOW(),DATE_ADD(CURDATE(),INTERVAL 2 MONTH),NOW(),
                :post,:a_min,:education,:exp,:c_name,:c_phone,:email)');
            $query->execute(array(":user" => $_SESSION['id'],
                ":type" => $type,
                ":s_min" => $_POST['JobSalaryMin'],
                ":s_max" => $_POST['JobSalaryMax'],
                ":currency" => $_POST['JobCurrency'],
                ":sex" => $_POST['JobSex'],
                ":schedule" => $_POST['JobSchedule'],
                ":text" => $_POST['JobText'],
                ":post" => $_POST['JobPost'],
                ":a_min" => $_POST['JobAgeMin'],
                ":education" => $_POST['JobEducation'],
                ":exp" => $_POST['JobExperience'],
                ":c_name" => $_POST['JobContactName'],
                ":c_phone" => $_POST['JobContactPhone'],
                ":email" => $_POST['JobEmail']));
            $query2 = $mysql->prepare('INSERT INTO k_job_person
                (k_jp_job_id,k_jp_avatar,k_jp_marital) VALUES (:id,:avatar,:marital)');
            $query2->execute(array(":id" => $mysql->lastInsertId(), ":avatar" => $_POST['JobAvatar'], ":marital" => $_POST['JobMarital']));
        }
        if ($_POST['JobType'] == 1) {
            $query = $mysql->prepare('UPDATE k_job
                SET k_j_salary_min=:s_min,k_j_salary_max=:s_max,
                k_j_currency=:currency,k_j_sex=:sex,
                k_j_schedule=:schedule,k_j_text=:text,
                k_j_post=:post,k_j_age_min=:a_min,
                k_j_age_max=:a_max,k_j_education=:education,
                k_j_exp=:exp,k_j_contact_name=:c_name,
                k_j_contact_phone=:c_phone,k_j_email=:email
                WHERE k_j_user_id=:user AND k_j_type=:type AND k_j_id=:id');
            $query->execute(array(":user" => $_SESSION['id'],
                ":type" => $type,
                ":s_min" => $_POST['JobSalaryMin'],
                ":s_max" => $_POST['JobSalaryMax'],
                ":currency" => $_POST['JobCurrency'],
                ":sex" => $_POST['JobSex'],
                ":schedule" => $_POST['JobSchedule'],
                ":text" => $_POST['JobText'],
                ":post" => $_POST['JobPost'],
                ":a_min" => $_POST['JobAgeMin'],
                ":a_max" => $_POST['JobAgeMax'],
                ":education" => $_POST['JobEducation'],
                ":exp" => $_POST['JobExperience'],
                ":c_name" => $_POST['JobContactName'],
                ":c_phone" => $_POST['JobContactPhone'],
                ":email" => $_POST['JobEmail'],
                ":id" => $_POST['JobID']));
            $query2 = $mysql->prepare('UPDATE k_job_organizations SET k_jo_name=:name WHERE k_jo_job_id=:id');
            $query2->execute(array(":id" => $_POST['JobID'], ":name" => $_POST['JobOrganization']));
        }
        if ($_POST['JobType'] == 2) {
            $query = $mysql->prepare('UPDATE k_job
                SET k_j_salary_min=:s_min,k_j_salary_max=:s_max,
                k_j_currency=:currency,k_j_sex=:sex,
                k_j_schedule=:schedule,k_j_text=:text,
                k_j_post=:post,k_j_age_min=:a_min,
                k_j_education=:education,k_j_exp=:exp,
                k_j_contact_name=:c_name,k_j_contact_phone=:c_phone,
                k_j_email=:email
                WHERE k_j_user_id=:user AND k_j_type=:type AND k_j_id=:id');
            $query->execute(array(":user" => $_SESSION['id'],
                ":type" => $type,
                ":s_min" => $_POST['JobSalaryMin'],
                ":s_max" => $_POST['JobSalaryMax'],
                ":currency" => $_POST['JobCurrency'],
                ":sex" => $_POST['JobSex'],
                ":schedule" => $_POST['JobSchedule'],
                ":text" => $_POST['JobText'],
                ":post" => $_POST['JobPost'],
                ":a_min" => $_POST['JobAgeMin'],
                ":education" => $_POST['JobEducation'],
                ":exp" => $_POST['JobExperience'],
                ":c_name" => $_POST['JobContactName'],
                ":c_phone" => $_POST['JobContactPhone'],
                ":email" => $_POST['JobEmail'],
                ":id" => $_POST['JobID']));
            $query2 = $mysql->prepare('UPDATE k_job_person
                SET k_jp_avatar=:avatar, k_jp_marital=:marital WHERE k_jp_job_id=:id');
            $query2->execute(array(":id" => $_POST['JobID'], ":avatar" => $_POST['JobAvatar'], ":marital" => $_POST['JobMarital']));
        }
        header($_ENV['SERVER_PROTOCOL'] . " 202 ERROR", true, 200);
        header("Location: http://" . _SERVER_ADDRESS . "/profile/index.php?PageType=6");
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['SaveAgent'])) {
    $_POST['AgentSName'] = filter_var($_POST['AgentSName'], FILTER_SANITIZE_STRIPPED);
    $_POST['AgentFName'] = filter_var($_POST['AgentFName'], FILTER_SANITIZE_STRIPPED);
    $_POST['AgentLName'] = filter_var($_POST['AgentLName'], FILTER_SANITIZE_STRIPPED);
    $_POST['AgentEmail'] = filter_var($_POST['AgentEmail'], FILTER_SANITIZE_EMAIL);
    $_POST['AgentAddress'] = filter_var($_POST['AgentAddress'], FILTER_VALIDATE_INT);
    $_POST['AgentName'] = filter_var($_POST['AgentName'], FILTER_SANITIZE_STRIPPED);
    $_POST['AgentPhone'] = filter_var($_POST['AgentPhone'], FILTER_SANITIZE_STRIPPED);
    $_POST['AgentSite'] = CorrectURL($_POST['AgentSite']);
    $_POST['AgentDescription'] = filter_var($_POST['AgentDescription'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('UPDATE k_users
                SET k_ku_fname=:fname, k_ku_lname=:lname, k_ku_oname=:oname,
                k_ku_email=:email WHERE k_ku_id=:user');
        $query->execute(array(':fname' => $_POST['AgentFName'],
            ':lname' => $_POST['AgentSName'],
            ":user" => $_SESSION['id'],
            ":oname" => $_POST['AgentLName'],
            ":email" => $_POST['AgentEmail']));
        $query2 = $mysql->prepare('UPDATE k_users_agents
                SET k_ua_name=:name, k_ua_phone=:phone, k_ua_address=:address,
                k_ua_site=:site, k_ua_description=:descr WHERE k_ua_user_parent=:user');
        $query2->execute(array(':name' => $_POST['AgentName'],
            ':phone' => $_POST['AgentPhone'],
            ":address" => $_POST['AgentAddress'],
            ":site" => $_POST['AgentSite'],
            ":descr" => $_POST['AgentDescription'],
            ":user" => $_SESSION['id']));
        header($_ENV['SERVER_PROTOCOL'] . " 202 ERROR", true, 200);
        header("Location: http://" . _SERVER_ADDRESS . "/profile/prof_agent.php");
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['SaveExpert'])) {
    $_POST['ExpertHeader'] = filter_var($_POST['ExpertHeader'], FILTER_SANITIZE_STRIPPED);
    $_POST['ExpertTheme'] = filter_var($_POST['ExpertTheme'], FILTER_SANITIZE_STRIPPED);
    $_POST['ExpertBrief'] = filter_var($_POST['ExpertBrief'], FILTER_SANITIZE_STRIPPED);
    $_POST['ExpertDescription'] = filter_var($_POST['ExpertDescription'], FILTER_SANITIZE_STRIPPED);
    $_POST['ExpertAddress'] = filter_var($_POST['ExpertAddress'], FILTER_VALIDATE_INT);
    $_POST['ExpertPhone'] = filter_var($_POST['ExpertPhone'], FILTER_SANITIZE_STRIPPED);
    $_POST['ExpertSite'] = CorrectURL($_POST['ExpertSite']);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $query = $mysql->prepare('UPDATE k_experts
                SET k_e_header=:header, k_e_theme=:theme, k_e_brief=:brief,
                k_e_description=:descr, k_e_address=:address, k_e_phone=:phone,
                k_e_site=:site
                WHERE k_e_id=:id');
        $query->execute(array(':header' => $_POST['ExpertHeader'],
            ':theme' => $_POST['ExpertTheme'],
            ":brief" => $_POST['ExpertBrief'],
            ":descr" => $_POST['ExpertDescription'],
            ":address" => $_POST['ExpertAddress'],
            ":phone" => $_POST['ExpertPhone'],
            ":site" => $_POST['ExpertSite'],
            ":id" => $_SESSION['id_e']));
        $query2 = $mysql->prepare('DELETE FROM k_experts_categories_links WHERE k_ecl_expert_id=:id');
        $query2->execute(array(":id" => $_SESSION['id_e']));
        $query3 = $mysql->prepare('INSERT INTO k_experts_categories_links
            (k_ecl_expert_id,k_ecl_category_id)
            VALUES (:id,:cat)');
        foreach ($_POST['ExpertCat'] as $value) {
            $query3->execute(array(":id" => $_SESSION['id_e'], ":cat" => filter_var($value, FILTER_VALIDATE_INT)));
        }
        header($_ENV['SERVER_PROTOCOL'] . " 202 ERROR", true, 200);
        header("Location: http://" . _SERVER_ADDRESS . "/profile/prof_expert.php");
    } catch (PDOException $e) {
        exit();
    }
}
?>