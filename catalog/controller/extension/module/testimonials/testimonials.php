<?php
class ControllerExtensionModuleTestimonialsTestimonials extends Controller {

    private $error = array();

    public function index() {

        $this->language->load('extension/module/testimonials/testimonials');
        $this->load->model('extension/module/testimonials/testimonials');
        $this->load->model('tool/image');

        $lang_keys = array(
            'text_read_more',
            'text_image_manager',
            'text_browse',
            'text_clear',
            'button_avatar',
            'text_empty',
        );

        foreach ($lang_keys as $key) {
            $data[$key] = $this->language->get($key);
        }

        $grid_vars = array('page', 'limit');

        $page = 1;
        $limit = ($this->config->get('config_testimonials_limit')) ? $this->config->get('config_testimonials_limit') : 1;

        foreach ($grid_vars as $var) {
            if (isset($this->request->get[$var])) {
                ${$var} = $this->request->get[$var];
            }
        }

        $url = '';

        foreach ($grid_vars as $var) {
            if (isset($this->request->get[$var])) {
                $url .= "&{$var}=" . $this->request->get[$var];
            }
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );
        
        $data['breadcrumbs'][] = array( 
            'text'      => $this->language->get('text_testimonials'),
            'href'      => $this->url->link('extension/module/testimonials/testimonials')
        );
        
        $lang_id = $this->config->get('config_language_id');

        // $seo_config = $this->config->get('testimonial_desc');
        // $seo_info = array(
        //     'meta_title' => $seo_config['conf_meta_title'][$lang_id],
        //     'meta_desc' => $seo_config['conf_meta_desc'][$lang_id],
        //     'meta_keywords' => $seo_config['conf_meta_keywords'][$lang_id],
        //     //'identifier' => $seo_config['conf_identifier'][$lang_id],
        // );

        // above few lines code updated here below
        $seo_info = array(
            'meta_title' => $this->config->get('conf_meta_title'),
            'meta_desc' => $this->config->get('conf_meta_desc'),
            'meta_keywords' => $this->config->get('conf_meta_keywords')
        );

        $this->document->setTitle($seo_info['meta_title']);
        $this->document->setDescription($seo_info['meta_desc']);
        $this->document->setKeywords($seo_info['meta_keywords']);
        $this->document->addStyle('catalog/view/theme/' . $this->config->get('theme_default_directory') . '/stylesheet/testimonials/style.css');

        
        //$this->document->addLink($this->url->link('product/product', 'product_id=' . $this->request->get['product_id']), 'canonical');
        //$this->document->addScript('catalog/view/javascript/jquery/tabs.js');
        //$this->document->addScript('catalog/view/javascript/jquery/colorbox/jquery.colorbox-min.js');
        //$this->document->addStyle('catalog/view/javascript/jquery/colorbox/colorbox.css');

        $data['heading_title'] = $seo_info['meta_title'];

        $filter_data = array(
            'filters' => array(),
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        );

        $total = $this->model_extension_module_testimonials_testimonials->getTotal($filter_data);
        
        $testimonials = $this->model_extension_module_testimonials_testimonials->getTestimonials($filter_data);
        
        $data['testimonials'] = array();

        foreach ($testimonials as $row) {

            $defaultTheme = $this->config->get('config_theme');
            $size = ['_image_product_width','_image_product_height'];

            $width = $this->config->get('theme_'.$defaultTheme . $size[0]);
            $height = $this->config->get('theme_'.$defaultTheme . $size[1]);

            if ($row['avatar']) {
                $image = $this->model_tool_image->resize($row['avatar'], $width, $height);
            } else {
                $image = $this->model_tool_image->resize('no_image.png', 100, 100);
            }

            
            $data['testimonials'][] = array(
                'testimonials_id' => $row['testimonials_id'],
                'contact_name' => $row['contact_name'],
                'website' => $row['contact_website'],
                'short_desc' => strip_tags(html_entity_decode($row['short_desc'], ENT_QUOTES, 'UTF-8')),
                'testimonial_desc' => strip_tags(html_entity_decode($row['testimonial_desc'], ENT_QUOTES, 'UTF-8')),
                'avatar' => $image,
                'rating' => null,
                'href' => $this->url->link('extension/module/testimonials/testimonials/view', 'testimonials_id=' . $row['testimonials_id'] . $url)
            );
        }
        // echo "<pre>";
        // print_r($data['testimonials']);
        // exit();
        
        // new updates (17/4)
        $data['conf_admin_frontend_approval'] = $this->config->get('conf_admin_frontend_approval');
        $data['conf_admin_approval'] = $this->config->get('conf_admin_approval');
        $data['conf_allow_guest'] = $this->config->get('conf_allow_guest');
        $data['user_logged'] = $this->customer->isLogged();

        $data['limits'] = array();
        $page_limit = $this->config->get('config_testimonials_limit');
        $limits = array_unique(array($page_limit, 25, 50, 75, 100));

        sort($limits);

        foreach ($limits as $value) {
            $data['limits'][] = array(
                'text' => $value,
                'value' => $value,
                'href' => $this->url->link('extension/module/testimonials/testimonials', $url . '&limit=' . $value)
            );
        }

        $url = '';

        foreach ($grid_vars as $var) {
            if (isset($this->request->get[$var])) {
                $url .= "&{$var}=" . $this->request->get[$var];
            }
        }

        $pagination = new Pagination();
        $pagination->total = $total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->url = $this->url->link('extension/module/testimonials/testimonials', $url . '&page={page}');

        $data['pagination'] = $pagination->render();

        $data['pageresults'] = sprintf($this->language->get('text_pagination'), ($total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($total - $limit)) ? $total : ((($page - 1) * $limit) + $limit), $total, ceil($total / $limit));
        
        // $pagination->text = $this->language->get('text_pagination');
        // $pagination->url = $this->url->link('extension/module/testimonials/testimonials', $url . '&page={page}');

        // $data['pagination'] = $pagination->render();

        // $data['pageresults'] = sprintf($this->language->get('text_pagination'), ($total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($total - $limit)) ? $total : ((($page - 1) * $limit) + $limit), $total, ceil($total / $limit));


        $data['limit'] = $limit;

        
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
    
        if (file_exists(DIR_TEMPLATE . $this->config->get('theme_default_directory') . '/template/testimonials/testimonials.tpl')) {
            $this->response->setOutput($this->load->view('extension/module/testimonials/testimonials', $data));
        } else {
            $this->response->setOutput($this->load->view('extension/module/testimonials/testimonials', $data));
        }
    }

