<?php
namespace Zuuda;

use Zuuda\ServiceModel; 

class RouteService implements iTaskService, iRouteService 
{
	
	public static function GetInstance() { return self::__getInstance(); }
	public static function BootService( Application $app = NULL ) { return self::__bootService( $app ); }
	public static function Task( Model $model ) { return self::__task( $model ); }
	
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
			$_instance = new RouteService;
		}
		return $_instance;
	}
	
	private static function __routing( ServiceModel $model, $prefix, $url ) 
	{
		$model->hasOne('com_layout', 'com_layout', 'id', 'com_layout_id');
		$routes = $model->displayHasOne()->search();
		foreach( $routes as $route ) 
			if( $route['com_route']['url'] == $url ) 
				return $route['com_layout']['name'] . PS . $route['com_route']['id']; 
		return false;
	}
	
	private static function __load( Application $app, $service ) 
	{
		global $configs;
		if( !call( cFile::get(), $service )->exist() ) 
			return false; 
		
		$url = singleton( 'Global' )->get( 'url' );
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
				if( Config::has('COM') && !$app::hasUrl() ) 
					if( $path = self::__routing( servicemodel::instance()
						->setPrefix($prefix)
						->setAliasName($aliasName)
						->setModelName($modelName)
						->setTableName($tableName)
						->start(), 
						$prefix, 
						$url) )  
					{
						$app->setUrl( $path ); 
					}
			}
		}
	}
	
	private static function __bootService( Application $app = NULL ) 
	{
		$service = self::__applyConfigs();
		if( $service ) 
			return self::__load($app, __correctPath(__dispatch_service_file($service)));
		return false;
	}
	
}