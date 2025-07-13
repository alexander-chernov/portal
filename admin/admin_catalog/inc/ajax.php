<?php

include '../../inc/configs.php';
include 'classes.php';

if (isset($_POST['ShowInfoOrganization'])) {
    $_POST['ShowInfoOrganization'] = filter_var($_POST['ShowInfoOrganization'], FILTER_VALIDATE_INT);
    $org_1 = new Organizations(0, '', 50, 1);
    $org_1->LoadOne($_POST['ShowInfoOrganization']);
    $org_2 = new OrganizationAddresses($_POST['ShowInfoOrganization']);
    echo '<a class="close" onclick="CloseWindow(\'info_company\');">X</a>
        <br><br>
        <p class="style_7">Информация по организации</p>
        <table style="text-align: left;">
        <tr>
        <td><p class="style_2">Наименование:</p></td>
        <td><p class="style_9_1">' . $org_1->name[0] . '</p></td>
        </tr>
        <tr>
        <td><p class="style_2">Сайт:</p></td>
        <td><p class="style_4_4"><a href="' . $org_1->site[0] . '">' . $org_1->site[0] . '</a></p></td>
        </tr>
        <tr>
        <td><p class="style_2">E-mail:</p></td>
        <td><p class="style_4_4">' . $org_1->email[0] . '</p></td>
        </tr>
        </table>';
    for ($i = 0; $i < count($org_2->id); $i++) {
        echo '<table class="adres_table">
      <tr>
      <td><span>Адрес:</span></td>
      <td><a class="style_10">' . $org_2->address_str[$i] . '</a></td>
      </tr>
      <tr>
      <td><span>Телефон:</span></td>
      <td><a class="style_10_1" style="padding: 0;">';
        for ($n = 0; $n < count($org_2->phones); $n++) {
            if ($org_2->phones_numb[$n][0] == $org_2->id[$i]) {
                switch ($org_2->phones_types[$n][1]) {
                    case 1: echo 'телефон: ';
                        break;
                    case 2: echo 'факс: ';
                        break;
                    case 3: echo 'единая служба: ';
                        break;
                }
                echo $org_2->phones_numb[$n][1] . '<br>';
            }
        }
        echo '</a></td>
      </tr>
      <tr>
      <td><span>Рабочие дни и<br>время работы:</span></td>
      <td><a class="style_10_1" style="padding: 0;">';
        for ($n = 0; $n < count($org_2->days); $n++) {
            if ($org_2->days[$n][0] == $org_2->id[$i]) {
                if ($org_2->days[$n][2] == 1) {
                    echo $org_2->days[$n][1] . ' : ' . $org_2->hours_s[$n][1] . ' - ' . $org_2->hours_e[$n][1];
                    if ($org_2->hours_b_s[$n][1] == '00:00:00' && $org_2->hours_b_e[$n][1] == '00:00:00') {
                        echo ' / <span style="color: red;">без перерыва</span>';
                    } else {
                        echo ' / <span style="color: red;">перерыв ' . $org_2->hours_b_s[$n][1] . ' - ' . $org_2->hours_b_e[$n][1] . '</span>';
                    }
                } else {
                    echo '<span style="color: red;">' . $org_2->days[$n][1] . ' : Выходной</span>';
                }

                echo '<br>';
            }
        }
        echo '</a></td>
      </tr>
      </table>';
    }
}

