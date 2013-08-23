<?php

/**
 * Various billing methods
 * Right now only used for inspectors but soon to add insurance too
 *
 * @author stenberg 30-Jul-2012
 */
require_once('./includes/cybersource.php');
require_once('./includes/address.php');
require_once('./includes/transactions.php');

class billing extends address {

    // inspector #, if its an inspector we are billing:
    public $inspector = NULL;
   
   // insuranceAgent #, bill an insurance agent
    public $insuranceAgent = NULL;

// id of billingCredential:
    public $id = NULL;
    // id of address:
    public $aid = NULL;
    public $creditCardType = NULL;
    public $creditCard = NULL;
    public $unmaskedCreditCard = NULL ;
    public $CVN = NULL;
    public $unmaskedCVN = NULL ;
    public $expiresMonth = NULL;
    public $expiresYear = NULL;
    public $lastName = NULL;
    public $firstName = NULL;

    /**
     * mysql encryption key
     * @var type 
     */
    private static $SECRET = "jWihdsjhJSSSswuE76nzy4xkMHQgZqe4yTuriBb2pq1";

    /**
     * hwow much we charge inspectors
     * @var type 
     */
    public static $COST = 12.95;
    public static $AUTHCOST = '0.05' ;

    const DEBUG = false;

    /**
     * fetch a billing credential and related address
     * Note that the credit card and cvn are returned like "********1234"
     * @param type $id billing credential id
     */
    public function fetch($id) {

        $success = false;

        $query = "SELECT billingCredential.creditCardType , " .
                " AES_DECRYPT(billingCredential.creditCard , \"" . self::$SECRET . "\") 'unmaskedCard', ".
                " SUBSTRING(AES_DECRYPT(billingCredential.creditCard , \"" . self::$SECRET . "\"),-4) 'creditCard' ," .
                " AES_DECRYPT(billingCredential.CVN , \"" . self::$SECRET . "\") 'CVN', " .
                " billingCredential.expiresMonth, " .
                " billingCredential.expiresYear, " .
                " billingCredential.lastName , " .
                " billingCredential.firstName, " .
                " billingAddress, " .
                " address.line1 , " .
                " address.line2 , " .
                " address.city , address.state , address.zip " .
                " from billingCredential, address WHERE billingCredential.id = ? " .
                " and address.id = billingCredential.billingAddress ";

        $db = new transactions();


        $rows = $db->execSQL($success, $query, array('i', $id));

        if ($success) {
            $this->creditCardType = $rows[0]['creditCardType'];
            $this->CVN = "****";
            $this->unmaskedCVN = $rows[0]['CVN'] ;
            $this->creditCard = "************" . $rows[0]['creditCard'];
            $this->unmaskedCreditCard = $rows[0]['unmaskedCard'] ;
            $this->expiresMonth = $rows[0]['expiresMonth'] ;
            $this->expiresYear = $rows[0]['expiresYear'];

            $this->firstName = $rows[0]['firstName'];
            $this->lastName = $rows[0]['lastName'];

            $this->line1 = $rows[0]['line1'];
            $this->line2 = $rows[0]['line2'];
            $this->city = $rows[0]['city'];
            $this->state = $rows[0]['state'];
            $this->zip = $rows[0]['zip'];

            $this->id = $id;
            $this->aid = $rows[0]['billingAddress'];
        }

        if (self::DEBUG) {
            print_r($rows);
            var_dump($this);
            echo "\n";
        }



        return $success;
    }

