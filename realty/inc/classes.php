<?php

require_once 'functions.php';

class Ads {

    public $id = array();
    public $deal_type = array();
    public $subcategory = array();
    public $subcategory_string = array();
    public $immo_type = array();
    public $immo_type_string = array();
    public $address = array();
    public $address_string = array();
    public $district = array();
    public $district_id = array();
    public $new = array();
    public $material = array();
    public $material_string = array();
    public $floor = array();
    public $floor_all = array();
    public $eq = array();
    public $eq_string = array();
    public $rooms = array();
    public $area_all = array();
    public $area_live = array();
    public $area_kitchen = array();
    public $area_land = array();
    public $san = array();
    public $balcony = array();
    public $phone_stat = array();
    public $security = array();
    public $internet = array();
    public $balcony_gl = array();
    public $furniture = array();
    public $fridge = array();
    public $washing = array();
    public $microwave = array();
    public $tv = array();
    public $ctv = array();
    public $stove = array();
    public $plastic_windows = array();
    public $utilities = array();
    public $price = array();
    public $quickly = array();
    public $exchange = array();
    public $credit = array();
    public $privat = array();
    public $documents = array();
    public $merch = array();
    public $owned = array();
    public $description = array();
    public $contacts = array();
    public $contact_name = array();
    public $owner = array();
    public $owner_string = array();
    public $views = array();
    public $registration_date = array();
    public $end_date = array();
    public $base = array();
    public $user = array();
    public $user_string = array();
    public $state = array();
    public $email = array();
    public $ip = array();
    public $photo_url = array();
    public $special = array();
    public $object_id = array();
    public $agent_name = array();
    public $id_on_map = array();
    public $color_light = array();
    public $total = 0;
    public $centerX = array();
    public $centerY = array();

