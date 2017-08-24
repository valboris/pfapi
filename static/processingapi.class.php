<?php
/**
* Functionality to create/edit/cancel/delete faxes.
* 
* This is internal and will be called by the SPH systems.
*/
class ProcessingApi extends ApiClient
{
	/**
	* Translates a message for the given user.
	* 
	* Text constant to be translated is given as $text, all other data will be
	* used to generate replacements for the various placeholders in the text.
	*/
	public static function TranslateMessage($user, $container, $recipient, $text, $table = 'fax_history', $link_clickable = true)
	{
		return self::StaticApi('Processing/TranslateMessage',array('user' => $user, 'container' => $container, 'recipient' => $recipient, 'text' => $text, 'table' => $table, 'link_clickable' => $link_clickable),false);
	}

	/**
	* Sends a notification message to the given user.
	*/
	public static function SendNotificationMessage($user, $container, $recipient, $type, $text, $transmission_report = false)
	{
		return self::StaticApi('Processing/SendNotificationMessage',array('user' => $user, 'container' => $container, 'recipient' => $recipient, 'type' => $type, 'text' => $text, 'transmission_report' => $transmission_report),false);
	}

	/**
	* Processes all notifications for a given container and/or recipient.
	*/
	public static function ProcessNotifications($container, $recipient, $history = false)
	{
		return self::StaticApi('Processing/ProcessNotifications',array('container' => $container, 'recipient' => $recipient, 'history' => $history),false);
	}

	/**
	* Call this from remote processor to register it in the api backend.
	* 
	* Will return an id that must be used by processor with all subsequent
	* calls to identify itself.
	* @param string $identifier A human readable name for the processor
	*/
	public static function RegisterProcessor($identifier, $assembly = false, $version = false, $running = false, $actions = false, $cpu_speed = false, $cpu_count = false, $mem_total = false, $type_plugin = false, $class_name = false, $opt_destination = false, $opt_name = false, $opt_value = false, $opt_value_type = false, $threads = false)
	{
		return self::StaticApi('Processing/RegisterProcessor',array('identifier' => $identifier, 'assembly' => $assembly, 'version' => $version, 'running' => $running, 'actions' => $actions, 'cpu_speed' => $cpu_speed, 'cpu_count' => $cpu_count, 'mem_total' => $mem_total, 'type_plugin' => $type_plugin, 'class_name' => $class_name, 'opt_destination' => $opt_destination, 'opt_name' => $opt_name, 'opt_value' => $opt_value, 'opt_value_type' => $opt_value_type, 'threads' => $threads),false);
	}

	/**
	* Dummy function to let processors report only their working info.
	* 
	* These will be stored in ProcessorApi::UpdateProcessor automatically
	* Will also return cancelled and/or aborted tasks for the calling processor if there are some.
	*/
	public static function Ping($processor_id, $actions, $rt = array ())
	{
		return self::StaticApi('Processing/Ping',array('processor_id' => $processor_id, 'actions' => $actions, 'rt' => $rt),false);
	}

	/**
	* Allows processors to report the state of their plugins.
	*/
	public static function ReportPluginState($processor_id, $assembly = false, $version = false, $running = false, $threads = false, $opt_destination = false, $opt_name = false, $opt_value = false, $opt_value_type = false)
	{
		return self::StaticApi('Processing/ReportPluginState',array('processor_id' => $processor_id, 'assembly' => $assembly, 'version' => $version, 'running' => $running, 'threads' => $threads, 'opt_destination' => $opt_destination, 'opt_name' => $opt_name, 'opt_value' => $opt_value, 'opt_value_type' => $opt_value_type),false);
	}

	/**
	* For testing SPH.
	* 
	* Will be called when you click on 'Test' in the SPH main window.
	*/
	public static function TestSPH()
	{
		return self::StaticApi('Processing/TestSPH',array(),false);
	}

	/**
	* Checks for SPH updates.
	*/
	public static function CheckForUpdates($processor_id)
	{
		return self::StaticApi('Processing/CheckForUpdates',array('processor_id' => $processor_id),false);
	}

	/**
	* Returns a list of tasks that may be processed.
	* 
	* Reserves Tasks for calling processor
	* @param int $processor_id ID of calling processor (returned by RegisterProcessor)
	* @param array $actions Array of action types to be returned
	*/
	public static function ListTasks($processor_id, $actions, $rt = array ())
	{
		return self::StaticApi('Processing/ListTasks',array('processor_id' => $processor_id, 'actions' => $actions, 'rt' => $rt),false);
	}

