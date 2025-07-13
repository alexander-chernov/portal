<?php

defined('CHERNOV') or die('Restricted access');

class User {

    public $id;
    public $login;
    public $fname;
    public $lname;
    public $oname;
    public $email;
    public $immo_remain;
    public $immo_count;
    public $immo_monthly;
    public $photo_count;
    public $job_count;
    public $organizations_count;
    public $max_adv = 5000;

    /**
     * Загрузка пользователя
     * @param int $id ID пользователя берётся из сессии
     */
    function LoadUser($id) {
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            exit();
        }
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT * FROM k_users WHERE k_ku_id=:id LIMIT 1');
            $query->execute(array(':id' => $id));
            $row = $query->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['k_ku_id'];
            $this->login = $row['k_ku_login'];
            $this->fname = $row['k_ku_fname'];
            $this->lname = $row['k_ku_lname'];
            $this->oname = $row['k_ku_oname'];
            $this->email = $row['k_ku_email'];
            $this->immo_remain = $row['k_u_immo_remain'];
            $query1 = $mysql->prepare('SELECT count(k_isf_id) AS immo FROM k_immovables_sell WHERE k_isf_user_id=:id LIMIT 1');
            $query1->execute(array(':id' => $id));
            $row1 = $query1->fetch(PDO::FETCH_ASSOC);
            $this->immo_count = $row1['immo'];
            $query2 = $mysql->prepare('SELECT count(k_pd_id) AS photo FROM k_photodesk WHERE k_pd_user_id=:id LIMIT 1');
            $query2->execute(array(':id' => $id));
            $row2 = $query2->fetch(PDO::FETCH_ASSOC);
            $this->photo_count = $row2['photo'];
            $query3 = $mysql->prepare('SELECT count(k_j_id) AS job FROM k_job WHERE k_j_user_id=:id LIMIT 1');
            $query3->execute(array(':id' => $id));
            $row3 = $query3->fetch(PDO::FETCH_ASSOC);
            $this->job_count = $row3['job'];
            $query4 = $mysql->prepare('SELECT count(k_isf_id) AS immo FROM k_immovables_sell WHERE k_isf_user_id=:id AND k_isf_registration_date BETWEEN :date1 AND :date2');
            $query4->execute(array(':id' => $id, ':date1' => date('Y-m-1'), ':date2' => date('Y-m-t')));
            $row4 = $query4->fetch(PDO::FETCH_ASSOC);

            if ($row4['immo'] <= $this->max_adv) {
                $this->immo_monthly = $this->max_adv - $row4['immo'];
            } else {
                $this->immo_monthly = 0;
            }
            $query5 = $mysql->prepare('SELECT count(k_cf_id) AS org FROM k_catalog_firms_m WHERE c_cf_user=:user LIMIT 1');
            $query5->execute(array(':user' => $_SESSION['login']));
            $row5 = $query5->fetch(PDO::FETCH_ASSOC);
            $this->organizations_count = $row5['org'];
            $mysql = NULL;
        } catch (PDOException $e) {
            exit();
        }
    }

    /**
     * Сохранить пользователя
     * @return bool Прекращает выполнение в случае неудачи
     */
    function SaveUser() {
        $this->fname = filter_var($this->fname, FILTER_SANITIZE_STRIPPED);
        $this->lname = filter_var($this->lname, FILTER_SANITIZE_STRIPPED);
        $this->oname = filter_var($this->oname, FILTER_SANITIZE_STRIPPED);
        $this->email = filter_var($this->email, FILTER_SANITIZE_STRIPPED);
        $this->id = filter_var($this->id, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('UPDATE k_users SET k_ku_fname=:fname, k_ku_lname=:lname, k_ku_oname=:oname, k_ku_email=:email WHERE k_ku_id=:id');
            $query->execute(array(':fname' => $this->fname, ':lname' => $this->lname, ':oname' => $this->oname, ':email' => $this->email, ':id' => $this->id));
        } catch (PDOException $e) {
            exit();
        }
    }

}

class AdCreate {

    public $k_isf_deal_type;
    public $k_isf_subcategory;
    public $k_isf_owner;
    public $k_isf_rooms;
    public $k_isf_immovable_type;
    public $k_isf_address;
    public $k_isf_contacts;
    public $k_isf_contact_name;
    public $k_isf_floor;
    public $k_isf_floor_all;
    public $k_isf_area_all;
    public $k_isf_area_live;
    public $k_isf_area_land;
    public $k_isf_area_kitchen;
    public $k_isf_price;
    public $k_isf_new;
    public $k_isf_eq;
    public $k_isf_material;
    public $k_isf_description;
    public $Params = array();
    public $k_isf_san;
    public $k_isf_balcony;
    public $Adv = array();
    public $k_isf_utilities;
    public $k_isf_user_id;
    public $images = array();

    /**
     * Создаём объявление
     * @param int $dt Продаю/сдаю
     * @param int $sub Подкатегория (квартира, земля, гараж...)
     * @param int $owner Тип пользователя. Автоматически берётся из сессии.
     * @param int $rooms Число комнат.
     * @param int $immo_t
     * @param int $addr
     * @param String $cont
     * @param String $cont_name
     * @param int $floor
     * @param int $floor_all
     * @param int $area_all
     * @param int $area_live
     * @param int $area_land
     * @param int $area_kitchen
     * @param int $price
     * @param int $new
     * @param int $eq
     * @param int $material
     * @param String $descr
     * @param array $params
     * @param int $san
     * @param int $balcony
     * @param array $adv
     * @param int $util
     * @param int $user_id
     */
    public function AdsCreate($dt, $sub, $owner, $rooms, $immo_t, $addr, $cont, $cont_name, $floor, $floor_all, $area_all, $area_live, $area_land, $area_kitchen, $price, $new, $eq, $material, $descr, $params, $san, $balcony, $adv, $util, $user_id, $images) {
        $this->k_isf_deal_type = filter_var($dt, FILTER_VALIDATE_INT);
        $this->k_isf_subcategory = filter_var($sub, FILTER_VALIDATE_INT);
        $this->k_isf_owner = filter_var($owner, FILTER_VALIDATE_INT);
        $this->k_isf_rooms = filter_var($rooms, FILTER_VALIDATE_INT);
        $this->k_isf_immovable_type = filter_var($immo_t, FILTER_VALIDATE_INT);
        $this->k_isf_address = filter_var($addr, FILTER_VALIDATE_INT);
        $this->k_isf_contacts = filter_var($cont, FILTER_SANITIZE_STRIPPED);
        $this->k_isf_contact_name = filter_var($cont_name, FILTER_SANITIZE_STRIPPED);
        $this->k_isf_floor = filter_var($floor, FILTER_VALIDATE_INT);
        $this->k_isf_floor_all = filter_var($floor_all, FILTER_VALIDATE_INT);
        $this->k_isf_area_all = filter_var($area_all, FILTER_VALIDATE_INT);
        $this->k_isf_area_live = filter_var($area_live, FILTER_VALIDATE_INT);
        $this->k_isf_area_land = filter_var($area_land, FILTER_VALIDATE_INT);
        $this->k_isf_area_kitchen = filter_var($area_kitchen, FILTER_VALIDATE_INT);
        $this->k_isf_price = filter_var($price, FILTER_VALIDATE_INT);
        $this->k_isf_new = filter_var($new, FILTER_VALIDATE_INT);
        $this->k_isf_eq = filter_var($eq, FILTER_VALIDATE_INT);
        $this->k_isf_material = filter_var($material, FILTER_VALIDATE_INT);
        $this->k_isf_description = filter_var($descr, FILTER_SANITIZE_STRIPPED);
        $this->Params = $params;
        $this->k_isf_san = filter_var($san, FILTER_VALIDATE_INT);
        $this->k_isf_balcony = filter_var($balcony, FILTER_VALIDATE_INT);
        $this->Adv = $adv;
        $this->k_isf_utilities = filter_var($util, FILTER_VALIDATE_INT);
        $this->k_isf_user_id = filter_var($user_id, FILTER_VALIDATE_INT);
        $this->images = $images;
    }

