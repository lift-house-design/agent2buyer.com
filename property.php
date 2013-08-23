<?php
//do not show submitquote section if came from phone
if (isset($_GET['mobile'])) {
	$text_response="";
}
?>
<?php
include './includes/functions.php';
//this block handles receive submitQuote form info from main.js and echoing success/failue
if(!empty($_POST)){
	if(isset($_POST['inspFirstNameValue'])) {
		$insp_first_name = ucwords(mysql_real_escape_string($_POST['inspFirstNameValue']));
		$insp_last_name = ucwords(mysql_real_escape_string($_POST['inspLastNameValue']));
		$insp_email = (mysql_real_escape_string($_POST['inspEmailValue']));
		
		echo $insp_first_name.$insp_last_name.$insp_last_name.'fun';
	}
}
?>
<?php $accordian=""; ?>
<?php $accordion=""; ?>
<?php include './includes/header_dashboard.php'; ?>
<?php

	if (isset($_GET['id'])) {
		$property_id=mysql_real_escape_string($_GET['id']);
		
		$sql=mysql_query("SELECT * from property where id=".$property_id);
			$property = array(); 
				while($row = mysql_fetch_assoc($sql)) { 
				   $property[] = $row; 
				} 
			//Normalize data
			if ($property[0]['numRooms'] ==0) {
				$property[0]['numRooms']='Unknown';
			}
			if ($property[0]['numFloors'] ==0) {
				$property[0]['numFloors']='Unknown';
			}
			
		//Check if the request for this property is still open or if a bid has been accepted
		$sql=mysql_query("SELECT * from quoteRequest where property=".$property_id);
			$quoteRequest = array(); 
					while($row = mysql_fetch_assoc($sql)) { 
					   $quoteRequest[] = $row; 
					} 
					if ($quoteRequest[0]['status'] !== 'pending') {
						$msg='<span style="font-size:25px;">We are sorry. This inspection request has already been filled. </br>
						Please keep a look out for your next inspection opportunity.</span>';
					}
	}
?>
<div class="container">
	<div class="row" align="center" style="padding:5px;">
	<?php 
		if (isset($msg)){
			echo 
			
			$msg
			.'</div>
			</div>';
			include './includes/footer.php';
			exit;
		}
	?>
		<?php if(isset($mobile)): ?>
		<div style="margin:auto;" id="accordion">
	<?php endif; ?>	
	<?php if(!isset($mobile)): ?>
		<div style="width:37%; margin:auto;" id="accordion">
	<?php endif; ?>	
	<br /><br />
		<h3>Property Information</h3>
			<div style="font-size:12px; text-align:left;">
				<!--all info about property-->
				<span style="font-weight:bold; color: #60BDB8;">Must complete by: </span><?php echo $quoteRequest[0]['date']; ?></br>
				</br>
				<span style="font-weight:bold;">Address:</span> <?php echo $property[0]['street']; ?> <?php echo $property[0]['city']; ?>, <?php echo $property[0]['state']; ?>, <?php echo $property[0]['zip']; ?>
				</br>
				<span style="font-weight:bold;">Year Built:</span> <?php echo $property[0]['yearBuilt']; ?>
				</br>
				<span style="font-weight:bold;">Type:</span> <?php echo $property[0]['type']; ?>
				</br>
				<span style="font-weight:bold;">Stories:</span> <?php echo $property[0]['numFloors']; ?>
				</br>
				<span style="font-weight:bold;">Bedrooms:</span> <?php echo $property[0]['bedrooms']; ?>
				</br>
				<span style="font-weight:bold;">Bathrooms:</span> <?php echo $property[0]['bathrooms']; ?>
				</br>
				<span style="font-weight:bold;">Total Rooms:</span> <?php echo $property[0]['numRooms']; ?>
				</br>
				<span style="font-weight:bold;">Total Area:</span> <?php echo $property[0]['totalArea']; ?>
				</br>
				<span style="font-weight:bold;">Attached Garage (# of cars):</span> <?php echo $property[0]['garage_attached']; ?>
				</br>
				<span style="font-weight:bold;">Detached Garage (# of cars):</span> <?php echo $property[0]['garage_detached']; ?>
				</br>
				<span style="font-weight:bold;">Foundation:</span> <?php echo $property[0]['foundation']; ?>
				</br>
				<span style="font-weight:bold;">Waste System:</span> <?php echo $property[0]['citySewer']; ?>
				</br>
				</br>
				<?php if (($property[0]['pool'] ==1) || ($property[0]['hottub'] ==1) || ($property[0]['pier_and_beam'] ==1)
				 || ($property[0]['radon'] ==1) || ($property[0]['sprinkler'] ==1) || ($property[0]['termite'] ==1) ){
					echo '<span style="font-weight:bold;">Additional Reports</span></br>';
				
					if ($property[0]['pool'] ==1){
						echo "Pool</br>";
						}
				
				
					if ($property[0]['hottub'] ==1){
						echo "Hot Tub/Spa</br>";
						} 
				
					if ($property[0]['pier_and_beam'] ==1){
						echo "Pier And Beam</br>";
						}
				
					if ($property[0]['radon'] ==1){
						echo "Radon</br>";
						}
				
				
					if ($property[0]['sprinkler'] ==1){
						echo "Sprinkler</br>";
						} 
				
					if ($property[0]['termite'] ==1){
						echo "Termite";
						}
				
				}
				else {
					echo '<span style="font-weight:bold;">Additional Reports</span></br>
					None';
				}
				?>
			</div>	
		<!-- Only show the option to submit a quote if the user came from email, otherwise require text submission -->
		<?php if(isset($_GET['email'])): ?>
			<h3>Submit a Quote</h3>
				<div style="font-size:12px;">
				<span style="color: #60BDB8;font-weight:bold;">Reminder: </span>This inspection must be completed by: <span style="font-weight:bold;"><?php echo $quoteRequest[0]['date']; ?></span></br></br>
					<form style=" text-align:left;" id="submitQuote" action="./process_quote_email.php" method="POST">
						$<input name="insp_bid_amount" type="text" placeholder="Amount" /></br>
						<!--&nbsp;&nbsp;<input name="insp_first_name" type="text" placeholder="First Name" /></br>
						&nbsp;&nbsp;<input name="insp_last_name" type="text" placeholder="Last Name" /></br>-->
						&nbsp;&nbsp;<input name="insp_email" type="text" placeholder="Email" /></br>
						<input name="insp_quoteRequest_id" type="hidden" value="<?php echo $quoteRequest[0]['id']; ?>" /></br>
						<span><b style="text-decoration:underline;">Note:</b> Your email must be the same as where you recieved this inspection opportunity.</span></br></br>
						<input name="submit_verify" type="submit" value="Submit" />
						<div id="submitQuote_message"></div>
						</br>
					</form>		
				</div>
		<?php endif; ?>	
	</div>
	</div>
</div>
<?php include './includes/footer.php'; ?>
