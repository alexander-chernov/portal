<?php

//include '../../inc/configs.php';

/**
 * Класс "Недвижимость"
 */
class Realty {

    public $id = array();
    public $deal_type = array();
    public $subcategory = array();
    public $subcategory_string = array();
    public $immo_type = array();
    public $immo_type_string = array();
    public $address = array();
    public $address_string = array();
    public $district = array();
    public $district_id = array();
    public $new = array();
    public $material = array();
    public $material_string = array();
    public $floor = array();
    public $floor_all = array();
    public $eq = array();
    public $eq_string = array();
    public $rooms = array();
    public $area_all = array();
    public $area_live = array();
    public $area_kitchen = array();
    public $area_land = array();
    public $san = array();
    public $balcony = array();
    public $phone_stat = array();
    public $security = array();
    public $internet = array();
    public $balcony_gl = array();
    public $furniture = array();
    public $fridge = array();
    public $washing = array();
    public $microwave = array();
    public $tv = array();
    public $ctv = array();
    public $stove = array();
    public $plastic_windows = array();
    public $utilities = array();
    public $price = array();
    public $quickly = array();
    public $exchange = array();
    public $credit = array();
    public $privat = array();
    public $documents = array();
    public $merch = array();
    public $owned = array();
    public $description = array();
    public $contacts = array();
    public $contact_name = array();
    public $owner = array();
    public $owner_string = array();
    public $views = array();
    public $registration_date = array();
    public $end_date = array();
    public $base = array();
    public $user = array();
    public $user_string = array();
    public $state = array();
    public $email = array();
    public $ip = array();
    public $photo_url = array();
    public $special = array();
    public $on_main = array();
    public $color_light = array();
    public $locked_start = array();
    public $locked_end = array();

    /**
     * Загрузить объявления
     * @param int $limit Сколько объявлений нужно выгрузить
     * @param int $page Номер страницы
     * @param String $where WHERE в SQL запросе
     */
    function LoadRealty($limit, $page, $where) {
        mysql_query('DELETE FROM k_immovables_locked WHERE k_il_date_stop<NOW()');
        mysql_query('DELETE FROM k_immovables_sell WHERE k_isf_end_date < NOW() - INTERVAL 2 MONTH');
        /*$query000 = 'SELECT k_ip_url FROM k_immovables_photos WHERE k_ip_immo_id=' . $_POST['ImmoIDDelSubmit'];
        $result000 = mysql_query($query000);
        while ($row2 = mysql_fetch_array($result000)) {
            if (preg_match('/(video)/', $result000['k_ip_url'])) {
                unlink('../../../' . $result000['k_ip_url']);
            } else {
                unlink('../../' . $result000['k_ip_url']);
            }
        }*/
        $query = 'SELECT ise.*, isub.k_is_name, iow.k_io_name, ist.k_isft_name, kism.k_isfm_name, kise.k_isfe_name,
            kshn.k_shn_house_num, kstre.k_s_name, kdis.k_d_name, kdis.k_d_id, kshn.k_shn_object_id,
            kto.k_t_name, kuser.k_ku_login, kuser.k_ku_email, kuser.k_ku_last_ip, kiph.k_ip_url, kil.*
            FROM k_immovables_sell as ise
            LEFT JOIN k_immovables_subcategories as isub ON (isub.k_is_id = ise.k_isf_subcategory)
            LEFT JOIN k_immovables_owners as iow ON (iow.k_io_id = ise.k_isf_owner)
            LEFT JOIN k_immovables_sell_types as ist ON (ist.k_isft_id = ise.k_isf_immovable_type)
            LEFT JOIN k_immovables_sell_material as kism ON (kism.k_isfm_id = ise.k_isf_material)
            LEFT JOIN k_immovables_sell_eq as kise ON (kise.k_isfe_id = ise.k_isf_eq)
            LEFT JOIN k_streets_house_nums as kshn ON (kshn.k_shn_id = ise.k_isf_address)
            LEFT JOIN k_streets as kstre ON (kstre.k_s_id = kshn.k_shn_street_id)
            LEFT JOIN k_districts as kdis ON (kdis.k_d_id = kshn.k_shn_district_id)
            LEFT JOIN k_towns as kto ON (kdis.k_d_town = kto.k_t_id)
            LEFT JOIN k_users as kuser ON (kuser.k_ku_id = ise.k_isf_user_id)
            LEFT JOIN k_immovables_locked as kil ON (kil.k_il_ad_id = ise.k_isf_id)
            LEFT JOIN k_immovables_photos as kiph ON (kiph.k_ip_immo_id = ise.k_isf_id) ' . $where . '
            GROUP BY k_isf_id
            ORDER BY k_isf_registration_date DESC';
        if ($limit != 0) {
            $query .= ' LIMIT ' . (($page - 1) * $limit) . ',' . $limit;
        }
        $result = mysql_query($query);
        $n = 0;
        while ($row = mysql_fetch_array($result)) {
            $this->id[$n] = $row['k_isf_id'];
            $this->deal_type[$n] = $row['k_isf_deal_type'];
            $this->subcategory[$n] = $row['k_isf_subcategory'];
            $this->subcategory_string[$n] = $row['k_is_name'];
            $this->immo_type[$n] = $row['k_isf_immovable_type'];
            $this->immo_type_string[$n] = $row['k_isft_name'];
            $this->address[$n] = $row['k_isf_address'];
            if (preg_match('/(###)/', $row['k_s_name'])) {
                $street = explode('###', $row['k_s_name']);
                $house = explode('###', $row['k_shn_house_num']);
                array_push($this->address_string, $street[0] . ' ' . $house[0] . ' / ' . $street[1] . ' ' . $house[1]);
            } else {
                array_push($this->address_string, $row['k_s_name'] . ' ' . $row['k_shn_house_num']);
            }
            $this->district[$n] = $row['k_d_name'];
            $this->district_id[$n] = $row['k_d_id'];
            $this->new[$n] = $row['k_isf_new'];
            $this->material[$n] = $row['k_isf_material'];
            $this->material_string[$n] = $row['k_isfm_name'];
            $this->floor[$n] = $row['k_isf_floor'];
            $this->floor_all[$n] = $row['k_isf_floor_all'];
            $this->eq[$n] = $row['k_isf_eq'];
            $this->eq_string[$n] = $row['k_isfe_name'];
            $this->rooms[$n] = $row['k_isf_rooms'];
            $this->area_all[$n] = $row['k_isf_area_all'];
            $this->area_live[$n] = $row['k_isf_area_live'];
            $this->area_kitchen[$n] = $row['k_isf_area_kitchen'];
            $this->area_land[$n] = $row['k_isf_area_land'];
            $this->san[$n] = $row['k_isf_san'];
            $this->balcony[$n] = $row['k_isf_balcony'];
            $this->phone_stat[$n] = $row['k_isf_phone_stat'];
            $this->security[$n] = $row['k_isf_security'];
            $this->internet[$n] = $row['k_isf_internet'];
            $this->balcony_gl[$n] = $row['k_isf_balcony_gl'];
            $this->furniture[$n] = $row['k_isf_furniture'];
            $this->fridge[$n] = $row['k_isf_fridge'];
            $this->washing[$n] = $row['k_isf_washing'];
            $this->microwave[$n] = $row['k_isf_microwave'];
            $this->tv[$n] = $row['k_isf_tv'];
            $this->ctv[$n] = $row['k_isf_ctv'];
            $this->stove[$n] = $row['k_isf_stove'];
            $this->plastic_windows[$n] = $row['k_isf_plastic_windows'];
            $this->utilities[$n] = $row['k_isf_utilities'];
            $this->price[$n] = $row['k_isf_price'];
            $this->quickly[$n] = $row['k_isf_quickly'];
            $this->exchange[$n] = $row['k_isf_exchange'];
            $this->credit[$n] = $row['k_isf_credit'];
            $this->privat[$n] = $row['k_isf_privat'];
            $this->documents[$n] = $row['k_isf_documents'];
            $this->merch[$n] = $row['k_isf_merch'];
            $this->owned[$n] = $row['k_isf_owned'];
            $this->description[$n] = $row['k_isf_description'];
            $this->contacts[$n] = $row['k_isf_contacts'];
            $this->contact_name[$n] = $row['k_isf_contact_name'];
            $this->owner[$n] = $row['k_isf_owner'];
            $this->owner_string[$n] = $row['k_io_name'];
            $this->views[$n] = $row['k_isf_views'];
            $this->registration_date[$n] = $row['k_isf_registration_date'];
            $this->end_date[$n] = $row['k_isf_end_date'];
            $this->base[$n] = $row['k_isf_base'];
            $this->user[$n] = $row['k_isf_user_id'];
            $this->user_string[$n] = $row['k_ku_login'];
            $this->state[$n] = $row['k_isf_state'];
            $this->email[$n] = $row['k_ku_email'];
            $this->ip[$n] = $row['k_ku_last_ip'];
            $this->photo_url[$n] = str_replace('video/', '../../video/', str_replace('images/', '../images/', $row['k_ip_url']));
            $this->on_main[$n] = $row['k_isf_main_page'];
            $this->color_light[$n] = $row['k_isf_color_light'];
            $this->locked_start[$n] = $row['k_il_date_start'];
            $this->locked_end[$n] = $row['k_il_date_stop'];
            $n++;
        }
        $query2 = "SELECT k_is_immovable_id FROM k_immovables_special ORDER BY k_is_id DESC";
        $result2 = mysql_query($query2);
        $i = 0;
        while ($row1 = mysql_fetch_array($result2)) {
            $this->special[$i] = $row1['k_is_immovable_id'];
            $i++;
        }
    }

