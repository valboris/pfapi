<?php
/**
* The Common API contains functionality to get global information like system status, supported file types, etc.
*/
class CommonApi extends ApiClient
{
	/**
	* Returns the supported file types for documents that can be faxed.
	*/
	public function ListSupportedFileTypes()
	{
		return $this->CallApi('Common/ListSupportedFileTypes',array(),true);
	}

	/**
	* Returns file content.
	* 
	* Will return binary data and headers that give the filename and mimetype.
	* Note: User identified by usertoken must be owner of the file or
	* the owner must have shared this file to the requesting user.
	* Share dependencies are resolved (dependend on the type) via the
	* fax_inbox, fax_history_files or user_covers table.
	* Note: If you are using ApiClient reference implementation make sure
	* you set $GLOBALS['PAMFAX_API_MODE'] = ApiClient::API_MODE_PASSTHRU;
	* This will force the results of GetFile to be directly written out.
	* @param string $file_uuid The uuid of the file to get
	*/
	public function GetFile($file_uuid)
	{
		return $this->CallApi('Common/GetFile',array('file_uuid' => $file_uuid),false);
	}

	/**
	* Returns a preview page for a fax.
	* 
	* May be in progress, sent or from inbox.
	* @param string $uuid The uuid of the fax to get preview for
	* @param int $page_no Page number to get (1,2,...)
	* @param int $max_width Maximum width in Pixel
	* @param int $max_height Maximum height in Pixel
	*/
	public function GetPagePreview($uuid, $page_no, $max_width = false, $max_height = false)
	{
		return $this->CallApi('Common/GetPagePreview',array('uuid' => $uuid, 'page_no' => $page_no, 'max_width' => $max_width, 'max_height' => $max_height),false);
	}

	/**
	* Returns all countries with their translated names and the default zone
	* @param string $culture culture identifier, defaults to users culture. Accepts full culture-codes like en-US, de-DE and just a language code like en, de, ...
	*/
	public function ListCountries($culture = false)
	{
		return $this->CallApi('Common/ListCountries',array('culture' => $culture),true);
	}

	/**
	* Returns all countries in the given zone
	* 
	* Result includes their translated names, countrycode and country-prefix.
	* @param int $zone Zone of the country which is wanted (1-7)
	*/
	public function ListCountriesForZone($zone)
	{
		return $this->CallApi('Common/ListCountriesForZone',array('zone' => $zone),true);
	}

	/**
	* Returns price and price_pro for a given zone
	*/
	public function ListZones()
	{
		return $this->CallApi('Common/ListZones',array(),true);
	}

	/**
	* Returns the list of supported currencies.
	* 
	* Result contains convertion rates too.
	* If $code is given will only return the specified currency's information.
	* @param <string> $code CurrencyCode
	*/
	public function ListCurrencies($code = false)
	{
		return $this->CallApi('Common/ListCurrencies',array('code' => $code),true);
	}

	/**
	* Returns a list of strings translated into the given language.
	* @param array $ids array of String identifiers. You may also pass a comma separated list as $ids[0] (ids[0]=BTN_YES[NT],BTN_NO[NT]).
	* @param string $culture culture identifier, defaults to users culture. Accepts full culture-codes like en-US, de-DE and just a language code like en, de, ...
	*/
	public function ListStrings($ids = false, $culture = false)
	{
		return $this->CallApi('Common/ListStrings',array('ids' => $ids, 'culture' => $culture),true);
	}

	/**
	* Returns the current settings for timezone and currency.
	* 
	* This is the format/timezone ALL return values of the API are in. These are taken from the user
	* (if logged in, the api user's settings or the current ip address)
	*/
	public function GetCurrentSettings()
	{
		return $this->CallApi('Common/GetCurrentSettings',array(),false);
	}

	/**
	* Returns the current culture info data.
	*/
	public function GetCurrentCultureInfo()
	{
		return $this->CallApi('Common/GetCurrentCultureInfo',array(),false);
	}

	/**
	* Lists the current Versions.
	* 
	* Result contains versions for the PamFax Gadget, Client etc and returns
	* the version and update url.
	* Use is_beta param if needed latest Win & Office integrations beta versions.
	*/
	public function ListVersions($is_beta = false)
	{
		return $this->CallApi('Common/ListVersions',array('is_beta' => $is_beta),false);
	}

