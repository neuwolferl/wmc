<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\WS {

    class Apiserver extends \WolfMVC\Base {

        public $version = "0.1";

        /**
         * @readwrite
         */
        protected $_rawParameters;

        /**
         * @readwrite
         */
        protected $_baseurl = "";

        /**
         * @readwrite
         */
        protected $_request;

        public function __construct($options = array()) {
            if (!isset($options["baseurl"]))
            {
                throw new Exception("Cant initialize api server: missing baseurl par.", 500, NULL);
            }
            parent::__construct($options);
            if (!count($this->_rawParameters))
            {
                throw new Exception("Cant initialize api server: missing parameters.", 500, NULL);
            }
        }

        public function catch_request() {
            $pars = $this->_rawParameters;
//            echo "rawpars";
//            print_r($pars);
            $resource = array_shift($pars);
            if (count($pars))
            {
                $id = array_shift($pars);
            }
            switch (strtolower($_SERVER["REQUEST_METHOD"])) {
                case 'get':
                    $pars = $_GET;
                    unset($pars["url"]);
                    unset($pars["extension"]);
                    break;
                case 'post':
                    $request_body = file_get_contents('php://input');
                    $pars["__payload"] = json_decode($request_body, true);
                    break;
            }
            if (isset($id))
            {
                $pars["___id"] = $id;
            }
            //controllo se la risorsa è definita << prevedere risorse annidate
            $resource = explode("_", $resource);
            foreach ($resource as $k => $v) {
                $resource[$k] = ucfirst(strtolower($v));
            }
            $resource = implode("\\", $resource);
            try {
                if (!class_exists("\WolfMVC\WS\\" . $resource))
                {
                    throw new \WolfMVC\WS\Exception("Resource not found", 404, NULL);
                }
            } catch (\Exception $e) {
                throw new \WolfMVC\WS\Exception("Not well defined request (" . $resource . ")", 400, NULL);
            }
            $resource_class = "\WolfMVC\WS\\" . $resource;
            $this->_request = new Apirequest(
                    array(
                "resource" => new $resource_class,
                "parameters" => $pars,
                "method" => strtolower(filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING)), //$_SERVER["REQUEST_METHOD"],
                "baseurl" => $this->_baseurl
                    )
            );
            $resource = $this->_request->getResource();
            $resource->_baseurl = $this->_baseurl;
        }

        public function dispatch() {
            $res = $this->_request->getResource();
            $pars = $this->_request->getParameters();
//            print_r($pars);
            $headers = array();
            switch (strtolower($this->_request->getMethod())) {
                case 'get':
                    if (isset($pars["describe"]) && $pars["describe"])
                    {
                        $result = $res->describe();
                        $headers["X-Describe"] = "true";
                        return array(
                            "result" => $result,
                            "headers" => $headers
                        );
                    }
                    else
                    {
                        $id = $pars["id"];
                        $filters = $pars["filters"];
                        $limit = $pars["limit"];
                        $offset = $pars["offset"];
//                    $res->prepareSelect();
                        $result = $res->select($id, $filters, $limit, $offset);
                        $headers["X-Requested-Resource"] = str_ireplace(array("WolfMVC\WS\\", "\\"), array("", ">"), get_class($this->_request->getResource()));
                        if (is_array($result))
                        {
                            $headers["X-Count"] = count($result);
                        }
                        $headers["X-Filter-Id"] = json_encode($id);
                        $headers["X-Limit"] = json_encode($limit);
                        foreach ($filters as $k => $f) {
                            $headers["X-Filter-" . $k] = json_encode($f);
                        }
                        return array(
                            "result" => $result,
                            "headers" => $headers
                        );
                    }

                    break;
                case 'post':
                    return array(
                        "result" => $pars,
                        "headers" => array("X-TEST"=>"X-TEST")
                    );
                default :
                    throw new \WolfMVC\WS\Exception("Method not supported", 405, NULL);
            }
        }

    }

}