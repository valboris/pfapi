<?php
/**
* Functionality to manage Companies.
*/
class CompanyInfoApi extends ApiClient
{
	/**
	* Accept a membership for a given company.
	* 
	* Note that accepting membership to a company destroys memberships to other companies (if present)
	* or (if present) the own company. Company owner will be notified about user leaving the company if
	* a membership is terminated implicitely.
	* @param string $company_uuid The uuid of the company which membership is accepted
	*/
	public function AcceptMembership($company_uuid)
	{
		return $this->CallApi('CompanyInfo/AcceptMembership',array('company_uuid' => $company_uuid),false);
	}

	/**
	* Decline a membership for a given company.
	* 
	* Company owner will be notified about the declination.
	* @param string $company_uuid The uuid of the company which membership is declined
	*/
	public function DeclineMembership($company_uuid)
	{
		return $this->CallApi('CompanyInfo/DeclineMembership',array('company_uuid' => $company_uuid),false);
	}

	/**
	* Delete the membership of current user for a given company.
	* 
	* Company owner will ne notified about the membership termination.
	* @param string $company_uuid The uuid of the company which membership is deleted
	*/
	public function DeleteMembership($company_uuid)
	{
		return $this->CallApi('CompanyInfo/DeleteMembership',array('company_uuid' => $company_uuid),false);
	}

	/**
	* Create or Update the company for current user.
	* 
	* Be carefull! If user is member of another company this membership is deleted
	* and the owner will be notified about the membership termination.
	* @param string $company_name The name of the company
	* @param string $owner_name The name of the company-owner
	* @param string $street The street and number of the company-address
	* @param string $zip The zip of the company-address
	* @param string $city The city of the company-address
	* @param string $country The country of the company-address
	*/
	public function SaveCompany($companyname, $owner_name, $street, $zip, $city, $country)
	{
		return $this->CallApi('CompanyInfo/SaveCompany',array('companyname' => $companyname, 'owner_name' => $owner_name, 'street' => $street, 'zip' => $zip, 'city' => $city, 'country' => $country),false);
	}

	/**
	* Deletes the users company
	* 
	* All employees will be informed as if they were remove one-by-one.
	*/
	public function DeleteCompany()
	{
		return $this->CallApi('CompanyInfo/DeleteCompany',array(),false);
	}

	/**
	* Returns all Members of the current users Company.
	* 
	* Result contains information about member status (invited/member) and
	* the members settings (like auto-topup).
	*/
	public function ListEmployees()
	{
		return $this->CallApi('CompanyInfo/ListEmployees',array(),false);
	}

	/**
	* Get an CompanyMember by his (users) UUID
	* @param string $user_uuid The users uuid of the employee
	*/
	public function GetEmployee($user_uuid)
	{
		return $this->CallApi('CompanyInfo/GetEmployee',array('user_uuid' => $user_uuid),false);
	}

	/**
	* Save Settings for a CompanyMember by his (users) UUID
	* @param string $user_uuid The employees user uuid
	* @param int $auto_credit Autocredit On/Off value
	* @param int $inbox_access Inbox-Access 0/1/2 (none/read/delete)
	* @param float $auto_credit_value Amount which is autocredited
	* @param float $auto_credit_max Autocredit max-value
	*/
	public function SaveEmployee($user_uuid, $auto_credit, $inbox_access, $auto_credit_value = false, $auto_credit_max = false)
	{
		return $this->CallApi('CompanyInfo/SaveEmployee',array('user_uuid' => $user_uuid, 'auto_credit' => $auto_credit, 'inbox_access' => $inbox_access, 'auto_credit_value' => $auto_credit_value, 'auto_credit_max' => $auto_credit_max),false);
	}

	/**
	* Remove an employee from your company.
	* 
	* Member will be notified about that!
	* @param string $user_uuid The Employees user uuid
	*/
	public function DeleteEmployee($user_uuid)
	{
		return $this->CallApi('CompanyInfo/DeleteEmployee',array('user_uuid' => $user_uuid),false);
	}

	/**
	* Move credit from the current user to an employee's credit.
	* 
	* Note: User identified by $user_uuid must be employee of current user's company
	* @param string $user_uuid The Employees user uuid
	* @param float $value The amount which is added
	*/
	public function AddCreditToEmployee($user_uuid, $value)
	{
		return $this->CallApi('CompanyInfo/AddCreditToEmployee',array('user_uuid' => $user_uuid, 'value' => $value),false);
	}

	/**
	* Invite a pamfax user to my company
	* @param string username pamfax username of the user to invite
	*/
	public function InviteUser($username)
	{
		return $this->CallApi('CompanyInfo/InviteUser',array('username' => $username),false);
	}

	/**
	* List Faxes from all company members including own faxes
	* @param int $current_page The page which should be shown
	* @param int $items_per_page How many items are shown per page
	*/
	public function ListCompanyJournal($current_page = 1, $items_per_page = 20)
	{
		return $this->CallApi('CompanyInfo/ListCompanyJournal',array('current_page' => $current_page, 'items_per_page' => $items_per_page),true);
	}

	/**
	* Returns a list of memberships of the user
	* 
	* Note: ATM this should only contain one dataset as users can only be member of exactly one company.
	*/
	public function ListMemberships()
	{
		return $this->CallApi('CompanyInfo/ListMemberships',array(),true);
	}

	/**
	* Lists all timeframes that contain data for members expeses.
	* 
	* Use one of these timeframes to get the expenses using the ListMemberExpenses method.
	*/
	public function ListReportTimeframes()
	{
		return $this->CallApi('CompanyInfo/ListReportTimeframes',array(),true);
	}

	/**
	* Sums up all members expenses for the selected timeframe.
	* 
	* Use ListReportTimeframes method to list timeframes that contain data.
	* @param string $timeframe optional timeframe (from ListReportTimeframes)
	*/
	public function ListMemberExpenses($timeframe = false)
	{
		return $this->CallApi('CompanyInfo/ListMemberExpenses',array('timeframe' => $timeframe),true);
	}
}
