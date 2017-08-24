<?php
/**
* Functionality for buying and payment
* 
* The Shopping API has some functions to handle shop items, links, sessions etc
*/
class ShoppingApi extends ApiClient
{
	/**
	* Returns a list of available items from the shop.
	* 
	* When a user is logged in, it will also contain the items only available to validated customers.
	*/
	public static function ListAvailableItems()
	{
		return self::StaticApi('Shopping/ListAvailableItems',array(),false);
	}

	/**
	* Returns an invoice pdf file for a payment (see UserInfo/ListOrders).
	* 
	* Returns binary data so call with API_FORMAT_PASSTHRU.
	*/
	public static function GetInvoice($payment_uuid)
	{
		return self::StaticApi('Shopping/GetInvoice',array('payment_uuid' => $payment_uuid),false);
	}

	/**
	* Returns different shop links.
	* 
	* Use these links to open a browser window with the shop in a specific state.
	* @param <string> $type Type of link to return. Available: '', credit_packs, basic_plan, pro_plan, checkout
	* @param <string> $product Product to add. Available: <br/>&mdash; '' <br/>&mdash; BasicPlan12, ProPlan12 <br/>&mdash; OnDemand <br/>&mdash;  Pack10, Pack30, Pack50, Pack100, Pack250, Pack500, Pack1000
	* @param <string> $pay (DEPRECATED) direct leads to checkout page
	*/
	public static function GetShopLink($type = false, $product = false, $pay = false)
	{
		return self::StaticApi('Shopping/GetShopLink',array('type' => $type, 'product' => $product, 'pay' => $pay),false);
	}

	/**
	* INTERNAL
	* 
	* Shop passes the order to this function to start processing of the order (add credit, get number, etc)
	*/
	public static function HandleOrderSuccess($shop_user_id, $order_id, $items, $orderdata, $filedata = false)
	{
		return self::StaticApi('Shopping/HandleOrderSuccess',array('shop_user_id' => $shop_user_id, 'order_id' => $order_id, 'items' => $items, 'orderdata' => $orderdata, 'filedata' => $filedata),false);
	}

	/**
	* INTERNAL
	* 
	* Refund a shop order
	*/
	public static function HandleOrderRefund($shop_user_id, $order_id, $items)
	{
		return self::StaticApi('Shopping/HandleOrderRefund',array('shop_user_id' => $shop_user_id, 'order_id' => $order_id, 'items' => $items),false);
	}

	/**
	* Returns a list of countries where new fax-in numbers are currently available
	* @param string|null $culture attribute[UseCache]
	*/
	public static function ListFaxInCountries($culture = '')
	{
		return self::StaticApi('Shopping/ListFaxInCountries',array('culture' => $culture),false);
	}

	/**
	* Returns available fax-in area codes in a given country+state. See Shopping::ListFaxInCountries for country- and state-codes. If require_pi is 1, it means that user can only get fax-in number with this area code if he sends a verification document (see www.pamfax.biz/KB85)
	* @param string $country_code Countrycode of country to list (ISO 3166 Alpha 2 country codes. I.e. US, DE, ...)
	* @param string $state Otional state code (currently USA only)
	*/
	public static function ListFaxInAreacodes($country_code, $state = false)
	{
		return self::StaticApi('Shopping/ListFaxInAreacodes',array('country_code' => $country_code, 'state' => $state),false);
	}

	/**
	* This is replacement for current ValidateAddressForFaxInNumber. The new API required more fields for checking address.
	* @param int $group_id didGroupId
	* @param string $salutation Possible values<br>&mdash; MR or MS if first and last name filled<br>&mdash; COMPANY if company filled
	* @param string $country_code_a3 alpha-3 country code, example DEU for Germany
	* @param string $city valid city name in country, example Berlin for Germany
	* @param string $zip_code valid zip code for city/country
	* @param string $street_name street name
	* @param string $building_number building number
	* @param string $building_letter building letter, if exists
	* @param string $company_name mandatory if salutation is COMPANY
	* @param string $first_name mandatory if salutation MR or MS
	* @param string $last_name mandatory if salutation MR or MS
	* @param string $verify_doc_uuid mandatory if group_id required proof validation,
	* @param string $token used only for internal calls from portal/common/website pamfax.biz
	* @param string $rnd used only for internal calls from portal/common/website pamfax.biz
	* @param string $trial_request pass 1 if trial number will be purchased
	*/
	public static function CheckAddressForFaxInNumber($group_id, $salutation, $country_code_a3, $city, $zip_code, $street_name, $building_number, $building_letter = '', $company_name = '', $first_name = '', $last_name = '', $verify_doc_uuid = '', $token = '', $rnd = '', $trial_request = '')
	{
		return self::StaticApi('Shopping/CheckAddressForFaxInNumber',array('group_id' => $group_id, 'salutation' => $salutation, 'country_code_a3' => $country_code_a3, 'city' => $city, 'zip_code' => $zip_code, 'street_name' => $street_name, 'building_number' => $building_number, 'building_letter' => $building_letter, 'company_name' => $company_name, 'first_name' => $first_name, 'last_name' => $last_name, 'verify_doc_uuid' => $verify_doc_uuid, 'token' => $token, 'rnd' => $rnd, 'trial_request' => $trial_request),false);
	}

