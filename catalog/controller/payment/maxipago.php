<?php

class ControllerPaymentMaxiPago extends Controller {
    public function index() {
        $this->load->language('payment/maxipago');

        $data['text_credit_card'] = $this->language->get('text_credit_card');
        $data['text_loading'] = $this->language->get('text_loading');

        $data['entry_cc_owner'] = $this->language->get('entry_cc_owner');
        $data['entry_cc_number'] = $this->language->get('entry_cc_number');
        $data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');
        $data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');

        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['button_back'] = $this->language->get('button_back');

        $data['months'] = array();

        for ($i = 1; $i <= 12; $i++) {
            $data['months'][] = array(
                'text'  => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)),
                'value' => sprintf('%02d', $i)
            );
        }

        $today = getdate();

        $data['year_expire'] = array();

        for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
            $data['year_expire'][] = array(
                'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
                'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
            );
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/maxipago.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/payment/maxipago.tpl', $data);
        } else {
            return $this->load->view('default/template/payment/maxipago.tpl', $data);
        }
    }

    public function send() {

        set_include_path('./');

        require_once ("lib/maxipago/Autoload.php"); // Remove if using a globa autoloader

        require_once ("lib/maxipago.php");

        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        try {

            $maxiPago = new maxiPago;

            // Before calling any other methods you must first set your credentials
            // Define Logger parameters if preferred
            // Do *NOT* use 'DEBUG' for Production environment as Credit Card details WILL BE LOGGED
            // Severities INFO and up are safe to use in Production as Credi Card info are NOT logged
            $maxiPago->setLogger(dirname(__FILE__).'/logs','INFO');

            // Set your credentials before any other transaction methods
            $maxiPago->setCredentials("5695", "1xvoi8oairqgkpmkhw30djic");

            $maxiPago->setDebug(false);

            $maxiPago->setEnvironment("TEST");

            /*

            $data = array(
                "processorID" => "1", // REQUIRED - Use '1' for testing. Contact our team for production values //
                "referenceNum" => $this->session->data['order_id'], // REQUIRED - Merchant internal order number //
                "chargeTotal" => "10.00", // REQUIRED - Transaction amount in US format //
                "numberOfInstallments" => "2", // Optional - Number of installments for credit card transaction ("parcelas") //
                "chargeInterest" => "N", // Optional - Charge interest flag (Y/N) ("com" e "sem" juros) //
                "currencyCode" => "", // Optional - Valid only for ChasePaymentech multi-currecy setup. Please see full documentation for more info//
                "number" => "4111111111111111", // REQUIRED - Full credit card number //
                "expMonth" => "07", // REQUIRED - Credit card expiration month //
                "expYear" => "2020", // REQUIRED - Credit card expiration year //
                "cvvNumber" => "123", // Optional - Credit card verification number //
                "softDescriptor" => "ORDER12313", // Optional - Text printed in customer's credit card statement (Cielo only) //
                "authentication" => "", // Optional - Valid only for Cielo. Please see full documentation for more info //
                "ipAddress" => "123.123.123.123", // Optional //
                "bname" => "Fulano de Tal", // RECOMMENDED - Customer name //
                "baddress" => "Av. República do Chile, 230", // Optional - Customer address //
                "baddress2" => "16 Andar", // Optional - Customer address //
                "bcity" => "Rio de Janeiro", // Optional - Customer city //
                "bstate" => "RJ", // Optional - Customer state with 2 characters //
                "bpostalcode" => "20031-170", // Optional - Customer zip code //
                "bcountry" => "BR", // Optional - Customer country code per ISO 3166-2 //
                "bphone" => "2140099400", // Optional - Customer phone number //
                "bemail" => "fulanodetal@email.com", // Optional - Customer email address //
                "sname" => "Ciclano de Tal", // Optional - Shipping name //
                "saddress" => "Av. Prestes Maia, 737", // Optional - Shipping address //
                "saddress2" => "20 Andar", // Optional - Shipping address //
                "scity" => "São Paulo", // Optional - Shipping city //
                "sstate" => "SP", // Optional - Shipping stats with 2 characters //
                "spostalcode" => "01031-001", // Optional - Shipping zip code //
                "scountry" => "BR", // Optional - Shipping country code per ISO 3166-2 //
                "sphone" => "1121737900", // Optional - Shipping phone number //
                "semail" => "ciclanodetal@email.com", // Optional - Shipping email address //
                "comments" => "Pedido de teste.", // Optional - Additional comments //
            );

            */

            $data = array(
                "processorID" => "1", // REQUIRED - Use '1' for testing. Contact our team for production values //
                "referenceNum" => $this->session->data['order_id'], // REQUIRED - Merchant internal order number //
                "chargeTotal" => $order_info['total'], // REQUIRED - Transaction amount in US format //
                "bname" => $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'], // HIGHLY RECOMMENDED - Customer name //
                "number" => $this->request->post['cc_number'], // REQUIRED - Full credit card number //
                "expMonth" => $this->request->post['cc_expire_date_month'], // REQUIRED - Credit card expiration month //
                "expYear" => $this->request->post['cc_expire_date_year'], // REQUIRED - Credit card expiration year //
                "cvvNumber" => $this->request->post['cc_cvv2'], // HIGHLY RECOMMENDED - Credit card verification code //
                "bemail" => $order_info['email'],
                "bphone" => $order_info['telephone'],
                "currencyCode" => "BRL"
            );

            $maxiPago->creditCardSale($data);

            if ($maxiPago->isErrorResponse()) {
                $message = "Falha na transação<br>Erro: ".$maxiPago->getMessage();
                $json['error'] = (string)$message;
                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 10, $message, false);
            }

            elseif ($maxiPago->isTransactionResponse()) {
                if ($maxiPago->getResponseCode() == "0") {
                    $message = "Aprovada <br>Codigo: ".$maxiPago->getAuthCode();
                    $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('maxipago_order_status_id'), $message, false);
                    $json['redirect'] = $this->url->link('checkout/success', '', 'SSL');
                }
                else
                {
                    $message = "Negada<br>".$maxiPago->getMessage();
                    $json['error'] = (string)$message;
                    $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 8, $message, false);

                }
            }
        }
        catch (Exception $e)
        {
            $message = $e->getMessage()." in ".$e->getFile()." on line ".$e->getLine();
            $json['error'] = (string)$message;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));

        /*

        $request  = 'MERCHANT_ID=' . urlencode($this->config->get('web_payment_software_merchant_name'));
        $request .= '&MERCHANT_KEY=' . urlencode($this->config->get('web_payment_software_merchant_key'));
        $request .= '&TRANS_TYPE=' . urlencode($this->config->get('web_payment_software_method') == 'capture' ? 'AuthCapture' : 'AuthOnly');
        $request .= '&AMOUNT=' . urlencode($this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false));
        $request .= '&CC_NUMBER=' . urlencode(str_replace(' ', '', $this->request->post['cc_number']));
        $request .= '&CC_EXP=' . urlencode($this->request->post['cc_expire_date_month'] . substr($this->request->post['cc_expire_date_year'], 2));
        $request .= '&CC_CVV=' . urlencode($this->request->post['cc_cvv2']);
        $request .= '&CC_NAME=' . urlencode($order_info['payment_firstname'] . ' ' . $order_info['payment_lastname']);
        $request .= '&CC_COMPANY=' . urlencode($order_info['payment_company']);
        $request .= '&CC_ADDRESS=' . urlencode($order_info['payment_address_1']);
        $request .= '&CC_CITY=' . urlencode($order_info['payment_city']);
        $request .= '&CC_STATE=' . urlencode($order_info['payment_iso_code_2'] != 'US' ? $order_info['payment_zone'] : $order_info['payment_zone_code']);
        $request .= '&CC_ZIP=' . urlencode($order_info['payment_postcode']);
        $request .= '&CC_COUNTRY=' . urlencode($order_info['payment_country']);
        $request .= '&CC_PHONE=' . urlencode($order_info['telephone']);
        $request .= '&CC_EMAIL=' . urlencode($order_info['email']);
        $request .= '&INVOICE_NUM=' . urlencode($this->session->data['order_id']);

        if ($this->config->get('web_payment_software_mode') == 'test') {
            $request .= '&TEST_MODE=1';
        }

        $curl = curl_init('https://secure.web-payment-software.com/gateway');

        curl_setopt($curl, CURLOPT_PORT, 443);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

        $response = curl_exec($curl);

        curl_close($curl);

        //If in test mode strip results to only contain xml data
        if ($this->config->get('web_payment_software_mode') == 'test') {
            $end_index = strpos($response, '</WebPaymentSoftwareResponse>');
            $debug = substr($response, $end_index + 30);
            $response = substr($response, 0, $end_index) . '</WebPaymentSoftwareResponse>';
        }

        //get response xml
        $xml = simplexml_load_string($response);

        //create object to use as json
        $json = array();

        //If successful log transaction in opencart system
        if ('00' === (string)$xml->response_code) {
            $message = '';

            $message .= 'Response Code: ';

            if (isset($xml->response_code)) {
                $message .= (string)$xml->response_code . "\n";
            }

            $message .= 'Approval Code: ';

            if (isset($xml->approval_code)) {
                $message .= (string)$xml->approval_code . "\n";
            }

            $message .= 'AVS Result Code: ';

            if (isset($xml->avs_result_code)) {
                $message .= (string)$xml->avs_result_code . "\n";
            }

            $message .= 'Transaction ID (web payment software order id): ';

            if (isset($xml->order_id)) {
                $message .= (string)$xml->order_id . "\n";
            }

            $message .= 'CVV Result Code: ';

            if (isset($xml->cvv_result_code)) {
                $message .= (string)$xml->cvv_result_code . "\n";
            }

            $message .= 'Response Text: ';

            if (isset($xml->response_text)) {
                $message .= (string)$xml->response_text . "\n";
            }

        */

    }
}
?>