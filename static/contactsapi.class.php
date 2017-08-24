<?php
/**
* API to manage a users contacts
*/
class ContactsApi extends ApiClient
{
	/**
	* Lists all user defined contact groups
	*/
	public static function ListContactGroups()
	{
		return self::StaticApi('Contacts/ListContactGroups',array(),true);
	}

	/**
	* Lists all virtual contact groups
	* 
	* Those are such like 'last recipients', 'last added', 'ungrouped'
	*/
	public static function ListVirtualContactGroups()
	{
		return self::StaticApi('Contacts/ListVirtualContactGroups',array(),true);
	}

	/**
	* Returns a contact groups data
	*/
	public static function GetContactGroup($group_uuid)
	{
		return self::StaticApi('Contacts/GetContactGroup',array('group_uuid' => $group_uuid),true);
	}

	/**
	* Creates a new contact group
	* 
	* If there's already a group with the same name it will be updated.
	*/
	public static function AddContactGroup($label, $notes = false)
	{
		return self::StaticApi('Contacts/AddContactGroup',array('label' => $label, 'notes' => $notes),false);
	}

	/**
	* Updates a contact groups data
	* 
	* If there's another group with the label given returns an error.
	*/
	public static function UpdateContactGroup($group_uuid, $label, $notes = false)
	{
		return self::StaticApi('Contacts/UpdateContactGroup',array('group_uuid' => $group_uuid, 'label' => $label, 'notes' => $notes),false);
	}

	/**
	* Deletes a contact group
	*/
	public static function DeleteContactGroup($group_uuid)
	{
		return self::StaticApi('Contacts/DeleteContactGroup',array('group_uuid' => $group_uuid),false);
	}

	/**
	* Splits up a contact group into chunks
	* 
	* Will create multiple new groups and move the contacts there. Each of the new
	* groups has a maximum size of $chunksize contacts.
	*/
	public static function SplitContactGroup($group_uuid, $chunksize)
	{
		return self::StaticApi('Contacts/SplitContactGroup',array('group_uuid' => $group_uuid, 'chunksize' => $chunksize),false);
	}

	/**
	* Joins a range of contact groups
	* 
	* Will create a new contact group containing all contacts from the given groups.
	* Then those are removed.
	* groups has a maximum size of $chunksize contacts.
	*/
	public static function JoinContactGroups($label, $group_uuids)
	{
		return self::StaticApi('Contacts/JoinContactGroups',array('label' => $label, 'group_uuids' => $group_uuids),false);
	}

	/**
	* Lists contacts of the specified group
	* 
	* group_uuid may be a virtual group uuid or a user generated one.
	*/
	public static function ListContacts($group_uuid, $current_page = 1, $items_per_page = 20, $need_valid_number = false)
	{
		return self::StaticApi('Contacts/ListContacts',array('group_uuid' => $group_uuid, 'current_page' => $current_page, 'items_per_page' => $items_per_page, 'need_valid_number' => $need_valid_number),true);
	}

	/**
	* Returns a contacts data
	*/
	public static function GetContact($contact_uuid)
	{
		return self::StaticApi('Contacts/GetContact',array('contact_uuid' => $contact_uuid),true);
	}

	/**
	* Adds a contact
	* 
	* contact_data has to be an associative array.
	* possible values are: name, email, fax, phone_private, phone_work, phone_mobile, address1, address2, zip, city, state, country
	* If optional argument group_uuid is given, the new contact is automatically added to that group.
	*/
	public static function AddContact($contact_data, $group_uuid = false)
	{
		return self::StaticApi('Contacts/AddContact',array('contact_data' => $contact_data, 'group_uuid' => $group_uuid),false);
	}

	/**
	* Adds multiple contacts at once
	* 
	* contacts_data is an array of associative arrays (wo two dimensional), each
	* entry containing data as described in AddContact method.
	* If optional argument group_uuid is given, the new contact is automatically added to that group.
	*/
	public static function AddContacts($contacts_data, $group_uuid = false)
	{
		return self::StaticApi('Contacts/AddContacts',array('contacts_data' => $contacts_data, 'group_uuid' => $group_uuid),false);
	}

	/**
	* Updates a contacts data
	* 
	* For contact_data description see AddContact method.
	*/
	public static function UpdateContact($contact_uuid, $contact_data)
	{
		return self::StaticApi('Contacts/UpdateContact',array('contact_uuid' => $contact_uuid, 'contact_data' => $contact_data),false);
	}

	/**
	* Deletes a contact
	*/
	public static function DeleteContact($contact_uuid)
	{
		return self::StaticApi('Contacts/DeleteContact',array('contact_uuid' => $contact_uuid),false);
	}

	/**
	*/
	public static function AddContactsToGroup($group_uuid, $contact_uuids)
	{
		return self::StaticApi('Contacts/AddContactsToGroup',array('group_uuid' => $group_uuid, 'contact_uuids' => $contact_uuids),false);
	}

	/**
	*/
	public static function RemoveContactsFromGroup($group_uuid, $contact_uuids)
	{
		return self::StaticApi('Contacts/RemoveContactsFromGroup',array('group_uuid' => $group_uuid, 'contact_uuids' => $contact_uuids),false);
	}

	/**
	* Searched all contacts for text
	* 
	* Will search in all users contacts.
	* You may optionally add a group to search in.
	*/
	public static function Search($text, $group_uuid = false, $need_valid_number = false, $current_page = 1, $items_per_page = 20)
	{
		return self::StaticApi('Contacts/Search',array('text' => $text, 'group_uuid' => $group_uuid, 'need_valid_number' => $need_valid_number, 'current_page' => $current_page, 'items_per_page' => $items_per_page),true);
	}

	/**
	* Sends recommendation message to given contacts
	*/
	public static function Recommend($contact_uuids)
	{
		return self::StaticApi('Contacts/Recommend',array('contact_uuids' => $contact_uuids),false);
	}
}
