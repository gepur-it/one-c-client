<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 05.03.19
 */

namespace GepurIt\OneCClientBundle\DeferredRequestErrorHandler;

use GepurIt\OneCClientBundle\DeferredRequest\DeferredRequestError;
use GepurIt\OneCClientBundle\DeferredRequest\ErrorHandler\ConcreteErrorHandlerInterface;
use GepurIt\OneCClientBundle\HttpClient\ApiHttpClient;

/**
 * Class ResendRequest
 * @package GepurIt\OneCClientBundle\DefferedRequest\ErrorHandler
 */
class ResendRequest implements ConcreteErrorHandlerInterface
{
    /**
     * @var ApiHttpClient
     */
    private $client;

    /**
     * ResendRequest constructor.
     *
     * @param ApiHttpClient $client
     */
    public function __construct(ApiHttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param DeferredRequestError $error
     *
     * @return bool
     */
    public function accepts(DeferredRequestError $error): bool
    {
        return true;
    }

    /**
     * @param DeferredRequestError $error
     *
     * @return void
     */
    public function handle(DeferredRequestError $error): void
    {
        $this->client->queueRequestProrogued($error->getRequest());
    }
}