	/**
	* Sets a tasks started property to now().
	* @param int $processor_id ID of calling processor (returned by RegisterProcessor)
	* @param int $task_id ID of task to be started
	*/
	public static function TaskStarted($processor_id, $task_id)
	{
		return self::StaticApi('Processing/TaskStarted',array('processor_id' => $processor_id, 'task_id' => $task_id),false);
	}

	/**
	* Sets a task to a completed state. Added marker "stop". Use true if needed stop task now and prevent re-start task. If stop = true call ForceStop task.
	* @param int $processor_id ID of calling processor (returned by RegisterProcessor)
	* @param int $task_id ID of task to be completed
	* @param bool $succeeded true if task succeeded, else false
	* @param string $message a message to be added to the task
	* @param bool $stop true if need to stop repeating task, else false
	*/
	public static function TaskCompleted($processor_id, $task_id, $succeeded, $message = false, $stop = false)
	{
		return self::StaticApi('Processing/TaskCompleted',array('processor_id' => $processor_id, 'task_id' => $task_id, 'succeeded' => $succeeded, 'message' => $message, 'stop' => $stop),false);
	}

	/**
	* Sets a task to a cancelled state.
	* @param int $processor_id ID of calling processor (returned by RegisterProcessor)
	* @param int $task_id ID of task to be cancelled
	*/
	public static function TaskCancelled($processor_id, $task_id)
	{
		return self::StaticApi('Processing/TaskCancelled',array('processor_id' => $processor_id, 'task_id' => $task_id),false);
	}

	/**
	* Displays a MHTML(MHT) page that represents a coverpage.
	* 
	* Will be called from converter to generate Coverpages.
	* @param int $processor_id ID of calling processor (returned by RegisterProcessor)
	* @param int $container_id ID of the fax_container
	* @param int $recipient_id ID of the fax_recipient
	*/
	public static function ShowCover($processor_id, $container_id, $recipient_id = false)
	{
		return self::StaticApi('Processing/ShowCover',array('processor_id' => $processor_id, 'container_id' => $container_id, 'recipient_id' => $recipient_id),false);
	}

	/**
	* Stores a converted coverpage into the API backend.
	* @param int $processor_id ID of calling processor (returned by RegisterProcessor)
	* @param int $container_id ID of the fax_container
	* @param int $recipient_id ID of the fax_recipient
	* @param int $pages Optional number of pages in the file
	*/
	public static function StoreCover($processor_id, $task_id, $container_id, $recipient_id, $pages = false, $file_size = 0, $file_contentmd5 = '')
	{
		return self::StaticApi('Processing/StoreCover',array('processor_id' => $processor_id, 'task_id' => $task_id, 'container_id' => $container_id, 'recipient_id' => $recipient_id, 'pages' => $pages, 'file_size' => $file_size, 'file_contentmd5' => $file_contentmd5),false);
	}

	/**
	*/
	public static function StoreCoverPreview($file_id, $file_size = 0, $file_contentmd5 = '')
	{
		return self::StaticApi('Processing/StoreCoverPreview',array('file_id' => $file_id, 'file_size' => $file_size, 'file_contentmd5' => $file_contentmd5),false);
	}

	/**
	* Stores a converted file into the API backend.
	* @param int $processor_id ID of calling processor (returned by RegisterProcessor)
	* @param int $container_id ID of the fax_container
	* @param int $srcfile_id ID of the source file that was converted
	* @param int $pages Optional number of pages in the file
	*/
	public static function StoreConvertedFile($processor_id, $container_id, $srcfile_id, $pages = false, $task_id, $create_extractors = true, $srcfile_take = false, $file_size = 0, $file_contentmd5 = '')
	{
		return self::StaticApi('Processing/StoreConvertedFile',array('processor_id' => $processor_id, 'container_id' => $container_id, 'srcfile_id' => $srcfile_id, 'pages' => $pages, 'task_id' => $task_id, 'create_extractors' => $create_extractors, 'srcfile_take' => $srcfile_take, 'file_size' => $file_size, 'file_contentmd5' => $file_contentmd5),false);
	}

