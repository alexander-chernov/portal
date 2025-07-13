<?php

class TarrifPackages {

    public $id = array();
    public $name = array();
    public $lock_days = array();
    public $up = array();
    public $color = array();
    public $vip = array();
    public $price = array();

    function __construct() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT * FROM k_tariff_packages ORDER BY k_tp_id ASC');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                array_push($this->id, $row['k_tp_id']);
                array_push($this->name, $row['k_tp_name']);
                array_push($this->lock_days, $row['k_tp_lock_days']);
                array_push($this->up, $row['k_tp_up']);
                array_push($this->color, $row['k_tp_color']);
                array_push($this->vip, $row['k_tp_vip']);
                array_push($this->price, $row['k_tp_price']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class TarrifPricesForAd {

    public $id = array();
    public $name = array();
    public $days = array();
    public $price = array();

    function __construct() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT * FROM k_tariff_prices_for_ad ORDER BY k_tpfa_id ASC');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                array_push($this->id, $row['k_tpfa_id']);
                array_push($this->name, $row['k_tpfa_name']);
                array_push($this->days, $row['k_tpfa_days']);
                array_push($this->price, $row['k_tpfa_price']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class TarrifPackets {

    public $id = array();
    public $packet = array();
    public $owner = array();
    public $attr = array();
    public $price = array();

    function __construct() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT * FROM k_tariff_packets_attrs ORDER BY k_tpa_id ASC');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                array_push($this->id, $row['k_tpa_id']);
                array_push($this->packet, $row['k_tpa_packet']);
                array_push($this->owner, $row['k_tpa_owner']);
                array_push($this->attr, $row['k_tpa_int']);
                array_push($this->price, $row['k_tpa_price']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class TarrifOthers {

    public $id = array();
    public $name = array();
    public $price = array();

    function __construct() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT * FROM k_tariff_other ORDER BY k_to_id ASC');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                array_push($this->id, $row['k_to_id']);
                array_push($this->name, $row['k_to_name']);
                array_push($this->price, $row['k_to_price']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class TarrifVideo {

    public $id = array();
    public $name = array();
    public $price = array();
    public $duration = array();

    function __construct() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT * FROM k_tariff_video ORDER BY k_tv_id ASC');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                array_push($this->id, $row['k_tv_id']);
                array_push($this->name, $row['k_tv_name']);
                array_push($this->price, $row['k_tv_price']);
                array_push($this->duration, $row['k_tv_duration']);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

?>
