<?php

namespace Zuuda;

class cFile implements iFile 
{
	public static function GetInstance() { return self::__getInstance(); }
	public static function Get() { return self::__getInstance(); }
	public static function IsDir( $path ) { return self::__isDir( $path ); }
	public static function OpenDir( $path ) { return self::__openDir( $path ); }
	public static function ReadDir( $path ) { return self::__readDir( $path ); }
	public static function CloseDir( $path ) { return self::__closeDir( $path ); }
	public static function LookFile( $path, $file ) { return self::__lookFile( $path, $file ); }
	public static function LookDir( $path, $file = NULL ) { return self::__lookDir( $path, $file ); }
	
	public static function AssetPath( $path, $file = true ) { return __assetPath( $path, $file ); }
	public static function BuildPath( $path, $file = true ) { return __assetPath( $path, $file, true ); }
	
	public function __invoke( $name ) { return new FileInfo( $name ); }
	private function __clone(){}
	private function __construct(){} 
	private static function __getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new cFile;
		}
		return $_instance;
	}
	
	private static function __isDir( $path ) 
	{
		return is_dir( $path );
	}
	
	private static function __openDir( $path ) 
	{
		return opendir( $path );
	}
	
	private static function __readDir( $hl ) 
	{
		return readdir( $hl );
	}
	
	private static function __closeDir( $hl ) 
	{
		return closedir( $hl );
	}
	
	private static function __lookFile( $path, $file ) 
	{
		return self::__lookDir( $path, $file );
	}
	
	private static function __lookDir( $path, $file = NULL ) 
	{
		$out_path = array();
		$ignoreDirs = array('.','..','.DS_Store','empty');

		if( self::__isDir( $path ) ) 
		{
			if( $hl = self::__openDir( $path ) ) 
			{
				while( ( $com_name = self::__readDir( $hl ) ) !== false ) 
				{
					if( in_array( $com_name, $ignoreDirs ) ) 
					{
						continue;
					}

					if( self::__isDir( $path . $com_name ) ) 
					{

						if( $dl = self::__openDir( $path . $com_name ) ) 
						{
							while( ( $name = self::__readDir( $dl ) ) !== false ) 
							{
								if( in_array( $name, $ignoreDirs ) ) 
								{
									continue;
								}

								if( NULL==$file ) 
								{
									$post_name = $name; 
								} 
								else 
								{
									$need_path = $path . $com_name . DS . $file;
									$real_path = $path . $com_name . DS . $name;
									if( file_exists( $need_path ) && ( $real_path == $need_path ) ) 
									{
										$post_name = $file;
									} 
									else 
									{
										$post_name = $name . PS . $file;
									}
								}

								$result_path = $path . $com_name . DS . $post_name; 

								if( file_exists( $result_path ) ) 
								{
									$out_path = array_merge( $out_path, (array) $result_path );
								}
							}
						}
					}
					else 
					{
						if( file_exists( $path . $com_name ) ) 
						{
							$result_path = $com_name;
							$out_path = array_merge( $out_path, (array) $result_path );
						}
					}	
				} 
			}
		}
		
		return $out_path;
	}
	
}