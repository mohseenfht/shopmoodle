<?php
//error_reporting(0);
require_once (dirname(dirname(dirname(__FILE__))).'/config.php');
//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");
require_once('lib.php');
require_login();
$PAGE->set_pagelayout('standard');
$audi_config = get_config('local_audiplugin');





////////////////// Wallet /////////////////////////
if($_POST['method'] == 'credit_card__credit'){
   $amount = $_POST['wallet'];
   $child = $_POST['child'];

   if($_POST['isVoucher'] == '1'){
        echo getVoucherWalletPayment($amount, $child);
   }else{

        echo getWalletPayment($amount , $child);
   }

}


if($_POST['method'] =='voucher_card_credit') {
     $child = $_POST['child'];
     echo  getWalletVoucher($child);
}

////////////////// Wallet /////////////////////////



// Private Methosds  start//===============================================================================//


function getAudiPayment($voucher) {

        global $CFG, $PAGE, $USER,$DB;  

        $nowCredit = $voucher;

        foreach ($_SESSION['PRODUCTS'] as $k => $v) {
          $i++;
          $goodprice = $goodprice + $v["price"] * $v['qty'];
          $goodsName .= trim($v['name']).',';
          $goodsDesc .= trim($v['name']).'-'.trim($v['category']).'-'.trim($v['code']).'-'.trim($v['price']).',';  
        }

         if($nowCredit == $goodprice){

                    foreach ($_SESSION['PRODUCTS'] as  $value) {
                        $order_id = $value['code'];
                        $pro = explode('-',$order_id);
                        $pro_id = $pro[1];
                        $suess= course_enroll_user($pro_id, $USER->id);
                     } 
                    

                    if($suess) {
                                $goodprice = $goodprice * 100; 
                                $insert_data                =  new stdClass(); 
                                $insert_data->hashValidated          =   date('YmsHisA');
                                $insert_data->merchTxnRef             =  time()."-".$_SESSION['USER']->firstname.' '.$_SESSION['USER']->lastname;
                                $insert_data->merchantID             =  '0000000';
                                $insert_data->orderInfo        =  $goodsName; 
                                $insert_data->amount        =     $goodprice;
                                $insert_data->txnResponseCode        = 0;
                                $insert_data->receiptNo        =  rand(1111111111,9999999999);;
                                $insert_data->transactionNo        = 00000;
                                $insert_data->acqResponseCode        = 00;
                                $insert_data->authorizeID        =  00000; 
                                $insert_data->batchNo        = 00000;
                                $insert_data->cardType        = 'Pedapal Voucher';
                                $insert_data->userid        =$USER->id;
                                $insert_data->email        =$USER->email;
                                $insert_data->trans_date  = date('d-m-Y h:i:s a');
                                $res=$DB->insert_record('auodi_pament_info',$insert_data,true);
                        
                                /////// Email Send //////
                                    $sendParameter = array(
                                      'Course' => $insert_data->orderInfo ,
                                      'Amount ($)' => $goodprice /100 ,
                                      'Receipt No' =>  $insert_data->receiptNo,
                                      'Transaction Type' => $insert_data->cardType  ,
                                      'Date of Transaction' => $insert_data->trans_date
                                    );
                                    sendNotification($USER->email , 'Pedapal.com : Course Enrollment' , $sendParameter , 'Order Summary');
                                ///// email Send end /////

                               $successtxt = "Payment Done Successfully";
                               echo '<div class="well">
                                      <h5 style="color:green">' .$successtxt . '</h5>
                                      <a href="'.$CFG->wwwroot.'/course/">Go to My Course</a>
                                    </div>';
                                   ///  Delete Cart form the database start .////////
                                                foreach ($_SESSION['PRODUCTS'] as $key => $value) {
                                                             $res = $DB->execute("DELETE FROM mdl_carts WHERE code='".$value['code']."'");
                                                }
                                  /// Delete cart item form the database end ////
                        
                               unset($_SESSION['PRODUCTS']);
                    }
                    else{
                         $errorTxt = "Unable to enroll student for the course";
                         echo '<div >
                                <h5 style="color:red">' .$errorTxt . '</h5>
                                <a href="'.$CFG->wwwroot.'/local/audiplugin/checkout.php">go to cart </a>
                              </div>';
                      }

         }
         elseif ($nowCredit > $goodprice) {
                
                $user_info = $DB->get_record('user', array('id' => $_SESSION['USER']->id ), '*', MUST_EXIST); 
                $myCredit = $user_info->credit;
                $updatwCredit = intval($nowCredit -  $goodprice);
                $updatwCreditNew = intval($updatwCredit +  $myCredit);
                $DB->execute("UPDATE mdl_user SET credit = ".$updatwCredit." WHERE  id =". $_SESSION['USER']->id);

                 foreach ($_SESSION['PRODUCTS'] as  $value) {
                        $order_id = $value['code'];
                        $pro = explode('-',$order_id);
                        $pro_id = $pro[1];
                        $suess= course_enroll_user($pro_id, $USER->id);
                     } 
                    

                    if($suess) {

                          $goodprice = $goodprice * 100; 
                          $insert_data                =  new stdClass(); 
                          $insert_data->hashValidated          =   date('YmsHisA');
                          $insert_data->merchTxnRef             =  time()."-".$_SESSION['USER']->firstname.' '.$_SESSION['USER']->lastname;
                          $insert_data->merchantID             =  '0000000';
                          $insert_data->orderInfo        =  $goodsName; 
                          $insert_data->amount        =     $goodprice;
                          $insert_data->txnResponseCode        = 0;
                          $insert_data->receiptNo        =  rand(1111111111,9999999999);;
                          $insert_data->transactionNo        = 00000;
                          $insert_data->acqResponseCode        = 00;
                          $insert_data->authorizeID        =  00000; 
                          $insert_data->batchNo        = 00000;
                          $insert_data->cardType        = 'Padapal Voucher';
                          $insert_data->userid        =$USER->id;
                          $insert_data->email        =$USER->email;
                          $insert_data->trans_date  = date('d-m-Y h:i:s a');
                          $res=$DB->insert_record('auodi_pament_info',$insert_data,true);
                        
                                /////// Email Send //////
                                    $sendParameter = array(
                                      'Course' => $insert_data->orderInfo ,
                                      'Amount ($)' => $goodprice /100 ,
                                      'Receipt No' =>  $insert_data->receiptNo,
                                      'Transaction Type' => $insert_data->cardType  ,
                                      'Date of Transaction' => $insert_data->trans_date
                                    );
                                    sendNotification($USER->email , 'Pedapal.com : Course Enrollment' , $sendParameter , 'Order Summary');
                                ///// email Send end /////
                        
                        
                        
                         $successtxt = "Payment Done Successfully";
                         echo '<div >
                                  <h5 style="color:green">' .$successtxt . '</h5>
                                  <a href="'.$CFG->wwwroot.'/course/">Go to My Course</a>
                                </div>';
                            ///  Delete Cart form the database start .////////
                                                foreach ($_SESSION['PRODUCTS'] as $key => $value) {
                                                             $res = $DB->execute("DELETE FROM mdl_carts WHERE code='".$value['code']."'");
                                                }
                            /// Delete cart item form the database end ////
                        
                        unset($_SESSION['PRODUCTS']);
                    }
                    else{
                         $errorTxt = "Unable to enroll student for the course";
                         echo '<div >
                                <h5 style="color:red">' .$errorTxt . '</h5>
                                <a href="'.$CFG->wwwroot.'/local/audiplugin/checkout.php">go to cart </a>
                              </div>';
                      }
                
         }
         elseif($nowCredit < $goodprice){
             
             
                  $insert_data                =  new stdClass(); 
                  $insert_data->hashValidated          =   date('YmsHisA');
                  $insert_data->merchTxnRef             =  time()."-".$_SESSION['USER']->firstname.' '.$_SESSION['USER']->lastname;
                  $insert_data->merchantID             =  '0000000';
                  $insert_data->orderInfo        = 'Credit Deposit'; 
                  $insert_data->amount        =     $price * 100 ;
                  $insert_data->txnResponseCode        = 0;
                  $insert_data->receiptNo        =  rand(1111111111,9999999999);;
                  $insert_data->transactionNo        = 00000;
                  $insert_data->acqResponseCode        = 00;
                  $insert_data->authorizeID        =  00000; 
                  $insert_data->batchNo        = 00000;
                  $insert_data->cardType        = 'Padapal Voucher';
                  $insert_data->userid        =$USER->id;
                  $insert_data->email        =$USER->email;
                  $insert_data->trans_date  = date('d-m-Y h:i:s a');
                  $res=$DB->insert_record('auodi_pament_info',$insert_data,true);
             
                                 /////// Email Send //////
                                    $sendParameter = array(
                                      'Course' => $insert_data->orderInfo ,
                                      'Amount ($)' => $price ,
                                      'Receipt No' =>  $insert_data->receiptNo,
                                      'Transaction Type' => $insert_data->cardType  ,
                                      'Date of Transaction' => $insert_data->trans_date
                                    );
                                    sendNotification($USER->email , 'Pedapal.com : Credit Deposit' , $sendParameter , 'Order Summary');
                                ///// email Send end /////

                $user_info = $DB->get_record('user', array('id' => $_SESSION['USER']->id ), '*', MUST_EXIST); 
                $myCredit = $user_info->credit;
                $updatwCredit = intval($myCredit + $nowCredit);
                $DB->execute("UPDATE mdl_user SET credit = ".$updatwCredit." WHERE  id =". $_SESSION['USER']->id);

                $errorTxt = "Unable to enroll student for the course becuase your voucher price is less than cart totol , the voucher amount is added to your credit , please  try diffrent payment method";
                echo '<div class="span11" style="text-align : center">
                        <p style="color:red;">' .$errorTxt . '</p>
                        <a href="'.$CFG->wwwroot.'/local/audiplugin/checkout.php">Go to cart </a>
                      </div>';
         }

}








