<?php
/**
 * Created by PhpStorm.
 * User: Stefano "Yoghi" Tamagnini
 * Date: 23/09/15 - 21:32
 *
 */

function setup($config)
{

    foreach ($config['databases'] as $name => $database) {

        if ( $database['driver'] == 'sqlite' ){
            $dbh = new PDO('sqlite:'.$database['database']);
            $dbh = null;
        }

        if (!Illuminate\Database\Capsule\Manager::schema($name)->hasTable('annotations')) {

            Illuminate\Database\Capsule\Manager::schema($name)->create('annotations', function ($table) {
                $table->increments('id');
                $table->string('author');
                $table->string('source');
                $table->string('sourceId');
                $table->text('text');
                $table->text('textHtml');
                $table->string('hashtags');

                $table->softDeletes();
                $table->timestamps();
            });

            echo "<h3>$name - Annotations table created</h3>";

        } else {
            echo "<h3>$name - Annotations table ok</h3>";
        }

        if (!Illuminate\Database\Capsule\Manager::schema($name)->hasTable('attachments')) {

            Illuminate\Database\Capsule\Manager::schema($name)->create('attachments', function ($table) {
                $table->increments('id');
                $table->integer('annotation_id');
                $table->string('source');
                $table->string('fileName');
                $table->string('filePath')->unique();

                $table->softDeletes();
                $table->timestamps();
            });

            echo "<h3>$name - Attachments table created</h3>";

        } else {
            echo "<h3>$name - Attachments table ok</h3>";
        }

    }

}