    /**
     *  updates a billing credential previously fetched
     *  and returns true / false 
     *  
     */
    public function update() {

        $numberChanged = $this->creditCard[0] != '*';
        $cvnChanged = $this->CVN[0] != '*';

        $query = "UPDATE billingCredential, address SET " .
                " billingCredential.creditCardType = ? , " .
                ($numberChanged ?
                        " creditCard = AES_ENCRYPT(?,\"" . self::$SECRET . "\")," : "") .
                ($cvnChanged ?
                        " CVN = AES_ENCRYPT(?,\"" . self::$SECRET . "\")," : "") .
                " billingCredential.expiresMonth = ? , " .
                " billingCredential.expiresyear = ? , " .
                " billingCredential.lastName = ? , " .
                " billingCredential.firstName = ? , " .
                " address.line1 = ? , " .
                " address.line2 = ? , " .
                " address.city = ?  , address.state = ? , address.zip = ?" .
                " WHERE billingCredential.id =  " . $this->id .
                " and address.id = billingCredential.billingAddress ";

        //echo "\n" . '*' . $numberChanged . "!" . $cvnChanged . $query ;
        // start by assuming all 12 paramters:
        $params = array(
            NULL, $this->creditCardType, $this->creditCard, $this->CVN, $this->expiresMonth, $this->expiresYear
            , $this->lastName, $this->firstName, $this->line1, $this->line2, $this->city, $this->state, $this->zip
        );
        // but here assume only 10:
        $params[0] = 'ssssssssss';

        // Be sure to do in reverse order :-)
        if (!$cvnChanged) {

            unset($params[3]);
        }
        else
            $params[0] = $params[0] . "s";

        if (!$numberChanged) {

            unset($params[2]);
        }
        else
            $params[0] = $params[0] . "s";


        $tr = new transactions();

        $success = false;
        $tr->execSQL($success, $query, $params);

        return $success;
    }

    /**
     *    Stores a billing credential and loads up the
     *      class properties including ids.
     *    returns boolean status
     */
    public function store() {

        // create insert for billing address:
        $billingAddressQuery = NULL;
        $billingAddressParams = NULL;
        $this->insertStatementAddress($billingAddressQuery, $billingAddressParams);


        if (self::DEBUG) {
            echo "\n Billing Addr Params: \n";
            print_r($billingAddressParams);
        }

        $query = "INSERT billingCredential SET creditCardType = ? ," .
                " creditCard = AES_ENCRYPT(?,\"" . self::$SECRET . "\")" .
                ", CVN = AES_ENCRYPT(?,\"" . self::$SECRET . "\")" .
                ", expiresMonth = ?" .
                ", expiresyear = ? " .
                ", billingAddress = ? " .
                ", lastName = ?" .
                ", firstName = ? ";

        if ($this->inspector != NULL) {

            $lastQuery = "UPDATE inspector SET billingCredential = ? WHERE id = ?";
            $lastParams = array('ii', "%LASTID02%", $this->inspector);
        } else

            if ($this->insuranceAgent != NULL) {
               $lastQuery = "UPDATE ( insuranceAgentData as i ,agent) SET i.billingCredential = ? WHERE " .
                               " agent.id = ? and " .
                               " agent.insuranceAgentInfo = i.id ";
               
               $lastParams = array('ii', "%LASTID02%", $this->insuranceAgent);
            } else
            
               return NULL;


        $tr = new transactions();
        $stmts = array($billingAddressQuery, $query, $lastQuery);
        $params = array(
            $billingAddressParams,
            array('sssssiss', $this->creditCardType, $this->creditCard, $this->CVN, $this->expiresMonth, $this->expiresYear
                , "%LASTID01%", $this->lastName, $this->firstName),
            $lastParams
        );

        $status = false;
        $ids = $tr->transaction_list($status, $stmts, $params);


        if ($status) {
            $this->id = $ids[2];
            $this->aid = $ids[1];
        }

        return $status;
    }

    public static function test() {

        $b = new billing();

        $b->inspector = 123;

        $b->creditCardType = 'Mastercard';
        $b->creditCard = '4111111111111111';
        $b->expiresMonth = 12;
        $b->expiresYear = 2020;
        $b->firstName = 'John';
        $b->lastName = 'Doe';
        $b->CVN = '123';
        $b->city = 'Baltimore';
        $b->state = 'MD';
        $b->line1 = "1414 Heathfield Rd.";
        $b->zip = '21212';

        if (self::DEBUG)
            print_r($b);

        if (!$b->store()) {
            echo "Store failed\n";
            return;
        }
        else
            echo "Store worked, id# " . $b->id;

        // now fetch the one we just created:

        $c = NULL;
        $c = $b->fetch($b->id);
        if (!$c) {
            echo "Fetch failed\n";
            return;
        }

        // and change the name:
        $b->lastName = "Obama";
        $b->CVN = "9999";

        $status = $b->update();

        return $status;
    }

