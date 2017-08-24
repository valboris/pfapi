<?php

class ApiError
{
	var $Code;
	var $Message;
	var $AdditionalInfo;

    function  __construct($xml_element = false)
	{
		if(!$xml_element)
			return;
		if( is_string($xml_element) )
		{
			$this->Code = 500;
			$this->Message = $xml_element;
		}
		else
		{
			$this->Code = $xml_element->code;
			$this->Message = isset($xml_element->message)?$xml_element->message:"";
			if( isset($xml_element->additionalinfo) )
				$this->AdditionalInfo = $xml_element->additionalinfo;
		}
	}
}
