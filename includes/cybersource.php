<?
/*
	CreditCard
	S. Tenberg , March 28 2006

	Just took Cyber example and made it a class....
	--
	R. DeFusco, March 28 2006
	
	Added $ErrorText return and converted all printf to sprintf to
	return error messages to $ErrorText
	--
	R. DeFusco, June 29, 2012

	Starting updating existing creditcard class for Locizzle
	--
	R. DeFusco, July 18, 2012

	CyberSource Simple API module is 32-bit only, rewrote class to use SOAP instead
	
*/

define( 'MERCHANT_ID', '345272407882' );
define( 'WSDL_TESTING_URL', 'https://ics2wstest.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.74.wsdl' );
define( 'WSDL_PRODUCTION_URL', 'https://ics2ws.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.74.wsdl' );
define( 'TRANSACTION_KEY_FILE', __DIR__.'/CyberSourceKey_20120718104749.txt');

// Production constants
if($_GLOBALS['environment'] == 'live')
	define( 'WSDL_URL', WSDL_PRODUCTION_URL );
// Testing constants
else
	define( 'WSDL_URL', WSDL_TESTING_URL ); // production or testing?

class ExtendedClient extends SoapClient {

	function __construct($wsdl, $options = null) {
	 parent::__construct($wsdl, $options);
	}

	// This section inserts the UsernameToken information in the outgoing SOAP message.
	function __doRequest($request, $location, $action, $version) {
	
	 $user = MERCHANT_ID;
	 $password = '';

	 if( file_exists(TRANSACTION_KEY_FILE) ) {
		 $password = file_get_contents(TRANSACTION_KEY_FILE);
		 if( $password === false )
			 $password = ''; // failed to load key, this will trigger 'UsernameToken authentication failed.' error
	 }
	 
	 
	 $soapHeader = "<SOAP-ENV:Header xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:wsse=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd\"><wsse:Security SOAP-ENV:mustUnderstand=\"1\"><wsse:UsernameToken><wsse:Username>$user</wsse:Username><wsse:Password Type=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText\">$password</wsse:Password></wsse:UsernameToken></wsse:Security></SOAP-ENV:Header>";
	
	 $requestDOM = new DOMDocument('1.0');
	 $soapHeaderDOM = new DOMDocument('1.0');
	
	 try {
	
	$requestDOM->loadXML($request);
	$soapHeaderDOM->loadXML($soapHeader);
	
	$node = $requestDOM->importNode($soapHeaderDOM->firstChild, true);
	$requestDOM->firstChild->insertBefore(
	$node, $requestDOM->firstChild->firstChild);
	
	$request = $requestDOM->saveXML();
	
	//printf( "Modified Request:\n*$request*\n" );
	
	 } catch (DOMException $e) {
		die( 'Error adding UsernameToken: ' . $e->code);
	 }

	 return parent::__doRequest($request, $location, $action, $version);
	}
}

class ChargeCard {

	var $ccAuthService_run = 'true';
        var $ccCapture_run = 'true';
	var $merchantReferenceCode = '';
	var $firstName = '';
	var $lastName = '';
	var $streetAddress = '';
	var $city = '';
	var $state = '';
	var $zipCode = '';
	var $country = '';
	var $email = '';
	var $ipAddress = '';
	var $cardNumber = '';
	var $cardMonth = '';
	var $cardYear = '';
	var $cardCvn = '';
	var $purchaseCurrency = 'USD'; // default currency is USD but can be changed if needed
	var $unitPrices = array();


	/*
	Authorize a sale
	return boolean of true or false, depending on the result
	*/

