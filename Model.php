<?php
namespace Zuuda;

define( 'MODEL_SFREE', 'MODEL_SFREE' );

class Model extends SQLQuery 
{
	protected $_model;
	
	final public function rootName() { return __CLASS__; }

	protected function setTable() { return MODEL_SFREE; }

	public function __construct() 
	{
		if ( !isset( $this->abstract ) ) 
		{
			if( _hasBase() )
			{
				$prefix = $this->_getPrefix();
				
				if( $this->setTable() == MODEL_SFREE ) 
				{
					return;
				}

				if( isset( $this->_hasOne ) ) 
				{
					$this->_orderHasOne( $this->_hasOne );
				}

				if( isset( $this->_hasMany ) ) 
				{
					$this->_orderHasMany( $this->_hasMany );
				}

				if( isset( $this->_hasManyAndBelongsToMany ) ) 
				{
					$this->_orderHMABTM( $this->_hasManyAndBelongsToMany );
				}

				$this->_table = $prefix . $this->_table;

				$this->_startConn();
			}
		}
	} 
	
	protected function _startConn() 
	{
		global $configs;
		
		if( !isset( $configs[ 'DATABASE' ][ 'HANDLECN' ] ) ) 
		{
			$hostname = $configs['DATABASE']['HOSTNAME'];
			$username = $configs['DATABASE']['USERNAME'];
			$password = $configs['DATABASE']['PASSWORD'];
			$database = $configs['DATABASE']['DATABASE'];
			$r = $this->connect( $hostname, $username, $password, $database ); 
			
			if( $r ) 
			{
				$configs[ 'DATABASE' ][ 'HANDLECN' ] = $this->_getDBHandle();
			}
		} 
		else 
		{
			$this->_setDBHandle( $configs[ 'DATABASE' ][ 'HANDLECN' ] );
		}
		
		$this->_describe();
		
		return $this;
	}
}