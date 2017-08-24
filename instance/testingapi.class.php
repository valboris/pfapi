<?php
/**
* Testing playground.
*/
class TestingApi extends ApiClient
{
	/**
	*/
	public function ApiResultsExample()
	{
		return $this->CallApi('Testing/ApiResultsExample',array(),false);
	}

	/**
	*/
	public function TestServerSideTask($url, $delay = 0, $arguments = array ())
	{
		return $this->CallApi('Testing/TestServerSideTask',array('url' => $url, 'delay' => $delay, 'arguments' => $arguments),false);
	}

	/**
	*/
	public function TestChangeLister()
	{
		return $this->CallApi('Testing/TestChangeLister',array(),false);
	}

	/**
	*/
	public function TestADO()
	{
		return $this->CallApi('Testing/TestADO',array(),false);
	}

	/**
	*/
	public function DieTest($required_arg)
	{
		return $this->CallApi('Testing/DieTest',array('required_arg' => $required_arg),false);
	}

	/**
	*/
	public function DateTimeTest()
	{
		return $this->CallApi('Testing/DateTimeTest',array(),false);
	}

	/**
	*/
	public function EventTest()
	{
		return $this->CallApi('Testing/EventTest',array(),false);
	}

	/**
	*/
	public function TestCallback($user_uuid, $login_token, $event_type)
	{
		return $this->CallApi('Testing/TestCallback',array('user_uuid' => $user_uuid, 'login_token' => $login_token, 'event_type' => $event_type),false);
	}

	/**
	*/
	public function TestNumberInfo()
	{
		return $this->CallApi('Testing/TestNumberInfo',array(),false);
	}

	/**
	*/
	public function MimeTest()
	{
		return $this->CallApi('Testing/MimeTest',array(),false);
	}

	/**
	*/
	public function TestMht()
	{
		return $this->CallApi('Testing/TestMht',array(),false);
	}

	/**
	* Lists estimated sending times for different zones.
	*/
	public function ListEST($zones = false)
	{
		return $this->CallApi('Testing/ListEST',array('zones' => $zones),false);
	}

	/**
	* Lists estimated sending times for different zones.
	*/
	public function GenerateNiceNumberDependencies()
	{
		return $this->CallApi('Testing/GenerateNiceNumberDependencies',array(),false);
	}

	/**
	*/
	public function TestGoogleCreateFolder()
	{
		return $this->CallApi('Testing/TestGoogleCreateFolder',array(),false);
	}

	/**
	*/
	public function GenerateError()
	{
		return $this->CallApi('Testing/GenerateError',array(),false);
	}

	/**
	*/
	public function MemCacheInfo()
	{
		return $this->CallApi('Testing/MemCacheInfo',array(),false);
	}

	/**
	*/
	public function Foo()
	{
		return $this->CallApi('Testing/Foo',array(),false);
	}

	/**
	*/
	public function WelcomeFax($number)
	{
		return $this->CallApi('Testing/WelcomeFax',array('number' => $number),false);
	}

	/**
	*/
	public function TestLargeFax($username, $password)
	{
		return $this->CallApi('Testing/TestLargeFax',array('username' => $username, 'password' => $password),false);
	}

	/**
	*/
	public function TestDidSetAddress()
	{
		return $this->CallApi('Testing/TestDidSetAddress',array(),false);
	}

	/**
	* Returns some information about the current session in the API backend
	*/
	public function ReflectorTest()
	{
		return $this->CallApi('Testing/ReflectorTest',array(),false);
	}

	/**
	* Testing PhpDocComment class
	* @return Just the return values description, most likely liked to samples
	* @deprecated Use some other function instead
	*/
	public function TestDocCommentParser()
	{
		return $this->CallApi('Testing/TestDocCommentParser',array(),false);
	}

	/**
	*/
	public function GetSlowqueriesLog()
	{
		return $this->CallApi('Testing/GetSlowqueriesLog',array(),false);
	}

	/**
	*/
	public function TestPdo()
	{
		return $this->CallApi('Testing/TestPdo',array(),false);
	}

	/**
	*/
	public function MigrateOldFiles($limit = 100)
	{
		return $this->CallApi('Testing/MigrateOldFiles',array('limit' => $limit),false);
	}

	/**
	*/
	public function MigrateOldFiles2($limit = 100)
	{
		return $this->CallApi('Testing/MigrateOldFiles2',array('limit' => $limit),false);
	}

	/**
	*/
	public function WriteLogLines()
	{
		return $this->CallApi('Testing/WriteLogLines',array(),false);
	}

	/**
	*/
	public function RepairFileReferences($limit = 10, $start_at_file_id = false, $auto_restart = false)
	{
		return $this->CallApi('Testing/RepairFileReferences',array('limit' => $limit, 'start_at_file_id' => $start_at_file_id, 'auto_restart' => $auto_restart),false);
	}

	/**
	*/
	public function TestDateTimeEx()
	{
		return $this->CallApi('Testing/TestDateTimeEx',array(),false);
	}

	/**
	* Prepares a CSV delivered by Esker for import in our DB
	* 
	* Make sure you do the following with the XLSX delivered by Esker:
	* - Open in Excel
	* - Save As...CSV named 'esker_zones.csv'
	* - Copy to DEV server (next to this file)
	*/
	public function PrepareEskerZoneData()
	{
		return $this->CallApi('Testing/PrepareEskerZoneData',array(),false);
	}

	/**
	*/
	public function AdjustPayments_Mantis_8826($limit = 1)
	{
		return $this->CallApi('Testing/AdjustPayments_Mantis_8826',array('limit' => $limit),false);
	}
}
