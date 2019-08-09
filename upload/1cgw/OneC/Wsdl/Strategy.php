<?php

require_once 'Zend/Soap/Wsdl/Strategy/ArrayOfTypeSequence.php';

class OneC_Wsdl_Strategy extends Zend_Soap_Wsdl_Strategy_ArrayOfTypeSequence
{

	public function addComplexType($type)
    {
    	parent::addComplexType($type);
    	return 'xsd:anyType';
    }
    
}