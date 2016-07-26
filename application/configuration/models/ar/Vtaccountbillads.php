<?php

namespace AR {

    class Vtaccountbillads extends \ActiveRecord\Model {

        static $table_name = 'vtiger_accountbillads';
        static $db = "vtiger540";
        static $connection = "vtiger";
        static $alias_attribute = array(
            'citta' => 'bill_city',
            'via' => 'bill_street',
            'cap' => 'bill_code',
            'regione' => 'bill_country',
            'provincia' => 'bill_state'
        );

//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
    }

}
?>