////Wallet/////////
function getWalletPayment($price , $u) {

          $audi_config = get_config('local_audiplugin');
          global $CFG, $PAGE, $USER,$DB;  

          $appendAmp = 0;
          $vpcURL = "";
          $newHash = "";


            $goodprice = 0;
            $goodsName = 'Credit Deposit to '.getUsernameByID($u);
            $goodsDesc = 'Credit Deposit to '.getUsernameByID($u);
           
          $goodprice = $price;
          $goodprice = $goodprice * 100 ;
   

          $host_url= $CFG->wwwroot;

          $pay  = '<form id="paypal_form" action="'.  $host_url .'/local/audiplugin/payment_wallet.php" method="POST">';
          $pay .= '<input type="hidden" name="accessCode" value="'.$audi_config->ma.'">';

          $pay .= '<input type="hidden" value="'.$audi_config->mi.'" name="merchant">';
          $pay .= '<input type="hidden" value="'. htmlspecialchars(substr($goodsName , 0 , 30 )). '" name="orderInfo">';
          $pay .= '<input name="amount" type="hidden" value="'.$goodprice.'" />';
          $pay .= '<input type="hidden" value="'.time()."-". $_SESSION['USER']->firstname.' '.$_SESSION['USER']->lastname .'"  name="merchTxnRef">';
          $pay .= '<input type="hidden" name="returnURL" value="'.$host_url.'/local/audiplugin/parent_return_wallet.php?action=py&members='.$u.'" />';
    
          $pay .='<input id="confirm-audi" type="submit" class="btn btn-success"  name="submit" value="Continue to Pay">';   
          $pay .='</form>';
          return $pay;
}


