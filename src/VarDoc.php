<?php

namespace Zuuda;

use Zuuda\Fx;
use Zuuda\Error;

class VarDoc 
{ 
	
	private static $this = '\Zuuda\VarDoc';
	public function rootName() { return __CLASS__; }
	private $_input;
	private $_data = []; 
	private $_otag = '{{';
	private $_ctag = '}}';
	final private function __construct() {} 
	final private function __clone() {}
	final static public function Template() { return call_user_func_array(array(self::$this, '__instance'), func_get_args()); } 
	final public function TagOpen() { return call_user_func_array(array($this, '__tagOpen'), func_get_args()); } 
	final public function TagClose() { return call_user_func_array(array($this, '__tagClose'), func_get_args()); } 
	final public function Tags() { return call_user_func_array(array($this, '__tags'), func_get_args()); }
	final public function Assign() { return call_user_func_array(array($this, '__assign'), array(func_get_args())); } 
	final public function Build() { return call_user_func_array(array($this, '__build'), array()); }
	final public function Store() { return call_user_func_array(array($this, '__store'), func_get_args()); } 
	final public function Touch() { return call_user_func_array(array($this, '__touch'), func_get_args()); } 
	
	final static private function __instance( $tpl ) 
	{ 
		$inst = new VarDoc; 
		if( fx::file_exists($tpl) ) 
		{ 
			$inst->_input = fx::file_get_contents($tpl); 
		} 
		else 
		{ 
			error::trigger("The file <b>".$tpl."</b> does not exit"); 
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
	
	final private function __tags( $otag, $ctag=NULL ) 
	{ 
		$this->_otag = $otag; 
		if( NULL!==$ctag ) 
			$this->_ctag = $ctag; 
		else
			$this->_ctag = $otag; 
		return $this; 
	} 
	
	final private function __tagClose( $tag ) 
	{ 
		$this->_ctag = $tag;
		return $this;
	}  
	
	final private function __tagOpen( $tag ) 
	{ 
		$this->_otag = $tag;
		return $this;
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
		$data = $this->_data;
		foreach($data as $key => $item) 
		{ 
			list($k, $v) = make_item(array($key=>$item), $this->_otag, $this->_ctag); 
			$data[$k]=$v;
		} 
		return preg_replace_callback( "#(".$this->_otag."[a-zA-Z0-9\_]+".$this->_ctag.")#", 
			function( $m ) use( $data ) 
			{
				if( isset($data[$m[0]]) ) 
					$replacement = $data[$m[0]];
				else 
					$replacement = $m[0];
				return $replacement;
			}, 
			$this->_input
		); 
	} 
	
} 

#------------------------------------------------
	#Send to: %user_mail%
	#--------------------------------------------
	#Hi, %user_name%
	#I send you a message.