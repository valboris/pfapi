<?php
// do everything from sample 1
require_once('sample_001.php');

// create a new fax and give the users IP, UserAgent and an origin
FaxJobApi::Create($_SERVER['REMOTE_ADDR'],$_SERVER['HTTP_USER_AGENT'],'API Samples');
// add a recipient to the fax
FaxJobApi::AddRecipient('+15752087007','Optional recipient name');
// set the cover to template 1 and the text to some value
FaxJobApi::SetCover(1,'This is my test fax using the PamFax API');

// wait for the API to prepare the fax
do
{
	sleep(5);
	$test = FaxJobApi::GetFaxState();
	if( ($test instanceof ApiError) // explicit error 
		 || !isset($test['FaxContainer']) ) // implicit error 
		die("Error preparing the fax");
}while( $test['FaxContainer']->state != "ready_to_send" );

// finally send it
FaxJobApi::Send();

