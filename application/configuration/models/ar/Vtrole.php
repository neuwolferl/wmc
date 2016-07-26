<?php

namespace AR {

    class Vtrole extends \ActiveRecord\Model {

        static $table_name = 'vtiger_user2role';
        static $db = "vtiger540";
        static $connection = "vtiger";
        
        static $has_one = array(
            array("roledetails","class_name" => "AR\Vtroledetails", "primary_key" => "roleid", "foreign_key" => "roleid")
        );
        static $has_many = array(
            array("users2","class_name" => "AR\Vtuser", "primary_key" => "userid", "foreign_key" => "id")
        );
        static $alias_attribute = array(
            "id_utente" => "userid",
            "id_ruolo" => "roleid",
        );
//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
    }

}
?>