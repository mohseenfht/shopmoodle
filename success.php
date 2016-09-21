<?php 
require_once (dirname(dirname(dirname(__FILE__))).'/config.php');
require_login();
$strheading = "Success ";
$PAGE->set_title( $strheading );
require_once('lib.php');
$moodo = get_config('local_moodocommerce');
echo $OUTPUT->header();
global $CFG, $PAGE, $USER,$DB, $goodprice;           
if(checkValidity()){
///////   Getting  record from datbase  start ///////////////
$goodprice =0; 
$goodsName ='' ; 
$goodsDesc ='';
$user_info = $DB->get_record('user_credit', array('user_id' => $USER->id ), '*', MUST_EXIST); 
$nowCredit = $user_info->credit;
$products = myCartitems($USER->id);
foreach ($products as  $v) {
    $goodsName .= trim($v->name).',';
    $goodsDesc .= trim($v->name).'-'.trim($v->category).'-'.trim($v->code).'-'.trim($v->price).',';
}
$goodprice = myCartTotol($USER->id);
//////////////  Getting  record from datbase End ///////////////

///////   Getting  record from encode string start ///////////////
$code = $_GET['method'];
$docode = base64_decode($code);
$array = explode( '-', $docode );
$type = $array['1'];
$userid = $array['0'];
///////   Getting  record from encode string  end ///////////////


/////////////////////////////////////////   Enroll using system credit end ////////////////////////////////////////////////////////
if($type == 'credit' && $USER->id == $userid) {

                if($goodprice <= $nowCredit){
                
                    ///////////// Inserting Into Database  sending email start  ////////////////////////////////////////////////////////////////
                                $insert_data                =  new stdClass(); 
                                $insert_data->hashValidated          =   date('YmsHisA');
                                $insert_data->merchTxnRef             =  time()."-".$USER->firstname.' '.$USER->lastname;
                                $insert_data->merchantID             =  '0000000';
                                $insert_data->orderInfo        =  hyphenize ( substr($goodsName, 0 , 20) ).'...'; 
                                $insert_data->amount        =     $moodo->currency_symbol.' '.$goodprice;
                                $insert_data->txnResponseCode        = 0;
                                $insert_data->receiptNo        =  rand(0 ,2000);
                                $insert_data->transactionNo        = 00000;
                                $insert_data->acqResponseCode        = 00;
                                $insert_data->authorizeID        =  00000; 
                                $insert_data->batchNo        = 00000;
                                $insert_data->cardType        = 'Credit';
                                $insert_data->userid        =$USER->id;
                                $insert_data->email        =$USER->email;
                                $insert_data->trans_date  = date('d-m-Y h:i:s a');
                                $res=$DB->insert_record('user_payment_info',$insert_data);
                    
                                /////// Email Send //////
                               $sendParameter = array(
                                  'Courses' => $goodsName,
                                  'Amount '.$moodo->currency_symbol => $goodprice,
                                  'Receipt No' =>  $insert_data->receiptNo,
                                  'Transaction Type' => $insert_data->cardType  ,
                                  'Date of Transaction' => $insert_data->trans_date
                                );
                               sendNotification($USER->email , 'Course Enrollment' , $sendParameter , 'Order Summary');
                                ///// email Send end /////
                    ///////////// Inserting Into Database  sending email start  ////////////////////////////////////////////////////////////////
                    
                           
                    
                    
                            //// Update Credit Card //////////////////////////////////
                              if($goodprice == $nowCredit){    
                                  global $DB;
                                  $u1 = $DB->execute("UPDATE {user_credit} SET credit=0  WHERE  user_id ='".$USER->id."' ");
                               }
                              if($goodprice < $nowCredit ){
                                  global $DB;
                                  $updatwCredit = $nowCredit - $goodprice;
                                  $u2 =  $DB->execute("UPDATE {user_credit} SET credit = '".$updatwCredit."' WHERE  user_id ='".$USER->id."'" );
                               }
                            //// Update Credit Card //////////////////////////////////

                             foreach ($products as  $value) {
                                  $order_id = $value->code;
                                  $pro = explode('-',$order_id);
                                  $pro_id = $pro[1];
                                  $suess= course_enroll_user($pro_id, $USER->id);
                             } 
                    

                            if($suess) {
                                 $successtxt = "Successfully enroll to the course";
                                 echo '<div class="well">
                                        <h5 style="color:green">' .$successtxt . '</h5>
                                        <a href="'.$CFG->wwwroot.'/course/">Go to My Course</a>
                                      </div>';
                              ///  Delete Cart form the database start .////////
                                foreach ($products as $key => $value) {
                                             $res = $DB->execute("DELETE FROM {carts} WHERE code='".$value->code."'");
                                }
                              /// Delete cart item form the database end ////
                            }
                            else{
                                 $errorTxt = "Unable to enroll student for the course";
                                 echo '<div class="well">
                                        <h5 style="color:red">' .$errorTxt . '</h5>
                                        <a href="'.$CFG->wwwroot.'/local/moodocommerce/checkout.php">go to cart </a>
                                      </div>';
                              }
                }

                else{
                         $errorTxt = "Your credit is low recharge your credit or try diffrent payment method";
                         echo '<div class="well">
                                <h5 style="color:red">' .$errorTxt . '</h5>
                                <a href="'.$CFG->wwwroot.'/local/moodocommerce/checkout.php">go to cart </a>
                              </div>';
                }
}
/////////////////////////////////////////   Enroll using system credit end ////////////////////////////////////////////////////////



//////////////////////////////////////// Enroll using stripe Payment   /////////////////////////////
else if($type == 'stripe' && $USER->id == $userid) {
    if(!empty($_POST) && !empty($_POST['stripeToken']) && !empty($_POST['stripeTokenType']) && !empty($_POST['stripeEmail']) ){
                ///////////// Inserting Into Database  sending email start  ////////////////////////////////////////////////////////////////
                                $insert_data                =  new stdClass(); 
                                $insert_data->hashValidated          =   date('YmsHisA');
                                $insert_data->merchTxnRef             =  time()."-".$USER->firstname.' '.$USER->lastname;
                                $insert_data->merchantID             =  $_POST['stripeToken'];
                                $insert_data->orderInfo        =  hyphenize ( substr($goodsName, 0 , 20) ).'...'; 
                                $insert_data->amount        =     $moodo->currency_symbol." ".$goodprice;
                                $insert_data->txnResponseCode        = 0;
                                $insert_data->receiptNo        =  $_POST['stripeToken'];
                                $insert_data->transactionNo        = 00000;
                                $insert_data->acqResponseCode        = 00;
                                $insert_data->authorizeID        =  00000; 
                                $insert_data->batchNo        = 00000;
                                $insert_data->cardType        = 'Credit card (Strip)';
                                $insert_data->userid        =$USER->id;
                                $insert_data->email        =$USER->email;
                                $insert_data->trans_date  = date('d-m-Y h:i:s a');
                                $res=$DB->insert_record('user_payment_info',$insert_data);
                    
                                /////// Email Send //////
                               $sendParameter = array(
                                  'Courses' => $goodsName,
                                  'Amount '.$moodo->currency_symbol => $goodprice,
                                  'Receipt No' =>  $insert_data->receiptNo,
                                  'Transaction Type' => $insert_data->cardType  ,
                                  'Date of Transaction' => $insert_data->trans_date
                                );
                               sendNotification($USER->email , 'Course Enrollment' , $sendParameter , 'Order Summary');
                                ///// email Send end /////
                    ///////////// Inserting Into Database  sending email start  ////////////////////////////////////////////////////////////////


                                 foreach ($products as  $value) {
                                      $order_id = $value->code;
                                      $pro = explode('-',$order_id);
                                      $pro_id = $pro[1];
                                      $suess= course_enroll_user($pro_id, $USER->id);
                                 } 
                                    if($suess) {
                                         $successtxt = "Successfully enroll to the course";
                                         echo '<div class="well">
                                                <h5 style="color:green">' .$successtxt . '</h5>
                                                <a href="'.$CFG->wwwroot.'/course/">Go to My Course</a>
                                              </div>';
                                      ///  Delete Cart form the database start .////////
                                        foreach ($products as $key => $value) {
                                                     $res = $DB->execute("DELETE FROM {carts} WHERE code='".$value->code."'");
                                        }
                                      /// Delete cart item form the database end ////
                                    }
                                    else{
                                           $errorTxt = "Unable to enroll student for the course";
                                           echo '<div class="well">
                                                  <h5 style="color:red">' .$errorTxt . '</h5>
                                                  <a href="'.$CFG->wwwroot.'/local/moodocommerce/checkout.php">go to cart </a>
                                                </div>';
                                        }
    }
    else{
            $errorTxt = "Unable to enroll student for the course";
             echo '<div class="well">
                    <h5 style="color:red">' .$errorTxt . '</h5>
                    <a href="'.$CFG->wwwroot.'/local/moodocommerce/checkout.php">go to cart </a>
                  </div>';
    }
}
////////////////////////////////////// Enroll Using strip Payment end ///////////////////////////////



//////////////////////////////////////// Enroll using authorize Payment   /////////////////////////////
else if($type == 'authorize' && $USER->id == $userid) {
                ///////////// Inserting Into Database  sending email start  ////////////////////////////////////////////////////////////////
                                $insert_data                =  new stdClass(); 
                                $insert_data->hashValidated          =   date('YmsHisA');
                                $insert_data->merchTxnRef             =  time()."-".$USER->firstname.' '.$USER->lastname;
                                $insert_data->merchantID             =  rand(1000,1000000);
                                $insert_data->orderInfo        =  hyphenize ( substr($goodsName, 0 , 20) ).'...'; 
                                $insert_data->amount        =   $moodo->currency_symbol .' '.  $goodprice;
                                $insert_data->txnResponseCode        = 0;
                                $insert_data->receiptNo        = rand(1000,1000000);
                                $insert_data->transactionNo        = 00000;
                                $insert_data->acqResponseCode        = 00;
                                $insert_data->authorizeID        =  00000; 
                                $insert_data->batchNo        = 00000;
                                $insert_data->cardType        = 'Credit card (Authorize.net)';
                                $insert_data->userid        =$USER->id;
                                $insert_data->email        =$USER->email;
                                $insert_data->trans_date  = date('d-m-Y h:i:s a');
                                $res=$DB->insert_record('user_payment_info',$insert_data);
                    
                                /////// Email Send //////
                               $sendParameter = array(
                                  'Courses' => $goodsName,
                                  'Amount '.$moodo->currency_symbol => $goodprice,
                                  'Receipt No' =>  $insert_data->receiptNo,
                                  'Transaction Type' => $insert_data->cardType  ,
                                  'Date of Transaction' => $insert_data->trans_date
                                );
                               sendNotification($USER->email , 'Course Enrollment' , $sendParameter , 'Order Summary');
                                ///// email Send end /////
                    ///////////// Inserting Into Database  sending email start  ////////////////////////////////////////////////////////////////


                                 foreach ($products as  $value) {
                                      $order_id = $value->code;
                                      $pro = explode('-',$order_id);
                                      $pro_id = $pro[1];
                                      $suess= course_enroll_user($pro_id, $USER->id);
                                 } 
                                    if($suess) {
                                         $successtxt = "Successfully enroll to the course";
                                         echo '<div class="well">
                                                <h5 style="color:green">' .$successtxt . '</h5>
                                                <a href="'.$CFG->wwwroot.'/course/">Go to My Course</a>
                                              </div>';
                                      ///  Delete Cart form the database start .////////
                                        foreach ($products as $key => $value) {
                                                     $res = $DB->execute("DELETE FROM {carts} WHERE code='".$value->code."'");
                                        }
                                      /// Delete cart item form the database end ////
                                    }
                                    else{
                                           $errorTxt = "Unable to enroll student for the course";
                                           echo '<div class="well">
                                                  <h5 style="color:red">' .$errorTxt . '</h5>
                                                  <a href="'.$CFG->wwwroot.'/local/moodocommerce/checkout.php">go to cart </a>
                                                </div>';
                                    }
}
////////////////////////////////////// Enroll Using Skrill Payment end ///////////////////////////////


//////////////////////////////////////// Enroll using skrill Payment   /////////////////////////////
else if($type == 'skrill' && $USER->id == $userid) {
                ///////////// Inserting Into Database  sending email start  ////////////////////////////////////////////////////////////////
                                $insert_data                =  new stdClass(); 
                                $insert_data->hashValidated          =   date('YmsHisA');
                                $insert_data->merchTxnRef             =  time()."-".$USER->firstname.' '.$USER->lastname;
                                $insert_data->merchantID             =  rand(1000,1000000);
                                $insert_data->orderInfo        =  hyphenize ( substr($goodsName, 0 , 20) ).'...'; 
                                $insert_data->amount        =  $moodo->currency_symbol .' '.$goodprice;
                                $insert_data->txnResponseCode        = 0;
                                $insert_data->receiptNo        = rand(1000,1000000);
                                $insert_data->transactionNo        = 00000;
                                $insert_data->acqResponseCode        = 00;
                                $insert_data->authorizeID        =  00000; 
                                $insert_data->batchNo        = 00000;
                                $insert_data->cardType        = 'Credit card (Skrill)';
                                $insert_data->userid        =$USER->id;
                                $insert_data->email        =$USER->email;
                                $insert_data->trans_date  = date('d-m-Y h:i:s a');
                                $res=$DB->insert_record('user_payment_info',$insert_data);
                    
                                /////// Email Send //////
                               $sendParameter = array(
                                  'Courses' => $goodsName,
                                  'Amount '.$moodo->currency_symbol => $goodprice,
                                  'Receipt No' =>  $insert_data->receiptNo,
                                  'Transaction Type' => $insert_data->cardType  ,
                                  'Date of Transaction' => $insert_data->trans_date
                                );
                               sendNotification($USER->email , 'Course Enrollment' , $sendParameter , 'Order Summary');
                                ///// email Send end /////
                    ///////////// Inserting Into Database  sending email start  ////////////////////////////////////////////////////////////////


                                 foreach ($products as  $value) {
                                      $order_id = $value->code;
                                      $pro = explode('-',$order_id);
                                      $pro_id = $pro[1];
                                      $suess= course_enroll_user($pro_id, $USER->id);
                                 } 
                                    if($suess) {
                                         $successtxt = "Successfully enroll to the course";
                                         echo '<div class="well">
                                                <h5 style="color:green">' .$successtxt . '</h5>
                                                <a href="'.$CFG->wwwroot.'/course/">Go to My Course</a>
                                              </div>';
                                      ///  Delete Cart form the database start .////////
                                        foreach ($products as $key => $value) {
                                                     $res = $DB->execute("DELETE FROM {carts} WHERE code='".$value->code."'");
                                        }
                                      /// Delete cart item form the database end ////
                                    }
                                    else{
                                           $errorTxt = "Unable to enroll student for the course";
                                           echo '<div class="well">
                                                  <h5 style="color:red">' .$errorTxt . '</h5>
                                                  <a href="'.$CFG->wwwroot.'/local/moodocommerce/checkout.php">go to cart </a>
                                                </div>';
                                        }
}
////////////////////////////////////// Enroll Using skrill Payment end ///////////////////////////////


//////////////////////////////////////// Enroll using twocheckout Payment   /////////////////////////////
else if($type == 'twocheckout' && $USER->id == $userid) {
                ///////////// Inserting Into Database  sending email start  ////////////////////////////////////////////////////////////////
                                $insert_data                =  new stdClass(); 
                                $insert_data->hashValidated          =   date('YmsHisA');
                                $insert_data->merchTxnRef             =  time()."-".$USER->firstname.' '.$USER->lastname;
                                $insert_data->merchantID             =  rand(1000,1000000);
                                $insert_data->orderInfo        =  hyphenize ( substr($goodsName, 0 , 20) ).'...'; 
                                $insert_data->amount        =     $moodo->currency_symbol." ".$goodprice;
                                $insert_data->txnResponseCode        = 0;
                                $insert_data->receiptNo        = rand(1000,1000000);
                                $insert_data->transactionNo        = 00000;
                                $insert_data->acqResponseCode        = 00;
                                $insert_data->authorizeID        =  00000; 
                                $insert_data->batchNo        = 00000;
                                $insert_data->cardType        = 'Credit card (2Checkout)';
                                $insert_data->userid        =$USER->id;
                                $insert_data->email        =$USER->email;
                                $insert_data->trans_date  = date('d-m-Y h:i:s a');
                                $res=$DB->insert_record('user_payment_info',$insert_data);
                    
                                /////// Email Send //////
                               $sendParameter = array(
                                  'Courses' => $goodsName,
                                  'Amount '.$moodo->currency_symbol => $goodprice,
                                  'Receipt No' =>  $insert_data->receiptNo,
                                  'Transaction Type' => $insert_data->cardType  ,
                                  'Date of Transaction' => $insert_data->trans_date
                                );
                               sendNotification($USER->email , 'Course Enrollment' , $sendParameter , 'Order Summary');
                                ///// email Send end /////
                    ///////////// Inserting Into Database  sending email start  ////////////////////////////////////////////////////////////////


                                 foreach ($products as  $value) {
                                      $order_id = $value->code;
                                      $pro = explode('-',$order_id);
                                      $pro_id = $pro[1];
                                      $suess= course_enroll_user($pro_id, $USER->id);
                                 } 
                                    if($suess) {
                                         $successtxt = "Successfully enroll to the course";
                                         echo '<div class="well">
                                                <h5 style="color:green">' .$successtxt . '</h5>
                                                <a href="'.$CFG->wwwroot.'/course/">Go to My Course</a>
                                              </div>';
                                      ///  Delete Cart form the database start .////////
                                        foreach ($products as $key => $value) {
                                                     $res = $DB->execute("DELETE FROM {carts} WHERE code='".$value->code."'");
                                        }
                                      /// Delete cart item form the database end ////
                                    }
                                    else{
                                           $errorTxt = "Unable to enroll student for the course";
                                           echo '<div class="well">
                                                  <h5 style="color:red">' .$errorTxt . '</h5>
                                                  <a href="'.$CFG->wwwroot.'/local/moodocommerce/checkout.php">go to cart </a>
                                                </div>';
                                        }
}
////////////////////////////////////// Enroll Using twocheckout Payment end ///////////////////////////////


//////////////////////////////////////// Enroll using Paypal Payment   /////////////////////////////
else if($type == 'paypal' && $USER->id == $userid) {
                ///////////// Inserting Into Database  sending email start  ////////////////////////////////////////////////////////////////
                                $insert_data                =  new stdClass(); 
                                $insert_data->hashValidated          =   date('YmsHisA');
                                $insert_data->merchTxnRef             =  time()."-".$USER->firstname.' '.$USER->lastname;
                                $insert_data->merchantID             =  rand(1000,1000000);
                                $insert_data->orderInfo        =  hyphenize ( substr($goodsName, 0 , 20) ).'...'; 
                                $insert_data->amount        =    $moodo->currency_symbol ." ". $goodprice;
                                $insert_data->txnResponseCode        = 0;
                                $insert_data->receiptNo        = rand(1000,1000000);
                                $insert_data->transactionNo        = 00000;
                                $insert_data->acqResponseCode        = 00;
                                $insert_data->authorizeID        =  00000; 
                                $insert_data->batchNo        = 00000;
                                $insert_data->cardType        = 'Credit card (Paypal)';
                                $insert_data->userid        =$USER->id;
                                $insert_data->email        =$USER->email;
                                $insert_data->trans_date  = date('d-m-Y h:i:s a');
                                $res=$DB->insert_record('user_payment_info',$insert_data);
                    
                                /////// Email Send //////
                               $sendParameter = array(
                                  'Courses' => $goodsName,
                                  'Amount '.$moodo->currency_symbol => $goodprice,
                                  'Receipt No' =>  $insert_data->receiptNo,
                                  'Transaction Type' => $insert_data->cardType  ,
                                  'Date of Transaction' => $insert_data->trans_date
                                );
                               sendNotification($USER->email , 'Course Enrollment' , $sendParameter , 'Order Summary');
                                ///// email Send end /////
                    ///////////// Inserting Into Database  sending email start  ////////////////////////////////////////////////////////////////


                                 foreach ($products as  $value) {
                                      $order_id = $value->code;
                                      $pro = explode('-',$order_id);
                                      $pro_id = $pro[1];
                                      $suess= course_enroll_user($pro_id, $USER->id);
                                 } 
                                    if($suess) {
                                         $successtxt = "Successfully enroll to the course";
                                         echo '<div class="well">
                                                <h5 style="color:green">' .$successtxt . '</h5>
                                                <a href="'.$CFG->wwwroot.'/course/">Go to My Course</a>
                                              </div>';
                                      ///  Delete Cart form the database start .////////
                                        foreach ($products as $key => $value) {
                                                     $res = $DB->execute("DELETE FROM {carts} WHERE code='".$value->code."'");
                                        }
                                      /// Delete cart item form the database end ////
                                    }
                                    else{
                                           $errorTxt = "Unable to enroll student for the course";
                                           echo '<div class="well">
                                                  <h5 style="color:red">' .$errorTxt . '</h5>
                                                  <a href="'.$CFG->wwwroot.'/local/moodocommerce/checkout.php">go to cart </a>
                                                </div>';
                                        }
}
////////////////////////////////////// Enroll Using Paypal Payment end ///////////////////////////////


//////////////////////////////////////// Enroll using Paypal Payment   /////////////////////////////
else if($type == 'audipay' && $USER->id == $userid) {
  if (isset($_GET['vpc_TxnResponseCode'])) {
        //function to map each response code number to a text message 
        function getResponseDescription($responseCode) {
            switch ($responseCode) {
                case "0" : $result = "Transaction Successful"; break;
                case "?" : $result = "Transaction status is unknown"; break;
                case "1" : $result = "Unknown Error"; break;
                case "2" : $result = "Bank Declined Transaction"; break;
                case "3" : $result = "No Reply from Bank"; break;
                case "4" : $result = "Expired Card"; break;
                case "5" : $result = "Insufficient funds"; break;
                case "6" : $result = "Error Communicating with Bank"; break;
                case "7" : $result = "Payment Server System Error"; break;
                case "8" : $result = "Transaction Type Not Supported"; break;
                case "9" : $result = "Bank declined transaction (Do not contact Bank)"; break;
                case "A" : $result = "Transaction Aborted"; break;
                case "C" : $result = "Transaction Cancelled"; break;
                case "D" : $result = "Deferred transaction has been received and is awaiting processing"; break;
                case "E" : $result = "Invalid Credit Card"; break;
                case "F" : $result = "3D Secure Authentication failed"; break;
                case "I" : $result = "Card Security Code verification failed"; break;
                case "G" : $result = "Invalid Merchant"; break;
                case "L" : $result = "Shopping Transaction Locked (Please try the transaction again later)"; break;
                case "N" : $result = "Cardholder is not enrolled in Authentication scheme"; break;
                case "P" : $result = "Transaction has been received by the Payment Adaptor and is being processed"; break;
                case "R" : $result = "Transaction was not processed - Reached limit of retry attempts allowed"; break;
                case "S" : $result = "Duplicate SessionID (OrderInfo)"; break;
                case "T" : $result = "Address Verification Failed"; break;
                case "U" : $result = "Card Security Code Failed"; break;
                case "V" : $result = "Address Verification and Card Security Code Failed"; break;
                case "X" : $result = "Credit Card Blocked"; break;
                case "Y" : $result = "Invalid URL"; break;                
                case "B" : $result = "Transaction was not completed"; break;                
                case "M" : $result = "Please enter all required fields"; break;                
                case "J" : $result = "Transaction already in use"; break;
                case "BL" : $result = "Card Bin Limit Reached"; break;                
                case "CL" : $result = "Card Limit Reached"; break;                
                case "LM" : $result = "Merchant Amount Limit Reached"; break;                
                case "Q" : $result = "IP Blocked"; break;                
                case "R" : $result = "Transaction was not processed - Reached limit of retry attempts allowed"; break;                
                case "Z" : $result = "Bin Blocked"; break;

                default  : $result = "Unable to be determined"; 
            }
            return $result;
        }
  
          //function to display a No Value Returned message if value of field is empty
          function null2unknown($data) 
          {
              if ($data == "") 
                  return "No Value Returned";
               else 
                  return $data;
          }     
          //get secure hash value of merchant 
          //get the secure hash sent from payment client
          $vpc_Txn_Secure_Hash = addslashes($_GET["vpc_SecureHash"]);
          unset($_GET["vpc_SecureHash"]); 
          ksort($_GET);
          // set a flag to indicate if hash has been validated
          $errorExists = false;
  //check if the value of response code is valid
  if (strlen($SECURE_SECRET) > 0 && addslashes($_GET["vpc_TxnResponseCode"]) != "7" && addslashes($_GET["vpc_TxnResponseCode"]) != "No Value Returned") 
  {
    //creat an md5 variable to be compared with the passed transaction secure hash to check if url has been tampered with or not
      $md5HashData = $SECURE_SECRET;

    //creat an md5 variable to be compared with the passed transaction secure hash to check if url has been tampered with or not
      $md5HashData_2 = $SECURE_SECRET;

      // sort all the incoming vpc response fields and leave out any with no value
      foreach($_GET as $key => $value) 
      {
          if ($key != "vpc_SecureHash" && strlen($value) > 0 && $key != 'action' ) 
          {
        
        $md5HashData_2 .= str_replace(" ",'+',$value);
              $md5HashData .= $value;
              
          }
      }




      //if transaction secure hash is the same as the md5 variable created 
      if ((strtoupper($vpc_Txn_Secure_Hash) == strtoupper(md5($md5HashData)) || strtoupper($vpc_Txn_Secure_Hash) == strtoupper(md5($md5HashData_2))))
      {
          $hashValidated = "CORRECT";
            ///////////////////////////   Insert If Only If  The Hash is Correct //////////////////////////////////
      //the the fields passed from the url to be displayed
      $amount          = null2unknown(addslashes($_GET["amount"]));
      $locale          = null2unknown(addslashes($_GET["vpc_Locale"]));
      $batchNo         = null2unknown(addslashes($_GET["vpc_BatchNo"]));
      $command         = null2unknown(addslashes($_GET["vpc_Command"]));
      $message         = null2unknown(addslashes($_GET["vpc_Message"]));
      $version         = null2unknown(addslashes($_GET["vpc_Version"]));
      $cardType        = null2unknown(addslashes($_GET["vpc_Card"]));
      $orderInfo       = null2unknown(addslashes($_GET["orderInfo"]));
      $receiptNo       = null2unknown(addslashes($_GET["vpc_ReceiptNo"]));
      $merchantID      = null2unknown(addslashes($_GET["merchant"]));
      $authorizeID     = null2unknown(addslashes($_GET["vpc_AuthorizeId"]));
      $merchTxnRef     = null2unknown(addslashes($_GET["merchTxnRef"]));
      $transactionNo   = null2unknown(addslashes($_GET["vpc_TransactionNo"]));
      $acqResponseCode = null2unknown(addslashes($_GET["vpc_AcqResponseCode"]));
      $txnResponseCode = null2unknown(addslashes($_GET["vpc_TxnResponseCode"]));
  
      // Show 'Error' in title if an error condition
      $errorTxt = "";
  
      // Show this page as an error page if vpc_TxnResponseCode equals '7'
      if ($txnResponseCode == "7" || $txnResponseCode == "No Value Returned" || $errorExists) {
          $errorTxt = "Payment unsuccessfully . Please after some time";
      }
      // This is the display title for 'Receipt' page 

      if($txnResponseCode=="0")
      {
                        
                        global $CFG, $PAGE, $USER,$DB;       
                        foreach ($products as  $value) {
                              $order_id = $value->code;
                              $pro = explode('-',$order_id);
                              $pro_id = $pro[1];
                              $suess= course_enroll_user($pro_id, $USER->id);
                         }
                          if($suess) {
                               $successtxt = "Payment Done Successfully";
                               echo '<div class="well">
                                      <h5 style="color:green">' .$successtxt . '</h5>
                                      <a href="'.$CFG->wwwroot.'/course/">Go to My Course</a>
                                    </div>';
                                    ///  Delete Cart form the database start .////////
                                          foreach ($products as $key => $value) {
                                                       $res = $DB->execute("DELETE FROM {carts} WHERE code='".$value->code."'");
                                          }
                                   /// Delete cart item form the database end ////
                          }
                          else{
                               $errorTxt = "Unable to enroll student for the course";
                               echo '<div class="well">
                                      <h5 style="color:red">' .$errorTxt . '</h5>
                                      <a href="'.$CFG->wwwroot.'/local/moodocommerce/checkout.php">go to cart </a>
                                    </div>';
                            }
                    



                      $insert_data     =  new stdClass(); 
                      $insert_data->hashValidated          =$hashValidated;
                      $insert_data->merchTxnRef             =  $merchTxnRef;
                      $insert_data->merchantID             =  $merchantID;
                      $insert_data->orderInfo        =  hyphenize( $orderInfo ); 
                      $insert_data->amount        =$moodo->currency_symbol ." " .$amount / 100;
                      $insert_data->txnResponseCode        =$txnResponseCode;
                      $insert_data->receiptNo        =  $receiptNo; 
                      $insert_data->transactionNo        =$transactionNo;
                      $insert_data->acqResponseCode        =$acqResponseCode;
                      $insert_data->authorizeID        =  $authorizeID; 
                      $insert_data->batchNo        =$batchNo;
                      $insert_data->cardType        =$cardType;
                      $insert_data->userid        =$USER->id;
                      $insert_data->email        =$USER->email;
                      $insert_data->trans_date  = date('d-m-Y h:i:s a');
                      $res=$DB->insert_record('user_payment_info',$insert_data,true);

                 /////// Email Send //////
                                $sendParameter = array(
                                  'Product' =>  $insert_data->orderInfo,
                                  'Amount '.$moodo->currency_symbol => $insert_data->amount / 100 ,
                                  'Receipt No' =>  $insert_data->receiptNo,
                                  'Transaction Type' => 'Credit Card'  ,
                                  'Date of Transaction' => $insert_data->trans_date
                                );

                                sendNotification($USER->email , 'Course Enrollment' , $sendParameter , 'Order Summary');
                                ///// email Send end /////
                  
                       
                         
      }
            else{ 
                  echo '<div class="well">
                            <h5 style="color:red">' . getResponseDescription($txnResponseCode) .'</h5>
                        </div>' ;
                }      ////// If The hash is Correct ///////////////////////  
            
            
      } 
      else 
      {   
        $txnResponseCode = null2unknown(addslashes($_GET["vpc_TxnResponseCode"]));
          echo $hashValidated = "<div class='well'><h5 style='color:red'> Unable to process your request (Invalid Hash)</h5></div>";
          $errorExists = true;
      }
  } 
  else 
  {
     $txnResponseCode = null2unknown(addslashes($_GET["vpc_TxnResponseCode"]));
     echo $hashValidated = "<div class='well'><h5 style='color:red'> Unable to process your request (Invalid Hash)</h5></div>";
  }
    
  }
//////////////////// check $_GET From audi pay end 
}
////////////////////////////////////// Enroll Using Paypal Payment end ///////////////////////////////



              
else{

$errorTxt = "Unable to enroll student for the course";
echo '<div class="well">
<h5 style="color:red">' .$errorTxt . '</h5>
<a href="'.$CFG->wwwroot.'/local/moodocommerce/checkout.php">go to cart </a>
</div>';

}     

}
else{
      echo getPluginErrorMessage();
}
?>




<?php  echo $OUTPUT->footer();  ?>