    /**
     *
     * @param type $template
     * @param type $email
     * @param type $name
     * @param type $reason
     * @return boolean 
     */
    private static function sendEmail($template, $email, $name, $reason) {


        phpmail::send(NULL, $email, 'Problem with your credit card', $template, array('name' => $name,
            'reason' => $reason));

        return false;
    }

    /**
     * Create or update billing record
     * @param type $bid     id of accepted bid
     * @param type $amount
     * @param type $status  either 'good' or 'failure'
     * @param type $message 
     */
    private static function storeBilling($bid, $amount, $status, $message, $query) {


        $success = false;

        $params = array('isss', $bid, $amount, $status, $message);

        $tr = new transactions();

        $success = false;
        $tr->execSQL($success, $query, $params);
        //echo $query ;
        //print_r( $params) ;
        return $success;
    }

    
      /**
     * This will to an authorize/hold for a nickel. The dataElements passed in 
       * $row are preserved in the caller's billing class - $this - for the subseqent call to
       * update/store of the credentials at post time.
       * .
     * returns the authorization code
     * The first character is a flag:
     *   ! = sucess
     *   ? = failure
     *   . = internal failure
     * @param array $row 
     */
    public function validateCard(Array $row) {

        $cc = new ChargeCard();        
        
        //validation only
        $cc->ccCapture_run = false ;
        
        // Order #:
        $cc->merchantReferenceCode = $row['id'];
         
        // User information:
        $cc->firstName = $row['firstName'];
        $this->firstName = $cc->firstName;
        
        $cc->lastName = $row['lastName'];
        $this->lastName = $cc->lastName ;
        
        $cc->streetAddress = $row['line1'] . ' ' . $row['line2'];
        $this->line1 =  $row['line1'] ;
        $this->line2 =  $row['line2'] ;
        
        
        $cc->city = $row['city'];
        $this->city = $cc->city;
        
        $cc->state = $row['state'];
        $this->state = $cc->state ;
        
        $cc->zipCode = $row['zip'];
        $this->zip = $cc->zipCode ;
        
        $cc->country = 'US';
        $cc->email = $row['email'];
        $cc->ipAddress = $row['ip'];

        // Credit card information:
        $cc->cardNumber = $row['cc'];
        $this->creditCard = $cc->cardNumber;
        
        $cc->cardMonth = $row['expiresMonth'];
        $this->expiresMonth = $cc->cardMonth ;
        
        $cc->cardYear = $row['expiresYear'];
        $this->expiresYear = $cc->cardYear ;
        
        $cc->cardCvn = $row['cvn'];
        $this->CVN = $cc->cardCvn ;
        
        // Order price (can be multiple entries):
        $cc->unitPrices[] = self::$AUTHCOST; // item 1 (required) - can also do: array_push( $cc->unitPrices, '12.50' );
        // Initialize return variables
        $AuthorizationNumber = 0;
        $AuthorizationID = 0;
        $ErrorText = 0;

        // Process order:
        $reply = $cc->Authorize($AuthorizationNumber, $AuthorizationID, $ErrorText);

        if ($reply === true) {
             return "!" . $AuthorizationID;
        } else if ($AuthorizationNumber != '') {
            return "?" . $ErrorText;
        } else {
            return "." . "Sorry an Internal Error Occurred";
        }
    }

   
    
    
    /*
     *  This function bills all inspectors for quotes accepted
     *  Right now that is a very arbitrary 4 days after acceptance
     */


    
    
