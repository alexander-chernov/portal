<?php

class SearchOnMap {

    public $address = array();
    public $address_str = array();
    public $count = 0;

    public function SearchOnMap($input) {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $input = WrongLanguage(1, filter_var($input, FILTER_SANITIZE_STRIPPED));
            $input = str_replace('-', ' ', $input);
            $search_string = explode(" ", $input);
            $search_num = "";
            $a = 0;
            if (preg_match("(^[0-9])", $search_string[count($search_string) - 1]) && count($search_string) > 1) {
                $search_num = $search_string[count($search_string) - 1];
                $a = 1;
            }
            try {
                $cant_find = FALSE;
                $queue = 'SELECT * FROM k_streets_house_nums as kshn
                LEFT JOIN k_streets as ks ON (ks.k_s_id = kshn.k_shn_street_id)
                WHERE k_s_name LIKE CONCAT ("%", :addr, "%")';
                if ($search_num != "") {
                    $queue .= ' AND k_shn_house_num LIKE CONCAT ("' . $search_num . '", "%") ';
                }
                $queue .= 'ORDER BY k_shn_house_num ASC';
                $query = $mysql->prepare($queue);
                $query->execute(array(':addr' => RussianRules(str_replace($search_num, '', $input))));
                if ($query->rowCount() > 0) {
                    $row = $query->fetchAll(PDO::FETCH_ASSOC);
                } elseif (count($search_string) > 1) {
                    $queue = 'SELECT fin.* FROM (SELECT * FROM k_streets_house_nums as kshn
                LEFT JOIN k_streets as ks ON (ks.k_s_id = kshn.k_shn_street_id)
                WHERE k_s_name LIKE CONCAT ("%", :addr, "%")) AS fin
                WHERE k_s_name LIKE CONCAT ("%", :addr2, "%")';
                    if ($search_num != "") {
                        $queue .= ' AND k_shn_house_num LIKE CONCAT ("' . $search_num . '", "%") ';
                    }
                    $queue .= 'ORDER BY k_shn_house_num ASC';
                    $query = $mysql->prepare($queue);
                    $query->execute(array(":addr" => RussianRules($search_string[0]), ":addr2" => RussianRules($search_string[1])));
                    if ($query->rowCount() > 0) {
                        $row = $query->fetchAll(PDO::FETCH_ASSOC);
                    }
                } else {
                    $cant_find = TRUE;
                }
                if ($cant_find) {
                    for ($i = 0; $i < count($search_string); $i++) {
                        $query = $mysql->prepare('SELECT * FROM k_streets_house_nums as kshn
                        LEFT JOIN k_streets as ks ON (ks.k_s_id = kshn.k_shn_street_id)
                        WHERE k_s_name LIKE CONCAT ("%", :addr, "%") ORDER BY k_shn_house_num ASC');
                        $query->execute(array(':addr' => RussianRules($search_string[$i])));
                        $row = $query->fetchAll(PDO::FETCH_ASSOC);
                    }
                }
                $this->count = $query->rowCount();
                if ($query->rowCount() > 0) {
                    foreach ($row as $value) {
                        $street = array();
                        $house = array();
                        $this->address[] = $value['k_shn_id'];
                        if (preg_match('/(###)/', $value['k_s_name'])) {
                            $street = explode('###', $value['k_s_name']);
                            $house = explode('###', $value['k_shn_house_num']);
                            $this->address_str[] = $street[0] . ' ' . $house[0] . ' / ' . $street[1] . ' ' . $house[1];
                        } else {
                            $this->address_str[] = $value['k_s_name'] . ' ' . $value['k_shn_house_num'];
                        }
                    }
                }
            } catch (PDOException $e) {
                exit();
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

function WhereAddress($input) {
    $where .= ' OR (';
    $defis_cut = str_replace("-", " ", $input);
    $search_string = explode(" ", str_replace("-", " ", $defis_cut));
    $search_num = "";
    $a = 0;
    if (!preg_match('/^[а-Я]/', $search_string[count($search_string) - 1])) {
        $search_num = $search_string[count($search_string) - 1];
        $a = 1;
    }
    for ($i = 0; $i < count($search_string) - $a; $i++) {
        $where .= ' k_s_name LIKE "%' . trim($search_string[$i]) . '%" ';
        if ($i != (count($search_string) - $a - 1)) {
            $where .= ' OR ';
        }
    }
    $where .= ') ';
    if ($a != 0) {
        $where .= ' AND k_shn_house_num LIKE "%' . $search_num . '%" ';
    }
    return $where;
}

function WhereWordsPhoto($input) {
    $input = preg_replace('/\\s+/', ' ', $input);
    $search_array = explode(' ', $input);
    $where = ' WHERE (';
    foreach ($search_array as $value) {
        if (strlen($value) > 2) {
            $value = RussianRules($value);
        }
        $where .= ' k_pd_text LIKE "%' . $value . '%" OR k_pd_theme LIKE "%' . $value . '%" OR ';
    }
    $where = substr($where, 0, strlen($where) - 3);
    $where .= ') ';
    return $where;
}

function WhereWordsJob($input) {
    $input = preg_replace('/\\s+/', ' ', $input);
    $search_array = explode(' ', $input);
    $where = ' AND (';
    foreach ($search_array as $value) {
        if (strlen($value) > 2) {
            $value = RussianRules($value);
        }
        $where .= ' k_j_education LIKE "%' . $value . '%" OR k_j_exp LIKE "%' . $value . '%" OR k_j_text LIKE "%' . $value . '%" OR k_j_post LIKE "%' . $value . '%" OR k_j_schedule LIKE "%' . $value . '%" OR ';
    }
    $where = substr($where, 0, strlen($where) - 3);
    $where .= ') ';
    return $where;
}

function WhereWordsBlog($input) {
    $input = preg_replace('/\\s+/', ' ', $input);
    $search_array = explode(' ', $input);
    $where = ' WHERE (';
    foreach ($search_array as $value) {
        if (strlen($value) > 2) {
            $value = RussianRules($value);
        }
        $where .= ' k_b_name LIKE "%' . $value . '%" OR k_b_brief LIKE "%' . $value . '%" OR k_b_text LIKE "%' . $value . '%" OR ';
    }
    $where = substr($where, 0, strlen($where) - 3);
    $where .= ') AND  k_b_state=1 ';
    return $where;
}

function WhereWordsCatalog($input) {
    $input = preg_replace('/\\s+/', ' ', $input);
    $search_array = explode(' ', $input);
    $where = ' WHERE (';
    foreach ($search_array as $value) {
        if (strlen($value) > 2) {
            $value = RussianRules($value);
        }
        $where .= ' town LIKE "%' . $value . '%" OR
            name LIKE "%' . $value . '%" OR
            description LIKE "%' . $value . '%" OR
            address LIKE "%' . $value . '%" OR
            category LIKE "%' . $value . '%" OR
            subcategory LIKE "%' . $value . '%" OR
            site LIKE "%' . $value . '%" OR
            email LIKE "%' . $value . '%" OR
            phone1 LIKE "%' . $value . '%" OR
            phone2 LIKE "%' . $value . '%" OR
            phone3 LIKE "%' . $value . '%" OR
            phone4 LIKE "%' . $value . '%" ';
    }
    //$where = substr($where, 0, strlen($where) - 3);
    $where .= ')  ';
    //$where .= ') AND k_cfp_parent_id != 0 ';
    return $where;
}

function WhereWordsSites($input) {
    $input = preg_replace('/\\s+/', ' ', $input);
    $search_array = explode(' ', $input);
    $where = ' WHERE (';
    foreach ($search_array as $value) {
        if (strlen($value) > 2) {
            $value = RussianRules($value);
        }
        $where .= ' k_s_name LIKE "%' . $value . '%" OR k_s_url LIKE "%' . $value . '%" OR k_s_description LIKE "%' . $value . '%" OR ';
    }
    $where = substr($where, 0, strlen($where) - 3);
    $where .= ') ';
    return $where;
}

function plural_form($n, $form1, $form2) {
    $n = abs($n) % 100;
    $n1 = $n % 10;
    if ($n > 10 && $n < 20)
        return $form1;
    if ($n1 > 1 && $n1 < 5)
        return $form2;
    if ($n1 == 1)
        return $form1;
    return $form2;
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
    public $all = 0;

    /**
     * Загрузить баннеры главной страницы
     * @param int $id Загрузить конкретный баннер
     */
    public function Blog($page, $where, $admin, $limit) {
        if (!$limit) {
            $limit = 10;
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
        }
        $this->all = $result1['num'];
    }

    function GenerateNavigation($page, $where, $link, $limit) {
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
                LEFT JOIN k_blog AS kb ON (kb.k_b_category = kbc.k_bc_id AND k_b_text!="")
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

class SitesCategories {

    public $id = array();
    public $name = array();
    public $count = array();

    public function SitesCategories() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT ksc.*, count(DISTINCT k_sl_site_id) AS num
                FROM k_site_categories AS ksc
                LEFT JOIN k_sites_subcategories AS kss ON (ksc.k_sc_id = kss.k_ss_category)
                LEFT JOIN k_sites_links AS ksl ON (ksl.k_sl_sub_id = kss.k_ss_id)
                GROUP BY k_sc_id
                ORDER BY k_sc_name ASC');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                $this->id[] = $row['k_sc_id'];
                $this->name[] = $row['k_sc_name'];
                $this->count[] = $row['num'];
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class SitesSubcategories {

    public $id = array();
    public $id_parent = array();
    public $name = array();
    public $name_parent = array();
    public $count = array();

    public function SitesSubcategories($id, $where) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id != 0) {
            $where = ' AND k_sc_id=' . $id . ' ';
        }
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT ksc.*, kss.*, count(DISTINCT k_sl_site_id) AS num
                FROM k_site_categories AS ksc
                LEFT JOIN k_sites_subcategories AS kss ON (ksc.k_sc_id = kss.k_ss_category)
                LEFT JOIN k_sites_links AS ksl ON (ksl.k_sl_sub_id = kss.k_ss_id)
                WHERE k_ss_id IS NOT NULL ' . $where . '
                GROUP BY k_ss_id
                ORDER BY k_ss_name ASC');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                $this->id[] = $row['k_ss_id'];
                $this->name[] = $row['k_ss_name'];
                $this->id_parent[] = $row['k_sc_id'];
                $this->name_parent[] = $row['k_sc_name'];
                $this->count[] = $row['num'];
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class Sites {

    public $id = array();
    public $name = array();
    public $url = array();
    public $description = array();
    public $avatar = array();
    public $contact_name = array();
    public $contact_phone = array();
    public $email = array();
    public $date = array();
    public $state = array();
    public $subcategories = array();

    public function Sites($page, $where, $limit) {
        $page = filter_var($page, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = 'SELECT * FROM k_sites AS ks
                LEFT JOIN k_sites_links AS ksl ON (ksl.k_sl_site_id = ks.k_s_id)
                ' . $where . '
                GROUP BY k_s_id
                ORDER BY k_s_date DESC';
            if ($limit != 0) {
                $queue .= 'LIMIT ' . (($page - 1) * $limit) . ',' . $limit;
            }
            $query = $mysql->prepare($queue);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                $this->id[] = $row['k_s_id'];
                $this->name[] = $row['k_s_name'];
                $this->url[] = $row['k_s_url'];
                $this->description[] = $row['k_s_description'];
                $this->avatar[] = $row['k_s_image'];
                $this->contact_name[] = $row['k_s_contact_name'];
                $this->contact_phone[] = $row['k_s_contact_phone'];
                $this->email[] = $row['k_s_email'];
                $this->date[] = $row['k_s_date'];
                $this->state[] = $row['k_s_state'];
                $this->subcategories[] = new SitesSubcategories(0, ' WHERE k_sl_site_id=' . $row['k_s_id'] . ' ');
            }
        } catch (PDOException $e) {
            exit();
        }
    }

    function Refresh() {
        echo '<tr style="background: #7caed3;">
            <td colspan="2"><p class="style_5">№ сайта</p></td>
            <td><p class="style_5">фото</p></td>
            <td><p class="style_5">Адрес</p></td>
            <td><p class="style_5">Дата</p></td>
            <td><p class="style_5">Статус</p></td>
            <td><p class="style_5">Действие</p></td></tr>';
        for ($i = 0; $i < count($this->id); $i++) {
            echo '<tr style="background: #f0f4f4;">
                <td style="width: 20px;"><input type="checkbox" value=""></td>
                <td style="width: 80px;"><p class="style_4">' . $this->id[$i] . '</p></td>
                <td style="width: 80px;">';
            if ($this->avatar[$i] && (file_exists('../' . $this->avatar[$i]) || file_exists('../../' . $this->avatar[$i]))) {
                echo '<img class="img_ob" src="../' . $this->avatar[$i] . '" alt="' . $this->id[$i] . '">';
            } else {
                echo '<img class="img_ob" src="../images/noimage.png" alt="' . $this->id[$i] . '">';
            }
            echo '</td><td>
                <p class="style_4"><a target="_blank" href="' . $this->url[$i] . '">' . $this->url[$i] . '</a>
                </p></td><td><p class="style_4">' . $this->date[$i] . '</p></td><td>';
            if ($this->state[$i] == 0) {
                echo '<p class="style_4_2">Скрыт</p>';
            }
            if ($this->state[$i] == 1) {
                echo '<p class="style_4_1">Размещен</p>';
            }
            echo '</td><td>';
            if ($this->email[$i]) {
                echo '<a class="a_1" onclick="ShowEmailWindow(this);">
                    <img src="../images/send_email.png" title="Отправить E-mail" alt="' . $this->id[$i] . '"></a>';
            }
            echo '<a class="a_1" onclick="ShowSiteChange(this);">
                <img src="../images/edit.png" title="Редактировать" alt="' . $this->id[$i] . '"></a>
                <a class="a_1" onclick="ShowSitePhotos(this);"><img src="../images/photo.png" title="Редактировать фото" alt="' . $this->id[$i] . '"></a>
                <a class="a_1" onclick="ShowSiteCategories(this);">
                <img src="../images/add_tool.png" title="Редактировать категории" alt="' . $this->id[$i] . '"></a>
                <a class="a_1" onclick="ChangeSiteState(this);">';
            if ($this->state[$i] == 0) {
                echo '<img src="../images/disable_1.png" title="Разместить сайт" alt="' . $this->id[$i] . '">';
            }
            if ($this->state[$i] == 1) {
                echo '<img src="../images/enable.png" title="Скрыть сайт" alt="' . $this->id[$i] . '">';
            }
            echo '</a><a class="a_1" onclick="DeleteSite(this);">
                <img src="../images/delete.png" title="Удалить сайт" alt="' . $this->id[$i] . '"></a></td></tr>';
        }
    }

    function GenerateNavigation($page, $where, $link, $limit) {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue1 = $mysql->prepare('SELECT count(DISTINCT k_s_id) AS max FROM k_sites AS ks
                LEFT JOIN k_sites_links AS ksl ON (ksl.k_sl_site_id = ks.k_s_id)
                ' . $where);
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

?>
