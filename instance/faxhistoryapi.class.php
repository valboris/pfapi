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
	public function ListSentFaxes($current_page = 1, $items_per_page = 20, $array_for_api)
	{
		return $this->CallApi('FaxHistory/ListSentFaxes',array('current_page' => $current_page, 'items_per_page' => $items_per_page, 'array_for_api' => $array_for_api),false);
	}

	/**
	* Faxes in the outbox that are currently in the sending process
	* @param int $current_page The page which should be shown
	* @param int $items_per_page How many items are shown per page
	*/
	public function ListOutboxFaxes($current_page = 1, $items_per_page = 20)
	{
		return $this->CallApi('FaxHistory/ListOutboxFaxes',array('current_page' => $current_page, 'items_per_page' => $items_per_page),false);
	}

	/**
	* List all faxes in the inbox of the current user.
	* @param int $current_page The page which should be shown
	* @param int $items_per_page How many items are shown per page
	*/
	public function ListInboxFaxes($current_page = 1, $items_per_page = 20)
	{
		return $this->CallApi('FaxHistory/ListInboxFaxes',array('current_page' => $current_page, 'items_per_page' => $items_per_page),false);
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
	public function ListUnpaidFaxes($current_page = 1, $items_per_page = 20)
	{
		return $this->CallApi('FaxHistory/ListUnpaidFaxes',array('current_page' => $current_page, 'items_per_page' => $items_per_page),false);
	}

	/**
	* Lists all faxes in a group (that are sent as on job).
	* @param string $uuid Uuid of one of the faxes in the group.
	* @param int $current_page The page which should be shown
	* @param int $items_per_page How many items are shown per page
	*/
	public function ListFaxGroup($uuid, $current_page = 1, $items_per_page = 20)
	{
		return $this->CallApi('FaxHistory/ListFaxGroup',array('uuid' => $uuid, 'current_page' => $current_page, 'items_per_page' => $items_per_page),false);
	}

	/**
	* Lists all faxes in trash.
	* @param int $current_page The page which should be shown
	* @param int $items_per_page How many items are shown per page
	*/
	public function ListTrash($current_page = 1, $items_per_page = 20)
	{
		return $this->CallApi('FaxHistory/ListTrash',array('current_page' => $current_page, 'items_per_page' => $items_per_page),false);
	}

	/**
	* Returns a list of latest faxes for the user.
	* 
	* Does not contain deleted and delayed faxes (See ListTrash for deleted faxes).
	* @param <int> $count The count of items to return. Valid values are between 1 and 100
	* @param <array> $data_to_list Any message types you want this function to return. Allowed models are 'sent', 'inbox', 'outbox'. Leave empty to get faxes of any type.
	*/
	public function ListRecentFaxes($count = 15, $data_to_list = false)
	{
		return $this->CallApi('FaxHistory/ListRecentFaxes',array('count' => $count, 'data_to_list' => $data_to_list),false);
	}

	/**
	* Returns a list of the last recipient
	* 
	* This is fixed to a maximum of 20 records.
	*/
	public function ListRecentRecipients()
	{
		return $this->CallApi('FaxHistory/ListRecentRecipients',array(),false);
	}

	/**
	* Sets a fax' read date to current time.
	* 
	* Fax needs to be a fax in the inbox.
	* @param string $uuid UUID of the fax to show
	*/
	public function SetFaxRead($uuid)
	{
		return $this->CallApi('FaxHistory/SetFaxRead',array('uuid' => $uuid),true);
	}

	/**
	* Sets the read date of all the faxes to the current time
	* @return a list of the faxes that have been marked as read
	*/
	public function SetFaxesAsRead($uuids)
	{
		return $this->CallApi('FaxHistory/SetFaxesAsRead',array('uuids' => $uuids),false);
	}

	/**
	* Returns the details of a fax in the inbox.
	* @param string $uuid UUID of the fax to show
	* @param bool $mark_read If true marks the fax as read (default: false).
	*/
	public function GetInboxFax($uuid, $mark_read = false)
	{
		return $this->CallApi('FaxHistory/GetInboxFax',array('uuid' => $uuid, 'mark_read' => $mark_read),false);
	}

	/**
	* Returns the details of a fax in progress.
	* @param string $uuid UUID of the fax to show
	*/
	public function GetFaxDetails($uuid)
	{
		return $this->CallApi('FaxHistory/GetFaxDetails',array('uuid' => $uuid),false);
	}

	/**
	* Returns a fax groups details.
	* @param string $uuid Uuid of one of the faxes in the group.
	*/
	public function GetFaxGroup($uuid)
	{
		return $this->CallApi('FaxHistory/GetFaxGroup',array('uuid' => $uuid),false);
	}

	/**
	* INTERNAL: Returns the esker details of a sent fax.
	* @param int $fax_id ID of the fax to show
	*/
	public function GetEskerDetails($fax_id)
	{
		return $this->CallApi('FaxHistory/GetEskerDetails',array('fax_id' => $fax_id),false);
	}

	/**
	* Returns the number of faxes from users history with a specific state.
	* @param string $type Possible values: history, inbox, inbox_unread, outbox or unpaid
	*/
	public function CountFaxes($type)
	{
		return $this->CallApi('FaxHistory/CountFaxes',array('type' => $type),false);
	}

	/**
	* Removes all faxes from trash for user and if user is member of a company and has delete rights also for the owners inbox faxes
	*/
	public function EmptyTrash()
	{
		return $this->CallApi('FaxHistory/EmptyTrash',array(),false);
	}

	/**
	* Removes a single fax from trash similar to EmptyTrash() which deletes all the faxes
	* @param <type> $uuid id from fax to be removed vom trash
	* @deprecated Use DeleteFaxesFromTrash instead
	*/
	public function DeleteFaxFromTrash($uuid)
	{
		return $this->CallApi('FaxHistory/DeleteFaxFromTrash',array('uuid' => $uuid),false);
	}

	/**
	* Removes faxes from trash
	* 
	* This method is similar to EmptyTrash() which deletes all the faxes from trash
	* @param <type> $uuids ids of faxes to be removed vom trash
	*/
	public function DeleteFaxesFromTrash($uuids)
	{
		return $this->CallApi('FaxHistory/DeleteFaxesFromTrash',array('uuids' => $uuids),false);
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
	public function DeleteFax($uuid, $siblings_too = false)
	{
		return $this->CallApi('FaxHistory/DeleteFax',array('uuid' => $uuid, 'siblings_too' => $siblings_too),false);
	}

	/**
	* Moves faxes to the trash.
	* 
	* If siblings_too is true will perform for the given faxes and all other recipients
	* from the same fax jobs.
	* siblings_too will only be evaluated for uuids beloging to an outgoing fax and will be
	* ignored for incoming faxes uuids
	*/
	public function DeleteFaxes($uuids, $siblings_too = false)
	{
		return $this->CallApi('FaxHistory/DeleteFaxes',array('uuids' => $uuids, 'siblings_too' => $siblings_too),false);
	}

	/**
	* Restores a fax from the trash.
	*/
	public function RestoreFax($uuid)
	{
		return $this->CallApi('FaxHistory/RestoreFax',array('uuid' => $uuid),false);
	}

	/**
	* Get a .pdf-Version of a transmission report.
	* 
	* On the transmission report basic data of the fax and a preview of the first page is shown.
	* Should always be called with API_MODE_PASSTHRU, as the result is the pdf as binary data
	*/
	public function GetTransmissionReport($uuid)
	{
		return $this->CallApi('FaxHistory/GetTransmissionReport',array('uuid' => $uuid),false);
	}

	/**
	* Lists all notes for the given fax in reverse order (latest first)
	*/
	public function ListFaxNotes($fax_uuid)
	{
		return $this->CallApi('FaxHistory/ListFaxNotes',array('fax_uuid' => $fax_uuid),false);
	}

	/**
	* Add a note (free text) to the fax
	*/
	public function AddFaxNote($fax_uuid, $note)
	{
		return $this->CallApi('FaxHistory/AddFaxNote',array('fax_uuid' => $fax_uuid, 'note' => $note),false);
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
	public function SetSpamStateForFaxes($uuids, $is_spam = false)
	{
		return $this->CallApi('FaxHistory/SetSpamStateForFaxes',array('uuids' => $uuids, 'is_spam' => $is_spam),false);
	}

	/**
	* Publishes a fax.
	* @param string $uuid UUID of the fax
	*/
	public function PublishFax($uuid)
	{
		return $this->CallApi('FaxHistory/PublishFax',array('uuid' => $uuid),false);
	}

	/**
	* Revokes the public state of a fax.
	* @param string $uuid UUID of the fax
	*/
	public function UnPublishFax($uuid)
	{
		return $this->CallApi('FaxHistory/UnPublishFax',array('uuid' => $uuid),false);
	}

	/**
	* Returns data for a published fax
	* @param string $uuid UUID of the fax
	*/
	public function GetPublishedFax($uuid)
	{
		return $this->CallApi('FaxHistory/GetPublishedFax',array('uuid' => $uuid),false);
	}
}
