<?php
class ModelMarketingLTNewsletter extends Model {

	public function exportLTNewsletter() {
		$sql = "SELECT email FROM " . DB_PREFIX . "lt_newsletter ORDER BY email ASC";
		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function deleteLTNewsletter($id) {
		$this->event->trigger('pre.admin.lt_newsletter.delete', $id);

		$this->db->query("DELETE FROM " . DB_PREFIX . "lt_newsletter WHERE id = '" . (int)$id . "'");

		$this->event->trigger('post.admin.lt_newsletter.delete', $id);
	}

	public function getLTNewsletters($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "lt_newsletter";

		$implode = array();

		if (!empty($data['filter_email'])) {
			$implode[] = "email LIKE '%" . $this->db->escape($data['filter_email']) . "%'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'id',
			'email',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY email";
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

	public function getTotalLTNewsletters($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "lt_newsletter";

		$implode = array();

		if (!empty($data['filter_email'])) {
			$implode[] = "email LIKE '" . $this->db->escape($data['filter_email']) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	
	public function add_table()
	{
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "lt_newsletter` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `email` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
		");
	}
}