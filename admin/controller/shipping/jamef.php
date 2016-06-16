<?php
class ControllerShippingJamef extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->load->language('shipping/jamef');

		$this->document->setTitle("Transportadora Jamef");
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$this->model_setting_setting->editSetting('jamef', $this->request->post);		
			$this->session->data['success'] = "Dados salvos com sucesso!";
			$this->response->redirect($this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_none'] = $this->language->get('text_none');
		
		$data['entry_rate'] = $this->language->get('entry_rate');
		$data['entry_tax_class'] = $this->language->get('entry_tax_class');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => "Inicial",
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => "Meios de Entrega",
			'href'      => $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$data['breadcrumbs'][] = array(
       		'text'      => "Jamef",
			'href'      => $this->url->link('shipping/jamef', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$data['action'] = $this->url->link('shipping/jamef', 'token=' . $this->session->data['token'], 'SSL');
		
		$data['cancel'] = $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL');
		
		//dados
		if (isset($this->request->post['jamef_tax_class_id'])) {
			$data['jamef_tax_class_id'] = $this->request->post['jamef_tax_class_id'];
		} else {
			$data['jamef_tax_class_id'] = $this->config->get('jamef_tax_class_id');
		}

		if (isset($this->request->post['jamef_geo_zone_id'])) {
			$data['jamef_geo_zone_id'] = $this->request->post['jamef_geo_zone_id'];
		} else {
			$data['jamef_geo_zone_id'] = $this->config->get('jamef_geo_zone_id');
		}
		
		if (isset($this->request->post['jamef_status'])) {
			$data['jamef_status'] = $this->request->post['jamef_status'];
		} else {
			$data['jamef_status'] = $this->config->get('jamef_status');
		}
		
		if (isset($this->request->post['jamef_sort_order'])) {
			$data['jamef_sort_order'] = $this->request->post['jamef_sort_order'];
		} else {
			$data['jamef_sort_order'] = $this->config->get('jamef_sort_order');
		}

        if (isset($this->request->post['jamef_cnpj'])) {
			$data['jamef_cnpj'] = $this->request->post['jamef_cnpj'];
		} else {
			$data['jamef_cnpj'] = $this->config->get('jamef_cnpj');
		}

        if (isset($this->request->post['jamef_origem'])) {
			$data['jamef_origem'] = $this->request->post['jamef_origem'];
		} else {
			$data['jamef_origem'] = $this->config->get('jamef_origem');
		}

        if (isset($this->request->post['jamef_uf'])) {
			$data['jamef_uf'] = $this->request->post['jamef_uf'];
		} else {
			$data['jamef_uf'] = $this->config->get('jamef_uf');
		}

        if (isset($this->request->post['jamef_nome'])) {
			$data['jamef_nome'] = $this->request->post['jamef_nome'];
		} else {
			$data['jamef_nome'] = $this->config->get('jamef_nome');
		}		

		$this->load->model('localisation/tax_class');
		
		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
		
		$this->load->model('localisation/geo_zone');
		
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('shipping/jamef.tpl', $data));

	}

}
?>