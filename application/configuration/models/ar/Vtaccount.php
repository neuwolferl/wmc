<?php

namespace AR {

    class Vtaccount extends \ActiveRecord\Model {

        static $table_name = 'vtiger_account';
        static $db = "vtiger540";
        static $primary_key = "accountid";
        static $connection = "vtiger";
        static $has_one = array(
            array("cf", "class_name" => "AR\Vtaccountscf", "primary_key" => "accountid", "foreign_key" => "accountid"),
            array("ent", "class_name" => "AR\Vtcrmentity", "primary_key" => "accountid", "foreign_key" => "crmid"),
            array("billads", "class_name" => "AR\Vtaccountbillads", "primary_key" => "accountid", "foreign_key" => "accountaddressid")
        );
        static $has_many = array(
            array("contacts", "class_name" => "AR\Vtcontact", "primary_key" => "accountid", "foreign_key" => "accountid"),
            array("potentials", "class_name" => "AR\Vtpotential", "primary_key" => "accountid", "foreign_key" => "related_to")
        );
        static $alias_attribute = array(
            'numero_cliente' => 'account_no',
            'tipo_cliente' => 'account_type',
            'id_cliente' => 'accountid',
            'ragione_sociale' => 'accountname',
            'fatturato' => 'annualrevenue',
            'dipendenti' => 'employees',
            'telefono' => 'phone',
            'email' => 'email1',
            'sito' => 'website'
        );

//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
    }

}
?>