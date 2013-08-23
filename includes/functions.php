<?php
//session start
session_start();

//error_reporting(E_NONE);
ini_set('display_errors','Off');

mysql_connect (config("mysql.host"), config("mysql.user"), config("mysql.pass"));
mysql_select_db (config("mysql.dbname"));

//config helper function
function config($key=null)
{
	$config &= $_GLOBALS['config'];
	
	// Get the config options if they have not been loaded yet
	if(empty($config))
		$config=require('config.php');
		
	// If requesting a specific key, only return that
	if(isset($key) && isset($config[$key]))
		return $config[$key];

	// Otherwise return the whole config array
	return $config;
}

function send_sms($to_phone_number, $message)
{
	require_once(__DIR__.'/../Services/Twilio.php');
	$account_sid = "AC295178e1f333781132528cd16d55e49b"; // Twilio account sid
	$auth_token = "81905b30336cc2fb674adf13e3f17fb2"; // Twilio auth token
	
	$client = new Services_Twilio($account_sid, $auth_token);
	$message = $client->account->sms_messages->create(
	  config('twilio.number'), // From a Twilio number in your account
	  $to_phone_number, // Text any number
	  $message
	);
}

// This is needed in two places, so I've moved it to a function
function accept_bid($inspector_id)
{
	//this block will accept a quote	
	$quote_insp=$inspector_id;
	
	$sql=mysql_query("SELECT * from bid where inspector='$quote_insp' AND status='open'");
	while($row = mysql_fetch_assoc($sql)) {
		$bid_id=$row['id']; 
		$quoteRequest_id=$row['quoteRequest']; 
	}
	//get property id for this inspection
	$query=mysql_query("SELECT * from quoteRequest where id='$quoteRequest_id'");
	while($row1 = mysql_fetch_assoc($query)) {
		$property_id=$row1['property']; 
	}
	//get property details to send to failed inspectors so they know which quote wasn't accepted
	$query2=mysql_query("SELECT * from property where id='$property_id'");
	while($row2 = mysql_fetch_assoc($query2)) {
		$street=$row2['street'];
		$city=$row2['city'];
		$state=$row2['state'];
		$zip=$row2['zip'];
	}
	
	
	
	$sql=mysql_query("UPDATE bid SET status='accepted' where id='$bid_id'");
	$sql=mysql_query("UPDATE bid SET status='closed' WHERE quoteRequest='$quoteRequest_id' AND status='open'");
	$sql=mysql_query("UPDATE inspectorsQuoteRequests SET status='-2', last_updated=NOW() WHERE quoteRequest='$quoteRequest_id'");
	$sql=mysql_query("UPDATE quoteRequest SET status='accepted', person_awarded_to='$quote_insp', award_time=now() WHERE id='$quoteRequest_id'");		
	
	//now message/email inspector saying that they've been accepted, and need to pay.
	$sql=mysql_query("SELECT * FROM person WHERE id='$quote_insp'");
	while($row = mysql_fetch_assoc($sql)) { 
		$insp_text_capable = $row['text_capable']; 
	    $insp_mobile_phone = $row['mobile_phone']; 
		$insp_email = $row['email']; 
		$insp_first_name = $row['first_name']; 

		$emailBody="Hello ".$insp_first_name.",
			
Congratulations, your inspection quote has been accepted! Click here (".config('site.domain')."/billing-info.php?id=".$bid_id.") to pay our $12.50 service fee, and receive the home buyer's detailed contact information.

Locizzle.com, Inc.";
	} 
	
	//here we check for if inspector is text capable
	if ($insp_text_capable ==1) {		
		//now Send SMS to inspector 
		require_once('./Services/Twilio.php');
		$account_sid = "AC295178e1f333781132528cd16d55e49b"; // Twilio account sid
		$auth_token = "81905b30336cc2fb674adf13e3f17fb2"; // Twilio auth token
		
		$client = new Services_Twilio($account_sid, $auth_token);
		$message = $client->account->sms_messages->create(
		  '+15128618405', // From a Twilio number in your account
		  $insp_mobile_phone, // Text any number
		  "Your inspection quote has been accepted! Visit (".config('site.domain')."/billing-info.php?id=$bid_id)"
		);
		
		//ALSO email quote accepted to inspector, even if text capable
		$to      = $insp_email;
		$subject = 'Your Quote Has Been Accepted! Locizzle.com';
		$message = $emailBody;
		$headers = 'From: '.config('site.contact_email'). "\r\n" .
			'Reply-To: '.config('site.automail_reply') . "\r\n" .
		    'X-Mailer: PHP/' . phpversion();
		
		mail($to, $subject, $message, $headers);
	}
	else {
		//email quote accepted to inspector
		$to      = $insp_email;
		$subject = 'Your Quote Has Been Accepted! Locizzle.com';
		$message = $emailBody;
		$headers = 'From: '.config('site.contact_email'). "\r\n" .
			'Reply-To: '.config('site.automail_reply') . "\r\n" .
		    'X-Mailer: PHP/' . phpversion();
		
		mail($to, $subject, $message, $headers);
	}

	//now message inspectors that gave a bid but were not accepted
	$sql3=mysql_query("SELECT * from bid where status='closed' AND quoteRequest='$quoteRequest_id'");
	while($row3 = mysql_fetch_assoc($sql3)) {
		$failed_insp=$row3['inspector']; 
		//now message/email inspector saying that they've been accepted, and need to pay.
		$sql4=mysql_query("SELECT * FROM person WHERE id='$failed_insp'");
		while($row4 = mysql_fetch_assoc($sql4)) { 
			$insp_text_capable = $row4['text_capable']; 
		    $insp_mobile_phone = $row4['mobile_phone']; 
			$insp_email = $row4['email']; 
			$insp_first_name = $row4['first_name']; 
	
			$emailBody="Hello ".$insp_first_name.",
				
	Unfortunately, your inspection quote has not been accepted for ".$street." ".$city.", ".$state.", ".$zip.". Keep a look out for your next opportunity from Locizzle.com!
	
	Locizzle.com, Inc.";
		} 
		
		//here we check for if inspector is text capable
		if ($insp_text_capable ==1) {		
			//now Send SMS to inspector 
			require_once('./Services/Twilio.php');
			$account_sid = "AC295178e1f333781132528cd16d55e49b"; // Twilio account sid
			$auth_token = "81905b30336cc2fb674adf13e3f17fb2"; // Twilio auth token
			
			$client = new Services_Twilio($account_sid, $auth_token);
			$message = $client->account->sms_messages->create(
			  '+15128618405', // From a Twilio number in your account
			  $insp_mobile_phone, // Text any number
			  "Unfortunately, your inspection quote has not been accepted for ".$street." ".$city.", ".$state.", ".$zip.". Keep a look out for your next opportunity from Locizzle.com!"
			);
			
			//ALSO email quote accepted to inspector, even if text capable
			$to      = $insp_email;
			$subject = 'Your Quote Has Been Accepted! | Locizzle.com';
			$message = $emailBody;
			$headers = 'From: '.config('site.contact_email'). "\r\n" .
				'Reply-To: '.config('site.automail_reply') . "\r\n" .
			    'X-Mailer: PHP/' . phpversion();
			
			mail($to, $subject, $message, $headers);
		}
		else {
			//email quote accepted to inspector
			$to      = $insp_email;
			$subject = 'Your Quote Has Not Been Accepted! Locizzle.com';
			$message = $emailBody;
			$headers = 'From: '.config('site.contact_email'). "\r\n" .
				'Reply-To: '.config('site.automail_reply') . "\r\n" .
			    'X-Mailer: PHP/' . phpversion();
			
			mail($to, $subject, $message, $headers);
		}		 
	}
}
	
