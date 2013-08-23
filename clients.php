<?php include './includes/functions.php'; ?>
<?php
	$action='';
	if(isset($_POST['action']))
		$action=$_POST['action'];
	elseif(isset($_GET['action']))
		$action=$_GET['action'];
			
	switch($action)
	{
		case 'login':
			$email=urldecode(mysql_real_escape_string($_POST['email']));
			$pin=mysql_real_escape_string($_POST['pin']);
			
			$sql='select * from client where email="'.$email.'" and pin="'.$pin.'" limit 1';
			$result=mysql_query($sql) or die(mysql_error());
			
			if($client=mysql_fetch_assoc($result))
			{
				$_SESSION['client']=$client['id'];
			}
			else
			{
				$error='Your e-mail address or PIN was incorrect. Please try again.';
			}
			
			break;
		
		case 'logout':
			unset($_SESSION['client']);
			
			break;
		
		case 'change_pin':
			$old_pin=mysql_real_escape_string($_POST['old_pin']);
			$new_pin=mysql_real_escape_string($_POST['new_pin']);
			$confirm_new_pin=mysql_real_escape_string($_POST['confirm_new_pin']);
			
			$sql='select * from client where pin="'.$old_pin.'" limit 1';
			$result=mysql_query($sql) or die(mysql_error());
			if(mysql_num_rows($result)>0)
			{
				if($new_pin==$confirm_new_pin)
				{
					if(is_numeric($new_pin))
					{
						$sql='update client set pin="'.$new_pin.'" where id='.$_SESSION['client'].' limit 1';
						mysql_query($sql) or die(mysql_error().$sql);
						echo 'Your PIN was successfully updated.';
					}
					else
					{
						echo 'Your PIN must be numeric.';
					}
				}
				else
				{
					echo 'You must enter your new PIN correctly in both fields.';
				}
			}
			else
			{
				echo 'You did not enter your current PIN correctly.';
			}
			
			exit;
			break;
			
		case 'accept_bid':
			$inspector_id=mysql_real_escape_string($_POST['inspector_id']);
			accept_bid($inspector_id);
			break;
	}
	
	if(isset($_SESSION['client']))
	{
		$client_id=$_SESSION['client'];
		
		// Get the client data
		$sql='select * from client where id='.$client_id.' limit 1';
		$result=mysql_query($sql) or die(mysql_error());
		$client=mysql_fetch_assoc($result);
		
		// Get the client's quote requests
		$sql='select * from quoteRequest where client='.$client_id.' and status="pending" order by submitted';
		$result=mysql_query($sql) or die(mysql_error());
		
		$quote_requests=array();
		$properties=array();
		$inspectors=array();
		
		while($quote_request=mysql_fetch_assoc($result))
		{
			$quote_request['bids']=array();
			
			$sql='select * from bid where quoteRequest='.$quote_request['id'].' order by time_entered';
			$r=mysql_query($sql) or die(mysql_error());
			while($bid=mysql_fetch_assoc($r))
			{
				$quote_request['bids'][ $bid['id'] ]=$bid;
				
				if(!isset($inspectors[ $bid['inspector'] ]))
				{
					$sql='select * from person where id='.$bid['inspector'].' limit 1';
					$r2=mysql_query($sql) or die(mysql_error());
					$inspector=mysql_fetch_assoc($r2);
					$inspectors[ $inspector['id'] ]=$inspector;
				}
			}
				
			
			$quote_requests[ $quote_request['id'] ]=$quote_request;
			
			if(!isset($properties[ $quote_request['property'] ]))
			{
				$sql='select * from property where id='.$quote_request['property'].' limit 1';
				$r=mysql_query($sql) or die(mysql_error());
				$property=mysql_fetch_assoc($r);
				$properties[ $property['id'] ]=$property;
			}
		}
	}
	
	include './includes/header_dashboard.php';
	
	if(empty($_SESSION['client']))
		include './includes/clients_login.php';
	else
		include './includes/clients_dashboard.php';
		
	include './includes/footer.php';
?>