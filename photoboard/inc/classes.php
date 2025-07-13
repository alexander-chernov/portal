<?php

class PhotoAdsTable {

    protected $id = array();
    protected $theme = array();
    protected $category = array();
    protected $category_str = array();
    protected $user_id = array();
    protected $user_str = array();
    protected $user_ip = array();
    protected $text = array();
    protected $phone = array();
    protected $price = array();
    protected $vip = array();
    protected $paid = array();
    protected $up_date = array();
    protected $reg_date = array();
    protected $end_date = array();
    protected $last_visit = array();
    protected $photo_url = array();
    protected $views = array();
    protected $views_day = array();
    public $color = array();
    public $all_ads = 0;

    /**
     * Загрузить объявления
     * @param int $limit Число объявлений на странице
     * @param int $page Номер страницы
     * @param array $where = array(array('column1','column2'),array(':var1',':var2'),array('AND','OR'),array('value1','value2'));
     */
    function LoadAds($limit, $page, $where, $tod) {
        //var_dump($limit);
        $limit = filter_var($limit, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => 50)));
        $page = filter_var($page, FILTER_VALIDATE_INT);
        $query = 'SELECT distinct kpd.*, kpdc.k_pdc_name, ku.k_ku_login, ku.k_ku_last_ip, kpp.k_pdp_link, ph_v, count(views2.k_pdv_id) as ph_v2
            FROM k_photodesk AS kpd
            LEFT JOIN k_photodesk_categories AS kpdc ON (kpdc.k_pdc_id = kpd.k_pd_category)
            LEFT JOIN k_users AS ku ON (ku.k_ku_id = kpd.k_pd_user_id)
            LEFT JOIN (SELECT * FROM k_photodesk_photos GROUP BY k_pdp_ad_id) AS kpp ON (kpp.k_pdp_ad_id  = kpd.k_pd_id)
            LEFT JOIN (SELECT count(*) as ph_v,k_pdv_photo_id FROM k_photodesk_views GROUP BY k_pdv_photo_id) AS views ON (views.k_pdv_photo_id = kpd.k_pd_id)
            LEFT JOIN (SELECT k_pdv_id,k_pdv_photo_id FROM k_photodesk_views WHERE k_pdv_date > "' . date('Y-m-d 00:00:00', time()) . '") AS views2 ON (views2.k_pdv_photo_id = kpd.k_pd_id) ';
        $vars = array();
        $query2 = 'SELECT count(*) as max FROM k_photodesk ';
        if ($tod) {
            $query .= $tod;
            $query2 .= $tod;
        }
        if (count($where[0]) != 0) {
            $query1 .= ' WHERE ';
            for ($i = 0; $i < count($where[0]); $i++) {
                $where[0][$i] = filter_var($where[0][$i], FILTER_SANITIZE_STRIPPED);
                $where[1][$i] = filter_var($where[1][$i], FILTER_SANITIZE_STRIPPED);
                $where[2][$i] = filter_var($where[2][$i], FILTER_SANITIZE_STRIPPED);
                $query1 .= $where[0][$i] . '=' . $where[1][$i] . ' ' . $where[2][$i] . ' ';
                $vars[$where[1][$i]] = $where[3][$i];
            }
            $query1 .= ' AND k_pd_end_date>NOW() ';
        } else {
            if (!$tod) {
                $query1 .= ' WHERE k_pd_end_date>NOW() ';
            }
        }
        $query .= $query1;
        $query2 .= $query1;
        $query .= ' GROUP BY k_pd_id
            ORDER BY k_pd_up_date DESC ';
        //echo $query2.'<br>';
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue0 = $mysql->prepare($query2);
            $queue0->execute($vars);
            $result0 = $queue0->fetch(PDO::FETCH_ASSOC);
            $this->all_ads = $result0['max'];
        } catch (PDOException $e) {
            exit();
        }
        if ($limit != 0) {
            $query .= ' LIMIT ' . (($page>0?$page-1:$page) * $limit) . ',' . $limit;
        }
        //var_dump($query);

        try {
            $queue = $mysql->prepare($query);
            $queue->execute($vars);
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        $i = 1;
        foreach ($result as $value) {
            $this->setID($value['k_pd_id'], $i);
            $this->setViews($value['ph_v'], $i);
            $this->setViewsDays($value['ph_v2'], $i);
            $this->setTheme($value['k_pd_theme'], $i);
            $this->setCategory($value['k_pd_category'], $i);
            $this->setCategoryStr($value['k_pdc_name'], $i);
            $this->setUser($value['k_pd_user_id'], $i);
            $this->setUserStr($value['k_ku_login'], $i);
            $this->setText($value['k_pd_text'], $i);
            $this->setPhone($value['k_pd_phone'], $i);
            $this->setPrice($value['k_pd_price'], $i);
            $this->setVIP($value['k_pd_vip'], $i);
            $this->setPaid($value['k_pd_paid'], $i);
            $this->setUpDate($value['k_pd_up_date'], $i);
            $this->setRegDate($value['k_pd_reg_date'], $i);
            $this->setEndDate($value['k_pd_end_date'], $i);
            $this->setLastDate($value['k_pd_last_visit'], $i);
            $this->setPhoto($value['k_pdp_link'], $i);
            $this->setIp($value['k_ku_last_ip'], $i);
            $this->color[$i] = $value['k_pd_color_light'];
            $i++;
        }
    }

    function GenerateNavigation($page, $where, $link, $limit=12) {
        $limit = filter_var($limit, FILTER_VALIDATE_INT, array("options" => array("MIN_RANGE" => 1, "MAX_RANGE" => 50)));
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue1 = $mysql->prepare('SELECT count(k_pd_id) as max FROM k_photodesk as kpd LEFT JOIN k_users AS ku ON (ku.k_ku_id = kpd.k_pd_user_id) ' . $where);
            $queue1->execute();
            $row = $queue1->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        if ($row['max'] == 0) {
            $row['max'] = 1;
        }
        if ($limit>0) {
            $pages = intval($row['max'] / $limit);
            if ($row['max'] % $limit != 0) {
                $pages++;
            }
        }
        if ($pages != 1) {
            echo '<div class="listing">';
            if ($page == $pages || $page > 1) {
                echo '<a class="style_listing" href="../photoboard/?PageType=1&PageIndex=' . ($page - 1) . $link . '&LimitOnPage=' . $limit . '">Предыдущая</a>';
            }

            if ($pages <= 11) {
                for ($i = 1; $i <= $pages; $i++) {
                    if ($i == $page) {
                        echo '<a class="active_listing" href="../photoboard/?PageType=1&PageIndex=' . $i . $link . '&LimitOnPage=' . $limit . '">' . $i . '</a>';
                    } else {
                        echo '<a class="style_listing" href="../photoboard/?PageType=1&PageIndex=' . $i . $link . '&LimitOnPage=' . $limit . '">' . $i . '</a>';
                    }
                }
            } else {
                if ($page <= 6) {
                    for ($i = 1; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="../photoboard/?PageType=1&PageIndex=' . $i . $link . '&LimitOnPage=' . $limit . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="../photoboard/?PageType=1&PageIndex=' . $i . $link . '&LimitOnPage=' . $limit . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="../photoboard/?PageType=1&PageIndex=' . $i . $link . '&LimitOnPage=' . $limit . '">' . $i . '</a>';
                    }
                }
                if ($page > 6 && $page <= ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="../photoboard/?PageType=1&PageIndex=' . $i . $link . '&LimitOnPage=' . $limit . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $page - 2; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="../photoboard/?PageType=1&PageIndex=' . $i . $link . '&LimitOnPage=' . $limit . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="../photoboard/?PageType=1&PageIndex=' . $i . $link . '&LimitOnPage=' . $limit . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="../photoboard/?PageType=1&PageIndex=' . $i . $link . '&LimitOnPage=' . $limit . '">' . $i . '</a>';
                    }
                }
                if ($page > ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="../photoboard/?PageType=1&PageIndex=' . $i . $link . '&LimitOnPage=' . $limit . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 5; $i <= $pages; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="../photoboard/?PageType=1&PageIndex=' . $i . $link . '&LimitOnPage=' . $limit . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="../photoboard/?PageType=1&PageIndex=' . $i . $link . '&LimitOnPage=' . $limit . '">' . $i . '</a>';
                        }
                    }
                }
            }

            if ($page == 1 || $page < $pages) {
                echo '<a class="style_listing" href="../photoboard/?PageType=1&PageIndex=' . ($page + 1) . $link . '&LimitOnPage=' . $limit . '">Следующая</a>';
            }

            echo '</div>';
        }
    }

    function ViewsAdd($user_id) {
        $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
        if (!$user_id) {
            $user_id = 0;
        }
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue1 = $mysql->prepare('INSERT INTO k_photodesk_views (k_pdv_photo_id,k_pdv_date,k_pdv_user) VALUES (:id,NOW(),:user_id)');
            $queue1->execute(array(":id" => $this->id[1], ":user_id" => $user_id));
        } catch (PDOException $e) {
            exit();
        }
    }

    //SET'n'GET
    protected function setID($val, $i) {
        $this->id[$i] = filter_var($val, FILTER_VALIDATE_INT);
    }

    public function getID($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->id;
        } else {
            return $this->id[$i];
        }
    }

    protected function setViews($val, $i) {
        $this->views[$i] = filter_var($val, FILTER_VALIDATE_INT);
        if (!$this->views[$i]) {
            $this->views[$i] = 0;
        }
    }

    public function getViews($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->views;
        } else {
            return $this->views[$i];
        }
    }

    protected function setViewsDays($val, $i) {
        $this->views_day[$i] = filter_var($val, FILTER_VALIDATE_INT);
    }

    public function getViewsDays($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->views_day;
        } else {
            return $this->views_day[$i];
        }
    }

    protected function setTheme($val, $i) {
        if (mb_strlen(filter_var($val, FILTER_SANITIZE_STRIPPED), "utf-8") > 15) {
            $this->theme[$i] = mb_substr(filter_var($val, FILTER_SANITIZE_STRIPPED), 0, 15, "utf-8") . '...';
        } else {
            $this->theme[$i] = filter_var($val, FILTER_SANITIZE_STRIPPED);
        }
    }

    public function getTheme($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->theme;
        } else {
            return $this->theme[$i];
        }
    }

    protected function setCategory($val, $i) {
        $this->category[$i] = filter_var($val, FILTER_VALIDATE_INT);
    }

    public function getCategory($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->category;
        } else {
            return $this->category[$i];
        }
        return $this->category;
    }

    protected function setCategoryStr($val, $i) {
        $this->category_str[$i] = filter_var($val, FILTER_SANITIZE_STRIPPED);
    }

    public function getCategoryStr($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->category_str;
        } else {
            return $this->category_str[$i];
        }
    }

    protected function setUser($val, $i) {
        $this->user_id[$i] = filter_var($val, FILTER_VALIDATE_INT);
    }

    public function getUser($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->user_id;
        } else {
            return $this->user_id[$i];
        }
    }

    protected function setUserStr($val, $i) {
        $this->user_str[$i] = filter_var($val, FILTER_SANITIZE_STRIPPED);
    }

    public function getUserStr($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->user_str;
        } else {
            return $this->user_str[$i];
        }
    }

    protected function setIp($val, $i) {
        $this->user_ip[$i] = filter_var($val, FILTER_SANITIZE_STRIPPED);
    }

    public function getIp($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->user_ip;
        } else {
            return $this->user_ip[$i];
        }
    }

    protected function setText($val, $i) {
        $this->text[$i] = $val;
    }

    public function getText($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->text;
        } else {
            return $this->text[$i];
        }
    }

    protected function setPhone($val, $i) {
        $this->phone[$i] = filter_var($val, FILTER_SANITIZE_STRIPPED);
    }

    public function getPhone($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->phone;
        } else {
            return $this->phone[$i];
        }
    }

    protected function setPrice($val, $i) {
        $this->price[$i] = filter_var($val, FILTER_VALIDATE_INT, array("options" => array('min_range' => 1)));
    }

    public function getPrice($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->price;
        } else {
            return $this->price[$i];
        }
    }

    protected function setVIP($val, $i) {
        $this->vip[$i] = filter_var($val, FILTER_VALIDATE_INT, array("options" => array('min_range' => 0, 'max_range' => 1)));
    }

    public function getVIP($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->vip;
        } else {
            return $this->vip[$i];
        }
    }

    protected function setPaid($val, $i) {
        $this->paid[$i] = filter_var($val, FILTER_VALIDATE_INT, array("options" => array('min_range' => 0, 'max_range' => 1)));
    }

    public function getPaid($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->paid;
        } else {
            return $this->paid[$i];
        }
    }

    protected function setUpDate($val, $i) {
        $this->up_date[$i] = filter_var($val, FILTER_SANITIZE_STRIPPED);
    }

    public function getUpDate($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->up_date;
        } else {
            return $this->up_date[$i];
        }
    }

    protected function setRegDate($val, $i) {
        $this->reg_date[$i] = filter_var($val, FILTER_SANITIZE_STRIPPED);
    }

    public function getRegDate($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->reg_date;
        } else {
            return $this->reg_date[$i];
        }
    }
    protected function setEndDate($val, $i) {
            $this->end_date[$i] = filter_var($val, FILTER_SANITIZE_STRIPPED);
        }

        public function getEndDate($i) {
            $i = filter_var($i, FILTER_VALIDATE_INT);
            if ($i == 0) {
                return $this->end_date;
            } else {
                return $this->end_date[$i];
            }
        }
    protected function setLastDate($val, $i) {
        $this->last_visit[$i] = filter_var($val, FILTER_SANITIZE_STRIPPED);
    }

    public function getLastDate($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->last_visit;
        } else {
            return $this->last_visit[$i];
        }
    }

    protected function setPhoto($val, $i) {
        $this->photo_url[$i] = filter_var($val, FILTER_SANITIZE_STRIPPED);
    }

    public function getPhoto($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->photo_url;
        } else {
            return $this->photo_url[$i];
        }
    }

}

