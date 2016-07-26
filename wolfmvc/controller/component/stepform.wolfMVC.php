<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Controller\Component {

    use \WolfMVC\Template\Component\Formcomponent as FC;

    /**
     * Estensione di Flux che permette di gestire automaticamente form concettualmente uniti ma presentati su più pagine. 
     * In ogni pagina viene presentato un form ridotto e un pulsante avanti che porta allo step successivo.
     * Esempio di uso:<br><code><?php
     * $sf = new WolfMVC\Controller\Component\Stepform();
     * $sf->setNumberofsteps(2);
     * $sf->setPassedparameters($params); // $params è un array di parametri passati al ctrl, di cui il primo è un intero che
     *                                    // contiene lo step da eseguire
     * $forms = array(...);               // $forms è un vettore di oggetti che estendono la classe astratta 
     *                                    // {@see WolfMVC\Template\Component\Form}
     * $sf->setForms($forms);
     * $html = $sf->make("");             //$html può essere passato alla vista o a successive elaborazioni 
     * </code>
     * 
     */
    class Stepform extends Flux {

        /**
         * @var array Vettore di {@see WolfMVC\Template\Component\Int\Form} da implementare passo passo.
         * @readwrite
         */
        protected $_forms = array();

        /**
         *
         * @var string Get o post, sovrascrive il valore per tutti i form contenuti in cui non è specificato a mano
         * @readwrite
         */
        protected $_method;

        /**
         * Controlla il tipo dell'attributo {@see class::_forms}. Identifica lo step e genera il form.
         * @todo Aggiungere eccezione
         * @param string $html Stringa a cui viene appeso l'html del form da visualizzare
         */
        public function make($html) {
            foreach ($this->_forms as $form) {
                if (!($form instanceof \WolfMVC\Template\Component\Form)) { //questo controllo mi garantisce di poter invocare il metodo make
                    echo "Errore di implementazione interfaccia";
                    return;
                }
            }
            if (count($this->_passedparameters) == 0) {
                echo "Errore, non trovo i parametri";
                return;
            }
            $this->_actualstep = array_shift($this->_passedparameters);

            if ($this->_actualstep == $this->_numberofsteps + 1) { // passo finale
                $form = new \WolfMVC\Template\Component\Simpleform();
//                $fieldsc = array();
//                for ($cnt = 0; $cnt < $this->_numberofsteps; $cnt++) {
//                    $previousfields = $this->_forms[$cnt]->getSensibleFields();
//                    foreach ($previousfields as $pf) {
//                        $fieldsc[] = array();
//                        $fieldsc[] = array("text", $pf, $pf, "100", \WolfMVC\RequestMethods::post($pf));
//                    }
//                }
//                $form->setFields($fieldsc);
                $this->_forms[$this->_numberofsteps] = $form;
            }
            else if (($this->_actualstep > $this->_numberofsteps) || ($this->_actualstep > count($this->_forms))) {
                echo "Errore di parametri stepform";
                return;
            }

            if ($this->_actualstep > 1) { // trasporto dati step precedenti
                for ($cnt = 0; $cnt < $this->_actualstep - 1; $cnt++) {
                    $form = $this->_forms[$cnt];
                    $previousfields = $form->getSensibleFields();
                    foreach ($previousfields as $pf) {
                        
                        $fc = $form->fromindexFields($pf);
                        $fc = $fc[0];
                        $this->_forms[$this->_actualstep - 1]->add(new FC\Hidden(), true)
                        ->setName($fc->getName())->setValue(\WolfMVC\RequestMethods::post($fc->getName()));
                    }
                }
            }
            $this->_forms[$this->_actualstep - 1]->add("br");
            $this->_forms[$this->_actualstep - 1]->add(new FC\Label(), true)
              ->setContent($this->_actualstep . "/" . $this->_numberofsteps);

            if ($this->_actualstep < $this->_numberofsteps) {
                $this->_forms[$this->_actualstep - 1]->add(new FC\Button(), true)
                  ->setValue("avanti")->setName("avanti")->setContent("Avanti");
            }
            else {
                $this->_forms[$this->_actualstep - 1]->add(new FC\Button(), true)
                  ->setValue("conferma")->setName("conferma")->setContent("Conferma");
            }

            if (!(empty($this->_method))) {
                $this->_forms[$this->_actualstep - 1]->setMethod($this->_method);
            }

            $this->_forms[$this->_actualstep - 1]->setAction(SITE_PATH . \WolfMVC\Registry::get("router")->getController() . "/" . \WolfMVC\Registry::get("router")->getAction() . "/" . ($this->_actualstep + 1) . "/" . join("/", $this->_passedparameters));
            return $this->_forms[$this->_actualstep - 1]->make($html);
        }

        function describe() {
            ob_start();
            echo "Questo &eacute; uno stepform con form registrati:<pre>";
            print_r($this->_forms);
            echo "</pre>";
            echo "Passo: " . $this->_actualstep . "/" . $this->_numberofsteps . "<br>";
            echo "<pre>";
            print_r($this->_passedparameters);
            echo "</pre>";
            foreach ($this->_forms as $key => $form) {
                echo "Campi sensibili form {$key}<pre>";
                print_r($form->getSensibleFields());
                echo "</pre>";
            }
            $ret = ob_get_contents();
            ob_end_clean();
            return $ret;
        }

    }

}

?>