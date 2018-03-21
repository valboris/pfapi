<?php
/**
* Users account management
* 
* The UserInfo API contains functionality to get information
* about users and "their" faxes
*/
class UserInfoApi extends ApiClient {

    /**
     * Check SkypeBot connection status.
     * @return boolean
     *
     */ public static function IsSkypeBotConnected() {
         return self::StaticApi('UserInfo/IsSkypeBotConnected',array(),true);
     }

	/**
	* Validate a username for a new user.
	* 
	* Returns a list of suggestions for the username if given username is already in use. Call this prior to UserInfo/CreUser to show alternative usernames to the user if the entered username is already occupied or invalid.
	* @param string $username Unique username to validate. Call will return error with bad_username if the username already exists. Min 6 chars, max 60 chars.
	* @param array $dictionary DEPRECATED with 3.4 MR5. Will be removed in later version.
	*/
	public static function ValidateNewUsername($username)
	{
		return self::StaticApi('UserInfo/ValidateNewUsername',array('username' => $username),false);
	}

	/**
	*/
	public static function ConfirmRegistration($user_uuid, $code)
	{
		return self::StaticApi('UserInfo/ConfirmRegistration',array('user_uuid' => $user_uuid, 'code' => $code),false);
	}

	/**
	*/
	public static function isEmailConfirmed($email)
	{
		return self::StaticApi('UserInfo/isEmailConfirmed',array('email' => $email),false);
	}

	/**
	*/
	public static function GetConfirmationCode($user_uuid, $sentbyemail = '0')
	{
		return self::StaticApi('UserInfo/GetConfirmationCode',array('user_uuid' => $user_uuid, 'sentbyemail' => $sentbyemail),false);
	}

	/**
	* Create a new PamFax user and logs him in
	* @param string $name First and last name of the user
	* @param string $username Unique username. Call will fail with bad_username error if the username (or email) already exists. Min 6 chars, max 60 chars. Can be left empty if externalprofile is given. Then the username will be generated from externalprofile. You could use UserInfo::ValidateNewUsername to create a unique username first and then pass it to this call. CHANGED in 3.4 MR5: Should be the same as the email address below
	* @param string $password a password for the user. If left empty, a new password will be generated. Min 8 chars, max 20 chars. If length is 32, it's assumed as md5 of original password. Please then check min and max length by yourself!
	* @param string $email A valid email address for this user. Should be the same as "username" parameter.
	* @param string $culture The culture of the user (en-US, de-DE, ...)
	* @param array $externalprofile External profile data. externalprofile["type"] is the type of data: skype. externalprofile["client_ip"] should be set to the user's ip address
	* @param array $campaign_id is used to payout recommendation bonus to given user uuid. i.e if user clicked on links like http://www.pamfax.biz/?ref=b36d019d53ba, the b36d019d53ba should be passed as campaign_id
	* @param string $withconfirmation Pass 1 if two step registraion needed. First step is: Create user with ConfirmationCode, Send email with code to user. Second step: Validate code before first login
	*/
	public static function CreateUser($name = '', $username = '', $password, $email, $culture, $externalprofile = false, $campaign_id = false, $withconfirmation = '0')
	{
		return self::StaticApi('UserInfo/CreateUser',array('name' => $name, 'username' => $username, 'password' => $password, 'email' => $email, 'culture' => $culture, 'externalprofile' => $externalprofile, 'campaign_id' => $campaign_id, 'withconfirmation' => $withconfirmation),false);
	}

	/**
	* Deletes the currently logged in users account.
	* 
	* All assigned numbers and data will be deleted too!
	* Warning: User will be deleted permanently without any chance to recover his data!
	*/
	public static function DeleteUser()
	{
		return self::StaticApi('UserInfo/DeleteUser',array(),false);
	}

	/**
	* INTERNAL
	* 
	* Map an external login identifier to a pamfax user
	*/
	public static function AddIdentifier($profiletype, $identifier, $externalprofile = false)
	{
		return self::StaticApi('UserInfo/AddIdentifier',array('profiletype' => $profiletype, 'identifier' => $identifier, 'externalprofile' => $externalprofile),false);
	}

