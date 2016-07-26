<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

use WolfMVC\Controller as Controller;
use WolfMVC\Registry as Registry;
use WolfMVC\RequestMethods as RequestMethods;
use WolfMVC\Template\Component\Formcomponent as FC;

class Progetti extends Controller {

    protected $_conf;

    public function __construct($options = array()) {
        parent::__construct($options);
        $database = \WolfMVC\Registry::get("database_vtiger");
//        $database->connect();
    }

    /**
     * @once
     */
    public function checkVT() {
        $session = Registry::get("session");
        if (!$session->get("vtiger_logged_user_id"))
        {
            header("Location: " . filter_var(SITE_PATH . "error/missingVT", FILTER_SANITIZE_URL));
        }
    }

    /**
     * @protected
     */
    public function script_including() {
        $reg = Registry::get("module_incassi");
        $this->_conf = parse_ini_file($reg["conf"]);
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/utils.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/jquery.js\"></script>";
        
//        $this->_system_js_including .="<link rel=\"stylesheet\" href=\"" . SITE_PATH . "css/bs/bootstrap.min.css\">";


        $view = $this->getLayoutView();
        $view->set("moduleName", "PROGETTI");
    }

    /**
     * @before checkVT
     */
    public function index() {
        $view = $this->getActionView();
        $view->set("action_1", "Supervisione");
        $view->set("action_2", "Gestione");
        $view->set("path_action_1", SITE_PATH . \WolfMVC\Registry::get("router")->getController() . "/supervisione");
        $view->set("path_action_2", SITE_PATH . \WolfMVC\Registry::get("router")->getController() . "/gestione");
//        $view->set("gest_path", SITE_PATH . \WolfMVC\Registry::get("router")->getController() . "/gest");
//        $view->set("amm_path", SITE_PATH . \WolfMVC\Registry::get("router")->getController() . "/amm");
//        $view->set("bankinsert_path", SITE_PATH . \WolfMVC\Registry::get("router")->getController() . "/amm");
    }

    /**
     * @before checkVT
     */
    public function supervisione() {
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("PROGETTI" => "progetti", "Supervisione" => "last")));
        $view = $this->getActionView();

//        $view->set("action_0", "Indietro");
//        $view->set("path_action_0", $this->getBackTrack());
        $view->set("action_1", "Assegnazione progetti");
        $view->set("path_action_1", SITE_PATH . \WolfMVC\Registry::get("router")->getController() . "/progetti_da_assegnare/1");
        $view->set("action_3", "Riassegnazione progetti non avviati");
        $view->set("path_action_3", SITE_PATH . \WolfMVC\Registry::get("router")->getController() . "/progetti_da_riassegnare/1");
        $view->set("action_2", "Richieste di conferma");
        $view->set("path_action_2", SITE_PATH . \WolfMVC\Registry::get("router")->getController() . "/progetti_da_confermare/1");
//        $view->set("gest_path", SITE_PATH . \WolfMVC\Registry::get("router")->getController() . "/gest");
//        $view->set("amm_path", SITE_PATH . \WolfMVC\Registry::get("router")->getController() . "/amm");
//        $view->set("bankinsert_path", SITE_PATH . \WolfMVC\Registry::get("router")->getController() . "/amm");
    }

    /**
     * @before checkVT
     */
    public function gestione() {
        $view = $this->getActionView();
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("PROGETTI" => "progetti", "Gestione" => "last")));
//        $view->set("action_0", "Indietro");
//        $view->set("path_action_0", $this->getBackTrack());
        $view->set("action_1", "Avvio progetti");
        $view->set("path_action_1", SITE_PATH . \WolfMVC\Registry::get("router")->getController() . "/progetti_da_avviare/1");
        $view->set("action_2", "I miei progetti");
        $view->set("path_action_2", SITE_PATH . \WolfMVC\Registry::get("router")->getController() . "/i_miei_progetti/1");
    }

    /**
     * @before checkVT
     */
    public function progetti_da_assegnare() {
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("PROGETTI" => "progetti", "Supervisione" => "progetti/supervisione", "Progetti da assegnare" => "last")));
        $view = $this->getActionView();
//        $view->set("action_0", "Indietro");
//        $view->set("path_action_0", $this->getBackTrack());
        $view->set('index', '$index');
        $view->set("angular_dollar_index", '$index');
        $view->set("expr_link", 'ListoneCtrl.avvia(datum["Pk_project"])');
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/angular.min.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ngbootstrap.min.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ng-ui-bootstrap-tpls-0.2.0.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/progetti/progettidaassegnare.js\"></script>";
        $this->_system_js_including .="<link rel=\"stylesheet\" href=\"" . SITE_PATH . "css/progetti/progetti_da_assegnare.css\">";


        return;
    }

    /**
     * @before checkVT
     */
    public function i_miei_progetti() {
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("PROGETTI" => "progetti", "Gestione" => "progetti/gestione", "I miei progetti" => "last")));
        $view = $this->getActionView();
        $view->set("action_0", "Indietro");
        $view->set("path_action_0", $this->getBackTrack());
        $view->set("root", '$root');
        $view->set("angular_dollar_index", '$index');
        $view->set("expr_link", 'ListoneCtrl.avvia(datum["Pk_project"])');
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/angular.min.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ngbootstrap.min.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ng-ui-bootstrap-tpls-0.2.0.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/progetti/imieiprogetti.js\"></script>";
        $this->_system_js_including .="<link rel=\"stylesheet\" href=\"" . SITE_PATH . "css/progetti/i_miei_progetti.css\">";


        return;
    }

    public function ws___i_miei_progetti() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);

//            $view = $this->getActionView();
        header('Content-type: application/json');
        $struct = self::getModelStructure("progetti");
        $session = Registry::get("session");
        $vtuser = $session->get("vtiger_logged_user_id");
//        echo $vtuser;
        $struct->addFilterToTable("project", "##projectstatus## = 'in progress'");
        $struct->addFilterToTable("project_ent", " AND ##smownerid## = '{$vtuser}'");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);

        try {
            $data = $smm->selectAll();
            
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
        $jsdata = array();

        $labels = array("Pk_project", "Assegnato a", "Situazione progetto", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Azienda cliente");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            $jsdata[$k] = array();
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
//        $jsdata[count($jsdata)] = array(count($data["data"]));
        echo (json_encode($jsdata));
//        echo json_encode($jsdata);
    }

    public function ws___progetti_da_assegnare() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        
//            $view = $this->getActionView();
        header('Content-type: application/json');
        $struct = self::getModelStructure("progetti");
        $struct->addFilterToTable("project", "##projectstatus## = 'prospecting' OR ##projectstatus## = 'initiated'");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);
        
        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
        $jsdata = array();

        $labels = array("Pk_project", "Numero progetto", "Stato progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Azienda cliente");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            $jsdata[$k] = array();
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
//        $jsdata[count($jsdata)] = array(count($data["data"]));
        echo (json_encode($jsdata));
//        echo json_encode($jsdata);
    }

    public function ws___progetti_da_confermare() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);

//            $view = $this->getActionView();
        header('Content-type: application/json');
        $struct = self::getModelStructure("progetti");
        $struct->addFilterToTable("projectcf", "##cf_693## = '1'");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);

        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
        $jsdata = array();

        $labels = array("Pk_project", "Numero progetto", "Nome progetto", "Stato progetto", "Tipo progetto", "Data apertura progetto", "Azienda cliente");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            $jsdata[$k] = array();
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
//        $jsdata[count($jsdata)] = array(count($data["data"]));
        echo (json_encode($jsdata));
