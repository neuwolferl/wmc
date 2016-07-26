<?php

class Tsnwanalyzer extends WolfMVC\Base {

    public $version = "0.1";

    /**
     * @readwrite
     * @var Tsnwrequestcollect
     */
    protected $_request;

    /**
     * @readwrite
     * @var string
     */
    protected $_returnType;
    private $_model;

    /**
     * @readwrite
     * @var string
     */
    protected $_mainres = "";

    /**
     * @readwrite
     * @var string
     */
    protected $_mainresid = "";

    /**
     * @readwrite
     * @var array
     */
    protected $_mainresquerypars = array();

    /**
     * @readwrite
     * @var array
     */
    protected $_payload = array();

    /**
     * @readwrite
     * @var array
     */
    protected $_path = array();

    public function __construct($options = array()) {
        parent::__construct($options);
    }

    public function getAnalyzedRequest() {
        $ret = new stdClass();
        $request = $this->_request->getRequest();
        $ret->method = $request->method;
        $ret->mainres = $this->_mainres;
        $ret->mainresid = $this->_mainresid;
        $ret->mainresquerypars = $this->_mainresquerypars;
        $ret->payload = $this->_payload;
        $ret->path = $this->_path;
        return $ret;
    }

    public function analyze() {
        if (!(isset($this->_request)) || !($this->_request instanceof Tsnwrequestcollect))
        {
            throw new Exception("Invalid Tsnwrequestcollect object", 0, null);
        }
        $this->_model = new Tsnwmodel();
        $request = $this->_request->getRequest();
        switch (strtolower($request->method)) {
            case 'get':
                $this->analyzeGET();
                break;
            case 'post':
                $this->analyzePOST();
                break;
            case 'put':
                $this->analyzePUT();
                break;
            case 'delete':
                $this->analyzeDELETE();
                break;
            default:
                break;
        }
        return $this;
        //analizzo risorse
//        return array_merge($this->_request,$model->getResources());
//        return $model->getResources();
//        print_r($this->_request->getRequest());
        //identify target resource
        //verb
//        $verb = $th
    }

    private function analyzeDELETE() {
        //cerco la risorsa
        $request = $this->_request->getRequest();
        $res = $request->payload;
        $res = $res["res"];
        $mainres = "";
        $mainresid = "";
        if (count($res) === 0)
        {
            throw new Tsnwexception("Resource missing", 400, null);
        }
        elseif (count($res) === 1)
        {
            throw new Tsnwexception("Bad request: id missing for the resource to be updated", 400, null);
        }
        else
        {
            $mainres = $res[count($res) - 2];
            $mainresid = $res[count($res) - 1];
            array_pop($res);
            array_pop($res);
        }
        $names = $this->_model->getNames();
        if (!isset($names[$mainres]))
        {
            throw new Tsnwexception($mainres . " is not a valid resource", 404, null);
        }
        else
        {
            $this->_mainres = $names[$mainres];
            $this->_mainresid = $mainresid;
        }
    }

    private function analyzePUT() {
        //cerco la risorsa
        $request = $this->_request->getRequest();
        $request_body = file_get_contents('php://input');
        $data = json_decode(str_replace("vm=", "", $request_body),true);
        $res = $request->payload;
        $res = $res["res"];
        $mainres = "";
        $mainresid = "";
        $mainrespayload = "";
        if (count($res) === 0)
        {
            throw new Tsnwexception("Resource missing", 400, null);
        }
        elseif (count($res) === 1)
        {
            throw new Tsnwexception("Bad request: id missing for the resource to be updated", 400, null);
        }
        else
        {
            $mainres = $res[count($res) - 2];
            $mainresid = $res[count($res) - 1];
            $mainrespayload = $data;
            array_pop($res);
            array_pop($res);
        }
        $names = $this->_model->getNames();
        if (!isset($names[$mainres]))
        {
            throw new Tsnwexception($mainres . " is not a valid resource", 404, null);
        }
        else
        {
            $this->_mainres = $names[$mainres];
            $this->_mainresid = $mainresid;
            $this->_payload = $mainrespayload;
        }
    }

    private function analyzePOST() {
        //cerco la risorsa
        $request = $this->_request->getRequest();
        $res = $request->payload;
        $res = $res["res"];
        $mainres = "";
        $mainrespayload = "";
        if (count($res) === 0)
        {
            throw new Tsnwexception("Resource missing", 400, null);
        }
        else
        {
            $mainres = $res[count($res) - 1];
            $mainrespayload = $request->payload["vm"];
            array_pop($res);
        }

        $names = $this->_model->getNames();
        if (!isset($names[$mainres]))
        {
            throw new Tsnwexception($mainres . " is not a valid resource", 404, null);
        }
        else
        {
            $this->_mainres = $names[$mainres];
            $this->_payload = $mainrespayload;
        }
    }

    private function analyzeGET() {
        //cerco la risorsa
        $request = $this->_request->getRequest();
        $res = $request->payload;
        $res = $res["res"];
        $mainres = "";
        $mainresid = "";
        $mainresquerypars = array();
        //cerco l'ultima risorsa
        if (count($res) === 0)
        {
            throw new Tsnwexception("Resource missing", 400, null);
        }
        elseif (count($res) % 2 === 0)
        {
            $mainres = $res[count($res) - 2];
            $mainresid = $res[count($res) - 1];
            array_pop($res);
            array_pop($res);
        }
        else
        {
            $mainres = $res[count($res) - 1];
            $mainresquerypars = $request->payload["qs"];
            array_pop($res);
        }

        $names = $this->_model->getNames();
        if (!isset($names[$mainres]))
        {
            throw new Tsnwexception($mainres . " is not a valid resource", 404, null);
        }
        else
        {
            $this->_mainres = $names[$mainres];
            $this->_mainresid = $mainresid;
            $this->_mainresquerypars = $mainresquerypars;
        }
        $rels = $this->_model->getRels();
//        print_r($rels);
        $originalmainres = $mainres;
        $mainres = $names[$mainres];
        //cerco le altre risorse
        $path = array();
        while (count($res) > 0) {
            $otherResId = array_pop($res);
            $otherRes = array_pop($res);
            //esiste la risorsa?
            if (!isset($names[$otherRes]))
            {
                throw new Tsnwexception($otherRes . " is not a valid resource", 404, null);
            }
            else
            {
                $originalotherRes = $otherRes;
                $otherRes = $names[$otherRes];
            }
            //è collegata alla precedente?
            if (!isset($rels[$mainres]) || !isset($rels[$mainres]["direct"]))
            {
                throw new Tsnwexception($originalmainRes . " has no filtering options", 204, null);
            }
            else
            {
                $r = $rels[$mainres]["direct"];
                $flag = FALSE;

                foreach ($r as $k => $arr) {
                    if (isset($arr["refs"]) && $arr["via"] && $arr["refs"] === $otherRes)
                    {
                        $flag = TRUE;
                        $path[] = array($arr["via"] => $otherResId, "ref" => $arr["refs"]);
                    }
                }
                if (!$flag)
                {
                    throw new Tsnwexception("Can't filter " . $originalmainres . " by " . $originalotherRes, 400, null);
                }
                else
                {
                    $this->_path = $path;
                }
            }
        }
    }

}
?>

