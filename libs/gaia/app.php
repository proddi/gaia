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
class gaiaApp {

    /**
     * @var array
     */
    protected $_config = array();

    /**
     * @var gaiaAppRouter
     */
    protected $_router;

    /**
     * @var gaiaAppRequest
     */
    protected $_request;

    /**
     * @var gaiaAppResponse
     */
    protected $_response;

    /**
     * @var gaiaViewYate
     */
    protected $_view;

    protected $_mixins = array();

    public function __construct(array $config = array()) {
        $this->config(array_merge(array(
            'view' => 'gaiaViewYate'
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
     * @param string|gaiaAppMiddleware $mixin
     */
    public function middleware($mixin, $options = NULL) {
        if (is_string($mixin)) {
            $mixin = new $mixin($this, $options);
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
     * @return gaiaAppRouter
     */
    public function router() {
        if (!$this->_router) {
            $this->_router = new gaiaAppRouter($this);
        }
        return $this->_router;
    }

    /**
     * Get the Request object
     * @return gaiaAppRequest
     */
    public function request() {
        if (!$this->_request) {
            $this->_request = new gaiaAppRequest();
        }
        return $this->_request;
    }

    /**
     * Get the Response object
     * @return gaiaAppResponse
     */
    public function response($response = NULL) {
        if ($response) $this->_response = $response;
        if (!$this->_response) {
            $this->_response = new gaiaAppResponse();
        }
        return $this->_response;
    }

    /**
     * Get the View object
     * @return gaiaAppView
     */
    public function view() {
        if (!$this->_view) {
            $view = $this->config('view');
            $this->_view = is_string($view) ? new $view() : $view;
        }
        return $this->_view;
    }

    public function map($path, $callable) {
        return $this->router()->map($path, $callable);
    }

    public function get($path, $callable) {
        return $this->map($path, $callable)->via('GET');
    }

    public function post($path, $callable) {
        return $this->map($path, $callable)->via('POST');
    }

    public function put($path, $callable) {
        return $this->map($path, $callable)->via('PUT');
    }

    public function delete($path, $callable) {
        return $this->map($path, $callable)->via('DELETE');
    }

    public function options($path, $callable) {
        return $this->map($path, $callable)->via('OPTIONS');
    }

    public function on404($callable) {
        return $this->router()->on404($callable);
    }

    protected $_after;
    public function after($callable) {
        $this->_after = $callable;
    }

    public function stop() {
        throw new gaiaAppExceptionStop();
    }

    public function next() {
        throw new gaiaAppExceptionNext();
    }

//    public function halt($status, $message) {
//        $this->response()->status($status);
//    }

    protected $_invokeLevel = 0;

    public function __invoke() {
        $after = $this->_after;

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
            } catch (gaiaAppExceptionStop $e) {

            } catch (Exception $e) {
                $res = $this->response();
                $res->clear();
    //            $res->title('gaiaApp application error');
                $res->send(self::generateErrorMarkup($e));
            }

            if ($after) call_user_func ($after, $this);

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

class gaiaAppExceptionStop extends Exception{}

class gaiaAppExceptionNext extends Exception{}
