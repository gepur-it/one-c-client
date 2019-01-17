<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 23.11.18
 */

namespace GepurIt\OneCClientBundle\Rabbit;

use GepurIt\RabbitMqBundle\Configurator\AbstractDeadDeferredConfigurator;
use GepurIt\RabbitMqBundle\Rabbit;

/**
 * Class RequestQueue
 * @package OneCBundle\Rabbit
 */
class RequestQueue extends AbstractDeadDeferredConfigurator
{
    const QUEUE_NAME = 'one_c_request';
    const QUEUE_NAME_DEFERRED = 'one_c_request_dead';

    /** @var Rabbit */
    private $rabbit;

    /**
     * RequestQueue constructor.
     *
     * @param Rabbit $rabbit
     */
    public function __construct(Rabbit $rabbit)
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
     * @return Rabbit
     */
    public function getRabbit(): Rabbit
    {
        return $this->rabbit;
    }
}
