<?php

require 'vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;
use Todos\Middleware\Authentication as TodoAuth;
use Todos\Providers\EloquentProvider;
use Todos\Providers\MessagesControllerProvider;

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new EloquentProvider(), array(
    'dbs.options' => array (
        'mysql_read' => array(
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'dbname'    => 'testapi',
            'user'      => 'root',
            'password'  => '',
            'charset'   => 'utf8mb4',
        ),
        'mysql_write' => array(
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'dbname'    => 'testapi',
            'user'      => 'root',
            'password'  => '',
            'charset'   => 'utf8mb4',
        ),
    ),
));
$app->register(new Silex\Provider\ValidatorServiceProvider());

$app->before(function (Request $request, $app) {
    TodoAuth::authenticate($request, $app);
});


$app->mount('/messages', new MessagesControllerProvider());


$app->run();