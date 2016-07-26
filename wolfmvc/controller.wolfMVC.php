<?php

namespace WolfMVC {

    use WolfMVC\Base as Base;
    use WolfMVC\View as View;
    use WolfMVC\Registry as Registry;
    use WolfMVC\Template as Template;
    use WolfMVC\Controller\Exception as Exception;

    /**
     * Classe base di tutti i controller
     */
    class Controller extends Base {

        /**
         * @var array I parametri passati da url
         * @readwrite
         */
        protected $_parameters;

        /**
         * @readwrite
         */
        protected $_layoutView; //flag per render automatico

        /**
         * @readwrite
         */
        protected $_actionView; //flag per render automatico

        /**
         * @readwrite
         */
        protected $_willRenderLayoutView = true; //flag per render automatico

        /**
         * @readwrite
         */
        protected $_willRenderActionView = true; //flag per render automatico

        /**
         * @var string Il path relativo standard in cui cercare la view
         * @readwrite
         */
        protected $_defaultPath = "application/views";

        /**
         * @var string Il path relativo standard in cui cercare il layout
         * @readwrite
         */
        protected $_defaultLayout = "layouts/standard";

        /**
         * @readwrite
         */
        protected $_defaultExtension = "html";

        /**
         * @readwrite
         */
        protected $_defaultContentType = "text/html";

        /**
         * @readwrite
         */
        protected $_system_js_including = "";

        /**
         * @readwrite
         */
        protected $script_inc = array();

        /**
         *
         * @readwrite
         */
        protected $_useBackTracking = false;

        /**
         *
         * @readwrite
         */
        protected $_usePageComponent = false;

        /**
         * @read
         * @var array
         */
        protected $_regOperations;

        protected function anti_injection($input) {
            if (is_array($input))
            {
                foreach ($input as $k => $v) {
                    $input[$k] = $this->anti_injection($input[$k]);
                }
                return $input;
            }
            else
            {
                $pulito = strip_tags(addslashes(trim($input)));
//                $pulito = str_replace("'", "\'", $pulito);
//                $pulito = str_replace('"', '\"', $pulito);
//                $pulito = str_replace(';', '\;', $pulito);
//                $pulito = str_replace('--', '\--', $pulito);
//                $pulito = str_replace('+', '\+', $pulito);
//                $pulito = str_replace('(', '\(', $pulito);
//                $pulito = str_replace(')', '\)', $pulito);
//                $pulito = str_replace('=', '\=', $pulito);
//                $pulito = str_replace('>', '\>', $pulito);
//                $pulito = str_replace('<', '\<', $pulito);
                return $pulito;
            }
        }

        protected function pathTo($controller = "", $action = "", $parameters = array()) {
            $path = SITE_PATH;
            ;
            if ($controller !== "")
            {
                $path .= $controller . "/";
                if ($action !== "")
                {
                    $path .= $action . "/";
                    if (is_array($parameters))
                    {
                        $path .= join("/", $parameters);
                    }
                }
            }
            return $path;
        }

        protected function breadCrumb($path) {
            $ret = "";
            foreach ($path as $k => $v) {
                if ($v === "last")
                {
                    $ret .= "<li class=\"active\">{$k}</li>\n";
                }
                else
                {
                    $ret .= "<li><a href=\"" . SITE_PATH . "{$v}\">{$k}</a></li>\n";
                }
            }
            return $ret;
        }

        public function setJsonWS() {
            $this->setWillRenderActionView(false);
            $this->setWillRenderLayoutView(false);
            header('Content-type: application/json');
        }

