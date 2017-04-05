<?php

namespace Firefly\Communication\EventSource;


interface MessageInterface
{
    /**
     * @return string
     */
    public function getEvent();

    /**
     * @return string
     */
    public function getData();
}