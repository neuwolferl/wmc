<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

class Incasso extends WolfMVC\Model {
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
     * @type decimal
     * 
     */
    protected $_amount;

    /**
     * @column
     * @readwrite
     * @type datetime
     * 
     */
    protected $_createdate;

    /**
     * @column
     * @readwrite
     * @type datetime
     */
    protected $_editdate;

    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_type;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 255
     */
    protected $_description;

    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_accountid;

    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_state;

    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_deleted;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 50
     */
    protected $_ref;

    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_bankid;

    /**
     * @column
     * @readwrite
     * @type datetime
     */
    protected $_emissiondate;
    
    /**
     * @column
     * @readwrite
     * @type datetime
     */
    protected $_receiptdate;
    
    /**
     * @column
     * @readwrite
     * @type datetime
     */
    protected $_depositdate;
    
    /**
     * @column
     * @readwrite
     * @type datetime
     */
    protected $_valuedate;
    
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_ourbankid;


}

?>