<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

use WolfMVC\Controller as Controller;
use WolfMVC\Registry as Registry;
use WolfMVC\RequestMethods as RequestMethods;
use WolfMVC\Template\Component\Formcomponent as FC;

class Incassi extends Controller {

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
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/angular.min.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ngbootstrap.min.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ng-ui-bootstrap-tpls-0.2.0.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ngsortable.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/core/data.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/core/filteredtable.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/core/themask.js\"></script>";
        $this->_system_js_including .="<link rel=\"stylesheet\" href=\"" . SITE_PATH . "css/core/filteredtable.css\">";
        $this->_system_js_including .="<link rel=\"stylesheet\" href=\"" . SITE_PATH . "css/core/splitSo.css\">";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/core/splitSo.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/core/angdad.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/core/tsnwclient.js\"></script>";
    }

    /**
     * @before script_including
     */
    public function index() {

//questa istruzione dovrà dipendere dalla configurazione
        $view = $this->getActionView();
        $view->set("actions", array(
//            "Inserimento nuovo incasso" => $this->pathTo($this->nameofthiscontroller(), "inserimento", array(1)),
            "Gestione incassi" => $this->pathTo($this->nameofthiscontroller(), "gestione", array())
//            "Amministrazione incassi" => $this->pathTo($this->nameofthiscontroller(), "amministrazione", array()),
//            "Anagrafica banche" => $this->pathTo($this->nameofthiscontroller(), "banche", array())
        ));
    }

    /**
     * @before script_including
     */
    public function insert() {
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("INCASSI" => "incassi", "Inserimento nuovo incasso" => "last")));
        $view = $this->getActionView();
        $form = "";
        if (isset($this->_parameters[0]))
        {
            if (($this->_parameters[0] == 4) || ($this->_parameters[0] == "4"))
            { //salvataggio
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
                    if ($key == "table")
                    {
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
            else
            {
                $stepform = new Controller\Component\Stepform();
                $stepform->setNumberofsteps(4);
                $stepform->setPassedparameters($this->_parameters);
                $stepform->setMethod("POST");
                $sf = new WolfMVC\Template\Component\Simpleformws();
                $sf->add(new FC\Search, true)->setName("accountname")->setLabel("Nome Azienda:")->setSize(80)->setRequired(true);
                $sf->setCompleting()->setSensible();
                $sf->add("br");
                $sf->add(new FC\Text, true)->setName("accountphone")->setLabel("Contatto Azienda:")->setSize(30);
                $sf->setCompleting();
                $sf->add(new FC\Text, true)->setName("accountstreet")->setLabel("Indirizzo Azienda:")->setSize(40);
                $sf->setCompleting();
                $sf->add("br");
                $sf->add(new FC\Text, true)->setName("accountvat")->setLabel("Partita IVA:")->setSize(30);
                $sf->setCompleting();
                $sf->add(new FC\Hidden(), true)->setName("accountid");
                $sf->setCompleting();
                $sf->setSensible();
                $sf->setFormlabel("Identificazione Azienda Cliente");
                $sf->setSearchfield(array(0));
                $sf->setSearchurl("http://54.213.213.176/tsnw/application/services/request_accounts.php?name=###0###");
                $sf->setSuggestboxsize(array(800, 600));

                $sf2 = new WolfMVC\Template\Component\Simpleform();
                $sf2->setFormlabel("Dati del pagamento");
                $sf2->add(new FC\Select(), true)->setName("type")->setLabel("Modalit&agrave; di pagamento")->setRequired(true)
                        ->addoption(new FC\Option(), true)->setValue("1")->setContent("Assegno")->up()
                        ->addoption(new FC\Option(), true)->setValue("2")->setContent("Bonifico")->up()
                        ->addoption(new FC\Option(), true)->setValue("3")->setContent("Contanti");
                $sf2->setSensible();
                $sf2->add(new FC\Date, true)->setLabel("Data emissione:")->setRequired(true)
                        ->setName("emission_date");
                $sf2->setSensible();
                $sf2->add("br");
                $sf2->add(new FC\Number(), true)->setLabel("Importo Pagato:")->setRequired(true)->setName("amount")->setMin(1)->setStep(0.01);
                $sf2->setSensible();
                $sf2->add(new FC\Select(), true)->setLabel("Stato:")->setName("state")
                        ->addoption(new FC\Option(), true)->setValue("1")->setContent("Emesso")->up();
                $sf2->setSensible();
                $sf3 = new WolfMVC\Template\Component\Simpleformws();
                $sf3->setFormlabel("Dettagli sul pagamento");
                if ((isset($_REQUEST['type'])) && ($_REQUEST['type'] == "3"))
                {
                    $sf3->add(new FC\Label(), true)->setContent("Per il pagamento in contanti non sono necessari ulteriori dati sul pagamento");
                    $sf3->setSearchfield(array(0));
                    $sf->setSearchurl("http://54.213.213.176/tsnw/application/services/request_accounts.php?name=###0###");
                }
                else
                {
                    $sf3->add(new FC\Text(), true)->setName("rif")->setLabel("Riferimento")->setPlaceholder("Num. assegno o conto")
                            ->setSize(40);
                    $sf3->setSensible();
                    $sf3->add("br");
                    $sf3->add(new FC\Search(), true)->setName("bankname")->setLabel("Banca:")->setPlaceholder("Nome della banca")
                            ->setSize(80);
                    $sf3->setSensible();
                    $sf3->setCompleting();
                    $sf3->add("br");
                    $sf3->add(new FC\Search(), true)->setName("bankabi")->setLabel("ABI:")->setPlaceholder("00000")
                            ->setSize(30);
                    $sf3->setCompleting();
                    $sf3->add(new FC\Search(), true)->setName("bankcab")->setLabel("CAB:")->setPlaceholder("00000")
                            ->setSize(30);
                    $sf3->setCompleting();
                    $sf3->add("br");
                    $sf3->add(new FC\Text(), true)->setName("bankstreet")->setLabel("Indirizzo:")->setSize(50);
                    $sf3->setCompleting();
                    $sf3->add(new FC\Text(), true)->setName("bankcode")->setLabel("CAP:")->setSize(20);
                    $sf3->setCompleting();
                    $sf3->add("br");
                    $sf3->add(new FC\Text(), true)->setName("bankcity")->setLabel("Citt&agrave;:")->setSize(40);
                    $sf3->setCompleting();
                    $sf3->add(new FC\Text(), true)->setName("bankprovince")->setLabel("Provincia:")->setSize(40);
                    $sf3->setCompleting();
                    $sf3->add("br");
                    $sf3->add(new FC\Text(), true)->setName("bankdescription")->setLabel("Descrizione:")->setSize(80);
                    $sf3->setCompleting();
                    $sf3->add(new FC\Hidden(), true)->setName("bankid");
                    $sf3->setCompleting();
                    $sf3->setSensible();
                    $sf3->setSearchfield(array(2, 4, 5));

                    $sf3->setSearchurl("http://54.213.213.176/tsnw/application/services/request_banks.php?bank=###0###&abi=###1###&cab=###2###");
                    $sf3->setSuggestboxsize(array(800, 600));
                }
                $stepform->setForms(array($sf, $sf2, $sf3));
                $view->set("form", $stepform->make($form));
            }
        }
    }

    public function gestione() {
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("INCASSI" => "incassi", "Gestione incassi" => "last")));
        $view = $this->getActionView();
        $view->set("root", '$root');
        $view->set("index", '$index');
    }

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

    public function gest2() {
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
        $dd->setWhere(array("a.deleted = '0' AND a.id > '900'"));
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

        if (isset($this->_parameters[1]))
        {
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

        if (isset($this->_parameters[0]))
        {
            if (($this->_parameters[0] == 2) || ($this->_parameters[0] == "2"))
            { //salvataggio
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

    public function ws___gestione() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $config = array(
            "services" => array(
                "getCollections" => SITE_PATH . $this->nameofthiscontroller() . "/getCollections.ws",
                "getSuggerimentiCliente" => SITE_PATH . $this->nameofthiscontroller() . "/getSuggerimentiCliente.ws",
                "getSuggerimentiBanca" => SITE_PATH . $this->nameofthiscontroller() . "/getSuggerimentiBanca.ws",
                "insertCollection" => SITE_PATH . $this->nameofthiscontroller() . "/insertCollection.ws",
                "insertBank" => SITE_PATH . $this->nameofthiscontroller() . "/insertBank.ws",
                "deleteCollection" => SITE_PATH . $this->nameofthiscontroller() . "/deleteCollection.ws",
                "getSo" => SITE_PATH . $this->nameofthiscontroller() . "/getSo.ws",
                "getCollSo" => SITE_PATH . $this->nameofthiscontroller() . "/getCollSo.ws",
                "col2So" => SITE_PATH . $this->nameofthiscontroller() . "/col2So.ws",
                "changeStatus" => SITE_PATH . $this->nameofthiscontroller() . "/changeStatus.ws",
                "changeOurBank" => SITE_PATH . $this->nameofthiscontroller() . "/changeOurBank.ws",
                "changeDates" => SITE_PATH . $this->nameofthiscontroller() . "/changeDates.ws",
                "toDistinta" => SITE_PATH . $this->nameofthiscontroller() . "/toDistinta.ws",
                "toVersato" => SITE_PATH . $this->nameofthiscontroller() . "/toVersato.ws"
            )
        );

        echo json_encode($config);
        return;
    }

    public function ws___getCollections() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $db = \WolfMVC\Registry::get("database_vtiger");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $link->set_charset("utf8");
        $sql = "SELECT a.id as 'N', a.amount as 'Ammontare', a.type as 'Tipo', a.description as 'Descrizione', a.accountid, "
                . "c.accountname as 'Cliente', c.account_no as 'Numero cliente', "
                . "a.state as statusid, b.statename as 'Stato', "
                . "a.bankid, e.bankname as 'Banca', a.emissiondate as 'Data emissione', "
                . "a.receiptdate as 'Data ricezione', a.depositdate as 'Data versamento', a.valuedate as 'Data valuta', "
                . "a.ourbankid, d.ourbankname as 'Nostra banca', "
                . "a.ref as 'Riferimento' "
                . "FROM external_collections a "
                . "LEFT JOIN external_collections_state b ON (a.state = b.state) "
                . "LEFT JOIN vtiger_account c ON (a.accountid = c.accountid) "
                . "LEFT JOIN external_ourbank d ON (a.ourbankid = d.ourbankid) "
                . "LEFT JOIN external_banks e ON (a.bankid = e.bankid) "
                . "WHERE a.deleted = '0'";
//        echo $sql;
//        return;
        if ($link->connect_errno)
        {
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $result = $link->query($sql);
        if ($result)
        {
            echo json_encode($result->fetch_all(MYSQLI_ASSOC));
            return;
        }
        else
        {
            echo json_encode("An error occurred retrieving collections: " . $link->error);
            return;
        }
    }

    public function ws___getCollectionsDeferred() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $headers = getallheaders();
        $deferred = (isset($headers["X-DeferredResponseReady"]) && $headers["X-DeferredResponseReady"]);
        $count = (isset($headers["X-DeferredResponse-Count"]) && $headers["X-DeferredResponse-Count"]);
        $limit = (isset($headers["X-DeferredResponse-Limit"]) && $headers["X-DeferredResponse-Limit"]);
        if ($limit)
        {
            $limitLimit = explode("|", $headers["X-DeferredResponse-Limit"]);
        }
        $db = \WolfMVC\Registry::get("database_vtiger");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $link->set_charset("utf8");
        $mainquery = "SELECT a.id as 'N', a.amount as 'Ammontare', a.type as 'Tipo', a.description as 'Descrizione', a.accountid, "
                . "c.accountname as 'Cliente', c.account_no as 'Numero cliente', "
                . "a.state as statusid, b.statename as 'Stato', "
                . "a.bankid, e.bankname as 'Banca', a.emissiondate as 'Data emissione', "
                . "a.receiptdate as 'Data ricezione', a.depositdate as 'Data versamento', a.valuedate as 'Data valuta', "
                . "a.ourbankid, d.ourbankname as 'Nostra banca', "
                . "a.ref as 'Riferimento' "
                . "FROM external_collections a "
                . "LEFT JOIN external_collections_state b ON (a.state = b.state) "
                . "LEFT JOIN vtiger_account c ON (a.accountid = c.accountid) "
                . "LEFT JOIN external_ourbank d ON (a.ourbankid = d.ourbankid) "
                . "LEFT JOIN external_banks e ON (a.bankid = e.bankid) "
                . "WHERE a.deleted = '0'";
        $countquery = "SELECT COUNT(*) as 'count' FROM (" . $mainquery . ") pippo";
        $limitquery = $mainquery . " LIMIT %s,%s";
        if ($link->connect_errno)
        {
            echo json_encode("Error occurred in db connection!");
            return;
        }
        if ($count)
        {
            $sql = $countquery;
        }
        else if ($limit)
        {
            $sql = sprintf($limitquery, $limitLimit[0], $limitLimit[1]);
        }
        else
        {
            $sql = $mainquery;
        }
        $result = $link->query($sql);
        if ($result)
        {
            echo json_encode($result->fetch_all(MYSQLI_ASSOC));
            return;
        }
        else
        {
            echo json_encode("An error occurred retrieving collections: " . $link->error);
            return;
        }
    }

    public function ws___getSo() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $ret = new stdClass();
        if (!isset($_POST["data"]))
        {
            $ret->result = false;
            $ret->error = "Missing or invalid parameters " . print_r($_POST, true);
            echo json_encode($ret);
            return;
        }
        $data = json_decode($_POST["data"], true);
        if (!isset($data["accountid"]))
        {
            $ret->result = false;
            $ret->error = "Missing or invalid parameter accountid";
            echo json_encode($ret);
            return;
        }
        $accountid = $this->anti_injection($data["accountid"]);
        $db = \WolfMVC\Registry::get("database_vtiger");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $link->set_charset("utf8");
        $sql = "SELECT " .
                "a.salesorderid as 'idSo', a.subject as 'soggettoSo', " .
                "a.salesorder_no as 'numSo', " .
                "a.total as 'ammontareSo', " .
                "a.sostatus as 'statoSo', " .
                "a.duedate as 'scadenzaSo', " .
                "CONCAT('{',GROUP_CONCAT(concat('\"',c.invoiceid,'\": ','{','\"idInv\": \"',c.invoiceid,'\", \"soggettoInv\": \"',c.subject,'\", \"dateInv\": \"', " .
                "c.invoicedate,'\", \"ammontareInv\": \"', c.total,'\", \"numeroInv\": \"', c.invoice_no, '\"}')),'}') as 'fatture' " .
                "FROM " .
                "vtiger_salesorder a " .
                "LEFT JOIN vtiger_crmentity b ON (b.crmid = a.salesorderid) " .
                "LEFT JOIN vtiger_invoice c ON (c.salesorderid = a.salesorderid) " .
                "LEFT JOIN vtiger_crmentity d ON (d.crmid = c.invoiceid) " .
                "WHERE a.accountid = '{$accountid}' " .
                "AND b.deleted = '0' " .
                "AND (d.deleted = '0' or isnull(d.deleted))" .
                "GROUP BY a.salesorderid";
//        echo $sql;
//        return;


        if ($link->connect_errno)
        {
            $ret->result = false;
            $ret->error = "Error occurred in db connection!";
            echo json_encode($ret);
            return;
        }
        $result = $link->query($sql);
        if ($result)
        {
            $ret->result = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($ret);
            return;
        }
        else
        {
            $ret->result = false;
            $ret->error = "An error occurred retrieving collections: " . $link->error;
            echo json_encode($ret);
            return;
        }
    }

    public function ws___col2So() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $ret = new stdClass();
        if (!isset($_POST["data"]))
        {
            $ret->result = false;
            $ret->error = "Missing or invalid parameters " . print_r($_POST, true);
            echo json_encode($ret);
            return;
        }
        $data = json_decode($_POST["data"], true);
        if (!isset($data["idCol"]))
        {
            $ret->result = false;
            $ret->error = "Missing or invalid parameter collection id";
            echo json_encode($ret);
            return;
        }
        $idCol = $this->anti_injection($data["idCol"]);
        if (!isset($data["idSo"]))
        {
            $ret->result = false;
            $ret->error = "Missing or invalid parameter salesorder id";
            echo json_encode($ret);
            return;
        }
        $idSo = $this->anti_injection($data["idSo"]);
        if (!isset($data["amount"]))
        {
            $ret->result = false;
            $ret->error = "Missing or invalid parameter amount";
            echo json_encode($ret);
            return;
        }
        $amount = $this->anti_injection($data["amount"]);
        $db = \WolfMVC\Registry::get("database_vtiger");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $link->set_charset("utf8");
        $sql = "INSERT INTO external_collections_so (idcollection, idso, amount) VALUES ('{$idCol}', '{$idSo}', '{$amount}')";
//        echo $sql;
//        return;


        if ($link->connect_errno)
        {
            $ret->result = false;
            $ret->error = "Error occurred in db connection!";
            echo json_encode($ret);
            return;
        }
        $result = $link->query($sql);
        if ($result)
        {
            $ret->result = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($ret);
            return;
        }
        else
        {
            $ret->result = false;
            $ret->error = "An error occurred assigning collections: " . $link->error;
            echo json_encode($ret);
            return;
        }
    }

    public function ws___changeStatus() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $ret = new stdClass();
        if (!isset($_POST["data"]))
        {
            $ret->result = false;
            $ret->error = "Missing or invalid parameters " . print_r($_POST, true);
            echo json_encode($ret);
            return;
        }
        $data = json_decode($_POST["data"], true);
        if (!isset($data["idCol"]))
        {
            $ret->result = false;
            $ret->error = "Missing or invalid parameter collection id";
            echo json_encode($ret);
            return;
        }
        $idCol = $this->anti_injection($data["idCol"]);
        if (!isset($data["newstatus"]))
        {
            $ret->result = false;
            $ret->error = "Missing or invalid parameter new status";
            echo json_encode($ret);
            return;
        }
        $newstatus = $this->anti_injection($data["newstatus"]);
        $db = \WolfMVC\Registry::get("database_vtiger");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $link->set_charset("utf8");
        $sql = "UPDATE external_collections set state='{$newstatus}' WHERE id='{$idCol}'";
