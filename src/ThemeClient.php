<?php

namespace Zuuda; 

class ThemeClient implements iThemeClient 
{
	
	public static function Load() { return self::_load(); }
	
	private static function _applyConfigs() 
	{
		global $configs;
		if( isset( $configs[ 'COM' ] ) ) 
		{
			return array
			(
				'basename'	=> 'theme', 
				'extension'	=> '.xml', 
				'hostpath'	=> THEME_DIR, 
			);
		}
		return false;
	}
	
	private static function _fetch( $data )
	{
		return array
		(
			'version' => $data->version, 
			'datetime' => $data->datetime, 
			'author' => $data->name[ 'author' ], 
			'company' => $data->name[ 'company' ], 
			'name' => $data->name, 
			'description' => $data->description, 
			'notes' => $data->notes, 
			'install_dir' => $data['installdir'], 
			'preview' => $data->preview, 
		);
	}
	
	private function __construct() {}
	private function __clone() {}
	private static function _getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new ThemeClient;
		}
		return $_instance;
	}
	
	private static function _loadConfigs() 
	{
		$configs = self::_applyConfigs();
		if( $configs ) 
		{
			return array
			(
				$configs[ 'hostpath' ] => $configs[ 'basename' ] . $configs[ 'extension' ] 
			);
		}
		return false;
	}
	
	private static function _loadData( $handle ) 
	{
		$outs = array(); 
		$len = count( $handle );
		for( $i = 0; $i < $len; $i++ ) 
		{
			$data = $handle->theme[ $i ];
			array_push( $outs, self::_fetch( $data ) );
		}
		return $outs;
	}
	
	private static function _request( $themes ) 
	{
		$outs = array();
		foreach( $themes as $key => $theme ) 
		{
			$handle = simplexml_load_file( $theme );
			array_push( $outs, self::_loadData( $handle ) );
		}
		return $outs;
	}
	
	private static function _load() 
	{
		$configs = self::_loadConfigs();
		
		if( $configs ) 
		{
			list( $realpath, $filename ) = each( $configs );
			$themes = cFile::lookDir( $realpath, $filename );
			return self::_request( $themes );
		}
		return NULL;
	}
	
}