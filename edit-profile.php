<?php
include './includes/functions.php';
	if (isset($_GET['id'])) {
		
		$inspector_id=mysql_real_escape_string($_GET['id']);
		
		//get inspector email/mobile_phone for get_id
		$sql=mysql_query("SELECT * FROM person WHERE id='$inspector_id'");
		while ($row = mysql_fetch_assoc($sql)) {
				$insp_email=$row['email'];
				$insp_mobile_phone=$row['mobile_phone'];
				$insp_first_name=$row['first_name'];
				$insp_mobile_phone=$row['mobile_phone'];
		}
		//email this person a link to edit their profile (similar to accept_invitation.php)
		$to      = $insp_email;
		$subject = 'Edit Profile | Locizzle.com';
		$message = 'Hello, '.$insp_first_name.'
		
You have requested a link to edit your profile. Please visit: '.config('site.domain').'/accept_invitation.php?id='.$inspector_id.'.

Locizzle, Inc.
';
		$headers = 'From: '.config('site.contact_email'). "\r\n" .
			'Reply-To: '.config('site.automail_reply') . "\r\n" .
		    'X-Mailer: PHP/' . phpversion();
		
		mail($to, $subject, $message, $headers);
		
		$msg='We have emailed you a link to edit your profile. For further assistance please contact us at '.config('site.automail_reply').'.';
	}
?>
<?php include './includes/header_dashboard.php'; ?>
<div class="container">
	<div class="row" align="center" style="color: #3C2111; font-size:25px;">
	<br /><br /><br /><br />
		<hr />
			</br>
			<?php
				 if (isset($msg)) {
				 	echo $msg;
				 }
				 else {
				 	echo 'We have experienced an error. Please try again, or contact us at '.config('site.automail_reply').'.';
				 }
			?>	
			</br></br>
		<hr />
	<br /><br /><br /><br />
	</div>
</div>
<?php include './includes/footer.php'; ?>