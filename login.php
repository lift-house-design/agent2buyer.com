<?php
	$login="";
	$accordion="";
?>
<?php
//connect to database
include './includes/functions.php';
    if (isset($_POST['login_email'])) {
    	
		$email = mysql_real_escape_string($_POST['login_email']);
		$password = mysql_real_escape_string($_POST['login_password']);
		
		$sql = mysql_query ("SELECT * FROM person where email='$email' AND password='".sha1($password)."' AND verification_code='1' and enabled=1");
		    $doublecheck = mysql_num_rows($sql); 
			
	    if($doublecheck == 1){ 
				while($row = mysql_fetch_assoc($sql)) { 
				$data = array();
					$data = $row;
					$_SESSION['person'] = $data;
					
					//check if user is a broker
					$user_id=$_SESSION['person']['user'];
					$sql2 = mysql_query ("SELECT * FROM user where id='$user_id'");
					while ($row2 = mysql_fetch_assoc($sql2)) {
						$user_role=$row2['role'];
					}
					
					if($user_role=='broker'){
						header ('Location: ./dashboard.php');
						exit();
					}
					
				
				} 	
		} 
		else {
			$msgToUser =  "You have not entered your login credentials correctly. <a style='color: #60BDB8;' href='#login-box' class='login-window'>Please try again.</a>";
		}
	}
?>
<?php include './includes/header.php'; ?>
<div class="container">
	<div class="row" align="center" style="color: #3C2111; font-size:25px;padding:100px;">
		<hr />
			</br>
				<?php echo $msgToUser; ?>	
			</br></br>
		<hr />
	</div>
</div>
<?php include './includes/footer.php'; ?>
