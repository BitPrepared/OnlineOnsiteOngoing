<?php
/**
 * Created by PhpStorm.
 * User: Stefano "Yoghi" Tamagnini
 * Date: 23/09/15 - 23:50
 * 
 */

require 'script.base.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Indaba\Dashboard\Annotation as Annotation;
use Indaba\Dashboard\Attachment as Attachment;
use Indaba\Dashboard\Evaluation as Evaluation;
use Indaba\Dashboard\Source as Source;
use Indaba\Dashboard\Evaluation\Parser as Parser;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\Plugin\ListWith;

try {

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

                $result = Parser::parse($titolo);
                if ($result != false){
                    $evaluation = new Evaluation(array(
                        'annotation_id' => $annotation->id,
                        'sessione' => $result->sessione,
                        'evento' => $result->evento,
                        'punteggio' => $result->punteggio
                    ));
                    $evaluation->setConnection($connection_name);
                    $evaluation->save();
                }

                $attachments = $mail->getAttachments();
                foreach ($attachments as $attachment) {
                    $filePath = $attachment->filePath;
                    $data = file_get_contents($filePath);

                    $fileName = $attachment->name;
                    $filesystem->put($mail_id . '/' . $fileName, $data);
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

} catch(\Exception $e){
    $log->addError($e->getFile().' on '.$e->getLine().' '.' because : '.$e->getMessage());
}