<?php

namespace AR {

    class Leadamend extends \ActiveRecord\Model {

        static $table_name = 'aziende_all_amend';
        static $db = "imprese";
        static $connection = "mkt";
        static $primary_key = "id";
        static $has_one = array(
            array("lead", "class_name" => "AR\Lead", "primary_key" => "partitaiva", "foreign_key" => "partitaiva"),
        );

//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
    }

}
?>