<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\WS {

    class Apirequest extends \WolfMVC\Base {

        public $version = "0.1";

        /**
         * @readwrite
         */
        protected $_resource;

        /**
         * @readwrite
         */
        protected $_method;

        /**
         * @readwrite
         */
        protected $_parameters;

        /**
         * @readwrite
         */
        protected $_baseurl;

        public function __construct($options = array()) {
            parent::__construct($options);
        }

        public function adjust_parameters() {
            $parameters = array(
                "describe" => FALSE,
                "id" => FALSE,
                "filters" => array(),
                "sorting" => array(),
                "limit" => "",
                "offset" => "",
                "payload" => array()
            );
            
            if (!count($this->_parameters))
            {
                throw new \WolfMVC\WS\Exception("No parameters given for " . get_class($this->_resource), 400, NULL);
            }
            else if (isset($this->_parameters["___id"]))
            {
                if ($this->_parameters["___id"] === "__DESCRIBE__")
                    $parameters["describe"] = true;
                else
                    $parameters["id"] = $this->_parameters["___id"];
            }
            else
            {
                foreach ($this->_parameters as $k => $p) {

                    if ($k === "___limit")
                    {
                        $limit = json_decode($p);
                        $parameters["limit"] = (isset($limit->amount) ? $limit->amount : null);
                        $parameters["offset"] = (isset($limit->offset) ? $limit->offset : null);
                    }
                    else if ($k === "___sort")
                    {
                        $sort = json_decode($p, true);
                        foreach ($sort as $field => $dir) {
                            $dir = strtolower($dir);
                            if ($dir === "asc" || $dir === "desc")
                            {
                                $parameters["sorting"][$field] = $dir;
                            }
                        }
                    }
                    else if ($k === "__payload"){
                        $parameters["payload"] = $p;
                    }
                    else
                    {
                        $field = $k;
                        $filters_for_field = json_decode($p, true);

                        $fieldCheck = $this->_resource->has_attribute($field);
                        if ($fieldCheck === FALSE || !is_array($fieldCheck) || !isset($fieldCheck["content"]))
                        {
                            throw new \WolfMVC\WS\Exception("$field is not a valid field for resource " . get_class($this->_resource), 400, NULL);
                        }
                        if (is_array($fieldCheck["content"]))
                        {
                            throw new \WolfMVC\WS\Exception("$field is not a valid field for such a filter in " . get_class($this->_resource), 400, NULL);
                        }
                        foreach ($filters_for_field as $i => $ff) {
                            switch ($i) {
                                case 'eq':
                                    $expr = "£f£ = £v£";
                                    break;
                                case 'lt':
                                    $expr = "£f£ < £v£";
                                    break;
                                case 'le':
                                    $expr = "£f£ <= £v£";
                                    break;
                                case 'gt':
                                    $expr = "£f£ > £v£";
                                    break;
                                case 'ge':
                                    $expr = "£f£ >= £v£";
                                    break;
                                case 'ne':
                                    $expr = "£f£ != £v£";
                                    break;
                                case 'cc':
                                    $expr = "£f£ LIKE CONCAT('%', £v£ ,'%')";
                                    break;
                                case 'nc':
                                    $expr = "NOT(£f£ LIKE CONCAT('%', £v£ ,'%'))";
                                    break;
                                default:
                                    throw new \WolfMVC\WS\Exception("$i is not a valid op ", 400, NULL);
                                    break;
                            }
                            $value = $ff;
                            $parameters["filters"][] = array(
                                "field" => $field,
                                "value" => $value,
                                "expr" => $expr,
                                "type_of_field" => $fieldCheck["content"]
                            );
                        }
                    }
                }
            }

            $this->_parameters = $parameters;
//            print_r($this->_parameters);
        }

    }

}