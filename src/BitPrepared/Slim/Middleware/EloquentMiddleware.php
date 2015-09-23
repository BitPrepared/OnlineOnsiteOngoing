<?php
/**
 * Created by PhpStorm.
 * User: Stefano "Yoghi" Tamagnini
 * Date: 23/09/15 - 20:10
 * 
 */

namespace BitPrepared\Slim\Middleware;

use Slim\Middleware;
use Illuminate\Database\Capsule\Manager as Capsule;

class EloquentMiddleware extends Middleware {

    private function setupCapsule($databases) {
        $capsule = new Capsule;
        foreach($databases as $name => $database) {
            $capsule->addConnection($database, $name);
        }
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        return $capsule;
    }

    public function call() {
        $app       = $this->app;
        $databases = $app->config('databases');
        $capsule   = $this->setupCapsule($databases);
        $app->container->singleton('db', function() use ($capsule) {
            return $capsule;
        });
        $this->next->call();

    }


}