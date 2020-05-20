<?php

namespace Zuuda;

class Text 
{
	private $_data;
	
	private static $this = "Zuuda\Text"; 
	final public function rootName() { return __CLASS__; } 
	final public function __construct( $data=NULL ) { $this->_data = $data; }
	final public function __toString() { return $this->_data; }
	final public function StripSlashesDeep() { return call_user_func_array(array($this, '__stripSlashesDeep'), array()); } 
	final public function UrlDecode() { return call_user_func_array(array($this, '__urlDecode'), func_get_args()); } 
	final public function JsonDecode() { return call_user_func_array(array($this, '__jsonDecode'), func_get_args()); }
	final static function Instance() { return call_user_func_array(array(self::$this, '__instance'), func_get_args()); } 
	
	final static private function __instance( $data=NULL ) 
	{ 
		static $inst;
		if( NULL===$data ) 
		{
			if( NULL===$inst ) 
			{
				$inst = new Text;
			} 
			return $inst; 
		} 
		return new Text( $data ); 
	} 

	final private function __stripSlashesDeep() 
	{
		return stripSlashesDeep($this->_data);
	} 
	
	final private function __urlDecode( $in=NULL ) 
	{ 
		$out = array(); 
		$data = $in ?: $this->_data;  
		$items = explode("&", trim($data));
		foreach($items as $item) 
		{ 
			$rs = preg_match("#^([^=]*)[=]([^=]*)#", $item, $ms);
			if($rs) 
			{
				$out[urldecode($ms[1])] = urldecode($ms[2]);
			}
		} 
		return $out;
	} 
	
	final private function __jsonDecode( $in=NULL ) 
	{ 
		$out = array(); 
		$data = $in ?: $this->_data; 
		$out = json_decode($data, true); 
		if(empty($out) && strlen($data)) 
		{ 
			$out = $this->__urlDecode($data); 
		} 
		return $out;
	} 
	
	final private function __addQuote( $in=NULL ) 
	{ 
		$out = array(); 
		$data = $in ?: $this->_data; 
		$out = quote.$data.quote;
		return $out;
	} 
}