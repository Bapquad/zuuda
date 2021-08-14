<?php

namespace Zuuda; 

use PDO;

class PdoConnector extends PDO 
{
	
	/**
	 * Declares a connector driver. 
	 */
	protected $_driver = 'mysql';
	 
	/**
	 * Declares a host name
	 */
	protected $_host;
	
	/**
	 * Declares a database name
	 */
	protected $_dbname;
	
	/**
	 * Declares an user name
	 */
	protected $_username;
	
	/**
	 * Declares an user's password
	 */
	protected $_password;
	
	/**
	 * 
	 */
	
	/**
	 * The constructor.
	 */
	public function __construct() 
	{
		try 
		{
			parent::__construct( $this->_dns(), $this->_username, $this->_password, $this->__configs() );
		}
		catch(PDOException $e) 
		{
			abort( 400, $e->getMessage() );
		}
	}
	
	/**
	 * Initialize the connection basic informations.
	 */
	private function __init() 
	{
		global $configs;
		$ds = $configs['DATASOURCE'];
		$myapp = $ds['server']['default'];
		$server = $ds['server'][$ds[$myapp]['server']];
		$this->_dbname 		= $ds[$myapp]['database']; 
		$this->_host 		= $server['hostname']; 
		$this->_username	= $server['username']; 
		$this->_password	= $server['password']; 
	} 
	
	/**
	 * Getting the domain name server.
	 */
	private function _dns() 
	{ 
		$this->__init();
		return "$this->_driver:host=$this->_host;dbname=$this->_dbname";
	}
	
	/**
	 * Setup the contection options.
	 */
	private function __configs() 
	{
		$options = array();
		$options += array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ); 
		if( $this->_driver==='mysql' ) 
			$options += array( PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8" ); 
		return $options;
	}
	
}