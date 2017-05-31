<?php
namespace Zuuda;

class RouteService implements iTaskService, iRouteService 
{
	
	public static function GetInstance() { return self::_getInstance(); }
	public static function BootService( Application $app = NULL ) { return self::_bootService( $app ); }
	public static function Task( Model $model ) { return self::_task( $model ); }
	
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
			$_instance = new RouteService;
		}
		return $_instance;
	}
	
	private static function _routing( Model $model, $prefix, $url ) 
	{
		$model->setHasOne( array( 'com_layout' => $prefix.'com_layout' ) );
		$routes = $model->showHasOne()->query();
		foreach( $routes as $route ) 
		{
			if( $route[ 'com_route' ][ 'url' ] == $url ) 
			{
				return $route[ 'com_layout' ][ 'name' ] . PS . $route[ 'com_route' ][ 'id' ];
			}
		}
		return false;
	}
	
	private static function _load( Application $app, $service ) 
	{
		if( !call( cFile::get(), $service )->exist() )  
		{
			return false;
		}
		
		$url = getSingleton( 'Global' )->get( 'url' );
		$handle = simplexml_load_file( $service );
		
		foreach( $handle as $key => $program ) 
		{
			$name = $program->name;
			if( $name == __CLASS__ ) 
			{
				$model = new Model();
				$prefix = $program->name[ 'prefix' ];
				$model_name = str_replace( ' ', '_', strtolower( $program->name[ 'model' ] ) );
				$table_name = getSingleton( 'Inflect' )->pluralize( strtolower( str_replace( ' ', '_', $program->name[ 'model' ] ) ) );
				if( Config::get( 'COM' ) && !$app::hasUrl() ) 
				{
					if( $path = self::_routing( $model->setModelName( $model_name )->setPrefix( $prefix)->setTableName( $table_name ), $prefix, $url ) ) 
					{
						$app->setUrl( $path );
					}
				}
			}
		}
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