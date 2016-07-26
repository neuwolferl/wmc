<?php

use WolfMVC\Controller as Controller;

class Test extends Controller {

    public function __construct($options = array()) {

        parent::__construct($options);
    }

    /**
     * @protected
     */
    public function script_including() {

        /*
         * temporaneamente appoggio qui l'autorizzazione sul controller
         */
        $session = \WolfMVC\Registry::get("session");
        $user = $session->get("user");
        if (!in_array($user, array("Alberto Brudaglio", "Vincenzo Cervadoro")))
        {
            header("Location: " . SITE_PATH);
        }

//        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/jquery.js\"></script>";
//        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/angular.min.js\"></script>";
//        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/angular-resource.min.js\"></script>";
//        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ngbootstrap.min.js\"></script>";
//        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ng-ui-bootstrap-tpls-0.2.0.js\"></script>";
//        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/core/data.js\"></script>";
//        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/core/tsnwclient.js\"></script>";

        $view = $this->getLayoutView();
        $view->set("moduleName", "PAGINA DI TEST PER RICHIESTE HTTP");
    }

    public function api() {
        header("Access-Control-Allow-Origin: *");
        $this->loadScript("utils.js");
        $this->loadScript("jquery.js");
        $this->loadScript("angular.min.js");
        $this->loadScript("ngbootstrap.min.js");
        $this->loadScript("ng-ui-bootstrap-tpls-0.2.0.js");
        $this->loadScript("ngsortable.js");
        $this->loadScript("core/data-0.2/data-0.2.js");
        $this->loadScript("core/themask.js");
        $this->loadScript("core/angdad.js");
        $this->loadScript("lodash.js");
        $this->loadScript("angular-google-maps.min.js");
        $this->loadScript("core/tsMkt/tsMkt-0.1.js");
        $this->loadScript("core/common/common-0.1.js");
        $this->loadScript("core/common/commonInclude.js");
        $this->unloadScript("core/data-0.2/data-0.2.js");
        $this->loadScript("core/datamodule/data-0.5.js");
        $this->loadScript("core/smartButton/smartButton-0.1-min.js");
        $this->loadScript("component/multiPage/multiPage-0.1.js");
        $this->loadScript("component/sinottico/sinottico-0.2.js");
        $this->loadScript("core/RGraph/libraries/RGraph.common.core.js");
        $this->loadScript("core/RGraph/libraries/RGraph.gantt.js");
        $this->loadScript("core/RGraph/libraries/RGraph.common.dynamic.js");
        $this->loadScript("core/RGraph/libraries/RGraph.drawing.rect.js");
        $this->loadScript("core/RGraph/libraries/RGraph.drawing.text.js");
        $this->loadScript("restangular.js");
//        $this->loadScript("component/pageSwiper/pageSwiper.css");
        $this->loadScript("angular-animate.min.js");
        $view = $this->getLayoutView();
        $view->set("moduleName", "PAGINA DI TEST api");
        $aaview = $this->getActionView();
        $aaview->set("index", '$index');
        $aaview->set("root", '$root');
        $aaview->set("error", '$error');
        $aaview->set("dirty", '$dirty');
        $aaview->set("invalid", '$invalid');
    }