class StatisticsTable {

    protected $today;
    protected $yesterday;
    protected $all;

    function LoadStat() {
        $today = date('Y-m-d 00:00:00');
        $yesterday = date('Y-m-d 00:00:00', time() - 60 * 60 * 24);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare('SELECT count(*) as max FROM k_photodesk WHERE k_pd_reg_date>"' . $today . '"');
            $queue->execute();
            $result = $queue->fetch(PDO::FETCH_ASSOC);
            $this->SetToday($result['max']);
            $queue2 = $mysql->prepare('SELECT count(*) as max FROM k_photodesk WHERE k_pd_reg_date>"' . $yesterday . '" AND k_pd_reg_date<"' . $today . '"');
            $queue2->execute();
            $result2 = $queue2->fetch(PDO::FETCH_ASSOC);
            $this->SetYesterday($result2['max']);
            // $queue3 = $mysql->prepare('SELECT count(*) as max FROM k_photodesk');
            $queue3 = $mysql->prepare('SELECT count(*) as max FROM k_photodesk WHERE k_pd_end_date>NOW()');
            $queue3->execute();
            $result3 = $queue3->fetch(PDO::FETCH_ASSOC);
            $this->SetAll($result3['max']);
        } catch (PDOException $e) {
            exit();
        }
    }

    //Set'n'Get
    protected function SetToday($val) {
        $val = filter_var($val, FILTER_VALIDATE_INT);
        $this->today = $val;
    }

    function GetToday() {
        return $this->today;
    }

    protected function SetYesterday($val) {
        $val = filter_var($val, FILTER_VALIDATE_INT);
        $this->yesterday = $val;
    }

    function GetYesterday() {
        return $this->yesterday;
    }

    protected function SetAll($val) {
        $val = filter_var($val, FILTER_VALIDATE_INT);
        $this->all = $val;
    }

    function GetAll() {
        return $this->all;
    }

}

