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
			$name = (string)$handle->route[ $i ][ 'name' ];
			$left = (string)$handle->route[ $i ]->left;
			$right = (string)$handle->route[ $i ]->right;
			$parsed_left = explode(PS, $left); 
			$parsed_url = explode(PS, $url); 
			$next_route = false; 
			if(count($parsed_left)==count($parsed_url)) 
			{
				$end_url = array(); 
				$left_url = array(); 
				foreach( $parsed_left as $key => $value ) 
				{ 
					if( false!==stripos($value, ':') ) 
					{ 
						$end_url[] = $parsed_url[$key];
						continue;
					}  
					if($parsed_url[$key]!=$value) 
					{ 
						$next_route = true; 
						break; 
					} 
					$left_url[] = $value; 
				} 
				if($next_route) 
				{ 
					continue; 
				} 
				$left_url = implode(PS, $left_url); 
				$right .= PS.implode(PS, $end_url); 
				$live_path = str_replace( $basename, 'live.xml', $file_path );
				if( call( cFile::get(), $live_path )->exist() ) 
				{
					$live_xml = simplexml_load_file( $live_path );
					if( (int) $live_xml->live->status ) 
					{
						getSingleton( 'Config' )->set( 'CODE_OF', $live_xml->live->codeof->__toString() ); 
						$app->setUrl( str_replace( $left_url, $right, $url ) );
						return true;
					} 
					else 
					{ 
						abort(500, "Your extension is disabled."); 
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
				if($result = self::__routing( $app, $url, $file_path )) 
				{ 
					return $result; 
				} 
			}
		}

		return false;
	}
}