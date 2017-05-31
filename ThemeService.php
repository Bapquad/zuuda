<?php
namespace Zuuda; 

define( 'THEME_ACTIVE', 'active' );
define( 'THEME_INACTIVE', 'inactive' );

class ThemeService implements iTaskService, iThemeService 
{
	
	public static function GetInstance() { return self::_getInstance(); }
	public static function BootService() { return self::_bootService(); }
	public static function Task( Model $model ) { return self::_task( $model ); }
	public static function ResetDefault( Model $model ) { return self::_reset( $model ); }
	public static function Reset( Model $model ) { return self::_reset( $model ); } 
	public static function Install( Model $model, $theme_dir ) { return self::_install( $model, $theme_dir ); } 
	public static function Load() { return self::_load(); }
	
	private static function _applyConfigs() 
	{
		if( Config::get( 'COM' ) ) 
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
	private function __clone() {}
	private static function _getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new ThemeService;
		}
		return $_instance;
	}
	
	private static function _bootService() 
	{
		$service = self::_applyConfigs();
		if( $service ) 
		{
			return self::_load( _correctPath( implode( '', $service ) ) );
		}
		return false;
	}
	
	private static function _task( Model $model ) 
	{
		if( Config::get( 'COM' ) ) 
		{
			$data = $model->getLastedData();
			$data = $model->item( $data, 0 );
			if( $data[ 'status' ] != THEME_INACTIVE ) 
			{
				Config::Set( 'themes', $data[ 'install_dir' ] );
			}
			return true;
		}
		return false;
	}
	
	private static function _load( $service ) 
	{
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
				$model = new Model();
				$prefix = $program->name[ 'prefix' ];
				$model_name = str_replace( ' ', '', $program->name[ 'model' ] );
				$table_name = getSingleton( 'Inflect' )->pluralize( strtolower( str_replace( ' ', '_', $program->name[ 'model' ] ) ) );
				self::_task( $model->setModelName( $model_name )->setPrefix( $prefix)->setTableName( $table_name ) );
				break;
			}
		}
		
		return true;
	}
	
	private static function _install( Model $model, $theme_dir ) 
	{
		$lt = $model->getLastedData();
		list( $a, $data ) = each( $lt );
		if( $data[ $model->getModel() ][ 'install_dir' ] == $theme_dir ) 
		{
			if( $data[ $model->getModel() ][ 'status' ] == 'inactive' ) 
			{
				$model->setId( (int) $data[ $model->getModel() ][ 'id' ] )->setData( 'status', 'active' )->save();
			}
			return true;
		}
		
		$themes = ThemeClient::load();
		
		foreach( $themes as $key => $theme ) 
		{
			foreach( $theme as $i => $data ) 
			{
				if( $data[ 'install_dir' ] == $theme_dir ) 
				{
					break;
				}
			}
		}
		
		$model->setData( $data )->setData( 'status', 'active' )->save();
		
		return true;
	}
	
	private static function _reset( Model $model ) 
	{
		if( $id = $model->getMaxId( 'id' ) ) 
		{
			$model->setId( $id )->setData( 'status', 'inactive' )->save();
		}
		return true;
	}
	
}