	/**
	* INTERNAL!!! Add test fax in number with Basic plan to current PamFax user Now available to call only from Portal/Common/Website, but in future may be activated for all API partners.
	*/
	public static function RequestTrialNumber($group_id, $address_uuid, $token, $rnd)
	{
		return self::StaticApi('Shopping/RequestTrialNumber',array('group_id' => $group_id, 'address_uuid' => $address_uuid, 'token' => $token, 'rnd' => $rnd),false);
	}

	/**
	* Validates an address if it's suitable for buying a fax-number
	* 
	* Before purchasing, you can validate if the given address is valid to purchase a fax-in number with the given area code.
	* In some countries, you are only allowed to purchase a number at the address where you live (i.e. Germany).
	* If this function returns require_personal_ident = 1, you need to add a scanned document (as file upload) containing the address of the user to HandleOrderSuccess. The document will then be verified manually and then the number will be assigned.
	* area_code_id is the id field of the area code of the ListFaxInAreaCodes call
	*/
	public static function ValidateAddressForFaxInNumber($area_code_id, $name, $company, $address1, $zip, $city, $country_code)
	{
		return self::StaticApi('Shopping/ValidateAddressForFaxInNumber',array('area_code_id' => $area_code_id, 'name' => $name, 'company' => $company, 'address1' => $address1, 'zip' => $zip, 'city' => $city, 'country_code' => $country_code),false);
	}

	/**
	* Redeem a credit voucher.
	* 
	* These are different then the shop vouchers to be used in the online shop!
	* You can use "PCPC0815" to test this function in the Sandbox API
	* @param string $vouchercode vouchercode The voucher code. Format is ignored.
	*/
	public static function RedeemCreditVoucher($vouchercode)
	{
		return self::StaticApi('Shopping/RedeemCreditVoucher',array('vouchercode' => $vouchercode),false);
	}

	/**
	* Get the nearest available fax-in area code for the given IP-Address.
	* 
	* Used to show "You can get a fax-in number in <area_code>..." to the user to offer PamFax plans to him
	* @param string $ip_address IP-Address to find nearest number for
	*/
	public static function GetNearestFaxInNumber($ip_address)
	{
		return self::StaticApi('Shopping/GetNearestFaxInNumber',array('ip_address' => $ip_address),false);
	}

	/**
	* Returns price for new FaxIn Number and plan.
	* @param string $plan_type availible BASIC or PRO
	* @param string $months value from 1 to 12
	*/
	public static function GetNumberPrice($plan_type, $months = '1')
	{
		return self::StaticApi('Shopping/GetNumberPrice',array('plan_type' => $plan_type, 'months' => $months),false);
	}

	/**
	* @param string $number current active fax in number
	* @param string $number current active fax in number
	*/
	public static function GetExtendNumberCost($number)
	{
		return self::StaticApi('Shopping/GetExtendNumberCost',array('number' => $number),false);
	}

	/**
	* Return all availible for sale numbers from pool Note: Don't forget, $ocuntry_code should be match with available fax in country codes. For example, if you want get result for RU you receive error message "Invalid country", sure RU not invalid, but now not available in PamFax FaxIn numbers.
	* @param string $country_code Countrycode of country to list (ISO 3166 Alpha 2 country codes. I.e. US, DE, ...)
	* @param string $prefix Prefix for number, example +375 or +49
	* @param string $us_state Optional state code (currently USA only)
	* @param string $group_id Optional FaxIn number group_id,
	* @param string $limit Optional Limit for result max=100
	*/
	public static function ListAvailablePoolNumbers($country_code, $prefix = '', $us_state = '', $group_id = '', $limit = '10')
	{
		return self::StaticApi('Shopping/ListAvailablePoolNumbers',array('country_code' => $country_code, 'prefix' => $prefix, 'us_state' => $us_state, 'group_id' => $group_id, 'limit' => $limit),false);
	}

	/**
	* Some FaxIn numbers requires upload Personal Info doc. This function returns needed PI doc or not for given group_id
	* @param string $group_id from ListFaxInAreacodes
	*/
	public static function isPIRequiredForGroup($group_id)
	{
		return self::StaticApi('Shopping/isPIRequiredForGroup',array('group_id' => $group_id),false);
	}