    /**
     * Загрузить объявления с дополнительными параметрами
     * @param int $limit LIMIT в SQL запросе. Игнорируется, если $limit = 0.
     * @param int $page Номер страницы в списке объявлений. Игнорируется, если $limit = 0.
     * @param String $where WHERE в SQL запросе.
     * @param int $id ID объявления, если требуется загрузить конкретное объявление. Игнорируется, если $id = 0. Загружает специальные предложения, если $id = -1.
     */
    function LoadAds($limit, $page, $where, $id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        $page = filter_var($page, FILTER_VALIDATE_INT);
        $limit = filter_var($limit, FILTER_VALIDATE_INT, array("options" => array("min_range" => 0, "max_range" => 50)));
        if ($id != 0) {
            $where = ' AND k_isf_id=' . $id;
        }
        if ($id == -1) {
            $where = ' AND kisp.k_is_id IS NOT NULL ';
        }
        $query = 'SELECT ise.*, isub.k_is_name, iow.k_io_name, ist.k_isft_name, kism.k_isfm_name, kise.k_isfe_name,
            kshn.k_shn_house_num, kstre.k_s_name, kdis.k_d_name, kdis.k_d_id, kshn.k_shn_object_id, mb.fid,
            kto.k_t_name, kuser.k_ku_login, kuser.k_ku_email, kuser.k_ku_last_ip, kiph.k_ip_url, kuag.k_ua_name,
            kshn.centerX, kshn.centerY
            FROM k_immovables_sell as ise
            LEFT JOIN k_immovables_subcategories as isub ON (isub.k_is_id = ise.k_isf_subcategory)
            LEFT JOIN k_immovables_owners as iow ON (iow.k_io_id = ise.k_isf_owner)
            LEFT JOIN k_immovables_sell_types as ist ON (ist.k_isft_id = ise.k_isf_immovable_type)
            LEFT JOIN k_immovables_sell_material as kism ON (kism.k_isfm_id = ise.k_isf_material)
            LEFT JOIN k_immovables_sell_eq as kise ON (kise.k_isfe_id = ise.k_isf_eq)
            LEFT JOIN k_streets_house_nums as kshn ON (kshn.k_shn_id = ise.k_isf_address)
            LEFT JOIN k_streets as kstre ON (kstre.k_s_id = kshn.k_shn_street_id)
            LEFT JOIN k_districts as kdis ON (kdis.k_d_id = kshn.k_shn_district_id)
            LEFT JOIN k_towns as kto ON (kdis.k_d_town = kto.k_t_id)
            LEFT JOIN k_users as kuser ON (kuser.k_ku_id = ise.k_isf_user_id)
            LEFT JOIN (SELECT * FROM k_immovables_photos ORDER BY k_ip_priority DESC) as kiph ON (kiph.k_ip_immo_id = ise.k_isf_id)
            LEFT JOIN k_immovables_special as kisp ON (kisp.k_is_immovable_id = ise.k_isf_id)
            LEFT JOIN k_users_agents as kuag ON (kuag.k_ua_user_parent = kuser.k_ku_id)
            LEFT JOIN map_buildings AS mb ON (mb.k_shn_id = kshn.k_shn_id)
            LEFT JOIN k_immovables_locked as kil ON (kil.k_il_ad_id = ise.k_isf_id)
            WHERE (k_isf_state=1 AND k_isf_end_date > NOW()) ' . $where . '
            GROUP BY k_isf_id
            ORDER BY k_il_date_start DESC, k_isf_up_date DESC';
        if ($limit) {
            $query .= ' LIMIT ' . (($page - 1) * $limit) . ',' . $limit;
        }
        //echo '<br>'.$query.'<br>';
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare($query);
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit('');
        }
        $n = 0;
        foreach ($result as $row) {
            $this->id[$n] = $row['k_isf_id'];
            $this->deal_type[$n] = $row['k_isf_deal_type'];
            $this->subcategory[$n] = $row['k_isf_subcategory'];
            $this->subcategory_string[$n] = $row['k_is_name'];
            $this->immo_type[$n] = $row['k_isf_immovable_type'];
            $this->immo_type_string[$n] = $row['k_isft_name'];
            $this->address[$n] = $row['k_isf_address'];
            if (preg_match('/(###)/', $row['k_s_name'])) {
                $street = explode('###', $row['k_s_name']);
                $house = explode('###', $row['k_shn_house_num']);
                array_push($this->address_string, $street[0] . ', ' . $house[0] . ' / ' . $street[1] . ', ' . $house[1]);
            } else {
                array_push($this->address_string, $row['k_s_name'] . ', ' . $row['k_shn_house_num']);
            }
            $this->district[$n] = $row['k_d_name'];
            $this->district_id[$n] = $row['k_d_id'];
            $this->new[$n] = $row['k_isf_new'];
            $this->material[$n] = $row['k_isf_material'];
            $this->material_string[$n] = $row['k_isfm_name'];
            $this->floor[$n] = $row['k_isf_floor'];
            $this->floor_all[$n] = $row['k_isf_floor_all'];
            $this->eq[$n] = $row['k_isf_eq'];
            $this->eq_string[$n] = $row['k_isfe_name'];
            $this->rooms[$n] = $row['k_isf_rooms'];
            $this->area_all[$n] = $row['k_isf_area_all'];
            $this->area_live[$n] = $row['k_isf_area_live'];
            $this->area_kitchen[$n] = $row['k_isf_area_kitchen'];
            $this->area_land[$n] = $row['k_isf_area_land'];
            $this->san[$n] = $row['k_isf_san'];
            $this->balcony[$n] = $row['k_isf_balcony'];
            $this->phone_stat[$n] = $row['k_isf_phone_stat'];
            $this->security[$n] = $row['k_isf_security'];
            $this->internet[$n] = $row['k_isf_internet'];
            $this->balcony_gl[$n] = $row['k_isf_balcony_gl'];
            $this->furniture[$n] = $row['k_isf_furniture'];
            $this->fridge[$n] = $row['k_isf_fridge'];
            $this->washing[$n] = $row['k_isf_washing'];
            $this->microwave[$n] = $row['k_isf_microwave'];
            $this->tv[$n] = $row['k_isf_tv'];
            $this->ctv[$n] = $row['k_isf_ctv'];
            $this->stove[$n] = $row['k_isf_stove'];
            $this->plastic_windows[$n] = $row['k_isf_plastic_windows'];
            $this->utilities[$n] = $row['k_isf_utilities'];
            $this->price[$n] = $row['k_isf_price'];
            $this->quickly[$n] = $row['k_isf_quickly'];
            $this->exchange[$n] = $row['k_isf_exchange'];
            $this->credit[$n] = $row['k_isf_credit'];
            $this->privat[$n] = $row['k_isf_privat'];
            $this->documents[$n] = $row['k_isf_documents'];
            $this->merch[$n] = $row['k_isf_merch'];
            $this->owned[$n] = $row['k_isf_owned'];
            $this->description[$n] = $row['k_isf_description'];
            $this->contacts[$n] = $row['k_isf_contacts'];
            $this->contact_name[$n] = $row['k_isf_contact_name'];
            $this->owner[$n] = $row['k_isf_owner'];
            $this->owner_string[$n] = $row['k_io_name'];
            $this->views[$n] = $row['k_isf_views'];
            $this->registration_date[$n] = $row['k_isf_registration_date'];
            $this->end_date[$n] = $row['k_isf_end_date'];
            $this->base[$n] = $row['k_isf_base'];
            $this->user[$n] = $row['k_isf_user_id'];
            $this->user_string[$n] = $row['k_ku_login'];
            $this->state[$n] = $row['k_isf_state'];
            $this->email[$n] = $row['k_ku_email'];
            $this->ip[$n] = $row['k_ku_last_ip'];
            $this->photo_url[$n] = $row['k_ip_url'];
            $this->object_id[$n] = $row['k_shn_object_id'];
            $this->agent_name[$n] = $row['k_ua_name'];
            $this->id_on_map[$n] = $row['fid'];
            $this->centerX[$n] = $row['centerX'];
            $this->centerY[$n] = $row['centerY'];
            $n++;
        }
        //Загружаем исключительно специальные предложения в столбец справа.
        $query = $mysql->prepare("SELECT count(k_is_immovable_id) AS max
            FROM k_immovables_special AS kisp
            LEFT JOIN k_immovables_sell AS kis ON (kisp.k_is_immovable_id = kis.k_isf_id)
            WHERE (k_isf_state=1 AND k_isf_end_date > NOW())
            ORDER BY k_isf_up_date DESC");
        $query->execute();
        $result2 = $query->fetch(PDO::FETCH_ASSOC);
        array_push($this->special, $result2['max']);
        $query = 'SELECT k_isf_id
            FROM k_immovables_sell as ise
            LEFT JOIN k_immovables_subcategories as isub ON (isub.k_is_id = ise.k_isf_subcategory)
            LEFT JOIN k_immovables_owners as iow ON (iow.k_io_id = ise.k_isf_owner)
            LEFT JOIN k_immovables_sell_types as ist ON (ist.k_isft_id = ise.k_isf_immovable_type)
            LEFT JOIN k_immovables_sell_material as kism ON (kism.k_isfm_id = ise.k_isf_material)
            LEFT JOIN k_immovables_sell_eq as kise ON (kise.k_isfe_id = ise.k_isf_eq)
            LEFT JOIN k_streets_house_nums as kshn ON (kshn.k_shn_id = ise.k_isf_address)
            LEFT JOIN k_streets as kstre ON (kstre.k_s_id = kshn.k_shn_street_id)
            LEFT JOIN k_districts as kdis ON (kdis.k_d_id = kshn.k_shn_district_id)
            LEFT JOIN k_towns as kto ON (kdis.k_d_town = kto.k_t_id)
            LEFT JOIN k_users as kuser ON (kuser.k_ku_id = ise.k_isf_user_id)
            LEFT JOIN (SELECT * FROM k_immovables_photos ORDER BY k_ip_priority DESC) as kiph ON (kiph.k_ip_immo_id = ise.k_isf_id)
            LEFT JOIN k_immovables_special as kisp ON (kisp.k_is_immovable_id = ise.k_isf_id)
            LEFT JOIN k_users_agents as kuag ON (kuag.k_ua_user_parent = kuser.k_ku_id)
            LEFT JOIN map_buildings AS mb ON (mb.k_shn_id = kshn.k_shn_id)
            WHERE (k_isf_state=1 AND k_isf_end_date > NOW()) ' . $where . '
            GROUP BY k_isf_id
            ORDER BY k_isf_up_date DESC';
        try {
            $queueT = $mysql->prepare($query);
            $queueT->execute();
            $resultT = $queueT->fetchAll(PDO::FETCH_ASSOC);
            $this->total = count($resultT);
        } catch (PDOException $e) {
            exit('');
        }
        $mysql = NULL;
    }
    function LoadThisAds($id) {
        $where = ' AND (';
        foreach ($id as $value) {
            $value = filter_var($value, FILTER_VALIDATE_INT);
            $where .= ' k_isf_id=' . $value . ' OR ';
        }
        $where = mb_substr($where, 0, -3, "utf-8") . ') ';
        $query = 'SELECT ise.*, isub.k_is_name, iow.k_io_name, ist.k_isft_name, kism.k_isfm_name, kise.k_isfe_name,
            kshn.k_shn_house_num, kstre.k_s_name, kdis.k_d_name, kdis.k_d_id, kshn.k_shn_object_id, mb.fid,
            kto.k_t_name, kuser.k_ku_login, kuser.k_ku_email, kuser.k_ku_last_ip, kiph.k_ip_url, kuag.k_ua_name
            FROM k_immovables_sell as ise
            LEFT JOIN k_immovables_subcategories as isub ON (isub.k_is_id = ise.k_isf_subcategory)
            LEFT JOIN k_immovables_owners as iow ON (iow.k_io_id = ise.k_isf_owner)
            LEFT JOIN k_immovables_sell_types as ist ON (ist.k_isft_id = ise.k_isf_immovable_type)
            LEFT JOIN k_immovables_sell_material as kism ON (kism.k_isfm_id = ise.k_isf_material)
            LEFT JOIN k_immovables_sell_eq as kise ON (kise.k_isfe_id = ise.k_isf_eq)
            LEFT JOIN k_streets_house_nums as kshn ON (kshn.k_shn_id = ise.k_isf_address)
            LEFT JOIN k_streets as kstre ON (kstre.k_s_id = kshn.k_shn_street_id)
            LEFT JOIN k_districts as kdis ON (kdis.k_d_id = kshn.k_shn_district_id)
            LEFT JOIN k_towns as kto ON (kdis.k_d_town = kto.k_t_id)
            LEFT JOIN k_users as kuser ON (kuser.k_ku_id = ise.k_isf_user_id)
            LEFT JOIN (SELECT * FROM k_immovables_photos ORDER BY k_ip_priority DESC) as kiph ON (kiph.k_ip_immo_id = ise.k_isf_id)
            LEFT JOIN k_immovables_special as kisp ON (kisp.k_is_immovable_id = ise.k_isf_id)
            LEFT JOIN k_users_agents as kuag ON (kuag.k_ua_user_parent = kuser.k_ku_id)
            LEFT JOIN map_buildings AS mb ON (mb.k_shn_id = kshn.k_shn_id)
            WHERE (k_isf_state=1 AND k_isf_end_date > NOW()) ' . $where . '
            GROUP BY k_isf_id
            ORDER BY k_isf_up_date DESC';
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare($query);
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit('');
        }
        $n = 0;
        foreach ($result as $row) {
            $this->id[$n] = $row['k_isf_id'];
            $this->deal_type[$n] = $row['k_isf_deal_type'];
            $this->subcategory[$n] = $row['k_isf_subcategory'];
            $this->subcategory_string[$n] = $row['k_is_name'];
            $this->immo_type[$n] = $row['k_isf_immovable_type'];
            $this->immo_type_string[$n] = $row['k_isft_name'];
            $this->address[$n] = $row['k_isf_address'];
            if (preg_match('/(###)/', $row['k_s_name'])) {
                $street = explode('###', $row['k_s_name']);
                $house = explode('###', $row['k_shn_house_num']);
                array_push($this->address_string, $street[0] . ', ' . $house[0] . ' / ' . $street[1] . ', ' . $house[1]);
            } else {
                array_push($this->address_string, $row['k_s_name'] . ', ' . $row['k_shn_house_num']);
            }
            $this->district[$n] = $row['k_d_name'];
            $this->district_id[$n] = $row['k_d_id'];
            $this->new[$n] = $row['k_isf_new'];
            $this->material[$n] = $row['k_isf_material'];
            $this->material_string[$n] = $row['k_isfm_name'];
            $this->floor[$n] = $row['k_isf_floor'];
            $this->floor_all[$n] = $row['k_isf_floor_all'];
            $this->eq[$n] = $row['k_isf_eq'];
            $this->eq_string[$n] = $row['k_isfe_name'];
            $this->rooms[$n] = $row['k_isf_rooms'];
            $this->area_all[$n] = $row['k_isf_area_all'];
            $this->area_live[$n] = $row['k_isf_area_live'];
            $this->area_kitchen[$n] = $row['k_isf_area_kitchen'];
            $this->area_land[$n] = $row['k_isf_area_land'];
            $this->san[$n] = $row['k_isf_san'];
            $this->balcony[$n] = $row['k_isf_balcony'];
            $this->phone_stat[$n] = $row['k_isf_phone_stat'];
            $this->security[$n] = $row['k_isf_security'];
            $this->internet[$n] = $row['k_isf_internet'];
            $this->balcony_gl[$n] = $row['k_isf_balcony_gl'];
            $this->furniture[$n] = $row['k_isf_furniture'];
            $this->fridge[$n] = $row['k_isf_fridge'];
            $this->washing[$n] = $row['k_isf_washing'];
            $this->microwave[$n] = $row['k_isf_microwave'];
            $this->tv[$n] = $row['k_isf_tv'];
            $this->ctv[$n] = $row['k_isf_ctv'];
            $this->stove[$n] = $row['k_isf_stove'];
            $this->plastic_windows[$n] = $row['k_isf_plastic_windows'];
            $this->utilities[$n] = $row['k_isf_utilities'];
            $this->price[$n] = $row['k_isf_price'];
            $this->quickly[$n] = $row['k_isf_quickly'];
            $this->exchange[$n] = $row['k_isf_exchange'];
            $this->credit[$n] = $row['k_isf_credit'];
            $this->privat[$n] = $row['k_isf_privat'];
            $this->documents[$n] = $row['k_isf_documents'];
            $this->merch[$n] = $row['k_isf_merch'];
            $this->owned[$n] = $row['k_isf_owned'];
            $this->description[$n] = $row['k_isf_description'];
            $this->contacts[$n] = $row['k_isf_contacts'];
            $this->contact_name[$n] = $row['k_isf_contact_name'];
            $this->owner[$n] = $row['k_isf_owner'];
            $this->owner_string[$n] = $row['k_io_name'];
            $this->views[$n] = $row['k_isf_views'];
            $this->registration_date[$n] = $row['k_isf_registration_date'];
            $this->end_date[$n] = $row['k_isf_end_date'];
            $this->base[$n] = $row['k_isf_base'];
            $this->user[$n] = $row['k_isf_user_id'];
            $this->user_string[$n] = $row['k_ku_login'];
            $this->state[$n] = $row['k_isf_state'];
            $this->email[$n] = $row['k_ku_email'];
            $this->ip[$n] = $row['k_ku_last_ip'];
            $this->photo_url[$n] = $row['k_ip_url'];
            $this->object_id[$n] = $row['k_shn_object_id'];
            $this->agent_name[$n] = $row['k_ua_name'];
            $this->id_on_map[$n] = $row['fid'];
            $this->centerX[$n] = $row['centerX'];
            $this->centerY[$n] = $row['centerY'];
            $n++;
        }
        $mysql = NULL;
    }
    /**
     * 
     * @param type $num
     */
    function GenerateTable($num=0) {
        if (empty($num) || $num == 0 || $num > count($this->id)) {
            $num = count($this->id);
        }
        //var_dump(count($this->id));
        for ($i = 0; $i < $num; $i++) {
            //Чередуем цвета\
            if ($this->color_light[$i] == 1) {
                echo '<div class="obiavlenie_color">';
            } else {
                if ($i % 2 == 0) {
                    echo '<div class="obiavlenie">';
                } else {
                    echo '<div class="obiavlenie_1">';
                }
            }
            /*
            if ($i % 2 == 0) {
                echo '<div class="obiavlenie">';
            } else {
                echo '<div class="obiavlenie_1">';
            }
            */
            echo '<div class="img_obiavlenie">';
            $file_name = str_replace('images/', '../admin/images/1_', $this->photo_url[$i]);
            $file_name = str_replace('video/', '../video/', $file_name);

            //Есть или нет изображение
            if (!file_exists($file_name) || !$this->photo_url[$i]) {
                echo '<a href="../realty/?ShowParam=20&id=' . $this->id[$i] . '"><img class="img_obiavlenie_1" src="../images/noimage.png" alt=""></a>';
                echo '<p class="bottom_text_1">' . $this->registration_date[$i] . '</p>';
            } else {
                echo '<a href="./?ShowParam=20&id=' . $this->id[$i] . '"><img class="img_obiavlenie_1" src="../admin/' . $this->photo_url[$i] . '" alt=""></a>
                    <p class="bottom_text_1">' . $this->registration_date[$i] . '</p>';
            }
            echo '<span class="grey_job blockLeft"></span>';
            if ($this->owner[$i] == 1) {
                echo '<a class="bottom_text_2" href="../realty/?ShowParam=21&Aid=' . $this->user[$i] . '">Агентство ' . $this->agent_name[$i] . '</a>';
            }
            if ($this->owner[$i] == 2) {
                echo '<span class="bottom_text_2">Строитель ' . /* $this->user_string[$i] . */ '</span>';
            }
            if ($this->owner[$i] == 3) {
                echo '<span class="bottom_text_2">Посредник ' . /* $this->user_string[$i] . */ '</span>';
            }
            if ($this->owner[$i] == 4) {
                echo '<span class="bottom_text_2">Собственник ' . /*$this->user_string[$i] . */'</span>';
            }
            /* echo '<p class="all_p"><a class="down_1" title="Поднять объявление" href="?AdsID=' . $this->id[$i] . '">Поднять</a>
              <a class="map_prev" href="../map/?fid=map_buildings.' . $this->id_on_map[$i] . '"><img class="kompas_map" src="../images/map_1.png" title="Показать на карте" alt=""></a>
              </p>'; */
            echo '</div><div class="obiavlenie_inline">';
            /* echo '<div class="obiavlenie_bottom_text">
              <span class="bottom_text_1">' . $this->registration_date[$i] . '</span>';
              echo '<a class="bottom_text_3" href="../map/?fid=map_buildings.' . $this->id_on_map[$i] . '"><img class="kompas_map" src="../images/map_1.png" title="Показать на карте" alt=""></a>
              <a class="map_open_karta">
              <img class="photo_map" src="../images/photo_1.png" onmouseover="ShowPhoto(this);" onmouseout="HidePhoto(this);" alt="' . $this->id[$i] . '">
              </a></div>'; */
            echo '<p class="all_p"><a class="down_1" title="Показать на карте" href="../map/?a=' . $this->centerX[$i].','.$this->centerY[$i] . '">Показать на карте</a>
                    </p>';

            echo '<p class="all_p"><a class="down_1" title="Поднять объявление" href="?AdsID=' . $this->id[$i] . '">Поднять</a>
                <!--<a class="map_prev" href="../map/?a=' . $this->centerX[$i].','.$this->centerY[$i] . '"><img class="kompas_map" src="../images/map_1.png" title="Показать на карте" alt=""></a>-->
                    </p>';

            echo '<div class="name_obiavlenie">';
            if ($this->deal_type[$i] == 1) {
                echo '<span>Продам:</span>';
            } else {
                echo '<span>Сдам:</span>';
            }

            //Подкатегории
            echo '<a class="name_o_1" href="../realty/?ShowParam=20&id=' . $this->id[$i] . '">';
            if ($this->subcategory[$i] == 1 || $this->subcategory[$i] == 6) {
                if ($this->rooms[$i] != 0) {
                    echo $this->rooms[$i] . '-комнатную Квартиру ';
                } else {
                    echo 'Квартиру ';
                }
                if ($this->immo_type[$i] != 0) {
                    echo '(' . $this->immo_type_string[$i] . ')';
                }
            }
            if ($this->subcategory[$i] == 2 || $this->subcategory[$i] == 7) {
                if ($this->rooms[$i] != 0) {
                    echo $this->rooms[$i] . '-комнатный Дом ';
                } else {
                    echo 'Дом ';
                }
                if ($this->immo_type[$i] != 0) {
                    echo ' (' . $this->immo_type_string[$i] . ')';
                }
            }
            $sub_not = array(1, 2, 6, 7);
            if (!in_array($this->subcategory[$i], $sub_not)) {
                if ($this->immo_type[$i] != 0) {
                    echo $this->immo_type_string[$i];
                }
            }
            echo '</a>';
            //ЗДЕСЬ ТЕКСТ
            echo '</div>
                <div class="text_obiavlenie"><p>';
            //Район
            switch ($this->district_id[$i]) {
                case 1: echo 'в Кировском районе города Томска';
                    break;
                case 2: echo 'в Ленинском районе города Томска';
                    break;
                case 3: echo 'в Октябрьском районе города Томска';
                    break;
                case 4: echo 'в Советском районе города Томска';
                    break;
                case 5: echo 'в районе Аэропорт города Томска';
                    break;
            }
            //Адрес
            echo ' по адресу ' . '<a class="bottom_text_4" href="../map/?f=' . $this->address_string[$i] . '" title="Показать на карте">' . $this->address_string[$i] . '</a>';
            //Этаж и материал
            $material = '';
            if ($this->material[$i] != 0) {
                $material = mb_strtolower(dropBackWords($this->material_string[$i]), 'UTF-8');
            }
            if ($this->subcategory[$i] != 4 && $this->subcategory[$i] != 9) {
                if ($this->floor[$i] != 0 && $this->floor_all[$i] != 0) {
                    echo ' на ' . $this->floor[$i] . '-м этаже ' . $this->floor_all[$i] . '-этажного';
                }
                if ($this->floor[$i] == 0 && $this->floor_all[$i] != 0) {
                    echo $this->floor_all[$i] . '-этажного';
                }
                if ($this->floor[$i] != 0 && $this->floor_all[$i] == 0) {
                    echo ' на ' . $this->floor[$i] . '-м этаже';
                }
                if (!empty($material)) {
                    echo ' ' . $material . ' дома';
                }
            } else {
                if ($this->floor[$i] != 0) {
                    echo ' на ' . $this->floor[$i] . '-ом этаже (всего ' . $this->floor_all[$i] . ') ';
                } else {
                    
                }
            }
            //Вторичное
            if ($this->new[$i] == 2) {
                echo ' (вторичное)';
            }
            //Площади
            $price_for_m = '';
            if ($this->area_all[$i] != 0) {
                echo ', общей площадью ' . $this->area_all[$i] . ' кв.м';
                if ($this->deal_type[$i] == 1) {
                    $price_for_m .= ' (' . (round($this->price[$i] / $this->area_all[$i], 2)) . ' за кв.м).';
                }
            }
            echo '.';
            //Отделка
            if ($this->eq[$i] != 0) {
                echo ' ' . $this->eq_string[$i] . '.';
            }
            if ($this->area_live[$i] != 0) {
                echo ' Жилая площадь ' . $this->area_live[$i] . ' кв.м.';
            }
            if ($this->area_kitchen[$i] != 0) {
                echo ' Площадь кухни ' . $this->area_kitchen[$i] . ' кв.м.';
            }
            if ($this->area_land[$i] != 0) {
                echo ' Площадь участка ' . $this->area_land[$i] . ' соток.';
            }
            //Всякие галочки и прочее
            if ($this->san[$i] != 0) {
                switch ($this->san[$i]) {
                    case 1: echo ' Санузел совмещён.';
                        break;
                    case 2: echo ' Раздельный санузел.';
                        break;
                }
            }
            $balcony_gl = '';
            if ($this->balcony[$i] != 0) {
                switch ($this->balcony[$i]) {
                    case 1: if ($this->balcony_gl[$i] != 0) {
                            $balcony_gl = ' (застеклён)';
                        }
                        echo ' Есть балкон' . $balcony_gl . '.';
                        break;
                    case 2: if ($this->balcony_gl[$i] != 0) {
                            $balcony_gl = ' (застеклена)';
                        }
                        echo ' Есть лоджия' . $balcony_gl . '.';
                        break;
                    case 3: echo ' Нет балкона.';
                        break;
                }
            }
            $not_first = FALSE;
            if ($this->phone_stat[$i] != 0) {
                echo ' Есть телефон';
                $not_first = TRUE;
            }
            if ($this->security[$i] != 0) {
                if ($not_first) {
                    echo ', охрана';
                } else {
                    echo ' Есть телефон';
                }
                $not_first = TRUE;
            }
            if ($this->internet[$i] != 0) {
                if ($not_first) {
                    echo ', интернет';
                } else {
                    echo ' Есть интернет';
                }
                $not_first = TRUE;
            }
            if ($this->furniture[$i] != 0) {
                if ($not_first) {
                    echo ', мебель';
                } else {
                    echo ' Есть мебель';
                }
                $not_first = TRUE;
            }
            if ($this->fridge[$i] != 0) {
                if ($not_first) {
                    echo ', холодильник';
                } else {
                    echo ' Есть холодильник';
                }
                $not_first = TRUE;
            }
            if ($this->washing[$i] != 0) {
                if ($not_first) {
                    echo ', стиральная машина';
                } else {
                    echo ' Есть стиральная машина';
                }
                $not_first = TRUE;
            }
            if ($this->microwave[$i] != 0) {
                if ($not_first) {
                    echo ', микроволновая печь';
                } else {
                    echo ' Есть микроволновая печь';
                }
                $not_first = TRUE;
            }
            if ($this->tv[$i] != 0) {
                if ($not_first) {
                    echo ', телевизор';
                } else {
                    echo ' Есть телевизор';
                }
                $not_first = TRUE;
            }
            if ($this->ctv[$i] != 0) {
                if ($not_first) {
                    echo ', кабельное телевидение';
                } else {
                    echo ' Есть кабельное телевидение';
                }
                $not_first = TRUE;
            }
            if ($this->stove[$i] != 0) {
                if ($not_first) {
                    echo ', кухонная плита';
                } else {
                    echo ' Есть кухонная плита';
                }
                $not_first = TRUE;
            }
            if ($this->plastic_windows[$i] != 0) {
                if ($not_first) {
                    echo ', пластиковые окна';
                } else {
                    echo ' Есть пластиковые окна';
                }
                $not_first = TRUE;
            }
            if ($not_first) {
                echo '.';
            }
            //Коммунальные услуги
            if ($this->utilities[$i] != 0) {
                switch ($this->utilities[$i]) {
                    case 1: echo ' Коммунальные услуги <u>входят в цену</u>.';
                        break;
                    case 2: echo ' Коммунальные услуги <u>не входят в цену</u>.';
                        break;
                }
            }
            if (mb_strlen($this->description[$i], 'UTF8') > 100) {
                $this->description[$i] = mb_substr($this->description[$i], 0, 100, 'utf-8') . '...';
            }
            echo '</p><p>' . $this->description[$i] . '</p>';

            /* echo '<p class="green_text">Цена:';
              echo ' <b>' . $this->price[$i] . ' т.р. ';
              if ($this->deal_type[$i] == 2) {
              echo ' / месяц ';
              }
              echo $price_for_m . ' </b>';
              if ($this->quickly[$i] == 1) {
              echo ' Срочно.';
              }
              if ($this->merch[$i] == 1) {
              echo ' Возможен торг.';
              }
              if ($this->credit[$i] == 1) {
              echo ' Ипотека.';
              }
              if ($this->documents[$i] == 1) {
              echo ' Документы готовы.';
              }
              if ($this->privat[$i] == 1) {
              echo ' Приватизирована.';
              }
              if ($this->exchange[$i] == 1) {
              echo ' Обмен.';
              }
              if ($this->owned[$i] == 1) {
              echo ' В собственности.';
              }
              if ($this->contacts[$i] != "") {
              echo '<br>Контакты: ' . ' <b>' . $this->contact_name[$i] . "&nbsp;&nbsp;&nbsp;" . $this->contacts[$i] . '</b>' . ';';
              }
              echo '</p>'; */

            echo '</div></div><div>
                <div class="img_obiavlenie">';
            /*
            echo '<p class="all_p"><a class="down_1" title="Поднять объявление" href="?AdsID=' . $this->id[$i] . '">Поднять</a>
                <!--<a class="map_prev" href="../map/?fid=map_buildings.' . $this->id_on_map[$i] . '"><img class="kompas_map" src="../images/map_1.png" title="Показать на карте" alt=""></a>-->
                    </p>';
            */
            echo '<p class="all_p_options"><a class="down_1" title="Поднять объявление" onclick="$(this).closest(\'p\').next(\'div\').slideToggle(500);">Опции</a></p>';
            echo '
            <div style="position: absolute; z-index: 1000; display: none; border: 1px solid black; background: white;">
            <a class="down_1_small" href="../payment.php?pay&realty&action=3&id=' . $this->id[$i] . '">Поднять и выделить цветом</a><br>
            <a class="down_1_small" href="../payment.php?pay&realty&action=1&id=' . $this->id[$i] . '">Закрепить в VIP</a><br>
            <a class="down_1_small" href="../payment.php?pay&realty&action=2&id=' . $this->id[$i] . '">Закрепить</a>
            </div>
            ';

            echo '</div>
                <div class="obiavlenie_inline">
                    <div class="text_obiavlenie">';
            /* <table>
              <tr style="line-height: 0.8em;">
              <td><p class="green_text">Цена:</p></td>
              <td><p class="green_text"><b>89698 т.р. (1164.91 за кв.м). </b></p></td>
              </tr>
              <tr style="line-height: 0.8em;">
              <td><p class="green_text">Контакты:</p></td>
              <td><p class="green_text"><b>Денис   343434;</b></p></td>
              </tr>
              </table> */
            echo '<p class="green_text">Цена:<span>';
            echo ' <b>' . $this->price[$i] . ' т.р. ';
            if ($this->deal_type[$i] == 2) {
                echo ' / месяц ';
            }
            echo $price_for_m . ' </b></span>';
            if ($this->quickly[$i] == 1) {
                echo ' Срочно.';
            }
            if ($this->merch[$i] == 1) {
                echo ' Возможен торг.';
            }
            if ($this->credit[$i] == 1) {
                echo ' Ипотека.';
            }
            if ($this->documents[$i] == 1) {
                echo ' Документы готовы.';
            }
            if ($this->privat[$i] == 1) {
                echo ' Приватизирована.';
            }
            if ($this->exchange[$i] == 1) {
                echo ' Обмен.';
            }
            if ($this->owned[$i] == 1) {
                echo ' В собственности.';
            }
            if ($this->contacts[$i] != "") {
                echo '<br>Контакты: ' . ' <b>' . $this->contact_name[$i] . "&nbsp;&nbsp;&nbsp;" . $this->contacts[$i] . '</b>' . ';';
            }
            echo '</p>';
            echo '</div></div></div></div>';
        }
    }

    function GenerateTableInside($num) {
        if (empty($num) || $num == 0 || $num > count($this->id)) {
            $num = count($this->id);
        }
        for ($i = 0; $i < $num; $i++) {
            //Чередуем цвета
            if ($i % 2 == 0) {
                echo '<div class="obiavlenie">';
            } else {
                echo '<div class="obiavlenie_1">';
            }
            echo '<div class="img_obiavlenie">';
            //Есть или нет изображение
            if (!file_exists('../admin/' . $this->photo_url[$i]) || !$this->photo_url[$i]) {
                echo '<a href="./?ShowParam=20&id=' . $this->id[$i] . '"><img class="img_obiavlenie_1" src="../images/noimage.png" alt=""></a>';
                echo '<p class="bottom_text_1">' . $this->registration_date[$i] . '</p>';
            } else {
                echo '<a href="./?ShowParam=20&id=' . $this->id[$i] . '"><img class="img_obiavlenie_1" src="../admin/' . $this->photo_url[$i] . '" alt=""></a>
                    <p class="bottom_text_1">' . $this->registration_date[$i] . '</p>';
            }
            if ($this->owner[$i] == 1) {
                echo '<a class="bottom_text_2" href="./?ShowParam=21&Aid=' . $this->user[$i] . '">Агентство: ' . $this->agent_name[$i] . '</a>';
            }
            if ($this->owner[$i] == 2) {
                echo '<span class="bottom_text_2">Строитель: ' . $this->user_string[$i] . '</span>';
            }
            if ($this->owner[$i] == 3) {
                echo '<span class="bottom_text_2">Посредник: ' . $this->user_string[$i] . '</span>';
            }
            if ($this->owner[$i] == 4) {
                echo '<span class="bottom_text_2">Собственник: ' . $this->user_string[$i] . '</span>';
            }
            /* echo '<p class="all_p"><a class="down_1" title="Поднять объявление" href="?AdsID=' . $this->id[$i] . '">Поднять</a>
              <a class="map_prev" href="../map/?fid=map_buildings.' . $this->id_on_map[$i] . '"><img class="kompas_map" src="../images/map_1.png" title="Показать на карте" alt=""></a>
              </p>'; */
            echo '</div><div class="obiavlenie_inline">';
            /* echo '<div class="obiavlenie_bottom_text">
              <span class="bottom_text_1">' . $this->registration_date[$i] . '</span>';
              echo '<a class="bottom_text_3" href="../map/?fid=map_buildings.' . $this->id_on_map[$i] . '"><img class="kompas_map" src="../images/map_1.png" title="Показать на карте" alt=""></a>
              <a class="map_open_karta">
              <img class="photo_map" src="../images/photo_1.png" onmouseover="ShowPhoto(this);" onmouseout="HidePhoto(this);" alt="' . $this->id[$i] . '">
              </a></div>'; */
            echo '<div class="name_obiavlenie">';
            if ($this->deal_type[$i] == 1) {
                echo '<span>Продам:</span>';
            } else {
                echo '<span>Сдам:</span>';
            }
            //Подкатегории
            echo '<a class="name_o_1" href="./?ShowParam=20&id=' . $this->id[$i] . '">';
            if ($this->subcategory[$i] == 1 || $this->subcategory[$i] == 6) {
                if ($this->rooms[$i] != 0) {
                    echo $this->rooms[$i] . '-комнатную Квартиру ';
                } else {
                    echo 'Квартиру ';
                }
                if ($this->immo_type[$i] != 0) {
                    echo '(' . $this->immo_type_string[$i] . ')';
                }
            }
            if ($this->subcategory[$i] == 2 || $this->subcategory[$i] == 7) {
                if ($this->rooms[$i] != 0) {
                    echo $this->rooms[$i] . '-комнатный Дом ';
                } else {
                    echo 'Дом ';
                }
                if ($this->immo_type[$i] != 0) {
                    echo ' (' . $this->immo_type_string[$i] . ')';
                }
            }
            $sub_not = array(1, 2, 6, 7);
            if (!in_array($this->subcategory[$i], $sub_not)) {
                if ($this->immo_type[$i] != 0) {
                    echo $this->immo_type_string[$i];
                }
            }
            echo '</a>';
            //ЗДЕСЬ ТЕКСТ
            echo '</div>
                <div class="text_obiavlenie"><p>';
            //Район
            switch ($this->district_id[$i]) {
                case 1: echo 'в Кировском районе города Томска';
                    break;
                case 2: echo 'в Ленинском районе города Томска';
                    break;
                case 3: echo 'в Октябрьском районе города Томска';
                    break;
                case 4: echo 'в Советском районе города Томска';
                    break;
                case 5: echo 'в районе Аэропорт города Томска';
                    break;
            }
            //Адрес
            echo ' по адресу ' . '<a class="bottom_text_4" href="../map/?f=' . $this->address_string[$i] . '" title="Показать на карте">' . $this->address_string[$i] . '</a>';
            //Этаж и материал
            $material = '';
            if ($this->material[$i] != 0) {
                $material = mb_strtolower(dropBackWords($this->material_string[$i]), 'UTF-8');
            }
            if ($this->subcategory[$i] != 4 && $this->subcategory[$i] != 9) {
                if ($this->floor[$i] != 0 && $this->floor_all[$i] != 0) {
                    echo ' на ' . $this->floor[$i] . '-м этаже ' . $this->floor_all[$i] . '-этажного';
                }
                if ($this->floor[$i] == 0 && $this->floor_all[$i] != 0) {
                    echo $this->floor_all[$i] . '-этажного';
                }
                if ($this->floor[$i] != 0 && $this->floor_all[$i] == 0) {
                    echo ' на ' . $this->floor[$i] . '-м этаже';
                }
                if (!empty($material)) {
                    echo ' ' . $material . ' дома';
                }
            } else {
                if ($this->floor[$i] != 0) {
                    echo ' на ' . $this->floor[$i] . '-ом этаже (всего ' . $this->floor_all[$i] . ') ';
                } else {
                    
                }
            }
            //Вторичное
            if ($this->new[$i] == 2) {
                echo ' (вторичное)';
            }
            //Площади
            $price_for_m = '';
            if ($this->area_all[$i] != 0) {
                echo ', общей площадью ' . $this->area_all[$i] . ' кв.м';
                if ($this->deal_type[$i] == 1) {
                    $price_for_m .= ' (' . (round($this->price[$i] / $this->area_all[$i], 2)) . ' за кв.м).';
                }
            }
            echo '.';
            //Отделка
            if ($this->eq[$i] != 0) {
                echo ' ' . $this->eq_string[$i] . '.';
            }
            if ($this->area_live[$i] != 0) {
                echo ' Жилая площадь ' . $this->area_live[$i] . ' кв.м.';
            }
            if ($this->area_kitchen[$i] != 0) {
                echo ' Площадь кухни ' . $this->area_kitchen[$i] . ' кв.м.';
            }
            if ($this->area_land[$i] != 0) {
                echo ' Площадь участка ' . $this->area_land[$i] . ' соток.';
            }
            //Всякие галочки и прочее
            if ($this->san[$i] != 0) {
                switch ($this->san[$i]) {
                    case 1: echo ' Санузел совмещён.';
                        break;
                    case 2: echo ' Раздельный санузел.';
                        break;
                }
            }
            $balcony_gl = '';
            if ($this->balcony[$i] != 0) {
                switch ($this->balcony[$i]) {
                    case 1: if ($this->balcony_gl[$i] != 0) {
                            $balcony_gl = ' (застеклён)';
                        }
                        echo ' Есть балкон' . $balcony_gl . '.';
                        break;
                    case 2: if ($this->balcony_gl[$i] != 0) {
                            $balcony_gl = ' (застеклена)';
                        }
                        echo ' Есть лоджия' . $balcony_gl . '.';
                        break;
                    case 3: echo ' Нет балкона.';
                        break;
                }
            }
            $not_first = FALSE;
            if ($this->phone_stat[$i] != 0) {
                echo ' Есть телефон';
                $not_first = TRUE;
            }
            if ($this->security[$i] != 0) {
                if ($not_first) {
                    echo ', охрана';
                } else {
                    echo ' Есть телефон';
                }
                $not_first = TRUE;
            }
            if ($this->internet[$i] != 0) {
                if ($not_first) {
                    echo ', интернет';
                } else {
                    echo ' Есть интернет';
                }
                $not_first = TRUE;
            }
            if ($this->furniture[$i] != 0) {
                if ($not_first) {
                    echo ', мебель';
                } else {
                    echo ' Есть мебель';
                }
                $not_first = TRUE;
            }
            if ($this->fridge[$i] != 0) {
                if ($not_first) {
                    echo ', холодильник';
                } else {
                    echo ' Есть холодильник';
                }
                $not_first = TRUE;
            }
            if ($this->washing[$i] != 0) {
                if ($not_first) {
                    echo ', стиральная машина';
                } else {
                    echo ' Есть стиральная машина';
                }
                $not_first = TRUE;
            }
            if ($this->microwave[$i] != 0) {
                if ($not_first) {
                    echo ', микроволновая печь';
                } else {
                    echo ' Есть микроволновая печь';
                }
                $not_first = TRUE;
            }
            if ($this->tv[$i] != 0) {
                if ($not_first) {
                    echo ', телевизор';
                } else {
                    echo ' Есть телевизор';
                }
                $not_first = TRUE;
            }
            if ($this->ctv[$i] != 0) {
                if ($not_first) {
                    echo ', кабельное телевидение';
                } else {
                    echo ' Есть кабельное телевидение';
                }
                $not_first = TRUE;
            }
            if ($this->stove[$i] != 0) {
                if ($not_first) {
                    echo ', кухонная плита';
                } else {
                    echo ' Есть кухонная плита';
                }
                $not_first = TRUE;
            }
            if ($this->plastic_windows[$i] != 0) {
                if ($not_first) {
                    echo ', пластиковые окна';
                } else {
                    echo ' Есть пластиковые окна';
                }
                $not_first = TRUE;
            }
            if ($not_first) {
                echo '.';
            }
            //Коммунальные услуги
            if ($this->utilities[$i] != 0) {
                switch ($this->utilities[$i]) {
                    case 1: echo ' Коммунальные услуги <u>входят в цену</u>.';
                        break;
                    case 2: echo ' Коммунальные услуги <u>не входят в цену</u>.';
                        break;
                }
            }
            echo '</p><p>' . $this->description[$i] . '</p>';

            /* echo '<p class="green_text">Цена:';
              echo ' <b>' . $this->price[$i] . ' т.р. ';
              if ($this->deal_type[$i] == 2) {
              echo ' / месяц ';
              }
              echo $price_for_m . ' </b>';
              if ($this->quickly[$i] == 1) {
              echo ' Срочно.';
              }
              if ($this->merch[$i] == 1) {
              echo ' Возможен торг.';
              }
              if ($this->credit[$i] == 1) {
              echo ' Ипотека.';
              }
              if ($this->documents[$i] == 1) {
              echo ' Документы готовы.';
              }
              if ($this->privat[$i] == 1) {
              echo ' Приватизирована.';
              }
              if ($this->exchange[$i] == 1) {
              echo ' Обмен.';
              }
              if ($this->owned[$i] == 1) {
              echo ' В собственности.';
              }
              if ($this->contacts[$i] != "") {
              echo '<br>Контакты: ' . ' <b>' . $this->contact_name[$i] . "&nbsp;&nbsp;&nbsp;" . $this->contacts[$i] . '</b>' . ';';
              }
              echo '</p>'; */

            echo '</div></div><div>
                <div class="img_obiavlenie">';
            echo '<p class="all_p"><a class="down_1" title="Опции">Опции</a>
                <!--<a class="map_prev" href="../map/?f=' . $this->address_string[$i] . '"><img class="kompas_map" src="../images/map_1.png" title="Показать на карте" alt=""></a>-->
                    </p>';
            echo '</div>
                <div class="obiavlenie_inline">
                    <div class="text_obiavlenie">';
            /* <table>
              <tr style="line-height: 0.8em;">
              <td><p class="green_text">Цена:</p></td>
              <td><p class="green_text"><b>89698 т.р. (1164.91 за кв.м). </b></p></td>
              </tr>
              <tr style="line-height: 0.8em;">
              <td><p class="green_text">Контакты:</p></td>
              <td><p class="green_text"><b>Денис   343434;</b></p></td>
              </tr>
              </table> */
            echo '<p class="green_text">Цена:<span>';
            echo ' <b>' . $this->price[$i] . ' т.р. ';
            if ($this->deal_type[$i] == 2) {
                echo ' / месяц ';
            }
            echo $price_for_m . ' </b></span>';
            if ($this->quickly[$i] == 1) {
                echo ' Срочно.';
            }
            if ($this->merch[$i] == 1) {
                echo ' Возможен торг.';
            }
            if ($this->credit[$i] == 1) {
                echo ' Ипотека.';
            }
            if ($this->documents[$i] == 1) {
                echo ' Документы готовы.';
            }
            if ($this->privat[$i] == 1) {
                echo ' Приватизирована.';
            }
            if ($this->exchange[$i] == 1) {
                echo ' Обмен.';
            }
            if ($this->owned[$i] == 1) {
                echo ' В собственности.';
            }
            if ($this->contacts[$i] != "") {
                echo '<br>Контакты: ' . ' <b>' . $this->contact_name[$i] . "&nbsp;&nbsp;&nbsp;" . $this->contacts[$i] . '</b>' . ';';
            }
            echo '</p>';
            echo '</div></div></div></div>';
        }
    }

    /**
     * Генерирует навигацию по страницам объявлений
     * @param int $page Номер открываемой страницы
     * @param int $limit Сколько объявлений на странице
     * @param String $where WHERE в SQL запросе, чтобы определить число страниц
     * @param String $new_url Изменение ссылки страницы в зависимости от дополнительных параметров
     */
    function GenerateNavigation($page, $limit, $where, $new_url) {
        $page = filter_var($page, FILTER_VALIDATE_INT);
        $limit = filter_var($limit, FILTER_VALIDATE_INT, array("options" => array("min_range" => 0, "max_range" => 50)));
        $where = filter_var($where, FILTER_SANITIZE_STRIPPED);
        $new_url = filter_var($new_url, FILTER_SANITIZE_STRIPPED);
        $query = 'SELECT k_isf_id
            FROM k_immovables_sell as ise
            LEFT JOIN k_immovables_subcategories as isub ON (isub.k_is_id = ise.k_isf_subcategory)
            LEFT JOIN k_immovables_owners as iow ON (iow.k_io_id = ise.k_isf_owner)
            LEFT JOIN k_immovables_sell_types as ist ON (ist.k_isft_id = ise.k_isf_immovable_type)
            LEFT JOIN k_immovables_sell_material as kism ON (kism.k_isfm_id = ise.k_isf_material)
            LEFT JOIN k_immovables_sell_eq as kise ON (kise.k_isfe_id = ise.k_isf_eq)
            LEFT JOIN k_streets_house_nums as kshn ON (kshn.k_shn_id = ise.k_isf_address)
            LEFT JOIN k_streets as kstre ON (kstre.k_s_id = kshn.k_shn_street_id)
            LEFT JOIN k_districts as kdis ON (kdis.k_d_id = kshn.k_shn_district_id)
            LEFT JOIN k_towns as kto ON (kdis.k_d_town = kto.k_t_id)
            LEFT JOIN k_users as kuser ON (kuser.k_ku_id = ise.k_isf_user_id)
            LEFT JOIN k_immovables_photos as kiph ON (kiph.k_ip_immo_id = ise.k_isf_id)
            LEFT JOIN k_immovables_special as kisp ON (kisp.k_is_immovable_id = ise.k_isf_id)
            LEFT JOIN k_users_agents as kuag ON (kuag.k_ua_user_parent = kuser.k_ku_id)
            WHERE (k_isf_state=1 AND k_isf_end_date > NOW()) ' . $where . '
            GROUP BY k_isf_id';
        //echo '<br>'.$query.'<br>';
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare($query);
            $queue->execute();
            $row = $queue->rowCount();
        } catch (PDOException $e) {
            exit();
        }
        $pages = intval($row / $limit);
        if ($row % $limit != 0) {
            $pages++;
        }
        if ($pages > 1) {
            echo '<div class="listing">';
            if ($page == $pages || $page > 1) {
                echo '<a class="style_listing" href="./?PageIndex=' . ($page - 1) . '&' . $new_url . '">Предыдущая</a>';
            }

            if ($pages <= 11) {
                for ($i = 1; $i <= $pages; $i++) {
                    if ($i == $page) {
                        echo '<a class="active_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                    } else {
                        echo '<a class="style_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                    }
                }
            } else {
                if ($page <= 6) {
                    for ($i = 1; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #000000; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                    }
                }
                if ($page > 6 && $page <= ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #000000; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $page - 2; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #000000; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                    }
                }
                if ($page > ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #000000; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 5; $i <= $pages; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                        }
                    }
                }
            }

            if ($page == 1 || $page < $pages) {
                echo '<a class="style_listing" href="./?PageIndex=' . ($page + 1) . '&' . $new_url . '">Следующая</a>';
            }

            echo '</div>';
        }
    }

    function GenerateAd($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        $one_ad = new Ads();
        $one_ad->LoadAds(0, 0, '', $id);
        $id = 0;
        $one_ad->views[$id]++;
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare('UPDATE k_immovables_sell SET k_isf_views=:views WHERE k_isf_id=:id');
            $queue->execute(array(':views' => $one_ad->views[$id], ':id' => $one_ad->id[$id]));
        } catch (PDOException $e) {
            exit('');
        }
        echo '<div id="f_20" class="block_content_1">';
        if (count($one_ad->id) == 0) {
            echo 'Объявление не найдено!</div>';
        } else {
            /* if ($one_ad->deal_type[$id] == 1) {
              echo '<span class="style_shapka_1_spec">Продам:</span> ';
              } else {
              echo '<span class="style_shapka_1_spec">Сдам:</span> ';
              }
              echo '<b><span class="style_shapka_1_spec">';
              //Подкатегории
              if ($one_ad->subcategory[$id] == 1 || $one_ad->subcategory[$id] == 6) {
              if ($one_ad->rooms[$id] != 0) {
              echo $one_ad->rooms[$id] . '-комнатную Квартиру ';
              } else {
              echo 'Квартиру ';
              }
              if ($one_ad->immo_type[$id] != 0) {
              echo '(' . $one_ad->immo_type_string[$id] . ')';
              }
              }
              if ($one_ad->subcategory[$id] == 2 || $one_ad->subcategory[$id] == 7) {
              if ($one_ad->rooms[$id] != 0) {
              echo $one_ad->rooms[$id] . '-комнатный Дом ';
              } else {
              echo 'Дом ';
              }
              if ($one_ad->immo_type[$id] != 0) {
              echo ' (' . $one_ad->immo_type_string[$id] . ')';
              }
              }
              $sub_not = array(1, 2, 6, 7);
              if (!in_array($one_ad->subcategory[$id], $sub_not)) {
              if ($one_ad->immo_type[$id] != 0) {
              echo $one_ad->immo_type_string[$id];
              }
              }
              echo '</span></b>';
              if ($one_ad->new[$id] == 1) {
              echo '<span class="style_tit">Новостройка</span>';
              }
              if ($one_ad->new[$id] == 2) {
              echo '<span class="style_tit">Вторичное</span>';
              } */
            echo '<span class="nambe_open">Объявление № ' . $one_ad->id[$id] . '</span>';
            $this->GenerateTableInside(0);
            try {
                $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
                $mysql->exec('set names utf8');
                $queue = $mysql->prepare('SELECT * FROM k_immovables_photos WHERE k_ip_immo_id=:id');
                $queue->execute(array(':id' => $one_ad->id[$id]));
                $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                exit('');
            }
            $img_urls = array();
            if (count($result) > 0) {
                echo '<div class="opisanie_element_1"><div class="n_block"><p>Дополнительные фото</p></div>';
                $n = 1;
                foreach ($result as $row) {
                    if (strpos($row['k_ip_url'], 'video') === FALSE) {
                        echo '<img class="img_element_1" src="../admin/' . $row['k_ip_url'] . '" alt="" title="Смотреть фотографии" onclick="ShowImage(' . $n . ');">';
                        array_push($img_urls, $row['k_ip_url']);
                        $n++;
                    }
                    /*
                    echo '<img class="img_element_1" src="../admin/' . $row['k_ip_url'] . '" alt="" title="Смотреть фотографии" onclick="ShowImage(' . $n . ');">';
                    array_push($img_urls, $row['k_ip_url']);
                    $n++;
                    */
                }
                echo '</div>';
            }
            echo '<div class="opisanie_element">
            <table class="bl_tab"><tr><td class="dop_info_bl">
            <div class="n_block"><p>Дополнительная информация</p></div>
            <table class="opisanie_table">
            <tr>
            <td class="left_td_op"><p>Тип</p></td>
            <td><span>' . $one_ad->immo_type_string[$id] . '</span></td>
            </tr>
            <tr>
            <td class="left_td_op"><p>Улица</p></td>
            <td><span>' . $one_ad->address_string[$id] . '</span>
            <!--УБРАНО ПО ДИЗАЙНУ <a class="spec_style_2" href="../map/?fid=map_buildings.' . $one_ad->id_on_map[$id] . '" title="Показать на карте">' . $one_ad->address_string[$id] . '</a>
            <a class="map_open_karta_2" title="Фото с карты">
            <img class="photo_map" src="../images/photo_1.png" alt="">
            <img class="map_img_obiavlenie_karta_2" src="images/karta_1.png" alt="">
            </a>УБРАНО ПО ДИЗАЙНУ-->            
            </td>
            </tr>
            <tr>
            <td class="left_td_op"><p>Район</p></td>
            <td><span>' . $one_ad->district[$id] . ' район</span></td>
            </tr>';
            if ($one_ad->eq[$id] != 0) {
                echo '<tr>
                <td><p>Отделка</p></td>
                <td><span>' . $one_ad->eq_string[$id] . '</span></td>
                </tr>';
            }
            if ($one_ad->area_all[$id] != 0) {
                echo '<tr>
                <td class="left_td_op"><p>Общая площадь</p></td>
                <td><span>' . $one_ad->area_all[$id] . ' кв/м</span></td>
                </tr>';
            }
            if ($one_ad->area_live[$id] != 0) {
                echo '<tr>
                <td class="left_td_op"><p>Жилая площадь</p></td>
                <td><span>' . $one_ad->area_live[$id] . ' кв/м</span></td>
                </tr>';
            }
            if ($one_ad->area_kitchen[$id] != 0) {
                echo '<tr>
                <td class="left_td_op"><p>Площадь кухни</p></td>
                <td><span>' . $one_ad->area_kitchen[$id] . ' кв/м</span></td>
                </tr>';
            }
            if ($one_ad->area_land[$id] != 0) {
                echo '<tr>
                <td class="left_td_op"><p>Площадь участка</p></td>
                <td><span>' . $one_ad->area_land[$id] . ' сот.</span></td>
                </tr>';
            }
            if ($one_ad->floor[$id] != 0 || $one_ad->floor_all[$id] != 0) {
                echo '<tr>
                <td class="left_td_op"><p>Этажность</p></td>
                <td><span>';
                if ($one_ad->floor[$id] != 0) {
                    echo $one_ad->floor[$id];
                }
                echo '/';
                if ($one_ad->floor_all[$id] != 0) {
                    echo $one_ad->floor_all[$id];
                }
                echo '</span></td>
                </tr>';
            }
            if ($one_ad->material[$id] != 0) {
                echo '<tr>
                <td class="left_td_op"><p>Материал</p></td>
                <td><span>' . $one_ad->material_string[$id] . '</span></td>
                </tr>';
            }
            if ($one_ad->san[$id] != 0) {
                echo '<tr>
                <td class="left_td_op"><p>Санузел</p></td>
                <td><span>';
                if ($one_ad->san[$id] == 1) {
                    echo 'Совмещённый';
                }
                if ($one_ad->san[$id] == 2) {
                    echo 'Раздельный';
                }
                echo '</span></td>
                </tr>';
            }
            if ($one_ad->balcony[$id] != 0) {
                echo '<tr>
                <td class="left_td_op"><p>Балкон</p></td>
                <td><span>';
                switch ($one_ad->balcony[$id]) {
                    case 1: echo 'Есть';
                        break;
                    case 2: echo 'Лоджия';
                        break;
                    case 3: echo 'Нет';
                        break;
                }
                if ($one_ad->balcony_gl[$id] == 1) {
                    echo ' (застелкён)';
                }
                echo '</span></td>
                </tr>';
            }
            if ($one_ad->deal_type[$id] == 2) {
                echo '<tr>
            <td class="left_td_op"><p>Удобства</p></td>
            <td style="text-align: left;">';
                if ($one_ad->phone_stat[$id] != 0) {
                    echo '<span>Телефон</span><br>';
                }
                if ($one_ad->security[$id] != 0) {
                    echo '<span>Охрана</span><br>';
                }
                if ($one_ad->internet[$id] != 0) {
                    echo '<span>Интернет</span><br>';
                }
                if ($one_ad->furniture[$id] != 0) {
                    echo '<span>Мебель</span><br>';
                }
                if ($one_ad->fridge[$id] != 0) {
                    echo '<span>Холодильник</span><br>';
                }
                if ($one_ad->washing[$id] != 0) {
                    echo '<span>Стиральная машина</span><br>';
                }
                if ($one_ad->microwave[$id] != 0) {
                    echo '<span>Микроволновая печь</span><br>';
                }
                if ($one_ad->tv[$id] != 0) {
                    echo '<span>Телевизор</span><br>';
                }
                if ($one_ad->ctv[$id] != 0) {
                    echo '<span>Кабельное телевидение</span><br>';
                }
                if ($one_ad->stove[$id] != 0) {
                    echo '<span>Кухонная плита</span><br>';
                }
                if ($one_ad->plastic_windows[$id] != 0) {
                    echo '<span>Пластиковые окна</span><br>';
                }
                echo '</td></tr>';
                if ($one_ad->utilities[$id] != 0) {
                    echo '<tr>
                <td class="left_td_op"><p>Коммунальные услуги</p></td>
                <td><span>';
                    if ($one_ad->utilities[$id] == 1) {
                        echo 'Входят в стоимость';
                    }
                    if ($one_ad->utilities[$id] == 2) {
                        echo 'Не входят в стоимость';
                    }
                    echo '</span></td>
                </tr>';
                }
            }
            echo '<tr>
            <td class="left_td_op"><p>Дополнительно</p></td>
            <td style="text-align: left;">';
            if ($one_ad->quickly[$id] != 0) {
                echo '<span>Срочно</span><br>';
            }
            if ($one_ad->exchange[$id] != 0) {
                echo '<span>Обмен</span><br>';
            }
            if ($one_ad->credit[$id] != 0) {
                echo '<span>Ипотека</span><br>';
            }
            if ($one_ad->privat[$id] != 0) {
                echo '<span>Приватизирована</span><br>';
            }
            if ($one_ad->documents[$id] != 0) {
                echo '<span>Документы готовы</span><br>';
            }
            if ($one_ad->merch[$id] != 0) {
                echo '<span>Возможен торг</span><br>';
            }
            if ($one_ad->owned[$id] != 0) {
                echo '<span>В собственности</span><br>';
            }
            echo '</span></td>
                </tr>';
            if ($one_ad->description[$id] != '') {
                echo '</table></td><td class="ot_td"></td>
                    <td class="contacts_block">
                        <div class="n_block"><p>Контакты</p></div>
                        <!--ВСТАВИЛ КОНТАКТЫ НАЧАЛО-->
                            <table class="opisanie_table">
                            <tr>
                            <td class="left_td_op"><p>Контактное лицо:</p></td>
                            <td><span>' . $one_ad->contact_name[$id] . '</span></td>
                            </tr>
                            <tr>
                            <td class="left_td_op"><p>Телефон:</p></td>
                            <td><span>' . $one_ad->contacts[$id] . '</span></td>
                            </tr>
                            <tr>
                            <td class="left_td_op"><p>Посмотреть № Объявления:</p></td>
                            <td><span>' . $one_ad->id[$id] . '</span></td>
                            </tr>
                            <tr>
                            <td class="left_td_op"><p>Разместил:</p></td>
                            <td><span>';
                if ($one_ad->owner[$id] == 1) {
                    echo $one_ad->agent_name[$id];
                } else {
                    echo $one_ad->user_string[$id];
                }
                echo '</span></td>
                            </tr>
                            <tr>
                            <td class="left_td_op"><p>Добавлено:</p></td>
                            <td><span>' . $one_ad->registration_date[$id] . '</span></td>
                            </tr>
                            <tr>
                            <td class="left_td_op"><p>Опубликовано:</p></td>
                            <td><span>' . $one_ad->registration_date[$id] . '</span></td>
                            </tr>
                            <tr>
                            <td class="left_td_op"><p>Истекает:</p></td>
                            <td><span>' . $one_ad->end_date[$id] . '</span></td>
                            </tr>
                            <tr>
                            <td class="left_td_op"><p>Просмотров:</p></td>
                            <td><span>' . $one_ad->views[$id] . '</span></td>
                            </tr>
                            <tr>
                            <td class="left_td_op"><p>Статус:</p></td>
                            <td><span>Опубликовано</span></td>
                            </tr>
                            </table>
                        <!--ВСТАВИЛ КОНТАКТЫ КОНЕЦ-->
                    </td></tr></table></div>';
            }
            echo '</div>';

            $mysql = NULL;
            $where = ' AND (k_isf_price BETWEEN ' . ($one_ad->price[$id] * 0.9) . ' AND ' . ($one_ad->price[$id] * 1.1) . ') AND k_isf_subcategory = ' . $one_ad->subcategory[$id] . ' AND k_isf_id != ' . $one_ad->id[$id] . ' ';
            $same_price = new Ads();
            $same_price->LoadAds(0, 0, $where, 0);
            echo '<div class="variant_cena">
                <span>
                <a class="variant_1" href="./?ShowParam=1&SamePrice=' . $one_ad->id[$id] . '">Варианты: <b>за эту же цену</b></a>
                <a class="variant_2">' . count($same_price->id) . '</a>
                </span>
                </div>';
            echo '<div style="width: auto; margin-left: 35px;">';
            $same_price->GenerateTable(4);
            echo '</div>';

            if (count($img_urls) > 0) {
                echo '<div id="wind_poto_nedvigimost" class="wind_nedvigimost">
            <a class="close_2" onclick="CloseWindow(\'wind_poto_nedvigimost\');">X</a>
            <div class="block_listing">';
                for ($i = 0; $i < count($img_urls); $i++) {
                    //echo '<img id="photo_nedvigimost_' . ($i + 1) . '" class="im_2" src="../admin/' . $img_urls[$i] . '">';
                    if (strpos($img_urls[$i], 'video') === FALSE) {
                        echo '<img id="photo_nedvigimost_' . ($i + 1) . '" class="im_2" src="../admin/' . $img_urls[$i] . '">';
                    } else {
                        echo '<img id="photo_nedvigimost_' . ($i + 1) . '" class="im_2" src="../' . $img_urls[$i] . '">';
                    }

                }
                echo '<input type="hidden" id="count_im" value="1">
            </div>
            <div class="listings">';
                echo '<a onclick="prevImage2(' . count($img_urls) . ');"><img class="left_list" src="images/left_4.png" alt="Листать влево"></a>';
                for ($i = 0; $i < count($img_urls); $i++) {
                    echo '<a  onclick="changeImage2(' . ($i + 1) . ');"><img class="mini_listing_img" src="../admin/' . $img_urls[$i] . '"></a>';
                }
                echo '<a onclick="nextImage2(' . count($img_urls) . ');"><img class="right_list" src="images/right_4.png" alt="Листать вправо"></a></div></div>';
            }
        }
    }

    function GenerateSpecial() {
        echo '<div class="right_nedvigimost">
            <div class="kriteri_nedvigemost_spec">
            <div class="shapka_bloka_spec">
            <a href="./?ShowParam=18" class="style_shapka_1_spec">VIP предложения</a>
            <!--<p class="style_shapka_3_spec" title="Всего объявлений">' . $this->special[0] . '</p>-->
            </div>';
        $query = 'SELECT ise.*, isub.k_is_name,ist.k_isft_name,kshn.k_shn_house_num, kstre.k_s_name, kdis.k_d_name, kiph.k_ip_url
            FROM k_immovables_sell as ise
            LEFT JOIN k_immovables_subcategories as isub ON (isub.k_is_id = ise.k_isf_subcategory)
            LEFT JOIN k_immovables_sell_types as ist ON (ist.k_isft_id = ise.k_isf_immovable_type)
            LEFT JOIN k_streets_house_nums as kshn ON (kshn.k_shn_id = ise.k_isf_address)
            LEFT JOIN k_streets as kstre ON (kstre.k_s_id = kshn.k_shn_street_id)
            LEFT JOIN k_districts as kdis ON (kdis.k_d_id = kshn.k_shn_district_id)
            LEFT JOIN k_immovables_special as kisp ON (kisp.k_is_immovable_id = ise.k_isf_id)
            LEFT JOIN (SELECT * FROM k_immovables_photos ORDER BY k_ip_priority DESC) as kiph ON (kiph.k_ip_immo_id = ise.k_isf_id)
            WHERE k_isf_state=1 AND kisp.k_is_id IS NOT NULL AND k_isf_end_date > NOW()
            GROUP BY k_isf_id
            ORDER BY k_isf_registration_date DESC
            LIMIT 8';
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare($query);
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            $dumped = array();
                        $count = count($result) - 1;
                        //Только 8 объявлений
                        while (count($result) > 8) {
                            if (!in_array(rand(0, $count), $dumped)) {
                                unset($result[rand(0, $count)]);
                                array_push($dumped, rand(0, $count));
                            }
                        }
        } catch (PDOException $e) {
            exit('');
        }
        foreach ($result as $row) {
            $file_name = str_replace('images/', '../admin/images/1_', $row['k_ip_url']);
            $file_name = str_replace('video/', '../video/', $file_name);

            echo '<div class="spec_div">';
            echo '<a href="./?ShowParam=20&id=' . $row['k_isf_id'] . '">';
            if ($row['k_ip_url'] != '') {
                echo '<img class="spec_img" src="../admin/' . $file_name . '" alt="" title="">';
            } else {
                echo '<img class="spec_img" src="../images/noimage.png" alt="" title="">';
            }
            echo '</a>';
            //echo '<p class="add_elem_time">' . $row['k_isf_registration_date'] . '</p>';
            echo '<div>
                <a class="spec_style_1" href="./?ShowParam=20&id=' . $row['k_isf_id'] . '">';
            $print_str = '';
            /* if ($row['k_isf_deal_type'] == 1) {
              $print_str .= 'Продам: ';
              } else {
              $print_str .= 'Сдам: ';
              } */
            if ($row['k_isf_subcategory'] == 1 || $row['k_isf_subcategory'] == 6) {
                if ($row['k_isf_rooms'] != 0) {
                    $print_str .= $row['k_isf_rooms'] . '-комн.';
                } else {
                    $print_str .= 'Квартира ';
                }
                if ($row['k_isf_immovable_type'] != 0) {
                    //$print_str .= '(' . $row['k_isft_name'] . ')';
                }
            }
            if ($row['k_isf_subcategory'] == 2 || $row['k_isf_subcategory'] == 7) {
                if ($row['k_isf_rooms'] != 0) {
                    $print_str .= $row['k_isf_rooms'] . '-комн.';
                } else {
                    $print_str .= 'Дом ';
                }
                if ($row['k_isf_immovable_type'] != 0) {
                    //$print_str .= ' (' . $row['k_isft_name'] . ')';
                }
            }
            if ($row['k_isf_subcategory'] == 4 || $row['k_isf_subcategory'] == 9) {
                $print_str .= $row['k_isft_name'];
            }
            if ($row['k_isf_subcategory'] == 3 || $row['k_isf_subcategory'] == 8) {
                $print_str .= 'Комм. (' . $row['k_isft_name'] . ')';
            }
            if ($row['k_isf_subcategory'] == 5) {
                $print_str .= $row['k_isft_name'];
            }
            if ($row['k_s_name']) {
                $print_str .= $row['k_s_name'];
            }

            mb_internal_encoding("UTF-8");
            echo mb_substr($print_str, 0, 20, "UTF-8") . '...';
            echo '</a>
                <span class="orange_job"></span>
                <hr size="3" class="orange_hr"></td>
                </div>';
            echo '
                <p class="spec_money_text">' . $row['k_isf_price'] . ' т.р.';
            if ($row['k_isf_deal_type'] == 2) {
                echo '/месяц';
            }
            echo '</p>';
            echo '</div>';
        }
        echo '</div></div>';
    }

    function LoadDistricts() {
        $queryDis = 'SELECT k_d_name, k_d_id FROM k_districts ORDER BY k_d_name ASC';
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare($queryDis);
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit('');
        }
        $districts = array();
        $dis_id = array();
        foreach ($result as $row) {
            array_push($districts, $row['k_d_name']);
            array_push($dis_id, $row['k_d_id']);
        }
        return array($dis_id, $districts);
    }

    function LoadEQ() {
        $queryEQ = 'SELECT k_isfe_id, k_isfe_name FROM k_immovables_sell_eq ORDER BY k_isfe_name ASC';
        //echo '<br>'.$queryEQ.'<br>';

        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare($queryEQ);
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit('');
        }
        $EQ = array();
        $EQ_id = array();
        foreach ($result as $row) {
            array_push($EQ, $row['k_isfe_name']);
            array_push($EQ_id, $row['k_isfe_id']);
        }
        return array($EQ_id, $EQ);
    }

    function LoadMaterail() {
        $queryMat = 'SELECT k_isfm_id, k_isfm_name FROM k_immovables_sell_material ORDER BY k_isfm_name ASC';
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare($queryMat);
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit('');
        }
        $Mat = array();
        $Mat_id = array();
        foreach ($result as $row) {
            array_push($Mat, $row['k_isfm_name']);
            array_push($Mat_id, $row['k_isfm_id']);
        }
        return array($Mat_id, $Mat);
    }

    function LoadImmoType($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare('SELECT k_isft_id, k_isft_name FROM  k_immovables_sell_types WHERE k_isft_sub_id = :id ORDER BY k_isft_name ASC');
            $queue->execute(array(':id' => $id));
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit('');
        }
        $Immo = array();
        $Immo_id = array();
        foreach ($result as $row) {
            array_push($Immo, $row['k_isft_name']);
            array_push($Immo_id, $row['k_isft_id']);
        }
        return array($Immo_id, $Immo);
    }

}

