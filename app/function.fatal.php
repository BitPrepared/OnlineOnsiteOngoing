<?php

function fatal_handler($config) {
    $errfile = "unknown file";
    $errstr  = "shutdown";
    $errno   = E_CORE_ERROR;
    $errline = 0;

    $error = error_get_last();

    if( $error !== NULL) {
        $errfile = $error["file"];
        $errstr  = $error["message"];
        $errno   = $error["type"];
        $errline = $error["line"];

        $identifiedIp = \BitPrepared\Security\IpIdentifier::get_ip_address();
        if ( isset($_SERVER['HTTP_HOST']) && isset($_SERVER['REQUEST_URI']) ) {
            $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        } else {
            $actual_link = 'script';
        }

        $msg = json_encode( array(
                'ip' => $identifiedIp,
                'call' => $actual_link,
                'no' => $errno,
                'str' => $errstr,
                'file' => $errfile,
                'line' => $errline
            )
        );
        // format_error( $errno, $errstr, $errfile, $errline, false);
        file_put_contents($config['log']['filename'],$msg."\n",FILE_APPEND);
    }
}

register_shutdown_function( "fatal_handler" , $config );