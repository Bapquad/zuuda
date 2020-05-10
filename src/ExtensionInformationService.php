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
				'basename'	=> array( 'live', 'about', 'menu', 'shortcut' ),  
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
			$host = array();
			foreach($configs['basename'] as $key => $name) 
			{ 
			
				$host[$configs['basename'][$key]] = $configs['basename'][$key].$configs['extension']; 
			}
			return array( $configs['host'] => $host );
		} 
		return false;
	} 

	private static function __read_live( $file_path ) 
	{
		if( call(cFile::get(), $file_path)->exist() ) 
		{
			$fp = simplexml_load_file( $file_path ); 
			$module = $fp->live['module']->__toString();
			$status = (int) $fp->live->status; 
			if( $status ) 
			{
				return array( 
					$module => array(
						'codeof' => $fp->live->codeof->__toString(), 
						'status' => (int) $fp->live->status, 
					), 
				);
			} 
			else 
			{ 
				return false;
			} 
		}
		return false;
	} 

	private static function __read_about( $file_path ) 
	{
		if( call(cFile::get(), $file_path)->exist() ) 
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
		return false;
	} 
	
	private static function __read_shortcut( $file_path, $codeof ) 
	{ 
		$instance = self::__getInstance();
		if( call(cFile::get(), $file_path)->exist() ) 
		{ 
			$fp = simplexml_load_file( $file_path );
			$module = $fp->shortcut['module']->__toString();
			
			$blocks = array();
			foreach($fp->shortcut->block as $block) 
			{
				$hidden = (int)$block['hidden']; 
				if( $hidden ) 
				{ 
					continue; 
				} 
				$instance->__push( 'shortcut', array(
					'class' => $module.BS."Blocks".BS.$block['name'], 
					'extend' => $block['extend']->__toString(), 
					'developer'=> $codeof, 
				));
			}
			
			return false;
		} 
	} 

	private static function __read_menu( $file_path ) 
	{
		if( call( cFile::get(), $file_path )->exist() ) 
		{
			$fp = simplexml_load_file( $file_path );
			$module = $fp->menu['module']->__toString(); 
			$output = array( $module => array() );
			$extend = strtolower( $module ); 

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
		return false;
	}

	private function __push( $branch, $value=NULL ) 
	{
		if( !array_key_exists($branch, $this->_exts) ) 
		{
			$this->_exts[$branch] = array();
		} 
		
		if( NULL!==$value ) 
		{
			$this->_exts[$branch][] = $value; 
		}
	}

	private static function __bootService( Application $app = NULL ) 
	{
		$instance = self::__getInstance();
		$configs = $instance->__loadConfigs();
		list( $code_area, $configs ) = each( $configs ); 
		$live_paths = cFile::lookFile( $code_area, $configs['live'] ); 
		unset($configs['live']);
		foreach($live_paths as $live_path) 
		{
			$result = $instance->__read_live( $live_path ); 
			if( $result ) 
			{
				$instance->__push( 'live', $result );
				
				list($module, $value) = each($result);
				
				foreach( $configs as $key => $config ) 
				{
					$command = '__read_'.$key; 
					$result = $instance->$command($code_area.$value['codeof'].DS.'Extensions'.DS.$module.DS.'driver'.DS.$config, $value['codeof']); 
					if( $result ) 
					{
						$instance->__push( $key, $result );
					}
				}
			}
		} 
		return false;
	}

}