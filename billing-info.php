<?php include './includes/functions.php'; ?>
<?php include './includes/billing.php'; ?>
<?php
$states_array = array('AL'=>"Alabama",  
			'AK'=>"Alaska",  
			'AZ'=>"Arizona",  
			'AR'=>"Arkansas",  
			'CA'=>"California",  
			'CO'=>"Colorado",  
			'CT'=>"Connecticut",  
			'DE'=>"Delaware",  
			'DC'=>"District Of Columbia",  
			'FL'=>"Florida",  
			'GA'=>"Georgia",  
			'HI'=>"Hawaii",  
			'ID'=>"Idaho",  
			'IL'=>"Illinois",  
			'IN'=>"Indiana",  
			'IA'=>"Iowa",  
			'KS'=>"Kansas",  
			'KY'=>"Kentucky",  
			'LA'=>"Louisiana",  
			'ME'=>"Maine",  
			'MD'=>"Maryland",  
			'MA'=>"Massachusetts",  
			'MI'=>"Michigan",  
			'MN'=>"Minnesota",  
			'MS'=>"Mississippi",  
			'MO'=>"Missouri",  
			'MT'=>"Montana",
			'NE'=>"Nebraska",
			'NV'=>"Nevada",
			'NH'=>"New Hampshire",
			'NJ'=>"New Jersey",
			'NM'=>"New Mexico",
			'NY'=>"New York",
			'NC'=>"North Carolina",
			'ND'=>"North Dakota",
			'OH'=>"Ohio",  
			'OK'=>"Oklahoma",  
			'OR'=>"Oregon",  
			'PA'=>"Pennsylvania",  
			'RI'=>"Rhode Island",  
			'SC'=>"South Carolina",  
			'SD'=>"South Dakota",
			'TN'=>"Tennessee",  
			'TX'=>"Texas",  
			'UT'=>"Utah",  
			'VT'=>"Vermont",  
			'VA'=>"Virginia",  
			'WA'=>"Washington",  
			'WV'=>"West Virginia",  
			'WI'=>"Wisconsin",  
			'WY'=>"Wyoming");
			
