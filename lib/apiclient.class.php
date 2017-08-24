<?php

if( !isset($GLOBALS['PAMFAX_API_URL']) )
	$GLOBALS['PAMFAX_API_URL']         = "https://api.pamfax.biz/";
if( !isset($GLOBALS['PAMFAX_API_APPLICATION']) )
	$GLOBALS['PAMFAX_API_APPLICATION'] = ""; // Your application name here
if( !isset($GLOBALS['PAMFAX_API_SECRET_WORD']) )
	$GLOBALS['PAMFAX_API_SECRET_WORD'] = ""; // Your secret word here
if( !isset($GLOBALS['PAMFAX_API_MODE']) )
	$GLOBALS['PAMFAX_API_MODE'] = ApiClient::API_MODE_XML;

if( !isset($GLOBALS['PAMFAX_API_USERTOKEN']) )
	$GLOBALS['PAMFAX_API_USERTOKEN'] = ""; // Set this value to result of Authentication/Verify
if( !isset($GLOBALS['PAMFAX_API_CACHING_DISABLED']) )
	$GLOBALS['PAMFAX_API_CACHING_DISABLED'] = false;
if( !isset($GLOBALS['PAMFAX_API_CACHING_TTL']) )
	$GLOBALS['PAMFAX_API_CACHING_TTL'] = 3600;		// how long to cache the results (in sec)? default = 60 minutes
if( !isset($GLOBALS['PAMFAX_NO_TYPED_OBJECTS']) )
	$GLOBALS['PAMFAX_NO_TYPED_OBJECTS'] = false;	// if true API_MODE_OBJECT will create stdClasses only and ignore type bindings

class ApiClient
{
	private static $_instance;
	private static $_loggingHandler = false;
	public static $LastUrl = "";
	public static $LastData = "";
	private $_lastResult = "";
	private $_lastCallInfo = array();
	private $_lastHeader = "";

	private $_unatt_find = array("&lt;", "&gt;", "&amp;", "&quot;", "&apos;");
	private $_unatt_replace = array("<", ">", "&", "\"","'");
	private static $_class_exists_cache = array();

	const API_MODE_XML = 0x00;
	const API_MODE_JSON = 0x01;
	const API_MODE_OBJECT = 0x02;
	const API_MODE_PASSTHRU = 0x03;
	const API_MODE_RETURN = 0x04;

	private static $_measureTimes = false;
	public static $TotalRequestingTime = 0;
	public static $TotalParsingTime = 0;

	private static function Instance()
	{
		if( !self::$_instance )
			self::$_instance = new ApiClient();
		return self::$_instance;
	}

	/**
	 * Registers a logging handler.
	 * May be a function name or an array($handler_object,$method_name).
	 * @param string|array $callback Callback that will handle the Logging. Signature: function(string $text)
	 */
	public static function RegisterLogFunction($callback,$measureTimes = false)
	{
		self::$_loggingHandler = $callback;
		if( $measureTimes )
			self::$_measureTimes = true;
	}

	public static function SetMeasuring($measureTimes = false)
	{
		self::$_measureTimes = $measureTimes;
	}

	private static function Log($text)
	{
		if( !self::$_loggingHandler )
			return;
		call_user_func(self::$_loggingHandler,$text);
	}

	/**
	 * Strips given tags from array (GET, POST, REQUEST)
	 * @see http://www.php.net/manual/en/function.strip-tags.php#93567
	 * @param array $param Parameter array to strip
	 */
	private static function sanitize_parameters(&$params)
	{
		$tags = array('script'); // for now only strip out <script> tags. may change later
		$size = sizeof($tags);
		$keys = array_keys($tags);
		$paramsize = sizeof($params);
		$paramkeys = array_keys($params);

		for ($j=0; $j<$paramsize; $j++)
		{
			for ($i=0; $i<$size; $i++)
			{
				$tag = $tags[$keys[$i]];
				if(is_string($params[$paramkeys[$j]]))
				{
					if(stripos($params[$paramkeys[$j]], $tag) !== false)
						$params[$paramkeys[$j]] = preg_replace('#</?'.$tag.'[^>]*>#is', '', $params[$paramkeys[$j]]);
				}
				elseif(is_array($params[$paramkeys[$j]]))
					self::sanitize_parameters($params[$paramkeys[$j]]);
			}
		}
	}