	/**
	* INTERNAL
	* 
	* Remove a login identifier from this user
	*/
	public static function RemoveIdentifier($identifier)
	{
		return self::StaticApi('UserInfo/RemoveIdentifier',array('identifier' => $identifier),false);
	}

	/**
	* Read the full profile of the user.
	*/
	public static function ListProfiles()
	{
		return self::StaticApi('UserInfo/ListProfiles',array(),true);
	}

	/**
	* Saves values to an extended user profile
	* 
	* Users may have different profiles. Use this method to store values in them.
	* Profiles will be created if not present yet.
	* @param string $prof Type of profile. Allowed values are user, company, skype, salesforce, gadget
	* @param string $properties key=>value pairs of all profiles properties to save
	* @param bool $ignoreerrors If a field can not be found in the profile object, just ignore it. Otherwise returns an error
	*/
	public static function SetProfileProperties($profile, $properties, $ignoreerrors = false)
	{
		return self::StaticApi('UserInfo/SetProfileProperties',array('profile' => $profile, 'properties' => $properties, 'ignoreerrors' => $ignoreerrors),false);
	}

	/**
	* Set a new login password for the currently logged in user.
	* 
	* You may use md5 encrypted passwords by setting the value of $hashFunction to 'md5'.
	* Note: $password values must be lower case when using a $hashFunction other than 'plain'!
	* @param <string> $password The new password. Will only be checked for vailidity when passed in plain text. Needs to be between 6 and 20 chars long and needs to contain at least 2 numbers and/or special chars
	* @param <string> $hashFunction The function used to enrycpt the password. Allowed values: plain or md5.
	* @param <string> $old_password The current password. This is optional for the moment but will be required in future versions. Note that the $hashFunction value applies to this argument too.
	*/
	public static function SetPassword($password, $hashFunction = 'plain', $old_password = false)
	{
		return self::StaticApi('UserInfo/SetPassword',array('password' => $password, 'hashFunction' => $hashFunction, 'old_password' => $old_password),false);
	}

	/**
	* Verifies a user password.
	* @param string $password The password (or the md5 of the password) that the user entered in the registration process for the given user (case sensitive).
	*/
	public static function VerifyPassword($password)
	{
		return self::StaticApi('UserInfo/VerifyPassword',array('password' => $password),false);
	}

	/**
	* Saves user profile
	* @param array $user Users settings as associative array
	* @param array $profile UserProfiles properties as associative array
	*/
	public static function SaveUser($user = false, $profile = false)
	{
		return self::StaticApi('UserInfo/SaveUser',array('user' => $user, 'profile' => $profile),false);
	}

	/**
	* Returns expirations from current user
	* 
	* if type==false all Expirations are Returned
	* else CREDIT, FREE_CREDIT, LOW_CREDIT, PROPLAN and 42.
	*/
	public static function ListExpirations($type = false)
	{
		return self::StaticApi('UserInfo/ListExpirations',array('type' => $type),false);
	}

	/**
	* Return the inboxes of the user with some additional data (like expiration).
	* @param bool $expired_too If true, lists all expired numbers too.
	*/
	public static function ListInboxes($expired_too = false, $shared_too = true)
	{
		return self::StaticApi('UserInfo/ListInboxes',array('expired_too' => $expired_too, 'shared_too' => $shared_too),true);
	}

	/**
	* Check if the user has a Plan.
	* 
	* This would NOT include other fax numbers user has access to. Will return NONE if no plan, PRO or BASIC otherwise
	*/
	public static function HasPlan()
	{
		return self::StaticApi('UserInfo/HasPlan',array(),true);
	}

	/**
	* Send a message to the user
	* @param string $body The message body
	* @param string $type Type of message to send. Currently implemented: email, skypechat or sms
	* @param string $recipient Recipient of the message. Might be an email address, IM username or phone number depending on the message type
	* @param string $subject Optionally a subject. Not used in all message types (likely not used in SMS and chat)
	*/
	public static function SendMessage($body, $type = false, $recipient = false, $subject = false)
	{
		return self::StaticApi('UserInfo/SendMessage',array('body' => $body, 'type' => $type, 'recipient' => $recipient, 'subject' => $subject),false);
	}

