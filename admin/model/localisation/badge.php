<?php
class ModelLocalisationBadge extends Model {
	public function addStockStatus($data) {

		foreach ($data['badge'] as $language_id => $value) {
			if (isset($badge_id)) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "badge SET badge_id = '" . (int)$badge_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			} else {
				$this->db->query("INSERT INTO " . DB_PREFIX . "badge SET language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");

				$badge_id = $this->db->getLastId();
			}
		}

		$this->cache->delete('badge');
	}

	public function editStockStatus($badge_id, $data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "badge WHERE badge_id = '" . (int)$badge_id . "'");

		foreach ($data['badge'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "badge SET badge_id = '" . (int)$badge_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		$this->cache->delete('badge');
	}

	public function deleteStockStatus($badge_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "badge WHERE badge_id = '" . (int)$badge_id . "'");

		$this->cache->delete('badge');
	}

	public function getStockStatus($badge_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "badge WHERE badge_id = '" . (int)$badge_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getStockStatuses($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "badge WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";

			$sql .= " ORDER BY name";

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
		} else {
			$badge_data = $this->cache->get('badge.' . (int)$this->config->get('config_language_id'));

			if (!$badge_data) {
				$query = $this->db->query("SELECT badge_id, name FROM " . DB_PREFIX . "badge WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");

				$badge_data = $query->rows;

				$this->cache->set('badge.' . (int)$this->config->get('config_language_id'), $badge_data);
			}

			return $badge_data;
		}
	}

	public function getStockStatusDescriptions($badge_id) {
		$badge_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "badge WHERE badge_id = '" . (int)$badge_id . "'");

		foreach ($query->rows as $result) {
			$badge_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $badge_data;
	}

	public function getTotalStockStatuses() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "badge WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row['total'];
	}
}