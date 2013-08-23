<?php
	if(!empty($_POST))
	{
		// Handle errors
		$errors=array();
		
		// Required
		$required_fields=array(
			'first_name'=>'First Name',
			'last_name'=>'Last Name',
			'mobile_phone'=>'Mobile Phone',
			'email'=>'E-mail',
			'postal_code'=>'Postal Code',
		);
		foreach($required_fields as $field=>$label)
			if(empty($_POST[$field]))
				$errors[]=$label.' is required.';
		
		require_once('functions.php');
		
		// Sanitize all inputs
		foreach($_POST as $i=>$data)
			if(!is_array($data))
				$_POST[$i]=mysql_real_escape_string(trim($data));
		
		// Build the update query
		$sql='
			update
				person
			set
				first_name="'.$_POST['first_name'].'",
				last_name="'.$_POST['last_name'].'",
				mobile_phone="'.$_POST['mobile_phone'].'",
				text_capable="'.(empty($_POST['text_capable']) ? 0 : 1).'",
				office_phone="'.$_POST['office_phone'].'",
				ext="'.$_POST['ext'].'",
				email="'.$_POST['email'].'",
				postal_code="'.$_POST['postal_code'].'"
		';
		
		// Check for a password change
		if(!empty($_POST['password']))
		{
			// Change the password if desired
			if($_POST['password']==$_POST['confirm_password'])
			{
				// Leading comma must be included to separate this assignment from the others
				$sql.='
					,password="'.$_POST['password'].'"
				';
			}
			else
				$errors[]='Password and Confirm Password did not match. Please make sure you are entering the correct password in both fields.';
		}
		
		$sql.='
			where
				id='.$_SESSION['person']['id'].'
			limit 1
		';
		
		if(!empty($errors))
		{
			echo '<div class="form-errors">';
			if(count($errors)==1)
				echo $errors[0];
			else
			{
				echo '<ul>';
				foreach($errors as $e)
					echo '<li>'.$e.'</li>';
				echo '</ul>';
			}
			echo '</div>';
			exit;
		}
		
		// Save the profile
		mysql_query($sql) or die(mysql_error());
		
		// Update the session data
		$r=mysql_query('select * from person where id='.$_SESSION['person']['id']);
		$_SESSION['person']=mysql_fetch_assoc($r);
		
		echo 'Your profile has been saved.';
		exit;
	}
	
	$person=$_SESSION['person'];
?>
<style>
	.placeholder {
		position: relative;
		}
		.placeholder label {
			position: absolute;
			left: 14px;
			top: 2px;
			font-size: 14px;
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
<div id="my-info-result"></div>
<form id="my-info" action="includes/update_my_info.php">
	<input type="text" class="half" name="first_name" placeholder="* First Name" value="<?php echo $person['first_name'] ?>" />
	<input type="text" class="half" name="last_name" placeholder="* Last Name" value="<?php echo $person['last_name'] ?>" />
	<input type="text" class="half" id="phone" onkeydown="javascript:backspacerDOWN(this,event);" onkeyup="javascript:backspacerUP(this,event);" name="mobile_phone" placeholder="* Mobile Phone" value="<?php echo $person['mobile_phone'] ?>" />
	<input type="checkbox" name="text_capable" checked="<?php echo (empty($person['text_capable']) ? '' : 'checked') ?>" /> My mobile phone is text capable
	<input type="text" class="half" id="phone" onkeydown="javascript:backspacerDOWN(this,event);" onkeyup="javascript:backspacerUP(this,event);"name="office_phone" placeholder="Office Phone" value="<?php echo $person['office_phone'] ?>" />
	<input type="text" class="tiny" name="ext" placeholder="Ext." value="<?php echo $person['ext'] ?>" /><br />
	<input type="text" name="email" placeholder="* E-mail" value="<?php echo $person['email'] ?>" />
	<!--[if !IE]>-->
	<input type="password" class="half" name="password" placeholder="Change Password" />
	<input type="password" class="half" name="confirm_password" placeholder="Confirm Password" />
	<!--<![endif]-->
	<!--[if IE]>
	<span class="placeholder">
		<label for="password_field">* Password</label>
		<input type="password" class="half" name="password" />
	</span>
	<span class="placeholder">
		<label for="cpassword_field">* Re-Enter Password</label>
		<input type="password" class="half" name="confirm_password" />
	</span>
	<![endif]-->
	<input type="text" name="postal_code" placeholder="* Postal Code" value="<?php echo $person['postal_code'] ?>" /></br>
	<input type="submit" name="update_my_info" value="Update My Info" />
</form>
<script>
	$(document)
		.on('submit','#my-info',function(){
			var data={};
			
			$(this)
				.find('input')
				.each(function(){
					data[$(this).attr('name')]=$(this).val();
				});
				
			$.ajax({
				url: 'includes/update_my_info.php',
				type: 'post',
				data: data,
				success: function(html,status){
					$('#my-info-result')
						.html(html)
						.show();
					
					setInterval(function(){
						$('#my-info-result').hide();
					},5000);
				}
			});
			
			return false;
		});
</script>