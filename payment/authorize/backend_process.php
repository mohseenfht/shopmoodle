<?php
require_once (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.php');
require_once("$CFG->libdir/formslib.php");
require_once('../lib.php');
$moodo = get_config('local_moodocommerce');
////////////////   Authorize Payment Process Backend ////////////////////////////////

  if($_GET['method'] == 'authorizesend') {
    global $CFG,$USER,$DB;
    $url = 'https://test.authorize.net/gateway/transact.dll'; // Test 
    //$url = 'https://secure.authorize.net/gateway/transact.dll'; // server

    $data = array();

    $data['x_login'] = $moodo->authorize_loginid;
    $data['x_tran_key'] = $moodo->authorize_transkey;
    $data['x_version'] = '3.1';
    $data['x_delim_data'] = 'true';
    $data['x_delim_char'] = '|';
    $data['x_encap_char'] = '"';
    $data['x_relay_response'] = 'false';
    $data['x_first_name'] = html_entity_decode($USER->firstname, ENT_QUOTES, 'UTF-8');
    $data['x_last_name'] = html_entity_decode($USER->lastname, ENT_QUOTES, 'UTF-8');
    $data['x_company'] = html_entity_decode($USER->institution, ENT_QUOTES, 'UTF-8');
    $data['x_address'] = html_entity_decode($USER->address, ENT_QUOTES, 'UTF-8');
    $data['x_city'] = html_entity_decode($USER->city, ENT_QUOTES, 'UTF-8');
    $data['x_state'] = html_entity_decode($USER->country, ENT_QUOTES, 'UTF-8');
    $data['x_zip'] = html_entity_decode($USER->timezone, ENT_QUOTES, 'UTF-8');
    $data['x_country'] = html_entity_decode($USER->country, ENT_QUOTES, 'UTF-8');
    $data['x_phone'] = $USER->phone1;
    $data['x_customer_ip'] = $_SERVER['REMOTE_ADDR'];
    $data['x_email'] = $USER->email;
    $data['x_description'] = html_entity_decode(myCartProductsName($USER->id), ENT_QUOTES, 'UTF-8');
    $data['x_amount'] = myCartTotol($USER->id);
    $data['x_currency_code'] = $moodo->currency_code;
    $data['x_method'] = 'CC';
    $data['x_type'] = 'AUTH_ONLY';
    $data['x_card_num'] = str_replace(' ', '', $_POST['cc_number']);
    $data['x_exp_date'] = $_POST['cc_expire_date_month'] . $_POST['cc_expire_date_year'];
    $data['x_card_code'] = $_POST['cc_cvv2'];
    $data['x_invoice_num'] = 'UID-'.$USER->id.'-'.date('YmdHis');
    $data['x_solution_id'] = 'A1000015';

    /* Customer Shipping Address Fields */
      $data['x_ship_to_first_name'] = html_entity_decode($USER->firstname, ENT_QUOTES, 'UTF-8');
      $data['x_ship_to_last_name'] = html_entity_decode($USER->lastname, ENT_QUOTES, 'UTF-8');
      $data['x_ship_to_company'] = html_entity_decode($USER->institution, ENT_QUOTES, 'UTF-8');
      $data['x_ship_to_address'] = html_entity_decode($USER->address, ENT_QUOTES, 'UTF-8');
      $data['x_ship_to_city'] = html_entity_decode($USER->city, ENT_QUOTES, 'UTF-8');
      $data['x_ship_to_state'] = html_entity_decode($USER->country, ENT_QUOTES, 'UTF-8');
      $data['x_ship_to_zip'] = html_entity_decode($USER->timezone, ENT_QUOTES, 'UTF-8');
      $data['x_ship_to_country'] = html_entity_decode($USER->country, ENT_QUOTES, 'UTF-8');
      $data['x_test_request'] = 'true';


    $curl = curl_init($url);

    curl_setopt($curl, CURLOPT_PORT, 443);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));

    $response = curl_exec($curl);

    $json = array();

    if (curl_error($curl)) {
      $json['error'] = 'CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl);

      //$this->log->write('AUTHNET AIM CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl));
    } elseif ($response) {
      $i = 1;

      $response_info = array();

      $results = explode('|', $response);

      foreach ($results as $result) {
        $response_info[$i] = trim($result, '"');

        $i++;
      }

      if ($response_info[1] == '1') {
        $message = '';

        if (isset($response_info['5'])) {
          $message .= 'Authorization Code: ' . $response_info['5'] . "\n";
        }

        if (isset($response_info['6'])) {
          $message .= 'AVS Response: ' . $response_info['6'] . "\n";
        }

        if (isset($response_info['7'])) {
          $message .= 'Transaction ID: ' . $response_info['7'] . "\n";
        }

        if (isset($response_info['39'])) {
          $message .= 'Card Code Response: ' . $response_info['39'] . "\n";
        }

        if (isset($response_info['40'])) {
          $message .= 'Cardholder Authentication Verification Response: ' . $response_info['40'] . "\n";
        }

        $json['redirect'] = $_POST['return'];
      } else {
        $json['error'] = $response_info[4];
      }
    } else {
      $json['error'] = 'Empty Gateway Response';

      //$this->log->write('AUTHNET AIM CURL ERROR: Empty Gateway Response');
    }

    curl_close($curl);


    echo  json_encode($json);

}
?>
	