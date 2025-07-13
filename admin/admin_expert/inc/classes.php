<?php

/**
 * Построение таблицы экспертов
 */
class TableExpertsBuild {

    public $id = array();
    public $brief = array();
    public $address = array();
    public $phone = array();
    public $site = array();
    public $email = array();
    public $theme = array();
    public $header = array();
    public $description = array();
    public $ex_cat = array();
    public $all_cat = array();
    public $all_cat_id = array();
    public $all_cat_count = array();
    public $regdate = array();
    public $avatar = array();
    public $state = array();
    public $on_main = array();

    /**
     * Загрузить экспертов
     */
    public function LoadExperts($page, $where) {
        if ($page != 0) {
            $page = filter_var($page, FILTER_VALIDATE_INT);
            $where = filter_var($where, FILTER_SANITIZE_STRIPPED);
            try {
                $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
                $mysql->exec('set names utf8');
                $query = $mysql->prepare('SELECT * FROM k_experts
                    ' . $where . '
                    ORDER BY k_e_date DESC
                    LIMIT ' . (($page - 1) * 50) . ',50');
                $query->execute();
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                exit();
            }
            $n = 0;
            foreach ($result as $row) {
                $this->id[$n] = $row['k_e_id'];
                $this->brief[$n] = $row['k_e_brief'];
                $this->address[$n] = $row['k_e_address'];
                $this->phone[$n] = $row['k_e_phone'];
                $this->site[$n] = $row['k_e_site'];
                $this->email[$n] = $row['k_e_email'];
                $this->theme[$n] = $row['k_e_theme'];
                $this->header[$n] = $row['k_e_header'];
                $this->description[$n] = $row['k_e_description'];
                $this->regdate[$n] = $row['k_e_date'];
                $this->avatar[$n] = $row['k_e_image'];
                $this->state[$n] = $row['k_e_active'];
                $this->on_main[$n] = $row['k_e_main_page'];
                $n++;
            }
        }
    }

    function GenerateNavigation($page, $where, $link) {
        $page = filter_var($page, FILTER_VALIDATE_INT);
        $where = filter_var($where, FILTER_SANITIZE_STRIPPED);
        $link = filter_var($link, FILTER_SANITIZE_STRIPPED);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue1 = $mysql->prepare('SELECT * FROM k_experts ' . $where);
            $queue1->execute();
            $row['max'] = $queue1->rowCount();
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

    /**
     * Загрузить категории
     */
    public function LoadCategories() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT k_ec_name,k_ec_id,count(kecl.k_ecl_expert_id) AS num
                FROM k_experts_categories AS kec
                LEFT JOIN k_experts_categories_links AS kecl ON (kecl.k_ecl_category_id = kec.k_ec_id)
                GROUP BY k_ec_id
                ORDER BY k_ec_name ASC');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        foreach ($result as $row) {
            $this->all_cat[] = $row['k_ec_name'];
            $this->all_cat_id[] = $row['k_ec_id'];
            $this->all_cat_count[] = $row['num'];
        }
    }

    /**
     * Проверить категории эксперта на "checked" и вывести их список
     * @param int $id ID эксперта
     */
    public function CompareCategories($id) {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT k_ec_name
                FROM k_experts as ex
                LEFT JOIN k_experts_categories_links as el ON (ex.k_e_id = el.k_ecl_expert_id)
                LEFT JOIN k_experts_categories as ec ON (el.k_ecl_category_id = ec.k_ec_id)
                WHERE k_e_id=:id
                ORDER BY k_ec_name ASC');
            $query->execute(array(':id' => $id));
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        $a = 0;
        foreach ($result as $row) {
            $this->ex_cat[$a] = $row['k_ec_name'];
            $a++;
        }
        echo '<ul class="access_list">';
        for ($a = 0; $a < count($this->all_cat); $a++) {
            if (in_array($this->all_cat[$a], $this->ex_cat)) {
                echo '<li><label><input type="checkbox" name="ex[]" value="' . $this->all_cat_id[$a] . '" checked>' . $this->all_cat[$a] . '</label></li>';
            } else {
                echo '<li><label><input type="checkbox" name="ex[]" value="' . $this->all_cat_id[$a] . '">' . $this->all_cat[$a] . '</label></li>';
            }
        }
        echo '</ul>';
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
        $query_1 = 'SELECT * FROM k_all_banners WHERE k_ab_type=4';
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
