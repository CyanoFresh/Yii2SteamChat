<?php

namespace app\commands;

use Ratchet\Server\IoServer;
use app\components\Chat;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use yii\console\Controller;

class SocketController extends Controller
{
    public function actionRun()
    {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Chat()
                )
            ),
            8080
        );

        echo 'Server started' . "\n";

        $server->run();
    }
}
