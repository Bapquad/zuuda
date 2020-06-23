<?php
namespace Zuuda\SQLpg;

use Exception; 
use Zuuda\SQLpgQuery; 

class Model extends SQLpgQuery
{
	protected $_model;
	
	final public function rootName() { return __CLASS__; }

	public function __construct() 
	{
		global $_inflect;
		try 
		{ 
			if( method_exists($this, 'init') ) return $this->init(); 
			$c1 = isset($this->_table) || isset($this->_propTable);
			$c2 = isset($this->_model) || isset($this->_propModel); 
			$c3 = isset($this->_alias) || isset($this->_propAlias); 
			$c4 = !isset($this->abstract); 
			$c5 = __useDB();
			if( $c1&&$c2&&$c3&&$c4&&$c5 ) 
			{
				$alias = (EMPTY_CHAR!==$this->_propAlias)?$this->_propAlias:$this->_alias; 
				$alias = explode(underscore, $alias); 
				sort($alias); 
				foreach( $alias as $key => $word ) 
					$alias[$key] = $_inflect->singularize(strtolower($word)); 
				$this->_alias = implode(underscore, $alias); 
				$this->__initConn();
			}
			if( NULL==$this->_primaryKey ) 
			{ 
				throw new Exception( "The <b>{$this->_propModel}Model</b> is being missed the primary key specification." );
			} 
		}			
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() ); 
		}
	} 
	
	protected function __initConn() 
	{
		global $configs;
		$src = $configs['DATASOURCE']['server']['default']; 
		if( isset($configs['DATASOURCE'][$src]) ) 
		{
			$srv = $configs['DATASOURCE'][$src]['server'];
			if( isset($configs['DATASOURCE']['server'][$srv]['source']) ) 
			{
				$datasrc = $configs['DATASOURCE']['server'][$srv]['source']; 
				if( $datasrc!=$src ) 
				{
					$this->__connect($src); 
				} 
				else 
				{ 
					if( isset($configs['DATASOURCE']['server'][$srv]['resource']) ) 
					{
						$this->__handled($configs['DATASOURCE']['server'][$srv]['resource'], $src); 
					} 
					else 
					{
						$this->__connect($src);
					}
				} 
			} 
			else 
			{
				$this->__connect($src); 
			}
		}
		
		return $this;
	}
}