    /**
     * Функция рисует таблицу объявлений в админке
     */
    function BuildTable() {
        for ($n = 0; $n < count($this->id); $n++) {
            //Цвет ячеек
            if ($this->owner[$n] == 1) {
                $color = "#f0e4f4";
                $user = 'Агентство';
                $style_u = 'user_table_1';
            }
            if ($this->owner[$n] == 2) {
                $color = "#f0f4f4";
                $user = 'Строитель';
                $style_u = 'user_table';
            }
            if ($this->owner[$n] == 3) {
                $color = "#f0f4f4";
                $user = 'Посредник';
                $style_u = 'user_table';
            }
            if ($this->owner[$n] == 4) {
                $color = "#f0f4f4";
                $user = 'Собственник';
                $style_u = 'user_table';
            }
            if ($this->state[$n] == 1) {
                $state = 'Активно';
                $style = 'style_4_1';
            } else {
                $state = 'Скрыто';
                $style = 'style_4_2';
            }
            $between = round((strtotime($this->end_date[$n]) - time()) / 86400, 0);
            $output = '<tr id="immo_tr_' . $this->id[$n] . '" style="background: ' . $color . ';">';
            $output .= '<td style = "width: 20px;"><input id="CheckedAds_' . $n . '" type = "checkbox" value = "' . $this->id[$n] . '"></td>';
            $output .= '<td style = "width: 80px;"><p class = "style_4">' . $this->id[$n] . '</p></td>';
            $output .= '<td style = "width: 80px;">';
            if ($this->photo_url[$n] && file_exists($this->photo_url[$n])) {
                $output .= '<img class = "img_ob" src = "' . $this->photo_url[$n] . '">';
            } else {
                $output .= '<img class = "img_ob" src = "../images/noimage.png">';
            }
            $output .= '</td>';
            $output .= '<td style = "width: 160px;"><p class = "style_4">' . $this->immo_type_string[$n] . '</p></td>';
            $output .= '<td><p class = "style_4">' . $this->user_string[$n] . '</p><p class = "' . $style_u . '">' . $user . '</p></td>';
            $output .= '<td><p class = "style_4">' . $this->registration_date[$n] . '</p></td>';
            $output .= '<td><p id="state_immo_' . $this->id[$n] . '" class = "' . $style . '">' . $state . '</p></td>';
            $output .= '<td><p class = "style_4_3">' . $between . ' дней</p></td>';
            $output .= '<td>
                <a class="a_1" onclick="ImmoEmail(\'' . $this->email[$n] . '\');"><img src="../images/send_email.png" title="Отправить E-mail" alt=""></a>
                <a class="a_1" onclick = "ImmoInfo(' . $this->id[$n] . ', \'' . $this->immo_type_string[$n] . '\', \'' . $this->user_string[$n] . '\', \'' . $this->registration_date[$n] . '\', \'' . $state . '\', ' . $between . ', \'' . $this->contact_name[$n] . '\', \'' . $this->contacts[$n] . '\', \'' . $this->address_string[$n] . '\', \'' . $this->email[$n] . '\', \'' . $user . '\');" title = "Информация по объявлению"><img src = "../images/info.png" alt = ""></a>
                <a class="a_1" onclick="ImmoChangeAd(' . $this->id[$n] . ');"><img src="../images/edit.png" title="Редактировать объявление" alt=""></a>
                <a class="a_1" onclick="PhotoEdit(' . $this->id[$n] . ');"><img src="../images/photo.png" title="Редактировать фото объявления" alt=""></a>';
            if (in_array($this->id[$n], $this->special)) {
                $output .= ' <a class="a_1"><img id="special_' . $this->id[$n] . '" src="../images/spec_2.png" title="Убрать из спец предложения" onClick="SpecialAction(' . $this->id[$n] . ',2);" alt=""></a>';
            } else {
                $output .= ' <a class="a_1"><img id="special_' . $this->id[$n] . '" src="../images/spec_1.png" title="Добавить в спец предложения" onClick="SpecialAction(' . $this->id[$n] . ',1);" alt=""></a>';
            }
            $output .= ' <a class="a_1"><img onClick="UpImmoBlock(this);" src = "../images/up.png" title = "Поднять объявление" alt = "' . $this->id[$n] . '"></a>';
            $output .= ' <a class="a_1"><img onClick="AddDaysImmo(' . $this->id[$n] . ');" src = "../images/clock_green_1.png" title = "Продлить объявление" alt = ""></a>';
            if ($this->state[$n] == 1) {
                $output .= ' <a class="a_1"><img id="endis_' . $this->id[$n] . '" onClick="DisEnAd(' . $this->id[$n] . ',0);" src = "../images/disable_1.png" title = "Скрыть объявление" alt = ""></a>';
            } else {
                $output .= ' <a class="a_1"><img id="endis_' . $this->id[$n] . '" onClick="DisEnAd(' . $this->id[$n] . ',1);" src = "../images/enable.png" title = "Показать объявление" alt = ""></a>';
            }
            if ($this->on_main[$n] == 1) {
                $output .= ' <a class="a_1"><img src="../images/not_main.png" title="Убрать с главной страницы" alt="' . $this->id[$n] . '" onclick="RealtyMainPage(this);"></a>';
            } else {
                $output .= ' <a class="a_1"><img src="../images/on_main.png" title="Добавить на главную страницу" alt="' . $this->id[$n] . '" onclick="RealtyMainPage(this);"></a>';
            }
            if ($this->color_light[$n] == 1) {
                $output .= ' <a class="a_1"><img src="../images/no_light.png" title="Убрать выделение цветом" alt="' . $this->id[$n] . '" onclick="RealtyColor(this);"></a>';
            } else {
                $output .= ' <a class="a_1"><img src="../images/color_light.png" title="Добавить выделение цветом" alt="' . $this->id[$n] . '" onclick="RealtyColor(this);"></a>';
            }
            /* if ($this->color_light[$n] == 1) {
              $output .= ' <a class="a_1"><img src="../images/ungovno.png" title="Разобосрать" alt="'.$this->id[$n].'" onclick="RealtyColor(this);"></a>';
              } else {
              $output .= ' <a class="a_1"><img src="../images/govno.png" title="Обосрать" alt="'.$this->id[$n].'" onclick="RealtyColor(this);"></a>';
              } */
            if ($this->locked_start[$n] != '') {
                $output .= ' <a class="a_1"><img src="../images/unlock.png" title="Открепить" alt="' . $this->id[$n] . '" onclick="RealtyLock(this);"></a>';
            } else {
                $output .= ' <a class="a_1"><img src="../images/lock.png" title="Закрепить" alt="' . $this->id[$n] . '" onclick="RealtyLock(this);"></a>';
            }
            $output .= ' <a class="a_1"><img onClick="BlockIP(\'' . $this->ip[$n] . '\');" src = "../images/block_ip.png" title = "Блокировать по IP: ' . $this->ip[$n] . '" alt = ""></a>';
            $output .= ' <a class="a_1"><img src = "../images/delete.png" onClick="DeleteAd(' . $this->id[$n] . ');" title = "Удалить объявление" alt = ""></a>';
            $output .= '</td></tr>';
            echo $output;
        }
    }

    /**
     * Функция генерирует <select> типов квартир
     * @param Int $immo_id ID типа
     */
    function TypeGenerate($immo_id, $sub) {
        if ($sub > 5) {
            $sub -= 5;
        }
        $print = '<select id="ImmoChangeType" style="width: 100%;">';
        $query1 = 'SELECT k_isft_id, k_isft_name FROM k_immovables_sell_types WHERE k_isft_sub_id=' . $sub . ' ORDER BY k_isft_name ASC';
        $result1 = mysql_query($query1);
        $print .= '<option value="0">Не указано</option>';
        while ($row1 = mysql_fetch_array($result1)) {
            $print .= '<option value="' . $row1['k_isft_id'] . '">' . $row1['k_isft_name'] . '</option>';
        }
        $print .= '</select>';
        if ($immo_id != 0) {
            echo str_replace('value="' . $immo_id . '"', 'value="' . $immo_id . '" selected', $print);
        } else {
            echo $print;
        }
    }

