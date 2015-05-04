<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 14.04.15
 * Time: 15:34
 */

/**
 * Class PhonexaAbstractApiRequest
 * @property bool responseToAssocArray
 */
abstract class PhonexaAbstractApiRequest
{
    protected $_fields = [];
    private $_secretKey;
    private $_apiVersion;
    protected $_requestId;

    protected abstract function _checkRequiredParams();
    protected abstract function _prepareRequest();
    protected abstract function _getApiParams();

    public function __construct($secretKey, $apiVersion = 'api1', $requestId = null){
        $this->_secretKey = $secretKey;
        $this->_apiVersion = $apiVersion;
        if($requestId){
            $this->_requestId = $requestId;
        } else {
            $this->_requestId = $this->_getRequestId();
        }
    }

    public function __get($field){
        if(isset($this->_fields[$field])){
            return $this->_fields[$field];
        } else {
            return null;
        }
    }

    public  function __set($field, $value){
        $this->_fields[$field] = $value;
    }

    public function getData(){
        return $this->_sendRequest();
    }

    protected function _sendRequest(){
        $serverOutput = false;
        if($this->_checkRequiredParams()) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->_getAPUrl());
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_prepareRequest());
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            // receive server response ...
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $serverOutput = curl_exec($ch);

            curl_close($ch);

            // further processing ....
            if ($serverOutput === false) {
            } else {
                $serverOutput = json_decode($serverOutput, $this->_fields['responseToAssocArray']);
            }
        }
        return $serverOutput;
    }

    protected function _getAPUrl(){
        $apiParams = $this->_getApiParams();
        $url = $apiParams[$this->_apiVersion]['server_domain'];
        $url .= '/'.$this->_apiVersion;
        $url .= $apiParams[$this->_apiVersion]['routes'][$this->_fields['type']]['url'];
        return $url;
    }

    protected function _getAPIKey(){
        $key = md5($this->_requestId.$this->_secretKey);
        return $key;
    }

    protected function _getRequestId(){
        $dt = new DateTime();
        return $dt->getTimestamp();
    }
} 