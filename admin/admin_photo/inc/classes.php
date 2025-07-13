<?php

class PhotoAds {

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
    protected $on_main = array();
    protected $color_light = array();

    /**
     * Загрузить объявления
     * @param int $limit Число объявлений на странице
     * @param int $page Номер страницы
     * @param array $where = array(array('column1','column2'),array(':var1',':var2'),array('AND','OR'),array('value1','value2'));
     */
    function LoadAds($limit, $page, $where) {
        $limit = filter_var($limit, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => 50)));
        $page = filter_var($page, FILTER_VALIDATE_INT);
        $query = 'SELECT kpd.*, kpdc.k_pdc_name, ku.k_ku_login, ku.k_ku_last_ip, kpp.k_pdp_link
            FROM k_photodesk AS kpd
            LEFT JOIN k_photodesk_categories AS kpdc ON (kpdc.k_pdc_id = kpd.k_pd_category)
            LEFT JOIN k_users AS ku ON (ku.k_ku_id = kpd.k_pd_user_id)
            LEFT JOIN k_photodesk_photos AS kpp ON (kpp.k_pdp_ad_id  = kpd.k_pd_id) ';
        $vars = array();
        if (count($where[0]) != 0) {
            $query .= ' WHERE ';
            for ($i = 0; $i < count($where[0]); $i++) {
                $where[0][$i] = filter_var($where[0][$i], FILTER_SANITIZE_STRIPPED);
                $where[1][$i] = filter_var($where[1][$i], FILTER_SANITIZE_STRIPPED);
                $where[2][$i] = filter_var($where[2][$i], FILTER_SANITIZE_STRIPPED);
                $query .= $where[0][$i] . '=' . $where[1][$i] . ' ' . $where[2][$i] . ' ';
                $vars[$where[1][$i]] = $where[3][$i];
            }
        }
        $query .= ' GROUP BY k_pd_id
            ORDER BY k_pd_reg_date DESC ';
        if ($limit != 0) {
            $query .= ' LIMIT ' . (($page - 1) * $limit) . ',' . $limit;
        }
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare($query);
            $queue->execute($vars);
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            if (file_exists('../../logs/mysql_logs')) {
                $file = fopen('../../logs/mysql_logs', "a");
                fwrite($file, "\r\n" . $e->getMessage() . " --- " . date('Y-m-d H:i:s', time()) . " --- " . "admin/admin_photo/inc/classes.php PhotoAds::LoadAds(limit, page, where)");
                fclose($file);
            }
            exit();
        }
        $i = 1;
        foreach ($result as $value) {
            $this->setID($value['k_pd_id'], $i);
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
            $this->setOnMain($value['k_pd_main_page'], $i);
            $this->setColorLight($value['k_pd_color_light'], $i);
            $i++;
        }
    }

    /**
     * Добавить/убрать объявление в VIP
     * @param Int $id ID объявления
     * @param Int $act Действие
     * @return boolean Удалось или нет
     */
    public function PhotoVIP($id, $act) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        $act = filter_var($act, FILTER_VALIDATE_INT, array("options" => array("min-range" => 0, "max-range" => 1)));
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare('UPDATE k_photodesk SET k_pd_vip=:act WHERE k_pd_id=:id');
            $queue->execute(array(':act' => $act, ':id' => $id));
            return TRUE;
        } catch (PDOException $e) {
            if (file_exists('../../logs/mysql_logs')) {
                $file = fopen('../../logs/mysql_logs', "a");
                fwrite($file, "\r\n" . $e->getMessage() . " --- " . date('Y-m-d H:i:s', time()) . " --- " . "admin/admin_photo/inc/classes.php PhotoAds::CommentVIP(id,act)");
                fclose($file);
            }
            exit();
        }
        return FALSE;
    }

    /**
     * Добавить в платную ленту
     * @param type $id ID объявления
     * @param Int $act Действие
     * @return boolean Удалось или нет
     */
    public function PhotoPaid($id, $act) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        $act = filter_var($act, FILTER_VALIDATE_INT, array("options" => array("min-range" => 0, "max-range" => 1)));
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare('UPDATE k_photodesk SET k_pd_paid=:act WHERE k_pd_id=:id');
            $queue->execute(array(':act' => $act, ':id' => $id));
            return TRUE;
        } catch (PDOException $e) {
            if (file_exists('../../logs/mysql_logs')) {
                $file = fopen('../../logs/mysql_logs', "a");
                fwrite($file, "\r\n" . $e->getMessage() . " --- " . date('Y-m-d H:i:s', time()) . " --- " . "admin/admin_photo/inc/classes.php PhotoAds::CommentPaid(id,act)");
                fclose($file);
            }
            exit();
        }
        return FALSE;
    }

    public function PhotoUp($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare('UPDATE k_photodesk SET k_pd_up_date=NOW() WHERE k_pd_id=:id');
            $queue->execute(array(':id' => $id));
            return TRUE;
        } catch (PDOException $e) {
            if (file_exists('../../logs/mysql_logs')) {
                $file = fopen('../../logs/mysql_logs', "a");
                fwrite($file, "\r\n" . $e->getMessage() . " --- " . date('Y-m-d H:i:s', time()) . " --- " . "admin/admin_photo/inc/classes.php PhotoAds::PhotoUp(id)");
                fclose($file);
            }
            exit();
        }
        return FALSE;
    }

    public function BlockIP($ip) {
        $ip = filter_var($ip, FILTER_SANITIZE_STRIPPED);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare('SELECT k_ubil_id FROM k_users_ban_ip_list WHERE k_ubil_ip=:ip');
            $queue->execute(array(':ip' => $ip));
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            if (count($result) > 0) {
                return 2;
            }
            $queue2 = $mysql->prepare('INSERT INTO k_users_ban_ip_list (k_ubil_ip) VALUES (:ip)');
            $queue2->execute(array(':ip' => $ip));
            return 1;
        } catch (PDOException $e) {
            if (file_exists('../../logs/mysql_logs')) {
                $file = fopen('../../logs/mysql_logs', "a");
                fwrite($file, "\r\n" . $e->getMessage() . " --- " . date('Y-m-d H:i:s', time()) . " --- " . "admin/admin_photo/inc/classes.php PhotoAds::BlockIP(ip)");
                fclose($file);
            }
            exit();
        }
        return 0;
    }

    public function DeleteAd($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue1 = $mysql->prepare('DELETE FROM k_photodesk WHERE k_pd_id=:id');
            $queue1->execute(array(':id' => $id));
            $queue2 = $mysql->prepare('DELETE FROM k_photodesk_comments WHERE k_pc_photodesk_id=:id');
            $queue2->execute(array(':id' => $id));
            $queue3 = $mysql->prepare('SELECT k_pdp_link FROM k_photodesk_photos WHERE k_pdp_ad_id=:id');
            $queue3->execute(array(':id' => $id));
            $result = $queue3->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $value) {
                unlink('../../' . $value['k_pdp_link']);
            }
            $queue4 = $mysql->prepare('DELETE FROM k_photodesk_photos WHERE k_pdp_ad_id=:id');
            $queue4->execute(array(':id' => $id));
            return TRUE;
        } catch (PDOException $e) {
            if (file_exists('../../logs/mysql_logs')) {
                $file = fopen('../../logs/mysql_logs', "a");
                fwrite($file, "\r\n" . $e->getMessage() . " --- " . date('Y-m-d H:i:s', time()) . " --- " . "admin/admin_photo/inc/classes.php PhotoAds::DeleteAd(id)");
                fclose($file);
            }
            exit();
        }
        return FALSE;
    }

    function GenerateNavigation($page, $where, $link) {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue1 = $mysql->prepare('SELECT count(k_pd_id) as max FROM k_photodesk as kpd LEFT JOIN k_users AS ku ON (ku.k_ku_id = kpd.k_pd_user_id) ' . $where);
            $queue1->execute();
            $row = $queue1->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        $pages = intval($row['max'] / 50);
        if ($row['max'] % 50 != 0) {
            $pages++;
        }
        if ($pages > 1) {
            echo '<div class="listing">';
            if ($page == $pages || $page > 1) {
                echo '<a class="style_listing" href="./?PageType=1&PageIndex=' . ($page - 1) . $link . '">Предыдущая</a>';
            }

            if ($pages <= 11) {
                for ($i = 1; $i <= $pages; $i++) {
                    if ($i == $page) {
                        echo '<a class="active_listing" href="./?PageType=1&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    } else {
                        echo '<a class="style_listing" href="./?PageType=1&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
            } else {
                if ($page <= 6) {
                    for ($i = 1; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageType=1&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageType=1&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageType=1&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
                if ($page > 6 && $page <= ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageType=1&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $page - 2; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageType=1&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageType=1&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageType=1&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
                if ($page > ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageType=1&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 5; $i <= $pages; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageType=1&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageType=1&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                }
            }

            if ($page == 1 || $page < $pages) {
                echo '<a class="style_listing" href="./?PageType=1&PageIndex=' . ($page + 1) . $link . '">Следующая</a>';
            }

            echo '</div>';
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

    protected function setTheme($val, $i) {
        $this->theme[$i] = filter_var($val, FILTER_SANITIZE_STRIPPED);
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
    
    protected function setOnMain($val, $i) {
        $this->on_main[$i] = filter_var($val, FILTER_VALIDATE_INT);
    }

    public function getOnMain($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->on_main;
        } else {
            return $this->on_main[$i];
        }
    }
    
    protected function setColorLight($val, $i) {
        $this->color_light[$i] = filter_var($val, FILTER_VALIDATE_INT);
    }

    public function getColorLight($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->color_light;
        } else {
            return $this->color_light[$i];
        }
    }

    protected function setText($val, $i) {
        $this->text[$i] = filter_var($val, FILTER_SANITIZE_STRIPPED);
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

class PhotoComments {

    protected $id = array();
    protected $text = array();
    protected $date = array();
    protected $photo_id = array();
    protected $user_id = array();
    protected $user_str = array();

    /**
     * Загрузить комментарии
     * @param Int $id ID объявления
     */
    public function LoadComments($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare('SELECT kpc.*, ku.k_ku_login FROM k_photodesk_comments AS kpc
                LEFT JOIN k_users AS ku ON (ku.k_ku_id = kpc.k_pc_user_id)
                WHERE k_pc_photodesk_id=:id
                ORDER BY k_pc_date DESC');
            $queue->execute(array(':id' => $id));
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            if (file_exists('../../logs/mysql_logs')) {
                $file = fopen('../../logs/mysql_logs', "a");
                fwrite($file, "\r\n" . $e->getMessage() . " --- " . date('Y-m-d H:i:s', time()) . " --- " . "admin/admin_photo/inc/classes.php PhotoComments::LoadComments(id)");
                fclose($file);
            }
            exit();
        }
        $i = 1;
        foreach ($result as $value) {
            $this->setId($value['k_pc_id'], $i);
            $this->setText($value['k_pc_text'], $i);
            $this->setPhotoId($value['k_pc_photodesk_id'], $i);
            $this->setDate($value['k_pc_date'], $i);
            $this->setUserId($value['k_pc_user_id'], $i);
            $this->setUserStr($value['k_ku_login'], $i);
            $i++;
        }
    }

    /**
     * Удалить комментарий
     * @param Int $id ID комментария
     * @return boolean Удалось или нет
     */
    public function DeleteComment($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare('DELETE FROM k_photodesk_comments WHERE k_pc_id=:id');
            $queue->execute(array(':id' => $id));
            return TRUE;
        } catch (PDOException $e) {
            if (file_exists('../../logs/mysql_logs')) {
                $file = fopen('../../logs/mysql_logs', "a");
                fwrite($file, "\r\n" . $e->getMessage() . " --- " . date('Y-m-d H:i:s', time()) . " --- " . "admin/admin_photo/inc/classes.php PhotoComments::DeleteComment(id)");
                fclose($file);
            }
            exit();
        }
        return FALSE;
    }

    protected function setId($val, $i) {
        $this->id[$i] = filter_var($val, FILTER_VALIDATE_INT);
    }

    public function getId($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->id;
        } else {
            return $this->id[$i];
        }
    }

    protected function setText($val, $i) {
        $this->text[$i] = filter_var($val, FILTER_SANITIZE_STRIPPED);
    }

    public function getText($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->text;
        } else {
            return $this->text[$i];
        }
    }

    protected function setDate($val, $i) {
        $this->date[$i] = filter_var($val, FILTER_SANITIZE_STRIPPED);
    }

    public function getDate($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->date;
        } else {
            return $this->date[$i];
        }
    }

    protected function setPhotoId($val, $i) {
        $this->photo_id[$i] = filter_var($val, FILTER_VALIDATE_INT);
    }

    public function getPhotoId($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->photo_id;
        } else {
            return $this->photo_id[$i];
        }
    }

    protected function setUserId($val, $i) {
        $this->user_id[$i] = filter_var($val, FILTER_VALIDATE_INT);
    }

    public function getUserId($i) {
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

}

class CatalogCategoriesP {

    protected $id = array();
    protected $name = array();
    protected $count = array();

    function LoadCategories() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare('SELECT kpd.*, count(k_pd_id) as count
                FROM k_photodesk_categories AS kpd
                LEFT JOIN k_photodesk AS kp ON (kp.k_pd_category = kpd.k_pdc_id)
                GROUP BY k_pdc_id
                ORDER BY k_pdc_name ASC');
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        $i = 1;
        foreach ($result as $value) {
            $this->setId($value['k_pdc_id'], $i);
            $this->setName($value['k_pdc_name'], $i);
            $this->setCount($value['count'], $i);
            $i++;
        }
    }

    function DeleteCategory($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare('SELECT k_pdp_link FROM k_photodesk AS kp
                INNER JOIN k_photodesk_photos AS kpp ON (kpp.k_pdp_ad_id = kp.k_pd_id)
                WHERE k_pd_category=:id');
            $queue->execute(array(':id' => $id));
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $value) {
                $filename = '../../' . $value['k_pdp_link'];
                echo $filename;
                if (file_exists($filename)) {
                    unlink($filename);
                }
            }
            $queue4 = $mysql->prepare('DELETE FROM k_photodesk_photos WHERE k_pdp_ad_id IN (SELECT k_pd_id FROM k_photodesk WHERE k_pd_category=:id)');
            $queue4->execute(array(':id' => $id));
            $queue2 = $mysql->prepare('DELETE FROM k_photodesk WHERE k_pd_category=:id');
            $queue2->execute(array(':id' => $id));
            $queue3 = $mysql->prepare('DELETE FROM k_photodesk_categories WHERE k_pdc_id=:id');
            $queue3->execute(array(':id' => $id));
            echo 'yes';
        } catch (PDOException $e) {
            exit();
        }
    }

    //Set'n'Get
    protected function setName($val, $i) {
        $this->name[$i] = filter_var($val, FILTER_SANITIZE_STRIPPED);
    }

    public function getName($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->name;
        } else {
            return $this->name[$i];
        }
    }

    protected function setId($val, $i) {
        $this->id[$i] = filter_var($val, FILTER_VALIDATE_INT);
    }

    public function getId($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->id;
        } else {
            return $this->id[$i];
        }
    }

    protected function setCount($val, $i) {
        $this->count[$i] = filter_var($val, FILTER_VALIDATE_INT);
    }

    public function getCount($i) {
        $i = filter_var($i, FILTER_VALIDATE_INT);
        if ($i == 0) {
            return $this->count;
        } else {
            return $this->count[$i];
        }
    }

}

