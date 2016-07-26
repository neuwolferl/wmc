<?php

namespace AR {

    class Bank extends \ActiveRecord\Model {

        static $table_name = 'external_banks';
        static $db = "vtiger540";
        static $primary_key = "bankid";
        static $connection = "vtiger";
        static $has_many = array(
            array("collections", "class_name" => "AR\Collection", "primary_key" => "bankid", "foreign_key" => "bankid")
        );
        static $alias_attribute = array(
            'id_banca' => 'bankid',
            'abi' => 'bankabi',
            'cab' => 'bankcab',
            'nome' => 'bankname',
            'descrizione' => 'bankdescription',
            'indirizzo' => 'bankstreet',
            'cap' => 'bankcode',
            'citta' => 'bankcity',
            'provincia' => 'bankprovince'
        );

//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
    }

}
?>