//        echo json_encode($jsdata);
    }

    /**
     * @before checkVT
     */
    public function progetti_da_riassegnare() {
        $view = $this->getActionView();
        $view->set("action_0", "Indietro");
        $view->set("path_action_0", $this->getBackTrack());

        $struct = self::getModelStructure("progetti");
        $smm = new WolfMVC\Smm();
        $smm->setStructure($struct);
        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
//        echo "<pre>";
//        print_r($data);
//        echo "</pre>";
        return;
    }

    /**
     * @before checkVT
     */
    public function progetti_da_confermare() {
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("PROGETTI" => "progetti", "Supervisione" => "progetti/supervisione", "Progetti da confermare" => "last")));
        $view = $this->getActionView();
        $view->set("action_0", "Indietro");
        $view->set("path_action_0", $this->getBackTrack());
        $view->set("angular_dollar_index", '$index');
        $view->set("expr_link", 'ListoneCtrl.avvia(datum["Pk_project"])');

        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/angular.min.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ngbootstrap.min.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ng-ui-bootstrap-tpls-0.2.0.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/progetti/progettidaconfermare.js\"></script>";
        $this->_system_js_including .="<link rel=\"stylesheet\" href=\"" . SITE_PATH . "css/progetti/progetti_da_confermare.css\">";


        return;
    }

    /**
     * @before checkVT
     */
    public function assegnazione() {
        if (!isset($this->_parameters[0]))
        {
            throw new \Exception("Devi selezionare un progetto", 0, NULL);
        }
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("PROGETTI" => "progetti", "Supervisione" => "progetti/supervisione", "Progetti da assegnare" => "progetti/progetti_da_assegnare", "Assegnazione" => "last")));
        $projectid = $this->_parameters[0];
        $view = $this->getActionView();
        $view->set("action_0", "Indietro");
        $view->set("path_action_0", $this->getBackTrack());
        $view->set("index", '$index');
        $view->set("parent", '$parent');
        $view->set("root", '$root');
        $view->set("fieldIsString", "field.type === 'string'");
        $view->set("fieldIsDate", "field.type === 'date'");
        $view->set("fieldIsCurrency", "field.type === 'currency'");
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/angular.min.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/progetti/assegnazioneprogetto.js\"></script>";
        $this->_system_js_including .="<link rel=\"stylesheet\" href=\"" . SITE_PATH . "css/progetti/assegnazione.css\">";
    }

    /**
     * @before checkVT
     */
    public function assegnazione2() {
        if (!isset($this->_parameters[0]))
        {
            throw new \Exception("Devi selezionare un progetto", 0, NULL);
        }
        $projectid = $this->_parameters[0];
        $view = $this->getActionView();

        $view->set("action_0", "Indietro");
        $view->set("path_action_0", $this->getBackTrack());
        $struct = self::getModelStructure("progetti_ext");
        $struct->addFilterToTable("project", "##projectid## = '{$projectid}'");
        $smm = new WolfMVC\Smm();
        $smm->setStructure($struct);
        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }

        $structPrType = self::getModelStructure("tipoprogetto");

        $smmPrType = new WolfMVC\Smm();
        $smmPrType->setStructure($structPrType);
        try {
            $prType = $smmPrType->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }

        $structCons = self::getModelStructure("consulenti");

        $smmCons = new WolfMVC\Smm();
        $smmCons->setStructure($structCons);
        try {
            $consulenti = $smmCons->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }

        echo "<!---<pre>";
        print_r($data);
        echo "</pre>--->";
        $dettagliProgetto = new WolfMVC\Template\Component\Prospectus();
        $dettagliProgetto->setComponents(array(
            "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Assegnato a"
        ));
        $dettagliProgetto->setData($data["data"][0]);
        $dataAperturaProgetto = $data["data"][0]["Data apertura progetto"];
        $prId = $data["data"][0]["Pk_project"];
        $dettagliProgetto->setArrangement(array(
            "{{Numero progetto:__label__}}&nbsp;&nbsp;{{Numero progetto}}&nbsp;&nbsp;&nbsp;{{Nome progetto:__label__}}&nbsp;&nbsp;{{Nome progetto}}",
            "Tipologia progetto:&nbsp;&nbsp;{{Tipo progetto}}",
            "Data apertura prevista:&nbsp;&nbsp;{{Data apertura progetto}}",
            "{{Assegnato a:__label__}}&nbsp;&nbsp;{{Assegnato a}}"
        ));
        $view->set("dettagliProgetto", $dettagliProgetto->make(""));
        $dettagliCliente = new WolfMVC\Template\Component\Prospectus();
        $dettagliCliente->setComponents(array(
            "Numero cliente", "Azienda cliente", "Telefono cliente", "Ev.le altro telefono", "Email",
            "Fax", "Dipendenti", "Partita Iva", "Vendita gestita da", "Contatto gestito da", "Analista"
        ));
        $dettagliCliente->setData($data["data"][0]);

        $dettagliCliente->setArrangement(array(
            "{{Numero cliente:__label__}}&nbsp;&nbsp;{{Numero cliente}}&nbsp;&nbsp;&nbsp;{{Azienda cliente:__label__}}&nbsp;&nbsp;{{Azienda cliente}}",
            "{{Telefono cliente:__label__}}&nbsp;&nbsp;{{Telefono cliente}}&nbsp;&nbsp;&nbsp;{{Ev.le altro telefono:__label__}}&nbsp;&nbsp;{{Ev.le altro telefono}}",
            "{{Email:__label__}}&nbsp;&nbsp;{{Email}}",
            "{{Fax:__label__}}&nbsp;&nbsp;{{Fax}}",
            "{{Dipendenti:__label__}}&nbsp;&nbsp;{{Dipendenti}}&nbsp;&nbsp;&nbsp;{{Partita Iva:__label__}}&nbsp;&nbsp;{{Partita Iva}}",
            "Analista:&nbsp;&nbsp;{{Analista}}",
            "Venditore:&nbsp;&nbsp;{{Vendita gestita da}}"
        ));
        $view->set("dettagliCliente", $dettagliCliente->make(""));

        $dettagliContattoData = array();
        $dettagliContattoDataTmp = array();
        $dettagliContattoDataTmp['Contatto'] = explode(",", $data["data"][0]["Contatto"]);
        $dettagliContattoDataTmp['Tel'] = explode(",", $data["data"][0]["tel"]);
        $dettagliContattoDataTmp['Cell'] = explode(",", $data["data"][0]["cell"]);
        $dettagliContattoOut = "";
        foreach ($dettagliContattoDataTmp["Contatto"] as $k => $v) {
            $dettagliContattoData[$k] = array();
            $txt = $dettagliContattoDataTmp['Contatto'][$k];
            $txt = explode(": ", $txt);
            $txt = str_ireplace("}", "", $txt[1]);
            $dettagliContattoData[$k]["Contatto"] = $txt;
            $txt = $dettagliContattoDataTmp['Tel'][$k];
            $txt = explode(": ", $txt);
            $txt = str_ireplace("}", "", $txt[1]);
            $dettagliContattoData[$k]["Tel"] = $txt;
            $txt = $dettagliContattoDataTmp['Cell'][$k];
            $txt = explode(": ", $txt);
            $txt = str_ireplace("}", "", $txt[1]);
            $dettagliContattoData[$k]["Cell"] = $txt;
            $dettagliContatto = new WolfMVC\Template\Component\Prospectus();
            $dettagliContatto->setComponents(array(
                "Contatto", "Tel", "Cell"
            ));
            $dettagliContatto->setData($dettagliContattoData[$k]);
            $dettagliContatto->setArrangement(array(
                "{{Contatto:__label__}}&nbsp;&nbsp;{{Contatto}}",
                "{{Tel:__label__}}&nbsp;&nbsp;{{Tel}}",
                "{{Cell:__label__}}&nbsp;&nbsp;{{Cell}}"
            ));
            $dettagliContattoOut .= $dettagliContatto->make("");

            $form = "";
            $form .= "<form action=\"" . SITE_PATH . $this->nameofthiscontroller() . "/assegna\" method=\"POST\">";
            $form .= "<input type=\"hidden\" name=\"projectid\" value=\"{$prId}\">";

            $form .= "<label for=\"nomeProgetto\">Nuovo nome progetto:</label>&nbsp;&nbsp;"
                    . "<input size=\"50\" type=\"text\" name=\"nomeProgetto\" id=\"nomeProgetto\" "
                    . "value=\"{$data["data"][0]["Nome progetto"]}\"><br><br>";
            $form .= "<label for=\"tipoProgetto\">Nuova tipologia progetto:</label>&nbsp;&nbsp;"
                    . "<select name=\"tipoProgetto\" id=\"tipoProgetto\"> ";
            foreach ($prType["data"] as $k => $v) {
                $form .= "<option value=\"{$v['type']}\">{$v['type']}</option>";
            }
            $form .="</select><br><br>";
            $form .= "<label for=\"consulente\">Nuovo assegnatario:</label>&nbsp;&nbsp;"
                    . "<select name=\"consulente\" id=\"consulente\"> ";
            foreach ($consulenti["data"] as $consk => $cons) {
                $form .= "<option value=\"{$cons['Pk_consulenti']}\">{$cons['Consulente']}</option>";
            }
            $form .="</select><br><br>";
            $form .= "<label for=\"data\">Nuova data avvio:</label>&nbsp;&nbsp;"
                    . "<input type=\"date\" name=\"data\" id=\"data\" value=\"{$dataAperturaProgetto}\"><br><br>";
            $form .="<button type=\"submit\">Applica modifiche e assegna</button>";
            $form .= "</form>";


            $view->set("form", $form);
        }



        $view->set("dettagliContatto", $dettagliContattoOut);
    }

