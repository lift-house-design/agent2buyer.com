<?php
	// Broker table data
	$sql='select broker.*, company.name as company_name from broker, company where broker.company_id = company.id';
	$r=mysql_query($sql) or die(mysql_error());
	while($row=mysql_fetch_assoc($r))
	{
		$brokers[$row['person']]=$row;
	}
	
	// Inspector table data
	$sql='select * from inspector';
	$r=mysql_query($sql) or die(mysql_error());
	while($row=mysql_fetch_assoc($r))
	{
		$inspectors[$row['person']]=$row;
	}
	

	// Person table data
	$sql='select * from person';
	$r=mysql_query($sql) or die(mysql_error());
	while($row=mysql_fetch_assoc($r))
	{
		if(isset($brokers[$row['id']]))
		{
			$row['role']='Broker';
			$row['company']=$brokers[$row['id']]['company_name'];
			$row['transaction_count']=$brokers[$row['id']]['transaction_count'];
		}
		elseif(isset($inspectors[$row['id']]))
		{
			$row['role']='Inspector';
			$row['company']=$inspectors[$row['id']]['company'];
			$row['transaction_count']=$inspectors[$row['id']]['transaction_count'];
		}
		else
		{
			// Only show brokers/inspectors
			continue;
			
			/*$row['role']='Unknown';
			$row['company']='';*/
		}
		
		
		$row['start_date']=date('m/d/Y',strtotime($row['time_verification_code_sent']));
		
		$people[]=$row;
	}
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('#users').dataTable();
		
		var successColor='rgb(184,255,185)';
		var errorColor='red';
		
		$(document)
			.on('change','#users .billing input[type="checkbox"]',function(){
				var data=$(this)
					.parents('tr')
					.attr('id');
				
				data=data.split('_');
				
				var role=data[0];
				var id=data[1];
				
				var checkbox=this;
				
				$.ajax({
					url: 'admin.php',
					type: 'post',
					dataType: 'json',
					data: {
						action: 'billing',
						person_id: id,
						value: this.checked ? 1 : 0
					},
					success: function(data, status){
						var useColor=errorColor;
						var cells=$(checkbox)
							.parents('tr')
							.children('td');
						
						if(data.status=='success')
						{
							useColor=successColor;
						}
						else
						{
							checkbox.checked=!checkbox.checked;
							alert('There was a problem saving that entry\'s billing status.');
						}
						
						cells.css('background-color',useColor);
						
						setTimeout(function(){
							cells.css('background-color','transparent');
						},1000);
					}
				});
			})
			.on('change','#users .disabled input[type="checkbox"]',function(){
				var data=$(this)
					.parents('tr')
					.attr('id');
				
				data=data.split('_');
				
				var role=data[0];
				var id=data[1];
				
				var checkbox=this;
				
				$.ajax({
					url: 'admin.php',
					type: 'post',
					dataType: 'json',
					data: {
						action: 'disabled',
						person_id: id,
						value: this.checked ? 1 : 0
					},
					success: function(data, status){
						var useColor=errorColor;
						var cells=$(checkbox)
							.parents('tr')
							.children('td');
						
						if(data.status=='success')
						{
							useColor=successColor;
						}
						else
						{
							checkbox.checked=!checkbox.checked;
							alert('There was a problem saving that entry\'s disabled status.');
						}
						
						cells.css('background-color',useColor);
						
						setTimeout(function(){
							cells.css('background-color','transparent');
						},1000);
					}
				});
			});
	});
</script>
	<a href="admin.php?logout" style="float: right;">Logout of Administration</a>
	<h1>Administration Panel</h1>
	<table id="users">
		<thead>
			<tr>
				<th>E-mail</th>
				<th class="center">Company</th>
				<th class="center">Role</th>
				<th class="center">Transactions</th>
				<th class="center">Start Date</th>
				<th class="center">Billable</th>
				<th class="center">Disabled</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($people as $person): ?>
			<tr id="<?php echo strtolower($person['role']).'_'.$person['id'] ?>">
				<td><a href="admin.php?id=<?php echo $person['id'] ?>&role=<?php echo $person['role'] ?>" target="_blank"><?php echo $person['email'] ?></a></td>
				<td class="center"><?php echo $person['company'] ?></td>
				<td class="center"><?php echo $person['role'] ?></td>
				<td class="center"><?php echo $person['transaction_count'] ?></td>
				<td class="center"><?php echo $person['start_date'] ?></td>
				<td class="center billing">
				<?php if($person['role']=='Broker'): ?>
					<input type="checkbox"<?php echo $brokers[$person['id']]['bill_inspectors']==1 ? ' checked="checked"' : '' ?> />
				<?php endif; ?>
				</td>
				<td class="center disabled">
					<input type="checkbox"<input type="checkbox"<?php echo $person['enabled']==0 ? ' checked="checked"' : '' ?> />
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<!-- END CONTENT -->