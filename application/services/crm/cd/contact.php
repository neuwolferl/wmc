<?php

namespace WolfMVC\WS\Crm\Cd {

//    use \WolfMVC\WS\Apiresource as Apiresource;

    class Contact extends \WolfMVC\WS\Apiresource {

        protected static $_selectable = true;
        protected static $_creatable = false;
        protected static $_updatable = false;
        protected static $_deletable = false;
        protected static $_id = "id_account";
        protected static $_modeltype = "activerecord";
        protected static $_modelsinvolved = array("Vtaccount", "Vtaccountscf", "Vtcrmentity");
        protected static $_mainmodel = "Vtaccount";
//        protected $_modelwrapper = array();

        /**
         * @readwrite
         */
        protected static $_vm = array(
            "id_contatto" => array(
                "content" => "integer"
            ),
            "nome" => array(
                "content" => "string"
            ),
            "cognome" => array(
                "content" => "string"
            )
        );

        public function __construct($options = array()) {
            parent::__construct($options);
        }

        public function select($id,$filters, $limit,$offset) {
            return parent::select($id,$filters, $limit,$offset);
        }

    }

}
?>