	/**
	* Send a password reset message to a user
	* @param string $username PamFax username to send the message to
	*/
	public static function SendPasswordResetMessage($username, $user_ip = false)
	{
		return self::StaticApi('UserInfo/SendPasswordResetMessage',array('username' => $username, 'user_ip' => $user_ip),false);
	}

	/**
	* Returns a list of orders for this user
	*/
	public static function ListOrders($current_page = 1, $items_per_page = 20)
	{
		return self::StaticApi('UserInfo/ListOrders',array('current_page' => $current_page, 'items_per_page' => $items_per_page),false);
	}

	/**
	* Returns a list of transactions for this user
	*/
	public static function ListTransactions($current_page = 1, $items_per_page = 20)
	{
		return self::StaticApi('UserInfo/ListTransactions',array('current_page' => $current_page, 'items_per_page' => $items_per_page),false);
	}

	/**
	* Returns the users culture information
	*/
	public static function GetCultureInfo()
	{
		return self::StaticApi('UserInfo/GetCultureInfo',array(),false);
	}

	/**
	* Returns a list of activities for the user
	* 
	* This list contains report about what has been happened lately in users account
	* (like messages sent to the user, faxes sent, faxes received, orders placed, ...)
	* @param <int> $count The count of items to return. Valid values are between 1 and 100
	* @param <array> $data_to_list Any message types you want this function to return. Allowed models are 'faxsent', 'faxin', 'faxout', 'payment', 'message' and 'news'. Leave empty to get messages of any type.
	*/
	public static function ListWallMessages($count = 15, $data_to_list = false)
	{
		return self::StaticApi('UserInfo/ListWallMessages',array('count' => $count, 'data_to_list' => $data_to_list),true);
	}

	/**
	* Returns a list of user-agents the user has used to sent faxes.
	* 
	* List will be sorted by amount of faxes sent, so it is a top $max list.
	* @param <int> $max How many results (5-20, default 5)
	*/
	public static function ListUserAgents($max = 5)
	{
		return self::StaticApi('UserInfo/ListUserAgents',array('max' => $max),true);
	}

	/**
	* Reactivates inactive credit for current user
	*/
	public static function ReactivateCredit()
	{
		return self::StaticApi('UserInfo/ReactivateCredit',array(),false);
	}

	/**
	* Sets users OnlineStorage settings.
	* 
	* Expects the settings to be given as key-value pairs.
	* Currently supported settings are:
	* - inbox_enabled: Store incoming faxes (0|1, required)
	* - inbox_path: Path to store incoming faxes to (path as string folders separated by '/', leading and trailing '/' optional, required)
	* @param <string> $provider Provider store settings for (see OnlineStorageApi::ListProviders)
	* @param <array> $settings Key-value pairs of settings.
	*/
	public static function SetOnlineStorageSettings($provider, $settings)
	{
		return self::StaticApi('UserInfo/SetOnlineStorageSettings',array('provider' => $provider, 'settings' => $settings),false);
	}

	/**
	* Assigns a new fax-in number to the current user.
	* 
	* Address data is needed for legal regulation purposes.
	* @param <int> $areacode_id The area code id from Shopping::ListFaxInAreacodes
	* @param <string> $fullname Full name of the user. First space will be delimiter for Last and First names, example value "LastName FirstName"
	* @param <string> $street Postal street address
	* @param <string> $zip Zip code of user
	* @param <string> $city City of user's address
	* @param <string> $country_code The country code of the user (US, DE, ...)
	* @param <string> $company Company name of user's company (optional)
	* @param <string> $building_number Building number, Mandatory field from October 1, 2015
	* @param <string> $verify_doc_uuid mandatory if group_id required proof validation,
	*/
	public static function AssignFaxNumber($areacode_id, $fullname, $street, $zip, $city, $country_code, $company = false, $building_number = false, $verify_doc_uuid = false)
	{
		return self::StaticApi('UserInfo/AssignFaxNumber',array('areacode_id' => $areacode_id, 'fullname' => $fullname, 'street' => $street, 'zip' => $zip, 'city' => $city, 'country_code' => $country_code, 'company' => $company, 'building_number' => $building_number, 'verify_doc_uuid' => $verify_doc_uuid),false);
	}

