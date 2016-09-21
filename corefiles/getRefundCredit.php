<?php
//error_reporting(0);
require_once (dirname(dirname(dirname(__FILE__))).'/config.php');
//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

  $id = $_POST['iid'];
  echo  getPrices($id);

function getPrices($id){
	global $DB;
	$courses = $DB->get_records_sql("select * from mdl_user where id=".$id );

	foreach ($courses as $key => $value) {
	$arry = array('credit' => $value->credit);
	}
	return  json_encode($arry);
}
?>