<?php

namespace AR {

    class Vtuser extends \ActiveRecord\Model {

        static $table_name = 'vtiger_users';
        static $db = "vtiger540";
        static $connection = "vtiger";
        static $has_one = array(
            array("role", "class_name" => "AR\Vtrole", "primary_key" => "id", "foreign_key" => "userid")
        );
        static $has_many = array(
            array("groups", "class_name" => "AR\Vtgroup", "primary_key" => "id", "foreign_key" => "userid")
        );
        static $alias_attribute = array(
            "id_utente" => "id",
            "nome_utente" => "user_name",
            "nome" => "first_name",
            "cognome" => "last_name",
            "email" => "email1",
            "stato" => "status",
        );

//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
    }

}
?>