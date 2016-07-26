<?php

namespace WolfMVC {

    use WolfMVC\Base as Base;
    use WolfMVC\Events as Events;
    use WolfMVC\Registry as Registry;
    use WolfMVC\Boatswain as Boatswain;
    use WolfMVC\Router\Exception as Exception;

    /**
     * Gestisce l'instradamento della richiesta verso un controller e un'azione, ovvero
     * memorizza le route registrate e gestisce url, estensione, controller e azione richiesti.
     */
    class Router extends Base {

        /**
         * @var string L'url inserito
         * @readwrite
         */
        protected $_url;

        /**
         * @var string L'estensione inserita
         * @readwrite
         */
        protected $_extension;

        /**
         * @var string Il controller richiesto 
         * @read
         */
        protected $_controller;

        /**
         * @var string L'azione richiesta
         * @read
         */
        protected $_action;

        /**
         * @read
         * @var boolean Flag per redirezione verso WS
         */
        protected $_ws = false;

        /**
         *
         * @var array Le route memorizzate
         */
        protected $_routes = array();

        public function _getExceptionForImplementation($method) {
            return new Exception\Implementation("{$method} method not implemented");
        }

        /**
         * Memorizza una nuova route e restituisce il router
         * @param \WolfMVC\Router\Route $route
         * @return \WolfMVC\Router
         */
        public function addRoute($route) {
            $this->_routes[] = $route;
            return $this;
        }

        /**
         * Rimuove una route e restituisce il router
         * @param \WolfMVC\Router\Route $route
         * @return \WolfMVC\Router
         */
        public function removeRoute($route) {
            foreach ($this->_routes as $i => $stored) {
                if ($stored == $route)
                {
                    unset($this->_routes[$i]);
                }
            }
            return $this;
        }

        /**
         * Restituisce l'array delle route attualmente memorizzate
         * @return \WolfMVC\Router\Route[]
         */
        public function getRoutes() {
            $list = array();

            foreach ($this->_routes as $route) {
                $list[$route->pattern] = get_class($route);
            }

            return $list;
        }

        /**
         * Lanciatore. <br>
         * <ul>
         * <li>controller -> Controller</li>
         * <li>prova ad instanziare il controller con i parametri passati</li>
         * <li>prova a lanciare l'azione tenendo conto degli hook</li>
         * 
         * @param string $controller
         * @param string $action
         * @param array $parameters
         * @throws Exception\Controller
         * @throws Exception\Action
         */
        protected function _pass($controller, $action, $parameters = array()) {

            $ctrl = ucfirst($controller);

            $this->_controller = $controller;
            $this->_action = $action;
            
            try {
                $instance = new $ctrl(array(
                    "parameters" => $parameters
                ));
                Registry::set("controller", $instance);
            } catch (\Exception $e) {
                throw new Exception\Controller("Controller {$ctrl} not found");
            }

            if (!method_exists($instance, $action))
            { //se il metodo corrispondente all'azione non esiste disabilito le visualizzazioni
                $instance->willRenderLayoutView = false;
                $instance->willRenderActionView = false;

                throw new Exception\Action("Action {$action} not found");
            }
            $instance->script_including();
            
            $boatswain = new \WolfMVC\Boatswain($instance); //instanzio il boatswain
            $methodMeta = $boatswain->getMethodMeta($action); // che uso per leggere i meta del metodo-azione

            if (!empty($methodMeta["@protected"]) || !empty($methodMeta["@private"]))
            { // se questo metodo non è pubblico lancio eccezione
                throw new Exception\Action("Action {$action} not found");
            }

            $hooks = function($meta, $type) use ($boatswain, $instance) {
                if (isset($meta[$type]))
                {
                    $run = array();

                    foreach ($meta[$type] as $method) {
                        $hookMeta = $boatswain->getMethodMeta($method);

                        if (in_array($method, $run) && !empty($hookMeta["@once"]))
                        {
                            continue;
                        }

                        $instance->$method();
                        $run[] = $method;
                    }
                }
            };

            $hooks($methodMeta, "@before");

            call_user_func_array(array(//chiamo il metodo-azione
                $instance,
                $action
                    ), is_array($parameters) ? $parameters : array());

            $hooks($methodMeta, "@after");

            // unset controller

            Registry::erase("controller");
        }

        public function dispatch() {
            $url = $this->url;
            $parameters = array();
            $controller = "index";
            if ($this->_extension == "ws")
            {
                $this->_ws = true;
                if (isset($_SERVER['X-WolfFWK-Operation']))
                {
                    switch ($_SERVER['X-WolfFWK-Operation']) {
                        case 'describe':
                            $action = "ws___describe";
                            break;
                    }
                }
                else
                {
                    $action = "ws___describe"; // for ws the default method is ws___describe            
                }
            }
            else
            {
                $action = "index";
            }
            foreach ($this->_routes as $route) {
                $matches = $route->matches($url);
                if ($matches)
                {
                    $controller = $route->controller;
                    $action = $route->action;
                    $parameters = $route->parameters;

                    $this->_pass($controller, $action, $parameters);
                    return;
                }
            }
            //se sono qui è perchè nessuna route ha funzionato        
            //capturing extension for ws

            $parts = explode("/", trim($url, "/"));
            //spezzo l'url dove trovo / e assumo che sia controller/azione/parametri
            if (sizeof($parts) > 0)
            {
                $controller = $parts[0];

                if (sizeof($parts) >= 2)
                {
                    $action = ($this->_ws) ? "ws___" . $parts[1] : $parts[1];

                    $parameters = array_slice($parts, 2);
                }
            }
            $bypassingControllers = array("error","maintenace","authenticate","off","home"); 
            $check = ($this->_ws || in_array($controller, $bypassingControllers) || Authorization::isActive($controller, $action));
            $check = ($this->_ws || in_array($controller, $bypassingControllers) || Authorization::isAuthorized($controller, $action));
            
            $this->_pass($controller, $action, $parameters);
        }

    }

}