<?php
/**
 * Created by PhpStorm.
 * User: Stefano "Yoghi" Tamagnini
 * Date: 23/09/15 - 21:32
 *
 */

require '../vendor/autoload.php';

$config = require('../config.php');

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

foreach($config['databases'] as $name => $database) {
    $capsule->addConnection($database, $name);
}

$capsule->setAsGlobal();
$capsule->bootEloquent();

foreach($config['databases'] as $name => $database) {

    if (!Capsule::schema($name)->hasTable('annotations')) {

        Capsule::schema($name)->create('annotations', function ($table) {
            $table->increments('id');
            $table->string('author');
            $table->string('source');
            $table->string('text');
            $table->string('textHtml');
            $table->string('hashtags');

            $table->softDeletes();
            $table->timestamps();
        });

    }

    if (!Capsule::schema($name)->hasTable('attachments')) {

        Capsule::schema($name)->create('attachments', function ($table) {
            $table->increments('id');
            $table->integer('annotation_id');
            $table->string('source');
            $table->string('fileName');
            $table->string('filePath')->unique();

            $table->softDeletes();
            $table->timestamps();
        });

    }

}