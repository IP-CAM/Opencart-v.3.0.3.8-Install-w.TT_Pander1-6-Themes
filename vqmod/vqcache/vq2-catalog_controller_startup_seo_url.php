<?php
class ControllerStartupSeoUrl extends Controller {
	public function index() {
		// Add rewrite to url class
		if ($this->config->get('config_seo_url')) {
			$this->url->addRewrite($this);
		}

		// Decode URL


			if (isset($this->request->get['_route_'])) {

			if (strpos($this->request->get['_route_'], '/page/') !== false) {
					$this->request->get['page'] = str_replace('/page/','',substr($this->request->get['_route_'], strpos($this->request->get['_route_'], '/page/')));
					$this->request->get['_route_'] = str_replace('/page/'.$this->request->get['page'],'',$this->request->get['_route_']);


				}
			
			$lquery = $this->db->query("SELECT * FROM " . DB_PREFIX . "language;");
			foreach ($lquery->rows as $language) {
				if ((strpos($this->request->get['_route_'],$language['code'].'/')) === 0) {
					$this->session->data['language'] = $language['code'];
					$this->language = new Language((VERSION >= '2.2.0.0')? $language['code'] : $language['directory']);
					$this->language->load((VERSION >= '2.2.0.0')? $language['code'] : $language['directory']);
					$this->registry->set('language', $this->language);
					$this->config->set('config_language_id', $language['language_id']);
					$this->request->get['_route_'] = substr( $this->request->get['_route_'], strlen($language['code'].'/'));

			        }
				}
			}
			
		if (isset($this->request->get['_route_'])) {

			$redirect_settings = $this->config->get('redirect_settings');
			if (isset($redirect_settings['redirectmanager'])) {
				$redirects = $this->config->get('redirect');
				if ($redirects) {
				foreach ($redirects as $redirectlink) {
						if ($redirectlink['title'] == $this->request->get['_route_']) {
								$this->request->get['_route_'] = $redirectlink['url'];
							}
					}
				}
			}

			
			$parts = explode('/', $this->request->get['_route_']);

			// remove any empty arrays from trailing
			if (utf8_strlen(end($parts)) == 0) {
				array_pop($parts);
			}

			foreach ($parts as $part) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE keyword = '" . $this->db->escape($part) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");


			$redirect_settings = $this->config->get('redirect_settings');
			if ((isset($redirect_settings['autoredirect'])) && (!($query->num_rows))) {
			$link = $this->db->escape($part);
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE store_id = 0 and keyword sounds like '" . $this->db->escape($part) . "'");
				if (($query->num_rows)) {
					$this->db->query("insert into " . DB_PREFIX . "autoredirect values ('".$link."','".$query->row['keyword']."',now());");
				}
			}
			
				if ($query->num_rows) {
					$url = explode('=', $query->row['query']);

					if ($url[0] == 'product_id') {
						$this->request->get['product_id'] = $url[1];
					}

					if ($url[0] == 'category_id') {
						if (!isset($this->request->get['path'])) {
							$this->request->get['path'] = $url[1];
						} else {
							$this->request->get['path'] .= '_' . $url[1];
						}
					}

					if ($url[0] == 'manufacturer_id') {
						$this->request->get['manufacturer_id'] = $url[1];
					}

					if ($url[0] == 'information_id') {
						$this->request->get['information_id'] = $url[1];
					}

					if ($query->row['query'] && $url[0] != 'information_id' && $url[0] != 'manufacturer_id' && $url[0] != 'category_id' && $url[0] != 'product_id') {
						$this->request->get['route'] = $query->row['query'];
					}
				} else {
					

			if($this->request->get['_route_'] ==  'alisveris-listem') { $this->request->get['route'] =  'account/wishlist';
			} elseif ($this->request->get['_route_'] ==  'iletisim') { $this->request->get['route'] =  'information/contact';
			} elseif ($this->request->get['_route_'] ==  'hesap') { $this->request->get['route'] =  'account/account';
			} elseif ($this->request->get['_route_'] ==  'sitemap') { $this->request->get['route'] =  'information/sitemap';
			} elseif ($this->request->get['_route_'] ==  'marka') { $this->request->get['route'] =  'product/manufacturer';
			} elseif ($this->request->get['_route_'] ==  'affiliates') { $this->request->get['route'] =  'affiliate/account';
			} elseif ($this->request->get['_route_'] ==  'special') { $this->request->get['route'] =  'product/special';
			} elseif ($this->request->get['_route_'] ==  'giris') { $this->request->get['route'] =  'account/login';
			} elseif ($this->request->get['_route_'] ==  'cikis') { $this->request->get['route'] =  'account/logout';
			} elseif ($this->request->get['_route_'] ==  'kayit') { $this->request->get['route'] =  'account/signup';
			} elseif ($this->request->get['_route_'] ==  'sepet') { $this->request->get['route'] =  'checkout/cart';
			} elseif ($this->request->get['_route_'] ==  'odeme') { $this->request->get['route'] =  'checkout/checkout';
			} elseif ($this->request->get['_route_'] ==  'hesap/duzenle') { $this->request->get['route'] =  'account/edit';
			} elseif ($this->request->get['_route_'] ==  'hesap/sifre') { $this->request->get['route'] =  'account/password';
			} elseif ($this->request->get['_route_'] ==  'hesap/facebook') { $this->request->get['route'] =  'module/facegiris';
			} elseif ($this->request->get['_route_'] ==  'hesap/adres') { $this->request->get['route'] =  'account/address';
			} elseif ($this->request->get['_route_'] ==  'hesap/adres/ekle') { $this->request->get['route'] =  'account/address/add';
			} elseif ($this->request->get['_route_'] ==  'hesap/sifremi-unuttum') { $this->request->get['route'] =  'account/forgotten';
			} elseif ($this->request->get['_route_'] ==  'hesap/maillist') { $this->request->get['route'] =  'account/newsletter';
            } elseif ($this->request->get['_route_'] ==  'hesap/siparis') { $this->request->get['route'] =  'account/order';
			} elseif ($this->request->get['_route_'] ==  'hesap/iade') { $this->request->get['route'] =  'account/return';
			} elseif ($this->request->get['_route_'] ==  'hesap/iade/ekle') { $this->request->get['route'] =  'account/return/add';
			} elseif ($this->request->get['_route_'] ==  'hesap/alim-satim') { $this->request->get['route'] =  'account/transaction';
			} elseif ($this->request->get['_route_'] ==  'hesap/kupon') { $this->request->get['route'] =  'account/kupon';
			} elseif ($this->request->get['_route_'] ==  'odeme/tamamlandi') { $this->request->get['route'] =  'checkout/success';
			}
			else {
				$this->request->get['route'] = 'error/not_found';
			}

			

					break;
				}
			}


			if (strpos($this->request->get['_route_'], 'tags/') !== false) {
					$this->request->get['route'] = 'product/search';
					$this->request->get['tag'] = str_replace('tags/','',$this->request->get['_route_']);
				}
			
			if (!isset($this->request->get['route'])) {
				if (isset($this->request->get['product_id'])) {
					$this->request->get['route'] = 'product/product';
				} elseif (isset($this->request->get['path'])) {
					$this->request->get['route'] = 'product/category';
				} elseif (isset($this->request->get['manufacturer_id'])) {
					$this->request->get['route'] = 'product/manufacturer/info';
				} elseif (isset($this->request->get['information_id'])) {
					$this->request->get['route'] = 'information/information';

			}
			}
			if (isset($this->request->get['route']) && $this->request->get['route'] == 'error/not_found') {if (true) {$this->db->query("insert into " . DB_PREFIX . "404s_report values ('".$this->db->escape($this->request->get['_route_'])."',now());");
				}
			}
		}
	}

	public function rewrite($link) {
		$url_info = parse_url(str_replace('&amp;', '&', $link));

		$url = '';

		$data = array();

		parse_str($url_info['query'], $data);

			$extendedseo = $this->config->get('extendedseo');
			if (isset($data['route']) && ($data['route'] == 'product/category') || ($data['route'] == 'product/manufacturer/info')) {$slash = true;}
			

		foreach ($data as $key => $value) {
			if (isset($data['route'])) {
				if (($data['route'] == 'product/product' && $key == 'product_id') || (($data['route'] == 'product/manufacturer/info' || $data['route'] == 'product/product') && $key == 'manufacturer_id') || ($data['route'] == 'information/information' && $key == 'information_id')) {
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

					if ($query->num_rows && $query->row['keyword']) {
						$url .= '/' . $query->row['keyword'];

						unset($data[$key]);
					}

			} elseif (isset($data['route']) && $data['route'] ==   'common/home') { $url .=  '/';
			} elseif (isset($data['route']) && $data['route'] ==   'account/wishlist' && $key != 'remove') { $url .=  '/alisveris-listem';
			} elseif (isset($data['route']) && $data['route'] ==   'information/contact') { $url .=  '/iletisim';
			} elseif (isset($data['route']) && $data['route'] ==   'account/account') { $url .=  '/hesap';
			} elseif (isset($data['route']) && $data['route'] ==   'information/sitemap') { $url .=  '/sitemap';
			} elseif (isset($data['route']) && $data['route'] ==   'product/manufacturer') { $url .=  '/marka';
			} elseif (isset($data['route']) && $data['route'] ==   'affiliate/account') { $url .=  '/affiliates';
			} elseif (isset($data['route']) && $data['route'] ==   'product/special' && $key != 'page' && $key != 'sort' && $key != 'limit' && $key != 'order') { $url .=  '/special';
			} elseif (isset($data['route']) && $data['route'] ==   'account/login') { $url .=  '/giris';
			} elseif (isset($data['route']) && $data['route'] ==   'account/logout') { $url .=  '/cikis';
			} elseif (isset($data['route']) && $data['route'] ==   'account/signup') { $url .=  '/kayit';
			} elseif (isset($data['route']) && $data['route'] ==   'checkout/cart') { $url .=  '/sepet';
		    } elseif (isset($data['route']) && $data['route'] ==   'checkout/checkout') { $url .=  '/odeme';
			} elseif (isset($data['route']) && $data['route'] ==   'account/edit') { $url .=  '/hesap/duzenle';
			} elseif (isset($data['route']) && $data['route'] ==   'account/password') { $url .=  '/hesap/sifre';
			} elseif (isset($data['route']) && $data['route'] ==   'module/facegiris') { $url .=  '/hesap/facebook';
			} elseif (isset($data['route']) && $data['route'] ==   'account/address') { $url .=  '/hesap/adres';
			} elseif (isset($data['route']) && $data['route'] ==   'account/address/add') { $url .=  '/hesap/adres/ekle';
			} elseif (isset($data['route']) && $data['route'] ==   'account/forgotten') { $url .=  '/hesap/sifremi-unuttum';
			} elseif (isset($data['route']) && $data['route'] ==   'account/newsletter') { $url .=  '/hesap/maillist';
			} elseif (isset($data['route']) && $data['route'] ==   'account/order') { $url .=  '/hesap/siparis';
		    } elseif (isset($data['route']) && $data['route'] ==   'account/transaction') { $url .=  '/hesap/alim-satim';
			} elseif (isset($data['route']) && $data['route'] ==   'account/kupon') { $url .=  '/hesap/kupon';
			} elseif (isset($data['route']) && $data['route'] ==   'checkout/success') { $url .=  '/odeme/tamamlandi';
			

			} elseif ($data['route'] == 'product/search' && (isset($extendedseo['seotags'])) && ($key == 'filter_tag' || $key == 'tag')) {
						$url .= '/tags/' . $value;
						unset($data[$key]);
			
				} elseif ($key == 'path') {
					$categories = explode('_', $value);

					foreach ($categories as $category) {
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = 'category_id=" . (int)$category . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

						if ($query->num_rows && $query->row['keyword']) {
							$url .= '/' . $query->row['keyword'];
						} else {
							$url = '';

							break;
						}
					}

					unset($data[$key]);
				}
			}
		}


			$seopagination = $this->config->get('seopagination');
			if (isset($key) && $key == 'page' && $url && (isset($seopagination['pagination']))) {
						$url .= '/page/' . $value;
						unset($data[$key]);
					}
			

			if (isset($slash)&& (isset($extendedseo['slash'])) && ($slash)) {$url = $url.'/'; $slash = false;}
			
		if ($url) {

				$squery = $this->db->query("SELECT `value` FROM `" . DB_PREFIX . "setting` WHERE `key` = 'config_language'");
				$mlseo = $this->config->get('mlseo');
				if (isset($this->session->data['language']) && (isset($mlseo['subfolder'])) && (strpos($link, 'language') == false) && (strpos($link, 'currency') == false) && ($this->session->data['language'] <> $squery->row['value']) && (strpos($link, 'blog') == false)) {$url = '/'.$this->session->data['language'].$url;}
			
			unset($data['route']);

			$query = '';

			if ($data) {
				foreach ($data as $key => $value) {
					$query .= '&' . rawurlencode((string)$key) . '=' . rawurlencode((is_array($value) ? http_build_query($value) : (string)$value));
				}

				if ($query) {
					$query = '?' . str_replace('&', '&amp;', trim($query, '&'));
				}
			}

			return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url . $query;
		} else {
			return $link;
		}
	}
}