    public function index() {
        $this->disablerender();
        $ar_cfg = \ActiveRecord\Config::initialize(
                        \WolfMVC\Registry::get("activerecord_initializer")
        );

//        echo "Elenco risorse disponibili<br>";
//        $dirpath = $ar_cfg->get_model_directory();//APP_PATH . "/application/configuration/models";
//        echo $dirpath . "<br>";
//        $linear = \WolfMVC\FsMethods::pathLinearize($dirpath);
////        echo token_name(315);
//        $found_all = array();
//        $look_up = array(
//            array(T_NAMESPACE, T_WHITESPACE, T_STRING),
//            array(T_CLASS, T_WHITESPACE, T_STRING),
//            array(T_EXTENDS, T_WHITESPACE, T_NS_SEPARATOR, T_STRING, T_NS_SEPARATOR, T_STRING)
//        );
//        $look_up_static_lin_props = array(array(T_STATIC, T_WHITESPACE, T_VARIABLE));
//
//        $stop = true;
//        foreach ($linear as $k => $f) {
//            $tokens = token_get_all(file_get_contents($f[1] . "/" . $f[0]));
//            if (!is_array($tokens))
//            {
//                continue;
//            }
////            echo "<pre>";
////            print_r(array_map(function($t){
////                if (is_array($t) && count($t)>1)
////                    $t[0] = $t[0]."   ".token_name($t[0]);
////                return $t;
////            },$tokens));
//////            print_r($tokens);
////            echo "</pre>";
//            $start = 0;
//            $end = 0;
//            $found = WolfMVC\FileMethods::searchPhpCode($tokens, $look_up, $start, $end);
//
//            if (
//                    count($found) === count($look_up) && isset($found[0][2]) && $found[0][2] === "AR" && isset($found[2][3]) && $found[2][3] === "ActiveRecord" && isset($found[2][5]) && $found[2][5] === "Model"
//            )
//            {
//                $start = $end;
//                $static_props = WolfMVC\FileMethods::searchPhpCode($tokens, $look_up_static_lin_props, $start, $end);
//
//                $ar_classname = "\AR\\" . $found[1][2];
//                foreach ($static_props as $si => $sp) {
//                    $variablename = str_ireplace('$', '', $sp[2]);
//                    if (!isset($ar_classname::$$variablename))
//                        $static_props[$variablename] = null;
//                    else
//                        $static_props[$variablename] = $ar_classname::$$variablename;
//                    unset($static_props[$si]);
//                }
//                if ($f[0] === $found[1][2] . ".php")
//                {
//                    $found_all[] = array("path" => $f[1], "file" => $f[0], "class" => $found[1][2], "static_props" => $static_props);
//                }
//                continue;
//            }
//        }
//        echo "<pre>";
//        print_r($found_all);
//        echo "<pre>";

        echo "Users<br>";
        $users = \AR\Tsnwuser::all();
//        array_map(function($a) {
//            echo "<pre>";
//            print_r($a->attributes(true));
//            echo "</pre>";
//        }, $users);
        echo "Creo nuovo utente<br>";
        $user = new \AR\Tsnwuser();
//        print_r($user->attributes(true));
        $attrs = array(
            "nome_utente" => "nome_utente_prova",
            "password" => "akvz",
            "nome" => "nome_prova",
            "cognome" => "cognome_prova",
            "attivo" => 1,
            "nome_visualizzato" => "nome_cognome_prova"
        );
        $user->set_attributes($attrs);
        $table = $user->table();
//        $table::clear_cache(null);
//        echo "<pre>";
//        print_r($user->get_relationships());
//        echo "</pre>";
//        echo "<pre>";
//        print_r($user->table());
//        echo "</pre>";
        try {
            $user->build_groups(array(
                'group_name' => 'gruppo_prova',
                'parent' => null
            ));
        } catch (\Exception $e) {
            echo "<br>Eccezione<br>";
            echo "<br>" . $e->getCode() . "<br>";
            echo "<br>" . $e->getMessage() . "<br>";
            echo "<pre>";
            print_r($e->getTrace());
            echo "</pre>";
        }
        echo "Lo valorizzo<br>";
        echo "<pre>";
        print_r($user->attributes());
        print_r($user->groups);
        echo "<pre>";
//          $user = $users[0];
//        $user->nome_utente = "prova2";
//        $user->delete();
    }