        protected function _setResponseStatus($status = 200) {
            switch ($status) {
                case 503:
                    header("HTTP/1.0 503 Service Unavailable");
                    break;
                case 500:
                    header("HTTP/1.0 500 Internal Server Error");
                    break;
                case 409:
                    header("HTTP/1.0 409 Conflict");
                    break;
                case 405:
                    header("HTTP/1.0 405 Method Not Allowed");
                    break;
                case 404:
                    header("HTTP/1.0 404 Not Found");
                    break;
                case 403:
                    header("HTTP/1.0 403 Forbidden");
                    break;
                case 401:
                    header("HTTP/1.0 401 Unauthorized");
                    break;
                case 400:
                    header("HTTP/1.0 400 Bad Request");
                    break;
                case 202:
                    header("HTTP/1.0 202 Accepted");
                    break;
                case 201:
                    header("HTTP/1.0 201 Created");
                    break;
                default:
                    header("HTTP/1.0 200 OK");
                    break;
            }
        }

        public function loadScript($scriptRelPath, $scriptType = 0) {
            if ($scriptType === 0)
            {
                $split = explode(".", $scriptRelPath);
                if (count($split) < 2)
                    return;
                $ext = $split[count($split) - 1];
                switch ($ext) {
                    case "js":
                    case "css":
                        $scriptType = $ext;
                        break;
                    default:
                        return;
                }
            }
            foreach ($this->script_inc as $scriptI => $script) {
                if ($script[0] === $scriptType && $script[1] === $scriptRelPath)
                {
                    return;
                }
            }
            $this->script_inc[] = array($scriptType, $scriptRelPath);
            return;
        }

        public function unloadScript($scriptRelPath, $scriptType = 0) {
            if ($scriptType === 0)
            {
                $split = explode(".", $scriptRelPath);
                if (count($split) < 2)
                    return;
                $ext = $split[count($split) - 1];
                switch ($ext) {
                    case "js":
                    case "css":
                        $scriptType = $ext;
                        break;
                    default:
                        return;
                }
            }
            foreach ($this->script_inc as $scriptI => $script) {
                if ($script[0] === $scriptType && $script[1] === $scriptRelPath)
                {
                    unset($this->script_inc[$scriptI]);
                    return;
                }
            }
            return;
        }

        public function instantiateMysqli($registryDbName) {
            $db = \WolfMVC\Registry::get("database_" . $registryDbName);
            $link = new \mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
            $link->set_charset("utf8");
            return $link;
        }

        public function retrievePars($pars) {
            /*
             * pars = array
             * 0 => "nome par", "metodo", dieIfNotFound, default
             */
            $out = array();
            foreach ($pars as $k => $v) {
                if (strtolower($v[1]) === "get")
                {
                    if (isset($_GET[$v[0]]))
                    {
                        $out[$v[0]] = $this->anti_injection($_GET[$v[0]]);
                    }
                    else
                    {
                        if ($v[2])
                        { // die if not found
                            return array("error" => true, "error_desc" => "Missing or wrong parameter: " . $v[0]);
                        }
                        else
                        {
                            $out[$v[0]] = ((isset($v[3]) && !empty($v[3])) ? $v[3] : "");
                        }
                    }
                }
                else if (strtolower($v[1]) === "post")
                {
                    if (isset($_POST[$v[0]]))
                    {
                        $out[$v[0]] = $this->anti_injection($_POST[$v[0]]);
                    }
                    else
                    {
                        if ($v[2])
                        { // die if not found
                            return array("error" => $error, "error_desc" => "Missing or wrong parameter: " . $v[0]);
                        }
                        else
                        {
                            $out[$v[0]] = ((isset($v[3]) && !empty($v[3])) ? $v[3] : "");
                        }
                    }
                }
            }
            return $out;
        }

//        protected function setBackTrack() {
//            $session = \WolfMVC\Registry::get("session");
//            $back = $session->get("backtrack");
//            if ($back === NULL)
//            {
//                $back = array();
//            }
//            else
//            {
//                $last = $_SERVER['REQUEST_URI'];
//                $search = array_search($last, $back);
//                if ($search === FALSE)
//                {
//                    array_push($back, $last);
//                }
//                else
//                {
//                    $back = array_slice($back, 0, $search + 1);
//                }
//            }
//            $session->set("backtrack", $back);
//        }
//
//        protected function getBackTrack() {
//            $session = \WolfMVC\Registry::get("session");
//            $back = $session->get("backtrack");
//            if (!$back || empty($back))
//            {
//                return null;
//            }
//            if (count($back) < 2)
//            {
//                return null;
//            }
//            else
//            {
//                return $back[count($back) - 2];
//            }
//        }
//        public function setDataOp($mode, $pars = array()) {
//            if (!isset($this->_regOperations) || !(is_array($this->_regOperations)))
//            {
//                $this->_regOperations = array();
//            }
//            if (!isset($this->_regOperations["data"]) || !is_array($this->_regOperations["data"]))
//            {
//                $this->_regOperations["data"] = array();
//            }
//            if (!is_string($mode))
//            {
//                throw new \Exception("Mode must be a string", 0, null);
//            }
//            if (!is_array($pars))
//            {
//                throw new \Exception("Pars must be an array", 0, null);
//            }
//            $newoperationcode = Registry::hashing(15);
//            $this->_regOperations["data"][$newoperationcode] = array($mode, $pars);
//            return $this;
//        }
//        public function ws___data() {
//
//
////            $view = $this->getActionView();
//            header('Content-type: application/json');
//            if (!isset($this->_usePageComponent) || !($this->_usePageComponent))
//            {
//                echo json_encode("This controller doesn't allow pageComponent Service");
//                exit;
//            }
//            $ret = array();
//            $ret[0] = "No WS available at such address";
//            $ret['RequestAccept'] = $this->parseAcceptHeader();
//            echo json_encode("La componente " . $this->_parameters[0] . " richiede l'operazione " . $this->_parameters[1]);
//            exit;
////            $view->set("data", json_encode("No WS available at such address"));
//        }

