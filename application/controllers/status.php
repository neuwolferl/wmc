<?php

/* 
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */


use WolfMVC\Controller as Controller;
 
class Status extends Controller
{
    public function script_including(){
        
    }
    public function index()
    {
        echo "<h1>".self::$_lang->sh("WolfMVC.Controller.Status.message")."<h1>";
        echo "<h2>Di seguito sono elencate le informazioni sul sistema.</h2>";
        echo "<h3>Stato del registro:</h3>";
        \WolfMVC\Registry::esponi();
        echo "<h3>Stato del censore:</h3>";
        \WolfMVC\Censor::esponi();
    }
}


?>