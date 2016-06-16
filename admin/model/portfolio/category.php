<?php
	class ModelportfolioCategory extends Model {
		
		public function addCategory($data) {
			
			$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_category` SET parent_id = '" . (int)$data['parent_id'] . "', `top` = '" . (isset($data['top']) ? (int)$data['top'] : 0) . "', `column` = '" . (int)$data['column'] . "', blog_category_column = '" . (int)$data['blog_category_column'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', date_added = NOW(), date_modified = NOW()");
			
			$portfolio_category_id = $this->db->getLastId();
			
			if (isset($data['image'])) {
				$this->db->query("UPDATE `" . DB_PREFIX . "portfolio_category` SET image = '" . $this->db->escape($data['image']) . "' WHERE portfolio_category_id = '" . (int)$portfolio_category_id . "'");
			}
			
			if ($data['keyword']) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "url_alias` SET query = 'portfolio_category_id=" . (int)$portfolio_category_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
			}
			
			foreach ($data['category_description'] as $language_id => $value) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_category_description` SET portfolio_category_id = '" . (int)$portfolio_category_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', description = '" . $this->db->escape($value['description']) . "'");
			}
			
			if (isset($data['category_store'])) {
				foreach ($data['category_store'] as $store_id) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_category_to_store` SET portfolio_category_id = '" . (int)$portfolio_category_id . "', store_id = '" . (int)$store_id . "'");
				}
			}
			
			if (isset($data['category_layout'])) {
				foreach ($data['category_layout'] as $store_id => $layout) {
					if ($layout['layout_id']) {
						$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_category_to_layout` SET portfolio_category_id = '" . (int)$portfolio_category_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout['layout_id'] . "'");
					}
				}
			}
			
			$this->cache->delete('portfolio_category');
		}
		
		public function editCategory($portfolio_category_id, $data) {
			
			$this->db->query("UPDATE `" . DB_PREFIX . "portfolio_category` SET parent_id = '" . (int)$data['parent_id'] . "', `top` = '" . (isset($data['top']) ? (int)$data['top'] : 0) . "', `column` = '" . (int)$data['column'] . "', blog_category_column = '" . (int)$data['blog_category_column'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', date_modified = NOW() WHERE portfolio_category_id = '" . (int)$portfolio_category_id . "'");
			
			if (isset($data['image'])) {
				$this->db->query("UPDATE `" . DB_PREFIX . "portfolio_category` SET image = '" . $this->db->escape($data['image']) . "' WHERE portfolio_category_id = '" . (int)$portfolio_category_id . "'");
			}
			
			$this->db->query("DELETE FROM `" . DB_PREFIX . "url_alias` WHERE query = 'portfolio_category_id=" . (int)$portfolio_category_id. "'");
		
			if ($data['keyword']) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "url_alias` SET query = 'portfolio_category_id=" . (int)$portfolio_category_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
			}		
			
			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_category_description` WHERE portfolio_category_id = '" . (int)$portfolio_category_id . "'");

			foreach ($data['category_description'] as $language_id => $value) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_category_description` SET portfolio_category_id = '" . (int)$portfolio_category_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', description = '" . $this->db->escape($value['description']) . "'");
			}
			
			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_category_to_store` WHERE portfolio_category_id = '" . (int)$portfolio_category_id . "'");
		
			if (isset($data['category_store'])) {		
				foreach ($data['category_store'] as $store_id) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_category_to_store` SET portfolio_category_id = '" . (int)$portfolio_category_id . "', store_id = '" . (int)$store_id . "'");
				}
			}
			
			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_category_to_layout` WHERE portfolio_category_id = '" . (int)$portfolio_category_id . "'");

			if (isset($data['category_layout'])) {
				foreach ($data['category_layout'] as $store_id => $layout) {
					if ($layout['layout_id']) {
						$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_category_to_layout` SET portfolio_category_id = '" . (int)$portfolio_category_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout['layout_id'] . "'");
					}
				}
			}
			
			$this->cache->delete('portfolio_category');
		}		
		
		public function deleteCategory($portfolio_category_id) {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_category` WHERE portfolio_category_id = '" . (int)$portfolio_category_id . "'");
			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_category_description` WHERE portfolio_category_id = '" . (int)$portfolio_category_id . "'");
			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_category_to_store` WHERE portfolio_category_id = '" . (int)$portfolio_category_id . "'");
			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_category_to_layout` WHERE portfolio_category_id = '" . (int)$portfolio_category_id . "'");
			$this->db->query("DELETE FROM `" . DB_PREFIX . "url_alias` WHERE query = 'portfolio_category_id=" . (int)$portfolio_category_id . "'");
			
			$query = $this->db->query("SELECT portfolio_category_id FROM `" . DB_PREFIX . "portfolio_category` WHERE parent_id = '" . (int)$portfolio_category_id . "'");
	
			foreach ($query->rows as $result) {
				$this->deleteCategory($result['portfolio_category_id']);
			}
			
			$this->cache->delete('portfolio_category');
		}
		
		public function getCategory($portfolio_category_id) {
			$query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'portfolio_category_id=" . (int)$portfolio_category_id . "') AS keyword FROM `" . DB_PREFIX . "portfolio_category` WHERE portfolio_category_id = '" . (int)$portfolio_category_id . "'");
			return $query->row;
		}		
		
		public function getTotalCategories($data = array()) {
			$sql = $this->db->query("SELECT COUNT(DISTINCT(sbc.portfolio_category_id)) AS total FROM `" . DB_PREFIX . "portfolio_category` sbc LEFT JOIN `" . DB_PREFIX . "portfolio_category_description` sbcd ON(sbc.portfolio_category_id=sbcd.portfolio_category_id) WHERE sbcd.language_id='" . (int)$this->config->get('config_language_id') . "'");
			return $sql->row['total'];
		}
		
		public function getCategories($parent_id = 0) {
			$category_data = array();
				
			$sql = "SELECT * FROM `" . DB_PREFIX . "portfolio_category` sc LEFT JOIN `" . DB_PREFIX . "portfolio_category_description` scd ON (sc.portfolio_category_id = scd.portfolio_category_id) WHERE sc.parent_id = '" . (int)$parent_id . "' AND scd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
			
			 //ORDER BY c.sort_order, cd.name ASC
			
			$sort_data = array(
				'scd.name',
				'sc.sort_order',
				'sc.status'
			);	
			
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];	
			} else {
				$sql .= " ORDER BY scd.name";	
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
		
			foreach ($query->rows as $result) {
				$category_data[] = array(
					'portfolio_category_id' 	=> $result['portfolio_category_id'],
					'name'        		=> $this->getPath($result['portfolio_category_id'], $this->config->get('config_language_id')),
					'status'  	  		=> $result['status'],
					'sort_order'  		=> $result['sort_order']
				);
			
				$category_data = array_merge($category_data, $this->getCategories($result['portfolio_category_id']));
			}	
			
			return $category_data;
		}
		
		public function getPath($portfolio_category_id) {
			$query = $this->db->query("SELECT scd.name AS name, sc.parent_id AS parent_id FROM " . DB_PREFIX . "portfolio_category sc LEFT JOIN " . DB_PREFIX . "portfolio_category_description scd ON (sc.portfolio_category_id = scd.portfolio_category_id) WHERE sc.portfolio_category_id = '" . (int)$portfolio_category_id . "' AND scd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY sc.sort_order, scd.name ASC");
			
			if ($query->row['parent_id']) {
				return $this->getPath($query->row['parent_id'], $this->config->get('config_language_id')) . '&nbsp;&nbsp;&gt;&nbsp;&nbsp;' . $query->row['name'];
			} else {
				return $query->row['name'];
			}
		}
		
		public function getCategoryDescriptions($portfolio_category_id) {
			$simple_category_description_data = array();
			
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_category_description` WHERE portfolio_category_id = '" . (int)$portfolio_category_id . "'");
			
			foreach ($query->rows as $result) {
				$simple_category_description_data[$result['language_id']] = array(
					'name'             => $result['name'],
					'meta_keyword'     => $result['meta_keyword'],
					'meta_description' => $result['meta_description'],
					'description'      => $result['description']
				);
			}
			
			return $simple_category_description_data;
		}		
		
		public function getCategoryStores($portfolio_category_id) {
			$simple_category_store_data = array();
		
			$sql = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_category_to_store` WHERE portfolio_category_id = '" . (int)$portfolio_category_id . "'");
	
			foreach ($sql->rows as $result) {
				$simple_category_store_data[] = $result['store_id'];
			}
			
			return $simple_category_store_data;
		}
		
		public function getCategoryLayouts($portfolio_category_id) {
			$simple_category_layout_data = array();
			
			$sql = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_category_to_layout` WHERE portfolio_category_id = '" . (int)$portfolio_category_id . "'");
			
			foreach ($sql->rows as $result) {
				$simple_category_layout_data[$result['store_id']] = $result['layout_id'];
			}
			
			return $simple_category_layout_data;
		}		
		
		public function getTotalArticleCategoryWise($portfolio_category_id) {
			$sql = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_article_to_category` WHERE portfolio_category_id='" . (int)$portfolio_category_id . "'");
			
			return $sql->num_rows;
		}
		
	}
?>