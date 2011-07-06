<?php

/**
 * Classe para manipulação de requisições http
 * @author Eric Silva
 * @since 2010
 */
class Http {

    // Métodos suportados
    const HTTP_POST = 'POST';
    const HTTP_GET = 'GET';
    private static $_method = null;
    private static $_instance = null;


    public static function getInstance() {
        if(null == self::$_instance) {
            $classe = __CLASS__;
            self::$_instance = new $classe;
        }
        return self::$_instance;
    }

    /**
     * Método que será utilizada para solicitar a requisição
     * @param string $method string
     */
    public function setMethod($method) {
        $method = strtolower($method);
        switch($method) {
            case 'post':
                self::$_method = self::HTTP_POST;
                break;
            case 'get':
                self::$_method = self::HTTP_GET;
                break;
            default:
                throw new Exception('Método de requisição não suportado');
                break;
        }
        return self::$_instance;
    }

    /**
     * Método a ser utilizado na requisição
     *
     * @return void
     */
    public static function getMethod() {
        return self::$_method;
    }

    /**
     * Realiza uma requisição
     * 
     * @param string $url url a ser requisitada
     * @param array $params parâmetros passados para a url
     * @return mixed
     */
    private function request($url, array $params) {

        // montando a query string
        $query = http_build_query($params);

        // cabeçalho da requisição
        $paramsStream = array(
            'http' => array(
                'timeout' => 10,
                'method'  => self::getMethod(),
                'header'  =>
                      "Content-Type: application/x-www-form-urlencoded\r\n"
                    . 'Content-Length: ' . strlen($query),
                'content' => $query
            )
        );

        switch(self::getMethod()) {
            case self::HTTP_POST:
                $steram = stream_context_create($paramsStream);
                break;

            case self::HTTP_GET:
                $steram = null;
                $url .= '?' . $query;
                break;
            default:
                throw new Exception('Método de requisição não suportado');
                break;
        }

        return file_get_contents($url, false, $steram);
    }

    /**
     * Realiza uma requisição POST
     *
     * @param string $url url a ser requisitada
     * @param array $params parâmetros passados para a url
     * @return mixed
     */
    public static function post($url, $params = array()) {
        $s = self::getInstance();
        return $s->setMethod(self::HTTP_POST)->request($url, $params);
    }

    /**
     * Realiza uma requisição GET
     *
     * @param string $url url a ser requisitada
     * @param array $params parâmetros passados para a url
     * @return mixed
     */
    public static function get($url, $params = array()) {
        $s = self::getInstance();
        return $s->setMethod(self::HTTP_GET)->request($url, $params);
    }

}