    private function analyzeCustomTables($customDescription) {
        $tabs = $customDescription["tables"];
        $fies = $customDescription["fields"];
        $lastPos = 0;
        $positions = array();
        $matches = array();
        $tables = array();
        $fields = array();
        while ($lastPos = min(strpos($tabs, " LEFT JOIN ", $lastPos), strpos($tabs, " JOIN ", $lastPos))) {
            $positions[] = $lastPos;
            $lastPos += ($tabs[$lastPos + 1] === "L" ? 11 : 6);
        }
        if (count($positions) > 0)
            $matches[0] = substr($tabs, 0, $positions[0]);
        else
            $matches[0] = $tabs;
        $pippo = explode(" ", $matches[0]);
        $tablename = $pippo[0];
        $tablealias = $pippo[1];
        $tables[$tablealias] = $tablename;
        $matches[0] = $tablealias;

        foreach ($positions as $k => $p) {
            $matches[] = trim(substr($tabs, $p, ($tabs[$p + 1] === "L" ? 11 : 6)));
            if ($k < count($positions) - 1)
            {
                $str = substr($tabs, $p + ($tabs[$p + 1] === "L" ? 11 : 6), $positions[$k + 1] - $p - ($tabs[$p + 1] === "L" ? 11 : 6));
            }
            else
            {
                $str = substr($tabs, $p + ($tabs[$p + 1] === "L" ? 11 : 6), strlen($tabs) - $p - ($tabs[$p + 1] === "L" ? 11 : 6));
            }
            $vet = explode(" ON ", $str);

            $pippo = explode(" ", $vet[0]);
            $tablename = $pippo[0];
            $tablealias = $pippo[1];
            $tables[$tablealias] = $tablename;
            $matches[] = $tablealias;
            $matches[] = "ON";
            $matches[] = trim($vet[1], "()");
        }

        $db = \WolfMVC\Registry::get("database_vtiger");
        $link = new mysqli($db->host, $db->username, $db->password, $db->schema);
        $sql = "DESCRIBE %s";
        foreach ($tables as $alias => $tablename) {
            $result = $link->query(sprintf($sql, $tablename));
            if ($result)
            {
//                $tables[$alias] = array_merge(array("tablename" => $tablename), );
                $tmpfields = $result->fetch_all(MYSQLI_ASSOC);
                foreach ($tmpfields as $kfield => $field) {
                    if (isset($fies[$tablealias . "." . $field["Field"]]))
                    { //se il campo preso dal db è stato preso in struttura
                        $tmpfields[$kfield]["name"] = $field["Field"];
                        if (isset($fies[$tablealias . "." . $field["Field"]]["label"]))
                        {
                            $tmpfields[$kfield]["label"] = $fies[$tablealias . "." . $field["Field"]]["label"];
                        }
                        else
                        {
                            $tmpfields[$kfield]["label"] = ucfirst($tmpfields[$kfield]["name"]);
                        }
                        if (isset($fies[$tablealias . "." . $field["Field"]]["mandatory"]))
                        {
                            $tmpfields[$kfield]["mandatory"] = $fies[$tablealias . "." . $field["Field"]]["mandatory"];
                        }
                        else
                        {
                            $tmpfields[$kfield]["mandatory"] = FALSE;
                        }
                        $tmpfields[$kfield]["nullable"] = $tmpfields[$kfield]["Null"] === "NO" ? FALSE : TRUE;
                        $tmpfields[$kfield]["default"] = (isset($tmpfields[$kfield]["Default"]) && !empty($tmpfields[$kfield]["Default"]) ? $tmpfields[$kfield]["Default"] : "");
                        if (isset($fies[$tablealias . "." . $field["Field"]]["editable"]))
                        {
                            $tmpfields[$kfield]["editable"] = $fies[$tablealias . "." . $field["Field"]]["editable"];
                        }
                        else
                        {
                            $tmpfields[$kfield]["editable"] = TRUE;
                        }
                        if (isset($fies[$tablealias . "." . $field["Field"]]["type"]))
                        {
                            $tmpfields[$kfield]["type"] = $fies[$tablealias . "." . $field["Field"]]["type"];
                            if ($fies[$tablealias . "." . $field["Field"]]["type"] === "reference")
                            {
                                $tmpfields[$kfield]["refersTo"] = $fies[$tablealias . "." . $field["Field"]]["refersTo"];
                            }
                        }
                        unset($tmpfields[$kfield]["Field"]);
                        unset($tmpfields[$kfield]["Type"]);
                        unset($tmpfields[$kfield]["Null"]);
                        unset($tmpfields[$kfield]["Key"]);
                        unset($tmpfields[$kfield]["Default"]);
                        unset($tmpfields[$kfield]["Extra"]);
                    }
                    else
                    {
                        unset($tmpfields[$kfield]);
                    }
                }

                $fields = array_merge($fields, $tmpfields);
            }
        }
        return array(
            'createable' => $customDescription["createable"],
            'updateable' => $customDescription["updateable"],
            'deleteable' => $customDescription["deleteable"],
            'retrieveable' => $customDescription["retrieveable"],
            "tables" => array(
                "list" => $tables,
                "structure" => $matches
            ),
            "fields" => $fields
        );
    }

    private function retrieveConf($array) {
        $structure = array(
            'name',
            'createable',
            'updateable',
            'deleteable',
            'retrieveable',
            'fields' => array(
                '*' => array(
                    'name',
                    'label',
                    'mandatory',
                    'type' => array(
                        'picklistValues' => array(
                            '*' => array('value')
                        ),
                        'defaultValue',
                        'name',
                        'refersTo'
                    ),
                    'nullable',
                    'editable',
                    'default'
                )
            ),
            'idPrefix'
        );
        foreach ($array as $k => $arr) {
            if (is_array($arr))
            {
                $array[$k] = $this->retrieveConf($arr);
            }
            if ($k === "vtiger_module")
            {
                $client = \WolfMVC\Registry::get("VTWS");
                $describe = $client->doDescribe($arr);
                $describe = $this->superfilterarray($describe, $structure);
                $describe = $this->reducetype($describe);
                $array[$k] = $describe;
            }
            elseif ($k === "custom_description")
            {
                $array[$k] = $this->analyzeCustomTables($arr);
            }
        }
        return $array;
    }

    private function arraycompress($array) {
        if (count($array) === 0)
            return null;
        foreach ($array as $k => $v) {
            if (is_array($v))
            {
                $rec = $this->arraycompress($v);
                if (count($v) === 1)
                {
                    return $rec;
                }
            }
        }
    }

