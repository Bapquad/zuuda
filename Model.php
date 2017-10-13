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
			global $inflect;
			global $configs;
			
			if( _hasBase() )
			{
				if( $this->setTable() == MODEL_SFREE ) 
				{
					return;
				}
				
				$prefix = $this->_getPrefix();
				$has_one = $this->_getHasOne();
				
				if( !is_null( $prefix ) && $prefix != '' ) 
				{
					if( !is_null( $has_one ) ) 
					{
						foreach( $has_one as $alias_child => $table_child ) 
						{
							$has_one[ $alias_child ] = $prefix . $table_child;
						}
						$this->_setHasOne( $has_one );
					}
					if( isset( $this->hasMany ) ) 
					{
						foreach( $this->hasMany as $aliasChild => $tableChild ) 
						{
							$this->hasMany[ $aliasChild ] = $this->_prefix . $tableChild;
						}
					}
					if( isset( $this->hasManyAndBelongsToMany ) ) 
					{
						foreach( $this->hasManyAndBelongsToMany as $aliasChild => $tableChild ) 
						{
							$this->hasManyAndBelongsToMany[ $aliasChild ] = $this->_prefix . $tableChild;
						}
					}
				
					$this->_table = $this->_prefix . $this->_table;
				}

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