<?php
namespace App\Service;

use App\Entity\ChatMessage;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use SplObjectStorage;

class ChatServer implements MessageComponentInterface {

    protected $connections;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->connections = new SplObjectStorage;
        $this->entityManager = $entityManager;
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
        $logger = new Logger('websocket');
        try {
            $data = json_decode($msg, true);
            if (isset($data['type']) && $data['type'] === 'typing') {
                $userId = $data['user_id'];
                $user_name = $data['name'];
                // Broadcast the typing event to other connected users
                foreach($this->connections as $connection)
                {
                    if($connection !== $from)
                    {
                        $connection->send(json_encode([
                            'type' => 'typing',
                            'resourceId' => $from->resourceId,
                            'user_id' => $userId,
                            'user_name' => $user_name,
                            'status' => $data['status'],
                        ]));
                    }
                }
            } else {
                // Parse and process the message data
                $userId = $data['user_id'];
                $message = $data['message'];
                // Save the data to the database using Doctrine ORM, for example
                $entityManager = $this->entityManager;
                $user = $entityManager->getRepository(User::class)->find($userId);
                if ($user) {
                    $messageEntity = new ChatMessage();
                    $messageEntity->setUser($user);
                    $messageEntity->setMessage($message);
                    $entityManager->persist($messageEntity);
                    $entityManager->flush();
                }
            }
        } catch (\Exception $e) {
            // Log the error
            $logger->error('WebSocket error: ' . $e->getMessage());
            // Handle the error, e.g., notify the user
        }

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
