Code sample of Helper that generates the schedule tables at payments.hopesanddreams.co.uk

Outputs a schedule input or out for 4 options with error messages

Usage:

	echo $this->Schedule->scheduleGenerator(array('type' => 'input'));
	echo $this->Schedule->scheduleGenerator(array('type' => 'output', 'registration' => $registration));
