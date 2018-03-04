<?php
/**
 * Created by PhpStorm.
 * User: inilotic
 * Date: 05.03.2018
 * Time: 0:08
 */

namespace Todos\Providers;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Todos\Models\Message;

class MessagesProvider implements ControllerProviderInterface
{


    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->post('/receive/{id}', function (Request $request) {

            $message = Message::where('receiver_id', $request->attributes->get('userid'))
                ->where("created_at", ">", "")
                ->take(1000)
                ->with("sender")
                ->get();

            $payload = [];

            foreach($message as $msg) {
                $payload[$msg->id] = [
                    'body' => $msg->body,
                    'sender' => $msg->sender,
                    'created_at' => $msg->created_at
                ];
            }
            return new JsonResponse(
                $payload,
                JsonResponse::HTTP_OK,
                ['Content-Type' => 'application/json']
            );

        })->assert('id', '\d+');

        return $controllers;
    }
}