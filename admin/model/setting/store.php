<?php
class ModelSettingStore extends Model {
	public function addStore($data) {
		$this->event->trigger('pre.admin.store.add', $data);

        $this->db->query("INSERT INTO " . DB_PREFIX . "store SET name = '" . $this->db->escape($data['config_name']) . "',
		`url` = '" . $this->db->escape($data['config_prod_url']) . "',
        `ssl` = '" . $this->db->escape($data['config_prod_ssl']) . "'");

        $store_id = $this->db->getLastId();

		$this->dbcore->query("INSERT INTO " . DB_PREFIX_CORE . "store_to_client SET
		name = '" . $this->dbcore->escape($data['config_name']) . "',
		`prod_url` = '" . $this->dbcore->escape($data['config_prod_url']) . "',
		`dev_url` = '" . $this->dbcore->escape($data['config_dev_url']) . "',
		`prod_path` = '" . $this->dbcore->escape($data['config_prod_path']) . "',
		`dev_path` = '" . $this->dbcore->escape($data['config_dev_path']) . "',
        `dev_ssl` = '" . $this->dbcore->escape($data['config_dev_ssl']) . "',
        `prod_ssl` = '" . $this->dbcore->escape($data['config_prod_ssl']) . "',
        `id_store` = '$store_id',
        `id_client` = '".ID_CLIENT."'");

     	// Layout Route
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "layout_route WHERE store_id = '0'");

		foreach ($query->rows as $layout_route) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "layout_route SET layout_id = '" . (int)$layout_route['layout_id'] . "', route = '" . $this->db->escape($layout_route['route']) . "', store_id = '" . (int)$store_id . "'");
		}

		$this->cache->delete('store');

		$this->event->trigger('post.admin.store.add', $store_id);

		return $store_id;
	}

	public function editStore($store_id, $data,$id_client) {
		$this->event->trigger('pre.admin.store.edit', $data);

        /* atualiza local */
		$this->db->query("UPDATE " . DB_PREFIX . "store SET name = '" . $this->db->escape($data['config_name']) . "', `url` = '" . $this->db->escape($data['config_prod_url']) . "', `ssl` = '" . $this->db->escape($data['config_prod_ssl']) . "' WHERE store_id = '" . (int)$store_id . "'");

        /* atualiza core */

        /* insere loja no core */

		$sql = "update " . DB_PREFIX_CORE . "store_to_client SET
        dev_url = '" . $this->dbcore->escape($data['config_dev_url']) . "',
        dev_path = '" . $this->dbcore->escape($data['config_dev_path']) . "',
        dev_ssl = '" . $this->dbcore->escape($data['config_dev_ssl']) . "',
        prod_url = '" . $this->dbcore->escape($data['config_prod_url']) . "',
        prod_path = '" . $this->dbcore->escape($data['config_prod_path']) . "',
        prod_ssl = '" . $this->dbcore->escape($data['config_prod_ssl']) . "' WHERE
        id_store = '".$store_id."' AND id_client = '".$id_client."'";

        $this->dbcore->query($sql);

        $this->cache->delete('store');

		$this->event->trigger('post.admin.store.edit', $store_id);
	}

	public function deleteStore($store_id,$id_client=0) {

		if(ID_CLIENT != 0)
			$id_cliente_usar = ID_CLIENT;
		else
			$id_cliente_usar = $id_client;


		$this->event->trigger('pre.admin.store.delete', $store_id);

		$this->dbcore->query("DELETE FROM " . DB_PREFIX . "store WHERE store_id = '" . $store_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "store WHERE store_id = '" . $store_id . "'");

        $this->dbcore->query("DELETE FROM " . DB_PREFIX_CORE . "store_to_client WHERE id_store = '" . $store_id . "' and id_client = '".$id_cliente_usar."'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "layout_route WHERE store_id = '" . (int)$store_id . "'");

		$this->cache->delete('store');

		$this->event->trigger('post.admin.store.delete', $store_id);
	}

	public function getStore($store_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "store WHERE store_id = '" . (int)$store_id . "'");

		return $query->row;
	}

	public function getStores($data = array()) {

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store ORDER BY url");

		$store_data = $query->rows;

		return $store_data;
	}

    public function getStoresAdmin($data = array()) {

        $query = $this->dbcore->query("SELECT " . DB_PREFIX_CORE . "store_to_client.*,id_store as store_id,dev_url as url,oc_clients.config_name as name
        FROM " . DB_PREFIX_CORE . "store_to_client
        INNER JOIN " . DB_PREFIX_CORE . "clients on " . DB_PREFIX_CORE . "store_to_client.id_client = " . DB_PREFIX_CORE . "clients.id
        ORDER BY id_client");

        $store_data = $query->rows;

        return $store_data;
    }

	public function getTotalStores() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "store");

		return $query->row['total'];
	}

    public function getClientById() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "store");

        return $query->row['total'];
    }

	public function getTotalStoresByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_layout_id' AND `value` = '" . (int)$layout_id . "' AND store_id != '0'");

		return $query->row['total'];
	}

	public function getTotalStoresByLanguage($language) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_language' AND `value` = '" . $this->db->escape($language) . "' AND store_id != '0'");

		return $query->row['total'];
	}

	public function getTotalStoresByCurrency($currency) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_currency' AND `value` = '" . $this->db->escape($currency) . "' AND store_id != '0'");

		return $query->row['total'];
	}

	public function getTotalStoresByCountryId($country_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_country_id' AND `value` = '" . (int)$country_id . "' AND store_id != '0'");

		return $query->row['total'];
	}

	public function getTotalStoresByZoneId($zone_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_zone_id' AND `value` = '" . (int)$zone_id . "' AND store_id != '0'");

		return $query->row['total'];
	}

	public function getTotalStoresByCustomerGroupId($customer_group_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_customer_group_id' AND `value` = '" . (int)$customer_group_id . "' AND store_id != '0'");

		return $query->row['total'];
	}

	public function getTotalStoresByInformationId($information_id) {
		$account_query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_account_id' AND `value` = '" . (int)$information_id . "' AND store_id != '0'");

		$checkout_query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_checkout_id' AND `value` = '" . (int)$information_id . "' AND store_id != '0'");

		return ($account_query->row['total'] + $checkout_query->row['total']);
	}

	public function getTotalStoresByOrderStatusId($order_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_order_status_id' AND `value` = '" . (int)$order_status_id . "' AND store_id != '0'");

		return $query->row['total'];
	}
}