    /**
     * Функция генерирует район
     * @param Int $id ID района
     */
    function DistrictGenerate($id) {
        $query = 'SELECT * FROM k_districts WHERE k_d_id=' . $id;
        $result = mysql_query($query);
        $row = mysql_fetch_assoc($result);
        $print = '<span id="ImmoChangeDistrict" style="color: blue; font-weight: bold;">';
        $print .= $row['k_d_name'];
        $print .= '</span>';
        echo $print;
    }

    /**
     * Генерирует массив
     * @param int $id ID массива
     */
    function MassiveGenerate($id) {
        $query = 'SELECT * FROM k_towns_massives WHERE k_tm_id=' . $id;
        $result = mysql_query($query);
        $row = mysql_fetch_assoc($result);
        $print = '<span id="ImmoChangeMassive" style="color: blue; font-weight: bold;">';
        $print .= $row['k_tm_name'];
        $print .= '</span>';
        echo $print;
    }

    /**
     * Функция генерирует <select> материалов
     * @param int $material ID выделенного материала
     */
    function MaterialGenerate($material) {
        $print = '<select id="ImmoChangeMaterial" style="width: 95%;">';
        $query1 = 'SELECT k_isfm_id, k_isfm_name FROM k_immovables_sell_material ORDER BY k_isfm_name ASC';
        $result1 = mysql_query($query1);
        $print .= '<option value="0">Не указан</option>';
        while ($row1 = mysql_fetch_array($result1)) {
            $print .= '<option value="' . $row1['k_isfm_id'] . '">' . $row1['k_isfm_name'] . '</option>';
        }
        $print .= '</select>';
        echo str_replace('value="' . $material . '"', 'value="' . $material . '" selected', $print);
    }

    /**
     * Загрузка отделки
     * @return String Генерирует <option> отделки
     */
    function EQload() {
        $query = 'SELECT * FROM k_immovables_sell_eq ORDER BY k_isfe_name ASC';
        $result = mysql_query($query);
        $prnt = '';
        while ($row = mysql_fetch_array($result)) {
            $prnt .= '<option value="' . $row['k_isfe_id'] . '">' . $row['k_isfe_name'] . '</option>';
        }
        return $prnt;
    }

