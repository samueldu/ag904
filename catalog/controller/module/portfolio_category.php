<?php
	class ControllerModulePortfolioCategory extends Controller {
		public function index($setting) {
			$this->language->load('module/portfolio_category');
			
			$data['heading_title'] = $this->language->get('heading_title');
			
			$this->load->model('portfolio/article');
			
			$data['text_search_article'] = $this->language->get('text_search_article');
			$data['button_search'] = $this->language->get('button_search');
			
			if (isset($this->request->get['portfolio_category_id'])) {
				$parts = explode('_', (string)$this->request->get['portfolio_category_id']);
			} else {
				$parts = array();
			}
			
			if (isset($parts[0])) {
				$data['category_id'] = $parts[0];
			} else {
				$data['category_id'] = 0;
			}
			
			if (isset($parts[1])) {
				$data['child_id'] = $parts[1];
			} else {
				$data['child_id'] = 0;
			}
			
			$this->load->model('portfolio/article');
			
			$data['categories'] = array();
			
			$categories = $this->model_portfolio_article->getCategories(0);
			
			foreach ($categories as $category) {
				
				$children_data = array();
	
				$children = $this->model_portfolio_article->getCategories($category['portfolio_category_id']);
				
				foreach ($children as $child) {
					
					$article_total = $this->model_portfolio_article->getTotalArticles($child['portfolio_category_id']);
			
					$children_data[] = array(
						'category_id' => $child['portfolio_category_id'],
						'name'  => $child['name'],
						'href'  => $this->url->link('portfolio/category', 'portfolio_category_id=' . $category['portfolio_category_id'] . '_' . $child['portfolio_category_id'])
					);		
				}
	
				$data['categories'][] = array(
					'portfolio_category_id' => $category['portfolio_category_id'],
					'name'     => $category['name'],
					'children' => $children_data,
					'href'     => $this->url->link('portfolio/category', 'portfolio_category_id=' . $category['portfolio_category_id'])
				);
			}
			
            //print "<pre>"; print_r($data['categories']); die;
            
            if($this->config->has('portfolio_category_search_article')) {
                $data['portfolio_category_search_article'] = $this->config->get('portfolio_category_search_article');
            }
            
			//print "<pre>"; print_r($data['categories']); exit;
			if (isset($this->request->get['blog_search'])) {
				$data['blog_search'] = $this->request->get['blog_search'];
			} else {
				$data['blog_search'] = '';
			}
			
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/portfolio_category.tpl')) {
    			return $this->load->view($this->config->get('config_template') . '/template/module/portfolio_category.tpl', $data);
    		} else {
    			return $this->load->view('default/template/module/portfolio_category.tpl', $data);
    		}		
		}
	}
?>