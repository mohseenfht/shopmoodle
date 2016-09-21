<?php
require_once (dirname(dirname(dirname(__FILE__))).'/config.php');
require_login();
require_once("$CFG->libdir/formslib.php");
require_once('lib.php');
$strheading = "Payments List ";
$PAGE->set_pagelayout('standard');
$PAGE->set_title( $strheading );
$PAGE->navbar->add($strheading);
echo $OUTPUT->header();
global $CFG, $PAGE, $USER,$DB;  
if(checkValidity()){
if(is_siteadmin()){
                $user_list=user_pay_list();
                $output = '';
                    $output .= html_writer::start_tag('div', array('class' => 'info'));
                      $output .= html_writer::start_tag('h3', array('class' => 'name'));
                        $output .= html_writer::link('#', 'User Payments', array('title' => 'User Payments'));
                      $output .= html_writer::end_tag('h3');
                    $output .= html_writer::end_tag('div'); 
                    $output .= html_writer::start_tag('hr' ); html_writer::end_tag();
                $i=1;
                $table = new html_table();
                $table->head = array('Sr No','Date', 'TXN Ref' , 'Order Info', 'Amount','Receipt No', 'Card Type', 'User ID');
                foreach ($user_list as $value) {
                    $transdate = $value->trans_date;
                    $merchtxnref = $value->merchtxnref;
                    $orderinfo = $value->orderinfo;
                    $amount = $value->amount / 100;
                    $receiptno = $value->receiptno;
                    $cardtype = $value->cardtype;
                    $link = $CFG->wwwroot.'/user/profile.php?id='.$value->userid;
                    $table->data[] = array($i, $transdate, $merchtxnref,$orderinfo,$amount,$receiptno,$cardtype,'<a href="'.$link.'">View User</a>');
                    $i++;
                }
                echo $output;
                echo html_writer::table($table);
}
else{
                $user_list = user_list_id($USER->id);
                $output = '';
                    $output .= html_writer::start_tag('div', array('class' => 'info'));
                      $output .= html_writer::start_tag('h3', array('class' => 'name'));
                        $output .= html_writer::link('#', 'My Payments', array('title' => 'My Payments'));
                      $output .= html_writer::end_tag('h3');
                    $output .= html_writer::end_tag('div'); 
                    $output .= html_writer::start_tag('hr' ); html_writer::end_tag();
                $i=1;
                $table = new html_table();
                $table->head = array('Sr No','Date', 'TXN Ref' , 'Order Info', 'Amount','Receipt No', 'Payment Type');
                foreach ($user_list as $value) {
                    $transdate = $value->trans_date;
                    $merchtxnref = $value->merchtxnref;
                    $orderinfo = $value->orderinfo;
                    $amount = $value->amount;
                    $receiptno = $value->receiptno;
                    $cardtype = $value->cardtype;
                    $table->data[] = array($i, $transdate, $merchtxnref,$orderinfo,$amount,$receiptno,$cardtype);
                    $i++;
                }
                echo $output;
                echo html_writer::table($table);
}
}
else{
      echo getPluginErrorMessage();
}
?>

<?php  echo $OUTPUT->footer(); ?>

