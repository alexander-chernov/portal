<?php

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
        $query_1 = 'SELECT * FROM k_all_banners WHERE k_ab_type=10';
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

class Blog {

    public $id = array();
    public $category = array();
    public $category_name = array();
    public $image = array();
    public $name = array();
    public $brief = array();
    public $text = array();
    public $user = array();
    public $date = array();
    public $views = array();
    public $on_main = array();
    public $state = array();
    public $all = 0;

    /**
     * Загрузить баннеры главной страницы
     * @param int $id Загрузить конкретный баннер
     */
    public function Blog($page, $where, $admin, $limit='') {
        if (!$limit) {
            $limit = 10;
        }
        if ($admin != 1) {
            $where = 'WHERE k_b_text!="" ' . $where;
        } elseif (!empty($where)) {
            $where = 'WHERE (k_b_text!="" OR k_b_text="") ' . $where;
        }
        $page = filter_var($page, FILTER_VALIDATE_INT);
        $query_1 = 'SELECT * FROM k_blog AS kb
            LEFT JOIN k_blog_categories AS kbc ON (kbc.k_bc_id = kb.k_b_category)
            ' . $where . '
            ORDER BY k_b_date DESC
            LIMIT ' . (($page - 1) * $limit) . ',' . $limit;
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare($query_1);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            $query1 = $mysql->prepare('SELECT count(*) AS num FROM k_blog AS kb
            LEFT JOIN k_blog_categories AS kbc ON (kbc.k_bc_id = kb.k_b_category)
            ' . $where);
            $query1->execute();
            $result1 = $query1->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        foreach ($result as $row) {
            $this->id[] = $row['k_b_id'];
            $this->category[] = $row['k_b_category'];
            $this->category_name[] = $row['k_bc_name'];
            $this->image[] = $row['k_b_image'];
            $this->brief[] = $row['k_b_brief'];
            $this->text[] = $row['k_b_text'];
            $this->user[] = $row['k_b_user'];
            $this->name[] = $row['k_b_name'];
            $this->date[] = $row['k_b_date'];
            $this->views[] = $row['k_b_views'];
            $this->on_main[] = $row['k_b_main_page'];
            $this->state[] = $row['k_b_state'];
        }
        $this->all = $result1['num'];
    }

    function GenerateNavigation($page, $where, $link, $limit='') {
        if (!$limit) {
            $limit = 10;
        }
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue1 = $mysql->prepare('SELECT * FROM k_blog AS kb
            LEFT JOIN k_blog_categories AS kbc ON (kbc.k_bc_id = kb.k_b_category)
            ' . $where);
            $queue1->execute();
            $row['max'] = $queue1->rowCount();
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
                echo '<a class="style_listing" href="./?PageIndex=' . ($page - 1) . $link . '">Предыдущая</a>';
            }

            if ($pages <= 11) {
                for ($i = 1; $i <= $pages; $i++) {
                    if ($i == $page) {
                        echo '<a class="active_listing" href="./?PageIndex=' . $i . $link . '">' . $i . '</a>';
                    } else {
                        echo '<a class="style_listing" href="./?PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
            } else {
                if ($page <= 6) {
                    for ($i = 1; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
                if ($page > 6 && $page <= ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $page - 2; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
                if ($page > ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 5; $i <= $pages; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                }
            }

            if ($page == 1 || $page < $pages) {
                echo '<a class="style_listing" href="./?PageIndex=' . ($page + 1) . $link . '">Следующая</a>';
            }

            echo '</div>';
        }
    }

}

class BlogCategories {

    public $id = array();
    public $name = array();
    public $count = array();
    public $all = 0;

    public function BlogCategories() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT kbc.*, count(k_b_id) AS num
                FROM k_blog_categories AS kbc
                LEFT JOIN k_blog AS kb ON (kb.k_b_category = kbc.k_bc_id AND k_b_text!="" AND k_b_state=1)
                GROUP BY k_bc_id
                ORDER BY k_bc_name ASC');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        foreach ($result as $row) {
            $this->id[] = $row['k_bc_id'];
            $this->name[] = $row['k_bc_name'];
            $this->count[] = $row['num'];
            $this->all += $row['num'];
        }
    }

}

?>
