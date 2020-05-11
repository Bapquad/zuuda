<?php
namespace Zuuda;

class LocateService implements iLocateService 
{
	
	public static function GetInstance() { return self::__getInstance(); }
	public static function BootService( Application $app = NULL ) { return self::__bootService( $app ); }
	
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
	private static function __getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) )
		{
			$_instance = new LocateService;
		}
		return $_instance;
	}
	
	private static function __load( Application $app, $service ) 
	{
		if( !call( cFile::get(), $service )->exist() ) 
			return false; 
		
		$locate = Config::get('LOCATE');
		if( NULL!==$locate ) 
		{
			$locate_file = __correctPath($locate[$locate['default']]);
			
			if( Config::has('COM') ) 
			{
				$theme = Config::get('Theme');
				$theme_locate_path = WEB_DIR . $theme . $locate_file;
				if( call(cFile::get(), $theme_locate_path)->exist() ) 
				{
					Config::Trans( include_once( $theme_locate_path ) ); 
				} 
				else unset( $theme_locate_path );
			}
			
			if(!isset($theme_locate_path)) 
			{
				$locate_path = WEB_DIR . $locate_file;
				if( call(cFile::get(), $locate_path)->exist() ) 
				{
					Config::Trans( include_once( $locate_path ) );
				} 
				else unset( $locate_path );
			}
			
			if(!isset($theme_locate_path) && !isset($locate_path)) 
				Config::die('LOCATE');
		}
		
		return false;
	}
	
	private static function __bootService( Application $app = NULL ) 
	{
		$service = self::__applyConfigs(); 
		if( $service ) 
			return self::__load($app, __correctPath(__dispatch_service_file($service))); 
		return false; 
	}
	
}