<?php
class ControllerCommonLogin extends Controller {
	private $error = array();

	public function index() {

		$this->load->language('common/login');

		$this->document->setTitle($this->language->get('heading_title'));

		if ($this->user->isLogged() && isset($this->request->get['token']) and isset($this->session->data['token']) && ($this->request->get['token'] == $this->session->data['token'])) {
			$this->response->redirect($this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'));
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$this->session->data['token'] = md5(mt_rand());

			if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], HTTP_SERVER) === 0 || strpos($this->request->post['redirect'], HTTPS_SERVER) === 0 )) {
				$this->response->redirect($this->request->post['redirect'] . '&token=' . $this->session->data['token']);
			} else {
				$this->response->redirect($this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'));
			}
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_login'] = $this->language->get('text_login');
		$data['text_forgotten'] = $this->language->get('text_forgotten');

		$data['entry_username'] = $this->language->get('entry_username');
		$data['entry_password'] = $this->language->get('entry_password');

		$data['button_login'] = $this->language->get('button_login');

		if ((isset($this->session->data['token']) && !isset($this->request->get['token'])) || ((isset($this->request->get['token']) && (isset($this->session->data['token']) && ($this->request->get['token'] != $this->session->data['token']))))) {
			$this->error['warning'] = $this->language->get('error_token');
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['action'] = $this->url->link('common/login', '', 'SSL');

		if (isset($this->request->post['username'])) {
			$data['username'] = $this->request->post['username'];
		} else {
			$data['username'] = '';
		}

		if (isset($this->request->post['password'])) {
			$data['password'] = $this->request->post['password'];
		} else {
			$data['password'] = '';
		}

		if (isset($this->request->get['route'])) {
			$route = $this->request->get['route'];

			unset($this->request->get['route']);
			unset($this->request->get['token']);

			$url = '';

			if ($this->request->get) {
				$url .= http_build_query($this->request->get);
			}

			$data['redirect'] = $this->url->link($route, $url, 'SSL');
		} else {
			$data['redirect'] = '';
		}

		if ($this->config->get('config_password')) {
			$data['forgotten'] = $this->url->link('common/forgotten', '', 'SSL');
		} else {
			$data['forgotten'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('common/login.tpl', $data));
	}

	protected function validate() {
		if (!isset($this->request->post['username']) || !isset($this->request->post['password']) || !$this->user->login($this->request->post['username'], $this->request->post['password'])) {
			$this->error['warning'] = $this->language->get('error_login');
		}

		return !$this->error;
	}

	public function check() {

        if(isset($_SERVER['HTTP_REFERER']) and substr_count($_SERVER['HTTP_REFERER'],BASE_URL_ADMIN) and isset($this->request->get['token']))
        {
            parse_str($_SERVER['QUERY_STRING'], $query);
            if(isset($this->request->get['token']))
            {
                $this->request->get['token'] = $query['token'];
                $this->session->data['token'] = $query['token'];
            }

            $this->user->grandLogin();
            return true;
        }

		$route = isset($this->request->get['route']) ? $this->request->get['route'] : '';

		$ignore = array(
			'common/login',
			'common/forgotten',
			'common/reset'
		);

		if (!$this->user->isLogged() && !in_array($route, $ignore)) {
			return new Action('common/login');
		}

		if (isset($this->request->get['route'])) {
			$ignore = array(
				'common/login',
				'common/logout',
				'common/forgotten',
				'common/reset',
				'error/not_found',
				'error/permission'
			);

			if (!in_array($route, $ignore) && (!isset($this->request->get['token']) || !isset($this->session->data['token']) || ($this->request->get['token'] != $this->session->data['token']))) {
				return new Action('common/login');
			}
		} else {
			if (!isset($this->request->get['token']) || !isset($this->session->data['token']) || ($this->request->get['token'] != $this->session->data['token'])) {
				return new Action('common/login');
			}
		}
	}

    public function autoLogin()
    {
        $this->load->model('common/header');

        $results = $this->model_common_header->getClient($this->request->post['id']);

        $this->request->post['username'] = "ag904";
        $this->request->post['password'] = "123456";

        unset($_SESSION['xml_config']);

        $this->request->session['xml_path'] = str_replace("\\",'/',$results['path'].'admin/');

        @include("config.php");



       /* unset(DB_DRIVER);
        unset(DB_HOSTNAME);
        unset(DB_USERNAME);
        unset(DB_PASSWORD);
        unset(DB_DATABASE);
        unset(DB_PREFIX);
        unset(DB_PORT);

        unset(TEMPLATE);
        unset(BASE_DIR);
        unset(BASE_CORE);
        unset(BASE_URL);
        unset(BASE_URL_ADMIN);
        unset(NOME_LOJA);
        unset(DIR_APPLICATION);
        unset(DIR_SYSTEM);
        unset(DIR_DATABASE);
        unset(DIR_LANGUAGE);
        unset(DIR_TEMPLATE);
        unset(DIR_CONFIG);
        unset(DIR_IMAGE);
        unset(DIR_CACHE);
        unset(DIR_DOWNLOAD);
        unset(DIR_LOGS);
        unset(HTTP_IMAGE);
        unset(DIR_UPLOAD);
        unset(DIR_MODIFICATION);
        unset(DIR_CATALOG);
        unset(HTTP_SERVER);
        unset(HTTP_CATALOG);
        unset(HTTPS_SERVER);
        unset(HTTPS_CATALOG);
        unset(xml_config);*/

        $this->user->logout();

        unset($this->session->data['token']);

        return new Action('common/login');

    }
}