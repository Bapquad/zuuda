<?php 
namespace Zuuda;

class ExtensionInformationService implements iComService 
{
	private $_exts = array();

	public function Info( $part=NULL ) { return $this->__info( $part ); }
	public static function GetInstance() { return self::__getInstance(); } 
	public static function BootService( Application $app = NULL ) { return self::__bootService( $app ); } 

	private function __construct() {} 
	private function __clone() {} 
	private function __info( $part=NULL ) { return (NULL!==$part)?(isset($this->_exts[ $part ]))?$this->_exts[ $part ]:array():$this->_exts; }
	private static function __getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new ExtensionInformationService; 
		} 
		return $_instance;
	} 

	private static function __applyConfigs() 
	{
		if( Config::has( 'COM' ) ) 
		{
			return array(
				'basename'	=> array( 'about', 'live', 'menu' ),  
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
				$configs[ 'host' ] => array( 
					array( $configs[ 'basename' ][ 0 ] => $configs[ 'driver' ] . DS . $configs[ 'basename' ][ 0 ] . $configs[ 'extension' ] ), 
					array( $configs[ 'basename' ][ 1 ] => $configs[ 'driver' ] . DS . $configs[ 'basename' ][ 1 ] . $configs[ 'extension' ] ), 
					array( $configs[ 'basename' ][ 2 ] => $configs[ 'driver' ] . DS . $configs[ 'basename' ][ 2 ] . $configs[ 'extension' ] ), 
				)
			);
		} 
		return false;
	} 

	private static function __read_live( $file_path ) 
	{
		if( call( cFile::get(), $file_path )->exist() ) 
		{
			$fp = simplexml_load_file( $file_path ); 
			$module = $fp->live['module']->__toString();
			return array( 
				$module => array(
					'codeof' => $fp->live->codeof->__toString(), 
					'status' => (int) $fp->live->status, 
				), 
			);
		}
		return NULL;
	} 

	private static function __read_about( $file_path ) 
	{
		if( call( cFile::get(), $file_path )->exist() ) 
		{
			$fp = simplexml_load_file( $file_path ); 
			$module = $fp->about['module']->__toString(); 
			return array( 
				$module => array(
					'name' 		=> $fp->about->name->__toString(), 
					'version'	=> $fp->about->version->__toString(), 
					'datetime'	=> $fp->about->datetime->__toString(), 
					'company'	=> $fp->about->name[ 'company' ]->__toString(), 
					'author'	=> $fp->about->name[ 'author' ]->__toString(), 
					'description' => $fp->about->description->__toString(), 
					'notes'		=> $fp->about->notes->__toString(), 
					'preview'	=> $fp->about->preview->__toString() 
				)
			);
		} 
		return NULL;
	} 

	private static function __read_menu( $file_path, $instance ) 
	{
		// $basename = basename( $file_path );
		// $live_path = str_replace( $basename, 'live.xml', $file_path );
		// if( call( cFile::get(), $live_path )->exist() ) 
		// {
		// 	$live_xml = simplexml_load_file( $live_path );
		// 	if( !((int) $live_xml->live->status) ) 
		// 	{
		// 		return NULL;
		// 	}
		// }
		if( call( cFile::get(), $file_path )->exist() ) 
		{
			$fp = simplexml_load_file( $file_path );
			$module = $fp->menu['module']->__toString(); 
			$output = array( $module => array() );
			$extend = strtolower( $module ); 

			$live = $instance->info( 'live' );
			if( !$live[ $module ][ 'status' ] ) 
			{
				return NULL;
			}

			$label = $fp->menu[ 'label' ];
			if( NULL!==$label ) 
			{
				if( !isset( $output[ $module ][ $extend ] ) )
					$output[ $module ][ $extend ] = array( 'actions' => array() );
				$output[ $module ][ $extend ][ 'label' ] = $label->__toString(); 
			}

			$fa = $fp->menu[ 'fa' ]; 
			if( NULL!==$fa ) 
			{
				if( !isset( $output[ $module ][ $extend ] ) )
					$output[ $module ][ $extend ] = array( 'actions' => array() );
				$output[ $module ][ $extend ][ 'fa' ] = $fa->__toString();
			}

			$href = $fp->menu[ 'href' ]; 
			if( NULL!==$href ) 
			{
				if( !isset( $output[ $module ][ $extend ] ) )
					$output[ $module ][ $extend ] = array( 'actions' => array() );
				$output[ $module ][ $extend ][ 'href' ] = $href->__toString();
			}

			foreach( $fp->menu->action as $action ) 
			{
				if( NULL!==$action[ 'extend' ] ) 
				{
					$extend = $action[ 'extend' ]->__toString();
				} 

				if( !isset( $output[ $module ] ) )
					$output[ $module ] = array();

				if( !isset( $output[ $module ][ $extend ] ) )
					$output[ $module ][ $extend ] = array( 'actions' => array() );

				array_push( $output[ $module ][ $extend ][ 'actions' ], array(
					'label' => $action->label->__toString(), 
					'fa'	=> $action->fa->__toString(), 
					'bind'	=> $action->bind->__toString(), 
					'href'	=> $action->href->__toString()
				));
			} 
			return $output; 
		} 
		return NULL;
	}

	private function __push( $branch, $value=NULL ) 
	{
		if( FALSE===array_key_exists( $branch, $this->_exts ) ) 
		{
			$this->_exts[ $branch ] = array();
		} 
		if( NULL!==$value )
			$this->_exts[ $branch ] = array_merge( $this->_exts[ $branch ], (array) $value );
	}

	private static function __bootService( Application $app = NULL ) 
	{
		$instance = self::__getInstance();
		$configs = $instance->__loadConfigs();

		list( $code_area, $configs ) = each( $configs ); 

		foreach( $configs as $config ) 
		{
			list( $key, $filename ) = each( $config );
			$list = cFile::lookFile( $code_area, $filename ); 
			$command = '__read_'.$key; 

			foreach( $list as $file_path ) 
			{
				$result = $instance->$command( $file_path, $instance ); 
				if( NULL!==$result )
					$instance->__push( $key, $result );
			} 
		} 
		return false;
	}

}