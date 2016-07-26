<?php

namespace AR {

    class Fatturato extends \ActiveRecord\Model {

        static $table_name = 'FasciaFatturato';
        static $db = "imprese";
        static $connection = "mkt";
        static $primary_key = "FasciaFatturato";
        
        
        
//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
    }

}
?>