//    public function assegna() {
//        $lay = $this->getLayoutView();
//        $lay->set("breadCrumb",  $this->breadCrumb(array("PROGETTI"=>"progetti","Gestione" => "progetti/gestione", "Progetti da assegnare" => "progetti/progetti_da_assegnare", "Assegnazione" =>"last")));
//        $struct = self::getModelStructure("progetti");
//        $database = Registry::get("database_" . $struct->getDefaultDb());
//        $link = new \mysqli($database->getHost(), $database->getUsername(), $database->getPassword(), $database->getSchema());
//        $sql = "UPDATE vtiger_project " .
//                "SET " .
//                "projectname = '{$_POST['nomeProgetto']}', " .
//                "projecttype = '{$_POST['tipoProgetto']}', " .
//                "startdate = '{$_POST['data']}' " .
//                "WHERE projectid = {$_POST["projectid"]}";
//        $result = $link->query($sql);
//
//        $sql = "UPDATE vtiger_crmentity " .
//                "SET " .
//                "smownerid = '{$_POST['consulente']}' " .
//                "WHERE projectid = {$_POST["projectid"]}";
//        $result = $link->query($sql);
//        header("location: " . SITE_PATH . $this->nameofthiscontroller() . "/progetti_da_assegnare");
//    }

    /**
     * @before checkVT
     */
    public function avvia() {
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("PROGETTI" => "progetti", "Gestione" => "progetti/gestione", "Progetti da avviare" => "progetti/progetti_da_avviare", "Avvio progetto" => "last")));
        if (!isset($this->_parameters[0]))
        {
            throw new \Exception("Devi selezionare un progetto", 0, NULL);
        }
        $projectid = $this->_parameters[0];
        $view = $this->getActionView();
        $view->set("action_0", "Indietro");
        $view->set("path_action_0", $this->getBackTrack());
        $view->set("index", '$index');
        $view->set("parent", '$parent');
        $view->set("root", '$root');
        $view->set("fieldIsString", "field.type === 'string'");
        $view->set("fieldIsDate", "field.type === 'date'");
        $view->set("fieldIsCurrency", "field.type === 'currency'");
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/angular.min.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/progetti/avvioprogetto.js\"></script>";
        $this->_system_js_including .="<link rel=\"stylesheet\" href=\"" . SITE_PATH . "css/progetti/avvia.css\">";
    }

    /**
     * @before checkVT
     */
    public function avanzamento() {
        if (!isset($this->_parameters[0]))
        {
            throw new \Exception("Devi selezionare un progetto", 0, NULL);
        }
        $projectid = $this->_parameters[0];
        //devo controllare che l'utente possa trovarsi qui:
        $client = \WolfMVC\Registry::get("VTWS");
        $record = $client->doRetrieve("31x" . $projectid);
        if ($record && $record["cf_693"] === '0' && $record["projectstatus"] === 'in progress')
        {

            $lay = $this->getLayoutView();
            $lay->set("breadCrumb", $this->breadCrumb(array("PROGETTI" => "progetti", "Gestione" => "progetti/gestione", "I miei progetti" => "progetti/i_miei_progetti/", "Avanzamento progetto" => "last")));

            $view = $this->getActionView();
            $view->set("index", '$index');
            $view->set("parent", '$parent');
            $view->set("root", '$root');
            $view->set("fieldIsString", "field.type === 'string'");
            $view->set("fieldIsDate", "field.type === 'date'");
            $view->set("fieldIsCurrency", "field.type === 'currency'");
            $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/angular.min.js\"></script>";
            $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/progetti/avanzamentoprogetto.js\"></script>";
            $this->_system_js_including .="<link rel=\"stylesheet\" href=\"" . SITE_PATH . "css/progetti/avanzamento.css\">";
        }
        else
        {
            header("Location: " . SITE_PATH . "progetti/i_miei_progetti/");
        }
    }

    /**
     * @before checkVT
     */
    public function conferma() {
        if (!isset($this->_parameters[0]))
        {
            throw new \Exception("Devi selezionare un progetto", 0, NULL);
        }
        $projectid = $this->_parameters[0];
        //devo controllare che l'utente possa trovarsi qui:
        $client = \WolfMVC\Registry::get("VTWS");
        $record = $client->doRetrieve("31x" . $projectid);
        if ($record && $record["cf_693"] === '1')
        {

            $lay = $this->getLayoutView();
            $lay->set("breadCrumb", $this->breadCrumb(array("PROGETTI" => "progetti", "Supervisione" => "progetti/supervisione", "Progetti da confermare" => "progetti/progetti_da_confermare", "Conferma avanzamento" => "last")));

            $view = $this->getActionView();
            $view->set("index", '$index');
            $view->set("parent", '$parent');
            $view->set("root", '$root');
            $view->set("fieldIsString", "field.type === 'string'");
            $view->set("fieldIsDate", "field.type === 'date'");
            $view->set("fieldIsCurrency", "field.type === 'currency'");
            $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/angular.min.js\"></script>";
            $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/progetti/confermaprogetto.js\"></script>";
            $this->_system_js_including .="<link rel=\"stylesheet\" href=\"" . SITE_PATH . "css/progetti/conferma.css\">";
        }
        else
        {
            header("Location: " . SITE_PATH . "progetti/progetti_da_confermare/");
        }
    }

    public function ws___accountToSo() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        if (!isset($this->_parameters[0]))
        {
            throw new \Exception("Devi selezionare un progetto", 0, NULL);
        }
        $accountid = $this->_parameters[0];
        $sql = "SELECT " .
                "salesorderid as 'val', concat(salesorder_no, ' - ', subject ) as 'nome', " .
                "if (sostatus = 'Approved', '1', '0') as 'approved' " .
                "FROM " .
                "vtiger_salesorder " .
                "where accountid = '{$accountid}'";
        try {
            $db = \WolfMVC\Registry::get("database_vtiger");
            $link = new \mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
            if ($link->connect_errno)
            {
                echo "Failed to connect to MySQL: (" . $link->connect_errno . ") " . $link->connect_error;
                return;
            }
            else
            {
                if ($result = $link->query($sql))
                {
                    $data = array();
                    $fields = $result->fetch_fields();
                    $data["fields"] = array();
                    foreach ($fields as $key => $f) {
                        array_push($data["fields"], $f->name);
                    }
                    $data["data"] = $result->fetch_all(MYSQLI_ASSOC);
                }
                else
                {
                    echo "Failed to make query: (" . $link->error . ") ";
                }
            }
        } catch (\Exception $e) {
            print_r($e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
        $jsdata = array();
        $labels = $data["fields"]; //array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Stato progetto", "Assegnato a", "Azienda cliente", "Telefono cliente", "tel", "cell", "Contatto");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            $jsdata[$k] = array();
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
        echo (json_encode($jsdata));
    }

    public function ws___consulenti() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $sql = "SELECT a.id as val, " .
                "CONCAT_WS (' ',first_name,last_name) as nome, " .
//                "b.roleid, c.rolename ," .
                "IF (roleid = 'H12', '1', '0') as 'resp' " .
                "FROM vtiger_users a " .
                "LEFT join vtiger_user2role b on (a.id = b.userid) " .
                "LEFT JOIN vtiger_role c USING (roleid) " .
                "WHERE roleid IN ('H12','H13') OR a.first_name = 'Mirko' OR a.last_name ='Malgieri' OR a.last_name = 'Ferrari'";
        try {
            $db = \WolfMVC\Registry::get("database_vtiger");
            $link = new \mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
            if ($link->connect_errno)
            {
                echo "Failed to connect to MySQL: (" . $link->connect_errno . ") " . $link->connect_error;
                return;
            }
            else
            {
                if ($result = $link->query($sql))
                {
                    $data = array();
                    $fields = $result->fetch_fields();
                    $data["fields"] = array();
                    foreach ($fields as $key => $f) {
                        array_push($data["fields"], $f->name);
                    }
                    $data["data"] = $result->fetch_all(MYSQLI_ASSOC);
                }
                else
                {
                    echo "Failed to make query: (" . $link->error . ") ";
                }
            }
        } catch (\Exception $e) {
            print_r($e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
        $jsdata = array();
        $labels = $data["fields"]; //array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Stato progetto", "Assegnato a", "Azienda cliente", "Telefono cliente", "tel", "cell", "Contatto");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            $jsdata[$k] = array();
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
        echo (json_encode($jsdata));
    }

    public function ws___assegnazione() {
        if (!isset($this->_parameters[0]))
        {
            throw new \Exception("Devi selezionare un progetto", 0, NULL);
        }
        $projectid = $this->_parameters[0];

        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
//        $view = $this->getActionView();
        header('Content-type: application/json');
        $struct = self::getModelStructure("progetti_ext");
        $struct->addFilterToTable("project", "##projectid## = '$projectid'");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);

        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
        $jsdata = array();
        $labels = $data["fields"]; //array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Stato progetto", "Assegnato a", "Azienda cliente", "Telefono cliente", "tel", "cell", "Contatto");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            $jsdata[$k] = array();
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
        $struct = self::getModelStructure("progetti_so");
        $struct->addFilterToTable("project", "##projectid## = '$projectid'");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);

        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
//        $jsdata = array();
        $labels = $data["fields"]; //array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Stato progetto", "Assegnato a", "Azienda cliente", "Telefono cliente", "tel", "cell", "Contatto");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
        $struct = self::getModelStructure("tipoprogetto");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);

        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
        $jsdata["tipoprogetto"] = array();
        foreach ($data["data"] as $k => $v) {
            array_push($jsdata["tipoprogetto"], $v["type"]);
        }
        echo (json_encode($jsdata));
    }

    public function ws___conferma() {
        if (!isset($this->_parameters[0]))
        {
            throw new \Exception("Devi selezionare un progetto", 0, NULL);
        }
        $projectid = $this->_parameters[0];

        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
//        $view = $this->getActionView();
        header('Content-type: application/json');
        $struct = self::getModelStructure("progetti_ext");
        $struct->addFilterToTable("project", "##projectid## = '$projectid'");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);

        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
        $jsdata = array();
        $labels = $data["fields"]; //array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Stato progetto", "Assegnato a", "Azienda cliente", "Telefono cliente", "tel", "cell", "Contatto");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            $jsdata[$k] = array();
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
        $struct = self::getModelStructure("progetti_so");
        $struct->addFilterToTable("project", "##projectid## = '$projectid'");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);

        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
//        $jsdata = array();
        $labels = $data["fields"]; //array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Stato progetto", "Assegnato a", "Azienda cliente", "Telefono cliente", "tel", "cell", "Contatto");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
//        $jsdata[count($jsdata)] = array(count($data["data"]));
        echo (json_encode($jsdata));
    }

    public function ws___avanzamento() {
        if (!isset($this->_parameters[0]))
        {
            throw new \Exception("Devi selezionare un progetto", 0, NULL);
        }
        $projectid = $this->_parameters[0];

        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
//        $view = $this->getActionView();
        header('Content-type: application/json');
        $struct = self::getModelStructure("progetti_ext");
        $struct->addFilterToTable("project", "##projectid## = '$projectid'");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);

        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
        $jsdata = array();
        $labels = $data["fields"]; //array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Stato progetto", "Assegnato a", "Azienda cliente", "Telefono cliente", "tel", "cell", "Contatto");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            $jsdata[$k] = array();
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
        $struct = self::getModelStructure("progetti_so");
        $struct->addFilterToTable("project", "##projectid## = '$projectid'");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);

        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
//        $jsdata = array();
        $labels = $data["fields"]; //array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Stato progetto", "Assegnato a", "Azienda cliente", "Telefono cliente", "tel", "cell", "Contatto");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
