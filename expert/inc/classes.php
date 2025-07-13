<?php

class ExpertT {

    public $id = array();
    public $fid = array();
    public $brief = array();
    public $address = array();
    public $address_str = array();
    public $phone = array();
    public $site = array();
    public $email = array();
    public $theme = array();
    public $header = array();
    public $description = array();
    public $ex_cat = array();
    public $all_cat = array();
    public $watches = array();
    public $regdate = array();
    public $avatar = array();
    public $find_col = 0;
    public $online = array();

    /**
     * Загрузить экспертов
     */
    public function LoadExperts($limit, $page, $where) {
        $limit = filter_var($limit, FILTER_VALIDATE_INT);
        $page = filter_var($page, FILTER_VALIDATE_INT);
        $where = filter_var($where, FILTER_SANITIZE_STRIPPED);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = 'SELECT ke.*, kshn.k_shn_house_num, ks.k_s_name, mb.fid
                FROM k_experts AS ke
                LEFT JOIN k_experts_categories_links AS kecl ON (kecl.k_ecl_expert_id = ke.k_e_id)
                LEFT JOIN k_streets_house_nums AS kshn ON (kshn.k_shn_id = ke.k_e_address)
                LEFT JOIN k_streets AS ks ON (ks.k_s_id = kshn.k_shn_street_id)
                LEFT JOIN map_buildings AS mb ON (mb.k_shn_id = kshn.k_shn_id)
                WHERE k_e_active=1 AND k_e_verified=1
                ' . $where . '
                GROUP BY ke.k_e_id
                ORDER BY k_e_up_date DESC, k_e_date DESC';
            if ($limit != 0) {
                $queue .= ' LIMIT ' . (($page - 1) * $limit) . ',' . $limit;
            }
            $query = $mysql->prepare($queue);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            $query2 = $mysql->prepare('SELECT count(DISTINCT k_e_id) AS num
                FROM k_experts AS ke
                LEFT JOIN k_experts_categories_links AS kecl ON (kecl.k_ecl_expert_id = ke.k_e_id)
                WHERE k_e_active=1 AND k_e_verified=1
                ' . $where);
            $query2->execute();
            $result2 = $query2->fetch(PDO::FETCH_ASSOC);
            $this->find_col = $result2['num'];
        } catch (PDOException $e) {
            exit();
        }
        foreach ($result as $row) {
            $this->id[] = $row['k_e_id'];
            $this->fid[] = $row['fid'];
            $this->brief[] = $row['k_e_brief'];
            $this->address[] = $row['k_e_address'];
            if (preg_match('/(###)/', $row['k_s_name'])) {
                $street = explode('###', $row['k_s_name']);
                $house = explode('###', $row['k_shn_house_num']);
                array_push($this->address_str, $street[0] . ' ' . $house[0] . ' / ' . $street[1] . ' ' . $house[1]);
            } else {
                array_push($this->address_str, $row['k_s_name'] . ' ' . $row['k_shn_house_num']);
            }
            $this->phone[] = $row['k_e_phone'];
            $this->site[] = $row['k_e_site'];
            $this->email[] = $row['k_e_email'];
            $this->theme[] = $row['k_e_theme'];
            $this->header[] = $row['k_e_header'];
            $this->description[] = $row['k_e_description'];
            $this->watches[] = $row['k_e_watches'];
            $this->regdate[] = $row['k_e_date'];
            $this->avatar[] = $row['k_e_image'];
            $this->online[] = $row['k_e_online'];
        }
    }

