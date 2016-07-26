<?php

namespace AR {

    class Collectionso extends \ActiveRecord\Model {

        static $table_name = 'external_collections_so';
        static $db = "vtiger540";
        static $primary_key = "id";
        static $connection = "vtiger";
        static $has_one = array(
            array("collection", "class_name" => "AR\Collection", "primary_key" => "idcollection", "foreign_key" => "id"),
            array("so", "class_name" => "AR\Vtso", "primary_key" => "idso", "foreign_key" => "salesorderid")
        );
        static $alias_attribute = array(
            'id_associazione' => 'id',
            'id_incasso' => 'idcollection',
            'id_so' => 'idso',
            'ammontare' => 'amount'
        );

//        static $views = array(
//           "vista1" => array('limit' => 20,'conditions' => "donotcallbefore <> '0'")  
//        );
    }

}
?>