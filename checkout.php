<?php
require_once (dirname(dirname(dirname(__FILE__))).'/config.php');
require_once("$CFG->libdir/formslib.php");
require_login();
$strheading = "Checkout";
$PAGE->set_pagelayout('standard');
$PAGE->set_title( $strheading );
$PAGE->navbar->add($strheading);
global $DB, $USER ,$CFG , $SITE ,$PAGE ;
echo $OUTPUT->header();

if(checkValidity()){
$moodo = get_config('local_moodocommerce');
$output = '';
        $output .= html_writer::start_tag('div', array('class' => 'info'));
          $output .= html_writer::start_tag('h3', array('class' => 'name'));
            $output .= html_writer::link('#', 'Checkout', array('title' => 'Checkout'));
          $output .= html_writer::end_tag('h3');
        $output .= html_writer::end_tag('div');
        $output .= html_writer::start_tag('hr' ); html_writer::end_tag();

        $output .= html_writer::start_tag('div', array('class' => 'well well-sm'));
          $output .= html_writer::start_tag('h3') . " Billing Details  " .html_writer::end_tag('h3');
          $output .= html_writer::start_tag('p') . $USER->firstname."  ".$USER->lastname  .html_writer::end_tag('p');
          $output .= html_writer::start_tag('p') . $USER->address .html_writer::end_tag('p');
          $output .= html_writer::start_tag('p') . $USER->city."  ".$USER->country  .html_writer::end_tag('p');
        $output .= html_writer::end_tag('div');


        $output .= html_writer::start_tag('div', array('class' => 'well well-sm' ,'style' => 'min-height:78px  !important; padding:44px;'));

              if($moodo->paypal_status == 1){
                 $output .= html_writer::start_tag('div', array('class' => 'span4 col-md-4 col-sm-6 col-xs-12' , 'style'=>'margin-left:0px' ));
                       $output .= html_writer::start_tag('input', array('class' => 'credit_card' , 'name' => 'payment_method' ,'value' => 'paypal' , 'type' => 'radio' )) . "<b>Credit Card (Payapal) </b>" ;
                 $output .= html_writer::end_tag('div');
              }

              if($moodo->amazon_status == 1){
                 $output .= html_writer::start_tag('div', array('class' => 'span4 col-md-4 col-sm-6 col-xs-12' ,'style'=>'margin-left:0px'));
                       $output .= html_writer::start_tag('input', array('class' => 'credit_card' , 'name' => 'payment_method' ,'value' => 'amazon' , 'type' => 'radio' )) . "<b>Credit Card (Amazon Payment)</b> " ;
                 $output .= html_writer::end_tag('div');
              }


              if($moodo->authorize_status == 1){
                 $output .= html_writer::start_tag('div', array('class' => 'span4 col-md-4 col-sm-6 col-xs-12','style'=>'margin-left:0px' ));
                       $output .= html_writer::start_tag('input', array('class' => 'credit_card' , 'name' => 'payment_method' ,'value' => 'authorize' , 'type' => 'radio' )) . "<b>Credit Card (Authorize.net)</b> " ;
                 $output .= html_writer::end_tag('div');
              }


              if($moodo->hekapay_status == 1){
                 $output .= html_writer::start_tag('div', array('class' => 'span4 col-md-4 col-sm-6 col-xs-12','style'=>'margin-left:0px' ));
                       $output .= html_writer::start_tag('input', array('class' => 'credit_card' , 'name' => 'payment_method' ,'value' => 'hekapay' , 'type' => 'radio' )) . "<b>Hekapay Voucher</b>" ;
                 $output .= html_writer::end_tag('div');
              }


              if($moodo->skrill_status == 1){
                 $output .= html_writer::start_tag('div', array('class' => 'span4 col-md-4 col-sm-6 col-xs-12','style'=>'margin-left:0px' ));
                       $output .= html_writer::start_tag('input', array('class' => 'credit_card' , 'name' => 'payment_method' ,'value' => 'skrill' , 'type' => 'radio' )) . "<b>Credit Card (Skrill)</b> " ;
                 $output .= html_writer::end_tag('div');
              }


              if($moodo->stripe_status == 1){
                 $output .= html_writer::start_tag('div', array('class' => 'span4 col-md-4 col-sm-6 col-xs-12','style'=>'margin-left:0px' ));
                       $output .= html_writer::start_tag('input', array('class' => 'credit_card' , 'name' => 'payment_method' ,'value' => 'stripe' , 'type' => 'radio' )) . "<b> Credit Card (Stripe) </b>" ;
                 $output .= html_writer::end_tag('div');
              }

              if($moodo->twocheckout_status == 1){
                 $output .= html_writer::start_tag('div', array('class' => 'span4 col-md-4 col-sm-6 col-xs-12','style'=>'margin-left:0px' ));
                       $output .= html_writer::start_tag('input', array('class' => 'credit_card' , 'name' => 'payment_method' ,'value' => 'twocheckout' , 'type' => 'radio' )) . "<b> Credit Card (2Checkout) </b>" ;
                 $output .= html_writer::end_tag('div');
              }

              if($moodo->audi_status == 1){
                 $output .= html_writer::start_tag('div', array('class' => 'span4 col-md-4 col-sm-6 col-xs-12','style'=>'margin-left:0px' ));
                       $output .= html_writer::start_tag('input', array('class' => 'credit_card' , 'name' => 'payment_method' ,'value' => 'audipay' , 'type' => 'radio' )) . "<b> Credit Card (AudiPay) </b>" ;
                 $output .= html_writer::end_tag('div');
              }

               if($moodo->credit_status == 1){
                 $output .= html_writer::start_tag('div', array('class' => 'span4 col-md-4 col-sm-6 col-xs-12','style'=>'margin-left:0px' ));
                       $output .= html_writer::start_tag('input', array('class' => 'credit_card' , 'name' => 'payment_method' ,'value' => 'credit' , 'type' => 'radio' )) . "<b>Credits</b>
                        <span>( Credit :". $moodo->currency_symbol.' '.getMyCredit($USER->id)." )</span>" ;
                 $output .= html_writer::end_tag('div');
              }

        $output .= html_writer::end_tag('div');
        echo $output;
        $output = '';


                          $current_url = base64_encode($url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
                          $products = myCartitems( $USER->id );
                        	if(!empty($products)){
                        	   $total = 0;
                        		 $n = 1; 
                             $table = new html_table();
                             $table->head = array('Code','Course Name', 'Qty' , 'Unit Price','Action' );
                                        foreach ($products as $cart_itm) {
                                            $link = "cart_update.php?removep=".$cart_itm->code."&return_url=".$current_url ;
                                            $amounte = $moodo->currency_symbol ." " .$cart_itm->price ;
                                            $table->data[] = array($cart_itm->code, $cart_itm->name, $cart_itm->qty,$amounte,'<a href="'.$link.'"  class="btn btn-danger" title="Remove from the cart" style="color:#FFFFFF !important;" >x</a>');
                                            $subtotal = ($cart_itm->price*$cart_itm->qty);
                                            $total = ($total + $subtotal);
                                            $i++;
                                        }
                             echo html_writer::table($table);
	                       }
	
	else{

    $output .= html_writer::start_tag('div' , array('class' => 'note note-danger'));
        $output .= html_writer::start_tag('p') . "Your Cart is empty ! " . html_writer::start_tag('a', array('href' => $CFG->wwwroot.'/course/' , 'title' => 'Shop Now'  ))  .html_writer::end_tag('p');
    $output .= html_writer::end_tag('div');
			
	}

$output .=html_writer::start_tag('div' , array('class' => 'checkout-total-block pull-right'));
  $output .=html_writer::start_tag('span' , array('class' => 'price' , 'style' => 'font-size:19px ; font-weight:bold;')); 
     $output .=  $moodo->currency_symbol . " " .$total;
  $output .= html_writer::end_tag('span');
$output .=html_writer::end_tag('div');
$output .=html_writer::start_tag('div' , array('class' => 'clearfix'));
$output .=html_writer::end_tag('div');
$output .=html_writer::start_tag('div' , array('class' => 'col-xs-12 col-md-12'));
    $output .=html_writer::start_tag('div' , array('class' => 'span6 col-md-12'));
        $output .=html_writer::start_tag('a' , array('class' => 'btn btn-primary', 'href' => $CFG->wwwroot."/course/", 'style' => 'color:#FFFFFF !important'  ));
          $output .= "Continue Shopping";
        $output .=html_writer::end_tag('a');
        $output .=html_writer::start_tag('p')." if you want to add more courses ".html_writer::end_tag('p');;
    $output .=html_writer::end_tag('div');

    if($products) {
      $output .=html_writer::start_tag('div' , array('class'=> 'span6 col-md-6 pull-right'  , 'id' => 'getform' ,'style' => 'text-align:right !important;' ));
      $output .= html_writer::end_tag('div');
    }
$output .=html_writer::end_tag('div');

echo $output;

}
else{
      echo getPluginErrorMessage();
}

?>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript">
  $(function(){

    var host = '<?php echo $CFG->wwwroot ;?>/local/moodocommerce/corefiles/';
    $('.credit_card').on('click', function(){
          var paytype = $(this).attr("value");
          console.log("Payment Method selected  "+ paytype);
          $.post(host+ 'getPaymentform.php' , { 'paytype' : paytype }, function(data){
                  //console.log(data);
                  $('#getform').html(data);
          });
     });
});
</script>

<script type="text/javascript"><!--
$(document).on('click','#button-confirm' ,function() {
  $.ajax({
    url: 'corefiles/getPaymentform.php?method=authorizesend',
    type: 'post',
    data: $('#payment :input'),
    dataType: 'json',
    cache: false,
    beforeSend: function() {
      $('#msgdiv').html("<div class='alert alert-danger alert-dismissable'>Loading.......</div>");
    },
    complete: function() {
      //$('#button-confirm').button('reset');
    },
    success: function(json) {
      if (json['error']) {
        $('#msgdiv').html("<div class='alert alert-danger alert-dismissable'><i class='fa fa-ban'></i><button class='close' aria-hidden='true' data-dismiss='alert' type='button'>×</button> " + json['error'] + "</div>");
      }

      if (json['redirect']) {
        location = json['redirect'];
      }
    }
  });
});



$(document).on('click','#button-voucher' ,function() {
  $.ajax({
    url: 'corefiles/voucherprocess.php',
    type: 'post',
    data: $('#payment :input'),
    dataType: 'json',
    cache: false,
    beforeSend: function() {
      $('#msgdiv').html("<div class='alert alert-success alert-dismissable'>Loading.......</div>");
    },
    complete: function() {
      //$('#button-voucher').button('reset');
    },
    success: function(json) {
      if (json['error']) {
        $('#msgdiv').html("<div class='alert alert-danger alert-dismissable'><i class='fa fa-ban'></i><button class='close' aria-hidden='true' data-dismiss='alert' type='button'>×</button> " + json['error'] + "</div>");
      }

      if (json['valid']) {
        $('#msgdiv').html("<div class='alert alert-info'><i class='fa fa-ban'></i><button class='close' aria-hidden='true' data-dismiss='alert' type='button'>×</button> " + json['valid'] + "</div>");
        console.log(json['valid']);
      }
    }
  });
});


$(document).on('click','#paypal_payment' ,function() {
      window.location.href = 'corefiles/getPaymentform.php?method=paypalsend';
});


$(document).on('click','#skrill_payment' ,function() {
      window.location.href = 'corefiles/getPaymentform.php?method=skrillsend';
});
//--></script>

<?php 
echo $OUTPUT->footer();
?>
