<?php 
function __numeral( $number ) 
{
	return number_format( (int) $number, 0, ',', '.' );
}

function __correctPath( $class_path ) 
{
	return str_replace( PS, DS, $class_path );
} 

function __buildPath( $file_path, $file = false ) 
{
	return __assetPath( $file_path, $file, true );
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
	} 
	else return $str_name;
} 

function __t($str_name) 
{
	global $configs;
	if( isset($configs['LOCATE']) && isset($configs['LOCATE']['TRANS'][$str_name]) ) 
		return $configs['LOCATE']['TRANS'][$str_name];
	else 
		return $str_name;
}

function __assetPath( $file_path, $file = false, $build = false ) 
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
		$file_path = __correctPath($file_path);
		$theme_path = __correctPath($theme_path); 
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

function __currentControllerFile() 
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

function __currentModelClass() 
{
	global $configs;
	return $configs[ 'MODULE' ].BS.MODEL_PRE.BS.$configs[ 'CONTROLLER' ].MODEL;
}

function __currentViewClass() 
{
	global $configs;
	return $configs[ 'MODULE' ].BS.VIEW_PRE.BS.$configs[ 'CONTROLLER' ].VIEW;
}

function __availbleClass( $class_name ) 
{
	global $configs;
	$class_file = __correctPath( $class_name.$configs[ 'EXT' ] );
	
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

function isDev() 
{
	global $configs;
	return !($configs['DEVELOPMENT_ENVIRONMENT'] && $configs['DEVELOPER_WARNING']); 
}

function __hasDomain() 
{
	global $configs;
	return ( $_SERVER ["SERVER_NAME"] == $configs['DOMAIN'] );
}

function __useDB() 
{
	global $configs;
	return !is_null( $configs['DATASOURCE'] ); 
} 

function escape() 
{
	if( call_user_func('__escape') ) 
		exit(zero); 
}

function __escape() 
{
	global $configs;
	if( isset($configs['DATASOURCE']['HANDLECN']) ) 
		return mysqli_close( $configs['DATASOURCE']['HANDLECN'] ); 
	else 
		return true;
}

function __dispatch_service_file( $service_namespace ) 
{
	return str_replace(FW_NAME.DS, FW_NAME.DS.SRC_DIR ,implode(EMPTY_CHAR, $service_namespace));
}

function __dispatch_autoload_class_file( $class_name ) 
{
	global $configs; 
	$class_file = __correctPath( $class_name.$configs[ 'EXT' ] ); 
	$class_path = str_replace( FW_NAME.DS, FW_NAME.DS.SRC_DIR, VENDOR_DIR.$class_file ); 
	if( !file_exists( $class_path ) ) 
	{
		if( $configs[ 'COM' ] ) 
		{
			$class_path = COM . $class_file;
			if( !file_exists( $class_path ) ) 
				$class_path = CODE . ((isset($configs['CODE_OF']))?$configs['CODE_OF'].DS:NULL) . $class_file; 
		}
		
		if( !file_exists( $class_path ) ) 
			$class_path = MOD_DIR . $class_file;
	} 
	require_once( $class_path );
} 
function dispatch( $class_name ) {__dispatch_autoload_class_file( $class_name );}

function __direct( $url ) 
{
	__escape();
	header( "location: $url" );
	exit;
}
function redirect( $url ) 
{
	__direct( $url );
}

/**
 * Initialize the strip slash function.
 * @params
 * - $value : the value which wants to strip.
 */
function __stripSlashesDeep( $value ) 
{
	$value = is_array( $value ) ? array_map( 'stripSlashesDeep', $value ) : stripslashes( $value );
	return $value;
}


/**
 * The debug functions.
 */

function __trace_show( $var ) 
{
	if( EMPTY_CHAR===$var ) 
		echo 'string: ""'.nl;
	else if( is_null($var) ) 
		echo 'null: null'.nl; 
	else if( is_numeric($var) ) 
		echo 'numeric: '.$var.nl;
	else if( is_string($var) ) 
		echo 'string: '.$var.nl;
	else if( is_bool($var) )
		echo (($var)?'bool: true':'bool: false').nl;
	else if( is_object($var) || is_array($var) ) 
		var_dump($var); 
	else if( is_resource($var) ) 
		echo 'resource: '.$var;
}

function watch() 
{
	if( isDev() ) 
		return; 
	try 
	{
		if( 0===func_num_args() ) 
			throw new Exception("Sử dụng hàm <strong>watch</strong> không đúng cú pháp."); 
		Zuuda\RequestHeader::DisplayCode();
		$back_trace = debug_backtrace()[0];
		if($back_trace['function']==='watch') 
		{
			echo nl.'<pre>'.nl;
			echo basename($back_trace['file']).':'.$back_trace['line'].' [watch]'.nl;
			$args = $back_trace['args']; 
			foreach( $args as $arg ) 
				__trace_show($arg);
			echo nl.'</pre>'.nl;
		}
	}
	catch( Exception $e ) 
	{
		abort( 400, $e->getMessage() );
	}
} 

function write() 
{
	if( isDev() ) 
		return; 
	try 
	{
		if( 0===func_num_args() ) 
			throw new Exception("Sử dụng hàm <strong>write</strong> không đúng cú pháp."); 
		Zuuda\RequestHeader::DisplayCode();
		$back_trace = debug_backtrace()[0];
		if($back_trace['function']==='write') 
		{
			echo nl.'<pre>'.nl;
			echo basename($back_trace['file']).':'.$back_trace['line'].' [write]'.nl;
			$args = $back_trace['args']; 
			foreach( $args as $arg ) 
				__trace_show($arg);
			echo nl.'</pre>'.nl;
		}
	}
	catch( Exception $e ) 
	{
		abort( 400, $e->getMessage() );
	}
} 

function check() 
{
	if( isDev() ) 
		return; 
	try 
	{
		if( 0===func_num_args() ) 
			throw new Exception("Sử dụng hàm <strong>check</strong> không đúng cú pháp."); 
		Zuuda\RequestHeader::DisplayCode();
		$back_trace = debug_backtrace()[0];
		if($back_trace['function']==='check') 
		{
			echo nl.'<pre>'.nl;
			echo basename($back_trace['file']).':'.$back_trace['line'].' [check]'.nl;
			$args = $back_trace['args']; 
			foreach( $args as $arg ) 
				__trace_show($arg);
			echo nl.'</pre>'.nl;
			escape(); 
		}
	}
	catch( Exception $e ) 
	{
		abort( 400, $e->getMessage() );
	}
} 

function debug() 
{ 
	if( isDev() ) 
		return; 
	try 
	{
		if( 0===func_num_args() ) 
			throw new Exception("Sử dụng hàm <strong>debug</strong> không đúng cú pháp."); 
		Zuuda\RequestHeader::DisplayCode();
		$back_trace = debug_backtrace()[0];
		if($back_trace['function']==='debug') 
		{
			echo nl.'<pre>'.nl;
			echo basename($back_trace['file']).':'.$back_trace['line'].' [debug]'.nl;
			$args = $back_trace['args']; 
			foreach( $args as $arg ) 
				__trace_show($arg);
			echo nl.'</pre>'.nl;
			escape(); 
		}
	}
	catch( Exception $e ) 
	{
		abort( 400, $e->getMessage() );
	}
}

function leave() 
{
	if( isDev() ) 
		return; 
	if( func_num_args() ) 
	{
		Zuuda\RequestHeader::DisplayCode();
		$back_trace = debug_backtrace()[0];
		if($back_trace['function']==='leave') 
		{
			echo nl.'<pre>'.nl;
			echo basename($back_trace['file']).':'.$back_trace['line'].' [leave]'.nl;
			$args = $back_trace['args']; 
			foreach( $args as $arg ) 
				__trace_show($arg);
			echo nl.'</pre>'.nl;
			escape(); 
		}
	}
	
	Zuuda\RequestHeader::DisplayCode();
	$back_trace = debug_backtrace()[0];
	echo nl.'<pre>'.nl;
	echo basename($back_trace['file']).':'.$back_trace['line'].' [leave]'.nl;
	echo $back_trace['function']; 
	echo nl.'</pre>'.nl;
	escape(); 
}

function quit() 
{
	if( isDev() ) 
		return; 
	if( func_num_args() ) 
	{
		Zuuda\RequestHeader::DisplayCode();
		$back_trace = debug_backtrace()[0];
		if($back_trace['function']==='quit') 
		{
			echo nl.'<pre>'.nl;
			echo basename($back_trace['file']).':'.$back_trace['line'].' [quit]'.nl;
			$args = $back_trace['args']; 
			foreach( $args as $arg ) 
				__trace_show($arg); 
			echo nl.'</pre>'.nl;
			escape(); 
		}
	}
	
	Zuuda\RequestHeader::DisplayCode();
	$back_trace = debug_backtrace()[0]; 
	echo nl.'<pre>'.nl;
	echo basename($back_trace['file']).':'.$back_trace['line'].' [quit]'.nl;
	echo $back_trace['function']; 
	echo nl.'</pre>'.nl;
	escape(); 
} 

function stop() 
{
	if( isDev() ) 
		return; 
	if( func_num_args() ) 
	{
		Zuuda\RequestHeader::DisplayCode();
		$back_trace = debug_backtrace()[0];
		if($back_trace['function']==='stop') 
		{
			echo nl.'<pre>'.nl;
			echo basename($back_trace['file']).':'.$back_trace['line'].' [stop]'.nl;
			$args = $back_trace['args']; 
			foreach( $args as $arg ) 
				__trace_show($arg); 
			echo nl.'</pre>'.nl;
			escape(); 
		}
	}
	
	Zuuda\RequestHeader::DisplayCode();
	$back_trace = debug_backtrace()[0]; 
	echo nl.'<pre>'.nl;
	echo basename($back_trace['file']).':'.$back_trace['line'].' [stop]'.nl;
	echo $back_trace['function']; 
	echo nl.'</pre>'.nl;
	escape(); 
}

function __trace( Exception $e ) 
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

function __trace_once( Exception $e ) 
{
	Zuuda\RequestHeader::DisplayCode();
	__trace( $e );
	exit;
} 

function trace( Exception $e ) 
{
	__trace( $e ); 
}

function trace_once( Exception $e ) 
{
	__trace_once( $e );
}

function __move( $old, $target ) 
{
	if( copy( $old, $target ) ) 
		unlink( $old );
} 

function move( $old, $target ) 
{
	__move( $old, $target );
}

function encrypt($string) 
{
    $encryptedString = '';
    for($i=0;$i<strlen($string);$i++) 
	{
        if($encryptedString){ $encryptedString .= "-"; }
        $encryptedString .= ( 2 * ord( $string[$i] ) + 10 );
    }
    return $encryptedString;
}

function decrypt($encryptedString) 
{
    $decryptedString = '';
    $arr = explode("-",$encryptedString);
    foreach($arr as $num) 
	{
        $decryptedString .= chr(($num-10)/2);
    }
    return $decryptedString;
}

function singularize($str) 
{
    if( preg_match("/^(c|C)las$/",$str) ) 
		return "class"; 
    elseif( preg_match("/ies$/",$str) ) 
		return substr( $str, 0, -3 )."y"; 
    elseif( preg_match("/es$/",$str) ) 
		return substr( $str, 0, -2 ); 
    elseif( preg_match("/s$/",$str) ) 
		return substr( $str, 0, -1 ); 
    else 
		return $str; 
}

function plural($str) 
{
    if(preg_match("/s$/",$str)) 
		return $str."es"; 
    elseif(preg_match("/y$/",$str)) 
		return substr( $str, 0, -1 )."ies"; 
    else
		return $str."s";
} 

function uc_words($str)
{
	return ucwords(preg_replace('/_/', ' ', $str)); 
}

function tbl2cls($tblname) 
{
    $clasname = ucwords(singularize($tblname));
    if( $clasname == 'Class' )
	{ 
		$clasname = 'Clas'; 
	}
    return $clasname;
}

function number_to_words($number) 
{
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'fourty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );
    if(!is_numeric($number)) 
	{
        return false;
    }
    if(($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) 
	{
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }
    if($number < 0) 
	{
        return $negative . number_to_words(abs($number));
    }
    $string = $fraction = null;
    if(strpos($number, '.') !== false) 
	{
        list($number, $fraction) = explode('.', $number);
    }
    switch(true) 
	{
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if($units) 
			{
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if($remainder) 
			{
                $string .= $conjunction . number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if($remainder) 
			{
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= number_to_words($remainder);
            }
            break;
    }
    if(null !== $fraction && is_numeric($fraction)) 
	{
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) 
		{
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }
    return $string;
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
				<p>You can try following ways</p>
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
			case 'Put':
				return Zuuda\Put::getInstance(); 
			case 'Delete':
				return Zuuda\Delete::getInstance(); 
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