//        $jsdata[count($jsdata)] = array(count($data["data"]));
        echo (json_encode($jsdata));
    }

    public function ws___avanzamentoMoreInfo() {
        if (!isset($this->_parameters[0]))
        {
            throw new \Exception("Devi selezionare un progetto", 0, NULL);
        }
        $projectid = $this->_parameters[0];


        $db = \WolfMVC\Registry::get("database_vtiger");
        $link = new \mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());




        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');


        if ($link->connect_errno)
        {
            echo json_encode("Error occurred in db connection!");
            return;
        }

        //recupero info su scadenze
        $sql = "SELECT * "
                . "FROM external_project_milestone "
                . "WHERE projectid = '{$projectid}'";
        $result = $link->query($sql);
        if ($result)
        {
            $data["esecuzione"] = $result->fetch_all(MYSQLI_ASSOC);
        }
        else
        {
            echo json_encode("Error occured in querying db!");
            return;
        }
        //recupero info su fatture
        $sql = "SELECT * "
                . "FROM external_project_invoices "
                . "WHERE projectid = '{$projectid}'";
        $result = $link->query($sql);
        if ($result)
        {
            $data["fatturazione"] = $result->fetch_all(MYSQLI_ASSOC);
        }
        else
        {
            echo json_encode("Error occured in querying db!");
            return;
        }
        //recupero info su incasso
        $sql = "SELECT * "
                . "FROM external_project_collections "
                . "WHERE projectid = '{$projectid}'";
        $result = $link->query($sql);
        if ($result)
        {
            $data["incasso"] = $result->fetch_all(MYSQLI_ASSOC);
        }
        else
        {
            echo json_encode("Error occured in querying db!");
            return;
        }
        //recupero info su attivita
        $sql = "SELECT projecttaskid, projecttaskname, description, projecttaskprogress "
                . "FROM vtiger_projecttask LEFT JOIN vtiger_crmentity ON (projecttaskid = crmid) "
                . "WHERE projectid = '{$projectid}' AND deleted = '0'";
        $result = $link->query($sql);
        if ($result)
        {
            $data["attivita"] = $result->fetch_all(MYSQLI_ASSOC);
        }
        else
        {
            echo json_encode("Error occured in querying db!");
            return;
        }


        echo json_encode($data);
        return;

        $jsdata = array();
        $labels = $data["fields"]; //array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Stato progetto", "Assegnato a", "Azienda cliente", "Telefono cliente", "tel", "cell", "Contatto");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            $jsdata[$k] = array();
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
        $struct = self::getModelStructure("progetti_so");
        $struct->addFilterToTable("project", "##projectid## = '$projectid'");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);

        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
//        $jsdata = array();
        $labels = $data["fields"]; //array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Stato progetto", "Assegnato a", "Azienda cliente", "Telefono cliente", "tel", "cell", "Contatto");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
//        $jsdata[count($jsdata)] = array(count($data["data"]));
        echo (json_encode($jsdata));
    }

    public function ws___commenti() {
        if (!isset($this->_parameters[0]))
        {
            throw new \Exception("Devi selezionare un progetto", 0, NULL);
        }
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $projectid = $this->_parameters[0];
        if (!isset($_POST["data"]))
        {
            echo json_encode(array());
            return;
        }
        $data = json_decode($_POST["data"], true);
        if (!isset($data["op"]))
        {
            echo json_encode(array());
            return;
        }
        if ($data["op"] === "get")
        {
            $client = \WolfMVC\Registry::get("VTWS");
//        print_r($client);
            $query = "SELECT * FROM ModComments WHERE related_to = '31x{$projectid}'";
            $records = $client->doQuery($query);
            $data = array();
            $data["commenti"] = array();
            $data["utenti"] = array();
            if ($records)
            {
                foreach ($records as $recordk => $record) {
                    array_push($data["commenti"], array(
                        "Contenuto" => $record["commentcontent"],
                        "Data creazione" => $record["createdtime"],
                        "Autore" => $record["assigned_user_id"]
                    ));
                }
            }
            $query = "SELECT * FROM Users";
            $records = $client->doQuery($query);
            if ($records)
            {
                foreach ($records as $recordk => $record) {
                    array_push($data["utenti"], array(
                        "Userid" => $record["id"],
                        "Nome" => $record["first_name"],
                        "Cognome" => $record["last_name"]
                    ));
                }
            }

            echo (json_encode($data));
        }
        else
        {
            if (!isset($data["Contenuto"]))
            {
                echo json_encode("false");
                return;
            }
            $session = WolfMVC\Registry::get("session");
            $vtiger_logged_user_id = $session->get("vtiger_logged_user_id");
            $client = \WolfMVC\Registry::get("VTWS");
            if ($record = $client->doCreate("ModComments", array(
                "commentcontent" => $data["Contenuto"],
                "assigned_user_id" => "19x" . $vtiger_logged_user_id,
                "related_to" => "31x" . $projectid,
                "creator" => "19x" . $vtiger_logged_user_id
                    )))
                echo (json_encode("true"));
            else
                echo (json_encode("false"));
        }
    }

    public function ws___confermaMoreInfo() {
        if (!isset($this->_parameters[0]))
        {
            throw new \Exception("Devi selezionare un progetto", 0, NULL);
        }
        $projectid = $this->_parameters[0];


        $db = \WolfMVC\Registry::get("database_vtiger");
        $link = new \mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());




        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');


        if ($link->connect_errno)
        {
            echo json_encode("Error occurred in db connection!");
            return;
        }

        //recupero info su scadenze
        $sql = "SELECT * "
                . "FROM external_project_milestone "
                . "WHERE projectid = '{$projectid}'";
        $result = $link->query($sql);
        if ($result)
        {
            $data["esecuzione"] = $result->fetch_all(MYSQLI_ASSOC);
        }
        else
        {
            echo json_encode("Error occured in querying db!");
            return;
        }
        //recupero info su fatture
        $sql = "SELECT * "
                . "FROM external_project_invoices "
                . "WHERE projectid = '{$projectid}'";
        $result = $link->query($sql);
        if ($result)
        {
            $data["fatturazione"] = $result->fetch_all(MYSQLI_ASSOC);
        }
        else
        {
            echo json_encode("Error occured in querying db!");
            return;
        }
        //recupero info su incasso
        $sql = "SELECT * "
                . "FROM external_project_collections "
                . "WHERE projectid = '{$projectid}'";
        $result = $link->query($sql);
        if ($result)
        {
            $data["incasso"] = $result->fetch_all(MYSQLI_ASSOC);
        }
        else
        {
            echo json_encode("Error occured in querying db!");
            return;
        }
        //recupero info su attivita
        $sql = "SELECT projecttaskid, projecttaskname, description, projecttaskprogress "
                . "FROM vtiger_projecttask LEFT JOIN vtiger_crmentity ON (projecttaskid = crmid) "
                . "WHERE projectid = '{$projectid}' AND deleted = '0'";
        $result = $link->query($sql);
        if ($result)
        {
            $data["attivita"] = $result->fetch_all(MYSQLI_ASSOC);
        }
        else
        {
            echo json_encode("Error occured in querying db!");
            return;
        }


        echo json_encode($data);
        return;

        $jsdata = array();
        $labels = $data["fields"]; //array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Stato progetto", "Assegnato a", "Azienda cliente", "Telefono cliente", "tel", "cell", "Contatto");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            $jsdata[$k] = array();
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
        $struct = self::getModelStructure("progetti_so");
        $struct->addFilterToTable("project", "##projectid## = '$projectid'");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);

        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
//        $jsdata = array();
        $labels = $data["fields"]; //array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Stato progetto", "Assegnato a", "Azienda cliente", "Telefono cliente", "tel", "cell", "Contatto");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
//        $jsdata[count($jsdata)] = array(count($data["data"]));
        echo (json_encode($jsdata));
    }

    public function ws___avvia() {
        if (!isset($this->_parameters[0]))
        {
            throw new \Exception("Devi selezionare un progetto", 0, NULL);
        }
        $projectid = $this->_parameters[0];

        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
//        $view = $this->getActionView();
        header('Content-type: application/json');
        $struct = self::getModelStructure("progetti_ext");
        $struct->addFilterToTable("project", "##projectid## = '$projectid'");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);

        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
        $jsdata = array();
        $labels = $data["fields"]; //array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Stato progetto", "Assegnato a", "Azienda cliente", "Telefono cliente", "tel", "cell", "Contatto");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            $jsdata[$k] = array();
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
        $struct = self::getModelStructure("progetti_so");
        $struct->addFilterToTable("project", "##projectid## = '$projectid'");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);

        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
//        $jsdata = array();
        $labels = $data["fields"]; //array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Stato progetto", "Assegnato a", "Azienda cliente", "Telefono cliente", "tel", "cell", "Contatto");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
//        $jsdata[count($jsdata)] = array(count($data["data"]));
        echo (json_encode($jsdata));
    }

    public function ws___contatti() {
        if (!isset($this->_parameters[0]))
        {
            throw new \Exception("Devi selezionare un account", 0, NULL);
        }
        $accountid = $this->_parameters[0];

        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
//        $view = $this->getActionView();
        header('Content-type: application/json');
        $client = \WolfMVC\Registry::get("VTWS");
//        print_r($client);
        $query = "SELECT * FROM Contacts WHERE account_id = '11x{$accountid}'";
        $records = $client->doQuery($query);
        $data = array();
        if ($records)
        {
            foreach ($records as $recordk => $record) {
                array_push($data, array(
                    "Nome" => $record["firstname"],
                    "Cognome" => $record["lastname"],
                    "Numero contatto" => $record["contact_no"],
                    "Telefono" => $record["phone"],
                    "Cellulare" => $record["mobile"],
                    "Email" => $record["email"]
                ));
            }
        }
        echo (json_encode($data));
    }

    public function ws___salvaEsecuzioneAvvio() { // salva esecuzione avvio progetto
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
//        $view = $this->getActionView();
        header('Content-type: application/json');
        $data = $_POST;

        if (isset($data["esecuzione"]))
        {
            $client = \WolfMVC\Registry::get("VTWS");
            if (!$client)
            {
                echo json_encode($client);
                return;
            }
            $data["esecuzione"] = json_decode($data["esecuzione"]);

            $sql = "INSERT INTO external_project_milestone "
                    . "(date,label,status,projectid,flowstatus,isactual,actualdate,actuallabel,create_date) "
                    . "VALUES";

            $esecuzioneraw = $data["esecuzione"];
            $esecuzione = array();
            $rows = array();
            $projectid = $this->anti_injection($esecuzioneraw->projectid);
//            echo $projectid;
            for ($i = 0; $i < (int) $esecuzioneraw->numrows; $i++) {
                if (!isset($esecuzioneraw->{$i}))
                    break;
                $esecuzione[$i] = $esecuzioneraw->{$i};
                if ($i == 0 && $esecuzione[$i]->actualDate !== "")
                {
                    array_push($rows, " ('{$this->anti_injection($esecuzione[$i]->budgetDate)}','{$this->anti_injection($esecuzione[$i]->label)}','0','{$projectid}','1','1','{$this->anti_injection($esecuzione[$i]->actualDate)}','{$this->anti_injection($esecuzione[$i]->label)}',now())");
                    array_push($rows, " ('{$this->anti_injection($esecuzione[$i]->budgetDate)}','{$this->anti_injection($esecuzione[$i]->label)}','1','{$projectid}','1','1','{$this->anti_injection($esecuzione[$i]->actualDate)}','{$this->anti_injection($esecuzione[$i]->label)}',now())");
                }
                else
                {
                    array_push($rows, " ('{$this->anti_injection($esecuzione[$i]->budgetDate)}','{$this->anti_injection($esecuzione[$i]->label)}','0','{$projectid}','1','0','','',now())");
                    array_push($rows, " ('{$this->anti_injection($esecuzione[$i]->budgetDate)}','{$this->anti_injection($esecuzione[$i]->label)}','1','{$projectid}','1','0','','',now())");
                }
            }

            $project = $client->doRetrieve("31x" . $projectid);
            $project["cf_693"] = '1';
            $update = $client->doUpdate($project);
            $sql .= join(", ", $rows);
            $delete = "DELETE FROM external_project_milestone WHERE projectid='{$projectid}' AND status='0'";
            $db = \WolfMVC\Registry::get("database_vtiger");

            $link = new \mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
            if ($link->connect_errno)
            {
                echo json_encode("Error occurred in db connection!");
                return;
            }
            $delete = $link->query($delete);
            $query = $link->query($sql);
            if ($update && $delete && $query)
            {
                echo json_encode("Piano esecuzione correttamente inserito.");
                return;
            }
            else
            {
                echo json_encode("Il piano esecuzione non &eacute; stato correttamente inserito per via di un errore: " . $link->error);
                return;
            }
        }
        else
        {
            echo json_encode("Failed");
            return;
        }
    }

    public function ws___salvaFatturazioneAvvio() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
