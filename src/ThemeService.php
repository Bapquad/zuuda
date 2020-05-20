<?php
namespace Zuuda; 

use Zuuda\Auth;
use Zuuda\cFile;
use Zuuda\Cache;
use Zuuda\ServiceModel;
use Zuuda\Config;

class ThemeService implements iTaskService, iThemeService 
{
	
	public static function GetInstance() { return self::__getInstance(); }
	public static function BootService() { return self::__bootService(); }
	public static function Task( Model $model ) { return self::__task( $model ); }
	public static function ResetDefault( Model $model ) { return self::__reset( $model ); }
	public static function Reset( Model $model ) { return self::__reset( $model ); } 
	public static function Install( Model $model, $theme_dir ) { return self::__install( $model, $theme_dir ); } 
	public static function Load() { return self::__load(); }
	
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
			$_instance = new ThemeService;
		}
		return $_instance;
	}
	
	private static function __task( ServiceModel $model, $modelName ) 
	{
		if( Config::has( 'COM' ) ) 
		{
			$data = $model->where( 'key', 'theme_install_dir' )->item(':first'); 
			if( !empty($data) ) 
			{
				if( NULL!==$data['value'] || EMPTY_CHAR!==$data['value'] ) 
				{
					config::set( 'Theme', $data['value'] ); 
					config::set( 'ThemePath', PS.substr($data['value'], strlen($data['value'])-1, 1) );
				}
			}
			return true;
		}
		return false;
	}
	
	private static function __load( $service ) 
	{
		global $configs;
		if( !call( cFile::get(), $service )->exist() )  
		{
			return false;
		}
		
		$handle = simplexml_load_file( $service );
		foreach( $handle as $key => $program ) 
		{
			$name = $program->name;
			if( $name == __CLASS__ ) 
			{
				$prefix = $configs['DATASOURCE'][$configs['DATASOURCE']['server']['default']]['prefix'];;
				$modelName = $program->name['model']->__toString();
				$aliasName = $program->name['alias']->__toString(); 
				$tableName = $program->name['table']->__toString(); 
				self::__task( servicemodel::instance() 
					->setPrefix($prefix)
					->setAliasName($aliasName)
					->setModelName($modelName)
					->setTableName($tableName)
					->start(), 
					$modelName );
				break;
			}
		}

		return true;
	}
	
	private static function __install( Model $model, $theme_dir ) 
	{
		$model->equal( 'key', 'theme_install_dir' )->save(array(
			'value' => $theme_dir
		)); 
		
		if( !$model->affected() ) 
		{ 
			$model->save(array(
				'user_id' => 1, 
				'key' => 'theme_install_dir', 
				'value' => $theme_dir, 
			)); 
		} 
		
		Cache::clear('template');
		return true;
	}
	
	private static function __reset( Model $model ) 
	{
		$data = $model->equal( 'key', 'theme_install_dir' )->item(':first'); 
		
		if( !empty($data) ) 
		{
			$model->equal( 'key', 'theme_install_dir' )->delete(); 
			Cache::clear('template');
		}
		return true;
	}
	
	private static function __bootService() 
	{
		$service = self::__applyConfigs();
		if( $service ) 
			return self::__load(__correctPath(__dispatch_service_file($service))); 
		return false;
	}
	
}