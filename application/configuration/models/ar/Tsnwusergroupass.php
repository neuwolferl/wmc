<?php

namespace AR {

    class Tsnwusergroupass extends \ActiveRecord\Model { 

        static $table_name = 'tsnw_user2group';
        static $db = "tsnw";
        static $connection = "tsnw6";
        static $belongs_to = array(
            array('user', "class_name" => "AR\Tsnwuser", "primary_key" => "userid", "foreign_key" => "id"),
            array('group', "class_name" => "AR\Tsnwgroup", "primary_key" => "groupid", "foreign_key" => "id")
        );
        

    }

}
?>