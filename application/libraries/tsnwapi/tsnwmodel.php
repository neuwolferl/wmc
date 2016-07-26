<?php

class Tsnwmodel extends WolfMVC\Base {

    public $version = "0.1";

    /**
     * @readwrite
     * @var array
     */
    protected $_resources = array();

    /**
     * @readwrite
     * @var array
     */
    protected $_names = array();
    /**
     * @readwrite
     * @var array
     */
    protected $_prefixes = array();

    /**
     * @readwrite
     * @var array
     */
    protected $_rels = array();

    public function __construct($options = array()) {
        if (!isset($options["setup"]))
        {
            include (APP_PATH . "/application/configuration/tsnwapi/model.php");
            $this->_resources = $resources;
            include (APP_PATH . "/application/configuration/tsnwapi/rels.php");
            $this->_names = $rels["NAMES"];
            unset($rels["NAMES"]);
            $this->_prefixes = $rels["PREFIXES"];
            unset($rels["PREFIXES"]);
            $this->_rels = $rels;
        }
        parent::__construct();
    }

    public function extractByPath($path) {
        $path = explode(".", $path);
        $node = $this->_resources;
        foreach ($path as $k => $p) {
            if (!isset($node[$p]))
            {
                return null;
            }
            $node = $node[$p];
        }
        return $node;
    }

    public function setup() {
        include (APP_PATH . "/application/configuration/tsnwapi/basevaluemap.php");
        $resources = $this->retrieveConf($resources);
        $rels = array();
        $this->abstractrelations($resources, $rels);
//        echo "<pre>";
//        print_r($resources);
//        echo "</pre>";
        $out = '$resources = ' . $this->arrayToCode($resources, 1);
        $relsout = '$rels = ' . $this->arrayToCode($rels, 1);
        $success1 = file_put_contents(APP_PATH . "/application/configuration/tsnwapi/model.php", "<?php\n" . $out . ";\n?>");
        $success2 = file_put_contents(APP_PATH . "/application/configuration/tsnwapi/rels.php", "<?php\n" . $relsout . ";\n?>");
        if ($success1 && $success2)
            return $out;
        else
            return "Error in saving a file";
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
                        if ($tmpfields[$kfield]["Key"] === "PRI")
                        {
                            $tabid = $tmpfields[$kfield]["name"];
                        }
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
            'deleted' => $customDescription["deleted"],
            'database' => $customDescription["database"],
            'name' => $customDescription["name"],
            'idField' => (isset($tabid) ? $tabid : ""),
            "tables" => array(
                "list" => $tables,
                "structure" => $matches
            ),
            "tablesforquery" => $tabs,
            "fields" => $fields
        );
    }

    private function overrideArray($array, $override) {
        foreach ($override as $k => $v) {
            if (!isset($array[$k]))
            {
                continue;
            }
            else if (is_array($v) && !is_array($array[$k]))
            {
                continue;
            }
            else if (is_array($v) && is_array($array[$k]))
            {
                $array[$k] = $this->overrideArray($array[$k], $v);
            }
            else
            {
                $array[$k] = $v;
            }
        }
        return $array;
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
                if (isset($describe["name"]))
                {
                    $describe["name"] = strtolower($describe["name"]);
                }
                $array[$k] = $describe;
            }
            elseif ($k === "custom_description")
            {
                $array[$k] = $this->analyzeCustomTables($arr);
            }
        }
        $override = array(
            "bpm" => array(
                "eco" => array(
                    "invoices" => array(
                        "vtiger_module" => array(
                            "updateable" => FALSE
                        )
                    ),
                ),
                "com" => array(
                    "quotes" => array(
                        "vtiger_module" => array(
                            "updateable" => FALSE
                        )
                    ),
                    "salesorders" => array(
                        "vtiger_module" => array(
                            "updateable" => FALSE
                        )
                    )
                )
            )
        );
        $array = $this->overrideArray($array, $override);
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
                case 'email':
                    $array["type"] = "email";
                    break;
                case 'password':
                    $array["type"] = "password";
                    break;
                case 'boolean':
                    $array["type"] = "boolean";
                    break;
                case 'url':
                    $array["type"] = "url";
                    break;
                case 'integer':
                    $array["type"] = "integer";
                    break;
                case 'time':
                    $array["type"] = "time";
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
                        {
                            $value["value"] = str_ireplace("'", "\'", $value["value"]);
                            array_push($array["picklistValues"], $value["value"]);
                        }
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

    public function abstractrelations($node, &$rels, $prefix = "") {
        if (!isset($rels["NAMES"]))
        {
            $rels["NAMES"] = array();
        }
        if (!isset($rels["PREFIXES"]))
        {
            $rels["PREFIXES"] = array();
        }
        if (isset($node["fields"]))
        {
            if (!isset($rels["NAMES"][$node["name"]]))
            {
                $rels["NAMES"][$node["name"]] = $prefix;
            }
            if (!isset($rels["PREFIXES"][$prefix]))
            {
                $rels["PREFIXES"][$prefix] = isset($node["idPrefix"])?$node["idPrefix"]:"";
            }
            if (!is_array($node["fields"]))
            {
                return $node["fields"];
            }
            if (!isset($rels[$prefix]))
                $rels[$prefix] = array("direct" => array(), "inverse" => array());
            foreach ($node["fields"] as $k => $v) {
                if (isset($v["type"]) && $v["type"] === "reference" && isset($v["refersTo"]))
                {
                    $refs = explode("|", $v["refersTo"]);
                    foreach ($refs as $kkk => $rrr) {
                        if ($rrr === "")
                        {
                            continue;
                        }
                        $rels[$prefix]["direct"][] = array("refs" => $rrr, "via" => $v["name"]);
                        if (!isset($rels[$rrr]))
                            $rels[$rrr] = array("direct" => array(), "inverse" => array());
                        $rels[$rrr]["inverse"][] = array("refed" => $prefix, "by" => $v["name"]);
                    }
                }
            }
        }
        else
        {
            foreach ($node as $k => $v) {
                if (in_array($k, array("vtiger_module", "custom_description")))
                    $this->abstractrelations($v, $rels, $prefix);
                elseif ($prefix === "")
                    $this->abstractrelations($v, $rels, $k);
                else
                    $this->abstractrelations($v, $rels, $prefix . "." . $k);
            }
        }
    }

}
?>

