<?php

class AllAddressesExDouble {

    public $id = array();
    public $address = array();

    public function AllAddressesExDouble() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare("SELECT k_shn_id,k_s_name,k_shn_house_num
                FROM k_streets_house_nums AS kshn
                LEFT JOIN k_streets AS ks ON (kshn.k_shn_street_id = ks.k_s_id)
                WHERE k_s_name NOT LIKE '%###%'
                ORDER BY k_s_name,k_shn_house_num ASC");
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $value) {
                array_push($this->id, $value['k_shn_id']);
                array_push($this->address, $value['k_s_name'] . ' ' . $value['k_shn_house_num']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class AllDoubleAddresses {

    public $id = array();
    public $address = array();

    public function AllDoubleAddresses($page, $where) {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare("SELECT k_shn_id,k_s_name,k_shn_house_num
                FROM k_streets_house_nums AS kshn
                LEFT JOIN k_streets AS ks ON (kshn.k_shn_street_id = ks.k_s_id)
                WHERE k_s_name LIKE '%###%' " . $where . "
                ORDER BY k_s_name,k_shn_house_num ASC
                LIMIT " . (($page - 1) * 50) . ",50");
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $value) {
                array_push($this->id, $value['k_shn_id']);
                $street = explode('###', $value['k_s_name']);
                $house = explode('###', $value['k_shn_house_num']);
                array_push($this->address, $street[0] . ' ' . $house[0] . ' <--> ' . $street[1] . ' ' . $house[1]);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

    function GenerateNavigation($page, $where, $link) {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue1 = $mysql->prepare("SELECT count(k_shn_id) AS max
                FROM k_streets_house_nums AS kshn
                LEFT JOIN k_streets AS ks ON (kshn.k_shn_street_id = ks.k_s_id)
                WHERE k_s_name LIKE '%###%' " . $where);
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

class AllAddresses {

    public $id = array();
    public $address = array();
    public $with_photo = array();

    public function AllAddresses($page, $where) {
        $where = preg_replace('/^( AND)/', ' WHERE', $where);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = "SELECT k_shn_id,k_s_name,k_shn_house_num,k_shp_id
                FROM k_streets_house_nums AS kshn
                LEFT JOIN k_streets AS ks ON (kshn.k_shn_street_id = ks.k_s_id)
                LEFT JOIN k_street_house_photos AS kshp ON (kshn.k_shn_id = kshp.k_shp_parent)
                " . $where . "
                GROUP BY k_shn_id
                ORDER BY k_s_name,k_shn_house_num ASC";
            if ($page != 0) {
                $query .= " LIMIT " . (($page - 1) * 50) . ",50";
            }
            $queue = $mysql->prepare($query);
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $value) {
                if (preg_match('/(###)/', $value['k_s_name'])) {
                    $street = explode('###', $value['k_s_name']);
                    $house = explode('###', $value['k_shn_house_num']);
                    array_push($this->id, $value['k_shn_id']);
                    array_push($this->address, $street[0] . ' ' . $house[0] . ' / ' . $street[1] . ' ' . $house[1]);
                } else {
                    array_push($this->id, $value['k_shn_id']);
                    array_push($this->address, $value['k_s_name'] . ' ' . $value['k_shn_house_num']);
                }
                $this->with_photo[] = $value['k_shp_id'];
            }
        } catch (PDOException $e) {
            exit();
        }
    }

    function GenerateNavigation($page, $where, $link) {
        $where = preg_replace('/^( AND)/', ' WHERE', $where);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue1 = $mysql->prepare("SELECT k_shn_id
                FROM k_streets_house_nums AS kshn
                LEFT JOIN k_streets AS ks ON (kshn.k_shn_street_id = ks.k_s_id)
                LEFT JOIN k_street_house_photos AS kshp ON (kshn.k_shn_id = kshp.k_shp_parent)
                " . $where . "
                GROUP BY k_shn_id");
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
                echo '<a class="style_listing" href="./?PageType=3&PageIndex=' . ($page - 1) . $link . '">Предыдущая</a>';
            }

            if ($pages <= 11) {
                for ($i = 1; $i <= $pages; $i++) {
                    if ($i == $page) {
                        echo '<a class="active_listing" href="./?PageType=3&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    } else {
                        echo '<a class="style_listing" href="./?PageType=3&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
            } else {
                if ($page <= 6) {
                    for ($i = 1; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageType=3&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageType=3&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageType=3&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
                if ($page > 6 && $page <= ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageType=3&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $page - 2; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageType=3&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageType=3&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageType=3&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
                if ($page > ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageType=3&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 5; $i <= $pages; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageType=3&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageType=3&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                }
            }

            if ($page == 1 || $page < $pages) {
                echo '<a class="style_listing" href="./?PageType=3&PageIndex=' . ($page + 1) . $link . '">Следующая</a>';
            }

            echo '</div>';
        }
    }

}

class AddressPhotos {

    public $id = array();
    public $url = array();

    public function AddressPhotos($page, $where) {
        $page = filter_var($page, FILTER_VALIDATE_INT);
        $query = "SELECT * FROM k_street_house_photos " . $where;
        if ($page != 0) {
            $query .= " LIMIT " . (($page - 1) * 50) . ",50";
        }
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare($query);
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $value) {
                $this->id[] = $value['k_shp_id'];
                $this->url[] = $value['k_shp_url'];
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class AllDistricts {

    public $id = array();
    public $name = array();

    public function AllDistricts($page, $where) {
        try {
            $where = preg_replace('/^( AND)/', ' WHERE', $where);
            $page = filter_var($page, FILTER_VALIDATE_INT);
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = "SELECT k_d_id,k_d_name FROM k_districts " . $where . " ORDER BY k_d_name ASC ";
            if ($page != 0) {
                $query .= " LIMIT " . (($page - 1) * 50) . ",50";
            }
            $queue = $mysql->prepare($query);
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $value) {
                array_push($this->id, $value['k_d_id']);
                array_push($this->name, $value['k_d_name']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

    function GenerateNavigation($page, $where, $link) {
        $where = preg_replace('/^( AND)/', ' WHERE', $where);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue1 = $mysql->prepare("SELECT count(k_d_id) AS max FROM k_districts " . $where);
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

}

class AllMassives {

    public $id = array();
    public $name = array();

    public function AllMassives($page, $where) {
        try {
            $where = preg_replace('/^( AND)/', ' WHERE', $where);
            $page = filter_var($page, FILTER_VALIDATE_INT);
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = "SELECT k_tm_id,k_tm_name FROM k_towns_massives " . $where . " ORDER BY k_tm_name ASC ";
            if ($page != 0) {
                $query .= " LIMIT " . (($page - 1) * 50) . ",50";
            } /*else {
                array_push($this->id, '0');
                array_push($this->name, 'Нет массива');
            }*/
            $queue = $mysql->prepare($query);
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $value) {
                array_push($this->id, $value['k_tm_id']);
                array_push($this->name, $value['k_tm_name']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

    function GenerateNavigation($page, $where, $link) {
        $where = preg_replace('/^( AND)/', ' WHERE', $where);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue1 = $mysql->prepare("SELECT count(*) AS max FROM k_towns_massives " . $where);
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
                echo '<a class="style_listing" href="./?PageType=6&PageIndex=' . ($page - 1) . $link . '">Предыдущая</a>';
            }

            if ($pages <= 11) {
                for ($i = 1; $i <= $pages; $i++) {
                    if ($i == $page) {
                        echo '<a class="active_listing" href="./?PageType=6&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    } else {
                        echo '<a class="style_listing" href="./?PageType=6&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
            } else {
                if ($page <= 6) {
                    for ($i = 1; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageType=6&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageType=6&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageType=6&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
                if ($page > 6 && $page <= ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageType=6&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $page - 2; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageType=6&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageType=6&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageType=6&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
                if ($page > ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageType=6&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 5; $i <= $pages; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageType=6&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageType=6&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                }
            }

            if ($page == 1 || $page < $pages) {
                echo '<a class="style_listing" href="./?PageType=6&PageIndex=' . ($page + 1) . $link . '">Следующая</a>';
            }

            echo '</div>';
        }
    }

}

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

    function GenerateNavigation($page, $where, $link) {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue1 = $mysql->prepare("SELECT count(k_s_id) AS max FROM k_streets WHERE k_s_name NOT LIKE '%###%' " . $where);
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
                echo '<a class="style_listing" href="./?PageType=4&PageIndex=' . ($page - 1) . $link . '">Предыдущая</a>';
            }

            if ($pages <= 11) {
                for ($i = 1; $i <= $pages; $i++) {
                    if ($i == $page) {
                        echo '<a class="active_listing" href="./?PageType=4&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    } else {
                        echo '<a class="style_listing" href="./?PageType=4&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
            } else {
                if ($page <= 6) {
                    for ($i = 1; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageType=4&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageType=4&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageType=4&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
                if ($page > 6 && $page <= ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageType=4&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $page - 2; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageType=4&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageType=4&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageType=4&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                }
                if ($page > ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageType=4&PageIndex=' . $i . $link . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 5; $i <= $pages; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageType=4&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageType=4&PageIndex=' . $i . $link . '">' . $i . '</a>';
                        }
                    }
                }
            }

            if ($page == 1 || $page < $pages) {
                echo '<a class="style_listing" href="./?PageType=4&PageIndex=' . ($page + 1) . $link . '">Следующая</a>';
            }

            echo '</div>';
        }
    }

}

?>
