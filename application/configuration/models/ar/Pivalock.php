<?php

namespace AR {

    class Pivalock extends \ActiveRecord\Model {

        static $table_name = 'ts_piva_tmp_lock';
        static $db = "imprese";
        static $connection = "mkt";
        static $primary_key = "piva";
        
        static $alias_attribute = array(
            "tempo_sblocco" => "unlock_timestamp",
            "blocco_manuale" => "static_lock",
            "forza" => "force",
            "tempo_blocco" => "timestamp"
        );
    }

}
?>