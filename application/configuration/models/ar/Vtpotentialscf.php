<?php

namespace AR {

    class Vtpotentialscf extends \ActiveRecord\Model {

        static $table_name = 'vtiger_potentialscf';
        static $db = "vtiger540";
        static $connection = "vtiger";
        static $has_one = array(
            array("pot", "class_name" => "AR\Vtpotential", "primary_key" => "potentialid", "foreign_key" => "potentialid"),
            array("ent", "class_name" => "AR\Vtcrmentity", "primary_key" => "accountid", "foreign_key" => "crmid")
        );
        static $belongs_to = array(
            array("account", "class_name" => "AR\Vtaccount", "primary_key" => "potentialid", "foreign_key" => "accountid")
        );
        static $alias_attribute = array(
            'telemarketing' => 'cf_646',
            'venditore' => 'cf_647',
            'esito_visita' => 'cf_648',
            'analista' => 'cf_655',
            'esito_conferma' => 'cf_656',
            'ammontare_pesato' => 'cf_645',
            'pot_analisi_collegata' => 'cf_698',
            'grace_scadenza' => 'cf_721'
        );

//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
    }

}
?>