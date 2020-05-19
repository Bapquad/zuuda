<?php

namespace Zuuda;

use Zuuda\Comsite;

class ComsiteAction 
{ 
	
	private static $this = '\Zuuda\ComsiteAction'; 
	private static $inst;
	final private function __clone() {} 
	final private function __construct() {} 
	final static function Instance() { return call_user_func_array(array(self::$this, '__instance'), array()); } 
	final static function Connect() { return call_user_func_array(array(self::$this, '__connect'), array(func_get_args())); } 
	
	static private function __instance() 
	{ 
		static $_inst; 
		return $_inst ?: $_inst = new ComsiteAction; 
	} 
	
	static private function __connect( $args ) 
	{ 
		return call_user_func_array( array(comsite::$this, 'SocketIO'), $args );
	} 
	
} 