class BannersAll {

    public $banner_code = array();
    public $banner_id = array();
    public $banner_end_date = array();
    public $banner_end_days = array();
    public $banner_organization = array();
    public $banner_contact_name = array();
    public $banner_contacts = array();

    /**
     * Загрузить баннеры главной страницы
     * @param int $id Загрузить конкретный баннер
     */
    public function BannersAll($id=0) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        $query_1 = 'SELECT * FROM k_all_banners WHERE k_ab_type=3';
        if (!empty($id)) {
            $query_1 .= ' AND k_ab_id=' . $id;
        }
        $query_1 .= ' ORDER BY k_ab_id ASC';
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare($query_1);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        foreach ($result as $row) {
            $this->banner_code[] = $row['k_ab_code'];
            $this->banner_id[] = $row['k_ab_id'];
            $this->banner_end_date[] = $row['k_ab_end_date'];
            $this->banner_organization[] = $row['k_ab_organization'];
            $this->banner_contact_name[] = $row['k_ab_contact_name'];
            $this->banner_contacts[] = $row['k_ab_contacts'];
            $this->banner_end_days[] = round((strtotime($row['k_ab_end_date']) - time()) / 86400, 0) < 0 ? 0 : round((strtotime($row['k_ab_end_date']) - time()) / 86400, 0);
        }
    }

}
?>
