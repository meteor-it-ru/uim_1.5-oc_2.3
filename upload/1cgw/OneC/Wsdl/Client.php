<?php

require_once 'Zend/Soap/Client/DotNet.php';

class OneC_Wsdl_Client extends Zend_Soap_Client_DotNet
{
	
	public function __construct($wsdl, $login, $password) 
	{
		$options = array(
			'cache_wsdl' => false,
			'login' => $login,
			'password' => $password,
		);	
		parent::__construct($wsdl, $options);
	}	
	
	protected function _preProcessResult($result)
	{
		$_result = (array) $result;	
		return $_result['return'];
	}
	
}