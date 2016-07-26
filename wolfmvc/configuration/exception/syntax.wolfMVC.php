<?php

namespace WolfMVC\Configuration\Exception
{
    use WolfMVC\Configuration as Configuration;
    
    class Syntax extends Configuration\Exception
    {
        public function getMessageType(){
            return "Si &eacute; verificato un errore di tipo configuration\exception<br>";
        }
    }
}