<?php
namespace Zuuda;

interface iAuthorization 
{
	
	public function SetUserModel( Model $user_model );
	public function GetUserModel();
	public function ConfigOpenAuth( $data );
	public function GetAuth();
	public function Destroy();
	public function Clear();

	public function Authorizing( $input );
	public function GetData();
}