<?php

namespace Firefly\Communication\EventSource;


interface MessageInterface
{
    /**
     * @return string|null
     */
    public function getEvent();

    /**
     * @return string
     */
    public function getData();
}