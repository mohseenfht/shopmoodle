<?php


namespace local_moodocommerce\task;
defined('MOODLE_INTERNAL') || die();

class attempt_deleted extends \core\task\base {
  public function get_name() {
        // Shown in admin screens
        return get_string('pluginname', 'local_moodocommerce');
    }
  public function execute() {   
      global $CFG, $DB,$USER; 
      $insert_data                =  new stdClass(); 
      $insert_data->hashValidated          =   date('YmsHisA');
      $insert_data->merchTxnRef             =  time()."-".$USER->firstname.' '.$USER->lastname;
      $insert_data->merchantID             =  '0000000';
      $insert_data->orderInfo        =  '...'; 
      $insert_data->amount        =     "";
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
  }    
}
