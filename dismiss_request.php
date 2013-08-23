<?php
	include './includes/functions.php';
	if (isset($_GET['id'])) {
		$inspector_id=mysql_real_escape_string($_GET['id']);
		
		$sql=mysql_query("UPDATE inspectorsQuoteRequests SET status='-1', last_updated=NOW() WHERE inspector='$inspector_id' AND status='0'");
		$msg='Thank you. You have dismissed this request. </br></br>
		Hopefully your next opportunity will work out better!';
	}
	else {
		$msg='We are sorry, there has been an error. It appears some of the URL has been lost. </br></br>
		If this problem continues, please contact us at Mike@locizzle.com';
	}
?>
<?php include './includes/header.php'; ?>
<div class="container">
	<div class="row" style="font-size:20px;" align="center">
	<br />
	<br />
		<?php echo $msg; ?>
	<br />
	<br />
	</div>
</div>
<?php include './includes/footer.php'; ?>
