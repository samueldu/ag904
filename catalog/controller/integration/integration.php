<?php
class ControllerIntegrationIntegration extends Controller {
	private $error = array();

	public function index() {

		$this->load->model('checkout/order');

        $this->load->model('account/custom_field');

        $this->load->model('account/order');

		$this->load->model('integration/integration');

		$data_filter = array(
            "filter_order_status"=>1,
            "vendor"=>$_GET['vendor'],
            "sort"=>"o.order_id",
            "flag"=>1,
            "order"=>"DESC",
            "type"=>$_GET['type'],
            "limit"=>50);

		$return = $this->model_checkout_order->getOrders($data_filter);

		foreach($return as $key=>$value)
		{
            $data_filter['id_order'] = $return[$key]['order_id'];

			if(!$this->model_integration_integration->checkStatus($data_filter))
			{
				$order_info = $this->model_checkout_order->getOrder($return[$key]['order_id']);

                $order_info['id_order']=$return[$key]['order_id'];

                foreach($order_info['custom_field'] as $keyx=>$valuex)
                {
                    $aux = $this->model_account_custom_field->getCustomField($keyx);
                    $order_info['custom_field'][$aux['name']] = $order_info['custom_field'][$keyx];
                }

                $order_info['produtos'] = array();

                $order_info['produtos'] = $this->model_account_order->getOrderProducts($order_info['order_id']);

                $order_info['vendor']=$_GET['vendor'];

                $order_info['type'] = $_GET['type'];

				$this->$_GET['vendor']($order_info);
			}
            else
            {
                print $return[$key]['order_id']." ja processado<BR>";
            }

		}
    }

	public function vendabem($data)
	{

		$url = 'http://177.101.148.52/vendabemweb/ws/integracao_site_pedidos/';

		$dados= '[{
	"cliente":{
        "nome":"'.$data['firstname'].' '.$data['lastname'].'",
        "cpf":"'.@$data['custom_field']['cpf'].'",
        "endereco":"'.$data['shipping_address_1'].'",
        "nro_endereco":87,
        "cep":'.$data['shipping_postcode'].',
        "cidade":"'.$data['shipping_city'].'",
        "bairro":"'.$data['shipping_address_1'].'",
        "uf":"'.$data['shipping_zone_code'].'",
        "sexo":"M",
        "data_nascimento":"1980-08-15",
        "email":"'.$data['email'].'"
    },
    "pedido":{
        "nro_pedido_site":'.$data['order_id'].',
        "valor_pedido":'.number_format($data['total'],2).',
        "data_venda":"'.date('Y-m-d', strtotime($data['date_added'])).'",
        "convenio":1,
        "condicao":1,
        "tipo_frete": "T",
        "valor_frete":'.number_format($data['frete'],2).'
    },
    "produtos":[';

        foreach($data['produtos'] as $key=>$value) {
            $dados .= '
        {
        "codigo":' . $data['produtos'][$key]['product_id'] . ',
        "quantidade":' . $data['produtos'][$key]['quantity'] . ',
        "valor_unitario":' . $data['produtos'][$key]['price'] . ',
        "valor_desconto":0,
        "descricao":"' . $data['produtos'][$key]['name'] . '"
        },';
        }

        $dados =    rtrim($dados, ',');

        $dados.=']
}]';

		$fields = array('id_usuario' => urlencode('drlympsite'),
			'chave_acesso' => urlencode('UfaG43D2Sc4567HkloRtYA12Av'),
			'pedidos'=>$dados);

		$ch = curl_init();

		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        if(substr_count($result,"GRAVADO COM SUCESSO"))
        {
            $data['flag'] = 1;
            $this->model_integration_integration->insertStatus($data);
        }
        else
        {
            $texto = "erro ao enviar o pedido".$data['order_id']. " ".$result;

            $email_to = "samueldu@gmail.com";
            $mail = new Mail();
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->hostname = $this->config->get('config_smtp_host');
            $mail->username = $this->config->get('config_smtp_username');
            $mail->password = $this->config->get('config_smtp_password');
            $mail->port = $this->config->get('config_smtp_port');
            $mail->timeout = $this->config->get('config_smtp_timeout');
            $mail->setTo($email_to);
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender($this->config->get('config_email'));
            $mail->setSubject("erro ao fazer sync");
            $mail->setText($texto);
            $mail->send();

            print $texto;

        }

		curl_close($ch);
	}
}
?>