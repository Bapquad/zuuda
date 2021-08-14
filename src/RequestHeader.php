<?php

namespace Zuuda;

class RequestHeader extends Header
{
	
	private static $this = '\Zuuda\RequestHeader';	

	final static public function GetHeaders() { return call_user_func_array(array(self::$this, '__getHeaders', array())); } 
	
	final static private function __getHeaders() 
	{
		$headers = apache_request_headers(); 
		return $headers;
	}
	
}