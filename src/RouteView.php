<?php 

namespace Zuuda; 

class RouteView extends View 
{

	private static $this = '\Zuuda\RouteView';
	private function __construct() {} 
	final static public function Instance() { return call_user_func_array(array(self::$this, '__instance'), array()); } 
	
	final static protected function __instance() 
	{
		static $_inst; 
		return $_inst ?: new RouteView; 
	} 
	
	final public function Render( $template=NULL, $args=NULL ) 
	{
		$this->__setTemplate($template);
		$this->__renderLayout( $args );
	}

}