        /*
         * 
         * 
         * 
         */

        protected function _getExceptionForImplementation($method) {
//return new Exception\Implementation("{$method} method not implemented");
            return new Exception\Implementation(Registry::get("language")->sh("WolfMVC.Controller.Exception.Implementation", array($method)));
        }

        protected function _getExceptionForArgument() {
            return new Exception\Argument("Invalid argument");
        }

        public function nameofthiscontroller() {
            $router = \WolfMVC\Registry::get("router");
            return $router->getController();
        }

        public function nameofthisaction() {
            $router = \WolfMVC\Registry::get("router");
            $act = $router->getAction();
            $act = str_ireplace("ws___", "", $act);
            return $act;
        }

        public function index() {
            echo Registry::get("language")->sh("WolfMVC.Controller.genericindexmethod");
        }

        public function render() {
            $layoutenvvars = array(
                "stdpagetitle" => "WolfMVC",
                "customlogo" => "",
                "user" => "",
                "bootstrap" => "",
                "fa" => "",
                "bootstrapjs" => ""
            );
            $envvars = \WolfMVC\Registry::get("layoutenvvars");
            if (!(is_null($envvars)))
            {
                foreach ($layoutenvvars as $key => $var) {
                    if (isset($envvars->$key) && !(is_null($envvars->$key)))
                    {
                        $layoutenvvars[$key] = $envvars->$key;
                    }
                }
            }

            $defaultContentType = $this->getDefaultContentType();
            $results = null;
            $doAction = $this->getWillRenderActionView() && $this->getActionView();
            $doLayout = $this->getWillRenderLayoutView() && $this->getLayoutView();

            try {
                if ($doAction)
                {
                    $view = $this->getActionView();
                    $view->set("imgpath", "'/tsnwprerelease/public/img/'");

                    $results = $view->render();
                }

                if ($doLayout)
                {
                    $session = \WolfMVC\Registry::get("session");
                    $systemStatus = Registry::get("systemstatus");
                    $view = $this->getLayoutView();
                    $view->set("system_js_including", $this->_system_js_including);
                    $script_inc = "";
                    foreach ($this->script_inc as $scriptI => $script) {
                        if (count($script) !== 2)
                            continue;
                        switch ($script[0]) {
                            case 'js':
                                $script_inc .= "<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/{$script[1]}\"></script>";
                                break;
                            case 'css':
                                $script_inc .= "<link rel=\"stylesheet\" href=\"" . SITE_PATH . "css/{$script[1]}\">";
                                break;
                        }
                    }
                    $view->set("script_inc", $script_inc);
                    $view->set("stdpagetitle", $layoutenvvars["stdpagetitle"]);
                    $view->set("customlogo", $layoutenvvars["customlogo"]);
                    $view->set("bootstrap", $layoutenvvars["bootstrap"]);
                    $view->set("fa", $layoutenvvars["fa"]);
                    $view->set("bootstrapjs", $layoutenvvars["bootstrapjs"]);
                    $view->set("cssroot", SITE_PATH . "css/");
                    $view->set("logoutpath", "'" . SITE_PATH . "authenticate/logout'");
                    $view->set("logoutpath", "'" . SITE_PATH . "authenticate/logout'");
                    $view->set("imgpath", "/" . SITE_VERSION . "/public/img/");
                    $view->set("sitepath", SITE_PATH);
                    $view->set("sitepathapos", "'" . SITE_PATH . "'");
                    $view->set("session", print_r($session,true));
                    $view->set("registry", Registry::esponi(true));
                    if ($session->get("gLogged") && $session->get("auth") && \WolfMVC\Registry::get("usertodisplay") === "Alberto Brudaglio")
                    {
                        $adminPanel = "<br><br><br><br><br><br>PANNELLO ADMIN:<br>";
                    }
                    if (isset($systemStatus->news))
                    {
                        $view->set("news", $systemStatus->news);
                    }
                    ob_start();
                    print_r($_SESSION);
                    $sess = ob_get_contents();
                    ob_end_clean();
                    $view->set("session", $sess);
//autoinclude css and js
                    $cont = $this->nameofthiscontroller();
                    $act = $this->nameofthisaction();
                    $css_autoinclude = array();
                    $dir = APP_PATH . '/public/css/autoinclude/' . $cont . "/" . $act;

//                    echo $dir;
                    if (is_dir($dir))
                    {
                        $files = scandir($dir);
                        foreach ($files as $file) {
                            if ($file !== "." && $file !== "..")
                            {
                                array_push($css_autoinclude, str_ireplace("/var/www", "", $dir) . "/" . $file);
                            }
                        }
                    }
                    if (count($css_autoinclude) > 0)
                    {
                        $view->set("css_autoinclude", $css_autoinclude);
                    }
                    $js_autoinclude = array();
                    $dir = APP_PATH . '/public/js/autoinclude/' . $cont . "/" . $act;
                    if (is_dir($dir))
                    {

                        $files = scandir($dir);
                        foreach ($files as $file) {
                            $minified = $dir . "/" . $file;
                            $minified = str_ireplace(".js", "-min.js", $minified);
                            if (is_file($minified))
                                continue;
                            else if ($file !== "." && $file !== "..")
                            {
                                array_push($js_autoinclude, str_ireplace("/var/www", "", $dir) . "/" . $file);
                            }
                        }
                    }
                    if (count($js_autoinclude) > 0)
                    {
                        $view->set("js_autoinclude", $js_autoinclude);
                    }
                    $homeIcon = $view->get("imgpath") . "home.png";
                    $view->set("homeIcon", $homeIcon);
                    $session = \WolfMVC\Registry::get("session");
                    if ($session->get("gLogged") && $session->get("auth"))
                    {
                        $view->set("gLogged", true);
                        $googleIcon = $view->get("imgpath") . "google.png";
                        $view->set("googleIcon", $googleIcon);
                    }
                    if ($session->get("vtiger_logged_user_id") && $session->get("auth"))
                    {
                        $view->set("vtiger_logged_user_id", $session->get("vtiger_logged_user_id"));
                        $vtigerIcon = $view->get("imgpath") . "vtiger.png";
                        $view->set("vtigerIcon", $vtigerIcon);
                    }
                    if ($session->get("asterisk_extension") && $session->get("auth"))
                    {
                        $view->set("asterisk_extension", $session->get("asterisk_extension"));
//                        $vtigerIcon = $view->get("imgpath") . "vtiger.png";
//                        $view->set("vtigerIcon", $vtigerIcon);
                    }
                    $usertodisplay = \WolfMVC\Registry::get("usertodisplay");
                    if ((isset($usertodisplay)) && $usertodisplay !== "" && (!(is_null($usertodisplay))))
                    {
                        $layoutenvvars["user"] = $usertodisplay;
                    }
                    if ($session->get("auth"))
                    {
                        $view->set("user", $layoutenvvars["user"]);
                    }
                    $view->set("template", $results);
                    if (isset($adminPanel))
                    {
                        $adminPanel .= "<br>AUTH: " . $session->get("auth");
                        $adminPanel .= "<br>SYSTEM TIME: " . time();
                        $vtws = \WolfMVC\Registry::get("VTWS");
                        $adminPanel .= "<br>VTWS:<pre> " . print_r($vtws, true) . "<pre>";
                        $view->set("adminPanel", $adminPanel);
                    }
                    $results = $view->render();

                    header("Content-type: {$defaultContentType}");
                    echo $results;
                }
                else if ($doAction)
                {
                    header("Content-type: {$defaultContentType}");
                    echo $results;

                    $this->setWillRenderLayoutView(false);
                    $this->setWillRenderActionView(false);
                }
            } catch (\Exception $e) {
                throw new View\Exception\Renderer("Invalid layout/template syntax");
            }
        }

