<?php

function zuuda_api_autoload( $class_name ) 
{
	global $configs; 
	$class_file = _correctPath( $class_name.$configs[ 'EXT' ] ); 
	$class_path = VENDOR.DS.$class_file; 
	if( !file_exists( $class_path ) ) 
	{
		if( $configs[ 'COM' ] ) 
		{
			$class_path = COM . $class_file;
			if( !file_exists( $class_path ) ) 
			{
				$class_path = CODE . ((isset($configs['CODE_OF']))?$configs['CODE_OF'].DS:NULL) . $class_file; 
			}
		}
		
		if( !file_exists( $class_path ) ) 
			$class_path = MOD_DIR . $class_file;
	} 
	require_once( $class_path );
}

spl_autoload_register( 'zuuda_api_autoload' );

function _correctPath( $class_path ) 
{
	return str_replace( BS, PS, $class_path );
} 

function _buildPath( $file_path, $file = false ) 
{
	return _assetPath( $file_path, $file, true );
}

function _assetPath( $file_path, $file = false, $build = false ) 
{
	global $configs;
	if( isset( $configs[ 'themes' ] ) && isset( $configs[ 'SHIP' ] ) ) 
	{
		$path = WEB_DIR . $configs[ 'themes' ] . $file_path;
		
		if( call( Zuuda\cFile::get(), $path )->exist() || $build ) 
		{
			if( $file ) 
			{
				return $path;
			}
			return ( WEB_PATH . $configs[ 'themes' ] . $file_path );
		}
	} 
	
	if( $file ) 
	{
		return WEB_DIR . $file_path;
	}
	else 
	{
		return WEB_PATH . $file_path;
	}
}

function _currentControllerFile() 
{
	global $configs;
	$controller = $configs[ 'MODULE' ].DS.CTRLER_DIR.$configs[ 'CONTROLLER' ].CONTROLLER.$configs[ 'EXT' ];
	if( $configs[ 'COM' ] && isset( $configs['SHIP'] ) ) 
	{
		$code_path = CODE.((isset($configs['CODE_OF']))?$configs['CODE_OF'].DS:NULL).$controller;

		$com_path = COM.$controller;
		if( call( Zuuda\cFile::get(), $code_path )->exist() ) 
			return $code_path; 
		if( call( Zuuda\cFile::get(), $com_path )->exist() ) 
			return $com_path; 
	}
	return MOD_DIR.$controller; 
}

function _currentModelClass() 
{
	global $configs;
	return $configs[ 'MODULE' ].BS.MODEL_PRE.BS.$configs[ 'CONTROLLER' ].MODEL;
}

function _currentViewClass() 
{
	global $configs;
	return $configs[ 'MODULE' ].BS.VIEW_PRE.BS.$configs[ 'CONTROLLER' ].VIEW;
}

function _availbleClass( $class_name ) 
{
	global $configs;
	$class_file = _correctPath( $class_name.$configs[ 'EXT' ] );
	
	if( $configs[ 'COM' ] && isset( $configs[ 'SHIP' ] ) ) 
	{
		$class_path = COM.$class_file;
		if( !call( Zuuda\cFile::get(), $class_path )->exist() ) 
		{
			$class_path = CODE.((isset($configs['CODE_OF']))?$configs['CODE_OF'].DS:NULL).$class_file; 
			if( !call( Zuuda\cFile::get(), $class_path )->exist() ) 
				$class_path = MOD_DIR.$class_file; 
		}
	}
	else 
	{
		$class_path = MOD_DIR.$class_file; 
	}
	
	return call( Zuuda\cFile::get(), $class_path )->exist();
} 

function _hasDomain() 
{
	global $configs; 
	return ( $_SERVER [ "SERVER_NAME" ] == $configs[ 'DOMAIN' ] );
}

function _hasBase() 
{
	global $configs;
	return !is_null( $configs[ 'DATABASE' ] ); 
} 

function _closeDB() 
{
	if( isset( $configs[ 'DATABASE' ][ 'HANDLECN' ] ) ) 
	{
		@mysql_close( $configs[ 'DATABASE' ][ 'HANDLECN' ] );
	}
}

