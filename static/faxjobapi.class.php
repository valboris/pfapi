<?php
/**
* All function needed to create a fax.
* 
* Minimum steps to send a fax:
* - Create
* - AddRecipient
* - AddFile OR AddRemoteFile OR SetCover
* - call GetFaxState in a loop until it returns "ready_to_send"
* - Send
*/
class FaxJobApi extends ApiClient
{
	/**
	* Creates a new fax in the API backend and returns it.
	* 
	* If a fax job is currently in edit mode in this session, this fax job is returned instead.
	* Note: This is not an error. It provides you with the possibility to continue with the fax.
	* @param string $user_ip The IP address of the client (if available). If you pass an invalid IP address (i.e. just a "0"), the current public caller IP will be used.
	* @param string $user_agent User agent string of the client device. If not available, put something descriptive (like "iPhone OS 2.2")
	* @param string $origin From where was this fax started? i.e. "printer", "desktop", "home", ... For reporting and analysis purposes.
	*/
	public static function Create($user_ip, $user_agent, $origin)
	{
		return self::StaticApi('FaxJob/Create',array('user_ip' => $user_ip, 'user_agent' => $user_agent, 'origin' => $origin),false);
	}

	/**
	* Clones an already sent fax in the API backend and returns it.
	* @param string $uuid The uuid of the source fax
	* @param string $user_ip The IP address of the client (if available). Put your own IP address otherwise.
	* @param string $user_agent User agent string of the client device. If not available, put something descriptive (like "iPhone OS 2.2")
	*/
	public static function CloneFax($uuid, $user_ip, $user_agent)
	{
		return self::StaticApi('FaxJob/CloneFax',array('uuid' => $uuid, 'user_ip' => $user_ip, 'user_agent' => $user_agent),false);
	}

	/**
	* Adds a recipient to the current fax.
	* 
	* Note that the name may be 80 characters long. More characters will be cut off.
	* @param string $number The recipients number in international format
	* @param string $name Optional recipients name
	*/
	public static function AddRecipient($number, $name = false)
	{
		return self::StaticApi('FaxJob/AddRecipient',array('number' => $number, 'name' => $name),false);
	}

	/**
	* Adds recipients to the current fax.
	* 
	* The given recipients will be added to current recipients.
	* Note that each name may be 80 characters long. More characters will be cut off.
	* @param string $numbers Array of recipient numbers, each in international format
	* @param string $names Optional array of recipient names, lnegth must match length of numbers argument
	*/
	public static function AddRecipients($numbers, $names = false)
	{
		return self::StaticApi('FaxJob/AddRecipients',array('numbers' => $numbers, 'names' => $names),false);
	}

	/**
	* Creates recipients for the current fax.
	* 
	* All recipients are replaced with the given ones!
	* Note that each name may be 80 characters long. More characters will be cut off.
	* @param string $numbers Array of recipient numbers, each in international format
	* @param string $names Optional array of recipient names, lnegth must match length of numbers argument
	*/
	public static function SetRecipients($numbers, $names = false)
	{
		return self::StaticApi('FaxJob/SetRecipients',array('numbers' => $numbers, 'names' => $names),false);
	}

	/**
	* Returns the recipients for the current fax.
	* @param int $current_page The page which should be shown
	* @param int $items_per_page How many items are shown per page
	*/
	public static function ListRecipients($current_page = 1, $items_per_page = 100)
	{
		return self::StaticApi('FaxJob/ListRecipients',array('current_page' => $current_page, 'items_per_page' => $items_per_page),false);
	}

	/**
	* Removes all recipients for the current fax.
	*/
	public static function RemoveAllRecipients()
	{
		return self::StaticApi('FaxJob/RemoveAllRecipients',array(),false);
	}

	/**
	* Removes a recipient from the current fax
	* @param string $number The recipients number in international format
	*/
	public static function RemoveRecipient($number)
	{
		return self::StaticApi('FaxJob/RemoveRecipient',array('number' => $number),false);
	}

	/**
	* Adds a file to the current fax.
	* 
	* Requires the file to be uploaded as POST paramter named 'file' as a standard HTTP upload. This could be either Content-type: multipart/form-data with file content as base64-encoded data or as Content-type: application/octet-stream with just the binary data.
	* See http://www.faqs.org/rfcs/rfc1867.html for documentation on file uploads.
	* @param string $filename Name of the file. You can also use the same file name for each file (i.e "fax.pdf")
	* @param string $origin Optional file origin (ex: photo, scan,... - maximum length is 20 characters).
	*/
	public static function AddFile($localFile, $origin = '', $file_size = '', $file_contentmd5 = '')
	{
		if (empty($origin)) {
			$parts = explode('.', $localFile);
			$origin = 'file_'.uniqid().'.'.strtolower(array_pop($parts));
		}

		$postdata = [
			'filename'              => "@".$localFile,
			//'filename'          => $origin,
			'origin'            => $origin,
		];
		//return self::StaticApi('FaxJob/AddFile', $postdata, false);
		return self::StaticApi('FaxJob/AddFile',array('filename' => $postdata['filename'], 'origin' => $origin, 'file_size' => $file_size, 'file_contentmd5' => $file_contentmd5),false);
	}

