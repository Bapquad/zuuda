<?php 

namespace Zuuda; 

class RouteView extends View 
{

	private static $this = '\Zuuda\RouteView';
	final static public function Instance() { return call_user_func_array(array(self::$this, '__instance'), array()); } 
	
	private function __construct() 
	{
		parent::__construct(); 
	} 
	
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