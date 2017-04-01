<?php

namespace Firefly\Communication;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Firefly\Communication\EventSource\MessageInterface;
use Firefly\Communication\EventSource\NotAcceptException;

class EventSource
{
    const MIME_TYPE = 'text/event-stream';
    const CRLF = "\r\n";

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Ready to send the information list
     * @var array
     */
    protected $messageList = [];

    /**
     * Set next event id
     * @var string
     */
    protected $nextEventId;

    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     */
    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->response = $response;

        if (preg_match('#'.addcslashes(self::MIME_TYPE, '/').'#', $request->getHeaderLine('accept')) !== 1) {
            throw new NotAcceptException($response->withStatus(406), 'request not accept ' . $request->getHeaderLine('accept'));
        }
    }

    /**
     * Add a message
     *
     * @param MessageInterface $message
     *
     * @return $this
     */
    public function add(MessageInterface $message)
    {
        $this->messageList[spl_object_hash($message)] = $message;

        return $this;
    }

    /**
     * Remove a message
     * @param MessageInterface $message
     */
    public function remove(MessageInterface $message)
    {
        unset($this->messageList[spl_object_hash($message)]);
    }

    /**
     * Get on a last event ID
     *
     * @return bool
     */
    public function getLastEventId()
    {
        $lastEventID = $this->request->getHeaderLine('Last-Event-ID');

        if (!$lastEventID) {
            $query = [];
            parse_str($this->request->getUri()->getQuery(), $query);
            $lastEventID = isset($query["lastEventId"]) ? $query["lastEventId"] : false;
        }

        return $lastEventID ?: false;
    }

    /**
     * Set next Event ID
     * @param string $id
     *
     * @return $this
     */
    public function setNextEventId($id)
    {
        $this->nextEventId = $id;

        return $this;
    }

    /**
     * return response
     *
     * @return ResponseInterface
     */
    public function response()
    {
        $response = $this->response->withHeader('Content-Type', self::MIME_TYPE);
        $response = $response->withHeader('Transfer-Encoding',  'identity');
        $response = $response->withHeader('Cache-Control',      'no-cache');

        $response->getBody()->write($this->buildBody());

        return $response;
    }

    /**
     * Build event-stream body
     *
     * @return string
     */
    protected function buildBody()
    {
        $data = '';

        if (!is_null($this->nextEventId)) {
            $data .= $this->buildMessage($this->nextEventId, '_event_sync', $this->nextEventId);
        }

        foreach ($this->messageList as $item) {
            $data .= $this->buildMessage($item);
        }

        return $data;
    }

    /**
     * Build message
     * @param string|MessageInterface $data
     * @param string|null    $event
     * @param string|null    $id
     *
     * @return string
     */
    protected function buildMessage($data, $event = null, $id = null)
    {
        if ($data instanceof MessageInterface) {
            $event = $data->getEvent();
            $_data = $data->getData();
        } else {
            $_data = $data;
        }

        $line = '';

        //Now event ID
        if (!is_null($id)) {
            $line .= "id: " . $id . self::CRLF;
        }

        //event name
        if (!is_null($event)) {
            $line .= "event: " . $event . self::CRLF;
        }

        //data and return
        return $line . 'data: ' . $_data . self::CRLF . self::CRLF;
    }
}