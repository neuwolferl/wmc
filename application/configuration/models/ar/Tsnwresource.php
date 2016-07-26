<?php

namespace AR {

    class Tsnwresource extends \ActiveRecord\Model { 

        static $table_name = 'tsnw_resources';
        static $db = "tsnw";
        static $connection = "tsnw6";
        static $has_many = array(
//            array('Pageuserauth', "class_name" => "AR\Pageuserauth", "foreign_key" => "userid")
        );
        static $alias_attribute = array(
            'nome_risorsa' => 'resource_name',
            'tempo_creazione' => 'created_at',
            'tempo_ultima_modifica' => 'updated_at'
        );
        

    }

}
?>