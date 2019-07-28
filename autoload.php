<?php

function zuuda_api_autoload( $class_name ) {_dispatch( $class_name );}
spl_autoload_register( 'zuuda_api_autoload' );

function _numeral( $number ) 
{
	return number_format( (int) $number, 0, ',', '.' );
}

function _correctPath( $class_path ) 
{
	return str_replace( PS, DS, $class_path );
} 

function _buildPath( $file_path, $file = false ) 
{
	return _assetPath( $file_path, $file, true );
}

function __($str_name) 
{
	global $configs;
	if( isset($configs['LOCATE']) ) 
	{
		$refs = $configs['LOCATE']['TRANS'];
		$keys = explode('.', $str_name); 
		foreach($keys as $key) 
		{
			$refs = &$refs[$key];
		}
		return ($refs)?$refs:$str_name;
	} else return $str_name;
}

function _assetPath( $file_path, $file = false, $build = false ) 
{
	global $configs;
	$theme_path = EMPTY_CHAR;
	if( isset( $configs[ 'themes' ] ) && isset( $configs[ 'SHIP' ] ) ) 
	{
		$theme_path = $configs[ 'themes' ] . $file_path;
		$path = WEB_DIR . $theme_path;
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
		$path = WEB_DIR . $file_path;
		if( call(Zuuda\cFile::get(), $path)->exist() )
			return $path;
		$file_path = _correctPath($file_path);
		$theme_path = _correctPath($theme_path); 
		if(false!==stripos($file_path, CACHE_TPL_NAME_DIR)) 
		{
			return WEB_DIR . $file_path; 
		} 
		if( EMPTY_CHAR!==$file_path ) 
			abort( 400, "<strong style=\"\">$file_path</strong> is not found.<br/><strong style=\"\">$theme_path</strong> is not found also.</p>" );
		else 
			abort( 400, "<strong style=\"\">$file_path</strong> is not found.</p>" );
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
	return !is_null( $configs[ 'DATASOURCE' ] ); 
} 

function _closeDB() 
{
	if( isset( $configs[ 'DATASOURCE' ][ 'HANDLECN' ] ) ) 
	{
		@mysql_close( $configs[ 'DATASOURCE' ][ 'HANDLECN' ] );
	}
}

function _dispatch( $class_name ) 
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
function dispatch( $class_name ) {_dispatch( $class_name );}

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
	Zuuda\RequestHeader::DisplayText();
	echo $value . "\n"; 
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
	Zuuda\RequestHeader::DisplayText();
	die( var_dump( $var ) ); 
} 

function _watch( $var ) 
{
	Zuuda\RequestHeader::displayText();
	var_dump( $var ); 
} 

function watch( $var ) 
{
	_watch( $var ); 
} 

function _watch_once( $var ) 
{
	Zuuda\RequestHeader::DisplayText();
	die( _watch( $var ) );
} 

function watch_once( $var ) 
{
	_watch_once( $var ); 
} 

function stop( $var=NULL ) 
{
	_stop( $var );
}

function _stop( $var=NULL ) 
{
	if(NULL!==$var) 
	{
		Zuuda\RequestHeader::DisplayText();
		_watch( $var );
	}
	exit;
}

function debug( $var=NULL ) 
{ 
	_stop( $var );
}

function _move( $old, $target ) 
{
	if( copy( $old, $target ) ) 
		unlink( $old );
} 

function _trace( Exception $e ) 
{
	echo "ERROR Message: ".$e->getMessage().nl;
	echo "In the line ".$e->getLine()." of file :".$e->getFile().nl."The trace:".nl;
	$traces = $e->getTrace();
	foreach($traces as $trace) 
	{
		if( isset($trace[ 'file' ]) )
			echo "Line: ".$trace['line']." File:".$trace[ 'file' ].nl;
	} 
} 

function _trace_once( Exception $e ) 
{
	Zuuda\RequestHeader::DisplayText();
	_trace( $e );
	exit;
} 

function trace( Exception $e ) 
{
	_trace( $e ); 
}

function trace_once( Exception $e ) 
{
	_trace_once( $e );
}

function move( $old, $target ) 
{
	_move( $old, $target );
}

