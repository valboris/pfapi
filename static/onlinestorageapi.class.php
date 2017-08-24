<?php
/**
* Provides access to various online storage providers.
* 
* This API will only perform authentication and file listings. To use the files
* please refer to FaxJobApi::AddFileFromOnlineStorage. That one is the place where
* you'll have to put in the UUIDs from the listings retrieved here.
* Note:
* File listings will be stored and so stay available until the ned call to
* OnlineStorageApi::ListFolderContents with an empty folder given (root folder).
* That call will erase all stored listing data and file UUIDs will be regenerated.
* <b><u>Authentication</u></b>
* To authenticate PamFax to use you an online storage provider you will now need a Webbrowser.
* Just call <u>https://api.pamfax.biz/OspAuth/&lt;provider_name&gt;</u> (note: sandbox-api is the right subdomain for sandbox testing) and the workflow will start.
* &lt;provider_name&gt; := GoogleStorage|DropboxStorage|BoxnetStorage
* Note: For backwards compatibility you may skip the 'Storage' part of the provider_name.
* Please make sure you pass the usertoken to the URL by COOKIE or by GET (?usertoken=&lt;your_current_usertoken&gt;).
* That page will redirect the user to the providers authentication page (and ensures that he logs in before).
* Once the user granted access the browser is redirected to our page again and we deliver a success page.
* You may also add a backlink argument to the first URL to ensure PamFax redirects back to your service:
* https://api.pamfax.biz/OspAuth/GoogleStorage?usertoken=123somevalue456&backlink=http%3A%2F%2Fyourdomain%2Ecom%2Fanypage
* <ol>
* <li>When you specify a backlink argument it will be called without any argument on success, or with ?error=&lt;message&gt; on failure</li>
* <li>Without backlink PamFax will deliver an HTML page containing
* <ul>
* <li>a div element &lt;div id='pamfax_auth_state' style='display:none'&gt;state&lt;/div&gt;<br/>(where &lt;state&gt; := ok|err)</li>
* <li>JS code that sets a variable window.pamfax_oauth_state = '&lt;state&gt;';<br/>(where &lt;state&gt; := ok|err)</li>
* </ul></li>
* <li>PamFax calls window.close() when you do not specify the backlink argument. Most Browsers will ignore that, but it
* simplifies things for you if you are able to allow scripts to close windows.</li>
* </ol>
*/
class OnlineStorageApi extends ApiClient
{
	/**
	* Returns a list of supported providers.
	*/
	public static function ListProviders($attach_settings = false)
	{
		return self::StaticApi('OnlineStorage/ListProviders',array('attach_settings' => $attach_settings),false);
	}

	/**
	* Authenticate the current user for a Provider.
	* 
	* This is a one-time process and must be done only once for each user.
	* PamFax API will perform the login and store only an authentication token which
	* will be enough for future use of the service.
	* @param string $provider Provider name. See OnlineStorage::ListProviders for available providers.
	* @param string $username The user's name/login for the provider
	* @param string $password User's password to access his data at the provider side
	* @deprecated See OnlineStorage description for how to authenticate
	*/
	public static function Authenticate($provider, $username, $password)
	{
		return self::StaticApi('OnlineStorage/Authenticate',array('provider' => $provider, 'username' => $username, 'password' => $password),false);
	}

