<?php
/**
 * Created by PhpStorm.
 * User: Stefano "Yoghi" Tamagnini
 * Date: 23/09/15 - 22:00
 * 
 */

use \Indaba\Dashboard\Attachment as Attachment;
use Indaba\Dashboard\Source as Source;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\Plugin\ListWith;

function getResources($uploads_dir,$id,$connection = 'default'){

    $attachments = Attachment::on($connection)->where('id', $id)->get();

    if ( count($attachments) == 1 ) {
        $attachment = $attachments[0];

        switch($attachment->source){
            case Source::SMS:
                $adapter = new Local($uploads_dir . '/sms/');
                break;
            case Source::EMAIL:
                $adapter = new Local($uploads_dir . '/email/');
                break;
            case Source::TWITTER:
                $adapter = new Local($uploads_dir . '/twitter/');
                break;
            case Source::TELEGRAM:
                $adapter = new Local($uploads_dir . '/telegram/');
                break;
            default:
        }

        $filesystem = new Filesystem($adapter);
        $filesystem->addPlugin(new ListWith);

        $fp['data'] = $filesystem->read($attachment->filePath);
        $fp['mime'] = $filesystem->getMimetype($attachment->filePath);

        return $fp;

    }

    return false;

}
