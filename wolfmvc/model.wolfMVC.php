<?php

namespace WolfMVC {


    /**
     * Definisce i metodi standard che uniformano la visione di un modello da parte delle componenti utilizzatrici
     */
    interface Model {

        public function getVm();

        public function getFieldByName($fieldname);

        public function get_primary_key();

        public function attributes($aliased);
    }

}