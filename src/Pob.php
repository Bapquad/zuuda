<?php

namespace Zuuda;

use Zuuda\Fx;
use Zuuda\Error;

class Pob 
{ 

	private static $this = '\Zuuda\Pob';
	public function rootName() { return __CLASS__; } 
	private $_script;
	private $_output;
	private $_data = []; 
	final private function __construct() {} 
	final private function __clone() {} 
	final static public function Include() { return call_user_func_array(array(self::$this, '__instance'), func_get_args()); } 
	final public function Assign() { return call_user_func_array(array($this, '__assign'), array(func_get_args())); } 
	final public function Build() { return call_user_func_array(array($this, '__build'), array()); } 
	final public function Store() { return call_user_func_array(array($this, '__store'), func_get_args()); } 
	final public function Touch() { return call_user_func_array(array($this, '__touch'), func_get_args()); } 
	
	final static private function __instance( $script ) 
	{ 
		$inst = new Pob; 
		if( fx::file_exists($script) ) 
		{ 
			$inst->_script = $script; 
		} 
		else 
		{ 
			error::trigger("The file <b>".$script."</b> does not exit."); 
		} 
		return $inst; 
	} 
	
	final private function __touch() 
	{ 
		return call_user_func_array(array($this, '__store'), func_get_args()); 
	} 
	
	final private function __store( $targ ) 
	{ 
		return fx::file_put_contents($targ, $this->__build());
	} 
	
	final private function __assign( $args ) 
	{ 
		if( 1===count($args) ) 
		{
			$this->_data = array_merge($this->_data, current($args)); 
		} 
		else if ( 2===count($args) ) 
		{
			$this->_data[$args[0]] = $args[1]; 
		} 
		return $this; 
	} 
	
	final private function __build() 
	{ 
		extract($this->_data); 
		// Turn on output buffer.
		fx::ob_start();	
		include($this->_script);
		// Get the current buffered contents, and clean the output buffer.
		$this->_output = fx::ob_get_contents(); 
		fx::ob_end_clean();
		return $this->_output;
	} 

} 
