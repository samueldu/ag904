<?php
class ModelShippingTnt extends Model {
	
	private $quote_data = array();
	
	private	$user_login;
	private	$ie;
	private	$ie_destino;
	private	$cnpj;
	private	$situacao_trib;
	private	$situacao_trib_destino;
	private	$tipo_pessoa;
	private	$tipo_pessoa_destino;
	private	$cep_origem;
	private $cep_destino;
	private	$tipo_servico;
	private	$tipo_frete;
	private	$divisao_cliente;
	
	private $largura;
	private $altura;
	private $peso;
	private $peso_total;
	private $valor_nf;
	private $volumes;
	private $text;
	
	private $tnt = array(
		'tnt'				=>'tnt',
		'tnt'				=>'tnt',
	);
	

	
	// função responsável pelo retorno à loja dos valores finais dos valores dos fretes
	function getQuote($address_data)
	{

		// Initialize the variables		
		$user_login = '';
		$ie = '0';
		$ie_destino = '0';
		$cnpj = '0';
		$situacao_trib = 'CO';
		$situacao_trib_destino = 'CO';
		$tipo_pessoa = 'J';
		$tipo_pessoa_destino = 'F';
		$cep_origem = '0';
		$cep_destino = '0';
		$tipo_servico = 'RNC';
		$tipo_frete = 'C';
		$divisao_cliente = 1;
		
		$largura = "";
		$altura = "";
		$peso = 0;
		$peso_total = 0;
		$valor_nf = 0;
		$volumes = 0;
		$text = "";
		
		$this->load->language('shipping/tnt');
		
		if ($this->config->get('tnt_status')) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('tnt_geo_zone_id') . "' AND country_id = '" . (int)$address_data['country_id'] . "' AND (zone_id = '" . (int)$address_data['zone_id'] . "' OR zone_id = '0')");
		
