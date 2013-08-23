<?php
    /* local, dev, or live */
	//$_GLOBALS['environment'] = 'local';
	$_GLOBALS['environment'] = 'live';

	if($_GLOBALS['environment'] == 'local'){
		// local config
		return array(
			// Database connection info
			'mysql.host'=>'localhost',
			'mysql.user'=>'root',
			'mysql.pass'=>'root',
			'mysql.dbname'=>'locizzle',
			// If dev mode is true, mysql errors, etc will be displayed
			'site.dev_mode'=>false,
			// General contact e-mail address
			'site.contact_email'=>'bain.lifthousedesign@gmail.com',
			'site.automail_reply'=>'bain.lifthousedesign@gmail.com',
			'site.domain'=>'http://local.agent2buyer.com',
//			'paypalapi.user'=>'mike_api1.mvbeattie.com',
//			'paypalapi.pass'=>'AWNLPFJ94XLH2PGT',
//			'paypalapi.signature'=>'Akqw7M.hxQFSuBIVoKbQVW35wKlKAgUBfkbBvPP5td.0WQtU0hEmemrh',
			'twilio.number'=>'+15128618405',
		);
	}elseif($_GLOBALS['environment'] == 'dev'){
		// development config
		return array(
			// Database connection info
			'mysql.host'=>'',
			'mysql.user'=>'',
			'mysql.pass'=>'',
			'mysql.dbname'=>'',
			// If dev mode is true, mysql errors, etc will be displayed
			'site.dev_mode'=>false,
			// General contact e-mail address
			'site.contact_email'=>'Locizzle@locizzle.com',
			'site.automail_reply'=>'support@locizzle.com',
			'site.domain'=>'http://www.locizzle.com',
			'twilio.number'=>'+15128618405',
		);
	}elseif($_GLOBALS['environment'] == 'live'){
		// live config
		return array(
			// Database connection info
			'mysql.host'=>'localhost',
			'mysql.user'=>'thomas_agent2buy',
			'mysql.pass'=>'88oU~v@a~.W-',
			'mysql.dbname'=>'thomas_agent2buyer',
			// If dev mode is true, mysql errors, etc will be displayed
			'site.dev_mode'=>false,
			// General contact e-mail address
			'site.contact_email'=>'mike.lifthousedesign@gmail.com',
			'site.automail_reply'=>'support@agent2buyer.com',
			'site.domain'=>'http://agent2buyer.com',
		);
	}
?>