//        $view = $this->getActionView();
        header('Content-type: application/json');
        $data = $_POST;

        if (isset($data["fatturazione"]))
        {
            $data["fatturazione"] = json_decode($data["fatturazione"]);
            $sql = "INSERT INTO external_project_invoices "
                    . "(date, amount, salesorderid, status, flowstatus, projectid, create_date, isActual, actualamount) "
                    . "VALUES";

            $fatturazioneraw = $data["fatturazione"];
            if ((int) $fatturazioneraw->numrows === 0)
            {
                echo json_encode("Piano fatturazione (vuoto) correttamente inserito.");
                return;
            }
            $fatturazione = array();
            $rows = array();
            $projectid = $this->anti_injection($fatturazioneraw->projectid);
            $salesorderid = $this->anti_injection($fatturazioneraw->salesorderid);
//            echo $projectid;
            for ($i = 0; $i < (int) $fatturazioneraw->numrows; $i++) {
                if (!isset($fatturazioneraw->{$i}))
                    break;
                $fatturazione[$i] = $fatturazioneraw->{$i};
                array_push($rows, " ('{$this->anti_injection($fatturazione[$i]->date)}','{$this->anti_injection($fatturazione[$i]->amount)}','{$salesorderid}','0','1','{$projectid}',now(),'0','')");
                array_push($rows, " ('{$this->anti_injection($fatturazione[$i]->date)}','{$this->anti_injection($fatturazione[$i]->amount)}','{$salesorderid}','1','1','{$projectid}',now(),'0','')");
            }
            $sql .= join(", ", $rows);
            $delete = "DELETE FROM external_project_invoices WHERE projectid='{$projectid}' AND status='0'";
            $db = \WolfMVC\Registry::get("database_vtiger");
            $link = new \mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
            if ($link->connect_errno)
            {
                echo json_encode("Error occurred in db connection!");
                return;
            }
            $delete = $link->query($delete);
            $query = $link->query($sql);
            if ($delete && $query)
            {
                echo json_encode("Piano fatturazione correttamente inserito.");
                return;
            }
            else
            {
                echo json_encode("Il piano fatturazione non &eacute; stato correttamente inserito per via di un errore: " . $link->error);
                return;
            }
        }
        else
        {
            echo json_encode("Failed");
            return;
        }
    }

    public function ws___salvaIncassoAvvio() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
//        $view = $this->getActionView();
        header('Content-type: application/json');
        $data = $_POST;

        if (isset($data["incasso"]))
        {
            $data["incasso"] = json_decode($data["incasso"]);
            $sql = "INSERT INTO external_project_collections "
                    . "(date, amount, salesorderid, status, flowstatus, projectid, type, create_date, isactual, actualdate, actualamount, actualtype) "
                    . "VALUES";

            $incassoraw = $data["incasso"];
            if ((int) $incassoraw->numrows === 0)
            {
                echo json_encode("Piano incasso (vuoto) correttamente inserito.");
                return;
            }

            $incasso = array();
            $rows = array();
            $projectid = $this->anti_injection($incassoraw->projectid);
            $salesorderid = $this->anti_injection($incassoraw->salesorderid);

            for ($i = 0; $i < (int) $incassoraw->numrows; $i++) {
                if (!isset($incassoraw->{$i}))
                    break;
                $incasso[$i] = $incassoraw->{$i};
                array_push($rows, " ('{$this->anti_injection($incasso[$i]->date)}','{$this->anti_injection($incasso[$i]->amount)}','{$salesorderid}','0','1','{$projectid}','{$this->anti_injection($incasso[$i]->type)}',now(),'0','','','')");
                array_push($rows, " ('{$this->anti_injection($incasso[$i]->date)}','{$this->anti_injection($incasso[$i]->amount)}','{$salesorderid}','1','1','{$projectid}','{$this->anti_injection($incasso[$i]->type)}',now(),'0','','','')");
            }
            $sql .= join(", ", $rows);
            $delete = "DELETE FROM external_project_collections WHERE projectid='{$projectid}' AND status='0'";
            $db = \WolfMVC\Registry::get("database_vtiger");
            $link = new \mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
            if ($link->connect_errno)
            {
                echo json_encode("Error occurred in db connection!");
                return;
            }
            $delete = $link->query($delete);
            $query = $link->query($sql);
            if ($delete && $query)
            {
                echo json_encode("Piano incasso correttamente inserito.");
                return;
            }
            else
            {
                echo json_encode("Il piano incasso non &eacute; stato correttamente inserito per via di un errore: " . $link->error);
                return;
            }
        }
        else
        {
            echo json_encode("Failed");
            return;
        }
    }

    public function ws___salvaAttivitaAvvio() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $data = $_POST;

        if (isset($data["attivita"]))
        {
            $data["attivita"] = json_decode($data["attivita"]);
            $attivitaraw = $data["attivita"];
            $attivita = array();
            $rows = array();
            $projectid = $attivitaraw->projectid;
//            echo $projectid;

            for ($i = 0; $i < (int) $attivitaraw->numrows; $i++) {
                if (!isset($attivitaraw->{$i}))
                    break;
                $attivita[$i] = $attivitaraw->{$i};
            }
            $countsuccess = 0;
            $client = \WolfMVC\Registry::get("VTWS");
            $session = WolfMVC\Registry::get("session");
            $vtiger_logged_user_id = $session->get("vtiger_logged_user_id");
            foreach ($attivita as $attk => $att) {
                if ($record = $client->doCreate("ProjectTask", array(
                    "projecttaskname" => $att->name,
                    "projectid" => "31x" . $projectid,
                    "assigned_user_id" => "19x" . $vtiger_logged_user_id,
                    "modifiedby" => "19x" . $vtiger_logged_user_id,
                    "description" => $att->description
                        )))
                    $countsuccess++;
                else
                {
                    ob_start();
                    echo "Create {$countsuccess} attività su {$attivitaraw->numrows}. Si &eacute; verificato un errore";
                    print_r($record);
                    $ret = json_encode(ob_get_contents());
                    ob_end_clean();
                    echo $ret;
                    return;
                }
            }
            echo json_encode("Create {$countsuccess} attività su {$attivitaraw->numrows}.");
            return;
        }
        else
        {
            echo json_encode("Failed");
            return;
        }
    }

    public function ws___salvaAssegnazione() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $data = $_REQUEST;

        if (isset($data["data"]))
        {
            $data = json_decode($data["data"]);
            $projectid = $data->projectid;
            $assegna = $data->assegna;
            $data = $data->info;
            $client = \WolfMVC\Registry::get("VTWS");

            $proj = $client->doRetrieve("31x" . $projectid);
            $proj = array_merge($proj, (array) $data);
            if ($assegna)
            {
                $proj["projectstatus"] = "initiated";
                if (isset($proj["cf_691"])){
                    if (strpos($proj["cf_691"], "6x") === FALSE){
                        $proj["cf_691"] = "6x".$proj["cf_691"];
                    }
                            
                }
            }
            if ($client->doUpdate($proj))
            {
                echo json_encode("Update riuscito.");// vm was ". print_r($proj,true)."  ".print_r($client->lastError(),true));

                return;
            }
            else
            {
                echo json_encode("Update Failed.");// vm was ". print_r($proj,true)."  ".print_r($client->lastError(),true));
                return;
            }

            return;
        }
        else
        {
            echo json_encode("Failed");
            return;
        }
    }

    public function ws___salvaConferma() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