	/**
	* Add a remote file to the fax.
	* 
	* The url parameter may contain username:password for basic http auth, but this is the only supported
	* authentication method.
	* URL Examples:
	* http://myusername:andpassord@somewhere.com/download.php?id=0815
	* https://myusername:andpassord@somewhere.com/image1.jpeg
	* http://www.justplaindomain.com/test.txt
	* @param string $url The url of the file to add
	*/
	public static function AddRemoteFile($url)
	{
		return self::StaticApi('FaxJob/AddRemoteFile',array('url' => $url),false);
	}

	/**
	* Add a file identified by an online storage identifier.
	* 
	* You'll have to use the OnlineStorageApi to identify a user for an online storage provider first
	* and then get listings of his files. These will contain the file identifiers used by this method.
	* @param string $provider Identifies the provider used (see OnlineStorageApi)
	* @param string $uuid Identifies the file to be added (see OnlineStorageApi)
	*/
	public static function AddFileFromOnlineStorage($provider, $uuid)
	{
		return self::StaticApi('FaxJob/AddFileFromOnlineStorage',array('provider' => $provider, 'uuid' => $uuid),false);
	}

	/**
	* Adds another fax to this one
	* 
	* Use to add the resulting PDF from another fax (incoming or outgoing)
	* to this fax. This way you may forward a fax to another recipient.
	* @param string $fax_uuid Identifies the fax to be added
	*/
	public static function AddAnotherFax($fax_uuid)
	{
		return self::StaticApi('FaxJob/AddAnotherFax',array('fax_uuid' => $fax_uuid),false);
	}

	/**
	* Remove a file from the current fax.
	* @param string $file_uuid Identifies the file to be removed
	*/
	public static function RemoveFile($file_uuid)
	{
		return self::StaticApi('FaxJob/RemoveFile',array('file_uuid' => $file_uuid),false);
	}

	/**
	* Remove all uploaded files from the current fax
	*/
	public static function RemoveAllFiles()
	{
		return self::StaticApi('FaxJob/RemoveAllFiles',array(),false);
	}

	/**
	* Get all uploaded files for the current fax
	*/
	public static function ListFaxFiles()
	{
		return self::StaticApi('FaxJob/ListFaxFiles',array(),false);
	}

	/**
	* Sets the cover template for the current fax.
	* 
	* <b>IMPORTANT NOTE:
	* This method allowed only selection of cover template using the cover_id
	* argument up to and including API 3.4MR8. We kept that argument valid in API 3.5
	* meaning that the UUIDs for the standard covers are the same as their ID.
	* We will remove the template_id argument in future versions of this API
	* and most likely reorder the arguments.
	* So: for now everything is backwards compatible, but we will break that soon,
	* so please update your code to use the UUID pattern. Just do not pass template_id
	* anymore and pass template_uuid instead.</b>
	* Since API 3.5 you may set your own covers using the UserInfoAPI. See AddCover method there
	* for a detailed description of standard placeholders, here's just the list:
	* {ToName} {FaxNumber} {FromName} {Date} {Page} {Pages} {Message}
	* Using the $data $argument you can fill up the cover with user-specific data
	* other than the standard placeholders.
	* You may also use it to overwrite the standard placeholders values with you own data.
	* Your data keys should be build up like this: {my_var} (including the brackets)
	* Sample:
	* "{MyOwnName}"=>"PamFax Tester",
	* "{MyOwnLorem}"=>"Ipsum"
	* Note that you may send in more data than actually present in the cover template, meaning that if you
	* provide your own set of standard covers with your own placeholders, you may pass the values in here
	* even if the user selected another/an own cover without the placeholders inside.
	* @param int $template_id The id of the cover. Use 0 to remove cover.
	* @param string $text Cover text as pure text.
	* @param string $template_uuid Cover UUID as returned by UserInfoAPI::ListCovers
	* @param array $data Optional data to fill placeholders in the coverpage template (key-value pairs)
	*/
	public static function SetCover($template_id = false, $text = '', $template_uuid = '*', $data = false)
	{
		return self::StaticApi('FaxJob/SetCover',array('template_id' => $template_id, 'text' => $text, 'template_uuid' => $template_uuid, 'data' => $data),false);
	}