    function AllAddresses($ids) {
        $address = array();
        $id = array();
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare("SELECT k_shn_id,k_s_name,k_shn_house_num
                FROM k_streets_house_nums AS kshn
                LEFT JOIN k_streets AS ks ON (kshn.k_shn_street_id = ks.k_s_id)
                ORDER BY k_s_name,k_shn_house_num ASC");
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $value) {
                if (preg_match('/(###)/', $value['k_s_name'])) {
                    $street = explode('###', $value['k_s_name']);
                    $house = explode('###', $value['k_shn_house_num']);
                    array_push($id, $value['k_shn_id']);
                    array_push($address, $street[0] . ' ' . $house[0] . ' / ' . $street[1] . ' ' . $house[1]);
                } else {
                    array_push($id, $value['k_shn_id']);
                    array_push($address, $value['k_s_name'] . ' ' . $value['k_shn_house_num']);
                }
            }
            for ($i = 0; $i < count($id); $i++) {
                if ($ids == $id[$i]) {
                    $prnt .= '<option selected value="' . $id[$i] . '">' . $address[$i] . '</option>';
                } else {
                    $prnt .= '<option value="' . $id[$i] . '">' . $address[$i] . '</option>';
                }
            }
        } catch (PDOException $e) {
            exit();
        }
        return $prnt;
    }

    /**
     * Генерация окна редактирования объявления
     * @param int $id ID объявления
     */
    function GenerateWindowChange($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        $id_in = array_search($id, $this->id);
        //Шапка редактирования
        echo '<a class="close" onclick="CloseWindow(\'edit_obiavlenie\');">X</a><br>';
        if (!($id_in === FALSE)) {
            echo '<div class="edit_ob">
            <table><tr><td colspan="2">
                <b>Редактируем объявление № <font color="red">' . $id . '</font></b>
            </td></tr>
            <tr><td>Тип:</td><td>';
            //Тип объекта
            $this->TypeGenerate($this->immo_type[$id_in], $this->subcategory[$id_in]);
            //Ссылка на карту
            echo '</td></tr></table>';
            //Поиск дома
            echo '<div style="border: 1px solid #ccc;">
                <table>';
            echo '<tr><td>Выбор адреса:</td>
            <td id="ImmoAddressResult">
            <select id="ImmoAddressChosen" onchange="AddressSelectChange();" style="width: 100%;" name="ImmoAddressChosen">';
            echo $this->AllAddresses($this->address[$id_in]);
            echo '</select>
            </td></tr>';
            echo '<tr><td>Район:</td><td>';
            $this->DistrictGenerate($this->district_id[$id_in]);
            echo '</td></tr><tr><td>Жилмассив:</td><td>';
            $this->MassiveGenerate($this->district_id[$id_in]);
            echo '</td></tr></table></div><table>';
//Новостройка/вторичное
            $new = array(1, 2, 3, 4, 8);
            if (in_array($this->subcategory[$id_in], $new)) {
                $prnt = '<tr><td>Вид:</td><td><select id="ImmoChangeClass" style="width: 95%;">
                <option value="0">Не указано</option>
                <option value="1">Новостройка</option>
                <option value="2">Вторичное</option>
                </select></td></tr>';
                echo str_replace('value="' . $this->new[$id_in] . '"', 'value="' . $this->new[$id_in] . '" selected', $prnt);
            }
//Материал
            $material = array(1, 2, 3, 7, 8);
            if (in_array($this->subcategory[$id_in], $material)) {
                echo '<tr><td>Материал:</td><td>';
                $this->MaterialGenerate($this->material[$id_in]);
                echo '</td></tr>';
            }
//Этаж
            $floor = array(1, 3, 4, 6, 8, 9);
            if (in_array($this->subcategory[$id_in], $floor)) {
                $prnt = '<tr><td>Этаж:</td><td>';
                $prnt .= '<select id="ImmoChangeFloor" style="width: 95%;">';
                $prnt .= '<option value="0">Не указан</option>';
                for ($i = 1; $i <= 40; $i++) {
                    $prnt .= '<option value="' . $i . '">' . $i . '</option>';
                }
                $prnt .= '<option value="41">Мансарда с окнами</option>';
                $prnt .= '<option value="42">Мансарда без окон</option>';
                $prnt .= '<option value="43">Цоколь с окнами</option>';
                $prnt .= '<option value="44">Цоколь без окон</option></select></td></tr>';
                echo str_replace('value="' . $this->floor[$id_in] . '"', 'value="' . $this->floor[$id_in] . '" selected', $prnt);
                $prnt2 = '<tr><td>Количество этажей здания:</td><td>';
                $prnt2 .= '<select id="ImmoChangeFloorAll" style="width: 95%;">';
                $prnt2 .= '<option value="0">Не указан</option>';
                for ($i = 1; $i <= 40; $i++) {
                    if ($this->floor_all[$id_in] == $i) {
                        $prnt2 .= '<option selected value="' . $i . '">' . $i . '</option>';
                    } else {
                        $prnt2 .= '<option value="' . $i . '">' . $i . '</option>';
                    }
                }
                $prnt2 .= '</select></td></tr>';
                echo $prnt2;
            }
//Отделка
            $eq = array(1, 2, 3);
            if (in_array($this->subcategory[$id_in], $eq)) {
                $prnt3 = '<tr><td>Отделка:</td><td>';
                $prnt3 .= '<select id="ImmoChangeEQ" style="width: 95%;">';
                $prnt3 .= '<option value="0">Не указан</option>';
                $prnt3 .= $this->EQload();
                $prnt3 .= '</td></tr>';
                echo str_replace('value="' . $this->eq[$id_in] . '"', 'value="' . $this->eq[$id_in] . '" selected', $prnt3);
            }
//Комнаты
            $rooms = array(1, 2, 6, 7);
            if (in_array($this->subcategory[$id_in], $rooms)) {
                $prnt4 = '<tr><td>Колличество комнат:</td><td>';
                $prnt4 .= '<input type="text" maxlength="2" id="ImmoChangeRooms" value="' . $this->rooms[$id_in] . '" style="width: 95%;"></td></tr>';
                echo $prnt4;
            }
//Общая площадь
            $area_all = array(1, 2, 3, 4, 6, 7, 8, 9);
            if (in_array($this->subcategory[$id_in], $area_all)) {
                $prnt4 = '<tr><td>Общая площадь в м<sup>2</sup>:</td><td>';
                $prnt4 .= '<input type="text" id="ImmoChangeAreaAll" value="' . $this->area_all[$id_in] . '" style="width: 95%;"></td></tr>';
                echo $prnt4;
            }
//Жилая площадь
            $area_live = array(1, 2, 6, 7);
            if (in_array($this->subcategory[$id_in], $area_live)) {
                $prnt4 = '<tr><td>Жилая площадь в м<sup>2</sup>:</td><td>';
                $prnt4 .= '<input type="text" id="ImmoChangeAreaLive" value="' . $this->area_live[$id_in] . '" style="width: 95%;"></td></tr>';
                echo $prnt4;
            }
//Площадь кухни
            $area_kitchen = array(1, 6);
            if (in_array($this->subcategory[$id_in], $area_kitchen)) {
                $prnt4 = '<tr><td>Площадь кухни в м<sup>2</sup>:</td><td>';
                $prnt4 .= '<input type="text" id="ImmoChangeAreaKitchen" value="' . $this->area_kitchen[$id_in] . '" style="width: 95%;"></td></tr>';
                echo $prnt4;
            }
//Площадь участка
            $area_land = array(2, 5, 7);
            if (in_array($this->subcategory[$id_in], $area_land)) {
                $prnt4 = '<tr><td>Площадь участка в сотках:</td><td>';
                $prnt4 .= '<input type="text" id="ImmoChangeAreaLand" value="' . $this->area_land[$id_in] . '" style="width: 95%;"></td></tr>';
                echo $prnt4;
            }
//Санузел
            $san = array(1, 6);
            if (in_array($this->subcategory[$id_in], $san)) {
                $prnt5 = '<tr><td>Санузел:</td><td>';
                $prnt5 .= '<select id="ImmoChangeSan" style="width: 95%;">';
                $prnt5 .= '<option value="0">Не указан</option>';
                $prnt5 .= '<option value="1">Совмещённый</option>';
                $prnt5 .= '<option value="2">Раздельный</option>';
                $prnt5 .= '</select></td></tr>';
                echo str_replace('value="' . $this->san[$id_in] . '"', 'value="' . $this->san[$id_in] . '" selected', $prnt5);
                $prnt6 = '<tr><td>Балкон:</td><td>';
                $prnt6 .= '<select id="ImmoChangeBalcony" style="width: 95%;">';
                $prnt6 .= '<option value="0">Не указан</option>';
                $prnt6 .= '<option value="1">Балкон</option>';
                $prnt6 .= '<option value="2">Лоджия</option>';
                $prnt6 .= '<option value="3">Нет</option>';
                echo str_replace('value="' . $this->balcony[$id_in] . '"', 'value="' . $this->balcony[$id_in] . '" selected', $prnt6);
            }
//Телефон
            $phone_stat = array(1, 2, 3, 7, 8);
            if (in_array($this->subcategory[$id_in], $phone_stat)) {
                $prnt5 = '<tr><td>Телефон:</td><td>';
                if ($this->phone_stat[$id_in] == 1) {
                    $chk = 'checked';
                }
                $prnt5 .= '<input id="ImmoChangePhoneStat" type="checkbox" ' . $chk . '>';
                echo $prnt5;
            }
//Охрана
            $security = array(3, 8);
            if (in_array($this->subcategory[$id_in], $security)) {
                $prnt5 = '<tr><td>Охрана:</td><td>';
                $chk = '';
                if ($this->security[$id_in] == 1) {
                    $chk = 'checked';
                }
                $prnt5 .= '<input id="ImmoChangeSecurity" type="checkbox" ' . $chk . '></td></tr>';
                echo $prnt5;
                $prnt6 = '<tr><td>Интернет:</td><td>';
                $chk = '';
                if ($this->internet[$id_in] == 1) {
                    $chk = 'checked';
                }
                $prnt6 .= '<input id="ImmoChangeInternet" type="checkbox" ' . $chk . '></td></tr>';
                echo $prnt6;
            }
//Балкон застеклён
            if ($this->subcategory[$id_in] == 6) {
                $prnt5 = '<tr><td>Балкон/лоджия застеклена:</td><td>';
                $chk = '';
                if ($this->balcony_gl[$id_in] == 1) {
                    $chk = 'checked';
                }
                $prnt5 .= '<input id="ImmoChangeBalconyGl" type="checkbox" ' . $chk . '></td></tr>';
                echo $prnt5;
            }
//Мебель
            $furniture = array(6, 7);
            if (in_array($this->subcategory[$id_in], $furniture)) {
                $prnt5 = '<tr><td>Мебель:</td><td>';
                $chk = '';
                if ($this->furniture[$id_in] == 1) {
                    $chk = 'checked';
                }
                $prnt5 .= '<input id="ImmoChangeFurniture" type="checkbox" ' . $chk . '></td></tr>';
                echo $prnt5;
                $prnt1 = '<tr><td>Холодильник:</td><td>';
                $chk = '';
                if ($this->fridge[$id_in] == 1) {
                    $chk = 'checked';
                }
                $prnt1 .= '<input id="ImmoChangeFridge" type="checkbox" ' . $chk . '></td></tr>';
                echo $prnt1;
                $prnt2 = '<tr><td>Стиральная машина:</td><td>';
                $chk = '';
                if ($this->washing[$id_in] == 1) {
                    $chk = 'checked';
                }
                $prnt2 .= '<input id="ImmoChangeWashing" type="checkbox" ' . $chk . '></td></tr>';
                echo $prnt2;
                $prnt3 = '<tr><td>Микроволновая печь:</td><td>';
                $chk = '';
                if ($this->microwave[$id_in] == 1) {
                    $chk = 'checked';
                }
                $prnt3 .= '<input id="ImmoChangeMicrowave" type="checkbox" ' . $chk . '></td></tr>';
                echo $prnt3;
                $prnt4 = '<tr><td>Телевизор:</td><td>';
                $chk = '';
                if ($this->tv[$id_in] == 1) {
                    $chk = 'checked';
                }
                $prnt4 .= '<input id="ImmoChangeTV" type="checkbox" ' . $chk . '></td></tr>';
                echo $prnt4;
                $prnt6 = '<tr><td>Кабельное ТВ:</td><td>';
                $chk = '';
                if ($this->ctv[$id_in] == 1) {
                    $chk = 'checked';
                }
                $prnt6 .= '<input id="ImmoChangeCTV" type="checkbox" ' . $chk . '></td></tr>';
                echo $prnt6;
            }
            if ($this->subcategory[$id_in] == 6) {
                $prnt7 = '<tr><td>Кухонная плита:</td><td>';
                $chk = '';
                if ($this->stove[$id_in] == 1) {
                    $chk = 'checked';
                }
                $prnt7 .= '<input id="ImmoChangeStove" type="checkbox" ' . $chk . '></td></tr>';
                echo $prnt7;
                $prnt8 = '<tr><td>Пластиковые окна:</td><td>';
                $chk = '';
                if ($this->plastic_windows[$id_in] == 1) {
                    $chk = 'checked';
                }
                $prnt8 .= '<input id="ImmoChangePlastic" type="checkbox" ' . $chk . '></td></tr>';
                echo $prnt8;
            }
//Коммунальные услуги
            $utilities = array(6, 7, 8, 9);
            if (in_array($this->subcategory[$id_in], $utilities)) {
                $prnt = '<tr><td>Комунальные услуги:</td><td>';
                $prnt .= '<select id="ImmoChangeUtil" style="width: 95%;">';
                $prnt .= '<option value="0">Не указан</option>';
                $prnt .= '<option value="1">Входят в цену</option>';
                $prnt .= '<option value="2">Не входят в цену</option>';
                $prnt .= '</select></td></tr>';
                echo str_replace('value="' . $this->utilities[$id_in] . '"', 'value="' . $this->utilities[$id_in] . '" selected', $prnt);
            }
            echo '<tr><td>Цена (тысяч рублей)</td><td><input id="ImmoChangePrice" type="text" value="' . $this->price[$id_in] . '" style="width: 95%;"></td></tr>';
//За кв.м.
            $sq_m = array(1, 2);
            if (in_array($this->subcategory[$id_in], $sq_m)) {
                if ($this->area_all[$id_in]>0) {
                    $pr_m = round(($this->price[$id_in] / $this->area_all[$id_in]), 1);
                } else {
                    $pr_m = 0;
                }

                if ($pr_m != 0) {
                    $prnt = '<tr><td>Цена за м<sup>2</sup>:</td><td>';
                    $prnt .= round(($this->price[$id_in] / $this->area_all[$id_in]), 1) . ' тысяч рублей</td></tr>';
                    echo $prnt;
                }
            }
            $chk = '';
            if ($this->quickly[$id_in] == 1) {
                $chk = 'checked';
            }
            echo '<tr><td>Срочно</td><td><input id="ImmoChangeQuickly" type="checkbox" ' . $chk . '></td></tr>';
            $chk = '';
            if ($this->merch[$id_in] == 1) {
                $chk = 'checked';
            }
            echo '<tr><td>Торг</td><td><input id="ImmoChangeMerch" type="checkbox" ' . $chk . '></td></tr>';
//Дополнительно
            $adv = array(1, 2, 3, 4, 5);
            if (in_array($this->subcategory[$id_in], $adv)) {
                $chk = '';
                if ($this->exchange[$id_in] == 1) {
                    $chk = 'checked';
                }
                $prnt = '<tr><td>Обмен</td><td><input id="ImmoChangeExchange" type="checkbox" ' . $chk . '></td></tr>';
                $chk = '';
                if ($this->credit[$id_in] == 1) {
                    $chk = 'checked';
                }
                $prnt .= '<tr><td>Ипотека</td><td><input id="ImmoChangeCredit" type="checkbox" ' . $chk . '></td></tr>';
                $chk = '';
                if ($this->documents[$id_in] == 1) {
                    $chk = 'checked';
                }
                $prnt .= '<tr><td>Документы готовы</td><td><input id="ImmoChangeDoc" type="checkbox" ' . $chk . '></td></tr>';
                $chk = '';
                if ($this->owned[$id_in] == 1) {
                    $chk = 'checked';
                }
                $prnt .= '<tr><td>В собственности</td><td><input id="ImmoChangeOwned" type="checkbox" ' . $chk . '></td></tr>';
                echo $prnt;
            }
            $adv2 = array(1, 2, 4, 5);
            if (in_array($this->subcategory[$id_in], $adv2)) {
                $chk = '';
                if ($this->privat[$id_in] == 1) {
                    $chk = 'checked';
                }
                $prnt = '<tr><td>Приватизировано</td><td><input id="ImmoChangePrivat" type="checkbox" ' . $chk . '></td></tr>';
            }
            echo '<tr><td>Описание</td><td><textarea id="ImmoChangeDescr" rows="5" cols="30" style="resize: none;">' . $this->description[$id_in] . '</textarea></td></tr>';
            echo '<tr><td>Контактное лицо</td><td><input id="ImmoChangeContName" type="text" value="' . $this->contact_name[$id_in] . '" style="width: 95%;"></td></tr>';
            echo '<tr><td>Контакты</td><td><input type="text" id="ImmoChangeContacts" value="' . $this->contacts[$id_in] . '" style="width: 95%;"></td></tr>';
            echo '<tr><td colspan="2"><button onClick="ImmoSaveInDB(' . $this->subcategory[$id_in] . ',' . $this->id[$id_in] . ');" style="width: 100%;">Изменить</button></td></tr>';
            echo '</table>';
            echo '</div>';
        } else {
            echo '<br>Объявление №' . $id . ' не найдено!';
        }
    }

    function GeneratePhotos($id) {
        $query = 'SELECT * FROM k_immovables_photos WHERE k_ip_immo_id=' . $id;
        $result = mysql_query($query);
        $print = '<a class="close" onclick="CloseWindow(\'photo_obiavlenie\');">X</a>
            <br><br>
            <p class="style_7">Все фото объявления</p>
            <table>';
        while ($row = mysql_fetch_array($result)) {
            $file_name = str_replace('video/', '../../video/', str_replace('images/', '../images/', $row['k_ip_url']));
            $print .= '<tr id="photo_id_' . $row['k_ip_id'] . '" style="background: #f0f4f4;">
                <td style="width: 80px;">
                <img class="img_ob" src="' . $file_name . '" onclick="PhotoShow(\'' . $file_name . '\')">
                </td>
                <td style="width: 150px;">
                <a class="a_1">
                <img src="../images/delete.png" title="Удалить фото" alt="" onClick="DeletePhoto(' . $row['k_ip_id'] . ');">
                </a>
                </td>
                </tr>';
        }
        $print .= '</table>';
        echo $print;
    }

    function GenerateNavigation($page) {
        $query = 'SELECT count(k_isf_id) as max FROM k_immovables_sell';
        $result = mysql_query($query);
        $row = mysql_fetch_assoc($result);
        $pages = intval($row['max'] / 50);
        if ($row['max'] % 50 != 0) {
            $pages++;
        }
        if ($pages > 1) {
            echo '<div class="listing">';
            if ($page == $pages || $page > 1) {
                echo '<a class="style_listing" href="./?PageType=1&PageIndex=' . ($page - 1) . '">Предыдущая</a>';
            }

            if ($pages <= 11) {
                for ($i = 1; $i <= $pages; $i++) {
                    if ($i == $page) {
                        echo '<a class="active_listing" href="./?PageType=1&PageIndex=' . $i . '">' . $i . '</a>';
                    } else {
                        echo '<a class="style_listing" href="./?PageType=1&PageIndex=' . $i . '">' . $i . '</a>';
                    }
                }
            } else {
                if ($page <= 6) {
                    for ($i = 1; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageType=1&PageIndex=' . $i . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageType=1&PageIndex=' . $i . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageType=1&PageIndex=' . $i . '">' . $i . '</a>';
                    }
                }
                if ($page > 6 && $page <= ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageType=1&PageIndex=' . $i . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $page - 2; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageType=1&PageIndex=' . $i . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageType=1&PageIndex=' . $i . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageType=1&PageIndex=' . $i . '">' . $i . '</a>';
                    }
                }
                if ($page > ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageType=1&PageIndex=' . $i . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 5; $i <= $pages; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageType=1&PageIndex=' . $i . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageType=1&PageIndex=' . $i . '">' . $i . '</a>';
                        }
                    }
                }
            }

            if ($page == 1 || $page < $pages) {
                echo '<a class="style_listing" href="./?PageType=1&PageIndex=' . ($page + 1) . '">Следующая</a>';
            }

            echo '</div>';
        }
    }

}

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
        $query_1 = 'SELECT * FROM k_all_banners WHERE k_ab_type=2';
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