class Statistics {

    public $statistics;

    function Statistics() {
        $query = 'SELECT k_is_id,count(k_isf_id) as how FROM k_immovables_subcategories as kis1
            LEFT JOIN k_immovables_sell as kis ON (kis.k_isf_subcategory = kis1.k_is_id AND kis.k_isf_state=1 AND k_isf_end_date > NOW())
            WHERE (k_is_parent=1 OR k_is_parent=2)
            GROUP BY k_is_id
            ORDER BY k_is_id ASC';
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare($query);
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit('');
        }
        $n = 0;
        foreach ($result as $row) {
            $this->statistics[$n] = $row['how'];
            $n++;
        }
        $this->statistics[9] = 0;
        $this->statistics[10] = 0;
        for ($i = 0; $i < 9; $i++) {
            if ($i < 5) {
                $this->statistics[9] += $this->statistics[$i];
            } else {
                $this->statistics[10] += $this->statistics[$i];
            }
        }
        $this->statistics[11] = $this->statistics[9] + $this->statistics[10];
        try {
            $queue = $mysql->prepare('SELECT count(*) as num FROM k_immovables_buy');
            $queue->execute();
            $row = $queue->fetch(PDO::FETCH_ASSOC);
            $queue = $mysql->prepare('SELECT count(*) as max FROM k_immovables_subcategories_news');
            $queue->execute();
            $row2 = $queue->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit('');
        }
        $this->statistics[12] = $row['num'];
        $this->statistics[13] = $row2['max'];
        $time = date('Y-m-d H:i:s', time() - 24 * 60 * 60);
        try {
            $queue = $mysql->prepare('SELECT count(*) as max FROM k_immovables_subcategories_news WHERE k_isn_date>:date');
            $queue->execute(array(':date' => $time));
            $row3 = $queue->fetch(PDO::FETCH_ASSOC);
            $queue = $mysql->prepare('SELECT count(*) as max FROM k_immovables_sell WHERE k_isf_registration_date>:date AND k_isf_state=1');
            $queue->execute(array(':date' => $time));
            $row4 = $queue->fetch(PDO::FETCH_ASSOC);
            $queue = $mysql->prepare('SELECT count(*) as max FROM k_immovables_buy WHERE k_ib_date>:date');
            $queue->execute(array(':date' => $time));
            $row5 = $queue->fetch(PDO::FETCH_ASSOC);
            $queue = $mysql->prepare('SELECT count(*) as max FROM k_immovables_buy WHERE k_ib_subcategory=1');
            $queue->execute();
            $row6 = $queue->fetch(PDO::FETCH_ASSOC);
            $queue = $mysql->prepare('SELECT count(*) as max FROM k_immovables_buy WHERE k_ib_subcategory=2');
            $queue->execute();
            $row7 = $queue->fetch(PDO::FETCH_ASSOC);
            $queue = $mysql->prepare('SELECT count(*) as max FROM k_immovables_buy WHERE k_ib_subcategory=3');
            $queue->execute();
            $row8 = $queue->fetch(PDO::FETCH_ASSOC);
            $queue = $mysql->prepare('SELECT count(*) as max FROM k_immovables_buy WHERE k_ib_subcategory=4');
            $queue->execute();
            $row9 = $queue->fetch(PDO::FETCH_ASSOC);
            $queue = $mysql->prepare('SELECT count(*) as max FROM k_immovables_buy WHERE k_ib_subcategory=5');
            $queue->execute();
            $row10 = $queue->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit('');
        }
        $this->statistics[15] = $row3['max'] + $row4['max'] + $row5['max'];
        $this->statistics[14] = $this->statistics[9] + $this->statistics[10] + $this->statistics[12] + $this->statistics[13];
        $this->statistics[20] = $row6['max'];
        $this->statistics[21] = $row7['max'];
        $this->statistics[22] = $row8['max'];
        $this->statistics[23] = $row9['max'];
        $this->statistics[24] = $row10['max'];
    }

    function GenerateMenu() {
        echo '<div class="kriteri_nedvigemost">
            <div class="shapka_bloka">
            <p class="style_shapka_1">Разделы</p>

            <!--<p class="style_shapka_3" title="Всего объявлений">' . $this->statistics[14] . '</p>
            <p class="style_shapka_2" title="Последние добавления за сутки">+' . $this->statistics[15] . '</p>-->
            </div>
            <div class="obveden_block" id="menu_2">
            <a class="style_menu_left" href="./?ShowParam=1">Последние объявления</a><span class="style_menu_left_3 font80">' . $this->statistics[11] . '</span><br>
            <div class="inline_menu">
            <p class="style_menu_left_1"><a href="./?ShowParam=2">ПРОДАЮ</a><span class="style_menu_left_3">' . $this->statistics[9] . '</span></p>
            <ul style="display:none">
            <li><p class="style_menu_left_2"><a href="./?ShowParam=3">Квартиры</a><span class="style_menu_left_3">' . $this->statistics[0] . '</span></p></li>
            <li><p class="style_menu_left_2"><a href="./?ShowParam=4">Дома/дачи</a><span class="style_menu_left_3">' . $this->statistics[1] . '</span></p></li>
            <li><p class="style_menu_left_2"><a href="./?ShowParam=5">Коммерческая</a><span class="style_menu_left_3">' . $this->statistics[2] . '</span></p></li>
            <li><p class="style_menu_left_2"><a href="./?ShowParam=6">Гараж/погреб</a><span class="style_menu_left_3">' . $this->statistics[3] . '</span></p></li>
            <li><p class="style_menu_left_2"><a href="./?ShowParam=7">Земля</a><span class="style_menu_left_3">' . $this->statistics[4] . '</span></p></li>
            </ul>
            </div>
            <div class="inline_menu">
            <p class="style_menu_left_1"><a href="./?ShowParam=8">СДАЮ</a><span class="style_menu_left_3">' . $this->statistics[10] . '</span></p>
            <ul style="display:none">
            <li><p class="style_menu_left_2"><a href="./?ShowParam=9">Квартиры</a><span class="style_menu_left_3">' . $this->statistics[5] . '</span></p></li>
            <li><p class="style_menu_left_2"><a href="./?ShowParam=10">Дома/дачи</a><span class="style_menu_left_3">' . $this->statistics[6] . '</span></p></li>
            <li><p class="style_menu_left_2"><a href="./?ShowParam=11">Коммерческая</a><span class="style_menu_left_3">' . $this->statistics[7] . '</span></p></li>
            <li><p class="style_menu_left_2"><a href="./?ShowParam=12">Гараж/погреб</a><span class="style_menu_left_3">' . $this->statistics[8] . '</span></p></li>
            </ul>
            </div>
            
            <div class="inline_menu">
            <p class="style_menu_left_1"><a href="./?ShowParam=13">КУПЛЮ</a><span class="style_menu_left_3">' . $this->statistics[12] . '</span></p>
            <ul style="display:none">
            <li><p class="style_menu_left_2"><a href="./?BuySubcategory[]=1">Квартиры</a><span class="style_menu_left_3">' . $this->statistics[20] . '</span></p></li>
            <li><p class="style_menu_left_2"><a href="./?BuySubcategory[]=2">Дома/дачи</a><span class="style_menu_left_3">' . $this->statistics[21] . '</span></p></li>
            <li><p class="style_menu_left_2"><a href="./?BuySubcategory[]=3">Коммерческая</a><span class="style_menu_left_3">' . $this->statistics[22] . '</span></p></li>
            <li><p class="style_menu_left_2"><a href="./?BuySubcategory[]=4">Гараж/погреб</a><span class="style_menu_left_3">' . $this->statistics[23] . '</span></p></li>
            <li><p class="style_menu_left_2"><a href="./?BuySubcategory[]=5">Земля</a><span class="style_menu_left_3">' . $this->statistics[24] . '</span></p></li>
            </ul>
            </div>

            <div class="inline_menu">
            <p class="style_menu_left_1"><a href="./?ShowParam=14">Новости</a><span class="style_menu_left_3">' . $this->statistics[13] . '</span></p>';
        $query = 'SELECT k_is_id,count(k_isn_id) as nums,k_is_name FROM k_immovables_subcategories as kis1
            LEFT JOIN k_immovables_subcategories_news as kisn ON (kisn.k_isn_parent = kis1.k_is_id)
            WHERE k_is_parent=4
            GROUP BY k_is_id
            ORDER BY k_is_name ASC';
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare($query);
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        foreach ($result as $row) {
            echo '<p class="style_menu_left_1"><a href="./?ShowParam=14&NewsSub=' . $row['k_is_id'] . '">' . $row['k_is_name'] . '</a><span class="style_menu_left_3">' . $row['nums'] . '</span></p>';
        }
        echo '</div>
</div>
                         
        </div>';
    }

}

