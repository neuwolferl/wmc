<?php

namespace AR {

    class Tsnwpage extends \ActiveRecord\Model {

        static $table_name = 'tsnw_pages';
        static $db = "tsnw";
        static $connection = "tsnw6";
        static $has_many = array(
//            array('Pageuserauth', "class_name" => "AR\Pageuserauth", "foreign_key" => "userid")
        );
        static $alias_attribute = array(
            'controller' => 'controller',
            'azione' => 'action',
            'active' => 'active',
            'tempo_creazione' => 'created_at',
            'tempo_ultima_modifica' => 'updated_at'
        );
        

    }

}
?>