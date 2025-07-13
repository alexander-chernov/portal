<?php

/**
 * Класс родитель для админки для построения таблиц
 */
class TableBuild {

    public $login = array();
    public $password = array();
    public $fname = array();
    public $lname = array();
    public $reg_date = array();
    public $id = array();
    public $email = array();
    public $query;

    /**
     * Загружаем данные
     */
    public function LoadTable() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare($this->query);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        $n = 0;
        foreach ($result as $row) {
            $this->login[$n] = $row['k_ku_login'];
            $this->password[$n] = $row['k_ku_password'];
            $this->reg_date[$n] = $row['k_ku_autor_date'];
            $this->fname[$n] = $row['k_ku_fname'];
            $this->lname[$n] = $row['k_ku_lname'];
            $this->status[$n] = $row['k_ku_verified'];
            $this->email[$n] = $row['k_ku_email'];
            $this->id[$n] = $row['k_ku_id'];
            $n++;
        }
    }

    /**
     * Query для "Администраторов"
     */
    public function AdminLoad() {
        $this->query = "SELECT k_ku_id, k_ku_login, k_ku_password, k_ku_autor_date, k_ku_fname, k_ku_lname, k_ku_email, k_ku_verified
            FROM k_users
            WHERE k_u_privileges=1
            ORDER BY k_ku_autor_date DESC";
    }

}

/**
 * Класс для построения таблиц модераторов
 */
class TableModeratorBuild extends TableBuild {

    public $categories = array();
    public $categories_id = array();
    public $moder_cat = array();
    public $moder_cat_id = array();

    /**
     * Query для "Модераторов"
     */
    public function ModeratorLoad() {
        $this->query = "SELECT k_ku_id, k_ku_login, k_ku_password, k_ku_autor_date, k_ku_fname, k_ku_lname, k_ku_email, k_ku_verified
            FROM k_users
            WHERE k_u_privileges=2
            ORDER BY k_ku_autor_date DESC";
    }

    /**
     * Загрузить категории модераторов
     * @return array Возвращает массив формата array(категория,id)
     */
    public function CategoriesLoad() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT * FROM k_users_categories ORDER BY k_uc_name ASC');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        $n = 1;
        foreach ($result as $row) {
            $this->categories[$n] = $row['k_uc_name'];
            $this->categories_id[$n] = $row['k_uc_id'];
            $n++;
        }
        return array($this->categories, $this->categories_id);
    }

    /**
     * Генерация списка категорий с учётом "checked" категорий
     * @param int $id ID модератора
     */
    public function ListGenerate($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT k_uc_name,k_uc_id FROM k_users as us
                LEFT JOIN  k_users_categories_links as kl ON (us.k_ku_id = kl.k_ucl_user_id)
                LEFT JOIN k_users_categories as kc ON (kl.k_ucl_cat_id = kc.k_uc_id)
                WHERE k_ku_id=:id
                ORDER BY us.k_ku_login, kc.k_uc_name ASC');
            $query->execute(array(':id' => $id));
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        $a = 1;
        foreach ($result as $row) {
            $this->moder_cat[$a] = $row['k_uc_name'];
            $this->moder_cat_id[$a] = $row['k_uc_id'];
            $a++;
        }
        echo '<ul class="access_list">';
        for ($a = 1; $a <= count($this->categories); $a++) {
            if (in_array($this->categories_id[$a], $this->moder_cat_id)) {
                echo '<li><label><input type="checkbox" name="mod[]" value="' . $this->categories_id[$a] . '" checked>' . $this->categories[$a] . '</label></li>';
            } else {
                echo '<li><label><input type="checkbox" name="mod[]" value="' . $this->categories_id[$a] . '">' . $this->categories[$a] . '</label></li>';
            }
        }
        echo '</ul>';
    }

}

/**
 * Класс для построения таблиц пользователей
 */
class TableUsersBuild extends TableBuild {

    public $ads = array();
    public $banned = array();
    public $banned_ip_list = array();
    public $last_ip = array();

    /**
     * Query для "Пользователей"
     * @param int $page Номер страницы отображаемого списка
     */
    public function UsersLoad($page) {
        $this->query = 'SELECT k_ku_id, k_ku_login, k_ku_password, k_ku_autor_date, k_ku_fname, k_ku_lname, k_ku_email, k_ku_banned_forever, k_ku_last_ip, k_ku_verified
            FROM k_users
            WHERE k_u_privileges=3
            ORDER BY k_ku_autor_date DESC
            LIMIT ' . (($page - 1) * 50) . ',50';
    }