function _direct( $url ) 
{
	header( "location: $url" );
	die();
}
function redirect( $url ) 
{
	_direct( $url );
}

/**
 * Initialize the strip slash function.
 * @params
 * - $value : the value which wants to strip.
 */
function _stripSlashesDeep( $value ) 
{
	$value = is_array( $value ) ? array_map( 'stripSlashesDeep', $value ) : stripslashes( $value );
	return $value;
}


/**
 * Initialize the bug function.
 * This will print the value.
 * @params
 * - $value : the value which wants to see.
 */
function _revealBug( $value ) 
{
	echo $value . '<br>'; 
	return $value;
} 


/**
 * Initialize the bug function.
 * This will die the program flows.
 * @params
 * - $value : the value which wants to see.
 */
function _bugDie( $var ) 
{
	die( var_dump( $var ) ); 
} 

function _watch( $var ) 
{
	echo '<pre>' . nl;
	// Zuuda\RequestHeader::displayText();
	var_dump( $var ); 
	echo '</pre>' . nl;
} 

function watch( $var ) 
{
	_watch( $var ); 
} 

function _watch_once( $var ) 
{
	die( _watch( $var ) );
} 

function watch_once( $var ) 
{
	_watch_once( $var ); 
}

function _move( $old, $target ) 
{
	if( copy( $old, $target ) ) 
		unlink( $old );
} 

function move( $old, $target ) 
{
	_move( $old, $target );
}


if( Zuuda\GlobalModifier::func( 'getSingleTon' ) ) 
{
	function getSingleTon( $const ) 
	{
		switch( $const ) 
		{
			case 'Session':
				return Zuuda\Session::getInstance();
			case 'Html':
				return Zuuda\Html::getInstance();
			case 'File':
				return Zuuda\cFile::getInstance();
			case 'Config':
				return Zuuda\Config::getInstance();
			case 'Global':
				return Zuuda\GlobalModifier::getInstance();
			case 'Post':
				return Zuuda\Post::getInstance(); 
			case 'Get':
				return $_GET;
			case 'Query':
				return Zuuda\Config::get( 'QUERY_STRING' );
			case 'Request':
				return Zuuda\Config::get( 'REQUEST_VARIABLES' );
			case 'Inflect':
				global $inflect;
				return $inflect; 
			default: 
				global $configs;
				if( TRUE===$configs[ 'COM' ] ) 
					if( 'ExtensionServices'===$const ) 
					{
						return Zuuda\ExtensionInformationService::getInstance(); 
					} 
					else if( 'ExtensionServiceInformations'===$const ) 
					{
						return Zuuda\ExtensionInformationService::getInstance()->info();
					}
					else if( 'ExtensionServiceMenu'===$const ) 
					{
						return Zuuda\ExtensionInformationService::getInstance()->info( 'menu' );
					} 
					else if( 'ExtensionServiceLive'===$const ) 
					{
						return Zuuda\ExtensionInformationService::getInstance()->info( 'live' ); 
					} 
					else if( 'ExtensionServiceAbout'===$const ) 
					{
						return Zuuda\ExtensionInformationService::getInstance()->info( 'about' );
					}
				else 
					return NULL;
				break;
		}
	}
}



/**
 * Initialize the calling function.
 * This will call any a function.
 * @params
 * - $func : the function want to call.
 * - $args: the arguments  
 */
if( Zuuda\GlobalModifier::func( 'call' ) ) 
{
	function call( $func, $args = NULL ) 
	{
		if( is_callable( $func ) && is_array( $args ) ) 
		{
			return call_user_func_array( $func, $args );
		}
		elseif( !is_object( $args ) ) 
		{
			return call_user_func( $func, $args );
		}
		return false;
	}
}



/**
 * Initialize the calling function.
 * This will call any a function end the program.
 * @params ( not )
 */
if( Zuuda\GlobalModifier::func( 'finish' ) ) 
{
	function finish() 
	{
		exit();
	}
}