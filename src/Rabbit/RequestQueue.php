<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 23.11.18
 */

namespace GepurIt\OneCClientBundle\Rabbit;

use GepurIt\RabbitMqBundle\Configurator\AbstractDeadDeferredConfigurator;
use GepurIt\RabbitMqBundle\RabbitInterface;

/**
 * Class RequestQueue
 * @package OneCBundle\Rabbit
 */
class RequestQueue extends AbstractDeadDeferredConfigurator
{
    const QUEUE_NAME = 'one_c_request';
    const QUEUE_NAME_DEFERRED = 'one_c_request_dead';

    /** @var RabbitInterface */
    private $rabbit;

    /**
     * RequestQueue constructor.
     *
     * @param RabbitInterface $rabbit
     */
    public function __construct(RabbitInterface $rabbit)
    {
        $this->rabbit = $rabbit;
    }

    /**
     * @return string
     */
    public function getDeferred(): string
    {
        return self::QUEUE_NAME_DEFERRED;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::QUEUE_NAME;
    }

    /**
     * @return int
     */
    public function getTtl(): int
    {
        return 300000;
    }

    /**
     * @return RabbitInterface
     */
    public function getRabbit(): RabbitInterface
    {
        return $this->rabbit;
    }
}