	/**
	* Stores a file's page into the API backend.
	* @param int $processor_id ID of calling processor (returned by RegisterProcessor)
	* @param int $user_id ID of the user this file belongs to
	* @param int $file_id ID of the source file (that was uploaded)
	* @param int $converted_id ID of the converted file
	* @param int $page_no Optional number of pages in the file
	*/
	public static function StorePage($processor_id, $user_id, $file_id, $converted_id, $page_no, $file_size, $file_contentmd5, $task_id = false)
	{
		return self::StaticApi('Processing/StorePage',array('processor_id' => $processor_id, 'user_id' => $user_id, 'file_id' => $file_id, 'converted_id' => $converted_id, 'page_no' => $page_no, 'file_size' => $file_size, 'file_contentmd5' => $file_contentmd5, 'task_id' => $task_id),false);
	}

	/**
	* Stores a pre-merged file into the API backend.
	* 
	* This contains all converted files pages, but NOT the covers pages
	*/
	public static function StorePreMergedFile($processor_id, $task_id, $container_id)
	{
		return self::StaticApi('Processing/StorePreMergedFile',array('processor_id' => $processor_id, 'task_id' => $task_id, 'container_id' => $container_id),false);
	}

	/**
	* Stores a merged file into the API backend.
	* 
	* This one will then be used for downloads when the user requests it.
	*/
	public static function StoreMergedFile($processor_id, $task_id, $container_id, $recipient_id, $filename, $pages, $is_pam2pam = false, $file_size, $file_contentmd5, $is_format_pdf = true)
	{
		return self::StaticApi('Processing/StoreMergedFile',array('processor_id' => $processor_id, 'task_id' => $task_id, 'container_id' => $container_id, 'recipient_id' => $recipient_id, 'filename' => $filename, 'pages' => $pages, 'is_pam2pam' => $is_pam2pam, 'file_size' => $file_size, 'file_contentmd5' => $file_contentmd5, 'is_format_pdf' => $is_format_pdf),false);
	}

	/**
	* Returns a file from the API Storage.
	* @param int $file_id ID of the file to get
	*/
	public static function GetFile($file_id, $processor_id = false, $task_id = false)
	{
		return self::StaticApi('Processing/GetFile',array('file_id' => $file_id, 'processor_id' => $processor_id, 'task_id' => $task_id),false);
	}

	/**
	* Sets the Exsker transport ID for the given task.
	* 
	* This will update all dependent Tasks too.
	* @param int $processor_id ID of calling processor (returned by RegisterProcessor)
	* @param int $task_id ID of the send-to-esker task
	* @param int $transport_id The Transport-ID returned from Esker
	*/
	public static function SetEskerTransportId($processor_id, $task_id, $transport_id)
	{
		return self::StaticApi('Processing/SetEskerTransportId',array('processor_id' => $processor_id, 'task_id' => $task_id, 'transport_id' => $transport_id),false);
	}

	/**
	* Sets the Monopond transport ID for the given task.
	* 
	* This will update all dependent Tasks too.
	* @param int $processor_id ID of calling processor (returned by RegisterProcessor)
	* @param int $task_id ID of the send-to-esker task
	* @param int $transport_id The Transport-ID returned from Monopond (or generated by SPH)
	*/
	public static function SetMonopondTransportId($processor_id, $task_id, $transport_id)
	{
		return self::StaticApi('Processing/SetMonopondTransportId',array('processor_id' => $processor_id, 'task_id' => $task_id, 'transport_id' => $transport_id),false);
	}

	/**
	* Sets the ByFax transport ID for the given task.
	* 
	* This will update all dependent Tasks too.
	* @param int $processor_id ID of calling processor (returned by RegisterProcessor)
	* @param int $task_id ID of the send-to-esker task
	* @param int $transport_id The Transport-ID returned from ByFax (or generated by SPH)
	*/
	public static function SetByFaxTransportId($processor_id, $task_id, $transport_id)
	{
		return self::StaticApi('Processing/SetByFaxTransportId',array('processor_id' => $processor_id, 'task_id' => $task_id, 'transport_id' => $transport_id),false);
	}

	/**
	* Sets the number of pages a coverpage contains.
	* 
	* Will be called from PrecompileCoverTask.
	* @param int $processor_id ID of calling processor (returned by RegisterProcessor)
	* @param int $container_id ID of the fax_container
	* @param int $pages Number of pages in the coverpage
	*/
	public static function SetCoverpageLength($processor_id, $container_id, $pages)
	{
		return self::StaticApi('Processing/SetCoverpageLength',array('processor_id' => $processor_id, 'container_id' => $container_id, 'pages' => $pages),false);
	}

