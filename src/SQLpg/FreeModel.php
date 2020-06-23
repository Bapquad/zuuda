<?php

namespace Zuuda\SQLpg; 

class FreeModel extends Model
{
	
	private static $this = '\Zuuda\SQLpg\FreeModel';
	final private function __clone() {} 
	final private function __construct() {} 
	final static public function Instance() { return call_user_func_array(array(self::$this, '__instance'), array()); } 
	final public function Close() { return call_user_func_array(array(self::$this, '__close'), array()); } 
		
	final static public function __instance() 
	{
		static $_instance; 
		return $_instance ?: ($_instance = new FreeModel); 
	} 
	
	final static protected function __close() 
	{
		global $_CONFIG;
		if( isset($_CONFIG['DATASOURCE']) ) 
		{ 
			$ds = $_CONFIG['DATASOURCE']; 
			foreach($ds['server'] as $svr) 
			{ 
				if( is_array($svr) && 
					isset($svr['resource']) &&
					is_object($svr['resource']) )
				{
					if( "pgsql"===$svr['driver'] ) 
					{
						pg_close( $svr['resource'] );
					}
				}
			} 
		}
	}
	
}