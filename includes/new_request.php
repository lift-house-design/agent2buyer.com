<?php
if(!empty($_POST)){
	if (!empty($_POST['requestDateValue'])){

		$date=$_POST['requestDateValue'];
		
		$extra_comment=$_POST['requestCommentValue'];
		
		$street=$_POST['requestStreetValue'];
		$street2 = str_replace(" ", "+", $street);
		
		$city=$_POST['requestCityValue'];
		
		$state=$_POST['requestStateValue'];
		
		$zip=$_POST['requestZipValue'];
		
	//Get property info from zillow
	
		//this URL sends info to API
	    $url = "http://www.zillow.com/webservice/GetDeepSearchResults.htm?zws-id=X1-ZWz1bi2n96dekr_9ej1g&address='$street2'&citystatezip='$zip'";
		//now I get the results
		$xml= file_get_contents($url);	
	
		//now I encode it
		$simpleXml = simplexml_load_string($xml);	
		//$simpleXml = json_encode($simpleXml);
		//echo $simpleXml;
		
	//set variables
	$zpid = (string) $simpleXml->response->results->result[0]->zpid;
	$type = (string) $simpleXml->response->results->result[0]->useCode;
	$yearBuilt = (string) $simpleXml->response->results->result[0]->yearBuilt;
	$bathrooms = (string) $simpleXml->response->results->result[0]->bathrooms;
	$bedrooms = (string) $simpleXml->response->results->result[0]->bedrooms;
	$totalArea = (string) $simpleXml->response->results->result[0]->finishedSqFt;
	
	//Make second call to check updatedPropertyInformation
		$url="http://www.zillow.com/webservice/GetUpdatedPropertyDetails.htm?zws-id=X1-ZWz1bi2n96dekr_9ej1g&zpid=".$zpid;
		//now I get the results
		
		$xml= file_get_contents($url);	
		
		//now I encode it
		$simpleXml = simplexml_load_string($xml);	
			//$simpleXml = json_encode($simpleXml);
			//echo $simpleXml;
		
		//numFloors
		if(!empty($simpleXml->response->editedFacts->numFloors)) {
			$numFloors = (string) $simpleXml->response->editedFacts->numFloors;	
		}
		else $numFloors = "Unknown";
		
		//check numRooms
		if(!empty($simpleXml->response->editedFacts->numRooms)) {
			$numRooms = (string) $simpleXml->response->editedFacts->numRooms;
		}
		else $numRooms = "Unknown";
	
		if (empty($zpid)) {
			echo '<form id="new_request_confirm" action="./pillow.php" method="POST">
					</br>
					<div style="text-align:center;">
						<label class="bold" style="color: #60BDB8;">We could not find this property on Zillow.com</label>
					</div>
					</br>
					<div style="text-align:center;">
						<label  style="color: #60BDB8;">Please provide the details below.</label>
					</div>
					</br>
					<div style="text-align:left;">
						<label class="address">Confirm Date:</label>
					</div>
					<input class="new_request calendar" type="text" name="request_date2" value="'.$date.'" />
						</br>
						</br>
					<div style="text-align:left;">
						<label class="address">Confirm Address of inspection request:</label>
					</div>
					<input class="new_request" type="text" name="request_street2" value="'.$street.'" />
						</br>
					<input class="new_request" type="text" name="request_city2" value="'.$city.'" />
						</br>
					<input class="new_request" type="text" name="request_state2" value="'.$state.'" />
						</br>
					<input class="new_request" type="text" name="request_zip2" value="'.$zip.'" />
						</br>
						</br>
					<div style="text-align:left;">
						<label class="address">Additional Availability Notes:</label>
					</div>
					<textarea class="new_request" type="text" name="extra_comment">'.$extra_comment.'</textarea>
						</br>
						</br>
					<div style="text-align:left;">
						<label class="address">Please Provide Additional Details:</label>
					</div>
					<div style="text-align:left;">
						<label class="address">Year Built</label>
					</div>
					<input class="new_request error" type="text" name="yearBuilt2" value="" />
					<div style="text-align:left;">
						<label class="address">Type</label>
					</div>
					<input class="new_request error" type="text" name="type2" value="" />
					<div style="text-align:left;">
						<label class="address">Bedrooms</label>
					</div>
					<input class="new_request error" type="text" name="bedrooms" value="" />
					<div style="text-align:left;">
						<label class="address">Bathrooms</label>
					</div>
					<input class="new_request error" type="text" name="bathrooms" value="" />
					<div style="text-align:left;">
						<label class="address">Bedrooms</label>
					</div>';
					if($bedrooms>=5)
					{
						$bedrooms='5+';
					}
					elseif(empty($bedrooms))
					{
						$bedrooms=0;
					}
					
					echo '<select'.($bedrooms==0 ? ' class="error"' : '').' name="bedrooms">';
					
					
					$bedroomList=array(0,1,2,3,4,'5+');
					foreach($bedroomList as $val)
						echo '<option'.($bedrooms==$val ? ' selected="selected"' : '') .'>'.$val.'</option>';
						
					echo '</select>
					
					
					<div style="text-align:left;">
						<label class="address">Bathrooms</label>
					</div>';
					if($bathrooms>=5)
					{
						$bathrooms='5+';
					}
					elseif(empty($bathrooms))
					{
						$bathrooms=0;
					}
					
					echo '<select'.($bathrooms==0 ? ' class="error"' : '').' name="$bathrooms">';
					
					
					$bathroomList=array(0,1.5,2.0,2.5,3.0,3.5,4.0,4.5,'5+');
					foreach($bathroomList as $val)
						echo '<option'.($bathrooms==$val ? ' selected="selected"' : '') .'>'.$val.'</option>';
						
					echo '</select>
					<div style="text-align:left;">
						<label class="address">Total Area</label>
					</div>
					<input class="new_request error" type="text" name="totalArea" value="" />
					<div style="text-align:left;">
						<label class="address">Total # of Rooms</label>
					</div>
					<input class="new_request error" type="text" name="numRooms2" value="" />
					
					<div style="text-align:left; padding:5px 0px 5px 0px;">
						<label class="address">Stories</label></br>	
					</div>	
					<select class="error" id="numFloors2">
						<option value="Unknown">Unknown</option>
						<option value="1">1</option>
						<option value="1.5">1.5</option>
						<option value="1.75">1.75</option>
						<option value="2">2</option>
						<option value="2.5">2.5</option>
						<option value="2.75">2.75</option>
						<option value="3">3</option>
						<option value="3.5">3.5</option>
						<option value="3.75">3.75</option>
						<option value="4">4</option>
						<option value="5">5+</option>
					</select>
					
					
					<div style="text-align:left; padding:5px 0px 5px 0px;">
						<label class="address">Foundation</label>
					</div>
					<select class="error" id="foundation">
						<option value="Unknown">Unknown</option>
						<option value="Slab">Slab</option>
						<option value="Pier & Beam">Pier & Beam</option>
						<option value="Basement">Basement</option>
						<option value="Crawl Space">Crawl Space</option>
					</select>
					
					<div style="text-align:left; padding:5px 0px 5px 0px;">
						<label class="address">Attached Garage</label>
					</div>
					<select class="error" id="garage1">
						<option value="None">None</option>
						<option value="1">1 car</option>
						<option value="2">2 cars</option>
						<option value="3">3 cars</option>
						<option value="4">4 cars</option>
						<option value="5">5+ cars</option>
					</select>
					
					<div style="text-align:left; padding:5px 0px 5px 0px;">
						<label class="address">Detached Garage</label>
					</div>
					<select class="error" id="garage2">
						<option value="None">None</option>
						<option value="1">1 car</option>
						<option value="2">2 cars</option>
						<option value="3">3 cars</option>
						<option value="4">4 cars</option>
						<option value="5">5+ cars</option>
					</select>
					
					<div style="text-align:left; padding:5px 0px 5px 0px;">
						<label class="address">Waste System</label>
					</div>
					<select class="error" id="waste_system">
						<option value="Unknown">Unknown</option>
						<option value="City Sewer">City Sewer</option>
						<option value="Septic">Septic</option>
					</select>
					</br>
					</br>
					<div style="text-align:left;">
						<label class="address">Optional Reports</label></br>
					</div>
					</br>
					<div>
					Pool <input type="checkbox" id="pool" name="pool" />&nbsp;&nbsp;
					Hot Tub/Spa <input type="checkbox" id="hottub" name="hottub" />&nbsp;&nbsp;
					Pier & Beam <input type="checkbox" id="pier_and_beam" name="pier_and_beam" /></br></br>
					Radon <input type="checkbox" id="radon" name="radon" />&nbsp;&nbsp;
					Sprinkler <input type="checkbox" id="sprinkler" name="sprinkler" />&nbsp;&nbsp;
					Termite <input type="checkbox" id="termite" name="termite" />
					</div>
					</br>
					<div style="text-align:left;">
						<label class="address">Client Information</label></br>
					</div>
					</br>
					<div style="text-align:left;">
						<label style="width:330px;font-weight:normal;" class="address"><input type="checkbox" id="copy_to_client" name="copy_to_client" />  Click here to forward all inspector quote responses to your client</label>
					</div>
					</br>
					<div id="client_info" style="display: none;">
						<input class="new_request" type="text" name="client_first_name" placeholder="Client\'s First Name" />
						</br>
						<input class="new_request" type="text" name="client_last_name" placeholder="Client\'s Last Name" />
						</br>
						<input class="" name="client_mobile_phone" autocomplete="off" id="phone" onkeydown="javascript:backspacerDOWN(this,event);" onkeyup="javascript:backspacerUP(this,event);" type="text" placeholder="* Client\'s Mobile Phone" />
						<input name="client_text_capable" checked="checked" type="checkbox" value="1" id="text_capable"/> This phone is text capable
						</br>
						<input class="new_request" type="text" name="client_email" placeholder="Client\'s Email Address" />
						</br>
					</div>
					<input style="margin-left:300px;"class="request_confirm_button2" type="submit" name="new_request_confirm" value="Submit" />
				  </form>
				  <div id="request_final">
				  </div>';
			exit;		
		}
		
		else {
			//add in date-picker to confirm date
			//highlight empty fields
			//validate to require all fields have been filled in
			echo '<form id="new_request_confirm" action="./pillow.php" method="POST">
					</br>
					<div style="text-align:center;">
						<label class="bold" style="color: #60BDB8;">The property information associated with this address has been found on Zillow.</label>
					</div>
					</br>
					<div style="text-align:center;">
						<label  style="color: #60BDB8;">Please confirm all information below, and enter any additional property details.</label>
					</div>
					</br>
					<div style="text-align:left;">
						<label class="address">Confirm Date:</label>
					</div>
					<input class="new_request" type="text" name="request_date2" value="'.$date.'" />
						</br>
						</br>
					<div style="text-align:left;">
						<label class="address">Confirm Address of inspection request:</label>
					</div>
					<input class="new_request" type="text" name="request_street2" value="'.$street.'" />
						</br>
					<input class="new_request" type="text" name="request_city2" value="'.$city.'" />
						</br>
					<input class="new_request" type="text" name="request_state2" value="'.$state.'" />
						</br>
					<input class="new_request" type="text" name="request_zip2" value="'.$zip.'" />
						</br>
						</br>
					<div style="text-align:left;">
						<label class="address">Additional Availability Notes:</label>
					</div>
					<textarea class="new_request" type="text" name="extra_comment">'.$extra_comment.'</textarea>
						</br>
						</br>
					<div style="text-align:left;">
						<label class="address">Please Confirm Details:</label>
					</div>
					<div style="text-align:left;">
						<label class="address">Year Built</label>
					</div>
					';
					echo '<input class="new_request'.(empty($yearBuilt) ? ' error' : '').'" type="text" name="yearBuilt2" value="'.$yearBuilt.'" />
				
					<div style="text-align:left;">
						<label class="address">Type</label>
					</div>';
					
					echo '<select'.(empty($type) ? ' class="error"' : '').' name="type2">';
					
					
					$typeList=array('SingleFamily', 'Duplex', 'Triplex', 'Quadruplex', 'Condominium', 'Cooperative', 'Mobile', 'Multi-Family 2 to 4', 'Multi-Family 5 plus', 'timeshare');
					foreach($typeList as $val)
						echo '<option'.($type==$val ? ' selected="selected"' : '') .'>'.$val.'</option>';
						
					echo '</select>
					
					
					<div style="text-align:left;">
						<label class="address">Bedrooms</label>
					</div>';
					if($bedrooms>=5)
					{
						$bedrooms='5+';
					}
					elseif(empty($bedrooms))
					{
						$bedrooms=0;
					}
					
					echo '<select'.($bedrooms==0 ? ' class="error"' : '').' name="bedrooms">';
					
					
					$bedroomList=array(0,1,2,3,4,'5+');
					foreach($bedroomList as $val)
						echo '<option'.($bedrooms==$val ? ' selected="selected"' : '') .'>'.$val.'</option>';
						
					echo '</select>
					
					
					<div style="text-align:left;">
						<label class="address">Bathrooms</label>
					</div>';
					if($bathrooms>=5)
					{
						$bathrooms='5+';
					}
					elseif(empty($bathrooms))
					{
						$bathrooms=0;
					}
					
					echo '<select'.($bathrooms==0 ? ' class="error"' : '').' name="$bathrooms">';
					
					
					$bathroomList=array(0,1.5,2.0,2.5,3.0,3.5,4.0,4.5,'5+');
					foreach($bathroomList as $val)
						echo '<option'.($bathrooms==$val ? ' selected="selected"' : '') .'>'.$val.'</option>';
						
					echo '</select>
					
					
					
					<div style="text-align:left;">
						<label class="address">Total Area</label>
					</div>
					<input class="new_request" type="text" name="totalArea" value="'.$totalArea.'" />
					<div style="text-align:left;">
						<label class="address">Total # of Rooms</label>
					</div>
					<input class="new_request" type="text" name="numRooms2" value="'.$numRooms.'" />
					
					<div style="text-align:left; padding:5px 0px 5px 0px;">
						<label class="address">Stories</label></br>	
					</div>	
					<select id="numFloors2">
						<option value="Unknown">Unknown</option>
						<option value="1">1</option>
						<option value="1.5">1.5</option>
						<option value="1.75">1.75</option>
						<option value="2">2</option>
						<option value="2.5">2.5</option>
						<option value="2.75">2.75</option>
						<option value="3">3</option>
						<option value="3.5">3.5</option>
						<option value="3.75">3.75</option>
						<option value="4">4</option>
						<option value="5">5+</option>
					</select>
					
					
					<div style="text-align:left; padding:5px 0px 5px 0px;">
						<label class="address">Foundation</label>
					</div>
					<select id="foundation">
						<option value="Unknown">Unknown</option>
						<option value="Slab">Slab</option>
						<option value="Pier & Beam">Pier & Beam</option>
						<option value="Basement">Basement</option>
						<option value="Crawl Space">Crawl Space</option>
					</select>
					
					<div style="text-align:left; padding:5px 0px 5px 0px;">
						<label class="address">Attached Garage</label>
					</div>
					<select id="garage1">
						<option value="None">None</option>
						<option value="1">1 car</option>
						<option value="2">2 cars</option>
						<option value="3">3 cars</option>
						<option value="4">4 cars</option>
						<option value="5">5+ cars</option>
					</select>
					
					<div style="text-align:left; padding:5px 0px 5px 0px;">
						<label class="address">Detached Garage</label>
					</div>
					<select id="garage2">
						<option value="None">None</option>
						<option value="1">1 car</option>
						<option value="2">2 cars</option>
						<option value="3">3 cars</option>
						<option value="4">4 cars</option>
						<option value="5">5+ cars</option>
					</select>
					
					<div style="text-align:left; padding:5px 0px 5px 0px;">
						<label class="address">Waste System</label>
					</div>
					<select id="waste_system">
						<option value="Unknown">Unknown</option>
						<option value="City Sewer">City Sewer</option>
						<option value="Septic">Septic</option>
					</select>
					</br>
					</br>
					<div style="text-align:left;">
						<label class="address">Optional Reports</label></br>
					</div>
					</br>
					<div>
					Pool <input type="checkbox" id="pool" name="pool" />&nbsp;&nbsp;
					Hot Tub/Spa <input type="checkbox" id="hottub" name="hottub" />&nbsp;&nbsp;
					Pier & Beam <input type="checkbox" id="pier_and_beam" name="pier_and_beam" /></br></br>
					Radon <input type="checkbox" id="radon" name="radon" />&nbsp;&nbsp;
					Sprinkler <input type="checkbox" id="sprinkler" name="sprinkler" />&nbsp;&nbsp;
					Termite <input type="checkbox" id="termite" name="termite" />
					</div>
					</br>
					<div style="text-align:left;">
						<label class="address">Client Information</label></br>
					</div>
					</br>
					<div style="text-align:left;">
						<label style="width:330px; font-weight:normal;" class="address"><input type="checkbox" id="copy_to_client" name="copy_to_client" />  Click here to forward all inspector quote responses to your client</label>
					</div>
					</br>
					<div id="client_info" style="display: none;">
						<input class="new_request" type="text" name="client_first_name" placeholder="Client\'s First Name" />
						</br>
						<input class="new_request" type="text" name="client_last_name" placeholder="Client\'s Last Name" />
						</br>
						<input class="" name="client_mobile_phone" autocomplete="off" id="phone" onkeydown="javascript:backspacerDOWN(this,event);" onkeyup="javascript:backspacerUP(this,event);" type="text" placeholder="* Client\'s Mobile Phone" />
						<input name="client_text_capable" checked="checked" type="checkbox" value="1" id="client_text_capable"/> This phone is text capable
						</br>
						<input class="new_request" type="text" name="client_email" placeholder="Client\'s Email Address" />
						</br>
					</div>
					<input style="margin-left:300px;"class="request_confirm_button2" type="submit" name="new_request_confirm" value="Submit" />
				  </form>
				  <div id="request_final">
				  </div>';
			exit;
		}
	}
	elseif (isset($_POST['requestDateValue2'])) {
		include './functions.php';
		$date2=mysql_real_escape_string($_POST['requestDateValue2']);
		$street2=mysql_real_escape_string($_POST['requestStreetValue2']);
		$city2=mysql_real_escape_string($_POST['requestCityValue2']);
		$state2=mysql_real_escape_string($_POST['requestStateValue2']);
		$zip2=mysql_real_escape_string($_POST['requestZipValue2']);
		$extra_comment=mysql_real_escape_string($_POST['extraCommentValue']);
		$yearBuilt2=mysql_real_escape_string($_POST['yearBuiltValue2']);
		$type2=mysql_real_escape_string($_POST['typeValue2']);
		$bedrooms2=mysql_real_escape_string($_POST['bedroomsValue2']);
		$bathrooms2=mysql_real_escape_string($_POST['bathroomsValue2']);
		$totalArea2=mysql_real_escape_string($_POST['totalAreaValue2']);
		$numRooms2=mysql_real_escape_string($_POST['numRoomsValue2']);
		$numFloors2=mysql_real_escape_string($_POST['numFloorsValue2']);
		$foundation=mysql_real_escape_string($_POST['foundationValue']);
		$garage1=mysql_real_escape_string($_POST['garage1Value']);
		$garage2=mysql_real_escape_string($_POST['garage2Value']);
		$waste_system=mysql_real_escape_string($_POST['wasteSystemValue']);
	//checkbox values
		$pool=mysql_real_escape_string($_POST['poolValue']);
		$hottub=mysql_real_escape_string($_POST['hottubValue']);
		$pier_and_beam=mysql_real_escape_string($_POST['pier_and_beamValue']);
		$radon=mysql_real_escape_string($_POST['radonValue']);
		$sprinkler=mysql_real_escape_string($_POST['sprinklerValue']);
		$termite=mysql_real_escape_string($_POST['termiteValue']);
		$copy_to_client=mysql_real_escape_string($_POST['copyToClientValue']);
	
	//client info
		$client_first_name=mysql_real_escape_string($_POST['clientFirstValue']);
		$client_last_name=mysql_real_escape_string($_POST['clientLastValue']);
		$client_mobile_phone=mysql_real_escape_string($_POST['clientMobileValue']);
		$client_text_capable=mysql_real_escape_string($_POST['clientTextValue']);
		$client_email=mysql_real_escape_string($_POST['clientEmailValue']);
	//Connect to database, start session
			
		$broker_id = $_SESSION['person']['id'];
		
	//now add this property to database
		$query = mysql_query("INSERT into property (street, city, state, zip, 
			yearBuilt, type, numFloors, foundation, totalArea, numRooms, bedrooms, bathrooms, garage_attached, garage_detached, citySewer, 
			pool, hottub, pier_and_beam, radon, sprinkler, termite) 
			VALUES ('$street2', '$city2', '$state2', '$zip2', 
			'$yearBuilt2', '$type2', '$numFloors2', '$foundation', '$totalArea2', '$numRooms2', '$bedrooms2', '$bathrooms2', '$garage1', '$garage2', '$waste_system',
			'$pool', '$hottub', '$pier_and_beam', '$radon', '$sprinkler', '$termite')");		
	
	//get the id of this property
		$property_id = mysql_insert_id();
		
	//now create the inspection request for this property in the database
		$query = mysql_query("INSERT into quoteRequest (person, property, submitted, date, extra_comment, status) VALUES ('$broker_id', '$property_id', now(), '$date2', '$extra_comment', 'pending')");
		
	//get the id of this Quote Request
		$request_id = mysql_insert_id();

			echo '<form id="send-request-form" action="./send_request.php?request='.$request_id.($copy_to_client == '1' ? '&client=true' : '').'" method="POST">
				 		<div style="text-align:center;">
							<label style="color: #60BDB8;">Thank you, the property information for '.$street2.' has been saved.</label>
						</div>
						<div style="text-align:center;">
							<label style="color: #60BDB8;">Your request is being processed, please wait...</label>
						</div>
						<div id="client_info" style="display: none;">
							<input class="new_request" type="text" name="client_first_name" value="'.$client_first_name.'" />
							</br>
							<input class="new_request" type="text" name="client_last_name" value="'.$client_last_name.'" />
							</br>
							<input class="" name="client_mobile_phone" autocomplete="off" id="phone" onkeydown="javascript:backspacerDOWN(this,event);" onkeyup="javascript:backspacerUP(this,event);" type="text" value="'.$client_mobile_phone.'" />
							<input name="client_text_capable" type="checkbox" value="1" id="client_text_capable"'.( !empty($client_text_capable) ? ' checked="checked"' : '' ).' /> This phone is text capable
							</br>
							<input class="new_request" type="text" name="client_email" value="'.$client_email.'" />
							</br>
						</div>
						
				</form>
				<script>
					setTimeout(function(){
						$("#send-request-form").submit();
					},1000);
				</script>
				';
		exit;
	}
	else exit;
}