//        $view = $this->getActionView();
        header('Content-type: application/json');
        $data = $_POST["data"];
        $data = json_decode($data, true);
        if (isset($data["esito"]) && isset($data["projectid"]) && isset($data["tipo"]))
        {
            $projectid = $data["projectid"];
            if ($data["tipo"] === "AVVIO")
            {
                $db = \WolfMVC\Registry::get("database_vtiger");
                $link = new \mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());

                if ($data["esito"] . "" === "1")
                {

                    $sql = array("UPDATE external_project_collections SET flowstatus = '0' WHERE projectid='{$projectid}' AND flowstatus = '1'",
                        "UPDATE external_project_invoices SET flowstatus = '0' WHERE projectid='{$projectid}' AND flowstatus = '1'",
                        "UPDATE external_project_milestone SET flowstatus = '0' WHERE projectid='{$projectid}' AND flowstatus = '1'");
                }
                else if ($data["esito"] . "" === "-1")
                {
                    $sql = array("DELETE FROM external_project_collections WHERE projectid='{$projectid}' AND status = '1'",
                        "DELETE FROM external_project_invoices WHERE projectid='{$projectid}' AND status = '1'",
                        "DELETE FROM external_project_milestone WHERE projectid='{$projectid}' AND status = '1'",
                        "UPDATE external_project_collections SET flowstatus = '2' WHERE projectid='{$projectid}' AND flowstatus = '1'",
                        "UPDATE external_project_invoices SET flowstatus = '2' WHERE projectid='{$projectid}' AND flowstatus = '1'",
                        "UPDATE external_project_milestone SET flowstatus = '2' WHERE projectid='{$projectid}' AND flowstatus = '1'");
                }
                if (!isset($sql))
                {
                    echo json_encode("sql non e definito");
                    return;
                }
                $client = \WolfMVC\Registry::get("VTWS");
                $proj = $client->doRetrieve("31x" . $projectid);
                if ($proj["projectstatus"] === "prospecting" && $data["esito"] . "" === "1")
                    $proj["projectstatus"] = "initiated";
                if ($proj["projectstatus"] === "initiated" && $data["esito"] . "" === "1")
                    $proj["projectstatus"] = "in progress";
                if ($data["esito"] . "" === "1")
                    $proj["cf_693"] = 0;
                else if ($data["esito"] . "" === "-1")
                    $proj["cf_693"] = 2;
                $update = $client->doUpdate($proj);
                $result = true;
                foreach ($sql as $s) {
                    $result = $result && $link->query($s);
                    if (!$result)
                    {
                        echo json_encode("Procedura di conferma non completata per via di un errore: " . $link->error);
                        return;
                    }
                }

                if ($result && $update)
                {
                    echo json_encode("Procedura di conferma completata.");
                    return;
                }
                else
                {
                    echo json_encode("Procedura di conferma non completata per via di un errore: " . $link->error);
                    return;
                }
            }
            else
            {
                if ($data["esito"] . "" === "1")
                {
                    $sql = array("DELETE FROM external_project_collections WHERE projectid='{$projectid}' AND flowstatus = '2'",
                        "UPDATE external_project_collections SET status = '-1' WHERE projectid='{$projectid}' AND status = '1' AND flowstatus = '0'",
                        "UPDATE external_project_collections SET flowstatus = '0' WHERE projectid='{$projectid}' AND status = '1' AND flowstatus = '1'",
                        "DELETE FROM external_project_collections WHERE projectid='{$projectid}' AND status = '-1'",
                        "DELETE FROM external_project_invoices WHERE projectid='{$projectid}' AND flowstatus = '2'",
                        "UPDATE external_project_invoices SET status = '-1' WHERE projectid='{$projectid}' AND status = '1' AND flowstatus = '0'",
                        "UPDATE external_project_invoices SET flowstatus = '0' WHERE projectid='{$projectid}' AND status = '1' AND flowstatus = '1'",
                        "DELETE FROM external_project_invoices WHERE projectid='{$projectid}' AND status = '-1'",
                        "DELETE FROM external_project_milestone WHERE projectid='{$projectid}' AND flowstatus = '2'",
                        "UPDATE external_project_milestone SET status = '-1' WHERE projectid='{$projectid}' AND status = '1' AND flowstatus = '0'",
                        "UPDATE external_project_milestone SET flowstatus = '0' WHERE projectid='{$projectid}' AND status = '1' AND flowstatus = '1'",
                        "DELETE FROM external_project_milestone WHERE projectid='{$projectid}' AND status = '-1'");
                }
                if ($data["esito"] . "" === "-1")
                {
                    $sql = array("UPDATE external_project_collections SET flowstatus = '2' WHERE projectid='{$projectid}' AND status = '1' AND flowstatus = '1'",
                        "UPDATE external_project_invoices SET flowstatus = '2' WHERE projectid='{$projectid}' AND status = '1' AND flowstatus = '1'",
                        "UPDATE external_project_milestone SET flowstatus = '2' WHERE projectid='{$projectid}' AND status = '1' AND flowstatus = '1'");
                }
                $client = \WolfMVC\Registry::get("VTWS");
                $proj = $client->doRetrieve("31x" . $projectid);
                if ($proj["projectstatus"] === "prospecting" && $data["esito"] . "" === "1")
                    $proj["projectstatus"] = "initiated";
                $proj["cf_693"] = 0;
                $update = $client->doUpdate($proj);
                $db = \WolfMVC\Registry::get("database_vtiger");
                $link = new \mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
                if ($link->connect_errno)
                {
                    echo json_encode("Error occurred in db connection!");
                    return;
                }

//                $result = $link->multi_query($sql);
                $result = true;
                foreach ($sql as $s) {
                    $result = $result && $link->query($s);
                    if (!$result)
                    {
                        echo json_encode("Procedura di conferma non completata per via di un errore: " . $link->error);
                        return;
                    }
                }
                if ($result && $update)
                {
                    echo json_encode("Procedura di conferma completata.");
                    return;
                }
                else
                {
                    echo json_encode("Procedura di conferma non completata per via di un errore: " . $link->error);
                    return;
                }
            }
        }
        else
        {
            echo json_encode("Failed");
            return;
        }
    }

    public function progetti_da_avviare() {
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("PROGETTI" => "progetti", "Gestione" => "progetti/gestione", "Progetti da avviare" => "last")));
        $view = $this->getActionView();
        $view->set("action_0", "Indietro");
        $view->set("path_action_0", $this->getBackTrack());
        $view->set("angular_dollar_index", '$index');
        $view->set("expr_link", 'ListoneCtrl.avvia(datum["Pk_project"])');

//        $this->_system_js_including .= "<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/angularjs/1.2.25/angular.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/angular.min.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ngbootstrap.min.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ng-ui-bootstrap-tpls-0.2.0.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/progetti/progettidaavviare.js\"></script>";
        $this->_system_js_including .="<link rel=\"stylesheet\" href=\"" . SITE_PATH . "css/progetti/progetti_da_avviare.css\">";
        return;
    }

    public function ws___progetti_da_avviare() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        $session = Registry::get("session");
        $vtuser = $session->get("vtiger_logged_user_id");
//        echo $vtuser;
        
//            $view = $this->getActionView();
        header('Content-type: application/json');
        $struct = self::getModelStructure("progetti");
        $struct->addFilterToTable("project", "##projectstatus## = 'initiated'");
        $struct->addFilterToTable("project_ent", " AND ##smownerid## = '{$vtuser}'");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);

        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
        $jsdata = array();

        $labels = array("Pk_project", "Numero progetto", "Nome progetto", "Situazione progetto", "Tipo progetto", "Data apertura progetto", "Azienda cliente");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            $jsdata[$k] = array();
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
//        $jsdata[count($jsdata)] = array(count($data["data"]));
        echo (json_encode($jsdata));
//        echo json_encode($jsdata);
    }

    public function ws___describe() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);

//            $view = $this->getActionView();
        header('Content-type: application/json');
        $ret = array();
        $ret[0] = "No WS available at such address";
        $ret['RequestAccept'] = $this->parseAcceptHeader();
        echo json_encode($ret);
        exit;
//            $view->set("data", json_encode("No WS available at such address"));
    }

    public function ws___data() {
        $this->usePageComp("0.1");
        parent::ws___data();
    }

    public function ws___salvaEsecuzione() { // salva esecuzione progetto
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
//        $view = $this->getActionView();
        header('Content-type: application/json');
        $data = $_POST;

        if (isset($data["esecuzione"]))
        {
            $client = \WolfMVC\Registry::get("VTWS");
            if (!$client)
            {
                echo json_encode($client);
                return;
            }
            $data["esecuzione"] = json_decode($data["esecuzione"]);

            $sql = "INSERT INTO external_project_milestone "
                    . "(date,label,status,projectid,flowstatus,isactual,actualdate,actuallabel,create_date) "
                    . "VALUES";

            $esecuzioneraw = $data["esecuzione"];
            $esecuzione = array();
            $rows = array();
            $projectid = $this->anti_injection($esecuzioneraw->projectid);
//            echo $projectid;
            $sqldelete = "DELETE FROM external_project_milestone WHERE projectid = '{$projectid}' AND flowstatus='1'";
            $sqldelete2 = "DELETE FROM external_project_milestone WHERE projectid = '{$projectid}' AND flowstatus='2'";

            for ($i = 0; $i < (int) $esecuzioneraw->numrows; $i++) {
                if (!isset($esecuzioneraw->{$i}))
                    break;
                $esecuzione[$i] = $esecuzioneraw->{$i};
                if ($esecuzione[$i]->isactual . "" === '1' || ($esecuzione[$i]->actualdate && $esecuzione[$i]->actualdate !== "" && $esecuzione[$i]->actualdate !== "0000-00-00" ))
                {
                    $isactual = 1;
                    $esecuzione[$i]->actuallabel = $esecuzione[$i]->label;
                }
                else
                    $isactual = 0;
                array_push($rows, " ('{$this->anti_injection($esecuzione[$i]->date)}','{$this->anti_injection($esecuzione[$i]->label)}','1','{$projectid}','1','{$isactual}','{$esecuzione[$i]->actualdate}','{$esecuzione[$i]->actuallabel}',now())");
            }

            $project = $client->doRetrieve("31x" . $projectid);
            $project["cf_693"] = '1';
            $update = $client->doUpdate($project);
            $sql .= join(", ", $rows);
            $db = \WolfMVC\Registry::get("database_vtiger");

            $link = new \mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
            if ($link->connect_errno)
            {
                echo json_encode("Error occurred in db connection!");
                return;
            }
            $delete = $link->query($sqldelete);
            $delete2 = $link->query($sqldelete2);
            $query = $link->query($sql);
            if ($update && $delete && $delete2 && $query)
            {
                echo json_encode("Piano esecuzione correttamente inserito.");
                return;
            }
            else
            {
                echo json_encode("Il piano esecuzione non &eacute; stato correttamente inserito per via di un errore: " . $link->error);
                return;
            }
        }
        else
        {
            echo json_encode("Failed");
            return;
        }
    }

    public function ws___salvaFatturazione() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
