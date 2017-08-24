<?php
	require_once(dirname(__FILE__)."/lib/apiclient.class.php");
	require_once(dirname(__FILE__)."/lib/apierror.class.php");
	require_once(dirname(__FILE__)."/lib/apilist.class.php");
	require_once(dirname(__FILE__)."/lib/errorcode.class.php");

	function pamfax_use_static(){ foreach(glob(dirname(__FILE__)."/static/*") as $f) require_once($f); }
	function pamfax_use_instance(){ foreach(glob(dirname(__FILE__)."/instance/*") as $f) require_once($f); }
	