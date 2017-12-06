<?php
namespace Zuuda;

define( 'AUTH_DATA', 'authorization' );
define( 'auth', AUTH_DATA );

abstract class Authorization implements iAuthorization 
{
	
	private $_user_model;
	private $_data;
	
	final public function GetUserModel() { return $this->_getUserModel(); }
	final public function SetUserModel( Model $user_model ) { return $this->_setUserModel( $user_model ); }
	final public function ConfigOpenAuth( $data ) { return $this->_configOpenAuth( $data ); }
	final public function Destroy() { return $this->_destroyAuth(); }
	final public function Clear() { return $this->_destroyAuth(); }
	final public function GetAuth($key=NULL) { return $this->_getAuth($key); }
	/** abstract public function Authorizing( $input ); */
	
	protected function _setUserModel( $user_model ) { $this->_user_model = $user_model; return $this; }
	protected function _setData( $data, $value = NULL ) 
	{
		if( NULL != $value ) 
		{
			try 
			{
				if( !is_string( $data ) ) 
				{
					throw new Exception("Authorization::_setData parameter 1 must be a string key.", 1);
				}
				$this->_data[ $data ] = $value;
				return $this;
			} 
			catch( Exception $e ) 
			{
				echo $e->getMessage(); 
				exit;
			}
		}

		if( NULL === $data ) 
		{
			$this->_data = $data;
		}
		else 
		{
			$this->_data = array_merge( $data ); 
		}

		return $this;
	}
	
	protected function _getUserModel() { return $this->_user_model; }
	protected function _getData($key=NULL) 
	{
		if( NULL !== $key && NULL !== $this->_data ) 
		{
			if( array_key_exists( $key, $this->_data ) ) 
			{
				return $this->_data[ $key ];
			}
			return NULL;
		}
		return $this->_data; 
	}
	
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
	
	private function _getAuth($key=NULL) 
	{
		// first look auth
		if( $data = $this->_lookAuth() )
		{
			$this->_setData( $data );

			return $this->_getData($key);
		} 
		
		return false;
	}
	
	private function _destroyAuth() 
	{
		if( NULL !== Session::get( AUTH_DATA ) )
		{
			$this->_setData( NULL );
			Session::unregister( AUTH_DATA );
		}
		
		return $this;
	}
	
	private function _lookAuth() 
	{
		if( NULL !== Session::get( AUTH_DATA ) )
		{
			return Session::get( AUTH_DATA );
		}
		
		return false;
	}
	
}