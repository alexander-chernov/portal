<?php
define('TOMSKLINE', 1);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Статистика</title>
    </head>
    <body>
        <?php
        require_once 'inc/configs.php';

        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            echo '<h2>КАРТА</h2>';
            echo '<i><b>Произошло обновление карты!</b><br>* Возможность добавлять двойные адреса. Во вкладке "Карта" жмём на пункт "Объединение адресов"
                Выбираем адрес из одной колонки и из другой. Жмём кнопку "Объединить". Двойной адрес создан. Он будет виден в списке адресов на карте.
                Адреса можно разъединять, нажав на красный крестик возле соответствующего двойного адреса. Любые действия с адресами будут видны
                после обновления страницы, в которой находится редактор".
                <br>* Возможность загружать фотографии для домов. Во вкладке "Карта" жмём на пункт "Добавление / удаление фото у адреса".
                Далее необходимо выбрать адрес из списка. Кнопка "Добавить фото" работает только тогда, когда выбран адрес. Удаление фото
                осуществляется нажатием на красный крестик под соответствующим изображением.</i>';
            $queue = $mysql->prepare('SELECT user,count(DISTINCT geometry) as num
                    FROM map_buildings
                    WHERE user != "kristina" AND user != "nikon" AND user != "pavel"
                    GROUP BY user
                    ORDER BY num DESC');
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            echo '<table border="1"><tr><th>Пользователь</th><th>Число зданий</th><th>Рублей</th></tr>';
            foreach ($result as $value) {
                echo '<tr><td>' . $value['user'] . '</td><td>' . $value['num'] . '</td><td>' . ($value['num'] * 3) . '</td></tr>';
            }
            echo '</table><br><br>';
            $queue2 = $mysql->prepare('SELECT user,count(DISTINCT geometry) as num
                    FROM map_district
                    WHERE user != "kristina" AND user != "nikon" AND user != "pavel"
                    GROUP BY user
                    ORDER BY num DESC');
            $queue2->execute();
            $result2 = $queue2->fetchAll(PDO::FETCH_ASSOC);
            echo '<table border="1"><tr><th>Пользователь</th><th>Число районов</th><th>Рублей</th></tr>';
            foreach ($result2 as $value) {
                echo '<tr><td>' . $value['user'] . '</td><td>' . $value['num'] . '</td><td>' . ($value['num'] * 2.5) . '</td></tr>';
            }
            echo '</table><br><br>';
            $queue3 = $mysql->prepare('SELECT user,count(DISTINCT fid) as num
                    FROM map_landuse
                    WHERE user != "kristina" AND user != "nikon" AND user != "pavel"
                    GROUP BY user
                    ORDER BY num DESC');
            $queue3->execute();
            $result3 = $queue3->fetchAll(PDO::FETCH_ASSOC);
            echo '<table border="1"><tr><th>Пользователь</th><th>Число земель</th><th>Рублей</th></tr>';
            foreach ($result3 as $value) {
                echo '<tr><td>' . $value['user'] . '</td><td>' . $value['num'] . '</td><td>' . ($value['num'] * 2.5) . '</td></tr>';
            }
            echo '</table><br><br>';
            $queue4 = $mysql->prepare('SELECT user,count(DISTINCT fid) as num
                    FROM map_markers
                    WHERE user != "kristina" AND user != "nikon" AND user != "pavel"
                    GROUP BY user
                    ORDER BY num DESC');
            $queue4->execute();
            $result4 = $queue4->fetchAll(PDO::FETCH_ASSOC);
            echo '<table border="1"><tr><th>Пользователь</th><th>Число маркеров</th><th>Рублей</th></tr>';
            foreach ($result4 as $value) {
                echo '<tr><td>' . $value['user'] . '</td><td>' . $value['num'] . '</td><td>' . ($value['num'] * 2.5) . '</td></tr>';
            }
            echo '</table><br><br>';
            $queue5 = $mysql->prepare('SELECT user,count(DISTINCT fid) as num
                    FROM map_rails
                    WHERE user != "kristina" AND user != "nikon" AND user != "pavel"
                    GROUP BY user
                    ORDER BY num DESC');
            $queue5->execute();
            $result5 = $queue5->fetchAll(PDO::FETCH_ASSOC);
            echo '<table border="1"><tr><th>Пользователь</th><th>Число ж/д</th><th>Рублей</th></tr>';
            foreach ($result5 as $value) {
                echo '<tr><td>' . $value['user'] . '</td><td>' . $value['num'] . '</td><td>' . ($value['num'] * 2.5) . '</td></tr>';
            }
            echo '</table><br><br>';
            $queue6 = $mysql->prepare('SELECT user,count(DISTINCT fid) as num
                    FROM  map_routes
                    WHERE user != "kristina" AND user != "nikon" AND user != "pavel"
                    GROUP BY user
                    ORDER BY num DESC');
            $queue6->execute();
            $result6 = $queue6->fetchAll(PDO::FETCH_ASSOC);
            echo '<table border="1"><tr><th>Пользователь</th><th>Число маршрутов</th><th>Рублей</th></tr>';
            foreach ($result6 as $value) {
                echo '<tr><td>' . $value['user'] . '</td><td>' . $value['num'] . '</td><td>' . ($value['num'] * 2.5) . '</td></tr>';
            }
            echo '</table><br><br>';
            $queue7 = $mysql->prepare('SELECT user,count(DISTINCT fid) as num
                    FROM  map_station
                    WHERE user != "kristina" AND user != "nikon" AND user != "pavel"
                    GROUP BY user
                    ORDER BY num DESC');
            $queue7->execute();
            $result7 = $queue7->fetchAll(PDO::FETCH_ASSOC);
            echo '<table border="1"><tr><th>Пользователь</th><th>Число остановок</th><th>Рублей</th></tr>';
            foreach ($result7 as $value) {
                echo '<tr><td>' . $value['user'] . '</td><td>' . $value['num'] . '</td><td>' . ($value['num'] * 2.5) . '</td></tr>';
            }
            echo '</table><br><br>';
            $queue8 = $mysql->prepare('SELECT user,count(DISTINCT fid) as num
                    FROM  map_streets
                    WHERE user != "kristina" AND user != "nikon" AND user != "pavel"
                    GROUP BY user
                    ORDER BY num DESC');
            $queue8->execute();
            $result8 = $queue8->fetchAll(PDO::FETCH_ASSOC);
            echo '<table border="1"><tr><th>Пользователь</th><th>Число улиц</th><th>Рублей</th></tr>';
            foreach ($result8 as $value) {
                echo '<tr><td>' . $value['user'] . '</td><td>' . $value['num'] . '</td><td>' . ($value['num'] * 2.5) . '</td></tr>';
            }
            echo '</table><br><br>';
            echo '<hr><h2>КАТАЛОГ</h2>';
            $queue9 = $mysql->prepare('SELECT c_cf_user,count(k_cfa_id) as num
                    FROM k_catalog_firms AS kcf
                    LEFT JOIN k_catalog_firms_addresses AS kcfa ON (kcfa.k_cfa_parent = kcf.k_cf_id)
                    WHERE c_cf_user != "kristina" AND c_cf_user != "nikon" AND c_cf_user != "pavel"
                    GROUP BY c_cf_user
                    ORDER BY num DESC');
            $queue9->execute();
            $result9 = $queue9->fetchAll(PDO::FETCH_ASSOC);
            echo '<table border="1"><tr><th>Пользователь</th><th>Число адресов организаций</th><th>Рублей</th></tr>';
            foreach ($result9 as $value) {
                echo '<tr><td>' . $value['c_cf_user'] . '</td><td>' . $value['num'] . '</td><td>' . ($value['num'] * 4) . '</td></tr>';
            }
            echo '</table><br><br>';
        } catch (PDOException $e) {
            exit();
        }
        ?>
    </body>
</html>