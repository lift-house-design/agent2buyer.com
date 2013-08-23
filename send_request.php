<?php
//conect to database
	include './includes/functions.php';
//make sure session data still exists
if ( (isset($_SESSION['person']['id']) && !empty($_SESSION['person']['id']) ) || isset($_GET['person'])){
//first if checks request id
	if (isset($_GET['request'])) {
		
		$quoteRequest_id = mysql_real_escape_string($_GET['request']);
		
		//define broker for this request
		if(isset($_GET['person']))
			$broker_id=$_GET['person'];
		else
			$broker_id=$_SESSION['person']['id'];
		
		//second if checks to see if we should add a client
		if (isset($_GET['client'])) {
			
			//now check client information is accurate
			if ( (($_POST['client_first_name']) !=="") && (($_POST['client_last_name']) !=="") && (($_POST['client_email']) !=="") ) {
				$client_first_name = mysql_real_escape_string($_POST['client_first_name']);
				$client_last_name = mysql_real_escape_string($_POST['client_last_name']);
				$client_email = mysql_real_escape_string($_POST['client_email']);
				$client_mobile_phone = mysql_real_escape_string($_POST['client_mobile_phone']);
				$client_text_capable=( !empty($_POST['client_text_capable']) ? 1 : 0 );
				
				// Check if client already exists
				$sql='select * from client where email="'.$client_email.'" limit 1';
				$result=mysql_query($sql) or die(mysql_error());
				if($client=mysql_fetch_assoc($result))
				{
					// This client already exists
					
					$client_id=$client['id'];
					$pin=$client['pin'];
					
					$sql='select * from brokersClients where broker='.$broker_id.' and client="'.$client_id.'" limit 1';
					$result=mysql_query($sql) or die(mysql_error());
					
					// Check if client is not already associated with this broker
					if(mysql_num_rows($result)<1)
					{
						$sql='insert into brokersClients (broker, client) values ('.$broker_id.', '.$client_id.')';
						mysql_query($sql) or die(mysql_error());
					}
				}
				else
				{
					// Client does not yet exist
					
					// Create a random PIN for the client
					$pinChars='0123456789';
					$pin='';
					for($i=0;$i<4;$i++)
						$pin.=$pinChars[rand(0,strlen($pinChars)-1)];
					
					//now add client to client table and brokersClients bridge table
					$sql="INSERT into client (first_name, last_name, email, pin, mobile_phone, text_capable) 
						VALUES ('$client_first_name', '$client_last_name', '$client_email', '$pin', '$client_mobile_phone', '$client_text_capable')";
					$query = mysql_query($sql) or die(mysql_error().' >> '.$sql);	
					
					//get the id of this client for bridge table
					$client_id = mysql_insert_id();
	
					$sql="INSERT into brokersClients (broker, client) 
						VALUES ('$broker_id', '$client_id')";
					$query = mysql_query($sql) or die(mysql_error().' >> '.$sql);
				}
				
				//add client to quoteRequest so we can contact them about bids, etc...
				$query = mysql_query("UPDATE quoteRequest set client='$client_id', copy_to_client='1' WHERE id='$quoteRequest_id'");
				
				// Now notify the client about the quote request
				$sql='select * from client where id="'.$client_id.'" limit 1';
				$result=mysql_query($sql) or die(mysql_error());
				$client=mysql_fetch_assoc($result);
				
				$sql='select property.* from quoteRequest, property where quoteRequest.id = '.$quoteRequest_id.' and quoteRequest.property = property.id limit 1';
				$result=mysql_query($sql) or die(mysql_error());
				$property=mysql_fetch_assoc($result);
				
				$sql='select * from person where id='.$broker_id.' limit 1';
				$result=mysql_query($sql) or die(mysql_error());
				$broker=mysql_fetch_assoc($result);
				
				$to=$client['email'];
				$subject='Inspection Quote Requested for '.$property['street'].', '.$property['city'].', '.$property['state'].' '.$property['zip'];
				$message='Hello '.$client['first_name'].',
				
Your broker, '.$broker['first_name'].' '.$broker['last_name'].', has requested home inspection quotes for the property located at: '.$property['street'].', '.$property['city'].', '.$property['state'].' '.$property['zip'].'. We will notify you when inspection quotes are received.';

				$headers =	'From: '.config('site.contact_email'). "\r\n" .
		    				'Reply-To: '.config('site.automail_reply') . "\r\n" .
							'X-Mailer: PHP/' . phpversion();
				
				mail($to, $subject, $message, $headers);
				
				// And text them if they are text capable
				if($client['text_capable']==1)
				{
					send_sms($client['mobile_phone'],$message);
				}
			}
			//error handling for bad client info
			else {
				$msg= '<form action="./send_request.php?request='.$quoteRequest_id.'&&client=true" method="POST">
				 	<div style="text-align:center;">
							<label style="color: #60BDB8;">Some Client Information was missing. Please enter their contact info so we can send them inspector responses.</label>
						</div>
						</br>
						<input class="new_request" type="text" name="client_first_name" placeholder="First Name" />
							</br>
						<input class="new_request" type="text" name="client_last_name" placeholder="Last Name" />
							</br>
						<input class="new_request" type="text" name="client_email" placeholder="Email Address" />
							</br>
						<input style="margin-left:300px;"class="request_confirm_button2" type="submit" value="Add Client" />
				</form>
				</br>
				</br>
				<hr />
				</br>
		 		<div style="text-align:center;">
					<label style="color: #60BDB8;">Or <a href="./send_request.php?request='.$quoteRequest_id.'">click here</a> to opt out of sending them any quote information.</label>
				</div>';
			}
			
		}
		
//now get info and run scripts to send request if error $msg has not been thrown
	if (!isset($msg)) {
		
		//get date and property info
		$query1=mysql_query("SELECT * from quoteRequest, property WHERE quoteRequest.id='$quoteRequest_id' AND property.id=quoteRequest.property");
			while ($row1 = mysql_fetch_assoc($query1)) {
				$date=$row1['date'];
				$street=$row1['street'];
				$city=$row1['city'];
				$state=$row1['state'];
				$zip=$row1['zip'];
				$property_id=$row1['id'];
				
			
				
	
		//get broker's "Go-To" inspectors
		$query2=mysql_query("SELECT * from brokersInspectors WHERE broker='$broker_id' AND relationship='1'");
		$inspectorsWithRequests=0;
			while ($row2 = mysql_fetch_assoc($query2)) {
				$inspector_id=$row2['inspector'];
				
				//make sure these inspectors do not currently have inspection requests pending (status is 0 or 1), else send different msg
					$sql1=mysql_query("SELECT * from inspectorsQuoteRequests WHERE inspector='$inspector_id' AND status>=0 AND status<2");
					
					//now get these inspectors' information
					$query4=mysql_query("SELECT * from person WHERE id='$inspector_id'");
					$row4 = mysql_fetch_assoc($query4);
					$inspector_first_name=$row4['first_name'];
					$inspector_last_name=$row4['last_name'];
					$inspector_mobile_phone=$row4['mobile_phone'];
					$inspector_text_capable=$row4['text_capable'];
					$inspector_email=$row4['email'];
					$inspector_zip=$row4['postal_code'];
					
					$doublecheck = mysql_num_rows($sql1);
					if($doublecheck === 0){ 
						//Add all these inspectors with $quoteRequest_id to the inspectorsQuoteRequest bridge table
						mysql_query("INSERT into inspectorsQuoteRequests (inspector, quoteRequest, last_updated) 
								VALUES ('$inspector_id', '$quoteRequest_id', NOW())");
						$smsBody="You have an inspection opportunity at Address: ".$street." ".$city.", ".$state.", ".$zip.". Can you complete this inspection by ".$date."? Respond Yes or No.";
						$link=config('site.domain')."/property.php?email=true&id=".$property_id;
						$link2=config('site.domain')."/dismiss_request.php?id=".$inspector_id;
						$emailBody='Hello '.$inspector_first_name.',
									
You have an inspection opportunity for Address: 
'.$street.' '.$city.', '.$state.' '.$zip.'.
							
This inspection needs to be completed by:
'.$date.'.
							
To submit a quote and view property information go to: 
'.$link.'

Or, to dismiss this request, visit: 
'.$link2.'

Please note: You will not be able to receive any future inspection requests until you have quoted, or dismissed this request.

Thank you,						
Locizzle, Inc.';
						
					} //end check to make sure there aren't any pending requests out for specific inspector
					else{
						//Add all these inspectors with $quoteRequest_id to the inspectorsQuoteRequest bridge table
						mysql_query("INSERT into inspectorsQuoteRequests (inspector, quoteRequest, last_updated) 
								VALUES ('$inspector_id', '$quoteRequest_id', NOW())");
						$inspectorsWithRequests++;
						$smsBody= $inspector_first_name.', you have been requested for a new inspection quote at Locizzle.com. This inspection request will not be sent to you until you complete your pending request by submitting a quote, or dismissing it. Thanks.';
						$emailBody='Hello '.$inspector_first_name.',
									
You have been requested for a new inspection quote. This inspection request will not be sent to you until you complete your pending request by submitting a quote, or dismissing it. Thanks.
							
Locizzle.com, Inc.'; 
					}
					
					//here we check for text capable, ELSE EMAIL
					if ($inspector_text_capable =='1') {				
						//now Send SMS to inspector 
						require_once('./Services/Twilio.php');
						$account_sid = "AC295178e1f333781132528cd16d55e49b"; // Twilio account sid
						$auth_token = "81905b30336cc2fb674adf13e3f17fb2"; // Twilio auth token
						
						$client = new Services_Twilio($account_sid, $auth_token);
						$message = $client->account->sms_messages->create(
						  config('twilio.number'), // From a Twilio number in your account
						  $inspector_mobile_phone, // Text any number
						  $smsBody
						);
					}
						
					//email inspection request to inspector
					$to      = $inspector_email;
					$subject = 'Inspection Opportunity | Locizzle.com';
					$message = $emailBody;
					$headers = 'From: '.config('site.contact_email'). "\r\n" .
		    			'Reply-To: '.config('site.automail_reply') . "\r\n" .
					    'X-Mailer: PHP/' . phpversion();
					
					mail($to, $subject, $message, $headers);
								
					$available_inspectors .= $inspector_first_name.' '.$inspector_last_name.'</br>';
				}//end while loop that grabs all brokersInspectors
			}//end outer while loop
		}//end check that error has not been thrown
	}
	
	else {
		$msg= 'Uh oh, we have experienced an error. It appears some of the URL has been lost. Please contact us at Mike@locizzle.com or try again.</br>
		<a href="./dashboard.php">Back to Dashboard &#187;</a>';
	}
}
else {
	$msg= 'Uh oh, we have experienced an error. It appears some your session has expired. Please try again, or contact us at Mike@locizzle.com if the problem continues.</br>
		<a href="./index.php">Back to Home &#187;</a>';
}
?>
<?php
	include './includes/header_dashboard.php';
?>
	<div class="row" style="padding:40px; font-size:20px;" align="center">
		<?php 
			if (isset($msg)) {
				echo $msg; 
			}
			else {
				if(isset($available_inspectors)) {
					echo 'Inspection request sent to:</br></br>
					'.$available_inspectors.'</br>
					</br>
					</br>
					<a href="./dashboard.php">Return to Dashboard &#187;</a>';	
				}
			}		 
		?>
	</div>
<?php
	include './includes/footer.php';
?>