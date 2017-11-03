<?php

namespace Firefly\Communication\EventSource;

class Message implements MessageInterface
{
    /**
     * @var string|null
     */
    protected $event;

    /**
     * @var string
     */
    protected $data;

    public function __construct($data, $event = null)
    {
        $this->data = $data;
        $this->event = $event;
    }

    /**
     * @return string|null
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param string|null $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}