?>
<?php
//find user state based on zipcode
$broker_postal_code=$_SESSION['person']['postal_code'];
$sql=mysql_query("SELECT * FROM zip_code WHERE zip_code='$broker_postal_code'");
while ($row = mysql_fetch_assoc($sql)) {
	$state_name=$row['state_name'];
}
?>
	<form style="background: #F0EADD; padding:15px;" id= "new_request" action="./pillow.php" method="POST">
		<div style="text-align:left;">
			<label class="address">Date:</label>
		</div>
		<input type="text" class="new_request calendar" name="request_date"  placeholder="When do you need this inspection completed by?"/>
			</br>
			</br>
		<div style="text-align:left;">
			<label class="address">Additional Availability Comment:</label>
		</div>
		<textarea class="new_request" type="text" name="request_comment"></textarea>
			</br>
			</br>
		<div style="text-align:left;">
			<label class="address">Address of inspection request:</label>
		</div>
		<input class="new_request" type="text" name="request_street" placeholder="Street Address" />
			</br>
		<input class="new_request" type="text" name="request_city" placeholder="City" />
			</br>
		<select class="new_request" id="request_state" name="request_state">
					<?php foreach($state_list as $abbr=>$state): ?>
						<?php echo '<option'. ($state_name==$state ? ' selected="selected"' : '') .' value='.$abbr .'>' .$state.'</option>'; ?>
					<?php endforeach; ?>
		</select>
			</br>
		<input class="new_request" type="text" name="request_zip" placeholder="Postal Code" />
			</br>
		<input style="margin-left:300px;"class="new_request_button" type="submit" name="new_request" value="submit" />
	</form>
	<a id="new_request_anchor"></a>
	<div id="request_confirmation">
	</div>
<script>
	$(document)
		.on('change','#copy_to_client',function(){
			var isChecked=$(this).eq(0)[0].checked;
			
			if (isChecked) {
				$('#client_info').show();
			} else {
				$('#client_info').hide();
			}
		});
</script>