//        echo $sql;
//        return;


        if ($link->connect_errno)
        {
            $ret->result = false;
            $ret->error = "Error occurred in db connection!";
            echo json_encode($ret);
            return;
        }
        $result = $link->query($sql);
        if ($result)
        {
            $ret->result = true;
            echo json_encode($ret);
            return;
        }
        else
        {
            $ret->result = false;
            $ret->error = "An error occurred updating collection: " . $link->error;
            echo json_encode($ret);
            return;
        }
    }

    public function ws___toDistinta() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $ret = new stdClass();
        if (!isset($_POST["data"]))
        {
            $ret->result = false;
            $ret->error = "Missing or invalid parameters " . print_r($_POST, true);
            echo json_encode($ret);
            return;
        }
        $data = json_decode($_POST["data"], true);
        $in = array();
        foreach ($data as $k => $v) {
            $in[] = "'" . $this->anti_injection($v) . "'";
        }
        $db = \WolfMVC\Registry::get("database_vtiger");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $link->set_charset("utf8");
        $sql = "UPDATE external_collections set state='12' WHERE id IN (" . join(",", $in) . ")";


        if ($link->connect_errno)
        {
            $ret->result = false;
            $ret->error = "Error occurred in db connection!";
            echo json_encode($ret);
            return;
        }
        $result = $link->query($sql);
        if ($result)
        {
            $ret->result = true;
            echo json_encode($ret);
            return;
        }
        else
        {
            $ret->result = false;
            $ret->error = "An error occurred updating collection: " . $link->error . " Query was " + $sql;
            ;
            echo json_encode($ret);
            return;
        }
    }

    public function ws___toVersato() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $ret = new stdClass();
        if (!isset($_POST["data"]))
        {
            $ret->result = false;
            $ret->error = "Missing or invalid parameters " . print_r($_POST, true);
            echo json_encode($ret);
            return;
        }
        $data = json_decode($_POST["data"], true);
        $in = array();
        foreach ($data as $k => $v) {
            $in[] = "'" . $this->anti_injection($v) . "'";
        }
        $db = \WolfMVC\Registry::get("database_vtiger");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $link->set_charset("utf8");
        $sql = "UPDATE external_collections set state='4' WHERE id IN (" . join(",", $in) . ")";


        if ($link->connect_errno)
        {
            $ret->result = false;
            $ret->error = "Error occurred in db connection!";
            echo json_encode($ret);
            return;
        }
        $result = $link->query($sql);
        if ($result)
        {
            $ret->result = true;
            echo json_encode($ret);
            return;
        }
        else
        {
            $ret->result = false;
            $ret->error = "An error occurred updating collection: " . $link->error . " Query was " + $sql;
            ;
            echo json_encode($ret);
            return;
        }
    }

    public function ws___changeOurBank() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $ret = new stdClass();
        if (!isset($_POST["data"]))
        {
            $ret->result = false;
            $ret->error = "Missing or invalid parameters " . print_r($_POST, true);
            echo json_encode($ret);
            return;
        }
        $data = json_decode($_POST["data"], true);
        if (!isset($data["idCol"]))
        {
            $ret->result = false;
            $ret->error = "Missing or invalid parameter collection id";
            echo json_encode($ret);
            return;
        }
        $idCol = $this->anti_injection($data["idCol"]);
        if (!isset($data["ourbankid"]))
        {
            $ret->result = false;
            $ret->error = "Missing or invalid parameter ourbankid";
            echo json_encode($ret);
            return;
        }
        $ourbankid = $this->anti_injection($data["ourbankid"]);
        $db = \WolfMVC\Registry::get("database_vtiger");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $link->set_charset("utf8");
        $sql = "UPDATE external_collections set ourbankid='{$ourbankid}' WHERE id='{$idCol}'";
