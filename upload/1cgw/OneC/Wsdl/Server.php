<?php

require_once 'Zend/Soap/Wsdl/Exception.php';
require_once 'Zend/Soap/AutoDiscover.php';
require_once 'Zend/Soap/Server.php';
//require_once 'Zend/Soap/Wsdl/Strategy/ArrayOfTypeSequence.php';
require_once 'Zend/Soap/Wsdl/Strategy/ArrayOfTypeComplex.php';
require_once 'OneC/Wsdl/Strategy.php';

class OneC_Wsdl_Server
{

	protected $_service = null;
	protected $_proxy = null;

	public function __construct($service = null)
	{
		if ($service != null) {
			$this->setService($service);
		}
	}

	public function getUri()
	{
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
			$uri = 'https';
		} else {
			$uri = 'http';
		}
		$uri .= '://' . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		return $uri;
	}

	public function setService($service)
	{
		if (is_string($service)) {
			$this->_service = $service;
		} elseif (class_exists($service)) {
			$this->_service = get_class($service);
		} else {
			throw new Zend_Soap_Wsdl_Exception('Service is bad.');
		}
	}

	public function getService()
	{
		return $this->_service;
	}

	public function setProxy($proxy)
	{
		$this->_proxy = $proxy;
	}

	public function getProxy()
	{
		return $this->_proxy;
	}

	public function handle()
	{
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$autodiscover = new Zend_Soap_AutoDiscover('OneC_Wsdl_Strategy');
			$autodiscover->setBindingStyle(array('style'=>'rpc'));
			$autodiscover->setOperationBodyStyle(array('use' => 'literal'));
			$autodiscover->setClass($this->getService());
			$autodiscover->handle();
		} else if($_SERVER['REQUEST_METHOD'] == 'POST') {						
			$server = new Zend_Soap_Server($this->getUri(), array('cache_wsdl' => false));
			$server->setClass($this->getService());
    		$server->handle();
		} else {
			throw new Zend_Soap_Wsdl_Exception(sprintf("Method '%s' is not supported.", $_SERVER['REQUEST_METHOD']));
		}
	}

}