function getVoucherWalletPayment($price, $u) {
          global $CFG, $PAGE, $USER,$DB;  
    
          $pay = '';
          $insert_data                =  new stdClass(); 
          $insert_data->hashValidated          =   date('YmsHisA');
          $insert_data->merchTxnRef             =  time()."-".$_SESSION['USER']->firstname.' '.$_SESSION['USER']->lastname;
          $insert_data->merchantID             =  '0000000';
          $insert_data->orderInfo        = 'Credit Deposit to '.getUsernameByID($u);; 
          $insert_data->amount        =     $price * 100 ;
          $insert_data->txnResponseCode        = 0;
          $insert_data->receiptNo        =  rand(1111111111,9999999999);;
          $insert_data->transactionNo        = 00000;
          $insert_data->acqResponseCode        = 00;
          $insert_data->authorizeID        =  00000; 
          $insert_data->batchNo        = 00000;
          $insert_data->cardType        = 'Padapal Voucher';
          $insert_data->userid        =$USER->id;
          $insert_data->email        =$USER->email;
          $insert_data->trans_date  = date('d-m-Y h:i:s a');
          $res=$DB->insert_record('auodi_pament_info',$insert_data,true);
    
         /////// Email Send //////
            $sendParameter = array(
              'Course' => $insert_data->orderInfo ,
              'Amount ($)' => $price ,
              'Receipt No' =>  $insert_data->receiptNo,
              'Transaction Type' => $insert_data->cardType  ,
              'Date of Transaction' => $insert_data->trans_date
            );
            sendNotification($USER->email , 'Pedapal.com : Credit Deposit' , $sendParameter , 'Order Summary');
        ///// email Send end /////
    
    
          $user_info = $DB->get_record('user', array('id' => $u ), '*', MUST_EXIST); 
          $nowCredit = $user_info->credit;
          $updatwCredit = intval($nowCredit + $price);
          $DB->execute("UPDATE mdl_user SET credit = ".$updatwCredit." WHERE  id =". $u);
          
          $pay .='<p style="color:green">Credit transfer successfully</p>';   
          $pay .='<a href="'.$CFG->wwwroot.'/local/audiplugin/payment_report.php" title="Go to cart" class="btn btn-success" > Go to statement </a>';
          return $pay;
}

function  getWalletVoucher($child)  {
          global $CFG, $PAGE, $USER,$DB;  
          $pay = '';
          $host_url= $CFG->wwwroot;
          $pay .='<a  href="'.$host_url.'/local/audiplugin/parent_redeem_credit.php?members='.$child.'" class="btn btn-warning" >Continue to Redeem</a>';   
          return $pay;    
}


////Wallet End ////////////
// Private Methosds  end//===============================================================================//


?>