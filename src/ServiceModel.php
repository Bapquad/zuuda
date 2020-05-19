<?php

namespace Zuuda;

use Zuuda\Fx;

class ServiceModel extends Model
{
	
	private static $this = '\Zuuda\ServiceModel';
	final static public function Instance() { return call_user_func_array(array(self::$this, '__instance'), array()); } 
	final static public function Test() { return call_user_func_array(array(self::$this, '__test'), func_get_args()); } 
	final public function Start() { return call_user_func_array(array($this, '__start'), array()); } 
	final private function __clone() {} 
	final private function __construct() {} 

	final static public function __instance() 
	{
		return new ServiceModel; 
	} 
	
	final private function __start() 
	{
		parent::__construct();
		return $this; 
	} 
	
	final static public function __test( $link, $table ) 
	{ 
		if( fx::mysql_query($link, "select * from ".$table." limit 1") ) 
		{ 
			return true;
		} 
		return false;
	} 
	
}