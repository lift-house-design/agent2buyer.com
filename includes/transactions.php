<?php

/* Hi, I just write a function to do all my sql statements based on all the others comments in this page, maybe it can be useful for someone else :)

  Usage:

  execSQL($success,$sql, $parameters, $close);
  $success = returns true or false
  $sql = Statement to execute;
  $parameters = array of type and values of the parameters (if any)
  $close = true to close $stmt (in inserts) false to return an array with the values;
  $ignoreError  - any value will disable the printing of errors to consol
 * 
  Examples:

  $results = execSQL($success,"SELECT * FROM table WHERE id = ?", array('i', $id), false);

  $results = execSQL($success,"SELECT * FROM table", array(), false);

  $results = execSQL($success,"INSERT INTO table(id, name) VALUES (?,?)", array('ss', $id, $name), true);

  execSQL returns true/false in $success - $results are returned.

  rev - mc - 04/11/2012  - added transaction_list function.
 *                         see function testThis for example
 */
/**
 * Description of transaction
 *
 * @author mike
 */
//require_once '/locizzle/include/locizzle.php.inc';

class transactions {

    static function refValues($arr) {
        if (strnatcmp(phpversion(), '5.3') >= 0) { //Reference is required for PHP 5.3+
            $refs = array();
            foreach ($arr as $key => $value)
                $refs[$key] = &$arr[$key];

            return $refs;
        }
        return $arr;
    }

    static function execSQL(&$success, $sql, $params, mysqli &$mysqli = null , $ignoreError = null) {
        
        if ($mysqli == null) {
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
            $dont_close_connection = false;
        } else {
            $dont_close_connection = true;
        }

       // error_log(var_dump($sql,$params),3,'/var/tmp/my-debug.log') ;
       // error_log(var_dump($params),3,'/var/tmp/my-debug.log') ;
        $stmt = $mysqli->prepare($sql);
       
        if ($mysqli->errno > 0) {
            if ( $ignoreError == null ) {
                print "Prepare result: (" . $mysqli->errno . ") " . $mysqli->error . "\n";
                var_dump($sql, $params); // for console log
            }
            
            $success = false;
            return null;
        }


        if (count($params) > 0) 
            call_user_func_array(array($stmt, 'bind_param'), transactions::refValues($params));
        
        if ($stmt->execute() == false) {
            if ( $ignoreError == null) {
            print "execute failed: (" . $mysqli->errno . ") " . $mysqli->error . "\n";
            var_dump($sql, $params); //for console log
            }
            $success = false;
            return null;
        }

        if (!stristr(substr($sql, 0, 10), "select")) { //replaced $close param with this                                                         
            $result = $mysqli->affected_rows;
            $success = true;
        } else {

            $meta = $stmt->result_metadata();
            while ($field = $meta->fetch_field()) {
                $parameters[] = &$row[$field->name];
            }
      
            call_user_func_array(array($stmt, 'bind_result'), transactions::refValues($parameters));

            //added this to stop run time error 
            // to when no rows to fetch below.
            $results = null;
            while ($stmt->fetch()) {
                $x = array();
                foreach ($row as $key => $val) {
                    $x[$key] = $val;
                }
                $results[] = $x;
            }

            $result = $results;
        }

        $stmt->close();
        if ($dont_close_connection == false) {
            $mysqli->close();
        }
        $success = true;

        return $result;
    }

    static public function transaction_list(&$success, $stmts, $params) {
        /**
         * This takes and array of statements and and array of params.
         * build a list of lastids on insters and replaces the any parameter
         * with %LASTID99%  is replaced with corresponding lastid in the 
         * holding array.
         *  Returns true or false  - See testThis for example
         * Note that for single insters/updates call execSQL instead since
         * it returns success/failure as well as a result. 
         */
        $good2go = true;
        $cur_cnt = 0;
        $last_ids = array(count($stmts));
        $last_id_cnt = 1;

        //var_dump($stmts,$params) ;

        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
        if (count($stmts) > 1)
            $mysqli->autocommit(FALSE);

        While ($good2go and ( $cur_cnt < count($stmts))) {
            $p = $params[$cur_cnt];
            $s = $stmts[$cur_cnt];

            /* Replace any %lastid99% tokens with the lastids stored from the 
              previous inserts */
           
            for ($i = 1; $i < count($p); $i++) { //change this loop to start at 1 to skip the 'isssiiss' string
                if ($p[0][$i-1] != "b" ) {  //skip blobs
                $foo = stripos($p[$i], '%LAST');
                if ($foo > -1) {
                    $p[$i] = $last_ids[(int) substr($p[$i], 7, 2)];
                }}
                
            }

            $results = transactions::execSQL($success, $s, $p, $mysqli);

            if ($success == false)
                $good2go = false;
            else {
                if (stristr(substr($s, 0, 10), "INSERT")) {
                    $last_ids[$last_id_cnt++] = $mysqli->insert_id;
                }
            }
            
            $cur_cnt = $cur_cnt + 1;
        }

        if ($good2go) {
            $mysqli->commit();
            $mysqli->close();
        } else {
            $mysqli->rollback();
            $mysqli->close();
        }
        return $last_ids;
    }

    public function testThis() {

        $tester = new transactions();
        $stmts = array('Select * from user where id = ?',
            'drop table if exists test_table_for_php_transaction_class ',
            'CREATE TABLE test_table_for_php_transaction_class ( id INT, data VARCHAR(100) )',
            'ALTER TABLE `test_table_for_php_transaction_class` ADD PRIMARY KEY( `id`)',
            "ALTER TABLE `test_table_for_php_transaction_class` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT",
            "INSERT INTO test_table_for_php_transaction_class (id, data) VALUES (null,?)",
            "INSERT INTO test_table_for_php_transaction_class (id, data) VALUES (null,?)",
            "INSERT INTO test_table_for_php_transaction_class (id, data) VALUES (null,?)",
            "INSERT INTO test_table_for_php_transaction_class (id, data) VALUES (null,?)",
            "INSERT into user (  id  , role  ,passwordChecksum  ,disabled  ,locked  , numberFailedLogins) values ( ?,?,?,?,?,? )",
            "Select * from test_table_for_php_transaction_class where id > ?"
                //   " This is a test %LASTID01% of this string. Here is %lastid02% and then %lastid03% and then %lastid04% done",
        );
        //  'update user  set username="tester2" where id = ?',
        //  'update user3  set username="tester3" where id > ?') ;

        $params = array(
            array('i', '2'),
            array(),
            array(),
            array(),
            array(),
            array('s', 'TESTIT'),
            array('s', 'TESTIT1'),
            array('s', 'TESTIT2'),
            array('s', 'TESTIT3'),
            array('issssi', 'null', 'cunsomer', 'dfdf', 'true', 'true', '0'),
            array('i', '0'),
            array(),
        );

        if ($tester->transaction_list($stmts, $params) == 1)
            print "Success " . "\n";
        else
            print "Failed " . "\n";
    }

    public function __construct() {
        
    }

}

?>
