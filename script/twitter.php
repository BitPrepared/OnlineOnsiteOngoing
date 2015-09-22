<?php
/**
 * Created by PhpStorm.
 * User: Stefano "Yoghi" Tamagnini
 * Date: 21/09/15 - 22:46
 *
 */

require '../vendor/autoload.php';
require '../config.php';

use Abraham\TwitterOAuth\TwitterOAuth;

$connection = new TwitterOAuth($twitter_consumer_key, $twitter_consumer_secret, $twitter_access_token, $twitter_access_secret);
$connection->setTimeouts(10, 15);
$content = $connection->get("account/verify_credentials");

file_put_contents('info.json', json_encode($content));

//$limiti = $connection->get("application/rate_limit_status", array());
//file_put_contents('limiti.json',json_encode($limiti));

$content_search = json_decode(file_get_contents('bologna.json'));

$lastId = analyzeSearch($content_search);

// 646065951641890816;
$statuses = $connection->get("search/tweets", array("q" => "#bologna", "result_type" => "recent", "count" => "100", "since_id" => "$lastId"));
$data = json_encode($statuses);
file_put_contents('bologna2.json', $data);

echo "\n";
echo '###################################### after' . " $lastId\n";
echo "\n";

analyzeSearch(json_decode($data));

// RETWEET
//$id = 646059742188732416;
//$statues = $connection->post("statuses/retweet", array("id" => $id));
//file_put_contents('retweet.json',json_encode($statues));

/**
 * @param $content_search
 * @See https://dev.twitter.com/rest/reference/get/search/tweets
 */
function analyzeSearch($content_search)
{
    $lastId = '0';
    foreach ($content_search->statuses as $elem_search) {

        // https://dev.twitter.com/overview/api/tweets

        //metadata
        //created_at
        //id
        //id_str
        //text
        //source
        //truncated
        //in_reply_to_status_id
        //in_reply_to_status_id_str
        //in_reply_to_user_id
        //in_reply_to_user_id_str
        //in_reply_to_screen_name
        //user
        //geo
        //coordinates
        //place
        //contributors
        //is_quote_status
        //retweet_count
        //favorite_count
        //entities
        //favorited
        //retweeted
        //possibly_sensitive
        //lang

        $id_tweet = $elem_search->id_str;
        $lastId = $id_tweet;

        // https://dev.twitter.com/overview/api/users
        $nome = $elem_search->user->name;
        $nick = $elem_search->user->screen_name;
        $profile_image_url = $elem_search->user->profile_image_url;
        $testo_tweet = $elem_search->text;

        if (isset($elem_search->retweeted_status)) {
            // caso retweet
            $testo_tweet = 'RT: ' . $elem_search->retweeted_status->text;
        }

        //$coordinates = $elem_search->coordinates;
        // or full:
        //$place_name = $elem_search->place->name;

        if (isset($elem_search->coordinates)) {
            $point = \GeoJson\GeoJson::jsonUnserialize($elem_search->coordinates);
        }

        // Entites https://dev.twitter.com/overview/api/entities

        $hashtags = [];
        foreach ($elem_search->entities->hashtags as $ht) {
            // lo # non c'e' se serve va messo
            $hashtags[] = $ht->text;
        }

        $resources = [];
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
                    $str = explode('/',$parts['path']);
                    $filename = $str[4];
                }

                // fixme: dove le salvo??
                file_put_contents($id_tweet . $imageCount . $filename, $dataImg);
            }
        }

        if (isset ($elem_search->entities->urls)) {
            foreach ($elem_search->entities->urls as $url) {
                $resources[] = $url->expanded_url;
            }
        }

        $sizeResources = count($resources);
        echo "$id_tweet - $testo_tweet - [ $sizeResources / $imageCount ]\n";

//    print_r($elem_search->contributors);
//    print_r($elem_search->entities);
//    break;
    }
    return $lastId;
}