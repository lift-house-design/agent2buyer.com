<style>
	#users.login input[type="text"],
	#users.login input[type="password"] {
		width: 400px;
	}
</style>
<?php if(isset($error)): ?>
<p><?php echo $error ?></p>
<?php endif; ?>
<form method="post" action="admin.php">
<table id="users" class="users_details login">
	<tbody>
		<tr>
			<th>Username</th>
			<td><input type="text" name="username" /></td>
		</tr>
		<tr>
			<th>Password</th>
			<td><input type="password" name="password" /></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" value="Login" />
			</td>
		</tr>
	</tbody>
</table>
</form>