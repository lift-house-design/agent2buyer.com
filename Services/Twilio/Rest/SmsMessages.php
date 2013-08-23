<?php

class Services_Twilio_Rest_SmsMessages
    extends Services_Twilio_ListResource
{
    public function __construct($client, $uri) {
        $uri = preg_replace("#SmsMessages#", "SMS/Messages", $uri);
        parent::__construct($client, $uri);
    }

    function create($from, $to, $body, array $params = array()) {
    	// [Nick Niebaum] Modified the send SMS method to split a text into 
		// multiple texts if the body is greater than 160 characters
    	if(strlen($body)>160)
		{
			$messages=$this->_splitBody($body,152);
			$how_many=count($messages);
			foreach($messages as $index=>$message)
			{
				$msg_number=($index+1);
				$message='('.$msg_number.'/'.$how_many.')'.$message;
				
				parent::_create(array(
		            'From' => $from,
		            'To' => $to,
		            'Body' => substr($message,0,160)
		        ) + $params);
			}
		}
		else
		{
			return parent::_create(array(
	            'From' => $from,
	            'To' => $to,
	            'Body' => $body
	        ) + $params);
		}
    }
	
	function _splitBody($body)
	{
		$charLimit=160;
		
		// Accomodate the message number indicator
		$charLimit-=strlen('(xx/xx) ');
		$chunks=array();
		$bodyChunks=explode(' ',$body);
		$j=0;
		
		for($i=0,$len=strlen($body);$i<ceil($len/$charLimit);$i++)
		{
			while(strlen($chunks[$i]) < $charLimit)
			{
				$chunks[$i].=' '.$bodyChunks[$j++];
			}
			$chunks[$i]=trim($chunks[$i]);
		}

		
		return $chunks;
	}
}