class UserPhoto {

    protected $last_date;
    protected $ad_count;
    protected $login;
    protected $email;
    protected $id;
    protected $photos = array();

    function LoadUser($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare('SELECT DISTINCT ku.k_ku_last_date, ku.k_ku_login, ku.k_ku_email, ku.k_ku_id
                FROM k_photodesk AS kpd
                LEFT JOIN k_users AS ku ON (ku.k_ku_id = kpd.k_pd_user_id)
                WHERE k_pd_id=:id');
            $queue->execute(array(':id' => $id));
            $result = $queue->fetch(PDO::FETCH_ASSOC);
            $this->SetLastDay($result['k_ku_last_date']);
            $this->SetLogin($result['k_ku_login']);
            $this->SetEmail($result['k_ku_email']);
            $this->SetID($result['k_ku_id']);
            $queue2 = $mysql->prepare('SELECT count(k_pd_id) AS max FROM k_photodesk WHERE k_pd_user_id=:u_id');
            $queue2->execute(array(':u_id' => $this->GetID()));
            $result2 = $queue2->fetch(PDO::FETCH_ASSOC);
            $this->ad_count = $result2['max'];
            $queue3 = $mysql->prepare('SELECT k_pdp_link FROM k_photodesk_photos WHERE k_pdp_ad_id=:id');
            $queue3->execute(array(':id' => $id));
            $result3 = $queue3->fetchAll(PDO::FETCH_ASSOC);
            $i = 1;
            foreach ($result3 as $value) {
                $this->SetPhotos($value['k_pdp_link'], $i);
                $i++;
            }
            //var_dump($this->photos);
        } catch (PDOException $e) {
            exit();
        }
    }