	/**
	* Replace current verification doc for FaxIn number to new. Number will be temporary marked as UNVERIFIED. Supported validation doc extensions @see Shopping::UploadVerifyDoc
	* @param string $number Valid user fax-in number, example +1234567890
	*/
	public static function ChangeVerifyDoc($number, $file_size, $file_contentmd5)
	{
		return self::StaticApi('Shopping/ChangeVerifyDoc',array('number' => $number, 'file_size' => $file_size, 'file_contentmd5' => $file_contentmd5),false);
	}

	/**
	* Some numbers require verification document. Store it before calling @see Shopping::OrderNumberForPFCredit Also, require_pi value available from @see Shopping::isPIRequiredForGroup Note! Required file_size or file_contentmd5 -- uploaded file will be checked (handling upload errors in API)
	* 
	* Supported extensions: "png", "jpg", "doc", "docx", "pdf", "jpeg", "tiff", "tif", "gif", "bmp"
	* Supported MIME: 'application/octet-stream', 'image/png', 'image/jpeg', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/pdf', 'image/tiff', 'image/bmp', 'image/gif'
	* Max file size: 2 Mb
	* Pass $address_uuid if this verify doc should be attached to FaxInAddress
	* @param string $file_size size of file in bytes
	* @param string $file_content_md5 md5 of file
	*/
	public static function UploadVerifyDoc($file_size = '', $file_content_md5 = '')
	{
		return self::StaticApi('Shopping/UploadVerifyDoc',array('file_size' => $file_size, 'file_content_md5' => $file_content_md5),false);
	}

	/**
	* Converts current Basic plan to Professional plan (PamFax credit will be used). Pass $only_calculate = 0 for real order. 1 - if only upgrade calculation cost needed
	* @param string $number fax in number, example +12345678. Number should have valid BASIC plan
	*/
	public static function UpgradeBasic2ProForPFCredit($number, $only_calculate = '1')
	{
		return self::StaticApi('Shopping/UpgradeBasic2ProForPFCredit',array('number' => $number, 'only_calculate' => $only_calculate),false);
	}

	/**
	* Replacement for Shopping::ValidateAddressForFaxInNumber.
	* @param string $name name and surname. Example John Smith
	* @param string $address address, example Tverskaya strasse, 22
	* @param string $zip zip code, example 220000
	* @param string $city city name, example Berlin
	* @param string $country_code ISO country code,
	* @param string $company user company name. Leave blank if no company in user account. Example "PamConsult GmbH"
	* @param string $state_code State code if country have states.
	* @param int $only_validate use 0 for real order, 1 if only validation needed
	* @param int $months 1..12 months in plan
	* @param string $pool_id for order or extending numbers from pool.
	* @param string $verify_doc_uuid if number require_pi = 1. pass uuid from
	*/
	public static function OrderNumberForPFCredit($area_code_id, $name, $address, $zip, $city, $country_code, $plan_type, $state_code = '', $company = '', $only_validate = '1', $months = '1', $pool_id = '', $verify_doc_uuid = '')
	{
		return self::StaticApi('Shopping/OrderNumberForPFCredit',array('area_code_id' => $area_code_id, 'name' => $name, 'address' => $address, 'zip' => $zip, 'city' => $city, 'country_code' => $country_code, 'plan_type' => $plan_type, 'state_code' => $state_code, 'company' => $company, 'only_validate' => $only_validate, 'months' => $months, 'pool_id' => $pool_id, 'verify_doc_uuid' => $verify_doc_uuid),false);
	}

	/**
	* @param string $number fax in number to extend, example +1234567890
	* @param string $name name and surname. Example John Smith
	* @param string $address address, example Tverskaya strasse, 22
	* @param string $zip zip code, example 220000
	* @param string $city city name, example Berlin
	* @param string $country_code ISO country code,
	* @param string $company user company name. Leave blank if no company in user account. Example "PamConsult GmbH"
	* @param string $state_code State code if country have states.
	* @param int $only_validate use 0 for real order, 1 if only validation needed
	* @param int $months 1..12 months in plan
	*/
	public static function ExtendNumberForPFCredit($number, $name, $address, $zip, $city, $country_code, $plan_type, $state_code = '', $company = '', $only_validate = '1', $months = '1')
	{
		return self::StaticApi('Shopping/ExtendNumberForPFCredit',array('number' => $number, 'name' => $name, 'address' => $address, 'zip' => $zip, 'city' => $city, 'country_code' => $country_code, 'plan_type' => $plan_type, 'state_code' => $state_code, 'company' => $company, 'only_validate' => $only_validate, 'months' => $months),false);
	}

	/**
	* Returns all available items for sale for PamFax credit. Now supported only Plans with FaxIn numbers.
	*/
	public static function ListAvailableItemsForPFCredit()
	{
		return self::StaticApi('Shopping/ListAvailableItemsForPFCredit',array(),false);
	}

	/**
	* INTERNAL!!!
	* @return 
	*/
	public static function UploadVerifyDocFromPortal($token)
	{
		return self::StaticApi('Shopping/UploadVerifyDocFromPortal',array('token' => $token),false);
	}
}
