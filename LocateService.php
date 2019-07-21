<?php
namespace Zuuda;

class LocateService implements iLocateService 
{
	
	public static function GetInstance() { return self::_getInstance(); }
	public static function BootService( Application $app = NULL ) { return self::_bootService( $app ); }
	
	private static function _applyConfigs() 
	{
		if( Config::has( 'COM' ) ) 
		{
			return array
			(
				VENDOR . DS, 
				'Zuuda\ServiceBooter', 
				'.xml', 
			);
		}
		return false;
	}
	
	private function __construct() {}
	private static function _getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) )
		{
			$_instance = new LocateService;
		}
		return $_instance;
	}
	
	private static function _load( Application $app, $service ) 
	{
		if( !call( cFile::get(), $service )->exist() )  
		{
			return false;
		}
		
		$locate = Config::get( 'LOCATE' );
		if( NULL!==$locate ) 
		{
			$locate_file = _correctPath($locate[$locate['default']]);
			
			if( Config::has( 'COM' ) ) 
			{
				$theme = Config::get( 'themes' );
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
				Config::die( 'LOCATE' );
		}
		
		return false;
	}
	
	private static function _bootService( Application $app = NULL ) 
	{
		$service = self::_applyConfigs();
		if( $service ) 
		{
			return self::_load( $app, _correctPath( implode( '', $service ) ) );
		}
		return false;
	}
	
}