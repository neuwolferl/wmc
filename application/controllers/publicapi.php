<?php

use WolfMVC\Controller as Controller;

class Publicapi extends Controller {

    public function __construct($options = array()) {

        parent::__construct($options);
    }

    public function script_including() {
        
    }

    public function index() {
        $vtws = \WolfMVC\Registry::get("VTWS");
//        $sql = "SELECT * FROM Accounts WHERE employees = 6 AND accounttype = 'New Business PMI' "
//                . "LIMIT 0,100;";
//        $acc = $vtws->doQuery($sql);
        $acc = \VT\Accounts::find("ALL", array(
            "conditions" => array("accounttype = ? AND accountname LIKE '%?%'", 'New Business PMI','C')
            ));
        echo "<pre>";
        print_r($acc);
        echo "</pre>"; 
    }

    public function testapi() {

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

    public function setup() {
        $this->loadScript("utils.js");
        $this->loadScript("jquery.js");
        $this->loadScript("angular.min.js");
        $this->loadScript("core/angdad.js");
        $this->loadScript("ngbootstrap.min.js");
        $this->loadScript("ng-ui-bootstrap-tpls-0.2.0.js");
        $this->loadScript("ngsortable.js");
        $this->loadScript("core/common/common-0.1.js");
        $this->loadScript("core/common/commonInclude.js");
        $this->loadScript("lodash.js");
        $this->loadScript("core/datamodule/data-0.5.js");
        $this->loadScript("core/smartButton/smartButton-0.1-min.js");
        $this->loadScript("restangular.js");
        $this->loadScript("angular-animate.min.js");
        $this->loadScript("component/treeMenu/treeMenu-0.1.js");
        $this->loadScript("component/treeMenu/treeMenu.css");
        $this->loadScript("component/multiselect/multiselect-0.1.js");
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("PUBLICAPI" => "publicapi", "Setup" => "last")));
        $view = $this->getActionView();
        $view->set("root", '$root');
        $view->set("index", '$index');
        $view->set("parent", '$parent');
        $view->set("modelValue", '$modelValue');
    }

    public function ws___setup() {
        $this->setJsonWS();
        $config = array(
            "services" => array(
                "describe_active_record" => SITE_PATH . "publicapi/describe_active_record.ws",
                "describe_services" => SITE_PATH . "publicapi/describe_services.ws",
                "describe_services_in_db" => SITE_PATH . "publicapi/describe_services_in_db.ws",
            )
        );

        echo json_encode($config);
        return;
    }

    public function ws___json() {
        $this->setJsonWS();
        $parameters = $this->_parameters;


        $api = new WolfMVC\WS\Apiserver(array(
            "rawParameters" => $parameters,
            "baseurl" => ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]) ? "https://" : "http://") .
            $_SERVER["SERVER_NAME"] .
            SITE_PATH . $this->nameofthiscontroller() . "/" . $this->nameofthisaction() . "/{{pars}}.ws"
        ));
        $api->catch_request();
        $api->getRequest()->adjust_parameters();
        \ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );

//        echo "<pre>";
        try {
            $result = $api->dispatch();
            if (!is_array($result))
            {
                throw new \Exception("Unkwnown error", 500, NULL);
            }
//            $out = array("result" => $result, "count" => count($result));
            $out = $result["result"];
            foreach ($result["headers"] as $k => $h)
                header($k . ":" . $h);
            echo json_encode($out);
        } catch (\Exception $e) {
            $code = (int) $e->getCode();
            if ($code > 0)
            {
                $this->_setResponseStatus($code);
            }
            else
            {
                $this->_setResponseStatus(500);
            }
            echo json_encode($e->getMessage());
        }