if (isset($_POST['ChangeOrganization'])) {
    $_POST['ChangeOrganization'] = filter_var($_POST['ChangeOrganization'], FILTER_VALIDATE_INT);
    $org_1 = new Organizations(0, '', 50, 1);
    $org_1->LoadOne($_POST['ChangeOrganization']);
    $org_2 = new OrganizationAddresses($_POST['ChangeOrganization']);
    $c = new CatalogCategories();
    $bs = new CatalogSubCategories();
    $s = new SubSubcategories(0, '');
    $s->OneBigSS($org_1->big_sub[0]);
    echo '<a class="close" onclick="CloseWindow(\'edit_company\');">X</a>
        <br><br>
        <p id="OrganizationChange" class="style_7">Редактирование организации в каталоге</p>';
    echo '<table class="adres_table" style="text-align: left;">
        <tr><td><p class="style_2">Наименование организации:</p></td>
        <td><input id="org_p_name" style="width: 300px;" type="text" value="' . $org_1->name[0] . '"></td></tr>
        <tr><td><p class="style_2">Сайт организации:</p></td>
        <td><input id="org_p_site" style="width: 300px;" type="text" value="' . $org_1->site[0] . '"></td></tr>
        <tr><td><p class="style_2">E-mail организации:</p></td>
        <td><input id="org_p_email" style="width: 300px;" type="text" value="' . $org_1->email[0] . '"></td></tr>
        <tr><td><p class="style_2">Описание организации:</p></td>
        <td><textarea id="org_p_descr" style="width: 300px;" rows="5" cols="10">' . $org_1->descr[0] . '</textarea></td></tr>
        <tr><td><a class="a_1" onclick="SaveAllOrgParams(' . $_POST['ChangeOrganization'] . ');"><img src="../images/enable.png" title="Сохранить изменения" alt=""></a></td></tr></table>';
    for ($i = 0; $i < count($org_1->sub_name[$_POST['ChangeOrganization']]); $i++) {
        if (!empty($org_1->sub_name[$_POST['ChangeOrganization']][$i])) {
            echo '<table class="adres_table" id="cat_block_' . $org_1->firm_to_parent[$_POST['ChangeOrganization']][$i] . '" style="text-align: left;">
        <tr><td><p class="style_2">Каталог:</p></td>
        <td id="cat_td_' . $org_1->firm_to_parent[$_POST['ChangeOrganization']][$i] . '">' . $org_1->category_name[$_POST['ChangeOrganization']][$i] . '</td></tr>
        <tr><td><p class="style_2">Рубрика:</p></td>
        <td id="big_sub_td_' . $org_1->firm_to_parent[$_POST['ChangeOrganization']][$i] . '">' . $org_1->big_sub_name[$_POST['ChangeOrganization']][$i] . '</td></tr>
        <tr><td><p class="style_2">Подрубрика:</p></td>
        <td id="sub_td_' . $org_1->firm_to_parent[$_POST['ChangeOrganization']][$i] . '">' . $org_1->sub_name[$_POST['ChangeOrganization']][$i] . '</td></tr>
        <tr><td>
        <a class="a_1" id="SaveGal_' . $org_1->firm_to_parent[$_POST['ChangeOrganization']][$i] . '" style="visibility: hidden;" onclick="SaveChangedCategory(' . $org_1->firm_to_parent[$_POST['ChangeOrganization']][$i] . ',' . $_POST['ChangeOrganization'] . ');"><img src="../images/enable.png" title="Сохранить изменения" alt=""></a>
        <a class="a_1" onclick="OpenCategoryForm(' . $org_1->firm_to_parent[$_POST['ChangeOrganization']][$i] . ');"><img src="../images/edit.png" title="Редактировать каталог" alt=""></a>
        <a class="a_1" onclick="DeleteFromCategory(' . $org_1->firm_to_parent[$_POST['ChangeOrganization']][$i] . ');"><img src="../images/delete.png" title="Удалить каталог" alt=""></a>
        </td><td colspan="2"></td></tr></table>';
        }
    }
    echo '<a class="a_1" onclick="CreateNewCategorySelect(' . $_POST['ChangeOrganization'] . ');"><img src="../images/add_team.png" title="Добавить в каталог" alt=""></a>';
    for ($i = 0; $i < count($org_2->id); $i++) {
        echo '<table class="adres_table" id="ATB_id_' . $org_2->id[$i] . '">
      <tr>
      <td><span>Адрес:</span></td>
      <td><a class="style_10">' . $org_2->address_str[$i];
        if ($org_2->address_advanced[$i]) {
            echo '<br>' . $org_2->address_advanced[$i];
        }
        echo '</a></td>
      </tr>
      <tr>
      <td><span>Телефон:</span></td>
      <td><a class="style_10_1" style="padding: 0;">';
        for ($n = 0; $n < count($org_2->phones); $n++) {
            if ($org_2->phones_numb[$n][0] == $org_2->id[$i]) {
                switch ($org_2->phones_types[$n][1]) {
                    case 1: echo 'телефон: ';
                        break;
                    case 2: echo 'факс: ';
                        break;
                    case 3: echo 'единая служба: ';
                        break;
                }
                echo $org_2->phones_numb[$n][1] . '<br>';
            }
        }
        echo '</a></td>
      </tr>
      <tr>
      <td><span>Рабочие дни и<br>время работы:</span></td>
      <td><a class="style_10_1" style="padding: 0;">';
        for ($n = 0; $n < count($org_2->days); $n++) {
            if ($org_2->days[$n][0] == $org_2->id[$i]) {
                if ($org_2->days[$n][2] == 1) {
                    echo $org_2->days[$n][1] . ' : ' . $org_2->hours_s[$n][1] . ' - ' . $org_2->hours_e[$n][1];
                    if ($org_2->hours_b_s[$n][1] == '00:00:00' && $org_2->hours_b_e[$n][1] == '00:00:00') {
                        echo ' / <span style="color: red;">без перерыва</span>';
                    } else {
                        echo ' / <span style="color: red;">перерыв ' . $org_2->hours_b_s[$n][1] . ' - ' . $org_2->hours_b_e[$n][1] . '</span>';
                    }
                } else {
                    echo '<span style="color: red;">' . $org_2->days[$n][1] . ' : Выходной</span>';
                }

                echo '<br>';
            }
        }
        echo '</a></td>
      </tr>
      <tr><td colspan="2">
      <a class="a_1" onclick="ChangeAddress(' . $org_2->address[$i] . ',' . $org_2->id[$i] . ',' . $_POST['ChangeOrganization'] . ');"><img src="../images/edit.png" title="Редактировать Адрес" alt=""></a>
      <a class="a_1" onclick="DeleteAddressFromOrganization(' . $org_2->id[$i] . ');"><img src="../images/delete.png" title="Удалить Адрес" alt=""></a>
      </td></tr>
      </table>';
    }
}

