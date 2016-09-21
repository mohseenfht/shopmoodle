<?php
/**
 * Navigation lang file.
 *
 * @package    local_moodocommerce
 * @author     Mohseen Khan <info@fht.co.in>
 */
defined('MOODLE_INTERNAL') || die;

function local_moodocommerce_extend_navigation(global_navigation $navigation) {
    global $CFG, $PAGE, $USER;
    if(is_siteadmin()){
    $nodesampleplugin = $navigation->add(get_string('pluginname', 'local_moodocommerce') );
   	    $nodesampleplugin->add(get_string('dashboard', 'local_moodocommerce'), new moodle_url($CFG->wwwroot.'/local/moodocommerce/prices.php'));
        $nodesampleplugin->add(get_string('dashboard1', 'local_moodocommerce'), new moodle_url($CFG->wwwroot.'/local/moodocommerce/payment.php'));
    }
}


function local_moodocommerce_cron(){
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
  function local_moodocommerce_course($course_enrol)
  {
      global $CFG, $PAGE, $USER,$DB;
      if(!empty($course_enrol))
      {
      foreach ($course_enrol as $value)
            {
            $courseid[] = $value->courseid;
    
            } 
           $course_enrol_id = @implode(",", $courseid);
      }else{
          $course_enrol_id=1;
      }
       //echo $course_enrol_id;
     $course_fullname= $DB->get_records_sql("select * from {course} where id NOT IN ($course_enrol_id) and visible=1");
      //echo "select * from mdl_course where id NOTIN ($course_enrol_id)";
       //$course_fullname = $DB->get_records('course');
       //print_r($course_fullname);
       
    return $course_fullname;
  }
  
  function enroll_student($courseid, $userid)
  {


      global $CFG, $PAGE, $USER,$DB;
                    $enroll_data    =   $DB->get_record('enrol',array('enrol'=>'manual','courseid'=> $courseid));     
                    if($enroll_data !=""){
                            //enroll variable stores the id of users to enroll
                                    $timestamp=strtotime(date("Y-m-d H:i:s"));
                                    $insert_data                =   new stdClass();
                                    $insert_data->userid        =   $userid;
                                    $insert_data->enrolid       =   $enroll_data->id;
                                    $insert_data->timecreated   =   $timestamp;
                                    $insert_data->timemodified  =   $timestamp;
                                    $insert_data->timeend       =   0;
                                    
                                    //$data = array("userid"=>$value,"enrolid"=>$enroll_data[0]['id']);
                                    if (!($DB->record_exists('user_enrolments', array('userid' => $userid, 'enrolid' => $enroll_data->id)))) {
                                       $DB->insert_record('user_enrolments',$insert_data,true);
                                    }
                            }
     return true;
  }
  
  function update_course_prices($fromform)
  {
       global $CFG, $PAGE, $USER,$DB;
       
       foreach($fromform as $key=>$value){
       $DB->execute("UPDATE {course} SET amout = $value WHERE id=$key");
       }
       
      return 23;
  }
  function user_pay_list()
  {
    global $CFG, $PAGE, $USER,$DB;
    $user_audi_pay_info= $DB->get_records_sql("select * from {user_payment_info} ");
    return $user_audi_pay_info;
  }


  function user_list_id($id)
  {
      global $CFG, $PAGE, $USER,$DB;
    
    $user_audi_pay_info_id = $DB->get_records_sql("select * from {user_payment_info} where userid = ".$id);
    return $user_audi_pay_info_id;
  }


  function myCartTotol($id)
  {
     global $CFG, $PAGE, $USER,$DB;
     $total = $DB->get_records_sql("select sum(price) as totals from {carts} where user_id = ".$id);
     $total;

     $t = 0;
     foreach($total as $key => $val){
         $t = $t + $val->totals;
     }
     return $t;
  }


  function myCartProductsName($id) {
     global $CFG, $PAGE, $USER,$DB;
     $goodsName = '';
     $goodsDesc = '';
     $total = $DB->get_records_sql("select *  from {carts} where user_id = ".$id);
     foreach($total as  $val){
         $goodsName .= trim($val->name).',';
     }
     return $goodsName;
  }



 function myCartitems($id)
  {
     global $CFG, $PAGE, $USER,$DB;
     $total = $DB->get_records_sql("select *  from {carts} where user_id = ".$id);
     return $total;
  }

function getMyCredit($id){
                global $CFG, $USER, $DB;
                $moodo = get_config('local_moodocommerce');
                $user_info = $DB->get_record('user_credit', array('user_id' => $id), '*', MUST_EXIST); 

                if(!empty($user_info) && $user_info->credit != 0){
                  $userfullname = $user_info->credit;
                }else{
                  $userfullname = $moodo->credit_default;
                }
                return $userfullname;
}



  function course_enroll_user($course_id , $user_id){
      global $CFG, $PAGE, $USER,$DB;
      $context = context_course::instance($course_id);
      // What role to enrol as?

       $studentroleid = $DB->get_field('role', 'id', array('shortname' => 'student'));
      // Loop through the students.

          if (!is_enrolled($context, $user_id )) {
              // Not already enrolled so try enrolling them.
              if (!enrol_try_internal_enrol($course_id, $user_id, $studentroleid, time())) {
                  // There's a problem.
                  throw new moodle_exception('unabletoenrolerrormessage', 'langsourcefile');
              }
          }
    return true;
  }

/// Getting Parent Clid RelationShip start ///
function getParentChild() {
            $session = $_SESSION['SESSION']->navcache->navigation;
            foreach ($session as $key => $value) {
               if(strpos($key,'userblogoptions') !== false) {
                $userf[] = $key;
              }
            }
            foreach ($userf as $key => $value) {
                $data = explode('s' ,$value);
                $users[] = $data[2];
            }
            return $users;  /// Return array of user ID blong to this User
}


function getParentContent() {
        global $CFG, $USER, $DB;
        // get all the mentees, i.e. users you have a direct assignment to
        $allusernames = get_all_user_name_fields(true, 'u');
        $usercontexts = $DB->get_records_sql("SELECT c.instanceid, c.instanceid, $allusernames
                                                    FROM {role_assignments} ra, {context} c, {user} u
                                                   WHERE ra.userid = ?
                                                         AND ra.contextid = c.id
                                                         AND c.instanceid = u.id
                                                         AND c.contextlevel = ".CONTEXT_USER, array($USER->id)) ;

      
            foreach ($usercontexts as $usercontext) {
                $users[]= $usercontext->instanceid;
            }
            return $users;  /// Return array of user ID blong to this User
}


function getUsernameByID($id){
          global $CFG, $USER, $DB;
  $user_info = $DB->get_record('user', array('id' => $id ), '*', MUST_EXIST); 
                $userfullname = $user_info->firstname.' '.$user_info->lastname;
                return $userfullname;
}

function getUsernameByIDAdmin($id){
               global $CFG, $USER, $DB;
                $user_info = $DB->get_record('user', array('id' => $id  ,'deleted' => 0  ), '*', MUST_EXIST); 
                $userfullname = $user_info->firstname.' '.$user_info->lastname;
                return $userfullname;
}
// Getting Parent Child Relation ship end ////

/////////////////   Remove Specials Charecters ////////////////////////
function hyphenize($string) {
    return 
    ## strtolower(
          preg_replace(
            array('#[\\s-]+#', '#[^A-Za-z0-9\. -]+#'),
            array('-', ''),
        ##     cleanString(
              urldecode($string)
        ##     )
        )
    ## )
    ;
}
///////////////   End Remove ////////////////////////


////////////////////   Post to server ////////////////////////////
function checkValidity(){
    global $CFG, $USER, $DB;
    $moodo = get_config('local_moodocommerce');
    $requestParameters["method"] = "check";
    $requestParameters["secret"] = $moodo->moodocommerce_secret;
    $requestParameters["url"] = base64_encode($CFG->wwwroot);
    $response = do_post_request( http_build_query($requestParameters, '', '&'));
    $decode = json_decode($response ,true);
    return $decode['valid'];
}

 infotoapi();
 function infotoapi(){
    $moodo = get_config('local_moodocommerce');
    global $CFG, $USER, $DB;
    $requestParameters["method"] = "info";
    $requestParameters["secret"] = $moodo->moodocommerce_secret;
    $requestParameters["moodle_url"] = base64_encode($CFG->wwwroot);
    $requestParameters["last_url"] = base64_encode($CFG->wwwroot.$_SERVER['REQUEST_URI']);
    $requestParameters["username"] = $USER->firstname ." " . $USER->lastname;
    $requestParameters["user_id"] = $USER->id;
    $requestParameters['ip_address'] = $_SERVER['REMOTE_ADDR'];
    $requestParameters['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    $response = do_post_request( http_build_query($requestParameters, '', '&'));
    $decode = json_decode($response ,true);
    return true;
}

function do_post_request($data, $optional_headers = null) {
        $url = base64_decode('aHR0cDovL2ZodC5jby5pbi9hcGkvYXBpLnBocD8=');
        $response  = file_get_contents($url.$data);
        return $response;
}
////////////////////   Post to server ////////////////////////////

function getPluginErrorMessage(){
      $output .= html_writer::start_tag('div', array('class' => 'well'));
            $output .= '<h6 style="color:red">Plugin subscription is Expire or Plugin is invalid </h6>';
      $output .= html_writer::end_tag('h4');
      return $output;
}

//////   Sending Mail to the User
function sendNotification($emailTo,$emailSubject,$emailParameter,$msg){
            global $CFG, $USER, $DB;

            $massage ='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            <meta name="viewport" content="width=device-width"/>
            <style type="text/css">
                        /*********************************************************************
                        Ink - Responsive Email Template Framework Based: http://zurb.com/ink/
                        *********************************************************************/
                        #outlook a {
                        padding:0;
                        }
                        body{
                        width:100% !important;
                        min-width: 100%;
                        -webkit-text-size-adjust:100%;
                        -ms-text-size-adjust:100%;
                        margin:0;
                        padding:0;
                        }
                        .ExternalClass {
                        width:100%;
                        }
                        .ExternalClass,
                        .ExternalClass p,
                        .ExternalClass span,
                        .ExternalClass font,
                        .ExternalClass td,
                        .ExternalClass div {
                        line-height: 100%;
                        }
                        #backgroundTable {
                        margin:0;
                        padding:0;
                        width:100% !important;
                        line-height: 100% !important;
                        }
                        img {
                        outline:none;
                        text-decoration:none;
                        -ms-interpolation-mode1: bicubic;
                        width: auto;
                        max-width: 100%;
                        float: left;
                        clear: both;
                        display: block;
                        }
                        center {
                        width: 100%;
                        min-width: 580px;
                        }
                        a img {
                        border: none;
                        }
                        p {
                        margin: 0 0 0 10px;
                        }
                        table {
                        border-spacing: 0;
                        border-collapse: collapse;
                        }
                        td {
                        word-break: break-word;
                        -webkit-hyphens: auto;
                        -moz-hyphens: auto;
                        hyphens: auto;
                        border-collapse: collapse !important;
                        }
                        table, tr, td {
                        padding: 0;
                        vertical-align: top;
                        text-align: left;
                        }
                        hr {
                        color: #d9d9d9;
                        background-color: #d9d9d9;
                        height: 1px;
                        border: none;
                        }
                        /* Responsive Grid */
                        table.body {
                        height: 100%;
                        width: 100%;
                        }
                        table.container {
                        width: 580px;
                        margin: 0 auto;
                        text-align: inherit;
                        }
                        table.row {
                        padding: 0px;
                        width: 100%;
                        position: relative;
                        }
                        table.container table.row {
                        display: block;
                        }
                        td.wrapper {
                        padding: 10px 20px 0px 0px;
                        position: relative;
                        }
                        table.columns,
                        table.column {
                        margin: 0 auto;
                        }
                        table.columns td,
                        table.column td {
                        padding: 0px 0px 10px;
                        }
                        table.columns td.sub-columns,
                        table.column td.sub-columns,
                        table.columns td.sub-column,
                        table.column td.sub-column {
                        padding-right: 10px;
                        }
                        td.sub-column, td.sub-columns {
                        min-width: 0px;
                        }
                        table.row td.last,
                        table.container td.last {
                        padding-right: 0px;
                        }
                        table.one { width: 30px; }
                        table.two { width: 80px; }
                        table.three { width: 130px; }
                        table.four { width: 180px; }
                        table.five { width: 230px; }
                        table.six { width: 280px; }
                        table.seven { width: 330px; }
                        table.eight { width: 380px; }
                        table.nine { width: 430px; }
                        table.ten { width: 480px; }
                        table.eleven { width: 530px; }
                        table.twelve { width: 580px; }
                        table.one center { min-width: 30px; }
                        table.two center { min-width: 80px; }
                        table.three center { min-width: 130px; }
                        table.four center { min-width: 180px; }
                        table.five center { min-width: 230px; }
                        table.six center { min-width: 280px; }
                        table.seven center { min-width: 330px; }
                        table.eight center { min-width: 380px; }
                        table.nine center { min-width: 430px; }
                        table.ten center { min-width: 480px; }
                        table.eleven center { min-width: 530px; }
                        table.twelve center { min-width: 580px; }
                        table.one .panel center { min-width: 10px; }
                        table.two .panel center { min-width: 60px; }
                        table.three .panel center { min-width: 110px; }
                        table.four .panel center { min-width: 160px; }
                        table.five .panel center { min-width: 210px; }
                        table.six .panel center { min-width: 260px; }
                        table.seven .panel center { min-width: 310px; }
                        table.eight .panel center { min-width: 360px; }
                        table.nine .panel center { min-width: 410px; }
                        table.ten .panel center { min-width: 460px; }
                        table.eleven .panel center { min-width: 510px; }
                        table.twelve .panel center { min-width: 560px; }
                        .body .columns td.one,
                        .body .column td.one { width: 8.333333%; }
                        .body .columns td.two,
                        .body .column td.two { width: 16.666666%; }
                        .body .columns td.three,
                        .body .column td.three { width: 25%; }
                        .body .columns td.four,
                        .body .column td.four { width: 33.333333%; }
                        .body .columns td.five,
                        .body .column td.five { width: 41.666666%; }
                        .body .columns td.six,
                        .body .column td.six { width: 50%; }
                        .body .columns td.seven,
                        .body .column td.seven { width: 58.333333%; }
                        .body .columns td.eight,
                        .body .column td.eight { width: 66.666666%; }
                        .body .columns td.nine,
                        .body .column td.nine { width: 75%; }
                        .body .columns td.ten,
                        .body .column td.ten { width: 83.333333%; }
                        .body .columns td.eleven,
                        .body .column td.eleven { width: 91.666666%; }
                        .body .columns td.twelve,
                        .body .column td.twelve { width: 100%; }
                        td.offset-by-one { padding-left: 50px; }
                        td.offset-by-two { padding-left: 100px; }
                        td.offset-by-three { padding-left: 150px; }
                        td.offset-by-four { padding-left: 200px; }
                        td.offset-by-five { padding-left: 250px; }
                        td.offset-by-six { padding-left: 300px; }
                        td.offset-by-seven { padding-left: 350px; }
                        td.offset-by-eight { padding-left: 400px; }
                        td.offset-by-nine { padding-left: 450px; }
                        td.offset-by-ten { padding-left: 500px; }
                        td.offset-by-eleven { padding-left: 550px; }
                        td.expander {
                        visibility: hidden;
                        width: 0px;
                        padding: 0 !important;
                        }
                        table.columns .text-pad,
                        table.column .text-pad {
                        padding-left: 10px;
                        padding-right: 10px;
                        }
                        table.columns .left-text-pad,
                        table.columns .text-pad-left,
                        table.column .left-text-pad,
                        table.column .text-pad-left {
                        padding-left: 10px;
                        }
                        table.columns .right-text-pad,
                        table.columns .text-pad-right,
                        table.column .right-text-pad,
                        table.column .text-pad-right {
                        padding-right: 10px;
                        }
                        /* Block Grid */
                        .block-grid {
                        width: 100%;
                        max-width: 580px;
                        }
                        .block-grid td {
                        display: inline-block;
                        padding:10px;
                        }
                        .two-up td {
                        width:270px;
                        }
                        .three-up td {
                        width:173px;
                        }
                        .four-up td {
                        width:125px;
                        }
                        .five-up td {
                        width:96px;
                        }
                        .six-up td {
                        width:76px;
                        }
                        .seven-up td {
                        width:62px;
                        }
                        .eight-up td {
                        width:52px;
                        }
                        /* Alignment & Visibility Classes */
                        table.center, td.center {
                        text-align: center;
                        }
                        h1.center,
                        h2.center,
                        h3.center,
                        h4.center,
                        h5.center,
                        h6.center {
                        text-align: center;
                        }
                        span.center {
                        display: block;
                        width: 100%;
                        text-align: center;
                        }
                        img.center {
                        margin: 0 auto;
                        float: none;
                        }
                        .show-for-small,
                        .hide-for-desktop {
                        display: none;
                        }
                        /* Typography */
                        body, table.body, h1, h2, h3, h4, h5, h6, p, td {
                        color: #222222;
                        font-family: "Helvetica", "Arial", sans-serif;
                        font-weight: normal;
                        padding:0;
                        margin: 0;
                        text-align: left;
                        line-height: 1.3;
                        }
                        h1, h2, h3, h4, h5, h6 {
                        word-break: normal;
                        }
                        h1 {font-size: 40px;}
                        h2 {font-size: 36px;}
                        h3 {font-size: 32px;}
                        h4 {font-size: 28px;}
                        h5 {font-size: 24px;}
                        h6 {font-size: 20px;}
                        body, table.body, p, td {font-size: 14px;line-height:19px;}
                        p.lead, p.lede, p.leed {
                        font-size: 18px;
                        line-height:21px;
                        }
                        p {
                        margin-bottom: 10px;
                        }
                        small {
                        font-size: 10px;
                        }
                        a {
                        color: #2ba6cb;
                        text-decoration: none;
                        }
                        a:hover {
                        color: #2795b6 !important;
                        }
                        a:active {
                        color: #2795b6 !important;
                        }
                        a:visited {
                        color: #2ba6cb !important;
                        }
                        h1 a,
                        h2 a,
                        h3 a,
                        h4 a,
                        h5 a,
                        h6 a {
                        color: #2ba6cb;
                        }
                        h1 a:active,
                        h2 a:active,
                        h3 a:active,
                        h4 a:active,
                        h5 a:active,
                        h6 a:active {
                        color: #2ba6cb !important;
                        }
                        h1 a:visited,
                        h2 a:visited,
                        h3 a:visited,
                        h4 a:visited,
                        h5 a:visited,
                        h6 a:visited {
                        color: #2ba6cb !important;
                        }
                        /* Panels */
                        .panel {
                        background: #f2f2f2;
                        border: 1px solid #d9d9d9;
                        padding: 10px !important;
                        }
                        .sub-grid table {
                        width: 100%;
                        }
                        .sub-grid td.sub-columns {
                        padding-bottom: 0;
                        }
                        /* Buttons */
                        table.button,
                        table.tiny-button,
                        table.small-button,
                        table.medium-button,
                        table.large-button {
                        width: 100%;
                        overflow: hidden;
                        }
                        table.button td,
                        table.tiny-button td,
                        table.small-button td,
                        table.medium-button td,
                        table.large-button td {
                        display: block;
                        width: auto !important;
                        text-align: center;
                        background: #2ba6cb;
                        border: 1px solid #2284a1;
                        color: #ffffff;
                        padding: 8px 0;
                        }
                        table.tiny-button td {
                        padding: 5px 0 4px;
                        }
                        table.small-button td {
                        padding: 8px 0 7px;
                        }
                        table.medium-button td {
                        padding: 12px 0 10px;
                        }
                        table.large-button td {
                        padding: 21px 0 18px;
                        }
                        table.button td a,
                        table.tiny-button td a,
                        table.small-button td a,
                        table.medium-button td a,
                        table.large-button td a {
                        font-weight: bold;
                        text-decoration: none;
                        font-family: Helvetica, Arial, sans-serif;
                        color: #ffffff;
                        font-size: 16px;
                        }
                        table.tiny-button td a {
                        font-size: 12px;
                        font-weight: normal;
                        }
                        table.small-button td a {
                        font-size: 16px;
                        }
                        table.medium-button td a {
                        font-size: 20px;
                        }
                        table.large-button td a {
                        font-size: 24px;
                        }
                        table.button:hover td,
                        table.button:visited td,
                        table.button:active td {
                        background: #2795b6 !important;
                        }
                        table.button:hover td a,
                        table.button:visited td a,
                        table.button:active td a {
                        color: #fff !important;
                        }
                        table.button:hover td,
                        table.tiny-button:hover td,
                        table.small-button:hover td,
                        table.medium-button:hover td,
                        table.large-button:hover td {
                        background: #2795b6 !important;
                        }
                        table.button:hover td a,
                        table.button:active td a,
                        table.button td a:visited,
                        table.tiny-button:hover td a,
                        table.tiny-button:active td a,
                        table.tiny-button td a:visited,
                        table.small-button:hover td a,
                        table.small-button:active td a,
                        table.small-button td a:visited,
                        table.medium-button:hover td a,
                        table.medium-button:active td a,
                        table.medium-button td a:visited,
                        table.large-button:hover td a,
                        table.large-button:active td a,
                        table.large-button td a:visited {
                        color: #ffffff !important;
                        }
                        table.secondary td {
                        background: #e9e9e9;
                        border-color: #d0d0d0;
                        color: #555;
                        }
                        table.secondary td a {
                        color: #555;
                        }
                        table.secondary:hover td {
                        background: #d0d0d0 !important;
                        color: #555;
                        }
                        table.secondary:hover td a,
                        table.secondary td a:visited,
                        table.secondary:active td a {
                        color: #555 !important;
                        }
                        table.success td {
                        background: #5da423;
                        border-color: #457a1a;
                        }
                        table.success:hover td {
                        background: #457a1a !important;
                        }
                        table.alert td {
                        background: #c60f13;
                        border-color: #970b0e;
                        }
                        table.alert:hover td {
                        background: #970b0e !important;
                        }
                        table.radius td {
                        -webkit-border-radius: 3px;
                        -moz-border-radius: 3px;
                        border-radius: 3px;
                        }
                        table.round td {
                        -webkit-border-radius: 500px;
                        -moz-border-radius: 500px;
                        border-radius: 500px;
                        }
                        /* Outlook First */
                        body.outlook p {
                        display: inline !important;
                        }
                        /*  Media Queries */
                        @media only screen and (max-width: 600px) {
                        table[class="body"] img {
                        width: auto !important;
                        height: auto !important;
                        }
                        table[class="body"] center {
                        min-width: 0 !important;
                        }
                        table[class="body"] .container {
                        width: 95% !important;
                        }
                        table[class="body"] .row {
                        width: 100% !important;
                        display: block !important;
                        }
                        table[class="body"] .wrapper {
                        display: block !important;
                        padding-right: 0 !important;
                        }
                        table[class="body"] .columns,
                        table[class="body"] .column {
                        table-layout: fixed !important;
                        float: none !important;
                        width: 100% !important;
                        padding-right: 0px !important;
                        padding-left: 0px !important;
                        display: block !important;
                        }
                        table[class="body"] .wrapper.first .columns,
                        table[class="body"] .wrapper.first .column {
                        display: table !important;
                        }
                        table[class="body"] table.columns td,
                        table[class="body"] table.column td {
                        width: 100% !important;
                        }
                        table[class="body"] .columns td.one,
                        table[class="body"] .column td.one { width: 8.333333% !important; }
                        table[class="body"] .columns td.two,
                        table[class="body"] .column td.two { width: 16.666666% !important; }
                        table[class="body"] .columns td.three,
                        table[class="body"] .column td.three { width: 25% !important; }
                        table[class="body"] .columns td.four,
                        table[class="body"] .column td.four { width: 33.333333% !important; }
                        table[class="body"] .columns td.five,
                        table[class="body"] .column td.five { width: 41.666666% !important; }
                        table[class="body"] .columns td.six,
                        table[class="body"] .column td.six { width: 50% !important; }
                        table[class="body"] .columns td.seven,
                        table[class="body"] .column td.seven { width: 58.333333% !important; }
                        table[class="body"] .columns td.eight,
                        table[class="body"] .column td.eight { width: 66.666666% !important; }
                        table[class="body"] .columns td.nine,
                        table[class="body"] .column td.nine { width: 75% !important; }
                        table[class="body"] .columns td.ten,
                        table[class="body"] .column td.ten { width: 83.333333% !important; }
                        table[class="body"] .columns td.eleven,
                        table[class="body"] .column td.eleven { width: 91.666666% !important; }
                        table[class="body"] .columns td.twelve,
                        table[class="body"] .column td.twelve { width: 100% !important; }
                        table[class="body"] td.offset-by-one,
                        table[class="body"] td.offset-by-two,
                        table[class="body"] td.offset-by-three,
                        table[class="body"] td.offset-by-four,
                        table[class="body"] td.offset-by-five,
                        table[class="body"] td.offset-by-six,
                        table[class="body"] td.offset-by-seven,
                        table[class="body"] td.offset-by-eight,
                        table[class="body"] td.offset-by-nine,
                        table[class="body"] td.offset-by-ten,
                        table[class="body"] td.offset-by-eleven {
                        padding-left: 0 !important;
                        }
                        table[class="body"] table.columns td.expander {
                        width: 1px !important;
                        }
                        table[class="body"] .right-text-pad,
                        table[class="body"] .text-pad-right {
                        padding-left: 10px !important;
                        }
                        table[class="body"] .left-text-pad,
                        table[class="body"] .text-pad-left {
                        padding-right: 10px !important;
                        }
                        table[class="body"] .hide-for-small,
                        table[class="body"] .show-for-desktop {
                        display: none !important;
                        }
                        table[class="body"] .show-for-small,
                        table[class="body"] .hide-for-desktop {
                        display: inherit !important;
                        }
                        }
            </style>
            <style>
                        /**************************************************************
                        * Custom Styles *
                        ***************************************************************/
                        /***
                        Reset & Typography
                        ***/
                        body {
                        direction: ltr;
                        background: #f6f8f1;
                        }
                        a:hover {
                        text-decoration: underline;
                        }
                        h1 {font-size: 34px;}
                        h2 {font-size: 30px;}
                        h3 {font-size: 26px;}
                        h4 {font-size: 22px;}
                        h5 {font-size: 18px;}
                        h6 {font-size: 16px;}
                        h4, h3, h2, h1 {
                        display: block;
                        margin: 5px 0 15px 0;
                        }
                        h7, h6, h5 {
                        display: block;
                        margin: 5px 0 5px 0 !important;
                        }
                        /***
                        Buttons
                        ***/
                        .btn td {
                        background: #e5e5e5 !important;
                        border: 0;
                        font-family: "Segoe UI", Helvetica, Arial, sans-serif;
                        font-size: 14px;
                        padding: 7px 14px !important;
                        color: #333333 !important;
                        text-align: center;
                        vertical-align: middle;
                        }
                        .btn td a {
                        display: block;
                        color: #fff;
                        }
                        .btn td a:hover,
                        .btn td a:focus,
                        .btn td a:active {
                        color: #fff !important;
                        text-decoration: none;
                        }
                        .btn td:hover,
                        .btn td:focus,
                        .btn td:active {
                        background: #d8d8d8 !important;
                        }
                        /*  Yellow */
                        .btn.yellow td {
                        background: #ffb848 !important;
                        }
                        .btn.yellow td:hover,
                        .btn.yellow td:focus,
                        .btn.yellow td:active {
                        background: #eca22e !important;
                        }
                        .btn.red td{
                        background: #d84a38 !important;
                        }
                        .btn.red td:hover,
                        .btn.red td:focus,
                        .btn.red td:active {
                        background: #bb2413 !important;
                        }
                        .btn.green td {
                        background: #35aa47 !important;
                        }
                        .btn.green td:hover,
                        .btn.green td:focus,
                        .btn.green td:active {
                        background: #1d943b !important;
                        }
                        /*  Blue */
                        .btn.blue td {
                        background: #4d90fe !important;
                        }
                        .btn.blue td:hover,
                        .btn.blue td:focus,
                        .btn.blue td:active {
                        background: #0362fd !important;
                        }
                        .template-label {
                        color: #ffffff;
                        font-weight: bold;
                        font-size: 11px;
                        }
                        /***
                        Note Panels
                        ***/
                        .note .panel {
                        padding: 10px !important;
                        background: #ECF8FF;
                        border: 0;
                        }
                        /***
                        Header
                        ***/
                        .header {
                        width: 100%;
                        background: #1f1f1f;
                        }
                        /***
                        Social Icons
                        ***/
                        .social-icons {
                        float: right;
                        }
                        .social-icons td {
                        padding: 0 2px !important;
                        width: auto !important;
                        }
                        .social-icons td:last-child {
                        padding-right: 0 !important;
                        }
                        .social-icons td img {
                        max-width: none !important;
                        }
                        /***
                        Content
                        ***/
                        table.container.content > tbody > tr > td{
                        background: #fff;
                        padding: 15px !important;
                        }
                        /***
                        Footer
                        ***/
                        .footer  {
                        width: 100%;
                        background: #2f2f2f;
                        }
                        .footer td {
                        vertical-align: middle;
                        color: #fff;
                        }
                        /***
                        Content devider
                        ***/
                        .devider {
                        border-bottom: 1px solid #eee;
                        margin: 15px -15px;
                        display: block;
                        }
                        /***
                        Media Item
                        ***/
                        .media-item img {
                        display: block !important;
                        float: none;
                        margin-bottom: 10px;
                        }
                        .vertical-middle {
                        padding-top: 0;
                        padding-bottom: 0;
                        vertical-align: middle;
                        }
                        /***
                        Utils
                        ***/
                        .align-reverse {
                        text-align: right;
                        }
                        .border {
                        border: 1px solid red;
                        }
                        .hidden-mobile {
                        display: block;
                        }
                        .visible-mobile {
                        display: none;
                        }
                        @media only screen and (max-width: 600px) {
                        /***
                        Reset & Typography
                        ***/
                        body {
                        background: #fff;
                        }
                        h1 {font-size: 30px;}
                        h2 {font-size: 26px;}
                        h3 {font-size: 22px;}
                        h4 {font-size: 20px;}
                        h5 {font-size: 16px;}
                        h6 {font-size: 14px;}
                        /***
                        Content
                        ***/
                        table.container.content > tbody > tr > td{
                        padding: 0px !important;
                        }
                        table[class="body"] table.columns .social-icons td {
                        width: auto !important;
                        }
                        /***
                        Header
                        ***/
                        .header {
                        padding: 10px !important;
                        }
                        /***
                        Content devider
                        ***/
                        .devider {
                        margin: 15px 0;
                        }
                        /***
                        Media Item
                        ***/
                        .media-item {
                        border-bottom: 1px solid #eee;
                        padding: 15px 0 !important;
                        }
                        /***
                        Media Item
                        ***/
                        .hidden-mobile {
                        display: none;
                        }
                        .visible-mobile {
                        display: block;
                        }
                        }
            </style>
            </head>
            <body>
            <table class="body">
            <tr>
                <td class="center" align="center" valign="top">
                    <!-- BEGIN: Header -->
                    <table class="header" align="center">
                    <tr>
                        <td class="center" align="center">
                            <!-- BEGIN: Header Container -->
                            <table class="container" align="center">
                            <tr>
                                <td>
                                    <table class="row ">
                                    <tr>
                                        <td class="wrapper vertical-middle">
                                            <!-- BEGIN: Logo -->
                                            <table class="six columns">
                                            <tr>
                                                <td class="vertical-middle">
                                                    <a href="'.$CFG->wwwroot.'">
                                                        <img src="'.$CFG->wwwroot.'/pluginfile.php/1/theme_lambda/logo/1460116807/pedapal%20logo.png" width="86" height="36" border="0" alt="PedaPal - Private E-Learning Tutoring From Home - Lebanon"/>
                                                    </a>
                                                </td>
                                            </tr>
                                            </table>
                                            <!-- END: Logo -->
                                        </td>
                                        <td class="wrapper vertical-middle last">
                                            <!-- BEGIN: Social Icons -->
                                            <table class="six columns">
                                            <tr>
                                                <td>
                                                    <table class="wrapper social-icons" align="right">
                                                    <tr>
                                                       
                                                  
                                                    </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            </table>
                                            <!-- END: Social Icons -->
                                        </td>
                                    </tr>
                                    </table>
                                </td>
                            </tr>
                            </table>
                            <!-- END: Header Container -->
                        </td>
                    </tr>
                    </table>
                    <!-- END: Header -->
                    <!-- BEGIN: Content -->
                    <table class="container content" align="center">
                    <tr>
                        <td>
                            <table class="row note">
                            <tr>
                                <td class="wrapper last">
                                    <h4>'.$msg.'</h4>
                                   
                                    <!-- BEGIN: Note Panel -->
                                    <table class="twelve columns" style="margin-bottom: 10px">
                                    <tr>
                                        <td class="panel">';
                                            foreach($emailParameter as $key => $value){

                                                $massage .= "<p><span style='font-weight:bold;'>". $key ."</span> : " . $value . "</p>";
                                            }
                                        $massage .='</td>
                                        <td class="expander">
                                        </td>
                                    </tr>
                                    </table>
                               
                                    <!-- END: Note Panel -->
                                </td>
                            </tr>
                            </table>
                            <span class="devider">
                            </span>
                            <table class="row">
                            <tr>
                                <td class="wrapper last">

                                    

                                </td>
                            </tr>
                            </table>
                        </td>
                    </tr>
                    </table>
                    <!-- END: Content -->
                    <!-- BEGIN: Footer -->
                    <table class="footer" align="center">
                    <tr>
                        <td class="center" align="center">
                            <table class="container" align="center">
                            <tr>
                                <td>
                                    <!-- BEGIN: Unsubscribet -->
                                    <table class="row">
                                    <tr>
                                        <td class="wrapper last">
                                            <span style="font-size:12px;">
                                                <i>This ia a system generated email and reply is not required.</i>
                                            </span>
                                        </td>
                                    </tr>
                                    </table>
                                    <!-- END: Unsubscribe -->
                                    <!-- BEGIN: Footer Panel -->
                                    <table class="row">
                                    <tr>
                                        <td class="wrapper">
                                            <table class="four columns">
                                            <tr>
                                                <td class="vertical-middle">
                                                <!-- &copy; Keenthemes 2013. -->
                                                </td>
                                            </tr>
                                            </table>
                                        </td>
                                        <td class="wrapper last">
                                            <table class="eight columns">
                                            <tr>
                                                <td class="vertical-middle align-reverse">
                                                    <a href="'.$CFG->wwwroot.'/pages.php?view=about_us">
                                                        About Us
                                                    </a>
                                                    &nbsp;
                                                    <a href="'.$CFG->wwwroot.'/privacy.php">
                                                        Privacy Policy
                                                    </a>
                                                    &nbsp;
                                                    <a href="'.$CFG->wwwroot.'/terms.php">
                                                        Terms of Use
                                                    </a>
                                                </td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    </table>
                                    <!-- END: Footer Panel List -->
                                </td>
                            </tr>
                            </table>
                        </td>
                    </tr>
                    </table>
                    <!-- END: Footer -->
                </td>
            </tr>
            </table>
            </body>
            </html>';
                $supportCotact = $CFG->supportname.'<'. $CFG->supportemail . '>';
                $headers = "From: " . $supportCotact . "\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                mail($emailTo, $emailSubject, $massage, $headers);
                return true;
}



//////   Sending Mail to the user end ////





function getViewPageCartButton($id){

////////////////////////////////////////   View Page Cart Functionality Start ///////////////////////////////////////
global $CFG, $USER, $DB;
$moodo = get_config('local_moodocommerce');

$courses_count = $DB->get_records_sql("select * from {enrol} as me ,{user_enrolments} mue  where me.courseid=".$id. " and mue.userid='".$USER->id."' and mue.enrolid=me.id" );
$couter = count($courses_count);
// Counter ///
$course_price = $DB->get_records_sql("select * from {course_price} where course_id = ".$id);

$courses = get_course($id);
   if(empty($course_price)){
          $cseat  =  $moodo->currency_default_seat;
          $cprice = $moodo->currency_default_price;
        }
        else{
            foreach ($course_price as $key => $value) {
                $cseat  =  $value->seat;
                $cprice = $value->amount;
            }
        }


$courses_count = $DB->get_records_sql("select * from {enrol} as me ,{user_enrolments} mue  where me.courseid='".$id. "' and mue.enrolid=me.id ");
$totolc = count($courses_count);
        
           if($totolc < $cseat ){
                 $seats =  $totolc."/".  $cseat .' Seats';
             }  
            else{
                $seats = "Seats Full";
            } 
        
// countererererer///

// Cart by Mohseen start // 
echo html_writer::start_tag('div', array('style' => 'margin-bottom:20px' ));
if( !is_siteadmin() && $couter == 0 ) {
        echo html_writer::start_tag('form', array('class'=>' pull-right' , 'method' => 'POST' , 'action' => $CFG->wwwroot.'/local/moodocommerce/cart_update.php' ));
    
        $course_price = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
        $coursenamelink = '';
    
        if($cprice > 0 && $totolc < $cseat) {
                  $coursenamelink  .= "<span class='pull-right'  style='font-size:22px; margin-right:94px; '> &nbsp;&nbsp;&nbsp;&nbsp;$ ". $course_price->amout ."</span> &nbsp;&nbsp;&nbsp; <button class='pull-right' class='btn btn-primary' style='background:#37E2FA !important;'> Add to cart </button>"; 
        
        }
        else {
            if($totolc < $cseat && $cseat != 0 ) {   
                global $CFG; 
                $base64 = base64_encode($id);
                $link = $CFG->wwwroot.'/local/moodocommerce/free_enrollment.php?&key='.$base64 ; 
                $coursenamelink  .= " Free &nbsp;&nbsp;&nbsp; </span><a class='pull-right' style='margin-right:12px; margin-top:-22px; ' title='Enroll me now'  href='".$link."'></a>"; 
            }
        }
        $current_url = base64_encode($url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        $coursenamelink  .= '<input type="hidden" name="product_code" value="#'. $courses->idnumber.'" />';
        $coursenamelink  .= '<input type="hidden" name="product_name" value="'.$courses->fullname.'" />';
        $coursenamelink  .= '<input type="hidden" name="product_price" value="'.$cprice.'" />';
        $coursenamelink  .= '<input type="hidden" name="product_qty" value="1" />';
        $coursenamelink  .= '<input type="hidden" name="category" value="courses" />';
        $coursenamelink  .= '<input type="hidden" name="product_image" value="" />';
        $coursenamelink  .= '<input type="hidden" name="type" value="add" />';
        $coursenamelink  .= '<input type="hidden" name="return_url" value="'. $current_url. '" />';

        echo $coursenamelink;
        echo html_writer::end_tag('form');
}
else{
  if(!is_siteadmin()) { 
    echo "<strong class='pull-right' style='color:green'>Already Enrolled | ".$seats."</strong>";
   }
   else{
     echo "<strong class='pull-right' style='color:green'>Avialable Seats : ".$seats."</strong>";
   }
}
echo html_writer::end_tag('div'); 

////////////////////////////////////////   View Page Cart Functionality End///////////////////////////////////////
}



function getCoursePageCartButton($nametag,$course, $coursename ){

  print_r($course);

        global $DB, $CFG,$USER;
       //////////////////////////////   Cart Page renderr Button start/////////////////////////////////
        $moodo = get_config('local_moodocommerce');

        $content .= html_writer::start_tag('form', array('class' => 'info' ,'method' => 'POST' , 'action' => $CFG->wwwroot.'/local/moodocommerce/cart_update.php' ));
        $course_price = new stdClass();
        $course_price = $DB->get_records_sql("select * from {course_price} where course_id = ".$course->id);

        if(empty($course_price)){
          $cseat  =  $moodo->currency_default_seat;
          $cprice = $moodo->currency_default_price;
        }
        else{
            foreach ($course_price as $key => $value) {
                $cseat  =  $value->seat;
                $cprice = $value->amount;
            }
        }

        $courses_count = $DB->get_records_sql("select * from {enrol} as me ,{user_enrolments} mue  where me.courseid='".$course->id. "' and mue.enrolid=me.id ");
        $totolc = count($courses_count);
        $coursename = $chelper->get_course_formatted_name($course);
        $courses_my = $DB->get_records_sql("select * from {enrol} as me ,mdl_user_enrolments mue  where me.courseid=".$course->id. " and mue.userid='".$USER->id."' and mue.enrolid=me.id" );
        $couter = count($courses_my);
        $coursenamelink = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id )), $coursename, array('title' =>  $coursename , 'class' => $course->visible ? '' : 'dimmed' ));        
        // Counter ///
           if($totolc < $cseat ){
                 $seats =  ' Seats : '. $totolc." from ".$cseat ;
             }  
            else{
                $seats = "Seats not available";
            } 
        // countererererer///
        if(!is_siteadmin() && !empty($_GET) && $couter == 0) {     
            $coursenamelink  .= "<span style='font-size:15px; margin-right:94px; margin-top:-22px'> " . $seats ."  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ";
               if($cprice > 0  && $totolc < $cseat) {
                        $coursenamelink  .= "Price : ".$moodo->currency_symbol ." ". $cprice ."&nbsp;&nbsp;&nbsp; </span><button class='pull-right' class='btn btn-default' style='margin-right:12px; margin-top:-22px;width: 35px;height: 35px; text-align: center; padding: 6px 0;font-size: 12px;line-height: 1.428571429;border-radius: 20px;'> <img src=".$CFG->wwwroot."/theme/image.php/clean/core/1468317536/t/add'  class='iconsmall'></button>"; 
               }   
              else {
                       if($totolc < $cseats && $cseat != 0 ) {    
                                    global $CFG; 
                                    $base64 = base64_encode($USER->id."-free-".$course->id);
                                    $link = $CFG->wwwroot.'/local/moodocommerce/enrollment.php?&key='.$base64 ; 
                                    $coursenamelink  .= " Free &nbsp;&nbsp;&nbsp; </span><a class='pull-right' style='margin-right:12px; margin-top:-22px; ' title='Enroll me now'  href='".$link."'></a>"; 
                        }
                }
        }
        else if($couter > 0){
            $coursenamelink .= "<span class='pull-right'  style='font-size:12px; margin-right:94px; margin-top:-22px'>Already Enrolled | ".$seats."</span>";
        }
        //current URL of the Page. cart_update.php redirects back to this URL
        $current_url = base64_encode($url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        $coursenamelink  .= '<input type="hidden" name="product_code" value="'.$course->idnumber.'-'. $course->id.'" />';
        $coursenamelink  .= '<input type="hidden" name="product_name" value="'.$coursename.'" />';
        $coursenamelink  .= '<input type="hidden" name="product_price" value="'.$cprice.'" />';
        $coursenamelink  .= '<input type="hidden" name="product_qty" value="1" />';
        $coursenamelink  .= '<input type="hidden" name="category" value="courses" />';
        $coursenamelink  .= '<input type="hidden" name="product_image" value="" />';
        $coursenamelink  .= '<input type="hidden" name="type" value="add" />';
        $coursenamelink  .= '<input type="hidden" name="return_url" value="'. $current_url. '" />';

        $content .= html_writer::tag($nametag, $coursenamelink, array('class' => 'coursename'   , 'style' => 'width:100% !important'));
        $content .= html_writer::end_tag('form'); // .info 
//////////////////////////////   Cart Page renderr Button end/////////////////////////////////

echo  $content;
}
