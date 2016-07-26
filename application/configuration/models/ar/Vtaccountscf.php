<?php

namespace AR {

    class Vtaccountscf extends \ActiveRecord\Model {

        static $table_name = 'vtiger_accountscf';
        static $db = "vtiger540";
        static $connection = "vtiger";
        static $has_one = array(
            array("acc", "class_name" => "AR\Vtaccount", "primary_key" => "accountid", "foreign_key" => "accountid"),
            array("ent", "class_name" => "AR\Vtcrmentity", "primary_key" => "accountid", "foreign_key" => "crmid"),
            array("billads", "class_name" => "AR\Vtaccountbillads", "primary_key" => "accountid", "foreign_key" => "accountaddressid")
        );
        static $alias_attribute = array(
            'partita_iva' => 'cf_641',
            'codice_fiscale' => 'cf_642',
            'venditore' => 'cf_643',
            'telemarketing' => 'cf_644',
            'fascia_fatturato' => 'cf_703',
            'classe_dipendenti' => 'cf_704',
            'forma_giuridica' => 'cf_705',
            'fascia_fatturato_com' => 'cf_717',
            'classe_dipendenti_com' => 'cf_718',
            'forma_giuridica_com' => 'cf_719',
            'fascia_fatturato_ana' => 'cf_725',
            'classe_dipendenti_ana' => 'cf_723',
            'forma_giuridica_ana' => 'cf_724'
        );

//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
    }

}
?>