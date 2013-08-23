<?php 
	$dashboard=''; 
	$accordion=''; 
	$accordian='';
	$no_home_button='';
?>
<?php include './includes/functions.php'; ?>
<?php include './includes/header_dashboard.php'; ?>
<?php
//this block will accept a quote
if (isset($_POST['quoteInspValue'])) {
	$quote_insp=$_POST['quoteInspValue'];
	accept_bid($quote_insp);
}
?>
<?php
//delete inspector from master list
	if (isset($_GET['delete'])) {
		$delete_id=$_GET['delete'];
		$broker_id=$_SESSION['person']['id'];
		$sql=mysql_query("DELETE from brokersInspectors where broker='$broker_id' AND inspector='$delete_id'");
		
		$insp_message= "The Inspector has been successfully deleted from your Master List";
	}
?>
<?php
//remove inspector from "go-to" list
	if (isset($_GET['remove'])) {
		$remove_id=$_GET['remove'];
		$broker_id=$_SESSION['person']['id'];
		$sql=mysql_query("UPDATE brokersInspectors SET relationship='0' where broker='$broker_id' AND inspector='$remove_id'");
		
		$insp_message= 'The Inspector has been successfully removed from your "Go-to" List';
	}
?>
<?php
//Add inspector to "go-to" list
	if (isset($_GET['add'])) {
		$add_id=$_GET['add'];
		$broker_id=$_SESSION['person']['id'];
		$sql=mysql_query("UPDATE brokersInspectors SET relationship='1' where broker='$broker_id' AND inspector='$add_id'");
		
		$insp_message= 'The Inspector has been successfully added to your "Go-To" List';
	}
?>
<?php
//remove client from client list
	if (isset($_GET['removeClient'])) {
		$deleteClient_id=$_GET['removeClient'];
		$broker_id=$_SESSION['person']['id'];
		$sql=mysql_query("DELETE from brokersClients where broker='$broker_id' AND client='$deleteClient_id'");
		$sql=mysql_query("DELETE from client where id='$deleteClient_id'");
		
		$insp_message= 'The Client has been successfully removed from your Client List';
	}
?>
<?php 
//this validates to make sure the user has inspectors, and at least one is available
	$broker_id=$_SESSION['person']['id'];
	$sql1=mysql_query("SELECT * FROM brokersInspectors where broker='$broker_id' AND relationship='1'");
	$doublecheck1 = mysql_num_rows($sql1);
		if($doublecheck1 === 0){ 
			$error_no_inspectors='You do not have any inspectors on your "Go-To" List. Please add them below in the Inspectors section.';
		}
?>
<!-- START CONTENT -->
<div class="container widget">
<!-- INSPECTIION REQUESTS SECTION 
view pending inspections, accept quotes, view history-->
<?php
	if(isset($_SERVER['HTTP_REFERER']))
	{
		$pathinfo=pathinfo($_SERVER['HTTP_REFERER']);
		$referring_script=$pathinfo['filename'];
	}
	else
		$referring_script='';
?>
<div class="row widget-section">
	<span id="faq/help"><a href="./faq.php">FAQ/Help</a></span>
	<h1 class="bottom-border">My Home Inspections</h1>
	<div id="nestedAccordion">
		<h2 class="odd"><span class="sign"><?php echo $referring_script=='add_insp' ? '-' : '+' ?></span> Create New Home Inspection</h2>
			<div class="<?php echo $referring_script=='add_insp' ? 'content' : 'sub-accordion' ?>">
			<?php if(isset($error_no_inspectors)): ?>
				<?php echo '<span style="display:inline-block; text-align:left;">'.$error_no_inspectors.'</span>' ?>
			<?php else: ?>
				<!-- Inspector Information -->
				<?php include './includes/new_request.php'; ?>
			<?php endif; ?>		
			</div>
		<h2 class="even"><span class="sign">+</span> Pending Inspections</h2>
			<div class="sub-accordion" style="text-align:left;" >
				<!-- place list of all PENDING inspections in here ($pending_inspections .=)-->
				<?php 
					if (isset($mobile)){
						include './includes/mobile_pending_inspections_list.php';
					}
					else {
						include './includes/pending_inspections_list.php';
					}	 
				?>
				
			</div>
		<h2 class="odd"><span class="sign">+</span> Inspection History</h2>
			<div class="sub-accordion" style="text-align:left;" >
			<?php 
				if (isset($mobile)){
					include './includes/mobile_history_inspections_list.php';
				}
				else {
					include './includes/history_inspections_list.php';
				}	 
			?>
			</div>
	</div>
