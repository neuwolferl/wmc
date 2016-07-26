<?php

namespace AR {

    class Vtcrmentity extends \ActiveRecord\Model {

        static $table_name = 'vtiger_crmentity';
        static $db = "vtiger540";
        static $connection = "vtiger";
        static $has_many = array(
            array("activityrel","class_name" => "AR\Vtactivityrel", "primary_key" => "crmid", "foreign_key" => "crmid")
        );
        static $alias_attribute = array(
            'descrizione' => 'description'
        );
//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
    }

}
?>