<?php

namespace AR {

    class Lead extends \ActiveRecord\Model {

        static $table_name = 'aziende_all';
        static $db = "imprese";
        static $connection = "mkt";
        static $primary_key = "internal_id";
        static $has_many = array(
            array("pipe", "class_name" => "AR\Pipelead", "primary_key" => "partitaiva", "foreign_key" => "piva"),
            array("lock", "class_name" => "AR\Pivalock", "primary_key" => "partitaiva", "foreign_key" => "piva"),
            array("log", "class_name" => "AR\Tsnwlog", "primary_key" => "partitaiva", "foreign_key" => "piva"),
            array("numeritelefono", "class_name" => "AR\Numerotelefono", "primary_key" => "partitaiva", "foreign_key" => "partitaiva"),
            array("leadamend", "class_name" => "AR\Leadamend", "primary_key" => "partitaiva", "foreign_key" => "partitaiva")
        );

//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
    }

}
?>