    private function reducetype($array) {
        include (APP_PATH . "/application/configuration/tsnwapi/translate.php");
        if (isset($array["fields"]) && is_array($array["fields"]))
        {
            $array["fields"] = $this->reducetype($array["fields"]);
            return $array;
        }
        if (isset($array["type"]) && is_array($array["type"]))
        {
            switch ($array["type"]["name"]) {
                case 'autogenerated':
                    $array["type"] = "autogenerated";
                    break;
                case 'string':
                    $array["type"] = "string";
                    break;
                case 'text':
                    $array["type"] = "text";
                    break;
                case 'phone':
                    $array["type"] = "phone";
                    break;
                case 'date':
                    $array["type"] = "date";
                    break;
                case 'currency':
                    $array["type"] = "currency";
                    break;
                case 'double':
                    $array["type"] = "double";
                    break;
                case 'datetime':
                    $array["type"] = "datetime";
                    break;
                case 'owner':
                    $array["refersTo"] = $translate['Users'];
                    $array["type"] = "reference";
                    break;
                case 'reference':
                    $refersTo = $array["type"]["refersTo"];
                    foreach ($refersTo as $kr => $rr) {
                        if (isset($translate[$rr]) && $translate[$rr] !== "")
                        {
                            $refersTo[$kr] = $translate[$rr];
                        }
                        else
                        {
                            unset($refersTo[$kr]);
                        }
                    }
                    $array["refersTo"] = $refersTo;
                    $array["refersTo"] = join("|", $array["refersTo"]);
                    $array["type"] = "reference";
                    break;
                case 'picklist':

                    $array["picklistValues"] = array();
                    foreach ($array["type"]["picklistValues"] as $kvalue => $value) {
                        if (isset($value["value"]))
                            array_push($array["picklistValues"], $value["value"]);
                    }
                    $array["picklistValues"] = join("|", $array["picklistValues"]);
                    $array["defaultPicklistValue"] = $array["type"]["defaultValue"];
                    $array["type"] = "picklist";
                    break;
            }
        }
        foreach ($array as $k => $v) {
            if (is_array($v) && isset($v["type"]))
            {
                $array[$k] = $this->reducetype($v);
            }
        }
        return $array;
    }

    private function superfilterarray($array, $structure) {
        $ret = array();
        foreach ($structure as $k => $s) {
            if ($k === "*")
            {
                foreach ($array as $l => $v) {
                    $ret[$l] = $this->superfilterarray($array[$l], $s);
                }
                break;
            }
            else
            {
                if (is_array($s))
                {
                    if (isset($array[$k]))
                    {
                        $rec = $this->superfilterarray($array[$k], $s);

                        $ret[$k] = $rec;
                    }
                }
                elseif (isset($array[$s]))
                {
                    $ret[$s] = $array[$s];
                }
            }
        }
        return $ret;
    }

    public function request() {
        $view = $this->getActionView();
        header("Access-Control-Allow-Origin: *");
    }

    public function ws___respond() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        //paylaod
        if (isset($_REQUEST["data"]))
            $data = json_decode($_POST["data"], true);
        else
            $data = array();
//        get_header();
//        header("HTTP/1.0 200 OK");
        header("HTTP/1.0 404 Not Found");
//        header("HTTP/1.0 201 Created");
//        header("HTTP/1.0 204 No Content");
        $ret = new stdClass();
        $ret->method = $_SERVER["REQUEST_METHOD"];
        $ret->received_payload = $data;
        echo json_encode($ret, JSON_FORCE_OBJECT);
    }

    public function provaarray() {
        $array = array(1, 2, 3, 4 => array(
                "a" => 27, "b" => "rftg", "c" => array(
                    "a" => true
                ), 5
            ), 5, 6);
        echo "<pre>";
        echo $this->arrayToCode($array);
        echo "</pre>";
    }

    private function arrayToCode($array, $depth = 0) {
        $out = "array(\n";
        if (!is_array($array))
        {
            return null;
        }
        $count = count($array);
        $index = 1;
        foreach ($array as $key => $value) {
            for ($i = 0; $i < $depth; $i++) {
                $out .= "\t";
            }
            if (is_string($key))
            {
                $k = "'" . $key . "' => ";
            }
            else
            {
                $k = "";
            }
            if (is_object($value))
            {
                $val = (array) $value;
                $out .= $k . $this->arrayToCode($val, $depth + 1);
            }
            else if (is_array($value))
            {
                $out .= $k . $this->arrayToCode($value, $depth + 1);
            }
            else if (is_string($value))
            {
                $out .= $k . "'" . $value . "'";
            }
            else if ($value === TRUE)
            {
                $out .= $k . "TRUE";
            }
            else if ($value === FALSE)
            {
                $out .= $k . "FALSE";
            }
            else
            {
                $out .= $k . $value;
            }
            if ($index < $count)
            {
                $out .= ",";
            }
            $out .= "\n";
            $index++;
        }
        for ($i = 0; $i < $depth; $i++) {
            $out .= "\t";
        }
        $out .= ")";
        return $out;
    }

}
