<?php

namespace AR {

    class Numerotelefono extends \ActiveRecord\Model {

        static $table_name = 'Numeri_telefono';
        static $db = "imprese";
        static $connection = "mkt";
        static $primary_key = "id";
//        static $belongs_to = array(
//            array("lead", "class_name" => "AR\Lead", "primary_key" => "partitaiva", "foreign_key" => "partitaiva")
//            );
        static $alias_attribute = array(
            "telefono" => "tel"
        );
//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
    }

}
?>