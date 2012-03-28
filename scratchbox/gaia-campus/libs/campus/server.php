<?php

/**
 * Description of server
 *
 * @author proddi@splatterladder.com
 */
class campusServer {
    //put your code here

    static public function controller($className, $param1 = NULL, $param2 = NULL, $param3 = NULL) {
        return function(&$req, &$res, &$data) use ($className, $param1, $param2, $param3) {
            $class = new $className($param1, $param2, $param3);
            return $class($req, $res, $data);
        };
    }

    static public function call($scope, $method) {
        return function(&$req, &$res, &$data) use ($scope, $method) {
            return $scope->$method($req, $res, $data);
        };
    }

}

?>
