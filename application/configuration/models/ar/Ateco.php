<?php

namespace AR {

    class Ateco extends \ActiveRecord\Model {

        static $table_name = 'ateco_semplice';
        static $db = "imprese";
        static $connection = "mkt";
        static $primary_key = "id";
        
        
//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
    }

}
?>