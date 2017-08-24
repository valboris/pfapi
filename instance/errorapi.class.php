<?php
/**
* INTERNAL USE ONLY!
* 
* This class just handles errors that may occur when accessing the API.
* Do not use it directly!
*/
class ErrorApi extends ApiClient
{
	/**
	* INTERNAL USE ONLY!
	* 
	* This class just handles errors that may occur when accessing the API.
	* Do not use it directly!
	*/
	public function LogError($code, $message, $data = false)
	{
		return $this->CallApi('Error/LogError',array('code' => $code, 'message' => $message, 'data' => $data),false);
	}

	/**
	*/
	public function Die500($message = false, $additionalinfo = false)
	{
		return $this->CallApi('Error/Die500',array('message' => $message, 'additionalinfo' => $additionalinfo),false);
	}

	/**
	*/
	public function Die403($message = false, $additionalinfo = false)
	{
		return $this->CallApi('Error/Die403',array('message' => $message, 'additionalinfo' => $additionalinfo),false);
	}

	/**
	*/
	public function Die404($message = false, $additionalinfo = false)
	{
		return $this->CallApi('Error/Die404',array('message' => $message, 'additionalinfo' => $additionalinfo),false);
	}

	/**
	*/
	public function TokenExpired()
	{
		return $this->CallApi('Error/TokenExpired',array(),false);
	}

	/**
	*/
	public function ApiError($code = 500, $message = false)
	{
		return $this->CallApi('Error/ApiError',array('code' => $code, 'message' => $message),false);
	}

	/**
	*/
	public function DieWrongSubsystem($url = false)
	{
		return $this->CallApi('Error/DieWrongSubsystem',array('url' => $url),false);
	}
}
