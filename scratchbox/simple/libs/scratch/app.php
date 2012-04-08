<?php

error_reporting(E_ALL | E_STRICT);

/**
 * Description of app
 *
 * @author proddi@splatterladder.com
 */
class scratchApp {

    protected $config = array();

    /**
     * @var scratchAppRouter
     */
    protected $_router;

    /**
     * @var scratchAppRequest
     */
    protected $request;

    /**
     * @var scratchAppResponse
     */
    protected $response;

    protected $view;

    protected $_mixins = array();

    public function __construct(array $config = array()) {
        $this->config(array_merge(array(
            'view' => 'scratchViewYate'
        ), $config));
        $this->register('use', array($this, 'middleware'));
    }

    public function config($key = NULL, $value = NULL) {
        $numArgs = func_num_args();
        if (2 === $numArgs) {
            $this->config[$key] = $value;
        } else if (1 === $numArgs) {
            if (is_array($key)) {
                return $this->config = $key;
            } else {
                return $this->config[$key];
            }
        }
        return $this->config;
    }

    public function middleware($mixin) {
        if (is_string($mixin)) {
            $mixin = new $mixin($this);
        }
        $this->_mixins[] = $mixin;
    }

    protected $_customMethods = array();
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
            $this->_router = new scratchAppRouter();
        }
        return $this->_router;
    }

    /**
     * Get the Request object
     * @return scratchAppRequest
     */
    public function request() {
        if (!$this->request) {
            $this->request = new scratchAppRequest();
        }
        return $this->request;
    }

    /**
     * Get the Response object
     * @return scratchAppResponse
     */
    public function response($response = NULL) {
        if ($response) $this->response = $response;
        if (!$this->response) {
            $this->response = new scratchAppResponse();
        }
        return $this->response;
    }

    /**
     * Get the View object
     * @return scratchAppView
     */
    public function view() {
        if (!$this->view) {
            $view = $this->config('view');
            $this->view = is_string($view) ? new $view() : $view;
        }
        return $this->view;
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

    protected $_invokeLevel = 0;

    public function __invoke() {
        $xxx = 0 === $this->_invokeLevel++;
        $mixins = $this->_mixins;
        $mixins[] = $this->router();
        $this->_router = NULL; // reset for embeddet app() calls

        if ($xxx) {
            try {
                $callable = array_shift($mixins);
                $callable($this, $mixins);
            } catch (Exception $e) {
                $res = $this->response();
                $res->clear();
    //            $res->title('scratchApp application error');
                $res->send(self::generateErrorMarkup($e));
            }

            $this->response()->streamOut();
        } else {
            try {
                $callable = array_shift($mixins);
                $callable($this, $mixins);
            } catch (Exception $e) {
                $this->_invokeLevel--;
                throw $e;
            }
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

?>