	/**
	* Returns all pages for the given container/recipient
	* @param int $processor_id ID of calling processor (returned by RegisterProcessor)
	* @param int $container_id ID of the fax_container
	* @param int $recipient_id ID the FaxRecipient
	*/
	public static function ListFaxPages($processor_id, $task_id, $container_id, $recipient_id, $without_cover = false, $only_cover = false)
	{
		return self::StaticApi('Processing/ListFaxPages',array('processor_id' => $processor_id, 'task_id' => $task_id, 'container_id' => $container_id, 'recipient_id' => $recipient_id, 'without_cover' => $without_cover, 'only_cover' => $only_cover),false);
	}

	/**
	* Sets the state of a fax sent via esker.
	* 
	* Will only work on final states and create all history entries.
	* @param int $processor_id ID of calling processor (returned by RegisterProcessor)
	* @param int task_id ID task
	* @param int $recipient_id ID the FaxRecipient
	* @param array $data key-value-pairs data delivered by the esker query service
	*/
	public static function SetEskerState($processor_id, $task_id, $recipient_id, $data)
	{
		return self::StaticApi('Processing/SetEskerState',array('processor_id' => $processor_id, 'task_id' => $task_id, 'recipient_id' => $recipient_id, 'data' => $data),false);
	}

	/**
	* Sets the state of a fax sent via monopond.
	* 
	* Will only work on final states and create all history entries.
	* @param int $processor_id ID of calling processor (returned by RegisterProcessor)
	* @param int $recipient_id ID the FaxRecipient
	* @param array $data key-value-pairs data delivered by the esker query service
	*/
	public static function SetMonopondState($processor_id, $task_id, $recipient_id, $data)
	{
		return self::StaticApi('Processing/SetMonopondState',array('processor_id' => $processor_id, 'task_id' => $task_id, 'recipient_id' => $recipient_id, 'data' => $data),false);
	}

	/**
	* Sets the state of a fax sent via byfax.
	* 
	* Will only work on final states and create all history entries.
	* @param int $processor_id ID of calling processor (returned by RegisterProcessor)
	* @param int $recipient_id ID the FaxRecipient
	* @param array $data key-value-pairs data delivered by the esker query service
	*/
	public static function SetByFaxState($processor_id, $task_id, $recipient_id, $data)
	{
		return self::StaticApi('Processing/SetByFaxState',array('processor_id' => $processor_id, 'task_id' => $task_id, 'recipient_id' => $recipient_id, 'data' => $data),false);
	}

	/**
	* Called when a conversion failed.
	* 
	* Note: This will NOT be called for step 1 doc coversion, but
	* only in sending process when a Merger Task fails
	*/
	public static function ConversionFailed($processor_id, $task_id, $recipient_id = false, $container_id = false, $check_first = false)
	{
		return self::StaticApi('Processing/ConversionFailed',array('processor_id' => $processor_id, 'task_id' => $task_id, 'recipient_id' => $recipient_id, 'container_id' => $container_id, 'check_first' => $check_first),false);
	}

	/**
	* Called when delivering a fax to Esker failed.
	*/
	public static function DeliveryFailed($processor_id, $task_id, $recipient_id)
	{
		return self::StaticApi('Processing/DeliveryFailed',array('processor_id' => $processor_id, 'task_id' => $task_id, 'recipient_id' => $recipient_id),false);
	}

	/**
	* Used in Sandbox to fake sending.
	*/
	public static function SandboxSetEskerState($processor_id, $task_id, $recipient_id)
	{
		return self::StaticApi('Processing/SandboxSetEskerState',array('processor_id' => $processor_id, 'task_id' => $task_id, 'recipient_id' => $recipient_id),false);
	}

	/**
	* Redirects a fax to a pamfax fax-in number.
	*/
	public static function RedirectToInbox($processor_id, $task_id, $recipient_id, $file_id = false)
	{
		return self::StaticApi('Processing/RedirectToInbox',array('processor_id' => $processor_id, 'task_id' => $task_id, 'recipient_id' => $recipient_id, 'file_id' => $file_id),false);
	}

	/**
	* Deletes all recipient/container datasets.
	*/
	public static function CleanupFaxJob($container_id)
	{
		return self::StaticApi('Processing/CleanupFaxJob',array('container_id' => $container_id),false);
	}

