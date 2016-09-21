<?php 
require_once (dirname(dirname(dirname(__FILE__))).'/config.php');
if (!isloggedin()) {
  $return_url = $CFG->wwwroot;
  redirect($return_url.'/login/', $OUTPUT->notification("Please login to buy course"));
}
require_login();

$strheading = "User Enrollment";
$PAGE->set_title( $strheading );
echo $OUTPUT->header();
require_once('lib.php');


$moodo = get_config('local_moodocommerce');
global $CFG, $PAGE, $USER,$DB;      
///////   Getting  record from encode string start ///////////////
$code = $_GET['key'];
$docode = base64_decode($code);
$array = explode( '-', $docode );
$type = $array['1'];
$userid = $array['0'];
$courseid = $array['2'];
///////   Getting  record from encode string  end ///////////////
if(checkValidity()){
if($type == 'free' && $USER->id == $userid) {
        $courses_my = $DB->get_records_sql("select * from {erol} as me ,{user_enrolments} mue  where me.courseid=".$courseid. " and mue.userid='".$USER->id."' and mue.enrolid=me.id" );
        $couter = count($courses_my);  
        $courses_name = $DB->get_records_sql("select * from {course} where id='".$courseid. "' and visible=1 ");
        $cname = $courses_name[$courseid]->fullname ;
  
        if($couter == 0) {
            $suess= course_enroll_user($courseid, $USER->id);
                                        
            if($suess) {
                 $successtxt = "Successfully enroll to the course";
                 echo '<div class="well">
                        <h5 style="color:green">' .$successtxt . '</h5>
                        <a href="'.$CFG->wwwroot.'/course/">Go to My Course</a>
                      </div>';
            }
            else{
                 $errorTxt = "Unable to enroll student for the course";
                 echo '<div class="well">
                        <h5 style="color:red">' .$errorTxt . '</h5>
                        <a href="'.$CFG->wwwroot.'/course/">Go to courses</a>
                      </div>';

              }


              ///////////////////  Sending Email to the user ///////////////////
                               $insert_data                =  new stdClass(); 
                                $insert_data->hashValidated          =   date('YmsHisA');
                                $insert_data->merchTxnRef             =  time()."-".$_SESSION['USER']->firstname.' '.$_SESSION['USER']->lastname;
                                $insert_data->merchantID             =  '0000000';
                                $insert_data->orderInfo        =  $cname; 
                                $insert_data->amount        =     0;
                                $insert_data->txnResponseCode        = 0;
                                $insert_data->receiptNo        =  rand(1111111111,9999999999);
                                $insert_data->transactionNo        = 00000;
                                $insert_data->acqResponseCode        = 00;
                                $insert_data->authorizeID        =  00000; 
                                $insert_data->batchNo        = 00000;
                                $insert_data->cardType        = 'Free';
                                $insert_data->userid        =$USER->id;
                                $insert_data->email        =$USER->email;
                                $insert_data->trans_date  = date('d-m-Y h:i:s a');
                                $res=$DB->insert_record('auodi_pament_info',$insert_data);
                    
                                /////// Email Send //////
                               $sendParameter = array(
                                  'Courses' => $goodsName,
                                  'Amount ($)' =>'Free',
                                  'Receipt No' =>  $insert_data->receiptNo,
                                  'transactionNo Type' => $insert_data->cardType  ,
                                  'Date of Transaction' => $insert_data->trans_date
                                );
                               sendNotification($USER->email , 'Course Enrollment' , $sendParameter , 'Order Summary');
              ///// email Send end /////
        }
        else {

                 $errorTxt = "You are already enrolled for this course";
                 echo '<div class="well">
                        <h5 style="color:red">' .$errorTxt . '</h5>
                        <a href="'.$CFG->wwwroot.'/course/">Go to courses</a>
                      </div>';

        }
}      
}
else{
        echo getPluginErrorMessage();
}                   
?>
<?php  echo $OUTPUT->footer();  ?>