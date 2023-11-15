<?php
namespace WebSocketProject;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        /*$messages = getMessage();
        foreach ($messages as $messagePDO) {
            $message = $messagePDO;
            $conn->send(json_encode($message));
        }*/
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {   
        $patternfirst = '/{{%@(.*?)@%}}/';
        preg_match($patternfirst, $msg, $first);
        $patternsecond = '/{{@%(.*?)%@}}/';
        preg_match($patternsecond, $msg, $second);
        if (!empty($first) && !empty($second != "")) {
            $query = $GLOBALS['mysqlClientPDO']->prepare('UPDATE user SET user_connection_id = :id WHERE user_name = :username');
            $query->execute([
                'id' => $from->resourceId,
                'username' => $first[1]
            ]);
            $messages = getMessage($first[1], $second[1]);
            foreach ($messages as $messagePDO) {
                $message = $messagePDO;
                $from->send(json_encode($message));
            }
        } else {
            $patternpseudo = '/{#%(.*?)%#}/';
            preg_match($patternpseudo, $msg, $sender);
            $patternto = '/{%#(.*?)#%}/';
            preg_match($patternto, $msg, $to);
            $senderid = getId($sender[1]);
            $toid = getId($to[1]); 
            echo "Message from {$senderid[0]["user_connection_id"]} to {$toid[0]["user_connection_id"]}\n";
            $msg = preg_replace($patternpseudo, '', $msg);
            $msg = preg_replace($patternto, '', $msg);
            $id = newMessage($msg, $sender[1], $to[1]);
            foreach ($this->clients as $client) {
                echo "Client ID : {$client->resourceId}\n";
                if ($client->resourceId == $toid[0]["user_connection_id"]) {
                    $client->send(json_encode(getMessageById($id)[0]));
                }
            }
            $from->send(json_encode(getMessageById($id)[0]));
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
        $query = $GLOBALS['mysqlClientPDO']->prepare('UPDATE user SET user_connection_id = -1 WHERE user_connection_id = :id');
        $query->execute([
            'id' => $conn->resourceId,
        ]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
        $query = $GLOBALS['mysqlClientPDO']->prepare('UPDATE user SET user_connection_id = -1');
        $query->execute([
            'id' => $conn->resourceId,
        ]);
    }
}