//        $view = $this->getActionView();
        header('Content-type: application/json');
        $data = $_POST;

        if (isset($data["fatturazione"]))
        {

            $data["fatturazione"] = json_decode($data["fatturazione"]);
//            echo json_encode($data["fatturazione"]);
//            return;
            $sql = "INSERT INTO external_project_invoices "
                    . "(date, amount, salesorderid, status, flowstatus, projectid, create_date, isactual, actualamount, actualdate) "
                    . "VALUES";

            $fatturazioneraw = $data["fatturazione"];
            if ((int) $fatturazioneraw->numrows === 0)
            {
                echo json_encode("Piano fatturazione (vuoto) correttamente inserito.");
                return;
            }
            $fatturazione = array();
            $rows = array();
            $projectid = $this->anti_injection($fatturazioneraw->projectid);
            $salesorderid = $this->anti_injection($fatturazioneraw->salesorderid);
            $sqldelete = "DELETE FROM external_project_invoices WHERE projectid = '{$projectid}' AND flowstatus='1'";
            $sqldelete2 = "DELETE FROM external_project_invoices WHERE projectid = '{$projectid}' AND flowstatus='2'";
            for ($i = 0; $i < (int) $fatturazioneraw->numrows; $i++) {
                if (!isset($fatturazioneraw->{$i}))
                    break;
                $fatturazione[$i] = $fatturazioneraw->{$i};
                if ($fatturazione[$i]->isactual . "" === "1" || ($fatturazione[$i]->actualdate && $fatturazione[$i]->actualdate !== "" && $fatturazione[$i]->actualdate !== "0000-00-00" ))
                {
                    $isactual = 1;
                    $fatturazione[$i]->actualamount = $fatturazione[$i]->amount;
                }
                else
                {
                    $isactual = 0;
                    $fatturazione[$i]->actualdate = '0000-00-00';
                }
                array_push($rows, " ('{$this->anti_injection($fatturazione[$i]->date)}','{$this->anti_injection($fatturazione[$i]->amount)}','{$salesorderid}','1','1','{$projectid}',now(),'{$isactual}','{$this->anti_injection($fatturazione[$i]->actualamount)}','{$this->anti_injection($fatturazione[$i]->actualdate)}')");
            }
            $sql .= join(", ", $rows);
            $db = \WolfMVC\Registry::get("database_vtiger");
            $link = new \mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
            if ($link->connect_errno)
            {
                echo json_encode("Error occurred in db connection!");
                return;
            }
            $delete = $link->query($sqldelete);
            $delete2 = $link->query($sqldelete2);
            $query = $link->query($sql);
            if ($delete && $delete2 && $query)
            {
                echo json_encode("Piano fatturazione correttamente inserito.");
                return;
            }
            else
            {
                echo json_encode("Il piano fatturazione non &eacute; stato correttamente inserito per via di un errore: " . $link->error);
                return;
            }
        }
        else
        {
            echo json_encode("Failed");
            return;
        }
    }

    public function ws___salvaIncasso() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
