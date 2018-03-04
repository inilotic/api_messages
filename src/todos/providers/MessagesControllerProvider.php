<?php
/**
 * Created by PhpStorm.
 * User: inilotic
 * Date: 05.03.2018
 * Time: 0:08
 */

namespace Todos\Providers;


use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\DateTimeValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Required;
use Todos\Models\Message;

class MessagesControllerProvider implements ControllerProviderInterface
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

        $controllers->post('/receive/{id}', function (Request $request) use ($app) {

            $since = $request->get("since");

            $errors = $app['validator']->validate($since, [
                new NotBlank(),
                new Required(),
                new DateTime()
            ]);

            if (count($errors) > 0) {
                return new Response("Bad since param must be valid date", Response::HTTP_BAD_REQUEST);
            }

            $sinceDate = new \DateTime($since);

            $message = Message::where('receiver_id', $request->attributes->get('userid'))
                ->where("created_at", ">", $sinceDate->format("Y-m-d H:i:s"))
                ->take(1000)
                ->with("sender")
                ->get();

            $payload = [];

            foreach ($message as $msg) {
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