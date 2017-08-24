<?php
/**
* INTERNAL USE ONLY!
*/
class CronApi extends ApiClient
{
	/**
	* Updates the Currencies
	*/
	public function UpdateCurrencies()
	{
		return $this->CallApi('Cron/UpdateCurrencies',array(),false);
	}

	/**
	*/
	public function UpdateFaxHistory()
	{
		return $this->CallApi('Cron/UpdateFaxHistory',array(),false);
	}

	/**
	* Get all the area codes and countries from voxbone and save them in our DB
	*/
	public function UpdateVoxbone()
	{
		return $this->CallApi('Cron/UpdateVoxbone',array(),false);
	}

	/**
	* Get all the area codes and countries from voxbone and save them in our DB Compatible with Voxbone API 3. Also updates Geo-Information too.
	*/
	public function UpdateVoxbone3()
	{
		return $this->CallApi('Cron/UpdateVoxbone3',array(),false);
	}

	/**
	*/
	public function ExpireOldCredits($limit = 20)
	{
		return $this->CallApi('Cron/ExpireOldCredits',array('limit' => $limit),false);
	}

	/**
	* Performs a system-wide cleanup.
	* 
	* Note: Be careful when triggering this and make sure you know what you are doing!
	*/
	public function Cleanup()
	{
		return $this->CallApi('Cron/Cleanup',array(),false);
	}

	/**
	* Performs a cleanup of old files
	* 
	* Note: Be careful when triggering this and make sure you know what you are doing!
	*/
	public function CleanupFiles()
	{
		return $this->CallApi('Cron/CleanupFiles',array(),false);
	}

	/**
	* Clears the system-global cache
	*/
	public function ClearGlobalCache()
	{
		return $this->CallApi('Cron/ClearGlobalCache',array(),false);
	}

	/**
	* Collects monitoring data.
	* 
	* Will also perform some basic system-health checks and send warning emails to alert@pamfax.biz
	*/
	public function Monitor()
	{
		return $this->CallApi('Cron/Monitor',array(),false);
	}

	/**
	* Counts all active user sessions.
	* 
	* Will be counted depending on their api-id and saved for later use in coneole (ActivityHeatmap)
	*/
	public function MonitorActiveSessions()
	{
		return $this->CallApi('Cron/MonitorActiveSessions',array(),false);
	}

	/**
	*/
	public function MonitorMonthly()
	{
		return $this->CallApi('Cron/MonitorMonthly',array(),false);
	}

	/**
	*/
	public function UpdatePlans($limit = 100)
	{
		return $this->CallApi('Cron/UpdatePlans',array('limit' => $limit),false);
	}

	/**
	* Fill up the monthly free pages (Pro Plan)
	*/
	public function FillUpMonthlyFreeCredits($emulate = 0)
	{
		return $this->CallApi('Cron/FillUpMonthlyFreeCredits',array('emulate' => $emulate),false);
	}

	/**
	* Send emails to users when they have a recent shop order in "New Order Placed" state
	*/
	public function NewOrderReminder()
	{
		return $this->CallApi('Cron/NewOrderReminder',array(),false);
	}

	/**
	*/
	public function RemindUnpaidFaxes($limit = 100)
	{
		return $this->CallApi('Cron/RemindUnpaidFaxes',array('limit' => $limit),false);
	}

	/**
	* Keeps our buffered Voxbone data up-to-date.
	* 
	* Validates unvalidated FaxIN numbers, cancels DIDs that are not in our Database
	* or assigns them to cbuenger, cancels refunded numbers at voxbone
	* @param string $canceldids if set to 1 it'll cancel DID's that are not in our Database
	*/
	public function CrawlVoxbone($canceldids = false)
	{
		return $this->CallApi('Cron/CrawlVoxbone',array('canceldids' => $canceldids),false);
	}

	/**
	* Ensures task-assignment.
	* 
	* Call at high frequency (once each 3-5 minutes)
	* Will ensure task processing even if tasks had MySQL or programming errors.
	* Will also perform Cleanup of unreferenced tasks, arguments and dependencies.
	*/
	public function Heartbeat()
	{
		return $this->CallApi('Cron/Heartbeat',array(),false);
	}

	/**
	* Resends failed emails.
	* 
	* Searches the messages table for emails that aren't sent and failed and trys to sent them again
	* @param int $limit the maximum number of mails that should be resend
	*/
	public function ResendUnsentMails($limit = 100)
	{
		return $this->CallApi('Cron/ResendUnsentMails',array('limit' => $limit),false);
	}

	/**
	* Counts all files in the filesystem that are not referenced in DB anymore.
	* 
	* Hints:
	* - Count is returned at the very end of the output
	* - Processing will take it's time (>1min) so be patient
	* 2010-08-06 11:00 -> Count = 24
	*/
	public function CountUselessFiles()
	{
		return $this->CallApi('Cron/CountUselessFiles',array(),false);
	}

	/**
	*/
	public function ReCheckCovers()
	{
		return $this->CallApi('Cron/ReCheckCovers',array(),false);
	}

	/**
	* Updates if new 2499 geolocations from fax_in_groups a day
	*/
	public function UpdateFaxInGeoLocation()
	{
		return $this->CallApi('Cron/UpdateFaxInGeoLocation',array(),false);
	}

	/**
	* Reminds users whose credit run low and who have at least one purchase
	*/
	public function SendCreditNotifications($limit = 100)
	{
		return $this->CallApi('Cron/SendCreditNotifications',array('limit' => $limit),false);
	}

	/**
	* Optimizes all db tables to reclaim unsused space and to defragment the data file
	*/
	public function OptimizeDatabases()
	{
		return $this->CallApi('Cron/OptimizeDatabases',array(),false);
	}

	/**
	* Cancels old numbers from our number pool at the Voxbone side of the world
	*/
	public function CancelPooledNumbers($limit = 100)
	{
		return $this->CallApi('Cron/CancelPooledNumbers',array('limit' => $limit),false);
	}

	/**
	* Validates current unverified numbers or
	*/
	public function ValidateNumbers($limit = 100)
	{
		return $this->CallApi('Cron/ValidateNumbers',array('limit' => $limit),false);
	}

	/**
	*/
	public function ProcessResellerInvoices($reseller_id)
	{
		return $this->CallApi('Cron/ProcessResellerInvoices',array('reseller_id' => $reseller_id),false);
	}

	/**
	*/
	public function ProcessAffiliateCreditNotes($affiliate_id = false)
	{
		return $this->CallApi('Cron/ProcessAffiliateCreditNotes',array('affiliate_id' => $affiliate_id),false);
	}

	/**
	*/
	public function GenerateSkypeAffiliateReport($month = false, $year = false)
	{
		return $this->CallApi('Cron/GenerateSkypeAffiliateReport',array('month' => $month, 'year' => $year),false);
	}

	/**
	* @return type
	* @return 
	*/
	public function SPNotification()
	{
		return $this->CallApi('Cron/SPNotification',array(),false);
	}
}
