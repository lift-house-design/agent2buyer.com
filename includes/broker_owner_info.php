<?php
	require_once('functions.php');
	
	if(!empty($_POST))
	{
		// Handle errors
		$errors=array();
		
		// Sanitize all inputs
		foreach($_POST as $i=>$data)
			if(!is_array($data))
				$_POST[$i]=mysql_real_escape_string(trim($data));
		
		$sql='
			select
				company_id
			from
				broker
			where
				person = "'.mysql_real_escape_string($_SESSION['person']['id']).'"
			limit 1
		';
		$company_id_result=mysql_query($sql) or die(mysql_error());
		$company_id_row=mysql_fetch_assoc($company_id_result);
		$company_id=$company_id_row['company_id'];
		
		if(empty($company_id))
		{
			$sql='
				insert into
					company
					(
						name,
						owners_first_name,
						owners_last_name,
						owners_email,
						owners_phone
					)
					values
					(
						"'.$_POST['name'].'",
						"'.$_POST['first_name'].'",
						"'.$_POST['last_name'].'",
						"'.$_POST['email'].'",
						"'.$_POST['phone'].'"
					)
			';
			mysql_query($sql) or die(mysql_error());
			$company_id=mysql_insert_id();
			
			$sql='
				update
					broker
				set
					company_id='.$company_id.'
				where
					person='.$_SESSION['person']['id'].'
				limit 1
			';
			mysql_query($sql) or die(mysql_error());
		}
		else
		{
			$sql='
				update
					company
				set
					name="'.$_POST['name'].'",
					owners_first_name="'.$_POST['first_name'].'",
					owners_last_name="'.$_POST['last_name'].'",
					owners_email="'.$_POST['email'].'",
					owners_phone="'.$_POST['phone'].'"
				where
					id='.$company_id.'
				limit 1
			';
			mysql_query($sql) or die(mysql_error());
		}
		
		echo 'Your broker/owner information has been saved.';
		exit;
	}
	
	$r=mysql_query('select company.* from broker, company where broker.person='.$_SESSION['person']['id'].' and broker.company_id = company.id');
	$company=mysql_fetch_assoc($r);
?>
<div id="broker-owner-info-result"></div>
<form id="broker-owner-info" action="includes/broker_owner_info.php">
	<input type="text" name="name" placeholder="Compay's Name" value="<?php echo $company['name'] ?>" />
	<input type="text" class="half" name="first_name" placeholder="Broker Owner's First Name" value="<?php echo $company['owners_first_name'] ?>" />
	<input type="text" class="half" name="last_name" placeholder="Broker Owner's Last Name" value="<?php echo $company['owners_last_name'] ?>" />
	<input type="text" class="half" id="phone" onkeydown="javascript:backspacerDOWN(this,event);" onkeyup="javascript:backspacerUP(this,event);" name="phone" placeholder="Broker Owner's Phone Number" value="<?php echo $company['owners_phone'] ?>" />
	<input type="text" class="half" name="email" placeholder="Broker Owner's E-mail" value="<?php echo $company['owners_email'] ?>" />
	<input type="checkbox" name="is_owner" id="is_owner" /> <label for="is_owner" style="display: inline-block; margin: 10px 0; cursor: pointer;">I am the broker owner of this company.</label><br />
	<input type="submit" name="broker_owner_info" value="Update broker owner Info" />
</form>
<script>
	$(document)
		.on('change','input[name="is_owner"]',function(){
			var isChecked=$(this).eq(0)[0].checked;
			
			if(isChecked)
			{
				var firstName=$('#my-info input[name="first_name"]').val();
				var lastName=$('#my-info input[name="last_name"]').val();
				var phoneNumber=$('#my-info input[name="mobile_phone"]').val();
				var email=$('#my-info input[name="email"]').val();
				
				$(this).siblings('input[name="first_name"]').val(firstName);
				$(this).siblings('input[name="last_name"]').val(lastName);
				$(this).siblings('input[name="phone"]').val(phoneNumber);
				$(this).siblings('input[name="email"]').val(email);
			}
		})
		.on('submit','#broker-owner-info',function(){
			var data={};
			
			$(this)
				.find('input')
				.each(function(){
					data[$(this).attr('name')]=$(this).val();
				});
				
			$.ajax({
				url: 'includes/broker_owner_info.php',
				type: 'post',
				data: data,
				success: function(html,status){
					$('#broker-owner-info-result')
						.html(html)
						.show();
					
					setInterval(function(){
						$('#broker-owner-info-result').hide();
					},5000);
				}
			});
			
			return false;
		});
</script>