    /**
     * Загрузить все забаненные IP адреса
     */
    public function BanIPList() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT k_ubil_ip FROM k_users_ban_ip_list');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        foreach ($result as $row) {
            array_push($this->banned_ip_list[$n], $row['k_ubil_ip']);
        }
    }

    /**
     * Проверить пользователя на бан по IP
     * @param String $ip IP адрес пользователя
     * @return boolean Забанен или нет
     */
    public function CheckBanIP($ip) {
        if (in_array($ip, $this->banned_ip_list)) {
            return true;
        }
        return false;
    }

    /**
     * Переопределяем метод загрузки для пользователей
     */
    public function LoadTable() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare($this->query);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        $n = 0;
        foreach ($result as $row) {
            $this->login[$n] = $row['k_ku_login'];
            $this->password[$n] = $row['k_ku_password'];
            $this->reg_date[$n] = $row['k_ku_autor_date'];
            $this->fname[$n] = $row['k_ku_fname'];
            $this->lname[$n] = $row['k_ku_lname'];
            $this->email[$n] = $row['k_ku_email'];
            $this->id[$n] = $row['k_ku_id'];
            $this->status[$n] = $row['k_ku_verified'];
            $this->banned[$n] = $row['k_ku_banned_forever'];
            $this->last_ip[$n] = $row['k_ku_last_ip'];
            $n++;
        }
    }

    /**
     * Генерация навигации по страницам
     * @param int $page Номер страницы
     */
    public function GenerateNavigation($page) {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT count(k_ku_id) as max FROM k_users WHERE k_u_privileges=3');
            $query->execute();
            $row = $query->fetch(PDO::FETCH_ASSOC);
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
                echo '<a class="style_listing" href="./?PageType=6&PageIndex=' . ($page - 1) . '">Предыдущая</a>';
            }

            if ($pages <= 11) {
                for ($i = 1; $i <= $pages; $i++) {
                    if ($i == $page) {
                        echo '<a class="active_listing" href="./?PageType=6&PageIndex=' . $i . '">' . $i . '</a>';
                    } else {
                        echo '<a class="style_listing" href="./?PageType=6&PageIndex=' . $i . '">' . $i . '</a>';
                    }
                }
            } else {
                if ($page <= 6) {
                    for ($i = 1; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageType=6&PageIndex=' . $i . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageType=6&PageIndex=' . $i . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageType=6&PageIndex=' . $i . '">' . $i . '</a>';
                    }
                }
                if ($page > 6 && $page <= ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageType=6&PageIndex=' . $i . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $page - 2; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageType=6&PageIndex=' . $i . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageType=6&PageIndex=' . $i . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageType=6&PageIndex=' . $i . '">' . $i . '</a>';
                    }
                }
                if ($page > ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageType=6&PageIndex=' . $i . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 5; $i <= $pages; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageType=6&PageIndex=' . $i . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageType=6&PageIndex=' . $i . '">' . $i . '</a>';
                        }
                    }
                }
            }

            if ($page == 1 || $page < $pages) {
                echo '<a class="style_listing" href="./?PageType=6&PageIndex=' . ($page + 1) . '">Следующая</a>';
            }

            echo '</div>';
        }
    }

}

/**
 * Класс для выведения статистики на главной странице админки
 */
class MainStatistics {

    public $last_visit_date = '';
    public $last_visit_ip = '';
    public $ads_all = 0;
    public $immo_ads = 0;
    public $ads_photo = 0;
    public $ads_job = 0;
    public $ads_catalog = 0;
    public $ads_webcams = 0;
    public $user_all = 0;
    public $user_active = 0;
    public $admin_active = 0;
    public $moderator_active = 0;
    public $user_inactive = 0;
    public $admin_inactive = 0;
    public $moderator_inactive = 0;
    public $user_with_ads = 0;
    public $user_experts = 0;
    public $user_admins_online = 0;
    public $user_moders_online = 0;
    public $last_5_users_date = array();
    public $last_5_users_login = array();

    /**
     * Загрузить всю статистику
     */
    public function MainStatistics() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT k_ku_last_ip, k_ku_last_date FROM k_users 
                WHERE k_ku_login=:login
                ORDER BY k_ku_last_date DESC
                LIMIT 1');
            $query->execute(array(":login" => $_SESSION['login']));
            $getresults = $query->fetch(PDO::FETCH_ASSOC);
            $this->last_visit_date = $getresults['k_ku_last_date'];
            $this->last_visit_ip = $getresults['k_ku_last_ip'];

