<?php

namespace AR {

    class Comunegeocap extends \ActiveRecord\Model {

        static $table_name = 'comuni_geo_cap';
        static $db = "imprese";
        static $connection = "mkt";
        static $primary_key = "id";
        static $has_many = array(
            array("lead", "class_name" => "AR\Lead", "primary_key" => "localita", "foreign_key" => "localita"),
        );
        /*
         * id
         * localita
         * provincia
         * regione
         * cap
         * lat
         * lng
         */
    }

}
?>