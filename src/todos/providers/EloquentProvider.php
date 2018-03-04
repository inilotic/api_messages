<?php

namespace Todos\Providers;

use Pimple\ServiceProviderInterface;
use Illuminate\Database\Capsule\Manager as Capsule;
use Silex\Api\BootableProviderInterface;
use Silex\Application;

class EloquentProvider implements ServiceProviderInterface, BootableProviderInterface
{

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param \Pimple\Container $pimple A container instance
     */
    public function register(\Pimple\Container $app)
    {
        $app['db'] = new Capsule();
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     *
     * @param Application $app
     */
    public function boot(Application $app)
    {
        if (!isset($app['dbs.options'])) {
            return;
        }
        foreach ($app['dbs.options'] as $cn => $cdata) {
            if (strtolower($cn) === 'mysql_read') {
                $app['db']->addConnection([
                    "driver" => $cdata['driver'],
                    "host" => $cdata['host'],
                    "database" => $cdata['dbname'],
                    "username" => $cdata['user'],
                    "password" => $cdata['password'],
                    "charset" => $cdata['charset']
                ]);
            }

            $app['db']->addConnection([
                "driver" => $cdata['driver'],
                "host" => $cdata['host'],
                "database" => $cdata['dbname'],
                "username" => $cdata['user'],
                "password" => $cdata['password'],
                "charset" => $cdata['charset']
            ], $cn);

        }

        $app['db']->bootEloquent();

    }
}