	/**
	* Manually sets auth token for the current user.
	* 
	* $token must contain an associative array including the tokens.
	* sample (google):
	* 'auth_token_cp'=>DQAAAHoAAADCrkpB7Ip_vuelbla2UKE9s_ObVKTNA_kT6Ej26SwddJvMUmEz_9qbLlZJnsAdm583Sddp_0FYS9QmmwoUpf51RHxkgPUL20OqsdAP5OnCgY_TdVbvXX8tMQBBX30V4_NhTcE_0sI6zhba5Y3yZWV5nljliG98eA36ybekKucuhQ
	* 'auth_token_writely'=>DQAAAHoAAADCrkpB7Ip_vuelbla2UKE9s_ObVKTNA_kT6Ej62SDwdJvMUmEz_9qbLlZJnsAdm583Sddp_0FYS9QmmwoUpf51RHxkgPUL20OqsdAP5OnCgY_TdVbvXX8tMQBBX30V4_NhTcE_0sI6zhba5Y3yZWV5nljliG98eA36ybekKucuhQ
	* 'auth_token_wise'=>DQAAAHoAAADCrkpB7Ip_vuelbla2UKE9s_ObVKTNA_kT6Ej26SDdwJvMUmEz_9qbLlZJnsAdm583Sddp_0FYS9QmmwoUpf51RHxkgPUL20OqsdAP5OnCgY_TdVbvXX8tMQBBX30V4_NhTcE_0sI6zhba5Y3yZWV5nljliG98eA36ybekKucuhQ
	* 'auth_token_lh2'=>DQAAAHoAAADCrkpB7Ip_vuelbla2UKE9s_ObkvTNA_kT6Ej26SDwdJvMUmEz_9qbLlZJnsAdm583Sddp_0FYS9QmmwoUpf51RHxkgPUL20OqsdAP5OnCgY_TdVbvXX8tMQBBX30V4_NhTcE_0sI6zhba5Y3yZWV5nljliG98eA36ybekKucuhQ
	* @param string $provider Provider name
	* @param array $token Associative array with token information
	* @param string $username Optional username (for displaying purposes)
	*/
	public static function SetAuthToken($provider, $token, $username = false)
	{
		return self::StaticApi('OnlineStorage/SetAuthToken',array('provider' => $provider, 'token' => $token, 'username' => $username),false);
	}

	/**
	* Will drop the users authentication for the given provider.
	* 
	* This will permanently erase all data related to the account!
	*/
	public static function DropAuthentication($provider)
	{
		return self::StaticApi('OnlineStorage/DropAuthentication',array('provider' => $provider),false);
	}

	/**
	* Outputs a providers logo in a given size.
	* 
	* Call ListProviders for valid sizes per Provider.
	*/
	public static function GetProviderLogo($provider, $size)
	{
		return self::StaticApi('OnlineStorage/GetProviderLogo',array('provider' => $provider, 'size' => $size),true);
	}

	/**
	* Lists all files and folders inside a given folder.
	* 
	* Leave $folder empty to get the contents of the root folder.
	* User must be autheticated for the provider given here to be able to recieve listings (see Authenticate method).
	* If clear_cache is set to true all subitems will be deleted and must be refetched (previously cached UUIDs are invalid)
	*/
	public static function ListFolderContents($provider, $folder = false, $clear_cache = false)
	{
		return self::StaticApi('OnlineStorage/ListFolderContents',array('provider' => $provider, 'folder' => $folder, 'clear_cache' => $clear_cache),false);
	}

	/**
	* Stores a file into an online storage.
	* 
	* $fullpath specifies the path in the providers 'filesystem', so could be '/somefolder/sub1/myfile.txt'.
	* Will create all missing folders in the path (in the sample 'somefolder' and 'sub1') recusively before storing the file.
	* Expects the files data in the $_FILES array named 'file' ($_FILES['file']), so you'll need to
	* do an upload or put info in there manually before calling this method.
	* NOTE: To be sure we have actual data buffered StoreFile will clear the providers cache. So all folder and file uuids
	* needs to be reread by your side.
	* @param string $provider Provider to use
	* @param string $fullpath Path of the file in the Providers 'filesystem'
	*/
	public static function StoreFile($provider, $fullpath = '/')
	{
		return self::StaticApi('OnlineStorage/StoreFile',array('provider' => $provider, 'fullpath' => $fullpath),false);
	}

	/**
	* Return info about current provider state.
	* @param string $provider Provider name.
	*/
	public static function CheckProviderState($provider)
	{
		return self::StaticApi('OnlineStorage/CheckProviderState',array('provider' => $provider),false);
	}
}
