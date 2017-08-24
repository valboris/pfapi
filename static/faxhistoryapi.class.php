<?php
/**
* Fax history access.
* 
* Provides methods to access users inbox, outbox and sent faxes
*/
class FaxHistoryApi extends ApiClient
{
	/**
	* List all sent faxes (successful or not)
	* @param int $current_page The page which should be shown
	* @param int $items_per_page How many items are shown per page
	* @param <array> 'array_for_api' Contains additional information for selection conditions in list sent faxes
	*/
	public static function ListSentFaxes($current_page = 1, $items_per_page = 20, $array_for_api)
	{
		return self::StaticApi('FaxHistory/ListSentFaxes',array('current_page' => $current_page, 'items_per_page' => $items_per_page, 'array_for_api' => $array_for_api),false);
	}

	/**
	* Faxes in the outbox that are currently in the sending process
	* @param int $current_page The page which should be shown
	* @param int $items_per_page How many items are shown per page
	*/
	public static function ListOutboxFaxes($current_page = 1, $items_per_page = 20)
	{
		return self::StaticApi('FaxHistory/ListOutboxFaxes',array('current_page' => $current_page, 'items_per_page' => $items_per_page),false);
	}

	/**
	* List all faxes in the inbox of the current user.
	* @param int $current_page The page which should be shown
	* @param int $items_per_page How many items are shown per page
	*/
	public static function ListInboxFaxes($current_page = 1, $items_per_page = 20)
	{
		return self::StaticApi('FaxHistory/ListInboxFaxes',array('current_page' => $current_page, 'items_per_page' => $items_per_page),false);
	}

	/**
	* Lists all unpaid faxes
	* 
	* That are waiting for a payment.
	* When this user makes a transaction to add credit, these faxes will be sent automatically
	* if they are younger that 2 hours.
	* @param int $current_page The page which should be shown
	* @param int $items_per_page How many items are shown per page
	*/
	public static function ListUnpaidFaxes($current_page = 1, $items_per_page = 20)
	{
		return self::StaticApi('FaxHistory/ListUnpaidFaxes',array('current_page' => $current_page, 'items_per_page' => $items_per_page),false);
	}

	/**
	* Lists all faxes in a group (that are sent as on job).
	* @param string $uuid Uuid of one of the faxes in the group.
	* @param int $current_page The page which should be shown
	* @param int $items_per_page How many items are shown per page
	*/
	public static function ListFaxGroup($uuid, $current_page = 1, $items_per_page = 20)
	{
		return self::StaticApi('FaxHistory/ListFaxGroup',array('uuid' => $uuid, 'current_page' => $current_page, 'items_per_page' => $items_per_page),false);
	}

	/**
	* Lists all faxes in trash.
	* @param int $current_page The page which should be shown
	* @param int $items_per_page How many items are shown per page
	*/
	public static function ListTrash($current_page = 1, $items_per_page = 20)
	{
		return self::StaticApi('FaxHistory/ListTrash',array('current_page' => $current_page, 'items_per_page' => $items_per_page),false);
	}

	/**
	* Returns a list of latest faxes for the user.
	* 
	* Does not contain deleted and delayed faxes (See ListTrash for deleted faxes).
	* @param <int> $count The count of items to return. Valid values are between 1 and 100
	* @param <array> $data_to_list Any message types you want this function to return. Allowed models are 'sent', 'inbox', 'outbox'. Leave empty to get faxes of any type.
	*/
	public static function ListRecentFaxes($count = 15, $data_to_list = false)
	{
		return self::StaticApi('FaxHistory/ListRecentFaxes',array('count' => $count, 'data_to_list' => $data_to_list),false);
	}

	/**
	* Returns a list of the last recipient
	* 
	* This is fixed to a maximum of 20 records.
	*/
	public static function ListRecentRecipients()
	{
		return self::StaticApi('FaxHistory/ListRecentRecipients',array(),false);
	}

	/**
	 * Sets flag add_to_recent in fax_history to 1.
	 *
	 * Is removed from list of recent recepients in sending faxes.
	 * @param string $number NUNBER of the recipient in fax_history.
	 */
	public static function DeleteFromListRecentRecipients($number)
	{
		return self::StaticApi('FaxHistory/DeleteFromListRecentRecipients',array('number' => $number),true);
	}

