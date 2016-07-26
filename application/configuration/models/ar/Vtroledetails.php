<?php

namespace AR {

    class Vtroledetails extends \ActiveRecord\Model {

        static $table_name = 'vtiger_role';
        static $db = "vtiger540";
        static $connection = "vtiger";
//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
        static $alias_attribute = array(
            "id_ruolo" => "roleid",
            "nome_ruolo" => "rolename",
            "ruolo_padre" => "parentrole",
            "profondita" => "depth"
        );

    }

}
?>