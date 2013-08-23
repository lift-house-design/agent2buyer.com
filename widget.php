<?php

	/*function showError($err)
	{
		
	}
	
	if(empty($_GET['token']))
	{
		echo ''
	}
	$token=$_GET['token'];*/
	
	require('includes/functions.php');
	
	function set_error($error)
	{
		$_SESSION['widget_error']=$error;
		
		/*$step=isset($_GET['step']) ? $_GET['step'] : 1;
		if(--$step==0)
			$step=1;
		$_SESSION['widget_session']['step']=$step;*/
		header('Location: widget.php');
	}
	
	function get_error()
	{
		if(!empty($_SESSION['widget_error']))
		{
			$error=$_SESSION['widget_error'];
			unset($_SESSION['widget_error']);
			return $error;
		}
		
		return false;
	}
	
	function get_brokers($company_id)
	{
		$sql='
			select
				person.id,
				person.first_name,
				person.last_name
			from
				person,
				broker
			where
				broker.company_id = '.$company_id.' and
				broker.person = person.id
		';
		$r=mysql_query($sql) or die(mysql_error());
		$brokers=array();
		while($row=mysql_fetch_assoc($r))
			$brokers[]=$row;
		
		return $brokers;
	}
	
	function get_zillow_data($street_address,$zip)
	{
		$url = "http://www.zillow.com/webservice/GetDeepSearchResults.htm?zws-id=X1-ZWz1bi2n96dekr_9ej1g&address=".urlencode($street_address)."&citystatezip=".urlencode($zip);
		$xml= file_get_contents($url);	
		$simpleXml = simplexml_load_string($xml);
		
		$status_code=(string)$simpleXml->message->code;
		if($status_code!=0)
			return false;
		
		$result=$simpleXml->response->results->result[0];
		
		$zillowData=array(
			'zpid'=>(string) $result->zpid,
			'type'=>(string) $result->useCode,
			'year_built'=>(string) $result->yearBuilt,
			'bathrooms'=>(string) $result->bathrooms,
			'bedrooms'=>(string) $result->bedrooms,
			'total_area'=>(string) $result->finishedSqFt,
			// Extra Data
			'floors'=>'Unknown',
			'roof'=>'Unknown',
			'exterior'=>'Unknown',
			'cooling'=>'Unknown',
			'floor_covering'=>'Unknown',
			// Placeholders for unknown data
			'foundation'=>'Unknown',
			'attached_garage'=>'Unknown',
			'detached_garage'=>'Unknown',
			'waste_system'=>'Unknown',
		);
		
		$url="http://www.zillow.com/webservice/GetUpdatedPropertyDetails.htm?zws-id=X1-ZWz1bi2n96dekr_9ej1g&zpid=".urlencode($zillowData['zpid']);
		$xml= file_get_contents($url);	
		$simpleXml = simplexml_load_string($xml);
		
		$status_code=(string)$simpleXml->message->code;
		if($status_code==0)
		{
			$result=$simpleXml->response->editedFacts;
			
			$extraData=array(
				'floors'=>			empty($result->numFloors) ? 'Unknown' : (string)$result->numFloors,
				'roof'=>			empty($result->roof) ? 'Unknown' : (string)$result->roof,
				'exterior'=>		empty($result->exteriorMaterial) ? 'Unknown' : (string)$result->exteriorMaterial,
				'cooling'=>			empty($result->coolingSystem) ? 'Unknown' : (string)$result->coolingSystem,
				'floor_covering'=>	empty($result->floorCovering) ? 'Unknown' : (string)$result->floorCovering,
			);
			
			$zillowData=array_merge($zillowData,$extraData);
		}
		
		return $zillowData;
	}
	
	function insert_property()
	{
		$sql='
			insert into
				property
				(
					street,
					city,
					state,
					zip,
					yearBuilt,
					type,
					numFloors,
					foundation,
					totalArea,
					numRooms,
					bedrooms,
					bathrooms,
					garage_attached,
					garage_detached,
					citySewer,
					pool,
					hottub,
					pier_and_beam,
					radon,
					sprinkler,
					termite
				)
				values
				(
					"'.mysql_real_escape_string($_SESSION['widget_session']['address']['address']).'",
					"'.mysql_real_escape_string($_SESSION['widget_session']['address']['city']).'",
					"'.mysql_real_escape_string($_SESSION['widget_session']['address']['state']).'",
					"'.mysql_real_escape_string($_SESSION['widget_session']['address']['zip']).'",
					"'.mysql_real_escape_string($_SESSION['widget_session']['details']['year_built']).'",
					"'.mysql_real_escape_string($_SESSION['widget_session']['details']['type']).'",
					"'.mysql_real_escape_string($_SESSION['widget_session']['details']['floors']).'",
					"'.mysql_real_escape_string($_SESSION['widget_session']['details']['foundation']).'",
					"'.mysql_real_escape_string($_SESSION['widget_session']['details']['total_area']).'",
					"'.mysql_real_escape_string($_SESSION['widget_session']['details']['bedrooms']).'",
					"'.mysql_real_escape_string($_SESSION['widget_session']['details']['bedrooms']).'",
					"'.mysql_real_escape_string($_SESSION['widget_session']['details']['bathrooms']).'",
					"'.mysql_real_escape_string($_SESSION['widget_session']['details']['attached_garage']).'",
					"'.mysql_real_escape_string($_SESSION['widget_session']['details']['detached_garage']).'",
					"'.mysql_real_escape_string($_SESSION['widget_session']['details']['waste_system']).'",
					"'.(in_array('pool',$_SESSION['widget_session']['details']['optional_reports']) ? 1 : 0).'",
					"'.(in_array('hot_tub',$_SESSION['widget_session']['details']['optional_reports']) ? 1 : 0).'",
					"'.(in_array('pier_beam',$_SESSION['widget_session']['details']['optional_reports']) ? 1 : 0).'",
					"'.(in_array('radon',$_SESSION['widget_session']['details']['optional_reports']) ? 1 : 0).'",
					"'.(in_array('sprinkler',$_SESSION['widget_session']['details']['optional_reports']) ? 1 : 0).'",
					"'.(in_array('termite',$_SESSION['widget_session']['details']['optional_reports']) ? 1 : 0).'"
				)
		';
		mysql_query($sql) or die(mysql_error());
		
		return mysql_insert_id();	
	}
	
	function insert_quote_request($property_id,$broker_id)
	{
		$sql='
			insert into
				quoteRequest
				(
					person,
					property,
					submitted,
					date,
					extra_comment,
					status
				)
				values
				(
					'.$broker_id.',
					'.$property_id.',
					now(),
					"'.date('Y-m-d',strtotime($_SESSION['widget_session']['complete_by'])).'",
					"'.mysql_real_escape_string($_SESSION['widget_session']['availability_notes']).'",
					"pending"
				)
		';
		mysql_query($sql) or die(mysql_error());
		
		return mysql_insert_id();
	}
	
	function send_request($broker_id,$quote_request_id)
	{
		$url='https://www.locizzle.com/send_request.php?request='.$quote_request_id.'&client=true&person='.$broker_id;
		$postData=array(
			'client_first_name'=>$_SESSION['widget_session']['contact_info']['first_name'],
			'client_last_name'=>$_SESSION['widget_session']['contact_info']['last_name'],
			'client_email'=>$_SESSION['widget_session']['contact_info']['email'],
			'client_mobile_phone'=>$_SESSION['widget_session']['contact_info']['mobile'],
			'client_text_capable'=>$_SESSION['widget_session']['contact_info']['text_capable'] ? 1 : 0,
		);
		$postString='';
		foreach($postData as $k=>$v)
			$postString.=$k.'='.$v.'&';
		rtrim($postString,'&');
		
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, count($postData));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $postString);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		$ch_result = curl_exec($ch);
		curl_close($ch);
	}
	
	if(empty($_SESSION['widget_session']))
	{
		$_SESSION['widget_session']=array(
			'step'=>1,
			// Step 1
			'contact_info'=>array(),
			'complete_by'=>'',
			// Step 2
			'address'=>array(),
			'availability_notes'=>'',
			// Step 3
			'details'=>array(),
		);
	}
	
	/*if(empty($_GET['step']) || !is_numeric($_GET['step']))
		$step=1;
	else
		$step=$_GET['step'];*/
	
	//$company_id=14;
	//$broker_id=214; // Person ID
	
	if(isset($_GET['person_id']))
		$_SESSION['widget_person_id']=$_GET['person_id'];
	$broker_id=$_SESSION['widget_person_id'];
	if(isset($_GET['company_id']))
		$_SESSION['widget_company_id']=$_GET['company_id'];
	$company_id=$_SESSION['widget_company_id'];
	ob_start();
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="/css/reset.css" />
		<link rel="stylesheet" href="/css/widget.css" />
	</head>
	<body>
	<?php if($error=get_error()): ?>
		<div class="error"><?php echo $error ?></div>
	<?php endif; ?>
