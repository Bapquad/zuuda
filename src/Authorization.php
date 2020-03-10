<?php
namespace Zuuda;

use Exception;

define( 'AUTH_DATA', 'authorization' );
define( 'auth', AUTH_DATA );

abstract class Authorization implements iAuthorization 
{
	
	private $_user_model;
	private $_data;
	
	final public function GetUserModel() { return $this->__getUserModel(); }
	final public function SetUserModel( Model $user_model ) { return $this->__setUserModel( $user_model ); }
	final public function ConfigOpenAuth( $data ) { return $this->__configOpenAuth( $data ); }
	final public function Destroy() { return $this->__destroyAuth(); }
	final public function Clear() { return $this->__destroyAuth(); }
	final public function GetAuth($key=NULL) { return $this->__getAuth($key); } 
	final public function Data($key=NULL) { return $this->__getAuth($key); } 
	
	protected function __setUserModel( $user_model ) { $this->_user_model = $user_model; return $this; }
	protected function __setData( $data, $value = NULL ) 
	{
		if( NULL!=$value ) 
		{
			try 
			{
				if( !is_string($data) ) 
				{
					throw new Exception("Authorization::__setData parameter 1 must be a string key.", 1);
				}
				$this->_data[$data] = $value;
				return $this;
			} 
			catch( \Exception $e ) 
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
	
	protected function __getUserModel() { return $this->_user_model; }
	protected function __getData($key=NULL) 
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
		$this->__setUserModel( $user_model );
	}
	
	private function __configOpenAuth( $data ) 
	{
		if( !is_null( $user_model = $this->__getUserModel() ) ) 
		{
			$user_model->setData( $data )->save();
		}
		
		return $this;
	}
	
	private function __getAuth($key=NULL) 
	{
		// first look auth
		if( $data = $this->__lookAuth() )
		{
			$this->__setData( $data );

			return $this->__getData($key);
		} 
		
		return false;
	}
	
	private function __destroyAuth() 
	{
		if( NULL !== Session::get( AUTH_DATA ) )
		{
			$this->__setData( NULL );
			Session::unregister( AUTH_DATA );
			Cookie::unregister( M_UUID );
		}
		
		return $this;
	}
	
	private function __lookAuth() 
	{
		if( NULL !== Session::get( AUTH_DATA ) )
		{
			return Session::get( AUTH_DATA );
		}
		
		return false;
	}
	
}