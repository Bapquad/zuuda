<?php

namespace Zuuda; 

class ThemeClient implements iThemeClient 
{
	
	public static function Load() { return self::__load(); }
	
	private static function __applyConfigs() 
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
	
	private static function __fetch( $data )
	{
		return array (
			'version' => $data->version->__toString(), 
			'datetime' => $data->datetime->__toString(), 
			'author' => $data->name[ 'author' ]->__toString(), 
			'company' => $data->name[ 'company' ]->__toString(), 
			'name' => $data->name->__toString(), 
			'description' => $data->description->__toString(), 
			'notes' => $data->notes->__toString(), 
			'install_dir' => $data['installDir']->__toString(), 
			'unique_id' => $data['uniqueId']->__toString(), 
			'preview' => $data->preview->__toString(), 
		);
	}
	
	private function __construct() {}
	private function __clone() {}
	private static function __getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new ThemeClient;
		}
		return $_instance;
	}
	
	private static function __loadConfigs() 
	{
		$configs = self::__applyConfigs();
		if( $configs ) 
		{
			return array
			(
				$configs['hostpath'] => $configs['basename'].$configs['extension'] 
			);
		}
		return false;
	}
	
	private static function __loadData( $handle ) 
	{
		$outs = array(); 
		$len = count( $handle );
		for( $i = 0; $i < $len; $i++ ) 
		{
			$data = $handle->theme[ $i ];
			array_push( $outs, self::__fetch($data) );
		}
		return $outs;
	}
	
	private static function __request( $themes ) 
	{
		$outs = array();
		foreach( $themes as $key => $theme ) 
		{
			$handle = simplexml_load_file( $theme );
			array_push( $outs, self::__loadData( $handle ) );
		}
		return $outs;
	}
	
	private static function __load() 
	{
		$configs = self::__loadConfigs();
		
		if( $configs ) 
		{
			list( $realpath, $filename ) = item( $configs );
			$themes = cFile::lookFile( $realpath, $filename );
			return self::__request( $themes );
		}
		return NULL;
	}
	
}