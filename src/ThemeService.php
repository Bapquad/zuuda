<?php
namespace Zuuda; 

define( 'THEME_ACTIVE', 'active' );
define( 'THEME_INACTIVE', 'inactive' );

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
			$data = $model->where('status', 1)->last(); 
			if( $data[$modelName]['status'] != THEME_INACTIVE ) 
				Config::Set( 'Theme', $data[$modelName]['install_dir'] );
			return true;
		}
		return false;
	}
	
	private static function __load( $service ) 
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
				$model = new ServiceModel(); 
				$prefix = $program->name[ 'prefix' ];
				$model_name = (string) $program->name[ 'model' ];
				$alias_name = preg_replace( '/[\-\_\s]/', '_', $program->name[ 'alias' ] ); 
				$table_name = explode( '_', $alias_name );
				foreach( $table_name as $key => $value ) 
					$table_name[ $key ] = getSingleton( 'Inflect' )->pluralize( $value ); 
				$table_name = implode( '_', $table_name ); 
				self::__task( $model->setPrefix($prefix)->setAliasName($alias_name)->setModelName($model_name)->setTableName($table_name)->initialize(), $model_name );
				break;
			}
		}

		return true;
	}
	
	private static function __install( Model $model, $theme_dir ) 
	{
		$lt = $model->getLastedData();
		list( $a, $data ) = each( $lt );
		if( $data[ $model->getModel() ][ 'install_dir' ] == $theme_dir ) 
		{
			if( $data[ $model->getModel() ][ 'status' ] == 'inactive' ) 
				$model->setId( (int) $data[ $model->getModel() ][ 'id' ] )->setData( 'status', 'active' )->save();
			return true;
		}
		
		$themes = ThemeClient::load();
		foreach( $themes as $key => $theme ) 
		{
			foreach( $theme as $i => $data ) 
				if( $data[ 'install_dir' ] == $theme_dir ) 
					break;
		}
		$model->setData( $data )->setData( 'status', 'active' )->save();
		return true;
	}
	
	private static function __reset( Model $model ) 
	{
		if( $id = $model->getMaxId('id') ) 
			$model->setId( $id )->setData( 'status', 'inactive' )->save();
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