    function GenerateNavigation($page, $limit, $where) {
        $limit = filter_var($limit, FILTER_VALIDATE_INT, array("options" => array("MIN_RANGE" => 1, "MAX_RANGE" => 50)));
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue1 = $mysql->prepare('SELECT * FROM k_experts AS ke
                LEFT JOIN k_experts_categories_links AS kecl ON (kecl.k_ecl_expert_id = ke.k_e_id)
                WHERE k_e_active=1 AND k_e_verified=1 ' . $where . '
                GROUP BY ke.k_e_id');
            $queue1->execute();
            $row['max'] = $queue1->rowCount();
            //$row = $queue1->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        if ($row['max'] == 0) {
            $row['max'] = 1;
        }
        $pages = intval($row['max'] / $limit);
        if ($row['max'] % $limit != 0) {
            $pages++;
        }
        if ($pages != 1) {
            echo '<div class="listing">';
            if ($page == $pages || $page > 1) {
                echo '<a class="style_listing" href="../expert/?PageType=1&Limit=' . $limit . '&PageIndex=' . ($page - 1) . '">Предыдущая</a>';
            }

            if ($pages <= 11) {
                for ($i = 1; $i <= $pages; $i++) {
                    if ($i == $page) {
                        echo '<a class="active_listing" href="../expert/?PageType=1&Limit=' . $limit . '&PageIndex=' . $i . '">' . $i . '</a>';
                    } else {
                        echo '<a class="style_listing" href="../expert/?PageType=1&Limit=' . $limit . '&PageIndex=' . $i . '">' . $i . '</a>';
                    }
                }
            } else {
                if ($page <= 6) {
                    for ($i = 1; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="../expert/?PageType=1&Limit=' . $limit . '&PageIndex=' . $i . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="../expert/?PageType=1&Limit=' . $limit . '&PageIndex=' . $i . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="../expert/?PageType=1&Limit=' . $limit . '&PageIndex=' . $i . '">' . $i . '</a>';
                    }
                }
                if ($page > 6 && $page <= ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="../expert/?PageType=1&Limit=' . $limit . '&PageIndex=' . $i . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $page - 2; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="../expert/?PageType=1&Limit=' . $limit . '&PageIndex=' . $i . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="../expert/?PageType=1&Limit=' . $limit . '&PageIndex=' . $i . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="../expert/?PageType=1&Limit=' . $limit . '&PageIndex=' . $i . '">' . $i . '</a>';
                    }
                }
                if ($page > ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="../expert/?PageType=1&Limit=' . $limit . '&PageIndex=' . $i . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 5; $i <= $pages; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="../expert/?PageType=1&Limit=' . $limit . '&PageIndex=' . $i . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="../expert/?PageType=1&Limit=' . $limit . '&PageIndex=' . $i . '">' . $i . '</a>';
                        }
                    }
                }
            }

            if ($page == 1 || $page < $pages) {
                echo '<a class="style_listing" href="../expert/?PageType=1&Limit=' . $limit . '&PageIndex=' . ($page + 1) . '">Следующая</a>';
            }

            echo '</div>';
        }
    }

    /**
     * Загрузить категории
     */
    public function LoadCategories() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT * FROM k_experts_categories ORDER BY k_ec_name ASC');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        $n = 0;
        foreach ($result as $row) {
            $this->all_cat[$n] = $row['k_ec_name'];
            $n++;
        }
    }

}

class LeftBlock {

    protected $id = array();
    protected $name = array();
    protected $count = array();

    function LoadBlock() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT kec.*, count(kel.k_ecl_id) AS count
                FROM k_experts_categories AS kec
                LEFT JOIN k_experts_categories_links AS kel ON (kel.k_ecl_category_id = kec.k_ec_id)
                GROUP BY kec.k_ec_name
                ORDER BY k_ec_name ASC');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            $i = 1;
            foreach ($result as $value) {
                $this->setID($value['k_ec_id'], $i);
                $this->setName($value['k_ec_name'], $i);
                $this->setCount($value['count'], $i);
                $i++;
            }
        } catch (PDOException $e) {
            exit();
        }
    }

    //Set'n'Get
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

    public function getAllCount() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT count(*) AS num FROM k_experts');
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['num'];
        } catch (PDOException $e) {
            exit();
        }
        return 0;
    }

}

class QuestionAnswer {

    public $ans_id = array();
    public $ques_id = array();
    public $name = array();
    public $email = array();
    public $text_q = array();
    public $text_a = array();
    public $date = array();

    function Load($e_id) {
        $e_id = filter_var($e_id, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT * FROM k_experts_questions AS keq
                LEFT JOIN k_experts_answers AS kea ON (kea.k_ea_question_id = keq.k_eq_id)
                WHERE k_eq_expert_id=:id
                ORDER BY k_eq_datetime DESC
                LIMIT 30');
            $query->execute(array(":id" => $e_id));
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $value) {
                array_push($this->ans_id, $value['k_ea_id']);
                array_push($this->ques_id, $value['k_eq_id']);
                array_push($this->name, $value['k_eq_name']);
                array_push($this->email, $value['k_eq_email']);
                array_push($this->text_q, $value['k_eq_text']);
                array_push($this->text_a, $value['k_ea_text']);
                array_push($this->date, $value['k_eq_datetime']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

?>
