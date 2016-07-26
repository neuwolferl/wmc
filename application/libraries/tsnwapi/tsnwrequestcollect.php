<?php

class Tsnwrequestcollect extends WolfMVC\Base {

    public $version = "0.1";

    /**
     * @var string
     */
    protected $_method = "";

    /**
     * @var string
     */
    protected $_payload;

    /**
     * @readwrite
     * @var string
     */
//    protected $method = "";


    public function __construct($parameters, $controller = "", $action = "") {
        $this->_method = $_SERVER["REQUEST_METHOD"];
        $this->_payload = new stdClass();
        $payload = array();

        switch ($this->_method) {
            case 'GET':
            case 'get':
                $payload["qs"] = array();
                parse_str($_SERVER['QUERY_STRING'], $payload["qs"]);
                $payload["res"] = array();
                $index = 0;

                foreach ($parameters as $k => $par) {

                    $payload["res"][$k] = $par;
                }

                if (isset($payload["qs"]["url"]))
                {
                    unset($payload["qs"]["url"]);
                }
                if (isset($payload["qs"]["extension"]))
                {
                    unset($payload["qs"]["extension"]);
                }
                $this->_payload = $payload;
//                $this->_payload = $payload;
//                print_r($this->_payload);
                break;
            case 'POST':
            case 'post':
                $payload["vm"] = array();
//                parse_str($_SERVER['QUERY_STRING'], $payload["qs"]);
                $payload["res"] = array();
                $index = 0;
                foreach ($parameters as $k => $par) {
                    while (isset($payload["res"][$index])) {
                        $index++;
                    }
                    $payload["res"][$index] = $par;
                }
                foreach ($_REQUEST as $k => $post) {
                    $payload["vm"][$k] = $post;
                }
                if (isset($payload["vm"]["url"]))
                {
                    unset($payload["vm"]["url"]);
                }
                if (isset($payload["vm"]["extension"]))
                {
                    unset($payload["vm"]["extension"]);
                }
                $this->_payload = $payload;

                break;
            case 'PUT':
            case 'put':
//                parse_str($_SERVER['QUERY_STRING'], $payload);
                $payload["res"] = array();
                $payload["vm"] = array();
                $index = 0;
                foreach ($parameters as $k => $par) {
                    while (isset($payload["res"][$index])) {
                        $index++;
                    }
                    $payload["res"][$index] = $par;
                }
                foreach ($_REQUEST as $k => $post) {
                    $payload["vm"][$k] = $post;
                }
                if (isset($payload["vm"]["url"]))
                {
                    unset($payload["vm"]["url"]);
                }
                if (isset($payload["vm"]["extension"]))
                {
                    unset($payload["vm"]["extension"]);
                }
                $this->_payload = $payload;
                
                break;
            case 'DELETE':
            case 'delete':
                $payload["res"] = array();
                $index = 0;
                foreach ($parameters as $k => $par) {
                    while (isset($payload["res"][$index])) {
                        $index++;
                    }
                    $payload["res"][$index] = $par;
                }
                $this->_payload = $payload;
                break;
            default :
                parse_str($_SERVER['QUERY_STRING'], $payload);
                $payload["res"] = array();
                $index = 0;
                foreach ($parameters as $k => $par) {
                    while (isset($payload["res"][$index])) {
                        $index++;
                    }
                    $payload["res"][$index] = $par;
                }
                foreach ($_REQUEST as $k => $post) {
                    $payload[$k] = $post;
                }
                if (isset($payload["url"]))
                {
                    unset($payload["url"]);
                }
                if (isset($payload["extension"]))
                {
                    unset($payload["extension"]);
                }
//                $this->_payload = $payload;
                $this->_payload = $payload;
                break;
        }
//        print_r($this->_payload);
        parent::__construct(array());
    }

    public function getRequest() {
        $ret = new stdClass();
        $ret->method = $this->_method;
        $ret->payload = $this->_payload;

        return $ret;
    }

    private function _arrayToObject($array) {
        if (!is_array($array))
        {
            return $array;
        }

        $object = new stdClass();
        if (is_array($array) && count($array) > 0)
        {
            foreach ($array as $name => $value) {
                $name = strtolower(trim($name));
                if (is_numeric($name))
                    $name = "" . $name;
                if (!empty($name))
                {
                    $object->$name = $this->_arrayToObject($value);
                }
            }
            return $object;
        }
        else
        {
            return FALSE;
        }
    }

}
?>

