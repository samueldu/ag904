<?php
	class ModelportfolioArticle extends Model {
		
		public function addArticle($data) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_article` SET portfolio_author_id='" . (int)$data['portfolio_author_id'] . "', allow_comment='" . (int)$data['allow_comment'] . "', sort_order='" . (int)$data['sort_order'] . "', status='" . (int)$data['status'] . "', date_added=NOW(), date_modified=NOW(), gallery_id='".$data['gallery_id'] . "',url='".$data['url'] . "'");
			
			$portfolio_article_id = $this->db->getLastId();
			
			if (isset($data['image'])) {
				$this->db->query("UPDATE `" . DB_PREFIX . "portfolio_article` SET image = '" . $this->db->escape($data['image']) . "' WHERE portfolio_article_id = '" . (int)$portfolio_article_id . "'");
			}
			
			if (isset($data['featured_image'])) {
				$this->db->query("UPDATE `" . DB_PREFIX . "portfolio_article` SET featured_image = '" . $this->db->escape($data['featured_image']) . "' WHERE portfolio_article_id = '" . (int)$portfolio_article_id . "'");
			}
			
			if ($data['keyword']) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "url_alias` SET query = 'portfolio_article_id=" . (int)$portfolio_article_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
			}
			
			if(isset($data['article_description']) and is_array($data['article_description']))
			foreach ($data['article_description'] as $language_id => $value) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_article_description`
				SET portfolio_article_id = '" . (int)$portfolio_article_id . "',
				language_id = '" . (int)$language_id . "',
				sub_title = '" . $this->db->escape($value['sub_title']) . "',
				short_description = '" . $this->db->escape($value['short_description']) . "',
				article_title = '" . $this->db->escape($value['article_title']) . "',
				description = '" . $this->db->escape($value['description']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
			}

            if(isset($data['article_addition_description'])  and is_array($data['article_addition_description']))
			foreach($data['article_addition_description'] as $key => $additional_value) {
				foreach($additional_value as $val_key => $value) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_article_description_additional`
					SET portfolio_article_id='" . (int)$portfolio_article_id . "',
					language_id='" . (int)$val_key . "',
					additional_description='" . $this->db->escape($value['additional']) . "',
					title='" . $this->db->escape($value['title']) . "',
					sort_order='" . $this->db->escape($value['sort_order']) . "',
					sub_title='" . $this->db->escape($value['sub_title']) . "'");
				}
			}
			
			// adding article category
			if (isset($data['article_category'])) {
				foreach ($data['article_category'] as $category_id) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_article_to_category` SET portfolio_article_id = '" . (int)$portfolio_article_id . "', portfolio_category_id = '" . (int)$category_id . "'");
				}
			}

			// adding article store
			if (isset($data['article_store'])) {
				foreach ($data['article_store'] as $store_id) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_article_to_store` SET portfolio_article_id = '" . (int)$portfolio_article_id . "', store_id = '" . (int)$store_id . "'");
				}
			}
			
			// adding layout
			if (isset($data['article_layout'])) {
				foreach ($data['article_layout'] as $store_id => $layout) {
					if ($layout['layout_id']) {
						$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_article_to_layout` SET portfolio_article_id = '" . (int)$portfolio_article_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout['layout_id'] . "'");
					}
				}
			}
			
			// now adding related product for article
			if($data['related_article'] == 'category_wise') {
				//$product_list = $this->getProductCategoryWise($data['category_wise']);
				if(isset($data['category_wise'])) {
					$option = array();
					
					$option['category_wise'] = $data['category_wise'];
					
					$options = serialize($option);
					
					$product_list = $this->getProductCategoryWise($data['category_wise']);
					
					foreach($product_list as $product_id) {
						$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_article_product_related` SET portfolio_article_id='" . (int)$portfolio_article_id . "', product_id='" . (int)$product_id . "'");
					}
					
					$this->db->query("UPDATE `" . DB_PREFIX . "portfolio_article` SET article_related_method='" . $this->db->escape($data['related_article']) . "', article_related_option='" . $this->db->escape($options) . "' WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
				} else {
					$this->db->query("UPDATE `" . DB_PREFIX . "portfolio_article` SET article_related_method='" . $this->db->escape($data['related_article']) . "', article_related_option='' WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
				}			
			} else if($data['related_article'] == 'manufacturer_wise') {
				if(isset($data['manufacturer_wise'])) {					
					$option = array();
					
					$option['manufacturer_wise'] = $data['manufacturer_wise'];
					
					$options = serialize($option);
					
					$product_list = $this->getProductManufacturerWise($data['manufacturer_wise']);
					
					foreach($product_list as $product_id) {
						$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_article_product_related` SET portfolio_article_id='" . (int)$portfolio_article_id . "', product_id='" . (int)$product_id . "'");
					}

					$this->db->query("UPDATE `" . DB_PREFIX . "portfolio_article` SET article_related_method='" . $this->db->escape($data['related_article']) . "', article_related_option='" . $this->db->escape($options) . "' WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
				} else {
					$this->db->query("UPDATE `" . DB_PREFIX . "portfolio_article` SET article_related_method='" . $this->db->escape($data['related_article']) . "', article_related_option='' WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
				}
			} else {
				if(isset($data['product_wise'])) {
					foreach($data['product_wise'] as $product_id) {
						$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_article_product_related` SET portfolio_article_id='" . (int)$portfolio_article_id . "', product_id='" . (int)$product_id . "'");
					}					
					$this->db->query("UPDATE `" . DB_PREFIX . "portfolio_article` SET article_related_method='" . $this->db->escape($data['related_article']) . "', article_related_option='' WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
				} else {
					$this->db->query("UPDATE `" . DB_PREFIX . "portfolio_article` SET article_related_method='" . $this->db->escape($data['related_article']) . "', article_related_option='' WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
				}
			}	
			
			// insert related articles
			if(isset($this->request->post['blog_related_articles'])) {
				foreach($this->request->post['blog_related_articles'] as $related_article) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_related_article` SET portfolio_article_id='" . (int)$portfolio_article_id . "', portfolio_article_related_id='" . (int)$related_article['portfolio_article_related_id'] . "', sort_order='" . (int)$related_article['sort_order'] . "', status='" . (int)$related_article['status'] . "', date_added=NOW()");
				}
			}
							
		}
		
		public function editArticle($portfolio_article_id, $data) {
			$this->db->query("UPDATE `" . DB_PREFIX . "portfolio_article` SET portfolio_author_id='" . (int)$data['portfolio_author_id'] . "', allow_comment='" . (int)$data['allow_comment'] . "', sort_order='" . (int)$data['sort_order'] . "', status='" . (int)$data['status'] . "', date_modified=NOW(), gallery_id= '".$data['gallery_id'] . "',url= '".$data['url'] . "' WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
			
			if (isset($data['image'])) {
				$this->db->query("UPDATE `" . DB_PREFIX . "portfolio_article` SET image = '" . $this->db->escape($data['image']) . "' WHERE portfolio_article_id = '" . (int)$portfolio_article_id . "'");
			}
			
			if (isset($data['featured_image'])) {
				$this->db->query("UPDATE `" . DB_PREFIX . "portfolio_article` SET featured_image = '" . $this->db->escape($data['featured_image']) . "' WHERE portfolio_article_id = '" . (int)$portfolio_article_id . "'");
			}
			
			$this->db->query("DELETE FROM `" . DB_PREFIX . "url_alias` WHERE query = 'portfolio_article_id=" . (int)$portfolio_article_id. "'");
		
			if ($data['keyword']) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "url_alias` SET query = 'portfolio_article_id=" . (int)$portfolio_article_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
			}
			
			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_article_description` WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
			
			// adding article description
			foreach ($data['article_description'] as $language_id => $value) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_article_description`
				SET portfolio_article_id = '" . (int)$portfolio_article_id . "',
				language_id = '" . (int)$language_id . "',
				article_title = '" . $this->db->escape($value['article_title']) . "',
			    sub_title = '" . $this->db->escape($value['sub_title']) . "',
				description = '" . $this->db->escape($value['description']) . "',
				short_description = '" . $this->db->escape($value['short_description']) . "',
				meta_description = '" . $this->db->escape($value['meta_description']) . "',
				meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
			}


			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_article_description_additional` WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
			
			// adding article additional description
			if(isset($data['article_addition_description']))
			foreach($data['article_addition_description'] as $key => $additional_value) {
				foreach($additional_value as $val_key => $value) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_article_description_additional`
					SET portfolio_article_id='" . (int)$portfolio_article_id . "',
					language_id='" . (int)$val_key . "',
					additional_description='" . $this->db->escape($value['additional']) . "',
					title='" . $this->db->escape($value['title']) . "',
					sort_order='" . $this->db->escape($value['sort_order']) . "',
					sub_title='" . $this->db->escape($value['sub_title']) . "'");
				}
			}
			
			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_article_to_category` WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
			
			// adding article category
			if (isset($data['article_category'])) {
				foreach ($data['article_category'] as $category_id) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_article_to_category` SET portfolio_article_id = '" . (int)$portfolio_article_id . "', portfolio_category_id = '" . (int)$category_id . "'");
				}
			}
			
			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_article_to_store` WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
			
			// adding article store
			if (isset($data['article_store'])) {
				foreach ($data['article_store'] as $store_id) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_article_to_store` SET portfolio_article_id = '" . (int)$portfolio_article_id . "', store_id = '" . (int)$store_id . "'");
				}
			}
			
			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_article_to_layout` WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
			
			if (isset($data['article_layout'])) {
				foreach ($data['article_layout'] as $store_id => $layout) {
					if ($layout['layout_id']) {
						$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_article_to_layout` SET portfolio_article_id = '" . (int)$portfolio_article_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout['layout_id'] . "'");
					}
				}
			}	
			
			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_article_product_related` WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
			
			// now adding related product for article
			if($data['related_article'] == 'category_wise') {
				//$product_list = $this->getProductCategoryWise($data['category_wise']);
				if(isset($data['category_wise'])) {
					$option = array();
					
					$option['category_wise'] = $data['category_wise'];
					
					$options = serialize($option);
					
					$product_list = $this->getProductCategoryWise($data['category_wise']);
					
					foreach($product_list as $product_id) {
						$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_article_product_related` SET portfolio_article_id='" . (int)$portfolio_article_id . "', product_id='" . (int)$product_id . "'");
					}
					
					$this->db->query("UPDATE `" . DB_PREFIX . "portfolio_article` SET article_related_method='" . $this->db->escape($data['related_article']) . "', article_related_option='" . $this->db->escape($options) . "' WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
				} else {
					$this->db->query("UPDATE `" . DB_PREFIX . "portfolio_article` SET article_related_method='" . $this->db->escape($data['related_article']) . "', article_related_option='' WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
				}			
			} else if($data['related_article'] == 'manufacturer_wise') {
				if(isset($data['manufacturer_wise'])) {					
					$option = array();
					
					$option['manufacturer_wise'] = $data['manufacturer_wise'];
					
					$options = serialize($option);
					
					$product_list = $this->getProductManufacturerWise($data['manufacturer_wise']);
					
					foreach($product_list as $product_id) {
						$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_article_product_related` SET portfolio_article_id='" . (int)$portfolio_article_id . "', product_id='" . (int)$product_id . "'");
					}

					$this->db->query("UPDATE `" . DB_PREFIX . "portfolio_article` SET article_related_method='" . $this->db->escape($data['related_article']) . "', article_related_option='" . $this->db->escape($options) . "' WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
				} else {
					$this->db->query("UPDATE `" . DB_PREFIX . "portfolio_article` SET article_related_method='" . $this->db->escape($data['related_article']) . "', article_related_option='' WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
				}
			} else {
				if(isset($data['product_wise'])) {
					foreach($data['product_wise'] as $product_id) {
						$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_article_product_related` SET portfolio_article_id='" . (int)$portfolio_article_id . "', product_id='" . (int)$product_id . "'");
					}					
					$this->db->query("UPDATE `" . DB_PREFIX . "portfolio_article` SET article_related_method='" . $this->db->escape($data['related_article']) . "', article_related_option='' WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
				} else {
					$this->db->query("UPDATE `" . DB_PREFIX . "portfolio_article` SET article_related_method='" . $this->db->escape($data['related_article']) . "', article_related_option='' WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
				}
			}		
			
			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_related_article` WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
			
			// insert related articles
			if(isset($this->request->post['blog_related_articles'])) {
				foreach($this->request->post['blog_related_articles'] as $related_article) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_related_article` SET portfolio_article_id='" . (int)$portfolio_article_id . "', portfolio_article_related_id='" . (int)$related_article['portfolio_article_related_id'] . "', sort_order='" . (int)$related_article['sort_order'] . "', status='" . (int)$related_article['status'] . "', date_added=NOW()");
				}
			}					
		}
		
		public function deleteArticle($portfolio_article_id) {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_article` WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
			$this->db->query("DELETE FROM `" . DB_PREFIX . "url_alias` WHERE query = 'portfolio_article_id=" . (int)$portfolio_article_id. "'");
			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_article_description` WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_article_description_additional` WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_article_to_category` WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_article_to_store` WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_article_to_layout` WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_article_product_related` WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_related_article` WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_view` WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
		}
		
		public function checkDeleteArticle($portfolio_article_id) {
			$sql = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_related_article` WHERE portfolio_article_related_id='" . (int)$portfolio_article_id . "'");
			
			return $sql->num_rows;
		}
		
		public function getArticle($portfolio_article_id) {
			$query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'portfolio_article_id=" . (int)$portfolio_article_id . "') AS keyword FROM `" . DB_PREFIX . "portfolio_article` sba LEFT JOIN `" . DB_PREFIX . "portfolio_article_description` sbad ON (sba.portfolio_article_id = sbad.portfolio_article_id) WHERE sba.portfolio_article_id = '" . (int)$portfolio_article_id . "' AND sbad.language_id = '" . (int)$this->config->get('config_language_id') . "'");
			return $query->row;
		}		
		
		public function getTotalArticle($data = array()) {
			$sql = "SELECT COUNT(DISTINCT(sba.portfolio_article_id)) AS total FROM `" . DB_PREFIX . "portfolio_article` sba LEFT JOIN `" . DB_PREFIX . "portfolio_article_description` sbad ON(sba.portfolio_article_id=sbad.portfolio_article_id) LEFT JOIN `" . DB_PREFIX . "portfolio_author` sbau ON(sba.portfolio_author_id = sbau.portfolio_author_id) WHERE sbad.language_id='" . (int)$this->config->get('config_language_id'). "'";
			
			$query = $this->db->query($sql);
			
			return $query->row['total'];
		}
		
		public function getArticles($data = array()) {
			$sql = "SELECT sba.*, sbau.name AS author_name, sbad.article_title AS article_title FROM `" . DB_PREFIX . "portfolio_article` sba LEFT JOIN `" . DB_PREFIX . "portfolio_article_description` sbad ON(sba.portfolio_article_id=sbad.portfolio_article_id) LEFT JOIN `" . DB_PREFIX . "portfolio_author` sbau ON(sba.portfolio_author_id = sbau.portfolio_author_id) WHERE sbad.language_id='" . (int)$this->config->get('config_language_id'). "'";
			
			if(isset($data['filter_article']) && !empty($data['filter_article'])) {
				$sql .= " AND sbad.article_title LIKE '" . $this->db->escape($data['filter_article']) . "%'";
			}
			
			$sort_data = array(
				'sbad.article_title',
				'sbau.name',
				'sba.sort_order',
				'sba.status',
				'sba.date_added'				
			);	
			
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY sba.date_added";	
			}
			
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}
			
			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}				

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}	
			
				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}

			$query = $this->db->query($sql);
			
			return $query->rows;			
		}	
		
		public function getArticleDescriptions($portfolio_article_id) {
			$portfolio_article_description_data = array();
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_article_description` WHERE portfolio_article_id = '" . (int)$portfolio_article_id . "'");
			
			foreach ($query->rows as $result) {
				
				$portfolio_article_description_data[$result['language_id']] = array(
					'portfolio_article_description_id'	=> $result['portfolio_article_description_id'],
					'article_title'      				=> $result['article_title'],
                    'sub_title'      					=> $result['sub_title'],
					'description' 						=> $result['description'],
                    'short_description' 				=> $result['short_description'],
					'meta_description'   				=> $result['meta_description'],
					'meta_keyword'    					=> $result['meta_keyword']
				);
			}
			return $portfolio_article_description_data;
		}
		
		public function getArticleAdditionalDescriptions($portfolio_article_id) {
			$portfolio_article_additional_description = array();
			
			$sql = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_article_description_additional`
			WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
			
			if($sql->num_rows > 1) {
			    
                $this->load->model('localisation/language');
                
                $language_total = $this->model_localisation_language->getTotalLanguages();
                
				$addition_blog_array = array();
				
                $counter = 0;
                
				foreach($sql->rows as $key => $result) {
				    
                    $counter++;
                    
				    $addition_blog_array[$result['language_id']] = array(
                        'additional' => $result['additional_description'],
                        'sort_order' => $result['sort_order'],
                        'title' => $result['title'],
                        'sub_title' => $result['sub_title']
					); 
                    
                    if($counter == $language_total) {
                        
                        $portfolio_article_additional_description[] = $addition_blog_array;
                        $addition_blog_array = array();
                        $counter = 0;
                    } 
				}
				//print "<pre>"; print_r($portfolio_article_additional_description); die;
				
			} else {
				foreach($sql->rows as $result) {
					$portfolio_article_additional_description[][$result['language_id']] = array(
						'additional' => $result['additional_description'],
                        'sort_order' => $result['sort_order'],
                        'title' => $result['title'],
                        'sub_title' => $result['sub_title']
					);
				}	
			}
			
			return $portfolio_article_additional_description;
		}
		
		public function getArticleStore($portfolio_article_id) {
			$article_store_data = array();
		
			$sql = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_article_to_store` WHERE portfolio_article_id = '" . (int)$portfolio_article_id . "'");
	
			foreach ($sql->rows as $result) {
				$article_store_data[] = $result['store_id'];
			}			
			return $article_store_data;
		}

		public function getArticleCategories($portfolio_article_id) {
			$article_category_data = array();
			
			$sql = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_article_to_category` WHERE portfolio_article_id = '" . (int)$portfolio_article_id . "'");
			
			foreach ($sql->rows as $result) {
				$article_category_data[] = $result['portfolio_category_id'];
			}			
			return $article_category_data;
		}
		
		public function getArticleLayouts($portfolio_article_id) {
			$article_layout_data = array();
		
			$sql = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_article_to_layout` WHERE portfolio_article_id = '" . (int)$portfolio_article_id . "'");
			
			foreach ($sql->rows as $result) {
				$article_layout_data[$result['store_id']] = $result['layout_id'];
			}
			
			return $article_layout_data;
		}
		
		public function getProductManufacturerWise($manufacturers) {
			
			$product_list = array();
			
			foreach($manufacturers as $manufacturer) {
				$sql = $this->db->query("SELECT product_id FROM `" . DB_PREFIX . "product` WHERE manufacturer_id='" . (int)$manufacturer . "'");
				//print "<pre>";print_r($sql->rows); exit;
				
				foreach($sql->rows as $result) {
					//echo $result['product_id'] . "<br />";
					if(!in_array($result['product_id'], $product_list)) {
						$product_list[] = $result['product_id'];
					}	
				}	
				
				return $product_list;
			}				
		}	
		
		public function getProductCategoryWise($categories) {
			$product_list = array();
			
			foreach($categories as $category_id) {
				$sql = $this->db->query("SELECT product_id FROM `" . DB_PREFIX . "product_to_category` WHERE category_id='" . (int)$category_id. "'");
				foreach($sql->rows as $result) {
					if(!in_array($result['product_id'], $product_list)) {
						$product_list[] = $result['product_id'];
					}	
				}
			}			
			
			return $product_list;
		}
		
		public function getArticleProduct($portfolio_article_id) {
			$sql = $this->db->query("SELECT product_id FROM `" . DB_PREFIX . "portfolio_article_product_related` WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
			return $sql->rows;	
		}	
		
		public function checkAuthorName($author_name) {
			$sql = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_author` WHERE LCASE(name) = '" . $this->db->escape(utf8_strtolower($author_name)) . "'");
			return $sql->num_rows;
		}	
		
		public function checkArticleName($language_id, $article_name, $portfolio_article_id = 0) {
			
			if(!$portfolio_article_id) {
				$sql = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_article_description` WHERE LCASE(article_title)='" . $this->db->escape(utf8_strtolower($article_name)) . "' AND language_id='" . (int)$language_id . "'");
				return $sql->num_rows;
			} else {
				$sql = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_article_description` WHERE LCASE(article_title)='" . $this->db->escape(utf8_strtolower($article_name)) . "' AND language_id='" . (int)$language_id . "' AND portfolio_article_id <> '" . (int)$portfolio_article_id . "'");
				return $sql->num_rows;	
			}		
		}	
		
		public function getRelatedArticles($portfolio_article_id) {
			$portfolio_related_article_data = array();
			
			$sql = $this->db->query("SELECT sbra.*, sbad.article_title AS article_title FROM `" . DB_PREFIX . "portfolio_related_article` sbra LEFT JOIN `" . DB_PREFIX . "portfolio_article_description` sbad ON(sbad.portfolio_article_id=sbra.portfolio_article_related_id) WHERE sbra.portfolio_article_id='" . (int)$portfolio_article_id . "' AND sbad.language_id='" . $this->config->get('config_language_id') . "'");
			
			foreach($sql->rows as $row) {
				$portfolio_related_article_data[] = array(
					'portfolio_article_related_id' => $row['portfolio_article_related_id'],
					'article_title'				=> $row['article_title'],
					'sort_order'				=> $row['sort_order'],
					'status'					=> $row['status']
				);
			}
			
			return $portfolio_related_article_data;
		}
		
		public function getArticlesRelated($data, $portfolio_article_id) {
			$sql = "SELECT * FROM `" . DB_PREFIX . "portfolio_article_description` WHERE LCASE(article_title) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%' AND portfolio_article_id <> '" . (int)$portfolio_article_id . "'";
			
			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}				

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}	
			
				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}	
				
			$query = $this->db->query($sql);			
			
			return $query->rows;
		} 
				
	}
?>