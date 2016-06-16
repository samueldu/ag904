<?php
class ControllerModuleGallery extends Controller {
	public function index($setting) {
		static $module = 0;

		$this->load->model('design/banner');
		$this->load->model('tool/image');
		
		$this->document->addStyle('catalog/view/theme/'.$this->config->get('config_template').'/stylesheet/touchTouch/touchTouch.css');
		$this->document->addScript('catalog/view/javascript/jquery/touchTouch/touchTouch.jquery.js');
		
		$data['banners'] = array();

		$results = $this->model_design_banner->getBanner($setting['banner_id']);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$data['banners'][] = array(
					'title' => $result['title'],
					'link'  => $result['link'],
					'thumb' => $this->model_tool_image->resize($result['image'], $setting['thumb_width'], $setting['thumb_height']),
					'image' => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height'])
				);
			}
		}

		$data['module'] = $module++;
		$data['setting'] = $setting;

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/gallery.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/module/gallery.tpl', $data);
		} else {
			return $this->load->view('default/template/module/gallery.tpl', $data);
		}
	}
}