        public function __destruct() {
            $this->render();
        }

        public function disablerender() {
            $this->setWillRenderActionView(false);
            $this->setWillRenderLayoutView(false);
        }

        /**
         * @before disablerender
         */
        public function ws___describe() {


//            $view = $this->getActionView();
            header('Content-type: application/json');
            $ret = array();
            $ret[0] = "No WS available at such address";
            $ret['RequestAccept'] = $this->parseAcceptHeader();
            echo json_encode($ret);
            exit;
//            $view->set("data", json_encode("No WS available at such address"));
        }

        public function parseAcceptHeader() {
            $hdr = $_SERVER['HTTP_ACCEPT'];
            $accept = array();
            foreach (preg_split('/\s*,\s*/', $hdr) as $i => $term) {
                $o = new \stdclass;
                $o->pos = $i;
                if (preg_match(",^(\S+)\s*;\s*(?:q|level)=([0-9\.]+),i", $term, $M))
                {
                    $o->type = $M[1];
                    $o->q = (double) $M[2];
                }
                else
                {
                    $o->type = $term;
                    $o->q = 1;
                }
                $accept[] = $o;
            }
            usort($accept, function ($a, $b) {
                /* first tier: highest q factor wins */
                $diff = $b->q - $a->q;
                if ($diff > 0)
                {
                    $diff = 1;
                }
                else if ($diff < 0)
                {
                    $diff = -1;
                }
                else
                {
                    /* tie-breaker: first listed item wins */
                    $diff = $a->pos - $b->pos;
                }
                return $diff;
            });
            $accept_data = array();
            foreach ($accept as $a) {
                $accept_data[$a->type] = $a->type;
            }
            return $accept_data;
        }

