<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

use WolfMVC\Controller as Controller;
use WolfMVC\Registry as Registry;
use WolfMVC\RequestMethods as RequestMethods;
use WolfMVC\Template\Component\Formcomponent as FC;

class Banche extends Controller {

    protected $_conf;

    public function __construct($options = array()) {
        parent::__construct($options);
        $database = \WolfMVC\Registry::get("database_vtiger");
        $database->connect();
    }

    /**
     * @protected
     */
    public function script_including() {

        $reg = Registry::get("module_incassi");
        $this->_conf = parse_ini_file($reg["conf"]);
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/utils.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/jquery.js\"></script>";
    }

    /**
     * @before script_including
     */
    public function index() {

//questa istruzione dovrà dipendere dalla configurazione
        $view = $this->getActionView();
        echo "Questo &eacute; il metodo index del sistema Banche.<br>"
        . "Da qui &eacute; possibile raggiungere quasi tutte le schermate della gestione degli incassi.<br>";
        echo "Quando sar&agrave; attivata la modalit&agrave; layout questa pagina sar&agrave; molto pi&ugrave; bella.<br>";
        echo "<br> Le opzioni disponibili sono <br>";
        echo "<ul><li>Inserimento nuova banca</li>";
        echo "<li>Visualizzazione elenco banche</li>";
        echo "</ul>";
        $view->set("insert_path", SITE_PATH . \WolfMVC\Registry::get("router")->getController() . "/insert/1");
//        $view->set("gest_path", SITE_PATH . \WolfMVC\Registry::get("router")->getController() . "/gest");
//        $view->set("amm_path", SITE_PATH . \WolfMVC\Registry::get("router")->getController() . "/amm");
    }

    /**
     * @before script_including
     */
    public function insert() {
        $view = $this->getActionView();
        $form = "";
        if (isset($this->_parameters[0])) {
            if (($this->_parameters[0] == 4) || ($this->_parameters[0] == "4")) { //salvataggio
                $data = array(
                  "amount" => RequestMethods::post("amount"),
                  "createdate" => date("Y-m-d"),
                  "editdate" => date("Y-m-d"),
                  "type" => RequestMethods::post("type"),
                  "description" => RequestMethods::post("description"),
                  "accountid" => RequestMethods::post("accountid"),
                  "state" => RequestMethods::post("state"),
                  "deleted" => 0,
                  "ref" => RequestMethods::post("rif"),
                  "bankid" => RequestMethods::post("bankid"),
                  "emissiondate" => RequestMethods::post("emission_date"),
                  "ourbankid" => RequestMethods::post("our_bankid"),
                  "table" => "external_collections"
                );
                $incasso = new Incasso($data);
                $incasso->save();
                $view->set("success", true);
                $retrieve = array();
                foreach ($data as $key => $value) {
                    if ($key == "table") {
//                        $retrieve["table"] = "external_collections";
                    }
//                        
                    else
                        $retrieve[$key . " = ?"] = $value;
                }
                $incasso2 = Incasso::first($retrieve, array('*'), null, null, "external_collections");
                ob_start();
                echo "<br>Dato inserito:<pre>";
                print_r($incasso2->basic_show());
                echo "</pre>";
                $feedback = ob_get_contents();
                ob_end_clean();
                $view->set("feedback", $feedback);
            }
            else {
                $stepform = new Controller\Component\Stepform();
                $stepform->setNumberofsteps(1);
                $stepform->setPassedparameters($this->_parameters);
                $stepform->setMethod("POST");
                $sf2 = new WolfMVC\Template\Component\Simpleform();
                $sf2->setFormlabel("Dati della banca");
                $sf2->add(new FC\Text(), true)->setLabel("ABI:")->setRequired(true)->setName("bankabi");
                $sf2->setSensible();
                $sf2->add(new FC\Text(), true)->setLabel("CAB:")->setRequired(true)->setName("bankcab");
                $sf2->setSensible();
                $sf2->add("br");
                $sf2->add(new FC\Text(), true)->setLabel("Nome banca:")->setRequired(true)->setName("bankname");
                $sf2->setSensible();
                $sf2->add("br");
                $sf2->add(new FC\Textarea(), true)->setLabel("Descrizione banca:")->setRequired(false)->setName("bankdescription");
                $sf2->setSensible();
                $sf2->add("br");
                $sf2->add(new FC\Text(), true)->setLabel("Indirizzo:")->setRequired(true)->setName("bankstreet");
                $sf2->setSensible();
                $sf2->add(new FC\Text(), true)->setLabel("CAP:")->setRequired(true)->setName("bankcode");
                $sf2->setSensible();
                $sf2->add("br");
                $sf2->add(new FC\Text(), true)->setLabel("Localit&agrave;:")->setRequired(true)->setName("bankcity");
                $sf2->setSensible();
                $sf2->add(new FC\Text(), true)->setLabel("Provincia (es. MI):")->setRequired(true)->setName("bankprovince");
                $sf2->setSensible();
                $sf2->add("br");
                
                $stepform->setForms(array($sf2));
                $view->set("form", $stepform->make($form));
//                ob_start();
//                echo "<pre>";
//                print_r($_REQUEST);
//                echo "</pre>";
//                $view->set("data", ob_get_contents());
//                ob_end_clean();
            }
        }
//        $view->set("form", $stepform->describe());
    }