    /**
     * Проверяет checkbox на "checked"
     * @param String $str Парамерт, который должен пройти проверку
     * @param Array $arr Массив, который содержит все "чекнутые" элементы
     * @return int 1 - чекнут, 0 - не чекнут
     */
    function CheckOrNot($str, $arr) {
        if (in_array($str, $arr)) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Сохранить объявление
     * @return boolean Удалось или нет
     */
    public function SaveAd($action, $priority) {
        $action = filter_var($action, FILTER_VALIDATE_INT);
        $priority = str_replace('../video/', 'video/', str_replace('../admin/images/1_', 'images/', filter_var($priority, FILTER_SANITIZE_STRIPPED)));
        try {
            $user = new User();
            $user->LoadUser($_SESSION['id']);
            if ($_SESSION['owner'] == 2 || $_SESSION['owner'] == 3) {
                $user_packets = new UserPackets($_SESSION['id']);
                $user->immo_monthly = $user_packets->current_remain;
            }
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            if ($user->immo_monthly > 0 || $user->immo_remain > 0) {
                if ($action == 0) {
                    $query = $mysql->prepare('INSERT INTO k_immovables_sell (k_isf_deal_type,k_isf_subcategory,k_isf_owner,k_isf_rooms,k_isf_immovable_type,
                k_isf_address,k_isf_registration_date,k_isf_end_date,k_isf_contacts,k_isf_contact_name,k_isf_floor,k_isf_floor_all,
                k_isf_area_all,k_isf_area_live,k_isf_area_land,k_isf_area_kitchen,k_isf_price,k_isf_new,k_isf_eq,k_isf_material,
                k_isf_description,k_isf_quickly,k_isf_exchange,k_isf_merch,k_isf_privat,k_isf_owned,k_isf_credit,k_isf_documents,
                k_isf_san,k_isf_balcony,k_isf_phone_stat,k_isf_security,k_isf_internet,k_isf_balcony_gl,k_isf_furniture,k_isf_fridge,
                k_isf_washing,k_isf_microwave,k_isf_tv,k_isf_ctv,k_isf_stove,k_isf_plastic_windows,k_isf_utilities,k_isf_user_id,k_isf_up_date)
                VALUES (:k_isf_deal_type, :k_isf_subcategory, :k_isf_owner, :k_isf_rooms, :k_isf_immovable_type, :k_isf_address,
                NOW(),NOW() + INTERVAL 1 MONTH, :k_isf_contacts, :k_isf_contact_name, :k_isf_floor, :k_isf_floor_all, :k_isf_area_all,
                :k_isf_area_live, :k_isf_area_land, :k_isf_area_kitchen, :k_isf_price, :k_isf_new, :k_isf_eq, :k_isf_material,
                :k_isf_description, :quickly, :exchange, :merch, :privat, :owned, :credit, :documents, :k_isf_san, :k_isf_balcony,
                :phone_stat, :security, :internet, :balcony_gl, :furniture, :fridge, :washing, :microwave, :tv, :ctv, :stove,
                :plastic_windows, :k_isf_utilities, :k_isf_user_id, NOW())');
                    $query->execute(array(
                        ':k_isf_deal_type' => $this->k_isf_deal_type,
                        ':k_isf_subcategory' => $this->k_isf_subcategory,
                        ':k_isf_owner' => $this->k_isf_owner,
                        ':k_isf_rooms' => $this->k_isf_rooms,
                        ':k_isf_immovable_type' => $this->k_isf_immovable_type,
                        ':k_isf_address' => $this->k_isf_address,
                        ':k_isf_contacts' => $this->k_isf_contacts,
                        ':k_isf_contact_name' => $this->k_isf_contact_name,
                        ':k_isf_floor' => $this->k_isf_floor,
                        ':k_isf_floor_all' => $this->k_isf_floor_all,
                        ':k_isf_area_all' => $this->k_isf_area_all,
                        ':k_isf_area_live' => $this->k_isf_area_live,
                        ':k_isf_area_land' => $this->k_isf_area_land,
                        ':k_isf_area_kitchen' => $this->k_isf_area_kitchen,
                        ':k_isf_price' => $this->k_isf_price,
                        ':k_isf_new' => $this->k_isf_new,
                        ':k_isf_eq' => $this->k_isf_eq,
                        ':k_isf_material' => $this->k_isf_material,
                        ':k_isf_description' => $this->k_isf_description,
                        ':quickly' => $this->CheckOrNot('quickly', $this->Params),
                        ':exchange' => $this->CheckOrNot('exchange', $this->Params),
                        ':merch' => $this->CheckOrNot('merch', $this->Params),
                        ':privat' => $this->CheckOrNot('privat', $this->Params),
                        ':owned' => $this->CheckOrNot('owned', $this->Params),
                        ':credit' => $this->CheckOrNot('credit', $this->Params),
                        ':documents' => $this->CheckOrNot('documents', $this->Params),
                        ':k_isf_san' => $this->k_isf_san,
                        ':k_isf_balcony' => $this->k_isf_balcony,
                        ':phone_stat' => $this->CheckOrNot('phone_stat', $this->Adv),
                        ':security' => $this->CheckOrNot('security', $this->Adv),
                        ':internet' => $this->CheckOrNot('internet', $this->Adv),
                        ':balcony_gl' => $this->CheckOrNot('balcony_gl', $this->Adv),
                        ':furniture' => $this->CheckOrNot('furniture', $this->Adv),
                        ':fridge' => $this->CheckOrNot('fridge', $this->Adv),
                        ':washing' => $this->CheckOrNot('washing', $this->Adv),
                        ':microwave' => $this->CheckOrNot('microwave', $this->Adv),
                        ':tv' => $this->CheckOrNot('tv', $this->Adv),
                        ':ctv' => $this->CheckOrNot('ctv', $this->Adv),
                        ':stove' => $this->CheckOrNot('stove', $this->Adv),
                        ':plastic_windows' => $this->CheckOrNot('plastic_windows', $this->Adv),
                        ':k_isf_utilities' => $this->k_isf_utilities,
                        ':k_isf_user_id' => $this->k_isf_user_id));
                    $last = $mysql->lastInsertId();
                    $query2 = $mysql->prepare('INSERT INTO k_immovables_photos (k_ip_url,k_ip_immo_id) VALUES (:url,:id)');
                    for ($i = 0; $i < count($this->images); $i++) {
                        $query2->execute(array(":url" => str_replace('../video/', 'video/', str_replace('../admin/images/1_', 'images/', $this->images[$i])), ":id" => $last));
                    }
                    if ($user->immo_monthly == 0) {
                        $query2 = $mysql->prepare('UPDATE k_users SET k_u_immo_remain=:remain WHERE k_ku_id=:id');
                        $query2->execute(array(":remain" => $user->immo_remain - 1, ":id" => $_SESSION['id']));
                    }
                    if ($_SESSION['owner'] == 2 || $_SESSION['owner'] == 3) {
                        $query3 = $mysql->prepare('UPDATE k_users_packets SET k_up_remain=:remain WHERE k_up_id=:id AND k_up_user=:user');
                        $query3->execute(array(":remain" => $user_packets->current_remain - 1, ":id" => $user_packets->id[0], ":user" => $_SESSION['id']));
                    }
                }
            }
            if ($action > 0) {
                $query = $mysql->prepare('UPDATE k_immovables_sell SET k_isf_deal_type=:k_isf_deal_type,
                    k_isf_subcategory=:k_isf_subcategory,
                    k_isf_owner=:k_isf_owner,
                    k_isf_rooms=:k_isf_rooms,
                    k_isf_immovable_type=:k_isf_immovable_type,
                    k_isf_address=:k_isf_address,
                    k_isf_contacts=:k_isf_contacts,
                    k_isf_contact_name=:k_isf_contact_name,
                    k_isf_floor=:k_isf_floor,
                    k_isf_floor_all=:k_isf_floor_all,
                    k_isf_area_all=:k_isf_area_all,
                    k_isf_area_live=:k_isf_area_live,
                    k_isf_area_land=:k_isf_area_land,
                    k_isf_area_kitchen=:k_isf_area_kitchen,
                    k_isf_price=:k_isf_price,
                    k_isf_new=:k_isf_new,
                    k_isf_eq=:k_isf_eq,
                    k_isf_material=:k_isf_material,
                    k_isf_description=:k_isf_description,
                    k_isf_quickly=:quickly,
                    k_isf_exchange=:exchange,
                    k_isf_merch=:merch,
                    k_isf_privat=:privat,
                    k_isf_owned=:owned,
                    k_isf_credit=:credit,
                    k_isf_documents=:documents,
                    k_isf_san=:k_isf_san,
                    k_isf_balcony=:k_isf_balcony,
                    k_isf_phone_stat=:phone_stat,
                    k_isf_security=:security,
                    k_isf_internet=:internet,
                    k_isf_balcony_gl=:balcony_gl,
                    k_isf_furniture=:furniture,
                    k_isf_fridge=:fridge,
                    k_isf_washing=:washing,
                    k_isf_microwave=:microwave,
                    k_isf_tv=:tv,
                    k_isf_ctv=:ctv,
                    k_isf_stove=:stove,
                    k_isf_plastic_windows=:plastic_windows,
                    k_isf_utilities=:k_isf_utilities,
                    k_isf_user_id=:k_isf_user_id,
                    k_isf_state=0
                    WHERE k_isf_id=:im_id');
                $query->execute(array(
                    ':k_isf_deal_type' => $this->k_isf_deal_type,
                    ':k_isf_subcategory' => $this->k_isf_subcategory,
                    ':k_isf_owner' => $this->k_isf_owner,
                    ':k_isf_rooms' => $this->k_isf_rooms,
                    ':k_isf_immovable_type' => $this->k_isf_immovable_type,
                    ':k_isf_address' => $this->k_isf_address,
                    ':k_isf_contacts' => $this->k_isf_contacts,
                    ':k_isf_contact_name' => $this->k_isf_contact_name,
                    ':k_isf_floor' => $this->k_isf_floor,
                    ':k_isf_floor_all' => $this->k_isf_floor_all,
                    ':k_isf_area_all' => $this->k_isf_area_all,
                    ':k_isf_area_live' => $this->k_isf_area_live,
                    ':k_isf_area_land' => $this->k_isf_area_land,
                    ':k_isf_area_kitchen' => $this->k_isf_area_kitchen,
                    ':k_isf_price' => $this->k_isf_price,
                    ':k_isf_new' => $this->k_isf_new,
                    ':k_isf_eq' => $this->k_isf_eq,
                    ':k_isf_material' => $this->k_isf_material,
                    ':k_isf_description' => $this->k_isf_description,
                    ':quickly' => $this->CheckOrNot('quickly', $this->Params),
                    ':exchange' => $this->CheckOrNot('exchange', $this->Params),
                    ':merch' => $this->CheckOrNot('merch', $this->Params),
                    ':privat' => $this->CheckOrNot('privat', $this->Params),
                    ':owned' => $this->CheckOrNot('owned', $this->Params),
                    ':credit' => $this->CheckOrNot('credit', $this->Params),
                    ':documents' => $this->CheckOrNot('documents', $this->Params),
                    ':k_isf_san' => $this->k_isf_san,
                    ':k_isf_balcony' => $this->k_isf_balcony,
                    ':phone_stat' => $this->CheckOrNot('phone_stat', $this->Adv),
                    ':security' => $this->CheckOrNot('security', $this->Adv),
                    ':internet' => $this->CheckOrNot('internet', $this->Adv),
                    ':balcony_gl' => $this->CheckOrNot('balcony_gl', $this->Adv),
                    ':furniture' => $this->CheckOrNot('furniture', $this->Adv),
                    ':fridge' => $this->CheckOrNot('fridge', $this->Adv),
                    ':washing' => $this->CheckOrNot('washing', $this->Adv),
                    ':microwave' => $this->CheckOrNot('microwave', $this->Adv),
                    ':tv' => $this->CheckOrNot('tv', $this->Adv),
                    ':ctv' => $this->CheckOrNot('ctv', $this->Adv),
                    ':stove' => $this->CheckOrNot('stove', $this->Adv),
                    ':plastic_windows' => $this->CheckOrNot('plastic_windows', $this->Adv),
                    ':k_isf_utilities' => $this->k_isf_utilities,
                    ':k_isf_user_id' => $this->k_isf_user_id,
                    ':im_id' => $action));
            }
            $query2 = $mysql->prepare('INSERT INTO k_immovables_photos (k_ip_url,k_ip_immo_id) VALUES (:url,:id)');
            $query3 = $mysql->prepare('SELECT * FROM k_immovables_photos WHERE k_ip_url=:url');
            for ($i = 0; $i < count($this->images); $i++) {
                $query3->execute(array(":url" => str_replace('../video/', 'video/', str_replace('../admin/images/1_', 'images/', $this->images[$i]))));
                if ($query3->rowCount() == 0) {
                    $query2->execute(array(":url" => str_replace('../video/', 'video/', str_replace('../admin/images/1_', 'images/', $this->images[$i])), ":id" => $action));
                }
                if ($priority == str_replace('../video/', 'video/', str_replace('../admin/images/1_', 'images/', $this->images[$i]))) {
                    $query4 = $mysql->prepare('UPDATE k_immovables_photos SET k_ip_priority=0 WHERE k_ip_immo_id=:id');
                    $query4->execute(array(":id" => $action));
                    $query5 = $mysql->prepare('UPDATE k_immovables_photos SET k_ip_priority=1 WHERE k_ip_url=:url');
                    $query5->execute(array(":url" => $priority));
                }
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class AdSelf {

    public $photo_url = array();
    public $description = array();
    public $date_reg = array();
    public $date_end = array();
    public $id = array();
    public $state = array();
    public $color = array();
    public $lock_start = array();
    public $lock_end = array();
    public $special = array();

    public function AdSelf($user_id, $page) {
        $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
        $page = filter_var($page, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $mysql->exec('DELETE FROM k_immovables_locked WHERE k_il_date_stop<NOW()');
            $query = $mysql->prepare('SELECT ise.k_isf_description, ise.k_isf_registration_date, ise.k_isf_end_date, kiph.k_ip_url,
                ise.k_isf_id, ise.k_isf_state, ise.k_isf_color_light, kil.*
                FROM k_immovables_sell as ise
                LEFT JOIN (SELECT * FROM k_immovables_photos ORDER BY k_ip_priority DESC) as kiph ON (kiph.k_ip_immo_id = ise.k_isf_id)
                LEFT JOIN k_immovables_locked as kil ON (kil.k_il_ad_id = ise.k_isf_id)
                WHERE k_isf_user_id=:user
                GROUP BY k_isf_id
                ORDER BY k_isf_registration_date DESC
                LIMIT ' . (($page - 1) * 10) . ',' . ($page * 10));
            $query->execute(array(':user' => $user_id));
            $row = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($row as $value) {
                if (mb_strlen($value['k_isf_description'], 'UTF8') > 100) {
                    $value['k_isf_description'] = mb_substr($value['k_isf_description'], 0, 100, 'utf-8') . '...';
                }
                array_push($this->description, $value['k_isf_description']);
                array_push($this->date_reg, $value['k_isf_registration_date']);
                array_push($this->date_end, $value['k_isf_end_date']);
                array_push($this->photo_url, $value['k_ip_url']);
                array_push($this->id, $value['k_isf_id']);
                array_push($this->state, $value['k_isf_state']);
                array_push($this->color, $value['k_isf_color_light']);
                array_push($this->lock_start, $value['k_il_date_start']);
                array_push($this->lock_end, $value['k_il_date_stop']);
            }
            $query2 = $mysql->prepare('SELECT k_is_immovable_id FROM k_immovables_special ORDER BY k_is_id DESC');
            $query2->execute();
            $row2 = $query2->fetchAll(PDO::FETCH_ASSOC);
            foreach ($row2 as $value) {
                array_push($this->special, $value['k_is_immovable_id']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

    function GenerateNavigation($page, $where, $link) {
        $page = filter_var($page, FILTER_VALIDATE_INT);
        $where = filter_var($where, FILTER_SANITIZE_STRIPPED);
        $link = filter_var($link, FILTER_SANITIZE_STRIPPED);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue1 = $mysql->prepare('SELECT count(*) AS max FROM k_immovables_sell WHERE ' . $where . ' k_isf_user_id=:id');
            $queue1->execute(array(":id" => $_SESSION['id']));
            $row = $queue1->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        $pages = intval($row['max'] / 10);
        if ($row['max'] % 10 != 0) {
            $pages++;
        }
        if ($pages > 1) {
            echo '<div class="listing">';
            if ($page == $pages || $page > 1) {
                echo '<a class="style_listing" href="./?PageType=4&PageIndex=' . ($page - 1) . $link . '">Предыдущая</a>';
            }

            if ($pages <= 11) {
                for ($i = 1; $i <= $pages; $i++) {
                    if ($i == $page) {
                        echo '<a class="active_listing" href="./?PageType=4&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    } else {
                        echo '<a class="style_listing" href="./?PageType=4&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
            } else {
                if ($page <= 6) {
                    for ($i = 1; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageType=4&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageType=4&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageType=4&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
                if ($page > 6 && $page <= ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageType=4&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $page - 2; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageType=4&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageType=4&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageType=4&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
                if ($page > ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageType=4&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 5; $i <= $pages; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageType=4&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageType=4&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                }
            }

            if ($page == 1 || $page < $pages) {
                echo '<a class="style_listing" href="./?PageType=4&PageIndex=' . ($page + 1) . $link . '">Следующая</a>';
            }

            echo '</div>';
        }
    }

}

class AdPhotos {

    public $photo_url = array();
    public $theme = array();
    public $description = array();
    public $category = array();
    public $category_name = array();
    public $date_reg = array();
    public $date_end = array();
    public $id = array();
    public $paid = array();
    public $vip = array();
    public $phone = array();
    public $price = array();
    public $all_photos = array();

    public function AdPhotos($user_id, $where) {
        $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT *
                FROM k_photodesk as kp
                LEFT JOIN (SELECT * FROM k_photodesk_photos ORDER BY k_pdp_priority DESC) AS kpp ON (kpp.k_pdp_ad_id = kp.k_pd_id)
                LEFT JOIN k_photodesk_categories AS kpc ON (kpc.k_pdc_id = kp.k_pd_category)
                WHERE k_pd_user_id=:user ' . $where . '
                GROUP BY k_pd_id
                ORDER BY k_pd_reg_date DESC');
            $query->execute(array(':user' => $user_id));
            $row = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($row as $value) {
                array_push($this->photo_url, $value['k_pdp_link']);
                array_push($this->theme, $value['k_pd_theme']);
                array_push($this->description, $value['k_pd_text']);
                array_push($this->date_reg, $value['k_pd_reg_date']);
                array_push($this->date_end, $value['k_pd_end_date']);
                array_push($this->id, $value['k_pd_id']);
                array_push($this->paid, $value['k_pd_paid']);
                array_push($this->vip, $value['k_pd_vip']);
                array_push($this->category, $value['k_pd_category']);
                array_push($this->category_name, $value['k_pdc_name']);
                array_push($this->phone, $value['k_pd_phone']);
                array_push($this->price, $value['k_pd_price']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

    public function LoadPhotos($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT *
                FROM k_photodesk_photos
                WHERE k_pdp_ad_id=:id
                ORDER BY k_pdp_priority DESC');
            $query->execute(array(':id' => $id));
            $row = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($row as $value) {
                array_push($this->all_photos, array($value['k_pdp_id'], $value['k_pdp_link'], $value['k_pdp_priority']));
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class PhotoCategories {

    public $id = array();
    public $name = array();

    public function PhotoCategories() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT * FROM k_photodesk_categories ORDER BY k_pdc_name ASC');
            $query->execute();
            $row = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($row as $value) {
                array_push($this->id, $value['k_pdc_id']);
                array_push($this->name, $value['k_pdc_name']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class AdJob {

    public $id = array();
    public $type = array();
    public $salary_min = array();
    public $salary_max = array();
    public $currency = array();
    public $currency_name = array();
    public $date_reg = array();
    public $date_end = array();
    public $sex = array();
    public $schedule = array();
    public $text = array();
    public $post = array();
    public $age_min = array();
    public $age_max = array();
    public $education = array();
    public $education_t = array();
    public $education_t_str = array();
    public $experience = array();
    public $state = array();
    public $contact_name = array();
    public $contact_phone = array();
    public $email = array();
    public $organization = array();
    public $avatar = array();
    public $marital = array();

    public function AdJob($user_id, $where) {
        $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT *
                FROM k_job as kj
                LEFT JOIN k_job_organizations AS kjo ON (kjo.k_jo_job_id = kj.k_j_id)
                LEFT JOIN k_job_person AS kjp ON (kjp.k_jp_job_id = kj.k_j_id)
                LEFT JOIN k_job_currency AS kjc ON (kjc.k_jc_id = kj.k_j_currency)
                LEFT JOIN k_job_education AS kje ON (kje.k_je_id = kj.k_j_education_type)
                WHERE k_j_user_id=:user AND k_j_date_end>NOW() ' . $where . '
                GROUP BY k_j_id
                ORDER BY k_j_date_reg DESC');
            $query->execute(array(':user' => $user_id));
            $row = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($row as $value) {
                array_push($this->id, $value['k_j_id']);
                array_push($this->type, $value['k_j_type']);
                array_push($this->salary_min, $value['k_j_salary_min']);
                array_push($this->salary_max, $value['k_j_salary_max']);
                array_push($this->currency, $value['k_j_currency']);
                array_push($this->currency_name, $value['k_jc_name']);
                array_push($this->date_reg, $value['k_j_date_reg']);
                array_push($this->date_end, $value['k_j_date_end']);
                array_push($this->sex, $value['k_j_sex']);
                array_push($this->schedule, $value['k_j_schedule']);
                array_push($this->text, $value['k_j_text']);
                array_push($this->post, $value['k_j_post']);
                array_push($this->age_min, $value['k_j_age_min']);
                array_push($this->age_max, $value['k_j_age_max']);
                array_push($this->education, $value['k_j_education']);
                array_push($this->education_t, $value['k_j_education_type']);
                array_push($this->education_t_str, $value['k_je_name']);
                array_push($this->experience, $value['k_j_exp']);
                array_push($this->state, $value['k_j_state']);
                array_push($this->contact_name, $value['k_j_contact_name']);
                array_push($this->contact_phone, $value['k_j_contact_phone']);
                array_push($this->email, $value['k_j_email']);
                if ($value['k_jo_name'] != 'null') {
                    array_push($this->organization, $value['k_jo_name']);
                } else {
                    array_push($this->organization, '');
                }
                if ($value['k_jp_avatar'] != 'null') {
                    array_push($this->avatar, $value['k_jp_avatar']);
                } else {
                    array_push($this->avatar, '');
                }
                if ($value['k_jp_marital'] != 'null') {
                    array_push($this->marital, $value['k_jp_marital']);
                } else {
                    array_push($this->marital, '');
                }
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class Agency {

    public $id = 0;
    public $name = '';
    public $avatar = '';
    public $state = 0;
    public $phone = '';
    public $address = 0;
    public $address_string = 0;
    public $site = '';
    public $description = '';
    public $user = '';
    public $fname = '';
    public $sname = '';
    public $lname = '';
    public $email = '';

    public function Agency($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT *
                FROM k_users_agents AS kua
                LEFT JOIN k_users AS ku ON (ku.k_ku_id = kua.k_ua_user_parent)
                LEFT JOIN k_streets_house_nums AS kshn ON (kshn.k_shn_id = kua.k_ua_address)
                LEFT JOIN k_streets AS ks ON (ks.k_s_id = kshn.k_shn_street_id)
                WHERE k_ua_user_parent=:user AND k_ua_state=1 LIMIT 1');
            $query->execute(array(':user' => $id));
            $row = $query->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['k_ua_id'];
            $this->name = $row['k_ua_name'];
            $this->avatar = $row['k_ua_avatar'];
            $this->state = $row['k_ua_state'];
            $this->phone = $row['k_ua_phone'];
            $this->address = $row['k_ua_address'];
            $this->site = $row['k_ua_site'];
            $this->description = $row['k_ua_description'];
            $this->user = $row['k_ku_login'];
            $this->fname = $row['k_ku_fname'];
            $this->sname = $row['k_ku_lname'];
            $this->lname = $row['k_ku_oname'];
            $this->email = $row['k_ku_email'];
            if (preg_match('/(###)/', $row['k_s_name'])) {
                $street = explode('###', $row['k_s_name']);
                $house = explode('###', $row['k_shn_house_num']);
                $this->address_string = $street[0] . ' ' . $house[0] . ' / ' . $street[1] . ' ' . $house[1];
            } else {
                $this->address_string = $row['k_s_name'] . ' ' . $row['k_shn_house_num'];
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class Expert {

    public $id = 0;
    public $brief = '';
    public $address = 0;
    public $address_string = '';
    public $phone = '';
    public $site = '';
    public $email = '';
    public $theme = '';
    public $header = '';
    public $description = '';
    public $avatar = '';
    public $end_date = '';
    public $categories = array();

    public function Expert($login) {
        $login = filter_var($login, FILTER_SANITIZE_EMAIL);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT *
                FROM k_experts AS ke
                LEFT JOIN k_streets_house_nums AS kshn ON (kshn.k_shn_id = ke.k_e_address)
                LEFT JOIN k_streets AS ks ON (ks.k_s_id = kshn.k_shn_street_id)
                WHERE k_e_email=:user AND k_e_verified=1 LIMIT 1');
            $query->execute(array(':user' => $login));
            $row = $query->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['k_e_id'];
            $this->brief = $row['k_e_brief'];
            $this->phone = $row['k_e_phone'];
            $this->address = $row['k_e_address'];
            $this->site = $row['k_e_site'];
            $this->theme = $row['k_e_theme'];
            $this->header = $row['k_e_header'];
            $this->description = $row['k_e_description'];
            $this->avatar = $row['k_e_image'];
            $this->end_date = $row['k_e_end_date'];
            $this->email = $row['k_e_email'];
            if (preg_match('/(###)/', $row['k_s_name'])) {
                $street = explode('###', $row['k_s_name']);
                $house = explode('###', $row['k_shn_house_num']);
                $this->address_string = $street[0] . ' ' . $house[0] . ' / ' . $street[1] . ' ' . $house[1];
            } else {
                $this->address_string = $row['k_s_name'] . ' ' . $row['k_shn_house_num'];
            }
            $query2 = $mysql->prepare('SELECT k_ecl_category_id
                FROM k_experts_categories_links WHERE k_ecl_expert_id=:id
                ORDER BY k_ecl_category_id ASC');
            $query2->execute(array(":id" => $this->id));
            $row2 = $query2->fetchAll(PDO::FETCH_ASSOC);
            foreach ($row2 as $value) {
                array_push($this->categories, $value['k_ecl_category_id']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class ExpertQuestions {

    public $id = array();
    public $name = array();
    public $email = array();
    public $text_q = array();
    public $text_a = array();
    public $date = array();
    public $need_answer = 0;
    public $have_answer = 0;

    public function ExpertQuestions($id, $page) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        $page = filter_var($page, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT *
                FROM k_experts_questions AS keq
                LEFT JOIN k_experts_answers AS kea ON (keq.k_eq_id = kea.k_ea_question_id)
                WHERE k_eq_expert_id=:id
                ORDER BY k_eq_datetime DESC');
            //LIMIT ' . (($page - 1) * 50) . ', 50');
            $query->execute(array(":id" => $id));
            $row = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($row as $value) {
                $this->id[] = $value['k_eq_id'];
                $this->name[] = $value['k_eq_name'];
                $this->email[] = $value['k_eq_email'];
                $this->text_q[] = $value['k_eq_text'];
                $this->text_a[] = $value['k_ea_text'];
                $this->date[] = $value['k_eq_datetime'];
            }
            $query2 = $mysql->prepare('SELECT count(*) AS num
                FROM k_experts_questions AS keq
                LEFT JOIN k_experts_answers AS kea ON (keq.k_eq_id = kea.k_ea_question_id)
                WHERE k_eq_expert_id=:id AND k_ea_question_id IS NULL');
            $query2->execute(array(":id" => $id));
            $row2 = $query2->fetch(PDO::FETCH_ASSOC);
            $this->need_answer = $row2['num'];
            $query3 = $mysql->prepare('SELECT count(*) AS num
                FROM k_experts_questions AS keq
                LEFT JOIN k_experts_answers AS kea ON (keq.k_eq_id = kea.k_ea_question_id)
                WHERE k_eq_expert_id=:id AND k_ea_question_id IS NOT NULL');
            $query3->execute(array(":id" => $id));
            $row3 = $query3->fetch(PDO::FETCH_ASSOC);
            $this->have_answer = $row3['num'];
        } catch (PDOException $e) {
            exit();
        }
    }

}

class ExpertCategories {

    public $id = array();
    public $name = array();

    public function ExpertCategories() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT * FROM k_experts_categories ORDER BY k_ec_name ASC');
            $query->execute();
            $row = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($row as $value) {
                array_push($this->id, $value['k_ec_id']);
                array_push($this->name, $value['k_ec_name']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class Messages {

    public $id = array();
    public $text = array();
    public $user_id = array();
    public $user_login = array();
    public $sender_id = array();
    public $sender_login = array();
    public $read = array();
    public $date = array();
    public $count = 0;

    public function MessagesInbox() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT * FROM k_user_messages AS kum
                LEFT JOIN k_users AS ku ON (kum.k_um_sender_id = ku.k_ku_id)
                WHERE k_um_user_id=:id
                ORDER BY k_um_date DESC');
            $query->execute(array(":id" => $_SESSION['id']));
            $row = $query->fetchAll(PDO::FETCH_ASSOC);
            $this->count = $query->rowCount();
            foreach ($row as $value) {
                array_push($this->id, $value['k_um_id']);
                array_push($this->text, $value['k_um_text']);
                array_push($this->sender_id, $value['k_um_sender_id']);
                if ($value['k_ku_login'] != '') {
                    array_push($this->sender_login, $value['k_ku_login']);
                } else {
                    array_push($this->sender_login, 'Гость');
                }
                array_push($this->date, $value['k_um_date']);
                array_push($this->read, $value['k_um_read']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

    public function MessagesOutbox() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT * FROM k_user_messages AS kum
                LEFT JOIN k_users AS ku ON (kum.k_um_user_id = ku.k_ku_id)
                WHERE k_um_sender_id=:id
                ORDER BY k_um_date DESC');
            $query->execute(array(":id" => $_SESSION['id']));
            $row = $query->fetchAll(PDO::FETCH_ASSOC);
            $this->count = $query->rowCount();
            foreach ($row as $value) {
                array_push($this->id, $value['k_um_id']);
                array_push($this->text, $value['k_um_text']);
                array_push($this->user_id, $value['k_um_sender_id']);
                array_push($this->user_login, $value['k_ku_login']);
                array_push($this->date, $value['k_um_date']);
                array_push($this->read, $value['k_um_read']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class ProfileAgent {

    public $agent_id = array();
    public $firm_id = array();
    public $agent_login = array();
    public $agent_ip = array();
    public $agent_autor_date = array();
    public $agent_fname = array();
    public $agent_lname = array();
    public $agent_oname = array();
    public $agent_email = array();
    public $agent_last_date = array();
    public $agent_online = array();
    public $agent_name = array();
    public $agent_avatar = array();
    public $agent_state = array();
    public $agent_phone = array();
    public $agent_address = array();
    public $agent_address_str = array();
    public $agent_site = array();
    public $agent_end_date = array();
    public $agent_end_days = array();
    public $agent_description = array();
    public $agent_ads = array();

    function LoadAgent($limit, $page, $where) {
        $limit = filter_var($limit, FILTER_VALIDATE_INT, array("options" => array("min_range" => 0, "max_range" => 50)));
        $page = filter_var($page, FILTER_VALIDATE_INT);
        $where = filter_var($where, FILTER_SANITIZE_STRIPPED);
        $query = 'SELECT ku.*, kua.*, ks.k_s_name, kshn.k_shn_house_num, kshn.k_shn_id, kuar.k_uar_id, count( kisell.k_isf_id ) as agent_ads
            FROM k_users as ku
            LEFT JOIN k_users_agents as kua ON (kua.k_ua_user_parent = ku.k_ku_id)
            LEFT JOIN k_streets_house_nums as kshn ON (kua.k_ua_address = kshn.k_shn_id)
            LEFT JOIN k_streets as ks ON (kshn.k_shn_street_id = ks.k_s_id)
            LEFT JOIN k_users_agents_register as kuar ON (kuar.k_uar_user_id = ku.k_ku_id)
            LEFT JOIN k_immovables_sell as kisell ON (kisell.k_isf_user_id = ku.k_ku_id)
            WHERE k_u_privileges=4 AND k_ua_last_date>NOW() AND k_ua_state=1 ' . $where . ' 
            GROUP BY k_ku_id';
        $query .= ' ORDER BY k_uar_id DESC , k_ku_id ASC';
        if ($limit != 0) {
            $query .= ' LIMIT ' . (($page - 1) * $limit) . ',' . $limit;
        }
        //echo $query.'<br>';
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare($query);
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        $n = 0;
        foreach ($result as $row) {
            $this->agent_id[$n] = $row['k_ku_id'];
            $this->firm_id[$n] = $row['k_ua_id'];
            $this->agent_login[$n] = $row['k_ku_login'];
            $this->agent_ip[$n] = $row['k_ku_last_ip'];
            $this->agent_autor_date[$n] = $row['k_ku_autor_date'];
            $this->agent_fname[$n] = $row['k_ku_fname'];
            $this->agent_lname[$n] = $row['k_ku_lname'];
            $this->agent_oname[$n] = $row['k_ku_oname'];
            $this->agent_email[$n] = $row['k_ku_email'];
            $this->agent_last_date[$n] = $row['k_ku_last_date'];
            $this->agent_online[$n] = $row['k_u_online'];
            $this->agent_name[$n] = $row['k_ua_name'];
            $this->agent_avatar[$n] = $row['k_ua_avatar'];
            $this->agent_state[$n] = $row['k_ua_state'];
            $this->agent_phone[$n] = $row['k_ua_phone'];
            $this->agent_address[$n] = $row['k_shn_id'];
            $this->agent_address_str[$n] = $row['k_s_name'] . ' ' . $row['k_shn_house_num'];
            $this->agent_site[$n] = $row['k_ua_site'];
            $this->agent_description[$n] = $row['k_ua_description'];
            $this->agent_ads[$n] = $row['agent_ads'];
            $this->agent_end_date[$n] = $row['k_ua_last_date'];
            $this->agent_end_days[$n] = round((strtotime($row['k_ua_last_date']) - time()) / 86400, 0);
            $n++;
        }
    }

    function GenerateAgent($id) {
        $one_agent = new ProfileAgent();
        $one_agent->LoadAgent(0, 0, ' AND k_ku_id=' . $id);
        if (count($one_agent->agent_id) == 0) {
            echo '<div class="block_content_1">Агентство не найдено!</div>';
        } else {
            $id = 0;
            $query = 'SELECT k_is_id, k_is_name, count(k_isf_id) as max, k_is_parent
            FROM k_immovables_subcategories as kisu
            LEFT JOIN k_immovables_sell as kise ON (kise.k_isf_subcategory = kisu.k_is_id AND kise.k_isf_user_id = :id)
            WHERE (k_is_parent = 1 OR k_is_parent = 2)
            GROUP BY k_is_id ORDER BY k_is_id ASC';
            //echo $query.'<br>';
            try {
                $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
                $mysql->exec('set names utf8');
                $queue = $mysql->prepare($query);
                $queue->execute(array(':id' => $one_agent->agent_id[$id]));
                $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                exit();
            }
            $subcategory = array();
            $count = array();
            $parent = array();
            $sell = 0;
            $rent = 0;
            foreach ($result as $row) {
                array_push($subcategory, $row['k_is_name']);
                array_push($count, $row['max']);
                array_push($parent, $row['k_is_parent']);
                if ($row['k_is_parent'] == 1) {
                    $sell += $row['max'];
                }
                if ($row['k_is_parent'] == 2) {
                    $rent += $row['max'];
                }
            }
            $allinone = array($subcategory, $count, $parent);
            echo '<div class="left_agent_menu">
            <p class="conteiner">
            <a class="menu_kab_t" href="prof_agent.php?PageType=5&DealType=1"><b>ПРОДАЮ</b></a>
            <span class="kab_nambe">' . $sell . '</span></p>';
            for ($i = 0; $i < count($allinone[0]); $i++) {
                if ($allinone[2][$i] == 1) {
                    echo '<p class="conteiner">
                        <a class="menu_kab_t" href="prof_agent.php?PageType=5&Category=' . ($i + 1) . '">' . $allinone[0][$i] . '</a>
                        <span class="kab_nambe">' . $allinone[1][$i] . '</span></p>';
                }
            }
            echo '</div>
            <div class="right_agent_menu">
            <a class="menu_kab_t" href="prof_agent.php?PageType=5&DealType=2"><b>СДАЮ</b></a>
            <span class="kab_nambe">' . $rent . '</span><br>';
            for ($i = 0; $i < count($allinone[0]); $i++) {
                if ($allinone[2][$i] == 2) {
                    echo '<p class="conteiner">
                        <a class="menu_kab_t" href="prof_agent.php?PageType=5&Category=' . ($i + 1) . '">' . $allinone[0][$i] . '</a>
                        <span class="kab_nambe">' . $allinone[1][$i] . '</span></p>';
                }
            }
            echo '</div>';
        }
    }

}

class AdSelfAgency {

    public $photo_url = array();
    public $description = array();
    public $date_reg = array();
    public $date_end = array();
    public $id = array();
    public $state = array();
    public $color = array();
    public $lock_start = array();
    public $lock_end = array();
    public $special = array();

    public function AdSelfAgency($user_id, $category, $deal_type, $page) {
        $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
        $category = filter_var($category, FILTER_VALIDATE_INT);
        $deal_type = filter_var($deal_type, FILTER_VALIDATE_INT);
        $page = filter_var($page, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = 'SELECT ise.k_isf_description, ise.k_isf_registration_date, ise.k_isf_end_date, kiph.k_ip_url,
                ise.k_isf_id, ise.k_isf_state, ise.k_isf_color_light, kil.*
                FROM k_immovables_sell as ise
                LEFT JOIN (SELECT * FROM k_immovables_photos ORDER BY k_ip_priority DESC) as kiph ON (kiph.k_ip_immo_id = ise.k_isf_id)
                LEFT JOIN k_immovables_locked as kil ON (kil.k_il_ad_id = ise.k_isf_id)
                WHERE k_isf_user_id=:user ';
            if ($category != 0) {
                $queue .= ' AND k_isf_subcategory=' . $category . ' ';
            }
            if ($deal_type != 0) {
                $queue .= ' AND k_isf_deal_type=' . $deal_type . ' ';
            }
            $queue .='GROUP BY k_isf_id
                ORDER BY k_isf_registration_date DESC
                LIMIT ' . (($page - 1) * 10) . ',' . $page * 10;
            $query = $mysql->prepare($queue);
            $query->execute(array(':user' => $user_id));
            $row = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        foreach ($row as $value) {
            if (mb_strlen($value['k_isf_description'], 'UTF8') > 100) {
                $value['k_isf_description'] = mb_substr($value['k_isf_description'], 0, 100, 'utf-8') . '...';
            }
            array_push($this->description, $value['k_isf_description']);
            array_push($this->date_reg, $value['k_isf_registration_date']);
            array_push($this->date_end, $value['k_isf_end_date']);
            array_push($this->photo_url, $value['k_ip_url']);
            array_push($this->id, $value['k_isf_id']);
            array_push($this->state, $value['k_isf_state']);
            array_push($this->color, $value['k_isf_color_light']);
            array_push($this->lock_start, $value['k_il_date_start']);
            array_push($this->lock_end, $value['k_il_date_stop']);
        }
        $query2 = $mysql->prepare('SELECT k_is_immovable_id FROM k_immovables_special ORDER BY k_is_id DESC');
        $query2->execute();
        $row2 = $query2->fetchAll(PDO::FETCH_ASSOC);
        foreach ($row2 as $value) {
            array_push($this->special, $value['k_is_immovable_id']);
        }
    }

    function GenerateNavigation($page, $where, $link) {
        $page = filter_var($page, FILTER_VALIDATE_INT);
        $where = filter_var($where, FILTER_SANITIZE_STRIPPED);
        $link = filter_var($link, FILTER_SANITIZE_STRIPPED);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue1 = $mysql->prepare('SELECT count(*) AS max FROM k_immovables_sell WHERE ' . $where . ' k_isf_user_id=:id');
            $queue1->execute(array(":id" => $_SESSION['id']));
            $row = $queue1->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        $pages = intval($row['max'] / 10);
        if ($row['max'] % 10 != 0) {
            $pages++;
        }
        if ($pages > 1) {
            echo '<div class="listing">';
            if ($page == $pages || $page > 1) {
                echo '<a class="style_listing" href="./prof_agent?PageType=5&PageIndex=' . ($page - 1) . $link . '">Предыдущая</a>';
            }

            if ($pages <= 11) {
                for ($i = 1; $i <= $pages; $i++) {
                    if ($i == $page) {
                        echo '<a class="active_listing" href="./prof_agent?PageType=5&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    } else {
                        echo '<a class="style_listing" href="./prof_agent?PageType=5&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
            } else {
                if ($page <= 6) {
                    for ($i = 1; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./prof_agent?PageType=5&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./prof_agent?PageType=5&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./prof_agent?PageType=5&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
                if ($page > 6 && $page <= ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./prof_agent?PageType=5&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $page - 2; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./prof_agent?PageType=5&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./prof_agent?PageType=5&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./prof_agent?PageType=5&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
                if ($page > ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./prof_agent?PageType=5&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 5; $i <= $pages; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./prof_agent?PageType=5&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./prof_agent?PageType=5&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                }
            }

            if ($page == 1 || $page < $pages) {
                echo '<a class="style_listing" href="./prof_agent?PageType=5&PageIndex=' . ($page + 1) . $link . '">Следующая</a>';
            }

            echo '</div>';
        }
    }

}

class EducationTypes {

    public $id = array();
    public $name = array();

    public function EducationTypes() {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT * FROM k_job_education ORDER BY k_je_id ASC');
            $query->execute();
            $row = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($row as $value) {
                array_push($this->id, $value['k_je_id']);
                array_push($this->name, $value['k_je_name']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class UserPackages {

    public $id = array();
    public $package = array();
    public $num = array();

    public function __construct($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT k_tp_id,k_tp_name,k_up_id,k_up_num
                FROM k_tariff_packages AS ktp
                LEFT JOIN k_users_packages AS kup ON (kup.k_up_package_id = ktp.k_tp_id AND k_up_user_id=:id)
                GROUP BY k_tp_id
                ORDER BY k_tp_id ASC');
            $query->execute(array(":id" => $id));
            $row = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($row as $value) {
                array_push($this->id, $value['k_up_id']);
                array_push($this->package, $value['k_tp_id']);
                if ($value['k_up_num'] == '') {
                    array_push($this->num, '0');
                } else {
                    array_push($this->num, $value['k_up_num']);
                }
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class UserPackets {

    public $id = array();
    public $packet = array();
    public $total = array();
    public $price = array();
    public $remain = array();
    public $start_date = array();
    public $current_remain = 0;

    public function __construct($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query0 = $mysql->prepare('DELETE FROM k_users_packets WHERE k_up_start_date < NOW() - INTERVAL 1 MONTH OR k_up_remain=0');
            $query0->execute();
            $query = $mysql->prepare('SELECT *
                FROM k_users_packets AS kup
                LEFT JOIN k_tariff_packets_attrs AS ktpa ON (ktpa.k_tpa_id = kup.k_up_packet)
                WHERE k_up_user=:id
                ORDER BY k_up_start_date ASC');
            $query->execute(array(":id" => $id));
            $row = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($row as $value) {
                array_push($this->id, $value['k_up_id']);
                array_push($this->packet, $value['k_up_packet']);
                array_push($this->total, $value['k_tpa_int']);
                array_push($this->price, $value['k_tpa_price']);
                array_push($this->remain, $value['k_up_remain']);
                array_push($this->start_date, $value['k_up_start_date']);
            }
            $query2 = $mysql->prepare('SELECT * FROM k_users_packets WHERE k_up_start_date < NOW() AND k_up_user=:id');
            $query2->execute(array(":id" => $id));
            $row2 = $query2->fetch(PDO::FETCH_ASSOC);
            if ($query2->rowCount() != 0) {
                $this->current_remain = $row2['k_up_remain'];
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class PacketsForUser {

    public $id = array();
    public $total = array();
    public $price = array();

    public function __construct($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT * FROM k_tariff_packets_attrs WHERE k_tpa_packet=4 AND k_tpa_owner=:id ORDER BY k_tpa_id ASC');
            $query->execute(array(":id" => $id));
            $row = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($row as $value) {
                array_push($this->id, $value['k_tpa_id']);
                array_push($this->total, $value['k_tpa_int']);
                array_push($this->price, $value['k_tpa_price']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class ImmovablesPackges {

    public $id = array();
    public $immo = array();
    public $package = array();
    public $lock = array();
    public $up = array();
    public $color = array();
    public $vip = array();

    public function __construct($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT kip.*
                    FROM k_immovables_sell AS kis
                    LEFT JOIN k_immovables_packets AS kip ON (kip.k_ip_immo_id = kis.k_isf_id)
                    WHERE k_isf_user_id=:id
                    ORDER BY k_ip_immo_id ASC, k_ip_packet ASC');
            $query->execute(array(":id" => $id));
            $row = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($row as $value) {
                array_push($this->id, $value['k_ip_id']);
                array_push($this->immo, $value['k_ip_immo_id']);
                array_push($this->package, $value['k_ip_packet']);
                array_push($this->lock, $value['k_ip_lock']);
                array_push($this->up, $value['k_ip_up']);
                array_push($this->color, $value['k_ip_color']);
                array_push($this->vip, $value['k_ip_vip']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

?>
