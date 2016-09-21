<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * The mod_scorm attempt deleted event.
 *
 * @package    mod_scorm
 * @copyright  2013 onwards Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_moodocommerce\event;
defined('MOODLE_INTERNAL') || die();

/**
 * The mod_scorm attempt deleted event class.
 *
 * @property-read array $other {
 *      Extra information about event properties.
 *
 *      - int attemptid: Attempt id.
 * }
 *
 * @package    mod_scorm
 * @since      Moodle 2.7
 * @copyright  2013 onwards Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attempt_deleted extends \core\event\base {

    /**
     * Init method.
     */
    protected function init() {
        // $this->data['crud'] = 'd';
        // $this->data['edulevel'] = self::LEVEL_TEACHING;

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

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' deleted the attempt with id '{$this->other['attemptid']}' " .
            "for the scorm activity with course module id '$this->contextinstanceid'.";
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        // return get_string('eventattemptdeleted', 'mod_scorm');
    }

    /**
     * Get URL related to the action
     *
     * @return \moodle_url
     */
    public function get_url() {
        // return new \moodle_url('/mod/scorm/report.php', array('id' => $this->contextinstanceid));
    }

    /**
     * Replace add_to_log() statement.
     *
     * @return array of parameters to be passed to legacy add_to_log() function.
     */
    protected function get_legacy_logdata() {
        // return array($this->courseid, 'scorm', 'delete attempts', 'report.php?id=' . $this->contextinstanceid,
        //         $this->other['attemptid'], $this->contextinstanceid);
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        // parent::validate_data();

        // if (empty($this->other['attemptid'])) {
        //     throw new \coding_exception('The \'attemptid\' must be set in other.');
        // }
    }

    public static function get_other_mapping() {
        // Nothing to map.
        return false;
    }
}