    /**
     * Actually charge a card.  Pass in the row from the result set.
     * returns the authorization code
     * The first character is a flag:
     *   ! = sucess
     *   ? = failure
     *   . = internal failure
     * @param array $row 
     */
	/*$data=array(
		'firstName'=>'Nick',
		// ...
		'line1'=>'1012 Fuck St.',
	);
	$chargeResult=billing::chargeInspector($data);
	if($chargeResult[0]=='.')
	{
		// Some internal failure
	}
	elseif($chargeResult[0]=='?')
	{
		// Some failure
	}
	elseif($chargeResult[0]=='!')
	{
		// Successfuk
	}
	
	*/
    public static function chargeInspector(Array $row) {

        $cc = new ChargeCard();

        // Order #:
        $cc->merchantReferenceCode = $row['id'];

        // User information:
        $cc->firstName = $row['firstName'];
        $cc->lastName = $row['lastName'];
        $cc->streetAddress = $row['line1'] . ' ' . $row['line2'];
        $cc->city = $row['city'];
        $cc->state = $row['state'];
        $cc->zipCode = $row['zip'];
        $cc->country = 'US';
        $cc->email = $row['email'];
        $cc->ipAddress = $row['ip'];

        // Credit card information:
        $cc->cardNumber = $row['cc'];
        $cc->cardMonth = $row['expiresMonth'];
        $cc->cardYear = $row['expiresYear'];
        $cc->cardCvn = $row['cvn'];

        // Order price (can be multiple entries):
        $cc->unitPrices[] = strval($row['amt']); // item 1 (required) - can also do: array_push( $cc->unitPrices, '12.50' );
        // Initialize return variables
        $AuthorizationNumber = 0;
        $AuthorizationID = 0;
        $ErrorText = 0;

        //echo "   Please wait, attempting to process your order...\n";

        // Process order:
        $reply = $cc->Authorize($AuthorizationNumber, $AuthorizationID, $ErrorText);

        if ($reply === true) {
            //echo "Success, # $AuthorizationNumber Authorization ID: $AuthorizationID\n"; // Charged successfully, store $AuthorizationID to db
            return "!" . $AuthorizationID;
        } else if ($AuthorizationNumber != '') {
            //echo "Failure: $ErrorText\n";  // Charge declined, $AuthorizationNumber contains the error code and ErrorText contains the message to display
			return "?" . $ErrorText;
        } else {
            //echo "An error occurred!\n"; // Something bad happened (such as an exception), display a simple error. $ErrorText contains details that can be logged
            //echo "Internal error details: $ErrorText\n"; // this shouldn't be shown to the user
            return "." . "Sorry an Internal Error Occurred";
        }
    }

    /*
     *  This function bills all inspectors for quotes accepted
     *  Right now that is a very arbitrary 4 days after acceptance
     */

    public static function chargeInspectors($real) {

        echo "Starting billing, real = $real \n" ;
        //phpmail::sendProgrammerEmail("foo", "to you") ;
        
        $query = file_get_contents("/locizzle/data/queries/billing/find.sql");
        $insert = file_get_contents("/locizzle/data/queries/billing/insert.sql");

        $success = false;

        $db = new transactions();

        $success = NULL;
        $rows = $db->execSQL($success, $query, array('ss', self::$SECRET, self::$SECRET));

        if ($success) {

            if (count($rows))
             foreach ($rows as $row) {
                if (self::DEBUG)
                    print_r($row);

                // first, maybe they don't even have a billing credential?
                // in this case we store a failure and send an email
                // the first time, and every 5th time

                if ($row['expiresMonth'] == NULL) {
                    echo "  quote Request id# " . $row['id'] . " has no credit card" . "\n";

                    if ($row['numberAttempts'] == NULL | ($row['numberAttempts'] % 5 == 0)) {
                        echo "    sending mail\n";
                        if ($real)
                            self::sendEmail("problemWithYourCreditCard", $row['email'], $row['inspectorFirst'] . 
                                      ' ' . $row['inspectorLast'], "no credit card on file");
                    }

                    if ($real)
                        self::storeBilling($row['bid.id'], self::$COST, 'failure', 'No credit card on File', $insert);

                    continue;
                }

                // OK they appear to have a credit card... try to bill them
                echo "\n  billing... \n";
                if ($real) {
                    $message = self::chargeInspector($row);

                    self::storeBilling($row['bid.id'], self::$COST, ($message[0] == "!" ? 'good' : 'failure'), $message, $insert);
                    
                    if ($message[0] == ".") 
                            phpmail::sendProgrammerEmail("Credit Card Issue for quoteRequest " . $row['id'] , $message ) ;
                    else 
                    if  ($message[0] == "?") 
                        if ($row['numberAttempts'] == NULL | ($row['numberAttempts'] % 5 == 0)) {
                        self::sendEmail("problemWithYourCreditCard", $row['email'], $row['inspectorFirst'] . 
                                      ' ' . $row['inspectorLast'], substr($message,1) );
                        echo "\nSending email...\n" ;
                        }
                        
                }
            }
        }
    }

}

?>
