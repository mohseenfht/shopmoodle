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
 * Post installation procedure
 *
 * @see upgrade_plugins_modules()
 */
function xmldb_local_moodocommerce_install() {
    global $DB;

    // $record = new stdClass();
    // $record->course         = '';
    // $record->wiziqid = '';
    // $record->type = '1';
    // $record->name = 'My Content';
    // $record->title = '';
    // $record->parentid = '0';
    // $record->path = '';
    // $record->userid = '';
    // $record->uploadtime = time();
    // $record->contentid = '';
    // $record->status = '';
    // $record->wcid = "1".time();
    // $DB->insert_record('wiziq_content', $record);
    return true;
}

/**
 * Post installation recovery procedure
 *
 * @see upgrade_plugins_modules()
 */
function xmldb_local_moodocommerce_install_recovery() {
}