    public function view() {

        $this->language->load('extension/module/testimonials/testimonials');
        $this->load->model('extension/module/testimonials/testimonials');
        $this->load->model('tool/image');

        $testimonial_id = null;
        $identifier = null;
        $filter_data = array(
            'filters'
        );

        if (isset($this->request->get['testimonials_id'])) {
            $testimonial_id = (int)$this->request->get['testimonials_id'];;
            $filter_data['filters']['testimonials_id'] = $testimonial_id;
        } elseif (isset($this->request->get['identifier'])) {
            $identifier = $this->request->get['identifier'];
            $filter_data['filters']['identifier'] = $identifier;
        }

        $lang_id = $this->config->get('config_language_id');

        // $seo_config = $this->config->get('testimonial_desc');
        $seo_config = $this->config->get('conf_meta_title');
        $seo_info = array(
            // 'meta_title' => $seo_config['conf_meta_title'][$lang_id],
            'meta_title' => $seo_config,
        );

        $lang_keys = array(
            'text_read_more',
        );

        foreach ($lang_keys as $key) {
            $data[$key] = $this->language->get($key);
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $seo_info['meta_title'],
            'href' => $this->url->link('extension/module/testimonials/testimonials')
        );


        $info = $this->model_extension_module_testimonials_testimonials->getTestimonial($filter_data);

        $data['testimonials'] = array();

        if (empty($info)) {

            $this->_error(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY));
            return;
        }

        $data['breadcrumbs'][] = array(
            'text' => $info['contact_name'],
            'href' => $this->url->link('extension/module/testimonials/testimonials/view', '&testimonials_id=' . $testimonial_id)
        );
        
        $data['heading_title'] = $info['contact_name'];

        $this->document->setTitle(($info['meta_title'])? $info['meta_title']: $info['contact_name']);
        $this->document->setDescription(($info['meta_desc'])? $info['meta_desc']: $info['short_desc']);
        $this->document->setKeywords(($info['meta_keywords'])? $info['meta_keywords']: '');
        $this->document->addStyle('catalog/view/theme/' . $this->config->get('theme_default_directory') . '/stylesheet/testimonials/style.css');