//        echo $sql;
//        return;


        if ($link->connect_errno)
        {
            $ret->result = false;
            $ret->error = "Error occurred in db connection!";
            echo json_encode($ret);
            return;
        }
        $result = $link->query($sql);
        if ($result)
        {
            $ret->result = true;
            echo json_encode($ret);
            return;
        }
        else
        {
            $ret->result = false;
            $ret->error = "An error occurred updating collection: " . $link->error;
            echo json_encode($ret);
            return;
        }
    }

    public function ws___changeDates() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $ret = new stdClass();
        if (!isset($_POST["data"]))
        {
            $ret->result = false;
            $ret->error = "Missing or invalid parameters " . print_r($_POST, true);
            echo json_encode($ret);
            return;
        }
        $data = json_decode($_POST["data"], true);
        if (!isset($data["idCol"]))
        {
            $ret->result = false;
            $ret->error = "Missing or invalid parameter collection id";
            echo json_encode($ret);
            return;
        }
        $idCol = $this->anti_injection($data["idCol"]);
        if (!isset($data["emissiond"]))
        {
            $ret->result = false;
            $ret->error = "Missing or invalid parameter emissiond";
            echo json_encode($ret);
            return;
        }
        $emissiondate = $this->anti_injection($data["emissiond"]);
        if (!isset($data["receiptd"]))
        {
            $ret->result = false;
            $ret->error = "Missing or invalid parameter receiptd";
            echo json_encode($ret);
            return;
        }
        $receiptdate = $this->anti_injection($data["receiptd"]);
        if (!isset($data["depositd"]))
        {
            $ret->result = false;
            $ret->error = "Missing or invalid parameter depositd";
            echo json_encode($ret);
            return;
        }
        $depositdate = $this->anti_injection($data["depositd"]);
        if (!isset($data["valued"]))
        {
            $ret->result = false;
            $ret->error = "Missing or invalid parameter valued";
            echo json_encode($ret);
            return;
        }
        $valuedate = $this->anti_injection($data["valued"]);
        $db = \WolfMVC\Registry::get("database_vtiger");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $link->set_charset("utf8");
        $sql = "UPDATE external_collections set emissiondate='{$emissiondate}', "
                . " receiptdate='{$receiptdate}', depositdate='{$depositdate}', valuedate='{$valuedate}' WHERE id='{$idCol}'";
