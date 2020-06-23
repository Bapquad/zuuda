<?php
namespace Zuuda\NoSQL\MongoDB;

use Exception; 
use Zuuda\NoSQL\MongoDBQuery; 

abstract class Model extends MongoDBQuery 
{
	
	protected $_model; 
	protected $_propLive = true;
	
	final public function rootName() { return __CLASS__; }
	
	final public function IsLive() 
	{ 
		return $this->_propLive; 
	} 
	
	final public function Blind() 
	{ 
		$this->_propLive = false; 
		return $this;
	} 
	
	final public function Unblind() 
	{ 
		$this->_propLive = true; 
		return $this; 
	} 
	
	final public function ParseStmtSelection() 
	{
		return $this->__parseStmtSelection( $this->_propModel, $this->_propsUndescribe );
	} 
	
	final public function ParseStmtHasOne() 
	{
		return $this->__parseStmtHasOne(); 
	} 
	
	final public function ParseStmtMerge() 
	{ 
		return $this->__parseStmtMerge(); 
	} 
	
	final public function ParseStmtMergeLeft() 
	{ 
		return $this->__parseStmtMergeLeft(); 
	} 
	
	final public function ParseStmtMergeRight() 
	{ 
		return $this->__parseStmtMergeRight(); 
	} 
	
	final public function GetForeignKey() 
	{
		return $this->_propForeignKey;
	} 
	
	final public function GetAliasKey() 
	{
		return $this->_propAliasKey; 
	} 
	
	final public function SetForeignKey( $key ) 
	{ 
		return $this->__setForeignKey( $key ); 
	} 
	
	final public function SetAliasKey( $key ) 
	{
		return $this->__setAliasKey( $key ); 
	}
	
	final public function SetAliasModel($model) 
	{
		return $this->__setAliasModel($model); 
	}
	
	public function __construct() 
	{
		global $_inflect;
		if( method_exists($this, 'init') ) return $this->init();
		
		$abstract = false; 
		if( isset($this->abstract) ) 
		{
			$abstract = $this->abstract;
		}
		
		if( __useDB() && !$abstract ) 
		{ 
			try 
			{
				if( !isset($this->_schema) ) 
					throw new Exception("<b><i>[EXCEPTION]</i></b> You have not a schema declaration. Let's define a schema for this model."); 
				
				if( !isset($this->_collection) ) 
					throw new Exception("<b><i>[EXCEPTION]</i></b> You have not a collection declaration. Let's define a collection for this model."); 
				
				if( !isset($this->_model) ) 
					throw new Exception("<b><i>[EXCEPTION]</i></b> You have not a model declaration. Let's define a collection for this model."); 
				
				// $alias = $this->_propAlias?:$this->_alias; 
				// $alias = explode(underscore, $alias); 
				// sort($alias);
				// foreach( $alias as $key => $word ) 
					// $alias[$key] = $_inflect->singularize(strtolower($word)); 
				// $this->_alias = implode(underscore, $alias); 
				
				$this->__initConn(); 
			} 
			catch( Exception $e ) 
			{ 
				abort( 500, $e->getMessage() );
			}
		}
	}
	
	protected function __initConn() 
	{ 
		global $_CONFIG; 
		$src = $_CONFIG['DATASOURCE']['server']['default']; 
		if( isset($_CONFIG['DATASOURCE'][$src]) ) 
		{ 
			$srv = $_CONFIG['DATASOURCE'][$src]['server']; 
			if( isset($_CONFIG['DATASOURCE']['server'][$srv]['source']) ) 
			{ 
				$datasrc = $_CONFIG['DATASOURCE']['server'][$srv]['source']; 
				if( $datasrc!=$src ) 
				{ 
					$this->__connect($src); 
				} 
				else 
				{ 
					if( isset($_CONFIG['DATASOURCE']['server'][$srv]['resource']) ) 
					{
						$this->__selectDB($_CONFIG['DATASOURCE'][$src]['database']); 
						$this->__handled( $_CONFIG['DATASOURCE']['server'][$srv]['resource'], $src ); 
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
	}
	
	private function __setForeignKey( $key ) 
	{
		$this->_propAliasKey = $key; 
		return $this;
	} 
	
	private function __setAliasKey( $key ) 
	{
		$this->_propForeignKey = $key; 
		return $this; 
	} 
	
	private function __setAliasModel( $key ) 
	{
		$this->_propAliasModel = $key; 
		return $this;
	}
	
}