        if ($info['avatar']) {
            $image = $this->model_tool_image->resize($info['avatar'], 100, 100);
        } else {
            $image = $this->model_tool_image->resize('no_image.png', 100, 100);
        }


        $data['testimonial'] = array(
            'testimonials_id' => $info['testimonials_id'],
            'contact_name' => $info['contact_name'],
            'website' => $info['contact_website'],
            'avatar' => $image,
            'contact_company' => $info['contact_company'],
            'testimonial_desc' => html_entity_decode($info['testimonial_desc'], ENT_QUOTES, 'UTF-8'), //strip_tags(html_entity_decode($info['testimonial_desc'], ENT_QUOTES, 'UTF-8')),
            'ratings' => null
        );

        // preg_replace is not working in twig, so we should place it here in controller
        $preg_replace = preg_replace('/<p[^>]*>(.*)<\/p[^>]*>/i', '$1', $data['testimonial']['testimonial_desc']);
        $data['preg_replace'] = $preg_replace;
        
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('theme_default_directory') . '/template/testimonials/testimonials_view.tpl')) {
            $this->response->setOutput($this->load->view('extension/module/testimonials/testimonials_view', $data));
        } else {
            $this->response->setOutput($this->load->view('extension/module/testimonials/testimonials_view', $data));
        }
    }

    protected function _error($query) {

        $this->language->load('extension/module/testimonials/testimonials');
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_error'),
            'href' => $this->url->link(str_replace('route=', '', $query)),
            'separator' => $this->language->get('text_separator')
        );

        $this->document->setTitle($this->language->get('text_error'));

        $data['heading_title'] = $this->language->get('text_error');

        $data['text_error'] = $this->language->get('text_error');

        $data['button_continue'] = $this->language->get('button_continue');

        $data['continue'] = $this->url->link('extension/module/testimonials/testimonials');

        $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . '/1.1 404 Not Found');

        if (file_exists(DIR_TEMPLATE . $this->config->get('theme_default_directory') . '/template/error/not_found.tpl')) {
            $this->template = $this->config->get('theme_default_directory') . '/template/error/not_found';
        } else {
            $this->template = 'default/template/error/not_found';
        }

        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }

    public function upload() {

        $this->language->load('product/product');
        $this->language->load('extension/module/testimonials/testimonials');

        $json = array();

        if (!empty($this->request->files['file']['name'])) {
            $filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8')));

            if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 64)) {
                $json['error'] = $this->language->get('error_filename');
            }

            // Allowed file extension types
            $allowed = array();

            $filetypes = explode("\n", $this->config->get('config_file_ext_allowed'));

            foreach ($filetypes as $filetype) {
                $allowed[] = trim($filetype);
            }

            if (!in_array(substr(strrchr($filename, '.'), 1), $allowed)) {
                $json['error'] = $this->language->get('error_filetype');
            }

            // Allowed file mime types      
            $allowed = array();

            $filetypes = explode("\n", $this->config->get('config_file_mime_allowed'));

            foreach ($filetypes as $filetype) {
                $allowed[] = trim($filetype);
            }

            if (!in_array($this->request->files['file']['type'], $allowed)) {
                $json['error'] = $this->language->get('error_filetype');
            }

            if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                $json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
            }
        } else {
            $json['error'] = $this->language->get('error_upload');
        }

        if (!$json && is_uploaded_file($this->request->files['file']['tmp_name']) && file_exists($this->request->files['file']['tmp_name'])) {
            $file = basename($filename);//. '.' . md5(mt_rand());

            // Hide the uploaded file name so people can not link to it directly.
            $json['file'] = $this->encryption->encrypt($file);

            move_uploaded_file($this->request->files['file']['tmp_name'], DIR_IMAGE . $file);

            $json['success'] = $this->language->get('text_upload');
        }

        $this->response->setOutput(json_encode($json));
    }

    public function add() {

        $this->language->load('extension/module/testimonials/testimonials');
        $this->load->model('extension/module/testimonials/testimonials');

        $post = $this->request->post;
        $file = $this->request->files;
        
        $response = array();
        
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->load->model('setting/store');

            $post['testimonials_store'] = array($this->config->get('config_store_id'));
            
            if (isset($file['image']['name']) && !empty($file['image']['name'])) {
                
                $img = $file['image'];

                $name = $img['name'];
                $type = $img['type'];
                $tmp_name = $img['tmp_name'];
                $size = $img['size'];
                $error = $img['error'];

                // valid extensions
                $valid_extensions = array('jpeg', 'jpg', 'png', 'gif', 'bmp');

                // upload directory
                $path = DIR_IMAGE.'catalog/';

                // Do some stuff with it here
                // $file_path = $path . "testimonials/";

                // if (!file_exists($file_path)) {
                //     mkdir($file_path);
                // }


                // get uploaded file's extension
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

                // check's valid format
                if(in_array($ext, $valid_extensions)) 
                { 
                    $path = $path.$name;
                    // echo $path;exit;
                    move_uploaded_file($tmp_name, $path);
                } else {
                    echo 'invalid';
                    exit();
                }
            }
        
            $getLastId = $this->model_extension_module_testimonials_testimonials->addTestimonial($post);
            
            if (isset($post['rating'])) {
                $this->rate($getLastId, $post['rating']);
            }

            // Admin alert
            $mail = new Mail($this->config->get('config_mail_engine'));
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
            $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

            $mail->setTo($this->config->get('config_email'));
            $mail->setFrom($this->request->post['contact_email']);
            $mail->setSender(html_entity_decode($this->request->post['contact_name'], ENT_QUOTES, 'UTF-8'));
            $mail->setSubject(html_entity_decode(sprintf($this->language->get('email_subject'), $this->request->post['contact_name']), ENT_QUOTES, 'UTF-8'));
            $mail->setText(strip_tags(html_entity_decode($this->request->post['testimonial_desc'], ENT_QUOTES, 'UTF-8')));
            $mail->send();

            // User alert
            $mail = new Mail($this->config->get('config_mail_engine'));
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
            $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

            $mail->setTo($this->request->post['contact_email']);
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender($this->language->get('email_sender_name'));
            $mail->setSubject(html_entity_decode(sprintf($this->language->get('email_subject'), $this->config->get('config_email')), ENT_QUOTES, 'UTF-8'));
            $mail->setText(strip_tags(html_entity_decode($this->language->get('send_email_text'), ENT_QUOTES, 'UTF-8')));
            $mail->send();
            
            $response['success'] = $this->language->get('text_success');

        } else {
            $response['error'] = implode(',', $this->error);
        }

        $this->response->setOutput(json_encode($response));
    }

    public function fetchRate($id = '') {

        if (isset($this->request->get['testimonials_id'])) {
            $id = $this->request->get['testimonials_id']; // rating data by testimonials_id
        }else{
            $id = $id;
        }

        $this->load->model('extension/module/testimonials/testimonials');

        $ratings = $this->model_extension_module_testimonials_testimonials->isRatingExist($id);

        $prep_json = array(
            'widget_id' => 'r' . $id,
            'number_votes' => isset($ratings['number_votes']) ? $ratings['number_votes'] : 0,
            'total_points' => isset($ratings['total_points']) ? $ratings['total_points'] : 0,
            'dec_avg' => isset($ratings['dec_avg']) ? $ratings['dec_avg'] : 0,
            'whole_avg' => isset($ratings['whole_avg']) ? $ratings['whole_avg'] : 0,
        );


        $this->response->setOutput(json_encode($prep_json));
    }

    public function rate($testimonial_id, $vote) {

        $this->load->model('extension/module/testimonials/testimonials');
        
        $id = $testimonial_id; // rating data by testimonials_id
        
        // $post = $this->request->post;

        // preg_match('/star_([1-5]{1})/', $post['clicked_on'], $match);
        // $vote = $match[1];
        // $id = $this->request->get['testimonials_id']; // rating data by testimonials_id

        $store_id = $this->config->get('config_store_id');
        
        if($this->customer->isLogged()) { 

            $session_id = $this->session->getId();

            $user_id = $this->customer->getId();
            
            $filter = array(
                'session_id' => $session_id,
                'user_id' => $user_id,
            );
            // if session against testimonial exist in rating
            $is_exist = (bool) $this->model_extension_module_testimonials_testimonials->isExistRatingByUser($id, $filter);
            if ($is_exist) {
                $this->fetchRate($id);
                // return error as the session, user id and testimonials id already exist in record against vote.
                return;
            }
            // make sure the keys are same as in the table (column names)
            $filter_data = array(
                'session_id' => (string)$session_id,
                'user_id' => (int)$user_id,
                'store_id' => (int)$store_id,
                'testimonials_id' => (int)$id,
                'visitor_ip' => $_SERVER['REMOTE_ADDR'],
                'votes' => $vote,
            );
        } else {
            $is_exist_ip = (bool) $this->model_extension_module_testimonials_testimonials->isExistRatingByIP($id, array('visitor_ip' => $_SERVER['REMOTE_ADDR']));
            if ($is_exist_ip) {
                $this->fetchRate($id);
                // return error as the session, user id and testimonials id already exist in record against vote.
                return;
            }
            // make sure the keys are same as in the table (column names)
            $filter_data = array(
                'store_id' => (int)$store_id,
                'testimonials_id' => (int)$id,
                'visitor_ip' => $_SERVER['REMOTE_ADDR'],
                'votes' => $vote,
            );
        }
        
        
        $this->model_extension_module_testimonials_testimonials->addRatings($filter_data);
        $this->fetchRate($id);
    }

    protected function validateForm() {

        if ((utf8_strlen($this->request->post['contact_name']) < 1) || (utf8_strlen($this->request->post['contact_name']) > 32)) {
            $this->error['contact_name'] = $this->language->get('error_contact_name');
        }

        // check duplicate
        $this->load->model('extension/module/testimonials/testimonials');

        // allow only letters
        if ( ctype_alpha(str_replace(' ', '', $this->request->post['contact_name'])) === false ) {
            $this->error['error_contact_name_str'] = $this->language->get('error_name_str');
        }

        if (isset($this->request->post['contact_email']) && !empty($this->request->post['contact_email'])) {
            if ((utf8_strlen($this->request->post['contact_email']) > 96) || !filter_var($this->request->post['contact_email'], FILTER_VALIDATE_EMAIL)) {
                $this->error['contact_email'] = $this->language->get('error_email_validate');
            }

            // check email duplication
            $email = $this->request->post['contact_email'];
            if ($this->model_extension_module_testimonials_testimonials->checkEmail_or_UrlDuplicate($email, null)) {
                $this->error['email_duplicate_error_var'] = $this->language->get('email_duplicate_error');
            }
        }

        if ((utf8_strlen($this->request->post['short_desc']) < 10) || (utf8_strlen($this->request->post['short_desc']) > 250)) {
            $this->error['short_desc'] = $this->language->get('error_short_desc_limit_reached');
        }

        if ((utf8_strlen($this->request->post['testimonial_desc']) < 10) || (utf8_strlen($this->request->post['testimonial_desc']) > 250)) {
            $this->error['testimonial_desc'] = $this->language->get('error_limit_reached');
        }

        // validate website url
        if (isset($this->request->post['contact_website']) && !empty($this->request->post['contact_website'])) {
            if (!filter_var($this->request->post['contact_website'], FILTER_VALIDATE_URL) === TRUE ) {
                $this->error['error_url_validate_var'] = $this->language->get('error_url_validate');
            }
            // check website url duplication
            $website_url = $this->request->post['contact_website'];
            if ($this->model_extension_module_testimonials_testimonials->checkEmail_or_UrlDuplicate(null, $website_url)) {
                $this->error['url_duplicate_error_var'] = $this->language->get('url_duplicate_error');
            }
        }

        // validate rating
        if (!isset($this->request->post['rating'])) {
            $this->error['error_rating_validate_var'] = $this->language->get('error_rating_validate');
        }
        
        // remove large spaces between words
        $this->request->post['contact_name'] = preg_replace('!\s+!', ' ', $this->request->post['contact_name']);
        $this->request->post['short_desc'] = preg_replace('!\s+!', ' ', $this->request->post['short_desc']);
        $this->request->post['testimonial_desc'] = preg_replace('!\s+!', ' ', $this->request->post['testimonial_desc']);

        //if (empty($this->session->data['captcha']) || ($this->session->data['captcha'] != $this->request->post['captcha'])) {
            //$this->error['captcha'] = $this->language->get('error_captcha');
        //}

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function captcha() {
        $this->load->library('captcha');

        $captcha = new Captcha();

        $this->session->data['captcha'] = $captcha->getCode();

        $captcha->showImage();
    }

}


