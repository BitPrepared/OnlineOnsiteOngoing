<?php

$app->get('/feed(/:startFrom)', function($startFrom = 0) use ($app,$config,$connection_name) {
    try {
        $app->response->headers->set('Content-Type', 'application/json');
        include '../app/functions.feed.php';
        echo feed($connection_name,$startFrom);
    } catch(\Exception $e){
        $app->error($e);
    }
});
$app->get('/history/:startFrom', function($startFrom) use ($app,$config,$connection_name) {
    try {
        $app->response->headers->set('Content-Type', 'application/json');
        include '../app/functions.feed.php';
        echo feedhistory($connection_name,$startFrom);
    } catch(\Exception $e){
        $app->error($e);
    }
});
$app->get('/resources/:id(/:maxWidth)', function($id,$maxWidth = 0) use ($app,$config,$connection_name) {
    try {
        include '../app/functions.image.php';
        $fp = getResources($config['uploads_dir'],$id,$maxWidth,$connection_name);
        if (!$fp) {
            $app->notFound();
        }
        $app->response->headers->set('Content-Type', $fp['mime']);
        $app->response->setBody($fp['data']);
    } catch(\Exception $e){
        $app->error($e);
    }
});
$app->get('/annotation/new', function() use ($app,$config,$connection_name) {
    try {
        include '../app/functions.web.php';
        $app->render('annotation/form.php', array());
    } catch(\Exception $e){
        $app->error($e);
    }
});
$app->post('/annotation/new', function() use ($app,$config,$connection_name) {
    try {
        include '../app/functions.web.php';
        $req = $app->request;
        insertAnnotation($req,$connection_name);
        $app->render('annotation/ok.php', array());
    } catch(\Exception $e){
        $app->error($e);
    }
});