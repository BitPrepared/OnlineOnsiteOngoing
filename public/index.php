<?php
/**
 * Created by PhpStorm.
 * User: Stefano "Yoghi" Tamagnini
 * Date: 21/09/15 - 22:41
 * 
 */

require '../vendor/autoload.php';

date_default_timezone_set('Europe/Rome');

$config = require('../config.php');

require '../app/function.fatal.php';

$streamToFile = new \Monolog\Handler\StreamHandler( $config['log']['filename'] );
//@See https://github.com/Seldaek/monolog/blob/master/doc/01-usage.md
// DEFAULT: "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
$output = "[%datetime%] [%level_name%] [%extra%] : %message% %context%\n";
$formatter = new Monolog\Formatter\LineFormatter($output);
$streamToFile->setFormatter($formatter);
$streamToFile->pushProcessor(function ($record) use ($config) {
    if ( $config['enviroment'] == 'development' ){
        $record['extra']['connection'] = 'testing';
    } else {
        $record['extra']['connection'] = 'default';
    }
    return $record;
});
$handlers[] = $streamToFile;
$logger_writer = new \Flynsarmy\SlimMonolog\Log\MonologWriter(array(
    'handlers' => $handlers,
    'processors' => array(
        new Monolog\Processor\UidProcessor(),
        new Monolog\Processor\WebProcessor($_SERVER),
    )
));

$app = new \Slim\Slim(array(
    'mode' => $config['enviroment'],
    'log.level' => \Slim\Log::DEBUG,
    'log.enabled' => true,
    'log.writer' => $logger_writer,
    'templates.path' => '../templates'
));

$app->config('databases', $config['databases']);

$app->add(new \BitPrepared\Slim\Middleware\EloquentMiddleware);
$corsOptions = array(
    "origin" => "*"
);
$app->add(new \CorsSlim\CorsSlim($corsOptions));

$app->hook('slim.before.router', function () use ($app) {
    $req = $app->request;
    $allGetVars = $req->get();
    $allPostVars = $req->post();
    $allPutVars = $req->put();
    
    $vars = array_merge($allGetVars,$allPostVars);
    $vars = array_merge($vars,$allPutVars);

    $srcParam = json_encode($vars);

    $srcUri = $req->getRootUri();
    $srcUrl = $req->getResourceUri();
    //$app->log->info(@Kint::dump( $srcUrl ));
    $app->log->debug('REQUEST : '.var_export($_REQUEST,true));
    // Sono stati messi nel log dal logger
    // $app->log->debug('URI : '.$srcUri);
    // $app->log->debug('URL : '.$srcUrl);
    $app->log->debug('Params : '.$srcParam);
    $req->isAjax() ? $app->log->debug('Ajax attivo') : $app->log->debug('Ajax non attivo');
});

$app->hook('slim.after.dispatch', function () use ($app) {
    $status = $app->response->getStatus();
    $app->log->debug('terminato con stato ['.$status.']');
});


// $log = $app->getLog();
// $view = $app->view();

$app->configureMode('development', function() use ($app,$config) {
    $app->config(array(
        'debug' => true
    ));
    $connection_name = 'testing';
    include '../app/app.php';
});

$app->configureMode('production', function() use ($app,$config) {
    $app->config(array(
        'debug' => false
    ));
    $connection_name = 'default';
    include '../app/app.php';
});

$app->get('/', function () use ($app,$config)  {
    if ( $config['maintenance'] ) {
        $app->render('home/maintenance.php', array());
    } else {
        $app->render('home/index.php', array());
    }
});

$app->get('/setup', function() use ($app,$config) {
    include '../app/functions.setup.php';
    setup($config);
});

$app->notFound(function () use ($app) {
    $app->render('404.html');
});

$app->error(function (\Exception $e) use ($app) {
    $app->log->error($e->getFile().' on '.$e->getLine().' '.' because : '.$e->getMessage());
    $app->response->headers->set('Content-Type', 'text/html');
    $app->render('500.html');
});

$app->run();
?>