class Agents {

    public $agent_id = array();
    public $firm_id = array();
    public $agent_login = array();
    public $agent_ip = array();
    public $agent_autor_date = array();
    public $agent_fname = array();
    public $agent_lname = array();
    public $agent_oname = array();
    public $agent_email = array();
    public $agent_last_date = array();
    public $agent_online = array();
    public $agent_name = array();
    public $agent_avatar = array();
    public $agent_state = array();
    public $agent_phone = array();
    public $agent_address = array();
    public $agent_address_str = array();
    public $agent_site = array();
    public $agent_end_date = array();
    public $agent_end_days = array();
    public $agent_description = array();
    public $agent_in_top = array();

    function LoadAgents($limit, $page, $where) {
        $page = filter_var($page, FILTER_VALIDATE_INT);
        $limit = filter_var($limit, FILTER_VALIDATE_INT);
        $query = 'SELECT ku.*, kua.*, ks.k_s_name, kshn.k_shn_house_num, kshn.k_shn_id, kuar.k_uar_id
            FROM k_users as ku
            LEFT JOIN k_users_agents as kua ON (kua.k_ua_user_parent = ku.k_ku_id)
            LEFT JOIN k_streets_house_nums as kshn ON (kua.k_ua_address = kshn.k_shn_id)
            LEFT JOIN k_streets as ks ON (kshn.k_shn_street_id = ks.k_s_id)
            LEFT JOIN k_users_agents_register as kuar ON (kuar.k_uar_user_id = ku.k_ku_id)
            WHERE k_u_privileges=4 ' . $where;
        $query .= ' ORDER BY k_ku_id DESC';
        if ($limit != 0) {
            $query .= ' LIMIT ' . (($page - 1) * $limit) . ',' . $limit;
        }
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare($query);
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                $this->agent_id[] = $row['k_ku_id'];
                $this->firm_id[] = $row['k_ua_id'];
                $this->agent_login[] = $row['k_ku_login'];
                $this->agent_ip[] = $row['k_ku_last_ip'];
                $this->agent_autor_date[] = $row['k_ku_autor_date'];
                $this->agent_fname[] = $row['k_ku_fname'];
                $this->agent_lname[] = $row['k_ku_lname'];
                $this->agent_oname[] = $row['k_ku_oname'];
                $this->agent_email[] = $row['k_ku_email'];
                $this->agent_last_date[] = $row['k_ku_last_date'];
                $this->agent_online[] = $row['k_u_online'];
                $this->agent_name[] = $row['k_ua_name'];
                $this->agent_avatar[] = $row['k_ua_avatar'];
                $this->agent_state[] = $row['k_ua_state'];
                $this->agent_phone[] = $row['k_ua_phone'];
                $this->agent_address[] = $row['k_shn_id'];
                $this->agent_address_str[] = $row['k_s_name'] . ' ' . $row['k_shn_house_num'];
                $this->agent_site[] = $row['k_ua_site'];
                $this->agent_description[] = $row['k_ua_description'];
                $this->agent_end_date[] = $row['k_ua_last_date'];
                $this->agent_end_days[] = round((strtotime($row['k_ua_last_date']) - time()) / 86400, 0);
            }
            $queue2 = $mysql->prepare('SELECT * FROM k_users_agents_register');
            $queue2->execute();
            $result2 = $queue2->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result2 as $row) {
                $this->agent_in_top[] = $row['k_uar_user_id'];
            }
        } catch (PDOException $e) {
            exit();
        }
    }

    function AllAddresses($ids) {
        $address = array();
        $id = array();
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare("SELECT k_shn_id,k_s_name,k_shn_house_num
                FROM k_streets_house_nums AS kshn
                LEFT JOIN k_streets AS ks ON (kshn.k_shn_street_id = ks.k_s_id)
                ORDER BY k_s_name,k_shn_house_num ASC");
            $queue->execute();
            $result = $queue->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $value) {
                if (preg_match('/(###)/', $value['k_s_name'])) {
                    $street = explode('###', $value['k_s_name']);
                    $house = explode('###', $value['k_shn_house_num']);
                    array_push($id, $value['k_shn_id']);
                    array_push($address, $street[0] . ' ' . $house[0] . ' / ' . $street[1] . ' ' . $house[1]);
                } else {
                    array_push($id, $value['k_shn_id']);
                    array_push($address, $value['k_s_name'] . ' ' . $value['k_shn_house_num']);
                }
            }
            for ($i = 0; $i < count($id); $i++) {
                if ($ids == $id[$i]) {
                    $prnt .= '<option selected value="' . $id[$i] . '">' . $address[$i] . '</option>';
                } else {
                    $prnt .= '<option value="' . $id[$i] . '">' . $address[$i] . '</option>';
                }
            }
        } catch (PDOException $e) {
            exit();
        }
        return $prnt;
    }

    function GenerateTable() {
        for ($n = 0; $n < count($this->agent_id); $n++) {
            echo '<tr style="background: #f0e4f4;" id="AgentRowID_' . $this->agent_id[$n] . '">
                <td style="width: 20px;"><input id="CheckedAgents_' . $n . '" type="checkbox" value="' . $this->agent_id[$n] . '"></td>
                <td style="width: 80px;"><p class="style_4">' . $this->agent_id[$n] . '</p></td>
                <td style="width: 80px;"><img id="AgentTableImage_' . $this->agent_id[$n] . '" class="img_ob" src="../' . $this->agent_avatar[$n] . '"></td>
                <td style="width: 160px;"><p class="style_4">' . $this->agent_name[$n] . '</p></td>
                <td><p class="style_4">' . $this->agent_login[$n] . '</p></td>
                <td><p class="style_4">' . $this->agent_autor_date[$n] . '</p></td>';
            if ($this->agent_state[$n] == 1) {
                echo '<td><p class="style_4_1" id="AgentStateInTable_' . $this->agent_id[$n] . '">Активно</p></td>';
            } else {
                echo '<td><p class="style_4_2" id="AgentStateInTable_' . $this->agent_id[$n] . '">Скрыто</p></td>';
            }
            echo '<td><p class="style_4_3">' . $this->agent_end_days[$n] . ' дней</p></td>
                <td>
                <a class="a_1" onclick="ImmoEmail(\'' . $this->agent_email[$n] . '\');"><img src="../images/send_email.png" title="Отправить E-mail" alt=""></a>
                <a class="a_1" onclick="AgentsInfo(' . $this->agent_id[$n] . ');" title="Информация по Агентству"><img src="../images/info.png" alt=""></a>
                <a class="a_1" onclick="AgentEdit(' . $this->agent_id[$n] . ');"><img src="../images/edit.png" title="Редактировать Агентство" alt=""></a>
                <a class="a_1" onclick="AgentAvatarLoad(' . $this->agent_id[$n] . ');"><img src="../images/photo.png" title="Редактировать аватарку Агентства" alt=""></a>
                <a class="a_1" onclick="ChangePasswordWindow(' . $this->agent_id[$n] . ');"><img src="../images/pass.png" title="Изменить пароль для входа" alt=""></a>';
            if (!in_array($this->agent_id[$n], $this->agent_in_top)) {
                echo ' <a class="a_1"><img src="../images/up.png" id="AgentRegister_' . $this->agent_id[$n] . '" onclick="AgentInRegister(' . $this->agent_id[$n] . ',1);" title="Поднять Агентство" alt=""></a>';
            } else {
                echo ' <a class="a_1"><img src="../images/down.png" id="AgentRegister_' . $this->agent_id[$n] . '" onclick="AgentInRegister(' . $this->agent_id[$n] . ',2);" title="Отменить поднятие" alt=""></a>';
            }
            if ($this->agent_state[$n] == 1) {
                echo ' <a class="a_1"><img src="../images/disable_1.png" onclick="DisEnAgent(' . $this->agent_id[$n] . ',0);" title="Скрыть Агентство" id="AgentIDState_' . $this->agent_id[$n] . '" alt=""></a>';
            } else {
                echo ' <a class="a_1"><img src="../images/enable.png" onclick="DisEnAgent(' . $this->agent_id[$n] . ',1);" title="Активировать Агентство" id="AgentIDState_' . $this->agent_id[$n] . '" alt=""></a>';
            }
            echo ' <a class="a_1"><img src="../images/block_ip.png" onclick="BlockIP(\'' . $this->agent_ip[$n] . '\');" title="Блокировать по IP" alt=""></a>';
            echo ' <a class="a_1"><img src="../images/delete.png" onclick="DeleteAgent(' . $this->agent_id[$n] . ');" title="Удалить Агентство" alt=""></a></td></tr>';
        }
    }

    function GenerateAvatarTable($id) {
        $id_array = array_search($id, $this->agent_id);
        echo '<tr style="background: #f0f4f4;">
            <td style="width: 80px;"><img class="img_ob" id="AgentAvatarShow" src="../' . $this->agent_avatar[$id_array] . '"></td>
            <td style="width: 150px;"><a class="a_1"><img src="../images/delete.png" title="Удалить фото" onclick="DeleteAgentAvatar();" alt=""></a></td>
        </tr>';
    }

    function GenerateEditTable() {
        echo '<tr>
                <td><p class="style_2">№ Агентства:</p></td>
                <td><p class="style_4_2">' . $this->agent_id[0] . '</p></td>
            </tr>
            <tr>
                <td><p class="style_2">Название Агентства:</p></td>
                <td><input type="text" id="AgentEditName" value="' . $this->agent_name[0] . '"></td>
            </tr>
            <tr>
                <td><p class="style_2">Телефон:</p></td>
                <td><input type="text" id="AgentEditPhone" value="' . $this->agent_phone[0] . '"></td>
            </tr>
            <tr>
                <td><p class="style_2">Выберите из списка:</p></td>
                <td id="AgentAddressResult">
                <select id="ImmoAddressChosen" style="width: 100%;" name="ImmoAddressChosen">';
        echo $this->AllAddresses($this->agent_address[0]);
        echo '</select>
                </td>
            </tr>
            <tr>
                <td><p class="style_2">Имя:</p></td>
                <td><input type="text" id="AgentEditFName" value="' . $this->agent_fname[0] . '"></td>
            </tr>
            <tr>
                <td><p class="style_2">Фамилия:</p></td>
                <td><input type="text" id="AgentEditLName" value="' . $this->agent_lname[0] . '"></td>
            </tr>
            <tr>
                <td><p class="style_2">Отчество:</p></td>
                <td><input type="text" id="AgentEditOName" value="' . $this->agent_oname[0] . '"></td>
            </tr>
            <tr>
                <td><p class="style_2">E-mail:</p></td>
                <td><input type="text" id="AgentEditEmail" value="' . $this->agent_email[0] . '"></td>
            </tr>
            <tr>
                <td><p class="style_2">Сайт Агентства:</p></td>
                <td><input type="text" id="AgentEditSite" value="' . $this->agent_site[0] . '"></td>
            </tr>
            <tr>
                <td colspan="2">
                    <p class="style_2">Описание Агентства:</p>
                    <textarea rows="12" cols="55" id="AgentEditDescr" name="text">' . $this->agent_description[0] . '</textarea>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <button style="float:left;" onclick="AgentEditSubmit(' . $this->agent_id[0] . ');">Изменить</button>
                </td>
            </tr>';
    }

    function GenerateInfoTable() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare('SELECT count(*) AS ads_c FROM k_immovables_sell WHERE k_isf_user_id=:id LIMIT 1');
            $queue->execute(array(":id" => $this->agent_id[0]));
            $row = $queue->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit();
        }
        if ($this->agent_state[0] == 1) {
            $state = 'Активно';
        } else {
            $state = 'Скрыто';
        }
        echo '<tr>
          <td><p class="style_2">№ Агентства:</p></td>
          <td><p class="style_4_2">' . $this->agent_id[0] . '</p></td>
          </tr>
          <tr>
          <td><p class="style_2">Объявлений у Агентства:</p></td>
          <td><p class="style_4_4">' . $row['ads_c'] . '</p></td>
          </tr>
          <tr>
          <td><p class="style_2">Название Агентства:</p></td>
          <td><p class="style_4_4">' . $this->agent_name[0] . '</p></td>
          </tr>
          <tr>
          <td><p class="style_2">Ник пользователя:</p></td>
          <td><p class="style_4_4">' . $this->agent_login[0] . '</p></td>
          </tr>
          <tr>
          <td><p class="style_2">Дата размещения:</p></td>
          <td><p class="style_4_4">' . $this->agent_autor_date[0] . '</p></td>
          </tr>
          <tr>
          <td><p class="style_2">Статус:</p></td>
          <td><p class="style_4_4">' . $state . '</p></td>
          </tr>
          <tr>
          <td><p class="style_2">Время действия Агентства:</p></td>
          <td><p class="style_4_4">' . $this->agent_end_days[0] . ' дней</p></td>
          </tr>
          <tr>
          <td><p class="style_2">Контактное лицо:</p></td>
          <td><p class="style_4_1">' . $this->agent_fname[0] . ' ' . $this->agent_lname[0] . ' ' . $this->agent_oname[0] . '</p></td>
          </tr>
          <tr>
          <td><p class="style_2">Телефон:</p></td>
          <td><p class="style_4_4">' . $this->agent_phone[0] . '</p></td>
          </tr>
          <tr>
          <td><p class="style_2">Адрес:</p></td>
          <td><p class="style_4_4">' . $this->agent_address_str[0] . '</p></td>
          </tr>
          <tr>
          <td><p class="style_2">E-mail:</p></td>
          <td><p class="style_4_4">' . $this->agent_email[0] . '</p></td>
          </tr>
          <tr>
          <td><p class="style_2">Сайт Агентства:</p></td>
          <td><p class="style_4_4"><a href="' . $this->agent_site[0] . '">' . $this->agent_site[0] . '</a></p></td>
          </tr>';
    }

    function GenerateNavigation($page) {
        $page = filter_var($page, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $queue = $mysql->prepare('SELECT count(k_ua_id) AS max FROM k_users_agents');
            $queue->execute();
            $row = $queue->fetch(PDO::FETCH_ASSOC);
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
                echo '<a class="style_listing" href="./?PageIndex=' . ($page - 1) . '">Предыдущая</a>';
            }

            if ($pages <= 11) {
                for ($i = 1; $i <= $pages; $i++) {
                    if ($i == $page) {
                        echo '<a class="active_listing" href="./?PageIndex=' . $i . '">' . $i . '</a>';
                    } else {
                        echo '<a class="style_listing" href="./?PageIndex=' . $i . '">' . $i . '</a>';
                    }
                }
            } else {
                if ($page <= 6) {
                    for ($i = 1; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageIndex=' . $i . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageIndex=' . $i . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageIndex=' . $i . '">' . $i . '</a>';
                    }
                }
                if ($page > 6 && $page <= ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageIndex=' . $i . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $page - 2; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageIndex=' . $i . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageIndex=' . $i . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="./?PageIndex=' . $i . '">' . $i . '</a>';
                    }
                }
                if ($page > ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="./?PageIndex=' . $i . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 5; $i <= $pages; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="./?PageIndex=' . $i . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="./?PageIndex=' . $i . '">' . $i . '</a>';
                        }
                    }
                }
            }

            if ($page == 1 || $page < $pages) {
                echo '<a class="style_listing" href="./?PageIndex=' . ($page + 1) . '">Следующая</a>';
            }

            echo '</div>';
        }
    }

}