	/**
	* Creates or updates an alias for a fax number.
	* 
	* "Alias" means that this alias number is shown to user instead of real, internal fax-in number in future. Can be removed via UserInfo::RemoveNumberAlias
	* @param <string> $number The existing valid fax-in number of this user.
	* @param <string> $alias_number A new alias number. This is the number that will be shown to user instead of number then. Must be correctly formatted incl. countrycode (i.e. +12139851886)
	*/
	public static function SetNumberAlias($number, $alias_number)
	{
		return self::StaticApi('UserInfo/SetNumberAlias',array('number' => $number, 'alias_number' => $alias_number),false);
	}

	/**
	* Removes an alias for a fax number.
	*/
	public static function RemoveNumberAlias($number, $alias_number)
	{
		return self::StaticApi('UserInfo/RemoveNumberAlias',array('number' => $number, 'alias_number' => $alias_number),false);
	}

	/**
	* Releases a fax number from the current user.
	* @param string $number The number to cancel in international format
	*/
	public static function CancelNumber($number)
	{
		return self::StaticApi('UserInfo/CancelNumber',array('number' => $number),false);
	}

	/**
	* Adds a cover to the users account
	* 
	* Note that this call waits until a preview of the first page has been created.
	* That may take some time, timeout is 300 seconds.
	* Your cover may contain various placeholders that will be replaced with useful data
	* refgarding the fax. These placeholders are:
	* {ToName} The recipients name
	* {FaxNumber} The recipients fax number
	* {FromName} Senders name, build from the profile data. See account settings in the portal for sample how this is build
	* {Date} The current date formatted in senders locale (attached timezone)
	* {Page} Number of the current page
	* {Pages} Number of pages
	* {Message} A text message
	* @param FileUpload $file The file upload
	*/
	public static function AddCover($title = false)
	{
		return self::StaticApi('UserInfo/AddCover',array('title' => $title),false);
	}

	/**
	* Permanetnly deletes a coverpage
	*/
	public static function DeleteCover($uuid)
	{
		return self::StaticApi('UserInfo/DeleteCover',array('uuid' => $uuid),false);
	}

	/**
	* Restores all default covers
	* 
	* Users may delete the default covers. This method allows to restore them
	*/
	public static function RestoreDefaultCovers()
	{
		return self::StaticApi('UserInfo/RestoreDefaultCovers',array(),false);
	}

	/**
	* Returns a list of all coverpages of the user.
	*/
	public static function ListCovers()
	{
		return self::StaticApi('UserInfo/ListCovers',array(),false);
	}

	/**
	* Returns the users current notification settings
	*/
	public static function GetNotifySettings()
	{
		return self::StaticApi('UserInfo/GetNotifySettings',array(),false);
	}

	/**
	* Sets the users global notification settings
	*/
	public static function SetGlobalNotifySettings($single_notification_limit, $attach_transmission_report, $max_attachment_size_mb)
	{
		return self::StaticApi('UserInfo/SetGlobalNotifySettings',array('single_notification_limit' => $single_notification_limit, 'attach_transmission_report' => $attach_transmission_report, 'max_attachment_size_mb' => $max_attachment_size_mb),false);
	}

	/**
	* Sets settings for a specific notification provider
	* @param <string> $provider Currently supported notification providers: mail, skype, sms
	*/
	public static function SetNotifyProviderSettings($provider, $success, $failure, $inbox, $group)
	{
		return self::StaticApi('UserInfo/SetNotifyProviderSettings',array('provider' => $provider, 'success' => $success, 'failure' => $failure, 'inbox' => $inbox, 'group' => $group),false);
	}
}
