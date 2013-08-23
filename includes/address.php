<?php

/**
 * 
 * Jul 27 2012 S.Tenberg, added general update method, so anybody can use class
 * 
 * @author mike
 */
//require_once('/locizzle/include/locizzle.php.inc');


class address {

    //address table 
    var $aid = 0;
    var $line1 = '';
    var $line2 = '';
    var $city = '';
    var $province = '';
    var $state = '';
    var $country = '';
    var $zip = '';
 

    /**
     * original method, hardwired for "inspectorAddress"
     * @param string $stmt
     * @param type $params 
     */
    function updateStatementAddress(&$stmt, &$params) {

        $stmt = "UPDATE address SET " .
               
                "line1 = ? , " .
                
                "city = ?  ,  " .
                "state = ? , " .
                "zip = ? " . 
                "WHERE id = ? ";
        

        $params = array('ssssi',$this->inspectorAddress->line1,
                                $this->inspectorAddress->city,
                                $this->inspectorAddress->state,
                                $this->inspectorAddress->zip,
                                $this->inspectorAddress->aid);
       
        
    }
    
    
    /**
     * general update method, usable by anybody
     * @param string $stmt
     * @param type $params 
     */
    function update(&$stmt, &$params) {

         $stmt = "UPDATE address SET " .
                "line1 = ? , " .
                "line2 = ? ,  " .
                "city = ? , " .
                "province = ? , " .
                "state = ? , " .
                "country = ? , " .
                "zip = ?  WHERE id = ? ";

        $params = array('sssssssi',
            'null',
            $this->line1,
            $this->line2,
            $this->city,
            $this->province,
            $this->state,
            $this->country,
            $this->zip, 
            $this->aid);
       
        
    }
    
    function insertStatementAddress(&$stmt, &$params) {

        $stmt = "INSERT address SET " .
                "id = ? , " .
                "line1 = ? , " .
                "line2 = ? ,  " .
                "city = ? , " .
                "province = ? , " .
                "state = ? , " .
                "country = ? , " .
                "zip = ?   ";

        $params = array('isssssss',
            'null',
            $this->line1,
            $this->line2,
            $this->city,
            $this->province,
            $this->state,
            $this->country,
            $this->zip);
    }

}

?>
