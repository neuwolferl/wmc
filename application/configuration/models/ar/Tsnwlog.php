<?php

namespace AR {

    class Tsnwlog extends \ActiveRecord\Model {

        static $table_name = 'tsnw_log';
        static $db = "imprese";
        static $connection = "mkt";
        static $primary_key = "log_id";
        static $has_one = array(
            array("lead", "class_name" => "AR\Lead", "primary_key" => "piva", "foreign_key" => "partitaiva")
        );
        static $has_many = array(
            array("comment", "class_name" => "AR\Tsnwlogcomment", "primary_key" => "log_id", "foreign_key" => "log_id")
        );
        static $alias_attribute = array(
            "id_log" => "log_id",
            "partita_iva" => "piva",
            "timestamp" => "timestamp",
            "codice_tipo" => "what_type",
            "codice_registro_principale" => "what_main",
            "codice_registro_secondario" => "what_detail",
            "elemento" => "what_element",
            "chi" => "who",
            "chi_vtiger" => "who_vtiger",
            "parametri" => "parameters",
            "esito" => "esito",
            "appartiene_a" => "parent"
        );

    }

}
?>