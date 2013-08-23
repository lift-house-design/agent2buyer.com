<?php

	require('../includes/functions.php');
	
	define('NL',"\r\n");
	define('T',"\t");
	
	function query($sql)
	{
		$result=mysql_query($sql);
		
		if(!$result)
		{
			echo 'Error: '.T.T.mysql_error().NL;
			echo 'Query: '.T.T.trim($sql).NL;
			throw new Exception;
			return false;
		}
		
		return $result;
	}
	
	try
	{
		query('begin');
		
		/**
		 * create company table
		 */
		/*query('
			CREATE TABLE `company` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`name` varchar(64) NOT NULL,
				`owners_first_name` varchar(64) NOT NULL,
				`owners_last_name` varchar(64) NOT NULL,
				`owners_email` varchar(64) NOT NULL,
				`owners_phone` varchar(14) NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB
		');
		
		$broker_result=query('select * from broker');
		$broker_map=array();
		while($row=mysql_fetch_assoc($broker_result))
		{
			query('
				insert into
					company
					(
						name,
						owners_first_name,
						owners_last_name,
						owners_email,
						owners_phone
					)
					values
					(
						"'.mysql_real_escape_string($row['company_name']).'",
						"'.mysql_real_escape_string($row['company_owner_firstname']).'",
						"'.mysql_real_escape_string($row['company_owner_lastname']).'",
						"'.mysql_real_escape_string($row['company_owner_email']).'",
						"'.mysql_real_escape_string($row['company_owner_phone']).'"
					)
			');
			
			$broker_map[ $row['id'] ]=mysql_insert_id();
		}
		
		query('
			alter table
				broker
			drop company_name,
			drop company_owner_firstname,
			drop company_owner_lastname,
			drop company_owner_email,
			drop company_owner_phone
		');
		
		query('
			alter table
				broker
			add company_id int null after person
		');
		
		foreach($broker_map as $broker_id=>$company_id)
		{
			query('
				update
					broker
				set
					company_id = '.$company_id.'
				where
					id = '.$broker_id.'
				limit 1
			');
		}*/
		
		/**
		 * create widget table
		 */
		
		query('
			CREATE TABLE `widget` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`token` varchar(16) NOT NULL,
				`company_id` int(11) NOT NULL,
				`broker_id` int(11) NOT NULL COMMENT \'Should be the actual brokers ID\',
				`background_color` varchar(6) NOT NULL,
				`foreground_color` varchar(6) NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB
		');
		
		query('commit');
	}
	catch(Exception $e)
	{
		query('rollback');
		echo 'EXCEPTION THROWN';
	}

	echo 'done';
