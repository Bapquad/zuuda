<?php
namespace Zuuda;

define( 'AUTH_DATA', 'authorization' );

abstract class Authorization implements iAuthorization 
{
	
	private $_user_model;
	private $_data;
	
	private function _getUserModel() { return $this->_user_model; }
	private function _getData() { return $this->_data; }
	
	private function _setUserModel( $user_model ) { $this->_user_model = $user_model; return $this; }
	private function _setData( $data ) { $this->_data = $data; return $this; }
	
	final public function GetUserModel() { return $this->_getUserModel(); }
	final public function SetUserModel( Model $user_model ) { return $this->_setUserModel( $user_model ); }
	final public function ConfigOpenAuth( $data ) { return $this->_configOpenAuth( $data ); }
	final public function Destroy() { return $this->_destroyAuth(); }
	final public function Clear() { return $this->_destroyAuth(); }
	/** abstract public function Authorizing( $data ); */
	final public function GetAuth() { return $this->_getAuth(); }
	
	public function __construct( Model $user_model = NULL ) 
	{
		$this->_setUserModel( $user_model );
	}
	
	private function _configOpenAuth( $data ) 
	{
		if( !is_null( $user_model = $this->_getUserModel() ) ) 
		{
			$user_model->setData( $data )->save();
		}
		
		return $this;
	}
	
	private function _getAuth() 
	{
		if( !is_null( $data = $this->_getData() ) 
		{
			return $data;
		}
		else 
		{
			if( $data = $this->_lookAuth() )
			{
				$this->_setData( data );
				return $data;
			}
		}
		
		return false;
	}
	
	private function _destroyAuth() 
	{
		if( isset( Session::get( AUTH_DATA ) ) 
		{
			$this->_setData( NULL );
			return Session::unregister( AUTH_DATA );
		}
		
		return true;
	}
	
	private function _lookAuth() 
	{
		if( isset( Session::get( AUTH_DATA ) ) 
		{
			return Session::get( AUTH_DATA );
		}
		
		return false;
	}
	
}