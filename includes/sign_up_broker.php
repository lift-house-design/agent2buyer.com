<?php
error_reporting(E_ALL | E_WARNING | E_NOTICE);
ini_set('display_errors', TRUE);

if(!empty($_POST)){
	if(!empty($_POST['firstNameValue'])) {
	//Connect to database, start session
		include './functions.php';
		
		$first_name = ucwords(mysql_real_escape_string($_POST['firstNameValue']));
		$last_name = ucwords(mysql_real_escape_string($_POST['lastNameValue']));
		$mobile_phone = mysql_real_escape_string($_POST['mobilePhoneValue']);
		$text_capable = mysql_real_escape_string($_POST['textCapableValue']);
		$office_phone = mysql_real_escape_string($_POST['officePhoneValue']);
		$ext = mysql_real_escape_string($_POST['extValue']);
		$email = mysql_real_escape_string($_POST['emailValue']);
		$c_email = mysql_real_escape_string($_POST['cemailValue']);
		$password = mysql_real_escape_string($_POST['passwordValue']);
		$cpassword = mysql_real_escape_string($_POST['cpasswordValue']);
		$postal_code = mysql_real_escape_string($_POST['postalCodeValue']);
		$digits = 4;
		$user_id = rand(pow(10, $digits-1), pow(10, $digits)-1);
		$verification_code = rand(pow(10, $digits-1), pow(10, $digits)-1);
		
		try
		{
			mysql_query('start transaction');
			
			//must add validation, and update message with error if needed (enable send button?)
			//do passwords match?
			if ($password !== $cpassword) {
				throw new Exception('Sorry, '.$first_name.'. Your passwords do no match, please try again.');
			}
			if ($email !== $c_email) {
				throw new Exception('Sorry, '.$first_name.'. The email addresses entered do no match, please try again.');
			}
			//make sure all required fields exist
			if (empty($first_name) || empty($last_name) || empty($mobile_phone) || empty($email) || empty($password) || empty($cpassword)  || empty($postal_code)) {
				throw new Exception('Sorry. Please Enter all required fields.');
			}
				
			//make sure user doesn't already exist by email
			$sql = mysql_query ("SELECT * FROM person where email='$email'");
			$doublecheck = mysql_num_rows($sql); 
	
			if($doublecheck > 0){ 
				throw new Exception('Sorry, '.$first_name.'. This email already exists.');
			}	
			//make sure user doesn't already exist by phone number
			$sql2 = mysql_query ("SELECT * FROM person where mobile_phone='$mobile_phone'");
			$doublecheck2 = mysql_num_rows($sql2); 
	
			if($doublecheck2 > 0){ 
				throw new Exception('Sorry, '.$first_name.'. This mobile phone number already exists.');
			}	
			
			//add broker as a person, if no flags have been set off.
			$query = mysql_query("INSERT into person (
			first_name, last_name, mobile_phone, text_capable, office_phone, ext, email, password, postal_code, verification_code, time_verification_code_sent, user) 
			VALUES ('$first_name', '$last_name', '$mobile_phone', '$text_capable', '$office_phone', '$ext', '$email', '".sha1($password)."', '$postal_code', '$verification_code', now(), '$user_id')");
			
			if(!$query)
				throw new Exception(mysql_error());
			
			//add broker to user table
			$query = mysql_query("INSERT into user (
			id, role, password) 
			VALUES ('$user_id', 'broker', '".sha1($password)."')");
			
			if(!$query)
				throw new Exception(mysql_error());
			
			//need to add broker to a broker table
			//NOTE: could have used $broker_id = mysql_insert_id(); to retrieve ID
			$query=mysql_query("SELECT * from person where user='$user_id'");
			$row = mysql_fetch_assoc($query);
			$broker_id=$row['id'];
			
			$company_name=mysql_real_escape_string($_POST['companyNameValue']);
			if(empty($company_name))
			{
				throw new Exception('You must enter your company\'s name.');
			}
			
			// If a company exists by that name, associate them with that company, otherwise create a new one
			$query=mysql_query('select id from company where name="'.$company_name.'" limit 1');
			if($row=mysql_fetch_assoc($query))
			{
				$company_id=$row['company_id'];
			}
			else
			{
				mysql_query('
					insert into
						company
						(
							name
						)
						values
						(
							"'.$company_name.'"
						)
				') or die(mysql_error());
				$company_id=mysql_insert_id();
			}
			
			$query = mysql_query("INSERT into broker (
			person, company_id) 
			VALUES ('$broker_id','$company_id')");
			
			if(!$query)
				throw new Exception(mysql_error());
			
			//set session variables array
			$sql = mysql_query ("SELECT * FROM person where email='$email' AND password='".sha1($password)."'");
			$doublecheck = mysql_num_rows($sql); 
	
			if($doublecheck == 1){ 
				while($row = mysql_fetch_assoc($sql)) { 
					$data = array();
					$data = $row;
					$_SESSION['person'] = $data;
				} 	
			}
			
			//text or email?
			if ($text_capable == '1') {
				$medium=" text message and an email";
				//now text them.
				require('../Services/Twilio.php');
				$account_sid = "AC295178e1f333781132528cd16d55e49b"; // Twilio TEST account sid
				$auth_token = "81905b30336cc2fb674adf13e3f17fb2"; // Twilio TEST auth token
				
				$client = new Services_Twilio($account_sid, $auth_token);
				$message = $client->account->sms_messages->create(
				  config('twilio.number'), // From a Twilio number in your account
				  $mobile_phone, // Text any number
				  "Hello $first_name, Your confirmation code is $verification_code."
				);
				
				//also email
				$to      = $email;
				$subject = 'Sign Up Code Verification  |  Locizzle.com';
				$message = 'Hello '.$first_name.',
				
	Your verification code is "'.$verification_code.'". Please enter this code on our website to verify your account.		
	If you did not request this email, please respond to this email with the subject "Unsubscribe".
				
	Locizzle.com, Inc.';
				
				
				$headers = 'From: '.config('site.contact_email'). "\r\n" .
					'Reply-To: '.config('site.automail_reply') . "\r\n" .
					'X-Mailer: PHP/' . phpversion();
				
				mail($to, $subject, $message, $headers);
			}
			else {
				$medium="n email";
				//now send an email
				$to      = $email;
				$subject = 'Sign Up Code Verification  |  Locizzle.com';
				$message = 'Hello '.$first_name.',
				
	Your verification code is "'.$verification_code.'". Please enter this code on our website to verify your account.		
	If you did not request this email, please respond to this email with the subject "Unsubscribe".
				
	Locizzle.com, Inc.';
				
				
				$headers = 'From: '.config('site.contact_email'). "\r\n" .
					'Reply-To: '.config('site.automail_reply') . "\r\n" .
					'X-Mailer: PHP/' . phpversion();
				
				mail($to, $subject, $message, $headers);
			}
				
			echo '<span>Thanks, '.$first_name.'. We have sent you a'.$medium.' with a verification code.</span></br></br> <span style="font-size:12px;">  Please enter the verification code below to verify your account prior to closing your browser.</span>';
			mysql_query('commit');
		}
		catch(Exception $e)
		{
			echo '<span style="color: #F2713C;">&nbsp;&nbsp;'.$e->getMessage().'</span>';
			mysql_query('rollback');
		}
		
		exit;
	}

elseif(!empty($_POST['confirmationCodeValue'])) {
	//Connect to database, start session
	include './functions.php';
	if (isset($_SESSION['person']['first_name'])){
		$email=$_SESSION['person']['email'];
		$password=$_SESSION['person']['password'];
		$first_name=$_SESSION['person']['first_name'];
		
		$confirmation_code = $_POST['confirmationCodeValue'];
		
		$sql = mysql_query("SELECT * FROM person WHERE email = '$email' AND password='$password'");
			$row = mysql_fetch_assoc($sql);
				$code = $row['verification_code'];
				$time_verification_code_sent = $row['time_verification_code_sent'];
				$text_capable = $row['text_capable'];
				$mobile_phone = $row['mobile_phone'];
		
		$finder = "SELECT TIMESTAMPDIFF(minute, '$time_verification_code_sent', now())"; 
		$Difference = mysql_result(mysql_query($finder),0,0); 

					
		if ($code == $confirmation_code) {
				
				//check to make sure code has not expired, if so send new one
				if ($Difference > 5) {
					$digits=4;
					$verification_code = rand(pow(10, $digits-1), pow(10, $digits)-1);
					$sql=mysql_query("UPDATE person SET verification_code='$verification_code', time_verification_code_sent=now() WHERE email='$email' AND password='$password'");
					
					if ($text_capable==0) {
						//say you emailed new code
						$medium="n email";
						//now send an email
						$to      = $email;
						$subject = 'Sign Up Code Verification  |  Locizzle.com';
						$message = 'Hello '.$first_name.',
						
Your NEW verification code is "'.$verification_code.'". Please enter this code on our website to verify your account.						
If you did not request this email, please respond to this email with the subject "Unsubscribe".
						
Locizzle.com, Inc.';
						
						
						$headers = 'From: '.config('site.contact_email'). "\r\n" .
			    			'Reply-To: '.config('site.automail_reply') . "\r\n" .
						    'X-Mailer: PHP/' . phpversion();
						
						mail($to, $subject, $message, $headers);			
					}
					else {
						//send sms with new code
						$medium=" text message";
						//now send sms
						require('../Services/Twilio.php');
						$account_sid = "AC295178e1f333781132528cd16d55e49b"; // Twilio TEST account sid
						$auth_token = "81905b30336cc2fb674adf13e3f17fb2"; // Twilio TEST auth token
						
						$client = new Services_Twilio($account_sid, $auth_token);
						$message = $client->account->sms_messages->create(
						  config('twilio.number'), // From a Twilio number in your account
						  $mobile_phone, // Text any number
						  "Hello $first_name, Your NEW confirmation code is $verification_code."
						);
					}
					echo '<span style="color: #F2713C;">Sorry. This verification code has expired. You have been sent a'.$medium.' with a new code.</br> Please try again.</span>';
					exit;
				}
				else {
				//Verify User in DB, change verification_code to a 1.
				$sql=mysql_query("UPDATE person SET verification_code='1' WHERE email='$email' AND password='$password'");
				flush();
				echo '<span style="font-weight:bold; color: #60BDB8;">Thank you for verifying your account!</span></br><a style="color: #3C2111;" href="./add_insp.php">Add Your Inspectors &#187;</a>';
				//header("Location:./dashboard.php");
				exit();
				}
			}
				
			else {
				if ($code == 1) {
					$message="You have already been verified.";
				}
				else {
				$message="We're sorry. This is not the correct code. Please try again";
				}
					echo '<img style="height:40px; width:auto;margin-bottom:-15px;" src="./img/successful.png" /><span>'.$message.'</span>';
					exit();
			}
	}
	else {
		echo '<span style="color: #F2713C;">Sorry. Your session has expired, plese try again. </span>';
		exit;
	}
}
	else {
		exit;
	}
}
?>
<style>
	#cpassword_field {
		width: 160px;
	}
	.placeholder {
		position: relative;
		}
		.placeholder label {
			position: absolute;
			left: 14px;
			top: -1px;
			font-size: 13px;
			cursor: text;
		}
