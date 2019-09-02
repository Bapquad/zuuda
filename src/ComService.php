<?php 
namespace Zuuda;

class ComService implements iComService 
{
	
	public static function GetInstance() { return self::__getInstance(); }
	public static function BootService( Application $app = NULL ) { return self::__bootService( $app ); }
	
	private function __construct() {} 
	private function __clone() {}
	private static function __getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new ComService;
		}
		return $_instance;
	}
	
	private static function __applyConfigs() 
	{
		if( Config::has( 'COM' ) ) 
		{
			return array (
				'basename' 	=> 'route', 
				'driver'	=> 'driver', 
				'extension'	=> '.xml', 
				'host'		=> CODE,
			);
		}
		return false;
	}
	
	private static function __loadConfigs() 
	{
		$configs = self::__applyConfigs(); 
		if( $configs ) 
		{
			return array(
				$configs[ 'host' ] => $configs[ 'driver' ] . DS . $configs[ 'basename' ] . $configs[ 'extension' ] 
			);
		}
		return false;
	}
	
	private static function __routing( $app, $url, $file_path ) 
	{
		global $configs;
		$basename = basename( $file_path );
		$handle = simplexml_load_file( $file_path );
		$len = count( $handle->route );
		for( $i = 0; $i < $len; $i++ ) 
		{
			$name = $handle->route[ $i ][ 'name' ];
			$left = $handle->route[ $i ]->left;
			$right = $handle->route[ $i ]->right;
			if ( $left == $url ) 
			{
				$live_path = str_replace( $basename, 'live.xml', $file_path );
				if( call( cFile::get(), $live_path )->exist() ) 
				{
					$live_xml = simplexml_load_file( $live_path );
					if( (int) $live_xml->live->status ) 
					{
						getSingleton( 'Config' )->set( 'CODE_OF', $live_xml->live->codeof->__toString() );
						$app->setUrl( str_replace( $left, $right, $url ) );
						return true;
					}
				}
			}
		}
		return false;
	}
	
	private static function __bootService( Application $app = NULL ) 
	{
		if( Config::has( 'COM' ) && !$app->hasUrl() ) 
		{
			$url = getSingleton( 'Global' )->get( 'url' );
			$configs = self::__loadConfigs();
			list( $realpath, $filename ) = @each( $configs );
			
			$list = cFile::lookFile( $realpath, $filename );
			foreach( $list as $file_path ) 
			{
				return self::__routing( $app, $url, $file_path );
			}
			die();
		}

		return false;
	}
}