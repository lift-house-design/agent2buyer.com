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
		
		query('
			alter table
				inspectorsQuoteRequests
			add last_updated datetime not null
		');
		
		query('commit');
	}
	catch(Exception $e)
	{
		query('rollback');
		echo 'EXCEPTION THROWN';
	}

	echo 'done';