	/**
	* Sets the notification options for the current fax.
	* 
	* Notification options that are not in the array will not be changed/resetted.
	* Note: defaults for notification settings will be taken from users account, so potentially not need
	* to call this on every fax.
	* @param array $notifications An array of notification settings. Currently supported: sms, email, skypechat. one per row with 0/1 as value
	* @param int $group_notification Send grouped notifications for the whole job (makes sense only if multiple recipients)
	* @param int $error_notification Send only notificatiosn when an error occured
	* @param int $save_defaults Save Notification-Settings to user´s Profile
	* @deprecated We do not support setting these settings per fax anymore. Please use UserInfo::SetNotifyProviderSettings and UserInfo::SetGlobalNotifySettings instead to set the profile-wide settings.
	*/
	public static function SetNotifications($notifications, $group_notification = false, $error_notification = false, $save_defaults = false)
	{
		return self::StaticApi('FaxJob/SetNotifications',array('notifications' => $notifications, 'group_notification' => $group_notification, 'error_notification' => $error_notification, 'save_defaults' => $save_defaults),false);
	}

	/**
	* Resets the notification options for the current fax to their default values.
	* 
	* Notification options will be set to what is set for the user as default.
	*/
	public static function ResetNotifications()
	{
		return self::StaticApi('FaxJob/ResetNotifications',array(),false);
	}

	/**
	* Removes the cover from fax
	*/
	public static function RemoveCover()
	{
		return self::StaticApi('FaxJob/RemoveCover',array(),false);
	}

	/**
	* Returns a list of all coverpages the user may use.
	* 
	* Result includes the "no cover" if the fax job already contains a file as in that case
	* there's no need to add a cover.
	*/
	public static function ListAvailableCovers($defaults_if_empty = true)
	{
		return self::StaticApi('FaxJob/ListAvailableCovers',array('defaults_if_empty' => $defaults_if_empty),false);
	}

	/**
	* Returns the state of the current fax.
	* 
	* state can contain many different states, but the three mentioanable here
	* are: editing, ready_to_send, not_enough_credit
	* So when you are awaiting a fax to be ready you should test for 'ready_to_send' or
	* 'not_enough_credit'. In any other case it is still in progress.
	*/
	public static function GetFaxState()
	{
		return self::StaticApi('FaxJob/GetFaxState',array(),false);
	}

	/**
	* Starts creating the preview for this fax.
	* 
	* Call after fax is ready (GetFaxState returns FAX_READY_TO_SEND)
	* @deprecated Use FaxJob::GetPreview instead
	*/
	public static function StartPreviewCreation()
	{
		return self::StaticApi('FaxJob/StartPreviewCreation',array(),false);
	}

	/**
	* Returns the states of all preview pages.
	* 
	* Call after fax is ready (GetFaxState returns FAX_READY_TO_SEND).
	*/
	public static function GetPreview()
	{
		return self::StaticApi('FaxJob/GetPreview',array(),false);
	}

	/**
	* <strong>Note!</strong> $send_at or $datetime required.
	* @param string $fax_uuid Fax container uuid with delayed state
	* @param string $send_at_timezone example Europe/Moscow
	* @param string $datetime example: 2014-01-01 10:20:30
	*/
	public static function EditDelayedFax($fax_uuid, $send_at = false, $send_at_timezone = false, $datetime = false)
	{
		return self::StaticApi('FaxJob/EditDelayedFax',array('fax_uuid' => $fax_uuid, 'send_at' => $send_at, 'send_at_timezone' => $send_at_timezone, 'datetime' => $datetime),false);
	}

	/**
	* Start the fax sending.
	* 
	* Only successfull if all necessary data is set to the fax: at least 1 recipient and a cover page or a file uploaded.
	* Will only work if user has enough credit to pay for the fax.
	* You may pass in a datetime when the fax shall be sent. This must be a string formatted in the users chosen culture
	* (so exactly as you would show it to him) and may not be in the past nor be greater than 'now + 14days'.
	* Additionally you may give a timezone the send_at value is in.
	* @param string $send_at Datetime when the fax shall be sent
	* @param string $datetime Datetime in MySql format (example, 2014-10-12 04:56:59). note! if passed, $send_at value will be ignored
	*/
	public static function Send($send_at = false, $send_at_timezone = false, $datetime = false)
	{
		return self::StaticApi('FaxJob/Send',array('send_at' => $send_at, 'send_at_timezone' => $send_at_timezone, 'datetime' => $datetime),false);
	}