    /**
     * @before script_including
     */
    public function gest() {




        echo "Questo &eacute; il metodo gest del sistema di Incassi.<br>"
        . "Presenta la schermata di gestione degli incassi<br>";
        $view = $this->getActionView();
        $view->set("title", "<h1>Questo &eacute; il metodo index del controllo Home</h1>");

        $dd = new \WolfMVC\Model\Datadepict();
        $dd->addField(0, "id", "cid", "external_collections", "a")
          ->addField(0, "amount", "Importo", "external_collections", "a")
          ->addField(1, "type", "Tipo", "external_collections", "a", "0,ASS,BON,CON")
          ->addField(2, "accountname", "Cliente", "vtiger_account", "b", "accountid", "external_collections", "accountid")
          ->addField(0, "accountid", "accountid", "external_collections", "a")
          ->addField(4, "statename", "Stato", "external_collections_state", "c", "state", "external_collections", "state")
          ->addField(0, "ref", "Riferimento", "external_collections", "a")
          ->addField(2, "bankname", "Banca", "external_banks", "d", "bankid", "external_collections", "bankid")
          ->addField(0, "emissiondate", "Data emissione", "external_collections", "a")
          ->addField(0, "receiptdate", "Data ricezione", "external_collections", "a")
          ->addField(0, "depositdate", "Data versamento", "external_collections", "a")
          ->addField(0, "valuedate", "Data valuta", "external_collections", "a")
          ->addField(4, "ourbankname", "Ns. Banca", "external_ourbank", "e", "ourbankid", "external_collections", "ourbankid")
        ;
        $dd->setWhere(array("a.deleted = '0'"));
        $dd->getAllFromDb();
        $tab = new \WolfMVC\Template\Component\Datadisplay\Tabular($dd, "gestincassi");
        $tab->setSearchurl("http://54.213.213.176/tsnw/application/services/request_values_for_edit.php?idop=###0###");
        $tab->setEditurl("http://54.213.213.176/tsnw/application/services/edit_value.php?idop=###0###&idrecord=###1###&datum=###2###");
        $tab->setDeleteurl("http://54.213.213.176/tsnw/application/services/delete.php?idop=###0###&idrecord=###1###");

        $cols = array(
          "amount as is",
          "type as fixedpicklist as (0,ASS,BON,CON)",
          "accountname as link as " . $this->_conf["controller.incassi.gest.vtlinkforaccountdetails"] . " WITH " . $this->_conf["controller.incassi.gest.vtlinkforaccountdetailsparams"],
          "statename as is",
          "ref as link",
          "bankname as link",
          "emissiondate as is",
          "receiptdate as is",
          "depositdate as is",
          "valuedate as is",
          "ourbankname as is"
//          "Edit as op",
//          "Delete as op"
        );

        $tab->getColsFromModel($cols);
        $tab->setFieldOperation('emissiondate', 'edit', array(
          'event' => "onclick",
          'idop' => "1",
          'ispicklist' => false,
          'type' => "date",
          'index' => "{{a.accountid}}"
        ));
        $tab->setFieldOperation('receiptdate', 'edit', array(
          'event' => "onclick",
          'idop' => "2",
          'ispicklist' => false,
          'type' => "date",
          'index' => "{{a.accountid}}"
        ));
        $tab->setFieldOperation('depositdate', 'edit', array(
          'event' => "onclick",
          'idop' => "3",
          'ispicklist' => false,
          'type' => "date",
          'index' => "{{a.accountid}}"
        ));
        $tab->setFieldOperation('valuedate', 'edit', array(
          'event' => "onclick",
          'idop' => "4",
          'ispicklist' => false,
          'type' => "date",
          'index' => "{{a.accountid}}"
        ));
        $tab->setFieldOperation('ourbankname', 'edit', array(
          'event' => "onclick",
          'idop' => "5",
          'ispicklist' => true,
          'type' => "select",
          'secondaryid' => '2',
          'index' => "{{a.accountid}}"
        ));
        $tab->setFieldOperation('statename', 'editwithrestriction', array(
          'event' => "onclick",
          'idop' => "6", //
          'ispicklist' => true,
          'type' => "select",
          'secondaryid' => '1', //
          'index' => "{{a.accountid}}",
          'restriction' => array(
            1 => array(
              2 => array("{{receiptdate}} != ''", "{{receiptdate}} != '0000-00-00'"),
              3 => array("true")
            ),
            2 => array(
              3 => array("true"),
              4 => array("{{depositdate}} != ''", "{{depositdate}} != '0000-00-00'")
            ),
            3 => array(
              2 => array("true")
            )
          )
        ));
        $tab->setOperation("elimina", "del", 1);
        $tab->setOperation("dettagli", "SO", 2);
        $tab->setServicesforrecordop(
          array(
            "elimina" => array("http://54.213.213.176/tsnw/application/services/delete.php?idop=%s&idrecord=%s", array(1, "{{cid}}")),
            "dettagli" => array("http://54.213.213.176/tsnw/public/incassi/sodetails/1/%s/%s", array("{{cid}}", "{{accountid}}"), "red")
          )
        );
        $tab->getDataFromModel();
        $tab->showIndex(true)->setIndexFromModel(true, "cid");
        $view->set("data", $tab->make(""));
    }

