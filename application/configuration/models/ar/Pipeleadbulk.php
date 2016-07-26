<?php

namespace AR {

    class Pipeleadbulk extends \ActiveRecord\Model {

        static $table_name = 'ts_tmk_pipes_bulk';
        static $db = "imprese";
        static $connection = "mkt";
        static $primary_key = "id";
//        static $preDefinedFilters = array(
//           "filtroPersonali" => array('conditions' => "pipe = '6'")  
//        );
        static $alias_attribute = array(
            "lotto" => "lot",
            "localita" => "loc",
            "venditore" => "worker",
            "ammontare" => "amount",
            "tubo" => "pipe",
        );
    }

}
?>