<?php 
require_once (dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once('../lib.php');
$moodo = get_config('local_moodocommerce');
global $CFG, $PAGE, $USER,$DB; 
$appendAmp = 0;
$host_url= $CFG->wwwroot.'/';
$goodprice = myCartTotol($USER->id);

if($_GET['method'] == 'skrill'){

            if($_POST['amount'] == $goodprice ) {
              
                if(!empty($_POST['pay_to_email']) ) {

                        ksort($_POST);
                        $md5HashData = 'https://www.moneybookers.com/app/payment.pl?p=Moodle&';
                        
                        foreach($_POST as $key => $value) 
                        {
                            // create the md5 input and URL leaving out any fields that have no value
                            if (strlen($value) > 0 ) {
                                //print 'Key: '.$key.'  Value: '.$value."<br>";
                                // this ensures the first paramter of the URL is preceded by the '?' char
                                if ($appendAmp == 0) 
                                {
                                    $vpcURL .= urlencode($key) . '=' . urlencode($value);
                                    $appendAmp = 1;
                                } else {
                                    $vpcURL .= '&' . urlencode($key) . "=" . urlencode($value);
                                    
                                }
                               
                            }
                        } 
                        ////// Check on submit on every payment start  ///////////////// 
                                  $creditt = getMyCredit($USER->id);
                                  $url = $USER->id."-skrill-".$creditt;
                                  $encode = base64_encode($url);
                        ////// Check on submit on every payment end  ///////////////// 
                         $vpcURL .= "&return_url=".$CFG->wwwroot."local/moodocommerce/success.php?method=".$encode;
                         $vpcURL .= "&status_url=".$CFG->wwwroot."local/moodocommerce/success.php?method=".$encode;
                         $md5HashData .= $vpcURL;

                         //header('location:'.$md5HashData);
                        echo "<script language=\"javascript\">top.location.href='$md5HashData'</script>";
                        exit;
                  }
                 
            }
            else{
                    echo "<script >window.location.href='".$host_url."local/moodocommerce/checkout.php'</script>";
            }
}


if($_GET['method'] == 'paypal'){

              
                if(!empty($_POST['business']) && $_POST['business'] == $moodo->paypal_bussiness  ) {

                        ksort($_POST);
                        $md5HashData = 'https://www.paypal.com/cgi-bin/webscr?';
                        
                        foreach($_POST as $key => $value) 
                        {
                            // create the md5 input and URL leaving out any fields that have no value
                            if (strlen($value) > 0) {
                                //print 'Key: '.$key.'  Value: '.$value."<br>";
                                // this ensures the first paramter of the URL is preceded by the '?' char
                                if ($appendAmp == 0) 
                                {
                                    $vpcURL .= urlencode($key) . '=' . urlencode($value);
                                    $appendAmp = 1;
                                } else {
                                    $vpcURL .= '&' . urlencode($key) . "=" . urlencode($value);
                                }
                               
                            }
                        } 

              $products = myCartitems( $USER->id );
              foreach ($products as $key => $value) {
                $i = $i + $value->qty;
              }



                        ////// Check on submit on every payment start  ///////////////// 
                                  $creditt = getMyCredit($USER->id);
                                  $url = $USER->id."-paypal-".$creditt;
                                  $encode = base64_encode($url);
                        ////// Check on submit on every payment end  ///////////////// 
                         $vpcURL .= "&amount_1=".$goodprice;
                         $vpcURL .= "&quantity_1=".$i;
                         $vpcURL .= "&return=".$CFG->wwwroot."local/moodocommerce/success.php?method=".$encode;
                         $md5HashData .= $vpcURL;
                         //header('location:'.$md5HashData);
                         echo "<script language=\"javascript\">top.location.href='$md5HashData'</script>";
                        exit;
                  }
                 

}


if($_GET['method'] == 'audipay'){

$goodprice = $goodprice * 100;

      if($_POST['amount'] == $goodprice ) {
        
          if(!empty($_POST['accessCode'])) {

                  ksort($_POST);
                  $md5HashData = $moodo->audi_secret;
                  
                  foreach($_POST as $key => $value) {
                      if (strlen($value) > 0 && ($key == 'accessCode' || $key == 'merchTxnRef' || $key == 'merchant' || $key == 'orderInfo' || $key == 'amount')) {
                          if ($appendAmp == 0) 
                          {
                              $vpcURL .= urlencode($key) . '=' . urlencode($value);
                              $appendAmp = 1;
                          } else {
                              $vpcURL .= '&' . urlencode($key) . "=" . urlencode($value);
                          }
                          $md5HashData .= $value;
                      }
                  }


                       ////// Check on submit on every payment start  ///////////////// 
                          $creditt = getMyCredit($USER->id);
                          $url = $USER->id."-audipay-".$creditt;
                          $encode = base64_encode($url);
                        ////// Check on submit on every payment end  ///////////////// 
                       // $vpcURL .= "&returnURL=".$CFG->wwwroot."/local/moodocommerce/success.php?method=".$encode;

                  $newHash .= $vpcURL."&vpc_SecureHash=" . strtoupper(md5($md5HashData));

                  echo "<script language=\"javascript\">top.location.href='https://gw1.audicards.com/TPGWeb/payment/prepayment.action?$newHash'</script>";
                  exit;
            }
           
      }
      else{
              echo "<script >window.location.href='".$host_url."local/moodocommerce/checkout.php'</script>";
      }
                 

}



if($_GET['method'] == 'twocheckout'){

            if($_POST['total'] == $goodprice ) {
              
                if(!empty($_POST['sid']) && $_POST['sid'] == $moodo->twocheckout_accountid  ) {

                        ksort($_POST);
                        $md5HashData = 'https://www.2checkout.com/checkout/purchase?';
                        
                        foreach($_POST as $key => $value) 
                        {
                            // create the md5 input and URL leaving out any fields that have no value
                            if (strlen($value) > 0 && ($key == 'sid' || $key == 'cart_order_id' || $key == 'card_holder_name' || $key == 'email' || $key == 'id_type' || $key == 'return_url')) {
                                //print 'Key: '.$key.'  Value: '.$value."<br>";
                                // this ensures the first paramter of the URL is preceded by the '?' char
                                if ($appendAmp == 0) 
                                {
                                    $vpcURL .= urlencode($key) . '=' . urlencode($value);
                                    $appendAmp = 1;
                                } else {
                                    $vpcURL .= '&' . urlencode($key) . "=" . urlencode($value);
                                }
                               
                            }
                        } 
                        $md5HashData .= $vpcURL;

                       ////// Check on submit on every payment start  ///////////////// 
                                  $creditt = getMyCredit($USER->id);
                                  $url = $USER->id."-twocheckout-".$creditt;
                                  $encode = base64_encode($url);
                        ////// Check on submit on every payment end  ///////////////// 
                         $vpcURL .= "&return_url=".$CFG->wwwroot."local/moodocommerce/success.php?method=".$encode;
                         $md5HashData .= $vpcURL;

                         //header('location:'.$md5HashData);
                        echo "<script language=\"javascript\">top.location.href='$md5HashData'</script>";
                        exit;
                  }              
            }
}
?>