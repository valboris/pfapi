<?php
/**
* API to manage the PamFax partner's API account.
* 
* Has nothing to do with PamFax user accounts (see UserInfo API for that).
*/
class AccountApi extends ApiClient
{
	/**
	* Creates a new Api account and sends a notification with the apikey and secret to the passed email.
	* 
	* Function available only in dev and live
	* @return 
	*/
	public function Register($first_name, $last_name, $email, $company, $address1, $zip, $city, $state_code = '', $country_code, $companysize, $expected_volume = '', $product_name = '', $product_description = '')
	{
		return $this->CallApi('Account/Register',array('first_name' => $first_name, 'last_name' => $last_name, 'email' => $email, 'company' => $company, 'address1' => $address1, 'zip' => $zip, 'city' => $city, 'state_code' => $state_code, 'country_code' => $country_code, 'companysize' => $companysize, 'expected_volume' => $expected_volume, 'product_name' => $product_name, 'product_description' => $product_description),false);
	}

	/**
	* Exports all reseller data for a given datetime range to CSV.
	* 
	* Resulting CSV will contain the following fields:
	* fax_uuid: UUID of the fax
	* user_uuid: UUID of the user that sent the fax
	* number: Number the fax was sent to
	* sent: DateTime when fax was sent (in ISO 8601 formatting [http://en.wikipedia.org/wiki/ISO_8601], i.e. "2011-09-28T08:45:23+02:00")
	* pages: Number of pages in the fax
	* state: State of the fax (success,failure,cancelled)
	* price: Price of the fax in you accounts default currency
	* @param <string> $from DateTime from where to get the data from (i.e. 2011-09-01). In Timezone Europe/Berlin
	* @param <string> $to Last DateTime to include in the data (i.e. 2011-09-30)
	*/
	public function ExportResellerData($from, $to)
	{
		return $this->CallApi('Account/ExportResellerData',array('from' => $from, 'to' => $to),false);
	}

	/**
	* Returns information about your API account.
	*/
	public function GetInfo()
	{
		return $this->CallApi('Account/GetInfo',array(),false);
	}

	/**
	* Allowes changing the default timezone.
	* 
	* API will fall back to this if nothing else is specified (for current user)
	* and nothing can be detected automatically.
	* @param <string> $timezone The timezone identifier (Europe/Berlin, ...)
	*/
	public function SetDefaultTimezone($timezone)
	{
		return $this->CallApi('Account/SetDefaultTimezone',array('timezone' => $timezone),false);
	}
}
