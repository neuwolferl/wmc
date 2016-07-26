<?php

namespace AR {

    class Vtactivity extends \ActiveRecord\Model {

        static $table_name = 'vtiger_activity';
        static $db = "vtiger540";
        static $connection = "vtiger";
        static $has_one = array(
            array("cf","class_name" => "AR\Vtactivitycf", "primary_key" => "activityid", "foreign_key" => "activityid"),
            array("ent","class_name" => "AR\Vtcrmentity", "primary_key" => "activityid", "foreign_key" => "crmid")
        );
        static $has_many = array(
            array("activityrel","class_name" => "AR\Vtactivityrel", "primary_key" => "activityid", "foreign_key" => "activityid"),
            array("cntactivityrel","class_name" => "AR\Vtcntactivityrel", "primary_key" => "activityid", "foreign_key" => "activityid")
        );
        static $alias_attribute = array(
            'id_attivita' => 'activityid',
            'tipo_attivita' => 'activitytype',
            'data_inizio' => 'date_start',
            'data_fine' => 'due_date',
            'durata_ore' => 'duration_hours',
            'durata_minuti' => 'duration_minutes',
            'stato' => 'eventstatus',
            'indirizzo' => 'location',
            'nome_attivita' => 'subject',
            'orario_fine' => 'time_end',
            'orario_inizio' => 'time_start'
        );
//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
    }

}
?>