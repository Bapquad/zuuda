<?php

namespace Zuuda;

use Zuuda\Error;

class Text 
{
	private $_data = empc;
	
	private static $this = "Zuuda\Text"; 
	final public function rootName() { return __CLASS__; } 
	final public function __construct( $data=NULL ) { $this->_data = $data; }
	final public function __toString() { return $this->_data; }
	final public function StripSlashesDeep() { return call_user_func_array(array($this, '__stripSlashesDeep'), array()); } 
	final public function UrlDecode() { return call_user_func_array(array($this, '__urlDecode'), func_get_args()); } 
	final public function UrlEncode() { return call_user_func_array(array($this, '__urlEncode'), func_get_args()); } 
	final public function RawUrlDecode() { return call_user_func_array(array($this, '__rawUrlDecode'), func_get_args()); } 
	final public function RawUrlEncode() { return call_user_func_array(array($this, '__rawUrlEncode'), func_get_args()); } 
	final public function JsonDecode() { return call_user_func_array(array($this, '__jsonDecode'), func_get_args()); }
	final public function JsonEncode() { return call_user_func_array(array($this, '__jsonEncode'), func_get_args()); }
	final public function BuildQuery() { return call_user_func_array(array($this, '__buildQuery'), array(func_get_args())); }
	final public function ParseUrl() { return call_user_func_array(array($this, '__parseUrl'), func_get_args()); } 
	final public function ParseStr() { return call_user_func_array(array($this, '__parseStr'), func_get_args()); } 
	final public function ParseIni() { return call_user_func_array(array($this, '__parseIni'), func_get_args()); } 
	final public function ParseIniFile() { return call_user_func_array(array($this, '__parseIniFile'), func_get_args()); } 
	final public function Base64EncodeFromBlob() { return call_user_func_array(array($this, '__base64EncodeFromBlob'), func_get_args()); } 
	final public function Base64EncodeFromFile() { return call_user_func_array(array($this, '__base64EncodeFromFile'), func_get_args()); } 
	final public function Base64Decode() { return call_user_func_array(array($this, '__base64Decode'), func_get_args()); } 
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
		$data = trim($in ?: $this->_data); 
		return urldecode($data); 
	} 
	
	final private function __urlEncode( $in=NULL ) 
	{ 
		$data = trim($in ?: $this->_data); 
		return urlencode($data); 
	} 
	
	final private function __rawUrlDecode( $in=NULL ) 
	{ 
		$data = trim($in ?: $this->_data); 
		return rawurldecode($data);
	} 
	
	final private function __rawUrlEncode( $in=NULL ) 
	{ 
		$data = trim($in ?: $this->_data); 
		return rawurlencode($data); 
	} 
	
	final private function __jsonDecode( $in=NULL ) 
	{ 
		$out = array(); 
		$data = $in ?: $this->_data; 
		$out = json_decode($data, true); 
		if(empty($out) && strlen($data)) 
		{ 
			$out = $this->__parseStr($data); 
		} 
		return $out;
	} 
	
	final private function __jsonEncode( $in=NULL ) 
	{ 
		if( !is_array($in) ) 
		{
			error::trigger("The function <code><b>Text::JsonEncode</b></code> need a parameter in Array type"); 
		}
		$this->_data = json_encode($in); 
		return $this;
	} 
	
	final private function __buildQuery( $args ) 
	{ 
		$args_nums = count($args); 
		if( 1===$args_nums ) 
		{ 
			$args = current($args);
			if( is_array($args) )
			{
				$this->_data = http_build_query($args); 
			} 
			else if( is_string($args) && is_numeric($args) ) 
			{ 
				$this->_data = $args; 
			} 
		} 
		else if( 2===$args_nums ) 
		{ 
			$prefix = $args[1]; 
			$args = current($args); 
			if( is_array($args) )
			{
				$this->_data = http_build_query($args, $prefix); 
			} 
			else if( is_string($args) && is_numeric($args) ) 
			{ 
				$this->_data = $prefix.$args; 
			} 
		} 
		return $this;
	} 
	
	final private function __parseUrl( $in=NULL ) 
	{ 
		$out = array(); 
		$data = trim($in ?: $this->_data); 
		$out = parse_url($data); 
		return $out;
	} 
	
	final private function __parseStr( $in=NULL ) 
	{
		$out = array(); 
		$data = trim($in ?: $this->_data); 
		parse_str( $data, $out ); 
		return $out;
	} 
	
	final private function __parseIni( $ini ) 
	{
		return parse_ini_string( $ini, true ); 
	}
	
	final private function __parseIniFile( $filepath ) 
	{ 
		$ini = file_get_contents($filepath); 
		return parse_ini_string( $ini, true ); 
	} 
	
	final private function __base64EncodeFromBlob( $blob ) 
	{
		$this->_data = base64_encode($blob); 
		return $this;
	} 
	
	final private function __base64EncodeFromFile( $filepath ) 
	{ 
		$blob = file_get_contents( $filepath ); 
		$this->_data = base64_encode( $blob ); 
		return $this;
	} 
	
	final private function __base64Decode( $in ) 
	{
		$data = $in ?: $this->_data; 
		return base64_decode( $data ); 
	}
	
	final private function __addQuote( $in=NULL ) 
	{ 
		$out = array(); 
		$data = $in ?: $this->_data; 
		$out = quote.$data.quote;
		return $out;
	} 
}