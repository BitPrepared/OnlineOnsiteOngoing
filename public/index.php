<?php
/**
 * Created by PhpStorm.
 * User: Stefano "Yoghi" Tamagnini
 * Date: 21/09/15 - 22:41
 * 
 */

require '../vendor/autoload.php';
require '../config.php';

$app = new \Slim\Slim(array(
    'mode' => 'development',
    'debug' => true,
    'log.level' => \Slim\Log::DEBUG,
    'log.enabled' => true,
    'templates.path' => '../templates'
));

// After instantiation
$log = $app->getLog();

$view = $app->view();

$app->get('/', function () use ($app)  {
    $app->render('home/index.php', array());
});

$app->get('/info', function () use ($app)  {
    $app->render('info.php', array());
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