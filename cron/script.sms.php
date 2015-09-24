<?php
/**
 * Created by PhpStorm.
 * User: Stefano "Yoghi" Tamagnini
 * Date: 23/09/15 - 23:50
 *
 */

require '../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Indaba\Dashboard\Annotation as Annotation;
use Indaba\Dashboard\Evaluation as Evaluation;
use Indaba\Dashboard\Source as Source;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\Plugin\ListWith;

$config = require('../config.php');

if ( $config['enviroment'] == 'development' ){
    $connection_name = 'testing';
} else {
    $connection_name = 'default';
}

$adapter = new Local($config['uploads_dir'] . '/sms/');
$filesystem = new Filesystem($adapter);
$filesystem->addPlugin(new ListWith);

$capsule = new Capsule;
foreach ($config['databases'] as $name => $database) {
    $capsule->addConnection($database, $name);
}
$capsule->setAsGlobal();
$capsule->bootEloquent();

if ($filesystem->has('last.sms')) {
    $lastId = $filesystem->read('last.sms');
    $res = $capsule->getConnection($connection_name)->select('select ID as sms_id, SenderNumber as author, TextDecoded as plainText from inbox where ID > ?',array($lastId));
} else {
    $lastId = 0;
    $res = $capsule->getConnection($connection_name)->select('select ID as sms_id, SenderNumber as author, TextDecoded as plainText from inbox');
}

foreach($res as $sms){

//    [sms_id] => 2
//    [author] => +393395039915
//    [plainText] => 0054006500640074

    $sms_id = $sms->sms_id;
    $lastId = $sms_id;
    $author = $sms->author;
    $plainText = $sms->plainText;

    preg_match_all('/#(\w+)/', $plainText, $matches);
    $hashtags = array();
    if ( isset($matches) && count($matches) > 0 ) {
        foreach($matches[1] as $val){
            $hashtags[] = $val;
        }
    }

    $ann = Annotation::on($connection_name)->where('sourceId', $sms_id)->get();
    if ($ann->count() == 0) {

        $annotation = new Annotation(array(
            'author' => $author,
            'source' => Source::SMS,
            'sourceId' => $sms_id,
            'text' => $plainText,
            'textHtml' => $plainText,
            'hashtags' => $hashtags
        ));

        $annotation->setConnection($connection_name);
        $annotation->save();

        /**
         * FIXME: parser evaluations
         */
        $sessione = '';
        $evento = 0;
        $punteggio = 0;
        /**
         * fine FIXME: parser evaluations
         */

        $evaluation = new Evaluation(array(
            'annotation_id' => $annotation->id,
            'sessione' => $sessione,
            'evento' => $evento,
            'punteggio' => $punteggio
        ));
        $evaluation->setConnection($connection_name);
        $evaluation->save();

    }

}


if ($filesystem->has('last.sms')) {
    $filesystem->delete('last.sms');
}
$filesystem->write('last.sms', $lastId);

