<?php

namespace AR {

    class Vtactivityrel extends \ActiveRecord\Model {

        static $table_name = 'vtiger_seactivityrel';
        static $db = "vtiger540";
        static $connection = "vtiger";
        static $has_one = array(
            array("activity","class_name" => "AR\Vtactivity", "primary_key" => "activityid", "foreign_key" => "activityid"),
            array("ent","class_name" => "AR\Vtcrmentity", "primary_key" => "crmid", "foreign_key" => "crmid") // l'entità a cui l'attività è associata, non l'entità attività
        );
//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
    }

}
?>