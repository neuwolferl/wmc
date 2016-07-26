<?php

use WolfMVC\Controller as Controller;

class Home extends Controller {

    /**
     * @protected
     */
    public function script_including() {
        $view = new \WolfMVC\View(array(//questo pezzo può essere replicato altrove per cambiare il file template usato per il layout
            "file" => APP_PATH . "/{$this->getDefaultPath()}/layouts/daf.{$this->getDefaultExtension()}"
        ));
        $this->setLayoutView($view);
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/utils.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/jquery.js\"></script>";
    }

    public function index() {
        $view = $this->getActionView();
        $session = \WolfMVC\Registry::get("session");
        $user = $session->get("user");
        if ($user === "Alberto Brudaglio"){
            $view->set("isAdmin",true);
            $view->set("isAdmin",true);
        }
        else {
            $view->set("isAdmin",false);
        }
        $view->set("progettiIcon",SITE_PATH."img/home/torta.png");
        $view->set("sonosuperfigoIcon",SITE_PATH."img/home/lucchetto.png");
        $view->set("incassiIcon",SITE_PATH."img/home/assegno.png");
        $view->set("tmkIcon",SITE_PATH."img/home/cuffie.png");
        $view->set("backofficeIcon",SITE_PATH."img/home/impiegato.png");
        $view->set("vendIcon",SITE_PATH."img/home/venditore.png");
        $view->set("wipIcon",SITE_PATH."img/home/wip.png");
        $view->set("mktIcon",SITE_PATH."img/home/magnifier.png");
//        $ws = new WolfMVC\WS();
//        $ws->addOp("SELECT");
//        $ws->setParametersForOp("select",);
//        ob_start();
//        echo "<pre>";print_r($ws->describe());echo "</pre>";
//        $view->set("data", ob_get_contents());
//        ob_end_clean();
    }
    /**
     * @before script_including
     */
    public function provaprovasession() {

        $this->_system_js_including .="<script> $(document).ready( function() { \n" .
                "var aj = $.ajax({\n" .
                " type: \"GET\",\n" .
                "url: \"http://localhost/tsnw/public/home/provasession.ws\",\n" .
                "success: function(data) {\n" .
                " $('#ciao').append(data); return;\n" .
                "}\n" .
                "});\n" .
                "});\n</script>\n";
    }

    public function provasession() {
        echo "<pre>";
        print_r($_SESSION);
        echo "</pre>";
    }

    public function ws___provasession() {
        echo "<pre>";
        print_r($_SESSION);
        echo "</pre>";
    }

}
