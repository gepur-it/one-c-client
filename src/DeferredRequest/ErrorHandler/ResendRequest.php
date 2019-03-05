<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 05.03.19
 */

namespace GepurIt\OneCClientBundle\DeferredRequestErrorHandler;

use GepurIt\OneCClientBundle\DeferredRequest\DeferredRequestError;
use GepurIt\OneCClientBundle\DeferredRequest\ErrorHandler\ConcreteErrorHandlerInterface;
use GepurIt\OneCClientBundle\HttpClient\ApiHttpClient;
use Psr\Log\LoggerInterface;

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

    /** @var LoggerInterface */
    private $logger;

    /**
     * ResendRequest constructor.
     *
     * @param ApiHttpClient   $client
     * @param LoggerInterface $logger
     */
    public function __construct(ApiHttpClient $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
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
        $this->logger->error($error->getException()->getMessage(), [
            'request' => $error->getRequest()->toArray(),
            'exception' => $error->getException()->getTrace()
        ]);

        $this->client->queueRequestProrogued($error->getRequest());
    }
}