class Buys {

    public $buy_id = array();
    public $buy_user = array();
    public $buy_user_str = array();
    public $buy_date = array();
    public $buy_text = array();
    public $buy_phone = array();
    public $buy_subcategory = array();

    function LoadBuys($limit, $page, $where) {
        $query = 'SELECT kib.*, ku.k_ku_login FROM k_immovables_buy as kib
            LEFT JOIN k_users as ku ON (kib.k_ib_user_id = ku.k_ku_id) ' . $where;
        $query .= ' ORDER BY k_ib_date DESC';
        if ($limit != 0) {
            $query .= ' LIMIT ' . (($page - 1) * $limit) . ',' . $limit;
        }
        $result = mysql_query($query);
        $n = 0;
        while ($row = mysql_fetch_array($result)) {
            $this->buy_id[$n] = $row['k_ib_id'];
            $this->buy_user[$n] = $row['k_ib_user_id'];
            if ($row['k_ib_user_id'] != 0) {
                $this->buy_user_str[$n] = $row['k_ku_login'];
            } else {
                $this->buy_user_str[$n] = 'Гость';
            }
            $this->buy_date[$n] = $row['k_ib_date'];
            $this->buy_text[$n] = $row['k_ib_text'];
            $this->buy_phone[$n] = $row['k_ib_phone'];
            $this->buy_subcategory[$n] = $row['k_ib_subcategory'];
            $n++;
        }
    }