//        echo $sql;
//        return;

        if ($link->connect_errno)
        {
            $ret->result = false;
            $ret->error = "Error occurred in db connection!";
            echo json_encode($ret);
            return;
        }
        $result = $link->query($sql);
        if ($result)
        {
            $ret->result = true;
            echo json_encode($ret);
            return;
        }
        else
        {
            $ret->result = false;
            $ret->error = "An error occurred updating collection: " . $link->error;
            echo json_encode($ret);
            return;
        }
    }

    public function ws___getCollSo() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $ret = new stdClass();
        if (!isset($_POST["data"]))
        {
            $ret->result = false;
            $ret->error = "Missing or invalid parameters " . print_r($_POST, true);
            echo json_encode($ret);
            return;
        }
        $data = json_decode($_POST["data"], true);
        if (!isset($data["accountid"]))
        {
            $ret->result = false;
            $ret->error = "Missing or invalid parameter accountid";
            echo json_encode($ret);
            return;
        }
        $accountid = $this->anti_injection($data["accountid"]);
        $db = \WolfMVC\Registry::get("database_vtiger");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $link->set_charset("utf8");
        $sql = "SELECT " .
                "a.id as 'idCol', a.amount as 'ammontareCol', a.type as 'typeCol', " .
                "a.state as 'statusCol', a.ref as 'refCol', a.emissiondate as 'dateCol', " .
                "b.idso as 'col2So', b.amount as 'col2SoAmount' " .
                "FROM external_collections a " .
                "LEFT JOIN external_collections_so b ON (a.id = b.idcollection) " .
                "WHERE " .
                "a.accountid = '{$accountid}' " .
                "AND " .
                "a.deleted = '0' "
                . "ORDER BY a.emissiondate";

        if ($link->connect_errno)
        {
            $ret->result = false;
            $ret->error = "Error occurred in db connection!";
            echo json_encode($ret);
            return;
        }
        $result = $link->query($sql);
        if ($result)
        {
            $ret->result = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($ret);
            return;
        }
        else
        {
            $ret->result = false;
            $ret->error = "An error occurred retrieving collections: " . $link->error;
            echo json_encode($ret);
            return;
        }
    }

    public function ws___getSuggerimentiCliente() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        if (!isset($_POST["data"]))
        {
            echo json_encode("Missing or invalid parameters " . print_r($_POST, true));
            return;
        }
        $data = json_decode($_POST["data"], true);
        if (!isset($data["partialname"]))
        {
            echo json_encode("Missing or invalid parameters partial name" . print_r($_POST, true));
            return;
        }
        $ret = new stdClass();

        $partialname = $data["partialname"];
        header('Content-type: application/json');
        $db = \WolfMVC\Registry::get("database_vtiger");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $link->set_charset("utf8");
        $sql = "SELECT accountid,account_no,accountname FROM vtiger_account"
                . " LEFT JOIN vtiger_crmentity ON (accountid = crmid)"
                . " WHERE deleted = '0' AND accountname LIKE '%{$partialname}%'"
                . " LIMIT 0,10";
