<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 23.11.18
 */

namespace GepurIt\OneCClientBundle\Command;

use GepurIt\OneCClientBundle\DeferredRequest\DeferredRequestError;
use GepurIt\OneCClientBundle\DeferredRequest\ErrorHandler\DeferredRequestErrorHandler;
use GepurIt\OneCClientBundle\Exception\OneCSyncException;
use GepurIt\OneCClientBundle\HttpClient\ApiHttpClient;
use GepurIt\OneCClientBundle\Rabbit\RequestQueue;
use GepurIt\OneCClientBundle\Request\OneCRequest;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SendQueuedRequests
 * @package OneCBundle\Command
 */
class SendQueuedRequests extends Command
{
    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var RequestQueue */
    private $queue;

    /** @var ApiHttpClient */
    private $httpClient;

    /** @var LoggerInterface */
    private $logger;

    /** @var DeferredRequestErrorHandler */
    private $errorHandler;

    /**
     * SendQueuedRequests constructor.
     *
     * @param RequestQueue                $queue
     * @param ApiHttpClient               $httpClient
     * @param LoggerInterface             $logger
     * @param DeferredRequestErrorHandler $errorHandler
     */
    public function __construct(
        RequestQueue $queue,
        ApiHttpClient $httpClient,
        LoggerInterface $logger,
        DeferredRequestErrorHandler $errorHandler
    ) {
        $this->queue        = $queue;
        $this->httpClient   = $httpClient;
        $this->logger       = $logger;
        $this->errorHandler = $errorHandler;

        parent::__construct(null);
    }

    /**
     * @used-by execute
     *
     * @param \AMQPEnvelope $envelope
     * @param \AMQPQueue    $queue
     *
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \Exception
     */
    public function processEnvelope(\AMQPEnvelope $envelope, \AMQPQueue $queue): void
    {
        $message = $envelope->getBody();

        $requestData = json_decode($message, true);
        $supportData = $requestData['data']??[];
        $request = new OneCRequest($requestData['route'], $requestData['method'], $requestData['data'], $supportData);

        /** @var OneCRequest $request */
        try {
            $this->httpClient->sendRequest($request);
        } catch (OneCSyncException $exception) {
            $this->errorHandler->handle(new DeferredRequestError($exception, $request));
        }

        $queue->ack($envelope->getDeliveryTag());
        $this->queue->getRabbit()->flush();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('one-c:queue:request')
            ->setDescription('Send requests to OneC from queue');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPQueueException
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->input  = $input;
        $this->output = $output;
        $queue        = $this->queue->getQueue();
        $queue->consume([$this, 'processEnvelope']);
    }
}
