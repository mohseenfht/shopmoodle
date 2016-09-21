<?php 
require_once (dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
global $DB, $CFG,$USER;
require_once('../lib.php');
$moodo = get_config('local_moodocommerce');

	$token = trim($_POST['token']);
  $email = $moodo->hekapay_email;
	$secret = $moodo->hekapay_secret;
	$h = $email.$secret.$token;
	$hash = md5($h);
  $userid = md5($USER->id);
	$webServiceUrl = $moodo->hekapay_url.'/'.$email."/".$token."/".$hash."/".$userid;
	$string  = file_get_contents($webServiceUrl);

	if($array->responseCode == '200' && $array->data->redeem == true){
		$code = getCardProcess($array->data->price);
		$json['valid'] = $code;
	}
	else{
		$json['error'] = 'Invalid card';
	}

	echo json_encode($json);


	function getCardProcess($voucher) {

        global $CFG, $PAGE, $USER,$DB;  
        $products = myCartitems($USER->id);

        $goodprice = 0; 
        $goodsName = ''; 
        $goodsDesc = '';

        $nowCredit = $voucher;

        foreach ($products as $k => $v) {
          $goodsName .= trim($v->name).',';
          $goodsDesc .= trim($v->name).'-'.trim($v->category).'-'.trim($v->code).'-'.trim($v->price).',';  
        }
    
        $goodprice = myCartTotol($USER->id);

         if($nowCredit == $goodprice){

                    foreach ($products as  $value) {
                        $order_id = $value->code;
                        $pro = explode('-',$order_id);
                        $pro_id = $pro[1];
                        $suess= course_enroll_user($pro_id, $USER->id);
                     } 
                    

                    if($suess) {
                                
                                $insert_data                =  new stdClass(); 
                                $insert_data->hashValidated          =   date('YmsHisA');
                                $insert_data->merchTxnRef             =  time()."-".$USER->firstname.' '.$USER->lastname;
                                $insert_data->merchantID             =  '0000000';
                                $insert_data->orderInfo        =    substr($goodsName, 0 , 20).'...'; ; 
                                $insert_data->amount        =     $moodo->currency_symbol ." " .$goodprice;
                                $insert_data->txnResponseCode        = 0;
                                $insert_data->receiptNo        =  rand(1111111111,9999999999);
                                $insert_data->transactionNo        = 00000;
                                $insert_data->acqResponseCode        = 00;
                                $insert_data->authorizeID        =  00000; 
                                $insert_data->batchNo        = 00000;
                                $insert_data->cardType        = 'Hekapay Voucher';
                                $insert_data->userid        =$USER->id;
                                $insert_data->email        =$USER->email;
                                $insert_data->trans_date  = date('d-m-Y h:i:s a');
                                $res=$DB->insert_record('user_payment_info',$insert_data,true);
                        
                                ///// Email Send //////
                                $sendParameter = array(
                                  'Course' => $insert_data->orderInfo ,
                                  'Amount'.$moodo->currency_symbol => $goodprice,
                                  'Receipt No' =>  $insert_data->receiptNo,
                                  'Transaction Type' => $insert_data->cardType  ,
                                  'Date of Transaction' => $insert_data->trans_date
                                );
                                sendNotification($USER->email , 'Course Enrollment' , $sendParameter , 'Order Summary');
                                /// email Send end /////

                            	$msgtxt = 'Course purchase success with voucher amount '.$nowCredit;
                               ///  Delete Cart form the database start .////////
                                foreach ($products as $key => $value) {
                                             $res = $DB->execute("DELETE FROM {carts} WHERE code='".$value->code."'");
                                }
                              /// Delete cart item form the database end ////
   
                    }
                    else{
                         $msgtxt = "Unable to enroll student for the course";
                      }

         }
         elseif ($nowCredit > $goodprice) {
                
                
                 foreach ($products as  $value) {
                        $order_id = $value->code;
                        $pro = explode('-',$order_id);
                        $pro_id = $pro[1];
                        $suess= course_enroll_user($pro_id, $USER->id);
                     } 
                    

                    if($suess) {
                          $insert_data                =  new stdClass(); 
                          $insert_data->hashValidated          =   date('YmsHisA');
                          $insert_data->merchTxnRef             =  time()."-".$USER->firstname.' '.$USER->lastname;
                          $insert_data->merchantID             =  '0000000';
                          $insert_data->orderInfo        =  substr($goodsName, 0 , 20).'...'; 
                          $insert_data->amount        =     $goodprice; 
                          $insert_data->txnResponseCode        = 0;
                          $insert_data->receiptNo        =  rand(1111111111,9999999999);
                          $insert_data->transactionNo        = 00000;
                          $insert_data->acqResponseCode        = 00;
                          $insert_data->authorizeID        =  00000; 
                          $insert_data->batchNo        = 00000;
                          $insert_data->cardType        = 'Hekapay Voucher';
                          $insert_data->userid        =$USER->id;
                          $insert_data->email        =$USER->email;
                          $insert_data->trans_date  = date('d-m-Y h:i:s a');
                          $res=$DB->insert_record('user_payment_info',$insert_data,true);
                        
                        
                           /////// Email Send //////
                            $sendParameter = array(
                              'Course' => $insert_data->orderInfo ,
                              'Amount '.$moodo->currency_symbol => $goodprice,
                              'Receipt No' =>  $insert_data->receiptNo,
                              'Transaction Type' => $insert_data->cardType  ,
                              'Date of Transaction' => $insert_data->trans_date
                            );
                            sendNotification($USER->email , 'Course Enrollment' , $sendParameter , 'Order Summary');
                            ///// email Send end /////
                        	$msgtxt = "Course purchase success with voucher amount and rest amount is added to your credit";
                
                            ///  Delete Cart form the database start .////////
                            foreach ($products as $key => $value) {
                                $res = $DB->execute("DELETE FROM {carts}  WHERE code='".$value->code."'");
                            }
                            /// Delete cart item form the database end ////
                        
                        
                        /////  Update Credit Points //////////////////////////
                        $user_info = $DB->get_record('user_credit', array('user_id' => $USER->id ), '*', MUST_EXIST); 
                        $myCredit = $user_info->credit;
                        $updatwCredit = intval($nowCredit -  $goodprice);
                        $updatwCreditNew = intval($updatwCredit +  $myCredit);
                        $DB->execute("UPDATE {user_credit} SET credit = '".$updatwCreditNew."' WHERE  user_id ='". $USER->id."' ");
                        /////  update Credit End /////////////////////////////////////////////////
                        
                        
                    }
                    else{
                         $msgtxt = "Unable to enroll student for the course";
                      }
                
         }
         elseif($nowCredit < $goodprice){
             
             
                $user_info = $DB->get_record('user_credit', array('user_id' => $USER->id ), '*', MUST_EXIST); 
                $myCredit = $user_info->credit;
                $updatwCredit = intval($myCredit + $nowCredit);
                $DB->execute("UPDATE {user_credit} SET credit = '".$updatwCredit."' WHERE  user_id ='". $USER->id. "'");

                $msgtxt = "Unable to enroll student for the course becuase your voucher price is less than cart total , the voucher amount is added to your credit , please  try diffrent payment method";
              
                  $insert_data                =  new stdClass(); 
                  $insert_data->hashValidated          =   date('YmsHisA');
                  $insert_data->merchTxnRef             =  time()."-".$_SESSION['USER']->firstname.' '.$_SESSION['USER']->lastname;
                  $insert_data->merchantID             =  '0000000';
                  $insert_data->orderInfo        = 'Credit Deposit'; 
                  $insert_data->amount        =     $goodprice;
                  $insert_data->txnResponseCode        = 0;
                  $insert_data->receiptNo        =  rand(1111111111,9999999999);
                  $insert_data->transactionNo        = 00000;
                  $insert_data->acqResponseCode        = 00;
                  $insert_data->authorizeID        =  00000; 
                  $insert_data->batchNo        = 00000;
                  $insert_data->cardType        = 'Hekapay Voucher';
                  $insert_data->userid        =$USER->id;
                  $insert_data->email        =$USER->email;
                  $insert_data->trans_date  = date('d-m-Y h:i:s a');
                  $res=$DB->insert_record('user_payment_info',$insert_data,true);
             
                    /////// Email Send //////
                    $sendParameter = array(
                      'Product' => $insert_data->orderInfo ,
                      'Amount '.$moodo->currency_symbol => $goodprice,
                      'Receipt No' =>  $insert_data->receiptNo,
                      'Transaction Type' => $insert_data->cardType  ,
                      'Date of Transaction' => $insert_data->trans_date
                    );
                    sendNotification($USER->email , 'Credit Deposit' , $sendParameter , 'Order Summary');
                    ///// email Send end /////
             
         }
return $msgtxt;
}
?>