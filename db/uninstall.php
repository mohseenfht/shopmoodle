<?php
/**
 * This file is used to make install time changes.
 * 
 * This file replaces the legacy STATEMENTS section in db/install.xml,
 * lib.php/modulename_install() post installation hook and partially defaults.php
 *
 * @package    loc_moodocommerce
 * @copyright  www.fht.co.in 
 * @author     info@fht.co.in
 */


/**
 * Custom uninstallation procedure
 */
function xmldb_local_moodocommerce_uninstall() {
    return true;
}
