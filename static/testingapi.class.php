<?php
/**
* Testing playground.
*/
class TestingApi extends ApiClient
{
	/**
	*/
	public static function ApiResultsExample()
	{
		return self::StaticApi('Testing/ApiResultsExample',array(),false);
	}

	/**
	*/
	public static function TestServerSideTask($url, $delay = 0, $arguments = array ())
	{
		return self::StaticApi('Testing/TestServerSideTask',array('url' => $url, 'delay' => $delay, 'arguments' => $arguments),false);
	}

	/**
	*/
	public static function TestChangeLister()
	{
		return self::StaticApi('Testing/TestChangeLister',array(),false);
	}

	/**
	*/
	public static function TestADO()
	{
		return self::StaticApi('Testing/TestADO',array(),false);
	}

	/**
	*/
	public static function DieTest($required_arg)
	{
		return self::StaticApi('Testing/DieTest',array('required_arg' => $required_arg),false);
	}

	/**
	*/
	public static function DateTimeTest()
	{
		return self::StaticApi('Testing/DateTimeTest',array(),false);
	}

	/**
	*/
	public static function EventTest()
	{
		return self::StaticApi('Testing/EventTest',array(),false);
	}

	/**
	*/
	public static function TestCallback($user_uuid, $login_token, $event_type)
	{
		return self::StaticApi('Testing/TestCallback',array('user_uuid' => $user_uuid, 'login_token' => $login_token, 'event_type' => $event_type),false);
	}

	/**
	*/
	public static function TestNumberInfo()
	{
		return self::StaticApi('Testing/TestNumberInfo',array(),false);
	}

	/**
	*/
	public static function MimeTest()
	{
		return self::StaticApi('Testing/MimeTest',array(),false);
	}

	/**
	*/
	public static function TestMht()
	{
		return self::StaticApi('Testing/TestMht',array(),false);
	}

	/**
	* Lists estimated sending times for different zones.
	*/
	public static function ListEST($zones = false)
	{
		return self::StaticApi('Testing/ListEST',array('zones' => $zones),false);
	}

	/**
	* Lists estimated sending times for different zones.
	*/
	public static function GenerateNiceNumberDependencies()
	{
		return self::StaticApi('Testing/GenerateNiceNumberDependencies',array(),false);
	}

	/**
	*/
	public static function TestGoogleCreateFolder()
	{
		return self::StaticApi('Testing/TestGoogleCreateFolder',array(),false);
	}

	/**
	*/
	public static function GenerateError()
	{
		return self::StaticApi('Testing/GenerateError',array(),false);
	}

	/**
	*/
	public static function MemCacheInfo()
	{
		return self::StaticApi('Testing/MemCacheInfo',array(),false);
	}

	/**
	*/
	public static function Foo()
	{
		return self::StaticApi('Testing/Foo',array(),false);
	}

	/**
	*/
	public static function WelcomeFax($number)
	{
		return self::StaticApi('Testing/WelcomeFax',array('number' => $number),false);
	}

	/**
	*/
	public static function TestLargeFax($username, $password)
	{
		return self::StaticApi('Testing/TestLargeFax',array('username' => $username, 'password' => $password),false);
	}

	/**
	*/
	public static function TestDidSetAddress()
	{
		return self::StaticApi('Testing/TestDidSetAddress',array(),false);
	}

	/**
	* Returns some information about the current session in the API backend
	*/
	public static function ReflectorTest()
	{
		return self::StaticApi('Testing/ReflectorTest',array(),false);
	}

	/**
	* Testing PhpDocComment class
	* @return Just the return values description, most likely liked to samples
	* @deprecated Use some other function instead
	*/
	public static function TestDocCommentParser()
	{
		return self::StaticApi('Testing/TestDocCommentParser',array(),false);
	}

	/**
	*/
	public static function GetSlowqueriesLog()
	{
		return self::StaticApi('Testing/GetSlowqueriesLog',array(),false);
	}

	/**
	*/
	public static function TestPdo()
	{
		return self::StaticApi('Testing/TestPdo',array(),false);
	}

	/**
	*/
	public static function MigrateOldFiles($limit = 100)
	{
		return self::StaticApi('Testing/MigrateOldFiles',array('limit' => $limit),false);
	}

	/**
	*/
	public static function MigrateOldFiles2($limit = 100)
	{
		return self::StaticApi('Testing/MigrateOldFiles2',array('limit' => $limit),false);
	}

	/**
	*/
	public static function WriteLogLines()
	{
		return self::StaticApi('Testing/WriteLogLines',array(),false);
	}

	/**
	*/
	public static function RepairFileReferences($limit = 10, $start_at_file_id = false, $auto_restart = false)
	{
		return self::StaticApi('Testing/RepairFileReferences',array('limit' => $limit, 'start_at_file_id' => $start_at_file_id, 'auto_restart' => $auto_restart),false);
	}

	/**
	*/
	public static function TestDateTimeEx()
	{
		return self::StaticApi('Testing/TestDateTimeEx',array(),false);
	}

	/**
	* Prepares a CSV delivered by Esker for import in our DB
	* 
	* Make sure you do the following with the XLSX delivered by Esker:
	* - Open in Excel
	* - Save As...CSV named 'esker_zones.csv'
	* - Copy to DEV server (next to this file)
	*/
	public static function PrepareEskerZoneData()
	{
		return self::StaticApi('Testing/PrepareEskerZoneData',array(),false);
	}

	/**
	*/
	public static function AdjustPayments_Mantis_8826($limit = 1)
	{
		return self::StaticApi('Testing/AdjustPayments_Mantis_8826',array('limit' => $limit),false);
	}
}
