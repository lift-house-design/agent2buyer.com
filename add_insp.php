<?php $dashboard=""; ?>
<?php $accordion=""; ?>
<?php include './includes/functions.php'; ?>
<?php
/*This block will add pre-existing inspector to brokers list once +add button is pressed
	if (isset($_POST['inspIdValue'])) {
		
		$insp_id=$_POST['inspIdValue'];
		$broker_id=$_SESSION['person']['id'];
		
		//make sure inspector/broker relationship doesn't already exist by email
		$sql=mysql_query("SELECT * from brokersInspectors where broker='$broker_id' AND inspector='$insp_id'");
			$doublecheck = mysql_num_rows($sql); 	
			if($doublecheck > 0){ 
			echo "You have already added this inspector.";
			exit;
			}
			
		//make sure user doesn't already exist by phone number
			$sql2 = mysql_query ("SELECT * FROM person where mobile_phone='$mobile_phone'");
		    $doublecheck2 = mysql_num_rows($sql2); 

	    	if($doublecheck2 > 0){ 
				echo '<span style="color: #F2713C;">&nbsp;&nbsp;Sorry, '.$first_name.'. This mobile phone number already exists. </span>';
				exit;
			}
		//add insp_id to insp/broker bridge table
		//echo $insp_id;
		$query = mysql_query("INSERT into brokersInspectors (
		broker, inspector) 
		VALUES ('$broker_id', '$insp_id')");		
	}*/
?>
<?php
//zipcode range class
include('zipcode.php');

