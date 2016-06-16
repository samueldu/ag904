<?php
class ControllerModuleContact extends Controller {

    public function index(){

        $url = '';


        $this->language->load('module/contact');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('contact/contact');

        $data['heading_title']        = $this->language->get('heading_title');
        $data['text_contact']         = $this->language->get('text_contact');
        $data['text_module']          = $this->language->get('text_module');
        $data['button_delete']        = $this->language->get('button_delete');
        $data['text_no_results']      = $this->language->get('text_no_results');
        $data['button_cancel']        = $this->language->get('button_cancel');
        $data['button_read']        = $this->language->get('button_read');


        $data['column_name']               = $this->language->get('column_name');
        $data['column_email']              = $this->language->get('column_email');
        $data['column_ip']              = $this->language->get('column_ip');
        $data['column_description']        = $this->language->get('column_description');
        $data['heading_title_contact']     = $this->language->get('heading_title_contact');
        $data['column_action']             = $this->language->get('column_action');
        $data['text_view']                 = $this->language->get('text_view');
		$data['text_confirm']              = $this->language->get('text_confirm');
        $data['button_view']               = $this->language->get('button_view');
        $data['button_reply']              = $this->language->get('button_reply');
        $data['button_csv']              = $this->language->get('button_csv');


        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );


        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title_contact_list'),
            'href'      => $this->url->link('module/contact', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );


        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if(isset($this->session->data['error']))
        {
            $data['error'] = $this->session->data['error'];
            unset($this->session->data['error']);
        }else{
            $data['error'] = '';
        }

