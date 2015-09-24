<?php
/**
 * Created by PhpStorm.
 * User: Stefano "Yoghi" Tamagnini
 * Date: 23/09/15 - 22:00
 * 
 */

use \Indaba\Dashboard\Annotation as Annotation;
use Carbon\Carbon;

function feed($connection = 'default', $limit = 0){

    $annotations = Annotation::on($connection)->where('id', '>', $limit)->orderBy('id', 'desc')->take(20)->get();

    Carbon::setToStringFormat(DateTime::ISO8601);
    $result = [];
    foreach ($annotations as $annotation) {
        $result[] = json_decode($annotation->toJson());
    }
    return json_encode($result);
}

function feedhistory($connection = 'default', $limit){

    $annotations = Annotation::on($connection)->where('id', '<', $limit)->orderBy('id', 'desc')->take(20)->get();

    Carbon::setToStringFormat(DateTime::ISO8601);
    $result = [];
    foreach ($annotations as $annotation) {
        $result[] = json_decode($annotation->toJson());
    }
    return json_encode($result);
}


