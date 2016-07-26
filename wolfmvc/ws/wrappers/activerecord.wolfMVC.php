<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\WS\Wrappers {

    use WolfMVC\WS\Wrapper as Wrapper;

    class Activerecord extends Wrapper {

        public $version = "0.1";

        /**
         * @readwrite
         */
        protected $_submodel = "";
        static $supports_multimodel = true;

        public function __construct($options = array()) {
            parent::__construct($options);
        }

        public function all($pars) {
            $submodel = $this->getSubmodel();
            $ar_model_class = "\AR\\" . $submodel;
            $raw = call_user_func(array($ar_model_class, 'all'), $pars);
            return $raw;
//            $model = new $ar_model_class;
//            $table = \AR\Vtaccount::table();
//
////            $columns = $table
//            print_r($model);
//            print_r($ar_model_class::$alias_attribute);
        }
        
        
        public function create($pars){
            $submodel = $this->getSubmodel();
            $ar_model_class = "\AR\\" . $submodel;
            
        }

        public function translate_field($fieldName) {
            $submodel = $this->getSubmodel();
            if (!isset($submodel) || !$submodel || empty($submodel))
            {
                throw new \Exception("Undefined submodel in wrapper", 400, NULL);
            }
            $ar_model_class = "\AR\\" . $submodel;
            if (!class_exists($ar_model_class))
            {
                throw new \Exception("Invalid submodel in wrapper", 400, NULL);
            }
            $model = new $ar_model_class;
            //cerco il campo tra gli alias
            $attrs = $model->attributes();
            $alias_attrs = $model::$alias_attribute;
            foreach ($alias_attrs as $al => $fn) {
                if ($al === $fieldName)
                {
                    return $fn;
                }
            }
            if (array_key_exists($fieldName, $attrs))
            {
                return $fieldName;
            }
            return FALSE;
        }

        public function get_associated_model_by_desc($desc) {
            $submodel = $this->getSubmodel();
            if (!isset($submodel) || !$submodel || empty($submodel))
            {
                throw new \Exception("Undefined submodel in wrapper", 400, NULL);
            }
            $ar_model_class = "\AR\\" . $submodel;
            if (!class_exists($ar_model_class))
            {
                throw new \Exception("Invalid submodel in wrapper", 400, NULL);
            }
            $model = new $ar_model_class;
            $hasmany = "has_many";
            $hasone = "has_one";
            if (isset($model::$$hasone))
            {
                $hasone = $model::$$hasone;
                foreach ($hasone as $k => $ass) {
                    if ($ass[0] === $desc)
                    {
                        $der_model_class = $ass["class_name"];
                        if (!class_exists($der_model_class))
                        {
                            throw new \Exception("Derived submodel {$der_model_class} in wrapper is not defined", 500, NULL);
                        }
                        $der_model = new $der_model_class;

                        return array(
                            "modelname" => str_ireplace("AR\\", "", $ass["class_name"]),
                            "tablename" => $der_model::table_name(),
                            "mult" => "!"
                        );
                    }
                }
            }

            if (isset($model::$$hasmany))
            {
                $hasmany = $model::$$hasmany;
                foreach ($hasmany as $k => $ass) {
                    if ($ass[0] === $desc)
                    {
                        $der_model_class = $ass["class_name"];
                        if (!class_exists($der_model_class))
                        {
                            throw new \Exception("Derived submodel {$der_model_class} in wrapper is not defined", 500, NULL);
                        }
                        $der_model = new $der_model_class;

                        return array(
                            "modelname" => str_ireplace("AR\\", "", $ass["class_name"]),
                            "tablename" => $der_model::table_name(),
                            "mult" => "!"
                        );
                    }
                }
            }
            return FALSE;
        }

    }

}