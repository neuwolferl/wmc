<?php

namespace WolfMVC\WS\Tools\Users {

//    use \WolfMVC\WS\Apiresource as Apiresource;

    class Gusers extends \WolfMVC\WS\Apiresource {

        protected static $_selectable = true;
        protected static $_creatable = false;
        protected static $_updatable = false;
        protected static $_deletable = false;
        protected static $_id = "id";
        protected static $_modeltype = "activerecord";
        protected static $_modelsinvolved = array("User");
        protected static $_mainmodel = "User";
//        protected $_modelwrapper = array();

        /**
         * @readwrite
         */
        protected static $_vm = array(
            "id" => array(
                "content" => "integer"
            ),
            "googleid" => array(
                "content" => "string"
            ),
            "nome_visualizzato" => array(
                "content" => "string",
                "inmodel" => "display_name"
            ),
            "nome" => array(
                "content" => "string",
                "inmodel" => "first_name"
            ),
            "cognome" => array(
                "content" => "string",
                "inmodel" => "last_name"
            ),
            "email" => array(
                "content" => "string"
            ),
            "dominio" => array(
                "content" => "string",
                "inmodel" => "hd"
            ),
            "verificato" => array(
                "content" => "string",
                "inmodel" => "verified"
            ),
            "attivo" => array(
                "content" => "integer",
                "inmodel" => "active"
            )
        );
//        protected static $_staticFilters = array(
//            "ent" => array(
//                "deleted" => array(
//                    array("£f£ = ?", 0)
//                )
//            )
//        );

        public function __construct($options = array()) {
            parent::__construct($options);
        }

        public function select($id, $filters, $limit, $offset) {
            return parent::select($id, $filters, $limit, $offset);
        }

    }

}
?>