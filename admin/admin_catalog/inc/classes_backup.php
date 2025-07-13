<?php

class Categories {

    public $id = array();
    public $name = array();

    function Categories() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare('SELECT * FROM k_catalog_categories ORDER BY k_cc_name ASC');
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $value) {
                array_push($this->id, $value['k_cc_id']);
                array_push($this->name, $value['k_cc_name']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class SubCategories extends Categories {

    public $id_sub = array();
    public $name_sub = array();

    function SubCategories($where) {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare('SELECT *
                FROM  k_catalog_big_subcategories AS kcbs
                LEFT JOIN k_catalog_categories AS kcc ON (kcc.k_cc_id = kcbs.k_cbs_parent)
                ' . $where . '
                ORDER BY k_cbs_name ASC');
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $value) {
                array_push($this->id_sub, $value['k_cbs_id']);
                array_push($this->name_sub, $value['k_cbs_name']);
                array_push($this->id, $value['k_cc_id']);
                array_push($this->name, $value['k_cc_name']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

    function EditWindow($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        $ind = array_search($id, $this->id_sub);
        echo '<tr><td><p class="style_2">Изменить каталог рубрики:</p></td>
            <td>
            <select id="EditCategory">';
        $s = new Categories();
        for ($i = 0; $i < count($s->id); $i++) {
            if ($s->id[$i] == $this->id[$ind]) {
                echo '<option selected value="' . $s->id[$i] . '">' . $s->name[$i] . '</option>';
            } else {
                echo '<option value="' . $s->id[$i] . '">' . $s->name[$i] . '</option>';
            }
        }
        echo '</select>
            </td>
            </tr>
            <tr>
            <td><p class="style_2">Изменить название рубрики:</p></td>
            <td><input style="width: 280px;" type="text" id="EditSubcategory" value="' . $this->name_sub[$ind] . '"></td>
            </tr>
            <tr>
            <td colspan="2">
            <input type="hidden" id="SaveId" value="' . $id . '">
            <button onclick="SaveSubcategory();">Изменить</button>
            </td>
            </tr>';
    }

}

class SubSubcategories extends SubCategories {

    public $id_ss = array();
    public $name_ss = array();

    function SubSubcategories($page, $where, $limit) {
        $page = filter_var($page, FILTER_VALIDATE_INT);
        if ($page != 0) {
            try {
                $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
                $mysql->exec('set names utf8');
                if ($limit == 'unlimit') {
                    $queue = $mysql->prepare('SELECT *
                FROM k_catalog_subcategories AS kcs
                LEFT JOIN k_catalog_big_subcategories AS kcbs ON (kcs.k_cs_parent = kcbs.k_cbs_id)
                LEFT JOIN k_catalog_categories AS kcc ON (kcc.k_cc_id = kcbs.k_cbs_parent)
                ' . $where . '
                ORDER BY k_cs_name ASC');
                } else {
                    $queue = $mysql->prepare('SELECT *
                FROM k_catalog_subcategories AS kcs
                LEFT JOIN k_catalog_big_subcategories AS kcbs ON (kcs.k_cs_parent = kcbs.k_cbs_id)
                LEFT JOIN k_catalog_categories AS kcc ON (kcc.k_cc_id = kcbs.k_cbs_parent)
                ' . $where . '
                ORDER BY k_cs_name ASC
                LIMIT ' . (($page - 1) * 50) . ',50');
                }
                $queue->execute();
                $result = $queue->fetchAll(PDO::FETCH_ASSOC);
                foreach ($result as $value) {
                    array_push($this->id_sub, $value['k_cbs_id']);
                    array_push($this->name_sub, $value['k_cbs_name']);
                    array_push($this->id_ss, $value['k_cs_id']);
                    array_push($this->name_ss, $value['k_cs_name']);
                    array_push($this->id, $value['k_cc_id']);
                    array_push($this->name, $value['k_cc_name']);
                }
            } catch (PDOException $e) {
                exit();
            }
        }
    }

    function OneBigSS($big_sub) {
        $big_sub = filter_var($big_sub, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare('SELECT *
                FROM k_catalog_subcategories AS kcs
                LEFT JOIN k_catalog_big_subcategories AS kcbs ON (kcs.k_cs_parent = kcbs.k_cbs_id)
                LEFT JOIN k_catalog_categories AS kcc ON (kcc.k_cc_id = kcbs.k_cbs_parent)
                WHERE k_cbs_id=:id
                ORDER BY k_cs_name ASC');
            $queue->execute(array(":id" => $big_sub));
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $value) {
                array_push($this->id_sub, $value['k_cbs_id']);
                array_push($this->name_sub, $value['k_cbs_name']);
                array_push($this->id_ss, $value['k_cs_id']);
                array_push($this->name_ss, $value['k_cs_name']);
                array_push($this->id, $value['k_cc_id']);
                array_push($this->name, $value['k_cc_name']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

    function GenerateNavigation($page, $where, $link) {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue1 = $mysql->prepare('SELECT count(*) AS max
                FROM k_catalog_subcategories AS kcs
                LEFT JOIN k_catalog_big_subcategories AS kcbs ON (kcs.k_cs_parent = kcbs.k_cbs_id)
                LEFT JOIN k_catalog_categories AS kcc ON (kcc.k_cc_id = kcbs.k_cbs_parent)
                ' . $where);
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
                echo '<a class="style_listing" href="./?PageType=5&PageIndex=' . ($page - 1) . $link . '">Предыдущая</a>';
            }

            if ($pages <= 11) {
                for ($i = 1; $i <= $pages; $i++) {
                    if ($i == $page) {
                        echo '<a class="active_listing" href="./?PageType=5&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    } else {
                        echo '<a class="style_listing" href="./?PageType=5&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
            } else {
                if ($page <= 6) {
                    for ($i = 1; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageType=5&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageType=5&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageType=5&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
                if ($page > 6 && $page <= ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageType=5&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $page - 2; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageType=5&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageType=5&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageType=5&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
                if ($page > ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageType=5&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 5; $i <= $pages; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageType=5&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageType=5&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                }
            }

            if ($page == 1 || $page < $pages) {
                echo '<a class="style_listing" href="./?PageType=5&PageIndex=' . ($page + 1) . $link . '">Следующая</a>';
            }

            echo '</div>';
        }
    }

    function CountOrgInSubSub($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare("SELECT count(DISTINCT name) AS max FROM base_org WHERE subcategory=".$mysql->quote($id)." ");
            //$queue = $mysql->prepare('SELECT count(*) AS max FROM k_catalog_firms_parents WHERE k_cfp_parent_id=:id');
            $queue->execute(/*array(":id" => $id)*/);
            $result = $queue->fetch(PDO::FETCH_ASSOC);
            return $result['max'];
        } catch (PDOException $e) {
            exit();
        }
        return 0;
    }

}

class Organizations {

    public $id = array();
    public $name = array();
    public $watches = array();
    public $email = array();
    public $site = array();
    public $descr = array();
    public $sub = array();
    public $sub_name = array();
    public $big_sub = array();
    public $big_sub_name = array();
    public $category = array();
    public $category_name = array();
    public $firm_to_parent = array();

    public function Organizations($page, $where, $limit, $admin) {
        if (!$limit) {
            $limit = 50;
        }
        if (!$admin) {
            $admin = 1;
        }
        if ($page != 0) {
            $page = filter_var($page, FILTER_VALIDATE_INT);
            try {
                $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
                $mysql->exec('set names utf8');
                if ($where) {
                    $queue = $mysql->prepare('SELECT kcf.*, kcs.*, kcbs.*, kcc.*
                  FROM k_catalog_firms AS kcf
                  LEFT JOIN k_catalog_firms_parents AS kcfp ON (kcfp.k_cfp_firm_id = kcf.k_cf_id)
                  LEFT JOIN k_catalog_subcategories AS kcs ON (kcs.k_cs_id = kcfp.k_cfp_parent_id)
                  LEFT JOIN k_catalog_big_subcategories AS kcbs ON (kcs.k_cs_parent = kcbs.k_cbs_id)
                  LEFT JOIN k_catalog_categories AS kcc ON (kcbs.k_cbs_parent = kcc.k_cc_id)
                  ' . $where . '
                  GROUP BY k_cf_id
                  ORDER BY k_cf_id DESC
                  LIMIT ' . (($page - 1) * $limit) . ',' . $limit);
                } else {
                    $queue = $mysql->prepare('SELECT * FROM k_catalog_firms
                  GROUP BY k_cf_id
                  ORDER BY k_cf_id DESC
                  LIMIT ' . (($page - 1) * $limit) . ',' . $limit);
                }
                $queue->execute();
                $result = $queue->fetchAll(PDO::FETCH_ASSOC);
                if ($admin == 1) {
                    $queue2 = $mysql->prepare('SELECT kcs.*, kcbs.*, kcc.*, kcfp.*
                  FROM k_catalog_firms AS kcf
                  LEFT JOIN k_catalog_firms_parents AS kcfp ON (kcfp.k_cfp_firm_id = kcf.k_cf_id)
                  LEFT JOIN k_catalog_subcategories AS kcs ON (kcs.k_cs_id = kcfp.k_cfp_parent_id)
                  LEFT JOIN k_catalog_big_subcategories AS kcbs ON (kcs.k_cs_parent = kcbs.k_cbs_id)
                  LEFT JOIN k_catalog_categories AS kcc ON (kcbs.k_cbs_parent = kcc.k_cc_id)
                  WHERE k_cf_id=:id
                  ORDER BY k_cs_name ASC');
                }
                foreach ($result as $value) {
                    array_push($this->id, $value['k_cf_id']);
                    array_push($this->name, $value['k_cf_name']);
                    array_push($this->watches, $value['k_cf_watches']);
                    array_push($this->email, $value['k_cf_email']);
                    array_push($this->site, $value['k_cf_site']);
                    array_push($this->descr, $value['k_cf_description']);
                    if ($admin == 1) {
                        $queue2->execute(array(":id" => $value['k_cf_id']));
                        $result2 = $queue2->fetchAll(PDO::FETCH_ASSOC);
                        $sub_bet = array();
                        $sub_n_bet = array();
                        $bsub_bet = array();
                        $bsub_n_bet = array();
                        $cat_bet = array();
                        $cat_n_bet = array();
                        $ftp_bet = array();
                        foreach ($result2 as $value2) {
                            $sub_bet[] = $value2['k_cs_id'];
                            $sub_n_bet[] = $value2['k_cs_name'];
                            $bsub_bet[] = $value2['k_cbs_id'];
                            $bsub_n_bet[] = $value2['k_cbs_name'];
                            $cat_bet[] = $value2['k_cc_id'];
                            $cat_n_bet[] = $value2['k_cc_name'];
                            $ftp_bet[] = $value2['k_cfp_id'];
                        }
                        $this->sub[$value['k_cf_id']] = $sub_bet;
                        $this->sub_name[$value['k_cf_id']] = $sub_n_bet;
                        $this->big_sub[$value['k_cf_id']] = $bsub_bet;
                        $this->big_sub_name[$value['k_cf_id']] = $bsub_n_bet;
                        $this->category[$value['k_cf_id']] = $cat_bet;
                        $this->category_name[$value['k_cf_id']] = $cat_n_bet;
                        $this->firm_to_parent[$value['k_cf_id']] = $ftp_bet;
                    }
                }
            } catch (PDOException $e) {
                exit();
            }
        }
    }

    public function LoadOne($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare('SELECT kcf.*, kcs.*, kcbs.*, kcc.*
                FROM k_catalog_firms AS kcf
                LEFT JOIN k_catalog_firms_parents AS kcfp ON (kcfp.k_cfp_firm_id = kcf.k_cf_id)
                LEFT JOIN k_catalog_subcategories AS kcs ON (kcs.k_cs_id = kcfp.k_cfp_parent_id)
                LEFT JOIN k_catalog_big_subcategories AS kcbs ON (kcs.k_cs_parent = kcbs.k_cbs_id)
                LEFT JOIN k_catalog_categories AS kcc ON (kcbs.k_cbs_parent = kcc.k_cc_id)
                WHERE k_cf_id=:id
                ORDER BY k_cf_id DESC
                LIMIT 1');
            $queue->execute(array(":id" => $id));
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            $queue2 = $mysql->prepare('SELECT kcs.*, kcbs.*, kcc.*, kcfp.*
                FROM k_catalog_firms AS kcf
                LEFT JOIN k_catalog_firms_parents AS kcfp ON (kcfp.k_cfp_firm_id = kcf.k_cf_id)
                LEFT JOIN k_catalog_subcategories AS kcs ON (kcs.k_cs_id = kcfp.k_cfp_parent_id)
                LEFT JOIN k_catalog_big_subcategories AS kcbs ON (kcs.k_cs_parent = kcbs.k_cbs_id)
                LEFT JOIN k_catalog_categories AS kcc ON (kcbs.k_cbs_parent = kcc.k_cc_id)
                WHERE k_cf_id=:id
                ORDER BY k_cfp_id DESC');
            foreach ($result as $value) {
                array_push($this->id, $value['k_cf_id']);
                array_push($this->name, $value['k_cf_name']);
                array_push($this->watches, $value['k_cf_watches']);
                array_push($this->email, $value['k_cf_email']);
                array_push($this->descr, $value['k_cf_description']);
                array_push($this->site, $value['k_cf_site']);
                $queue2->execute(array(":id" => $value['k_cf_id']));
                $result2 = $queue2->fetchAll(PDO::FETCH_ASSOC);
                $sub_bet = array();
                $sub_n_bet = array();
                $bsub_bet = array();
                $bsub_n_bet = array();
                $cat_bet = array();
                $cat_n_bet = array();
                $ftp_bet = array();
                foreach ($result2 as $value2) {
                    $sub_bet[] = $value2['k_cs_id'];
                    $sub_n_bet[] = $value2['k_cs_name'];
                    $bsub_bet[] = $value2['k_cbs_id'];
                    $bsub_n_bet[] = $value2['k_cbs_name'];
                    $cat_bet[] = $value2['k_cc_id'];
                    $cat_n_bet[] = $value2['k_cc_name'];
                    $ftp_bet[] = $value2['k_cfp_id'];
                }
                $this->sub[$value['k_cf_id']] = $sub_bet;
                $this->sub_name[$value['k_cf_id']] = $sub_n_bet;
                $this->big_sub[$value['k_cf_id']] = $bsub_bet;
                $this->big_sub_name[$value['k_cf_id']] = $bsub_n_bet;
                $this->category[$value['k_cf_id']] = $cat_bet;
                $this->category_name[$value['k_cf_id']] = $cat_n_bet;
                $this->firm_to_parent[$value['k_cf_id']] = $ftp_bet;
            }
        } catch (PDOException $e) {
            exit();
        }
    }

    function GenerateNavigation($page, $where, $link, $limit) {
        if (!$limit) {
            $limit = 50;
        }
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue1 = $mysql->prepare('SELECT kcf.*, kcs.*, kcbs.*, kcc.*
                  FROM k_catalog_firms AS kcf
                  LEFT JOIN k_catalog_firms_parents AS kcfp ON (kcfp.k_cfp_firm_id = kcf.k_cf_id)
                  LEFT JOIN k_catalog_subcategories AS kcs ON (kcs.k_cs_id = kcfp.k_cfp_parent_id)
                  LEFT JOIN k_catalog_big_subcategories AS kcbs ON (kcs.k_cs_parent = kcbs.k_cbs_id)
                  LEFT JOIN k_catalog_categories AS kcc ON (kcbs.k_cbs_parent = kcc.k_cc_id)
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
                echo '<a class="style_listing" href="index.php?PageType=1&PageIndex=' . ($page - 1) . $link . '">Предыдущая</a>';
            }

            if ($pages <= 11) {
                for ($i = 1; $i <= $pages; $i++) {
                    if ($i == $page) {
                        echo '<a class="active_listing" href="index.php?PageType=1&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    } else {
                        echo '<a class="style_listing" href="index.php?PageType=1&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
            } else {
                if ($page <= 6) {
                    for ($i = 1; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="index.php?PageType=1&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="index.php?PageType=1&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="index.php?PageType=1&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
                if ($page > 6 && $page <= ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="index.php?PageType=1&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $page - 2; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="index.php?PageType=1&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="index.php?PageType=1&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="index.php?PageType=1&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
                if ($page > ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="index.php?PageType=1&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 5; $i <= $pages; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="index.php?PageType=1&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="index.php?PageType=1&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                }
            }

            if ($page == 1 || $page < $pages) {
                echo '<a class="style_listing" href="index.php?PageType=1&PageIndex=' . ($page + 1) . $link . '">Следующая</a>';
            }

            echo '</div>';
        }
    }

}

class OrganizationAddresses {

    public $id = array();
    public $fid = array();
    public $address = array();
    public $address_str = array();
    public $hours_s = array();
    public $hours_e = array();
    public $hours_b_s = array();
    public $hours_b_e = array();
    public $days = array();
    public $phones = array();
    public $phones_types = array();
    public $phones_numb = array();
    public $address_advanced = array();

    public function OrganizationAddresses($id) {
        if ($id != 0) {
            $id = filter_var($id, FILTER_VALIDATE_INT);
            try {
                echo 'Начало '.date('H:i:s').'<br>';
                $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
                $mysql->exec('set names utf8');
                $queue = $mysql->prepare('SELECT DISTINCT kcfa.*, kshn.*, ks.*, mb.fid
                FROM k_catalog_firms_addresses AS kcfa
                LEFT JOIN k_streets_house_nums AS kshn ON (kshn.k_shn_id = kcfa.k_cfa_address)
                LEFT JOIN k_streets AS ks ON (ks.k_s_id = kshn.k_shn_street_id)
                LEFT JOIN map_buildings AS mb ON (mb.k_shn_id = kshn.k_shn_id)
                WHERE k_cfa_parent=:id
                GROUP BY k_cfa_parent');
                $queue->execute(array(":id" => $id));
                $result = $queue->fetchAll(PDO::FETCH_ASSOC);
                echo 'Выполнили запрос '.date('H:i:s').'<br>';
                foreach ($result as $value) {
                    array_push($this->id, $value['k_cfa_id']);
                    if ($value['k_shn_id'] != 0) {
                        array_push($this->fid, $value['fid']);
                    }
                    array_push($this->address_advanced, $value['k_cfa_adv']);
                    array_push($this->address, $value['k_shn_id']);
                    if (preg_match('/(###)/', $value['k_s_name'])) {
                        $street = explode('###', $value['k_s_name']);
                        $house = explode('###', $value['k_shn_house_num']);
                        array_push($this->address_str, $street[0] . ' ' . $house[0] . ' / ' . $street[1] . ' ' . $house[1]);
                    } else {
                        array_push($this->address_str, $value['k_s_name'] . ' ' . $value['k_shn_house_num']);
                    }
                }
                echo 'Выполнили вставку '.date('H:i:s').'<br>';
                $queue2 = $mysql->prepare('SELECT DISTINCT *
                FROM k_catalog_firms_addresses AS kfa
                LEFT JOIN k_catalog_firms_days AS kcfd ON (kcfd.k_cfh_parent = kfa.k_cfa_id)
                LEFT JOIN k_catalog_firms_hours AS kcfh ON (kcfh.k_cfd_parent = kcfd.k_cfh_id)
                LEFT JOIN k_catalog_firms_week AS kcfw ON (kcfw.k_cfw_id = kcfd.k_cfh_day)
                WHERE k_cfa_parent=:id
                ORDER BY k_cfa_id,k_cfh_day ASC');
                $queue2->execute(array(":id" => $id));
                $result2 = $queue2->fetchAll(PDO::FETCH_ASSOC);
                echo 'Выполнили второй запрос '.date('H:i:s').'<br>';
                foreach ($result2 as $value) {
                    array_push($this->days, array($value['k_cfa_id'], $value['k_cfw_name'], $value['k_cfh_day_t']));
                    array_push($this->hours_s, array($value['k_cfh_id'], substr($value['k_cfd_hour_s'], 0, -3)));
                    array_push($this->hours_e, array($value['k_cfh_id'], substr($value['k_cfd_hour_e'], 0, -3)));
                    array_push($this->hours_b_s, array($value['k_cfh_id'], substr($value['k_cfd_hour_break_s'], 0, -3)));
                    array_push($this->hours_b_e, array($value['k_cfh_id'], substr($value['k_cfd_hour_break_e'], 0, -3)));
                }
                echo 'Выполнили вставку второго запроса '.date('H:i:s').'<br>';
                $queue3 = $mysql->prepare('SELECT DISTINCT *
                FROM k_catalog_firms_addresses AS kcfa
                LEFT JOIN k_catalog_firms_phones AS kcfp ON (kcfp.k_cfp_parent = kcfa.k_cfa_id)
                WHERE k_cfa_parent=:id');
                $queue3->execute(array(":id" => $id));
                $result3 = $queue3->fetchAll(PDO::FETCH_ASSOC);
                echo 'Выполнили третий запрос '.date('H:i:s').'<br>';
                foreach ($result3 as $value) {
                    array_push($this->phones, array($value['k_cfa_id'], $value['k_cfp_id']));
                    array_push($this->phones_numb, array($value['k_cfa_id'], $value['k_cfp_phone']));
                    switch ($value['k_cfp_type']) {
                        case 1: $type_p = 'Телефон';
                            break;
                        case 2: $type_p = 'Факс';
                            break;
                        case 3: $type_p = 'Единая служба';
                            break;
                    }
                    array_push($this->phones_types, array($value['k_cfa_id'], $value['k_cfp_type'], $type_p));
                }
                echo 'Выполнили вставку третьего запроса '.date('H:i:s').'<br>';
            } catch (PDOException $e) {
                exit();
            }
        }
    }

    public function LoadOne($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare('SELECT DISTINCT *
                FROM k_catalog_firms_addresses AS kcfa
                LEFT JOIN k_streets_house_nums AS kshn ON (kshn.k_shn_id = kcfa.k_cfa_address)
                LEFT JOIN k_streets AS ks ON (ks.k_s_id = kshn.k_shn_street_id)
                LEFT JOIN map_buildings AS mb ON (mb.k_shn_id = kshn.k_shn_id)
                WHERE k_cfa_id=:id');
            $queue->execute(array(":id" => $id));
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $value) {
                array_push($this->id, $value['k_cfa_id']);
                array_push($this->fid, $value['fid']);
                array_push($this->address, $value['k_shn_id']);
                if (preg_match('/(###)/', $value['k_s_name'])) {
                    $street = explode('###', $value['k_s_name']);
                    $house = explode('###', $value['k_shn_house_num']);
                    array_push($this->address_str, $street[0] . ' ' . $house[0] . ' / ' . $street[1] . ' ' . $house[1]);
                } else {
                    array_push($this->address_str, $value['k_s_name'] . ' ' . $value['k_shn_house_num']);
                }
                array_push($this->address_advanced, $value['k_cfa_adv']);
            }
            $queue2 = $mysql->prepare('SELECT DISTINCT *
                FROM k_catalog_firms_addresses AS kfa
                LEFT JOIN k_catalog_firms_days AS kcfd ON (kcfd.k_cfh_parent = kfa.k_cfa_id)
                LEFT JOIN k_catalog_firms_hours AS kcfh ON (kcfh.k_cfd_parent = kcfd.k_cfh_id)
                LEFT JOIN k_catalog_firms_week AS kcfw ON (kcfw.k_cfw_id = kcfd.k_cfh_day)
                WHERE k_cfa_id=:id
                ORDER BY k_cfa_id,k_cfh_day ASC');
            $queue2->execute(array(":id" => $id));
            $result2 = $queue2->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result2 as $value) {
                array_push($this->days, array($value['k_cfa_id'], $value['k_cfw_name'], $value['k_cfh_day_t'], $value['k_cfh_id']));
                array_push($this->hours_s, array($value['k_cfh_id'], substr($value['k_cfd_hour_s'], 0, -3), $value['k_cfd_id']));
                array_push($this->hours_e, array($value['k_cfh_id'], substr($value['k_cfd_hour_e'], 0, -3), $value['k_cfd_id']));
                array_push($this->hours_b_s, array($value['k_cfh_id'], substr($value['k_cfd_hour_break_s'], 0, -3), $value['k_cfd_id']));
                array_push($this->hours_b_e, array($value['k_cfh_id'], substr($value['k_cfd_hour_break_e'], 0, -3), $value['k_cfd_id']));
            }
            $queue3 = $mysql->prepare('SELECT DISTINCT *
                FROM k_catalog_firms_addresses AS kcfa
                LEFT JOIN k_catalog_firms_phones AS kcfp ON (kcfp.k_cfp_parent = kcfa.k_cfa_id)
                WHERE k_cfa_id=:id');
            $queue3->execute(array(":id" => $id));
            $result3 = $queue3->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result3 as $value) {
                array_push($this->phones, array($value['k_cfa_id'], $value['k_cfp_id']));
                array_push($this->phones_numb, array($value['k_cfa_id'], $value['k_cfp_phone']));
                array_push($this->phones_types, array($value['k_cfa_id'], $value['k_cfp_type']));
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class DayOfWeek {

    public $id = array();
    public $name = array();

    public function DayOfWeek() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare('SELECT * FROM k_catalog_firms_week ORDER BY k_cfw_id ASC');
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $value) {
                array_push($this->id, $value['k_cfw_id']);
                array_push($this->name, $value['k_cfw_name']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class AllAddresses {

    public $id = array();
    public $address = array();

    public function AllAddresses() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare('SELECT k_shn_id,k_s_name,k_shn_house_num
                FROM k_streets_house_nums AS kshn
                LEFT JOIN k_streets AS ks ON (kshn.k_shn_street_id = ks.k_s_id)
                ORDER BY k_s_name,k_shn_house_num ASC');
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $value) {
                array_push($this->id, $value['k_shn_id']);
                if (preg_match('/(###)/', $value['k_s_name'])) {
                    $street = explode('###', $value['k_s_name']);
                    $house = explode('###', $value['k_shn_house_num']);
                    array_push($this->address, $street[0] . ' ' . $house[0] . ' / ' . $street[1] . ' ' . $house[1]);
                } else {
                    array_push($this->address, $value['k_s_name'] . ' ' . $value['k_shn_house_num']);
                }
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class BannersAll {

    public $banner_code = array();
    public $banner_id = array();
    public $banner_type = array();
    public $banner_end_date = array();
    public $banner_end_days = array();
    public $banner_organization = array();
    public $banner_contact_name = array();
    public $banner_contacts = array();

    /**
     * Загрузить баннеры главной страницы
     * @param int $id Загрузить конкретный баннер
     */
    public function BannersAll($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        $query_1 = 'SELECT * FROM k_all_banners WHERE (k_ab_type=7 OR k_ab_type=8 OR k_ab_type=9)';
        if (!empty($id)) {
            $query_1 .= ' AND k_ab_id=' . $id;
        }
        $query_1 .= ' ORDER BY k_ab_type,k_ab_id ASC';
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
            $this->banner_type[] = $row['k_ab_type'];
            $this->banner_end_date[] = $row['k_ab_end_date'];
            $this->banner_organization[] = $row['k_ab_organization'];
            $this->banner_contact_name[] = $row['k_ab_contact_name'];
            $this->banner_contacts[] = $row['k_ab_contacts'];
            $this->banner_end_days[] = round((strtotime($row['k_ab_end_date']) - time()) / 86400, 0) < 0 ? 0 : round((strtotime($row['k_ab_end_date']) - time()) / 86400, 0);
        }
    }

}

?>
