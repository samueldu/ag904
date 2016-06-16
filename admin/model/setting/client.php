<?php
class ModelSettingClient extends Model {
	public function addStore($data) {

        /*insere cliente no core*/
		$this->dbcore->query("INSERT INTO " . DB_PREFIX . "clients SET
		 config_name = '" . $this->dbcore->escape($data['config_name']) . "',
		`config_nome_contato` = '" . $this->dbcore->escape($data['config_nome_contato']) . "',
		`config_email` = '" . $this->dbcore->escape($data['config_email']) . "',
		`config_documento` = '" . $this->dbcore->escape($data['config_documento']) . "',
		`config_logo` = '" . $this->dbcore->escape($data['config_logo']) . "',
		`config_prod_host` = '" . $this->dbcore->escape($data['config_prod_host']) . "',
		`config_prod_user` = '" . $this->dbcore->escape($data['config_prod_user']) . "',
		`config_prod_pass` = '" . $this->dbcore->escape($data['config_prod_pass']) . "',
		`config_prod_base` = '" . $this->dbcore->escape($data['config_prod_base']) . "',
		`config_dev_host` = '" . $this->dbcore->escape($data['config_dev_host']) . "',
		`config_dev_user` = '" . $this->dbcore->escape($data['config_dev_user']) . "',
		`config_dev_pass` = '" . $this->dbcore->escape($data['config_dev_pass']) . "',
		`config_dev_base` = '" . $this->dbcore->escape($data['config_dev_base']) . "'");

		$client_id = $this->dbcore->getLastId();

        //$store_id = $this->db->getLastId();
        $store_id = '0';

        /* insere dados loja no core */
        $this->dbcore->query("INSERT INTO " . DB_PREFIX_CORE . "store_to_client SET
        dev_url = '" . $this->dbcore->escape($data['config_dev_url']) . "',
        dev_path = '" . $this->dbcore->escape($data['config_dev_path']) . "',
        dev_ssl = '" . $this->dbcore->escape($data['config_dev_ssl']) . "',
        prod_url = '" . $this->dbcore->escape($data['config_prod_url']) . "',
        prod_path = '" . $this->dbcore->escape($data['config_prod_path']) . "',
        prod_ssl = '" . $this->dbcore->escape($data['config_prod_ssl']) . "',
        id_store = '".$store_id."',
        id_client = '".$client_id."'");

		return $client_id;
	}

    public function installDataBase($data)
    {

        GLOBAL $registry;
        GLOBAL $var;

        $var ="config_".$var;

        $db = new mysqli($data[$var."host"], $data[$var."user"], $data[$var."pass"]);

        // Check connection
        if ($db->connect_error) {
            die("Connection failed: " . $db->connect_error);
        }

        $sql = 'SELECT COUNT(*) AS `exists` FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMATA.SCHEMA_NAME="'.$data[$var."base"].'"';

        $query = $db->query($sql);
        if ($query === false) {
            throw new Exception($db->error, $db->errno);
        }

        $row = $query->fetch_object();
        $dbExists = (bool) $row->exists;

        if(!$dbExists)

        {
            $sql = "CREATE DATABASE ".$data[$var.'base'];

            if ($db->query($sql) === TRUE) {
               // echo "Database created successfully";
            }
            else {
                echo "erro ao criar tabasase";
            }
        }

        $db->select_db($data[$var.'base']);

        $file = BASE_STRUCTURE."/base.sql";

        $data['db_prefix'] = "oc_";

        if (!file_exists($file)) {
            exit('Could not load sql file: ' . $file);
        }

        $lines = file($file);

        if ($lines) {
            $sql = '';

            foreach($lines as $line) {
                if ($line && (substr($line, 0, 2) != '--') && (substr($line, 0, 1) != '#')) {
                    $sql .= $line;

                    if (preg_match('/;\s*$/', $line)) {
                        $sql = str_replace("DROP TABLE IF EXISTS `oc_", "DROP TABLE IF EXISTS `" . $data['db_prefix'], $sql);
                        $sql = str_replace("CREATE TABLE IF NOT EXISTS `oc_", "CREATE TABLE IF NOT EXISTS `" . $data['db_prefix'], $sql);
                        $sql = str_replace("INSERT INTO `oc_", "INSERT INTO `" . $data['db_prefix'], $sql);

                        $db->query(utf8_decode($sql));

                        $sql = '';
                    }
                }
            }

            $db->query("SET CHARACTER SET utf8");

            $db->query("SET @@session.sql_mode = 'MYSQL40'");

            $db->query("DELETE FROM `" . $data['db_prefix'] . "user` WHERE user_id = '1'");

            $salt = substr(md5(uniqid(rand(), true)), 0, 9);

            $db->query("INSERT INTO `" . $data['db_prefix'] . "user` SET user_id = '1', user_group_id = '2', username = 'admin', salt = '" . $salt . "', password = '" . sha1($salt . sha1($salt . sha1('123456'))) . "', firstname = 'Administrativo', lastname = '', email = '', status = '1', date_added = NOW()");

            $db->query("DELETE FROM `" . $data['db_prefix'] . "setting` WHERE `key` = 'config_email'");
            $db->query("INSERT INTO `" . $data['db_prefix'] . "setting` SET `code` = 'config', `key` = 'config_email', value = ''");

            $db->query("DELETE FROM `" . $data['db_prefix'] . "setting` WHERE `key` = 'config_url'");
            $db->query("INSERT INTO `" . $data['db_prefix'] . "setting` SET `code` = 'config', `key` = 'config_url', value = ''");

            $db->query("INSERT INTO `" . $data['db_prefix'] . "setting` SET `code` = 'config', `key` = 'config_parcelamento_nparcelas', value = '5'");

            $db->query("INSERT INTO `" . $data['db_prefix'] . "setting` SET `code` = 'config', `key` = 'config_parcelamento_juros', value = '0'");

            $db->query("INSERT INTO `" . $data['db_prefix'] . "setting` SET `code` = 'config', `key` = 'config_parcelamento_minima', value = '10'");


            $db->query("DELETE FROM `" . $data['db_prefix'] . "setting` WHERE `key` = 'config_encryption'");
            $db->query("INSERT INTO `" . $data['db_prefix'] . "setting` SET `code` = 'config', `key` = 'config_encryption', value = '" . md5(mt_rand()) . "'");

            $db->query("UPDATE `" . $data['db_prefix'] . "product` SET `viewed` = '0'");

            // create order API user
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            $api_username = '';
            $api_password = '';

            for ($i = 0; $i < 64; $i++) {
                $api_username .= $characters[rand(0, strlen($characters) - 1)];
            }

            for ($i = 0; $i < 256; $i++) {
                $api_password .= $characters[rand(0, strlen($characters) - 1)];
            }

            $db->query("INSERT INTO `" . $data['db_prefix'] . "api` SET username = '" . $api_username . "', `password` = '" . $api_password . "', status = 1, date_added = NOW(), date_modified = NOW()");

            $api_id = $db->insert_id;

            $db->query("DELETE FROM `" . $data['db_prefix'] . "setting` WHERE `key` = 'config_api_id'");
            $db->query("INSERT INTO `" . $data['db_prefix'] . "setting` SET `code` = 'config', `key` = 'config_api_id', value = '" . (int)$api_id . "'");
        }
    }

	public function editStore($store_id, $data) {

        $this->dbcore->query("UPDATE " . DB_PREFIX . "clients SET
		`config_name` = '" . $this->dbcore->escape($data['config_name']) . "',
		`config_nome_contato` = '" . $this->dbcore->escape($data['config_nome_contato']) . "',
		`config_email` = '" . $this->dbcore->escape($data['config_email']) . "',
		`config_documento` = '" . $this->dbcore->escape($data['config_documento']) . "',
		`config_logo` = '" . $this->dbcore->escape($data['config_logo']) . "',
		`config_prod_host` = '" . $this->dbcore->escape($data['config_prod_host']) . "',
		`config_prod_user` = '" . $this->dbcore->escape($data['config_prod_user']) . "',
		`config_prod_pass` = '" . $this->dbcore->escape($data['config_prod_pass']) . "',
		`config_prod_base` = '" . $this->dbcore->escape($data['config_prod_base']) . "',
		`config_dev_host` = '" . $this->dbcore->escape($data['config_dev_host']) . "',
		`config_dev_user` = '" . $this->dbcore->escape($data['config_dev_user']) . "',
		`config_dev_pass` = '" . $this->dbcore->escape($data['config_dev_pass']) . "',
		`config_dev_base` = '" . $this->dbcore->escape($data['config_dev_base']) . "' WHERE id = '".$store_id."'");

       /* insere loja no core */
        $this->dbcore->query("update " . DB_PREFIX_CORE . "store_to_client SET
        dev_url = '" . $this->dbcore->escape($data['config_dev_url']) . "',
        dev_path = '" . $this->dbcore->escape($data['config_dev_path']) . "',
        dev_ssl = '" . $this->dbcore->escape($data['config_dev_ssl']) . "',
        prod_url = '" . $this->dbcore->escape($data['config_prod_url']) . "',
        prod_path = '" . $this->dbcore->escape($data['config_prod_path']) . "',
        prod_ssl = '" . $this->dbcore->escape($data['config_prod_ssl']) . "' WHERE
        id_store = '0' AND
        id_client = '".$store_id."'");

/*
		$this->db->query("UPDATE " . DB_PREFIX . "store SET name = '" . $this->db->escape($data['config_name']) . "',
		`url` = '" . $this->db->escape($data['config_url']) . "', `ssl` = '" . $this->db->escape($data['config_ssl']) . "' WHERE store_id = '" . (int)$store_id . "'");

        $this->dbcore->query("UPDATE " . DB_PREFIX_CORE . "store_to_client SET
        `dev_url` = '" . $this->db->escape($data['config_url']) . "',
        `dev_ssl` = '" . $this->db->escape($data['config_ssl']) . "'
        WHERE id_store = '" . (int)$store_id . "' and id_client = '".ID_CLIENT."'");

*/

        $this->cache->delete('clients');

	}

	public function deleteStore($store_id) {


		$this->db->query("DELETE FROM " . DB_PREFIX . "store WHERE store_id = '" . $store_id . "'");

        $this->dbcore->query("DELETE FROM " . DB_PREFIX_CORE . "store_to_client WHERE id_client = '" . $store_id . "'");

        $this->dbcore->query("DELETE FROM " . DB_PREFIX_CORE . "clients WHERE id = '" . $store_id . "'");

		$this->cache->delete('store');

		$this->event->trigger('post.admin.store.delete', $store_id);
	}

	public function getStore($store_id) {
		$query = $this->dbcore->query("SELECT * FROM " . DB_PREFIX . "clients WHERE id = '" . (int)$store_id . "'");

        $queryx = $this->dbcore->query("SELECT
        dev_url as config_dev_url,
        dev_path as config_dev_path,
        dev_ssl as config_dev_ssl,
        prod_url as config_prod_url,
        prod_path as config_prod_path,
        prod_ssl as config_prod_ssl

        FROM " . DB_PREFIX . "store_to_client WHERE id_client = '" . (int)$store_id . "' and id_store = '0'");

        $result = array_merge($queryx->row, $query->row);

        return $result;

		return $query->row;
	}

	public function getStores($data = array()) {
		$store_data = $this->cache->get('clients');

		if (!$store_data) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store");

			$store_data = $query->rows;

			$this->cache->set('clients', $store_data);
		}

		return $store_data;
	}

    public function getStoresByClient($data = array()) {

            $query = $this->dbcore->query("SELECT * FROM " . DB_PREFIX . "store_to_client
            WHERE id_client = '".$data['id_client']."'");

            $store_data = $query->rows;

            $this->cache->set('clients', $store_data);

        return $store_data;
    }

    public function getClientDefaultStore($data) {

        $query = $this->dbcore->query("SELECT * FROM " . DB_PREFIX . "store_to_client
            WHERE id_client = '".$data."' and id_store='0'");

        $store_data = $query->num_rows;

        return $store_data;
    }

    public function getClients($data = array()) {

        $query = $this->dbcore->query("SELECT * FROM " . DB_PREFIX_CORE . "clients where id!= '0'");
        $store_data = $query->rows;
        return $store_data;
    }

	public function getTotalStores() {
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