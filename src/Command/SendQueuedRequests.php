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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SendQueuedRequests
 * @package OneCBundle\Command
 */
class SendQueuedRequests extends Command
{
    private InputInterface $input;
    private OutputInterface $output;
    private RequestQueue $queue;
    private ApiHttpClient $httpClient;
    private DeferredRequestErrorHandler $errorHandler;
    private int $retryingLimit;

    public function __construct(
        RequestQueue $queue,
        ApiHttpClient $httpClient,
        DeferredRequestErrorHandler $errorHandler,
        int $retryingLimit = 0
    ) {
        $this->queue        = $queue;
        $this->httpClient   = $httpClient;
        $this->errorHandler = $errorHandler;
        $this->retryingLimit = $retryingLimit;

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
        $supportData = $requestData['supportData']??[];
        $request = new OneCRequest($requestData['route'], $requestData['method'], $requestData['data'], $supportData);

        /** @var OneCRequest $request */
        try {
            $this->httpClient->sendRequest($request);
        } catch (OneCSyncException $exception) {
            $currentTrying = $envelope->getHeader('x-death')[0]['count'] ?? null;

            if ($currentTrying <= $this->retryingLimit || 0 === $this->retryingLimit) {
                $this->errorHandler->handle(new DeferredRequestError($exception, $request));
                $queue->nack($envelope->getDeliveryTag());
            } else {
                $queue->ack($envelope->getDeliveryTag());
            }
            return;
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
