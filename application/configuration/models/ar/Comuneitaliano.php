<?php

namespace AR {

    class Comuneitaliano extends \ActiveRecord\Model {

        static $table_name = 'Comuni_italiani';
        static $db = "imprese";
        static $connection = "mkt";
        static $primary_key = "id";

//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
    }

}
?>