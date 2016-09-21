<?php
/**
 * Navigation lang file.
 *
 * @package    local_moodocommerce
 * @author     Mohseen Khan <info@fht.co.in>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();
//error_reporting(0);
if ($hassiteconfig) {
    // New settings page





    $page = new admin_settingpage('moodocommerce', get_string('pluginname', 'local_moodocommerce'));
    // Document directory

    //////////////// --------------- Plugin Secrect Start ---------------------------///////////////

     $page->add(new admin_setting_configtext('local_moodocommerce/moodocommerce_secret',get_string('moodocommerce_secret', 'local_moodocommerce'), get_string('moodocommerce_secret', 'local_moodocommerce'),'',PARAM_TEXT,100));

   ///////////////-----------------Plugin secret End--------------------------------///////////////
    
    ////////////////--------------  Authorize net  Start ----------------------------///////////////
    $page->add(new admin_setting_heading('local_moodocommerce/moodocommerce_authorize', get_string('moodocommerce_authorize', 'local_moodocommerce'),''));
    $authorStatus = array('1' => 'Enabled' , '0' => 'Disabled');
    $page->add(new admin_setting_configselect('local_moodocommerce/authorize_status', get_string('authorize_status', 'local_moodocommerce'),get_string('authorize_status', 'local_moodocommerce'), 'Enabled', $authorStatus));
    $page->add(new admin_setting_configtext('local_moodocommerce/authorize_loginid',get_string('authorize_loginid', 'local_moodocommerce'), get_string('authorize_loginid', 'local_moodocommerce'),'',PARAM_TEXT,40));
    $page->add(new admin_setting_configtext('local_moodocommerce/authorize_transkey',get_string('authorize_transkey', 'local_moodocommerce'), get_string('authorize_transkey', 'local_moodocommerce'),'',PARAM_TEXT,40));
    $page->add(new admin_setting_configtext('local_moodocommerce/authorize_md5',get_string('authorize_md5', 'local_moodocommerce'), get_string('authorize_md5', 'local_moodocommerce'),'',PARAM_TEXT,40));
    ////////////////--------------  Authorize net  End ----------------------------///////////////

    ////////////////--------------  Payapal Start ----------------------------///////////////
    $payapalStatus = array('1' => 'Enabled' , '0' => 'Disabled');
    $page->add(new admin_setting_heading('local_moodocommerce/moodocommerce_paypal', get_string('moodocommerce_paypal', 'local_moodocommerce'),''));
    $page->add(new admin_setting_configselect('local_moodocommerce/paypal_status', get_string('paypal_status', 'local_moodocommerce'),get_string('paypal_status', 'local_moodocommerce'), 'Enabled', $payapalStatus));
    $page->add(new admin_setting_configtext('local_moodocommerce/paypal_bussiness',get_string('paypal_bussiness', 'local_moodocommerce'), get_string('paypal_bussiness', 'local_moodocommerce'),'',PARAM_TEXT,40));
    ////////////////--------------  Paypal  Start ----------------------------///////////////


    ///////////////-----------------Stripe Start ---------------------------////////////////
    $stripeStatus = array('1' => 'Enabled' , '0' => 'Disabled');
    $page->add(new admin_setting_heading('local_moodocommerce/moodocommerce_stripe', get_string('moodocommerce_stripe', 'local_moodocommerce'),''));
    $page->add(new admin_setting_configselect('local_moodocommerce/stripe_status', get_string('stripe_status', 'local_moodocommerce'),get_string('stripe_status', 'local_moodocommerce'), 'Enabled', $stripeStatus));
    $page->add(new admin_setting_configtext('local_moodocommerce/stripe_secretkey',get_string('stripe_secretkey', 'local_moodocommerce'), get_string('stripe_secretkey', 'local_moodocommerce'),'',PARAM_TEXT,40));
    $page->add(new admin_setting_configtext('local_moodocommerce/stripe_publishablekey',get_string('stripe_publishablekey', 'local_moodocommerce'), get_string('stripe_publishablekey', 'local_moodocommerce'),'',PARAM_TEXT,40));
    ///////////////-----------------Stripe End ---------------------------//////////////////


    ///////////////-----------------Amazon Start ---------------------------////////////////
    $amazonStatus = array('1' => 'Enabled' , '0' => 'Disabled');
    $page->add(new admin_setting_heading('local_moodocommerce/moodocommerce_amazon', get_string('moodocommerce_amazon', 'local_moodocommerce'),''));
    $page->add(new admin_setting_configselect('local_moodocommerce/amazon_status', get_string('amazon_status', 'local_moodocommerce'),get_string('amazon_status', 'local_moodocommerce'), 'Enabled', $amazonStatus));
    $page->add(new admin_setting_configtext('local_moodocommerce/amazon_merchantid',get_string('amazon_merchantid', 'local_moodocommerce'), get_string('amazon_merchantid', 'local_moodocommerce'),'',PARAM_TEXT,40));
    $page->add(new admin_setting_configtext('local_moodocommerce/amazon_accesskey',get_string('amazon_accesskey', 'local_moodocommerce'), get_string('amazon_accesskey', 'local_moodocommerce'),'',PARAM_TEXT,40));
    $page->add(new admin_setting_configtext('local_moodocommerce/amazon_secretkey',get_string('amazon_secretkey', 'local_moodocommerce'), get_string('amazon_secretkey', 'local_moodocommerce'),'',PARAM_TEXT,40));
    $page->add(new admin_setting_configtext('local_moodocommerce/amazon_clientid',get_string('amazon_clientid', 'local_moodocommerce'), get_string('amazon_clientid', 'local_moodocommerce'),'',PARAM_TEXT,40));
    $page->add(new admin_setting_configtext('local_moodocommerce/amazon_clientsecret',get_string('amazon_clientsecret', 'local_moodocommerce'), get_string('amazon_clientsecret', 'local_moodocommerce'),'',PARAM_TEXT,40));
    ///////////////-----------------Amazon Start ---------------------------////////////////


    ///////////////-----------------Skrill Start ---------------------------////////////////
    $skrillStatus = array('1' => 'Enabled' , '0' => 'Disabled');
    $page->add(new admin_setting_heading('local_moodocommerce/moodocommerce_skrill', get_string('moodocommerce_skrill', 'local_moodocommerce'),''));
    $page->add(new admin_setting_configselect('local_moodocommerce/skrill_status', get_string('skrill_status', 'local_moodocommerce'),get_string('skrill_status', 'local_moodocommerce'), 'Enabled', $skrillStatus));
    $page->add(new admin_setting_configtext('local_moodocommerce/skrill_email',get_string('skrill_email', 'local_moodocommerce'), get_string('skrill_email', 'local_moodocommerce'),'',PARAM_TEXT,40));
    $page->add(new admin_setting_configtext('local_moodocommerce/skrill_secret',get_string('skrill_secret', 'local_moodocommerce'), get_string('skrill_secret', 'local_moodocommerce'),'',PARAM_TEXT,40));
    ///////////////-----------------Skrill End ---------------------------//////////////////


    ///////////////-----------------2Checkout Start ---------------------------////////////////
    $twocheckStatus = array('1' => 'Enabled' , '0' => 'Disabled');
    $page->add(new admin_setting_heading('local_moodocommerce/moodocommerce_twocheckout', get_string('moodocommerce_twocheckout', 'local_moodocommerce'),''));
    $page->add(new admin_setting_configselect('local_moodocommerce/twocheckout_status', get_string('twocheckout_status', 'local_moodocommerce'),get_string('twocheckout_status', 'local_moodocommerce'), 'Enabled', $twocheckStatus));
    $page->add(new admin_setting_configtext('local_moodocommerce/twocheckout_accountid',get_string('twocheckout_accountid', 'local_moodocommerce'), get_string('twocheckout_accountid', 'local_moodocommerce'),'',PARAM_TEXT,40));
    $page->add(new admin_setting_configtext('local_moodocommerce/twocheckout_secretword',get_string('twocheckout_secretword', 'local_moodocommerce'), get_string('twocheckout_secretword', 'local_moodocommerce'),'',PARAM_TEXT,40));
    ///////////////-----------------2Checkout End ---------------------------//////////////////
   

    ///////////////---------------- Hekapay Setting Start ---------------------------////////////////
    $hekapayStatus = array('1' => 'Enabled' , '0' => 'Disabled');
    $page->add(new admin_setting_heading('local_moodocommerce/moodocommerce_hekapay', get_string('moodocommerce_hekapay', 'local_moodocommerce'),''));
    $page->add(new admin_setting_configselect('local_moodocommerce/hekapay_status', get_string('hekapay_status', 'local_moodocommerce'),get_string('hekapay_status', 'local_moodocommerce'), 'Disabled', $hekapayStatus));
     $page->add(new admin_setting_configtext('local_moodocommerce/hekapay_url', get_string('hekapay_url', 'local_moodocommerce'),'example : http://liveserver.com/hekapay/web/en/cards/redeem', '', PARAM_TEXT,40));
    $page->add(new admin_setting_configtext('local_moodocommerce/hekapay_email', get_string('hekapay_email', 'local_moodocommerce'),get_string('hekapay_email', 'local_moodocommerce'), '', PARAM_TEXT,40));
    $page->add(new admin_setting_configtext('local_moodocommerce/hekapay_secret',get_string('hekapay_secret', 'local_moodocommerce'), get_string('hekapay_secret', 'local_moodocommerce'),'',PARAM_TEXT,40));

    ///////////////-----------------Hekapay Setting End ---------------------------//////////////////



    ///////////////---------------- AudiPay Setting Start ---------------------------////////////////
    $audipayStatus = array('1' => 'Enabled' , '0' => 'Disabled');
    $page->add(new admin_setting_heading('local_moodocommerce/moodocommerce_audi', get_string('moodocommerce_audi', 'local_moodocommerce'),''));
    $page->add(new admin_setting_configselect('local_moodocommerce/audi_status', get_string('audi_status', 'local_moodocommerce'),get_string('audi_status', 'local_moodocommerce'), 'Disabled', $audipayStatus));
    $page->add(new admin_setting_configtext('local_moodocommerce/audi_merchantid', get_string('audi_merchantid', 'local_moodocommerce'),get_string('audi_merchantid', 'local_moodocommerce'), '', PARAM_TEXT,40));
    $page->add(new admin_setting_configtext('local_moodocommerce/audi_secret',get_string('audi_secret', 'local_moodocommerce'), get_string('audi_secret', 'local_moodocommerce'),'',PARAM_TEXT,40));
    $page->add(new admin_setting_configtext('local_moodocommerce/audi_merchantaccess',get_string('audi_merchantaccess', 'local_moodocommerce'), get_string('audi_merchantaccess', 'local_moodocommerce'),'',PARAM_TEXT,40));

    ///////////////-----------------AudiPay Setting End ---------------------------//////////////////





     ///////////////---------------- Credit Setting Start ---------------------------////////////////
    $creditStatus = array('1' => 'Enabled' , '0' => 'Disabled');
    $page->add(new admin_setting_heading('local_moodocommerce/moodocommerce_credit', get_string('moodocommerce_credit', 'local_moodocommerce'),''));
    $page->add(new admin_setting_configselect('local_moodocommerce/credit_status', get_string('credit_status', 'local_moodocommerce'),get_string('credit_status', 'local_moodocommerce'), 'Disabled', $creditStatus));
    $page->add(new admin_setting_configtext('local_moodocommerce/credit_default',get_string('credit_default', 'local_moodocommerce'), get_string('credit_default', 'local_moodocommerce'),'20',PARAM_TEXT,40));

    ///////////////-----------------Credit Setting End ---------------------------//////////////////




    ///////////////-----------------Currency Setting Start ---------------------------////////////////
    $page->add(new admin_setting_heading('local_moodocommerce/moodocommerce_currency', get_string('moodocommerce_currency', 'local_moodocommerce'),''));
    $page->add(new admin_setting_configtext('local_moodocommerce/currency_code', get_string('currency_code', 'local_moodocommerce'),get_string('currency_code', 'local_moodocommerce'), 'USD', PARAM_TEXT,40));
    $page->add(new admin_setting_configtext('local_moodocommerce/currency_symbol',get_string('currency_symbol', 'local_moodocommerce'), get_string('currency_symbol', 'local_moodocommerce'),'$',PARAM_TEXT,40));
    $page->add(new admin_setting_configtext('local_moodocommerce/currency_default_price',get_string('currency_default_price', 'local_moodocommerce'), '( If course price not set )','10',PARAM_TEXT,40));
    $page->add(new admin_setting_configtext('local_moodocommerce/currency_default_seat',get_string('currency_default_seat', 'local_moodocommerce'), '( If course seat not set )','10',PARAM_TEXT,40));
    ///////////////-----------------Currency Setting End ---------------------------//////////////////


 
    $str = '<center><img src="'.$CFG->wwwroot.'/local/moodocommerce/pix/fht-logo.png"/></center><br />';
    $page->add(new admin_setting_heading('local_moodocommerce/plugin_logo', '', $str));
    $page->add(new admin_setting_heading('local_moodocommerce/plugin_desc', 'Build. No : MC27FHT0120140702', get_string('plugin_desc', 'local_moodocommerce')));
   
    $ADMIN->add('localplugins', $page);
}
?>