            $query1 = $mysql->prepare('SELECT count(k_ku_id) as count, k_u_online as online, k_u_privileges FROM k_users
            WHERE k_ku_verified = 1
            GROUP BY k_u_online, k_u_privileges
            ORDER BY k_u_privileges ASC');
            $query1->execute();
            $results1 = $query1->fetchAll(PDO::FETCH_ASSOC);
            foreach ($results1 as $getresults) {
                if ($getresults['online'] == 1) {
                    if ($getresults['k_u_privileges'] == 3 || $getresults['k_u_privileges'] == 4) {
                        $this->user_active += $getresults['count'];
                    }
                    if ($getresults['k_u_privileges'] == 2) {
                        $this->moderator_active += $getresults['count'];
                    }
                    if ($getresults['k_u_privileges'] == 1) {
                        $this->admin_active += $getresults['count'];
                    }
                }
                if ($getresults['online'] == 0) {
                    if ($getresults['k_u_privileges'] == 3 || $getresults['k_u_privileges'] == 4) {
                        $this->user_inactive += $getresults['count'];
                    }
                    if ($getresults['k_u_privileges'] == 2) {
                        $this->moderator_inactive += $getresults['count'];
                    }
                    if ($getresults['k_u_privileges'] == 1) {
                        $this->admin_inactive += $getresults['count'];
                    }
                }
            }
            $this->user_all = $this->user_active + $this->user_inactive;

            $query2 = $mysql->prepare('SELECT k_ku_last_date,k_ku_login FROM k_users
            WHERE k_ku_verified = 1
            ORDER BY k_ku_autor_date DESC
            LIMIT 5');
            $query2->execute();
            $results2 = $query2->fetchAll(PDO::FETCH_ASSOC);
            $n = 0;
            foreach ($results2 as $getresults) {
                $this->last_5_users_date[$n] = $getresults['k_ku_last_date'];
                $this->last_5_users_login[$n] = $getresults['k_ku_login'];
                $n++;
            }
            $query3 = $mysql->prepare('SELECT count(k_isf_id) as max FROM k_immovables_sell');
            $query3->execute();
            $row3 = $query3->fetch(PDO::FETCH_ASSOC);
            $this->immo_ads = $row3['max'];

            $query4 = $mysql->prepare('SELECT count(k_j_id) as max FROM k_job');
            $query4->execute();
            $row4 = $query4->fetch(PDO::FETCH_ASSOC);
            $this->ads_job = $row4['max'];

            $query5 = $mysql->prepare('SELECT count(k_pd_id) as max FROM k_photodesk');
            $query5->execute();
            $row5 = $query5->fetch(PDO::FETCH_ASSOC);
            $this->ads_photo = $row5['max'];

            $this->ads_all = $this->immo_ads + $this->ads_job + $this->ads_photo;

            $query6 = $mysql->prepare('SELECT count(k_cf_id) as max FROM k_catalog_firms');
            $query6->execute();
            $row6 = $query6->fetch(PDO::FETCH_ASSOC);
            $this->ads_catalog = $row6['max'];

            $query7 = $mysql->prepare('SELECT count(k_w_id) as max FROM k_webcams');
            $query7->execute();
            $row7 = $query7->fetch(PDO::FETCH_ASSOC);
            $this->ads_webcams = $row7['max'];

            $query8 = $mysql->prepare('SELECT count(k_e_id) as max FROM k_experts');
            $query8->execute();
            $row8 = $query8->fetch(PDO::FETCH_ASSOC);
            $this->user_experts = $row8['max'];
        } catch (PDOException $e) {
            exit();
        }
    }

}

/**
 * Загрузить таблицу баннеров
 */
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
        $query_1 = 'SELECT * FROM k_all_banners WHERE k_ab_type=1';
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

class WarningMessages {

    public $message = array();
    public $date = array();

    public function WarningMessages() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT * FROM k_warning_messages ORDER BY k_wm_date DESC LIMIT 100');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                $this->message[] = $row['k_wm_message'];
                $this->date[] = $row['k_wm_date'];
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class IPBans {

    public $id = array();
    public $ip = array();
    public $date = array();

    public function IPBans() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query0 = $mysql->prepare('DELETE FROM k_users_ban_ip_list WHERE k_ubil_end_date<NOW()');
            $query0->execute();
            $query = $mysql->prepare('SELECT * FROM k_users_ban_ip_list ORDER BY k_ubil_id DESC');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                $this->id[] = $row['k_ubil_id'];
                $this->ip[] = $row['k_ubil_ip'];
                $this->date[] = $row['k_ubil_end_date'];
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

?>
