<?php
$broker_id=$_SESSION['person']['id'];
$sql=mysql_query("SELECT * from brokersInspectors WHERE broker='$broker_id' AND relationship='1'");
$doublecheck = mysql_num_rows($sql); 
		if($doublecheck > 0) {
			while ($row = mysql_fetch_assoc($sql)) {
				$inspector_id=$row['inspector'];
				
				
				$query=mysql_query("SELECT * from person WHERE id='$inspector_id'");
					while ($row = mysql_fetch_assoc($query)) {
						$inspector_first_name=$row['first_name'];
						$inspector_last_name=$row['last_name'];
						$inspector_mobile_phone=$row['mobile_phone'];
						$inspector_email=$row['email'];
						$inspector_zip=$row['postal_code'];
						$goto_inspector_list = '<h3 class="inspector_list">
												<span class="sign">+</span> <a target="_blank" href="./inspector.php?id='.$inspector_id.'">'.$inspector_first_name.' '.$inspector_last_name.
											'</a></h3>
												<div class="tester">
													<label class="insp_info">Mobile Phone:&nbsp;&nbsp;&nbsp;</label>'.$inspector_mobile_phone.'<a class="insp_remove" href="./dashboard.php?remove='.$inspector_id.'">Remove From "Go-To" List</a>
												</br>
													<label class="insp_info">Email: &nbsp;&nbsp;</label>'.$inspector_email.'</br>
													<label class="insp_info">Postal Code: &nbsp;&nbsp;</label>'.$inspector_zip.'</br>
												</div>';
						//mobile friendly list
						$mobile_goto_inspector_list = '<h3 class="inspector_list">
												<span class="sign">+</span> <a target="_blank" href="./inspector.php?id='.$inspector_id.'">'.$inspector_first_name.' '.$inspector_last_name.
											'</a></h3>
												<div class="tester" style="text-align:left;">
													<label class="bold">Mobile Phone</label></br>'.$inspector_mobile_phone.'
												</br>
													<label class="bold">Email</label></br>'.$inspector_email.'</br>
													<label class="bold">Postal Code</label></br>'.$inspector_zip.'</br>
													<a class="insp_remove" href="./dashboard.php?remove='.$inspector_id.'">Remove From "Go-To" List</a>
												</div>';
						
				
						}
		
				}
				
				if (isset($mobile)) {
					echo $mobile_goto_inspector_list;
				}
				else {
					echo $goto_inspector_list;
				}
				
		}
		else {
			echo 'You do not have any "Go-To" inspectors at this time.';
		}
?>
