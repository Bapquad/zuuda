<?php 
namespace Zuuda;

class WidgetInformationService implements iComService 
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
			$_instance = new WidgetInformationService; 
		} 
		return $_instance;
	} 

	private static function __applyConfigs() 
	{
		if( Config::has( 'COM' ) ) 
		{
			return array(
				'basename'	=> array( 'wlive', 'widget' ),  
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

	private static function __read_widget( $file_path ) 
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
		$code_area = key($configs); 
		$configs = current($configs);
		$live_paths = cFile::lookFile( $code_area, $configs['wlive'] ); 
		unset($configs['wlive']);
		foreach($live_paths as $live_path) 
		{
			$result = $instance->__read_live( $live_path ); 
			if( $result ) 
			{
				$instance->__push( 'live', $result );
				$module = key($result);
				$value = current($result);
				
				foreach( $configs as $key => $config ) 
				{
					$command = '__read_'.$key; 
					$result = $instance->$command($code_area.$value['codeof'].DS.WIDGETS.DS.$module.DS.'driver'.DS.$config, $value['codeof']); 
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