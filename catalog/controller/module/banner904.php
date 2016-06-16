<?php
class ControllerModuleBanner904 extends Controller {
    public function index($setting) {
        static $module = 0;

        $this->load->model('design/banner');
        $this->load->model('tool/image');

        $this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.carousel.css');
        $this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.transitions.css');
        $this->document->addScript('catalog/view/javascript/jquery/owl-carousel/owl.carousel.min.js');

        $data['grupos'] = array();

        $data['grupos'] = $this->model_design_banner->getGrupos();

        $data['banners'] = array();

        foreach ($setting['banner'] as $banner_id) {

            $results = $this->model_design_banner->getBanner($banner_id);

            foreach ($results as $result) {
                if (is_file(DIR_IMAGE . $result['image'])) {

                    $file_type = pathinfo($result['image'], PATHINFO_EXTENSION);

                    $file_responsive = str_replace(".".$file_type,'-resp.'.$file_type,$result['image']);

                    if (is_file(DIR_IMAGE . $file_responsive)) {
                        $file_responsive = $file_responsive;
                    }
                    else
                    {
                        $file_responsive = $result['image'];
                    }

                    $data['banners'][] = array(
                        'title' => $result['title'],
                        'subtitle' => $result['description'],
                        'link'  => $result['link'],
                        'image' => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']),
                        'image_resp' => $this->model_tool_image->resize($file_responsive,600,100)
                    );
                }
            }
        }

        shuffle($data['banners']);

        if($setting['limit'] != "0")
        {
            //$data['banners'] = array_slice($data['banners'], 1, (int)$setting['limit']);
        }

        foreach($data['banners'] as $key)
        {
            if(count($data['banners']) > $setting['limit'])
            {
                array_pop($data['banners']);
            }
        }

        $data['module'] = $module++;

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/banner904.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/module/banner904.tpl', $data);
        } else {
            return $this->load->view('default/template/module/banner904.tpl', $data);
        }
    }
}