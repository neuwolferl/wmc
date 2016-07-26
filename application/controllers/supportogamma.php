<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

use WolfMVC\Controller as Controller;
use WolfMVC\Registry as Registry;
use WolfMVC\RequestMethods as RequestMethods;
use WolfMVC\Template\Component\Formcomponent as FC;

class SupportoGamma extends Controller {

    protected $_conf;

    public function __construct($options = array()) {
        parent::__construct($options);
        $database = \WolfMVC\Registry::get("database_vtiger");
        $database->connect();
    }

    public function script_including() {

        $reg = Registry::get("module_incassi");
        $this->_conf = parse_ini_file($reg["conf"]);
    }

    public function index() {

    }

    public function ws___elencofatture() {
        $this->setJsonWS();
        $link = $this->instantiateMysqli("vtiger");
    }

}
