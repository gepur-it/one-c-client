<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 16.09.19
 */
declare(strict_types=1);

namespace GepurIt\OneCClientBundle\Event;

use GepurIt\OneCClientBundle\Exception\OneCSyncException;
use GepurIt\OneCClientBundle\Request\OneCRequest;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class RequestSentErrorEvent
 * @package GepurIt\OneCClientBundle\Event
 */
final class RequestSentErrorEvent extends Event
{
    private OneCRequest $request;
    private OneCSyncException $exception;

    /**
     * RequestSentEvent constructor.
     *
     * @param OneCRequest $request
     * @param OneCSyncException $exception
     */
    public function __construct(OneCRequest $request, OneCSyncException $exception)
    {
        $this->request = $request;
        $this->exception = $exception;
    }

    /**
     * @return OneCRequest
     */
    public function getRequest(): OneCRequest
    {
        return $this->request;
    }

    /**
     * @return OneCSyncException
     */
    public function getException(): OneCSyncException
    {
        return $this->exception;
    }
}