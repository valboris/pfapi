<?php
require_once('sample_001.php');

// create a new fax and give the users IP, UserAgent and an origin
FaxJobApi::Create($_SERVER['REMOTE_ADDR'],$_SERVER['HTTP_USER_AGENT'],'API Samples');
// add 3 recipients to the fax
FaxJobApi::AddRecipient('+15752087007','Recipient 1');
FaxJobApi::AddRecipient('+495822947714','Recipient 2');

// use the prefix '@' with the $full_path_to_local_file to tell api-client it shall upload a file
FaxJobApi::AddFile("@".$full_path_to_local_file);

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

