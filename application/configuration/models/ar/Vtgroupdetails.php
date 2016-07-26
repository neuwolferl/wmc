<?php

namespace AR {

    class Vtgroupdetails extends \ActiveRecord\Model {

        static $table_name = 'vtiger_groups';
        static $db = "vtiger540";
        static $connection = "vtiger";
//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
        static $alias_attribute = array(
            "id_gruppo" => "groupid",
            "nome_gruppo" => "groupname",
            "descrizione" => "description"
        );

    }

}
?>