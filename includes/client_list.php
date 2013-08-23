<?php
$broker_id=$_SESSION['person']['id'];
$sql=mysql_query("SELECT * from brokersClients WHERE broker='$broker_id'");
$doublecheck = mysql_num_rows($sql); 
	while ($row = mysql_fetch_assoc($sql)) {
		$client_id=$row['client'];
		
		
		$query=mysql_query("SELECT * from client WHERE id='$client_id'");
			while ($row = mysql_fetch_assoc($query)) {
				$client_first_name=$row['first_name'];
				$client_email=$row['email'];
				$client_mobile_phone=$row['mobile_phone'];
				$client_last_name=$row['last_name'];
				$client_list .= '<h3 class="client_list">
										<span class="sign">+</span> '.$client_first_name.' '.$client_last_name.
									'</h3>
										<div class="tester">
											<label class="insp_info">Mobile Phone:&nbsp;&nbsp;&nbsp;</label>'.$client_mobile_phone.'<a class="insp_remove" href="./dashboard.php?removeClient='.$client_id.'">Delete From Client List</a>
										</br>
											<label class="insp_info">Email: &nbsp;&nbsp;</label>'.$client_email.'</br>
										</div>';
				
		
				}

		}
?>
<?php
	if($doublecheck > 0) {
	echo $client_list;
	}
	else {
		echo 'You do not have any Clients at this time.';
	}
?>