//        echo $sql;
//        return;
        if ($link->connect_errno)
        {
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $result = $link->query($sql);
        if ($result)
        {
            $ret->result = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($ret);
            return;
        }
        else
        {
            echo json_encode("An error occurred retrieving collections: " . $link->error);
            return;
        }
    }

    public function ws___getBankFromAbiCab() {
        $this->setJsonWS();
        \ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        $pars = $this->retrievePars(array(
            array("abi", "get", true),
            array("cab", "get", true)
        ));
        if (!isset($pars["abi"]) || empty($pars["abi"]))
        {
            $this->_setResponseStatus(404);
        }
        if (!isset($pars["cab"]) || empty($pars["cab"]))
        {
            $this->_setResponseStatus(404);
        }
        $bank = \AR\Bank::first(array(
                    "conditions" => array("bankabi = ? AND bankcab = ?", $pars["abi"], $pars["cab"])
        ));
        if (!isset($bank) || !is_object($bank) || !$bank){
            $this->_setResponseStatus(404);
            return;
        }
        echo json_encode($bank->attributes(true));
        return;
    }

    public function ws___getSuggerimentiBanca() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        if (!isset($_POST["data"]))
        {
            echo json_encode("Missing or invalid parameters " . print_r($_POST, true));
            return;
        }
        $data = json_decode($_POST["data"], true);
        if (!isset($data["partialname"]))
        {
            echo json_encode("Missing or invalid parameters " . print_r($_POST, true));
            return;
        }
        $partialname = explode("|", $data["partialname"]);
        $abi = $partialname[0];
        $cab = $partialname[1];
        $ret = new stdClass();
        header('Content-type: application/json');
        $db = \WolfMVC\Registry::get("database_vtiger");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $link->set_charset("utf8");
        $sql = "SELECT bankid,bankabi,bankcab, bankname, bankstreet, bankcity FROM external_banks"
                . " WHERE bankabi LIKE '%{$abi}%' AND bankcab LIKE '%{$cab}%'"
                . " LIMIT 0,20";
