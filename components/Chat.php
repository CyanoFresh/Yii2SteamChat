<?php

namespace app\components;

use app\models\Message;
use app\models\User;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Yii;
use yii\helpers\VarDumper;

class Chat implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
        Yii::$app->db->createCommand('SET SESSION wait_timeout = 28800;')->execute();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $query = $conn->WebSocket->request->getQuery();

        $steamid = $query->get('steamid');
        $time = $query->get('time');
        $token = $query->get('token');

        $user = User::findOne([
            'steamid' => $steamid,
            'auth_key' => $token,
        ]);

        if (!$user) {
            echo "Failed to login!\n";
            return;
        }

        $conn->User = $user;

        $this->clients->attach($conn);

        echo "New connection!\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $model = new Message();

        $model->message = $msg;
        $model->date = time();
        $model->steamid = $from->User->steamid;

        $model->save();

        foreach ($this->clients as $client) {
            $client->send(json_encode([
                'username' => $from->User->username,
                'steamid' => $from->User->steamid,
                'avatar' => $from->User->avatar_md,
                'profile_url' => $from->User->profile_url,
                'message' => $msg,
            ]));
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
