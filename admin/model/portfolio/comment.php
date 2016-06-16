<?php
	class ModelportfolioComment extends Model {
		
		public function addArticleComment($data) {			
			$sql = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_article_description` WHERE article_title='" . $this->db->escape($data['article_title']) . "'");
					
			$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_comment` SET portfolio_article_id='" . (int)$sql->row['portfolio_article_id'] . "', author='" . $this->db->escape($data['author_name']) . "', comment='" . $this->db->escape($data['comment']) . "', status='" . (int)$data['status'] . "', date_added=NOW(), date_modified=NOW()");
			
			$portfolio_comment_id = $this->db->getLastId();
			
			if(isset($data['comment_reply'])) {
				foreach($data['comment_reply'] as $reply) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_comment` SET portfolio_article_id='" . (int)$sql->row['portfolio_article_id'] . "', portfolio_article_reply_id='" . (int)$portfolio_comment_id . "', author='" . $this->db->escape($reply['author']) . "', comment='" . $this->db->escape($reply['comment']) . "', status='" . (int)$reply['status'] . "', date_added=NOW(), date_modified=NOW()");
				}
			}
		}
		
		public function editArticleComment($portfolio_comment_id, $data) {
			$sql = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_article_description` WHERE article_title='" . $this->db->escape($data['article_title']) . "'");
			
			$this->db->query("UPDATE `" . DB_PREFIX . "portfolio_comment` SET portfolio_article_id='" . (int)$sql->row['portfolio_article_id'] . "', author='" . $this->db->escape($data['author_name']) . "', comment='" . $this->db->escape($data['comment']) . "', status='" . (int)$data['status'] . "', date_modified=NOW() WHERE portfolio_comment_id='" . (int)$portfolio_comment_id . "'");
			
			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_comment` WHERE portfolio_article_reply_id='" . (int)$portfolio_comment_id . "'");
			
			if(isset($data['comment_reply'])) {
				foreach($data['comment_reply'] as $reply) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "portfolio_comment` SET portfolio_article_id='" . (int)$sql->row['portfolio_article_id'] . "', portfolio_article_reply_id='" . (int)$portfolio_comment_id . "', author='" . $this->db->escape($reply['author']) . "', comment='" . $this->db->escape($reply['comment']) . "', status='" . (int)$reply['status'] . "', date_added=NOW(), date_modified=NOW()");
				}
			}			
		}
		
		public function deleteArticleComment($portfolio_comment_id) {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_comment` WHERE portfolio_comment_id='" . (int)$portfolio_comment_id . "'");
			$this->db->query("DELETE FROM `" . DB_PREFIX . "portfolio_comment` WHERE portfolio_article_reply_id='" . (int)$portfolio_comment_id . "'");
		}
		
		public function getArticleComment($portfolio_comment_id ) {
			$sql = $this->db->query("SELECT sbc.*, sbad.article_title AS article_title FROM `" . DB_PREFIX . "portfolio_comment` sbc LEFT JOIN `" . DB_PREFIX . "portfolio_article_description` sbad ON(sbc.portfolio_article_id=sbad.portfolio_article_id) WHERE sbc.portfolio_comment_id='" . (int)$portfolio_comment_id . "' AND sbad.language_id='" . (int)$this->config->get('config_language_id') . "'");
			
			return $sql->row;
		}
		
		public function getTotalArticleComment($data = array()) {
			$sql = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "portfolio_comment` WHERE portfolio_article_reply_id='" . $data['portfolio_article_reply_id'] . "'");
			return $sql->row['total'];
		}	
		
		public function getArticleComments($data = array()) {
			$sql = "SELECT sbc.*, sbad.article_title AS article_title FROM `" . DB_PREFIX . "portfolio_comment` sbc LEFT JOIN `" . DB_PREFIX . "portfolio_article_description` sbad ON(sbc.portfolio_article_id=sbad.portfolio_article_id) WHERE sbc.portfolio_article_reply_id='" . $data['portfolio_article_reply_id'] . "' AND sbad.language_id='" . (int)$this->config->get('config_language_id') . "'";
			
			$sort_data = array(
				'sbad.article_title',
				'sbc.author',
				'sbc.status',
				'sbc.date_added'				
			);	
			
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY sbc.date_added";	
			}
			
			if (isset($data['order']) && ($data['order'] == 'ASC')) {
				$sql .= " ASC";
			} else {
				$sql .= " DESC";
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
		
		public function getCommentReply($portfolio_commnet_id) {
			$comment_reply = array();
			
			$sql = $this->db->query("SELECT sbc.*, sbad.article_title AS article_title FROM `" . DB_PREFIX . "portfolio_comment` sbc LEFT JOIN `" . DB_PREFIX . "portfolio_article_description` sbad ON(sbc.portfolio_article_id=sbad.portfolio_article_id) WHERE sbc.portfolio_article_reply_id='" . (int)$portfolio_commnet_id . "' AND sbad.language_id='" . (int)$this->config->get('config_language_id') . "'");
			
			foreach($sql->rows as $result) {
				$comment_reply[] = array(
					'portfolio_comment_id'	=> $result['portfolio_comment_id'],
					'artile_title'		=> $result['article_title'],
					'author'			=> $result['author'],
					'comment'			=> $result['comment'],
					'status'			=> $result['status'],
				);
			}			
			
			return $comment_reply;			
		}
		
		public function checkArticleTitle($article_title) {
			$sql = $this->db->query("SELECT * FROM `" . DB_PREFIX . "portfolio_article_description` WHERE article_title='" . $this->db->escape($article_title) . "'");
			return $sql->num_rows;	
		}		
	}
?>