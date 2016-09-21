<?php

require_once (dirname(dirname(dirname(__FILE__))).'/config.php');

require_once("$CFG->libdir/formslib.php");

global $CFG, $PAGE, $USER,$DB; 
if (!isloggedin()) {
	    $return_url = $CFG->wwwroot;
		redirect($return_url.'/login/', $OUTPUT->notification("Please login to buy course"));
}


else {

// check if there is an entry in the database 
 $courses_my = $DB->get_records_sql("select * from mdl_carts  where user_id='".$USER->id. "' " );
 $counter = count($courses_my); 

 if($counter){

 	if($_SESSION['DCART'] != 1) {

     foreach ($courses_my as $key => $value) {

     	$new_product = array(array('name'=>$value->name, 'code'=>$value->code, 'qty'=>1, 'price'=>$value->price, 'image' => $value->image ,'category' => $value->category ));

     	if(isset($_SESSION["PRODUCTS"])) //if we have the session
		{
			$found = false; //set found item to false
			
			foreach ($_SESSION["PRODUCTS"] as $cart_itm) //loop through session array
			{
				if($cart_itm["code"] == $value->code){ //the item exist in array

					$product[] = array('name'=>$cart_itm["name"], 'code'=>$cart_itm["code"], 'qty'=> 1 , 'price'=>$cart_itm["price"] ,'image' => $cart_itm["image"],'category' => $cart_itm["category"] );
					$found = true;
				}else{
					//item doesn't exist in the list, just retrive old info and prepare array for session var
					$product[] = array('name'=>$cart_itm["name"], 'code'=>$cart_itm["code"], 'qty'=> 1, 'price'=>$cart_itm["price"] ,'image' => $cart_itm["image"],'category' => $cart_itm["category"]);
				}
			}
			
			if($found == false) //we didn't find item in array
			{
				//add new user item in array
				$_SESSION["PRODUCTS"] = array_merge($product, $new_product);
			}else{
				//found user item in array list, and increased the quantity
				$_SESSION["PRODUCTS"] = $product;
			}
			
		}else{
			//create a new session var if does not exist
            unset($_SESSION["PRODUCTS"]);
			$_SESSION["PRODUCTS"] = $new_product;
		}



     }

    // print_r($_SESSION['PRODUCTS']);
     $_SESSION['DCART'] = 1;
    }
} 

//add item in shopping cart
if(isset($_POST["type"]) && $_POST["type"]=='add')
{
	
	$product_code 	= filter_var($_POST["product_code"], FILTER_SANITIZE_STRING); //product code
	$product_qty 	= filter_var($_POST["product_qty"], FILTER_SANITIZE_NUMBER_INT); //product code
	
	$product_name 	= filter_var($_POST["product_name"], FILTER_SANITIZE_STRING); //product name
	$product_price 	= filter_var($_POST["product_price"], FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION); //product price
	$product_image 	= filter_var($_POST["product_image"], FILTER_SANITIZE_STRING); //product Image
	$product_category = filter_var($_POST["category"],FILTER_SANITIZE_STRING);
	$return_url  	= base64_decode($_POST["return_url"]); //return url

		//prepare array for the session variable
		$new_product = array(array('name'=>$product_name, 'code'=>$product_code, 'qty'=>1, 'price'=>$product_price, 'image' => $product_image ,'category' => $product_category , ));
        // insert in a cart table start //////////////////

	    $courses_my = $DB->get_records_sql("select * from mdl_carts  where user_id='".$USER->id. "' and code='".$product_code."' " );
            $counter = count($courses_my);
                 if( $counter == 0 ) {        
                            $insert_data     =	new stdClass(); 
                            $insert_data->name          =$product_name;
                            $insert_data->code             =  $product_code;
                            $insert_data->qty             =  $product_qty;
                            $insert_data->price        =  $product_price; 
                            $insert_data->image        =$product_image;
                            $insert_data->category        =  $product_category; 
                            $insert_data->row_id        =$USER->sesskey;
                            $insert_data->user_id        =$USER->id;
                           $res=$DB->insert_record('carts',$insert_data,true);
                 }

        // insert in a cart table start //////////////////

	
		if(isset($_SESSION["PRODUCTS"])) //if we have the session
		{
			$found = false; //set found item to false
			
			foreach ($_SESSION["PRODUCTS"] as $cart_itm) //loop through session array
			{
				if($cart_itm["code"] == $product_code){ //the item exist in array

					$product[] = array('name'=>$cart_itm["name"], 'code'=>$cart_itm["code"], 'qty'=>1, 'price'=>$cart_itm["price"] ,'image' => $cart_itm["image"],'category' => $cart_itm["category"] );
					$found = true;
				}else{
					//item doesn't exist in the list, just retrive old info and prepare array for session var
					$product[] = array('name'=>$cart_itm["name"], 'code'=>$cart_itm["code"], 'qty'=>1, 'price'=>$cart_itm["price"] ,'image' => $cart_itm["image"],'category' => $cart_itm["category"]);
				}
			}
			
			if($found == false) //we didn't find item in array
			{
				//add new user item in array
				$_SESSION["PRODUCTS"] = array_merge($product, $new_product);
			}else{
				//found user item in array list, and increased the quantity
				$_SESSION["PRODUCTS"] = $product;
			}
			
		}else{
			//create a new session var if does not exist
            unset($_SESSION["PRODUCTS"]);
			$_SESSION["PRODUCTS"] = $new_product;
		}
		
	
	//redirect back to original page
	header('Location:'.$return_url);
	
}

//remove item from shopping cart
if(isset($_GET["removep"]) && isset($_GET["return_url"]) && isset($_SESSION["PRODUCTS"]))
{
	$product_code 	= $_GET["removep"]; //get the product code to remove
	$return_url 	= base64_decode($_GET["return_url"]); //get return url
    $res = $DB->execute("DELETE FROM {carts} WHERE code='".$product_code."'");
	//redirect back to original page
	header('Location:'.$return_url);
}
}///// Else end
?>