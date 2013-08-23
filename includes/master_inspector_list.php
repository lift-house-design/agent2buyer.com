<?php
$broker_id=$_SESSION['person']['id'];
$sql=mysql_query("SELECT * from brokersInspectors WHERE broker='$broker_id' AND relationship='0'");
	while ($row = mysql_fetch_assoc($sql)) {
		$inspector_id=$row['inspector'];
		
		
		$query=mysql_query("SELECT * from person WHERE id='$inspector_id'");
			while ($row = mysql_fetch_assoc($query)) {
				$inspector_first_name=$row['first_name'];
				$inspector_last_name=$row['last_name'];
				$inspector_mobile_phone=$row['mobile_phone'];
				$inspector_email=$row['email'];
				$inspector_zip=$row['postal_code'];
				$master_inspector_list0 = '<h3 class="inspector_list">
										<span class="sign">+</span> <a target="_blank" href="./inspector.php?id='.$inspector_id.'">'.$inspector_first_name.' '.$inspector_last_name.
									'</a></h3>
										<div class="tester">
											<label class="insp_info">Mobile Phone:&nbsp;&nbsp;&nbsp;</label>'.$inspector_mobile_phone.'<span class="insp_move_links"><a class="insp_move" href="./dashboard.php?add='.$inspector_id.'">Add to "Go-To" list</a> | <a class="insp_delete" href="./dashboard.php?delete='.$inspector_id.'">Delete</a></span></br>
											<label class="insp_info">Email: &nbsp;&nbsp;</label>'.$inspector_email.'</br>
											<label class="insp_info">Postal Code: &nbsp;&nbsp;</label>'.$inspector_zip.'</br>										
											</div>';
				
				//mobile friendly list
				$mobile_master_inspector_list0 = '<h3 class="inspector_list">
										<span class="sign">+</span> <a target="_blank" href="./inspector.php?id='.$inspector_id.'">'.$inspector_first_name.' '.$inspector_last_name.
									'</a></h3>
										<div class="tester" style="text-align:left;">
											<label class="bold">Mobile Phone</label></br>'.$inspector_mobile_phone.'
										</br>
											<label class="bold">Email</label></br>'.$inspector_email.'</br>
											<label class="bold">Postal Code</label></br>'.$inspector_zip.'</br>
											<span class="insp_move_links"><a class="insp_move" href="./dashboard.php?add='.$inspector_id.'">Add to "Go-To" list</a> | <a class="insp_delete" href="./dashboard.php?delete='.$inspector_id.'">Delete</a></span>
										</div>';
				}

		}

$sql=mysql_query("SELECT * from brokersInspectors WHERE broker='$broker_id' AND relationship='1'");
	while ($row = mysql_fetch_assoc($sql)) {
		$inspector_id=$row['inspector'];
		
		
		$query=mysql_query("SELECT * from person WHERE id='$inspector_id'");
			while ($row = mysql_fetch_assoc($query)) {
				$inspector_first_name=$row['first_name'];
				$inspector_last_name=$row['last_name'];
				$inspector_mobile_phone=$row['mobile_phone'];
				$inspector_email=$row['email'];
				$inspector_zip=$row['postal_code'];
				$master_inspector_list1 = '<h3 class="inspector_list">
										<span class="sign">+</span> <a target="_blank" href="./inspector.php?id='.$inspector_id.'">'.$inspector_first_name.' '.$inspector_last_name.
									'</a></h3>
										<div class="tester">
											<label class="insp_info">Mobile Phone:&nbsp;&nbsp;&nbsp;</label>'.$inspector_mobile_phone.'<span class="insp_move_links"><a class="insp_delete" href="./dashboard.php?delete='.$inspector_id.'">Delete</a></span></br>
											<label class="insp_info">Email: &nbsp;&nbsp;</label>'.$inspector_email.'</br>
											<label class="insp_info">Postal Code: &nbsp;&nbsp;</label>'.$inspector_zip.'</br>										
											</div>';
											
				//mobile friendly list
				$mobile_master_inspector_list1 = '<h3 class="inspector_list">
										<span class="sign">+</span> <a target="_blank" href="./inspector.php?id='.$inspector_id.'">'.$inspector_first_name.' '.$inspector_last_name.
									'</a></h3>
										<div class="tester" style="text-align:left;">
											<label class="bold">Mobile Phone</label></br>'.$inspector_mobile_phone.'
										</br>
											<label class="bold">Email</label></br>'.$inspector_email.'</br>
											<label class="bold">Postal Code</label></br>'.$inspector_zip.'</br>
											<a class="insp_delete" href="./dashboard.php?delete='.$inspector_id.'">Delete</a>
										</div>';
				
		
				}

		}
?>
<?php
if (!isset($mobile)){
	if ((empty($master_inspector_list0)) && (empty($master_inspector_list1))) {
		echo 'You do not have any inspectors at this time. Add them below.';
	}
	else {
		echo $master_inspector_list0.$master_inspector_list1;
	}
}
else {
	echo $mobile_master_inspector_list0.$mobile_master_inspector_list1;
}
?>
