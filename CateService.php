<?php

namespace Zuuda;

class CateService implements  iTaskService, iCateService
{
	
	public static function GetInstance() { return self::_getInstance(); }
	public static function BootService( Application $app = NULL ) { return self::_bootService( $app ); }
	public static function Task( Model $model ) { return self::_routing( $model, getSingleton( 'Global' )->get( 'url' ) ); }
	public static function GetPath( $category, $item, $sp='/', $last=NULL ) { return self::_getPath( $category, $item, $sp, $last ); }
	public static function GetParent( $category, $item ) { return self::getParent( $category, $item ); }
	
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
	private function __clone() {}
	private static function _getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new CateService;
		}
		return $_instance;
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
	
	private static function _getParent( $category, $item ) 
	{
		if( !empty( $category ) ) 
		{
			foreach( $category as $cate ) 
			{
				if( $item[ 'parent_id' ] == $cate[ 'id' ] ) 
				{
					return $cate;
				}
			}
		}
	}
	
	private static function _getPath( $category, $item, $sp='/', $last=NULL ) 
	{
		$id = (int) $item[ 'id' ];
		$parent_id = (int) $item[ 'parent_id' ];
		$path = $item[ 'name' ];
		
		if( !is_null( $last ) ) 
		{
			$path = $path . $sp . $last; 
		}
		
		if( $parent_id == 0 ) 
		{
			return $path;
		}
		
		return self::_getPath( $category, self::_getParent( $category, $item ), $sp, $path );
	}
	
	private static function _routing( Model $model, $url ) 
	{
		$category = $model->parse( $model->query() );
		GlobalModifier::register( 'category', $category );
		foreach( $category as $item ) 
		{
			if( (int) $item[ 'parent_id' ] == 0 ) 
			{
				if( $item[ 'name' ] == $url || $item[ 'name' ].'/' == $url ) 
				{
					return 'category/index/index' . '/' . $item[ 'id' ];
				}
			}
			else 
			{
				$path = self::_getPath( $category, $item, '\/' );
				if( $item[ 'name' ] == $url || $item[ 'name' ].'/' == $url ) 
				{
					return 'category/index/sub' . '/' . $item[ 'id' ];
				}
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
				$model_name = (string) $program->name[ 'model' ]; 
				
				$alias_name = preg_replace( '/[\-\_\s]/', '_', $program->name[ 'alias' ] ); 
				
				$table_name = explode( '_', $alias_name );
				foreach( $table_name as $key => $value ) 
				{
					$table_name[ $key ] = getSingleton( 'Inflect' )->pluralize( $value );
				}
				$table_name = implode( '_', $table_name );

				if( Config::has( 'COM' ) && Config::has( 'SHIP' ) && !$app::hasUrl() ) 
				{
					if( $path = self::_routing( $model->setAliasName( $alias_name )->setModelName( $model_name )->setPrefix( $prefix)->setTableName( $table_name ), $url ) ) 
					{
						$app->setUrl( $path );
					}
				}
				break;
			}
		}
		
	}
	
}