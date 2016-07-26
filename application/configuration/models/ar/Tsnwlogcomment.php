<?php

namespace AR {

    class Tsnwlogcomment extends \ActiveRecord\Model {

        static $table_name = 'tsnw_comments';
        static $db = "imprese";
        static $connection = "mkt";
        static $primary_key = "comment_id";
        static $has_one = array(
            array("log", "class_name" => "AR\Tsnwlog", "primary_key" => "log_id", "foreign_key" => "log_id")
        );
        static $alias_attribute = array(
            "id_commento" => "comment_id",
            "id_log" => "log_id",
            "commento" => "comment"
        );

    }

}
?>