    //Set'n'Get
    protected function SetLastDay($val) {
        $val = filter_var($val, FILTER_SANITIZE_STRIPPED);
        $this->last_date = $val;
    }

    function GetLastDay() {
        return $this->last_date;
    }

    protected function SetCount($val) {
        $val = filter_var($val, FILTER_VALIDATE_INT);
        $this->ad_count = $val;
    }

    function GetCount() {
        return $this->ad_count;
    }

    protected function SetID($val) {
        $val = filter_var($val, FILTER_VALIDATE_INT);
        $this->id = $val;
    }

    function GetID() {
        return $this->id;
    }

    protected function SetLogin($val) {
        $val = filter_var($val, FILTER_SANITIZE_STRIPPED);
        $this->login = $val;
    }

    function GetLogin() {
        return $this->login;
    }

    protected function SetPhotos($val, $i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        $val = filter_var($val, FILTER_SANITIZE_STRIPPED);
        $this->photos[$i] = $val;
    }

    function GetPhotos($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->photos;
        } else {
            return $this->photos[$i];
        }
    }

    protected function SetEmail($val) {
        $val = filter_var($val, FILTER_SANITIZE_EMAIL);
        $this->email = $val;
    }

    function GetEmail() {
        return $this->email;
    }

}

class Comments {

