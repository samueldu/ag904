<?php  
class ControllerModuleLTNewsletter extends Controller
{
	public function index($setting)
	{
		static $module = 0;

		$this->load->language('module/lt_newsletter');
		
		$this->load->model('module/lt_newsletter');

		$this->model_module_lt_newsletter->add_table();
	   
		$data['heading_title'] = $this->language->get('heading_title');
		$data['entry_email'] = $this->language->get('entry_email');
		
		$data['text_intro'] = $this->language->get('text_intro');
		$data['text_description'] = $this->language->get('text_description');
		$data['text_button'] = $this->language->get('text_button');
		
		$data['action'] = $this->url->link('module/lt_newsletter/subscribe', '', 'SSL');
		
		if($this->config->get('lt_newsletter_status'))
		{
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/lt_newsletter.tpl')) {
				return $this->load->view($this->config->get('config_template') . '/template/module/lt_newsletter.tpl', $data);
			} else {
				return $this->load->view('default/template/module/lt_newsletter.tpl', $data);
			}
		}
	}
	
	public function subscribe()
	{
		$this->load->language('module/lt_newsletter');
		
		$this->load->model('module/lt_newsletter');

		if (($this->request->server['REQUEST_METHOD'] == 'POST'))
		{
			$email = $this->request->post['lt_newsletter_email'];
			
			if ((utf8_strlen($email) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $email)) {
				$json['error'] = $this->language->get('error_invalid');
			}
			else{
				$row = $this->model_module_lt_newsletter->row($email);
				if($row)
				{
					$this->model_module_lt_newsletter->unsubscribe($email);
					$json['success'] = $this->language->get('text_unsubscribed');
				}
				else{
					$this->model_module_lt_newsletter->subscribe($email);
					$json['success'] = $this->language->get('text_subscribed');
					
					// Send to main admin email if notify email is enabled
					if ($this->config->get('lt_newsletter_notify'))
					{
						$message  = sprintf($this->language->get('text_message'), $email) . "\n\n";

						$mail = new Mail();
						$mail->protocol = $this->config->get('config_mail_protocol');
						$mail->parameter = $this->config->get('config_mail_parameter');
						$mail->smtp_hostname = $this->config->get('config_mail_smtp_host');
						$mail->smtp_username = $this->config->get('config_mail_smtp_username');
						$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
						$mail->smtp_port = $this->config->get('config_mail_smtp_port');
						$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
							
						$mail->setTo($this->config->get('config_email'));
						$mail->setFrom($email);
						$mail->setSender($email);
						$mail->setSubject($this->language->get('text_subject'));
						$mail->setText($message);
						$mail->send();
					}
					
				}
			}
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
		else{
			$this->response->redirect($this->url->link('common/home', '', 'SSL'));
		}
	}
}