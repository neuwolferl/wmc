<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

class Incassoso extends WolfMVC\Model {
    /**
     * @column
     * @readwrite
     * @primary
     * @type autonumber
     */
    protected $_id;

    /**
     * @column
     * @readwrite
     * @type integer
     * 
     */
    protected $_idcollection;

    /**
     * @column
     * @readwrite
     * @type integer
     * 
     */
    protected $_idso;

    /**
     * @column
     * @readwrite
     * @type decimal
     */
    protected $_amount;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 255
     */
    protected $_description;


}

?>