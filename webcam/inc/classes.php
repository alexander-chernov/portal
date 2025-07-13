<?php

class WebcamsSite {

    public $id = array();
    public $image = array();
    public $name = array();
    public $url = array();
    public $all_cams = 0;

    public function WebcamsSite($page, $limit) {
        $page = filter_var($page, FILTER_VALIDATE_INT);
        $limit = filter_var($limit, FILTER_VALIDATE_INT, array("options" => array("min_range" => 2, "max_range" => 50)));
        if ($page != 0) {
            try {
                $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
                $mysql->exec('set names utf8');
                $queue = $mysql->prepare('SELECT * FROM k_webcams
                    ORDER BY k_w_id DESC
                    LIMIT ' . (($page - 1) * $limit) . ',' . $limit);
                $queue->execute();
                $result = $queue->fetchAll(PDO::FETCH_ASSOC);
                foreach ($result as $value) {
                    array_push($this->id, $value['k_w_id']);
                    array_push($this->name, $value['k_w_name']);
                    array_push($this->image, $value['k_w_image']);
                    array_push($this->url, $value['k_w_url']);
                }
                $queue2 = $mysql->prepare('SELECT count(*) AS max FROM k_webcams');
                $queue2->execute();
                $result2 = $queue2->fetch(PDO::FETCH_ASSOC);
                $this->all_cams = $result2['max'];
            } catch (PDOException $e) {
                exit();
            }
        }
    }

    function GenerateNavigation($page, $limit) {
        $page = filter_var($page, FILTER_VALIDATE_INT);
        $limit = filter_var($limit, FILTER_VALIDATE_INT, array("options" => array("min_range" => 2, "max_range" => 50)));
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue1 = $mysql->prepare('SELECT count(*) AS max FROM k_webcams');
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
                echo '<a class="style_listing" href="index.php?PageIndex=' . ($page - 1) . '&limit=' . $limit . '">Предыдущая</a>';
            }

            if ($pages <= 11) {
                for ($i = 1; $i <= $pages; $i++) {
                    if ($i == $page) {
                        echo '<a class="active_listing" href="index.php?PageIndex=' . $i . '&limit=' . $limit . '">' . $i . '</a>';
                    } else {
                        echo '<a class="style_listing" href="index.php?PageIndex=' . $i . '&limit=' . $limit . '">' . $i . '</a>';
                    }
                }
            } else {
                if ($page <= 6) {
                    for ($i = 1; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="index.php?PageIndex=' . $i . '&limit=' . $limit . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="index.php?PageIndex=' . $i . '&limit=' . $limit . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="index.php?PageIndex=' . $i . '&limit=' . $limit . '">' . $i . '</a>';
                    }
                }
                if ($page > 6 && $page <= ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="index.php?PageIndex=' . $i . '&limit=' . $limit . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $page - 2; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="index.php?PageIndex=' . $i . '&limit=' . $limit . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="index.php?PageIndex=' . $i . '&limit=' . $limit . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="index.php?PageIndex=' . $i . '&limit=' . $limit . '">' . $i . '</a>';
                    }
                }
                if ($page > ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="index.php?PageIndex=' . $i . '&limit=' . $limit . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 5; $i <= $pages; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="index.php?PageIndex=' . $i . '&limit=' . $limit . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="index.php?PageIndex=' . $i . '&limit=' . $limit . '">' . $i . '</a>';
                        }
                    }
                }
            }

            if ($page == 1 || $page < $pages) {
                echo '<a class="style_listing" href="index.php?PageIndex=' . ($page + 1) . '&limit=' . $limit . '">Следующая</a>';
            }

            echo '</div>';
        }
    }

}

?>