    protected $comment = array();
    protected $date = array();
    protected $user = array();

    function LoadComments($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare('SELECT k_pc_text,k_pc_date,k_ku_login FROM k_photodesk_comments
                LEFT JOIN k_users ON (k_users.k_ku_id = k_photodesk_comments.k_pc_user_id)
                WHERE k_pc_photodesk_id=:id
                ORDER BY k_pc_date DESC');
            $queue->execute(array(':id' => $id));
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            $i = 1;
            foreach ($result as $value) {
                $this->SetComments($value['k_pc_text'], $i);
                $this->SetData($value['k_pc_date'], $i);
                $this->SetLogin($value['k_ku_login'], $i);
                $i++;
            }
        } catch (PDOException $e) {
            exit();
        }
    }

    protected function SetComments($val, $i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        $val = filter_var($val, FILTER_SANITIZE_STRIPPED);
        $this->photos[$i] = $val;
    }

    function GetComments($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->photos;
        } else {
            return $this->photos[$i];
        }
    }

    protected function SetData($val, $i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        $val = filter_var($val, FILTER_SANITIZE_STRIPPED);
        $this->date[$i] = $val;
    }

    function GetDate($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->date;
        } else {
            return $this->date[$i];
        }
    }

    protected function SetLogin($val, $i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        $val = filter_var($val, FILTER_SANITIZE_STRIPPED);
        $this->user[$i] = $val;
    }

    function GetLogin($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->user;
        } else {
            return $this->user[$i];
        }
    }

}

class StatisticsLeftBlock {

    public $id = array();
    public $name = array();
    public $count = array();

    public function StatisticsLeftBlock() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT kpc.*, count(k_pd_id) AS num
                FROM k_photodesk_categories AS kpc
                LEFT JOIN k_photodesk AS kp ON (kp.k_pd_category = kpc.k_pdc_id)
                GROUP BY k_pdc_id
                ORDER BY k_pdc_name ASC');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $value) {
                $this->id[] = $value['k_pdc_id'];
                $this->name[] = $value['k_pdc_name'];
                $this->count[] = $value['num'];
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

?>
