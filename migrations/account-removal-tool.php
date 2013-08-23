<?php

	/*
	|--------------------------------------------------------------------------
	| Account Removal Tool
	|--------------------------------------------------------------------------
	|
	| This tool simply removes accounts and associated data, given the e-mail
	| address or phone number of desired user(s) to be removed.
	|
	|--------------------------------------------------------------------------
	*/

	require('../includes/functions.php');
	define('NL',"\r\n");
	
	
	$emails_to_remove=array(
		'mike@mvbeattie.com',
		'mbeattie@locizzle.com'
	);
	
	$phones_to_remove=array(
		'(469) 554-0027',
		'(512) 627-5557',
	);
	
	function delete_person($person,$found_with=FALSE)
	{
		echo 'Found '.$person['first_name'].' '.$person['last_name'].( $found_with===FALSE ? '' : ' with '.$found_with ).NL;
			
		$sql='
			delete from
				person
			where
				id = '.$person['id'].'
			limit 1
		';
		mysql_query($sql) or die(mysql_error());
		echo 'Deleted person ('.$person['id'].')'.NL;
		
		/*
		|--------------------------------------------------------------------------
		| Check if person is a broker
		|--------------------------------------------------------------------------
		*/
		$sql='
			select
				*
			from
				broker
			where
				person = '.$person['id'].'
			limit 1
		';
		$broker_result=mysql_query($sql) or die(mysql_error());
		
		if($broker=mysql_fetch_assoc($broker_result))
		{
			echo 'Person ('.$person['id'].') is a broker'.NL;
			
			// Broker
			$sql='
				delete from
					broker
				where
					id = '.$broker['id'].'
				limit 1
			';
			mysql_query($sql) or die(mysql_error());
			echo 'Deleted broker ('.$broker['id'].')'.NL;
			
			// Client relationships
			$sql='
				delete from
					brokersClients
				where
					broker = '.$person['id'];
			mysql_query($sql) or die(mysql_error());
			echo 'Deleted broker\'s clients relationships'.NL;
			
			// Inspector relationships
			$sql='
				delete from
					brokersInspectors
				where
					broker = '.$person['id'];
			mysql_query($sql) or die(mysql_error());
			echo 'Deleted broker\'s inspectors relationships'.NL;
			
			$sql='
				select
					*
				from
					quoteRequest
				where
					person = '.$person['id'];
			$quote_request_result=mysql_query($sql) or die(mysql_error());
			
			while($quote_request=mysql_fetch_assoc($quote_request_result))
			{
				echo 'Person ('.$person['id'].') has a quote request ('.$quote_request['id'].')'.NL;
				
				$sql='
					delete from
						quoteRequest
					where
						id = '.$quote_request['id'].'
					limit 1
				';
				mysql_query($sql) or die(mysql_error());
				echo 'Deleted broker\'s quote request ('.$quote_request['id'].')'.NL;
				
				$sql='
					delete from
						property
					where
						id = '.$quote_request['property'].'
					limit 1
				';
				mysql_query($sql) or die(mysql_error());
				echo 'Deleted property for quote request ('.$quote_request['id'].')'.NL;
				
				$sql='
					delete from
						bid
					where
						quoteRequest = '.$quote_request['id'].'
				';
				mysql_query($sql) or die(mysql_error());
				echo 'Deleted bids for quote request ('.$quote_request['id'].')'.NL;
			}
		}
		
		/*
		|--------------------------------------------------------------------------
		| Check if person is an inspector
		|--------------------------------------------------------------------------
		*/
		$sql='
			select
				*
			from
				inspector
			where
				person = '.$person['id'].'
			limit 1
		';
		$inspector_result=mysql_query($sql) or die(mysql_error());
		
		if($inspector=mysql_fetch_assoc($inspector_result))
		{
			echo 'Person ('.$person['id'].') is an inspector'.NL;
			
			// Broker
			$sql='
				delete from
					inspector
				where
					id = '.$inspector['id'].'
				limit 1
			';
			mysql_query($sql) or die(mysql_error());
			echo 'Deleted inspector ('.$inspector['id'].')'.NL;
			
			// Quote request relationships
			$sql='
				delete from
					inspectorsQuoteRequests
				where
					inspector = '.$person['id'];
			mysql_query($sql) or die(mysql_error());
			echo 'Deleted inspector\'s quote request relationships'.NL;
		}
		
		/*
		|--------------------------------------------------------------------------
		| Delete user for person
		|--------------------------------------------------------------------------
		*/
		$sql='
			delete from
				user
			where
				id = '.$person['user'].'
			limit 1
		';
		mysql_query($sql) or die(mysql_error());
		echo 'Deleted user for person ('.$person['id'].')'.NL;
	}

	foreach($emails_to_remove as $email)
	{
		$sql='
			select
				*
			from
				person
			where
				email = "'.mysql_real_escape_string($email).'"
		';
		$person_result=mysql_query($sql) or die(mysql_error());
		
		while($person=mysql_fetch_assoc($person_result))
		{
			delete_person($person,'email: '.$email);
		}
	}
	
	foreach($phones_to_remove as $phone)
	{
		$sql='
			select
				*
			from
				person
			where
				mobile_phone = "'.mysql_real_escape_string($phone).'"
		';
		$person_result=mysql_query($sql) or die(mysql_error());
		
		while($person=mysql_fetch_assoc($person_result))
		{
			delete_person($person,'phone number: '.$phone);
		}
	}	