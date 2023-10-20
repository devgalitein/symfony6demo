<?php
namespace App\Service;

use App\Entity\ChatMessage;
use Doctrine\ORM\EntityManagerInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use SplObjectStorage;
use Symfony\Component\Security\Core\Security;

class ChatServer implements MessageComponentInterface {

    protected $connections;

    public function __construct()
    {
        $this->connections = new SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->connections->attach($conn);
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->connections->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->connections->detach($conn);
        $conn->close();
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        foreach($this->connections as $connection)
        {
            if($connection === $from)
            {
                continue;
            }
            $connection->send($msg);
        }
    }


}
