<?php

class ControllerExtensionExtensionSeoPack extends Controller {
	private $error = array();

	public function index() {

		$this->load->language('extension/extension/seopack');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('seopack', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension/seopack', 'user_token=' . $this->session->data['user_token'], 'SSL'));
		}

		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL'),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/extension/seopack', 'user_token=' . $this->session->data['user_token'], 'SSL'),
      		'separator' => ' :: '
   		);

		$data['heading_title'] = $this->language->get('heading_title');
		$data['user_token'] = $this->session->data['user_token'];

		$data['action'] = $this->url->link('extension/extension/seopack', 'user_token=' . $this->session->data['user_token'], 'SSL');

		$data['clearmetas'] = $this->url->link('extension/extension/seopack/clearmetas', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['clearkeywords'] = $this->url->link('extension/extension/seopack/clearkeywords', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['cleartags'] = $this->url->link('extension/extension/seopack/cleartags', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['clearproducts'] = $this->url->link('extension/extension/seopack/clearproducts', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['clearurls'] = $this->url->link('extension/extension/seopack/clearurls', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['friendlyurls'] = $this->url->link('extension/extension/seopack/friendlyurls', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['seourls'] = $this->url->link('extension/extension/seopack/seourls', 'user_token=' . $this->session->data['user_token'], 'SSL');


		$data['seopack_parameters'] = array();

		if (isset($this->request->post['seopack_parameters'])) {
			$data['seopack_parameters'] = $this->request->post['seopack_parameters'];
		} elseif ($this->config->get('seopack_parameters')) {
			$data['seopack_parameters'] = $this->config->get('seopack_parameters');
		}
		$initial_seopack_parameters = array('seopack_parameters'=>array('keywords'=>'%p%c','metas'=>'%p - %f','tags'=>'%p%c','related'=>'5', 'ext'=>'.html', 'gkey' => md5(uniqid(rand(), true))));
		if (!$data['seopack_parameters'])
			{
			$this->model_setting_setting->editSetting('seopack', $initial_seopack_parameters);
			$data['seopack_parameters']  = $initial_seopack_parameters['seopack_parameters'];
			}

			$data['gkey'] = md5(uniqid(rand(), true));
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = (str_replace('warning', '', $this->language->get('warning')))?$this->language->get('warning'):'';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

	$data['header'] = $this->load->controller('common/header');
						$data['column_left'] = $this->load->controller('common/column_left');
						$data['footer'] = $this->load->controller('common/footer');

						$this->response->setOutput($this->load->view('extension/extension/seopack', $data));


	}


	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/extension/seopack')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	public function clearmetas() {

		$query = $this->db->query("update " . DB_PREFIX . "product_description set meta_description = '';");

		$this->session->data['success'] = "Meta descriptions were deleted.";

		$this->response->redirect($this->url->link('extension/extension/seopack', 'user_token=' . $this->session->data['user_token'], 'SSL'));

	}
	public function clearkeywords() {

		$query = $this->db->query("update " . DB_PREFIX . "product_description set meta_keyword = '';");

		$this->session->data['success'] = "Meta keywords were deleted.";

		$this->response->redirect($this->url->link('extension/extension/seopack', 'user_token=' . $this->session->data['user_token'], 'SSL'));

	}
	public function cleartags() {

		$query = $this->db->query("update " . DB_PREFIX . "product_description set tag = '';");

		$this->session->data['success'] = "Product tags were deleted.";

		$this->response->redirect($this->url->link('extension/extension/seopack', 'user_token=' . $this->session->data['user_token'], 'SSL'));

	}
	public function clearproducts() {

		$query = $this->db->query("delete from " . DB_PREFIX . "product_related;");

		$this->session->data['success'] = "Related products were deleted.";

		$this->response->redirect($this->url->link('extension/extension/seopack', 'user_token=' . $this->session->data['user_token'], 'SSL'));

	}
	public function clearurls() {

		$query = $this->db->query("delete from " . DB_PREFIX . "seo_url where store_id = 0;");

		$this->session->data['success'] = "SEO URLs were deleted.";

		$this->response->redirect($this->url->link('extension/extension/seopack', 'user_token=' . $this->session->data['user_token'], 'SSL'));

	}

	public function friendlyurls() {

		$this->session->data['success'] = "Friendly urls were created.";

		$this->response->redirect($this->url->link('extension/extension/seopack', 'user_token=' . $this->session->data['user_token'], 'SSL'));

	}


	public function seourls($r = 0) {

		$this->session->data['success'] = '';

		$this->db->query("delete from " . DB_PREFIX . "seo_url where keyword = '';");

		$data['seopack_parameters'] = array();

		if (isset($this->request->post['seopack_parameters'])) {
			$data['seopack_parameters'] = $this->request->post['seopack_parameters'];
		} elseif ($this->config->get('seopack_parameters')) {
			$data['seopack_parameters'] = $this->config->get('seopack_parameters');
		}

		$ext = $data['seopack_parameters']['ext'];

		$query = $this->db->query("SELECT pd.product_id, pd.name, pd.language_id ,l.code FROM ".DB_PREFIX."product p
				inner join ".DB_PREFIX."product_description pd ON p.product_id = pd.product_id
				inner join ".DB_PREFIX."language l on l.language_id = pd.language_id ");

		$i = 0;
		foreach ($query->rows as $product_row ){


			if( strlen($product_row['name']) > 1 ){

				$slug = $this->generateSlug($product_row['name']).$ext;
				$exist_query = $this->db->query("SELECT query FROM " . DB_PREFIX . "seo_url WHERE " . DB_PREFIX . "seo_url.query = 'product_id=" . $product_row['product_id'] . "' and store_id = 0 and language_id=".$product_row['language_id']);

				if(!$exist_query->num_rows){

					$exist_keyword = $this->db->query("SELECT query FROM " . DB_PREFIX . "seo_url WHERE store_id = 0 and " . DB_PREFIX . "seo_url.keyword = '" . $slug . "'");
					if($exist_keyword->num_rows){
						$exist_keyword_lang = $this->db->query("SELECT query FROM " . DB_PREFIX . "seo_url WHERE store_id = 0 and " . DB_PREFIX . "seo_url.keyword = '" . $slug . "' AND " . DB_PREFIX . "seo_url.query <> 'product_id=" . $product_row['product_id'] . "'");
						if($exist_keyword_lang->num_rows){
								$slug = $this->generateSlug($product_row['name']).'-'.rand().$ext;
							}
							else
							{
								$slug = $this->generateSlug($product_row['name']).'-'.$product_row['code'].$ext;
							}
						}


					$add_query = "INSERT INTO " . DB_PREFIX . "seo_url (query, keyword, language_id) VALUES ('product_id=" . $product_row['product_id'] . "', '" . $slug . "', " . $product_row['language_id'] . ")";
					$this->db->query($add_query);
					$i++;
				}
			}
		}
		$this->session->data['success'] .=  $i . " new product friendly SEO urls were created <br>";

		$query = $this->db->query("SELECT cd.category_id, cd.name, cd.language_id, l.code FROM ".DB_PREFIX."category c
				inner join ".DB_PREFIX."category_description cd on c.category_id = cd.category_id
				inner join ".DB_PREFIX."language l on l.language_id = cd.language_id");

		$i = 0;
		foreach ($query->rows as $category_row){


			if( strlen($category_row['name']) > 1 ){

				$slug = $this->generateSlug($category_row['name']);
				$exist_query =  $this->db->query("SELECT query FROM " . DB_PREFIX . "seo_url WHERE store_id = 0 and " . DB_PREFIX . "seo_url.query = 'category_id=" . $category_row['category_id'] . "' and language_id=".$category_row['language_id']);

				if(!$exist_query->num_rows){

					$exist_keyword = $this->db->query("SELECT query FROM " . DB_PREFIX . "seo_url WHERE store_id = 0 and " . DB_PREFIX . "seo_url.keyword = '" . $slug . "'");
					if($exist_keyword->num_rows){
						$exist_keyword_lang = $this->db->query("SELECT query FROM " . DB_PREFIX . "seo_url WHERE store_id = 0 and " . DB_PREFIX . "seo_url.keyword = '" . $slug . "' AND " . DB_PREFIX . "seo_url.query <> 'category_id=" . $category_row['category_id'] . "'");
						if($exist_keyword_lang->num_rows){
								$slug = $this->generateSlug($category_row['name']).'-'.rand();
							}
							else
							{
								$slug = $this->generateSlug($category_row['name']).'-'.$category_row['code'];
							}
						}



					$add_query = "INSERT INTO " . DB_PREFIX . "seo_url (query, keyword,language_id) VALUES ('category_id=" . $category_row['category_id'] . "', '" . $slug . "', " . $category_row['language_id'] . ")";
					$this->db->query($add_query);
					$i++;
				}
			}
		}

		$this->session->data['success'] .= $i . " new category friendly SEO urls were created <br>";

		$query = $this->db->query("SELECT l.language_id, m.name, m.manufacturer_id, l.code from ".DB_PREFIX."manufacturer m join ".DB_PREFIX."language l;");
		$i = 0;

		foreach ($query->rows as $manufacturer_row){


			if( strlen($manufacturer_row['name']) > 1 ){

				$slug = $this->generateSlug($manufacturer_row['name']);
				$exist_query = $this->db->query("SELECT query FROM " . DB_PREFIX . "seo_url WHERE store_id = 0 and " . DB_PREFIX . "seo_url.query = 'manufacturer_id=" . $manufacturer_row['manufacturer_id'] . "' and language_id=".$manufacturer_row['language_id']);

				if(!$exist_query->num_rows){

					$exist_keyword = $this->db->query("SELECT query FROM " . DB_PREFIX . "seo_url WHERE store_id = 0 and " . DB_PREFIX . "seo_url.keyword = '" . $slug . "'");
					if($exist_keyword->num_rows){ $slug = $this->generateSlug($manufacturer_row['name']).'-'.rand();}


					$add_query = "INSERT INTO " . DB_PREFIX . "seo_url (query, keyword,language_id) VALUES ('manufacturer_id=" . $manufacturer_row['manufacturer_id'] . "', '" . $slug . "', " . $manufacturer_row['language_id'] . ")";
					$this->db->query($add_query);
					$i++;
				}
			}
		}

		$this->session->data['success'] .=  $i . " new manufacturer friendly SEO urls were created <br>";

		$query = $this->db->query("SELECT id.information_id, id.title, id.language_id, l.code FROM ".DB_PREFIX."information i
			inner join ".DB_PREFIX."information_description id on i.information_id = id.information_id
			inner join ".DB_PREFIX."language l on l.language_id = id.language_id;");
		$i = 0;

		foreach ($query->rows as $info_row){


			if( strlen($info_row['title']) > 1 ){

				$slug = $this->generateSlug($info_row['title']).$ext;
				$exist_query = $this->db->query("SELECT query FROM " . DB_PREFIX . "seo_url WHERE store_id = 0 and " . DB_PREFIX . "seo_url.query = 'information_id=" . $info_row['information_id'] . "'  and language_id=".$info_row['language_id']);

				if(!$exist_query->num_rows){

					$exist_keyword = $this->db->query("SELECT query FROM " . DB_PREFIX . "seo_url WHERE store_id = 0 and " . DB_PREFIX . "seo_url.keyword = '" . $slug . "'");
					if($exist_keyword->num_rows){
						$exist_keyword_lang = $this->db->query("SELECT query FROM " . DB_PREFIX . "seo_url WHERE store_id = 0 and " . DB_PREFIX . "seo_url.keyword = '" . $slug . "' AND " . DB_PREFIX . "seo_url.query <> 'information_id=" . $info_row['information_id'] . "'");
						if($exist_keyword_lang->num_rows){
								$slug = $this->generateSlug($info_row['title']).'-'.rand();
							}
							else
							{
								$slug = $this->generateSlug($info_row['title']).'-'.$info_row['code'];
							}
						}

					$add_query = "INSERT INTO " . DB_PREFIX . "seo_url (query, keyword, language_id) VALUES ('information_id=" . $info_row['information_id'] . "', '" . $slug . "', " . $info_row['language_id'] . ")";
					$this->db->query($add_query);
					$i++;
				}
			}
		}

		$this->session->data['success'] .=  $i . " new information friendly SEO urls were created <br>";

		$add_query = "ALTER TABLE `" . DB_PREFIX . "seo_url` ADD UNIQUE( `store_id`, `language_id`, `query`);";
		$this->db->query($add_query);

		$query = $this->db->query("SELECT * FROM ".DB_PREFIX."store;");

		foreach ($query->rows as $store_row) {
				$add_query = "insert ignore into `" . DB_PREFIX . "seo_url` (store_id, language_id, query, keyword) select " . $store_row['store_id'] . ", language_id, query, keyword from `" . DB_PREFIX . "seo_url` where store_id = 0;";
				$this->db->query($add_query);
		}
		if (!$r) {$this->response->redirect($this->url->link('extension/extension/seopack', 'user_token=' . $this->session->data['user_token'], 'SSL'));}

	}

	function _transliteration_process($string, $unknown = '?', $source_langcode = NULL) {

        if (!preg_match('/[\x80-\xff]/', $string)) {
            return $string;
        }

        static $tailBytes;

        if (!isset($tailBytes)) {

            $tailBytes = array();
            for ($n = 0; $n < 256; $n++) {
                if ($n < 0xc0) {
                    $remaining = 0;
                }
                elseif ($n < 0xe0) {
                    $remaining = 1;
                }
                elseif ($n < 0xf0) {
                    $remaining = 2;
                }
                elseif ($n < 0xf8) {
                    $remaining = 3;
                }
                elseif ($n < 0xfc) {
                    $remaining = 4;
                }
                elseif ($n < 0xfe) {
                    $remaining = 5;
                }
                else {
                    $remaining = 0;
                }
                $tailBytes[chr($n)] = $remaining;
            }
        }


        preg_match_all('/[\x00-\x7f]+|[\x80-\xff][\x00-\x40\x5b-\x5f\x7b-\xff]*/', $string, $matches);

        $result = '';
        foreach ($matches[0] as $str) {
            if ($str[0] < "\x80") {

                $result .= $str;
                continue;
            }


            $head = '';
            $chunk = strlen($str);

            $len = $chunk + 1;

            for ($i = -1; --$len;) {
                $c = $str[++$i];
                if ($remaining = $tailBytes[$c]) {

                    $sequence = $head = $c;
                    do {

                        if (--$len && ($c = $str[++$i]) >= "\x80" && $c < "\xc0") {

                            $sequence .= $c;
                        }
                        else {
                            if ($len == 0) {

                                $result .= $unknown;
                                break 2;
                            }
                            else {

                                $result .= $unknown;

                                --$i;
                                ++$len;
                                continue 2;
                            }
                        }
                    } while (--$remaining);

                    $n = ord($head);
                    if ($n <= 0xdf) {
                        $ord = ($n - 192) * 64 + (ord($sequence[1]) - 128);
                    }
                    elseif ($n <= 0xef) {
                        $ord = ($n - 224) * 4096 + (ord($sequence[1]) - 128) * 64 + (ord($sequence[2]) - 128);
                    }
                    elseif ($n <= 0xf7) {
                        $ord = ($n - 240) * 262144 + (ord($sequence[1]) - 128) * 4096 + (ord($sequence[2]) - 128) * 64 + (ord($sequence[3]) - 128);
                    }
                    elseif ($n <= 0xfb) {
                        $ord = ($n - 248) * 16777216 + (ord($sequence[1]) - 128) * 262144 + (ord($sequence[2]) - 128) * 4096 + (ord($sequence[3]) - 128) * 64 + (ord($sequence[4]) - 128);
                    }
                    elseif ($n <= 0xfd) {
                        $ord = ($n - 252) * 1073741824 + (ord($sequence[1]) - 128) * 16777216 + (ord($sequence[2]) - 128) * 262144 + (ord($sequence[3]) - 128) * 4096 + (ord($sequence[4]) - 128) * 64 + (ord($sequence[5]) - 128);
                    }
                    $result .= $this->_transliteration_replace($ord, $unknown, $source_langcode);
                    $head = '';
                }
                elseif ($c < "\x80") {

                    $result .= $c;
                    $head = '';
                }
                elseif ($c < "\xc0") {

                    if ($head == '') {
                        $result .= $unknown;
                    }
                }
                else {

                    $result .= $unknown;
                    $head = '';
                }
            }
        }
        return $result;
    }

   function _transliteration_replace($ord, $unknown = '?', $langcode = NULL) {
        static $map = array();

        $bank = $ord >> 8;

        if (!isset($map[$bank][$langcode])) {
            $file = DIR_APPLICATION . 'controller/extension/extension/trans_db/' . sprintf('x%02x', $bank) . '.php';
            if (file_exists($file)) {
                include $file;
                if ($langcode != 'en' && isset($variant[$langcode])) {

                    $map[$bank][$langcode] = $variant[$langcode] + $base;
                }
                else {
                    $map[$bank][$langcode] = $base;
                }
            }
            else {
                $map[$bank][$langcode] = array();
            }
        }

        $ord = $ord & 255;

        return isset($map[$bank][$langcode][$ord]) ? $map[$bank][$langcode][$ord] : $unknown;
    }

	public function generateSlug($phrase) {

	$cyr = array(
        "??"=>"i","??"=>"c","??"=>"u","??"=>"k","??"=>"e","??"=>"n",
        "??"=>"g","??"=>"sh","??"=>"sh","??"=>"z","??"=>"x","??"=>"\'",
        "??"=>"f","??"=>"i","??"=>"v","??"=>"a","??"=>"p","??"=>"r",
        "??"=>"o","??"=>"l","??"=>"d","??"=>"zh","??"=>"ie","??"=>"e",
        "??"=>"ya","??"=>"ch","??"=>"c","??"=>"m","??"=>"i","??"=>"t",
        "??"=>"\'","??"=>"b","??"=>"yu",
        "??"=>"I","??"=>"C","??"=>"U","??"=>"K","??"=>"E","??"=>"N",
        "??"=>"G","??"=>"SH","??"=>"SH","??"=>"Z","??"=>"X","??"=>"\'",
        "??"=>"F","??"=>"I","??"=>"V","??"=>"A","??"=>"P","??"=>"R",
        "??"=>"O","??"=>"L","??"=>"D","??"=>"ZH","??"=>"IE","??"=>"E",
        "??"=>"YA","??"=>"CH","??"=>"C","??"=>"M","??"=>"I","??"=>"T",
        "??"=>"\'","??"=>"B","??"=>"YU"
    );

	$gr = array(
		"??" => "V", "??" => "Y", "??" => "Th", "??" => "E", "??" => "Z", "??" => "E",
		"??" => "Th", "??" => "i", "??" => "K", "??" => "L", "??" => "M", "??" => "N",
		"??" => "X", "??" => "O", "??" => "P", "??" => "R", "??" => "S", "??" => "T",
		"??" => "E", "??" => "F", "??" => "Ch", "??" => "Ps", "??" => "O", "??" => "a",
		"??" => "v", "??" => "y", "??" => "th", "??" => "e", "??" => "z", "??" => "e",
		"??" => "th", "??" => "i", "??" => "k", "??" => "l", "??" => "m", "??" => "n",
		"??" => "x", "??" => "o", "??" => "p", "??" => "r", "??" => "s", "??" => "t",
		"??" => "e", "??" => "f", "??" => "ch", "??" => "ps", "??" => "o", "??" => "s",
		"??" => "s", "??" => "s", "??" => "s", "??" => "e", "??" => "i", "??" => "a",
		"??" => "e", "??" => "o", "??" => "o"
	);

	$arabic = array(
		"??"=>"a", "??"=>"a", "??"=>"a", "??"=>"e", "??"=>"b", "??"=>"t", "??"=>"th", "??"=>"j",
		"??"=>"h", "??"=>"kh", "??"=>"d", "??"=>"d", "??"=>"r", "??"=>"z", "??"=>"s", "??"=>"sh",
		"??"=>"s", "??"=>"d", "??"=>"t", "??"=>"z", "??"=>"'e", "??"=>"gh", "??"=>"f", "??"=>"q",
		"??"=>"k", "??"=>"l", "??"=>"m", "??"=>"n", "??"=>"h", "??"=>"w", "??"=>"y", "??"=>"a",
		"??"=>"'e", "??"=>"'",
		"??"=>"'e", "????"=>"la", "??"=>"h", "??"=>"?", "!"=>"!",
		"??"=>"",
		"??"=>",",
		"?????"=>"a", "??"=>"u", "?????"=>"e", "??"=>"un", "??"=>"an", "??"=>"en", "??"=>""
	);

	$persian = array(
		"??"=>"a", "??"=>"a", "??"=>"a", "??"=>"e", "??"=>"b", "??"=>"t", "??"=>"th",
		"??"=>"j", "??"=>"h", "??"=>"kh", "??"=>"d", "??"=>"d", "??"=>"r", "??"=>"z",
		"??"=>"s", "??"=>"sh", "??"=>"s", "??"=>"d", "??"=>"t", "??"=>"z", "??"=>"'e",
		"??"=>"gh", "??"=>"f", "??"=>"q", "??"=>"k", "??"=>"l", "??"=>"m", "??"=>"n",
		"??"=>"h", "??"=>"w", "??"=>"y", "??"=>"a", "??"=>"'e", "??"=>"'",
		"??"=>"'e", "????"=>"la", "??"=>"ke", "??"=>"pe", "??"=>"che", "??"=>"je", "??"=>"gu",
		"??"=>"a", "??"=>"", "??"=>"h", "??"=>"?", "!"=>"!",
		"??"=>"",
		"??"=>",",
		"?????"=>"a", "??"=>"u", "?????"=>"e", "??"=>"un", "??"=>"an", "??"=>"en", "??"=>""
	);

	$normalize = array(
		'??'=>'S', '??'=>'s', '??'=>'Dj','??'=>'Z', '??'=>'z', '??'=>'A', '??'=>'A', '??'=>'A', '??'=>'A', '??'=>'A',
		'??'=>'A', '??'=>'A', '??'=>'C', '??'=>'E', '??'=>'E', '??'=>'E', '??'=>'E', '??'=>'I', '??'=>'I', '??'=>'I',
		'??'=>'I', '??'=>'N', '??'=>'O', '??'=>'O', '??'=>'O', '??'=>'O', '??'=>'O', '??'=>'O', '??'=>'U', '??'=>'U',
		'??'=>'U', '??'=>'U', '??'=>'Y', '??'=>'B', '??'=>'Ss','??'=>'a', '??'=>'a', '??'=>'a', '??'=>'a', '??'=>'a',
		'??'=>'a', '??'=>'a', '??'=>'c', '??'=>'e', '??'=>'e', '??'=>'e', '??'=>'e', '??'=>'i', '??'=>'i', '??'=>'i',
		'??'=>'i', '??'=>'o', '??'=>'n', '??'=>'o', '??'=>'o', '??'=>'o', '??'=>'o', '??'=>'o', '??'=>'o', '??'=>'u',
		'??'=>'u', '??'=>'u', '??'=>'y', '??'=>'y', '??'=>'b', '??'=>'y', '??'=>'f', '??'=>'G', '??'=>'S', '??'=>'U',
		'??'=>'u', '???'=>'Z', '???'=>'z', '??'=>'N', '??'=>'n', '??'=>'O', '??'=>'o', '??'=>'U', '??'=>'u', '???'=>'W',
		'???'=>'w', '???'=>'Y', '???'=>'y', '??'=>'c', '??'=>'C', '??'=>'a', '??'=>'A', '??'=>'c', '??'=>'C', '??'=>'d',
		'??'=>'D', '??'=>'e', '??'=>'E', '??'=>'e', '??'=>'E', '??'=>'i', '??'=>'I', '??'=>'n', '??'=>'N', '??'=>'o',
		'??'=>'O', '??'=>'r', '??'=>'R', '??'=>'s', '??'=>'S', '??'=>'t', '??'=>'T', '??'=>'u', '??'=>'U', '??'=>'u',
		'??'=>'U', '??'=>'y', '??'=>'Y', '??'=>'z', '??'=>'Z', "??"=>'a', '??'=>'A', '??'=>'c', '??'=>'C', '??'=>'e',
		'??'=>'E', '??'=>'l', '??'=>'n', '??'=>'o', '??'=>'s', '??'=>'S', '??'=>'z', '??'=>'Z', '??'=>'z', '??'=>'Z',
		'??'=>'i', '??'=>'s', '??'=>'g', '??'=>'i'
	);

	$result = html_entity_decode($phrase, ENT_COMPAT, "UTF-8");

	$result = strtr($result, $cyr);
	$result = strtr($result, $gr);
	$result = strtr($result, $arabic);
	$result = strtr($result, $persian);
	$result = strtr($result, $normalize);
	$result = strtolower($this->_transliteration_process($result));


	$result = strtolower($result);
	$result = str_replace('&', '-and-', $result);
	$result = str_replace('^', '', $result);
    $result = preg_replace("/[^a-z0-9-]/", "-", $result);
	$result = preg_replace('{(-)\1+}','$1', $result);
	$result = trim(substr($result, 0, 800));
	$result = trim($result,'-'); //Thanks to LeXXoS

    return $result;
	}




}
?>
