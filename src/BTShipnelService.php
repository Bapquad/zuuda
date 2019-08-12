<?php
namespace Zuuda;

class BTShipnelService implements iTaskService, iBTShipnelService 
{
	
	public static function GetInstance() { return self::_getInstance(); }
	public static function BootService() { return self::_bootService(); }
	public static function Task( Model $model ) { return sefl::_task( $model ); }
	
	private static function _applyConfigs() 
	{
		if( Config::has( 'COM' ) ) 
		{
			return array
			(
				VENDOR_DIR, 
				'Zuuda\ServiceBooter', 
				'.xml', 
			);
		}
		return false;
	}
	
	private function __construct() {}
	private function __clone() {}
	private static function _getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new BTShipnelService();
		} 
		return $_instance;
	}
	
	private static function _load( $service ) 
	{
		if( !call( cFile::get(), $service )->exist() ) 
			return false; 
		
		$handle = simplexml_load_file( $service );
		
		foreach( $handle as $key => $program ) 
		{
			$name = $program->name;
			if( $name == __CLASS__ ) 
			{
				self::_task( $program );
				break;
			}
		}
	}
	
	private static function _task( $program ) 
	{
		if( $program->name[ 'ship' ] != SHIPNEL )
		{
			call( 'finish' );
		}
		else 
		{
			Config::set( 'SHIP', SHIPNEL );
			$url = getSingleton( 'Global' )->get( 'url' );
		}
		
		if( self::_withUrl( $program->name[ 'route' ].DOT.$program->name[ 'class' ], $url ) )
		{
			RequestHeader::Download( 'shipnel.info' );
			echo 'info: "BTShipnelService"' . NL;
			echo 'ship code: "' . SHIPNEL . '"' . NL;
			echo 'orgin: "'. ORIGIN_DOMAIN . '"' . NL;
			echo 'remote ip: "' . $_SERVER[ 'REMOTE_ADDR' ] . '"' . NL;
			call( 'finish' );
		}
	}
	
	private static function _withUrl( $ship, $url ) 
	{
		return $ship === $url;
	}
	
	private static function _bootService() 
	{
		$service = self::_applyConfigs(); 
		if( $service ) 
			return self::_load(_correctPath(_dispatch_service_file($service))); 
		return $service;
	}
	
}



/** Setup the shipnel for all the service */
define( 'SHIPNEL', '097370591949c9e67f2d54e4403fe1b7' );