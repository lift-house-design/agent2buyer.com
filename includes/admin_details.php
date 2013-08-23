<?php
	$person_id=mysql_real_escape_string($_GET['id']);
	$role=strtolower(mysql_real_escape_string($_GET['role']));

	if(!in_array($role,array('broker','inspector')))
	{
		?>
		<p>That role was not found.</p>
		<?php
	}
	
	// Get person data
	$sql='
		select
			*
		from
			person
		where
			id='.$person_id.'
		limit 1
	';
	$r=mysql_query($sql) or die(mysql_error());
	if(mysql_num_rows($r)<1)
	{
		?>
		<p>That user was not found.</p>
		<?php
	}
	$person=mysql_fetch_assoc($r);
	
	// Get broker/inspector data
	$sql='
		select
			*
		from
			'.$role.'
		where
			person='.$person_id.'
		limit 1
	';
	$r=mysql_query($sql) or die(mysql_error());
	if(mysql_num_rows($r)<1)
	{
		?>
		<p>That <?php echo $role ?> was not found.</p>
		<?php
	}
	
	$role_data=mysql_fetch_assoc($r);
	
	if($role=='broker')
		$person['company']=$role_data['company_name'];
	elseif($role=='inspector')
		$person['company']=$role_data['company'];
	
?>
<h2>Contact Information</h2>
<table id="users" class="users_details">
	<tbody>
		<tr>
			<th>First Name</th>
			<td><?php echo $person['first_name'] ?></td>
		</tr>
		<tr>
			<th>Last Name</th>
			<td><?php echo $person['last_name'] ?></td>
		</tr>
		<tr>
			<th>E-mail</th>
			<td><?php echo $person['email'] ?></td>
		</tr>
		<tr>
			<th>Mobile Phone</th>
			<td><?php echo $person['mobile_phone'] . ' <span class="small">(IS '.(empty($person['text_capable']) ? 'NOT ' : '').'text capable)</span>' ?></td>
		</tr>
		<tr>
			<th>Office Phone</th>
			<td><?php echo $person['office_phone'] . (empty($person['ext']) ? '' : ' (ext: '.$person['ext'].')') ?></td>
		</tr>
		<tr>
			<th>Company</th>
			<td><?php echo $person['company'] ?></td>
		</tr>
		<tr>
			<th>Postal Code</th>
			<td><?php echo $person['postal_code'] ?></td>
		</tr>
	</tbody>
</table>
<h2><?php echo ucfirst($role) ?> Information</h2>
<?php if($role=='broker'): ?>
<table id="users" class="users_details">
	<tbody>
		<tr>
			<th>Start Date</th>
			<td><?php echo $person['time_verification_code_sent'] ?></td>
		</tr>
		<tr>
			<th># Transactions</th>
			<td>
				<span id="num_transactions_value"><?php echo $role_data['transaction_count'] ?></span>
				<span id="date_range">
					<input type="radio" name="num_transactions_mode" value="all" checked="checked" /> Any Date
					<input type="radio" name="num_transactions_mode" value="range" /> Date Range:
					<span id="date_range_picker" style="display: none">
						<input type="text" name="from_date" class="datepicker" /> - 
						<input type="text" name="to_date" class="datepicker" />
					</span>
				</span>
			</td>
		</tr>
		<tr>
			<th>Billing Status</th>
			<td>
				Broker's inspectors <strong>
				<?php if(empty($role_data['bill_inspectors'])): ?>
					WILL NOT
				<?php else: ?>
					WILL
				<?php endif; ?>
				</strong> be billed.
			</td>
		</tr>
	</tbody>
</table>
<?php elseif($role=='inspector'): ?>
<table id="users" class="users_details">
	<tbody>
		<tr>
			<th>Start Date</th>
			<td><?php echo $person['time_verification_code_sent'] ?></td>
		</tr>
		<tr>
			<th># Transactions</th>
			<td><?php echo $role_data['transaction_count'] ?></td>
		</tr>
		<tr>
			<th># Pre-paid Transactions</th>
			<td><?php echo $role_data['paid_transactions'] ?></td>
		</tr>
	</tbody>
</table>
<?php endif; ?>
<script>
	$(document)
		.on('change','#date_range input[name="num_transactions_mode"]',function(){
			var mode=$(this).val();
			
			if(mode=='all')
			{
				$('#num_transactions_value').html('<?php echo $role_data['transaction_count'] ?>');
				$('#date_range_picker').hide();
			}
			else if(mode=='range')
			{
				$('#date_range_picker').show();
			}
		})
		.on('change','#date_range_picker input',function(){
			var from_date=$('#date_range_picker input[name="from_date"]').val();
			var to_date=$('#date_range_picker input[name="to_date"]').val();
			
			if(from_date != '' && to_date != '')
			{
				var id='<?php echo $person_id ?>';
				console.log('ready: '+id);
				
				$.ajax({
					url: 'admin.php',
					type: 'post',
					dataType: 'json',
					data: {
						action: 'num_transactions',
						person_id: id,
						from_date: from_date,
						to_date: to_date
					},
					success: function(data, status){
						console.log(data.status);
						if(data.status=='success')
						{
							$('#num_transactions_value').html(data.value+' <span class="small">(From '+from_date+' to '+to_date+')</span>');
						}
						else
						{
							$('#num_transactions_value').html('<?php echo $role_data['transaction_count'] ?>');
						}
					}
				});
			}
			else
			{
				console.log('not ready: '+from_date+' :: '+to_date);
			}
		});
	$(function(){
		$('.datepicker').datepicker({ dateFormat: 'yy-mm-dd' });
	});
</script>
<a href="admin.php"><< Back to Administration Panel</a>
