<?php

function zuuda_api_autoload( $class_name ) 
{
	global $configs; 
	$class_file = $class_name.$configs[ 'EXT' ]; 
	$class_path = VENDOR.DS.$class_file; 
	$class_path = _correctPath( $class_path );
	
	if( !file_exists( $class_path ) ) 
	{
		if( $configs[ 'COM' ] ) 
		{
			$class_path = _correctPath( COM . $class_file );
			if( !file_exists( $class_path ) ) 
			{
				$class_path = _correctPath( CODE . $class_file ); 
			}
		}
		
		if( !file_exists( $class_path ) ) 
			$class_path = _correctPath( MOD_DIR . $class_file );
	}
	
	require_once( $class_path );
}

spl_autoload_register( 'zuuda_api_autoload' );



function _correctPath( $class_path ) 
{
	$correct_class_path = str_replace( DS, PS, $class_path );
	return $correct_class_path;
}



function _assetPath( $file_path, $file = false ) 
{
	global $configs;
	if( isset( $configs[ 'themes' ] ) && isset( $configs[ 'SHIP' ] ) ) 
	{
		$path = _correctPath( WEB_DIR . $configs[ 'themes' ] . DS . $file_path );
		if( call( Zuuda\cFile::get(), $path )->exist() ) 
		{
			if( $file ) 
			{
				return $path;
			}
			return ( _correctPath( WEB_PATH . $configs[ 'themes' ] . PS . $file_path ) );
		}
	} 
	
	if( $file ) 
	{
		return _correctPath( WEB_DIR . $file_path );
	}
	else 
	{
		return _correctPath( WEB_PATH . $file_path );
	}
}



function _currentControllerFile() 
{
	global $configs;
	$controller = $configs[ 'MODULE' ].DS.CTRL_DIR.$configs[ 'CONTROLLER' ].CONTROLLER.$configs[ 'EXT' ];
	if( $configs[ 'COM' ] && isset( $configs['SHIP'] ) ) 
	{
		if( call( Zuuda\cFile::get(), CODE.$controller )->exist() ) 
			return CODE.$controller; 
		if( call( Zuuda\cFile::get(), COM.$controller )->exist() ) 
			return COM.$controller; 
	}
	return MOD_DIR.$controller; 
}



function _currentModelClass() 
{
	global $configs;
	return $configs[ 'MODULE' ].DS.MODEL_DIR.$configs[ 'CONTROLLER' ].MODEL;
}



function _currentViewClass() 
{
	global $configs;
	return $configs[ 'MODULE' ].DS.VIEW_DIR.$configs[ 'CONTROLLER' ].VIEW;
}



function _availbleClass( $class_name ) 
{
	global $configs;
	$class_file = $class_name.$configs[ 'EXT' ];
	
	if( $configs[ 'COM' ] && isset( $configs[ 'SHIP' ] ) ) 
	{
		$class_path = _correctPath( COM.$class_file ); 
		if( !call( Zuuda\cFile::get(), $class_path )->exist() ) 
		{
			$class_path = _correctPath( CODE.$class_file ); 
			if( !call( Zuuda\cFile::get(), $class_path )->exist() ) 
				$class_path = _correctPath( MOD_DIR.$class_file ); 
		}
	}
	else 
	{
		$class_path = _correctPath( MOD_DIR.$class_file ); 
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
function _bugDie( $value ) 
{
	die( var_dump( $value ) ); 
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