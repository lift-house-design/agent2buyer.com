<?php
	$broker_id=$_SESSION['person']['id'];
	$group=null;
	//take all from quoteRequess where person = $broker_id
	//$query1=mysql_query("SELECT * from property, person, bid, quoteRequest where quoteRequest.person='$broker_id' AND quoteRequest.status='pending' AND property.id=quoteRequest.property AND bid.quoteRequest=quoteRequest.id AND bid.inspector=person.id");
	$query1=mysql_query("SELECT * from property, person, quoteRequest where quoteRequest.person='$broker_id' AND quoteRequest.person=person.id AND quoteRequest.status='pending' AND property.id=quoteRequest.property");
	$doublecheck = mysql_num_rows($query1); 
		if($doublecheck > 0) {
			while($row1 = mysql_fetch_assoc($query1)) {
				$quoteRequest_id= $row1['id']; 
				//$property_id= $row1['property'];
				$date= $row1['date'];
				$street = $row1['street']; 
				$city = $row1['city']; 
				$state = $row1['state']; 
				$zip = $row1['zip'];

				
				$query2=mysql_query("SELECT * FROM person, bid where bid.quoteRequest=".$quoteRequest_id." AND bid.inspector=person.id");
				$hasBids=mysql_num_rows($query2)>0;
				
				echo '<h3 class="inspector_list">';

				echo '<span class="sign">+</span> ';
				
				echo 	$street.' '.$city.', '.$state.', '.$zip.
					 	'</br><span class="insp_move_links">Needed by: '.$date.'</span>
					  </h3>';
				
				
				if($hasBids)
				{
					echo '<div class="tester" style="padding:10px;">';
					
					while($row2=mysql_fetch_assoc($query2))
					{
						$insp_id = $row2['inspector']; 
						$insp_first_name=$row2['first_name'];
						$insp_last_name=$row2['last_name'];
						$bid_amount = $row2['bid_amount']; 
						
						echo 	'<b class="bold">Inspector: </b><a target="_blank" href="./inspector.php?id='.$insp_id.'">'.$insp_first_name.' '.$insp_last_name.'</a>
								
								</br>
								<b class="bold">Quote Amount: </b>$'.$bid_amount.'<span class="test" style="height:10px;"><input type="hidden" name="accept_quote" value="'.$insp_id.'" /><input class="accept_quote_button" type="submit" value="+ Accept Quote" name="accept" /></span></br></br></br><hr /></br>
								';
					}
					
				}
				else
				{
					echo '<div class="tester" style="padding:10px;">This inspection has not received any quotes yet.';
				}
				
				echo '</div>';
				/*echo '';
						//if ($quoteRequest_id != $group) {
						
											
						//}
	
						
								
								$group=$quoteRequest_id;*/
								
				}
		}
		else {
			echo 'You do not have any pending inspections at this time.';
		}
				
?>