	/**
	* Put the fax in the unpaid faxes queue.
	* 
	* Only possible if user has NOT enough credit to send this fax directly.
	* You may pass in a datetime when the fax shall be sent. This must be a string formatted in the users chosen culture
	* (so exactly as you would show it to him) and may not be in the past nor be greater than 'now + 14days'.
	* Additionally you may give a timezone the send_at value is in.
	* @param string $send_at Datetime when the fax shall be sent
	* @param string $datetime Datetime in MySql format (example, 2014-10-12 04:56:59). note! if passed, $send_at value will be ignored
	*/
	public static function SendLater($send_at = false, $send_at_timezone = false, $datetime = false)
	{
		return self::StaticApi('FaxJob/SendLater',array('send_at' => $send_at, 'send_at_timezone' => $send_at_timezone, 'datetime' => $datetime),false);
	}

	/**
	* Send unpaid faxes
	* 
	* Will work until credit reaches zero.
	* Will return two lists: SentFaxes and UnpaidFaxes that contain the
	* faxes that could or not be sent.
	* @param string $uuids Array of UUIDs of unpaid faxes
	*/
	public static function SendUnpaidFaxes($uuids)
	{
		return self::StaticApi('FaxJob/SendUnpaidFaxes',array('uuids' => $uuids),false);
	}

	/**
	* Send a previously delayed fax now.
	* 
	* Use this method if you want to send a fax right now that was initially delayed (by giving a send_at value into Send).
	* @param string $uuid UUID previously delayed fax
	*/
	public static function SendDelayedFaxNow($uuid)
	{
		return self::StaticApi('FaxJob/SendDelayedFaxNow',array('uuid' => $uuid),false);
	}

	/**
	* Cancels fax sending for a fax recipient or a whole fax job.
	* 
	* If siblings_too is true will cancel all faxes in the job the
	* fax with uuid belongs to.
	* @param string $uuid UUID of the fax to be cancelled
	* @param bool $siblings_too If true cancels all other faxes (recipients) in the same job
	*/
	public static function Cancel($uuid, $siblings_too = false)
	{
		return self::StaticApi('FaxJob/Cancel',array('uuid' => $uuid, 'siblings_too' => $siblings_too),false);
	}

	/**
	* Sends a fax synchronously.
	* 
	* Be careful when using this method: It will return once the fax reached sending state or an error occured.
	* This may take some time and perhaps the sending user will not have enough credit, so faxing will fail.
	* Note1: If you want to add files to the fax use POST method (multipart/formdata) to attach them.
	* QuickSend will then add them to the fax.
	* Note2: You must ether give cover_id+cover_text OR upload files, otherwise QuickSend will fail.
	* @param string $username Username of the sending user
	* @param string $password Password of the sending user
	* @param string $user_ip Current IP of user
	* @param string $origin The fax origin
	* @param string|array $rec_number One or many numbers (must be valid, invalid will silenty be ignored)
	* @param string|array $rec_name One or many recipient names (count must match rec_number count)
	* @param int $cover_id Optional covers id
	* @param string $cover_text Optional cover text
	*/
	public static function QuickSend($username, $password, $user_ip, $origin, $rec_number, $rec_name, $cover_id = false, $cover_text = false)
	{
		return self::StaticApi('FaxJob/QuickSend',array('username' => $username, 'password' => $password, 'user_ip' => $user_ip, 'origin' => $origin, 'rec_number' => $rec_number, 'rec_name' => $rec_name, 'cover_id' => $cover_id, 'cover_text' => $cover_text),false);
	}

	/**
	* Sets sender details
	* 
	* Allows you to set the faxs sender details which will be shown on the coverpage (if present)
	* and in the fax header.  Note that setting these values to an empty string is possible too and
	* will oerride the defaults. Setting them to false will release and use the defaults again.
	* @param string $number Fax sender number (max 50 chars)
	* @param string $name Fax sender name (max 50 chars)
	*/
	public static function SetSenderDetails($number = false, $name = false)
	{
		return self::StaticApi('FaxJob/SetSenderDetails',array('number' => $number, 'name' => $name),false);
	}

	/**
	* Returns count in progress faxes: States "In queue", "Delivering", "Sending"
	* 
	* Also, will be returned count of unpaid faxes
	* Short info about fax will be contain:
	* recipient_uuid, recipient_id, container_id, state (as code and as string), created, updated times
	*/
	public static function FaxesInProgress()
	{
		return self::StaticApi('FaxJob/FaxesInProgress',array(),false);
	}
}
