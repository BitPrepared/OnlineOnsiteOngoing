<?php 

date_default_timezone_set('Europe/Rome');

require '../vendor/autoload.php';

$config = require('../config.php');

require '../app/function.fatal.php';

$streamToFile = new \Monolog\Handler\StreamHandler( $config['log']['filename'] );
//@See https://github.com/Seldaek/monolog/blob/master/doc/01-usage.md
// DEFAULT: "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
$output = "[%datetime%] [%level_name%] [%extra%] : %message% %context%\n";
$formatter = new Monolog\Formatter\LineFormatter($output);
$streamToFile->setFormatter($formatter);

if ( $config['enviroment'] == 'development' ){
    $connection_name = 'testing';
} else {
    $connection_name = 'default';
}

$streamToFile->pushProcessor(function ($record) use ($connection_name,$argv) {
    $record['extra']['connection'] = $connection_name;
    $record['extra']['scriptname'] = $argv[0];
    return $record;
});

$log = new \Monolog\Logger('script');
$log->pushHandler($streamToFile);
