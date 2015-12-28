<?php

namespace app\commands;

use Ratchet\Server\IoServer;
use app\components\Chat;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use yii\console\Controller;

class ServerController extends Controller
{
    public function actionIndex($port = 8080)
    {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Chat()
                )
            ),
            $port
        );

        echo 'Server running on port: ' . $port . "\n";

        $server->run();
    }
}
