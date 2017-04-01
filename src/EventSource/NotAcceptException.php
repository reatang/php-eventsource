<?php
/**
 * Created by PhpStorm.
 * User: reatang
 * Date: 17/3/31
 * Time: 下午6:03
 */

namespace Firefly\Communication\EventSource;

use Throwable;
use Psr\Http\Message\ResponseInterface;

class NotAcceptException extends \LogicException
{
    protected $response;

    public function __construct(ResponseInterface $response, $message = "", $code = 406, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }
}