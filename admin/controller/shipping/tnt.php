<?php
class ControllerShippingTnt extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->load->language('shipping/tnt');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->model_setting_setting->editSetting('tnt', $this->request->post);		
			
			$this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_aviso'] = $this->language->get('text_aviso');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_sim'] = $this->language->get('text_sim');
		$data['text_nao'] = $this->language->get('text_nao');
		$data['text_tnt_padrao'] = $this->language->get('text_tnt_padrao');
		
		$data['entry_cost'] = $this->language->get('entry_cost');
		$data['entry_tax'] = $this->language->get('entry_tax');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_servicos'] = $this->language->get('entry_servicos');
		$data['entry_prazo_adicional'] = $this->language->get('entry_prazo_adicional');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		
		$data['tab_general'] = $this->language->get('tab_general');
		
		$data['entry_user_login']= $this->language->get('entry_user_login');
		$data['entry_ie']= $this->language->get('entry_ie');
		$data['entry_cnpj']= $this->language->get('entry_cnpj');
		$data['entry_situacao_trib']= $this->language->get('entry_situacao_trib');
		$data['entry_tipo_pessoa']= $this->language->get('entry_tipo_pessoa');
		$data['entry_postcode']= $this->language->get('entry_postcode');
		$data['entry_tipo_servico']= $this->language->get('entry_tipo_servico');
		$data['entry_tipo_frete']= $this->language->get('entry_tipo_frete');
		$data['entry_divisao_cliente']= $this->language->get('entry_divisao_cliente');
		
		$data['entry_aviso_recebimento']= $this->language->get('entry_aviso_recebimento');
		$data['entry_adicional']= $this->language->get('entry_adicional');
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['user_login'])) {
			$data['error_user_login'] = $this->error['user_login'];
		} else {
			$data['error_user_login'] = '';
		}
		if (isset($this->error['ie'])) {
			$data['error_ie'] = $this->error['ie'];
		} else {
			$data['error_ie'] = '';
		}
		if (isset($this->error['cnpj'])) {
			$data['error_cnpj'] = $this->error['cnpj'];
		} else {
			$data['error_cnpj'] = '';
		}
		if (isset($this->error['situacao_trib'])) {
			$data['error_situacao_trib'] = $this->error['situacao_trib'];
		} else {
			$data['error_situacao_trib'] = '';
		}
		if (isset($this->error['tipo_pessoa'])) {
			$data['error_tipo_pessoa'] = $this->error['tipo_pessoa'];
		} else {
			$data['error_tipo_pessoa'] = '';
		}		
		if (isset($this->error['postcode'])) {
			$data['error_postcode'] = $this->error['postcode'];
		} else {
			$data['error_postcode'] = '';
		}
		if (isset($this->error['tipo_servico'])) {
			$data['error_tipo_servico'] = $this->error['tipo_servico'];
		} else {
			$data['error_tipo_servico'] = '';
		}
		if (isset($this->error['tipo_frete'])) {
			$data['error_tipo_frete'] = $this->error['tipo_frete'];
		} else {
			$data['error_tipo_frete'] = '';
		}
		if (isset($this->error['divisao_cliente'])) {
			$data['error_divisao_cliente'] = $this->error['divisao_cliente'];
		} else {
			$data['error_divisao_cliente'] = '';
		}
		
		if (isset($this->error['servico'])) {
			$data['error_servico'] = $this->error['servico'];
		} else {
			$data['error_servico'] = '';
		}		
		
		$data['breadcrumbs'] = array();
   		
   		$data['breadcrumbs'][] = array(
       		'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);
   		$data['breadcrumbs'][] = array(
       		'href'      => $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'),
       		'text'      => $this->language->get('text_shipping'),
      		'separator' => ' :: '
   		);
   		$data['breadcrumbs'][] = array(
       		'href'      => $this->url->link('shipping/tnt', 'token=' . $this->session->data['token'], 'SSL'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);
		
   		$data['action'] = $this->url->link('shipping/tnt', 'token=' . $this->session->data['token'], 'SSL');
		
   		$data['cancel'] = $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset($this->request->post['tnt_status'])) {
			$data['tnt_status'] = $this->request->post['tnt_status'];
		} else {
			$data['tnt_status'] = $this->config->get('tnt_status');
		}
		
		if (isset($this->request->post['tnt_geo_zone_id'])) {
			$data['tnt_geo_zone_id'] = $this->request->post['tnt_geo_zone_id'];
		} else {
			$data['tnt_geo_zone_id'] = $this->config->get('tnt_geo_zone_id');
		}
		
		if (isset($this->request->post['tnt_user_login'])) {
			$data['tnt_user_login'] = $this->request->post['tnt_user_login'];
		} else {
			$data['tnt_user_login'] = $this->config->get('tnt_user_login');
		}
		if (isset($this->request->post['tnt_ie'])) {
			$data['tnt_ie'] = $this->request->post['tnt_ie'];
		} else {
			$data['tnt_ie'] = $this->config->get('tnt_ie');
		}
		if (isset($this->request->post['tnt_cnpj'])) {
			$data['tnt_cnpj'] = $this->request->post['tnt_cnpj'];
		} else {
			$data['tnt_cnpj'] = $this->config->get('tnt_cnpj');
		}
		if (isset($this->request->post['tnt_situacao_trib'])) {
			$data['tnt_situacao_trib'] = $this->request->post['tnt_situacao_trib'];
		} else {
			$data['tnt_situacao_trib'] = $this->config->get('tnt_situacao_trib');
		}
		if (isset($this->request->post['tnt_tipo_pessoa'])) {
			$data['tnt_tipo_pessoa'] = $this->request->post['tnt_tipo_pessoa'];
		} else {
			$data['tnt_tipo_pessoa'] = $this->config->get('tnt_tipo_pessoa');
		}
		if (isset($this->request->post['tnt_postcode'])) {
			$data['tnt_postcode'] = $this->request->post['tnt_postcode'];
		} else {
			$data['tnt_postcode'] = $this->config->get('tnt_postcode');
		}
		if (isset($this->request->post['tnt_tipo_servico'])) {
			$data['tnt_tipo_servico'] = $this->request->post['tnt_tipo_servico'];
		} else {
			$data['tnt_tipo_servico'] = $this->config->get('tnt_tipo_servico');
		}
		if (isset($this->request->post['tnt_tipo_frete'])) {
			$data['tnt_tipo_frete'] = $this->request->post['tnt_tipo_frete'];
		} else {
			$data['tnt_tipo_frete'] = $this->config->get('tnt_tipo_frete');
		}
		if (isset($this->request->post['tnt_divisao_cliente'])) {
			$data['tnt_divisao_cliente'] = $this->request->post['tnt_divisao_cliente'];
		} else {
			$data['tnt_divisao_cliente'] = $this->config->get('tnt_divisao_cliente');
		}
		
		
		if (isset($this->request->post['tnt_adicional'])) {
			$data['tnt_adicional'] = $this->request->post['tnt_adicional'];
		} else {
			$data['tnt_adicional'] = $this->config->get('tnt_adicional');
		}
		if (isset($this->request->post['tnt_prazo_adicional'])) {
			$data['tnt_prazo_adicional'] = $this->request->post['tnt_prazo_adicional'];
		} else {
			$data['tnt_prazo_adicional'] = $this->config->get('tnt_prazo_adicional');
		}
		

		if (isset($this->request->post['tnt_padrao'])) {
			$data['tnt_padrao'] = $this->request->post['tnt_padrao'];
		} else {
			$data['tnt_padrao'] = $this->config->get('tnt_padrao');
		}
		
		
		
		if (isset($this->request->post['tnt_sort_order'])) {
			$data['tnt_sort_order'] = $this->request->post['tnt_sort_order'];
		} else {
			$data['tnt_sort_order'] = $this->config->get('tnt_sort_order');
		}
		$this->load->model('localisation/tax_class');
		
		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
		
		$this->load->model('localisation/geo_zone');
		
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('shipping/tnt.tpl', $data));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'shipping/tnt')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if (!preg_match ("/^([0-9]{2})\.?([0-9]{3})-?([0-9]{3})$/", $this->request->post['tnt_postcode'])) {
			$this->error['postcode'] = $this->language->get('error_postcode');
		}
		if (!isset($this->request->post['tnt_padrao'])){
			$this->error['servico'] = $this->language->get('error_servico');
		}		
		
		return !$this->error;
	}
}
?>
