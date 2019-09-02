<?php
namespace Zuuda;

class BTShipnelService implements iTaskService, iBTShipnelService 
{
	
	public static function GetInstance() { return self::__getInstance(); }
	public static function BootService() { return self::__bootService(); }
	public static function Task( Model $model ) { return sefl::__task( $model ); }
	
	private static function __applyConfigs() 
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
	private static function __getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new BTShipnelService();
		} 
		return $_instance;
	}
	
	private static function __load( $service ) 
	{
		if( !call( cFile::get(), $service )->exist() ) 
			return false; 
		
		$handle = simplexml_load_file( $service );
		
		foreach( $handle as $key => $program ) 
		{
			$name = $program->name;
			if( $name == __CLASS__ ) 
			{
				self::__task( $program );
				break;
			}
		}
	}
	
	private static function __task( $program ) 
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
		
		if( self::__withUrl( $program->name[ 'route' ].DOT.$program->name[ 'class' ], $url ) )
		{
			RequestHeader::Download( 'shipnel.info' );
			echo 'info: "BTShipnelService"' . NL;
			echo 'ship code: "' . SHIPNEL . '"' . NL;
			echo 'orgin: "'. ORIGIN_DOMAIN . '"' . NL;
			echo 'remote ip: "' . $_SERVER[ 'REMOTE_ADDR' ] . '"' . NL;
			call( 'finish' );
		}
	}
	
	private static function __withUrl( $ship, $url ) 
	{
		return $ship === $url;
	}
	
	private static function __bootService() 
	{
		$service = self::__applyConfigs(); 
		if( $service ) 
			return self::__load(__correctPath(__dispatch_service_file($service))); 
		return $service;
	}
	
}



/** Setup the shipnel for all the service */
define( 'SHIPNEL', '097370591949c9e67f2d54e4403fe1b7' );