<?php
class ControllerMarketingLTNewsletter extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('marketing/lt_newsletter');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('marketing/lt_newsletter');
		
		$this->model_marketing_lt_newsletter->add_table();

		$this->getList();
	}

	public function export()
	{
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename=LTNewsletter_'. date('Y_m_d') .'.csv');
		header('Pragma: no-cache');
		header("Expires: 0");

		$output  = fopen("php://output", "w");  
		
		fputcsv($output, array('Email'));

		$this->load->model('marketing/lt_newsletter');
		
		$rows = $this->model_marketing_lt_newsletter->exportLTNewsletter();
		foreach($rows as $row)
		{
			fputcsv($output, array($row['email']));
		}

		fclose($output);
	}

	public function delete()
	{
		$this->load->language('marketing/lt_newsletter');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('marketing/lt_newsletter');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $id) {
				$this->model_marketing_lt_newsletter->deleteLTNewsletter($id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('marketing/lt_newsletter', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'email';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('marketing/lt_newsletter', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['export'] = $this->url->link('marketing/lt_newsletter/export', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('marketing/lt_newsletter/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['newsletters'] = array();

		$filter_data = array(
			'filter_email'      => $filter_email,
			'sort'              => $sort,
			'order'             => $order,
			'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'             => $this->config->get('config_limit_admin')
		);

		$total = $this->model_marketing_lt_newsletter->getTotalLTNewsletters($filter_data);

		$results = $this->model_marketing_lt_newsletter->getLTNewsletters($filter_data);

		foreach ($results as $result) {
			$data['newsletters'][] = array(
				'id'		   => $result['id'],
				'email'        => $result['email'],
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_id'] = $this->language->get('column_id');
		$data['column_email'] = $this->language->get('column_email');

		$data['entry_email'] = $this->language->get('entry_email');

		$data['button_export'] = $this->language->get('button_export');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');

		$data['token'] = $this->session->data['token'];

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
		
		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_id'] = $this->url->link('marketing/lt_newsletter', 'token=' . $this->session->data['token'] . '&sort=id' . $url, 'SSL');
		$data['sort_email'] = $this->url->link('marketing/lt_newsletter', 'token=' . $this->session->data['token'] . '&sort=email' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('marketing/lt_newsletter', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($total - $this->config->get('config_limit_admin'))) ? $total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $total, ceil($total / $this->config->get('config_limit_admin')));

		$data['filter_email'] = $filter_email;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('marketing/lt_newsletter_list.tpl', $data));
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'marketing/lt_newsletter')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}