//        echo $sql;
//        return;
        if ($link->connect_errno)
        {
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $result = $link->query($sql);
        if ($result)
        {
            $ret->result = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($ret);
            return;
        }
        else
        {
            echo json_encode("An error occurred retrieving collections: " . $link->error);
            return;
        }
    }

    public function ws___insertCollection() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        if (!isset($_POST["data"]))
        {
            echo json_encode("Missing or invalid parameters " . print_r($_POST, true));
            return;
        }
        $data = json_decode($_POST["data"], true);
        if (!isset($data["amount"]) || $data["amount"] === "" || !(is_numeric($data["amount"])))
        {
            echo json_encode("Missing or invalid parameter amount.");
            return;
        }
        if (!isset($data["type"]) || $data["type"] === "" || !(is_numeric($data["type"])))
        {
            echo json_encode("Missing or invalid parameter type.");
            return;
        }
        if (!isset($data["accountid"]) || $data["accountid"] === "" || !(is_numeric($data["accountid"])))
        {
            echo json_encode("Missing or invalid parameter accountid.");
            return;
        }
        if (!isset($data["state"]) || $data["state"] === "" || !(is_numeric($data["state"])))
        {
            $data["state"] = 1;
        }
        $ret = new stdClass();
        header('Content-type: application/json');
        $db = \WolfMVC\Registry::get("database_vtiger");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $link->set_charset("utf8");
        $sql = "INSERT INTO external_collections " .
                "(amount,createdate,editdate,type,description,accountid,state,deleted,ref,bankid,emissiondate,receiptdate,depositdate, " .
                "valuedate,ourbankid) VALUES " .
                "('{$data["amount"]}',now(),now(),'{$data["type"]}','{$data["description"]}','{$data["accountid"]}', "
                . "'{$data["state"]}','0','{$data["ref"]}','{$data["bankid"]}','{$data["emissiondate"]}','{$data["receiptdate"]}',"
                . "'{$data["depositdate"]}','{$data["valuedate"]}','{$data["ourbankid"]}')";
