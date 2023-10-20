<?php

namespace App\Command;

use App\Service\ChatServer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Security\Core\Security;

class StartWebSocketServerCommand extends Command {

    protected static $defaultName = 'app:start-websocket-server';

    protected function configure() {
        $this->setDescription('Start the WebSocket server');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $port = 3001;
        $output->writeln("Starting server on port " . $port);
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new ChatServer()
                )
            ),
            $port
        );
        $server->run();
        return 0;
    }
}