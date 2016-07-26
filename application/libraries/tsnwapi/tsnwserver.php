<?php

require_once('../application/libraries/tsnwapi/tsnwauth.php');
require_once('../application/libraries/tsnwapi/tsnwmodel.php');
require_once('../application/libraries/tsnwapi/tsnwoperation.php');
require_once('../application/libraries/tsnwapi/tsnwanalyzer.php');
require_once('../application/libraries/tsnwapi/tsnwpublisher.php');
require_once('../application/libraries/tsnwapi/tsnwrequestcollect.php');
require_once('../application/libraries/tsnwapi/tsnwexception.php');

class Tsnwserver extends WolfMVC\Base {

    public $version = "0.1";

    /**
     * @readwrite
     * @var Tsnwrequestcollect
     */
    protected $_request;

    /**
     * @readwrite
     * @var Tsnwanalyzer
     */
    protected $_analyzedrequest;

    /**
     * @readwrite
     */
    private $_analyzedrequestdef;

    /**
     * @readwrite
     * @var Tsnwoperation
     */
    protected $_operation;

    public function __construct($options = array()) {
        parent::__construct($options);
    }

    public function authorize() {
        return true;
    }

    public function requestcollect($parameters) {
        $this->_request = new Tsnwrequestcollect($parameters);
        return $this->_request;
    }

    public function requestanalyze() {
        if (!isset($this->_request) || (!($this->_request instanceof Tsnwrequestcollect)))
        {
            throw new \Exception("Invalid tsnwrequestcollect object", 0, null);
        }
        $this->_analyzedrequest = new Tsnwanalyzer(array("request" => $this->_request));
        $this->_analyzedrequestdef = $this->_analyzedrequest->analyze()->getAnalyzedRequest();
        
        return $this->_analyzedrequest;
    }

    public function actionperform() {
        if (!isset($this->_analyzedrequest) || (!($this->_analyzedrequest instanceof Tsnwanalyzer)))
        {
            throw new \Exception("Invalid tsnwanalyzer object", 0, null);
        }
        $this->_operation = new Tsnwoperation(array("analyzedrequest" => $this->_analyzedrequestdef));
        if (in_array(strtolower($this->_analyzedrequestdef->method), array("get", "post", "put", "delete")))
        {
            $method = "execute" . ucfirst($this->_analyzedrequestdef->method);
            return array(call_user_func(array($this->_operation,$method)),  $this->_operation);
        }
    }

}
?>

