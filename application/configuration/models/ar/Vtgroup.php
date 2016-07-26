<?php

namespace AR {

    class Vtgroup extends \ActiveRecord\Model {

        static $table_name = 'vtiger_users2group';
        static $db = "vtiger540";
        static $connection = "vtiger";
        static $belongs_to = array(
            array("user","class_name" => "AR\Vtuser", "primary_key" => "roleid", "foreign_key" => "roleid")
        );
        static $has_one = array(
            array("groupdetails","class_name" => "AR\Vtgroupdetails", "primary_key" => "groupid", "foreign_key" => "groupid")
        );
        static $alias_attribute = array(
            "id_utente" => "userid",
            "id_gruppo" => "groupid"
        );

//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
    }

}
?>