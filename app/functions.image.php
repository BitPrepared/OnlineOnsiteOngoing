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

use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;

function getResources($uploads_dir,$id,$maxWidth, $connection = 'default'){

    $attachments = Attachment::on($connection)->where('id', $id)->get();

    if ( count($attachments) == 1 ) {
        $attachment = $attachments[0];

        switch($attachment->source){
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
                break;
        }

        $filesystem = new Filesystem($adapter);
        $filesystem->addPlugin(new ListWith);

        if ($filesystem->has($attachment->filePath)) {

            $data = $filesystem->read($attachment->filePath);
            $fp['data'] = $data;
            $fp['mime'] = $filesystem->getMimetype($attachment->filePath);

            if ( $maxWidth > 0 ) {

                $imagine = new \Imagine\Gd\Imagine();

                $image = $imagine->load($data);

                $size = $image->getSize();

                if ( $size->getWidth() > $maxWidth ) {

                    // AWIDTH : AHEIGHT = NWIDTH : NHEIGHT
                    // HHEIGHT = AHEIGHT * NWIDTH / AWIDTH

                    $height = $size->getHeight() * $maxWidth / $size->getWidth();
                    $width = $maxWidth;

                    $fp['data'] = $image->resize(new Box($width,$height), ImageInterface::FILTER_UNDEFINED)->show('png'); //FILTER_QUADRATIC

                }

            }

            return $fp;

        }

    }

    return false;

}