//        $view = $this->getActionView();
        header('Content-type: application/json');
        $data = $_POST;

        if (isset($data["incasso"]))
        {
            $data["incasso"] = json_decode($data["incasso"]);
            $sql = "INSERT INTO external_project_collections "
                    . "(date, amount, salesorderid, status, flowstatus, projectid, type, create_date, isactual, actualdate, actualamount, actualtype) "
                    . "VALUES";

            $incassoraw = $data["incasso"];
            if ((int) $incassoraw->numrows === 0)
            {
                echo json_encode("Piano incasso (vuoto) correttamente inserito.");
                return;
            }

            $incasso = array();
            $rows = array();
            $projectid = $this->anti_injection($incassoraw->projectid);
            $salesorderid = $this->anti_injection($incassoraw->salesorderid);
            $sqldelete = "DELETE FROM external_project_collections WHERE projectid = '{$projectid}' AND flowstatus='1'";
            $sqldelete2 = "DELETE FROM external_project_collections WHERE projectid = '{$projectid}' AND flowstatus='2'";
            for ($i = 0; $i < (int) $incassoraw->numrows; $i++) {
                if (!isset($incassoraw->{$i}))
                    break;
                $incasso[$i] = $incassoraw->{$i};
                if ($incasso[$i]->isactual . "" === '1' || ($incasso[$i]->actualdate && $incasso[$i]->actualdate !== "" && $incasso[$i]->actualdate !== "0000-00-00" ))
                {
                    $isactual = 1;
                    $incasso[$i]->actualamount = $incasso[$i]->amount;
                    $incasso[$i]->actualtype = $incasso[$i]->type;
                }
                else
                    $isactual = 0;
                array_push($rows, " ('{$this->anti_injection($incasso[$i]->date)}','{$this->anti_injection($incasso[$i]->amount)}','{$salesorderid}','1','1','{$projectid}','{$this->anti_injection($incasso[$i]->type)}',now(),'{$isactual}','{$this->anti_injection($incasso[$i]->actualdate)}','{$this->anti_injection($incasso[$i]->actualamount)}','{$this->anti_injection($incasso[$i]->actualtype)}')");
            }
            $sql .= join(", ", $rows);
            $db = \WolfMVC\Registry::get("database_vtiger");
            $link = new \mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
            if ($link->connect_errno)
            {
                echo json_encode("Error occurred in db connection!");
                return;
            }
            $delete = $link->query($sqldelete);
            $delete2 = $link->query($sqldelete2);
            $query = $link->query($sql);
            if ($delete && $delete2 && $query)
            {
                echo json_encode("Piano incasso correttamente inserito.");
                return;
            }
            else
            {
                echo json_encode("Il piano incasso non &eacute; stato correttamente inserito per via di un errore: " . $link->error);
                return;
            }
        }
        else
        {
            echo json_encode("Failed");
            return;
        }
    }

    public function ws___salvaAttivita() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $data = $_POST;

        if (isset($data["attivita"]))
        {
            $data["attivita"] = json_decode($data["attivita"]);
            $attivitaraw = $data["attivita"];
            $attivita = array();
            $rows = array();
            $projectid = $attivitaraw->projectid;
//            echo $projectid;

            for ($i = 0; $i < (int) $attivitaraw->numrows; $i++) {
                if (!isset($attivitaraw->{$i}))
                    break;
                $attivita[$i] = $attivitaraw->{$i};
            }
            $countcreate = 0;
            $countupdate = 0;
            $client = \WolfMVC\Registry::get("VTWS");
            $session = WolfMVC\Registry::get("session");
            $vtiger_logged_user_id = $session->get("vtiger_logged_user_id");
            foreach ($attivita as $attk => $att) {
                //2 casi
                if (!isset($att->projecttaskid))
                {
                    if ($record = $client->doCreate("ProjectTask", array(
                        "projecttaskname" => $att->projecttaskname,
                        "projectid" => "31x" . $projectid,
                        "assigned_user_id" => "19x" . $vtiger_logged_user_id,
                        "modifiedby" => "19x" . $vtiger_logged_user_id,
                        "description" => $att->description,
                        "projecttaskprogress" => $att->projecttaskprogress
                            )))
                        $countcreate++;
                    else
                    {

                        ob_start();
                        echo "Create {$countsuccess} attivit&agrave; su {$attivitaraw->numrows}. Si &eacute; verificato un errore";
                        print_r($record);
                        $ret = json_encode(ob_get_contents());
                        ob_end_clean();
                        echo $ret;
                        return;
                    }
                }
                else
                {
                    if ($projecttask = $client->doRetrieve("30x" . $att->projecttaskid))
                    {
                        $projecttask["projecttaskname"] = $att->projecttaskname;
                        $projecttask["description"] = $att->description;
                        $projecttask["projecttaskprogress"] = $att->projecttaskprogress;
                        if ($update = $client->doUpdate($projecttask))
                        {
                            $countupdate++;
                        }
                        else
                        {
                            ob_start();
                            echo "Create {$countsuccess} attivit&agrave; su {$attivitaraw->numrows}. Si &eacute; verificato un errore";
                            print_r($record);
                            $ret = json_encode(ob_get_contents());
                            ob_end_clean();
                            echo $ret;
                            return;
                        }
                    }
                    else
                    {
                        ob_start();
                        echo "Create {$countcreate} attività e aggiornate {$countupdate} attività su {$attivitaraw->numrows}. Si &eacute; verificato un errore";
                        print_r($record);
                        $ret = json_encode(ob_get_contents());
                        ob_end_clean();
                        echo $ret;
                        return;
                    }
                }
            }
            echo json_encode("Create {$countcreate} attività e aggiornate {$countupdate} attività su {$attivitaraw->numrows}.");
            return;
        }
        else
        {
            echo json_encode("Failed");
            return;
        }
    }

    public static function getModelStructure($modelname) {
        switch ($modelname) {
            case 'tipoprogetto':
                $struct = new \WolfMVC\Smm\Smmstructure();
                $struct->setDefaultDb("vtiger");
                $struct->addTable("vtiger_projecttype", "ptype");
                $struct->addField("projecttype", "ptype", "type");
                break;
            case 'consulenti':
                $struct = new \WolfMVC\Smm\Smmstructure();
                $struct->setDefaultDb("vtiger");
                $struct->addTable("vtiger_users", "consulenti");
                $struct->addTable("vtiger_user2role", "consulenti_ruoli");
                $struct->setRelation("consulenti", "consulenti_ruoli", "!", "id", "userid");
                $struct->addField("first_name", "consulenti", "nome");
                $struct->addField("last_name", "consulenti", "cognome");
                $struct->addField("roleid", "consulenti_ruoli", "ruolo");
                $struct->addFilterToField("ruolo", "##FIELD## = 'H4'");
                $struct->addCalculatedField("CONCAT_WS(' ',##1##,##2##)", array("nome", "cognome"), "Consulente");
                break;
            case 'progetti':
                $struct = new \WolfMVC\Smm\Smmstructure();
                $struct->setDefaultDb("vtiger");
                $struct->addTable("vtiger_project", "project")
                        ->addTable("vtiger_projectcf", "projectcf")
                        ->addTable("vtiger_crmentity", "project_ent")
                        ->setRelation("project", "projectcf", "!", "projectid", "projectid")
                        ->setRelation("project", "project_ent", "!", "projectid", "crmid")
                        ->setEntity("PROJECT", array("project", "projectcf", "project_ent"));
                $struct->addField("projectname", "project", "Nome progetto");
                $struct->addField("projecttype", "project", "Tipo progetto");
                $struct->addField("startdate", "project", "Data apertura progetto");
                $struct->addField("projectstatus", "project", "Stato progetto");
                $struct->addField("project_no", "project", "Numero progetto");
                $struct->addField("smownerid", "project_ent", "Id assegnato a");
                $struct->addField("cf_693", "projectcf", "Situazione progetto");
                $struct->setDbDrivenPicklist("Tipo progetto", self::getModelStructure("tipoprogetto"), "type", "type");
                $struct->addTable("vtiger_users", "consulenti")->setRelation("project_ent", "consulenti", "*", "smownerid", "id");
                $struct->addField("first_name", "consulenti", "Consulente nome")->addField("last_name", "consulenti", "Consulente cognome");
                $struct->addCalculatedField("CONCAT_WS(' ',##1##,##2##)", array("Consulente nome", "Consulente cognome"), "Assegnato a");
                $struct->addTable("vtiger_account", "account")->addTable("vtiger_accountscf", "accountcf")->addTable("vtiger_crmentity", "account_ent");
                $struct->setRelation("account", "accountcf", "!", "accountid", "accountid")->setRelation("account", "account_ent", "!", "accountid", "crmid");
                $struct->setEntity("ACCOUNT", array("account", "accountcf", "account_ent"));
                $struct->setRelation("project", "account", "*", "linktoaccountscontacts", "accountid");
                $struct->addField("accountname", "account", "Azienda cliente")->addField("phone", "account", "Telefono cliente");

                $struct->addTable("vtiger_contactdetails", "contatti")->setRelation("account", "contatti", "+", "accountid", "accountid");
                $struct->addTable("vtiger_contactscf", "contatticf")->setRelation("contatti", "contatticf", "!", "contactid", "contactid");
                $struct->addTable("vtiger_crmentity", "contatti_ent")->setRelation("contatti", "contatti_ent", "!", "contactid", "crmid");
                $struct->setEntity("CONTATTI", array("contatti", "contatticf", "contatti_ent"));

                $struct->addField("firstname", "contatti", "Nome contatto")->addField("lastname", "contatti", "Cognome contatto")
                        ->addCalculatedField("CONCAT_WS(' ',##1##,##2##)", array("Nome contatto", "Cognome contatto"), "Contatto", true);
                $struct->addField("phone", "contatti", "tel")->addField("mobile", "contatti", "cell");
                $struct->addFilterToTable("contatti_ent", "##deleted## = '0'");
                $struct->addFilterToTable("project_ent", "##deleted## = '0'");
                $struct->addFilterToTable("account_ent", "##deleted## = '0'");
                $struct->setFieldsEditable(array(
                    "Nome progetto", "Tipo progetto", "Id assegnato a"
                ));

                break;
            case 'progetti_ext':
                $struct = new \WolfMVC\Smm\Smmstructure();
                $struct->setDefaultDb("vtiger");
                $struct->addTable("vtiger_project", "project")
                        ->addTable("vtiger_projectcf", "projectcf")
                        ->addTable("vtiger_crmentity", "project_ent")
                        ->setRelation("project", "projectcf", "!", "projectid", "projectid")
                        ->setRelation("project", "project_ent", "!", "projectid", "crmid")
                        ->setEntity("PROJECT", array("project", "projectcf", "project_ent"));

                $struct->addField("projectname", "project", "Nome progetto");
                $struct->addField("projecttype", "project", "Tipo progetto");
                $struct->addField("startdate", "project", "Data apertura progetto");
                $struct->addField("projectstatus", "project", "Stato progetto");
                $struct->addField("project_no", "project", "Numero progetto");
                $struct->addField("smownerid", "project_ent", "Id assegnato a");
                $struct->addField("description", "project_ent", "Descrizione");
                $struct->setDbDrivenPicklist("Tipo progetto", self::getModelStructure("tipoprogetto"), "type", "type");
                $struct->addTable("vtiger_users", "consulenti")->setRelation("project_ent", "consulenti", "*", "smownerid", "id");
                $struct->addField("first_name", "consulenti", "Consulente nome")->addField("last_name", "consulenti", "Consulente cognome");
                $struct->addCalculatedField("CONCAT_WS(' ',##1##,##2##)", array("Consulente nome", "Consulente cognome"), "Assegnato a");
                $struct->addTable("vtiger_account", "account")->addTable("vtiger_accountscf", "accountcf")->addTable("vtiger_crmentity", "account_ent");
                $struct->setRelation("account", "accountcf", "!", "accountid", "accountid")->setRelation("account", "account_ent", "!", "accountid", "crmid");
                $struct->setEntity("ACCOUNT", array("account", "accountcf", "account_ent"));
                $struct->setRelation("project", "account", "*", "linktoaccountscontacts", "accountid");
                $struct->addTable("vtiger_potential", "pot")
                        ->setRelation("account", "pot", "!", "accountid", "related_to");
                $struct->addTable("vtiger_potentialscf", "potcf")
                        ->setRelation("pot", "potcf", "!", "potentialid", "potentialid");
                $struct->addFilterToTable("pot", "##potentialname## = 'PROGETTO'");
                $struct->addField("cf_655", "potcf", "Analista");
                $struct->addField("accountname", "account", "Azienda cliente")->addField("phone", "account", "Telefono cliente");
                $struct->addField("account_no", "account", "Numero cliente")->addField("annualrevenue", "account", "Fatturato");
                $struct->addField("otherphone", "account", "Ev.le altro telefono")->addField("email1", "account", "Email");
                $struct->addField("fax", "account", "Fax")->addField("employees", "account", "Dipendenti");
                $struct->addField("cf_641", "accountcf", "Partita Iva")->addField("cf_643", "accountcf", "Vendita gestita da");
                $struct->addField("cf_644", "accountcf", "Contatto gestito da");

                $struct->addTable("vtiger_contactdetails", "contatti")->setRelation("account", "contatti", "+", "accountid", "accountid");
                $struct->addTable("vtiger_contactscf", "contatticf")->setRelation("contatti", "contatticf", "!", "contactid", "contactid");
                $struct->addTable("vtiger_crmentity", "contatti_ent")->setRelation("contatti", "contatti_ent", "!", "contactid", "crmid");
                $struct->setEntity("CONTATTI", array("contatti", "contatticf", "contatti_ent"));

                $struct->addField("firstname", "contatti", "Nome contatto")->addField("lastname", "contatti", "Cognome contatto")
                        ->addCalculatedField("CONCAT_WS(' ',##1##,##2##)", array("Nome contatto", "Cognome contatto"), "Contatto", true);
                $struct->addField("phone", "contatti", "tel")->addField("mobile", "contatti", "cell");
                $struct->addFilterToTable("contatti_ent", "##deleted## = '0'");
                $struct->addFilterToTable("project_ent", "##deleted## = '0'");
                $struct->addFilterToTable("account_ent", "##deleted## = '0'");
                $struct->setFieldsEditable(array(
                    "Nome progetto", "Tipo progetto", "Id assegnato a"
                ));

                break;
            case 'progetti_so':
                $struct = new \WolfMVC\Smm\Smmstructure();
                $struct->setDefaultDb("vtiger");
                $struct->addTable("vtiger_project", "project")
//                        ->addTable("vtiger_crmentity", "project_ent")
                        ->addTable("vtiger_projectcf", "projectcf")
//                        ->setRelation("project", "project_ent", "!", "projectid", "crmid")
                        ->setRelation("project", "projectcf", "!", "projectid", "projectid");
//                        ->setEntity("PROJECT", array("project", "projectcf", "project_ent"));

                $struct->addField("projectname", "project", "Nome progetto")->addField("cf_691", "projectcf", "Id SO");

                $struct->addTable("vtiger_salesorder", "so")
                        ->addTable("vtiger_salesordercf", "socf")
                        ->addTable("vtiger_crmentity", "so_ent")
                        ->addTable("vtiger_sobillads", "sostreet")
                        ->setRelation("projectcf", "so", "!", "cf_691", "salesorderid")
                        ->setRelation("so", "so_ent", "!", "salesorderid", "crmid")
                        ->setRelation("so", "socf", "!", "salesorderid", "salesorderid")
                        ->setEntity("SO", array("so", "socf", "so_ent"));
                $struct->setRelation("so", "sostreet", "!", "salesorderid", "sobilladdressid");
                $struct->addField("total", "so", "Totale progetto ii");
                $struct->addField("subtotal", "so", "Totale progetto ie")->addField("salesorder_no", "so", "Numero SO");
                $struct->addField("bill_city", "sostreet", "Citta")->addField("bill_code", "sostreet", "Cap");
                $struct->addField("bill_state", "sostreet", "Provincia")->addField("bill_street", "sostreet", "Indirizzo");
                $struct->addFilterToTable("so_ent", "##deleted## = '0'");

                break;
            default :
                return parent::getModelStructure($modelname);
        }
        return $struct;
    }

}