	/**
	 * Calculates the API argument checksum.
	 */
	public static function CalcApiCheck($data,$api_secret_word)
	{
		self::sanitize_parameters($data);

		$data = array_change_key_case($data,CASE_LOWER);
		ksort($data);
		$apicheck = "";
		$sessionname = session_name();
		foreach ($data as $k => $val)
		{
			if(is_object($val) == false)
			{
				switch( $k )
				{
					case 'page':
					case 'event':
					case 'apikey':
					case 'apicheck':
					case 'usertoken':
					case 'xdebug_profile':
					case $sessionname:	// ignore passed session_id
						break;
					default:
						if(isset($val))
						{
							if( is_array($val) )
								$apicheck .= self::GetArrayVals($val);
							else
								$apicheck .= self::ValidateApiVal($val);
						}
						break;
				}
			}
		}
		$apicheck .= $api_secret_word;
//		log_debug($apicheck);
//		log_debug("apicheck: $apicheck$api_secret_word = ".md5($apicheck));
		return md5($apicheck);
	}

	private static function ValidateApiVal($val)
	{
		if (is_bool($val))
			return $val ? "1" : "0" ;
		else if( substr($val,0,1) == "@" )// skip file arguments
			return "";

		return $val;
	}

	private static function GetArrayVals($array)
	{
		$array = array_change_key_case($array,CASE_LOWER);
		ksort($array);

		$arrayVals = "";

		foreach($array as $key => $val)
		{
			if (is_array($val))
				$arrayVals .= self::GetArrayVals($val);
			else
				$arrayVals .= self::ValidateApiVal($val);
		}

		return $arrayVals;
	}

	private function _request($url, $postdata = null, $timeout = 120, $redirected=false)
	{
		global $PAMFAX_API_URL, $PAMFAX_API_APPLICATION, $PAMFAX_API_SECRET_WORD;

		if( $PAMFAX_API_URL == "" || $PAMFAX_API_APPLICATION == "" || $PAMFAX_API_SECRET_WORD == "" )
			die("Please define your API credentials ($PAMFAX_API_URL, $PAMFAX_API_APPLICATION, $PAMFAX_API_SECRET_WORD)");

		if( !$redirected )
			$url = $PAMFAX_API_URL . $url;

		$this->_lastCallInfo = array();
		$this->_lastHeader = "";
		$this->_lastResult = "";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, intval($timeout));
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
		curl_setopt($ch, CURLOPT_HEADER, 1);

		$headers = array (
			'Keep-Alive: 300',
			'Connection: Keep-Alive',
			'X-ClientIP: '.(isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:$_SERVER['SERVER_ADDR'])
		);

		if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && $_SERVER['HTTP_ACCEPT_LANGUAGE'] != "" )
		{
			// pass through accepted languages of client
			$headers[] = 'Accept-Language: '.$_SERVER['HTTP_ACCEPT_LANGUAGE'];
		}
		// pass the geolocation language of the user to api
		if(isset($GLOBALS["user_geoip_language"]))
			$headers[] = 'Force-Language: '.$GLOBALS["user_geoip_language"];
		elseif( function_exists('get_countrycode_by_ip') && $this->_class_exists("Localization"))
		{
			// note: this sets the IP detected culture to the Force-Language header, which then
			// overrides the BROWSER detected culture in Localization::getBrowserCulture()
			$ci = Localization::getIPCulture();
			if( $ci )
			{
				$GLOBALS["user_geoip_language"] = $ci->Code;
				$headers[] = 'Force-Language: '.$GLOBALS["user_geoip_language"];
			}
		}

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, $url);

		// use a cookie file to store the cookies from this request. this ensures session will stay alive on api server (optional!)
//		$cookie_file = ini_get("session.save_path")."/".session_name()."_".session_id().".txt";
//		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
//		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, "PamFax APIClient PHP");
	////PAMFAX-6347 Was added
		if(empty($postdata)) {
			$postdata = [];
		}
        elseif(!is_array($postdata)) {
            parse_str($postdata, $postdata);
        }