	/**
	* Sets the state of a sms sent via sms77.
	* @param int $processor_id ID of calling processor (returned by RegisterProcessor)
	* @param int $recipient_id ID the MessageModel
	* @param int $sms_id ID of the sms at sms77
	* @param bool $status_timeout tru if a timeout occured while getting the sms status
	* @deprecated Now system uses BulkSMS as provider, so SetBulkSMSStatus is used
	*/
	public static function SetSMS77Status($processor_id, $message_id, $sms_id = false, $status_timeout)
	{
		return self::StaticApi('Processing/SetSMS77Status',array('processor_id' => $processor_id, 'message_id' => $message_id, 'sms_id' => $sms_id, 'status_timeout' => $status_timeout),false);
	}

	/**
	* Sets the state of a sms sent via BulkSMS.
	* @param int $processor_id ID of calling processor (returned by RegisterProcessor)
	* @param int $recipient_id ID the MessageModel
	* @param int $sms_id ID of the sms at BulkSMS
	* @param bool $status_timeout tru if a timeout occured while getting the sms status
	*/
	public static function SetBulkSMSStatus($processor_id, $message_id, $sms_id = false, $status_timeout)
	{
		return self::StaticApi('Processing/SetBulkSMSStatus',array('processor_id' => $processor_id, 'message_id' => $message_id, 'sms_id' => $sms_id, 'status_timeout' => $status_timeout),false);
	}

	/**
	* Handler for ServerSideTask.
	* 
	* Sends an eMail message after filling out the needed variables.
	*/
	public static function SendFaxMail($processor_id, $message_id)
	{
		return self::StaticApi('Processing/SendFaxMail',array('processor_id' => $processor_id, 'message_id' => $message_id),false);
	}

	/**
	* Creates all tasks needed for sending a fax.
	* 
	* Used to async process Send() calls on fax job
	*/
	public static function CreateSendingTasks($task_id = false, $container_id, $rec_id_offset = 0)
	{
		return self::StaticApi('Processing/CreateSendingTasks',array('task_id' => $task_id, 'container_id' => $container_id, 'rec_id_offset' => $rec_id_offset),false);
	}

	/**
	*/
	public static function CancelFaxJob($task_id, $container_id, $recipient_id = false, $was_delayed = false, $rec_id_offset = 0, $console_cancel = 0)
	{
		return self::StaticApi('Processing/CancelFaxJob',array('task_id' => $task_id, 'container_id' => $container_id, 'recipient_id' => $recipient_id, 'was_delayed' => $was_delayed, 'rec_id_offset' => $rec_id_offset, 'console_cancel' => $console_cancel),false);
	}

	/**
	* This is first-shot implementation following Dicks requests.
	* 
	* Needs to be extended/rewritten later when requirements change!
	*/
	public static function OnlineBackup($processor_id, $task_id, $user_id, $provider, $file_id, $file_path)
	{
		return self::StaticApi('Processing/OnlineBackup',array('processor_id' => $processor_id, 'task_id' => $task_id, 'user_id' => $user_id, 'provider' => $provider, 'file_id' => $file_id, 'file_path' => $file_path),false);
	}

	/**
	* DEPRICATED! Since 3.5MR3. All operations for FaxForwarding now done in FaxJob API without any calls of SPH.
	*/
	public static function ListFilePages($file_id, $processor_id = false, $task_id = false)
	{
		return self::StaticApi('Processing/ListFilePages',array('file_id' => $file_id, 'processor_id' => $processor_id, 'task_id' => $task_id),false);
	}

	/**
	* DEPRICATED! Since 3.5MR3. All operations for FaxForwarding now done in FaxJob API without any calls of SPH.
	*/
	public static function ReplaceFile($file_id, $file_size = false, $file_contentmd5 = false, $processor_id = false, $task_id = false, $use_original = false)
	{
		return self::StaticApi('Processing/ReplaceFile',array('file_id' => $file_id, 'file_size' => $file_size, 'file_contentmd5' => $file_contentmd5, 'processor_id' => $processor_id, 'task_id' => $task_id, 'use_original' => $use_original),false);
	}

	/**
	*/
	public static function RecipientsToContacts($container_id)
	{
		return self::StaticApi('Processing/RecipientsToContacts',array('container_id' => $container_id),false);
	}
}