//        echo "</pre>";
    }

    public function ws___describe_services_in_db() {
        $this->setJsonWS();
        \ActiveRecord\Config::initialize(
                function($cfg) {
            $cfg->set_model_directory('../application/configuration/models');
            $cfg->set_connections_from_system(array("tsnw6" => "tsnw6"));
        }
        );
        $resources = AR\Tsnwresource::all();
        $out = array();
        foreach ($resources as $r) {
            $out[] = $r->attributes(true);
        }
        echo json_encode($out);
        return;
    }

    public function ws___describe_services() {
        $this->setJsonWS();

        $dirpath = APP_PATH . "/application/services";
        $linear = \WolfMVC\FsMethods::pathLinearize($dirpath);
        $found_all = array();
        $look_up = array(
            array(T_NAMESPACE, T_WHITESPACE, T_STRING, T_NS_SEPARATOR, T_STRING),
            array(T_CLASS, T_WHITESPACE, T_STRING),
            array(T_EXTENDS, T_WHITESPACE, T_NS_SEPARATOR, T_STRING, T_NS_SEPARATOR, T_STRING, T_NS_SEPARATOR, T_STRING)
        );
        $look_up_static_lin_props = array(array(T_STATIC, T_WHITESPACE, T_VARIABLE));

        $stop = true;
        foreach ($linear as $k => $f) {
            $tokens = token_get_all(file_get_contents($f[1] . "/" . $f[0]));
            if (!is_array($tokens))
            {
                continue;
            }
            $start = 0;
            $end = 0;
            $found = WolfMVC\FileMethods::searchPhpCode($tokens, $look_up, $start, $end, true);

            if (
                    count($found) === count($look_up) && isset($found[0][2][0]) && $found[0][2][0] === "WolfMVC" && isset($found[0][4][0]) && $found[0][4][0] === "WS" && isset($found[2][3][0]) && $found[2][3][0] === "WolfMVC" && isset($found[2][5][0]) && $found[2][5][0] === "WS" && isset($found[2][7][0]) && $found[2][7][0] === "Apiresource"
            )
            {
                $dummy = $found[0][4][1];

                $close_namespace = WolfMVC\FileMethods::searchPhpCode($tokens, array(array("{")), $found[0][4][1], $dummy, true);
                $close_namespace = $close_namespace[0][0][1];
                $namespace = "";
                for ($i = $found[0][2][1]; $i < $close_namespace; $i++) {
                    $namespace .= (is_string($tokens[$i]) ? $tokens[$i] : $tokens[$i][1]);
                }
                $namespace = trim($namespace);
                $start = $end;
                $static_props = WolfMVC\FileMethods::searchPhpCode($tokens, $look_up_static_lin_props, $start, $end);

                $ar_classname = "\\$namespace\\" . $found[1][2][0];
                foreach ($static_props as $si => $sp) {
                    $variablename = str_ireplace('$', '', $sp[2]);
                    if (!isset($ar_classname::$$variablename))
                    {
                        $static_props[$variablename] = $ar_classname::getP($variablename);
                    }
                    else
                        $static_props[$variablename] = $ar_classname::$$variablename;
                    unset($static_props[$si]);
                }
                if ($f[0] === strtolower($found[1][2][0]) . ".php")
                {
                    $found_all[] = array("path" => $f[1], "file" => $f[0], "class" => $found[1][2][0], "static_props" => $static_props);
                }
                continue;
            }
        }
        echo json_encode($found_all);
        return;
        echo "Elenco risorse disponibili<br>";
        $dirpath = $ar_cfg->get_model_directory();
        $modelname = "AR\Vtpotential";
        $table = $modelname::Table();
        $model = $modelname::first();
//        $columns = $table->columns;
        $out = $model->attributes(true);
        $out["cf"] = $model->cf->attributes(true);
        $out["ent"] = $model->ent->attributes(true);
        $out["account"] = $model->account->attributes(true);
        echo json_encode($out);
//        echo json_encode($table);
//        echo json_encode($columns);
    }

    public function ws___describe_active_record() {
        $this->setJsonWS();

        $ar_cfg = \ActiveRecord\Config::initialize(
                        \WolfMVC\Registry::get("activerecord_initializer")
        );
        $dirpath = $ar_cfg->get_model_directory();
        $linear = \WolfMVC\FsMethods::pathLinearize($dirpath);
//        echo token_name(315);
        $found_all = array();
        $look_up = array(
            array(T_NAMESPACE, T_WHITESPACE, T_STRING),
            array(T_CLASS, T_WHITESPACE, T_STRING),
            array(T_EXTENDS, T_WHITESPACE, T_NS_SEPARATOR, T_STRING, T_NS_SEPARATOR, T_STRING)
        );
        $look_up_static_lin_props = array(array(T_STATIC, T_WHITESPACE, T_VARIABLE));

        $stop = true;
        foreach ($linear as $k => $f) {
            $tokens = token_get_all(file_get_contents($f[1] . "/" . $f[0]));
            if (!is_array($tokens))
            {
                continue;
            }
            $start = 0;
            $end = 0;
            $found = WolfMVC\FileMethods::searchPhpCode($tokens, $look_up, $start, $end);

            if (
                    count($found) === count($look_up) && isset($found[0][2]) && $found[0][2] === "AR" && isset($found[2][3]) && $found[2][3] === "ActiveRecord" && isset($found[2][5]) && $found[2][5] === "Model"
            )
            {
                $start = $end;
                $static_props = WolfMVC\FileMethods::searchPhpCode($tokens, $look_up_static_lin_props, $start, $end);

                $ar_classname = "\AR\\" . $found[1][2];
                foreach ($static_props as $si => $sp) {
                    $variablename = str_ireplace('$', '', $sp[2]);
                    if (!isset($ar_classname::$$variablename))
                        $static_props[$variablename] = null;
                    else
                        $static_props[$variablename] = $ar_classname::$$variablename;
                    unset($static_props[$si]);
                }
                $other_static_props = WolfMVC\FileMethods::getStaticPropertiesRecursive($ar_classname);
                $tablename = $other_static_props["table_name"];
                if (is_array($tablename))
                {
                    $tablename = $tablename[0];
                }
                $table = $ar_classname::table();

                $columns = $table->columns;
                $fields = array();
                foreach ($columns as $colname => $col) {
                    $fields[] = array(
                        "fieldname" => $colname,
                        "type" => $col->raw_type,
                        "default" => $col->default,
                        "pk" => $col->pk,
                        "auto_increment" => $col->auto_increment,
                        "wrap_in" => null,
                        "wrap_out" => null,
                        "length" => $col->length,
                        "nullable" => $col->nullable,
                        "mandatory" => false
                    );
                }

                if ($f[0] === $found[1][2] . ".php")
                {
                    $found_all[] = array("fields" => $fields, "path" => $f[1], "file" => $f[0], "class" => $found[1][2], "static_props" => $other_static_props,
                        "table" => $table, "columns" => $columns, "pre" => "AR\\");
                }
                continue;
            }
        }
        echo json_encode($found_all);
        return;
        echo "Elenco risorse disponibili<br>";
        $dirpath = $ar_cfg->get_model_directory();
        $modelname = "AR\Vtpotential";
        $table = $modelname::Table();
        $model = $modelname::first();
//        $columns = $table->columns;
        $out = $model->attributes(true);
        $out["cf"] = $model->cf->attributes(true);
        $out["ent"] = $model->ent->attributes(true);
        $out["account"] = $model->account->attributes(true);
        echo json_encode($out);
//        echo json_encode($table);
//        echo json_encode($columns);
    }

    public function ws___describe_vtiger() {
        $this->setJsonWS();
        $vtws = \WolfMVC\Registry::get("VTWS");
        print_r($vtws->doDescribe("Accounts"));
    }

}