	/**
	 * Is removed from list of fax-history of sent (inbox, trash) faxes.
	 *
	 * @param string $type type of fax journal.
	 * @param string $date1 lower border PERIOD of deleting in fax_history, format DATE (sql). For example: '2015-02-24'.
	 * @param string $date2 upper border PERIOD of deleting in fax_history, format DATE (sql). For example: '2016-02-24'.
	 * @param boolean $no_trash fix process of deleteing: 1)to move to trash or 2)to delete without adding to trash.
	 *
	 * @attribute[RequestParam('type','string')]
	 * @attribute[RequestParam('date1','string')]
	 * @attribute[RequestParam('date2','string')]
	 * @attribute[RequestParam('no_trash','boolean',false)]
	 */
	public static function DeleteFaxesForPeriod($type, $date1, $date2, $no_trash = false)
	{
		return self::StaticApi('FaxHistory/DeleteFaxesForPeriod',array('type'=>$type,'date1' => $date1,'date2' => $date2,'no_trash'=>$no_trash ,),true);
	}

	/**
	* Sets a fax' read date to current time.
	* 
	* Fax needs to be a fax in the inbox.
	* @param string $uuid UUID of the fax to show
	*/
	public static function SetFaxRead($uuid)
	{
		return self::StaticApi('FaxHistory/SetFaxRead',array('uuid' => $uuid),true);
	}

	/**
	* Sets the read date of all the faxes to the current time
	* @return a list of the faxes that have been marked as read
	*/
	public static function SetFaxesAsRead($uuids)
	{
		return self::StaticApi('FaxHistory/SetFaxesAsRead',array('uuids' => $uuids),false);
	}

	/**
	* Returns the details of a fax in the inbox.
	* @param string $uuid UUID of the fax to show
	* @param bool $mark_read If true marks the fax as read (default: false).
	*/
	public static function GetInboxFax($uuid, $mark_read = false)
	{
		return self::StaticApi('FaxHistory/GetInboxFax',array('uuid' => $uuid, 'mark_read' => $mark_read),false);
	}

	/**
	* Returns the details of a fax in progress.
	* @param string $uuid UUID of the fax to show
	*/
	public static function GetFaxDetails($uuid)
	{
		return self::StaticApi('FaxHistory/GetFaxDetails',array('uuid' => $uuid),false);
	}

	/**
	* Returns a fax groups details.
	* @param string $uuid Uuid of one of the faxes in the group.
	*/
	public static function GetFaxGroup($uuid)
	{
		return self::StaticApi('FaxHistory/GetFaxGroup',array('uuid' => $uuid),false);
	}

	/**
	* INTERNAL: Returns the esker details of a sent fax.
	* @param int $fax_id ID of the fax to show
	*/
	public static function GetEskerDetails($fax_id)
	{
		return self::StaticApi('FaxHistory/GetEskerDetails',array('fax_id' => $fax_id),false);
	}

	/**
	* Returns the number of faxes from users history with a specific state.
	* @param string $type Possible values: history, inbox, inbox_unread, outbox,all or unpaid
	*/
	public static function CountFaxes($type)
	{
		return self::StaticApi('FaxHistory/CountFaxes',array('type' => $type),false);
	}

	/**
	* Removes all faxes from trash for user and if user is member of a company and has delete rights also for the owners inbox faxes
	*/
	public static function EmptyTrash()
	{
		return self::StaticApi('FaxHistory/EmptyTrash',array(),false);
	}

	/**
	* Removes a single fax from trash similar to EmptyTrash() which deletes all the faxes
	* @param <type> $uuid id from fax to be removed vom trash
	* @deprecated Use DeleteFaxesFromTrash instead
	*/
	public static function DeleteFaxFromTrash($uuid)
	{
		return self::StaticApi('FaxHistory/DeleteFaxFromTrash',array('uuid' => $uuid),false);
	}