        public function __construct($options = array()) {
            parent::__construct($options);
//            $this->setBackTrack();
            $session = \WolfMVC\Registry::get("session");

            if ($this->getWillRenderLayoutView())
            {
                $defaultPath = $this->getDefaultPath();
                $defaultLayout = $this->getDefaultLayout();
                $defaultExtension = $this->getDefaultExtension();

                $view = new View(array(//questo pezzo può essere replicato altrove per cambiare il file template usato per il layout
                    "file" => APP_PATH . "/{$defaultPath}/{$defaultLayout}.{$defaultExtension}"
                ));

                $this->setLayoutView($view);
            }

            if ($this->getWillRenderLayoutView())
            {

                $router = Registry::get("router");
                $controller = $router->getController();
                $action = $router->getAction();



                $view = new View(array(//questo pezzo può essere replicato altrove per cambiare il file template usato per la vista
                    "file" => APP_PATH . "/{$defaultPath}/{$controller}/{$action}.{$defaultExtension}"
                ));
                $this->setActionView($view);
            }
        }

//overwrite this in specific models
        public static function getModelStructure($modelname) {
            throw new \Exception("Unknown model " . $modelname, 0, null);
            return null;
        }

        public static function putLog($piva, $what, $parameters, $comment = NULL, $esito = NULL, $parent = 0) {
            \ActiveRecord\Config::initialize(
                    \WolfMVC\Registry::get("activerecord_initializer")
            );
            $session = \WolfMVC\Registry::get("session");
            $who = $session->get("userid");
            $who_vt = $session->get("vtiger_logged_user_id");
            if ($parameters && !is_string($parameters))
            {
                $parameters = json_encode($parameters, JSON_FORCE_OBJECT);
            }
            if (!in_array((int) $what[0], array(1, 2, 3, 4)))
            {
                switch ($what[0]) {
                    case 'ac':
                        $what[0] = 1;
                        break;
                    case 'op':
                        $what[0] = 2;
                        break;
                    case 'pr':
                        $what[0] = 3;
                        break;
                    case 'ev':
                        $what[0] = 4;
                        break;
                    default:
                        return array($what, 400, "Invalid what_type " . $what[0]);
                }
            }
            if (!isset($what[1]))
            {
                return array($what, 400, "Invalid what_main");
            }
            if (!isset($what[2]))
            {
                return array($what, 400, "Invalid what_detail");
            }
            if (!isset($what[3]))
            {
                return array($what, 400, "Invalid what_element");
            }
            try {
                $logRow = new \AR\Tsnwlog(array(
                    "piva" => $piva,
                    "what_type" => $what[0],
                    "what_main" => "" . $what[1],
                    "what_detail" => "" . $what[2],
                    "what_element" => $what[3],
                    "who" => $who,
                    "who_vtiger" => $who_vt,
                    "parameters" => $parameters,
                    "esito" => 1,
                    "parent" => $parent
                ));
                $logRow->save();
                if ($comment)
                {
                    $logRow->create_comment(array(
                        "commento" => $comment
                    ));
                }
            } catch (\ActiveRecord\ActiveRecordException $e) {

                return array("Something went wrong1", 500, array($e->getMessage(), "data" => array($piva, $what)));
            } catch (\Exception $e) {
                return array("Something went wrong2", 500, array($e->getCode(), $e->getFile(), $e->getLine(), $e->getMessage(), "data" => array($piva, $what)));
            }
            return array($logRow->attributes(true), 200, true);
        }

