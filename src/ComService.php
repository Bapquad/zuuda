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
				$configs['host'] => $configs['basename'].$configs['extension'] 
			);
		}
		return false;
	}
	
	private static function __checkLive($app, $file_path, $url, $left_url, $right_url) 
	{
		$live_path = str_replace( basename($file_path), 'live.xml', $file_path );
		if( call( cFile::get(), $live_path )->exist() ) 
		{
			$live_xml = simplexml_load_file( $live_path );
			if( (int) $live_xml->live->status ) 
			{
				singleton('Config')->set( 'CODE_OF', $live_xml->live->codeof->__toString() ); 
				$app->setUrl( str_replace( implode(PS, $left_url), $right_url, $url ) );
				return true;
			} 
			else 
			{ 
				abort(500, "Your extension is disabled."); 
			} 
		}
	}
	
	private static function __routing( $app, $url, $file_path ) 
	{
		global $_CONFIG;
		$handle = simplexml_load_file( $file_path ); 
		$len = count( $handle->route );
		for( $i=0; $i<$len; $i++ ) 
		{
			$left_url = array(); 
			$end_url = array(); 
			$left = $handle->route[$i]->left->__toString();
			$right_url = $handle->route[$i]->right->__toString();
			if( preg_match_all('#(/:[.]*)#', $left, $matches) ) 
			{
				$parsed_left = explode(PS, $left); 
				$parsed_url = explode(PS, $url); 
				if( count($parsed_left)==count($parsed_url) ) 
				{
					$next_route = false; 
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
					return self::__checkLive($app, $file_path, $url, $left_url, $right_url); 
				}
			}
			else if( preg_match_all('#^'.$left.'$#', $url, $matches) ) 
			{
				return self::__checkLive($app, $file_path, $url, explode(PS, $left), $right_url); 
			} 
		}
		return false;
	}
	
	private static function __bootService( Application $app = NULL ) 
	{
		if( Config::has( 'COM' ) && !$app->hasUrl() ) 
		{
			$url = singleton( 'Global' )->get( 'url' ); 
			$configs = self::__loadConfigs();
			$realpath = key( $configs ); 
			$filename = current( $configs );
			
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