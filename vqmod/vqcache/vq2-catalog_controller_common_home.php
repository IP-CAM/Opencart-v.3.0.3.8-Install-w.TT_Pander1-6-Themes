<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
class ControllerCommonHome extends Controller {
	public function index() {
	   
        
		$this->document->setTitle($this->config->get('config_meta_title'));

				$canonicals = $this->config->get('canonicals');
				if (isset($canonicals['canonicals_home'])) {
					$this->document->addLink($this->config->get('config_url'), 'canonical');
					}
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		if (isset($this->request->get['route'])) {
			$this->document->addLink($this->config->get('config_url'), 'canonical');
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
        
		
		
        $this->load->model('catalog/review');
		
        $data['reviews'] = array();
		
		if (isset($this->request->get['page'])) {

			$page = $this->request->get['page'];

		} else {

			$page = 1;

		}
		
		$review_total = $this->model_catalog_review->getTotalReviewsByAllProduct();

		$pagination = new Pagination();

		$pagination->total = $review_total;

		$pagination->page = $page;

		$pagination->limit = 5;

		$pagination->url = $this->url->link('common/home', 'page={page}');

		

		$data['pagination'] = $pagination->render();
		
		$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($review_total - 5)) ? $review_total : ((($page - 1) * 5) + 5), $review_total, ceil($review_total / 5));
		
		
		$results = $this->model_catalog_review->getReviewsAllProduct(($page - 1) * 5, 5);
	
		
			foreach ($results as $result) {

			$data['reviews'][] = array(

				'author'     => $result['author'],

				'text'       => nl2br($result['text']),

				'rating'     => (int)$result['rating'],

				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))

			);

		}
		
		
		
		
		$this->response->setOutput($this->load->view('common/home', $data));
	}
}
