<?php

namespace Zuuda;

class cFile implements iFile 
{
	public static function GetInstance() { return self::_getInstance(); }
	public static function Get() { return self::_getInstance(); }
	public static function IsDir( $path ) { return self::_isDir( $path ); }
	public static function OpenDir( $path ) { return self::_openDir( $path ); }
	public static function ReadDir( $path ) { return self::_readDir( $path ); }
	public static function CloseDir( $path ) { return self::_closeDir( $path ); }
	public static function LookFile( $path, $file ) { return self::_lookFile( $path, $file ); }
	public static function LookDir( $path, $file = NULL ) { return self::_lookDir( $path, $file ); }
	
	public static function AssetPath( $path, $file = true ) { return _assetPath( $path, $file ); }
	public static function BuildPath( $path, $file = true ) { return _assetPath( $path, $file, true ); }
	
	public function __invoke( $name ) { return new FileInfo( $name ); }
	private function __clone(){}
	private function __construct(){} 
	private static function _getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new cFile;
		}
		return $_instance;
	}
	
	private static function _isDir( $path ) 
	{
		return is_dir( $path );
	}
	
	private static function _openDir( $path ) 
	{
		return opendir( $path );
	}
	
	private static function _readDir( $hl ) 
	{
		return readdir( $hl );
	}
	
	private static function _closeDir( $hl ) 
	{
		return closedir( $hl );
	}
	
	private static function _lookFile( $path, $file ) 
	{
		return self::_lookDir( $path, $file );
	}
	
	private static function _lookDir( $path, $file = NULL ) 
	{
		$out_path = array();
		$ignoreDirs = array('.','..','.DS_Store','empty');

		if( self::_isDir( $path ) ) 
		{
			if( $hl = self::_openDir( $path ) ) 
			{
				while( ( $com_name = self::_readDir( $hl ) ) !== false ) 
				{
					if( in_array( $com_name, $ignoreDirs ) ) 
					{
						continue;
					}

					if( $dl = self::_openDir( $path . $com_name ) ) 
					{
						while( ( $name = self::_readDir( $dl ) ) !== false ) 
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
			}
		}
		
		return $out_path;
	}
	
}