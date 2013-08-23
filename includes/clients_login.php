<style>
	#login {
		width: 400px;
		margin: 0 auto;
		margin-top: 20px;
	}
	#login td.label {
		width: 70px;
		font-weight: bold;
		padding-right: 10px;
	}
	#login input[type="text"],
	#login input[type="password"] {
		width: 300px;
	}
	
	.error {
		width: 400px;
		margin-top: 20px;
		border: 1px solid red;
		padding: 10px;
		background-color: #fcc;
		margin: 0 auto;
	}
</style>
<?php if(isset($error)): ?>
	<div class="error"><?php echo $error ?></div>
<?php endif; ?>
<form method="post" action="clients.php">
	<input type="hidden" name="action" value="login" />
	<table id="login">
		<tr>
			<td class="label">E-mail:</td>
			<td>
			<?php if(isset($_GET['email'])): ?>
				<input type="hidden" name="email" value="<?php echo $_GET['email'] ?>" />
				<?php echo $_GET['email'] ?>
			<?php else: ?>
				<input type="text" name="email" />
			<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td class="label">PIN:</td>
			<td><input type="password" name="pin" maxlength="4" /></td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input type="submit" value="Login" />
			</td>
		</tr>
	</table>
</form>
<?php if(isset($_GET['email'])): ?>
<script>
	$(function(){
		$('input[name="pin"]').focus();
	});
</script>
<?php endif; ?>