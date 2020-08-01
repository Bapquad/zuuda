<?php 

namespace Zuuda; 

class RouteController extends Controller
{

	private static $this = '\Zuuda\RouteController';
	private function __construct() 
	{
		parent::service();
	} 
	final static public function Instance() { return call_user_func_array(array(self::$this, '__instance'), array()); } 
	
	final static protected function __instance() 
	{
		static $_inst; 
		if( NULL===$_inst ) 
		{
			$_inst = new RouteController; 
			$_view = $_inst->__getView(); 
			$_view->loadLayout(); 
		}
		return $_inst; 
	} 

}