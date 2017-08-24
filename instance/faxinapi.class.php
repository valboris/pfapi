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
	public function NewFax()
	{
		return $this->CallApi('FaxIn/NewFax',array(),false);
	}

	/**
	* Checks if a sener callerID is marked as spam sender in system
	*/
	public function IsSpamNumber($from, $recipientUserId = false)
	{
		return $this->CallApi('FaxIn/IsSpamNumber',array('from' => $from, 'recipientUserId' => $recipientUserId),false);
	}
}
