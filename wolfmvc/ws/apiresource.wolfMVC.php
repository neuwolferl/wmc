<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\WS {

    class Apiresource extends \WolfMVC\Base {

        public $version = "0.1";

        /**
         * @readwrite
         */
        public $_baseurl = "";

        /**
         * @readwrite
         */
        protected static $_vm = array();
        protected static $_id = "";
        protected static $_modeltype = "";
        protected static $_modelsinvolved;
        protected static $_staticFilters = array();

        /**
         * @readwrite
         */
        protected $_modelwrapper;
        protected static $_selectable = false;
        protected static $_creatable = false;
        protected static $_updatable = false;
        protected static $_deletable = false;

        public static function setP($p, $v) {
            
        }

        public static function getP($p) {
            if (isset(static::$$p))
            {
                return static::$$p;
            }
            else
            {
                return null;
            }
        }

        public function __construct($options = array()) {
            parent::__construct($options);

            //gestione multimodello - controllo il supporto nel wrapper
            $className = "\WolfMVC\WS\Wrappers\\" . ucfirst(strtolower(static::$_modeltype));
            $propertyName = "supports_multimodel";
            $support = $className::$$propertyName;


            $wrapperclass = "\WolfMVC\WS\Wrappers\\" . ucfirst(static::$_modeltype);
            try {
                if (!class_exists($wrapperclass))
                {
                    throw new \WolfMVC\WS\Exception("Can't find model wrapper for " . __CLASS__, 500, NULL);
                }
            } catch (\Exception $e) {
                throw new \WolfMVC\WS\Exception("Can't find model wrapper for " . __CLASS__, 500, NULL);
            }
            $this->_modelwrapper = array();
            foreach (static::$_modelsinvolved as $k => $m) {
                $wrap = new $wrapperclass(array("submodel" => $m));
//                echo "<pre>";
//                print_r($wrap);
//                echo "</pre>";
                $this->_modelwrapper[$m] = $wrap;
            }
        }

//        protected function getModelwrapper(){
//            return $this->_modelwrapper;
//        }

        public function has_attribute($attribute, $root = NULL) {
            $attr = explode(">", $attribute);
            if ($root === NULL)
            {
                $root = static::$_vm;
            }
            if (!count($attr))
            {
                return FALSE;
            }
            if (count($attr) === 1)
            {
                return (isset($root[$attr[0]]) ? $root[$attr[0]] : FALSE);
            }
            if (isset($root[$attr[0]]) && isset($root[$attr[0]]["content"]))
            {
                $prefix = array_shift($attr);
                return $this->has_attribute(join(">", $attr), $root[$prefix]["content"]);
            }
            else
                return FALSE;
        }

        public static function create($id, $payload) {
            if (!static::$_creatable)
            {
                throw new \WolfMVC\WS\Exception("This resource is not creatable for anyone", 403, NULL);
            }
            $wrap_main = $this->_modelwrapper[static::$_mainmodel];
        }

        public function prepareSelect($id, $filters) {
            $batch = array(
                "from_main_model" => array(),
                "from_derived_models" => array(),
                "main_model_filters" => array(),
                "derived_models_filters" => array()
            );

            //caso id
            if ($id)
            {
                if (!isset(static::$_id) || static::$_id === "")
                    throw new \WolfMVC\WS\Exception("Id for " . __CLASS__ . " is not well defined", 500, NULL);
                $fieldCheck = $this->has_attribute(static::$_id);
                if (!$fieldCheck)
                    throw new \WolfMVC\WS\Exception("Id for " . __CLASS__ . " is not well defined", 500, NULL);
                $filters = array(array(
                        "field" => static::$_id,
                        "value" => $id,
                        "expr" => "£f£ = £v£",
                        "type_of_field" => $fieldCheck["content"]
                    )
                );
            }
            //filtri statici
            $static_filters = static::$_staticFilters; // i filtri statici possono riferirsi a campi non presenti in vm ma solo
            // nel modello sottostante, quindi non controllo l'esistenza del campo



            $aux = array(
                "filters" => $filters
            );
            //prendo i campi main
            $vm = static::$_vm;
            $result = array();
            $fcn = function($k, $v, &$result, $aux) {
                if (is_array($v) && isset($v["content"]))
                { // è un campo o un gruppo
                    if (is_array($v["content"]))
                    { // è un gruppo
                        //do nothing
                    }
                    else
                    { // è un campo
                        if (!isset($v["from"]) || $v["from"] === "main")
                        {
                            $from_model = "main";
                            $result["from_main_model"][] = $k;
                            //cerco eventuali filtri
                            foreach ($aux["filters"] as $i => $f) {
                                if ($f["field"] === $k)
                                {
                                    $filter = $f;
                                    if (isset($v["inmodel"]))
                                    {
                                        $filter["field"] = $v["inmodel"];
                                    }
                                    $result["main_model_filters"][] = $filter;
                                }
                            }
                        }
                        else
                        {
                            $from_model = $v["from"];
                            if (!isset($result["from_derived_models"][$from_model]))
                            {
                                $result["from_derived_models"][$from_model] = array();
                            }
                            $result["from_derived_models"][$from_model][] = $k;
                            foreach ($aux["filters"] as $i => $f) {
                                if ($f["field"] === $k)
                                {
                                    if (!isset($result["derived_models_filters"][$from_model]))
                                        $result["derived_models_filters"][$from_model] = array();
                                    $filter = $f;
                                    if (isset($v["inmodel"]))
                                    {
                                        $filter["field"] = $v["inmodel"];
                                    }
                                    $result["derived_models_filters"][$from_model][] = $filter;
                                }
                            }
                        }
                    }
                }
            };
            $batch = \WolfMVC\ArrayMethods::array_explore($vm, $fcn, $batch, $aux);

            foreach ($static_filters as $k => $model) {
                foreach ($model as $kk => $field) {
                    foreach ($field as $kkk => $condition) {
                        if (!isset($batch["derived_models_filters"][$k]))
                            $batch["derived_models_filters"][$k] = array();
                        $filter = array(
                            "field" => $kk,
                            "value" => $condition[1],
                            "expr" => $condition[0],
                            "type_of_field" => "static"
                        );
                        $batch["derived_models_filters"][$k][] = $filter;
                    }
                }
            }
            return $batch;
        }

        public function select_by_id($id) {
            $id = static::$_id;
            if ($id === "")
            {
                throw new \WolfMVC\WS\Exception("Id undefined for resource " . __CLASS__, 400, NULL);
            }
        }

        public function select($id, $filters, $limit, $offset) {
            if (!static::$_selectable)
            {
                throw new \WolfMVC\WS\Exception("This resource is not selectable for anyone", 403, NULL);
            }
            $pre = $this->prepareSelect($id, $filters);
            $wrap_main = $this->_modelwrapper[static::$_mainmodel];
            $condition = array();
            $condition_pars = array();
            $joins = array();
            $include = \WolfMVC\ArrayMethods::contained_keys($pre["from_derived_models"]);
            foreach ($pre["main_model_filters"] as $k => $f) {
                $translated_field = $wrap_main->translate_field($f["field"]);
                if ($translated_field === FALSE)
                {
                    throw new \WolfMVC\WS\Exception($f["field"] . " is not a valid field for filters in " . __CLASS__, 400, NULL);
                }
                $condition[] = str_ireplace(array("£f£", "£v£"), array($translated_field, '?'), $f["expr"]);
                $condition_pars[] = $f["value"];
            }
            foreach ($pre["derived_models_filters"] as $km => $m) {
                foreach ($m as $k => $f) {
                    $wrap = null;
                    $ass = $wrap_main->get_associated_model_by_desc($km);
                    if ($ass === FALSE)
                    {
                        throw new \WolfMVC\WS\Exception($km . " is not a valid model in " . __CLASS__, 500, NULL);
                    }
                    else
                    {
                        $wrap = $this->_modelwrapper[$ass["modelname"]];
                    }
                    $translated_field = $wrap->translate_field($f["field"]);
                    if ($translated_field === FALSE)
                    {
                        throw new \WolfMVC\WS\Exception($f["field"] . " is not a valid field for filters in " . __CLASS__, 400, NULL);
                    }
                    $condition[] = str_ireplace(array("£f£", "£v£"), array($ass["tablename"] . "." . $translated_field, '?'), $f["expr"]);
                    $condition_pars[] = $f["value"];
                    if (!in_array($km, $joins))
                    {
                        $joins[] = $km;
                    }
                }
            }
            $condition = array(implode(" AND ", $condition));
            $conditions = array_merge($condition, $condition_pars);
            $raw = $wrap_main->all(array(
                "include" => $include,
                "joins" => $joins,
                "conditions" => $conditions,
                "limit" => $limit,
                "offset" => $offset
            ));
            $out = array();
            //ricompilo vm
            foreach ($raw as $ri => $r) {
                $vm = static::$_vm;
                $id = static::$_id;
                $vm = $this->fill_from_raw_object($vm, $r);
                if (isset($id) && $id !== "")
                {
                    $vm["__id"] = $r->get_values_for(array($id));
                    $vm["__id"] = $vm["__id"][$id];
                }
                $sugg = $this->make_suggestions("local", $vm);
                if (!empty($sugg))
                    $vm["_see_also"] = $sugg;
                $out[] = $vm;
            }
            return $out;
        }

        public function count($id, $filters, $limit) {
            $pre = $this->prepareSelect($id, $filters);
            $wrap_main = $this->_modelwrapper[static::$_mainmodel];
            $condition = array();
            $condition_pars = array();
            $joins = array();
            $include = \WolfMVC\ArrayMethods::contained_keys($pre["from_derived_models"]);
            foreach ($pre["main_model_filters"] as $k => $f) {
                $translated_field = $wrap_main->translate_field($f["field"]);
                if ($translated_field === FALSE)
                {
                    throw new \WolfMVC\WS\Exception($f["field"] . " is not a valid field for filters in " . __CLASS__, 400, NULL);
                }
                $condition[] = str_ireplace(array("£f£", "£v£"), array($translated_field, '?'), $f["expr"]);
                $condition_pars[] = $f["value"];
            }
            foreach ($pre["derived_models_filters"] as $km => $m) {
                foreach ($m as $k => $f) {
                    $wrap = null;
                    $ass = $wrap_main->get_associated_model_by_desc($km);
                    if ($ass === FALSE)
                    {
                        throw new \WolfMVC\WS\Exception($km . " is not a valid model in " . __CLASS__, 500, NULL);
                    }
                    else
                    {
                        $wrap = $this->_modelwrapper[$ass["modelname"]];
                    }
                    $translated_field = $wrap->translate_field($f["field"]);
                    if ($translated_field === FALSE)
                    {
                        throw new \WolfMVC\WS\Exception($f["field"] . " is not a valid field for filters in " . __CLASS__, 400, NULL);
                    }
                    $condition[] = str_ireplace(array("£f£", "£v£"), array($ass["tablename"] . "." . $translated_field, '?'), $f["expr"]);
                    $condition_pars[] = $f["value"];
                    if (!in_array($km, $joins))
                    {
                        $joins[] = $km;
                    }
                }
            }
            $condition = array(implode(" AND ", $condition));
            $conditions = array_merge($condition, $condition_pars);
            $raw = $wrap_main->count(array(
                "include" => $include,
                "joins" => $joins,
                "conditions" => $conditions
            ));
            print_r($raw);
            $out = array();
            //ricompilo vm
            foreach ($raw as $ri => $r) {
                $vm = static::$_vm;
                $vm = $this->fill_from_raw_object($vm, $r);
                $sugg = $this->make_suggestions("local", $vm);
                if (!empty($sugg))
                    $vm["_see_also"] = $sugg;
                $out[] = $vm;
            }
            return $out;
        }

        public static function update() {
            
        }

        public static function delete() {
            
        }

        public function fill_from_raw_object(array $vm, $raw) {
            if (empty($vm))
                return array();
            foreach ($vm as $k => $f) {
                if (!isset($f["content"]))
                {
                    throw new \WolfMVC\WS\Exception("Bad vm in " . __CLASS__, 500, NULL);
                }
                if (is_string($f["content"])) // è un campo
                {

                    if (!isset($f["from"]) || $f["from"] === "main")
                    {
                        if (isset($f["inmodel"]))
                            $search = $f["inmodel"];
                        else
                            $search = $k;
                        $val = $raw->get_values_for(array($search));
                    }
                    else
                    {
                        if (isset($f["inmodel"]))
                            $search = $f["inmodel"];
                        else
                            $search = $k;
                        $sub = $f["from"];
                        $val = $raw->$sub->get_values_for(array($search));
                    }
                    //riferimento
                    $content = $f["content"];
                    $content = explode("::", $content);
                    if (count($content) > 1)
                    {
                        if ($content[0] === "refto")
                        {
//                            $str = $this->_baseurl;
//                            $fill = str_ireplace("{{v}}", $val[$search], $content[1]);
//                            $str = str_ireplace("{{pars}}", $fill, $str);
                            $vm[$k] = $this->makeref($val[$search], $content[1]);
                        }
                    }
                    else
                    {
                        $vm[$k] = $val[$search];
                    }
                }
                else if (is_array($f["content"]))
                {
                    $vm[$k] = $this->fill_from_raw_object($f["content"], $raw);
                }
            }
            return $vm;
        }

        public function makeref($val, $reftemplate) {
            $str = $this->_baseurl;
            $fill = str_ireplace("{{v}}", $val, $reftemplate);
//            $str = str_ireplace("{{pars}}", $fill, $str);
            return $fill;
        }

        public function make_suggestions($scope, $data) {
            if ($scope === "local")
            {
                return array();
            }
            else if ($scope === "global")
            {
                return array();
            }
        }

        public function describe(array $vm = array()) {
            $result = array();
            if (empty($vm))
            {
                $vm = static::$_vm;
                if (isset(static::$_id) && static::$_id !== "")
                {
                    $result["__id"] = static::$_id;
                }
            }


            foreach ($vm as $k => $v) {
                if (!isset($v["content"]))
                {
                    return $result;
                }
                else
                {
                    if (is_string($v["content"]))
                    {
                        $result[$k] = $v["content"];
                    }
                    else if (is_array($v["content"]))
                    {
                        $result[] = $this->describe($v["content"]);
                    }
                }
            }
            return array($result);
        }

    }

}