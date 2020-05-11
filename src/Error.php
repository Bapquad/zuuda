<?php

namespace Zuuda; 

class Error 
{ 

	private static $class = '\Zuuda\Error';
	final public function rootName() { return __CLASS__; }
	private function __construct() {}
	private function __clone() {} 
	public static function Instance() { return self::__getInstance(); }
	
	final static public function __callStatic( $name, $args ) 
	{ 
		switch($name) 
		{ 
			case 'position':
				$traces = $args[0]->getTrace(); 
				$position = '<ul class="error-lines">'; 
				foreach( $traces as $trace ) 
				{ 
					if(isset($trace['line'])) 
					{
						$position .= '<li>'.$trace['file'].":".$trace['line']."</li>"; 
					}
				} 
				return $position.'</ul>';
			default:
				break;
		} 
	} 
	
	private static function __getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new Error;
		}
		return $_instance;
	}
	
} 