	/**
	* Returns Geo information based on the given IP address (IPV4)
	* @param string $ip the ip to get geo information off
	*/
	public function GetGeoIPInformation($ip)
	{
		return $this->CallApi('Common/GetGeoIPInformation',array('ip' => $ip),true);
	}

	/**
	* List all available languages.
	* 
	* Result may be filtered to that only languages are returned that are
	* at least translated $min_percent_translated %
	* @param int $min_percent_translated the percentage value the languages have to be translated
	*/
	public function ListLanguages($min_percent_translated = 75)
	{
		return $this->CallApi('Common/ListLanguages',array('min_percent_translated' => $min_percent_translated),true);
	}

	/**
	* List all supported timezones.
	* 
	* TimezonesList result list will contain attributes 'default'
	* and (if a user is logged in) 'user_timezone' which contain that values.
	* Additionally the corresponding list entries are marked with attributes
	* 'is_default' and 'is_user_timezone'.
	*/
	public function ListTimezones()
	{
		return $this->CallApi('Common/ListTimezones',array(),true);
	}

	/**
	* List states in country
	* @param string $country_code should be in ISO format (example US for USA, RU for Russia, DE for Germany)
	*/
	public function ListCountryStates($country_code = 'US')
	{
		return $this->CallApi('Common/ListCountryStates',array('country_code' => $country_code),true);
	}

	/**
	* Returns list of all supported cities for FaxIn Numbers ordering in countries with regulation required (example DEU Germany). Note return: NO_CITIES if country not needed strongly city value for number ordering.
	* @param string $country_code_a3 Alpha3 country code, DEU for Germany
	*/
	public function ListCities($country_code_a3)
	{
		return $this->CallApi('Common/ListCities',array('country_code_a3' => $country_code_a3),true);
	}

	/**
	* Returns all zip codes supported in received alpha-3 country code and city City value (id or name) should be given from Common::ListCities
	* @param string $country_code_a3 Alpha-3 country code, DEU for Germany
	* @param string $city_name_or_id ID or CityName from Common::ListCities
	* @param boolean $exact use 1 if exact values needed, for example Berlin will be ignore codes for "Berlin, stadt"
	*/
	public function ListZipCodes($country_code_a3, $city_name_or_id, $exact = false)
	{
		return $this->CallApi('Common/ListZipCodes',array('country_code_a3' => $country_code_a3, 'city_name_or_id' => $city_name_or_id, 'exact' => $exact),true);
	}

	/**
	* Future replacement for ListCountriesForZone. Returns the list of all supported countries for fax sending grouped by prices. For example cost to BY = .06ec, RU = .08ec, DE = .02ec, US = .06ec A list will be returned with Group 1, price basic. pro, on demand: DE Group 2, price basic. pro, on demand: BY, US Group 3, price basic. pro, on demand: RU List will be sorted by prices ASC
	* @param string $language alpha-2 code for translation if needed. List of supported languages: call Common::ListLanguages
	*/
	public function ListCountriesPrices($language = 'EN')
	{
		return $this->CallApi('Common/ListCountriesPrices',array('language' => $language),false);
	}

	/**
	* Returns  necessary format for later formation. For example: if country China, return ¥1.
	*/
	public function GetFormattedPrice($ip = false)
	{
		return $this->CallApi('Common/GetFormattedPrice',array('ip' => $ip),false);
	}

	/**
	* Returns  necessary format for later formation. For example: if country China, return ¥1.
	* @param string $lang language code DE EN IT
	*/
	public function GetCurrencyByLang($lang)
	{
		return $this->CallApi('Common/GetCurrencyByLang',array('lang' => $lang),false);
	}

	/**
	* Set culture for non-authorized users. API will return messages for non-authorized users: 1. Auto-detect language (geo-ip, etc) 2. Developer can set language by this function and API will not make auto-detects, valid on in current session
	* @param string $language alpha-2 code for translation if needed. List of supported languages: call Common::ListLanguages
	*/
	public function SetCulture($language)
	{
		return $this->CallApi('Common/SetCulture',array('language' => $language),false);
	}
}
