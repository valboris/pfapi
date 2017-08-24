<?php
/**
* API to shorten urls
* 
* Sample: https://portal.pamfax.biz/Login/Register/?ref=123456789123456789 -> http://pamfax.biz/goXhjz1 and vice versa
*/
class UrlShortenerApi extends ApiClient
{
	/**
	* Shorten an url
	* 
	* Shorten urls like from https://portal.pamfax.biz/Login/Register/?ref=123456789123456789 -> https://go.pamfax.biz/Xhjz1dF
	* @param <string> $longurl the long url that needs to be shortened
	*/
	public static function ShortenUrl($longurl)
	{
		return self::StaticApi('UrlShortener/ShortenUrl',array('longurl' => $longurl),true);
	}

	/**
	* Returns back the full long url for a given short url
	* 
	* Sample: https://go.pamfax.biz/Xhjz1dF -> https://portal.pamfax.biz/Login/Register/?ref=123456789123456789
	* Short url needs to be created with UrlShortener/ShortenUrl first and valid_until must be in future or NULL
	* @param <string> $shorturl The short url (case sensitive!)
	*/
	public static function GetLongUrl($shorturl)
	{
		return self::StaticApi('UrlShortener/GetLongUrl',array('shorturl' => $shorturl),true);
	}
}
