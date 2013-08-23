s<?php 
include './includes/functions.php';
	$accordion=''; 
	$_GET['id']='';
if(!empty($_POST)){
	if ((!empty($_POST['firstNameValue'])) && (!empty($_POST['emailValue'])) ) {
		$first_name=$_POST['firstNameValue'];
		$last_name=$_POST['lastNameValue'];
		$email=$_POST['emailValue'];
		$phone=$_POST['phoneValue'];
		$referral=$_POST['referralValue'];
		$message2=$_POST['messageValue'];
		
		//define $emailBody
		$emailBody='
		First Name: '.$first_name.'
		Last Name: '.$last_name.'
		Email: '.$email.'
		Phone: '.$phone.'
		Referred By: '.$referral.'
		Message: '.$message2;
		
		//email contact info to admin
		$to      = config('site.automail_reply');
		$subject = 'Contact Form | Locizzle.com';
		$message = $emailBody;
		$headers = 'From: '.$email. "\r\n" .
	   		'Reply-To: '.$email . "\r\n" .
		    'X-Mailer: PHP/' . phpversion();
		
		mail($to, $subject, $message, $headers);
		
		echo '<br /><span style="color: #60BDB8;">Thank you, '.$first_name.'. We will contact you shortly at '.$email.'.</span>';
		exit;
	}
	else {
		echo '<br /><span style="color: #F2713C;">Please enter your name and email.</span>';
		exit;
	}
}
?>
<?php include './includes/header_dashboard.php' ?>
<div class="container">
	<div class="row" align="center" style="text-align:left;color: #3C2111; font-size:20px;padding-top:30px;">
	<h1 style="font-size: 20px;">We'd like to hear from you. Call us at (888) 203-1221.</h1>
	<h1 style="font-size: 20px;">Or use the form below to send us an email.</h1><br /><br />
	<?php if(isset($mobile)): ?>
		<div style="margin:auto;" id="accordion">
	<?php endif; ?>	
	<?php if(!isset($mobile)): ?>
		<div style="width:45%; margin:auto;" id="accordion">
	<?php endif; ?>	
		<h3>Contact Us</h3>
			<div style="font-size:12px;">
				<form id="contactUs" action="./sign_up_broker.php" method="POST">
					*<span style="color: #F2713C;">Required Fields</span></br>
					<input type="text" name="first_name" placeholder="* First Name" /><br />
					<input type="text" name="last_name" placeholder="Last Name" /><br />
					<input type="text" name="email" placeholder="* Email" /><br />
					<input type="text" name="phone" placeholder="Your Phone" /><br />
					<input type="text" name="referral" placeholder="Who Referred You?" /><br /><br />
					<textarea style="width:200px; height:100px;" name="message">What is your message?</textarea></br>
					<input name="submit_contact" type="submit" value="Submit" />
					</br>
					<div id="contact-results"></div>
					</br>		
				</form>		
			</div>	
		</div>
	</div>
</div>
<?php include './includes/footer.php' ?>
