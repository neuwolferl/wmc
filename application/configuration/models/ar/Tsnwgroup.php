<?php

namespace AR {

    class Tsnwgroup extends \ActiveRecord\Model {

        static $table_name = 'tsnw_groups';
        static $db = "tsnw";
        static $connection = "tsnw6";
        static $primary_key = "id";
        static $has_many = array(
            array('tsnwusergroupass', "class_name" => "AR\Tsnwusergroupass", "primary_key" => "id", "foreign_key" => "groupid"),
            array('users', "class_name" => "AR\Tsnwgroup", 'through' => 'groupass'
            //, "primary_key" => "accountid", "foreign_key" => "userid"
            )
        );
        static $alias_attribute = array(
            'group_name' => 'group_name',
            'parent' => 'parent',
            'tempo_creazione' => 'created_at',
            'tempo_ultima_modifica' => 'updated_at'
        );

    }

}
?>