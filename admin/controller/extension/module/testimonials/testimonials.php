<?php
class ControllerExtensionModuleTestimonialsTestimonials extends Controller {

    private $error = array();

    public function index() {

        $this->language->load('extension/module/testimonials/testimonials');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/testimonials/testimonials');

        $this->_getList();
    }

    protected function _getList() {

        if (isset($this->request->get['filter_contact_name'])) {
            $filter_contact_name = $this->request->get['filter_contact_name'];
        } else {
            $filter_contact_name = null;
        }

        if (isset($this->request->get['filter_contact_email'])) {
            $filter_contact_email = $this->request->get['filter_contact_email'];
        } else {
            $filter_contact_email = null;
        }

        if (isset($this->request->get['filter_contact_company'])) {
            $filter_contact_company = $this->request->get['filter_contact_company'];
        } else {
            $filter_contact_company = null;
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = null;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 't.contact_name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_contact_name'])) {
            $url .= '&filter_contact_name=' . urlencode(html_entity_decode($this->request->get['filter_contact_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_contact_email'])) {
            $url .= '&filter_contact_email=' . urlencode(html_entity_decode($this->request->get['filter_contact_email'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_contact_company'])) {
            $url .= '&filter_contact_company=' . $this->request->get['filter_contact_company'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/testimonials/testimonials', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'),
            'separator' => ' :: '
        );

        $data['add'] = $this->url->link('extension/module/testimonials/testimonials/add', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');
        $data['delete'] = $this->url->link('extension/module/testimonials/testimonials/delete', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');

        $data['testimonials'] = array();

        $filter_data = array(
            'filters' => array(
                'contact_name' => $filter_contact_name,
                'contact_email' => $filter_contact_email,
                'contact_company' => $filter_contact_company,
                'curr_status' => $filter_status,
            ),
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $this->load->model('tool/image');

        $total = $this->model_extension_testimonials_testimonials->getTotal($filter_data);

        $results = $this->model_extension_testimonials_testimonials->getTestimonials($filter_data);

        foreach ($results as $row) {

            

            if ($row['avatar'] && file_exists(DIR_IMAGE . $row['avatar'])) {
                $image = $this->model_tool_image->resize($row['avatar'], 40, 40);
            } else {
                $image = $this->model_tool_image->resize('no_image.png', 40, 40);
            }

            $data['testimonials'][] = array(
                'testimonials_id' => $row['testimonials_id'],
                'contact_name' => $row['contact_name'],
                'contact_email' => $row['contact_email'],
                'contact_company' => $row['contact_company'],
                'short_desc' => strip_tags(html_entity_decode($row['short_desc'], ENT_QUOTES, 'UTF-8')),
                'sort_order' => $row['sort_order'],
                'curr_status' => ($row['curr_status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
                'selected' => isset($this->request->post['selected']) && in_array($row['testimonials_id'], $this->request->post['selected']),
                'avatar' => $image,
                'edit'   => $this->url->link('extension/module/testimonials/testimonials/edit', 'user_token=' . $this->session->data['user_token'] . '&testimonials_id=' . $row['testimonials_id'] . $url, 'SSL')
            );
        }
        
        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_list'] = $this->language->get('text_list');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['text_no_results'] = $this->language->get('text_no_results');
        
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        
        $data['column_image'] = $this->language->get('column_image');
        $data['column_contact_name'] = $this->language->get('column_contact_name');
        $data['column_contact_email'] = $this->language->get('column_contact_email');
        $data['column_contact_company'] = $this->language->get('column_contact_company');
        $data['column_short_desc'] = $this->language->get('column_short_desc');
        $data['column_sort_order'] = $this->language->get('column_sort_order');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_action'] = $this->language->get('column_action');
        
        $data['button_add'] = $this->language->get('button_add');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_filter'] = $this->language->get('button_filter');

        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        
        if (isset($this->request->post['selected'])) {
                $data['selected'] = (array)$this->request->post['selected'];
        } else {
                $data['selected'] = array();
        }
        
        $url = '';

        if (isset($this->request->get['filter_contact_name'])) {
            $url .= '&filter_contact_name=' . urlencode(html_entity_decode($this->request->get['filter_contact_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_contact_email'])) {
            $url .= '&filter_contact_email=' . urlencode(html_entity_decode($this->request->get['filter_contact_email'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_contact_company'])) {
            $url .= '&filter_contact_company=' . $this->request->get['filter_contact_company'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_contact_name'] = $this->url->link('extension/module/testimonials/testimonials', 'user_token=' . $this->session->data['user_token'] . '&sort=t.contact_name' . $url, 'SSL');
        $data['sort_contact_email'] = $this->url->link('extension/module/testimonials/testimonials', 'user_token=' . $this->session->data['user_token'] . '&sort=t.contact_email' . $url, 'SSL');
        $data['sort_contact_company'] = $this->url->link('extension/module/testimonials/testimonials', 'user_token=' . $this->session->data['user_token'] . '&sort=t.contact_company' . $url, 'SSL');
        $data['sort_status'] = $this->url->link('extension/module/testimonials/testimonials', 'user_token=' . $this->session->data['user_token'] . '&sort=t.curr_status' . $url, 'SSL');
        $data['sort_order'] = $this->url->link('extension/module/testimonials/testimonials', 'user_token=' . $this->session->data['user_token'] . '&sort=t.sort_order' . $url, 'SSL');

        $url = '';

        if (isset($this->request->get['filter_contact_name'])) {
            $url .= '&filter_contact_name=' . urlencode(html_entity_decode($this->request->get['filter_contact_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_contact_email'])) {
            $url .= '&filter_contact_email=' . urlencode(html_entity_decode($this->request->get['filter_contact_email'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_contact_company'])) {
            $url .= '&filter_contact_company=' . $this->request->get['filter_contact_company'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        // assign pagination
        $pagination = new Pagination();
        $pagination->total = $total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_admin_limit');
        $pagination->url = $this->url->link('extension/module/testimonials/testimonials', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['pageresults'] = sprintf($this->language->get('text_pagination'), ($total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($total - $this->config->get('config_limit_admin'))) ? $total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $total, ceil($total / $this->config->get('config_limit_admin')));


        $data['filter_contact_name'] = $filter_contact_name;
        $data['filter_contact_email'] = $filter_contact_email;
        $data['filter_contact_company'] = $filter_contact_company;
        $data['filter_status'] = $filter_status;

        $data['sort'] = $sort;
        $data['order'] = $order;
        // assign template
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/testimonials/grid', $data));
    }

    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_contact_name']) 
                || isset($this->request->get['filter_contact_email']) 
                || isset($this->request->get['filter_testimonials_id']) 
                || isset($this->request->get['filter_contact_company']) ) {
            
            $this->load->model('extension/testimonials/testimonials');


            if (isset($this->request->get['filter_contact_name'])) {
                $filter_contact_name = $this->request->get['filter_contact_name'];
            } else {
                $filter_contact_name = '';
            }

            if (isset($this->request->get['filter_contact_company'])) {
                $filter_contact_company = $this->request->get['filter_contact_company'];
            } else {
                $filter_contact_company = '';
            }
            
            if (isset($this->request->get['filter_contact_email'])) {
                $filter_contact_email = $this->request->get['filter_contact_email'];
            } else {
                $filter_contact_email = '';
            }

            if (isset($this->request->get['limit'])) {
                $limit = $this->request->get['limit'];
            } else {
                $limit = 20;
            }

            $data = array(
                'filter_contact_name' => $filter_contact_name,
                'filter_contact_email' => $filter_contact_email,
                'filter_contact_company' => $filter_contact_company,
                'start' => 0,
                'limit' => $limit
            );

            $results = $this->model_extension_testimonials_testimonials->getTestimonials($data);

            foreach ($results as $result) {

                $json[] = array(
                    'testimonials_id' => $result['testimonials_id'],
                    'contact_name' => strip_tags(html_entity_decode($result['contact_name'], ENT_QUOTES, 'UTF-8')),
                    'contact_email' => $result['contact_email'],
                    'contact_company' => $result['contact_company'],
                );
            }
        }

        $this->response->setOutput(json_encode($json));
    }

    public function add() {

        $this->language->load('extension/module/testimonials/testimonials');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/testimonials/testimonials');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            
            $getLastId = $this->model_extension_testimonials_testimonials->addTestimonial($this->request->post);

            // save rating against this testimonial
            if (isset($this->request->post['rating'])) {
                $this->rate($getLastId, $this->request->post['rating']);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/module/testimonials/testimonials', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function edit() {
        $this->language->load('extension/module/testimonials/testimonials');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/testimonials/testimonials');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            
            $id = $this->request->get['testimonials_id'];
            $data = $this->request->post;

            $this->model_extension_testimonials_testimonials->editTestimonials($id, $data);

            // save rating against this testimonial
            if (isset($data['rating'])) {
                $this->rate($id, $data['rating']);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_contact_name'])) {
                $url .= '&filter_contact_name=' . urlencode(html_entity_decode($this->request->get['filter_contact_name'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_contact_email'])) {
                $url .= '&filter_contact_email=' . urlencode(html_entity_decode($this->request->get['filter_contact_email'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_contact_company'])) {
                $url .= '&filter_contact_company=' . $this->request->get['filter_contact_company'];
            }

            if (isset($this->request->get['filter_quantity'])) {
                $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/module/testimonials/testimonials', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
        }

        $this->getForm();
    }
    
    public function delete() {
        $this->language->load('extension/module/testimonials/testimonials');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/testimonials/testimonials');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $testimonials_id) {
                $this->model_extension_testimonials_testimonials->delete($testimonials_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_contact_name'])) {
                $url .= '&filter_contact_name=' . urlencode(html_entity_decode($this->request->get['filter_contact_name'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_contact_email'])) {
                $url .= '&filter_contact_email=' . urlencode(html_entity_decode($this->request->get['filter_contact_email'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_contact_company'])) {
                $url .= '&filter_contact_company=' . $this->request->get['filter_contact_company'];
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/module/testimonials/testimonials', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
        }

        $this->_getList();
    }
    
    public function getForm() {

        $lang_keys = array(
            'heading_title',
            'tab_general',
            'tab_seo',
            'tab_store',
            'tab_design',
            'button_save',
            'button_cancel',
            'entry_identifier',
            'entry_mark_featured',
            'entry_contact_name',
            'entry_contact_email',
            'entry_contact_company',
            'entry_contact_website',
            'entry_avatar',
            'entry_sort_order',
            'entry_curr_status',
            'entry_short_desc',
            'entry_testimonial_desc',
            'entry_store',
            'entry_website',
            'entry_meta_title',
            'entry_meta_desc',
            'entry_meta_keywords',
            'entry_layout',
            'text_default',
            'text_browse',
            'text_clear',
            'text_image_manager',
            'text_enabled',
            'text_disabled',
            'rating_text',
            'good_text',
            'bad_text',
        );

        foreach ($lang_keys as $lkey) {
            $data[$lkey] = $this->language->get($lkey);
        }

        // Errors
        foreach ($this->error as $k => $v) {

            $data[$k] = $v;
        }
        
        $data['text_form'] = !isset($this->request->get['testimonials_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $data['note_url_format'] = $this->language->get('note_url_format') ;
        $data['note_numeric_values'] = $this->language->get('note_numeric_values') ;
        $data['note_min_length'] = $this->language->get('note_min_length') ;
        
        if (isset($this->error['warning'])) {
                $data['error_warning'] = $this->error['warning'];
        } else {
                $data['error_warning'] = '';
        }

        $url = '';

        if (isset($this->request->get['filter_contact_name'])) {
            $url .= '&filter_contact_name=' . urlencode(html_entity_decode($this->request->get['filter_contact_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_contact_email'])) {
            $url .= '&filter_contact_email=' . urlencode(html_entity_decode($this->request->get['filter_contact_email'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_contact_company'])) {
            $url .= '&filter_contact_company=' . $this->request->get['filter_contact_company'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/testimonials/testimonials', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => ' :: '
        );

        if (!isset($this->request->get['testimonials_id'])) {
            $data['action'] = $this->url->link('extension/module/testimonials/testimonials/add', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');
        } else {
            $data['action'] = $this->url->link('extension/module/testimonials/testimonials/edit', 'user_token=' . $this->session->data['user_token'] . '&testimonials_id=' . $this->request->get['testimonials_id'] . $url, 'SSL');
        }

        $data['cancel'] = $this->url->link('extension/module/testimonials/testimonials', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');

        if (isset($this->request->post['rating'])) {
                $data['dec_avg'] = $this->request->post['rating'];
        } else {    
                $data['dec_avg'] = '';
        }
  
        $info = array(); 
        if (!empty($this->request->post)) {
            
            $info = $this->request->post;
        } elseif (isset($this->request->get['testimonials_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) { 

            $info = $this->model_extension_testimonials_testimonials->getTestimonial($this->request->get['testimonials_id']);

            // fetch rating against this testimonial
            $rating = $this->fetchRate($this->request->get['testimonials_id']);
            if ($rating) {
                foreach ($rating as $key => $value) {
                    $data[$key] = $value;
                }
            } 
        }

        $data['testimonial'] = $info;

        $data['seo'] = array(
            'meta_title' => isset($info['meta_title'])? $info['meta_title']: '',
            'meta_desc' => isset($info['meta_desc'])? $info['meta_desc']: '',
            'meta_keywords' => isset($info['meta_keywords'])? $info['meta_keywords']: '',
        );
        
        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('setting/store');

        $data['stores'] = $this->model_setting_store->getStores();

        if (isset($this->request->post['testimonials_store'])) {
            $data['testimonials_store'] = $this->request->post['testimonials_store'];
        } elseif (isset($this->request->get['testimonials_id'])) {
            $data['testimonials_store'] = $this->model_extension_testimonials_testimonials->getStores($this->request->get['testimonials_id']);
        } else {
            $data['testimonials_store'] = array(0);
        }

        if (isset($this->request->post['avatar'])) {
            $data['avatar'] = $this->request->post['avatar'];
        } elseif (!empty($info)) {
            $data['avatar'] = $info['avatar'];
        } else {
            
            $data['avatar'] = '';
        }
        
        $this->load->model('tool/image');

        if (isset($this->request->post['avatar']) && file_exists(DIR_IMAGE . $this->request->post['avatar'])) {
            $data['thumb'] = $this->model_tool_image->resize($this->request->post['avatar'], 100, 100);
        } elseif (!empty($info) && $info['avatar'] && file_exists(DIR_IMAGE . $info['avatar'])) {
            $data['thumb'] = $this->model_tool_image->resize($info['avatar'], 100, 100);
        } else {
            $data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }
        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        
        
        if (isset($this->request->post['module_layout'])) {
            $data['module_layout'] = $this->request->post['module_layout'];
        } elseif (isset($this->request->get['testimonials_id'])) {
            $data['module_layout'] = $this->model_extension_testimonials_testimonials->getModuleLayouts($this->request->get['testimonials_id']);
        } else {
            $data['module_layout'] = array();
        }

        // echo "<pre>";
        // print_r($data);exit();

        $this->load->model('design/layout');

        $data['layouts'] = $this->model_design_layout->getLayouts();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/testimonials/testimonials_form', $data));
    }

    protected function validateForm() {

        if (!$this->user->hasPermission('modify', 'extension/module/testimonials/testimonials')) {
            $this->error['error_warning'] = $this->language->get('error_permission');
        }

        // check name length
        if (strlen($this->request->post['contact_name']) < 3) {
            $this->error['error_contact_name'] = $this->language->get('error_name');
        }

        // allow only letters
        if ( ctype_alpha(str_replace(' ', '', $this->request->post['contact_name'])) === false ) {
            $this->error['error_contact_name_str'] = $this->language->get('error_name_str');
        }
        
        // remove large spaces between words
        $this->request->post['contact_name'] = preg_replace('!\s+!', ' ', $this->request->post['contact_name']);
        
        // check short desc length
        if (strlen($this->request->post['short_desc']) > 255) {
            $this->error['error_limit_reached_var'] = $this->language->get('error_limit_reached');
        }

        // check duplicate
        $this->load->model('extension/testimonials/testimonials');

        $identifier = $this->request->post['identifier'];
        $testimonials_id = null;
        $primary_id = null;
        if (isset($this->request->get['testimonials_id'])) {
            $testimonials_id = $this->request->get['testimonials_id'];
            $primary_id = 'testimonials_id';
        }

        // check for duplicate identifier
        if ($identifier != '') {
            if ($this->model_extension_testimonials_testimonials->checkDuplicate($identifier, 'extendons_testimonials', 'identifier', $primary_id, $testimonials_id)) {
                $this->error['text_duplicate_error_var'] = $this->language->get('text_duplicate_error');
            }
        } else {
            $this->error['text_duplicate_error_var'] = $this->language->get('error_empty');
        }

        // sort order validate numerical
        if (isset($this->request->post['sort_order']) && is_numeric($this->request->post['sort_order']) == FALSE) {
            $this->error['error_int_validate_var'] = $this->language->get('error_int_validate');
        }

        // Email        
        $email = isset($this->request->post['contact_email']) ? $this->request->post['contact_email'] : '';
        if ($email != '') {
            if ((utf8_strlen($email) > 96) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->error['error_email_validate_var'] = $this->language->get('error_email_validate');
            }
            
            if (!isset($this->request->get['testimonials_id'])) {
                if ($this->model_extension_testimonials_testimonials->checkEmail_or_UrlDuplicate($email, null)) {
                    $this->error['email_duplicate_error_var'] = $this->language->get('email_duplicate_error');
                }
            }
        }

        // validate website url
        if (isset($this->request->post['contact_website']) && !empty($this->request->post['contact_website'])) {
            if (!filter_var($this->request->post['contact_website'], FILTER_VALIDATE_URL) === TRUE ) {
                $this->error['error_url_validate_var'] = $this->language->get('error_url_validate');
            }

            $website_url = $this->request->post['contact_website'];
            if (!isset($this->request->get['testimonials_id'])) {
                if ($this->model_extension_testimonials_testimonials->checkEmail_or_UrlDuplicate(null, $website_url)) {
                    $this->error['url_duplicate_error_var'] = $this->language->get('url_duplicate_error');
                }
            }
        }

        // validate rating
        if (!isset($this->request->post['rating'])) {
            $this->error['error_rating_validate_var'] = $this->language->get('error_rating_validate');
        }

        // echo "<pre>";print_r($this->error);exit;
        // warnings
        if ($this->error && !isset($this->error['error_warning'])) {
            $this->error['error_warning'] = $this->language->get('error_warning');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'extension/module/testimonials/testimonials')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }


    public function fetchRate($id = '') {
        
        if (isset($this->request->get['testimonials_id'])) {
            $id = $this->request->get['testimonials_id']; // rating data by testimonials_id
        }else{
            $id = $id;
        }

        $this->load->model('extension/testimonials/testimonials');

        $ratings = $this->model_extension_testimonials_testimonials->isRatingExist($id);
        return $ratings;
        
    }

    public function rate($testimonial_id, $vote) {

        $this->load->model('extension/testimonials/testimonials');
        
        $id = $testimonial_id; // rating data by testimonials_id

        $store_id = $this->config->get('config_store_id');

        $session_id = $this->session->getId();
        $user_id = $this->user->getId();
        
        $filter = array(
            'session_id' => $session_id,
            'user_id' => $user_id,
        );
        
        // make sure the keys are same as in the table (column names)
        $filter_data = array(
            'session_id' => (string)$session_id,
            'user_id' => (int)$user_id,
            'store_id' => (int)$store_id,
            'testimonials_id' => (int)$id,
            'visitor_ip' => $_SERVER['REMOTE_ADDR'],
            'votes' => $vote,
        );
        
        // if session against testimonial exist in rating
        $is_exist = (bool) $this->model_extension_testimonials_testimonials->isExistRatingByUser($id);

        if ($is_exist) {

            $this->model_extension_testimonials_testimonials->updateRatings($filter_data);

            return;

        } else {

            $this->model_extension_testimonials_testimonials->addRatings($filter_data);

        }
        
    }

}



