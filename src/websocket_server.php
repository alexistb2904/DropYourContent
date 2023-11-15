<?php
require '../vendor/autoload.php';
require 'functions.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use WebSocketProject\Chat;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    5620
);

echo "Serveur WebSocket démarré sur le port 5620...\n";

$server->run();
