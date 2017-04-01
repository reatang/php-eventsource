<?php
/**
 * Created by PhpStorm.
 * User: reatang
 * Date: 17/4/1
 * Time: 下午4:53
 */

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