//Set states array
$state_list = array('AL'=>"Alabama",
        'AK'=>"Alaska",  
        'AZ'=>"Arizona",  
        'AR'=>"Arkansas",  
        'CA'=>"California",  
        'CO'=>"Colorado",  
        'CT'=>"Connecticut",  
        'DE'=>"Delaware",  
        'DC'=>"District Of Columbia",  
        'FL'=>"Florida",  
        'GA'=>"Georgia",  
        'HI'=>"Hawaii",  
        'ID'=>"Idaho",  
        'IL'=>"Illinois",  
        'IN'=>"Indiana",  
        'IA'=>"Iowa",  
        'KS'=>"Kansas",  
        'KY'=>"Kentucky",  
        'LA'=>"Louisiana",  
        'ME'=>"Maine",  
        'MD'=>"Maryland",  
        'MA'=>"Massachusetts",  
        'MI'=>"Michigan",  
        'MN'=>"Minnesota",  
        'MS'=>"Mississippi",  
        'MO'=>"Missouri",  
        'MT'=>"Montana",
        'NE'=>"Nebraska",
        'NV'=>"Nevada",
        'NH'=>"New Hampshire",
        'NJ'=>"New Jersey",
        'NM'=>"New Mexico",
        'NY'=>"New York",
        'NC'=>"North Carolina",
        'ND'=>"North Dakota",
        'OH'=>"Ohio",  
        'OK'=>"Oklahoma",  
        'OR'=>"Oregon",  
        'PA'=>"Pennsylvania",  
        'RI'=>"Rhode Island",  
        'SC'=>"South Carolina",  
        'SD'=>"South Dakota",
        'TN'=>"Tennessee",  
        'TX'=>"Texas",  
        'UT'=>"Utah",  
        'VT'=>"Vermont",  
        'VA'=>"Virginia",  
        'WA'=>"Washington",  
        'WV'=>"West Virginia",  
        'WI'=>"Wisconsin",  
        'WY'=>"Wyoming");

?>