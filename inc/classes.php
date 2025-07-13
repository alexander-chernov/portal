<?php

class MainPageRealty {

    public $id = array();
    public $text = array();
    public $date = array();
    public $photo = array();
    public $address = array();
    public $fid = array();
    public $max_pages = 0;
    public $perPage = 5;
    public $titleLen = 17;
    public $textLen = 26;

    public function MainPageRealty($page) {
        $page = filter_var($page, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT *
                FROM k_immovables_sell AS kis
                LEFT JOIN k_immovables_sell_types AS kist ON (kist.k_isft_id = kis.k_isf_immovable_type)
                LEFT JOIN k_streets_house_nums AS kshn ON (kshn.k_shn_id = kis.k_isf_address)
                LEFT JOIN k_districts as d ON d.k_d_id = kshn.k_shn_district_id
                LEFT JOIN k_streets AS ks ON (ks.k_s_id = kshn.k_shn_street_id)
                LEFT JOIN k_street_house_photos AS kshp ON (kshp.k_shp_parent = kis.k_isf_address)
                LEFT JOIN map_buildings AS mb ON (mb.k_shn_id = kshn.k_shn_id)
                WHERE
                k_isf_state=1
                AND k_isf_main_page=1
                AND k_isf_end_date > NOW()
                GROUP BY k_isf_id
                ORDER BY k_isf_up_date DESC
                LIMIT ' . (($page - 1) * $this->perPage) . ','.$this->perPage.'');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                $this->id[] = $row['k_isf_id'];
                $this->fid[] = $row['fid'];
                $this->date[] = $row['k_isf_registration_date'];
                $this->photo[] = $row['k_shp_url'];
                $this->district[] = $row['k_d_name'];
                $this->square[] = $row['k_isf_area_all'];
                $this->floor[] = $row['k_isf_floor'];
                $this->price[] = $row['k_isf_price'];
                if ($row['k_isf_immovable_type'] != 0) {
                    $this->type[] = $row['k_isft_name'];
                }
                $this->rooms[] = $row['k_isf_rooms'];
                if ($row['k_isf_deal_type'] == 1) {
                    $this->contract[] = 'продам';
                } else {
                    $this->contract[] = 'сдам';
                }


/*
                $start = '<p class="vip_nedvig_opisan_1"><a href="realty/?ShowParam=20&id=' . $row['k_isf_id'] . '">';
                $text = '';
                if ($row['k_isf_subcategory'] == 1 || $row['k_isf_subcategory'] == 6) {
                    if ($row['k_isf_rooms'] != 0) {
                        $text .= $row['k_isf_rooms'] . '-комнатная Квартиру ';
                    } else {
                        $text .= 'Квартиру ';
                    }
                    if ($row['k_isf_immovable_type'] != 0) {
                        $text .= '(' . $row['k_isft_name'] . ')';
                    }
                }
                if ($row['k_isf_subcategory'] == 2 || $row['k_isf_subcategory'] == 7) {
                    if ($row['k_isf_rooms'] != 0) {
                        $text .= $row['k_isf_rooms'] . '-комнатный Дом ';
                    } else {
                        $text .= 'Дом ';
                    }
                    if ($row['k_isf_immovable_type'] != 0) {
                        $text .= ' (' . $row['k_isft_name'] . ')';
                    }
                }
                $sub_not = array(1, 2, 6, 7);
                if (!in_array($row['k_isf_subcategory'], $sub_not)) {
                    if ($row['k_isf_immovable_type'] != 0) {
                        $text .= $row['k_isft_name'];
                    }
                }
                if (mb_strlen($text, 'UTF8') > $this->textLen) {
                    $text = preg_match('/\($/', mb_substr($text, 0, $this->textLen, 'utf-8')) ? mb_substr($text, 0, ($this->textLen-1), 'utf-8') : mb_substr($text, 0, $this->textLen, 'utf-8');
                }
                $end = '...</a><span>' . $row['k_isf_price'] . ' т.р.</span></p>';
                $this->text[] = $start . $text . $end;
*/
                if (preg_match('/(###)/', $row['k_s_name'])) {
                    $street = explode('###', $row['k_s_name']);
                    $house = explode('###', $row['k_shn_house_num']);
                    array_push($this->address, $street[0] . ' ' . $house[0] . ' / ' . $street[1] . ' ' . $house[1]);
                } else {
                    array_push($this->address, $row['k_s_name'] . ' ' . $row['k_shn_house_num']);
                }
            }
            $query3 = $mysql->prepare('SELECT count(*) AS max
                FROM k_immovables_sell AS kis
                LEFT JOIN k_immovables_sell_types AS kist ON (kist.k_isft_id = kis.k_isf_immovable_type)
                WHERE
                k_isf_state=1
                AND k_isf_main_page=1
                AND k_isf_end_date > NOW()
                ');
            $query3->execute();
            $result3 = $query3->fetch(PDO::FETCH_ASSOC);
            if ($result3['max'] % $this->perPage == 0) {
                $this->max_pages = (int) ($result3['max'] / $this->perPage);
            } else {
                $this->max_pages = (int) (($result3['max'] / $this->perPage) + 1);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

}

class StatiscticsMainPage {

    public $immo_all = 0;
    public $immo_today = 0;
    public $photo_all = 0;
    public $photo_today = 0;
    public $experts_all = 0;
    public $experts_today = 0;
    public $blog_all = 0;
    public $blog_today = 0;
    public $job_all = 0;
    public $job_today = 0;
    public $catalog_all = 0;
    public $webcams_all = 0;
    public $perPage = 8;
    public $titleLen = 17;
    public $textLen = 30;

    public function StatiscticsMainPage() {
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT count(*) AS max FROM k_immovables_sell WHERE
                k_isf_state=1
                AND k_isf_main_page=1
                AND k_isf_end_date > NOW()
            ');
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            $this->immo_all = $result['max'];
            $query2 = $mysql->prepare('SELECT count(*) AS max FROM k_immovables_sell WHERE k_isf_state=1 AND k_isf_registration_date>:date');
            $query2->execute(array(":date" => date('Y-m-d 00:00:00', time())));
            $result2 = $query2->fetch(PDO::FETCH_ASSOC);
            $this->immo_today = $result2['max'];

            $query3 = $mysql->prepare('SELECT count(*) AS max FROM k_photodesk');
            $query3->execute();
            $result3 = $query3->fetch(PDO::FETCH_ASSOC);
            $this->photo_all = $result3['max'];
            $query4 = $mysql->prepare('SELECT count(*) AS max FROM k_photodesk WHERE k_pd_reg_date>:date');
            $query4->execute(array(":date" => date('Y-m-d 00:00:00', time())));
            $result4 = $query4->fetch(PDO::FETCH_ASSOC);
            $this->photo_today = $result4['max'];

            $query5 = $mysql->prepare('SELECT count(*) AS max FROM k_experts WHERE k_e_verified=1 AND k_e_active=1');
            $query5->execute();
            $result5 = $query5->fetch(PDO::FETCH_ASSOC);
            $this->experts_all = $result5['max'];
            $query6 = $mysql->prepare('SELECT count(*) AS max FROM k_experts WHERE k_e_date>:date AND k_e_end_date>NOW()');
            $query6->execute(array(":date" => date('Y-m-d 00:00:00', time())));
            $result6 = $query6->fetch(PDO::FETCH_ASSOC);
            $this->experts_today = $result6['max'];

            $query7 = $mysql->prepare('SELECT count(*) AS max FROM k_job WHERE k_j_state=1');
            $query7->execute();
            $result7 = $query7->fetch(PDO::FETCH_ASSOC);
            $this->job_all = $result7['max'];
            $query8 = $mysql->prepare('SELECT count(*) AS max FROM k_job WHERE k_j_state=1 AND k_j_date_reg>:date');
            $query8->execute(array(":date" => date('Y-m-d 00:00:00', time())));
            $result8 = $query8->fetch(PDO::FETCH_ASSOC);
            $this->job_today = $result8['max'];

            $query9 = $mysql->prepare('SELECT count(*) AS max FROM base_org');
            $query9->execute();
            $result9 = $query9->fetch(PDO::FETCH_ASSOC);
            $this->catalog_all = $result9['max'];

            $query10 = $mysql->prepare('SELECT count(*) AS max FROM k_blog');
            $query10->execute();
            $result10 = $query10->fetch(PDO::FETCH_ASSOC);
            $this->blog_all = $result10['max'];

            $query11 = $mysql->prepare('SELECT count(*) AS max FROM k_blog WHERE k_b_date>:date');
            $query11->execute(array(":date" => date('Y-m-d 00:00:00', time())));
            $result11 = $query11->fetch(PDO::FETCH_ASSOC);
            $this->blog_today = $result11['max'];

            $query12 = $mysql->prepare('SELECT count(*) AS max FROM k_webcams');
            $query12->execute();
            $result12 = $query12->fetch(PDO::FETCH_ASSOC);
            $this->webcams_all = $result12['max'];

        } catch (PDOException $e) {
            exit();
        }
    }

}

class MainPagePhotoboard {

    public $id = array();
    public $date = array();
    public $photo = array();
    public $text = array();
    public $price = array();
    public $max_pages = 0;
    public $perPage = 8;
    public $perMainPage = 10;
    public $titleLen = 60;
    public $textLen = 60;

    public function MainPagePhotoboard($page) {
        $page = filter_var($page, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query3 = $mysql->prepare('SELECT count(*) AS max FROM k_photodesk WHERE k_pd_main_page=1');
            $query3->execute();
            $result3 = $query3->fetch(PDO::FETCH_ASSOC);
            if ($page > $result3['max']) {
                $page = 1;
            }
            if ($page < 1) {
                $page = $result3['max'];
            }
            $query = $mysql->prepare('SELECT *
                FROM k_photodesk AS kp
                LEFT JOIN k_photodesk_photos AS kpp ON (kpp.k_pdp_ad_id = kp.k_pd_id)
                WHERE k_pd_main_page=1
                GROUP BY k_pd_id
                ORDER BY k_pd_id DESC
                LIMIT ' . (($page - 1) * $this->perMainPage) . ','.$this->perMainPage);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                $this->id[] = $row['k_pd_id'];
                $this->date[] = $row['k_pd_reg_date'];
                $this->photo[] = $row['k_pdp_link'];
                $this->price[] = $row['k_pd_price'];
                if (mb_strlen($row['k_pd_theme'], 'UTF8') > $this->titleLen) {
                    $this->text[] = mb_substr($row['k_pd_theme'], 0, $this->titleLen, 'utf-8') . '...';
                } else {
                    $this->text[] = $row['k_pd_theme'];
                }
            }
            if ($result3['max'] % $this->perMainPage == 0) {
                $this->max_pages = (int) ($result3['max'] / $this->perMainPage);
            } else {
                $this->max_pages = (int) (($result3['max'] / $this->perMainPage) + 1);
            }
            //var_dump($this->max_pages);
        } catch (PDOException $e) {
            exit();
        }
    }

    public function Refresh() {
        $print = '';
        for ($i = 0; $i < count($this->id); $i++) {
            $print .= '<div class="block_vip_content">
            <div class="block_vip_content_wrap">
                <a href="/photoboard/?PageType=2&PhotoNum=' . $this->id[$i] . '">';
                if ($this->photo[$i] && file_exists($_SERVER['DOCUMENT_ROOT'].'' . $this->photo[$i])) {
                    $print .= '<img class="img_vip_gl" src="' . $this->photo[$i] . '" alt="">';
                } else {
                    $print .= '<img class="img_vip_gl" src="admin/images/noimage.png" alt="">';
                }
                $print .= '</a>
            </div>
            <div class="block_vip_content_wrap block_vip_content_padd">
                <!--<p class="time_vip_phot time_vip_phot_p">' . mb_substr($this->date[$i], 0, -3, "utf-8") . '</p>-->
                <p class="text_photo_gl text_photo_gl_p">' . $this->text[$i] . '</p>
                <hr size=3 color="#444">
                <p class="text_photo_cena_gl text_photo_cena_gl_p">' . $this->price[$i] . ' руб.</p>
                </div>
            </div>';
        }
        return $print;
    }

    public function AjaxRefresh() {
        $print = '';
        for ($i = 0; $i < count($this->id); $i++) {
            /*
            $print .= '<div class="block_vip_content">
            <div class="ramka_img">
            <a href="photoboard/?PageType=2&PhotoNum=' . $this->id[$i] . '">';
            if ($this->photo[$i] && file_exists('../admin/' . str_replace('photo/', 'photo/1_', $this->photo[$i]))) {
                $print .= '<img class="img_vip_gl" src="admin/' . str_replace('photo/', 'photo/1_', $this->photo[$i]) . '" alt="">';
            } else {
                $print .= '<img class="img_vip_gl" src="admin/images/noimage.png" alt="">';
            }
            $print .= '</a></div>
            <p class="time_vip_nedvig">' . mb_substr($this->date[$i], 0, -3, "utf-8") . '</p>
            <p class="text_photo_gl">' . $this->text[$i] . '</p>
            <p class="text_photo_cena_gl">' . $this->price[$i] . ' руб.</p>
            </div>';
            */

            $print .= '<div class="block_vip_content">
            <div class="block_vip_content_wrap">
                <a href="/photoboard/?PageType=2&PhotoNum=' . $this->id[$i] . '">';
            if ($this->photo[$i] && file_exists($_SERVER['DOCUMENT_ROOT'].'/admin/' . str_replace('photo/', 'photo/1_', $this->photo[$i]))) {
                $print .= '<img class="img_vip_gl" src="/admin/' . str_replace('photo/', 'photo/1_', $this->photo[$i]) . '" alt="">';
            } else {
                $print .= '<img class="img_vip_gl" src="admin/images/noimage.png" alt="">';
            }
            $print .= '</a>
            </div>
            <div class="block_vip_content_wrap block_vip_content_padd">
                <!--<p class="time_vip_phot time_vip_phot_p">' . mb_substr($this->date[$i], 0, -3, "utf-8") . '</p>-->
                <p class="text_photo_gl text_photo_gl_p">' . $this->text[$i] . '</p>
                <hr size=3 color="#444">
                <p class="text_photo_cena_gl text_photo_cena_gl_p">' . $this->price[$i] . ' руб.</p>
                </div>
            </div>';

        }
        return $print;
    }

}

class MainPageExperts {

    public $id = array();
    public $date = array();
    public $photo = array();
    public $name = array();
    public $text = array();
    public $max_pages = 0;
    public $perPage = 5;
    public $titleLen = 17;
    public $textLen = 50;

    public function MainPageExperts($page) {
        $page = filter_var($page, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT * FROM k_experts
                WHERE k_e_main_page=1
                ORDER BY k_e_up_date DESC, k_e_date DESC
                LIMIT ' . (($page - 1) * $this->perPage) . ','.$this->perPage);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                $this->id[] = $row['k_e_id'];
                $this->date[] = $row['k_e_date'];
                $this->photo[] = $row['k_e_image'];
                if (mb_strlen($row['k_e_theme'], 'UTF8') > $this->titleLen) {
                    $this->name[] = mb_substr($row['k_e_theme'], 0, $this->titleLen, 'utf-8') . '...';
                } else {
                    $this->name[] = $row['k_e_theme'];
                }
                if (mb_strlen($row['k_e_brief'], 'UTF8') > $this->textLen) {
                    $this->text[] = mb_substr($row['k_e_brief'], 0, $this->textLen, 'utf-8') . '...';
                } else {
                    $this->text[] = $row['k_e_brief'];
                }
            }
            $query3 = $mysql->prepare('SELECT count(*) AS max FROM k_experts WHERE k_e_end_date>NOW()');
            $query3->execute();
            $result3 = $query3->fetch(PDO::FETCH_ASSOC);
            if ($result3['max'] % 5 == 0) {
                $this->max_pages = (int) ($result3['max'] / $this->perPage);
            } else {
                $this->max_pages = (int) (($result3['max'] / $this->perPage) + 1);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

    public function Refresh() {
        $print = '';
        for ($i = 0; $i < count($this->id); $i++) {
            $print .= '<div class="block_vip_expert">
            <div class="block_img_exp">
            <div class="ramka_img"><div>';
            $print .= '<a href="expert/?PageType=2&AdId=' . $this->id[$i] . '">';
            if ($this->photo[$i] && file_exists('admin/images/experts/' . $this->photo[$i])) {
                $print .= '<img class="img_exp_gl" src="admin/images/experts/' . $this->photo[$i] . '" alt="">';
            } else {
                $print .= '<img class="img_exp_gl" src="admin/images/noimage.png" alt="">';
            }
            $print .= '</a></div></div></div>
            <div class="block_text_exp_gl">
            <p class="time_vip_nedvig">' . mb_substr($this->date[$i], 0, -3, "utf-8") . '</p>
            <p class="text_photo_gl">' . $this->name[$i] . '</p>
            <p class="text_expert_team">' . $this->text[$i] . '</p>
            <a href="expert/?PageType=2&Question=1&AdId=' . $this->id[$i] . '" class="vopros_exp">Задать вопрос</a>
            </div></div>';
        }
        return $print;
    }

    public function AjaxRefresh() {
        $print = '';
        for ($i = 0; $i < count($this->id); $i++) {
            $print .= '<div class="block_vip_expert">
            <div class="block_img_exp">
            <div class="ramka_img"><div>';
            $print .= '<a href="expert/?PageType=2&AdId=' . $this->id[$i] . '">';
            if ($this->photo[$i] && file_exists('admin/images/experts/' . $this->photo[$i])) {
                $print .= '<img class="img_exp_gl" src="admin/images/experts/' . $this->photo[$i] . '" alt="">';
            } else {
                $print .= '<img class="img_exp_gl" src="admin/images/noimage.png" alt="">';
            }
            $print .= '</a></div></div></div>
            <div class="block_text_exp_gl">
            <p class="time_vip_nedvig">' . mb_substr($this->date[$i], 0, -3, "utf-8") . '</p>
            <p class="text_photo_gl">' . $this->name[$i] . '</p>
            <p class="text_expert_team">' . $this->text[$i] . '</p>
            <a href="expert/?PageType=2&Question=1&AdId=' . $this->id[$i] . '" class="vopros_exp">Задать вопрос</a>
            </div></div>';
        }
        return $print;
    }

}

class MainPageJob {

    public $id = array();
    public $date = array();
    public $post = array();
    public $salary = array();
    public $max_pages = 0;
    public $jtype = array();
    public $state = array();
    public $perPage = 17;
    public $titleLen = 17;
    public $textLen = 30;

    public function MainPageJob($page, $type) {
        $page = filter_var($page, FILTER_VALIDATE_INT);
        $type = filter_var($type, FILTER_VALIDATE_INT);
        if ($page > 6) {
            $page = 1;
        }
        if ($page < 1) {
            $page = 6;
        }
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT kj.*, kjc.*, DATEDIFF(now(),kj.k_j_up_date) as kint, kj.k_j_type as jtype
                FROM k_job AS kj
                LEFT JOIN k_job_currency AS kjc ON (kjc.k_jc_id = kj.k_j_currency)
                WHERE k_j_date_end>NOW() AND k_j_type=:type
                GROUP BY k_j_id
                ORDER BY k_j_up_date DESC
                LIMIT ' . (($page - 1) * $this->perPage) . ','.$this->perPage);
            $query->execute(array(":type" => $type));
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                $this->id[] = $row['k_j_id'];
                $this->date[] = $row['k_j_date_reg'];
                $this->jtype[] = $row['jtype'];
                $this->state[] = $row['k_j_state'];
                if (mb_strlen($row['k_j_post'], 'UTF8') > $this->textLen) {
                    $this->post[] = mb_substr($row['k_j_post'], 0, $this->textLen, 'utf-8') . '...';
                } else {
                    $this->post[] = $row['k_j_post'];
                }
                $salary = '';
                if ($row['k_j_salary_min'] != 0) {
                    $salary .= 'от ' . $row['k_j_salary_min'] . ' ' . $row['k_jc_name'] . ' ';
                }
                if ($row['k_j_salary_max'] != 0) {
                    //$salary .= '<br>до ' . $row['k_j_salary_max'] . ' ' . $row['k_jc_name'] . ' ';
                }
                if ($row['kint'] != 0) {
                    $this->interval[] = $row['kint'];
                }
                $this->salary[] = $salary;
            }
            $query3 = $mysql->prepare('SELECT count(*) AS max FROM k_job WHERE k_j_date_end>NOW() AND k_j_type=:type');
            $query3->execute(array(":type" => $type));
            $result3 = $query3->fetch(PDO::FETCH_ASSOC);
            if ($result3['max'] % 2 == 0) {
                $this->max_pages = (int) ($result3['max'] / $this->perPage);
            } else {
                $this->max_pages = (int) (($result3['max'] / $this->perPage) + 1);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

    public function Refresh() {
        $print = '';
        $toggle = FALSE;
        for ($i = 0; $i < count($this->id); $i++) {
            /* if ($i % 2 == 0) {
                //if ($toggle) {
                    //$toggle = FALSE;
                $print .= '<div class="gray_rab">';
                $print .= '<div class="block_gray_rab_1">';
            } else { */
                $print .= '<div class="white_rab white_rab_25">';
                $print .= '<div class="block_white_rab_1">';
                    //$toggle = TRUE;
                //}
            /*  } */
            /*
            if ($toggle) {
                $print .= '<div class="block_gray_rab_' . ($i % 2 + 1) . '">';
            } else {
                $print .= '<div class="block_white_rab_' . ($i % 2 + 1) . '">';
            }
            */
            $print .= '<div class="centriruem no_padding">';
            /*
            if ($this->interval[$i]>0 && $this->interval[$i]<2){
                $intclass="red_job";
            } elseif($this->interval[$i]>=2 && $this->interval[$i]<8) {
                $intclass="orange_job";
            } elseif($this->interval[$i]>=8 && $this->interval[$i]<15) {
                $intclass="green_job";
            } elseif($this->interval[$i]>=15 && $this->interval[$i]<31) {
                $intclass="blue_job";
            } else{
                $intclass="grey_job";
            }
            */
            if($this->jtype[$i]==1) {//требуется
                $intclass="orange_job";
            } else{
                $intclass="blue_job";
            }

            $print .= '
            <!--<p class="time_vip_nedvig">' . mb_substr($this->date[$i], 0, -3, "utf-8") . '</p>-->

            <span class="vip_nedvig_opisan_1 vip_nedvig_opisan_1_1"><span class="'.$intclass.'"></span>
            <a href="job/?PageType=4&Id=' . $this->id[$i] . '" class="text_rabota_gl">' . $this->post[$i] . '</a></span>
            <span class="text_rabota_cena_gl">' . $this->salary[$i] . '</span>
            </div></div>';
            //if ($i % 2 == 1 || $i == (count($this->id) - 1)) {
                $print .= '</div>';
            //}
        }
        return $print;
    }

}

class MainPageBlog {

    public $id = array();
    public $date = array();
    public $photo = array();
    public $name = array();
    public $text = array();

    public $max_pages = 0;
    public $perPage = 4;
    public $titleLen = 50;
    public $textLen = 50;

    public function MainPageBlog($page) {
        $page = filter_var($page, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT * FROM k_blog
                WHERE k_b_main_page=1 AND k_b_state=1
                ORDER BY k_b_date DESC
                LIMIT ' . (($page - 1) * $this->perPage) . ','.$this->perPage);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                $this->id[] = $row['k_b_id'];
                $this->date[] = $row['k_b_date'];
                $this->photo[] = $row['k_b_image'];
                if (mb_strlen($row['k_b_name'], 'UTF8') > $this->titleLen) {
                    $this->name[] = mb_substr($row['k_b_name'], 0, $this->titleLen, 'utf-8') . '...';
                } else {
                    $this->name[] = $row['k_b_name'];
                }
                if (mb_strlen($row['k_b_brief'], 'UTF8') > $this->textLen) {
                    $this->text[] = mb_substr($row['k_b_brief'], 0, $this->textLen, 'utf-8') . '...';
                } else {
                    $this->text[] = $row['k_b_brief'];
                }
            }
            $query3 = $mysql->prepare('SELECT count(*) AS max FROM k_blog');
            $query3->execute();
            $result3 = $query3->fetch(PDO::FETCH_ASSOC);
            if ($result3['max'] % 5 == 0) {
                $this->max_pages = (int) ($result3['max'] / $this->perPage);
            } else {
                $this->max_pages = (int) (($result3['max'] / $this->perPage) + 1);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

    public function Refresh() {
        $print = '';
        for ($i = 0; $i < count($this->id); $i++) {
            $print .= '<div class="block_vip_expert">
            <div class="block_img_exp">
            <div class="ramka_img"><div>';
            $print .= '<a href="blog/?PageType=2&ID=' . $this->id[$i] . '">';
            if ($this->photo[$i] && file_exists($_SERVER['DOCUMENT_ROOT'].'/admin/' . $this->photo[$i])) {
                $print .= '<img class="img_exp_gl" src="admin/' . $this->photo[$i] . '" alt="">';
            } else {
                $print .= '<img class="img_exp_gl" src="admin/images/noimage.png" alt="">';
            }
            $print .= '</a></div></div></div>
            <div class="block_text_exp_gl">
            <span class="add_rubrika_1 time_vip_nedvig_a">' . mb_substr($this->date[$i], 0, -3, "utf-8") . '</span>
            <span class="text_photo_gl">' . $this->name[$i] . '</span>
            </div></div>';
            //<!--<p class="text_blog_team">' . $this->text[$i] . '</p>-->

        }
        return $print;
    }

    public function AjaxRefresh() {
        $print = '';
        for ($i = 0; $i < count($this->id); $i++) {
            $print .= '<div class="block_vip_expert">
            <div class="block_img_exp">
            <div class="ramka_img"><div>';
            $print .= '<a href="blog/?PageType=2&ID=' . $this->id[$i] . '">';
            if ($this->photo[$i] && file_exists($_SERVER['DOCUMENT_ROOT'].'/admin/' . $this->photo[$i])) {
                $print .= '<img class="img_exp_gl" src="/admin/' . $this->photo[$i] . '" alt="">';
            } else {
                $print .= '<img class="img_exp_gl" src="/admin/images/noimage.png" alt="">';
            }
            $print .= '</a></div></div></div>
            <div class="block_text_exp_gl">
            <span class="add_rubrika_1 time_vip_nedvig_a">' . mb_substr($this->date[$i], 0, -3, "utf-8") . '</span>
            <span class="text_photo_gl">' . $this->name[$i] . '</span>
            </div></div>';
            //<p class="text_blog_team">' . $this->text[$i] . '</p>
        }
        return $print;
    }

}

class MainPageWebcams {

    public $id = array();
    public $url = array();
    public $photo = array();
    public $name = array();
    public $max_pages = 0;
    public $perPage = 6;
    public $titleLen = 17;
    public $textLen = 50;

    public function MainPageWebcams($page) {
        $page = filter_var($page, FILTER_VALIDATE_INT);
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            $query = $mysql->prepare('SELECT * FROM k_webcams
                ORDER BY k_w_name ASC
                LIMIT ' . (($page - 1) * $this->perPage) . ','.$this->perPage);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                $this->id[] = $row['k_w_id'];
                $this->url[] = $row['k_w_url'];
                $this->photo[] = $row['k_w_image'];
                if (mb_strlen($row['k_w_name'], 'UTF8') > $this->titleLen) {
                    $this->name[] = mb_substr($row['k_w_name'], 0, $this->titleLen, 'utf-8') . '...';
                } else {
                    $this->name[] = $row['k_w_name'];
                }
            }
            $query3 = $mysql->prepare('SELECT count(*) AS max FROM k_webcams');
            $query3->execute();
            $result3 = $query3->fetch(PDO::FETCH_ASSOC);
            if ($result3['max'] % $this->perPage == 0) {
                $this->max_pages = (int) ($result3['max'] / $this->perPage);
            } else {
                $this->max_pages = (int) (($result3['max'] / $this->perPage) + 1);
            }
        } catch (PDOException $e) {
            exit();
        }
    }

    public function Refresh() {
        $print = '';
        for ($i = 0; $i < count($this->id); $i++) {
            $print .= '<div class="block_vip_expert">
            <div class="block_img_exp">
            <div class="ramka_img"><div>';
            $print .= '<a href="' . $this->url[$i] . '">';
            if ($this->photo[$i] && file_exists('admin/' . $this->photo[$i])) {
                $print .= '<img class="img_exp_gl" src="admin/' . $this->photo[$i] . '" alt="">';
            } else {
                $print .= '<img class="img_exp_gl" src="admin/images/noimage.png" alt="">';
            }
            $print .= '</a></div></div></div>
            <div class="block_text_exp_gl">
            <a href="' . $this->url[$i] . '"><p class="text_photo_gl">' . mb_substr($this->url[$i], 7, strlen($this->url[$i]), "utf-8") . '</p></a>
            <p class="text_photo_gl">' . $this->name[$i] . '</p>
            </div></div>';
        }
        return $print;
    }

    public function AjaxRefresh() {
        $print = '';
        for ($i = 0; $i < count($this->id); $i++) {
            $print .= '<div class="block_vip_expert">
            <div class="block_img_exp">
            <div class="ramka_img"><div>';
            $print .= '<a href="' . $this->url[$i] . '">';
            if ($this->photo[$i] && file_exists('../admin/' . $this->photo[$i])) {
                $print .= '<img class="img_exp_gl" src="admin/' . $this->photo[$i] . '" alt="">';
            } else {
                $print .= '<img class="img_exp_gl" src="admin/images/noimage.png" alt="">';
            }
            $print .= '</a></div></div></div>
            <div class="block_text_exp_gl">
            <a href="' . $this->url[$i] . '"><p class="text_photo_gl">' . mb_substr($this->url[$i], 7, strlen($this->url[$i]), "utf-8") . '</p></a>
            <p class="text_photo_gl">' . $this->name[$i] . '</p>
            </div></div>';
        }
        return $print;
    }

}


class MainPageCatalog {

    public $id = array();
    public $name = array();
    public $text = array();
    public $url = array();
    public $type = 1;
    public $max_pages = 0;
    public $perPage = 8;
    public $titleLen = 40;
    public $textLen = 40;

    public function MainPageCatalog($page, $type) {
        $page = filter_var($page, FILTER_VALIDATE_INT);
        $this->type = filter_var($type, FILTER_VALIDATE_INT);
        if ($page > 6) {
            $page = 1;
        }
        if ($page < 1) {
            $page = 6;
        }
        try {
            $mysql = new PDO('mysql:host=' . _DB_addr . ';dbname=' . _DB_name, _DB_login, _DB_pass);
            $mysql->exec('set names utf8');
            if ($this->type == 1) {
                //$query = $mysql->prepare('SELECT * FROM k_catalog_firms ORDER BY k_cf_name ASC LIMIT ' . (($page - 1) * $this->perPage) . ','.$this->perPage);
                $query = $mysql->prepare('SELECT * FROM base_org ORDER BY name ASC LIMIT ' . (($page - 1) * $this->perPage) . ','.$this->perPage);
                $query->execute();
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                foreach ($result as $row) {
                    //$this->id[] = $row['k_cf_id'];
                    $this->id[] = $row['id'];
                    /*if (mb_strlen($row['k_cf_name'], 'UTF8') > $this->titleLen) {
                        $this->name[] = mb_substr($row['k_cf_name'], 0, $this->titleLen, 'utf-8') . '...';
                    } else {
                        $this->name[] = $row['k_cf_name'];
                    }*/
                    if (mb_strlen($row['name'], 'UTF8') > $this->titleLen) {
                        $this->name[] = mb_substr($row['name'], 0, $this->titleLen, 'utf-8') . '...';
                    } else {
                        $this->name[] = $row['name'];
                    }
                    /*
                    if (mb_strlen($row['k_cf_description'], 'UTF8') > $this->textLen) {
                        $this->text[] = mb_substr($row['k_cf_description'], 0, $this->textLen, 'utf-8') . '...';
                    } else {
                        $this->text[] = $row['k_cf_description'];
                    }
                    */
                    if (mb_strlen($row['description'], 'UTF8') > $this->textLen) {
                        $this->text[] = mb_substr($row['description'], 0, $this->textLen, 'utf-8') . '...';
                    } else {
                        $this->text[] = $row['description'];
                    }
                    /*
                    if ($row['k_cf_site'] != 'http://') {
                        $this->url[] = $row['k_cf_site'];
                    } else {
                        $this->url[] = '';
                    }*/
                    if ($row['site'] != 'http://') {
                        $this->url[] = $row['site'];
                    } else {
                        $this->url[] = '';
                    }
                    //$query3 = $mysql->prepare('SELECT count(*) AS max FROM k_catalog_firms');
                    $query3 = $mysql->prepare('SELECT count(*) AS max FROM base_org');
                    $query3->execute();
                    $result3 = $query3->fetch(PDO::FETCH_ASSOC);
                    if ($result3['max'] % 2 == 0) {
                        $this->max_pages = (int) ($result3['max'] / $this->perPage);
                    } else {
                        $this->max_pages = (int) (($result3['max'] / $this->perPage) + 1);
                    }
                }
            }
            if ($this->type == 2) {
                $query = $mysql->prepare('SELECT * FROM k_sites ORDER BY k_s_name ASC LIMIT ' . (($page - 1) * $this->perPage) . ','.$this->perPage);
                $query->execute();
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                foreach ($result as $row) {
                    $this->id[] = $row['k_s_id'];
                    if (mb_strlen($row['k_s_name'], 'UTF8') > $this->titleLen) {
                        $this->name[] = mb_substr($row['k_s_name'], 0, $this->titleLen, 'utf-8') . '...';
                    } else {
                        $this->name[] = $row['k_s_name'];
                    }
                    if (mb_strlen($row['k_s_description'], 'UTF8') > $this->textLen) {
                        $this->text[] = mb_substr($row['k_s_description'], 0, $this->textLen, 'utf-8') . '...';
                    } else {
                        $this->text[] = $row['k_s_description'];
                    }
                    $this->url[] = $row['k_s_url'];
                    $query3 = $mysql->prepare('SELECT count(*) AS max FROM k_sites');
                    $query3->execute();
                    $result3 = $query3->fetch(PDO::FETCH_ASSOC);
                    if ($result3['max'] % 2 == 0) {
                        $this->max_pages = (int) ($result3['max'] / $this->perPage);
                    } else {
                        $this->max_pages = (int) (($result3['max'] / $this->perPage) + 1);
                    }
                }
            }
        } catch (PDOException $e) {
            exit();
        }
    }

    public function Refresh() {
        $print = '';
        $toggle = FALSE;
        for ($i = 0; $i < count($this->id); $i++) {
            if ($i % 2 == 0) {
                //if ($toggle) {
                    //$toggle = FALSE;
                $print .= '<div class="gray_rab">';
                $print .= '<div class="block_gray_rab_1">';
            } else {
                $print .= '<div class="white_rab">';
                $print .= '<div class="block_white_rab_1">';
                    //$toggle = TRUE;
                //}
            }
            /*
            if ($toggle) {

            } else {

            }
            */
            $print .= '<div class="catalog_block">';
            $print .= '<a href="catalog/?PageType=1&id_org=' . $this->id[$i] . '" class="text_rabota_gl">' . $this->name[$i] . '</a><br>
            <a href="catalog/?PageType=1&id_org=' . $this->id[$i] . '" class="vip_catalog_opisan">' . $this->text[$i] . '</a>
            <a href="' . $this->url[$i] . '" class="text_catalog_link">' . $this->url[$i] . '</a>
            </div></div>';
            //if ($i % 2 == 1 || $i == (count($this->id) - 1)) {
                $print .= '</div>';
            //}
        }
        return $print;
    }

}

?>
