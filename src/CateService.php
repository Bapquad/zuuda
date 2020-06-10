<?php

namespace Zuuda;

use Zuuda\ServiceModel;

class CateService implements  iTaskService, iCateService
{
	
	public static function GetInstance() { return self::__getInstance(); }
	public static function BootService( Application $app = NULL ) { return self::__bootService( $app ); }
	public static function Task( Model $model ) { return self::__routing( $model, singleton( 'Global' )->get( 'url' ) ); }
	public static function GetPath( $category, $item, $sp='/', $last=NULL ) { return self::__getPath( $category, $item, $sp, $last ); }
	public static function GetParent( $category, $item ) { return self::getParent( $category, $item ); }
	
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
			$_instance = new CateService;
		}
		return $_instance;
	}
	
	private static function __getParent( $category, $item ) 
	{
		if( !empty($category) ) 
			foreach( $category as $cate ) 
			{
				$cate = $cate['Category'];
				if( $item['parent_id'] == $cate['id'] ) 
					return $cate;
			}
	}
	
	private static function __getPath( $category, $item, $sp='/', $last=NULL ) 
	{
		if( NULL!==$item ) 
		{
			$id = (int) $item['id'];
			$parent_id = (int) $item['parent_id'];
			$path = $item['name'];
			
			if( !is_null($last) ) 
				$path = $path . $sp . $last; 
			
			if( $parent_id==0 ) 
				return $path; 
			
			return self::__getPath( $category, self::__getParent( $category, $item ), $sp, $path );
		} 
	}
	
	private static function __routing( ServiceModel $model, $url ) 
	{
		$category = $model->search();
		foreach( $category as $item ) 
		{
			$item = $item['Category'];
			if( $item['parent_id'] == 0 ) 
			{
				if( $item['seo_friendly_url'] == $url || $item['seo_friendly_url'].'/' == $url ) 
				{
					return 'category/index/index' . '/' . $item['id'];
				}
			}
			else 
			{
				$path = self::__getPath( $category, $item, '\/' );
				if( $item['seo_friendly_url'] == $url || $item['seo_friendly_url'].'/' == $url ) 
				{
					return 'category/index/sub' . '/' . $item['id'];
				}
			}
		}
		GlobalModifier::register( 'category', $category );
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
				if( Config::has( 'COM' ) && Config::has( 'SHIP' ) && !$app::hasUrl() ) 
					if( $path = self::__routing( servicemodel::instance()
						->setPrefix($prefix)
						->setAliasName($aliasName)
						->setModelName($modelName)
						->setTableName($tableName)
						->start(), $url ) ) 
					{
						$app->setUrl( $path ); 
					}
				break;
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