if (isset($_POST['radiusValue'])) {

	if (isset($_POST['brokerZipCodeValue'])) {
		//set zip to whatever the user has intered
		$broker_zipcode = $_POST['brokerZipCodeValue'];		
		
	}
	
	else {
		//if no entry, use the session zipcode for the broker
		$broker_zipcode=$_SESSION['person']['postal_code'];
	}
	
	//define radius value
	$radius=$_POST['radiusValue'];
	
	// we instantiate ZipCode with a zip code
	$returned_zipcode = new ZipCode($broker_zipcode);
	
	//and/or by city state -- this is not used now, just an example of how it could be in future.
	//$ventura = new ZipCode("Ventura, CA");
	
	//FIRST get all inspectors in brokers zipcode
		$sql=mysql_query("select * from user, person where person.postal_code='$broker_zipcode' AND person.user=user.id AND user.role='inspector'");
		
		//now define variables and echo out results into an array ideally.
				while ($row = mysql_fetch_assoc($sql)) {
					
					$first_name=$row['first_name'];
					$last_name=$row['last_name'];
					$role=$row['role'];
					$insp_id=$row['id'];
					$same_zipcode_inspector_list .= '<h2 class="odd add_insp">'.$first_name.' '.$last_name.'<span class="add_insp_button"><input type="hidden" name="insp_id" value="'.$insp_id.'" /><input class="add_insp_button" type="submit" value="+ Add" name="add" /></span></h2>
											<div class="sub-accordion">
												<div>
												Profile</br>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus pulvinar feugiat porttitor. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut lorem lorem. Aliquam in felis urna, quis sollicitudin mi. Donec malesuada imperdiet augue vel ultrices. 
												</div>
											</div>';
				
				}
/*
You can get all of the zip codes within a distance range from the given_zip. Here we
are doing all zip codes between 0 and X miles. The returned array contains the
distance as the array's key and the array element is another ZipCode object. 
Then we search the db for all inspectors in that area, and make $inspector_list.
*/

	//SECOND find zips in range
	foreach ($returned_zipcode->getZipsInRange(0, $radius) as $miles => $zip) {
	    
	    $miles = round($miles, 1);
		//get all inspectors in this range of zip codes
		$query=mysql_query("select * from user, person where person.postal_code='$zip' AND person.user=user.id AND user.role='inspector'");
		
		//now define variables and echo out results into an array ideally.
				while ($row = mysql_fetch_assoc($query)) {
					
					$first_name=$row['first_name'];
					$last_name=$row['last_name'];
					$role=$row['role'];
					$insp_id=$row['id'];
					$inspector_list .= '<h2 class="odd add_insp">'.$first_name.' '.$last_name.'<span class="add_insp_button"><input type="hidden" name="insp_id" value="'.$insp_id.'" /><input class="add_insp_button" type="submit" value="+ Add" name="add" /></span></h2>
											<div class="sub-accordion">
												<div>
												Profile</br>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus pulvinar feugiat porttitor. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut lorem lorem. Aliquam in felis urna, quis sollicitudin mi. Donec malesuada imperdiet augue vel ultrices. 
												</div>
											</div>';
				
				}
										
	}
	echo '<div style="padding-top:10px;" id="nestedAccordion">'.$same_zipcode_inspector_list.$inspector_list.'</div>';	
	/*echo "Zip code <strong>$zip</strong> is <strong>$miles</strong> miles away from "
        ." <strong>$broker_zipcode</strong> ({$zip->getCounty()} county)<br/>";*/
	exit;
	
}
else {
	$inspector_list=null;
}
?>
<?php
if(!empty($_POST)){
	//This block processess adding a new inspector and inviting them to join
	if(!empty($_POST['inspFirstNameValue'])) {
		//Broker name, used for error handling if broker messes up/success.
		$first_name=$_SESSION['person']['first_name'];
		$last_name=$_SESSION['person']['last_name'];
		$broker_id=$_SESSION['person']['id'];
	
		// Get this brokers companys name
		$r=mysql_query('select company.name from broker, company where broker.person='.$broker_id.' and broker.company_id = company.id');
		$row=mysql_fetch_assoc($r);
		$company_name=$row['name'];
		
		//define variables from form
		$insp_first_name = mysql_real_escape_string(ucwords($_POST['inspFirstNameValue']));
		$insp_last_name = mysql_real_escape_string(ucwords($_POST['inspLastNameValue']));
		$insp_mobile_phone = mysql_real_escape_string($_POST['inspMobilePhoneValue']);
		$insp_text_capable = mysql_real_escape_string($_POST['inspTextCapableValue']);
		$insp_email = mysql_real_escape_string($_POST['inspEmailValue']);
		$cinsp_email = mysql_real_escape_string($_POST['cInspEmailValue']);
		$insp_postal_code = mysql_real_escape_string($_POST['inspPostalCodeValue']);
		$digits = 4;
		$insp_user_id = rand(pow(10, $digits-1), pow(10, $digits)-1);
		//see if they have already confirmed their info
		
		//must add validation, and update message with error if needed (enable send button)
			//make sure all required fields exist
				if (empty($insp_first_name) || empty($insp_last_name) || empty($insp_mobile_phone) || empty($insp_email) || empty($insp_postal_code)) {
					echo '<span style="color: #F2713C;">Sorry. Please Enter all required fields. </span>';
					exit;
				}
				if ($insp_email != $cinsp_email){
					echo '<span style="color: #F2713C;">Sorry. The email addresses entered do no match, please try again. </span>';
					exit;
				}
		
		//check if user already exists by email
				$sql = mysql_query ("SELECT * FROM person where email='$insp_email'");
			    $doublecheck = mysql_num_rows($sql); 
		//check if user already exist by phone number
				$sql2 = mysql_query ("SELECT * FROM person where mobile_phone='$insp_mobile_phone'");
			    $doublecheck2 = mysql_num_rows($sql2); 
				
		    	if(($doublecheck > 0) || ($doublecheck2 > 0)){		
				//assume this user exists, and add them as this brokers inspector, send email to inspector saying broker added them	
					$sql3=mysql_query("SELECT * FROM person WHERE email='$insp_email' OR mobile_phone='$insp_mobile_phone'");
					while ($row3 = mysql_fetch_assoc($sql3)) {
						$insp_person_id=$row3['id'];
						$insp_verification_code=$row3['verification_code'];
					}
					//make sure they aren't already connected with this inspector
					$sql4=mysql_query("SELECT * FROM brokersInspectors WHERE broker='$broker_id' AND inspector='$insp_person_id'");
					$doublecheck4 = mysql_num_rows($sql4); 
					if($doublecheck4 > 0) {
						echo '<span style="color: #F2713C;">This inspector is already one of your inspectors.</span><br />
						Would you like to invite <a href="./add_insp.php?id=repeat">another inspector</a>?';
						exit;
						
					}
					else {
						//if not, create relationship for broker and inspector
						$query3=mysql_query("INSERT INTO brokersInspectors (broker, inspector, relationship) VALUES ('$broker_id', '$insp_person_id', '1') ");
						
						echo '<span style="color: #60BDB8;">Thanks, '.$first_name.'. This inspector already existed in our system, so we have added them to your list of "Go-To" inspectors. </span><br />
						Would you like to invite <a href="./add_insp.php?id=repeat">another inspector</a>?';
					}

					if($insp_verification_code=='1'){
						//email notification that broker added them
						$to      = $insp_email;
						$subject = 'A Broker Has Added You | Locizzle.com';
						$message = 'Hello '.$insp_first_name.',
							
You have been added to the "Go-To" Inspectors List for '.$first_name.' '.$last_name.(empty($company_name) ? '' : ' of '.$company_name).'. They would like to use Locizzle to send Home Inspection Requests to you for quoting. 

Thanks,
		
Locizzle.com, Inc.';
							
					
							$headers = 'From: '.config('site.contact_email'). "\r\n" .
								'Reply-To: '.config('site.automail_reply') . "\r\n" .
							    'X-Mailer: PHP/' . phpversion();
							
							mail($to, $subject, $message, $headers);
					}
					else{
						//send same invitation as first time
						$to      = $insp_email;
						$subject = 'Invitation | Locizzle.com';
						$message = 'Hello '.$insp_first_name.',
							
You have been invited to join Locizzle.com by '.$first_name.' '.$last_name.(empty($company_name) ? '' : ' of '.$company_name).'. They would like to use Locizzle to send Home Inspection Requests to you for quoting. 
To accept your invitation and verify your information, visit '.config('site.domain').'/accept_invitation.php?id='.$insp_person_id.'.
		
Thanks,
		
Locizzle.com, Inc.';
							
					
							$headers = 'From: '.config('site.contact_email'). "\r\n" .
								'Reply-To: '.config('site.automail_reply') . "\r\n" .
							    'X-Mailer: PHP/' . phpversion();
							
							mail($to, $subject, $message, $headers);	
					}
					exit;
				}
				
		//add insp as a person, if no flags have been set off.
			$query = mysql_query("INSERT into person (
			first_name, last_name, mobile_phone, text_capable, email, postal_code, user) 
			VALUES ('$insp_first_name', '$insp_last_name', '$insp_mobile_phone', '$insp_text_capable','$insp_email', '$insp_postal_code', '$insp_user_id')");
			
		//add broker to user table
			$query = mysql_query("INSERT into user (
			id, role) 
			VALUES ('$insp_user_id', 'inspector')");
			
		//need to add insp to an insp table
			$query=mysql_query("SELECT * from person where user='$insp_user_id'");
				while ($row = mysql_fetch_assoc($query)) {
						$insp_person_id=$row['id'];
					}
					
			$query = mysql_query("INSERT into inspector (
			person) 
			VALUES ('$insp_person_id')");
		
		//now connect inspector and broker
			$query = mysql_query("INSERT into brokersInspectors (
			broker, inspector) 
			VALUES ('$broker_id', '$insp_person_id')");
		
		
			//now text them.
			require('./Services/Twilio.php');
			$account_sid = "AC295178e1f333781132528cd16d55e49b"; // Twilio TEST account sid
			$auth_token = "81905b30336cc2fb674adf13e3f17fb2"; // Twilio TEST auth token
			
			$client = new Services_Twilio($account_sid, $auth_token);
			$message = $client->account->sms_messages->create(
			  config('twilio.number'), // From a Twilio number in your account
			  $insp_mobile_phone, // Text any number
			  "You are invited to Locizzle.com by $first_name $last_name".(empty($company_name) ? '' : ' of '.$company_name).". To accept the invitation visit ".config('site.domain')."/accept_invitation.php?id=$insp_person_id"
			);
		
	
			//now send an email
			$to      = $insp_email;
			$subject = 'From '.$company_name.' | Locizzle.com';
			$message = 'Hello '.$insp_first_name.',
				
You have been invited to join Locizzle.com by '.$first_name.' '.$last_name.(empty($company_name) ? '' : ' of '.$company_name).'. They would like to use Locizzle to send Home Inspection Requests to you for quoting. 
To accept your invitation and verify your information, visit '.config('site.domain').'/accept_invitation.php?id='.$insp_person_id.'.
			
Thanks,
			
Locizzle.com, Inc.';
				
		
				$headers = 'From: '.config('site.contact_email'). "\r\n" .
					'Reply-To: '.config('site.automail_reply') . "\r\n" .
				    'X-Mailer: PHP/' . phpversion();
				
				mail($to, $subject, $message, $headers);
		
			
			echo '<span>Thanks, '.$first_name.'. We have sent '.$insp_first_name.' a message to invite them to use Locizzle.com.</span></br>
			Would you like to invite <a href="./add_insp.php?id=repeat">another inspector</a>?';
			exit;
			}
		else {
			echo 'Please enter all required information.';
		}
}
?>
<?php include './includes/header_dashboard.php'; ?>
<?php //Broker name, used for error handling if broker messes up.
	$first_name=$_SESSION['person']['first_name'];
	//Broker postal code, used to populate inspectors postal_code
	$postal_code=$_SESSION['person']['postal_code'];
