<?php
	$broker_id=$_SESSION['person']['id'];
	$group=null;
	//take all from quoteRequess where person = $broker_id
	$query1=mysql_query("SELECT * from property, person, quoteRequest, bid where quoteRequest.person='$broker_id' AND bid.status!='closed' AND bid.status!='open' AND quoteRequest.status='accepted' AND property.id=quoteRequest.property AND bid.quoteRequest=quoteRequest.id AND inspector=person.id");
	$doublecheck = mysql_num_rows($query1); 
		if($doublecheck > 0) {
			echo '<div>';
			while($row1 = mysql_fetch_assoc($query1)) {
				$quoteRequest_id= $row1['id']; 
				//$property_id= $row1['property'];
				$date= $row1['date'];
				$street = $row1['street']; 
				$city = $row1['city']; 
				$state = $row1['state']; 
				$zip = $row1['zip'];
				$bid_inspector = $row1['inspector']; 
				$bid_amount = $row1['bid_amount'];
				$bid_status = $row1['status'];    
				$insp_first_name=$row1['first_name'];
				$insp_last_name=$row1['last_name'];
						
						if ($quoteRequest_id != $group) {
						
							echo '</div><h3 class="inspector_list">
												<span class="sign">+</span> '.$street.' '.$city.', '.$state.', '.$zip.
												'<span class="insp_move_links" style="float:right;">Needed by: '.$date.'</span>
											</h3><div class="tester" style="padding:10px;">';				
						}
	
						
								echo '<b class="bold">Inspector: </b><a target="_blank" href="./inspector.php?id='.$bid_inspector.'">'.$insp_first_name.' '.$insp_last_name.'</a>
								</br>
								<b class="bold">Accepted Quote: </b>$'.$bid_amount.'</br></br>';
								if ($bid_status=='paid'){
									echo '<b class="bold">This inspection was confirmed by the inspector</b><hr /></br>';
								}
								else {
									echo '<b class="bold">Waiting for inspector confirmation...</b><hr /></br>';
								}
								
								$group=$quoteRequest_id;
								
				}
			echo '</div>';
		}
		else {
			echo 'You do not have any past inspections at this time.';
		}
		
?>