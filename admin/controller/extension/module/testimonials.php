<?php

class ControllerExtensionModuleTestimonials extends Controller {

    private $error = array();

    public function index() {
        
        // load language
        $this->language->load('extension/module/testimonials');
        // load model
        $this->load->model('setting/setting');
        $this->load->model('extension/testimonials/testimonials');
        //var_dump($this->model_extension_testimonials_testimonials->isModuleInstalled('testimonials'));exit;
        if ($this->model_extension_testimonials_testimonials->isModuleInstalled('testimonials') == NULL) {
            
            $this->session->data['error'] = $this->language->get('text_install_error');
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true));
        }
        
        $this->document->setTitle($this->language->get('heading_title'));
        $data['heading_title'] = $this->language->get('heading_title');
        
        $data['text_edit'] = $this->language->get('text_edit');
        
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
    
    
        //Save the settings if the user has submitted the admin form (ie if someone has pressed save).
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $post = $this->request->post;
            $post['testimonials_status'] = 1;
            // echo "<pre>";
            // print_r($post);exit;
            $this->model_extension_testimonials_testimonials->editSettings('testimonials', $post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true));
        }

        //This is how the language gets pulled through from the language file.
        //
        // If you want to use any extra language items - ie extra text on your admin page for any reason,
            // then just add an extra line to the $text_strings array with the name you want to call the extra text,
            // then add the same named item to the $_[] array in the language file.
            //
        // 'my_module_example' is added here as an example of how to add - see admin/language/english/module/my_module.php for the
        // other required part.
        $lang_keys = array(
            'heading_title',
            'tab_seo',
            'tab_basic',
            'tab_email',
            'tab_theme',
            'tab_slider',
            'entry_meta_title',
            'entry_meta_keywords',
            'entry_meta_desc',
            'entry_identifier',
            'entry_allow_guest',
            'entry_admin_approval',
            'entry_window_form',
            'entry_testimonials_limit',
            // new updates (17/4)
            'entry_admin_frontend_approval',
            'entry_admin_frontend_approval_tooltip_title',
        );

        foreach ($lang_keys as $lang) {
            $data[$lang] = $this->language->get($lang);
        }
        //END LANGUAGE
        //The following code pulls in the required data from either config files or user
        //submitted data (when the user presses save in admin). Add any extra config data
        // you want to store.
        //
        // NOTE: These must have the same names as the form data in your my_module.tpl file
        //
        $config_keys = array(
            'conf_form_window',
            'conf_admin_approval',
            'config_testimonials_limit',
            'conf_allow_guest',
            // below lines code added
            'conf_meta_desc',
            'conf_meta_keywords',
            'conf_meta_title',
            // new updates (17/4)
            'conf_admin_frontend_approval',
        );

        $modules = array();

        // commented and no more need of this code

        // if (isset($this->request->post['testimonial_desc'])) {
        //     $data['testimonial_desc'] = $this->request->post['testimonial_desc'];
        // } elseif ($this->config->get('testimonial_desc')) {
        //     $data['testimonial_desc'] = $this->config->get('testimonial_desc'); //echo '<pre>';print_r($data['modules']);exit;
        // }
        
        

        foreach ($config_keys as $conf) {
            if (isset($this->request->post[$conf])) {
                $modules[$conf] = $this->request->post[$conf];
            } else {
                $modules[$conf] = ($this->config->get($conf)) ? $this->config->get($conf) : '';
            }
        }
    
    
    
        $data['modules'] = $modules;
        // echo "<pre>";
        // print_r($data['modules']);
        // exit;
        
        //This creates an error message. The error['warning'] variable is set by the call to function validate() in this controller (below)
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        //SET UP BREADCRUMB TRAIL. YOU WILL NOT NEED TO MODIFY THIS UNLESS YOU CHANGE YOUR MODULE NAME.
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/testimonials', 'user_token=' . $this->session->data['user_token'], true),
            'separator' => ' :: '
        );

        $data['action'] = $this->url->link('extension/module/testimonials', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true);

        $data['user_token'] = $this->session->data['user_token'];
        
        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();
        
        // this will bring header, footer and menu
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

       $this->response->setOutput($this->load->view('extension/module/testimonials', $data));
    }

    public function install() {
        $this->load->model('extension/testimonials/testimonials');
        $this->model_extension_testimonials_testimonials->createTables();
        
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('testimonials', array('testimonials_status' => 1));
    }

    public function uninstall() {

        $this->load->model('extension/testimonials/testimonials');
        $this->model_extension_testimonials_testimonials->deleteTables();

        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('extendons_testimonials', array('testimonials_status' => 0));
    }
    /*
     * 
     * This function is called to ensure that the settings chosen by the admin user are allowed/valid.
     * You can add checks in here of your own.
     * 
     */

    private function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/testimonials/testimonials')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}