function abort( $code=404, $msg=NULL ) 
{
	global $configs;
	$try_again_link = '<li>Let\'s try <a href="javascript:void(0)" onclick="window.location.reload(true)">again</a>.</li>';
	if($code===500) 
	{
		header( "HTTP/1.1 500 Internal Server Error" ); 
		$page = WEB_DIR . "500.html";
		if( file_exists($page) ) 
		{
			include_once( $page );
			exit;
		}
		if(NULL===$msg) 
			$msg = "Woops! You have a internal server error.";
		$title = "<span style=\"font-size: 1.8rem\">Internal Server Error</span>";
	}
	else if($code===408) 
	{
		header( "HTTP/1.0 408 Request Timeout" ); 
		$page = WEB_DIR . "408.html";
		if( file_exists($page) ) 
		{
			include_once( $page );
			exit;
		}
		if(NULL===$msg) 
			$msg = "Woops! Looks like your request is timeout.";
		$title = "Request Timeout";
	} 
	else if($code===404) 
	{
		header( "HTTP/1.1 404 Not Found" ); 
		$page = WEB_DIR . "404.html";
		if( file_exists($page) ) 
		{
			include_once( $page );
			exit;
		}
		if(NULL===$msg) 
			$msg = "Woops! Looks like your page couldn't found.";
		$title = "Not Found";
	} 
	else if($code===403) 
	{
		header( "HTTP/1.0 403 Forbidden" ); 
		$page = WEB_DIR . "403.html";
		if( file_exists($page) ) 
		{
			include_once( $page );
			exit;
		}
		if(NULL===$msg) 
			$msg = "Woops! Looks like you have deny from this request.";
		$title = "Forbidden";
		$try_again_link = NULL;
	} 
	else if($code===401) 
	{
		header( "HTTP/1.0 401 Unauthorized" ); 
		$page = WEB_DIR . "401.html";
		if( file_exists($page) ) 
		{
			include_once( $page );
			exit;
		}
		if(NULL===$msg) 
			$msg = "Woops! Looks like you haven't authorized.";
		$title = "Unauthorized";
		$try_again_link = NULL;
	} 
	else if($code===400) 
	{
		header( "HTTP/1.0 400 Bad Request" ); 
		$page = WEB_DIR . "400.html";
		if( file_exists($page) ) 
		{
			include_once( $page );
			exit;
		}
		if(NULL===$msg) 
			$msg = "Woops! Looks like your request is invalid.";
		$title = "Bad Request";
	} 
	$uri = $_SERVER['REQUEST_URI'];
	$domain = $configs['DOMAIN'];
/*HTML*/echo 
<<<EOL
<!DOCTYPE html>
	<head>
		<meta charset="UTF-8">
		<title>$code - $title</title>
		<style type="text/css">
			body 
			{
				background-color: #dfdfdf; 
				font-family: "Segoe UI", Frutiger, "Frutiger Linotype", "Dejavu Sans", "Helvetica Neue", Arial, sans-serif; 
				font-size: 0.8rem;
				padding: 0;
				margin: 0;
			}
			
			a 
			{
				font-weight: bold;
			}
			
			.container 
			{
				position: fixed;
				display: flex;
				align-items: center;
				justify-content: center;
				height: 100%;
				width: 100%;
			}
			
			.content 
			{
				background-color: #eee;
				width: 36rem;
				min-width: 17.5rem;
				min-height: 18rem;
				margin: 0 auto;
				border-radius: 1rem;
				border: 0.8rem dashed #ccc;
				box-shadow: 0 0 0.5rem 0 #00000077;
				padding: 1.3rem 2rem;
			} 
			
			.title 
			{
				font-size: 3rem;
				line-height: 3rem;
				margin: 0rem;
				margin-top: .5rem;
				color: #b33333;
			} 
			
			.name 
			{
				font-size: 2.2rem;
				border-left: 0.05rem solid;
				padding-left: 1rem;
			}
			
			.url 
			{
				margin-top: 7rem;
			}
			
			.domain 
			{
				text-align: right;
			}
			
			.domain em 
			{
				color: #999;
			}
			
			.domain strong
			{
				font-weight: bold;
				color: #03f;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<div class="content">
				<h1 class="title">ERROR $code <span class="name">$title</span></h1>
				<p class="message">$msg</p>
				<p>You can try follow ways</p>
				<ul>
					$try_again_link
					<li>Return <a href="javascript:void(0)" onclick="window.history.back()">back</a> history.</li>
				</ul>
				<p class="url"><em><b>URI</b>: $uri<em></p>
				<p class="domain"><strong>Domain:</strong> <em>$domain</em></p>
			</div>
		</div>
	</body>
</html>
EOL;
/*HTML*/
	exit;
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