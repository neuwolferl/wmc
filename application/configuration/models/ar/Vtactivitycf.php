<?php

namespace AR {

    class Vtactivitycf extends \ActiveRecord\Model {

        static $table_name = 'vtiger_activitycf';
        static $db = "vtiger540";
        static $connection = "vtiger";
        static $has_one = array(
            array("act", "class_name" => "AR\Vtactivity", "primary_key" => "activityid", "foreign_key" => "activityid"),
            array("ent", "class_name" => "AR\Vtcrmentity", "primary_key" => "activityid", "foreign_key" => "crmid")
        );
        static $alias_attribute = array(
            'telemarketing' => 'cf_649',
            'venditore' => 'cf_650',
            'tipo_attivita_business' => 'cf_651',
            'id_google_calendar' => 'cf_652',
            'id_opportunita' => 'cf_702'
        );

//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
    }

}
?>