</style>
<!--[if IE]>
<script>
	$(document)
		.on('blur','.placeholder input',function(){
			if($(this).val()=='')
			{
				$(this)
					.parents('.placeholder')
					.find('label')
					.show();
			}
		})
		.on('focus','.placeholder input',function(){
			$(this)
				.parents('.placeholder')
				.find('label')
				.hide();
		});
</script>
<![endif]-->
	<?php if(isset($mobile)): ?>
		<div style="margin:auto;" id="accordion">
	<?php endif; ?>	
	<?php if(!isset($mobile)): ?>
		<div style="width:45%; margin:auto;" id="accordion">
	<?php endif; ?>	
	
		<h3 id="sign-up-broker-panel">My Contact Info</h3>
			<div style="font-size:12px;">
				<form id="signUpBroker" action="./sign_up_broker.php" method="POST">
					*<span style="color: #F2713C;">Required Fields</span></br>
					<input name="first_name" type="text" placeholder="* First Name" />
					<input name="last_name" type="text" placeholder="* Last Name" /></br>
					<input autocomplete="off" name="mobile_phone" id="phone" onkeydown="javascript:backspacerDOWN(this,event);" onkeyup="javascript:backspacerUP(this,event);" type="text" placeholder="* Mobile Phone" />
					<input name="text_capable" checked="checked" type="checkbox" id="text_capable"/>
					<span style="font-size:12px;">My mobile phone is text capable</span></br>
					<input name="office_phone" id="phone" onkeydown="javascript:backspacerDOWN(this,event);" onkeyup="javascript:backspacerUP(this,event);" type="text" placeholder="Office Phone" />
					<input name="ext" type="text" maxlength="4" size="4" placeholder="Ext." /></br>
					<input name="email" type="text" placeholder="* Email" /> <input name="c_email" type="text" placeholder="* Confirm Email" /></br>
					<!--[if !IE]>-->
					<input name="password" type="password" placeholder="* Password" />
					<input style="width:160px;" name="cpassword" type="password" placeholder="* Re-Enter Password" />
					<!--<![endif]-->
					<!--[if IE]>
					<span class="placeholder">
						<label for="password_field">* Password</label>
						<input name="password" id="password_field" type="password" />
					</span>
					<span class="placeholder">
						<label for="cpassword_field">* Re-Enter Password</label>
						<input name="cpassword" id="cpassword_field" type="password" />
					</span>
					<![endif]-->
					<br />
					<input name="company_name" type="text" placeholder="* Company Name" /></br>
					<input name="postal_code" type="text" placeholder="* Postal Code" /></br>
					<p style="margin: 5px 0;">By submitting you are agreeing to our <a href="./terms.php">Terms and Conditions</a>.</p>
					<input name="submit_contact" type="submit" value="Submit" />&nbsp 
					</br>
						
				</form>		
			</div>	
		<h3 id="verify-panel">Verification</h3>
			<div style="font-size:12px;">
				<div id="contact-message"></div>
				<br />	
				<form id="verifyBroker" action="./sign_up_broker.php" method="POST">
					<input name="confirmation_code" type="text" placeholder="Verification Code" />
					<input name="submit_verify" type="submit" value="Verify" />
					<div id="verification-message"></div>
					</br>
				</form>		
			</div>
	</div>

		<script>
			$("#verify-panel").click(function() {
			     $('html, body').animate({
			         scrollTop: $("#accordion").offset().top
			     }, {
				 	duration: 1000,
					complete: function(){
						$('#verifyBroker input[name="confirmation_code"]').focus();
					}
				 });
			 });
			$("#sign-up-broker-panel").click(function() {
			     $('html, body').animate({
			         scrollTop: $("#accordion").offset().top
			     }, {
				 	duration: 1000,
					complete: function(){
						$('#signUpBroker input[name="first_name"]').focus();
					}
				 });
			 });
 			/*
			$('#accordion h3:eq(1)')
				.click(function(){
					$('html,body').animate({ scrollTop: $('#accordion h3:eq(1)').offset().top },'slow');
				});*/
		</script>	