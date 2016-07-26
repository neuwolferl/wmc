<?php

namespace AR {

    class Vtcontact extends \ActiveRecord\Model {

        static $table_name = 'vtiger_contactdetails';
        static $db = "vtiger540";
        static $connection = "vtiger";
        static $has_one = array(
//            array("cf", "class_name" => "AR\Vtaccountscf", "primary_key" => "accountid", "foreign_key" => "accountid"),
            array("ent", "class_name" => "AR\Vtcrmentity", "primary_key" => "contactid", "foreign_key" => "crmid")
        );
        static $has_many = array(
            array("cntactivityrel","class_name" => "AR\Vtcntactivityrel", "primary_key" => "contactid", "foreign_key" => "contactid")
        );
        static $belongs_to = array(
            array("account", "class_name" => "AR\Vtaccount", "primary_key" => "accountid", "foreign_key" => "accountid"),
        );
        static $alias_attribute = array(
            'id_contatto' => "contactid",
            'numero_contatto' => "contact_no",
            'nome' => 'firstname',
            'cognome' => 'lastname',
            'email' => 'email',
            'telefono' => 'phone',
            'cellulare' => 'mobile',
            'ruolo' => 'title',
            'id_cliente' => 'accountid'
        );

//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
    }

}
?>