class NewsIm {

    public $news_id = array();
    public $news_image = array();
    public $news_header = array();
    public $news_subcategory = array();
    public $news_subcategory_string = array();
    public $news_date = array();
    public $news_text = array();

    function LoadNews($limit, $page, $where) {
        $limit = filter_var($limit, FILTER_VALIDATE_INT, array("options" => array("min_range" => 0, "max_range" => 50)));
        $page = filter_var($page, FILTER_VALIDATE_INT);
        $where = filter_var($where, FILTER_SANITIZE_STRIPPED);
        $query = 'SELECT kisn.*, kis.k_is_name FROM k_immovables_subcategories_news as kisn
            LEFT JOIN k_immovables_subcategories as kis ON (kis.k_is_id = kisn.k_isn_parent)
            WHERE kis.k_is_parent=4 ' . $where;
        $query .= ' ORDER BY k_isn_date DESC';
        if ($limit != 0) {
            $query .= ' LIMIT ' . (($page - 1) * $limit) . ',' . $limit;
        }
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
            $this->news_id[$n] = $row['k_isn_id'];
            $this->news_image[$n] = $row['k_isn_image'];
            $this->news_header[$n] = $row['k_isn_header'];
            $this->news_subcategory[$n] = $row['k_isn_parent'];
            $this->news_subcategory_string[$n] = $row['k_is_name'];
            $this->news_date[$n] = $row['k_isn_date'];
            $this->news_text[$n] = $row['k_isn_text'];
            $n++;
        }
    }

    function GenerateTable() {
        for ($i = 0; $i < count($this->news_id); $i++) {
            //Чередуем цвета
            if ($i % 2 == 0) {
                echo '<div class="obiavlenie">';
            } else {
                echo '<div class="obiavlenie_1">';
            }
            //Изображение
            //if (file_exists($_GET['images'][$k])) {
            //echo $this->news_image[$i];
            if ($this->news_image[$i] != "") {
                echo '<div class="img_obiavlenie">
                    <img class="img_obiavlenie_1" src="../admin/' . $this->news_image[$i] . '" alt="">
                    </div>';
            }
            echo '<div class="obiavlenie_inline">
                <div class="obiavlenie_bottom_text">
            <span class="bottom_text_1">' . $this->news_date[$i] . '</span>
            </div>
            <div class="name_obiavlenie">
            <a class="name_o_1">' . $this->news_header[$i] . '</a>
            </div>
            <div class="text_obiavlenie">
            <p>' . $this->news_text[$i] . '</p></div>
            </div></div>';
        }
    }

    function GenerateNavigation($page, $limit, $where, $new_url) {
        $page = filter_var($page, FILTER_VALIDATE_INT);
        $limit = filter_var($limit, FILTER_VALIDATE_INT, array("options" => array("min_range" => 0, "max_range" => 50)));
        $where = filter_var($where, FILTER_SANITIZE_STRIPPED);
        $new_url = filter_var($new_url, FILTER_SANITIZE_STRIPPED);
        $query = 'SELECT count(k_isf_id) as max FROM k_immovables_sell ' . $where;
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare($query);
            $queue->execute();
            $row = $queue->rowCount();
        } catch (PDOException $e) {
            exit();
        }
        if ($row != 0) {
            $pages = intval($row / $limit);
            if ($row % $limit != 0) {
                $pages++;
            }
        } else {
            $pages = 1;
        }
        if ($pages != 1) {
            echo '<div class="listing">';
            if ($page == $pages || $page > 1) {
                echo '<a class="style_listing" href="./?PageIndex=' . ($page - 1) . '&' . $new_url . '">Предыдущая</a>';
            }

            if ($pages <= 11) {
                for ($i = 1; $i <= $pages; $i++) {
                    if ($i == $page) {
                        echo '<a class="active_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                    } else {
                        echo '<a class="style_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                    }
                }
            } else {
                if ($page <= 6) {
                    for ($i = 1; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #000000; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                    }
                }
                if ($page > 6 && $page <= ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #000000; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $page - 2; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #000000; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                    }
                }
                if ($page > ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #000000; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 5; $i <= $pages; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                        }
                    }
                }
            }

            if ($page == 1 || $page < $pages) {
                echo '<a class="style_listing" href="./?PageIndex=' . ($page + 1) . '&' . $new_url . '">Следующая</a>';
            }

            echo '</div>';
        }
    }

}