////PAMFAX-6347
		if($postdata !== null)
		{
			if( $GLOBALS['PAMFAX_API_MODE'] == self::API_MODE_JSON )
				$postdata['apioutputformat'] = "API_FORMAT_JSON";
			else
				$postdata['apioutputformat'] = "API_FORMAT_XML";

			if(isset($_SESSION["XDEBUG_PROFILE"]) && ($_SESSION["XDEBUG_PROFILE"] == 1))
				$postdata['XDEBUG_PROFILE'] = 1;
if ((version_compare(PHP_VERSION, '5.5') < 0)){
			if( !$redirected )
			{
				$postdata['apikey']    = $PAMFAX_API_APPLICATION;
				if(isset($GLOBALS['PAMFAX_API_USERTOKEN']))
					$postdata['usertoken'] = $GLOBALS['PAMFAX_API_USERTOKEN'];

				$isfileupload = false;
				foreach($postdata as $k => $v)
				{
					if(is_string($v) && (strlen($v) > 1) && ($v{0} == "@") && file_exists(substr($v, 1)))
					{
						$isfileupload = true;
						if( !isset($postdata['file']) )
						{
						//	if ((version_compare(PHP_VERSION, '5.5') >= 0))
							//{
							//	$postdata['file'] = new CURLFile(substr($v,1));
							//	curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
						//	} else
							$postdata['file'] = $v;

							$postdata[$k] = basename(substr($v,1));
						}

						break;
					}
				}

				$postdata['apicheck']  =  self::CalcApiCheck($postdata, $PAMFAX_API_SECRET_WORD);

				if(!$isfileupload)		// only encode if no file upload
					$postdata = http_build_query($postdata);
			}

			//curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
			curl_setopt($ch, CURLOPT_POST, 1);
//			log_debug($postdata);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
}else {
			if( !$redirected )
			{
				$postdata['apikey']    = $PAMFAX_API_APPLICATION;
				if(isset($GLOBALS['PAMFAX_API_USERTOKEN']))
					$postdata['usertoken'] = $GLOBALS['PAMFAX_API_USERTOKEN'];

				$isfileupload = false;
				foreach($postdata as $k => $v)
				{
					if(is_string($v) && (strlen($v) > 1) && ($v{0} == "@") && file_exists(substr($v, 1)))
					{
						$isfileupload = true;

						if( !isset($postdata['file']) )
						{
						///Changes PAMFAX-6385
						//	if ((version_compare(PHP_VERSION, '5.5') >= 0))
						//	{
						//		$postdata['file'] = new CURLFile(substr($v,1));
						//		curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
						//	} else
								//		$postdata['file'] = $v;
						///Changes PAMFAX-6385
							$postdata['file'] = $v;
							$postdata[$k] = basename(substr($v,1));

						}
					break;
					}
				}
				///Changes PAMFAX-6385
				if (!empty($postdata['file']))
					$postdata['file'] = substr($postdata['file'],1);
				else
					$postdata['file'] = '';
				$postdata['file'] = new CURLFile($postdata['file']);
				///Changes PAMFAX-6385


				$postdata['apicheck']  =  self::CalcApiCheck($postdata, $PAMFAX_API_SECRET_WORD);

				if(!$isfileupload)		// only encode if no file upload
				$postdata = http_build_query($postdata);
			}
			else
				$postdata = http_build_query($postdata);

			curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
}

		} else {
			$postdata = array();

			if( $GLOBALS['PAMFAX_API_MODE'] == self::API_MODE_JSON )
				$postdata['apioutputformat'] = "API_FORMAT_JSON";
			else
				$postdata['apioutputformat'] = "API_FORMAT_XML";

			if(isset($_SESSION["XDEBUG_PROFILE"]) && ($_SESSION["XDEBUG_PROFILE"] == 1))
				$postdata['XDEBUG_PROFILE'] = 1;

			$postdata['apicheck'] = "";
		}


		set_time_limit(intval($timeout) + 20);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		self::$LastUrl = $url;
		self::$LastData = $postdata;

		$this->_lastResult = curl_exec($ch);
		if( $this->_lastResult === false )
		{
			trigger_error("ApiClient request error: ".curl_error($ch)." url: $url data: ".var_export(self::$LastData, true).")");
		}

		$this->_lastCallInfo = curl_getinfo($ch);
		curl_close($ch);
		if($this->_lastResult != "")
		{
			$this->_lastHeader = substr($this->_lastResult, 0, $this->_lastCallInfo['header_size']);
			$this->_lastResult = substr($this->_lastResult, $this->_lastCallInfo['header_size']);
		}

		// response is a HTTP forwarding, so follow it
		if(stripos($this->_lastHeader, "\r\nlocation:"))
		{
			foreach( explode("\r\n",$this->_lastHeader) as $head )
			{
				$h = explode(":",$head,2);
				if( strtolower(trim($h[0])) == 'location' )
				{
					$url = trim($h[1]);
					if (strpos($url,"https://api.pamfax.biz") === 0)
						$url = str_ireplace("https://api.pamfax.biz", "http://api.pamfax.biz", $url);
					return $this->_request($url,$postdata,$timeout,true);
				}
			}
		}
		return $this->_lastResult;
	}

	public static function StaticApi($url,$named_args = array(),$use_cache = false)
	{
		return self::Instance()->__callApi($url,$use_cache,$named_args);
	}

	public function CallApi($url,$named_args = array(),$use_cache = false)
	{
		return $this->__callApi($url,$use_cache,$named_args);
	}

	protected static function __staticApi($url,$use_cache = false)
	{
		return self::Instance()->__callApi($url,$use_cache);
	}

    protected function __callApi($url,$use_cache = false,$namedargs = false)
	{
		global $PAMFAX_API_URL;
		if(isset($_SESSION["api_last_request"]) && (strtolower(substr($url, -5)) == "/ping"))
		{
			// omit ping if there was another API call less then 2 minutes ago
			if($_SESSION["api_last_request"] > (time() - 120))
				return;
		}

		if($namedargs === false)
		{
			$trace = debug_backtrace(false);
			$x = 0;
			$callfunc = null;
			do
			{
				$callfunc = $trace[$x ++];
			}while( substr($callfunc['function'],0,2)=='__' );

			if( $callfunc['function'] == "CallApi" )
			{
				$namedargs = $callfunc['args'][1];
			}
			else
			{
				$ref = new ReflectionClass($callfunc['class']);
				$refmeth = $ref->getMethod($callfunc['function']);

				$namedargs = array();
				$params = $refmeth->getParameters();
				foreach( $params as &$p )
				{
					$i = $p->getPosition();
					$arg_val = isset($callfunc['args'][$i])?$callfunc['args'][$i]:$p->getDefaultValue();
					if( is_bool($arg_val) )
						$arg_val = $arg_val?"1":"0";
					$namedargs[$p->getName()] = $arg_val;
				}
			}
		}

		// prepare caching var
		$use_cache = $use_cache && !$GLOBALS['PAMFAX_API_CACHING_DISABLED'];
		if( $use_cache )
		{
			$cacheKey = $url.$GLOBALS['PAMFAX_API_MODE'];
			$keys = array_keys($namedargs);
			$cacheKey .= self::_r_implode('', $keys).self::_r_implode('', $namedargs);
			if( !isset($_SESSION['PAMFAX_API_CACHE']) )
				$_SESSION['PAMFAX_API_CACHE'] = array();
		}

		if( $use_cache
			&& isset($_SESSION['PAMFAX_API_CACHE'][$cacheKey])
			&& isset($_SESSION['PAMFAX_API_CACHE'][$cacheKey."_expires"])
			&& ($_SESSION['PAMFAX_API_CACHE'][$cacheKey."_expires"] > time() ) )
		{
			self::Log("[$PAMFAX_API_URL$url] Using cached result");
			$result = $_SESSION['PAMFAX_API_CACHE'][$cacheKey];
			self::$LastUrl = $PAMFAX_API_URL.$url;
			self::$LastData = http_build_query($namedargs);
		}
		else
		{
			self::Log("[$PAMFAX_API_URL$url] Requesting");
			$start = microtime(true);
			$result = $this->_request($url,$namedargs);
			if( self::$_measureTimes )
			{
				$duration = microtime(true)-$start;
				self::$TotalRequestingTime +=  $duration;
				self::Log("[$PAMFAX_API_URL$url] Request done in $duration");
			}
			else
				self::Log("[$PAMFAX_API_URL$url] Request done");

			if( (trim($result) != "") )
			{
				if( $use_cache && preg_match('/Cache-Control: max-age=(\d+)/', $this->_lastHeader, $m) )
				{
					if( $m[1] > 0 )
					{
						$_SESSION['PAMFAX_API_CACHE'][$cacheKey] = $result;
						$_SESSION['PAMFAX_API_CACHE'][$cacheKey."_expires"] = time()+$m[1];
						self::Log("[$PAMFAX_API_URL$url] Caching result for {$m[1]} seconds");
					}
				}
				if( preg_match('/X-Clear-Cache: api=([0-9a-zA-Z\*]*),method=(.*)/', $this->_lastHeader, $m) )
				{
					if( $m[1] == "*" )
					{
						self::Log("[$PAMFAX_API_URL$url] Cleared complete cache");
						$_SESSION['PAMFAX_API_CACHE'] = array();
					}
				}
			}
		}
		$_SESSION["api_last_request"] = time();		// save time of last request

		switch($GLOBALS['PAMFAX_API_MODE'])
		{
			case self::API_MODE_JSON:
				return $result;
				break;

			case self::API_MODE_PASSTHRU:
				// We need to pass all HTTP headers thru to the caller, but
				// we also need to skip the cookies, because this would destroy
				// the session on the callers side

				$header = str_replace("\r\n", "\n", $this->_lastHeader);
				$header = explode("\n",$header);
				foreach( $header as $h )
				{
					if(( strpos($h, "Content-") !== false )
							|| (strpos($h, "Expires: ") !== false)
							|| (strpos($h, "Pragma: ") !== false)
							|| (strpos($h, "Cache-Control: ") !== false)
							)
					{
						header($h);
					}
				}
				die($result);
				break;

			case self::API_MODE_RETURN:
				$header = str_replace("\r\n", "\n", $this->_lastHeader);
				$header = explode("\n",$header);
				$clean_header = array();
				foreach( $header as $h )
				{
					if( ( $val != '') && (( strpos($h, "Content-") !== false )
							|| (strpos($h, "Expires: ") !== false)
							|| (strpos($h, "Pragma: ") !== false)
							|| (strpos($h, "Cache-Control: ") !== false)
							))
					{
						$clean_header[] = $val;
					}
				}
				return array($clean_header,$result);
				break;

			case self::API_MODE_XML:
				return $result;
				break;

			case self::API_MODE_OBJECT:
				return self::ParseXmlResult($result);

			default:
				die("unknown PAMFAX_API_MODE: ".$GLOBALS['PAMFAX_API_MODE']);
				break;
		}
	}

	private static function _loadXml($xml_text)
	{
		if( !is_string($xml_text) )
			return $xml_text;

		try
		{
			$xml = @simplexml_load_string($xml_text);
			if($xml === false)
			{
				// retry to get error information:
				libxml_use_internal_errors(true);
				$xml = @simplexml_load_string($xml_text);
				$errtxt = var_export(libxml_get_errors(), true);
				trigger_error("XML parsing error: ".$errtxt."\nXML = $xml_text");

				$xml = new ApiError();
				$xml->Code = ErrorCode::HTML_500;
				$xml->Message = ErrorCode::ToText(ErrorCode::HTML_500);
				$xml->AdditionalInfo = $errtxt;
			}
		}
		catch(Exception $ex)
		{
			trigger_error("XML parsing error: ".$ex->getMessage()."\nxml:".$xml_text);
			$xml = new ApiError();
			$xml->Code = ErrorCode::HTML_500;
			$xml->Message = ErrorCode::ToText(ErrorCode::HTML_500);
			$xml->AdditionalInfo = $ex->getMessage()."\n".$ex->getTraceAsString();
		}
		return $xml;
	}

	public static function ParseXmlResult($xml_data)
	{
		$i = self::Instance();
		$xml = self::_loadXml($xml_data);
		if( $xml instanceof ApiError )
			return $xml;

		$start =  microtime(true);
		self::Log("[".self::$LastUrl."] Parsing result");
		$data = $i->_parse($xml);
		if( $data === false || !isset($data->result) )
        {
            trigger_error("Invalid API Response: ".$i->_lastResult." ($xml_data) token: ".$GLOBALS['PAMFAX_API_USERTOKEN']."\r\n\r\n".var_export($i->_lastHeader, true));
			$data->result = new ApiError("Invalid API Response: ".$i->_lastResult);
        }
		else
		{
			if( $data->result->code == "success")		// don't return the result data when everything went ok
				unset($data->result);
			else
				$data->result = new ApiError($data->result);
		}
		$res = get_object_vars($data);

		if( self::$_measureTimes )
		{
			$duration = microtime(true)-$start;
			self::$TotalParsingTime += $duration;
		}
		if( count($res) == 1 && isset($res['result']) && $res['result'] instanceof ApiError )
		{
			self::Log("[".self::$LastUrl."] Parsing done with ApiError".(isset($duration)?" in $duration":"").": ".var_export($res,true)."\n".self::$LastData);
			return $res['result'];
		}
		self::Log("[".self::$LastUrl."] Parsing done".(isset($duration)?" in $duration":""));
		return $res;
	}

	private function _parse($xml_data, $has_parent=false)
	{
		if( !is_object($xml_data) )
			return (string)$xml_data;

		$tag = (string)$xml_data->getName();
		$attr = $xml_data->attributes();
		$o = false;
		$is_api_list = false;

		// first check the type attribute if given
		if( isset($attr['type']) )
		{
			$type = (string)$attr['type'];
			switch( strtolower($type) )
			{
				case 'list':
					$is_api_list = true;  // for ApiList first use an array and convert it into list later
				case 'array':
					$o = array();
					$isao = true;
					unset($attr['type']);
					break;
				default:
					if( $GLOBALS['PAMFAX_NO_TYPED_OBJECTS'] === false && $this->_class_exists($type) )
						try{ $o = new $type(); $isao = false; }catch(Exception $ex){ $o = false; }
					break;
			}
		}

		if( $GLOBALS['PAMFAX_NO_TYPED_OBJECTS'] === false && $has_parent && $o === false && $this->_class_exists($tag))
		{
			try{ $o = new $tag(); $isao = false; }catch(Exception $ex){ $o = false; }
		}

		if( $o === false )
		{
			$o = new StdClass();
			$isao = false;
		}

		foreach( $attr as $name=>$value )
		{
			$value = $this->_unatt($value);
			if( $isao )
			{
				if( !$is_api_list )
					$o[$name] = (string)$value;
			}
			else
				$o->{$name} = (string)$value;
		}

		$children = $xml_data->children();
		if( count($children) > 0 )
		{
			$propname_cache = array();
			foreach( $children as $c )
			{
				$sub = $this->_parse($c,true);
				$propName = is_object($sub)?get_class($sub):'stdClass';

				if( $propName == "stdClass" )
				{
					$propName = $c->getName();
					if( is_object($sub) && !isset($sub->type) )
						$sub->type = $propName;
				}
				elseif( $sub instanceof ApiList && isset($sub->type) )
					$propName = $sub->type;

				if( isset($propname_cache[$propName]) )
				{
					$propname_cache[$propName]++;
					$propName = "$propName".$propname_cache[$propName];
				}
				else
					$propname_cache[$propName] = 0;

				if( $isao )
					$o[$propName] = $sub;
				else
					$o->{$propName}	= $sub;
			}
		}
		elseif( !$isao && count($attr) == 0 )
			return $this->_uncdata((string)$xml_data);

		if( $is_api_list )
		{
			$o = ApiList::FromArray($o);
			foreach( $attr as $name=>$value )
			{
				$value = $this->_unatt($value);
				$o->$name = (string)$value;
			}
			$o->type = $tag;
		}

		if( is_object($o) && get_class($o)!='stdClass' && method_exists($o, '__wakeup') )
			$o->__wakeup();

		return $o;
	}

	/**
	 * Un-Escapes the value of a XML attribute.
	 * @see http://www.w3.org/TR/REC-xml/#dt-escape
	 */
	protected function _unatt($attr)
	{
		$res = str_replace($this->_unatt_find, $this->_unatt_replace,$attr);

    /* mantis # 9423: missing user function, reproduce */
		$tags_to_strip = Array("script","img", "iframe"); //tags to strip
		foreach ($tags_to_strip as $tag){
	    		$res = preg_replace("/<\/?" . $tag . "(.|\s)*?>/","",$res);
		}
		return $res;
    /* end # 9423 */
    /* old code : return strip_only($res, array('script','img','iframe')); */
	}

	/**
	 * removes CDATA 'escaping' from a string
	 */
	protected function _uncdata($content)
	{
		return preg_replace('|\<\!\[CDATA\[(.*)\]\]\>|', '$1', $content);
	}

	/**
	 * Clear the ApiClient cache, i.e. after profile settings change
	 */
	static function ClearCache()
	{
		unset($_SESSION['PAMFAX_API_CACHE']);
	}

	/**
	 * Recursive implode.
	 * Will implode the given pieces into a string and handle
	 * multidimentional arrays too.
	 * @param string $glue String to be used as 'connector'
	 * @param array $pieces The pieces to be joined
	 * @return string Resulting string
	 */
	private static function _r_implode($glue,$pieces)
	{
		foreach( $pieces as $index=>&$item )
			if( is_array($item) )
				$pieces[$index] = self::_r_implode($glue,$item);

		return implode($glue,$pieces);
	}

	/**
	 * Will check if a class exists.
	 *
	 * Note:
	 * We are using an internal case here because class_exists performs auto-loading on each call.
	 * When calling in a recusion this is not needed always but the first time, so we're buffering.
	 * @param type $name
	 */
	private static function _class_exists($name)
	{
		if( !isset(self::$_class_exists_cache[$name]) )
			self::$_class_exists_cache[$name] = class_exists($name);
		return self::$_class_exists_cache[$name];
	}
}

