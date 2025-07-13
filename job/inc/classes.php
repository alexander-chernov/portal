<?php

class JobAdsSite {

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
    public $avatar = array();
    public $marital = array();
    public $views = array();
    public $all_req = 0;
    public $all_search = 0;

    /**
     * Загрузить объявления
     * @param int $limit Число объявлений на странице
     * @param int $page Номер страницы
     * @param array $where = array(array('column1','column2'),array(':var1',':var2'),array('AND','OR'),array('value1','value2'));
     */
    function LoadAds($limit, $page, $where, $where_tod) {
        $limit = filter_var($limit, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => 50)));
        $page = filter_var($page, FILTER_VALIDATE_INT);
        //$query = 'SELECT kj.*, kjc.k_jc_name, kjo.k_jo_name, ku.k_ku_login, kjp.k_jp_avatar, kjp.k_jp_marital
        $query = 'SELECT kj.*, kjc.k_jc_name, kjo.k_jo_name, ku.k_ku_login, kjp.k_jp_avatar, kjp.k_jp_marital, kje.k_je_name
            FROM k_job AS kj
            LEFT JOIN k_job_currency AS kjc ON (kjc.k_jc_id = kj.k_j_currency)
            LEFT JOIN k_job_organizations AS kjo ON (kjo.k_jo_job_id = kj.k_j_id)
            LEFT JOIN k_job_person AS kjp ON (kjp.k_jp_job_id = kj.k_j_id)
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
            $query .= ' AND k_j_state=1 AND k_j_date_end>NOW() ';
        } else {
            $query .= ' WHERE k_j_state=1 AND k_j_date_end>NOW() ';
        }
        $query .= $where_tod;
        $query .= ' GROUP BY k_j_id
            ORDER BY k_j_up_date DESC ';
        if ($limit != 0) {
            $query .= ' LIMIT ' . (($page - 1) * $limit) . ',' . $limit;
        }
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare($query);
            //echo $query;
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
                array_push($this->views, $value['k_j_views']);
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
                array_push($this->avatar, $value['k_jp_avatar']);
                array_push($this->marital, $value['k_jp_marital']);
                array_push($this->user_login, $value['k_ku_login']);
                array_push($this->email, $value['k_j_email']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

    function LoadStat() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue2 = $mysql->prepare('SELECT k_j_type,count(*) AS max FROM k_job WHERE k_j_state=1 AND k_j_date_end>NOW() GROUP BY k_j_type ORDER BY k_j_type ASC');
            $queue2->execute();
            $result2 = $queue2->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result2 as $value) {
                if ($value['k_j_type'] == 1) {
                    $this->all_req = $value['max'];
                }
                if ($value['k_j_type'] == 2) {
                    $this->all_search = $value['max'];
                }
            }
        } catch (PDOException $e) {
            exit();
        }
    }

    function GenerateNavigation($page, $where, $PageType, $limit) {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue1 = $mysql->prepare('SELECT count(k_j_id) as max FROM k_job ' . $where);
            $queue1->execute();
            $row = $queue1->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        $pages = intval($row['max'] / $limit);
        if ($row['max'] % $limit != 0) {
            $pages++;
        }
        if ($pages > 1) {
            echo '<div class="listing">';
            if ($page == $pages || $page > 1) {
                echo '<a class="style_listing" href="../job/?PageType=' . $PageType . '&PageIndex=' . ($page - 1) . '&limit=' . $limit . '">Предыдущая</a>';
            }

            if ($pages <= 11) {
                for ($i = 1; $i <= $pages; $i++) {
                    if ($i == $page) {
                        echo '<a class="active_listing" href="../job/?PageType=' . $PageType . '&PageIndex=' . $i . '&limit=' . $limit . '">' . $i . '</a>';
                    } else {
                        echo '<a class="style_listing" href="../job/?PageType=' . $PageType . '&PageIndex=' . $i . '&limit=' . $limit . '">' . $i . '</a>';
                    }
                }
            } else {
                if ($page <= 6) {
                    for ($i = 1; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="../job/?PageType=' . $PageType . '&PageIndex=' . $i . '&limit=' . $limit . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="../job/?PageType=' . $PageType . '&PageIndex=' . $i . '&limit=' . $limit . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="../job/?PageType=' . $PageType . '&PageIndex=' . $i . '&limit=' . $limit . '">' . $i . '</a>';
                    }
                }
                if ($page > 6 && $page <= ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="../job/?PageType=' . $PageType . '&PageIndex=' . $i . '&limit=' . $limit . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $page - 2; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="../job/?PageType=' . $PageType . '&PageIndex=' . $i . '&limit=' . $limit . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="../job/?PageType=' . $PageType . '&PageIndex=' . $i . '&limit=' . $limit . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="../job/?PageType=' . $PageType . '&PageIndex=' . $i . '&limit=' . $limit . '">' . $i . '</a>';
                    }
                }
                if ($page > ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="../job/?PageType=' . $PageType . '&PageIndex=' . $i . '&limit=' . $limit . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 5; $i <= $pages; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="../job/?PageType=' . $PageType . '&PageIndex=' . $i . '&limit=' . $limit . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="../job/?PageType=' . $PageType . '&PageIndex=' . $i . '&limit=' . $limit . '">' . $i . '</a>';
                        }
                    }
                }
            }

            if ($page == 1 || $page < $pages) {
                echo '<a class="style_listing" href="../job/?PageType=' . $PageType . '&PageIndex=' . ($page + 1) . '&limit=' . $limit . '">Следующая</a>';
            }

            echo '</div>';
        }
    }

    function AddViews($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue1 = $mysql->prepare('SELECT k_j_views FROM k_job WHERE k_j_id=:id');
            $queue1->execute(array(":id" => $id));
            $row = $queue1->fetch(PDO::FETCH_ASSOC);
            $queue2 = $mysql->prepare('UPDATE k_job SET k_j_views=:views WHERE k_j_id=:id');
            $queue2->execute(array(":id" => $id, ":views" => ($row['k_j_views'] + 1)));
        } catch (PDOException $e) {
            exit();
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

?>