	/**
	* Removes faxes from trash
	* 
	* This method is similar to EmptyTrash() which deletes all the faxes from trash
	* @param <type> $uuids ids of faxes to be removed vom trash
	*/
	public static function DeleteFaxesFromTrash($uuids)
	{
		return self::StaticApi('FaxHistory/DeleteFaxesFromTrash',array('uuids' => $uuids),false);
	}

	/**
	* Moves a fax to the trash.
	* 
	* If siblings_too is true will perform for the given fax and all other recipients
	* from the same fax job.
	* siblings_too will only be evaluated it given uuid belogs to an outgoing fax and will be
	* ignored for incoming faxes
	* @deprecated Use DeleteFaxes instead
	*/
	public static function DeleteFax($uuid, $siblings_too = false)
	{
		return self::StaticApi('FaxHistory/DeleteFax',array('uuid' => $uuid, 'siblings_too' => $siblings_too),false);
	}

	/**
	* Moves faxes to the trash.
	* 
	* If siblings_too is true will perform for the given faxes and all other recipients
	* from the same fax jobs.
	* siblings_too will only be evaluated for uuids beloging to an outgoing fax and will be
	* ignored for incoming faxes uuids
	*/
	public static function DeleteFaxes($uuids, $siblings_too = false)
	{
		return self::StaticApi('FaxHistory/DeleteFaxes',array('uuids' => $uuids, 'siblings_too' => $siblings_too),false);
	}

	/**
	* Restores a fax from the trash.
	*/
	public static function RestoreFax($uuid)
	{
		return self::StaticApi('FaxHistory/RestoreFax',array('uuid' => $uuid),false);
	}

	/**
	* Get a .pdf-Version of a transmission report.
	* 
	* On the transmission report basic data of the fax and a preview of the first page is shown.
	* Should always be called with API_MODE_PASSTHRU, as the result is the pdf as binary data
	*/
	public static function GetTransmissionReport($uuid)
	{
		return self::StaticApi('FaxHistory/GetTransmissionReport',array('uuid' => $uuid),false);
	}

	/**
	* Lists all notes for the given fax in reverse order (latest first)
	*/
	public static function ListFaxNotes($fax_uuid)
	{
		return self::StaticApi('FaxHistory/ListFaxNotes',array('fax_uuid' => $fax_uuid),false);
	}

	/**
	* Add a note (free text) to the fax
	*/
	public static function AddFaxNote($fax_uuid, $note)
	{
		return self::StaticApi('FaxHistory/AddFaxNote',array('fax_uuid' => $fax_uuid, 'note' => $note),false);
	}

	/**
	* Sets the spamscore for all the faxes depending on the flag "is_spam"
	* 
	* Takes an array of faxes UUIDs and marks them a spam if the second argument (is_spam) is true.
	* Removes the Spam state if is_spam is false.
	* If 15 or more faxes of the same sender has been marked as spam, all incoming faxes are directly moved to the trash.
	* This is user specific, so if user A reports 15 faxes of one sender, then only all incoming faxes from the sender to
	* him are directly sent to the trash.
	* @return two lists of faxes. First includes InboxFaxes whose Spam score could be sent, the other containing detailed error information about faxes whose score could not be set.
	*/
	public static function SetSpamStateForFaxes($uuids, $is_spam = false)
	{
		return self::StaticApi('FaxHistory/SetSpamStateForFaxes',array('uuids' => $uuids, 'is_spam' => $is_spam),false);
	}

	/**
	* Publishes a fax.
	* @param string $uuid UUID of the fax
	*/
	public static function PublishFax($uuid)
	{
		return self::StaticApi('FaxHistory/PublishFax',array('uuid' => $uuid),false);
	}

	/**
	* Revokes the public state of a fax.
	* @param string $uuid UUID of the fax
	*/
	public static function UnPublishFax($uuid)
	{
		return self::StaticApi('FaxHistory/UnPublishFax',array('uuid' => $uuid),false);
	}

	/**
	* Returns data for a published fax
	* @param string $uuid UUID of the fax
	*/
	public static function GetPublishedFax($uuid)
	{
		return self::StaticApi('FaxHistory/GetPublishedFax',array('uuid' => $uuid),false);
	}
}
