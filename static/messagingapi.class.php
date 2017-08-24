<?php
/**
* Semi-intelligent message handling.
* 
* Will be called by Skype handlers when users try to chat/call PamFax.
* In that case handler asks API what to do.
*/
class MessagingApi extends ApiClient
{
	/**
	* Called when user tried to chat with PamFax.
	* 
	* Creates a response in users language.
	*/
	public static function ReportIncomingChat($recipient = false, $chatmsg_id = false, $sender, $body, $sender_language = false)
	{
		return self::StaticApi('Messaging/ReportIncomingChat',array('recipient' => $recipient, 'chatmsg_id' => $chatmsg_id, 'sender' => $sender, 'body' => $body, 'sender_language' => $sender_language),false);
	}

	/**
	* Called when user tried to call PamFax.
	* 
	* Creates a chatmessage in users language.
	*/
	public static function ReportIncomingCall($recipient = false, $caller, $caller_language = false)
	{
		return self::StaticApi('Messaging/ReportIncomingCall',array('recipient' => $recipient, 'caller' => $caller, 'caller_language' => $caller_language),false);
	}
}
