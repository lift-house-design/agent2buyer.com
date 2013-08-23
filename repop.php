<?php
$state_list = array('AL'=>"Alabama",  
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
			
		mysql_connect ("localhost", "kaneia08_loc", "iotaalpha08");
	mysql_select_db ("kaneia08_locizzle");
$j=0;
$file=file_get_contents('zip_code_database.csv');
$rows=explode("\r\n",$file);
$a=array_shift($rows); var_dump($a);
foreach($rows as $row)
{
	
	$data=explode(',',$row);
	
	for($i=0;$i<count($data);$i++)
		$data[$i]=trim($data[$i],'" ');
	
	$zip=$data[0];
	$state_short=$data[5];
	$city=$data[2];
	$state_full=isset($state_list[$state_short]) ? $state_list[$state_short] : $state_short;
	$county=$data[6];
	$timezone=$data[7];
	$areacode=$data[8];
	$lat=$data[9];
	$long=$data[10];
	$country=$data[12];
	var_dump($data);
	$sql="insert into zip_code (zip_code, city, county, state_name, state_prefix, area_code, time_zone, lat, lon) values ('$zip','$city','$county','$state_full','".substr($state_short,0,2)."','$areacode','$timezone', $lat, $long)";
	mysql_query($sql) or die(mysql_error().$sql);
}
