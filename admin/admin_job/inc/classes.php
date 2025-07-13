<?php

class JobAds {

    public $id = array();
    public $user_id = array();
    public $user_login = array();
    public $type = array();
    public $salary_min = array();
    public $salary_max = array();
    public $currency = array();
    public $currency_str = array();
    public $sex = array();
    public $schedule = array();
    public $text = array();
    public $date_reg = array();
    public $date_end = array();
    public $post = array();
    public $age_min = array();
    public $age_max = array();
    public $education = array();
    public $education_t = array();
    public $education_t_str = array();
    public $exp = array();
    public $state = array();
    public $contact_name = array();
    public $contact_phone = array();
    public $email = array();
    public $organization_name = array();

    /**
     * Загрузить объявления
     * @param int $limit Число объявлений на странице
     * @param int $page Номер страницы
     * @param array $where = array(array('column1','column2'),array(':var1',':var2'),array('AND','OR'),array('value1','value2'));
     */
    function LoadAds($limit, $page, $where) {
        $limit = filter_var($limit, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => 50)));
        $page = filter_var($page, FILTER_VALIDATE_INT);
        $query = 'SELECT kj.*, kjc.k_jc_name, kjo.k_jo_name, ku.k_ku_login, kje.k_je_name
            FROM k_job AS kj
            LEFT JOIN k_job_currency AS kjc ON (kjc.k_jc_id = kj.k_j_currency)
            LEFT JOIN k_job_organizations AS kjo ON (kjo.k_jo_job_id = kj.k_j_id)
            LEFT JOIN k_users AS ku ON (ku.k_ku_id = kj.k_j_user_id)
            LEFT JOIN k_job_education AS kje ON (kje.k_je_id = kj.k_j_education_type) ';
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
        $query .= ' GROUP BY k_j_id
            ORDER BY k_j_date_reg DESC ';
        if ($limit != 0) {
            $query .= ' LIMIT ' . (($page - 1) * $limit) . ',' . $limit;
        }
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare($query);
            $queue->execute($vars);
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $value) {
                array_push($this->id, $value['k_j_id']);
                array_push($this->user_id, $value['k_j_user_id']);
                array_push($this->type, $value['k_j_type']);
                array_push($this->salary_min, $value['k_j_salary_min']);
                array_push($this->salary_max, $value['k_j_salary_max']);
                array_push($this->currency, $value['k_j_currency']);
                array_push($this->sex, $value['k_j_sex']);
                array_push($this->schedule, $value['k_j_schedule']);
                array_push($this->text, $value['k_j_text']);
                array_push($this->date_reg, $value['k_j_date_reg']);
                array_push($this->date_end, $value['k_j_date_end']);
                array_push($this->post, $value['k_j_post']);
                array_push($this->age_min, $value['k_j_age_min']);
                array_push($this->age_max, $value['k_j_age_max']);
                array_push($this->education, $value['k_j_education']);
                array_push($this->education_t, $value['k_j_education_type']);
                array_push($this->education_t_str, $value['k_je_name']);
                array_push($this->exp, $value['k_j_exp']);
                array_push($this->state, $value['k_j_state']);
                array_push($this->contact_name, $value['k_j_contact_name']);
                array_push($this->contact_phone, $value['k_j_contact_phone']);
                array_push($this->currency_str, $value['k_jc_name']);
                array_push($this->organization_name, $value['k_jo_name']);
                array_push($this->user_login, $value['k_ku_login']);
                array_push($this->email, $value['k_j_email']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

    function GenerateNavigation($page, $where, $link) {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue1 = $mysql->prepare('SELECT count(k_j_id) as max FROM k_job ' . $where);
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
        $query_1 = 'SELECT * FROM k_all_banners WHERE k_ab_type=6';
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