        if(isset($this->session->data['success']))
        {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }else{
            $data['success'] = '';
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'data';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $data['execute'] = $this->url->link('module/contact/operation', 'token=' . $this->session->data['token'], 'SSL');
        $data['csvfile'] = $this->url->link('module/contact/csvoutput', 'token=' . $this->session->data['token'], 'SSL');

        $filter_data = array(
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $category_total = $this->model_contact_contact->getContacts();

        $pagination = new Pagination();
        $pagination->total = $category_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('module/contact', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($category_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($category_total - $this->config->get('config_limit_admin'))) ? $category_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $category_total, ceil($category_total / $this->config->get('config_limit_admin')));


        $contact_info = $this->model_contact_contact->getContactinfo($filter_data);

        foreach($contact_info as $index=>$contactus){
            $id = $contactus['contact_id'];
            $contact_info[$index]['view'] = $this->url->link('module/contact/contact_details', 'token=' . $this->session->data['token'] .'&id='.$id, 'SSL');
            $contact_info[$index]['reply'] = $this->url->link('module/contact/contact_reply', 'token=' . $this->session->data['token'] .'&id='.$id, 'SSL');
            $contact_info[$index]['data'] = date('d/m/Y H:i:s', strtotime($contact_info[$index]['data']));
        }

        $data['contact_info'] = $contact_info;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');


        $this->response->setOutput($this->load->view('module/contact.tpl', $data));

    }

    public function operation(){
        if(isset($this->request->post['execute'])){
            if($this->request->post['execute']=='delete'){
                $this->delete();
            }
            if($this->request->post['execute']=='markasread'){
                $this->markasread();
            }
        }
        $this->response->redirect($this->url->link('module/contact', 'token=' . $this->session->data['token'] , 'SSL'));
    }

    private function delete(){
        $this->load->model('contact/contact');
        if(isset($this->request->post['execute'])){
            if(isset($this->request->post['selected'])){
                foreach($this->request->post['selected'] as $contact_id)
                    $this->model_contact_contact->deletecontact($contact_id);
            } else {
				
				if (isset($this->request->get['id'])) {
	                $contact_id = $this->request->get['id'];
					$this->model_contact_contact->deletecontact($contact_id);
				}
            }
        }
    }

    private function markasread(){
        $this->load->model('contact/contact');
        if(isset($this->request->post['execute'])){
            if(isset($this->request->post['selected'])){
                foreach($this->request->post['selected'] as $view_id)
                    $this->model_contact_contact->insertvalue($view_id);
            }
            $this->response->redirect($this->url->link('module/contact', 'token=' . $this->session->data['token'] , 'SSL'));
        }
    }

    public function csvoutput(){

        $this->load->model('contact/contact');
        $csv_output = $this->model_contact_contact->csvdata();
        $filename = 'file.csv';
        $output = "";

        $csv_terminated = "\n";
        $csv_separator = ",";
        $csv_enclosed = '"';
        $csv_escaped = "\\";

        foreach ($csv_output as $out) {
            $output .= $csv_enclosed .str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $out["firstname"]) . $csv_enclosed.$csv_separator;
            $output .= $csv_enclosed .str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $out["email"]) . $csv_enclosed.$csv_separator;


            $output .= $csv_terminated;
        }


        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Length: " . strlen($output));
        header("Content-type: text/x-csv");
        header("Content-Disposition: attachment; filename=$filename");
        echo $output;

    }

    public function contact_details(){

        $this->language->load('module/contact');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('contact/contact');

        $data['heading_title']        = $this->language->get('heading_title');
        $data['text_contact']         = $this->language->get('text_contact');
        $data['button_save']          = $this->language->get('button_save');
        $data['button_delete']        = $this->language->get('button_delete');
        $data['text_no_results']      = $this->language->get('text_no_results');
        $data['button_cancel']        = $this->language->get('button_cancel');

        $data['column_select']             = $this->language->get('column_select');
        $data['column_name']               = $this->language->get('column_name');
        $data['column_email']              = $this->language->get('column_email');
        $data['column_ip']              = $this->language->get('column_ip');
        $data['column_description']        = $this->language->get('column_description');
        $data['heading_title_contact']     = $this->language->get('heading_title_contact');
        $data['column_action']             = $this->language->get('column_action');
        $data['text_view']                 = $this->language->get('text_view');
		$data['text_confirm']              = $this->language->get('text_confirm');
        $data['column_action']             = $this->language->get('column_action');
        $data['button_view']               = $this->language->get('button_view');
        $data['button_cancel']             = $this->language->get('button_cancel');
        $data['button_reply']              = $this->language->get('button_reply');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title_contact_list'),
            'href'      => $this->url->link('module/contact', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title_contact_details'),
            'href'      => $this->url->link('module/contact', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );


        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if(isset($this->session->data['error']))
        {
            $data['error'] = $this->session->data['error'];
            unset($this->session->data['error']);
        }else{
            $data['error'] = '';
        }

        if(isset($this->session->data['success']))
        {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }else{
            $data['success'] = '';
        }




        $data['cancel'] = $this->url->link('module/contact', 'token=' . $this->session->data['token'], 'SSL');

        $view_id = $this->request->get['id'];
        if($view_id){
            $this->model_contact_contact->insertvalue($view_id);
        }

        $single_data = $this->model_contact_contact->getSingledata($this->request->get['id']);

        $single_data[0]['enquiry'] = nl2br($single_data[0]['enquiry']);

        $single_data[0]['data'] = date('d/m/Y H:i:s',(strtotime($single_data[0]['data'])));

        foreach($single_data as $contact){
            $data['reply'] = $this->url->link('module/contact/contact_reply', 'token=' . $this->session->data['token']. '&id='.$contact['contact_id'], 'SSL');
            $data['execute'] = $this->url->link('module/contact/operation', 'token=' . $this->session->data['token']. '&id='.$contact['contact_id'], 'SSL');
        }

        $data['single_data'] = $single_data;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');


        $this->response->setOutput($this->load->view('module/contact_details.tpl', $data));

    }

    public function contact_reply(){
        $this->language->load('module/contact');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('contact/contact');

        $data['heading_title']        = $this->language->get('heading_title');
        $data['text_contact']         = $this->language->get('text_contact');
        $data['button_save']          = $this->language->get('button_save');
        $data['button_delete']        = $this->language->get('button_delete');
        $data['text_no_results']      = $this->language->get('text_no_results');
        $data['button_cancel']        = $this->language->get('button_cancel');
        $data['heading_title_contact_reply']        = $this->language->get('heading_title_contact_reply');


        $data['column_name']               = $this->language->get('column_name');
        $data['column_email']              = $this->language->get('column_email');
        $data['column_description']        = $this->language->get('column_description');
        $data['heading_title_contact']     = $this->language->get('heading_title_contact');
        $data['column_action']             = $this->language->get('column_action');
        $data['text_view']                 = $this->language->get('text_view');
        $data['column_action']             = $this->language->get('column_action');
        $data['button_view']               = $this->language->get('button_view');
        $data['button_cancel']             = $this->language->get('button_cancel');
        $data['button_reply']              = $this->language->get('button_reply');
        $data['button_send']               = $this->language->get('button_send');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title_contact_list'),
            'href'      => $this->url->link('module/contact', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title_contact_reply'),
            'href'      => $this->url->link('module/contact', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );


        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if(isset($this->session->data['error']))
        {
            $data['error'] = $this->session->data['error'];
            unset($this->session->data['error']);
        }else{
            $data['error'] = '';
        }

        if(isset($this->session->data['success']))
        {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }else{
            $data['success'] = '';
        }

        $view_id = $this->request->get['id'];
        if($view_id){
            $this->model_contact_contact->insertvalue($view_id);
        }

        $data['send'] = $this->url->link('module/contact/sendMail', 'token=' . $this->session->data['token'], 'SSL');
        $data['cancel'] = $this->url->link('module/contact', 'token=' . $this->session->data['token'], 'SSL');


        $single_data = $this->model_contact_contact->getSingledata($this->request->get['id']);

        $data['single_data'] = $single_data;

        $this->children = array(
            'common/header',
            'common/footer'
        );

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('module/contact_reply.tpl', $data));

    }

    public function sendMail() {

        $this->language->load('module/contact');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $mail = new Mail();
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->hostname = $this->config->get('config_smtp_host');
            $mail->username = $this->config->get('config_smtp_username');
            $mail->password = $this->config->get('config_smtp_password');
            $mail->port = $this->config->get('config_smtp_port');
            $mail->timeout = $this->config->get('config_smtp_timeout');
            $mail->setTo($this->request->post['email']);
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender($this->config->get('config_name'));
            $mail->setSubject(html_entity_decode(sprintf($this->language->get('email_subject'), $this->request->post['name']), ENT_QUOTES, 'UTF-8'));
            $mail->setText(strip_tags(html_entity_decode($this->request->post['enquiry'], ENT_QUOTES, 'UTF-8')));
            $mail->send();
            $this->session->data['success']=$this->language->get('reply_message');
            $this->response->redirect($this->url->link('module/contact', 'token=' . $this->session->data['token'] , 'SSL'));
        }
    }

    public function install(){
        $this->load->model('contact/contact');

        $this->model_contact_contact->install();
    }

    public function uninstall(){
        $this->load->model('contact/contact');

        $this->model_contact_contact->uninstall();
    }



}

?>