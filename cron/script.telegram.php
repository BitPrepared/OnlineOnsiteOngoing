<?php
/**
 * Created by PhpStorm.
 * User: Stefano "Yoghi" Tamagnini
 * Date: 23/09/15 - 22:28
 *
 */

require '../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Indaba\Dashboard\Annotation as Annotation;
use Indaba\Dashboard\Attachment as Attachment;
use Indaba\Dashboard\Evaluation as Evaluation;
use Indaba\Dashboard\Source as Source;
use Indaba\Dashboard\Evaluation\Parser as Parser;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\Plugin\ListWith;

$config = require('../config.php');

if ( $config['enviroment'] == 'development' ){
    $connection_name = 'testing';
} else {
    $connection_name = 'default';
}

$adapter = new Local($config['uploads_dir'] . '/telegram/');
$filesystem = new Filesystem($adapter);
$filesystem->addPlugin(new ListWith);

$capsule = new Capsule;
foreach ($config['databases'] as $name => $database) {
    $capsule->addConnection($database, $name);
}
$capsule->setAsGlobal();
$capsule->bootEloquent();

$TOKEN = $config['telegram_token'];

$reqN = 0;
if ($filesystem->has('last.telegram')) {
    $reqN = $filesystem->read('last.telegram');
}
$url = 'https://api.telegram.org/bot'.$TOKEN.'/';
$function='getUpdates';
$data = array('offset' => $reqN);
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ),
);
$context  = stream_context_create($options);
$result = file_get_contents($url.$function, false, $context);
$obj_telegram = json_decode($result);

foreach ( $obj_telegram->result as $message_telegram) {

    $message = $message_telegram->message;

    $idMessaggio = $message->message_id;
    $nomeMittente = $message->from->first_name;
    $cognomeMittente = $message->from->last_name;
    $userName = $message->from->username;
    $author = $cognomeMittente.' '.$nomeMittente. ' @'.$userName;
    $reqN = $idMessaggio;

    $ann = Annotation::on($connection_name)->where('sourceId', $idMessaggio)->get();
    if ($ann->count() == 0) {

        //TESTO
        if (isset($message->text)) {
            $testo = $message->text;

            $annotation = new Annotation(array(
                'author' => $author,
                'source' => Source::TELEGRAM,
                'sourceId' => $idMessaggio,
                'text' => $testo,
                'textHtml' => $testo,
                'hashtags' => array()
            ));
            $annotation->setConnection($connection_name);
            $annotation->save();

            //primo carattere @IndabaBot
            if ( strpos($testo,'@IndabaBot') === false ){
                $result = Parser::parse($testo);
            } else {
                $testoDaParsare = mb_substr($testo, 11, strlen($testo)-11, 'utf-8');
                $result = Parser::parse($testoDaParsare);
            }

            if ($result != false) {
                $evaluation = new Evaluation(array(
                    'annotation_id' => $annotation->id,
                    'sessione' => $result->sessione,
                    'evento' => $result->evento,
                    'punteggio' => $result->punteggio
                ));
                $evaluation->setConnection($connection_name);
                $evaluation->save();
            }

        }

        //PHOTO
        if (isset($message->photo)) {
            $testo = 'Photo';

            $annotation = new Annotation(array(
                'author' => $author,
                'source' => Source::TELEGRAM,
                'sourceId' => $idMessaggio,
                'text' => $testo,
                'textHtml' => $testo,
                'hashtags' => array()
            ));
            $annotation->setConnection($connection_name);
            $annotation->save();

            foreach($id = $message->photo as $photo) {

                $function='getFile';
                $data = array( 'file_id' => $photo->file_id);
                $options = array(
                    'http' => array(
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'content' => http_build_query($data),
                    ),
                );
                $context  = stream_context_create($options);
                $result = file_get_contents($url.$function, false, $context);
                $fileInfos = json_decode($result);
                $file_path = $fileInfos->result->file_path;
                $url_download = 'https://api.telegram.org/file/bot'.$TOKEN.'/'.$file_path;
                $dataImg = file_get_contents($url_download);

                $filename = basename($file_path);

                $filesystem->write($idMessaggio . '/' . $filename, $dataImg);

                $attachments = new Attachment(array(
                    'annotation_id' => $annotation->id,
                    'source' => Source::TELEGRAM,
                    'fileName' => $filename,
                    'filePath' => $idMessaggio . '/' . $filename
                ));
                $attachments->setConnection($connection_name);
                $attachments->save();

            } //photos

        }

        //VIDEO
        if (isset($message->video)) {
            $testo = 'Video';

            $annotation = new Annotation(array(
                'author' => $author,
                'source' => Source::TELEGRAM,
                'sourceId' => $idMessaggio,
                'text' => $testo,
                'textHtml' => $testo,
                'hashtags' => array()
            ));
            $annotation->setConnection($connection_name);
            $annotation->save();

            foreach($id = $message->video as $video) {

                $function='getFile';
                $data = array( 'file_id' => $video->file_id);
                $options = array(
                    'http' => array(
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'content' => http_build_query($data),
                    ),
                );
                $context  = stream_context_create($options);
                $result = file_get_contents($url.$function, false, $context);
                $fileInfos = json_decode($result);
                $file_path = $fileInfos->result->file_path;
                $url_download = 'https://api.telegram.org/file/bot'.$TOKEN.'/'.$file_path;
                $dataImg = file_get_contents($url_download);

                $filename = basename($file_path);

                $filesystem->write($idMessaggio . '/' . $filename, $dataImg);

                $attachments = new Attachment(array(
                    'annotation_id' => $annotation->id,
                    'source' => Source::TELEGRAM,
                    'fileName' => $filename,
                    'filePath' => $idMessaggio . '/' . $filename
                ));
                $attachments->setConnection($connection_name);
                $attachments->save();

            } //video

        }



        //AUDIO
        if (isset($message->voice)) {
            $testo = 'Voice';

            $annotation = new Annotation(array(
                'author' => $author,
                'source' => Source::TELEGRAM,
                'sourceId' => $idMessaggio,
                'text' => $testo,
                'textHtml' => $testo,
                'hashtags' => array()
            ));
            $annotation->setConnection($connection_name);
            $annotation->save();

            foreach($id = $message->voice as $voice) {

                $function='getFile';
                $data = array( 'file_id' => $voice->file_id);
                $options = array(
                    'http' => array(
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'content' => http_build_query($data),
                    ),
                );
                $context  = stream_context_create($options);
                $result = file_get_contents($url.$function, false, $context);
                $fileInfos = json_decode($result);
                $file_path = $fileInfos->result->file_path;
                $url_download = 'https://api.telegram.org/file/bot'.$TOKEN.'/'.$file_path;
                $dataImg = file_get_contents($url_download);

                $filename = basename($file_path);

                $filesystem->write($idMessaggio . '/' . $filename, $dataImg);

                $attachments = new Attachment(array(
                    'annotation_id' => $annotation->id,
                    'source' => Source::TELEGRAM,
                    'fileName' => $filename,
                    'filePath' => $idMessaggio . '/' . $filename
                ));
                $attachments->setConnection($connection_name);
                $attachments->save();

            } //video

        }


    }

}

if ($filesystem->has('last.telegram')) {
    $filesystem->delete('last.telegram');
}
$filesystem->write('last.telegram', $reqN);

