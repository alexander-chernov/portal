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
        $query_1 = 'SELECT * FROM k_all_banners WHERE k_ab_type=11';
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
            $query = $mysql->prepare('SELECT * FROM k_sites AS ks
                LEFT JOIN k_sites_links AS ksl ON (ksl.k_sl_site_id = ks.k_s_id)
                ' . $where . '
                GROUP BY k_s_id
                ORDER BY k_s_date DESC
                LIMIT ' . (($page - 1) * $limit) . ',' . $limit);
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
