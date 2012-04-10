<?php

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);

/**
 * RESTful rails style application router.
 *
 * @package gaia
 * @subpackage app
 * @author proddi@splatterladder.com
 */
class scratchApp {

    /**
     * @var array
     */
    protected $_config = array();

    /**
     * @var scratchAppRouter
     */
    protected $_router;

    /**
     * @var scratchAppRequest
     */
    protected $_request;

    /**
     * @var scratchAppResponse
     */
    protected $_response;

    /**
     * @var scratchViewYate
     */
    protected $_view;

    protected $_mixins = array();

    public function __construct(array $config = array()) {
        $this->config(array_merge(array(
            'view' => 'scratchViewYate'
        ), $config));
        $this->register('use', array($this, 'middleware'));
    }

    /**
     * Reads or writes configuration.
     * @param type $key
     * @param type $value
     * @return mixed
     */
    public function config($key = NULL, $value = NULL) {
        $numArgs = func_num_args();
        if (2 === $numArgs) {
            $this->_config[$key] = $value;
        } else if (1 === $numArgs) {
            if (is_array($key)) {
                return $this->_config = $key;
            } else {
                return @$this->_config[$key];
            }
        }
        return $this->_config;
    }

    /**
     * Register middleware
     * @param string|scratchAppMiddleware $mixin
     */
    public function middleware($mixin) {
        if (is_string($mixin)) {
            $mixin = new $mixin($this);
        }
        $this->_mixins[] = $mixin;
    }

    /**
     * @var array
     */
    protected $_customMethods = array();

    /**
     * Registers custom function on application object to be available in life cycle.
     * @param string $method
     * @param callable $callback
     */
    public function register($method, $callback) {
        $this->_customMethods[$method] = $callback;
    }

    public function __call($method, $args) {
        if (array_key_exists($method, $this->_customMethods) && is_callable($callable = $this->_customMethods[$method])) {
            return call_user_func_array($callable, $args);
        }
        throw new BadMethodCallException('Call to undefined method ' . __CLASS__ . '::' . $method. '()');
    }

    /**
     * Get the Router object
     * @return scratchAppRouter
     */
    public function router() {
        if (!$this->_router) {
            $this->_router = new scratchAppRouter($this);
        }
        return $this->_router;
    }

    /**
     * Get the Request object
     * @return scratchAppRequest
     */
    public function request() {
        if (!$this->_request) {
            $this->_request = new scratchAppRequest();
        }
        return $this->_request;
    }

    /**
     * Get the Response object
     * @return scratchAppResponse
     */
    public function response($response = NULL) {
        if ($response) $this->_response = $response;
        if (!$this->_response) {
            $this->_response = new scratchAppResponse();
        }
        return $this->_response;
    }

    /**
     * Get the View object
     * @return scratchAppView
     */
    public function view() {
        if (!$this->_view) {
            $view = $this->config('view');
            $this->_view = is_string($view) ? new $view() : $view;
        }
        return $this->_view;
    }

    public function get($path, $callable) {
        return $this->router()->map($path, $callable)->via('GET');
    }

    public function on404($callable) {
        return $this->router()->on404($callable);
    }

    public function stop() {
        throw new scratchAppExceptionStop();
    }

//    public function halt($status, $message) {
//        $this->response()->status($status);
//    }

    protected $_invokeLevel = 0;

    public function __invoke() {
        if ($this->_invokeLevel++) {
            try {
                $mw = $this->router();
                $this->_router = NULL; // reset for embeddet app() calls
                $mw($this, array());
            } catch (Exception $e) {
                $this->_invokeLevel--;
                throw $e;
            }
        } else {
            try {
                $mixins = $this->_mixins;
                $mixins[] = $this->router();
                $this->_router = NULL; // reset for embeddet app() calls

                $callable = array_shift($mixins);
                $callable($this, $mixins);
            } catch (Exception $e) {
                $res = $this->response();
                $res->clear();
    //            $res->title('scratchApp application error');
                $res->send(self::generateErrorMarkup($e));
            }
            $this->response()->streamOut();
        }
        $this->_invokeLevel--;
    }

    static protected function generateErrorMarkup(Exception $e) {
        $html =  '';
        $html .= '<p>The application could not run because of the following error:</p>';
        $html .= '<h2>Details:</h2><strong>Message:</strong> ' . $e->getMessage() . '<br/>';
        $html .= '<strong>File:</strong> ' . $e->getFile() . '<br/>';
        $html .= '<strong>Line:</strong> ' . $e->getLine() . '<br/>';
        $html .= '<h2>Stack Trace:</h2>' . nl2br($e->getTraceAsString());
        return $html;
    }
}

class scratchAppExceptionStop extends Exception{}