<?php
	switch($_SESSION['widget_session']['step'])
	{
		case 1:
			if(!empty($_POST))
			{
				if(empty($_POST['first_name']))
					set_error('We need at least your first name.');
					
				if(empty($_POST['email']) && ( empty($_POST['mobile']) || empty($_POST['text_capable']) ))
					set_error('You must provide either your e-mail address or a text capable mobile phone number.');
				
				$_SESSION['widget_session']['contact_info']=array(
					'first_name'=>$_POST['first_name'],
					'last_name'=>$_POST['last_name'],
					'email'=>$_POST['email'],
					'mobile'=>$_POST['mobile'],
					'text_capable'=>empty($_POST['text_capable']) ? false : true,
				);
				
				if(empty($_POST['complete_by']))
					set_error('You must tell us when you need the inspection completed by.');
				
				$_SESSION['widget_session']['complete_by']=$_POST['complete_by'];
				$_SESSION['widget_session']['step']=2;
				
				header('Location: widget.php');
			}
		?>
<form method="post" action="widget.php">
	<div class="widget-section">
		<div class="label">Step 1 of 2: Provide your contact information.</div>
		<input type="text" name="first_name" placeholder="First Name" class="half" />
		<input type="text" name="last_name" placeholder="Last Name" class="half" /><br />
		<input type="text" name="email" placeholder="E-mail Address" class="full" /><br />
		<input type="text" name="mobile" placeholder="Mobile Phone" class="half" />
		<input type="checkbox" name="text_capable" /> Text Capable
	</div>
	<div class="widget-section">
		<div class="label">Step 2 of 2: When do you need the inspection completed by?</div>
		<input type="text" name="complete_by" class="full" />
	</div>
	<div class="widget-buttons">
		<input type="submit" value="Continue" />
	</div>
	<script src="/js/jquery-1.10.1.min.js"></script>
	<script src="/js/jquery-ui-1.10.3.min.js"></script>
	<link rel="stylesheet" href="/css/smoothness/jquery-ui-1.10.3.css" />
	<script>
	$(function(){
		$('input[name="complete_by"]').datepicker();
	});
	</script>
</form>
		<?php
			break;
		case 2:
			if(!empty($_POST))
			{
				if(empty($_POST['address']) || empty($_POST['zip']))
					set_error('You must enter the address and zip code of the location you would like inspected.');
				
				$_SESSION['widget_session']['address']=array(
					'address'=>$_POST['address'],
					'city'=>$_POST['city'],
					'state'=>$_POST['state'],
					'zip'=>$_POST['zip'],
				);
				$_SESSION['widget_session']['availability_notes']=$_POST['availability_notes'];
				$_SESSION['widget_session']['step']=3;
				
				header('Location: widget.php');
			}
		?>
<form method="post" action="widget.php">
	<div class="widget-section">
		<div class="label">Step 2 of 2 (cont.): What address do you need inspected?</div>
		<input type="text" name="address" placeholder="Street Address" class="full" /><br />
		<input type="text" name="city" placeholder="City" class="third" />
		<select name="state" class="third">
			<option value="">State</option>
		<?php foreach($state_list as $abbr=>$state): ?>
			<option value="<?php echo $abbr ?>"><?php echo $state ?></option>
		<?php endforeach; ?>
		</select>
		<input type="text" name="zip" placeholder="Zip" class="third" />
	</div>
	<div class="widget-section">
		<div class="label">Anything else the home inspector should know, such as the best time of day?</div>
		<textarea name="availability_notes" class="full" placeholder="Availability Notes"></textarea>
	</div>
	<div class="widget-buttons">
		<input type="submit" value="Submit &amp; Confirm Details" />
	</div>
</form>
		<?php
			break;
		case 3:
			if(!empty($_POST))
			{
				$_SESSION['widget_session']['details']=$_POST;
				$_SESSION['widget_session']['step']=4;
				
				header('Location: widget.php');
			}
			
			$details=array(
				'type'=>array(
					'label'=>'Type',
					'field'=>'select',
					'options'=>array(
						'Unknown',
						'SingleFamily',
						'Duplex',
						'Triplex',
						'Quadruplex',
						'Condominium',
						'Cooperative',
						'Mobile',
						'Multi-Family 2 to 4',
						'Multi-Family 5 plus',
						'timeshare',
					),
				),
				'year_built'=>array(
					'label'=>'Year Built',
					'field'=>'text',
				),
				'bedrooms'=>array(
					'label'=>'Bedrooms',
					'field'=>'select',
					'options'=>array(
						'Unknown',
						'0',
						'1',
						'2',
						'3',
						'4',
						'5+',
					),
				),
				'bathrooms'=>array(
					'label'=>'Bathrooms',
					'field'=>'select',
					'options'=>array(
						'Unknown',
						'0',
						'1',
						'1.5',
						'2',
						'2.5',
						'3',
						'3.5',
						'4',
						'4.5',
						'5+',
					),
				),
				'floors'=>array(
					'label'=>'Floors',
					'field'=>'select',
					'options'=>array(
						'Unknown',
						'1',
						'2',
						'3+',
					),
				),
				'total_area'=>array(
					'label'=>'Total Area (sq. ft.)',
					'field'=>'text',
				),
				'foundation'=>array(
					'label'=>'Foundation',
					'field'=>'select',
					'options'=>array(
						'Unknown',
						'Slab',
						'Pier & Beam',
						'Basement',
						'Crawl Space',
					),
				),
				'attached_garage'=>array(
					'label'=>'Attached Garage',
					'field'=>'select',
					'options'=>array(
						'Unknown',
						'1 Car',
						'2 Cars',
						'3 Cars',
						'4 Cars',
						'5+ Cars',
					),
				),
				'detached_garage'=>array(
					'label'=>'Detached Garage',
					'field'=>'select',
					'options'=>array(
						'Unknown',
						'1 Car',
						'2 Cars',
						'3 Cars',
						'4 Cars',
						'5+ Cars',
					),
				),
				'waste_system'=>array(
					'label'=>'Waste System',
					'field'=>'select',
					'options'=>array(
						'Unknown',
						'City Sewer',
						'Septic',
					),
				),
			);
			
			
			$zillowData=get_zillow_data($_SESSION['widget_session']['address']['address'],$_SESSION['widget_session']['address']['zip']);
			if($zillowData===false)
			{
				$newZillowData=array();
				foreach(array_keys($details) as $detail_key)
					$newZillowData[$detail_key]='Unknown';
				$zillowData=$newZillowData;
			}
		?>
<?php if(isset($newZillowData)): ?>
	<div class="error">We could not find that address on Zillow. Please fill in the details below.</div>
<?php endif; ?>
<form method="post" action="widget.php">
	<div class="widget-section">
		<div class="label">Please confirm the details:</div>
		<?php foreach($details as $detail_key=>$detail): ?>
			<div class="detail-label"><?php echo $detail['label'] ?></div>
			<?php switch($detail['field']):
			case 'text': ?>
				<input type="text" name="<?php echo $detail_key ?>" class="full" value="<?php echo $zillowData[$detail_key] ?>" />
			<?php break;
			case 'select': ?>
				<select name="<?php echo $detail_key ?>" class="full">
				<?php foreach($detail['options'] as $detail_option): ?>
					<option<?php echo $zillowData[$detail_key]==$detail_option ? ' selected="selected"' : '' ?>><?php echo $detail_option ?></option>
				<?php endforeach; ?>
				</select>
			<?php break;
			endswitch; ?>
		<?php endforeach; ?>
	</div>
	<div class="widget-section">
		<div class="label">Optional reports:</div>
		<div class="checkbox-wrapper">
			<input type="checkbox" name="optional_reports[]" value="pool" id="pool" /> <label for="pool">Pool</label><br />
			<input type="checkbox" name="optional_reports[]" value="hot_tub" id="hot_tub" /> <label for="hot_tub">Hot Tub/Spa</label><br />
			<input type="checkbox" name="optional_reports[]" value="radon" id="radon" /> <label for="radon">Radon</label><br />
			<input type="checkbox" name="optional_reports[]" value="pier_beam" id="pier_beam" /> <label for="pier_beam">Pier &amp; Beam</label><br />
			<input type="checkbox" name="optional_reports[]" value="sprinkler" id="sprinkler" /> <label for="sprinkler">Sprinkler</label><br />
			<input type="checkbox" name="optional_reports[]" value="termite" id="termite" /> <label for="termite">Termite</label>
		</div>
	</div>
	<div class="widget-buttons">
		<input type="submit" value="Complete Request" />
	</div>
</form>
		<?php
			break;
		case 4:
			$property_id=insert_property();
			$quote_request_id=insert_quote_request($property_id,$broker_id);
			send_request($broker_id,$quote_request_id);
			unset($_SESSION['widget_session']);
		?>
<div class="widget-section">
	<p>Thank you for your quote request! It has been sent to all of my inspectors. Have another quote request? <a href="widget.php?step=1">Click here to begin</a>.</p>
</div>
		<?php
			break;
	}
	

	?>
		<div id="powered-by-locizzle">
			<span class="locizzle-icon">&nbsp;</span>
			<span class="text">Powered by <a href="http://www.locizzle.com" target="_blank">LOCIZZLE.com</a></span>
		</div>
<!--div class="widget-section">
	<div class="label">Step 1 of 3: Tell us who your realtor is?</div>
	<select name="realtor" id="search-for-realtor">
	<?php foreach($brokers as $broker): ?>
		<option value="<?php echo $broker['id'] ?>"><?php echo $broker['first_name'].' '.$broker['last_name'] ?></option>
	<?php endforeach; ?>
	</select>
	<a id="search-for-realtor-btn">Search</a>
</div>
<div class="widget-section">
	<div class="label">Step 2 of 3: Provide your contact information.</div>
	<input type="text" name="first_name" placeholder="First Name" class="half" />
	<input type="text" name="last_name" placeholder="Last Name" class="half" /><br />
	<input type="text" name="email" placeholder="E-mail Address" class="full" /><br />
	<input type="text" name="mobile" placeholder="Mobile Phone" class="half" />
	<input type="checkbox" name="text_capable" /> Text Capable
</div>
<div class="widget-section">
	<div class="label">Step 3 of 3: When do you need the inspection completed by?</div>
	<input type="text" name="completed_by" class="full" />
</div>
<div class="widget-buttons">
	<input type="submit" value="Submit" />
</div-->
	</body>
</html>
<?php ob_end_flush(); ?>