    /**
     * @before script_including
     */
    public function amm() {
        echo "Questo &eacute; il metodo amm del sistema di Incassi.<br>"
        . "Presenta il form di immissione di un nuovo incasso<br>";
    }

    public function sodetails() {

        if (isset($this->_parameters[1])) {
            $cid = $this->_parameters[1];
            $view = $this->getActionView();

            $view->set("title", "<h1>Dettagli Incasso per SO</h1>");
            $view->set("back", "<form action=\"http://54.213.213.176/tsnw/public/incassi/gest\"><button type=\"submit\">Indietro</button></form>");

            $dd = new \WolfMVC\Model\Datadepict();
            $dd->addField(0, "id", "id", "external_collections_so", "a")
              ->addField(0, "idcollection", "cid", "external_collections_so", "a")
              ->addField(0, "idso", "idso", "external_collections_so", "a")
              ->addField(2, "subject", "Soggetto", "vtiger_salesorder", "b", "salesorderid", "external_collections_so", "idso")
              ->addField(0, "amount", "Ammontare", "external_collections_so", "a")
            ;

            $dd->setWhere(array("a.idcollection = '{$cid}'"));
            $dd->getAllFromDb();
            $tab = new \WolfMVC\Template\Component\Datadisplay\Tabular($dd, "sodetails");
//            $tab->setSearchurl("http://54.213.213.176/tsnw/application/services/request_values_for_edit.php?idop=###0###");
//            $tab->setEditurl("http://54.213.213.176/tsnw/application/services/edit_value.php?idop=###0###&idrecord=###1###&datum=###2###");
//            $tab->setDeleteurl("http://54.213.213.176/tsnw/application/services/delete.php?idop=###0###&idrecord=###1###");

            $cols = array(
              "subject as link as " . "http://54.213.213.176/vtigercrm/index.php?module=SalesOrder&action=DetailView&record=" . "{{idso}}",
              "amount as is",
            );

            $tab->getColsFromModel($cols);

            $tab->setOperation("elimina", "<button type=\"button\">Del</button", 2);
            $tab->getDataFromModel();
            $tab->showIndex(true)->setIndexFromModel(true, "id");
            $view->set("data", $tab->make(""));
        }

        if (isset($this->_parameters[0])) {
            if (($this->_parameters[0] == 2) || ($this->_parameters[0] == "2")) { //salvataggio
                $data = array(
                  "idcollection" => $this->_parameters[1],
                  "idso" => RequestMethods::post("idso"),
                  "amount" => RequestMethods::post("amount"),
                  "description" => RequestMethods::post("description"),
                  "table" => "external_collections_so"
                );
                $incassoso = new Incassoso($data);
                $incassoso->save();
                $view->set("success", true);
            }
            $stepform = new Controller\Component\Stepform();
            $stepform->setNumberofsteps(1);
            $stepform->setPassedparameters($this->_parameters);
            $stepform->setMethod("POST");
            $sf = new WolfMVC\Template\Component\Simpleform();
            $sf->setFormlabel("Dati del pagamento");
            $sf->add(new FC\Selectwithservice, true)->setName("idso")->setLabel("SO")->setRequired(true)
              ->setService('http://54.213.213.176/tsnw/application/services/request_sos_foraccount.php?accid=%s')
              ->setServiceparams(array($this->_parameters[2]));
            $sf->add("br");
            $sf->add(new FC\Number(), true)->setLabel("Ammontare:")->setRequired(true)->setName("amount")->setMin(1)->setStep(0.01);
            $sf->add("br");
            $sf->add("br");
            $sf->add(new FC\Textarea(), true)->setLabel("Note:")->setName("description");
            $stepform->setForms(array($sf));
            $view->set("form", $stepform->make(""));
        }
    }

}
