<?php

use WolfMVC\Controller as Controller;

class Error extends Controller {

    public function script_including() {
        
    }

    public function index() {
        $view = $this->getActionView();
        if (isset($this->_parameters[0])){
            $view->set("origin",  $this->_parameters[0]);
        }
    }

    public function noAuth() {
        
    }

    public function missingVT(){
        $view = $this->getActionView();
        $view->set("vtigerIcon",SITE_PATH."img/vtiger.png");
        if (isset($this->_parameters[0])){
            $view->set("origin",  $this->_parameters[0]);
        }
    }
    public function unavailablePage(){
        $view = $this->getActionView();
        $view->set("unavailableIcon",SITE_PATH."img/not-available.png");
        if (isset($this->_parameters[0])){
            $view->set("origin",  $this->_parameters[0]);
        }
    }

}