        public function ws___newPutLog() {
            $this->setJsonWS();
            $piva = RequestMethods::post("piva", false);
            if (!isset($piva) || $piva === false)
            {
                $this->_setResponseStatus(400);
                echo json_encode("Missing piva");
            }
            $what = RequestMethods::post("what", false);
            if (!isset($what) || $what === false)
            {
                $this->_setResponseStatus(400);
                echo json_encode("Missing or invalid what");
            }
            $parameters = RequestMethods::post("parameters", NULL);
            $comment = RequestMethods::post("comment", NULL);
            $esito = RequestMethods::post("esito", NULL);
            $parent = RequestMethods::post("parent", 0);
            $res = self::putLog($piva, $what, $parameters, $comment, $esito, $parent);
            $this->_setResponseStatus($res[1]);
            echo json_encode(array($res[0], $res[2]));
            return;
        }

        public function ws___putLog() {
            $session = \WolfMVC\Registry::get("session");
            $this->setJsonWS();
            $link = $this->instantiateMysqli("mkt");
            $link2 = $this->instantiateMysqli("vtiger");
            $pars = $this->retrievePars(
                    array(
                        array("piva", "post", true),
                        array("what", "post", true),
                        array("parameters", "post", false),
                        array("comment", "post", false),
                        array("esito", "post", false),
                        array("parent", "post", false)
                    )
            );
            if (isset($pars["error"]) && $pars["error"])
            {
                $this->_setResponseStatus(500);
                echo json_encode($pars);
                return;
            }
            if (isset($pars["parameters"]))
            {
                $pars["parameters"] = json_encode($pars["parameters"], JSON_FORCE_OBJECT);
            }
            //1 scrivere la riga
            //2 scrivere i commenti (eventuali)
            //3 restituire la riga inserita
            $query1 = "INSERT INTO tsnw_log " .
                    "(piva, what_type, what_main, what_detail, what_element, who, who_vtiger, parameters, esito, parent) values " .
                    "( '{$pars["piva"]}'";
            $what = $pars["what"];
            switch ($what[0]) {
                case 'ac':
                    $query1 .= ", '1'";
                    break;
                case 'op':
                    $query1 .= ", '2'";
                    break;
                case 'pr':
                    $query1 .= ", '3'";
                    break;
                case 'ev':
                    $query1 .= ", '4'";
                    break;
                default:
                    echo json_encode(false);
                    return;
            }
            $query1 .= ", '{$what[1]}', '{$what[2]}', '{$what[3]}'";

            $query1 .= ", '{$session->get("userid")}'";
            $query1 .= ", '{$session->get("vtiger_logged_user_id")}'";
            if (isset($pars["parameters"]))
            {
                $query1 .= ", '{$pars["parameters"]}'";
            }
            else
            {
                $query1 .= ", ''";
            }
            if (isset($pars["esito"]))
            {
                $query1 .= ", '{$pars["esito"]}'";
            }
            else
            {
                $query1 .= ", '1'";
            }
            if (isset($pars["parent"]))
            {
                $query1 .= ", '{$pars["parent"]}'";
            }
            else
            {
                $query1 .= ", '0'";
            }
            $query1 .= ")";


            if ($link->connect_errno)
            {
                $this->_setResponseStatus(500);
                echo json_encode("Error occurred in db connection!");
                return;
            }
            $sql = $query1;

            $result = $link->query($sql);
            if ($result && $link->affected_rows)
            {
                $pars["inserted_id"] = $link->insert_id;

                if (isset($pars["comment"]) && $pars["comment"] !== "")
                {
                    $query2 = "INSERT INTO tsnw_comments " .
                            "(log_id, comment) VALUES " .
                            "('{$pars["inserted_id"]}','{$pars["comment"]}')";
                    $result2 = $link->query($query2);
                    if ($result2 && $link->affected_rows)
                    {
                        echo json_encode($pars);
                        return;
                    }
                    else
                    {
                        $this->_setResponseStatus(500);
                        echo json_encode(false);
                        return;
                    }
                }
                else
                {
                    echo json_encode($pars);
                    return;
                }
            }
            else
            {
                $this->_setResponseStatus(500);
                echo json_encode($link->error);
                return;
            }
        }

