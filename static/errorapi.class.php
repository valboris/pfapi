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
	public static function LogError($code, $message, $data = false)
	{
		return self::StaticApi('Error/LogError',array('code' => $code, 'message' => $message, 'data' => $data),false);
	}

	/**
	*/
	public static function Die500($message = false, $additionalinfo = false)
	{
		return self::StaticApi('Error/Die500',array('message' => $message, 'additionalinfo' => $additionalinfo),false);
	}

	/**
	*/
	public static function Die403($message = false, $additionalinfo = false)
	{
		return self::StaticApi('Error/Die403',array('message' => $message, 'additionalinfo' => $additionalinfo),false);
	}

	/**
	*/
	public static function Die404($message = false, $additionalinfo = false)
	{
		return self::StaticApi('Error/Die404',array('message' => $message, 'additionalinfo' => $additionalinfo),false);
	}

	/**
	*/
	public static function TokenExpired()
	{
		return self::StaticApi('Error/TokenExpired',array(),false);
	}

	/**
	*/
	public static function ApiError($code = 500, $message = false)
	{
		return self::StaticApi('Error/ApiError',array('code' => $code, 'message' => $message),false);
	}

	/**
	*/
	public static function DieWrongSubsystem($url = false)
	{
		return self::StaticApi('Error/DieWrongSubsystem',array('url' => $url),false);
	}
}
