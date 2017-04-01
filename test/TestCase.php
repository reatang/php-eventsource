<?php

namespace Firefly\Test\Communication\EventSource;

use Firefly\Communication\EventSource;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    public function testRequest()
    {
        $env = Environment::mock(
            ['HTTP_ACCEPT' => "text/event-stream"]
        );

        $request = Request::createFromEnvironment($env);
        $response = new Response();
        $eventSource = new EventSource($request, $response);

        $msg = new EventSource\Message('hello event source');

        $eventSource->add($msg);

        $newResponse = $eventSource->response();

        $this->assertEquals((string) $newResponse->getHeaderLine('Content-Type') , "text/event-stream");
        $this->assertEquals((string) $newResponse->getBody() , "data: hello event source\r\n\r\n");
    }

    public function testEventId()
    {
        $lastEventID = 10;

        $env = Environment::mock(
            [
                'HTTP_ACCEPT' => "text/event-stream",
                "HTTP_LAST_EVENT_ID" => $lastEventID
            ]
        );

        $request = Request::createFromEnvironment($env);
        $response = new Response();
        $eventSource = new EventSource($request, $response);

        $id = $eventSource->getLastEventId();

        $this->assertEquals($id, $lastEventID);
    }

    public function testGetEventIdForQueryString()
    {
        $lastEventID = 10;

        $env = Environment::mock(
            [
                'HTTP_ACCEPT' => "text/event-stream",
                "QUERY_STRING" => "lastEventId={$lastEventID}"
            ]
        );

        $request = Request::createFromEnvironment($env);

        $response = new Response();
        $eventSource = new EventSource($request, $response);

        $id = $eventSource->getLastEventId();

        $this->assertEquals($id, $lastEventID);
    }

    public function testSetEventId()
    {
        $lastEventID = 10;

        $env = Environment::mock(
            [
                'HTTP_ACCEPT' => "text/event-stream",
                "HTTP_LAST_EVENT_ID" => $lastEventID
            ]
        );

        $request = Request::createFromEnvironment($env);
        $response = new Response();
        $eventSource = new EventSource($request, $response);

        $nextEventID = $eventSource->getLastEventId() + 1;

        $eventSource->setNextEventId($nextEventID);

        $newResponse = $eventSource->response();

        $this->assertEquals((string) $newResponse->getHeaderLine('Content-Type') , "text/event-stream");
        $this->assertEquals((string) $newResponse->getBody() , "id: {$nextEventID}\r\nevent: _event_sync\r\ndata: {$nextEventID}\r\n\r\n");
    }
}