<?php

function CorrectURL($url) {
    return preg_match('/^http:\/\/|^https:\/\//', filter_var($url, FILTER_SANITIZE_STRIPPED)) ? $url : 'http://' . $url;
}

function CreateTempTables() {
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
                    `k_cf_state` tinyint(2) unsigned NOT NULL ,
                    PRIMARY  KEY (`k_cf_id`)) ENGINE=MEMORY DEFAULT CHARSET=utf8;
                    SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");
        $temp_1_2 = $mysql->exec("INSERT INTO `k_kedr`.`k_catalog_firms_m`
                    SELECT k_cf_id,k_cf_name,k_cf_watches,k_cf_email,k_cf_site,c_cf_user,k_cf_state FROM `k_kedr`.`k_catalog_firms`;");
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
        */
        //Закончили с временными таблицами
    } catch (PDOException $e) {
        exit();
    }
}

?>
