<?php
/**
* PamFax internal functionality
*/
class InternalApi extends ApiClient
{
	/**
	*/
	public static function StoreErrorReport($username = false, $type = 'COMMON', $report = '')
	{
		return self::StaticApi('Internal/StoreErrorReport',array('username' => $username, 'type' => $type, 'report' => $report),false);
	}

	/**
	*/
	public static function UploadErrorReport($username = false, $type = 'COMMON')
	{
		return self::StaticApi('Internal/UploadErrorReport',array('username' => $username, 'type' => $type),false);
	}

	/**
	*/
	public static function RaiseEvent($task_id, $event_id)
	{
		return self::StaticApi('Internal/RaiseEvent',array('task_id' => $task_id, 'event_id' => $event_id),false);
	}

	/**
	* Prepares the currently logged in user to be used as testing user.
	* 
	* This means that it becomes 1000€ credit.
	*/
	public static function PrepareUserForTesting()
	{
		return self::StaticApi('Internal/PrepareUserForTesting',array(),false);
	}

	/**
	* Deletes all users data except the users and user_profiles entries.
	* 
	* Credit and free credits will be set to 0.
	*/
	public static function CleanupUsersTestingData()
	{
		return self::StaticApi('Internal/CleanupUsersTestingData',array(),false);
	}

	/**
	*/
	public static function GetGlobalCacheInfo()
	{
		return self::StaticApi('Internal/GetGlobalCacheInfo',array(),false);
	}

	/**
	* Sends a fax.
	* 
	* Uses user 'pamfax_internal' and FaxJob::QuickSend.
	* This is needed because FaxJob::QuickSend requires file-uploads (and SPH doesnt support that)
	* and because task_arguments value is limited to 255 chars, which will not be enough for covertext.
	* So polling this via RunOnServer Task and preparing a file_to_send before.
	* NOTE: Be careful when sending to non-pamfax numbers because pamfax_internal has no credit!
	*/
	public static function FaxToUser($number, $name, $file_id, $text_id)
	{
		return self::StaticApi('Internal/FaxToUser',array('number' => $number, 'name' => $name, 'file_id' => $file_id, 'text_id' => $text_id),false);
	}

	/**
	* Returns some information about the current session in the API backend
	*/
	public static function GetSessionInfo()
	{
		return self::StaticApi('Internal/GetSessionInfo',array(),false);
	}

	/**
	* Cleans the complete API cache
	* 
	* Server-side cache will be deleted and an APC value will be set so that API will inform clients
	* to clear their cache too.
	*/
	public static function CleanupApiCache()
	{
		return self::StaticApi('Internal/CleanupApiCache',array(),false);
	}

	/**
	* Adds a global cover to the DB
	* 
	* Server-side cache will be deleted and an APC value will be set so that API will inform clients
	* to clear their cache too.
	*/
	public static function AddGlobalCover($title)
	{
		return self::StaticApi('Internal/AddGlobalCover',array('title' => $title),false);
	}

	/**
	* Migrates notification settings from 3.4MR8 to 3.5
	* 
	* See mantis #6567: Redesign notification system
	*/
	public static function MigrateNotificationSettings($limit = 1, $batch = false)
	{
		return self::StaticApi('Internal/MigrateNotificationSettings',array('limit' => $limit, 'batch' => $batch),false);
	}

	/**
	* Returns current scam score value and explanation for current user. Possible action: - 'NONE' - only otput current scam score expiration - 'RECALC' - recalc current scam score - 'RECALC-WITHOUT_SAVE' - recalc current scam score - 'RECALC+LOCK' - also, lock account if scamscore > then $CONFIG[blocksroce] If $uuid not passed - SP will be calculated for current user.
	*/
	public static function GetScamScore($uuuid = '', $action = 'NONE')
	{
		return self::StaticApi('Internal/GetScamScore',array('uuuid' => $uuuid, 'action' => $action),false);
	}

	/**
	* Sends welcome fax to $number via landline (not pam2pam).
	* @param string $number Valid fax-in number in active state, example +1 234 5678...
	* @param string $fax_provider_id id (integer) of supported landline fax providers MON, ESK, BFX
	* @param string $number_owner_uuid user uuid (number owner)
	*/
	public static function TestFaxInNumber($number, $fax_provider_id, $number_owner_uuid)
	{
		return self::StaticApi('Internal/TestFaxInNumber',array('number' => $number, 'fax_provider_id' => $fax_provider_id, 'number_owner_uuid' => $number_owner_uuid),false);
	}


	public static function GetSetting($settingname, $defaultval = null)
	{

		return self::StaticApi('Internal/GetSetting',array('settingname'=>$settingname, 'defaultval'=>$defaultval),false);
	}
	/**
	 * Get a user's setting from the client DB
	 * @param string $settingname , example 'startupbuycreditshown', 'hide_hint_foreign_number'...
	 * @param string $settingvalue
	 */

	public static function SetSetting($settingname, $settingvalue)
	{

		return self::StaticApi('Internal/SetSetting',array('settingname'=>$settingname, 'settingvalue'=>$settingvalue),false);
	}
}
?>