        public function ws___getLog() {
            $session = \WolfMVC\Registry::get("session");
            $this->setJsonWS();
            $link = $this->instantiateMysqli("mkt");
            $link2 = $this->instantiateMysqli("vtiger");
            $pars = $this->retrievePars(
                    array(
                        array("piva", "get", true),
                    )
            );
            if (isset($pars["error"]) && $pars["error"])
            {
                $this->_setResponseStatus(500);
                echo json_encode($pars);
                return;
            }
            $query1 = "SELECT piva, timestamp, log_id, " .
                    "CONCAT(DAY(timestamp),'-',MONTH(timestamp),'-',YEAR(timestamp)) as data, " .
                    "CONCAT(HOUR(timestamp),':',MINUTE(timestamp),':',SECOND(timestamp)) as ora, " .
                    "c.what_main, c.what_detail, what_element, who, who_vtiger, " .
                    "a.what_main as code_main, a.what_detail as code_detail, a.what_type as code_type, " .
                    "parameters, b.comment, a.parent, now() as now " .
                    "FROM tsnw_log a " .
                    "LEFT JOIN tsnw_comments b USING (log_id) " .
                    "LEFT JOIN tsnw_log_what_codes c ON (a.what_main = c.code_main AND a.what_detail = c.code_detail) WHERE piva = '{$pars["piva"]}' " .
                    "ORDER BY timestamp ASC ";
            if ($link->connect_errno)
            {
                $this->_setResponseStatus(500);
                echo json_encode("Error occurred in db connection!");
                return;
            }
            $sql = $query1;

            $result = $link->query($sql);
            if ($result)
            {
                echo json_encode($result->fetch_all(MYSQLI_ASSOC));
                return;
            }
            else
            {
                $this->_setResponseStatus(500);
                echo json_encode($link->error);
                return;
            }
        }

