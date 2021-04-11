<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 05.03.19
 */
declare(strict_types=1);

namespace GepurIt\OneCClientBundle\Rabbit;

/**
 * Class RequestDeferredQueue
 * @package GepurIt\OneCClientBundle\Rabbit
 */
class RequestDeferredQueue extends RequestQueue
{
    /**
     * @param string      $message
     * @param string|null $routingKey
     * @param int         $flags
     * @param array       $attributes
     *
     * @return bool
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     * @throws \AMQPQueueException
     */
    public function publish(string $message, ?string $routingKey = null, int $flags = AMQP_NOPARAM, array $attributes = []): bool
    {
        $routingKey = $routingKey??$this->getDeferred();
        return $this->getExchange()->publish($message, $routingKey);
    }


    /**
     * @return \AMQPExchange
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     * @throws \AMQPQueueException
     */
    public function getExchange() :\AMQPExchange
    {
        parent::getExchange();

        $channel = $this->getRabbit()->getChannel();
        $deferredExchange = new \AMQPExchange($channel);
        $deferredExchange->setName($this->getDeferred());

        return $deferredExchange;
    }

}
