<?php

namespace AR {

    class Vtquote extends \ActiveRecord\Model {

        static $table_name = 'vtiger_quotes';
        static $db = "vtiger540";
        static $primary_key = "quoteid";
        static $connection = "vtiger";
//        static $has_one = array(
//            array("cf", "class_name" => "AR\Vtaccountscf", "primary_key" => "accountid", "foreign_key" => "accountid"),
//            array("ent", "class_name" => "AR\Vtcrmentity", "primary_key" => "accountid", "foreign_key" => "crmid"),
//            array("billads", "class_name" => "AR\Vtaccountbillads", "primary_key" => "accountid", "foreign_key" => "accountaddressid")
//        );
//        static $has_many = array(
//            array("contacts", "class_name" => "AR\Vtcontact", "primary_key" => "accountid", "foreign_key" => "accountid"),
//            array("potentials", "class_name" => "AR\Vtpotential", "primary_key" => "accountid", "foreign_key" => "related_to")
//        );
        static $alias_attribute = array(
            'numero_quote' => 'quote_no',
            'nome_quote' => 'subject',
            'id_opportunita' => 'potentialid',
            'stato_quote' => 'quotestage',
            'id_cliente' => 'accountid',
            'id_quote' => 'quoteid',
            'valido_fino' => 'validtill',
            'id_contatto' => 'contactid',
            'totale_netto' => 'subtotal',
            'totale_lordo' => 'total',
            'formato_tassazione' => 'taxtype',
            'sito' => 'website'
        );

//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
    }

}
?>