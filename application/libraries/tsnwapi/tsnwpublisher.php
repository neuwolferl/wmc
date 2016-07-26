<?php

class Tsnwpublisher extends WolfMVC\Base {

    public $version = "0.1";

    /**
     * @readwrite
     * @var integer
     */
    protected $_status = 200;

    /**
     * @readwrite
     * @var string
     */
    protected $_contentType = "";

    /**
     * @readwrite
     * @var string
     */
    protected $_content = "";
    private $_statusCodes = array(
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        301 => 'Moved Permanently',
        304 => 'Not Modified',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        409 => 'Conflict',
        410 => 'Gone',
        500 => 'Internal Server Error',
        501 => 'Not Implemented'
    );

    public function publishJSON() {
        header("HTTP/1.0 " . $this->_status . " " . $this->_statusCodes[$this->_status]);
        if ($this->_contentType !== "")
        {
            header('Content-type: application/' . $this->_contentType . '+json');
        }
        else
        {
            header('Content-type: application/json');
        }
        return $this->_content;
    }

}
?>