    function GenerateTable() {
        for ($i = 0; $i < count($this->buy_id); $i++) {
            echo '<tr id="RowBuys_' . $this->buy_id[$i] . '" style="background: #f5dac7;">
            <td style="width: 20px;"><input id="CheckBuys_' . $i . '" type="checkbox" value="' . $this->buy_id[$i] . '"></td>
            <td style="width: 80px;"><p class="style_4">' . $this->buy_id[$i] . '</p></td>
            <td><p class="style_4">' . $this->buy_user_str[$i] . '</p></td>
            <td><p class="style_4">' . implode(array_slice(explode('<br>', wordwrap($this->buy_text[$i], 200, '<br>', false)), 0, 1)) . '...</p></td>
            <td><p class="style_4">' . $this->buy_date[$i] . '</p></td>
            <td>
                <a class="a_1" onclick="ShowBuys(' . $this->buy_id[$i] . ');"><img src="../images/edit.png" title="Редактировать объявление" alt=""></a>
                <a class="a_1"><img src="../images/delete.png" onclick="DeleteBuysSubmit(' . $this->buy_id[$i] . ');" title="Удалить объявление" alt=""></a>
            </td>
        </tr>';
        }
    }

    function GenerateEdit($id) {
        $id_array = array_search($id, $this->buy_id);
        echo '<tr><td>
            <span>Текст объявления:</span><br>
            <textarea rows="10" cols="50" id="BuysTextEdit" name="text">' . $this->buy_text[$id_array] . '</textarea>
            </td></tr>
            <tr>
            <td><button onclick="EditBuysSubmit(' . $this->buy_id[$id_array] . ');" style="float:left;">Изменить</button></td>
            </tr>';
    }

    function GenerateNavigation($page) {
        $query = 'SELECT count(k_ib_id) as max FROM k_immovables_buy';
        $result = mysql_query($query);
        $row = mysql_fetch_assoc($result);
        $pages = intval($row['max'] / 50);
        if ($row['max'] % 50 != 0) {
            $pages++;
        }
        if ($pages > 1) {
            echo '<div class="listing">';
            if ($page == $pages || $page > 1) {
                echo '<a class="style_listing" href="../realty/?PageIndex=' . ($page - 1) . '">Предыдущая</a>';
            }

            if ($pages <= 11) {
                for ($i = 1; $i <= $pages; $i++) {
                    if ($i == $page) {
                        echo '<a class="active_listing" href="../realty/?PageIndex=' . $i . '">' . $i . '</a>';
                    } else {
                        echo '<a class="style_listing" href="../realty/?PageIndex=' . $i . '">' . $i . '</a>';
                    }
                }
            } else {
                if ($page <= 6) {
                    for ($i = 1; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="../realty/?PageIndex=' . $i . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="../realty/?PageIndex=' . $i . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="../realty/?PageIndex=' . $i . '">' . $i . '</a>';
                    }
                }
                if ($page > 6 && $page <= ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="../realty/?PageIndex=' . $i . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $page - 2; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="../realty/?PageIndex=' . $i . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="../realty/?PageIndex=' . $i . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="../realty/?PageIndex=' . $i . '">' . $i . '</a>';
                    }
                }
                if ($page > ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="../realty/?PageIndex=' . $i . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 5; $i <= $pages; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="../realty/?PageIndex=' . $i . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="../realty/?PageIndex=' . $i . '">' . $i . '</a>';
                        }
                    }
                }
            }

            if ($page == 1 || $page < $pages) {
                echo '<a class="style_listing" href="../realty/?PageIndex=' . ($page + 1) . '">Следующая</a>';
            }

            echo '</div>';
        }
    }

}

class News {

    public $news_id = array();
    public $news_image = array();
    public $news_header = array();
    public $news_subcategory = array();
    public $news_subcategory_string = array();
    public $news_date = array();
    public $news_text = array();

    function LoadNews($limit, $page, $where) {
        $query = 'SELECT kisn.*, kis.k_is_name FROM k_immovables_subcategories_news as kisn
            LEFT JOIN k_immovables_subcategories as kis ON (kis.k_is_id = kisn.k_isn_parent)
            WHERE kis.k_is_parent=4 ' . $where;
        $query .= ' ORDER BY k_isn_date DESC';
        if ($limit != 0) {
            $query .= ' LIMIT ' . (($page - 1) * $limit) . ',' . $limit;
        }
        $result = mysql_query($query);
        $n = 0;
        while ($row = mysql_fetch_array($result)) {
            $this->news_id[$n] = $row['k_isn_id'];
            $this->news_image[$n] = $row['k_isn_image'];
            $this->news_header[$n] = $row['k_isn_header'];
            $this->news_subcategory[$n] = $row['k_isn_parent'];
            $this->news_subcategory_string[$n] = $row['k_is_name'];
            $this->news_date[$n] = $row['k_isn_date'];
            $this->news_text[$n] = $row['k_isn_text'];
            $n++;
        }
    }

