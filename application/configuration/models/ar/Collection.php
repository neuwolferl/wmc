<?php

namespace AR {

    class Collection extends \ActiveRecord\Model {

        static $table_name = 'external_collections';
        static $db = "vtiger540";
        static $primary_key = "id";
        static $connection = "vtiger";
        static $has_one = array(
            array("account", "class_name" => "AR\Vtaccount", "primary_key" => "accountid", "foreign_key" => "accountid"),
            array("bank", "class_name" => "AR\Bank", "primary_key" => "bankid", "foreign_key" => "bankid"),
        );
        static $has_many = array(
            array("collectionso", "class_name" => "AR\Collectionso", "primary_key" => "id", "foreign_key" => "idcollection")
        );
        static $alias_attribute = array(
            'id_incasso' => 'id',
            'ammontare' => 'amount',
            'id_cliente' => 'accountid',
            'tipo' => 'type',
            'stato' => 'state',
            'riferimento' => 'ref',
            'id_banca' => 'bankid',
            'data_emissione' => 'emissiondate',
            'data_ricezione' => 'receiptdate',
            'data_versamento' => 'depositdate',
            'data_valuta' => 'valuedate',
            'id_nostra_banca' => 'ourbankid'
        );

//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
    }

}
?>