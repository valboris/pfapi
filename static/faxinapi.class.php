<?php
/**
* Entrypoint for delivering faxes to PamFax users.
* 
* Currently this is triggered from Voxbone only.
*/
class FaxInApi extends ApiClient
{
	/**
	* Receive a new fax from other system.
	*/
	public static function NewFax()
	{
		return self::StaticApi('FaxIn/NewFax',array(),false);
	}

	/**
	* Checks if a sener callerID is marked as spam sender in system
	*/
	public static function IsSpamNumber($from, $recipientUserId = false)
	{
		return self::StaticApi('FaxIn/IsSpamNumber',array('from' => $from, 'recipientUserId' => $recipientUserId),false);
	}
}
