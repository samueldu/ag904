<?php
class ModelShippingJamef extends Model {
    function getQuote($address) {
        //$this->load->language('shipping/jamef');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('jamef_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if (!$this->config->get('jamef_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $cost = 0;
            $weight = $this->cart->getWeight();

            $peso = number_format($weight,2,',','');
            $cub = $this->getCubic();
            $cnpj = $this->config->get('jamef_cnpj');
            $destino = $address['postcode'];
            $total = number_format($this->cart->getSubTotal(),2,',','');
            $regiao = $this->config->get('jamef_origem');
            $uforigem = $this->config->get('jamef_uf');


            $client = new SoapClient('http://www.jamef.com.br/webservice/JAMW0520.apw?WSDL');

            $function = 'JAMW0520_03';

            $arguments= array('JAMW0520_03' => array(
                'TIPTRA'   	=> 1,
                'CNPJCPF'      => $cnpj,
                'MUNORI'      => $regiao,
                'ESTORI'		=> $uforigem,
          /*      'MUNDES2'      => $address['city'],
                'ESTDES2'     => $address['zone_code'], */
                'SEGPROD'     => '000004',
                'QTDVOL' 	=> $this->cart->countProducts(),
                'PESO'      => $peso,
                'VALMER'      => $total,
                'METRO3'      => $cub,
                'CNPJDES'      => '',
                'FILCOT'      => '03',
                'CEPDES'      => $destino
            ));

            $options = array('location' => 'http://www.jamef.com.br/webservice/JAMW0520.apw');

            $result = $client->__soapCall($function, $arguments, $options);

            if(substr_count($result->JAMW0520_03RESULT->MSGERRO,"Ok -"))
            {
                end($result->JAMW0520_03RESULT->VALFRE->AVALFRE);         // move the internal pointer to the end of the array
                $key = key($result->JAMW0520_03RESULT->VALFRE->AVALFRE);
                $cost = $result->JAMW0520_03RESULT->VALFRE->AVALFRE[$key]->TOTAL;;

                    $quote_data['jamef'] = array(
                        'code'         => 'jamef.jamef',
                        'title'        => $this->config->get('jamef_nome'),
                        'cost'         => $cost,
                        'tax_class_id' => $this->config->get('jamef_tax_class_id'),
                        'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('jamef_tax_class_id'), $this->config->get('config_tax')))
                    );

                    $method_data = array(
                        'code'       => 'jamef',
                        'title'      => $this->config->get('jamef_nome'),
                        'quote'      => $quote_data,
                        'sort_order' => $this->config->get('jamef_sort_order'),
                        'error'      => false
                    );
                }
            }
            else
            {
                $method_data = array(
                    'code'       => 'jamef',
                    'title'      => $this->config->get('jamef_nome'),
                    'quote'      => '',
                    'sort_order' => $this->config->get('jamef_sort_order'),
                    'error'      => true
                );

                //$result->JAMW0520_03RESULT->MSGERRO;
            }

        return $method_data;
    }

    public function CmToM($valor){
        return $valor/100;
    }

    public function Limpar($st){
        $st = str_replace('-','',$st);
        $st = str_replace(' ','',$st);
        $st = str_replace('.','',$st);
        return $st;
    }

    public function getCubic() {
        $cubic = 0;
        foreach ($this->cart->getProducts() as $product) {
            $cubic += ($this->CmToM($product['length']) * $this->CmToM($product['width']) * $this->CmToM($product['height'])) * $product['quantity'];
        }
        $cubic = number_format($cubic,4,',','');
        return $cubic;
    }
}
?>