if (isset($_POST['ChangeAddress'])) {
    $_POST['ChangeAddress'] = filter_var($_POST['ChangeAddress'], FILTER_VALIDATE_INT);
    $_POST['ChangeAddressID'] = filter_var($_POST['ChangeAddressID'], FILTER_VALIDATE_INT);
    $_POST['ChangeAddressDop'] = filter_var($_POST['ChangeAddressDop'], FILTER_VALIDATE_INT);
    $org_1 = new Organizations(0, '', 50, 1);
    $org_1->LoadOne($_POST['ChangeAddress']);
    $org_2 = new OrganizationAddresses(0);
    $org_2->LoadOne($_POST['ChangeAddressID']);
    $a = new AllAddresses();
    echo '<a class="close" onclick="$(\'#red_adres\').slideUp(500);">X</a>
        <br><br>
        <p class="style_7">Редактирование адреса организации</p>
        <table class="adres_table">
        <tr><td><span>Адрес:</span></td>
        <td>
        <select id="toac">';
    for ($i = 0; $i < count($a->id); $i++) {
        if ($_POST['ChangeAddress'] == $a->id[$i]) {
            echo '<option selected value="' . $a->id[$i] . '">' . $a->address[$i] . '</option>';
        } else {
            echo '<option value="' . $a->id[$i] . '">' . $a->address[$i] . '</option>';
        }
    }
    echo '</select>
        </td>
        <td>
        <a class="a_1" onclick="SaveNewAddressCH(' . $_POST['ChangeAddressID'] . ',' . $_POST['ChangeAddressDop'] . ');"><img src="../images/enable.png" title="Сохранить" alt="' . $_POST['ChangeAddressID'] . '"></a>
        </td>
        </tr>
        <tr>
        <td>
        Доп. поле
        </td>
        <td>
        <input style="width: 80%;" type="text" value="' . $org_2->address_advanced[0] . '" id="toaca">
        </td>
        <td>
        <a class="a_1" onclick="SaveNewAddressAdvCH(' . $_POST['ChangeAddressID'] . ',' . $_POST['ChangeAddressDop'] . ');"><img src="../images/enable.png" title="Сохранить" alt="' . $_POST['ChangeAddressID'] . '"></a>
        </td>
        </tr>
        </table>';
    echo '<table class="adres_table" id="phones_table">';
    for ($i = 0; $i < count($org_2->phones); $i++) {
        if ($org_2->phones[$i][0] == $_POST['ChangeAddressID'] && !empty($org_2->phones[$i][1])) {
            echo '<tr id="phone_line_' . $org_2->phones[$i][1] . '"><td><select onchange="ChangePhoneType(' . $org_2->phones[$i][1] . ',this);">';
            if ($org_2->phones_types[$i][1] == 1) {
                echo '<option selected value="1">Телефон</option>';
            } else {
                echo '<option value="1">Телефон</option>';
            }
            if ($org_2->phones_types[$i][1] == 2) {
                echo '<option selected value="2">Факс</option>';
            } else {
                echo '<option value="2">Факс</option>';
            }
            if ($org_2->phones_types[$i][1] == 3) {
                echo '<option selected value="3">Единая служба</option>';
            } else {
                echo '<option value="3">Единая служба</option>';
            }
            echo '</select>';
            echo '</td><td><input onkeyup="ChangePhoneNumbField(' . $org_2->phones[$i][1] . ',this)" id="phone_' . $org_2->phones[$i][1] . '" type="text" value="' . $org_2->phones_numb[$i][1] . '"><span id="phone_s_' . $org_2->phones[$i][1] . '"></span></td>
                <td><a class="a_1" onclick="DeletePhone(' . $org_2->phones[$i][1] . ');"><img src="../images/delete.png" title="Удалить телефон" alt=""></a></td></tr>';
        }
    }
    echo '</table>';
    echo '<a class="a_1" onclick="AddPhone(' . $_POST['ChangeAddressID'] . ');"><img src="../images/add_team.png" title="Добавить телефон" alt=""></a>';
    echo '<p style="color: red; font-style: italic;">Отметьте галочкой рабочие дни. Если перерыва нет, оставьте в полях значения 00:00.</p>';
    echo '<table class="adres_table" style="border-collapse: collapse;"><tr><td>Рабочие дни</td><td>Рабочeе время</td><td>Перерыв</td></tr>';
    $n = 1;
    for ($i = 0; $i < count($org_2->days); $i++) {
        if ($org_2->days[$i][0] == $_POST['ChangeAddressID']) {
            echo '<tr style="border: 1px solid #cccccc;"><td><label>';
            if ($org_2->days[$i][2] == 1) {
                echo '<input onchange="ChangeWorkDay(this);" checked type="checkbox" value="' . $org_2->days[$i][3] . '">';
            } else {
                echo '<input onchange="ChangeWorkDay(this);" type="checkbox" value="' . $org_2->days[$i][3] . '">';
            }
            echo $org_2->days[$i][1] . '</label></td>';
            echo '<td><input type="text" onkeyup="ChangeTime(' . $org_2->hours_s[$i][2] . ',1,this);" id="time_s_' . $n . '" value="' . $org_2->hours_s[$i][1] . '"><span id="bl' . $org_2->hours_s[$i][2] . '1"></span><br><input type="text" onkeyup="ChangeTime(' . $org_2->hours_e[$i][2] . ',2,this);" id="time_e_' . $n . '" value="' . $org_2->hours_e[$i][1] . '"><span id="bl' . $org_2->hours_e[$i][2] . '2"></span></td>
                <td><input type="text" onkeyup="ChangeTime(' . $org_2->hours_b_s[$i][2] . ',3,this);" id="time_bs_' . $n . '" value="' . $org_2->hours_b_s[$i][1] . '"><span id="bl' . $org_2->hours_b_s[$i][2] . '3"></span><br><input type="text" onkeyup="ChangeTime(' . $org_2->hours_b_e[$i][2] . ',4,this);" id="time_be_' . $n . '" value="' . $org_2->hours_b_e[$i][1] . '"><span id="bl' . $org_2->hours_b_e[$i][2] . '4"></span></td>';
            $n++;
        }
    }
    echo '</table>';
}
/*
if (isset($_POST['ChangeWorkDay'])) {
    $_POST['ChangeWorkDay'] = filter_var($_POST['ChangeWorkDay'], FILTER_VALIDATE_INT);
    $_POST['ChangeWorkDayT'] = filter_var($_POST['ChangeWorkDayT'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare('UPDATE k_catalog_firms_days SET k_cfh_day_t=:type WHERE k_cfh_id=:id');
        $queue->execute(array(":type" => $_POST['ChangeWorkDayT'], ":id" => $_POST['ChangeWorkDay']));
        echo $_POST['ChangeWorkDayT'];
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['SaveTimeID'])) {
    $_POST['SaveTimeID'] = filter_var($_POST['SaveTimeID'], FILTER_VALIDATE_INT);
    $_POST['SaveTimeType'] = filter_var($_POST['SaveTimeType'], FILTER_VALIDATE_INT);
    $_POST['SaveTimeVal'] = filter_var($_POST['SaveTimeVal'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        switch ($_POST['SaveTimeType']) {
            case 1: $queue = $mysql->prepare('UPDATE k_catalog_firms_hours SET k_cfd_hour_s=:time WHERE k_cfd_id=:id');
                break;
            case 2: $queue = $mysql->prepare('UPDATE k_catalog_firms_hours SET k_cfd_hour_e=:time WHERE k_cfd_id=:id');
                break;
            case 3: $queue = $mysql->prepare('UPDATE k_catalog_firms_hours SET k_cfd_hour_break_s=:time WHERE k_cfd_id=:id');
                break;
            case 4: $queue = $mysql->prepare('UPDATE k_catalog_firms_hours SET k_cfd_hour_break_e=:time WHERE k_cfd_id=:id');
                break;
        }
        if ($queue->execute(array(":id" => $_POST['SaveTimeID'], ":time" => ($_POST['SaveTimeVal'] . ':00')))) {
            if (preg_match('/^[0-2][0-9]:[0-5][0-9]$/', $_POST['SaveTimeVal'])) {
                echo 'yes';
            }
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['ChangePhoneNumb'])) {
    $_POST['ChangePhoneNumb'] = filter_var($_POST['ChangePhoneNumb'], FILTER_VALIDATE_INT);
    $_POST['ChangePhoneNumbVal'] = filter_var($_POST['ChangePhoneNumbVal'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare('UPDATE k_catalog_firms_phones SET k_cfp_phone=:phone WHERE k_cfp_id=:id');
        if ($queue->execute(array(":id" => $_POST['ChangePhoneNumb'], ":phone" => $_POST['ChangePhoneNumbVal']))) {
            echo 'yes';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['ChangePhoneType'])) {
    $_POST['ChangePhoneType'] = filter_var($_POST['ChangePhoneType'], FILTER_VALIDATE_INT);
    $_POST['ChangePhoneTypeT'] = filter_var($_POST['ChangePhoneTypeT'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare('UPDATE k_catalog_firms_phones SET k_cfp_type=:type WHERE k_cfp_id=:id');
        if ($queue->execute(array(":id" => $_POST['ChangePhoneType'], ":type" => $_POST['ChangePhoneTypeT']))) {
            echo 'yes';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['DeletePhone'])) {
    $_POST['DeletePhone'] = filter_var($_POST['DeletePhone'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare('DELETE FROM k_catalog_firms_phones WHERE k_cfp_id=:id');
        if ($queue->execute(array(":id" => $_POST['DeletePhone']))) {
            echo 'yes';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['AddPhone'])) {
    $_POST['AddPhone'] = filter_var($_POST['AddPhone'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare('INSERT INTO k_catalog_firms_phones (k_cfp_phone,k_cfp_type,k_cfp_parent) VALUES ("",1,:parent)');
        if ($queue->execute(array(":parent" => $_POST['AddPhone']))) {
            echo $mysql->lastInsertId();
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['DeleteFromCategory'])) {
    $_POST['DeleteFromCategory'] = filter_var($_POST['DeleteFromCategory'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue = $mysql->prepare('DELETE FROM k_catalog_firms_parents WHERE k_cfp_id=:id');
        if ($queue->execute(array(":id" => $_POST['DeleteFromCategory']))) {
            echo 'yes';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['OpenCategoryFormID'])) {
    $_POST['OpenCategoryFormID'] = filter_var($_POST['OpenCategoryFormID'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queueL = $mysql->prepare('SELECT *
            FROM k_catalog_firms_parents AS kcfp
            LEFT JOIN k_catalog_subcategories AS kcs ON (kcs.k_cs_id = kcfp.k_cfp_parent_id)
            LEFT JOIN k_catalog_big_subcategories AS kcbs ON (kcs.k_cs_parent = kcbs.k_cbs_id)
            LEFT JOIN k_catalog_categories AS kcc ON (kcbs.k_cbs_parent = kcc.k_cc_id)
            WHERE k_cfp_id=:id
            LIMIT 1');
        $queueL->execute(array(":id" => $_POST['OpenCategoryFormID']));
        $resultL = $queueL->fetch(PDO::FETCH_ASSOC);
        $queue1 = $mysql->prepare('SELECT * FROM k_catalog_categories ORDER BY k_cc_name ASC');
        $queue1->execute();
        $result1 = $queue1->fetchAll(PDO::FETCH_ASSOC);
        $queue2 = $mysql->prepare('SELECT * FROM k_catalog_big_subcategories WHERE k_cbs_parent=:id ORDER BY k_cbs_name ASC');
        $queue2->execute(array(":id" => $resultL['k_cbs_parent']));
        $result2 = $queue2->fetchAll(PDO::FETCH_ASSOC);
        $queue3 = $mysql->prepare('SELECT * FROM k_catalog_subcategories WHERE k_cs_parent=:id ORDER BY k_cs_name ASC');
        $queue3->execute(array(":id" => $resultL['k_cs_parent']));
        $result3 = $queue3->fetchAll(PDO::FETCH_ASSOC);
        $output1 = '<select onchange="OnCategoryChange(' . $_POST['OpenCategoryFormID'] . ');" id="category_select_change_' . $_POST['OpenCategoryFormID'] . '">';
        foreach ($result1 as $value) {
            if ($resultL['k_cc_name'] == $value['k_cc_name']) {
                $output1 .= '<option selected value="' . $value['k_cc_id'] . '">' . $value['k_cc_name'] . '</option>';
            } else {
                $output1 .= '<option value="' . $value['k_cc_id'] . '">' . $value['k_cc_name'] . '</option>';
            }
        }
        $output1 .= '</select>';
        $output2 = '<select onclick="OnBigSChange(' . $_POST['OpenCategoryFormID'] . ');" id="bs_select_change_' . $_POST['OpenCategoryFormID'] . '">';
        foreach ($result2 as $value) {
            if ($resultL['k_cbs_name'] == $value['k_cbs_name']) {
                $output2 .= '<option selected value="' . $value['k_cbs_id'] . '">' . $value['k_cbs_name'] . '</option>';
            } else {
                $output2 .= '<option value="' . $value['k_cbs_id'] . '">' . $value['k_cbs_name'] . '</option>';
            }
        }
        $output2 .= '</select>';
        $output3 = '<select id="sub_select_change_' . $_POST['OpenCategoryFormID'] . '">';
        foreach ($result3 as $value) {
            if ($resultL['k_cs_name'] == $value['k_cs_name']) {
                $output3 .= '<option selected value="' . $value['k_cs_id'] . '">' . $value['k_cs_name'] . '</option>';
            } else {
                $output3 .= '<option value="' . $value['k_cs_id'] . '">' . $value['k_cs_name'] . '</option>';
            }
        }
        $output3 .= '</select>';
        echo json_encode(array("categories" => $output1, "bigsub" => $output2, "sub" => $output3));
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['OnCategoryChange'])) {
    $_POST['OnCategoryChange'] = filter_var($_POST['OnCategoryChange'], FILTER_VALIDATE_INT);
    $_POST['OnCategoryChangeID'] = filter_var($_POST['OnCategoryChangeID'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue1 = $mysql->prepare('SELECT * FROM k_catalog_big_subcategories WHERE k_cbs_parent=:id ORDER BY k_cbs_name ASC');
        $queue1->execute(array(":id" => $_POST['OnCategoryChange']));
        $result1 = $queue1->fetchAll(PDO::FETCH_ASSOC);
        $queue2 = $mysql->prepare('SELECT * FROM k_catalog_subcategories WHERE k_cs_parent=:id ORDER BY k_cs_name ASC');
        $queue2->execute(array(":id" => $result1[0]['k_cbs_id']));
        $result2 = $queue2->fetchAll(PDO::FETCH_ASSOC);
        $output1 = '<select onclick="OnBigSChange(' . $_POST['OnCategoryChangeID'] . ');" id="bs_select_change_' . $_POST['OnCategoryChangeID'] . '">';
        foreach ($result1 as $value) {
            $output1 .= '<option value="' . $value['k_cbs_id'] . '">' . $value['k_cbs_name'] . '</option>';
        }
        $output1 .= '</select>';
        $output2 = '<select id="sub_select_change_' . $_POST['OnCategoryChangeID'] . '">';
        foreach ($result2 as $value) {
            $output2 .= '<option value="' . $value['k_cs_id'] . '">' . $value['k_cs_name'] . '</option>';
        }
        $output2 .= '</select>';
        if (!$result1 || !$result2) {
            echo json_encode(array("error" => 1));
        } else {
            echo json_encode(array("bigsub" => $output1, "sub" => $output2));
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['OnBigSChange'])) {
    $_POST['OnBigSChange'] = filter_var($_POST['OnBigSChange'], FILTER_VALIDATE_INT);
    $_POST['OnBigSChangeID'] = filter_var($_POST['OnBigSChangeID'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue1 = $mysql->prepare('SELECT * FROM k_catalog_subcategories WHERE k_cs_parent=:id ORDER BY k_cs_name ASC');
        $queue1->execute(array(":id" => $_POST['OnBigSChange']));
        $result1 = $queue1->fetchAll(PDO::FETCH_ASSOC);
        $output1 = '<select id="sub_select_change_' . $_POST['OnBigSChangeID'] . '">';
        foreach ($result1 as $value) {
            $output1 .= '<option value="' . $value['k_cs_id'] . '">' . $value['k_cs_name'] . '</option>';
        }
        $output1 .= '</select>';
        if (!$result1) {
            echo json_encode(array("error" => 1));
        } else {
            echo json_encode(array("sub" => $output1));
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['SaveChangedCategoryS'])) {
    $_POST['SaveChangedCategoryS'] = filter_var($_POST['SaveChangedCategoryS'], FILTER_VALIDATE_INT);
    $_POST['SaveChangedCategoryID'] = filter_var($_POST['SaveChangedCategoryID'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue1 = $mysql->prepare('UPDATE k_catalog_firms_parents SET k_cfp_parent_id=:parent WHERE k_cfp_id=:id');
        if ($queue1->execute(array(":id" => $_POST['SaveChangedCategoryID'], ":parent" => $_POST['SaveChangedCategoryS']))) {
            echo 'yes';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['CreateNewCategorySelect'])) {
    $_POST['CreateNewCategorySelect'] = filter_var($_POST['CreateNewCategorySelect'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue1 = $mysql->prepare('INSERT INTO k_catalog_firms_parents (k_cfp_firm_id,k_cfp_parent_id) VALUES (:id,1)');
        if ($queue1->execute(array(":id" => $_POST['CreateNewCategorySelect']))) {
            echo $mysql->lastInsertId();
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['ANOAA'])) {
    $_POST['ANOAA'] = filter_var($_POST['ANOAA'], FILTER_VALIDATE_INT);
    $_POST['ANOAS'] = filter_var($_POST['ANOAS'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue1 = $mysql->prepare('INSERT INTO k_catalog_firms_addresses (k_cfa_address,k_cfa_parent) VALUES (:address,:parent)');
        if ($queue1->execute(array(":address" => $_POST['ANOAA'], ":parent" => $_POST['ANOAS']))) {
            $parent = $mysql->lastInsertId();
            $queue2 = $mysql->prepare('INSERT INTO k_catalog_firms_days (k_cfh_day,k_cfh_day_t,k_cfh_parent) VALUES (:day_week,1,:parent)');
            $queue3 = $mysql->prepare('INSERT INTO k_catalog_firms_hours (k_cfd_parent) VALUES (:parent)');
            for ($i = 1; $i < 8; $i++) {
                $queue2->execute(array(":day_week" => $i, ":parent" => $parent));
                $queue3->execute(array(":parent" => $mysql->lastInsertId()));
            }
            echo 'yes';
        }
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['DAFO'])) {
    $_POST['DAFO'] = filter_var($_POST['DAFO'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue1 = $mysql->prepare('DELETE kcfd.*, kcfh.*
            FROM k_catalog_firms_days AS kcfd
            LEFT JOIN k_catalog_firms_hours AS kcfh ON (kcfh.k_cfd_parent = kcfd.k_cfh_id)
            WHERE k_cfh_parent=:parent');
        if ($queue1->execute(array(":parent" => $_POST['DAFO']))) {
            $queue2 = $mysql->prepare('DELETE FROM k_catalog_firms_addresses WHERE k_cfa_id=:id');
            $queue2->execute(array(":id" => $_POST['DAFO']));
            echo 'yes';
        }
    } catch (PDOException $e) {
        exit();
    }
}
*/
if (isset($_POST['DeleteAllOrganization'])) {
    $_POST['DeleteAllOrganization'] = filter_var($_POST['DeleteAllOrganization'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        /*
        $queue0 = $mysql->prepare('DELETE kcfd.*, kcfh.*, kcfp.*
            FROM k_catalog_firms_addresses AS kcfa
            LEFT JOIN k_catalog_firms_days AS kcfd ON (kcfd.k_cfh_parent = kcfa.k_cfa_id)
            LEFT JOIN k_catalog_firms_hours AS kcfh ON (kcfh.k_cfd_parent = kcfd.k_cfh_id)
            LEFT JOIN k_catalog_firms_phones AS kcfp ON (kcfp.k_cfp_parent = kcfa.k_cfa_id)
            WHERE k_cfa_parent=:parent');
        $queue0->execute(array(":parent" => $_POST['DeleteAllOrganization']));
        $queue1 = $mysql->prepare('DELETE FROM k_catalog_firms_addresses WHERE k_cfa_parent=:parent');
        $queue1->execute(array(":parent" => $_POST['DeleteAllOrganization']));
        $queue2 = $mysql->prepare('DELETE FROM k_catalog_firms_parents WHERE k_cfp_firm_id=:parent');
        $queue2->execute(array(":parent" => $_POST['DeleteAllOrganization']));
        $queue3 = $mysql->prepare('DELETE FROM k_catalog_firms WHERE k_cf_id=:parent');
        $queue3->execute(array(":parent" => $_POST['DeleteAllOrganization']));
        */
        $queue3 = $mysql->prepare('DELETE FROM base_org WHERE id=:parent');
        $queue3->execute(array(":parent" => $_POST['DeleteAllOrganization']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}
/*
if (isset($_POST['SaveNewAddressCH'])) {
    $_POST['SaveNewAddressCH'] = filter_var($_POST['SaveNewAddressCH'], FILTER_VALIDATE_INT);
    $_POST['SaveNewAddressCHID'] = filter_var($_POST['SaveNewAddressCHID'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue1 = $mysql->prepare('UPDATE k_catalog_firms_addresses SET k_cfa_address=:addr WHERE k_cfa_id=:id');
        $queue1->execute(array(":id" => $_POST['SaveNewAddressCHID'], ":addr" => $_POST['SaveNewAddressCH']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['SaveNewAddressAdvCH'])) {
    $_POST['SaveNewAddressAdvCH'] = filter_var($_POST['SaveNewAddressAdvCH'], FILTER_SANITIZE_STRIPPED);
    $_POST['SaveNewAddressAdvCHID'] = filter_var($_POST['SaveNewAddressAdvCHID'], FILTER_VALIDATE_INT);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue1 = $mysql->prepare('UPDATE k_catalog_firms_addresses SET k_cfa_adv=:adv WHERE k_cfa_id=:id');
        $queue1->execute(array(":id" => $_POST['SaveNewAddressAdvCHID'], ":adv" => $_POST['SaveNewAddressAdvCH']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['SaveAllOrgParamsName'])) {
    $_POST['SaveAllOrgParamsName'] = filter_var($_POST['SaveAllOrgParamsName'], FILTER_SANITIZE_STRIPPED);
    $_POST['SaveAllOrgParamsSite'] = filter_var($_POST['SaveAllOrgParamsSite'], FILTER_SANITIZE_STRIPPED);
    $_POST['SaveAllOrgParamsEmail'] = filter_var($_POST['SaveAllOrgParamsEmail'], FILTER_SANITIZE_STRIPPED);
    $_POST['SaveAllOrgParamsDescr'] = filter_var($_POST['SaveAllOrgParamsDescr'], FILTER_SANITIZE_STRIPPED);
    $_POST['SaveAllOrgParamsID'] = filter_var($_POST['SaveAllOrgParamsID'], FILTER_VALIDATE_INT);
    if (!preg_match('/^http:\/\//', $_POST['SaveAllOrgParamsSite'])) {
        $_POST['SaveAllOrgParamsSite'] = 'http://' . $_POST['SaveAllOrgParamsSite'];
    }
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue1 = $mysql->prepare('UPDATE k_catalog_firms SET k_cf_name=:name, k_cf_email=:email, k_cf_site=:site, k_cf_description=:descr WHERE k_cf_id=:id');
        $queue1->execute(array(":id" => $_POST['SaveAllOrgParamsID'],
            ":name" => $_POST['SaveAllOrgParamsName'],
            ":email" => $_POST['SaveAllOrgParamsEmail'],
            ":site" => $_POST['SaveAllOrgParamsSite'],
            ":descr" => $_POST['SaveAllOrgParamsDescr']));
        echo 'yes';
    } catch (PDOException $e) {
        exit();
    }
}

if (isset($_POST['CanBeCreated'])) {
    $_POST['CanBeCreated'] = filter_var($_POST['CanBeCreated'], FILTER_SANITIZE_STRIPPED);
    try {
        $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
        $mysql->exec('set names utf8');
        $queue0 = $mysql->prepare('SELECT count(*) AS num FROM k_catalog_firms WHERE k_cf_name=:name');
        $queue0->execute(array(":name" => $_POST['CanBeCreated']));
        $result0 = $queue0->fetch(PDO::FETCH_ASSOC);
        if ($result0['num'] > 0) {
            echo 'no';
        } else {
            echo 'yes';
        }
    } catch (PDOException $e) {
        exit();
    }
}
*/
?>
