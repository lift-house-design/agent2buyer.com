<?php

	/*
	|--------------------------------------------------------------------------
	| Process Bid Reminders
	|--------------------------------------------------------------------------
	|
	|	This script finds all inspectors who have responded with "yes" to a
	|	quote request, but never replied with a bid. It will notify these
	|	inspectors of their pending bid, and give them an option to cancel
	|	it in order to receive new quote requests.
	|
	*/

	require ('./includes/functions.php');

	// Find bids that are awaiting a price and are older than 5 minutes
	$sql='
		select
			*
		from
			inspectorsQuoteRequests
		where
			last_updated != "0000-00-00 00:00:00" and
			status = 1 and
			last_updated < subtime(now(),"00:05:00")
	';
	$result=mysql_query($sql) or die(mysql_error());

	require_once('./Services/Twilio.php');
	$account_sid = "AC295178e1f333781132528cd16d55e49b"; // Twilio account sid
	$auth_token = "81905b30336cc2fb674adf13e3f17fb2"; // Twilio auth token

	while($row=mysql_fetch_assoc($result))
	{
		echo "Iteration 1<br />";

		$sql='select * from quoteRequest where id='.$row['quoteRequest'];
		$quoteRequest_result=mysql_query($sql) or die(mysql_error());
		if($quoteRequest=mysql_fetch_assoc($quoteRequest_result))
		{
			$sql='select * from person where id='.$row['inspector'];
			$inspector_result=mysql_query($sql) or die(mysql_error());
			if(($inspector=mysql_fetch_assoc($inspector_result)) && $inspector['text_capable'])
			{
				$mobile_phone=$inspector['mobile_phone'];
				$smsBody='We have not yet received your quote for an inspection for the property: '.config('site.domain').'/property.php?id='.$quoteRequest['property']. ' . Please provide a quote using numerals (ex: $1,000), or dismiss the bid by responding with "dismiss" or "no". Thanks.';

				$client = new Services_Twilio($account_sid, $auth_token);
				$message = $client->account->sms_messages->create(
					'+15128618405', // From a Twilio number in your account
					$mobile_phone, // Text any number
					$smsBody
				);

				echo 'Sent reminder to inspector '.$inspector['first_name'].' '.$inspector['last_name']."\r\n";
			}
			else
			{
				echo "FAILED AT ".__LINE__.'<br />'.$sql;
				var_dump($inspector);
				continue;
			}
		}
		else
		{
			echo "FAILED AT ".__LINE__.'<br />'.$sql;
			var_dump($quoteRequest);
			continue;
		}
	}