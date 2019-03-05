<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 05.03.19
 */

namespace GepurIt\OneCClientBundle\Rabbit;


/**
 * Class RequestDeferredQueue
 * @package GepurIt\OneCClientBundle\Rabbit
 */
class RequestDeferredQueue extends RequestQueue
{
    /**
     * @param string      $message
     * @param null|string $routingKey
     *
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     * @throws \AMQPQueueException
     */
    public function publish(string $message, ?string $routingKey = null)
    {
        $routingKey = $routingKey??$this->getDeferred();
        $this->getExchange()->publish($message, $routingKey);
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
