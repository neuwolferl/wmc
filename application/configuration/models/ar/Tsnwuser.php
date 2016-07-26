<?php

namespace AR {

    class Tsnwuser extends \ActiveRecord\Model {

        static $table_name = 'tsnw_users';
        static $db = "tsnw";
        static $primary_key = "id";
        static $connection = "tsnw6";
        static $has_many = array(
            array('tsnwusergroupass', "class_name" => "AR\Tsnwusergroupass", "primary_key" => "id", "foreign_key" => "userid"),
            array('groups', "class_name" => "AR\Tsnwgroup", 'through' => 'AR\Tsnwusergroupass',
                 "primary_key" => "id", "foreign_key" => "id"
                )
        );
        static $alias_attribute = array(
            'nome_utente' => 'user_name',
            'password' => 'user_password',
            'nome' => 'first_name',
            'cognome' => 'last_name',
            'nome_visualizzato' => 'display_name',
            'attivo' => 'active',
            'tempo_creazione' => 'created_at',
            'tempo_ultima_modifica' => 'updated_at'
        );

    }

}
?>