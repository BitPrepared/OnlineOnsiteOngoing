<?php
/**
 * Created by PhpStorm.
 * User: Stefano "Yoghi" Tamagnini
 * Date: 23/09/15 - 22:28
 *
 */

require '../vendor/autoload.php';

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Database\Capsule\Manager as Capsule;
use Indaba\Dashboard\Annotation as Annotation;
use Indaba\Dashboard\Attachment as Attachment;
use Indaba\Dashboard\Evaluation as Evaluation;
use Indaba\Dashboard\Source as Source;
use Indaba\Dashboard\Evaluation\Parser as Parser;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\Plugin\ListWith;

function parseTweet($ret)
{
    $ret = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $ret);
    $ret = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret);
    $ret = preg_replace("/@(\w+)/", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $ret); // Usernames
    $ret = preg_replace("/#(\w+)/", "<a href=\"http://search.twitter.com/search?q=\\1\" target=\"_blank\">#\\1</a>", $ret); // Hash Tags
    return $ret;
}

$config = require('../config.php');

if ( $config['enviroment'] == 'development' ){
    $connection_name = 'testing';
} else {
    $connection_name = 'default';
}

$adapter = new Local($config['uploads_dir'] . '/twitter/');
$filesystem = new Filesystem($adapter);
$filesystem->addPlugin(new ListWith);

$capsule = new Capsule;
foreach ($config['databases'] as $name => $database) {
    $capsule->addConnection($database, $name);
}
$capsule->setAsGlobal();
$capsule->bootEloquent();

$connection = new TwitterOAuth($config['twitter_consumer_key'], $config['twitter_consumer_secret'], $config['twitter_access_token'], $config['twitter_access_secret']);
$connection->setTimeouts(10, 15);

if ($filesystem->has('last.tweet')) {
    $lastId = $filesystem->read('last.tweet');
    $content_search = $connection->get("search/tweets", array("q" => $config['twitter_hashtag_search'], "result_type" => "recent", "count" => "10", "since_id" => "$lastId"));
} else {
    $content_search = $connection->get("search/tweets", array("q" => $config['twitter_hashtag_search'], "result_type" => "recent", "count" => "10"));
}

foreach ($content_search->statuses as $elem_search) {
    $id_tweet = $elem_search->id_str;
    $lastId = $id_tweet;

    // https://dev.twitter.com/overview/api/users
    $nome = $elem_search->user->name;
    $nick = $elem_search->user->screen_name;
    $profile_image_url = $elem_search->user->profile_image_url;
    $testo_tweet = $elem_search->text;

    if (isset($elem_search->retweeted_status)) {
        $testo_tweet = 'RT: ' . $elem_search->retweeted_status->text;
    }

    if (isset($elem_search->coordinates)) {
        $point = \GeoJson\GeoJson::jsonUnserialize($elem_search->coordinates);
    }

    // Entites https://dev.twitter.com/overview/api/entities

    $hashtags = [];
    foreach ($elem_search->entities->hashtags as $ht) {
        // lo # non c'e' se serve va messo
        $hashtags[] = $ht->text;
    }

    $ann = Annotation::on($connection_name)->where('sourceId', $id_tweet)->get();
    if ($ann->count() == 0) {

        $annotation = new Annotation(array(
            'author' => $nick,
            'source' => Source::TWITTER,
            'sourceId' => $id_tweet,
            'text' => $testo_tweet,
            'textHtml' => parseTweet($testo_tweet),
            'hashtags' => $hashtags
        ));
        $annotation->setConnection($connection_name);
        $annotation->save();

        $result = Parser::parse($testo_tweet);
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

        $imageCount = 0;
        if (isset ($elem_search->entities->media)) {
            foreach ($elem_search->entities->media as $media) {
                $imageCount++;
                $resources[] = $media->media_url;
                $dataImg = file_get_contents($media->media_url);
                $filename = basename($media->media_url); //"http://pbs.twimg.com/media/CPbPvS6UkAE7dYw.jpg",

                if ('' == trim($filename)) {
                    $url = $media->expanded_url;
                    $parts = parse_url($url); // /Nonsprecare/status/645894353769111552/photo/1
                    echo $parts['path'];
                    $str = explode('/', $parts['path']);
                    $filename = $str[4];
                }

                $filesystem->write($id_tweet . '/' . $filename, $dataImg);

                $attachments = new Attachment(array(
                    'annotation_id' => $annotation->id,
                    'source' => Source::TWITTER,
                    'fileName' => $filename,
                    'filePath' => $id_tweet . '/' . $filename
                ));
                $attachments->setConnection($connection_name);
                $attachments->save();
            }
        }

    }

}
if ($filesystem->has('last.tweet')) {
    $filesystem->delete('last.tweet');
}
$filesystem->write('last.tweet', $lastId);

