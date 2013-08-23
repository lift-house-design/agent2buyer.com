<?php include './includes/functions.php'; ?>
<?php
	session_start();
	
	if(isset($_SESSION['person']))
		unset($_SESSION['person']);
	
	if(empty($_SESSION['admin']))
	{
		$users=require('includes/admin_users.php');
		
		if(!empty($_POST))
		{
			
			if(isset($users[$_POST['username']]) && $_POST['password']==$users[$_POST['username']])
			{
				$_SESSION['admin']=$_POST['username'];
				header('Location: admin.php');
			}
			else
				$error='Wrong username or password.';
		}
	}
	elseif(isset($_GET['logout']))
	{
		unset($_SESSION['admin']);
		header('Location: admin.php');
	}
	else
	{
		if(isset($_POST['action']))
		{
			// Set a default response
			$response=array(
				'status'=>'error',
			);
			
			switch($_POST['action'])
			{
				case 'billing':
					$value=empty($_POST['value']) ? 0 : 1;
					$person_id=mysql_real_escape_string($_POST['person_id']);
					
					$sql='
						update
							broker
						set
							bill_inspectors='.$value.'
						where
							person='.$person_id.'
						limit 1
					';
					
					if(mysql_query($sql))
					{
						$response['status']='success';
					}
					else
					{
						$response['status']='error';
						$response['error']=mysql_error();
					}
					break;
				
				case 'disabled':
					$value=empty($_POST['value']) ? 1 : 0;
					$person_id=mysql_real_escape_string($_POST['person_id']);
					
					$sql='
						update
							person
						set
							enabled='.$value.'
						where
							id='.$person_id.'
						limit 1
					';
					
					if(mysql_query($sql))
					{
						$response['status']='success';
					}
					else
					{
						$response['status']='error';
						$response['error']=mysql_error();
					}
					break;
					
				case 'num_transactions':
					$from_date=mysql_real_escape_string($_POST['from_date']).' 00:00:00';
					$to_date=mysql_real_escape_string($_POST['to_date']).' 23:59:59';
					$person_id=mysql_real_escape_string($_POST['person_id']);
					
					$sql='
						select 
							count(*) as num_transactions
						from
							bid,
							quoteRequest
						where
							bid.status = "paid" and
							bid.time_entered > "'.$from_date.'" and
							bid.time_entered < "'.$to_date.'" and
							bid.quoteRequest = quoteRequest.id and
							quoteRequest.person = '.$person_id.'
					';
					$r=mysql_query($sql) or die(mysql_error());
					$row=mysql_fetch_assoc($r);
					
					$response['status']='success';
					$response['value']=$row['num_transactions'];
					break;
					
				default:
					$response['error']='Action "'.$_POST['action'].'" not found.';
					break;
			}
			
			if(isset($response))
				echo json_encode($response);
			
			exit;
		}
	}
?>
<?php include './includes/header.php'; ?>
<script src="./js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="./css/jquery.dataTables.css" />
<style type="text/css">
	#admin {
		width:960px;
		margin: 20px auto;
	}
	#admin h1 {
		font-size: 22px;
	}
	#admin .small {
		font-size: 11px;
	}
	#admin strong {
		font-weight: bold;
	}
	
	#users_wrapper {
		margin-top: 10px;
	}
	#users_filter{
		margin-bottom: 5px;	
	}
	#users_filter input {
		height: 20px;
	}
	#users_info, #users_filter, #users_paginate, #users_length {
		font-size: 12px;
	}
	
	#users, #user_details {
		width: 100%;
		margin: 10px 0;
	}
	#users .center {
		text-align: center;
	}
	#users th, #users td {
		padding: 10px;
	}
	#users th {
		background-color: #FBF9F5;
		border-left: 1px solid #3C2111;
		border-top: 1px solid #3C2111;
		border-bottom: 1px solid #3C2111;
	}
	#users th:last-child {
		border-right: 1px solid #3C2111;
	}
	#users tr.odd, #users tr.even, #users td.sorting_1, #users td.sorting_2 {
		background-color: transparent;
	}
	#users tbody tr {
		cursor: pointer;
	}
	
	#users.users_details {
		margin-bottom: 25px;
	}
	#users.users_details tr {
		cursor: auto;
	}
	#users.users_details th {
		width: 200px;
		border-right: 1px solid #3C2111;
		text-align: right;
		font-weight: bold;
	}
	
	#date_range {
		font-size: 11px;
		margin-left: 20px;
	}
	#date_range_picker input {
		height: 20px;
	}
</style>
<!-- START CONTENT -->
<div id="admin">
<?php
	if(empty($_SESSION['admin']))
	{
		include './includes/admin_login.php';
	}
	else
	{
		if(isset($_GET['id']))
		{
			include './includes/admin_details.php';
		}
		else
		{
			include './includes/admin_listing.php';
		}
	}
?>
</div>
<?php include './includes/footer.php'; ?>