        public function ws___sendReportMail() {
            $this->setJsonWS();
            $payload = array(
                "dove" => \WolfMVC\RequestMethods::post("dove", false),
                "cosa" => \WolfMVC\RequestMethods::post("cosa", false),
                "commento" => \WolfMVC\RequestMethods::post("commento", false),
            );
            if (!isset($payload["dove"]) || !$payload["dove"])
            {
                $this->_setResponseStatus(400);
                echo json_encode("Missing dove");
                return;
            }
            if (!isset($payload["cosa"]) || !$payload["cosa"])
            {
                $this->_setResponseStatus(400);
                echo json_encode("Missing cosa");
                return;
            }
            $payload["quando"] = new \DateTime();
            $payload["quando"] = $payload["quando"]->format('Y-m-d H:i:s');
            $session = \WolfMVC\Registry::get("session");

            $payload["chi"] = $session->get("googleUser");
            if (is_array($payload["chi"])){
                $payload["chi"] = $payload["chi"]["displayName"];
            }
            else if (is_object($payload["chi"])){
                $payload["chi"] = $payload["chi"]->displayName;
            }

            $url = 'https://script.google.com/macros/s/AKfycbz_S7crWgK4S3y2tW46wni07oFK7nn2ig1qB6DL6n_NW4ThlUk/exec';
            $fields = array();
            foreach ($payload as $k => $p) {
                if (is_string($p))
                {
                    $fields[$k] = urldecode($p);
                }
                else
                {
                    $fields[$k] = urldecode(json_encode($p));
                }
            }
            $out_payload = http_build_query($fields);
            
//
            ob_start();
            $ch = curl_init($url);
            $options = array(CURLOPT_RETURNTRANSFER => true, // return web page
                CURLOPT_HEADER => 0, // don't return headers
                CURLOPT_FOLLOWLOCATION => true, // follow redirects
                CURLOPT_ENCODING => "", // handle compressed
                CURLOPT_USERAGENT => "test", // who am i
                CURLOPT_AUTOREFERER => true, // set referer on redirect
                CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
                CURLOPT_TIMEOUT => 120, // timeout on response
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_POST => count($fields),
                CURLOPT_POSTFIELDS => $out_payload
            );

            curl_setopt_array($ch, $options);

            $result = curl_exec($ch);
            if (is_string($result))
                $result = json_decode($result, true);
            $err = curl_errno($ch);
            $errmsg = curl_error($ch);
            $header = curl_getinfo($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);
            ob_end_clean();
            $out["fields"] = $fields;

            $payload["ricevuto"] = true;
            $out["payload"] = $payload;

            $out["out_payload"] = $out_payload;
            $out["result"] = $result;
            $out["err"] = $err;
            $out["errmsg"] = $errmsg;
            $this->_setResponseStatus($httpCode);
            if ($httpCode !== 200)
            {
                echo json_encode(array(
                    "err" => $err,
                    "errmsg" => $errmsg
                ));
                return;
            }
            if (is_array($result) && isset($result["ricevuto"]) && $result["ricevuto"] !== "ok")
            {
                echo json_encode(array(
                    "err" => $err,
                    "errmsg" => $errmsg,
                    "result" => $result
                ));
                return;
            }
            echo json_encode($out);
            return;
        }

    }

}
