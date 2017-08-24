<?php
// include the PamFax API client and set your credentials
require_once(__DIR__ . '/../pamfax_api.php');
$GLOBALS['PAMFAX_API_URL']         = $CONFIG['pamfax']['api_uri'];
$GLOBALS['PAMFAX_API_APPLICATION'] = "<your API key here>";
$GLOBALS['PAMFAX_API_SECRET_WORD'] = "<your API secret word here>";
// tell the API client to create objects from returned XML automatically
$GLOBALS['PAMFAX_API_MODE']        = ApiClient::API_MODE_OBJECT;
// tell the API client to use static wrapper classes
pamfax_use_static();

// verify the PamFax user (this is the same as used on https://portal.pamfax.biz etc to login):
$result = SessionApi::VerifyUser('test_user','test_users_password');
if( ($result instanceof ApiError) // explicit error 
	|| !isset($result['UserToken']) || !isset($result['User']) ) // implicit error 
	die("Unable to login");

// set the global usertoken
$GLOBALS['PAMFAX_API_USERTOKEN'] = $result['UserToken']->token;
// optionally remember the user for later use
$currentUser = $result['User'];

