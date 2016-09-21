<?php
/**
 * Navigation lang file.
 *
 * @package    local_moodocommerce
 * @author     Mohseen Khan <info@fht.co.in>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once (dirname(dirname(dirname(__FILE__))).'/config.php');
//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");
require_login();
$strheading = "Course Prices & seats";
$PAGE->set_pagelayout('standard');
$PAGE->set_title( $strheading );
$PAGE->navbar->add($strheading);
echo $OUTPUT->header();
global $CFG, $PAGE, $USER,$DB;

if(checkValidity()){
$courses = $DB->get_records_sql("select * from {course} where id NOT IN (1) and visible=1");
if(isset($_POST['submitbutton'])){
   extract($_POST);
   $priceRecord = $DB->get_records_sql("select * from {course_price} where course_id = '".$course."' ");

   foreach($priceRecord as $key => $val){
       $ids = $val->id;
   }
   $count = count($priceRecord);
   if($count == 0) {
      $insert_data     = new stdClass(); 
      $insert_data->course_id          =$course;
      $insert_data->amount             =  $price;
      $insert_data->seat             =  $seats;
      $res=$DB->insert_record('course_price',$insert_data,true);
   }
   else {
      $update_data     = new stdClass(); 
      $update_data->id = $ids;
      $update_data->course_id          =$course;
      $update_data->amount             =  $price;
      $update_data->seat             =  $seats;
      $res=$DB->update_record('course_price',$update_data,true);
   }
  $msg = 'Record updated successfully !';
}

$output = '';
$output = html_writer::start_tag('form', array('action' => new moodle_url('/local/moodocommerce/prices.php'),
                'id' => 'chooserform' ,'class' => 'form-horizontal', 'method' => 'post'));
$output .= html_writer::start_tag('fieldset', array('class' => 'clearfix collapsible'));
if(isset($msg)){
  $output .= '<div class="well">
                <h5>' .$msg . '</h5>
              </div>';
}

        $output .= html_writer::start_tag('div', array('class' => 'info'));
          $output .= html_writer::start_tag('h3', array('class' => 'name'));
            $output .= html_writer::link('#', 'Price & seats', array('title' => 'Price & seats'));
          $output .= html_writer::end_tag('h3');
        $output .= html_writer::end_tag('div'); 
        $output .= html_writer::start_tag('hr' ); html_writer::end_tag();

        $output .= html_writer::start_tag('div' , array ('class' =>'fcontainer clearfix') );

        $output .= html_writer::start_tag('div', array('class' => 'fitem fitem_fselect' , 'id' => 'fitem_id_country'));
              $output .= html_writer::start_tag('div' , array('class' => 'fitemtitle'));
                 $output .= html_writer::start_tag('label' , array('for' => 'id_country')) . 'Courses' . html_writer::end_tag();
              $output .= html_writer::end_tag('div'); 

             $output .= html_writer::start_tag('div' , array('class' => 'fitemtitle'));
               $output .= html_writer::start_tag('div' , array('class' => 'felement fselect'));
                  $output .= '<select id="id_course" name="course" required  style="width:50%;">';
                     $output .= '<option value="">----- Select a course-----------</option>';
                         foreach ($courses as $key => $value) {
                           $output .= '<option value="'. $value->id .'"> '. $value->fullname . '</option>'; 
                         }
                  $output .='</select>';
               $output .= html_writer::end_tag('div');
             $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');




        $output .= html_writer::start_tag('div', array('class' => 'fitem required fitem_ftext' , 'id' => 'fitem_id_price'));
              $output .= html_writer::start_tag('div' , array('class' => 'fitemtitle'));
                 $output .= html_writer::start_tag('label' , array('for' => 'id_country')) . 'Price *' . html_writer::end_tag();
              $output .= html_writer::end_tag('div'); 

              $output .= html_writer::start_tag('div' , array('class' => 'felement ftext'));
                $output .= '<input type="text" id="id_price" name="price" value="" style="width:50%;" required>';
              $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');


        $output .= html_writer::start_tag('div', array('class' => 'fitem required fitem_ftext' , 'id' => 'fitem_id_price'));
              $output .= html_writer::start_tag('div' , array('class' => 'fitemtitle'));
                 $output .= html_writer::start_tag('label' , array('for' => 'id_country')) . 'Seats *' . html_writer::end_tag();
              $output .= html_writer::end_tag('div'); 

              $output .= html_writer::start_tag('div' , array('class' => 'felement ftext'));
                $output .= '<input type="number" id="id_seats" name="seats" style="width:50%;" value="" required>';
              $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');
      $output .= html_writer::end_tag('div');  


$output .= html_writer::end_tag('fieldset');

$output .= html_writer::start_tag('fieldset' , array('class' => 'hidden'));
        $output .= html_writer::start_tag('div' , array('class'=> 'fitem fitem_actionbuttons fitem_fgroup' , 'id' => 'fgroup_id_buttonar'));
           $output .= html_writer::start_tag('div' , array('class' => 'felement fgroup'));
               $output .= html_writer::start_tag('input' ,array('id'=> 'id_submitbutton' , 'name' => 'submitbutton' , 'value' => 'Set Price & seats' , 'type' => 'submit'));
               $output .= html_writer::start_tag('input' ,array('id'=> 'id_cancel' , 'name' => 'cancel' , 'value' => 'Cancel' , 'type' => 'reset'));

           $output .=html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');


        $output .= html_writer::start_tag('div' , array('class' => 'fdescription required'));
             $output .= 'There are required fields in this form marked * .';
        $outpuy .= html_writer::end_tag('div');
$output .= html_writer::end_tag('fieldset');

$output .= html_writer::end_tag('form');

}else{
      echo getPluginErrorMessage();
}

echo $output;
?>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript">
  $(function(){
    var host = '<?php echo $CFG->wwwroot ;?>/local/moodocommerce/corefiles/getPricecourse.php';
    $('#id_course').on('change', function(){
        var id = $("#id_course :selected").val();
        $.post( host , { method : 'get', iid : id } , function(data){
          if(data != null){
              $('#id_price').val(data.amount);
              $('#id_seats').val(data.seat);
          }
          else {
               $('#id_price').val('0');
              $('#id_seats').val('0');
          }
        } , 'json');
     });

});
</script>
<?php 
echo $OUTPUT->footer(); 
?>
