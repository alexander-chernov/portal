<?php
class AllStreets {

    public $id = array();
    public $name = array();

    public function AllStreets($page, $where) {
        try {
            $page = filter_var($page, FILTER_VALIDATE_INT);
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = "SELECT k_s_id,k_s_name FROM k_streets WHERE k_s_name NOT LIKE '%###%' " . $where . " ORDER BY k_s_name ASC ";
            if ($page != 0) {
                $query .= " LIMIT " . (($page - 1) * 50) . ",50";
            }
            $queue = $mysql->prepare($query);
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $value) {
                array_push($this->id, $value['k_s_id']);
                array_push($this->name, $value['k_s_name']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }
}

class CatalogCategories {

    public $id = array();
    public $name = array();

    function CatalogCategories() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare('SELECT DISTINCT category FROM base_org ORDER BY category  ASC');
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            //var_dump($result);
            foreach ($result as $value) {
                array_push($this->id, $value['category']);
                array_push($this->name, $value['category']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class CatalogSubCategories extends CatalogCategories {

    public $id_sub = array();
    public $name_sub = array();

    function CatalogSubCategories($where='') {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare('SELECT DISTINCT subcategory,category
                FROM  base_org
                ' . $where . '
                ORDER BY category ASC, subcategory ASC');
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            //var_dump($result);
            foreach ($result as $value) {
                array_push($this->id_sub, $value['subcategory']);
                array_push($this->name_sub, $value['subcategory']);
                array_push($this->id, $value['category']);
                array_push($this->name, $value['category']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

    function EditWindow($id) {
        //$id = filter_var($id, FILTER_VALIDATE_INT);
        $ind = array_search($id, $this->id_sub);
        echo '<tr><td><p class="style_2">Изменить каталог рубрики:</p></td>
            <td>
            <select id="EditCategory">';
        $s = new CatalogCategories();
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


 class SubSubcategories extends CatalogSubCategories {

    public $id_ss = array();
    public $name_ss = array();

    function SubSubcategories($page, $where, $limit='') {
        $page = filter_var($page, FILTER_VALIDATE_INT);
        if ($page != 0) {
            try {
                $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
                $mysql->exec('set names utf8');
                if ($limit == 'unlimit') {
                    $sql = 'SELECT DISTINCT subcategory,category
                                                        FROM  base_org
                                                        ' . $where . '
                                                        ORDER BY subcategory ASC';
                    //echo '<br>'.$sql.'<br>';
                    $queue = $mysql->prepare($sql);

                } else {
                    if (!$limit) {
                        $limit = 50;
                    }
                    $queue = $mysql->prepare('SELECT DISTINCT subcategory,category
                                    FROM  base_org
                                    ' . $where . '
                                    ORDER BY subcategory ASC
                LIMIT ' . (($page - 1) * $limit) . ',' . $limit);
                }
                $queue->execute();
                $result = $queue->fetchAll(PDO::FETCH_ASSOC);
                foreach ($result as $value) {
                    array_push($this->id_sub, $value['subcategory']);
                    array_push($this->name_sub, $value['subcategory']);
                    array_push($this->id_ss, $value['subcategory']);
                    array_push($this->name_ss, $value['subcategory']);
                    array_push($this->id, $value['category']);
                    array_push($this->name, $value['category']);
                }
            } catch (PDOException $e) {
                exit();
            }
        }
    }

    function OneBigSS($big_sub) {
        //$big_sub = filter_var($big_sub, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare("SELECT DISTINCT subcategory,category
                                                FROM  base_org
                                                category =".$mysql->quote($big_sub)."
                                                ORDER BY subcategory ASC
                ");
            //$queue->execute(array(":id" => $big_sub));
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $value) {
                array_push($this->id_sub, $value['subcategory']);
                array_push($this->name_sub, $value['subcategory']);
                array_push($this->id_ss, $value['subcategory']);
                array_push($this->name_ss, $value['subcategory']);
                array_push($this->id, $value['category']);
                array_push($this->name, $value['category']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

    function GenerateNavigation($page, $where, $link) {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue1 = $mysql->prepare('SELECT count(DISTINCT subcategory) AS max
                FROM  base_org
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

        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            //echo "SELECT count(DISTINCT name) AS max FROM base_org WHERE subcategory=".$mysql->quote($id)." ";
            $queue = $mysql->prepare("SELECT count(DISTINCT name) AS max FROM base_org WHERE subcategory=".$mysql->quote($id)." ");
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
    public $subcategory = array();
    public $category_name = array();
    public $firm_to_parent = array();
    public $state = array();
    public $phone1 = array();
    public $phone2 = array();
    public $phone3 = array();
    public $phone4 = array();

    public function Organizations($page, $where, $limit, $admin) {
        /* if (!$limit) {
          $limit = 50;
          } */
        if (!$admin) {
            $admin = 1;
        }
        if ($page != 0) {
            $page = filter_var($page, FILTER_VALIDATE_INT);
            try {
                $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
                $mysql->exec('set names utf8');
                //Создаём временные таблицы
                /*
                $temp_0 = $mysql->exec('DROP TABLE IF EXISTS `k_catalog_firms_m`,`k_catalog_firms_addresses_m`,
                    `k_catalog_firms_days_m`,`k_catalog_firms_hours_m`,`k_catalog_firms_parents_m`,
                    `k_catalog_firms_phones_m`,`k_catalog_firms_week_m`,`k_streets_house_nums_m`,
                    `k_streets_m`,`map_buildings_m`,`k_catalog_big_subcategories_m`,`k_catalog_categories_m`,`k_catalog_subcategories_m`;');
                $temp_1_1 = $mysql->exec("CREATE TABLE `k_catalog_firms_m` (`k_cf_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `k_cf_name` varchar(200) NOT NULL,
                    `k_cf_watches` bigint(20) unsigned NOT  NULL ,
                    `k_cf_email` varchar(200) NOT NULL ,
                    `k_cf_site` varchar(200) NOT NULL ,
                    `c_cf_user` varchar(100) NOT NULL ,
                    PRIMARY  KEY (`k_cf_id`)) ENGINE=MEMORY DEFAULT CHARSET=utf8;
                    SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");
                $temp_1_2 = $mysql->exec("INSERT INTO `k_kedr`.`k_catalog_firms_m`
                    SELECT k_cf_id,k_cf_name,k_cf_watches,k_cf_email,k_cf_site,c_cf_user FROM `k_kedr`.`k_catalog_firms`;");
                $temp_2_1 = $mysql->exec("CREATE TABLE `k_kedr`.`k_catalog_firms_addresses_m` (  `k_cfa_id` bigint( 20  )  unsigned NOT  NULL  AUTO_INCREMENT ,
                    `k_cfa_address` bigint( 20  )  unsigned NOT  NULL ,
                    `k_cfa_parent` bigint( 20  )  unsigned NOT  NULL ,
                    `k_cfa_adv` varchar( 200  )  NOT  NULL DEFAULT  '',
                    PRIMARY  KEY (  `k_cfa_id`  )  ) ENGINE  =  MEMORY  DEFAULT CHARSET  = utf8;
                    SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");
                $temp_2_2 = $mysql->exec("INSERT INTO `k_kedr`.`k_catalog_firms_addresses_m` SELECT * FROM `k_kedr`.`k_catalog_firms_addresses`;");
                $temp_3_1 = $mysql->exec("CREATE  TABLE  `k_kedr`.`k_catalog_firms_days_m` (  `k_cfh_id` bigint( 20  )  unsigned NOT  NULL  AUTO_INCREMENT ,
                    `k_cfh_day` tinyint( 2  )  unsigned NOT  NULL ,
                    `k_cfh_day_t` tinyint( 1  )  unsigned NOT  NULL DEFAULT  '1',
                    `k_cfh_parent` bigint( 20  )  unsigned NOT  NULL ,
                    PRIMARY  KEY (  `k_cfh_id`  )  ) ENGINE  =  MEMORY  DEFAULT CHARSET  = utf8;
                    SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");
                $temp_3_2 = $mysql->exec("INSERT INTO `k_kedr`.`k_catalog_firms_days_m` SELECT * FROM `k_kedr`.`k_catalog_firms_days`;");
                $temp_4_1 = $mysql->exec("CREATE  TABLE  `k_kedr`.`k_catalog_firms_hours_m` (  `k_cfd_id` bigint( 20  )  unsigned NOT  NULL  AUTO_INCREMENT ,
                    `k_cfd_hour_s` time NOT  NULL DEFAULT  '00:00:00',
                    `k_cfd_hour_e` time NOT  NULL DEFAULT  '00:00:00',
                    `k_cfd_hour_break_s` time NOT  NULL DEFAULT  '00:00:00',
                    `k_cfd_hour_break_e` time NOT  NULL DEFAULT  '00:00:00',
                    `k_cfd_parent` bigint( 20  )  unsigned NOT  NULL ,
                    PRIMARY  KEY (  `k_cfd_id`  )  ) ENGINE  =  MEMORY  DEFAULT CHARSET  = utf8;
                    SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");
                $temp_4_2 = $mysql->exec("INSERT INTO `k_kedr`.`k_catalog_firms_hours_m` SELECT * FROM `k_kedr`.`k_catalog_firms_hours`;");
                $temp_5_1 = $mysql->exec("CREATE  TABLE  `k_kedr`.`k_catalog_firms_parents_m` (  `k_cfp_id` bigint( 20  )  unsigned NOT  NULL  AUTO_INCREMENT ,
                    `k_cfp_firm_id` bigint( 20  )  unsigned NOT  NULL ,
                    `k_cfp_parent_id` bigint( 20  )  unsigned NOT  NULL ,
                    PRIMARY  KEY (  `k_cfp_id`  )  ) ENGINE  =  MEMORY  DEFAULT CHARSET  = utf8;
                    SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");
                $temp_5_2 = $mysql->exec("INSERT INTO `k_kedr`.`k_catalog_firms_parents_m` SELECT * FROM `k_kedr`.`k_catalog_firms_parents`;");
                $temp_6_1 = $mysql->exec("CREATE  TABLE  `k_kedr`.`k_catalog_firms_phones_m` (  `k_cfp_id` bigint( 20  )  unsigned NOT  NULL  AUTO_INCREMENT ,
                    `k_cfp_phone` varchar( 50  )  NOT  NULL ,
                    `k_cfp_type` tinyint( 2  )  unsigned NOT  NULL DEFAULT  '1' ,
                    `k_cfp_parent` bigint( 20  )  unsigned NOT  NULL ,
                    PRIMARY  KEY (  `k_cfp_id`  )  ) ENGINE  =  MEMORY  DEFAULT CHARSET  = utf8;
                    SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");
                $temp_6_2 = $mysql->exec("INSERT INTO `k_kedr`.`k_catalog_firms_phones_m` SELECT * FROM `k_kedr`.`k_catalog_firms_phones`;");
                $temp_7_1 = $mysql->exec("CREATE  TABLE  `k_kedr`.`k_catalog_firms_week_m` (  `k_cfw_id` tinyint( 2  )  unsigned NOT  NULL  AUTO_INCREMENT ,
                    `k_cfw_name` varchar( 3  )  NOT  NULL ,
                    PRIMARY  KEY (  `k_cfw_id`  )  ) ENGINE  =  MEMORY  DEFAULT CHARSET  = utf8;
                    SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");
                $temp_7_2 = $mysql->exec("INSERT INTO `k_kedr`.`k_catalog_firms_week_m` SELECT * FROM `k_kedr`.`k_catalog_firms_week`;");
                $temp_8_1 = $mysql->exec("CREATE TABLE `k_streets_house_nums_m` (`k_shn_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `k_shn_house_num` varchar(100) NOT NULL ,
                    `k_shn_street_id` bigint(20) unsigned NOT  NULL DEFAULT  '0' ,
                    `k_shn_district_id` bigint(20)  unsigned NOT  NULL ,
                    `k_shn_massive_id` bigint( 20  )  NOT  NULL DEFAULT  '0' ,
                    `k_shn_object_id` bigint( 20  )  unsigned NOT  NULL DEFAULT  '0' ,
                    PRIMARY  KEY (  `k_shn_id`  )  ) ENGINE  =  MEMORY  DEFAULT CHARSET  = utf8;
                    SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");
                $temp_8_2 = $mysql->exec("INSERT INTO `k_kedr`.`k_streets_house_nums_m` SELECT * FROM `k_kedr`.`k_streets_house_nums`;");
                $temp_9_1 = $mysql->exec("CREATE  TABLE  `k_kedr`.`k_streets_m` (  `k_s_id` bigint( 20  )  unsigned NOT  NULL  AUTO_INCREMENT ,
                    `k_s_name` varchar( 200  )  NOT  NULL ,
                    `k_s_object_id` bigint( 20  )  NOT  NULL DEFAULT  '0' ,
                    `k_s_town` bigint( 20  )  NOT  NULL DEFAULT  '1' ,
                    PRIMARY  KEY (  `k_s_id`  )  ) ENGINE  =  MEMORY  DEFAULT CHARSET  = utf8;
                    SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");
                $temp_9_2 = $mysql->exec("INSERT INTO `k_kedr`.`k_streets_m` SELECT * FROM `k_kedr`.`k_streets`;");
                $temp_10_1 = $mysql->exec("CREATE  TABLE  `k_kedr`.`map_buildings_m` (  `fid` int( 11  )  NOT  NULL  AUTO_INCREMENT ,
                    `k_shn_id` int( 11  )  DEFAULT NULL ,
                    PRIMARY  KEY (  `fid`  )  ) ENGINE  = MEMORY  DEFAULT CHARSET  = utf8;
                    SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");
                $temp_10_2 = $mysql->exec("INSERT INTO `k_kedr`.`map_buildings_m` SELECT fid,k_shn_id FROM `k_kedr`.`map_buildings`;");
                $temp_11_1 = $mysql->exec("CREATE  TABLE  `k_kedr`.`k_catalog_big_subcategories_m` (  `k_cbs_id` bigint( 20  )  unsigned NOT  NULL  AUTO_INCREMENT ,
                    `k_cbs_name` varchar( 100  )  NOT  NULL ,
                    `k_cbs_parent` bigint( 20  )  unsigned NOT  NULL ,
                    PRIMARY  KEY (  `k_cbs_id`  )  ) ENGINE  =  MEMORY  DEFAULT CHARSET  = utf8;
                    SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");
                $temp_11_2 = $mysql->exec("INSERT INTO `k_kedr`.`k_catalog_big_subcategories_m` SELECT * FROM `k_kedr`.`k_catalog_big_subcategories`;");
                $temp_12_1 = $mysql->exec("CREATE  TABLE  `k_kedr`.`k_catalog_categories_m` (  `k_cc_id` bigint( 20  )  unsigned NOT  NULL  AUTO_INCREMENT ,
                    `k_cc_name` varchar( 200  )  NOT  NULL  COMMENT  'Категория в каталоге',
                    PRIMARY  KEY (  `k_cc_id`  )  ) ENGINE  =  MEMORY  DEFAULT CHARSET  = utf8;
                    SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");
                $temp_12_2 = $mysql->exec("INSERT INTO `k_kedr`.`k_catalog_categories_m` SELECT * FROM `k_kedr`.`k_catalog_categories`;");
                $temp_13_1 = $mysql->exec("CREATE  TABLE  `k_kedr`.`k_catalog_subcategories_m` (  `k_cs_id` bigint( 20  )  unsigned NOT  NULL  AUTO_INCREMENT ,
                    `k_cs_name` varchar( 200  )  NOT  NULL  COMMENT  'Подкатегория в каталоге',
                    `k_cs_parent` bigint( 20  )  NOT  NULL  COMMENT  'ID родителя в каталоге',
                    PRIMARY  KEY (  `k_cs_id`  )  ) ENGINE  =  MEMORY  DEFAULT CHARSET  = utf8;
                    SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");
                $temp_13_2 = $mysql->exec("INSERT INTO `k_kedr`.`k_catalog_subcategories_m` SELECT * FROM `k_kedr`.`k_catalog_subcategories`;");
                //Закончили с временными таблицами
                */
                $limit_q = '';
                if ($limit != 0) {
                    $limit_q = 'LIMIT ' . (($page - 1) * $limit) . ',' . $limit;
                }
                if ($where) {
                    $queue = $mysql->prepare('SELECT  *
                        FROM base_org
                        ' . $where . '
                        ORDER BY id DESC ' . $limit_q);
                } else {
                    $queue = $mysql->prepare('SELECT * FROM base_org
                        ORDER BY id DESC ' . $limit_q);
                }
                //var_dump($queue->queryString);
                $queue->execute();
                $result = $queue->fetchAll(PDO::FETCH_ASSOC);
                //var_dump($result);
                if ($admin == 1) {
                    $queue2 = $mysql->prepare('SELECT *
                        FROM base_org
                        WHERE id=:id
                        ORDER BY name ASC');
                }
                foreach ($result as $value) {
                    array_push($this->id, $value['id']);
                    array_push($this->name, $value['name']);
                    array_push($this->category, $value['category']);
                    array_push($this->subcategory, $value['subcategory']);
                    array_push($this->watches, $value['address']);
                    array_push($this->email, $value['email']);
                    array_push($this->site, $value['site']);
                    array_push($this->descr, $value['description']);
                    array_push($this->phone1, $value['phone1']);
                    array_push($this->phone2, $value['phone2']);
                    array_push($this->phone3, $value['phone3']);
                    array_push($this->phone4, $value['phone4']);
                    //array_push($this->state, $value['k_cf_state']);
                    if ($admin == 1) {
                        //$queue2->execute(array(":id" => $value['id']));
                        //$result2 = $queue2->fetchAll(PDO::FETCH_ASSOC);
                        $sub_bet = array();
                        $sub_n_bet = array();
                        $bsub_bet = array();
                        $bsub_n_bet = array();
                        $cat_bet = array();
                        $cat_n_bet = array();
                        $ftp_bet = array();
                        /*
                        foreach ($result as $value2) {
                            $sub_bet[] = $value2['category'];
                            $sub_n_bet[] = $value2['category'];
                            $bsub_bet[] = $value2['category'];
                            $bsub_n_bet[] = $value2['category'];
                            $cat_bet[] = $value2['subcategory'];
                            $cat_n_bet[] = $value2['subcategory'];
                            $ftp_bet[] = $value2['phone2'];
                        }
                        */
                        $sub_bet[] = $value['category'];
                        $sub_n_bet[] = $value['category'];
                        $bsub_bet[] = $value['category'];
                        $bsub_n_bet[] = $value['category'];
                        $cat_bet[] = $value['subcategory'];
                        $cat_n_bet[] = $value['subcategory'];
                        $ftp_bet[] = $value['phone2'];
/*
                        $this->sub[$value['category']] = $sub_bet;
                        $this->sub_name[$value['category']] = $sub_n_bet;
                        $this->big_sub[$value['category']] = $bsub_bet;
                        $this->big_sub_name[$value['category']] = $bsub_n_bet;
                        //$this->category[$value['subcategory']] = $cat_bet;
                        $this->category_name[$value['subcategory']] = $cat_n_bet;
                        $this->firm_to_parent[$value['phone2']] = $ftp_bet;
*/
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
            $queue = $mysql->prepare('SELECT *
                FROM base_org
                WHERE id=:id');
            $queue->execute(array(":id" => $id));
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            $queue2 = $mysql->prepare('SELECT *
                FROM base_org
                WHERE id=:id');
            foreach ($result as $value) {
                array_push($this->id, $value['id']);
                array_push($this->name, $value['name']);
                array_push($this->watches, $value['address']);
                array_push($this->email, $value['email']);
                array_push($this->descr, $value['description']);
                array_push($this->site, $value['site']);
                array_push($this->phone1, $value['phone1']);
                array_push($this->phone2, $value['phone2']);
                array_push($this->phone3, $value['phone3']);
                array_push($this->phone4, $value['phone4']);
                //$queue2->execute(array(":id" => $value['id']));
                //$result2 = $queue2->fetchAll(PDO::FETCH_ASSOC);
                $sub_bet = array();
                $sub_n_bet = array();
                $bsub_bet = array();
                $bsub_n_bet = array();
                $cat_bet = array();
                $cat_n_bet = array();
                $ftp_bet = array();
                foreach ($result as $value2) {
                    $sub_bet[] = $value2['category'];
                    $sub_n_bet[] = $value2['category'];
                    $bsub_bet[] = $value2['category'];
                    $bsub_n_bet[] = $value2['category'];
                    $cat_bet[] = $value2['subcategory'];
                    $cat_n_bet[] = $value2['subcategory'];
                    $ftp_bet[] = $value2['phone2'];
                }
                $this->sub[$value['category']] = $sub_bet;
                $this->sub_name[$value['category']] = $sub_n_bet;
                $this->big_sub[$value['category']] = $bsub_bet;
                $this->big_sub_name[$value['category']] = $bsub_n_bet;
                $this->category[$value['subcategory']] = $cat_bet;
                $this->category_name[$value['subcategory']] = $cat_n_bet;
                $this->firm_to_parent[$value['phone2']] = $ftp_bet;
            }
        } catch (PDOException $e) {
            exit();
        }
    }

    function GenerateNavigation($page, $where, $link, $limit=50) {
        if (!$limit) {
            $limit = 50;
        }
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            if (empty($where)) {
                $where = ' WHERE id!=0 ';
            }
            $queue1 = $mysql->prepare('SELECT count(id) AS max
                  FROM base_org
                  ' . $where);
            $queue1->execute();
            $row = $queue1->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
/*
        var_dump('SELECT count(id) AS max
                  FROM base_org
                  ' . $where);
*/
        $pages = intval($row['max'] / $limit);
        if ($row['max'] % $limit != 0) {
            $pages++;
        }
        //var_dump($pages);
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
            //$id = filter_var($id, FILTER_VALIDATE_INT);
            try {
                $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
                $mysql->exec('set names utf8');
                $queue = $mysql->prepare('SELECT  *
                    FROM base_org AS b
                    LEFT JOIN k_streets_house_nums AS h ON (trum(h.k_shn_house_num) = trim(b.house_num))
                    LEFT JOIN k_streets AS s ON (s.k_s_id = b.street_id)
                    LEFT JOIN map_buildings AS m ON (mb.k_shn_id = h.k_shn_id)
                    WHERE b.id='.$mysql->quote($id).'
                    ORDER BY b.id ASC');
                $queue->execute();
                $result = $queue->fetchAll(PDO::FETCH_ASSOC);
                /*
                $queue_0 = $mysql->prepare('SELECT * FROM k_catalog_firms_days_m AS kcfd
                    LEFT JOIN k_catalog_firms_week_m AS kcfw ON (kcfw.k_cfw_id = kcfd.k_cfh_day)
                    WHERE k_cfh_parent=:id
                    ORDER BY k_cfw_id ASC');
                $queue_1 = $mysql->prepare('SELECT * FROM k_catalog_firms_hours_m AS kcfd
                    WHERE k_cfd_parent=:id
                    ORDER BY k_cfd_id ASC');
                */
                foreach ($result as $value) {
                    array_push($this->id, $value['id']);
                    if ($value['k_shn_id'] != 0) {
                        //array_push($this->fid, $value['fid']);
                    }
                    array_push($this->address_advanced, $value['address']);
                    array_push($this->address, $value['k_shn_id']);
                    if (preg_match('/(###)/', $value['k_s_name'])) {
                        $street = explode('###', $value['k_s_name']);
                        $house = explode('###', $value['k_shn_house_num']);
                        array_push($this->address_str, $street[0] . ' ' . $house[0] . ' / ' . $street[1] . ' ' . $house[1]);
                    } else {
                        array_push($this->address_str, $value['k_s_name'] . ' ' . $value['k_shn_house_num']);
                    }
                    /*
                    $queue_0->execute(array(":id" => $value['id']));
                    $result_0 = $queue_0->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($result_0 as $value_0) {
                        array_push($this->days, array($value['k_cfa_id'], $value_0['k_cfw_name'], $value_0['k_cfh_day_t']));
                        $queue_1->execute(array(":id" => $value_0['k_cfh_id']));
                        $result_1 = $queue_1->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($result_1 as $value_1) {
                            array_push($this->hours_s, array($value_0['k_cfh_id'], substr($value_1['k_cfd_hour_s'], 0, -3)));
                            array_push($this->hours_e, array($value_0['k_cfh_id'], substr($value_1['k_cfd_hour_e'], 0, -3)));
                            array_push($this->hours_b_s, array($value_0['k_cfh_id'], substr($value_1['k_cfd_hour_break_s'], 0, -3)));
                            array_push($this->hours_b_e, array($value_0['k_cfh_id'], substr($value_1['k_cfd_hour_break_e'], 0, -3)));
                        }
                    }
                    */
                }
                /*
                $queue3 = $mysql->prepare('SELECT DISTINCT *
                    FROM k_catalog_firms_addresses_m AS kcfa
                    LEFT JOIN k_catalog_firms_phones_m AS kcfp ON (kcfp.k_cfp_parent = kcfa.k_cfa_id)
                    WHERE k_cfa_parent=:id');
                $queue3->execute(array(":id" => $id));
                $result3 = $queue3->fetchAll(PDO::FETCH_ASSOC);
                foreach ($result3 as $value) {
                */
                array_push($this->phones, array($value['phone1']));
                array_push($this->phones, array($value['phone2']));
                array_push($this->phones, array($value['phone3']));
                array_push($this->phones, array($value['phone4']));
                    //array_push($this->phones_numb, array($value['k_cfa_id'], $value['k_cfp_phone']));
                    switch ($value['k_cfp_type']) {
                        case 1: $type_p = 'Телефон';
                            break;
                        case 2: $type_p = 'Факс';
                            break;
                        case 3: $type_p = 'Единая служба';
                            break;
                    }
                    array_push($this->phones_types, array($value['k_cfa_id'], $value['k_cfp_type'], $type_p));
                //}
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
            $queue = $mysql->prepare('SELECT *
                FROM base_org as b
                LEFT JOIN k_streets_house_nums AS h ON (trum(h.k_shn_house_num) = trim(b.house_num))
                LEFT JOIN k_streets AS s ON (s.k_s_id = b.street_id)
                LEFT JOIN map_buildings AS m ON (mb.k_shn_id = h.k_shn_id)
                WHERE b.id='.$mysql->quote($id));
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $value) {
                array_push($this->id, $value['id']);
                //array_push($this->fid, $value['fid']);
                array_push($this->address, $value['k_shn_id']);
                if (preg_match('/(###)/', $value['k_s_name'])) {
                    $street = explode('###', $value['k_s_name']);
                    $house = explode('###', $value['k_shn_house_num']);
                    array_push($this->address_str, $street[0] . ' ' . $house[0] . ' / ' . $street[1] . ' ' . $house[1]);
                } else {
                    array_push($this->address_str, $value['k_s_name'] . ' ' . $value['k_shn_house_num']);
                }
                array_push($this->address_advanced, $value['address']);
            }
            /*
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
            */
            array_push($this->phones, array($value['phone1']));
            array_push($this->phones, array($value['phone2']));
            array_push($this->phones, array($value['phone3']));
            array_push($this->phones, array($value['phone4']));
                //array_push($this->phones_numb, array($value['k_cfa_id'], $value['k_cfp_phone']));
                //array_push($this->phones_types, array($value['k_cfa_id'], $value['k_cfp_type']));
            //}
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

?>
