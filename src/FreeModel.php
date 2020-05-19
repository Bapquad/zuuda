<?php

namespace Zuuda;

class FreeModel extends Model
{
	
	private static $this = '\Zuuda\FreeModel';
	final private function __clone() {} 
	final private function __construct() {} 
	final static public function Instance() { return call_user_func_array(array(self::$this, '__instance'), array()); } 
		
	final static public function __instance() 
	{
		static $_instance; 
		return $_instance ?: ($_instance = new FreeModel); 
	} 
	
}