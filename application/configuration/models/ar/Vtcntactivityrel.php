<?php

namespace AR {

    class Vtcntactivityrel extends \ActiveRecord\Model {

        static $table_name = 'vtiger_cntactivityrel';
        static $db = "vtiger540";
        static $connection = "vtiger";
        static $has_one = array(
            array("activity","class_name" => "AR\Vtactivity", "primary_key" => "activityid", "foreign_key" => "activityid"),
            array("contact","class_name" => "AR\Vtcontact", "primary_key" => "contactid", "foreign_key" => "crmid") // il contatto a cui l'attività è associata
        );
//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
    }

}
?>