	function Authorize( &$AuthorizationCode, // return authorization number,
			&$TransactionID, // return cc transaction id 
			&$ErrorText) { // return any error text

		$TransactionID = $AuthorizationCode = '' ;
		$ErrorText = '';

		try {
			$soapClient = new ExtendedClient(WSDL_URL, array());
		
			/*
			To see the functions and types that the SOAP extension can automatically
			generate from the WSDL file, uncomment this section:
			$functions = $soapClient->__getFunctions();
			print_r($functions);
			$types = $soapClient->__getTypes();
			print_r($types);
			*/
		
			$request = new stdClass();
			
			$request->merchantID = MERCHANT_ID;
			
			// Before using this example, replace the generic value with your own.
			$request->merchantReferenceCode = $this->merchantReferenceCode;
		
			// To help us troubleshoot any problems that you may encounter,
			// please include the following information about your PHP application.
			$request->clientLibrary = "PHP";
		 	$request->clientLibraryVersion = phpversion();
			$request->clientEnvironment = php_uname();
		
			// This section contains a sample transaction request for the authorization 
			// service with complete billing, payment card, and purchase (two items) information.	
			$ccAuthService = new stdClass();
			$ccAuthService->run = $this->ccAuthService_run ;
			$request->ccAuthService = $ccAuthService;
	
			$billTo = new stdClass();
			$billTo->firstName = $this->firstName;
			$billTo->lastName = $this->lastName;
			$billTo->street1 = $this->streetAddress;
			$billTo->city = $this->city;
			$billTo->state = $this->state;
			$billTo->postalCode = $this->zipCode;
			$billTo->country = $this->country;
			$billTo->email = $this->email;
			$billTo->ipAddress = $this->ipAddress;
			$request->billTo = $billTo;
		
			$card = new stdClass();
			$card->accountNumber = $this->cardNumber;
			$card->expirationMonth = $this->cardMonth;
			$card->expirationYear = $this->cardYear;
			$card->cvNumber = $this->cardCvn;
			$request->card = $card;
		
			$purchaseTotals = new stdClass();
			$purchaseTotals->currency = $this->purchaseCurrency;
			$request->purchaseTotals = $purchaseTotals;
		
			foreach( $this->unitPrices as $unitIndex=>$unitPrice ) {
				$item = new stdClass();
				$item->unitPrice = $unitPrice;
				$item->id = $unitIndex;
				$request->item[] = $item;
			}
			
			$reply = $soapClient->runTransaction($request);
			
			// This section will show all the reply fields.
			//var_dump($reply);
		
			// To retrieve individual reply fields, follow these examples.
			//printf( "decision = $reply->decision\n" );
			//printf( "reasonCode = $reply->reasonCode\n" );
			//printf( "requestID = $reply->requestID\n" );
			//printf( "requestToken = $reply->requestToken\n" );
			//printf( "ccAuthReply->reasonCode = " . $reply->ccAuthReply->reasonCode . "\n");

			$decision = $reply->decision;
			if (strtoupper( $decision ) == 'ACCEPT')
			{
				// return the requestID
				$TransactionID = $reply->requestID;
		
				// return AuthorizationCode
				$AuthorizationCode = $reply->ccAuthReply->reasonCode ;
				return true ;
			} else {
				// failed return error code
				if( isset($reply->ccAuthReply) )
					$AuthorizationCode = $reply->ccAuthReply->reasonCode ; // auth failure
				else
					$AuthorizationCode = $reply->reasonCode ; // general failure

				$prettyErrorText = array(
					100 => "Successful transaction.",
					101 => "The request is missing one or more required fields.",
					102 => "One or more fields in the request contains invalid data.",
					104 => "Duplicate merchantReferenceCode, same merchantReferenceCode already sent with in the last 15 minutes.",
					150 => "Error: General system failure.",
					151 => "Error: The request was received but there was a server timeout.",
					152 => "Error: The request was received, but a service did not finish running in time.",
					201 => "The issuing bank has questions about the request.",
					202 => "Expired card or expiration date provided does not match the date the issuing bank has on file.",
					203 => "General decline of the card. No other information provided by the issuing bank.",
					204 => "Insufficient funds in the account.",
					205 => "Stolen or lost card.",
					207 => "Issuing bank unavailable.",
					208 => "Inactive card or card not authorized for card-not-present transactions.",
					210 => "The card has reached the credit limit.",
					211 => "Invalid card verification number.",
					221 => "The customer matched an entry on the processor’s negative file.",
					231 => "Invalid account number.",
					232 => "The card type is not accepted by the payment processor.",
					233 => "General decline by the processor.",
					234 => "There is a problem with your CyberSource merchant configuration.",
					235 => "The requested amount exceeds the originally authorized amount.",
					236 => "Processor failure.",
					238 => "The authorization has already been captured.",
					239 => "The requested transaction amount must match the previous transaction amount.",
					240 => "The card type sent is invalid or does not correlate with the credit card number.",
					241 => "The request ID is invalid.",
					242 => "You requested a capture through the API, but there is no corresponding, unused authorization record.",
					246 => "Capture or credit not voidable, the capture or credit information has already been submitted to your processor.",
					247 => "You requested a credit for a capture that was previously voided.",
					250 => "Error: The request was received, but there was a timeout at the payment processor.",
					520 => "Authorization request was approved by the issuing bank but declined by CyberSource based on Smart Auth settings."
				);

				$ErrorText = (isset($prettyErrorText[$AuthorizationCode])) ? ($prettyErrorText[$AuthorizationCode]) : "An unknown error code of $AuthorizationCode occurred.";

		 		return false ;
			}
	
			// Should not happen
			$this->handleError( $status, $request, $reply, $ErrorText );
	
			return false ;
		} catch (SoapFault $exception) {
			//var_dump(get_class($exception));
			//var_dump($exception);
			$ErrorText = $exception->faultstring;
			return false;
		}
	}

