<?php
	include './includes/functions.php';
	if (isset($_GET['id'])) {
		$inspector_id=mysql_real_escape_string($_GET['id']);
		$sql1=mysql_query("SELECT * from person WHERE id='$inspector_id'");
		$person = array(); 
			while($row1 = mysql_fetch_assoc($sql1)) { 
			   $person[] = $row1; 
			}
		$sql2=mysql_query("SELECT * from inspector WHERE person='$inspector_id'");
		$inspector = array(); 
			while($row2 = mysql_fetch_assoc($sql2)) { 
			   $inspector[] = $row2; 
			}
	}
?>
<?php include './includes/header_dashboard.php'; ?>
<div class="container" id="learn_more_section">
	<?php if(isset($mobile)): ?>
		<div class="row" style="font-size:16px;">
	<?php endif; ?>	
	<?php if(!isset($mobile)): ?>
		<div class="row" style="padding:40px; width:60%; font-size:16px;">
	<?php endif; ?>	
	<br />
		<!-- Profile Image -->
		<div class="fourcol" align="center">
			<?php if(file_exists("./img/inspector/".$inspector_id.".".$inspector[0]['file_ext'])) {
					echo "<img style='width:150px; height:auto;' src='./img/inspector/".$inspector_id.".".$inspector[0]['file_ext']."' />";
				}
				else {
					echo "<img style='width:150px; height:auto;' src='./img/inspector/default.jpg' />";
				}
			?>
			</br>
			
			<?php if(empty($_SESSION['person'])): ?>
				<a style="text-align:left;" href="./edit-profile.php?id=<?php echo $inspector_id;?>">Edit Profile</a>
			<?php endif; ?>	
			
			</br>
		</div>
		<!-- Profile text -->
		<div class="fourcol" style="text-align:left;" align="center">
			<b style="font-size:24px; text-decoration:underline;" class="bold"><?php echo $person[0]['first_name'].' '.$person[0]['last_name']; ?></b></br>Inspector</br></br>
			<?php if(!empty($inspector[0]['company'])) {
					echo '<b style="display:inline-block; padding:5px 0px 5px 0px;" class="bold">Company:</b> '.$inspector[0]['company'].'</br>'; 
					}
			?>
			<b style="display:inline-block; padding:5px 0px 5px 0px;" class="bold">Mobile Phone:</b> <?php echo $person[0]['mobile_phone']; ?></br>
			<?php if(!empty($person[0]['office_phone'])) {
					echo '<b style="display:inline-block; padding:5px 0px 5px 0px;" class="bold">Office Phone:</b> '.$person[0]['office_phone'].'</br>'; 
					}
			?>
			<b style="display:inline-block; padding:5px 0px 5px 0px;" class="bold">Email:</b> <?php echo $person[0]['email']; ?></br>
			
			</br>
		</div>
		<!--Contact Info -->
		<div class="fourcol last" style="text-align:left;" align="center">
			<?php if(!empty($inspector[0]['description'])) {
					echo '<b style="padding:10px;" class="bold" style="text-decoration:underline;">Profile</b></br></br> '.$inspector[0]['description']; 
					}
			?>
		</div>
		
		
	</div>
</div>
<?php include './includes/footer.php'; ?>