if(isset($_GET['id'])) {
	//set bid id
	$bid_id=mysql_real_escape_string($_GET['id']);
	$bid_result=mysql_query("SELECT * FROM bid WHERE id='$bid_id'");
	$bid = mysql_fetch_assoc($bid_result);
	
	if($bid['status']=='paid')
	{
		$error='This bid has already been paid for.';
		$bid['id']=0;
		$bid_id=0;
		
		// Added later to prevent the addition of prepaid transactions causing problems with this continuing..
		echo $error;
		exit;
	}
	
	$insp_id=$bid['inspector'];
	$quote_request_id=$bid['quoteRequest'];
	
	$person_result=mysql_query("SELECT * FROM person WHERE id='$insp_id'");
	$person = mysql_fetch_assoc($person_result);
	
	$first_name=$person['first_name'];
	$last_name=$person['last_name'];
	$email=$person['email'];
	$zip=$person['postal_code'];

	// Handles cases where inspection is not billable
	$billable_inspection=true;
	$sql='
		select
			broker.bill_inspectors
		from
			broker,
			bid,
			quoteRequest
		where
			bid.id = '.$bid_id.' and
			bid.quoteRequest = quoteRequest.id and
			quoteRequest.person = broker.person
		limit 1
	';
	$r=mysql_query($sql) or die(mysql_error());
	$row=mysql_fetch_assoc($r);
	if($row['bill_inspectors']==0)
	{
		$billable_inspection=false;
		$payment_type='not_billable';
		$_POST['submit']=1;
		$_POST['agree_tos']=1;
	}
	else // Check if this inspector has paid in advance for other transactions
	{
		$paid_inspection=false;
		
		$sql='select paid_transactions from inspector where person='.$insp_id.' limit 1';
		$r=mysql_query($sql) or die(mysql_error());
		$row=mysql_fetch_assoc($r);
		$inspectors_paid_inspections=$row['paid_transactions'];
		if($inspectors_paid_inspections>0)
		{
			$paid_inspection=true;
			$payment_type='prepaid_payment';
			$_POST['submit']=1;
			$_POST['agree_tos']=1;
		}
	}
		

	if(isset($_POST['submit']))
	{
		if(empty($_POST['agree_tos']))
		{
			$error='You must agree to the fee, and our <a href="terms.php" target="_blank">terms and conditions</a>, in order to continue.';
		}
		else
		{
			if($billable_inspection==false || $paid_inspection==true) // Set vars to skip the payment process
			{
				$chargeResult='!';
			}
			else
			{
				//define post variables
				$first_name=mysql_real_escape_string($_POST['first_name']);
				$last_name=mysql_real_escape_string($_POST['last_name']);
				$line1=mysql_real_escape_string($_POST['line1']);
				$line2=mysql_real_escape_string($_POST['line2']);
				$city=mysql_real_escape_string($_POST['city']);
				$state=mysql_real_escape_string($_POST['state']);
				$zip=mysql_real_escape_string($_POST['zip']);
				$email=mysql_real_escape_string($_POST['email']);
				//card info
				$cc=mysql_real_escape_string($_POST['cc']);
				$expiresMonth=mysql_real_escape_string($_POST['expiresMonth']);
				$expiresYear=mysql_real_escape_string($_POST['expiresYear']);
				$cvn=mysql_real_escape_string($_POST['cvn']);
				
				$payment_type=mysql_real_escape_string($_POST['payment_type']);
				
				$merchantReferenceCode=109;
				$ipAddress=isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 0;
				
				$data=array(
					'id'=>$merchantReferenceCode,
					'firstName'=>$first_name,
					'lastName'=>$last_name,
					'line1'=>$line1,
					'line2'=>$line2,
					'city'=>$city,
					'state'=>$state,
					'zip'=>$zip,
					'email'=>$email,
					'ip'=>$ipAddress,
					//card info
					'cc'=>$cc,
					'expiresMonth'=>$expiresMonth,
					'expiresYear'=>$expiresYear,
					'cvn'=>$cvn,
					'amt'=>($payment_type=='ten_payments' ? '100.00' : '12.50'),
				);
				
				$chargeResult=billing::chargeInspector($data);
			}
			
			if($chargeResult[0]=='.')
			{
				// Some internal failure
				$error='An unknown error occured. Please contact us for help with your payment.';
			}
			elseif($chargeResult[0]=='?')
			{
				// Some failure
				$error='An error occured: '.substr($chargeResult,1);
			}
			elseif($chargeResult[0]=='!')
			{
				// SUCCESSFUL
				
				// If payment type is ten, just update their transaction count and resend them to this location
				if($payment_type=='ten')
				{
					mysql_query('update inspector set paid_transactions=10 where person='.$insp_id.' limit 1') or die(mysql_error());
					header('Location: billing-info.php?id='.$bid_id);
					exit;
				}
				
				// Update bid status
				mysql_query('update bid set status="paid", payment_type="'.$payment_type.'" where id='.$bid['id'].' limit 1');
				
				// Get the contact info for the client and/or brokers info that will be sent to the inspector
				$quote_request_result=mysql_query('select client, person as broker from quoteRequest where id='.$quote_request_id.' limit 1');
				$quote_request=mysql_fetch_assoc($quote_request_result);
				
				// Update broker transaction count
				$sql='select transaction_count from broker where person='.$quote_request['broker'].' limit 1';
				$r=mysql_query($sql) or die(mysql_error());
				$row=mysql_fetch_assoc($r);
				$broker_transaction_count=$row['transaction_count'];
				$broker_transaction_count++;
				mysql_query('update broker set transaction_count='.$broker_transaction_count.' where person='.$quote_request['broker'].' limit 1') or die(mysql_error());
				
				// Update inspector transaction count
				$sql='select transaction_count from inspector where person='.$insp_id.' limit 1';
				$r=mysql_query($sql) or die(mysql_error());
				$row=mysql_fetch_assoc($r);
				$inspector_transaction_count=$row['transaction_count'];
				$inspector_transaction_count++;
				mysql_query('update inspector set transaction_count='.$inspector_transaction_count.' where person='.$insp_id.' limit 1') or die(mysql_error());
				
				// Update inspectors paid inspection count if inspection was paid previously
				if($paid_inspection==true)
				{
					$inspectors_paid_inspections--;
					mysql_query('update inspector set paid_transactions='.$inspectors_paid_inspections.' where person='.$insp_id.' limit 1') or die(mysql_error());
				}
				
				if(!empty($quote_request['client']))
				{
					$client_result=mysql_query('select * from client where id='.$quote_request['client'].' limit 1');
					$client=mysql_fetch_assoc($client_result);
				}
				if(!empty($quote_request['broker']))
				{
					$broker_result=mysql_query('select * from broker where person='.$quote_request['broker'].' limit 1');
					$broker=mysql_fetch_assoc($broker_result);
					$broker2_result=mysql_query('select * from person where id='.$broker['person'].' limit 1');
					$broker=mysql_fetch_assoc($broker2_result);
				}
				
				$person_type=isset($client) ? 'client' : 'broker';
				$to=$email;
				$headers = 'From: '.config('site.contact_email'). "\r\n" .
'Reply-To: '.config('site.automail_reply') . "\r\n" .
'X-Mailer: PHP/' . phpversion();
				$subject='Your '.$person_type.'\'s contact information and receipt from Locizzle.com';
				$message='Hi '.$first_name.',

Thank you for using Locizzle.com! We have successfully received your payment from the card ending in '.str_repeat('*',12).substr($cc,-4,4).'. Please contact your '.$person_type.' with their contact information below to schedule a time.

Quote Request ID: '.$bid['quoteRequest'].'
Bid ID: '.$bid['id'].'

';
				if($person_type=='client')
				{
					$message.='Client First Name: '.$client['first_name'].'
Client Last Name: '.$client['last_name'].'
Client E-mail: '.$client['email'].'
Client Mobile Phone: '.$client['mobile_phone'].'

';
				}
				else
				{
					$message.='The realtor has requested that you contact them directly.
					
';
				}
					$message.='Realtor First Name: '.$broker['first_name'].'
Realtor Last Name: '.$broker['last_name'].'
Realtor E-mail: '.$broker['email'].'
Realtor Mobile Phone: '.$broker['mobile_phone'].'

Thank you for using Locizzle.com.';
		
				mail($to,$subject,$message,$headers);
				// Flag to show success page
				$successful=true;
			}
		}
	}
	
	$accordion='';
?>
<?php include './includes/header_dashboard.php'; ?>
<div class="container">
	<div class="row" style="color: #3C2111; font-size:25px;padding:20px 0px 20px 0px;">
	<!--<p style="font-size: 16px; width: 510px; margin: 0 auto 10px;">Order details and your customer's contact information will be provided for a transaction fee of $12.50. If you would like to establish a monthly account with us please contact sales@locizzle.com.</p>-->
		<?php if(!empty($error)): ?>
		<div id="error-box"><?php echo $error ?></div>
		<?php endif; ?>
		<?php if(isset($mobile)): ?>
			<div style="margin:auto;" id="accordion">
		<?php else: ?>
			<div style="width:45%; margin:auto;" id="accordion">
		<?php endif; ?>
		<?php if(isset($successful)): ?>
			<h3>Successful Payment</h3>
			<div id="billing-info">
				<p>We have successfully received your payment! You will receive a confirmation e-mail shortly containing your broker or client's contact information. Thank you for using Locizzle!</p>
			</div>
		<?php else: ?>			
			<h3>Billing Information</h3>
			<div id="billing-info">
				<form method="POST" action="" >
					<input name="first_name" type="text" class="half" value="<?php echo $first_name; ?>" placeholder="* First Name" />
					<input name="last_name" type="text" class="half" value="<?php echo $last_name; ?>" placeholder="* Last Name" /><br />
					<input name="line1" type="text" placeholder="Address 1" /></br>
					<input name="line2" type="text" placeholder="Address 2" /></br>
					<input name="city" type="text" placeholder="City" /></br>
					<select name="state">
						<option value="">State</option>
					<?php foreach($states_array as $key=>$val): ?>
						<option value="<?php echo $key ?>"><?php echo $val ?></option>
					<?php endforeach; ?>
					</select>
					<!--input name="state" type="text" placeholder="State" /--></br>
					<input name="zip" type="text" placeholder="Zip Code" /></br>
					<input name="email" type="text" placeholder="E-mail Address" /></br>
					<!--card info-->
					<input name="cc" type="text" placeholder="Credit Card Number" /></br>
					<select name="expiresMonth" class="half">
						<option value="">Exp. Month</option>
						<?php for($i=1;$i<13;$i++): ?>
							<option><?php echo str_pad($i, 2, 0,STR_PAD_LEFT) ?></option>
						<?php endfor; ?>
					</select>
					<!--input name="expiresMonth" type="text" placeholder="Exp. Month" /-->
					<select name="expiresYear" class="half">
						<option value="">Exp. Year</option>
					<?php for($i=date('Y'); $i<date('Y')+20; $i++): ?>
						<option><?php echo $i ?></option>
					<?php endfor; ?>
					</select>
					<!--input name="expiresYear" type="text" /--></br>
					<input name="cvn" type="text" placeholder="Card Security Code" /></br>
					<fieldset>
						<legend>Payment Type</legend>
						<input type="radio" name="payment_type" value="single" id="single_payment" checked="checked" />
						<label for="single_payment">I only want to purchase this inspection for $12.50.</label><br />
						<input type="radio" name="payment_type" value="ten" id="ten_payments" />
						<label for="ten_payments">I want to purchase 10 inspections for $100.00 ($10.00 each).</label>
					</fieldset>
					<input type="checkbox" name="agree_tos" value="1" /><span class="checkbox-label">Checking the box to the left confirms that you understand your card will be charged and have read and agree to our <a href="terms.php" target="_blank">terms and conditions</a>.</span><br /><br />
					<input type="submit" name="submit" value="Confirm Bid" />
				</form>
			</div>
		<?php endif; ?>
		</div>
	</div>
</div>
<?php include './includes/footer.php'; ?>
<?php
	}