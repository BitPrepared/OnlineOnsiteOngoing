<?php
/**
 * Created by PhpStorm.
 * User: Stefano "Yoghi" Tamagnini
 * Date: 21/09/15 - 22:41
 * 
 */

require '../vendor/autoload.php';

$config = require('../config.php');

$app = new \Slim\Slim(array(
    'mode' => $config['enviroment'],
    'debug' => true,
    'log.level' => \Slim\Log::DEBUG,
    'log.enabled' => true,
    'templates.path' => '../templates'
));

$app->config('databases', $config['databases']);

$app->add(new \BitPrepared\Slim\Middleware\EloquentMiddleware);
$corsOptions = array(
    "origin" => "*"
);
$app->add(new \CorsSlim\CorsSlim($corsOptions));

$log = $app->getLog();

$view = $app->view();

$app->get('/', function () use ($app)  {
    $app->render('home/index.php', array());
});

$app->get('/setup', function() use ($app,$config) {
    include '../app/functions.setup.php';
    setup($config);
});

$app->configureMode('development', function() use ($app,$config) {
    $app->get('/info', function () use ($app)  {
        $app->render('info.php', array());
    });
    $app->get('/feed(/:startFrom)', function($startFrom = 0) use ($app,$config) {
        $app->response->headers->set('Content-Type', 'application/json');
        include '../app/functions.feed.php';
        echo feed('testing',$startFrom);
    });
});

$app->configureMode('production', function() use ($app,$config) {
    $app->get('/feed(/:startFrom)', function($startFrom = 0) use ($app,$config) {
        $app->response->headers->set('Content-Type', 'application/json');
        include '../app/functions.feed.php';
        echo feed('default',$startFrom);
    });
});

$app->notFound(function () use ($app) {
    $app->render('404.html');
});

$app->error(function (\Exception $e) use ($app) {
	//FIXME: scrivo l'errore 
    $app->render('500.html');
});

$app->run();
?>