			if (!$this->config->get('tnt_geo_zone_id')) {
				$status = TRUE;
			} elseif ($query->num_rows) {
				$status = TRUE;
			} else {
				$status = FALSE;
			}
		} else {
			$status = FALSE;
		}		
		
		$error = false;
		$method_data = array();
		
		if ($status) {
		
			$currency_id = $this->session->data['currency'];
		
		  // Obtem os dados para a consulta no WebService da tnt
			$client = new SoapClient('http://ws.tntbrasil.com.br/servicos/CalculoFrete?wsdl');
			$user_login = $this->config->get('tnt_user_login');
			$ie = preg_replace ('/[^\d\s]/', '', $this->config->get('tnt_ie'));
			$cnpj = preg_replace ('/[^\d\s]/', '', $this->config->get('tnt_cnpj'));
			$situacao_trib = $this->config->get('tnt_situacao_trib');
			$tipo_pessoa = $this->config->get('tnt_tipo_pessoa');		
			$cep_origem = preg_replace ('/[^\d\s]/', '', $this->config->get('tnt_postcode'));
			$tipo_servico = $this->config->get('tnt_tipo_servico');
			$tipo_frete = $this->config->get('tnt_tipo_frete');
			$divisao_cliente = $this->config->get('tnt_divisao_cliente');
	
			
			// Configura os dados especificos do cliente
			$ie_destino = 0;
			$tipo_pessoa_destino = 'F';
			$situacao_trib_destino = 'CO';
			$cep_destino = preg_replace('/[^\d\s]/', '', $address_data['postcode']); 
		  
		  // Obtem os dados dos produtos
		  //peso ,volumes e valor - $peso $volumes $valor_nf
		  $produtos = $this->cart->getProducts();
		  $volumes = $this->cart->countProducts();
		  $valor_nf = $this->cart->getTotal();
		  
		  // Calcula a cubagem (Comp x Larg x Alt x Qnt x 300)
		  foreach($produtos as $prod){	  
				$peso = (($prod['length'] * 0.01) * ($prod['width'] * 0.01) * ($prod['height'] * 0.01));			
				$peso =(round($peso, 2)) * ($prod['quantity']);
				$peso_total += $peso;
		  }
		  // Multiplica pelo fator de metro quadrado
		  $peso_total = $peso_total * 300;
		  
			// ===============================================
		  // Webservice sem utilizacao de cubagem interna
			// ===============================================
		  //$cubagem = true;
		  //if($peso_total <= 90){
			//		$peso_total = $this->cart->getWeight();
			//		$cubagem = false;
			//}
			// ===============================================
			
			// Consulta ao WebService		
			$parametros = array('in0' => array(
					'cdDivisaoCliente'     							=> $divisao_cliente,
					'cepDestino'      									=> $cep_destino,
					'cepOrigem'       									=> $cep_origem,
					'login'        											=> $user_login,
					'nrIdentifClienteDest'      				=> '45486330146',
					'nrIdentifClienteRem'      					=> $cnpj,
					'nrInscricaoEstadualDestinatario'   => '',
					'nrInscricaoEstadualRemetente'      => $ie,
					'psReal'        										=> number_format($peso_total, 3, '.', ''),
					'senha'           									=> '',
					'tpFrete'      											=> $tipo_frete,
					'tpPessoaDestinatario'     					=> 'F',
					'tpPessoaRemetente'        					=> $tipo_pessoa,
					'tpServico'     										=> $tipo_servico,
					'tpSituacaoTributariaDestinatario' 	=> 'CO',
					'tpSituacaoTributariaRemetente' 		=> $situacao_trib,
					'vlMercadoria'   										=> number_format($valor_nf, 2, '.', ''),
				));
			
			$consulta = $client->calculaFrete($parametros);

			try
			{
				// verifica se nao ocorreu nenhum erro
				if(property_exists($consulta->out->errorList, "string") == false)
				{
					$this->log->write("ENTROU");
					
					$title = $this->language->get('text_tnt_padrao');
					
					// ===============================================
				  // Webservice sem utilizacao de cubagem interna
					// ===============================================
					//if($cubagem == true){
					//	$text = ($consulta->out->vlTotalFrete) + 2;
					//}else{
						$text = $consulta->out->vlTotalFrete;
					//}
					
					$valor_adicional = (is_numeric($this->config->get('tnt_adicional'))) ? $this->config->get('tnt_adicional') : 0 ;
						$new_cost = $text + ($text * ($valor_adicional/100));
					
					$text = $new_cost;
					$custo = $text;
					$text = $this->currency->format($text, $currency_id);
					
					// obtem o prazo e nome do municipio
					$prazo = $consulta->out->prazoEntrega + $this->config->get('tnt_prazo_adicional');
					$cidade_destino = $consulta->out->nmMunicipioDestino;
					
					// Gera a cotação
					$this->quote_data['tnt'] = array(
								'code'         => 'tnt.tnt',
								'title'        => $title . " (Prazo de Entrega para " . $cidade_destino . ": " . $prazo . " Dias.)",
								'cost'         => $custo,
								'tax_class_id' => $this->config->get('tnt_tax_class_id'),
								'tnt' => 'tnt',
								'text'         => $text 
							);			
					$this->log->write("PASSOU");
				}
				else
				{
					// Retorna a mensagem de erro do webservice
					$this->quote_data['tnt'] = array(
								'code'         => 'tnt.tnt',
								'title'        => "Error: " . $consulta->out->errorList->string,
								'cost'         => 0,
								'tax_class_id' => $this->config->get('tnt_tax_class_id'),
								'tnt' => 'tnt',
								'text'         => "" 
							);						
				}
			} catch (Exception $e) {
					$this->log->write("ERROR");
					$this->quote_data['tnt'] = array(
								'code'         => 'tnt.tnt',
								'title'        => "Exceção capturada: " . $e->getMessage(),
								'cost'         => 0,
								'tax_class_id' => $this->config->get('tnt_tax_class_id'),
								'tnt' => 'tnt',
								'text'         => "" 
							);						
			}						

			// Gera o retorno da cotacao			
			$method_data = array(
						'code'       => 'tnt',
						'title'      => $this->language->get('text_title'),
						'quote'      => $this->quote_data,
						'sort_order' => $this->config->get('tnt_sort_order'),
						'error'      => false
						);
		}
		
		return $method_data;
	}

}
?>