</div>
<!-- END inspection request section -->
<!-- MY NETWORK SECTION -->
<div class="row widget-section">
	<h1 class="bottom-border">My Network</h1>
	<span style="color: #60BDB8;"><?php 
		if (!empty($insp_message)) {
			echo $insp_message;
		}
	?></span>
	<div id="nestedAccordion">
		<h2 class="even"><span class="sign">+</span> Inspectors</h2>
			<div class="sub-accordion" style="text-align:left;">
			<span class="list_title" style="margin-bottom: 10px;">My "Go-to" List</span>
				<?php include './includes/goto_inspector_list.php'; ?>
				</br></br>
			<span class="list_title" style="margin-bottom: 10px;">My Master List</span>
				<?php include './includes/master_inspector_list.php'; ?>
				</br></br>
			
				<h3 class="inspector_list">
					<a style="color: #3C2111;" href="./add_insp.php?id=repeat">Invite A New Inspector</a>
				</h3>
					<!--<div class="tester" >
						<form id="signUpInsp2" action="./add_insp.php" method="POST">
							*<span style="color: #F2713C;">Required Fields</span></br>
							<input name="inspFirstNameValue" type="text" placeholder="* First Name" />
							<input name="inspLastNameValue" type="text" placeholder="* Last Name" /></br>
							<input name="inspMobilePhoneValue" id="phone" onkeydown="javascript:backspacerDOWN(this,event);" onkeyup="javascript:backspacerUP(this,event);" type="text" placeholder="* Mobile Phone" />
							<input name="inspTextCapableValue" type="checkbox" id="insp_text_capable"/>
							<span style="font-size:12px;">Text Capable</span></br>
							<input name="inspEmailValue" type="text" placeholder="* Email" /></br>
							Inspector's Postal Code:&nbsp;</br>
							<input name="inspPostalCodeValue" type="text" value="<?php echo $postal_code; ?>" /></br>
							<input name="submit_insp" type="submit" value="Invite" /></br>
							</br>
							<div id="insp-message"></div>
							</br>
				
						</form>	
					</div>-->
				<!--<h3 class="inspector_list" style="clear:both;margin-left:10px;padding:10px; border-bottom:1px solid #3C2111; color: #3C2111;background: #F0EADD;">
					<span class="sign">+</span> Find An Inspector In Your Area
				</h3>
					<div class="tester">
						<form action="./add_insp.php" method="POST">
							<span>Find Inspectors within a specific radius of you:</span></br></br>
							Your Postal Code: <input type="text" id="brokerzip" name="brokerzip" value="<?php echo $postal_code; ?>" /></br></br>
							<label for="50">
								<input class="radius" type="radio" name="radius" id="50" value="50">50 Miles
							</label>
							<label for="100">
								<input class="radius" type="radio" name="radius" id="100" value="100">100 Miles
							</label>
							<label for="150">
								<input class="radius" type="radio" name="radius" id="150" value="150">150 Miles
							</label>
						</form>
						<div id="inspector_list" class="accord">
							
						</div>
					</div>-->
				</br></br>
			</div>
		<!--<h2 class="even"><span class="sign">+</span> Agents</h2>
			<div class="sub-accordion">
				<h3>Child 1</h3>
				<div>Sub 1</div>		
			</div>-->
		<h2 class="odd"><span class="sign">+</span> Clients</h2>
		<div class="sub-accordion" style="text-align:left;">
			<?php include './includes/client_list.php'; ?>
		</div>
		<h2 class="even"><span class="sign">+</span> My Account</h2>
		<div id="my-account" class="sub-accordion">
			<h3 class="inspector_list"><span class="sign">+</span> My Info</h3>
			<div class="tester">
				<?php require('includes/update_my_info.php'); ?>
			</div>
			<h3 class="inspector_list"><span class="sign">+</span> Broker Owner Info</h3>
			<div class="tester">
				<?php require('includes/broker_owner_info.php') ?>
			</div>
		</div>
	</div>
</div>
<!-- END new inspection request section -->
</div>
<!-- END CONTENT -->
<?php include './includes/footer.php'; ?>