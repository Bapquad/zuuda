<?php

require_once __DIR__ . '/symbol.php';
function zuuda_api_autoload( $class_name ) {__dispatch_autoload_class_file( $class_name );}
spl_autoload_register( 'zuuda_api_autoload' );