class BuyIm {

    public $buy_id = array();
    public $buy_user = array();
    public $buy_user_str = array();
    public $buy_date = array();
    public $buy_text = array();
    public $buy_phone = array();
    public $buy_subcategory = array();
    public $total = 0;

    function LoadBuys($limit, $page, $where) {
        $limit = filter_var($limit, FILTER_VALIDATE_INT, array("options" => array("min_range" => 0, "max_range" => 50)));
        $page = filter_var($page, FILTER_VALIDATE_INT);
        $where = filter_var($where, FILTER_SANITIZE_STRIPPED);
        $query = 'SELECT kib.*, ku.k_ku_login FROM k_immovables_buy as kib
            LEFT JOIN k_users as ku ON (kib.k_ib_user_id = ku.k_ku_id) ' . $where;
        $query .= ' ORDER BY k_ib_date DESC';
        if ($limit != 0) {
            $query .= ' LIMIT ' . (($page - 1) * $limit) . ',' . $limit;
        }
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
            $this->buy_id[$n] = $row['k_ib_id'];
            $this->buy_user[$n] = $row['k_ib_user_id'];
            if ($row['k_ib_user_id'] != 0) {
                $this->buy_user_str[$n] = $row['k_ku_login'];
            } else {
                $this->buy_user_str[$n] = 'Гость';
            }
            $this->buy_date[$n] = $row['k_ib_date'];
            $this->buy_text[$n] = $row['k_ib_text'];
            $this->buy_phone[$n] = $row['k_ib_phone'];
            $this->buy_subcategory[$n] = $row['k_ib_subcategory'];
            $n++;
        }
        $query = 'SELECT k_ib_id FROM k_immovables_buy as kib
            LEFT JOIN k_users as ku ON (kib.k_ib_user_id = ku.k_ku_id) ' . $where;
        try {
            $queueT = $mysql->prepare($query);
            $queueT->execute();
            $resultT = $queueT->fetchAll(PDO::FETCH_ASSOC);
            $this->total = count($resultT);
        } catch (PDOException $e) {
            exit();
        }
    }

    function GenerateTable() {
        for ($i = 0; $i < count($this->buy_id); $i++) {
            $color = '';
            /* switch ($this->buy_subcategory[$i]) {
              case 0: $color = 'white';
              break;
              case 1: $color = '#efc4c4';
              break;
              case 2: $color = '#ceefc4';
              break;
              case 3: $color = '#c4cdef';
              break;
              case 4: $color = '#dcdddf';
              break;
              case 5: $color = '#f3ca95';
              break;
              } */
            if ($i % 2 == 0) {
                $color = '#ebede8';
            } else {
                $color = 'white';
            }
            $class_user = '';
            if ($this->buy_user_str[$i] != 'Гость') {
                $class_user = 'class="name_team_4" title="Пользователь"';
            } else {
                $class_user = 'class="name_team_2"';
            }
            echo '<div class="block_koment" style="background: ' . $color . ';">
            <span class="name_team_3">' . $this->buy_date[$i] . '</span>
            <span ' . $class_user . '>' . $this->buy_user_str[$i] . '<b style="float: right;">№ ' . $this->buy_id[$i] . '</b></span><br>
            <p class="text_team">' . $this->buy_text[$i] . '</p>';
            if ($this->buy_phone[$i] != '') {
                echo '<p class="text_team">Телефон: <b>' . $this->buy_phone[$i] . '</b></p>';
            }
            echo '</div>';
        }
    }

    function GenerateNavigation($page, $limit, $where, $new_url) {
        $page = filter_var($page, FILTER_VALIDATE_INT);
        $limit = filter_var($limit, FILTER_VALIDATE_INT, array("options" => array("min_range" => 0, "max_range" => 50)));
        $where = filter_var($where, FILTER_SANITIZE_STRIPPED);
        $new_url = filter_var($new_url, FILTER_SANITIZE_STRIPPED);
        $query = 'SELECT count(k_ib_id) as max FROM k_immovables_buy ' . $where;
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare($query);
            $queue->execute();
            $row = $queue->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        if ($row['max'] != 0) {
            $pages = intval($row['max'] / $limit);
            if ($row['max'] % $limit != 0) {
                $pages++;
            }
        } else {
            $pages = 1;
        }
        if ($pages != 1) {
            echo '<div class="listing">';
            if ($page == $pages || $page > 1) {
                echo '<a class="style_listing" href="./?PageIndex=' . ($page - 1) . '&' . $new_url . '">Предыдущая</a>';
            }

            if ($pages <= 11) {
                for ($i = 1; $i <= $pages; $i++) {
                    if ($i == $page) {
                        echo '<a class="active_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                    } else {
                        echo '<a class="style_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                    }
                }
            } else {
                if ($page <= 6) {
                    for ($i = 1; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #000000; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                    }
                }
                if ($page > 6 && $page <= ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #000000; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $page - 2; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #000000; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                    }
                }
                if ($page > ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #000000; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 5; $i <= $pages; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageIndex=' . $i . '&' . $new_url . '">' . $i . '</a>';
                        }
                    }
                }
            }

            if ($page == 1 || $page < $pages) {
                echo '<a class="style_listing" href="./?PageIndex=' . ($page + 1) . '&' . $new_url . '">Следующая</a>';
            }

            echo '</div>';
        }
    }

}