?>
<div class="container">
	<div class="row">
	</br>
	</br>
	<?php if(isset($mobile)): ?>
		<div style="margin:auto;" id="accordion">
	<?php endif; ?>	
	<?php if(!isset($mobile)): ?>
		<div style="width:50%; margin:auto;" id="accordion">
	<?php endif; ?>	
	<!-- this handles if it's a new inspector or not -->
	<?php if(isset($_GET['id']) && ($_GET['id'] == 'repeat')): ?>
		<h3>
			Invite Another Inspector
		</h3>
	<?php endif; ?>	
	<?php if(!isset($_GET['id'])): ?>
		<h3>
			Invite Your Inspectors (one-time setup)
		</h3>
	<?php endif; ?>	
			<div style="font-size:12px;">
				<form id="signUpInsp" action="./add_insp.php" method="POST">
					*<span style="color: #F2713C;">Required Fields</span></br>
					<input name="insp_first_name" type="text" placeholder="* First Name" />
					<input name="insp_last_name" type="text" placeholder="* Last Name" /></br>
					<input autocomplete="off" name="insp_mobile_phone" id="phone" onkeydown="javascript:backspacerDOWN(this,event);" onkeyup="javascript:backspacerUP(this,event);" type="text" placeholder="* Mobile Phone" />
					<input name="insp_text_capable" checked="checked" type="checkbox" id="insp_text_capable"/>
					<span style="font-size:12px;">Text Capable</span></br>
					<input name="insp_email" type="text" placeholder="* Email" /><input name="cinsp_email" type="text" placeholder="* Confirm Email" /></br>
					Inspector's Postal Code:&nbsp;</br>
					<input name="insp_postal_code" type="text" value="<?php echo $postal_code; ?>" /></br>
					<input name="submit_insp" type="submit" value="Invite" /></br>
					</br>
					<div id="insp-message"></div>
					</br>
		
				</form>		
			</div>	
		<!--<h3>Find Inspectors In Your Area</h3>
			<div style="font-size:12px;">
				<form action="./add_insp.php" method="POST">
					<span>Find Inspectors within a specific radius of you:</span></br></br>
					Your Postal Code: <input type="text" id="brokerzip" name="brokerzip" value="<?php echo $postal_code; ?>" /></br></br>
					<label for="50">
						<input class="radius" type="radio" name="radius" id="50" value="50">50 Miles
					</label>
					<label for="100">
						<input class="radius" type="radio" name="radius" id="100" value="100">100 Miles
					</label>
					<label for="150">
						<input class="radius" type="radio" name="radius" id="150" value="150">150 Miles
					</label>
				</form>
				<div id="inspector_list" class="accord">
					
				</div>
			</div>-->
		<a style="color: #3C2111;" href="./dashboard.php"><h3 href="./dashboard.php">Go to Dashboard and post an Inspection Request &#187;</h3></a>
		</div>		
	</div>
	<div class="container footer-drop-shadow">
		<div class="row" align="center">
			<br />
			<br />
			<a href="./faq.php" class="faq_link">Have a question about Locizzle? No problem!<br />Visit our frequestly asked questions here.</a>
			<br />
			<br />
			<br />
			<br />
			<br />
			<br />
		</div>
	</div>
</div>
<?php include './includes/footer.php'; ?>