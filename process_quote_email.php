<?php
if(!empty($_POST)){
	if(isset($_POST['insp_email'])) {
		
		include './includes/functions.php';
		//$insp_first_name = ucwords($_POST['insp_first_name']);
		//$insp_last_name = ucwords($_POST['insp_last_name']);
		$insp_email = (mysql_real_escape_string($_POST['insp_email']));
		$insp_bid_amount = (mysql_real_escape_string($_POST['insp_bid_amount']));
		$quoteRequest_id = (mysql_real_escape_string($_POST['insp_quoteRequest_id']));
		
		//find this inspectors ID
		$sql=mysql_query("SELECT * from person where email='$insp_email'");
			while ($row = mysql_fetch_assoc($sql)) {
				$inspector_id=$row['id'];
				$insp_first_name=$row['first_name'];
			}
		if (!empty($insp_first_name)) {
			//insert into bid table
			$sql=mysql_query("INSERT into bid (quoteRequest, inspector, bid_amount, status) VALUES ('$quoteRequest_id', '$inspector_id', '$insp_bid_amount', 'open')");
			
			//update this InspectorsQuoteRequests record to change status to a '2', meaning they can get another if they have a pre-prending request
			$sql = mysql_query("UPDATE inspectorsQuoteRequests SET status='2', last_updated=NOW() WHERE quoteRequest='$quoteRequest_id' AND inspector='$inspector_id'");
							
			//check if they have another (pre)pending request, and email it to them if they do.
			$sql1=mysql_query("SELECT * from inspectorsQuoteRequests WHERE inspector='$inspector_id' AND status>='0' AND status<'2' ORDER BY id LIMIT 1");
				$numrow = mysql_num_rows($sql1); 	
					if($numrow === 0){ 
						$msg= 'Thank you '.$insp_first_name.'. Your quote has been submitted, and you will receive an email once a quote has been accepted for this inspection request.';
					}
					else {
						$msg= 'Thank you '.$insp_first_name.'. Your quote has been submitted, and you will receive an email once a quote has been accepted for this inspection request.</br></br>Also, we have emailed you a new inspection opportunity!';
	
					//now get information for their new request, and email it to them.
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
					
					//email inspector
					$new_link=config('site.domain')."/property.php?id=".$new_property_id;
					$new_link2=config('site.domain')."/dismiss_request.php?id=".$inspector_id;
					$new_emailBody='Hello '.$insp_first_name.',
										
You have an inspection opportunity for Address: 
'.$new_street.' '.$new_city.', '.$new_state.' '.$new_zip.'.
							
This inspection needs to be completed by:
'.$new_date.'.
							
To submit a quote and view property information go to: 
'.$new_link.'

Or, to dismiss this request, visit: 
'.$new_link2.'

Please note: You will not be able to receive any future inspection requests until you have quoted, or dismissed this request.

Thank you,						
Locizzle, Inc.';
	
					//email inspection request to inspector
					$to      = $insp_email;
					$subject = 'Inspection Opportunity | Locizzle.com';
					$message = $new_emailBody;
					$headers = 'From: '.config('site.contact_email'). "\r\n" .
		    			'Reply-To: '.config('site.automail_reply') . "\r\n" .
					    'X-Mailer: PHP/' . phpversion();
					
					mail($to, $subject, $message, $headers);
					
					}
			
			//send sms/or email to broker (and client if selected) to tell them about the quote
			$query=mysql_query("SELECT * from quoteRequest where id='$quoteRequest_id'");
			while($row2 = mysql_fetch_assoc($query)) { 
				$broker_id=$row2['person'];
				$client_id=$row2['client'];
				$copy_to_client=$row2['copy_to_client'];
			}
			
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

				}
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
					//email quote notification to broker
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
		else {
		$msg="Sorry, this email address does not exist for an inspector. Please try again, and be sure to use the email address where you received this inspection opportunity.<br /><br /><A HREF='javascript:history.back()'>&#171; Back</A>";
	}
	}
	
}
?>
<?php include './includes/header_dashboard.php'; ?>
<div class="container">
	<div class="row" style="padding:40px; font-size:20px;" align="center">
		<?php echo $msg; ?>
	</div>
</div>
<?php include './includes/footer.php'; ?>