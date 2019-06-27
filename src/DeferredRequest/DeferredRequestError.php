<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 05.03.19
 */

namespace GepurIt\OneCClientBundle\DeferredRequest;

use GepurIt\OneCClientBundle\Exception\OneCSyncException;
use GepurIt\OneCClientBundle\Request\OneCRequest;

/**
 * Class DeferredRequestError
 * @package GepurIt\OneCClientBundle\DeferredRequest
 */
class DeferredRequestError
{
    /** @var bool */
    private $propagationStopped = false;

    /** @var OneCSyncException */
    private $exception;

    /** @var OneCRequest */
    private $request;

    /**
     * DeferredRequestError constructor.
     *
     * @param OneCSyncException $exception
     * @param OneCRequest       $request
     */
    public function __construct(OneCSyncException $exception, OneCRequest $request)
    {
        $this->exception = $exception;
        $this->request = $request;
    }

    /**
     * @return bool
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    /**
     * stop propagation
     */
    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }

    /**
     * @return OneCSyncException
     */
    public function getException(): OneCSyncException
    {
        return $this->exception;
    }

    /**
     * @return OneCRequest
     */
    public function getRequest(): OneCRequest
    {
        return $this->request;
    }
}
