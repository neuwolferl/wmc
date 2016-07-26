<?php

namespace AR {

    class Pipelead extends \ActiveRecord\Model {

        static $table_name = 'ts_tmk_pipes';
        static $db = "imprese";
        static $connection = "mkt";
        static $primary_key = "piva";
//        static $preDefinedFilters = array(
//           "filtroPersonali" => array('conditions' => "pipe = '6'")  
//        );
        static $has_one = array(
            array("lead", "class_name" => "\AR\Lead", "primary_key" => "piva", "foreign_key" => "partitaiva")
            );
        static $alias_attribute = array(
            "piva" => "piva",
            "lotto" => "lot",
            "tubo" => "pipe",
            "venditore" => "worker",
            "blocco_tmk" => "tmklock",
            "disponibile_da" => "donotcallbefore",
            "localita" => "loc",
            "tempo_inserimento" => "insert_timestamp"
        );

    }

}
?>