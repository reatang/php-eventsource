<?php
require_once '../vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Firefly\Communication\EventSource;
use Firefly\Communication\EventSource\Message;
use Firefly\Communication\EventSource\NotAcceptException;

$config = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$app = new \Slim\App($config);

$app->get('/', function (Request $request, Response $response) {
    return $response->getBody()->write(file_get_contents('index.tpl'));
});

$app->get('/server', function (Request $request, Response $response) {
    try {
        $eventSource = new EventSource($request, $response);
    } catch (NotAcceptException $e) {
        return $e->getResponse();
    }

    $eventSource->setNextEventId((int) $eventSource->getLastEventId() + 1);

    $msg = new Message('a simple message');
    $msg2 = new Message(date('c'), 'some_event_name');

    $eventSource->add($msg);
    $eventSource->add($msg2);

    return $eventSource->response();
});

$app->run();