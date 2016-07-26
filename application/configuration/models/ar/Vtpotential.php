<?php

namespace AR {

    class Vtpotential extends \ActiveRecord\Model {

        static $table_name = 'vtiger_potential';
        static $db = "vtiger540";
        static $connection = "vtiger";
        static $primary_key = "potentialid";
        static $has_one = array(
            array("cf", "class_name" => "AR\Vtpotentialscf", "primary_key" => "potentialid", "foreign_key" => "potentialid"),
            array("ent", "class_name" => "AR\Vtcrmentity", "primary_key" => "potentialid", "foreign_key" => "crmid")
        );
        static $belongs_to = array(
            array("account", "class_name" => "AR\Vtaccount", "primary_key" => "accountid", "foreign_key" => "related_to"),
        );
        static $has_many = array(
            array("activitycf", "class_name" => "AR\Vtactivitycf", "primary_key" => "potentialid", "foreign_key" => "cf_702"),
        );
        static $alias_attribute = array(
            'ammontare' => 'amount',
            'data_chiusura_attesa' => 'closingdate',
            'descrizione' => 'description',
            'fonte_lead' => 'leadsource',
            'prossimo_step' => 'nextstep',
            'numero_opportunita' => 'potential_no',
            'id_opportunita' => 'potentialid',
            'nome_opportunita' => 'potentialname',
            'tipo_opportunita' => 'potentialtype',
            'probabilita' => 'probability',
            'collegato_a' => 'related_to',
            'stadio_vendita' => 'sales_stage'
        );

//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
    }

}
?>