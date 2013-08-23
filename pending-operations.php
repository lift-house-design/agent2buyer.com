<?php
	include './includes/functions.php';
	
	if (isset($_GET['resend'])) {
		$quoteRequest_id=mysql_real_escape_string($_GET['resend']);
		//resend message to available inspectors
		//get date and property info
		$query1=mysql_query("SELECT * from quoteRequest, property WHERE quoteRequest.id='$quoteRequest_id' AND property.id=quoteRequest.property");
			while ($row1 = mysql_fetch_assoc($query1)) {
				$date=$row1['date'];
				$street=$row1['street'];
				$city=$row1['city'];
				$state=$row1['state'];
				$zip=$row1['zip'];
				$property_id=$row1['id'];
				
		//get broker's "Go-To" inspectors for this inspection request
		$query2=mysql_query("SELECT * from inspectorsQuoteRequests WHERE quoteRequest='$quoteRequest_id'");
			while ($row2 = mysql_fetch_assoc($query2)) {
				$inspector_id=$row2['inspector'];

					//now get these inspectors' information
					$query4=mysql_query("SELECT * from person WHERE id='$inspector_id'");
					$row4 = mysql_fetch_assoc($query4);
					$inspector_first_name=$row4['first_name'];
					$inspector_last_name=$row4['last_name'];
					$inspector_mobile_phone=$row4['mobile_phone'];
					$inspector_text_capable=$row4['text_capable'];
					$inspector_email=$row4['email'];
					$inspector_zip=$row4['postal_code'];

					$smsBody="Reminder: You have an inspection oppurtunity at Address: ".$street." ".$city.", ".$state.", ".$zip.". Can you complete this inspection by ".$date."? Respond Yes or No.";
					$link=config('site.domain')."/property.php?id=".$property_id;
					$link2=config('site.domain')."/dismiss_request.php?id=".$inspector_id;
					$emailBody='Hello '.$inspector_first_name.',
									
This is a reminder that you have an inspection oppurtunity for 
							
Address: '.$street.' '.$city.', '.$state.' '.$zip.'.
							
This inspection needs to be completed by '.$date.'.
							
Submit a quote and view property information: '.$link.'

Or dismiss this request: '.$link2.'
							
Locizzle.com, Inc.
							
PS: You can not receive any future oppurtunities until you have accepted or dismissed this request.';
						
					//make sure they haven't already quoted this request, if not then send
					$query3=mysql_query("SELECT * FROM bid WHERE quoteRequest='$quoteRequest_id' AND inspector='$inspector_id'");
					$doublecheck3 = mysql_num_rows($query3);
					//Also, make sure they didn't already turn this request down by saying no or dismissing
					$query4=mysql_query("SELECT * FROM inspectorsQuoteRequests WHERE quoteRequest='$quoteRequest_id' AND inspector='$inspector_id' AND status='-1'");
					$doublecheck4 = mysql_num_rows($query4);
					if(($doublecheck3 === 0) && ($doublecheck4 === 0)){ 
						//here we check for text capable, ELSE EMAIL
						if ($inspector_text_capable =='1') {				
							//now Send SMS to inspector 
							require_once('./Services/Twilio.php');
							$account_sid = "AC295178e1f333781132528cd16d55e49b"; // Twilio account sid
							$auth_token = "81905b30336cc2fb674adf13e3f17fb2"; // Twilio auth token
							
							$client = new Services_Twilio($account_sid, $auth_token);
							$message = $client->account->sms_messages->create(
							  '+15128618405', // From a Twilio number in your account
							  $inspector_mobile_phone, // Text any number
							  $smsBody
							);
						}
						else {
							
							//email inspection request to inspector
							$to      = $inspector_email;
							$subject = 'Inspection Opportunity | Locizzle.com';
							$message = $emailBody;
							$headers = 'From: '.config('site.contact_email'). "\r\n" .
				    			'Reply-To: '.config('site.automail_reply') . "\r\n" .
							    'X-Mailer: PHP/' . phpversion();
							
							mail($to, $subject, $message, $headers);
						}	
						$available_inspectors .= $inspector_first_name.' '.$inspector_last_name.'</br>';			
					}
				}//end while loop that grabs all brokersInspectors
			}//end outer while loop
			//make sure someone was actually available that had not already bid
			if(isset($available_inspectors)){
				$msg='Thank you. Inspection reminders have been sent to: </br>'.$available_inspectors.'</br></br><a href="./dashboard.php">&#171; Dashboard</a>';
			}
			else {
				$msg='Sorry. All of your inspectors have already placed quotes for this inspection, or are not available.</br></br><a href="./dashboard.php">&#171; Dashboard</a>';
			}
		}//end if(isset RESEND)
	
	elseif (isset($_GET['delete'])){
		$quoteRequest_id=mysql_real_escape_string($_GET['delete']);
		//delete inspection request, and free up go-to inspectors
			$query=mysql_query("SELECT * FROM quoteRequest WHERE id='$quoteRequest_id'");
			while ($row = mysql_fetch_assoc($query)) {
				$property_id=$row['property'];
			}
			//delete bids
			$sql=mysql_query("DELETE from bid WHERE quoteRequest='$quoteRequest_id'");
			//delete properties
			$sql1=mysql_query("DELETE from property WHERE id='$property_id'");
			//delete inspectorsQuoteRequests
			$sql2=mysql_query("DELETE from inspectorsQuoteRequests WHERE quoteRequest='$quoteRequest_id'");
			//delete quoteRequest
			$sql3=mysql_query("DELETE from quoteRequest WHERE id='$quoteRequest_id'");
			$msg='You have successfully deleted this Quote Request!</br></br><a href="./dashboard.php">&#171; Dashboard</a>';
		
	}
	
	else {
		$msg='There has been a critical error. Please try again. Contact us at email, if this problem continues.</br></br><a href="./dashboard.php">Dashboard</a>';
	}
?>
<?php include './includes/header_dashboard.php'; ?>
<div class="container">
	<div class="row" align="center" style="color: #3C2111; font-size:25px;padding:20px;">
		<hr />
			</br>
			<?php
				echo $msg;
			?>
			</br></br>
		<hr />
	</div>
</div>
<?php include './includes/footer.php'; ?>