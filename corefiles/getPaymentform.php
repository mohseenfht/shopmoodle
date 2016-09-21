<?php
require_once (dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once("$CFG->libdir/formslib.php");
require_once('../lib.php');

$moodo = get_config('local_moodocommerce');



if($_POST['paytype'] == 'stripe') {
    global $CFG,$USER,$DB;
    $output = '';
    require_once('../payment/stripe/lib/stripe.php');
    $stripe = array(
        "secret_key"      => $moodo->stripe_secretkey,
        "publishable_key" => $moodo->stripe_publishablekey,
    );
    Stripe::setApiKey($moodo->stripe_secretkey);
      ////// Check on submit on every payment start  ///////////////// 
      $creditt = getMyCredit($USER->id);
      $url = $USER->id."-stripe-".$creditt;
      $encode = base64_encode($url);
      ////// Check on submit on every payment end  ///////////////// 


    $output = '<form action="success.php?method='.$encode.'" method="post">
                <script src="https://checkout.stripe.com/checkout.js" class="stripe-button" 
                  data-key='.$moodo->stripe_publishablekey. '
                  data-description="'.myCartProductsName($USER->id).'"
                  data-amount="'.(myCartTotol($USER->id) * 100).'"
                  data-locale="auto"
                  data-currency = "'.strtolower($moodo->currency_code).'"
                  ></script>
              </form>';

    echo $output;
}

if($_POST['paytype'] == 'paypal') {
    global $CFG,$USER,$DB;
    $i = 0;
    $output  = '<form id="paypal_form" action="'.$CFG->wwwroot.'/local/moodocommerce/corefiles/process.php?method=paypal" method="POST" >';
    $output .= '<input type="hidden" name="business" value="'.$moodo->paypal_bussiness.'">';
    $output .=  '<input type="hidden" value="_cart" name="cmd">
                 <input type="hidden" value="1" name="upload">
                 <input type="hidden" value="utf-8" name="charset">
                 <input name="currency_code" type="hidden" value="'.$moodo->currency_code.'" />';
    $output .=   '<input name="first_name" type="hidden" value="'.$USER->firstname.' " /> 
                 <input name="last_name" type="hidden" value="'. $USER->lastname.'" />
                 <input name="email" type="hidden" value="'.$USER->email.'" />
                 <input type="hidden" value="'.$USER->city.'" name="city">
                 <input type="hidden" value="'.$USER->country.'" name="country">
                 <input type="hidden" value="'.time()."-".$USER->id."-". $USER->firstname."-".$USER->lastname.'" name="invoice">
                 <input type="hidden" value="authorization" name="paymentaction">';
    $output .=  '<input type="hidden" name="cancel_return" value="'.$CFG->wwwroot.'" />';

     
    $products = myCartitems( $USER->id );
              foreach ($products as $key => $value) {
                $i = $i + $value->qty;
              }
                $output .= '<input type="hidden" name="item_name_1" value="'.myCartProductsName($USER->id).'" />';
               // $output .= '<input type="hidden" name="quantity_1" value="'.$i.'" />';
            
        
   
        
    $output .='<button  type="submit" class="btn btn-info">Continue to Pay</button>';   
    $output .='</form>';
    echo $output;
}


if($_POST['paytype'] == 'audipay'){
          global $CFG, $PAGE, $USER,$DB; 
          $SECURE_SECRET = $audi_config->sc;
          $pay = "";
          $appendAmp = 0;
          $vpcURL = "";
          $newHash = "";
            $goodprice = 0;
            $goodsName = '';
            $goodsDesc = '';

            $products = myCartitems( $USER->id );
            foreach ($products as $k => $v) {
                $goodsName .= trim($v->name).',';
                $goodsDesc .= trim($v->name).'-'.trim($v->category).'-'.trim($v->code).'-'.trim($v->price).',';
            }
    
          $goodprice = myCartTotol($USER->id);
          $goodprice = $goodprice * 100 ;

            $host_url= $CFG->wwwroot.'/';

            $pay  = '<form id="audipay_form" action="'.  $host_url .'local/moodocommerce/corefiles/process.php?method=audipay" method="POST">';
            $pay .= '<input type="hidden" name="accessCode" value="'.$moodo->audi_merchantaccess.'">';

            $pay .= '<input type="hidden" value="'.$audi_config->mi.'" name="merchant">';
            $pay .= '<input type="hidden" value="'. htmlspecialchars(substr($goodsName,0 ,25) ). '" name="orderInfo">';
            $pay .= '<input name="amount" type="hidden" value="'.$goodprice.'" />';
            $pay .= '<input type="hidden" value="'.time()."-".$USER->firstname.' '.$USER->lastname .'"  name="merchTxnRef">';
            $pay .= '<input type="hidden" name="returnURL" value="'.$host_url.'local/moodocommerce/success.php?action=py" />';
            $pay .= '<input id="confirm-audipay" type="submit" class="btn btn-info"  name="submit" value="Continue to Pay">';   
            $pay .='</form>';
            echo $pay;

}


if($_POST['paytype'] == 'skrill') {
    global $CFG,$USER,$DB,$SITE ,$PAGE;
    $products = myCartitems( $USER->id );
    foreach ($products as $key => $value) {
          $detail1_text .= $value->name . ' x '.$value->qty ." , ";
    }
    $output .= '<form  method="post">
                  <input type="hidden" name="pay_to_email" value="'.$moodo->skrill_email.'" />
                  <input type="hidden" name="recipient_description" value="'.$SITE->fullname.'" />
                  <input type="hidden" name="transaction_id" value="'.$USER->id."".rand(10,1000).'" />
                  <input type="hidden" name="cancel_url" value="'.$CFG->wwwroot.'" />
                  <input type="hidden" name="language" value="'.$SITE->lang.'" />
                  <input type="hidden" name="logo_url" value="'.$PAGE->theme->setting_file_url('logo', 'logo').'" />
                  <input type="hidden" name="pay_from_email" value="'.$USER->email.'" />
                  <input type="hidden" name="firstname" value="'.$USER->firstname.'" />
                  <input type="hidden" name="lastname" value="'.$USER->lastname.'" />
                  <input type="hidden" name="address" value="'.$USER->address.'" />
                  <input type="hidden" name="address2" value="'.$USER->address.'" />
                  <input type="hidden" name="phone_number" value="'.$USER->phone1.'" />
                  <input type="hidden" name="postal_code" value="'.$USER->timezone.'" />
                  <input type="hidden" name="city" value="'.$USER->city.'" />
                  <input type="hidden" name="state" value="'.$USER->country.'" />
                  <input type="hidden" name="country" value="'.$USER->country.'" />
                  <input type="hidden" name="amount" value="'.myCartTotol($USER->id).'" />
                  <input type="hidden" name="currency" value="'.$moodo->currency_code.'" />
                  <input type="hidden" name="detail1_text" value="'.$detail1_text.'" />
                  <input type="hidden" name="merchant_fields" value="order_id" />
                  <input type="hidden" name="order_id" value="'.$USER->id.'-'.date('YmdHis').'" />
                  <input type="hidden" name="platform" value="31974336" />
                  <div class="buttons">
                    <div class="pull-right">
                      <button type="submit" class="btn btn-info" formaction="'.$CFG->wwwroot.'/local/moodocommerce/corefiles/process.php?method=skrill">Continue to Pay</button>
                    </div>
                  </div>
                </form>' ;
     echo $output;
}


if($_POST['paytype'] == 'twocheckout') {
    global $CFG,$USER,$DB;
    $i = 1;
    $output  = '<form id="twocheckout_form" action="'.$CFG->wwwroot.'/local/moodocommerce/corefiles/process.php?method=twocheckout" method="POST" >';
    $output .= '<input type="hidden" name="sid" value="'.$moodo->twocheckout_accountid.'">';
    $output .= '<input type="hidden" value="'.myCartTotol($USER->id).'" name="total">
                <input type="hidden" name="cart_order_id" value="'.$USER->id."".rand(10,1000).'" />
                <input type="hidden" name="card_holder_name" value="'.$USER->firstname .' '.$USER->lastname.'" />
                <input type="hidden" name="street_address" value="'.$USER->address.'" />
                <input type="hidden" name="city" value="'.$USER->city.'" />
                <input type="hidden" name="state" value="'.$USER->country.'" />
                <input type="hidden" name="zip" value="'.$USER->timezone.'" />
                <input type="hidden" name="country" value="'.$USER->country.'" />
                <input type="hidden" name="email" value="'.$USER->email.'" />
                <input type="hidden" name="phone" value="'.$USER->phone1.'" />
                <input type="hidden" name="ship_street_address" value="'.$USER->address.'" />
                <input type="hidden" name="ship_city" value="'.$USER->city.'" />
                <input type="hidden" name="ship_state" value="'.$USER->country.'" />
                <input type="hidden" name="ship_zip" value="'.$USER->timezone.'" />
                <input type="hidden" name="ship_country" value="'.$USER->country.'" />';

     
    $products = myCartitems( $USER->id );
              foreach ($products as $key => $value) {
                $output .= '<input type="hidden" name="c_name_'.$i.'" value="'.$value->name.'" />';
                $output .= '<input type="hidden" name="c_prod_'.$i.'" value="'.$value->code.'" />';
                $output .= '<input type="hidden" name="c_price_'.$i.'" value="'.$value->price.'" />';
                $output .= '<input type="hidden" name="c_description_'.$i.'" value="'.$value->name.'" />';
                $i++;
              }
    $output .= '<input type="hidden" name="id_type" value="1" />
                <input type="hidden" name="demo" value="false" />
                <input type="hidden" name="lang" value="'.$USER->lang.'" />';
   
        
    $output .='<button  type="submit" class="btn btn-info">Continue to Pay</button>';   
    $output .='</form>';
    echo $output;
}



if($_POST['paytype'] == 'hekapay') {
    global $CFG,$USER,$DB;

    $output = '<button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal">Continue to pay</button>
    <div class="modal fade" id="myModal" role="dialog" style="text-align:left !important;">
                  <div class="modal-dialog">
                       <!-- Modal content Start-->
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Redeem Voucher for Credit</h4>
                              </div>
                              <div class="modal-body">
                              <div id="msgdiv">
                              </div>
                               <form enctype="multipart/form-data" method="post" class="form-horizontal" id="payment">
                                  <fieldset>
                                        <div class="form-group col-md-6">
                                            <label>Digits Number:</label>
                                            <input type="text" name="card_number" value="" placeholder=" 14 Digits Number (Scratch Area) " id="input-cc-owner" class="form-control"  style="width:98%"/>
                                        </div>
                                    </fieldset>
                               </form>
                              </div>
                              <div class="modal-footer">
                                <input type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only btn btn-primary" id="button-voucher" value="Continue">
                              </div>
                            </div>
                        <!-- Modal content End-->
                  </div>
              </div>';

    echo $output;
}

if($_POST['paytype'] == 'credit') {
  global $CFG,$USER,$DB;
  $output = ''; 
  $cartt = myCartTotol($USER->id);
  $creditt = getMyCredit($USER->id);
  if($creditt >= $cartt){
      $url = $USER->id."-credit-".$creditt;
      $encode = base64_encode($url);
      $output .= '<form id="enroll_form" action="'.$CFG->wwwroot.'/local/moodocommerce/success.php?method='.$encode.'" method="POST" >
                    <button type="submit" class="btn btn-primary" id="button-enroll">Enroll Now</button>
                  </form>'; 
  }
  else{
      $output .= '<input type="button" class="btn btn-primary" id="button-addcredit" value="Add Credit">'; 
  }
  echo $output;

}



if($_POST['paytype'] == 'authorize') {
    global $CFG,$USER,$DB;

    $output = '<button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal">Continue to pay</button>
    <div class="modal fade" id="myModal" role="dialog" style="text-align:left !important;">
                  <div class="modal-dialog">
                       <!-- Modal content Start-->
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Credit Card Details</h4>
                              </div>
                              <div class="modal-body">
                              <div id="msgdiv">
                              </div>
                               <form enctype="multipart/form-data" method="post" class="form-horizontal" id="payment">
                                  <fieldset>
                                        <div class="form-group col-md-6">
                                            <label>Card Owner:</label>
                                            <input type="text" name="cc_owner" value="" placeholder="Card Owner" id="input-cc-owner" class="form-control"  style="width:98%"/>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Card Number:</label>
                                            <input type="text" class="form-control" id="input-cc-number" placeholder="Card Number" value="" name="cc_number" style="width:98%">
                                        </div>

                                        <div class="form-group ">
                                            <div class="col-md-6">              
                                                <label style="padding-left:0 !important;" class="col-md-12">Card Expiry Date:</label>
                                                   <div style="padding-left:0 !important; width:98%" class="col-md-6">
                                                    <select class="form-control" id="input-cc-expire-date" name="cc_expire_date_month">';
                                                       for ($i = 1; $i <= 12; $i++) { 
                                                               $output .='<option value="' . sprintf('%02d', $i) . '">'.strftime('%B', mktime(0, 0, 0, $i, 1, 2000)).'</option>';
                                                        }  

                                                    $output .='</select>
                                                    <select class="form-control" name="cc_expire_date_year">';
                                                        for ($i = date('Y'); $i < date('Y') + 11; $i++)  { 
                                                                $output .='<option value="'. strftime('%Y', mktime(0, 0, 0, 1, 1, $i)).'">' .strftime('%Y', mktime(0, 0, 0, 1, 1, $i)).'</option>';
                                                        } 
                                                    $output .='</select>
                                                   </div>
                                                   
                                        </div>
                                        </div>
                                         <div class="form-group col-md-6">
                                            <label>Card Security Code (CVV2):</label>
                                            <input type="text" class="col-sm-3 form-control" id="input-cc-cvv2" placeholder="Card Security Code (CVV2)" value="" name="cc_cvv2" style="width:98%">
                                        </div>
                                    </fieldset>
                               </form>
                              </div>
                              <div class="modal-footer">
                                <input type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only btn btn-primary" id="button-confirm" value="Continue to Pay">
                              </div>
                            </div>
                        <!-- Modal content End-->
                  </div>
              </div>';

    echo $output;
}




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



        ////// Check on submit on every payment start  ///////////////// 
          $creditt = getMyCredit($USER->id);
          $url = $USER->id."-authorize-".$creditt;
          $encode = base64_encode($url);
        ////// Check on submit on every payment end  ///////////////// 
        $returnURL =  $CFG->wwwroot.'/local/moodocommerce/success.php?method='.$encode;

        $json['redirect'] = $returnURL;
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










function fht_do_post_request($url, $data, $optional_headers = null)
      {  
      $params = array('http' => array(
              'method' => 'POST',
              'content' => $data
             ));
      if ($optional_headers !== null) 
      {
        $params['http']['header'] = $optional_headers;
      }


      $ctx = stream_context_create($params);
      $fp = @fopen($url, 'rb', false, $ctx);

      print_r($ctx);
      die();



      if (!$fp) 
      {
        throw new Exception("Problem with $url, $php_errormsg");
      }
      $response = @stream_get_contents($fp);
      if ($response === false) 
      {
        throw new Exception("Problem reading data from $url, $php_errormsg");
      }
       
       return $response;
      }















// if($_POST['method'] =='voucher_card') {
//   echo  getipaylinks();
// }


// if($_POST['method'] =='pedapal_credit') {
//   echo  getiCredits();
// }

////////////////// Wallet /////////////////////////
// if($_POST['method'] == 'credit_card__credit'){
//    $amount = $_POST['wallet'];

//    if($_POST['isVoucher'] == '1'){
//         echo getVoucherWalletPayment($amount);
//    }else{

//         echo getWalletPayment($amount);
//    }

// }


// if($_POST['method'] =='voucher_card_credit') {
//   echo  getWalletVoucher();
// }

////////////////// Wallet /////////////////////////



// // Private Methosds  start//===============================================================================//
// function getpaypal() {
//           global $CFG, $PAGE, $USER,$DB; 
//           $audi_config = get_config('local_audiplugin');

//           $SECURE_SECRET = $audi_config->sc;
//           $appendAmp = 0;
//           $vpcURL = "";
//           $newHash = "";


//             $goodprice = 0;
//             $goodsName = '';
//             $goodsDesc = '';
//             foreach ($_SESSION['PRODUCTS'] as $k => $v) {
//                 $goodsName .= trim($v['name']).',';
//                 $goodsDesc .= trim($v['name']).'-'.trim($v['category']).'-'.trim($v['code']).'-'.trim($v['price']).',';
//             }
    
//           $goodprice = myCartTotol($USER->id);
    
//           $goodprice = $goodprice * 100 ;
//           // If  Voucher is valid  start//
//           if(!empty($voucher) && $voucher != '' ){
//               $goodprice = $goodprice - ($voucher * 100) ;
//           }
//           // If  Voucher is valid  end//

//             $host_url= $CFG->wwwroot.'/';

//             $pay  = '<form id="paypal_form" action="'.  $host_url .'local/audiplugin/payment_audi.php" method="POST">';
//             $pay .= '<input type="hidden" name="accessCode" value="'.$audi_config->ma.'">';

//             $pay .= '<input type="hidden" value="'.$audi_config->mi.'" name="merchant">';
//             $pay .= '<input type="hidden" value="'. htmlspecialchars(substr($goodsName,0 ,25) ). '" name="orderInfo">';
//             $pay .= '<input name="amount" type="hidden" value="'.$goodprice.'" />';
//             $pay .= '<input type="hidden" value="'.time()."-".$_SESSION['USER']->firstname.' '.$_SESSION['USER']->lastname .'"  name="merchTxnRef">';
//             $pay .= '<input type="hidden" name="returnURL" value="'.$host_url.'local/audiplugin/return.php?action=py" />';

//             $pay .='<input id="confirm-audi" type="submit" class="btn btn-success pull-right"  name="submit" value="Continue to Pay">';	  
//             $pay .='</form>';
//             return $pay;
// }


// function getAudiPayment($voucher) {

//         global $CFG, $PAGE, $USER,$DB;  
//         $goodprice = 0; $goodsName = ''; $goodsDesc = '';

//         $nowCredit = $voucher;

//         foreach ($_SESSION['PRODUCTS'] as $k => $v) {
//           $goodsName .= trim($v['name']).',';
//           $goodsDesc .= trim($v['name']).'-'.trim($v['category']).'-'.trim($v['code']).'-'.trim($v['price']).',';  
//         }
    
//         $goodprice = myCartTotol($USER->id);

//          if($nowCredit == $goodprice){

//                     foreach ($_SESSION['PRODUCTS'] as  $value) {
//                         $order_id = $value['code'];
//                         $pro = explode('-',$order_id);
//                         $pro_id = $pro[1];
//                         $suess= course_enroll_user($pro_id, $USER->id);
//                      } 
                    

//                     if($suess) {
                                
//                                 $insert_data                =  new stdClass(); 
//                                 $insert_data->hashValidated          =   date('YmsHisA');
//                                 $insert_data->merchTxnRef             =  time()."-".$_SESSION['USER']->firstname.' '.$_SESSION['USER']->lastname;
//                                 $insert_data->merchantID             =  '0000000';
//                                 $insert_data->orderInfo        =    substr($goodsName, 0 , 20).'...'; ; 
//                                 $insert_data->amount        =     $goodprice * 100;
//                                 $insert_data->txnResponseCode        = 0;
//                                 $insert_data->receiptNo        =  rand(1111111111,9999999999);
//                                 $insert_data->transactionNo        = 00000;
//                                 $insert_data->acqResponseCode        = 00;
//                                 $insert_data->authorizeID        =  00000; 
//                                 $insert_data->batchNo        = 00000;
//                                 $insert_data->cardType        = 'Pedapal Voucher';
//                                 $insert_data->userid        =$USER->id;
//                                 $insert_data->email        =$USER->email;
//                                 $insert_data->trans_date  = date('d-m-Y h:i:s a');
//                                 $res=$DB->insert_record('auodi_pament_info',$insert_data,true);
                        
//                                 /////// Email Send //////
//                                 $sendParameter = array(
//                                   'Course' => $insert_data->orderInfo ,
//                                   'Amount ($)' => $goodprice,
//                                   'Receipt No' =>  $insert_data->receiptNo,
//                                   'Transaction Type' => $insert_data->cardType  ,
//                                   'Date of Transaction' => $insert_data->trans_date
//                                 );
//                                 sendNotification($USER->email , 'Pedapal.com : Course Enrollment' , $sendParameter , 'Order Summary');
//                                 ///// email Send end /////

//                                 $successtxt = "Payment Done Successfully";
//                                 echo '<div class="well">
//                                       <h5 style="color:green">' .$successtxt . '</h5>
//                                       <a href="'.$CFG->wwwroot.'/course/">Go to My Course</a>
//                                     </div>';
//                                    ///  Delete Cart form the database start .////////
//                                                 foreach ($_SESSION['PRODUCTS'] as $key => $value) {
//                                                              $res = $DB->execute("DELETE FROM mdl_carts WHERE code='".$value['code']."'");
//                                                 }
//                                   /// Delete cart item form the database end ////
                        
//                                 unset($_SESSION['PRODUCTS']);
//                     }
//                     else{
//                          $errorTxt = "Unable to enroll student for the course";
//                          echo '<div >
//                                 <h5 style="color:red">' .$errorTxt . '</h5>
//                                 <a href="'.$CFG->wwwroot.'/local/audiplugin/checkout.php">go to cart </a>
//                               </div>';
//                       }

//          }
//          elseif ($nowCredit > $goodprice) {
                
                
//                  foreach ($_SESSION['PRODUCTS'] as  $value) {
//                         $order_id = $value['code'];
//                         $pro = explode('-',$order_id);
//                         $pro_id = $pro[1];
//                         $suess= course_enroll_user($pro_id, $USER->id);
//                      } 
                    

//                     if($suess) {

                    
//                           $insert_data                =  new stdClass(); 
//                           $insert_data->hashValidated          =   date('YmsHisA');
//                           $insert_data->merchTxnRef             =  time()."-".$_SESSION['USER']->firstname.' '.$_SESSION['USER']->lastname;
//                           $insert_data->merchantID             =  '0000000';
//                           $insert_data->orderInfo        =  substr($goodsName, 0 , 20).'...'; 
//                           $insert_data->amount        =     $goodprice * 100; 
//                           $insert_data->txnResponseCode        = 0;
//                           $insert_data->receiptNo        =  rand(1111111111,9999999999);
//                           $insert_data->transactionNo        = 00000;
//                           $insert_data->acqResponseCode        = 00;
//                           $insert_data->authorizeID        =  00000; 
//                           $insert_data->batchNo        = 00000;
//                           $insert_data->cardType        = 'Padapal Voucher';
//                           $insert_data->userid        =$USER->id;
//                           $insert_data->email        =$USER->email;
//                           $insert_data->trans_date  = date('d-m-Y h:i:s a');
//                           $res=$DB->insert_record('auodi_pament_info',$insert_data,true);
                        
                        
//                            /////// Email Send //////
//                             $sendParameter = array(
//                               'Course' => $insert_data->orderInfo ,
//                               'Amount ($)' => $goodprice,
//                               'Receipt No' =>  $insert_data->receiptNo,
//                               'Transaction Type' => $insert_data->cardType  ,
//                               'Date of Transaction' => $insert_data->trans_date
//                             );
//                             sendNotification($USER->email , 'Pedapal.com : Course Enrollment' , $sendParameter , 'Order Summary');
//                             ///// email Send end /////
                        
                        
//                           $successtxt = "Payment Done Successfully";
//                            echo '<div >
//                                   <h5 style="color:green">' .$successtxt . '</h5>
//                                   <a href="'.$CFG->wwwroot.'/course/">Go to My Course</a>
//                                 </div>';
//                             ///  Delete Cart form the database start .////////
//                                                 foreach ($_SESSION['PRODUCTS'] as $key => $value) {
//                                                              $res = $DB->execute("DELETE FROM mdl_carts WHERE code='".$value['code']."'");
//                                                 }
//                             /// Delete cart item form the database end ////
                        
//                         unset($_SESSION['PRODUCTS']);
                        
//                         /////  Update Credit Points //////////////////////////
//                         $user_info = $DB->get_record('user', array('id' => $USER->id ), '*', MUST_EXIST); 
//                         $myCredit = $user_info->credit;
//                         $updatwCredit = intval($nowCredit -  $goodprice);
//                         $updatwCreditNew = intval($updatwCredit +  $myCredit);
//                         $DB->execute("UPDATE mdl_user SET credit = '".$updatwCreditNew."' WHERE  id ='". $USER->id."' ");
//                         /////  update Credit End /////////////////////////////////////////////////
                        
                        
//                     }
//                     else{
//                          $errorTxt = "Unable to enroll student for the course";
//                          echo '<div >
//                                 <h5 style="color:red">' .$errorTxt . '</h5>
//                                 <a href="'.$CFG->wwwroot.'/local/audiplugin/checkout.php">go to cart </a>
//                               </div>';
//                       }
                
//          }
//          elseif($nowCredit < $goodprice){
             
             
//                 $user_info = $DB->get_record('user', array('id' => $_SESSION['USER']->id ), '*', MUST_EXIST); 
//                 $myCredit = $user_info->credit;
//                 $updatwCredit = intval($myCredit + $nowCredit);
//                 $DB->execute("UPDATE mdl_user SET credit = '".$updatwCredit."' WHERE  id ='". $_SESSION['USER']->id. "'");

//                 $errorTxt = "Unable to enroll student for the course becuase your voucher price is less than cart total , the voucher amount is added to your credit , please  try diffrent payment method";
//                 echo '<div class="span11" style="text-align : center">
//                         <p style="color:red;">' .$errorTxt . '</p>
//                         <a href="'.$CFG->wwwroot.'/local/audiplugin/checkout.php">Go to cart </a>
//                       </div>';
             
             
             
             
//                   $insert_data                =  new stdClass(); 
//                   $insert_data->hashValidated          =   date('YmsHisA');
//                   $insert_data->merchTxnRef             =  time()."-".$_SESSION['USER']->firstname.' '.$_SESSION['USER']->lastname;
//                   $insert_data->merchantID             =  '0000000';
//                   $insert_data->orderInfo        = 'Credit Deposit'; 
//                   $insert_data->amount        =     $goodprice * 100 ;
//                   $insert_data->txnResponseCode        = 0;
//                   $insert_data->receiptNo        =  rand(1111111111,9999999999);
//                   $insert_data->transactionNo        = 00000;
//                   $insert_data->acqResponseCode        = 00;
//                   $insert_data->authorizeID        =  00000; 
//                   $insert_data->batchNo        = 00000;
//                   $insert_data->cardType        = 'Padapal Voucher';
//                   $insert_data->userid        =$USER->id;
//                   $insert_data->email        =$USER->email;
//                   $insert_data->trans_date  = date('d-m-Y h:i:s a');
//                   $res=$DB->insert_record('auodi_pament_info',$insert_data,true);
             
//                     /////// Email Send //////
//                     $sendParameter = array(
//                       'Product' => $insert_data->orderInfo ,
//                       'Amount ($)' => $goodprice,
//                       'Receipt No' =>  $insert_data->receiptNo,
//                       'Transaction Type' => $insert_data->cardType  ,
//                       'Date of Transaction' => $insert_data->trans_date
//                     );
//                     sendNotification($USER->email , 'Pedapal.com : Credit Deposit' , $sendParameter , 'Order Summary');
//                     ///// email Send end /////
             
//          }

// }




// function  getipaylinks()  {
//           global $CFG, $PAGE, $USER,$DB; 
//           $pay ='';
//           $host_url= $CFG->wwwroot.'/';
//           $pay .='<a  href="'.$host_url.'local/audiplugin/redeem_check.php" class="btn btn-success pull-right" >Continue to Pay</a>';   
//           return $pay;	  
// }



// function getiCredits() {
// global $CFG, $PAGE, $USER,$DB; 

// $pay = '';
// $host_url= $CFG->wwwroot.'/';

//     foreach ($_SESSION['PRODUCTS'] as $k => $v) {
//           $goodsName .= trim($v['name']).',';
//           $goodsDesc .= trim($v['name']).'-'.trim($v['category']).'-'.trim($v['code']).'-'.trim($v['price']).',';    
//     }
    
//     $goodprice = myCartTotol($USER->id);
//     $user_info = $DB->get_record('user', array('id' => $_SESSION['USER']->id ), '*', MUST_EXIST); 


//     if($goodprice <= $user_info->credit){
//       $host_url= $CFG->wwwroot.'/';
//       $pay .='<a  href="'.$host_url.'local/audiplugin/user_enrollment.php" class="btn btn-success pull-right" >Enroll Now</a>';   
//     }
//     else{
//       $pay .='<a  href="credit_buy.php" class="btn btn-success pull-right" >Add Credits to account</a>';   
//     }
// return $pay;
// }



// ////Wallet/////////
// function getWalletPayment($price) {
//           global $CFG, $PAGE, $USER,$DB; 
//           $audi_config = get_config('local_audiplugin');

//           $appendAmp = 0;
//           $vpcURL = "";
//           $newHash = "";


//             $goodprice = 0;
//             $goodsName = 'Credit Deposit';
//             $goodsDesc = 'Credit Deposit';
           
//           $goodprice = $price;
//           $goodprice = $goodprice * 100 ;
//           // If  Voucher is valid  start//
//           if(!empty($voucher) && $voucher != '' ){
//               $goodprice = $goodprice - ($voucher * 100) ;
//           }
//           // If  Voucher is valid  end//

//           $host_url= $CFG->wwwroot.'/';

//           $pay  = '<form id="paypal_form" action="'.  $host_url .'local/audiplugin/payment_wallet.php" method="POST">';
//           $pay .= '<input type="hidden" name="accessCode" value="'.$audi_config->ma.'">';

//           $pay .= '<input type="hidden" value="'.$audi_config->mi.'" name="merchant">';
//           $pay .= '<input type="hidden" value="'. htmlspecialchars($goodsName). '" name="orderInfo">';
//           $pay .= '<input name="amount" type="hidden" value="'.$goodprice.'" />';
//           $pay .= '<input type="hidden" value="'.time()."-".$_SESSION['USER']->firstname.' '.$_SESSION['USER']->lastname .'"  name="merchTxnRef">';
//           $pay .= '<input type="hidden" name="returnURL" value="'.$host_url.'local/audiplugin/return_wallet.php?action=py" />';
    
//           $pay .='<input id="confirm-audi" type="submit" class="btn btn-success"  name="submit" value="Continue to Pay">';   
//           $pay .='</form>';
//           return $pay;
// }


// function getVoucherWalletPayment($price) {
//           global $CFG, $PAGE, $USER,$DB; 
//           $pay = '';
//           $host_url= $CFG->wwwroot.'/';
    
//           $insert_data                =  new stdClass(); 
//           $insert_data->hashValidated          =   date('YmsHisA');
//           $insert_data->merchTxnRef             =  time()."-".$_SESSION['USER']->firstname.' '.$_SESSION['USER']->lastname;
//           $insert_data->merchantID             =  '0000000';
//           $insert_data->orderInfo        = 'Credit Deposit'; 
//           $insert_data->amount        =     $price * 100 ;
//           $insert_data->txnResponseCode        = 0;
//           $insert_data->receiptNo        =  rand(1111111111,9999999999);
//           $insert_data->transactionNo        = 00000;
//           $insert_data->acqResponseCode        = 00;
//           $insert_data->authorizeID        =  00000; 
//           $insert_data->batchNo        = 00000;
//           $insert_data->cardType        = 'Padapal Voucher';
//           $insert_data->userid        =$USER->id;
//           $insert_data->email        =$USER->email;
//           $insert_data->trans_date  = date('d-m-Y h:i:s a');
//           $res=$DB->insert_record('auodi_pament_info',$insert_data,true);
    
//          /////// Email Send //////
//             $sendParameter = array(
//               'Product' => $insert_data->orderInfo ,
//               'Amount ($)' => $price,
//               'Receipt No' =>  $insert_data->receiptNo,
//               'Transaction Type' => $insert_data->cardType  ,
//               'Date of Transaction' => $insert_data->trans_date
//             );

//             sendNotification($USER->email , 'Pedapal.com : Credit Deposit' , $sendParameter , 'Order Summary');
//          ///// email Send end /////
    
    
//           $user_info = $DB->get_record('user', array('id' => $_SESSION['USER']->id ), '*', MUST_EXIST); 
//           $nowCredit = $user_info->credit;
//           $updatwCredit = intval($nowCredit + $price);
//           $DB->execute("UPDATE mdl_user SET credit = '".$updatwCredit."'  WHERE  id ='". $_SESSION['USER']->id. "'" );
          
//           $pay .='<p style="color:green">Credit Points Added to your account</p>';   
//           $pay .='<a href="'.$host_url.'local/audiplugin/credit_buy.php" title="Go to cart" class="btn btn-success" > Check Credit </a>';
//           return $pay;
// }

// function  getWalletVoucher()  {
//           global $CFG, $PAGE, $USER,$DB; 
//           $pay = '';
//           $host_url= $CFG->wwwroot.'/';
//           $pay .='<a  href="'.$host_url.'local/audiplugin/redeem_credit.php" class="btn btn-warning" >Continue to Redeem</a>';   
//           return $pay;    
// }


////Wallet End ////////////
// Private Methosds  end//===============================================================================//


?>