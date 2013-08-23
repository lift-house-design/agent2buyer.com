<?php
    // start the session
    include ('./includes/functions.php');
	
	//Get phone number
	$mobile_phone =  $_REQUEST['From'];
	
	//format number from twilio +12102740566 to database (210) 274-0566
	if(  preg_match( '/^\+\d(\d{3})(\d{3})(\d{4})$/', $mobile_phone,  $matches ) )
		{
	   		$mobile_phone = '('.$matches[1].') '.$matches[2].'-'.$matches[3];
		}

	//search inspectors table for this number and get all info
	$sql = mysql_query("SELECT * FROM person WHERE mobile_phone='$mobile_phone'");
		$doublecheck = mysql_num_rows($sql); 	
		if($doublecheck === 1){ 
			$inspector = array(); 
				while($row = mysql_fetch_assoc($sql)) { 
				   $inspector[] = $row; 
				} 
		}

	//search inspectorsQuoteRequests table for this inspector and get quoteRequest ID
	$sql = mysql_query("SELECT * FROM inspectorsQuoteRequests WHERE status>='0' AND status<'2' AND inspector=".$inspector[0]['id']." ORDER BY id LIMIT 1");
		$doublecheck = mysql_num_rows($sql); 	
		if($doublecheck === 1){ 
				while($row = mysql_fetch_assoc($sql)) { 
				   $quoteRequest_id = $row['quoteRequest']; 
				   $status = $row['status']; 
				} 
		}
		else {
			//echo we did not find you in this database
			header('Content-type: text/xml');
				echo '<Response>';
				echo '<Sms>Sorry. We did not find any quote request for you. If you continue to experience this difficulty, please contact us at '.config('site.automail_reply').'</Sms>';
				echo '</Response>';
		}
	
	//search quoteRequest table for this inspection and get all info
	$sql = mysql_query("SELECT * FROM quoteRequest WHERE id=".$quoteRequest_id);
		$doublecheck = mysql_num_rows($sql); 	
		if($doublecheck == 1){ 
			$quoteRequest = array(); 
				while($row = mysql_fetch_assoc($sql)) { 
				   $quoteRequest[] = $row; 
				} 
		}
		
	//Find out what the user said in their text
	$body =  $_REQUEST['Body'];
 	
	//begin switch to determine how to interpret response
	switch ($status)
	{
		case 0:
			//run code for interpreting yes || no. note: (capitals, formating etc...)
			//afterward, send text to ask about price	
			
				//strip capitals from body
				$body = strtolower($body);
				
			
				header('Content-type: text/xml');
				echo '<Response>';
				
				// If we've got good data, save the response
				if (($body == "yes") || ($body == "yep") || ($body == "yeah") || ($body == "y") )
					{
					//add response to database 
					$response = $body;
					
					//change inspectorsQuoteRequests status from 0 to 1.
					$sql = mysql_query ("UPDATE inspectorsQuoteRequests SET status='1', last_updated=NOW() WHERE inspector=".$inspector[0]['id']." AND quoteRequest='$quoteRequest_id'");
					
					// Send SMS back asking for price.
					echo '<Sms>Thanks! What is your price quote for this inspection? Property: ( '.config('site.domain').'/property.php?id='.$quoteRequest[0]['property'].' )</Sms>';
					echo '</Response>';
					}
					
				elseif ( ($body == "no") || ($body == "nope") || ($body == "n") ) 
					{
					//interpret as no
					$response = $body;
					
					//change inspectorsQuoteRequests status from 0 to no.
					$sql = mysql_query ("UPDATE inspectorsQuoteRequests SET status='-1', last_updated=NOW() WHERE inspector=".$inspector[0]['id']." AND quoteRequest='$quoteRequest_id'");
					
					//check if they have another request pending, and tack that new request into this message if they do.
					$sql1=mysql_query("SELECT * from inspectorsQuoteRequests WHERE inspector=".$inspector[0]['id']." AND status>='0' AND status<'2' ORDER BY id LIMIT 1");
					$numrow = mysql_num_rows($sql1); 	
						if($numrow === 0){ 
							// Send SMS back saying thanks anyway.
							echo '<Sms> Thanks, '.$inspector[0]['first_name'].'! You said "'.$response.'." Hopefully your next inspection oppurtunity will work out better!</Sms>';
							echo '</Response>';
						
						}
						else {
								//close xml tag
								echo '</Response>';	
								//get info about next inspection request, and send "thanks anyway" you also have a new inspection request
								$row1 = mysql_fetch_assoc($sql1);
								$new_quoteRequest_id=$row1['quoteRequest'];
								
								$sql2=mysql_query("SELECT * from quoteRequest WHERE id='$new_quoteRequest_id'");
								$row2 = mysql_fetch_assoc($sql2);
								$new_property_id=$row2['property'];
								$new_date=$row2['date'];
								
								$sql3=mysql_query("SELECT * from property WHERE id='$new_property_id'");
								$row3 = mysql_fetch_assoc($sql3);
								$new_street=$row3['street'];
								$new_city=$row3['city'];
								$new_state=$row3['state'];
								$new_zip=$row3['zip'];
								
								$smsBody='Thanks, '.$inspector[0]['first_name'].'! You said "'.$response.'." Hopefully the next oppurtunity will work out better! You have a new inspection oppurtunity at Address: '.$new_street.' '.$new_city.', '.$new_state.', '.$new_zip.'. Can you complete this inspection by '.$new_date.'? Respond Yes or No.';
								
								require_once('./Services/Twilio.php');
									$account_sid = "AC295178e1f333781132528cd16d55e49b"; // Twilio account sid
									$auth_token = "81905b30336cc2fb674adf13e3f17fb2"; // Twilio auth token
									
									$client = new Services_Twilio($account_sid, $auth_token);
									$message = $client->account->sms_messages->create(
									  '+15128618405', // From a Twilio number in your account
									  $mobile_phone, // Text any number
									  $smsBody
									);
					}	
				}
					
				else {
					
					$response = $body;
			
					// Send SMS back for clarification.
					echo '<Sms> Thanks, '.$inspector[0]['first_name'].'! You said "'.$response.'." Please answer using yes or no.</Sms>';
					echo '</Response>';
				}		
		break; 
		
		case 1:
			//this is their response to our price quote question
				header('Content-type: text/xml');
				echo '<Response>';
				
				// If we've got good data, add to db
				if ( (isset($body)) )
				{
				$body=trim($body);
				$response = $body;
				$response = preg_replace("/[^0-9]/","",$response);
					if (!empty($response)) {
						//update inspectorsQuoteRequests status from 1 to 2, and add pricing info
						$sql = mysql_query("UPDATE inspectorsQuoteRequests SET status='2', last_updated=NOW() WHERE status='1' AND inspector=".$inspector[0]['id']);
						
						//create instance in bid table
						$sql = mysql_query("INSERT into bid (quoteRequest, inspector, bid_amount, status) VALUES ('$quoteRequest_id', ".$inspector[0]['id'].", '$response', 'open')");
						//
						//check if they have another request pending, and tack that new request into this message if they do.
						$sql1=mysql_query("SELECT * from inspectorsQuoteRequests WHERE inspector=".$inspector[0]['id']." AND status>='0' AND status<'2' ORDER BY id LIMIT 1");
						$numrow = mysql_num_rows($sql1); 	
							if($numrow === 0){ 
								//send basic "thanks" SMS response.
								echo '<Sms> Thanks, '.$inspector[0]['first_name'].'! You\'ve said you can do this inspection for "$'.$response.'" We will be in touch very soon with more information.</Sms>';
								echo '</Response>';		
							}
							else {
								//close xml tag
								echo '</Response>';	
								//get info about pending request, and send "thanks" plus info about new request
								$row1 = mysql_fetch_assoc($sql1);
								$new_quoteRequest_id=$row1['quoteRequest'];
								
								$sql2=mysql_query("SELECT * from quoteRequest WHERE id='$new_quoteRequest_id'");
								$row2 = mysql_fetch_assoc($sql2);
								$new_property_id=$row2['property'];
								$new_date=$row2['date'];
								
								$sql3=mysql_query("SELECT * from property WHERE id='$new_property_id'");
								$row3 = mysql_fetch_assoc($sql3);
								$new_street=$row3['street'];
								$new_city=$row3['city'];
								$new_state=$row3['state'];
								$new_zip=$row3['zip'];
								
								$smsBody='Thanks, '.$inspector[0]['first_name'].'! You\'ve said you can do this inspection for $'.$response.'. We will be in touch very soon with more information about this request. You also have a new inspection oppurtunity at Address: '.$new_street.' '.$new_city.', '.$new_state.', '.$new_zip.'. Can you complete this inspection by '.$new_date.'? Respond Yes or No.';
								
								require_once('./Services/Twilio.php');
									$account_sid = "AC295178e1f333781132528cd16d55e49b"; // Twilio account sid
									$auth_token = "81905b30336cc2fb674adf13e3f17fb2"; // Twilio auth token
									
									$client = new Services_Twilio($account_sid, $auth_token);
									$message = $client->account->sms_messages->create(
									  '+15128618405', // From a Twilio number in your account
									  $mobile_phone, // Text any number
									  $smsBody
									);
							}
							//send sms/or email to broker (and client if selected) to tell them about the quote
							$broker_id=$quoteRequest[0]['person'];
							$client_id=$quoteRequest[0]['client'];
							$copy_to_client=$quoteRequest[0]['copy_to_client'];
							
							$sql=mysql_query("SELECT * FROM person WHERE id='$broker_id'");
							while($row = mysql_fetch_assoc($sql)) { 
								$broker_text_capable = $row['text_capable']; 
							    $broker_mobile_phone = $row['mobile_phone']; 
								$broker_email = $row['email']; 
								$broker_first_name = $row['first_name']; 
								
								$smsBody= $broker_first_name.', a quote has been recieved for one of your inspections. You can view and/or accept this quote on your dashboard at Locizzle.com.';
								$emailBody='Hello '.$broker_first_name.',
									
A quote has been recieved for one of your inspections. You can view and/or accept this quote on your dashboard at http://www.locizzle.com.
					
Locizzle.com, Inc.';
							} 

						//here we check for if broker is text capable, ELSE EMAIL
						if ($broker_text_capable =='1') {				
							//now Send SMS to inspector 
							require_once('./Services/Twilio.php');
							$account_sid = "AC295178e1f333781132528cd16d55e49b"; // Twilio account sid
							$auth_token = "81905b30336cc2fb674adf13e3f17fb2"; // Twilio auth token
							
							$client = new Services_Twilio($account_sid, $auth_token);
							$message = $client->account->sms_messages->create(
							  '+15128618405', // From a Twilio number in your account
							  $broker_mobile_phone, // Text any number
							  $smsBody
							);
						}
						else {
							//email quote otification to broker
							$to      = $broker_email;
							$subject = 'New Quote Available | Locizzle.com';
							$message = $emailBody;
							$headers = 'From: '.config('site.contact_email'). "\r\n" .
			   					'Reply-To: '.config('site.automail_reply') . "\r\n" .
							    'X-Mailer: PHP/' . phpversion();
							
							mail($to, $subject, $message, $headers);
						}
						
						//check if we should also notify client
						if($copy_to_client==1) {
							$sql=mysql_query("SELECT * FROM client WHERE id='$client_id'");
							while($row = mysql_fetch_assoc($sql)) { 
								$client_text_capable = $row['text_capable']; 
							    $client_mobile_phone = $row['mobile_phone']; 
								$client_email = $row['email']; 
								$client_first_name = $row['first_name'];
								$client_pin=$row['pin'];
								
								$link=config('site.domain').'/clients.php?email='.urlencode($client_email);
								
								//define messages
								$smsBody='Hello, '.$client_first_name.', you and your broker have recieved a home inspection quote. You may log in with the PIN: '.$client_pin.' to accept inspection quotes for this property by visiting: '.$link;
							
								$emailBody='Hello '.$client_first_name.',
								
You and your broker have received a home inspection quote. You may log in with the PIN below to accept inspection quotes for this property by visiting: '.$link.'

PIN: '.$client_pin.'
								
Locizzle.com, Inc.';
								//email/text client here
								if ($client_text_capable =='1') {				
									//now Send SMS to inspector 
									require_once('./Services/Twilio.php');
									$account_sid = "AC295178e1f333781132528cd16d55e49b"; // Twilio account sid
									$auth_token = "81905b30336cc2fb674adf13e3f17fb2"; // Twilio auth token
									
									$client = new Services_Twilio($account_sid, $auth_token);
									$message = $client->account->sms_messages->create(
									  '+15128618405', // From a Twilio number in your account
									  $client_mobile_phone, // Text any number
									  $smsBody
									);
								}
								else {
									//email nquote otification to broker
									$to      = $client_email;
									$subject = 'New Quote Available | Locizzle.com';
									$message = $emailBody;
									$headers = 'From: '.config('site.contact_email'). "\r\n" .
			    						'Reply-To: '.config('site.automail_reply') . "\r\n" .
									    'X-Mailer: PHP/' . phpversion();
									
									mail($to, $subject, $message, $headers);
								}
							} 
						}
					}
					// Inspector has decided to cancel or dismiss the bid after saying yes
					elseif(in_array(strtolower($body),array('no','nope','n','dismiss')))
					{
						//interpret as no
						$response = $body;

						//change inspectorsQuoteRequests status from 0 to no.
						$sql = mysql_query ("UPDATE inspectorsQuoteRequests SET status='-1', last_updated=NOW() WHERE inspector=".$inspector[0]['id']." AND quoteRequest='$quoteRequest_id'");

						//check if they have another request pending, and tack that new request into this message if they do.
						$sql1=mysql_query("SELECT * from inspectorsQuoteRequests WHERE inspector=".$inspector[0]['id']." AND status>='0' AND status<'2' ORDER BY id LIMIT 1");
						$numrow = mysql_num_rows($sql1); 	
						if($numrow === 0){ 
							// Send SMS back saying thanks anyway.
							echo '<Sms> Thanks, '.$inspector[0]['first_name'].'! You said "'.$response.'." Hopefully your next inspection oppurtunity will work out better!</Sms>';
							echo '</Response>';
						}
						else {
							//close xml tag
							echo '</Response>';	
							//get info about next inspection request, and send "thanks anyway" you also have a new inspection request
							$row1 = mysql_fetch_assoc($sql1);
							$new_quoteRequest_id=$row1['quoteRequest'];

							$sql2=mysql_query("SELECT * from quoteRequest WHERE id='$new_quoteRequest_id'");
							$row2 = mysql_fetch_assoc($sql2);
							$new_property_id=$row2['property'];
							$new_date=$row2['date'];

							$sql3=mysql_query("SELECT * from property WHERE id='$new_property_id'");
							$row3 = mysql_fetch_assoc($sql3);
							$new_street=$row3['street'];
							$new_city=$row3['city'];
							$new_state=$row3['state'];
							$new_zip=$row3['zip'];

							$smsBody='Thanks, '.$inspector[0]['first_name'].'! You said "'.$response.'." Hopefully the next oppurtunity will work out better! You have a new inspection oppurtunity at Address: '.$new_street.' '.$new_city.', '.$new_state.', '.$new_zip.'. Can you complete this inspection by '.$new_date.'? Respond Yes or No.';

							require_once('./Services/Twilio.php');
							$account_sid = "AC295178e1f333781132528cd16d55e49b"; // Twilio account sid
							$auth_token = "81905b30336cc2fb674adf13e3f17fb2"; // Twilio auth token

							$client = new Services_Twilio($account_sid, $auth_token);
							$message = $client->account->sms_messages->create(
								'+15128618405', // From a Twilio number in your account
								$mobile_phone, // Text any number
								$smsBody
							);
						}	
					}
					else {
						echo '<Sms>You said '.$body.'. Please provide a quote using numerals (ex: $1,000), or dismiss the bid by responding with "dismiss" or "no". Thanks.</Sms>';
						echo '</Response>';
					}
				}
				
		break;
		
		case 2:
			//this is their response after we thank them for their price quote
				header('Content-type: text/xml');
				echo '<Response>';
				
				// If we've got good data, save the vote
				if ( (isset($body)) )
				{
				//add response to database 
				$response = $body;
				$response = preg_replace("/[^0-9]/","",$response);
				}
				// Send SMS back asking for price.
				echo '<Sms> Thanks, '.$inspector[0]['first_name'].'! We have received your information, and will be in touch shortly. For further assistance, please contact us at Mike@locizzle.com.</Sms>';
				echo '</Response>';
		break;  
		
		default:
			header('Content-type: text/xml');
			echo '<Response>';
			
			// Otherwise, give the user an example of how to vote because case doesn't exist
			
			echo '<Sms> hello, '.$inspector[0]['first_name'].'. Sorry, there is a permanent error. Please contact us at Mike@locizzle.com.</Sms>';
			echo '</Response>';
	}
?>