class Agent {

    public $agent_id = array();
    public $agent_fid = array();
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

    function LoadAgents($limit, $page, $where) {
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
            LEFT JOIN map_buildings AS mb ON (mb.k_shn_id = kshn.k_shn_id)
            WHERE k_u_privileges=4 AND 1=1 AND k_ua_state=1 ' . $where . '
            GROUP BY k_ku_id';
        //WHERE k_u_privileges=4 AND k_ua_last_date>NOW() AND k_ua_state=1 ' . $where . '
        $query .= ' ORDER BY k_uar_id DESC , k_ku_id ASC';
        if ($limit != 0) {
            $query .= ' LIMIT ' . (($page - 1) * $limit) . ',' . $limit;
        }
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
            $this->agent_fid[$n] = $row['fid'];
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

    function GenerateAgentRegister() {
        echo '<div class="reestr_agenstv">
            <div class="shapka_bloka">
            <a href="./?ShowParam=19" class="style_shapka_1">Реестр агентств</a>
            </div><div class="obveden_block_agent">';
        for ($i = 0; $i < count($this->agent_id); $i++) {
            echo '<div class="add_agenstvo">
                <div class="add_agenstvo_left">
                <a href="./?ShowParam=21&Aid=' . $this->agent_id[$i] . '"><img class="img_add_agenstvo" src="../admin/' . $this->agent_avatar[$i] . '" alt=""></a>
                </div>
                <div class="add_agenstvo_right">
                <p class="predl_coll_p">Предложений <a class="button_agent" href="./?ShowParam=21&Aid=' . $this->agent_id[$i] . '">' . $this->agent_ads[$i] . '</a></p>
                <a class="predl_coll_a">' . $this->agent_name[$i] . ' '.$this->agent_phone[$i].'</a>

                </div>
                </div>';
        }
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            //$queue = $mysql->prepare('SELECT count(*) as max FROM k_users_agents WHERE k_ua_last_date>NOW() AND k_ua_state=1');
            $queue = $mysql->prepare('SELECT count(*) as max FROM k_users_agents WHERE 1=1 AND k_ua_state=1');
            $queue->execute();
            $row = $queue->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        echo '<hr color="#444" size="1">';

        echo '<a class="all_agenstva blockLeft" href="./?ShowParam=19">Все агентства</a><!--<span class="style_menu_left_3 blockRight">' . $row['max'] . '</span>--><br>';


        $query2 = 'SELECT count(kisell.k_isf_id) as agent_ads
            FROM k_users_agents AS kua
            LEFT JOIN k_users as ku ON (kua.k_ua_user_parent = ku.k_ku_id)
            LEFT JOIN k_immovables_sell as kisell ON (kisell.k_isf_user_id = ku.k_ku_id)
            WHERE 1=1 AND k_ua_state=1';
            //WHERE k_ua_last_date>NOW() AND k_ua_state=1';
        try {
            $queue = $mysql->prepare($query2);
            $queue->execute();
            $row2 = $queue->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        echo '<a class="all_agenstva" href="./?ShowParam=1&AllAgents=1">Все объявления от агенств<span class="style_menu_left_3">' . $row2['agent_ads'] . '</span></a>';

        echo '</div></div>';
    }

    function GenerateTable() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare('SELECT count(*) as max FROM k_users_agents');
            $queue->execute();
            $row = $queue->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        echo '<div class="block_content_1">';
        echo '<a class="style_shapka_1_spec">Агентства недвижимости</a><p class="style_shapka_3_spec">' . $row['max'] . '</p><br>';
        for ($i = 0; $i < count($this->agent_id); $i++) {
            if ($i % 2 == 0) {
                echo '<div class="agenstvo">';
            } else {
                echo '<div class="agenstvo_1">';
            }
            echo '
                <div class="photo_agenstv">';
            if ($this->agent_avatar[$i] == "") {
                echo '<img class="img_agenstvo" src="../images/noimage.png" alt="">';
            } else {
                echo '<img class="img_agenstvo" src="../admin/' . $this->agent_avatar[$i] . '" alt="">';
            }
            echo '</div>
                <div class="text_agenstv">
                <a class="name_o_1" href="./?ShowParam=21&Aid=' . $this->agent_id[$i] . '">' . $this->agent_name[$i] . '</a><p class="style_shapka_3_spec">' . $this->agent_ads[$i] . '</p>
                <p>' . $this->agent_description[$i] . '</p>
                </div>
                </div>';
        }
        echo '</div>';
    }

    function GenerateAgent($id) {
        $one_agent = new Agent();
        $one_agent->LoadAgents(0, 0, ' AND k_ku_id=' . $id);
        if (count($one_agent->agent_id) == 0) {
            echo '<div class="block_content_1">Агентство не найдено!</div>';
        } else {
            $id = 0;
            echo '<div class="block_content_1">
            <p class="name_ag_tit">' . $one_agent->agent_name[$id] . '</p>
            <div class="team_agenstvo">';
            if ($one_agent->agent_avatar[$id] == "") {
                echo '<img class="agenstvo_img" src="../images/noimage.png" alt="">';
            } else {
                echo '<div class="block_im_ag"><img class="agenstvo_img" src="../admin/' . $one_agent->agent_avatar[$id] . '" alt=""></div>';
            }
            echo '<div class="agenstvo_block">
            <p>' . $one_agent->agent_description[$id] . '</p>
            </div>
            </div>
            </div>';
            $query = 'SELECT k_is_id, k_is_name, count(k_isf_id) as max, k_is_parent
            FROM k_immovables_subcategories as kisu
            LEFT JOIN k_immovables_sell as kise ON (kise.k_isf_subcategory = kisu.k_is_id AND kise.k_isf_user_id = :id)
            WHERE (k_is_parent = 1 OR k_is_parent = 2)
            GROUP BY k_is_id ORDER BY k_is_id ASC';
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
            echo '<div class="menu_agenstvo">
            <a class="m_ag">Предложения Агенства</a><br>
            <div class="menu_agenstvo_left">
            <a class="m_ag_1" href="./?ShowParam=2&UserId=' . $one_agent->agent_id[$id] . '">ПРОДАЮ</a><span class="style_menu_left_3">' . $sell . '</span><br>';
            for ($i = 0; $i < count($allinone[0]); $i++) {
                if ($allinone[2][$i] == 1) {
                    echo '<p class="menu_ag"><a class="m_ag_2" href="./?ShowParam=' . ($i + 3) . '&UserId=' . $one_agent->agent_id[$id] . '">' . $allinone[0][$i] . '</a><span class="style_menu_left_3">' . $allinone[1][$i] . '</span></p>';
                }
            }
            echo '</div>
            <div class="menu_agenstvo_center">
            <a class="m_ag_1" href="./?ShowParam=8&UserId=' . $one_agent->agent_id[$id] . '">СДАЮ</a><span class="style_menu_left_3">' . $rent . '</span><br>';
            for ($i = 0; $i < count($allinone[0]); $i++) {
                if ($allinone[2][$i] == 2) {
                    echo '<p class="menu_ag"><a class="m_ag_2" href="./?ShowParam=' . ($i + 4) . '&UserId=' . $one_agent->agent_id[$id] . '">' . $allinone[0][$i] . '</a><span class="style_menu_left_3">' . $allinone[1][$i] . '</span></p>';
                }
            }
            echo '</div>
            <div class="menu_agenstvo_right">
            <p class="m_ag_3">
            Адрес:
            <a class="spec_style_ag" href="../map/?f=' . $one_agent->agent_address_str[$id] . '" title="Показать на карте">' . $one_agent->agent_address_str[$id] . '</a>
            </p>
            <a class="m_ag_3">Тел:
            <span style="font-weight: bold;">' . $one_agent->agent_phone[$id] . '</span>
            </a>
            <a class="m_ag_3">
            Сайт:<a class="m_ag_4" href="' . $one_agent->agent_site[$id] . '">' . $one_agent->agent_site[$id] . '</a> 
            </a>
            <a class="m_ag_3">
            Email:<a class="m_ag_4" href="mailto:' . $one_agent->agent_email[$id] . '">' . $one_agent->agent_email[$id] . '</a>
            </a>
            </div>
            </div>';
        }
    }

}

?>