//        echo $sql;
//        return;
        if ($link->connect_errno)
        {
            $ret->result = false;
            $ret->error = "Error occurred in db connection!";
            echo json_encode($ret);
            return;
        }
        $result = $link->query($sql);
        if ($result)
        {
            $ret->result = true;
            echo json_encode($ret);
            return;
        }
        else
        {
            $ret->result = false;
            $ret->error = "An error occurred inserting collection: " . $link->error;
            echo json_encode($ret);
            return;
        }
    }

    public function ws___deleteCollection() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        if (!isset($_POST["data"]))
        {
            echo json_encode("Missing or invalid parameters " . print_r($_POST, true));
            return;
        }
        $data = json_decode($_POST["data"], true);
        if (!isset($data["N"]) || $data["N"] === "" || !(is_numeric($data["N"])))
        {
            echo json_encode("Missing or invalid parameter record Id.");
            return;
        }
        $id = $this->anti_injection($data["N"]);
        $ret = new stdClass();
        header('Content-type: application/json');
        $db = \WolfMVC\Registry::get("database_vtiger");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $link->set_charset("utf8");
        $sql = "UPDATE external_collections " .
                "SET deleted = '1' " .
                "WHERE id='{$id}'";
//        echo $sql;
//        return;
        if ($link->connect_errno)
        {
            $ret->result = false;
            $ret->error = "Error occurred in db connection!";
            echo json_encode($ret);
            return;
        }
        $result = $link->query($sql);
        if ($result)
        {
            $ret->result = true;
            echo json_encode($ret);
            return;
        }
        else
        {
            $ret->result = false;
            $ret->error = "An error occurred deleting collection: " . $link->error;
            echo json_encode($ret);
            return;
        }
    }

    public function ws___insertBank() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        if (!isset($_POST["data"]))
        {
            echo json_encode("Missing or invalid parameters " . print_r($_POST, true));
            return;
        }
        $data = json_decode($_POST["data"], true);
        if (!isset($data["bankabi"]) || $data["bankabi"] === "" || !(is_numeric($data["bankabi"])))
        {
            echo json_encode("Missing or invalid parameter abi.");
            return;
        }
        if (!isset($data["bankcab"]) || $data["bankcab"] === "" || !(is_numeric($data["bankcab"])))
        {
            echo json_encode("Missing or invalid parameter cab.");
            return;
        }
        if (!isset($data["bankname"]) || $data["bankname"] === "")
        {
            echo json_encode("Missing or invalid parameter name.");
            return;
        }
        $ret = new stdClass();
        header('Content-type: application/json');
        $db = \WolfMVC\Registry::get("database_vtiger");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $link->set_charset("utf8");
        $sql = "INSERT INTO external_banks " .
                "(bankabi,bankcab,bankname,bankdescription,bankstreet,bankcode,bankcity,bankprovince) VALUES " .
                "('{$data["bankabi"]}','{$data["bankcab"]}','{$data["bankname"]}','{$data["bankdescription"]}', "
                . "'{$data["bankstreet"]}','{$data["bankcode"]}','{$data["bankcity"]}','{$data["bankprovince"]}')";
