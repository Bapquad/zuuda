<?php

namespace Zuuda;

use Zuuda\Config;

class Fx 
{
	
	private static $this = '\Zuuda\Fx';
	final public function rootName() { return __CLASS__; }
	private function __construct() {}
	private function __clone() {} 
	
	final static public function save_content($path, $data, $flag=0, $resource=NULL) 
	{ 
		return file_put_contents($path);
	}
	
	final static public function load_file($path, $flag=0, $resource=NULL, $offset=-1, $maxlen=-1) 
	{ 
		return file_get_contents($path);
	} 
	
	final static public function touch($path, $data) 
	{ 
		return self::touch_file($path, $data); 
	} 
	
	final static public function touch_file($path, $data, $flags=0, $resource=NULL) 
	{ 
		return file_put_contents($path, $data);
	} 
	
	final static public function correct_path($path) 
	{ 
		return __correctPath($path);
	} 
	
	final static public function mysql_query( $l, $q ) 
	{ 
		return mysqli_query( $l, $q ); 
	} 
	
	final static public function remove($path) 
	{ 
		if(!self::file_exists($path)) return false;
		return unlink($path); 
	} 
	
	final static public function hash( $string ) 
	{
		return hash(config::get('ENCRYPT')['request'], $string); 
	}
	
	final static public function __callStatic( $name, $args ) 
	{ 
		switch($name) 
		{ 
			case 'file_exists':
				return $name($args[0]);
			case 'load_xml':
				return call_user_func_array('simplexml_load_file', $args); 
			default:
				return call_user_func_array($name, $args);
				break;
		} 
	} 
	
	final static public function get_ipv4() 
	{ 
		return gethostbyname(exec('hostname')); 
	} 
	
	final static public function get_ipv4s() 
	{ 
		return gethostbynamel(exec('hostname')); 
	} 
	
}