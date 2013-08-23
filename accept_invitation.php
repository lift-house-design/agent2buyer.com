<?php $accordion=""; ?>
<?php include './includes/functions.php'; ?>
<?php include './includes/header_dashboard.php'; ?>
<?php
//this block handles inserting insp profile information (step 2)
if(isset($_POST['submit_profile'])) {
	if(isset($_GET['id'])) {
	$inspector_id=mysql_real_escape_string($_GET['id']);
	}
	
	$insp_company_name = ucwords(mysql_real_escape_string($_POST['insp_company_name']));
	$insp_office_phone = mysql_real_escape_string($_POST['insp_office_phone']);
	$insp_ext = mysql_real_escape_string($_POST['insp_ext']);
	$insp_description = mysql_real_escape_string($_POST['insp_description']);
	
	//add image
	$info = pathinfo($_FILES['userFile']['name']);
	$ext = $info['extension']; // get the extension of the file
	$newname = $inspector_id.'.'.$ext; 
	
	$target = './img/inspector/'.$newname;
 	move_uploaded_file( $_FILES['userFile']['tmp_name'], $target);

	$sql2=mysql_query("UPDATE inspector SET company='$insp_company_name', description='$insp_description', file_ext='$ext' WHERE person='$inspector_id'");
	$sql3=mysql_query("UPDATE person SET office_phone='$insp_office_phone', ext='$insp_ext' WHERE id='$inspector_id'");

	//add image
	$info = pathinfo($_FILES['userFile']['name']);
	$ext = $info['extension']; // get the extension of the file
	$newname = $inspector_id.''.$ext; 
	
	$target = 'images/'.$newname;
 	move_uploaded_file( $_FILES['userFile']['tmp_name'], $target);
	
	header ("Location: ./inspector.php?id=".$inspector_id);
	exit;
}

?>
<?php
//this block handles updating insp info (step 1)
if(isset($_POST['submit_accept'])) {
		if(isset($_GET['id'])) {
		$inspector_id=mysql_real_escape_string($_GET['id']);
		}
	
	if (isset($_POST['insp_text_capable'])) {
		$_POST['insp_text_capable'] = '1';
	}
	else {
		$_POST['insp_text_capable'] = '0';
	}
	
	$insp_first_name = ucwords(mysql_real_escape_string($_POST['insp_first_name']));
	$insp_last_name = ucwords(mysql_real_escape_string($_POST['insp_last_name']));
	$insp_mobile_phone = mysql_real_escape_string($_POST['insp_mobile_phone']);
	$insp_text_capable = mysql_real_escape_string($_POST['insp_text_capable']);
	$insp_email = mysql_real_escape_string($_POST['insp_email']);
	$insp_postal_code = mysql_real_escape_string($_POST['insp_postal_code']);
				
	$sql=mysql_query("UPDATE person SET first_name='$insp_first_name', last_name='$insp_last_name', mobile_phone='$insp_mobile_phone', text_capable='$insp_text_capable', email='$insp_email', postal_code='$insp_postal_code', verification_code='1' WHERE id='$inspector_id'");
	
	$msg='You have successfully confirmed your information. Now create a profile below!';
}

?>
<?php
	if(isset($_GET['id'])) {
		$inspector_id=mysql_real_escape_string($_GET['id']);
		$sql1=mysql_query("SELECT * from person where id='$inspector_id'");
		$person = array(); 
			while($row1 = mysql_fetch_assoc($sql1)) { 
			   $person[] = $row1; 
			}
		$sql2=mysql_query("SELECT * from inspector where person='$inspector_id'");
		$inspector = array(); 
			while($row2 = mysql_fetch_assoc($sql2)) { 
			   $inspector[] = $row2; 
			}
	}
