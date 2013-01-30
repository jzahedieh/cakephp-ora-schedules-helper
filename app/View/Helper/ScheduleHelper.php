<?php
/**
 * Outputs a schedule input or out for 4 options with error messages
 *
 * Usage...
 *
 *   echo $this->Schedule->scheduleGenerator(array('type' => 'input'));
 *   echo $this->Schedule->scheduleGenerator(array('type' => 'output', 'registration' => $registration));
 *
 * @author Jonathan Zahedieh
 * @see http://github.com/jzahedieh/cakephp-ora-schedules-helper
 *
 * @license
 *   Licensed under The MIT License
 *   Redistributions of files must retain the above copyright notice.'d
 */
class ScheduleHelper extends FormHelper {

	public $helpers = array('Form');

	/**
	* @constructor
	*
	* Takes options
	*/
	public function __construct($options = array()) {
		parent::__construct($options); 
		$this->scheduleGenerator = $this->scheduleGenerator();
	}

	/**
	* Generates array of all individual schedules from db
	*
	* @return array
	*/
	private function getSchedules() {
		$schedule = ClassRegistry::init('Schedule');
		return $schedule->find('list', array('fields' => array('Schedule.day')));
	}

	/**
	* Generates HTML for the schedule tables
	*
	* @param array $options
	* @return array
	*/
	public function scheduleGenerator( $options=array() ) {

		if ( isset($options['type']) ) { //can get called before ready, return if case
			$type = $options['type'];
		} else {
			return;
		}
		if ( isset($options['registration']) ) {
			$registration = $options['registration'];
		}

		$output = '<table><tr>
			<td></td>
			<td>Full Day</td>
			<td>Half Day AM</td>
			<td>Half Day PM</td>
			<td>Drop In</td>';

		if ( $type === 'input' ) {
			$output .= '<td>Additional Notes</td>';
		}

		$output .= '</tr>';

		$i = 0; //start count
		foreach( $this->getSchedules() as $id=>$value ) {
			if( $i%4 === 0) {
				$first_word = explode(' ', trim($value)); //days for first column
				$output .= '<tr><td>'.$first_word[0].'</td>';
			}

			if( $i%4 !== 3 && isset($this->validationErrors['Registration']['Schedules']) ) {
				$output .= '<td class="input text required error">';
			} else if( $i%4 === 3 && isset($this->validationErrors['Registration']['Schedules']) ) {
				if( $this->validationErrors['Registration']['Schedules'][0] ===
					'This field must be completed.' ) {
					$output .= '<td class="input text required error">';
				} else {
					$output .= '<td>';
				}
			} else {
				$output .= '<td>';
			}

			if ( $type === 'input' ) { //generates the indivdual checkboxes
				$output .= $this->Form->checkbox('Schedule.Schedule.'.$id, array('value' => $id));
			} elseif ( $type === 'output' && isset($registration) ) {
				 if ($registration['Schedule']['Schedule'][$id] !== '0') {
					$output .= '&#9745;'; //ticked checkbox
				} else {
					$output .= '&#9744'; //empty checkbox
				}
			}

			if( $i === 3 && $type === 'input' ) {
				$output .= '<td rowspan = "5">'.
					$this->Form->input('AttendanceNote',array(
						'label' => false,
						'rows' => '6'
				)).'</td>';
			}

			if ( $i%4 === 3) {
				$output .= '</tr>';
			}
			$i++;
		}
		$output .= '</table>';

		if ( isset($this->validationErrors['Registration']['Schedules']) ) {
			$output .= '<div class="error-message"><ul>';
				foreach ($this->validationErrors['Registration']['Schedules'] as $message) {
					$output .= '<li>'.h($message).'</li>';
				}
			$output .= '</ul></div>';
		}

		return $this->output($output);
	}
}