	//------------------------------------------------------------------
	function handleError( $status, $request, $reply, &$ErrorText) {
		// echo "RunTransaction Status: $status\n";

		switch ($status) {
			case CYBS_S_PHP_PARAM_ERROR:
			$ErrorText = sprintf( "Please check the parameters passed to cybs_run_transaction for correctness.\n" );
			break;
	
			case CYBS_S_PRE_SEND_ERROR:
			$ErrorText = sprintf("The following error occurred before the request could be sent:\n%s\n",
				$reply[CYBS_SK_ERROR_INFO] );
			break;
	
			case CYBS_S_SEND_ERROR:
			$ErrorText = sprintf( "The following error occurred while sending the request:\n%s\n",
				$reply[CYBS_SK_ERROR_INFO] );
			break;

			case CYBS_S_RECEIVE_ERROR:
			$ErrorText = sprintf( "The following error occurred while waiting for or retrieving the reply:\n%s\n",
				$reply[CYBS_SK_ERROR_INFO] );
			handleCriticalError( $status, $request, $reply, $ErrorText );
			break;

			case CYBS_S_POST_RECEIVE_ERROR:
			$ErrorText = sprintf("The following error occurred after receiving and during processing of the reply:\n%s\n",
				$reply[CYBS_SK_ERROR_INFO] );
			handleCriticalError( $status, $request, $reply, $ErrorText );
			break;

			case CYBS_S_CRITICAL_SERVER_FAULT:
			$ErrorText = sprintf( "The server returned a CriticalServerError fault:\n%s\n",
				getFaultContent( $reply ) );
			handleCriticalError( $status, $request, $reply, $ErrorText );
			break;
	
			case CYBS_S_SERVER_FAULT:
			$ErrorText = sprintf( "The server returned a ServerError fault:\n%s\n",
				getFaultContent( $reply ) );
			break;

			case CYBS_S_OTHER_FAULT:
			$ErrorText = sprintf( "The server returned a fault:\n%s\n",
				getFaultContent( $reply ) );
			break;
 
			case CYBS_S_HTTP_ERROR:
			 $ErrorText = sprintf("An HTTP error occurred:\n%s\nResponse Body:\n%s\n",
				 $reply[CYBS_SK_ERROR_INFO], $reply[CYBS_SK_RAW_REPLY] );
			break;
		}
	}

	//-----------------------------------------------------------------------------
	// If an error occurs after the request has been sent to the server, but the
	// client can//t determine whether the transaction was successful, then the
	// error is considered critical.  If a critical error happens, the transaction
	// may be complete in the CyberSource system but not complete in your order
	// system.  Because the transaction may have been successfully processed by
	// CyberSource, you should not resend the transaction, but instead send the
	// error information and the order information (customer name, order number,
	// etc.) to the appropriate personnel at your company.  They should use the
	// information as search criteria within the CyberSource Transaction Search
	// Screens to find the transaction and determine if it was successfully
	// processed. If it was, you should update your order system with the
	// transaction information. Note that this is only a recommendation; it may not
	// apply to your business model.
	//------------------------------------------------------------------------
	function handleCriticalError( $status, $request, $reply, &$ErrorText ) {
		$replyType = '';
		$replyText = '';

		if ($status == CYBS_S_CRITICAL_SERVER_FAULT) {
			$replyType = 'FAULT DETAILS: ';
			$replyText = getFaultContent( $reply );
		} else {
			$replyText = $reply[CYBS_SK_RAW_REPLY];
			if ($replyText <> '')
				$replyType = 'RAW REPLY: ';
			else
				$replyType = "No Reply available.";
		}

		$ErrorText = sprintf( 
			"STATUS: %d\nERROR INFO: %s\nREQUEST: \n%s\n%s\n$s\n",
			nStatus, $reply[CYBS_SK_ERROR_INFO],
			getArrayContent( $request ), $replyType, $replyText );
	}

	//-----------------------------------------------------------------------
	function getFaultContent( $reply ) {
		$requestID = $reply[CYBS_SK_FAULT_REQUEST_ID];
		if ( $requestID == "")
			$requestID = "(unavailable)";

		return( sprintf(
			"Fault code: %s\nFault string: %s\nRequestID: %s\nFault document: %s",
			$reply[CYBS_SK_FAULT_CODE], $reply[CYBS_SK_FAULT_STRING],
			$requestID, $reply[CYBS_SK_FAULT_DOCUMENT] ) );
	}

	//-------------------------------------------------------------------
	function getArrayContent( $arr )
	{
		$content = '';
		while (list( $key, $val ) = each( $arr )) {
			$content = $content . $key . ' => ' . $val . "\n";
		}

		return( $content );
	}

} // end class
?>