    function GenerateTable() {
        for ($i = 0; $i < count($this->news_id); $i++) {
            echo '<tr id="NewsRow_' . $this->news_id[$i] . '" style="background: #f5dac7;">
                <td style="width: 20px;"><input type="checkbox" id="NewsCheck_' . $i . '" value="' . $this->news_id[$i] . '"></td>
                <td style="width: 80px;"><p class="style_4">' . $this->news_id[$i] . '</p></td>
                <td style="width: 80px;"><img id="NewsImage_' . $this->news_id[$i] . '" class="img_ob" src="../' . $this->news_image[$i] . '"></td>
                <td style="width: 160px;"><p id="NewsHeaderTable_' . $this->news_id[$i] . '" class="style_4">' . $this->news_header[$i] . '</p></td>
                <td><p id="NewsSubcategoryTable_' . $this->news_id[$i] . '" class="style_4">' . $this->news_subcategory_string[$i] . '</p></td>
                <td><p class="style_4">' . $this->news_date[$i] . '</p></td>
                <td>
                <a class="a_1" onclick="ShowNews(' . $this->news_id[$i] . ');" title="Посмотреть новость"><img src="../images/info.png" alt=""></a>
                <a class="a_1" onclick="EditNews(' . $this->news_id[$i] . ');"><img src="../images/edit.png" title="Редактировать Новость" alt=""></a>
                <a class="a_1" onclick="NewsEditAvatar(' . $this->news_id[$i] . ');"><img src="../images/photo.png" title="Редактировать аватарку Новости" alt=""></a>
                <a class="a_1"><img src="../images/delete.png" onclick="DeleteNewsSubmit(' . $this->news_id[$i] . ');" title="Удалить Новость" alt=""></a>
                </td></tr>';
        }
    }

    function GenerateNewsShow($id) {
        $id_array = array_search($id, $this->news_id);
        echo '<tr>
            <td><p class="style_2">№ Новости:</p></td>
            <td><p class="style_4_2">' . $this->news_id[$id_array] . '</p></td>
            </tr>
            <tr>
            <td><p class="style_2">Заголовок Новости:</p></td>
            <td><p class="style_4_4">' . $this->news_header[$id_array] . '</p></td>
            </tr>
            <tr>
            <td><p class="style_2">Текст Новости:</p></td>
            <td><p class="style_4_4">' . $this->news_text[$id_array] . '</p></td>
            </tr>
            <tr>
            <td><p class="style_2">Рубрика:</p></td>
            <td><p class="style_4_4">' . $this->news_subcategory_string[$id_array] . '</p></td>
            </tr>
            <tr>
            <td><p class="style_2">Дата размещения:</p></td>
            <td><p class="style_4_4">' . $this->news_date[$id_array] . '</p></td>
            </tr>';
    }

    function GenerateEditTable($id) {
        $id_array = array_search($id, $this->news_id);
        echo '<tr>
          <td><p class="style_2">№ Новости:</p></td>
          <td><p class="style_4_2">' . $this->news_id[$id_array] . '</p>
          <input type="hidden" id="NewsIDEditSub" value="' . $this->news_id[$id_array] . '"></td>
          </tr>
          <tr>
          <td><p class="style_2">Заголовок Новости:</p></td>
          <td><input style="width: 100%;" type="text" id="NewsHeaderEdit" value="' . $this->news_header[$id_array] . '"></td>
          </tr>
          <tr>
          <td><p class="style_2">Текст Новости:</p></td>
          <td><textarea style="resize: none;" rows="12" id="NewsTextEdit" cols="55">' . $this->news_text[$id_array] . '</textarea></td>
          </tr>
          <tr>
          <td><p class="style_2">Рубрика:</p></td>
          <td>';
        echo '<select id="NewsSubcategoriesEdit" style="width: 100%;">';
        $query = 'SELECT * FROM k_immovables_subcategories WHERE k_is_parent=4 ORDER BY k_is_name ASC';
        $result = mysql_query($query);
        $before = '';
        while ($row = mysql_fetch_array($result)) {
            $before .= '<option value="' . $row['k_is_id'] . '">' . $row['k_is_name'] . '</option>';
        }
        echo str_replace('value="' . $this->news_subcategory[$id_array] . '"', 'value="' . $this->news_subcategory[$id_array] . '" selected', $before);
        echo '</select>';
        echo '</td>
          </tr>
          <tr>
          <td colspan="2"><button onclick="NewsEditSubmit();" style="float:left; width: 100%;">Изменить</button></td>
          </tr>';
    }

    function GenerateAvatarEdit($id) {
        $id_array = array_search($id, $this->news_id);
        echo '<tr style="background: #f0f4f4;">
          <td style="width: 80px;"><img class="img_ob" id="NewsUploadImage" src="../' . $this->news_image[$id_array] . '"></td>
          <td style="width: 150px;"><a class="a_1"><img onclick="DeleteNewsAvatar();" src="../images/delete.png" title="Удалить фото" alt=""></a>
          <input type="hidden" id="AvatarNewsIDCh" value="' . $this->news_id[$id_array] . '"></td>
          </tr>';
    }

    function GenerateNavigation($page) {
        $query = 'SELECT count(k_isn_id) as max FROM k_immovables_subcategories_news';
        $result = mysql_query($query);
        $row = mysql_fetch_assoc($result);
        $pages = intval($row['max'] / 50);
        if ($row['max'] % 50 != 0) {
            $pages++;
        }
        if ($pages > 1) {
            echo '<div class="listing">';
            if ($page == $pages || $page > 1) {
                echo '<a class="style_listing" href="../realty/?PageIndex=' . ($page - 1) . '">Предыдущая</a>';
            }

            if ($pages <= 11) {
                for ($i = 1; $i <= $pages; $i++) {
                    if ($i == $page) {
                        echo '<a class="active_listing" href="../realty/?PageIndex=' . $i . '">' . $i . '</a>';
                    } else {
                        echo '<a class="style_listing" href="../realty/?PageIndex=' . $i . '">' . $i . '</a>';
                    }
                }
            } else {
                if ($page <= 6) {
                    for ($i = 1; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="../realty/?PageIndex=' . $i . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="../realty/?PageIndex=' . $i . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="../realty/?PageIndex=' . $i . '">' . $i . '</a>';
                    }
                }
                if ($page > 6 && $page <= ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="../realty/?PageIndex=' . $i . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $page - 2; $i <= $page + 2; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="../realty/?PageIndex=' . $i . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="../realty/?PageIndex=' . $i . '">' . $i . '</a>';
                        }
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 2; $i <= $pages; $i++) {
                        echo '<a class="style_listing" href="../realty/?PageIndex=' . $i . '">' . $i . '</a>';
                    }
                }
                if ($page > ($pages - 6)) {
                    for ($i = 1; $i < 4; $i++) {
                        echo '<a class="style_listing" href="../realty/?PageIndex=' . $i . '">' . $i . '</a>';
                    }
                    echo '<span style="color: #5370CE; font-size: 80%; font-weight: bold; padding: 5px; position: relative;top: 10px;">...</span>';
                    for ($i = $pages - 5; $i <= $pages; $i++) {
                        if ($i == $page) {
                            echo '<a class="active_listing" href="../realty/?PageIndex=' . $i . '">' . $i . '</a>';
                        } else {
                            echo '<a class="style_listing" href="../realty/?PageIndex=' . $i . '">' . $i . '</a>';
                        }
                    }
                }
            }

            if ($page == 1 || $page < $pages) {
                echo '<a class="style_listing" href="../realty/?PageIndex=' . ($page + 1) . '">Следующая</a>';
            }

            echo '</div>';
        }
    }

}

class VideoRealty {

    public $id = array();
    public $immo = array();
    public $url = array();
    public $state = array();

    /**
     * Загрузить объявления
     * @param int $limit Сколько объявлений нужно выгрузить
     * @param int $page Номер страницы
     * @param String $where WHERE в SQL запросе
     */
    function VideoRealty($limit, $page, $where) {
        $query = "SELECT * FROM k_immovables_photos WHERE k_ip_url LIKE '%video%' ORDER BY k_ip_id DESC ";
        if ($limit != 0) {
            $query .= ' LIMIT ' . (($page - 1) * $limit) . ',' . $limit;
        }
        $result = mysql_query($query);
        while ($row = mysql_fetch_array($result)) {
            $this->id[] = $row['k_ip_id'];
            $this->immo[] = $row['k_ip_immo_id'];
            $this->url[] = str_replace('jpg', 'flv', $row['k_ip_url']);
            $this->state[] = $row['k_ip_state'];
        }
    }
    
    function GenerateNavigation($page, $limit) {
        $query = "SELECT count(*) as max FROM k_immovables_photos WHERE k_ip_url LIKE '%video%'";
        $result = mysql_query($query);
        $row = mysql_fetch_assoc($result);
        $pages = intval($row['max'] / $limit);
        if ($row['max'] % $limit != 0) {
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

?>