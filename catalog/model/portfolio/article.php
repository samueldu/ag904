<?php
	class ModelPortfolioArticle extends Model {

		public function getNext($date)
		{
			$sql = "
			SELECT 'next' AS direction, MIN(portfolio_article_id) AS portfolio_article_id, MIN(date_added) AS date_key
			FROM `" . DB_PREFIX . "portfolio_article`
			  WHERE date_added > '$date' and `status`= '1'
				GROUP BY 1
				UNION
			SELECT 'prev' AS direction, MAX(portfolio_article_id) AS portfolio_article_id, MAX(date_added) AS date_key
  			FROM `" . DB_PREFIX . "portfolio_article`
  			WHERE date_added < '$date' and `status`= '1'
			GROUP BY 1
			ORDER BY 1 DESC";

			$query = $this->db->query($sql);

			return $query->rows;
		}

		
		public function getTotalArticle($data = array()) {
			
			$sql = "SELECT COUNT(DISTINCT(sba.portfolio_article_id)) AS total FROM `" . DB_PREFIX . "portfolio_article` sba LEFT JOIN `" . DB_PREFIX . "portfolio_article_description` sbad ON(sba.portfolio_article_id=sbad.portfolio_article_id) LEFT JOIN `" . DB_PREFIX . "portfolio_article_to_store` sbas ON(sba.portfolio_article_id=sbas.portfolio_article_id) LEFT JOIN `" . DB_PREFIX . "portfolio_author` sbau ON(sba.portfolio_author_id=sbau.portfolio_author_id) WHERE sba.status=1 AND sbau.status=1 AND sbas.store_id='" . (int)$this->config->get('config_store_id') . "'";
			
			if(!empty($data['portfolio_search'])) {
				$sql .= " AND LCASE(sbad.article_title) LIKE '" . $this->db->escape(utf8_strtolower($data['portfolio_search'])) . "%'";
			}
			
			$query = $this->db->query($sql);
			
			return $query->row['total'];
		}
		
		public function getArticles($data = array()) {
			
			$sql = "SELECT sba.*, sbad.* FROM `" . DB_PREFIX . "portfolio_article` sba
			LEFT JOIN `" . DB_PREFIX . "portfolio_article_description` sbad ON(sba.portfolio_article_id=sbad.portfolio_article_id)
			LEFT JOIN `" . DB_PREFIX . "portfolio_article_to_store` sbas ON(sba.portfolio_article_id=sbas.portfolio_article_id)
			LEFT JOIN `" . DB_PREFIX . "portfolio_author` sbau ON(sba.portfolio_author_id=sbau.portfolio_author_id)
			WHERE sba.status=1 AND sbas.store_id='" . (int)$this->config->get('config_store_id') . "'
			AND sbad.language_id='" . (int)$this->config->get('config_language_id') . "'";
			
			if(!empty($data['portfolio_search'])) {
				$sql .= " AND LCASE(sbad.article_title) LIKE '" . $this->db->escape(utf8_strtolower($data['portfolio_search'])) . "%'";
			}
			
			$sql .= " ORDER BY sba.sort_order DESC, sba.date_added DESC";
			
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
		
		public function getTotalCategories($parent_id = 0) {
			$sql = $this->db->query("SELECT COUNT(DISTINCT(sbc.portfolio_category_id)) AS total FROM `" . DB_PREFIX . "portfolio_category` sbc LEFT JOIN `" . DB_PREFIX . "portfolio_category_description` sbcd ON(sbc.portfolio_category_id=sbcd.portfolio_category_id) LEFT JOIN `" . DB_PREFIX . "portfolio_category_to_store` sbcs ON(sbc.portfolio_category_id=sbcs.portfolio_category_id) WHERE sbc.parent_id='" . (int)$parent_id . "' AND sbcd.language_id='" . (int)$this->config->get('config_language_id') . "' AND sbcs.store_id='" . (int)$this->config->get('config_store_id') . "' AND sbc.status=1 ORDER BY sbc.sort_order, LCASE(sbcd.name)");
			
			return $sql->row['total'];
		}
		
		public function getCategories($parent_id = 0) {
			$query = "SELECT * FROM `" . DB_PREFIX . "portfolio_category` sbc LEFT JOIN `" . DB_PREFIX . "portfolio_category_description` sbcd ON(sbc.portfolio_category_id=sbcd.portfolio_category_id) LEFT JOIN `" . DB_PREFIX . "portfolio_category_to_store` sbcs ON(sbc.portfolio_category_id=sbcs.portfolio_category_id) WHERE sbc.parent_id='" . (int)$parent_id . "' AND sbcd.language_id='" . (int)$this->config->get('config_language_id') . "' AND sbcs.store_id='" . (int)$this->config->get('config_store_id') . "' AND sbc.status=1 ORDER BY sbc.sort_order, LCASE(sbcd.name)";
            $sql = $this->db->query($query);
			return $sql->rows;
		}
		
		public function getCategorieByArticle($article_id)
		{
			
			$sql = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_category` sbc 
			INNER JOIN `" . DB_PREFIX . "portfolio_category_description` sbcd ON(sbc.portfolio_category_id=sbcd.portfolio_category_id)
			INNER JOIN `" . DB_PREFIX . "portfolio_article_to_category` sbac ON(sbc.portfolio_category_id=sbac.portfolio_category_id) and sbac.portfolio_article_id = '".$article_id."'
			INNER JOIN `" . DB_PREFIX . "portfolio_category_to_store` sbcs ON(sbc.portfolio_category_id=sbcs.portfolio_category_id)
			WHERE sbc.parent_id='0' 
			AND sbcd.language_id='" . (int)$this->config->get('config_language_id') . "' 
			AND sbcs.store_id='" . (int)$this->config->get('config_store_id') . "' 
			AND sbc.status=1 ORDER BY sbc.sort_order, LCASE(sbcd.name) LIMIT 1");
			
			return $sql->row;
			
		}
		
		public function getTotalArticles($portfolio_category_id) {
			$sql = $this->db->query("SELECT COUNT(DISTINCT(portfolio_article_id)) AS total FROM `" . DB_PREFIX . "portfolio_article_to_category` WHERE portfolio_category_id='" . (int)$portfolio_category_id . "'");
			return $sql->row['total'];
		}	
		
		public function getTotalComments($portfolio_article_id) {
			$sql = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "portfolio_comment` WHERE portfolio_article_id='" . (int)$portfolio_article_id . "' AND status=1");
			return $sql->row['total'];
		}
		
		public function getAdditionalDescription($portfolio_article_id) {
			$sql = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_article_description_additional` WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
			return $sql->rows;	
		}	
		
		public function getArticle($portfolio_article_id) {
			$sql = $this->db->query("SELECT sba.*, sbad.*, sbau.name AS author_name FROM `" . DB_PREFIX . "portfolio_article` sba
			LEFT JOIN `" . DB_PREFIX . "portfolio_article_description` sbad ON(sba.portfolio_article_id=sbad.portfolio_article_id)
			LEFT JOIN `" . DB_PREFIX . "portfolio_article_to_store` sbas ON(sba.portfolio_article_id=sbas.portfolio_article_id)
			LEFT JOIN `" . DB_PREFIX . "portfolio_author` sbau ON(sba.portfolio_author_id=sbau.portfolio_author_id)
			WHERE sba.portfolio_article_id='" . (int)$portfolio_article_id . "'
			 AND sba.status=1
			AND sbas.store_id='" . (int)$this->config->get('config_store_id') . "'
			AND sbad.language_id='" . $this->config->get('config_language_id') . "'");
			return $sql->row;
		}
		
		public function addBlogView($portfolio_article_id) {
			$sql = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_view` WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
			
			if($sql->num_rows) {
				$counter = $sql->row['view'];
				
				$counter++;
				
				$this->db->query("UPDATE `" . DB_PREFIX . "portfolio_view` SET view='" . (int)$counter . "', date_modified=NOW() WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
				
			} else {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_view` SET portfolio_article_id='" . (int)$portfolio_article_id . "', view=1, date_added=NOW(), date_modified=NOW()");
			}
		}
		
		public function getArticleAdditionalDescription($portfolio_article_id) {
			$sql = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_article_description_additional` WHERE language_id='" . (int)$this->config->get('config_language_id') . "' AND portfolio_article_id='" . (int)$portfolio_article_id . "'");
			return $sql->rows;
		}
		
		public function getArticleProductRelated($portfolio_article_id) {
			$sql = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_article_product_related` WHERE portfolio_article_id='" . (int)$portfolio_article_id . "'");
			return $sql->rows;
		}
		
		public function getTotalCommentsByArticleId($portfolio_article_id) {
			$sql = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "portfolio_comment` WHERE portfolio_article_id='" . (int)$portfolio_article_id . "' AND status=1 AND portfolio_article_reply_id=0");
			return $sql->row['total'];
		}
		
		public function getCommentsByArticle($portfolio_article_id, $start = 0, $limit = 20, $portfolio_comment_id = 0) {
			if(!$portfolio_comment_id) {
				
				if ($start < 0) {
					$start = 0;
				}
				
				if ($limit < 1) {
					$limit = 20;
				}
				
				$sql = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_comment` WHERE portfolio_article_id='" . (int)$portfolio_article_id . "' AND status=1 AND portfolio_article_reply_id='0' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);
				return $sql->rows;
			} else {
						
				if ($start < 0) {
					$start = 0;
				}
				
				if ($limit < 1) {
					$limit = 1000;
				}	
				
				$sql = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_comment` WHERE portfolio_article_reply_id='" . (int)$portfolio_comment_id . "' AND status=1 ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);
				return $sql->rows;
			}
		}
		
		public function addArticleComment($portfolio_article_id, $data) {
					
			if($this->config->get('portfolio_comment_auto_approval')) {
				$status = 1;
			} else {
				$status = 0;
			}
			
			if($data['reply_id']) {
				//echo "INSERT INTO `" . DB_PREFIX . "portfolio_comment` SET portfolio_article_id='" . (int)$portfolio_article_id . "', portfolio_article_reply_id='" . (int)$data['reply_id'] . "', author='" . $this->db->escape($data['name']) . "', comment='" . $this->db->escape($data['text']) . "', status='" . (int)$status . "', date_added=NOW(), date_modified=NOW()";
				//echo $data['reply_id']; exit;
				$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_comment` SET portfolio_article_id='" . (int)$portfolio_article_id . "', portfolio_article_reply_id='" . (int)$data['reply_id'] . "', author='" . $this->db->escape($data['name']) . "', comment='" . $this->db->escape($data['text']) . "', status='" . (int)$status . "', date_added=NOW(), date_modified=NOW()");
			} else {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_comment` SET portfolio_article_id='" . (int)$portfolio_article_id . "', author='" . $this->db->escape($data['name']) . "', comment='" . $this->db->escape($data['text']) . "', status='" . (int)$status . "', date_added=NOW(), date_modified=NOW()");
			}
		}
		
		public function getCategory($portfolio_category_id) {
			$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "portfolio_category` sbc LEFT JOIN `" . DB_PREFIX . "portfolio_category_description` sbcd ON (sbc.portfolio_category_id = sbcd.portfolio_category_id) LEFT JOIN `" . DB_PREFIX . "portfolio_category_to_store` sbcs ON (sbc.portfolio_category_id = sbcs.portfolio_category_id) WHERE sbc.portfolio_category_id = '" . (int)$portfolio_category_id . "' AND sbcd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND sbcs.store_id = '" . (int)$this->config->get('config_store_id') . "' AND sbc.status = '1'");
			
			return $query->row;
		}	
		
		public function getTotalArticleCategoryWise($data = array()) {
			$sql = $this->db->query("SELECT COUNT(DISTINCT(sba.portfolio_article_id)) AS total FROM `" . DB_PREFIX . "portfolio_article` sba LEFT JOIN `" . DB_PREFIX . "portfolio_article_description` sbad ON(sba.portfolio_article_id=sbad.portfolio_article_id) LEFT JOIN `" . DB_PREFIX . "portfolio_article_to_store` sbas ON(sba.portfolio_article_id=sbas.portfolio_article_id) LEFT JOIN `" . DB_PREFIX . "portfolio_author` sbau ON(sba.portfolio_author_id=sbau.portfolio_author_id) LEFT JOIN `" . DB_PREFIX . "portfolio_article_to_category` sbac ON(sba.portfolio_article_id=sbac.portfolio_article_id) WHERE sbac.portfolio_category_id='" . (int)$data['portfolio_article_id'] . "' AND sba.status=1 AND sbau.status=1 AND sbas.store_id='" . (int)$this->config->get('config_store_id') . "' AND sbad.language_id='" . $this->config->get('config_language_id') . "'");
			
			return $sql->row['total'];
		}
		
		public function getArticleCategoryWise($data = array()) {
			$sql = "SELECT sba.*, sbad.*, sbau.name AS author_name FROM `" . DB_PREFIX . "portfolio_article` sba
			LEFT JOIN `" . DB_PREFIX . "portfolio_article_description` sbad ON(sba.portfolio_article_id=sbad.portfolio_article_id)
			LEFT JOIN `" . DB_PREFIX . "portfolio_article_to_store` sbas ON(sba.portfolio_article_id=sbas.portfolio_article_id)
			LEFT JOIN `" . DB_PREFIX . "portfolio_author` sbau ON(sba.portfolio_author_id=sbau.portfolio_author_id)
			LEFT JOIN `" . DB_PREFIX . "portfolio_article_to_category` sbac ON(sba.portfolio_article_id=sbac.portfolio_article_id)
			WHERE sbac.portfolio_category_id='" . (int)$data['portfolio_article_id'] . "'
			AND sba.status=1 AND  sbas.store_id='" . (int)$this->config->get('config_store_id') . "'
			AND sbad.language_id='" . $this->config->get('config_language_id') . "'
			ORDER BY sba.date_modified DESC";
			
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
		
		public function getTotalArticleAuthorWise($portfolio_author_id) {
			$sql = $this->db->query("SELECT COUNT(DISTINCT(sba.portfolio_article_id)) AS total FROM `" . DB_PREFIX . "portfolio_article` sba LEFT JOIN `" . DB_PREFIX . "portfolio_article_description` sbad ON(sba.portfolio_article_id=sbad.portfolio_article_id) LEFT JOIN `" . DB_PREFIX . "portfolio_article_to_store` sbas ON(sba.portfolio_article_id=sbas.portfolio_article_id) LEFT JOIN `" . DB_PREFIX . "portfolio_author` sbau ON(sba.portfolio_author_id=sbau.portfolio_author_id) WHERE sba.portfolio_author_id='" . (int)$portfolio_author_id . "' AND sba.status=1 AND sbau.status=1 AND sbau.status=1 AND sbas.store_id='" . (int)$this->config->get('config_store_id') . "' AND sbad.language_id='" . $this->config->get('config_language_id') . "'");
			
			return $sql->row['total'];
		}
		
		public function getArticleAuthorWise($data = array()) {
			$sql = "SELECT sba.*, sbad.*, sbau.name AS author_name FROM `" . DB_PREFIX . "portfolio_article` sba LEFT JOIN `" . DB_PREFIX . "portfolio_article_description` sbad ON(sba.portfolio_article_id=sbad.portfolio_article_id) LEFT JOIN `" . DB_PREFIX . "portfolio_article_to_store` sbas ON(sba.portfolio_article_id=sbas.portfolio_article_id) LEFT JOIN `" . DB_PREFIX . "portfolio_author` sbau ON(sba.portfolio_author_id=sbau.portfolio_author_id) WHERE sba.portfolio_author_id='" . (int)$data['portfolio_author_id'] . "' AND sba.status=1 AND sbau.status=1 AND sbas.store_id='" . (int)$this->config->get('config_store_id') . "' AND sbad.language_id='" . $this->config->get('config_language_id') . "' ORDER BY sba.date_modified DESC";
			
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
		
		public function getAuthorInformation($portfolio_author_id) {
			$sql = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_author` sba LEFT JOIN `" . DB_PREFIX . "portfolio_author_description` sbau ON(sba.portfolio_author_id=sbau.portfolio_author_id) WHERE sba.portfolio_author_id='" . (int)$portfolio_author_id . "' AND sba.status=1 AND sbau.language_id='" . $this->config->get('config_language_id') . "'");
			return $sql->row;
		}
		
		public function getArticleModuleWise($data = array()) {
			$sql = "SELECT sba.*, sbad.*, sbau.name AS author_name FROM `" . DB_PREFIX . "portfolio_article` sba LEFT JOIN `" . DB_PREFIX . "portfolio_article_description` sbad ON(sba.portfolio_article_id=sbad.portfolio_article_id) LEFT JOIN `" . DB_PREFIX . "portfolio_article_to_store` sbas ON(sba.portfolio_article_id=sbas.portfolio_article_id) LEFT JOIN `" . DB_PREFIX . "portfolio_author` sbau ON(sba.portfolio_author_id=sbau.portfolio_author_id) LEFT JOIN `" . DB_PREFIX . "portfolio_article_to_category` sbac ON(sba.portfolio_article_id=sbac.portfolio_article_id) WHERE sba.status=1 AND sbau.status=1 AND sbas.store_id='" . (int)$this->config->get('config_store_id') . "' AND sbad.language_id='" . $this->config->get('config_language_id') . "'";
			
			if(!empty($data['filter_category_id'])) {
				$sql .= " AND sbac.portfolio_category_id='" . (int)$data['filter_category_id'] . "'";
			}
			
			$sql .= " GROUP BY sba.portfolio_article_id ORDER BY sba.date_added DESC";
			
			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}				
	
				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}	
	
				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}	
			
			//echo $sql; exit;
			
			$query = $this->db->query($sql);
			
			return $query->rows;	
		}
		
		public function getPopularArticlesModuleWise($data = array()) {
					
			$sql = "SELECT * FROM `" . DB_PREFIX . "portfolio_view`";
			
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
			
			if($query->num_rows) {
				$sql = "SELECT sba.*, sbad.*, sbau.name AS author_name FROM `" . DB_PREFIX . "portfolio_article` sba LEFT JOIN `" . DB_PREFIX . "portfolio_article_description` sbad ON(sba.portfolio_article_id=sbad.portfolio_article_id) LEFT JOIN `" . DB_PREFIX . "portfolio_article_to_store` sbas ON(sba.portfolio_article_id=sbas.portfolio_article_id) LEFT JOIN `" . DB_PREFIX . "portfolio_author` sbau ON(sba.portfolio_author_id=sbau.portfolio_author_id) LEFT JOIN `" . DB_PREFIX . "portfolio_article_to_category` sbac ON(sba.portfolio_article_id=sbac.portfolio_article_id) LEFT JOIN `" . DB_PREFIX . "portfolio_view` sbv ON(sbv.portfolio_article_id=sba.portfolio_article_id) WHERE sba.status=1 AND sbau.status=1 AND sbas.store_id='" . (int)$this->config->get('config_store_id') . "' AND sbad.language_id='" . $this->config->get('config_language_id') . "'";
			
				$sql .= "  GROUP BY sba.portfolio_article_id ORDER BY sbv.view DESC";
				
				if (isset($data['start']) || isset($data['limit'])) {
					if ($data['start'] < 0) {
						$data['start'] = 0;
					}				
		
					if ($data['limit'] < 1) {
						$data['limit'] = 20;
					}	
		
					$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
				}	
				
				//echo $sql; exit;
				
				$my_query = $this->db->query($sql);
			
				return $my_query->rows;
			} else {
				return '';
			}
			
		}
		
		public function getRelatedArticles($portfolio_article_id) {
					
			$this->load->model('tool/image');	
			
			$portfolio_related_article_data = array();
			
			$sql = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_related_article` WHERE portfolio_article_id='" . (int)$portfolio_article_id . "' AND status=1 ORDER BY sort_order");
			
			foreach($sql->rows as $row) {
				$article_info = $this->db->query("SELECT sba.*, sbad.article_title AS article_title, sbad.description AS description, sbau.portfolio_author_id AS portfolio_author_id, sbau.name AS author_name FROM `" . DB_PREFIX . "portfolio_article` sba LEFT JOIN `" . DB_PREFIX . "portfolio_article_description` sbad ON(sba.portfolio_article_id=sbad.portfolio_article_id) LEFT JOIN `" . DB_PREFIX . "portfolio_author` sbau ON(sba.portfolio_author_id=sbau.portfolio_author_id) WHERE sba.portfolio_article_id='" . (int)$row['portfolio_article_related_id'] . "' AND sbad.language_id='" . (int)$this->config->get('config_language_id') . "' AND sba.status=1 AND sbau.status=1");
				
				if($article_info->row) {
					
					$total_comment = $this->getTotalComments($row['portfolio_article_related_id']);
					
					$image = $this->model_tool_image->resize($article_info->row['featured_image'], 150, 150);
					
					$portfolio_related_article_data[] = array(
						'portfolio_article_id'	=> $article_info->row['portfolio_article_id'],
						'article_title'		=> $article_info->row['article_title'],
						'portfolio_author_id'	=> $article_info->row['portfolio_author_id'],
						'image'				=> $image,
						'description'		=> $article_info->row['description'],
						'author_name'		=> $article_info->row['author_name'],
						'date_added'		=> date('F jS, Y', strtotime($article_info->row['date_added'])),
						'date_modified'		=> date('F jS, Y', strtotime($article_info->row['date_modified'])),
						'total_comment'		=> $total_comment
					);	
				}				
			}
			
			
			return $portfolio_related_article_data;
		}
		
		public function getAuthors() {
			$sql = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_author` WHERE status=1");
			
			return $sql->rows;
		}
		
	}
?>