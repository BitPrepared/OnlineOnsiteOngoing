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
use Indaba\Dashboard\Attachment as Attachment;
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

$attachmentsDir = $config['uploads_dir'] . '/tmp/';

if ( !file_exists($attachmentsDir) ) {
    mkdir($attachmentsDir);
}

$adapter_tmp = new Local($attachmentsDir);
$filesystem_tmp = new Filesystem($adapter_tmp);
$filesystem_tmp->addPlugin(new ListWith);

$adapter = new Local($config['uploads_dir'] . '/email/');
$filesystem = new Filesystem($adapter);
$filesystem->addPlugin(new ListWith);

$capsule = new Capsule;
foreach ($config['databases'] as $name => $database) {
    $capsule->addConnection($database, $name);
}
$capsule->setAsGlobal();
$capsule->bootEloquent();

$mailbox = new PhpImap\Mailbox('{' . $config['imap_server'] . ':' . $config['imap_port'] . '/imap/' . $config['imap_protocol'] . '/debug/novalidate-cert}Inbox', $config['imap_mail'], $config['imap_password'], $attachmentsDir);
$mails = array();

$mailsIds = $mailbox->searchMailBox('ALL');
if (!$mailsIds) {
    echo('Mailbox is empty'."\n");
} else {
    print_r($mailsIds);

    foreach ($mailsIds as $mailId) {

        $mail = $mailbox->getMail($mailId);
//        var_dump($mail);

        $id = $mail->id;
        $mail_id = isset($mail->messageId) ? $mail->messageId : $id;
        $titolo = $mail->subject;
        $from = $mail->fromAddress;

        $textPlain = $mail->textPlain;
        if ( trim($mail->textHtml) == '' ){
            $textHtml = $titolo.' '.$textPlain;
        } else {
            $textHtml = $titolo.' '.$mail->textHtml;
        }

        if ( !isset($textPlain) || trim($textPlain) == '' ){
            $textPlain = strip_tags($textHtml);
        } else {
            $textPlain = $titolo.' '.$textPlain;
        }

        preg_match_all('/#(\w+)/', $textPlain, $matches);
        $hashtags = array();
        if ( isset($matches) && count($matches) > 0 ) {
             foreach($matches[1] as $val){
                 $hashtags[] = $val;
             }
        }

        $ann = Annotation::on($connection_name)->where('sourceId', $mail_id)->get();
        if ($ann->count() == 0) {

            $annotation = new Annotation(array(
                'author' => $from,
                'source' => Source::EMAIL,
                'sourceId' => $mail_id,
                'text' => $textPlain,
                'textHtml' => $textHtml,
                'hashtags' => $hashtags
            ));

            $annotation->setConnection($connection_name);
            $annotation->save();


            /**
             * FIXME: da estrapolare
            */
            $sessione = '';
            $evento = 0;
            $punteggio = 0;

            if ( strlen($titolo) == 6 ) {
                $firstChar = mb_substr($titolo, 0, 1, 'utf-8');
                $firstCharCode = ord($firstChar);
                if ( $firstCharCode > 64 && $firstCharCode < 91 ){
                    $sessione = $firstChar;
                    $code = mb_substr($titolo, 1, 2, 'utf-8');
                    if ( is_numeric($code) ) {
                        $evento = $code;
                        $evaluation = mb_substr($titolo,3, 3, 'utf-8');
                        $strlen = strlen( $evaluation );
                        for( $i = 0; $i <= $strlen; $i++ ) {
                            $char = substr( $evaluation, $i, 1 );
                            if ( $char == '+' ) {
                                $punteggio++;
                            }
                            if ( $char == '-' ) {
                                $punteggio--;
                            }
                        }
                    } else {
                        echo "analyze : $titolo -> invalid code : $code \n";
                    }
                } else {
                    echo "analyze : $titolo -> invalid firstChar : $firstChar ($firstCharCode) \n";
                }
            } else {
                echo "analyze : $titolo -> lunghezza invalida (".strlen($titolo).") \n";
            }

            /**
             * fine FIXME: da estrapolare
             */

            $evaluation = new Evaluation(array(
                'annotation_id' => $annotation->id,
                'sessione' => $sessione,
                'evento' => $evento,
                'punteggio' => $punteggio
            ));
            $evaluation->setConnection($connection_name);
            $evaluation->save();

            $attachments = $mail->getAttachments();
            foreach ($attachments as $attachment) {
                $filePath = $attachment->filePath;
                $data = file_get_contents($filePath);

                $fileName = $attachment->name;
                $filesystem->write($mail_id . '/' . $fileName, $data);
                $attachments = new Attachment(array(
                    'annotation_id' => $annotation->id,
                    'source' => Source::EMAIL,
                    'fileName' => $fileName,
                    'filePath' => $mail_id . '/' . $fileName
                ));
                $attachments->setConnection($connection_name);
                $attachments->save();
                $filesystem_tmp->delete(basename($filePath));
            }

            $mailbox->moveMail($id, 'Inbox.Elaborated');

        } else {
            $mailbox->markMailAsUnread($id);
            $mailbox->moveMail($id, 'Inbox.Failure');
        }


    }
}