//        echo $sql;
//        return;
        if ($link->connect_errno)
        {
            $ret->result = false;
            $ret->error = "Error occurred in db connection!";
            echo json_encode($ret);
            return;
        }
        $result = $link->query($sql);
        if ($result)
        {
            $ret->result = true;
            echo json_encode($ret);
            return;
        }
        else
        {
            $ret->result = false;
            $ret->error = "An error occurred inserting bank: " . $link->error;
            echo json_encode($ret);
            return;
        }
    }

    public function ws___soNoToAccount() {
        $this->setJsonWS();
        \ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        $pars = $this->retrievePars(array(
            array("num_so", "get", true)
        ));
        $so = \AR\Vtso::find("first", array(
                    "conditions" => array("salesorder_no = ?", $pars["num_so"]),
                    "include" => array("account")
        ));
        if (!isset($so) || !is_object($so) || $so->ent->deleted == '1')
        {
            $this->_setResponseStatus(404);
            echo json_encode(array());
            return;
        }
        else
        {
            echo json_encode($so->account->attributes(true));
            return;
        }
    }

    public function ws___refSearch() {
        $this->setJsonWS();
        \ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        $pars = $this->retrievePars(array(
            array("ref", "get", true)
        ));
        $coll = \AR\Collection::find("all", array(
                    "conditions" => array("ref = ? and deleted = '0'", $pars["ref"]),
        ));
        echo json_encode(array_map(function($x) {
                    if (is_object($x))
                    {
                        return $x->attributes(true);
                    }
                    else
                    {
                        return null;
                    }
                }, $coll));
        return;
    }

    public function ws___fullInsert() {
        $this->setJsonWS();
        \ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        parse_str(file_get_contents('php://input'), $data);

        if (isset($data["collection"]))
        {
            $data["collection"] = json_decode($data["collection"], true);
        }
        else
        {
            $this->_setResponseStatus(400);
            echo false;
        }
        if (isset($data["collectionso"]))
        {
            $data["collectionso"] = json_decode($data["collectionso"], true);
        }
        try {
            $out = array();
            $coll = new AR\Collection($data["collection"]);
            $coll->save();
            $out[] = $coll->attributes(true);
            foreach ($data["collectionso"] as $k => $v) {
                $so = \AR\Vtso::find("first", array("conditions" => array("salesorder_no = ?", $v["so"])));
                if ($so)
                {
                    $solinked = $coll->create_collectionso(array("idso" => $so->id_so, "amount" => $v["soam"]));
                    $out[] = $solinked->attributes(true);
                }
                echo json_encode($out);
            }
            echo json_encode($out);
        } catch (\Exception $e) {
            $this->_setResponseStatus(500);
            echo json_encode(array(
                "Si è verificato un errore",
                $e->getMessage(),
                $e->getLine(),
            ));
        }
//        echo json_encode($coll->c);
//        $coll = \AR\Collection::find("all", array(
//                    "conditions" => array("ref = ?", $pars["ref"])
//        ));
//        echo json_encode(array_map(function($x) {
//                    if (is_object($x))
//                    {
//                        return $x->attributes(true);
//                    }
//                    else
//                    {
//                        return null;
//                    }
//                }, $coll));
        return;
    }

}
