<style>
	div.sub-accordion {
		text-align: left;
	}
	.float-right {
		float: right;
	}
	strong {
		font-weight: bold;
	}
	#nestedAccordion {
		margin-top: 20px;
	}
	
	.inspection-heading {
		
	}
	.inspection-details {
		padding: 10px;
		margin-left: 10px;
	}
	.inspection-details .accept-quote-btn {
		float: right;
		background-color: #faf8f0;
		color: #3C2111;
		border-radius: 8px;
		margin: 3px;
		padding: 8px;
		border: 2px outset #ccc;
		font-size: 12px;
		cursor: pointer;
	}
	.inspection-details .accept-quote-btn:hover {
		background-color: #60BDB8;
		color: #fff;
	}
	.inspection-bid {
		border-bottom: 1px solid #ccc;
		padding: 15px;
	}
	#change-your-pin {
		padding: 15px;
		background-color: #F0EADD;
	}
	#change-your-pin input[type="text"] {
		width: 675px;
	}
</style>
	<div class="container widget">
		<div class="row widget-section">
			<a href="clients.php?action=logout" class="float-right">Logout</a>
			<h1>Welcome, <?php echo $client['first_name'].' '.$client['last_name'] ?></h1>
			<form id="accept-bid" method="post" action="clients.php">
				<input type="hidden" name="action" value="accept_bid" />
				<input type="hidden" name="inspector_id" value="" />
			</form>
			<div id="nestedAccordion">
				<h2 class="odd">
					<span class="sign">+</span>
					Pending Inspections
				</h2>
				<div class="sub-accordion">
					<?php if(empty($quote_requests)): ?>
						There are no pending inspections.
					<?php else: ?>
						<?php foreach($quote_requests as $quote_request): ?>
							<?php
								$property=$properties[ $quote_request['property'] ];
							?>
							<h3 class="inspection-heading inspector_list">
								<span class="sign">+</span>
								<?php echo $property['street'].', '.$property['city'].', '.$property['state'].' '.$property['zip'] ?>
								<span class="float-right">Needed by: <?php echo date('m/d/Y',strtotime($quote_request['date'])) ?></span>
							</h3>
							<div class="inspection-details">
								<?php if(empty($quote_request['bids'])): ?>
									There are no bids for this inspection request.
								<?php else: ?>
									<?php foreach($quote_request['bids'] as $bid): ?>
										<?php
											$inspector=$inspectors[ $bid['inspector'] ];
										?>
										<div class="inspection-bid">
											<a class="accept-quote-btn" data-inspector-id="<?php echo $inspector['id'] ?>">+ Accept Quote</a>
											<strong>Inspector: </strong><a href="./inspector.php?id=<?php echo $inspector['id'] ?>"><?php echo $inspector['first_name'].' '.$inspector['last_name'] ?></a><br />
											<strong>Quote Amount: </strong>$<?php echo number_format($bid['bid_amount'],2) ?>
										</div>
									<?php endforeach; ?>
								<?php endif; ?>
								
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
				<h2 class="even">
					<span class="sign">+</span>
					Change Your PIN
				</h2>
				<div class="sub-accordion">
					<form id="change-your-pin" method="post" action="clients.php">
						<div class="submit-message"></div>
						<input type="hidden" name="action" value="change_pin" />
						<input type="text" name="old_pin" placeholder="Old Pin" maxlength="4" /><br />
						<input type="text" name="new_pin" placeholder="New Pin" maxlength="4" /><br />
						<input type="text" name="confirm_new_pin" placeholder="Confirm New Pin" maxlength="4" /><br />
						<input type="submit" name="change_pin" value="Change PIN" />
					</form>
				</div>
			</div>
		</div>
	</div>
	<script>
		$(document)
			.on('submit', '#change-your-pin',function(){
				var data={};
				$('#change-your-pin :input').each(function(){
					data[$(this).attr('name')]=$(this).val();
				});
				$('#change-your-pin .submit-message').load($(this).attr('action'),data,function(){
					$('#change-your-pin input[type="text"]').val('');
				});
				return false;
			})
			.on('keyup','#change-your-pin input[type="text"]',function(){
			    if (/\D/g.test(this.value))
			    {
			        // Filter non-digits from input value.
			        this.value = this.value.replace(/\D/g, '');
			    }
			})
			.on('click','.accept-quote-btn',function(){
				$('#accept-bid input[name="inspector_id"]').val($(this).data('inspector-id'));
				$('#accept-bid').submit();
			})
			.on('click','.logout-btn',function(){
				
			});
	</script>