?>
<div class="container">
	
	<?php if(isset($mobile)): ?>
	<div class="row" style="margin:auto; color: #3C2111; font-size:18px;padding-top:20px;">
	<?php endif; ?>	
	<?php if(!isset($mobile)): ?>
		<div class="row" style="color: #3C2111; font-size:18px;padding-top:20px; width:20%; margin:auto;">
	<?php endif; ?>		
		Realtors use Locizzle.com to automate the process of arranging for a home inspection with their select group of "go-to" inspectors.  <a href="./img/inspector_learn_more.png" style="color: #60BDB8;"rel="prettyPhoto"><img style="display:none;" alt="Learn More">Learn More &#187;</img></a>
	<br />
	<br />
	Please confirm your information, and complete your profile.
	
	</div>
	<div class="row">
	</br>
	</br>
	<?php if(isset($mobile)): ?>
		<div style="margin:auto;" id="accordion">
	<?php endif; ?>	
	<?php if(!isset($mobile)): ?>
		<div style="width:50%; margin:auto;" id="accordion">
	<?php endif; ?>	
		<h3>Confirm Your Information</h3>
			<div style="font-size:12px;">
				<form id="submit_accept" class="accept" action="./accept_invitation.php?id=<?php echo $inspector_id; ?>" method="POST">
					<input name="insp_first_name" type="text" value="<?php echo $person[0]['first_name']; ?>" />
					<input name="insp_last_name" type="text" value="<?php echo $person[0]['last_name']; ?>" /></br>
					<input name="insp_mobile_phone" id="phone" onkeydown="javascript:backspacerDOWN(this,event);" onkeyup="javascript:backspacerUP(this,event);" type="text" value="<?php echo $person[0]['mobile_phone']; ?>" />
					<?php
						if($person[0]['text_capable'] == 0){
							echo '<input name="insp_text_capable" type="checkbox" id="insp_text_capable" />';
						}
						else {
							echo '<input name="insp_text_capable" type="checkbox" checked="checked" id="insp_text_capable" />';
						}
					?>
					<span style="font-size:12px;">My phone is text capable</span></br>
					<input name="insp_email" type="text" value="<?php echo $person[0]['email']; ?>" /></br>
					Postal Code:&nbsp;</br>
					<input name="insp_postal_code" type="text" value="<?php echo $person[0]['postal_code']; ?>" /></br>
					<input type="hidden" name="insp_id" value="<?php echo $inspector_id; ?>" />
					<input name="submit_accept" class="accept_invitation" id="submit_accept" type="submit" value="Save Information" /></br>
					</br>
				</form>		
				<div id="accept-message">
						<?php 
							if (isset($msg)) {
								echo $msg;
							}
						?>
				</div>
				</br>
			</div>	
		<h3>
			Add Your Profile Information
		</h3>
			<div style="font-size:12px;">
				<form id="accept information" enctype='multipart/form-data' action="./accept_invitation.php?id=<?php echo $inspector_id; ?>" method="POST">
					<input name="insp_company_name" type="text" value="<?php echo $inspector[0]['company']; ?>" placeholder="Company Name" /></br>
					<input name="insp_office_phone" id="phone" onkeydown="javascript:backspacerDOWN(this,event);" onkeyup="javascript:backspacerUP(this,event);" type="text" value="<?php echo $person[0]['office_phone']; ?>" placeholder="Office Phone" />
					<input name="insp_ext" maxlength="4" size="4" type="text" value="<?php echo $person[0]['ext']; ?>" placeholder="Ext." /></br></br>
					<b class="bold">Profile Description:&nbsp;</b></br>
					<textarea name="insp_description" style="width:250px; height:150px;" type="text" ><?php echo $inspector[0]['description']; ?></textarea></br></br>
					
					<!-- upload image to img/inspector directory if not mobile iOS doesn't currently support upload to server - must use separate app if desired-->
					<?php if(!isset($mobile)): ?>
					<b class="bold">Profile Picture:&nbsp;</b></br>
					<input type="file" name="userFile" /></br></br>
					<?php endif; ?>	
					
					<input name="submit_profile" type="submit" value="Create Profile" /></br>
					</br>
					<div id="insp-message">
					<?php 
							if (isset($msg2)) {
								echo $msg2;
							}
						?>
					</div>
					</br>
		
				</form>		
			</div>
		</div>		
	</div>
</div>

<?php include './includes/footer.php'; ?>

