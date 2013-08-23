<?php
include './includes/functions.php';
	if (isset($_POST['recover_email'])){
		$email=mysql_real_escape_string($_POST['recover_email']);
		
		$query=mysql_query("SELECT * FROM person where email='$email'");
		while ($row = mysql_fetch_assoc($query)) {
			$password=$row['password'];
			$first_name=$row['first_name'];
		}
		if (!empty($password)){
			// Create a random pw
			$chars='abcdefghijklmnopqrstuvwxyz';
			$chars.=strtoupper($chars);
			$chars.='0123456789';
			$new_password='';
			for($i=0;$i<6;$i++)
			{
				$new_password.=$chars[rand(0,strlen($chars))];
			}
			// Update tables with new pw
			$sql='update person set password="'.sha1($new_password).'" where email="'.$email.'" limit 1';
			mysql_query($sql) or die(mysql_error());
			$sql='select user from person where email="'.$email.'"';
			$result=mysql_query($sql);
			$row=mysql_fetch_assoc($result);
			$user_id=$row['user'];
			$sql='update user set password="'.sha1($new_password).'" where id='.$user_id.' limit 1';
			mysql_query($sql) or die(mysql_error());
			
			//email them their password
			$to      = $email;
			$subject = 'Password Recovery  |  Locizzle.com';
			$message = 'Hello '.$first_name.',
			
Your password is "'.$new_password.'".
			
Locizzle.com, Inc.';
			
			
			$headers = 'From: '.config('site.contact_email'). "\r\n" .
			    'Reply-To: '.config('site.automail_reply') . "\r\n" .
			    'X-Mailer: PHP/' . phpversion();
			
			mail($to, $subject, $message, $headers);
			$msg="We have sent you an email with your password. If you continue to experieince difficulties, please contact us at ".config('site.automail_reply');
		}
		else {
			//they don't exist in our system
			$msg="We are sorry, but this email address does not exist in our system as a broker. Please <a href='./'>Sign Up</a>.</br><span style='font-size:18px;'>Inspectors do not need to sign in to use are system. They simply need an invite from a broker.<span>";
			
		}
	}

?>
<?php include './includes/header.php'; ?>
<div class="container">
	<div class="row" align="center" style="color: #3C2111; font-size:25px;">
		<br /><br /><br /><br />
			<?php
				if (!isset($msg)){
					echo '<form method="POST" action="./forgot-password.php"><hr />
									</br>
								<span style="font-size:18px; color: #60BDB8;">Please type in your email, and we will send you your password.</span>
									</br>				
									</br>
								<input name="recover_email" type="text" placeholder="* Email Address" /></br>
								<input name="submit_contact" type="submit" value="Recover Password" />
									</br>
									</br>
								<hr />
							</form>';
				}
				else {
					echo '<hr />
								</br>
							'.$msg.'
								</br>
								</br>
							<hr />';
				}
			?>
	<br /><br /><br /><br />	
	</div>
</div>
<?php include './includes/footer.php'; ?>
