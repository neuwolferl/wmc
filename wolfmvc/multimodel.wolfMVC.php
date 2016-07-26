<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC {

    /**
     * Classe base dei modelli complessi
     */
    abstract class Multimodel extends Base {

        /**
         * @readwrite
         */
        protected $_connector;

        /**
         * @readwrite
         * @var array
         */
        protected $_data = array();

        public function getAllFromDb() {

            $query = $this->connector->connect()->query();
            $sql = $this->createSql();
            $query->setRawsql($sql);
            $this->_data = $query->all();
        }

        public abstract function createSql();

        public function getConnector() {
            if (empty($this->_connector)) {
                $database = Registry::get("database_vtiger"); /////////////////////////////////////////////////////////////////
                if (!$database) {
                    throw new Model\Exception\Connector("No connector availaible");
                }
                $this->_connector = $database->initialize();
            }
            return $this->_connector;
        }

        public abstract function Data();
    }

}
    