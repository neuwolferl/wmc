<?php

namespace AR {

    class Vtso extends \ActiveRecord\Model {

        static $table_name = 'vtiger_salesorder';
        static $db = "vtiger540";
        static $primary_key = "salesorderid";
        static $connection = "vtiger";
        static $has_one = array(
            //array("cf", "class_name" => "AR\Vtsocf", "primary_key" => "salesorderid", "foreign_key" => "salesorderid"),
            array("ent", "class_name" => "AR\Vtcrmentity", "primary_key" => "salesorderid", "foreign_key" => "crmid"),
            array("pot", "class_name" => "AR\Vtpotential", "primary_key" => "potentialid", "foreign_key" => "potentialid"),
            array("account", "class_name" => "AR\Vtaccount", "primary_key" => "accountid", "foreign_key" => "accountid")
        );
        static $alias_attribute = array(
            'id_so' => 'salesorderid',
            'soggetto' => 'subject',
            'id_opportunita' => 'potentialid',
            'numero_so' => 'salesorder_no',
            'id_preventivo' => 'quoteid',
            'id_contatto' => 'contactid',
            'scadenza' => 'duedate',
            'totale_lordo' => 'total',
            'totale_netto' => 'subtotal',
            'id_cliente' => 'accountid'
        );

//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
    }

}
?>