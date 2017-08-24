<?php
/**
* INTERNAL USE ONLY!
*/
class CronApi extends ApiClient
{
	/**
	* Updates the Currencies
	*/
	public static function UpdateCurrencies()
	{
		return self::StaticApi('Cron/UpdateCurrencies',array(),false);
	}

	/**
	*/
	public static function UpdateFaxHistory()
	{
		return self::StaticApi('Cron/UpdateFaxHistory',array(),false);
	}

	/**
	* Get all the area codes and countries from voxbone and save them in our DB
	*/
	public static function UpdateVoxbone()
	{
		return self::StaticApi('Cron/UpdateVoxbone',array(),false);
	}

	/**
	* Get all the area codes and countries from voxbone and save them in our DB Compatible with Voxbone API 3. Also updates Geo-Information too.
	*/
	public static function UpdateVoxbone3()
	{
		return self::StaticApi('Cron/UpdateVoxbone3',array(),false);
	}

	/**
	*/
	public static function ExpireOldCredits($limit = 20)
	{
		return self::StaticApi('Cron/ExpireOldCredits',array('limit' => $limit),false);
	}

	/**
	* Performs a system-wide cleanup.
	* 
	* Note: Be careful when triggering this and make sure you know what you are doing!
	*/
	public static function Cleanup()
	{
		return self::StaticApi('Cron/Cleanup',array(),false);
	}

	/**
	* Performs a cleanup of old files
	* 
	* Note: Be careful when triggering this and make sure you know what you are doing!
	*/
	public static function CleanupFiles()
	{
		return self::StaticApi('Cron/CleanupFiles',array(),false);
	}

	/**
	* Clears the system-global cache
	*/
	public static function ClearGlobalCache()
	{
		return self::StaticApi('Cron/ClearGlobalCache',array(),false);
	}

	/**
	* Collects monitoring data.
	* 
	* Will also perform some basic system-health checks and send warning emails to alert@pamfax.biz
	*/
	public static function Monitor()
	{
		return self::StaticApi('Cron/Monitor',array(),false);
	}

	/**
	* Counts all active user sessions.
	* 
	* Will be counted depending on their api-id and saved for later use in coneole (ActivityHeatmap)
	*/
	public static function MonitorActiveSessions()
	{
		return self::StaticApi('Cron/MonitorActiveSessions',array(),false);
	}

	/**
	*/
	public static function MonitorMonthly()
	{
		return self::StaticApi('Cron/MonitorMonthly',array(),false);
	}

	/**
	*/
	public static function UpdatePlans($limit = 100)
	{
		return self::StaticApi('Cron/UpdatePlans',array('limit' => $limit),false);
	}

	/**
	* Fill up the monthly free pages (Pro Plan)
	*/
	public static function FillUpMonthlyFreeCredits($emulate = 0)
	{
		return self::StaticApi('Cron/FillUpMonthlyFreeCredits',array('emulate' => $emulate),false);
	}

	/**
	* Send emails to users when they have a recent shop order in "New Order Placed" state
	*/
	public static function NewOrderReminder()
	{
		return self::StaticApi('Cron/NewOrderReminder',array(),false);
	}

	/**
	*/
	public static function RemindUnpaidFaxes($limit = 100)
	{
		return self::StaticApi('Cron/RemindUnpaidFaxes',array('limit' => $limit),false);
	}

	/**
	* Keeps our buffered Voxbone data up-to-date.
	* 
	* Validates unvalidated FaxIN numbers, cancels DIDs that are not in our Database
	* or assigns them to cbuenger, cancels refunded numbers at voxbone
	* @param string $canceldids if set to 1 it'll cancel DID's that are not in our Database
	*/
	public static function CrawlVoxbone($canceldids = false)
	{
		return self::StaticApi('Cron/CrawlVoxbone',array('canceldids' => $canceldids),false);
	}

	/**
	* Ensures task-assignment.
	* 
	* Call at high frequency (once each 3-5 minutes)
	* Will ensure task processing even if tasks had MySQL or programming errors.
	* Will also perform Cleanup of unreferenced tasks, arguments and dependencies.
	*/
	public static function Heartbeat()
	{
		return self::StaticApi('Cron/Heartbeat',array(),false);
	}

	/**
	* Resends failed emails.
	* 
	* Searches the messages table for emails that aren't sent and failed and trys to sent them again
	* @param int $limit the maximum number of mails that should be resend
	*/
	public static function ResendUnsentMails($limit = 100)
	{
		return self::StaticApi('Cron/ResendUnsentMails',array('limit' => $limit),false);
	}

	/**
	* Counts all files in the filesystem that are not referenced in DB anymore.
	* 
	* Hints:
	* - Count is returned at the very end of the output
	* - Processing will take it's time (>1min) so be patient
	* 2010-08-06 11:00 -> Count = 24
	*/
	public static function CountUselessFiles()
	{
		return self::StaticApi('Cron/CountUselessFiles',array(),false);
	}

	/**
	*/
	public static function ReCheckCovers()
	{
		return self::StaticApi('Cron/ReCheckCovers',array(),false);
	}

	/**
	* Updates if new 2499 geolocations from fax_in_groups a day
	*/
	public static function UpdateFaxInGeoLocation()
	{
		return self::StaticApi('Cron/UpdateFaxInGeoLocation',array(),false);
	}

	/**
	* Reminds users whose credit run low and who have at least one purchase
	*/
	public static function SendCreditNotifications($limit = 100)
	{
		return self::StaticApi('Cron/SendCreditNotifications',array('limit' => $limit),false);
	}

	/**
	* Optimizes all db tables to reclaim unsused space and to defragment the data file
	*/
	public static function OptimizeDatabases()
	{
		return self::StaticApi('Cron/OptimizeDatabases',array(),false);
	}

	/**
	* Cancels old numbers from our number pool at the Voxbone side of the world
	*/
	public static function CancelPooledNumbers($limit = 100)
	{
		return self::StaticApi('Cron/CancelPooledNumbers',array('limit' => $limit),false);
	}

	/**
	* Validates current unverified numbers or
	*/
	public static function ValidateNumbers($limit = 100)
	{
		return self::StaticApi('Cron/ValidateNumbers',array('limit' => $limit),false);
	}

	/**
	*/
	public static function ProcessResellerInvoices($reseller_id)
	{
		return self::StaticApi('Cron/ProcessResellerInvoices',array('reseller_id' => $reseller_id),false);
	}

	/**
	*/
	public static function ProcessAffiliateCreditNotes($affiliate_id = false)
	{
		return self::StaticApi('Cron/ProcessAffiliateCreditNotes',array('affiliate_id' => $affiliate_id),false);
	}

	/**
	*/
	public static function GenerateSkypeAffiliateReport($month = false, $year = false)
	{
		return self::StaticApi('Cron/GenerateSkypeAffiliateReport',array('month' => $month, 'year' => $year),false);
	}

	/**
	* @return type
	* @return 
	*/
	public static function SPNotification()
	{
		return self::StaticApi('Cron/SPNotification',array(),false);
	}
}
