<?php

	$body='Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed scelerisque, risus eget rutrum scelerisque, lorem mi aliquet orci, vel tempus quam dolor viverra lorem. Maecenas ut dolor elit. Sed non justo at urna pharetra blandit. Vestibulum odio lorem, porttitor quis suscipit ut, elementum at lectus. Proin suscipit odio urna.';
	$body='Thanks, Nicholas! You\'ve said you can do this inspection for $500. We will be in touch very soon with more information about this request.
You also have a new inspection oppurtunity at Address: 12118 Walnut Park Xing, Austin, TX 78753. Can you complete this inspection by 04/20/2013? Respond Yes or No.';
	//now Send SMS to inspector 
	require_once('./Services/Twilio.php');
	$account_sid = "AC295178e1f333781132528cd16d55e49b"; // Twilio account sid
	$auth_token = "81905b30336cc2fb674adf13e3f17fb2"; // Twilio auth token
	
	$client = new Services_Twilio($account_sid, $auth_token);
	
	var_dump(
		str_split($body,160),
		$client->account->sms_messages->_splitBody($body)
	);
	exit;
	
	$client->account->sms_messages->create(
		config('twilio.number'), // From a Twilio number in your account
		'3048716066', // Text any number
		$body
	);