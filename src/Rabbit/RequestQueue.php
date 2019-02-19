<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 23.11.18
 */

namespace GepurIt\OneCClientBundle\Rabbit;

use GepurIt\RabbitMqBundle\Configurator\SimpleDeadDeferredConfigurator;
use GepurIt\RabbitMqBundle\RabbitInterface;

/**
 * Class RequestQueue
 * @package OneCBundle\Rabbit
 */
class RequestQueue extends SimpleDeadDeferredConfigurator
{
    const QUEUE_NAME          = 'one_c_request';
    const QUEUE_NAME_DEFERRED = 'one_c_request_dead';
    const TTL                 = 300000;

    /**
     * RequestQueue constructor.
     *
     * @param RabbitInterface $rabbit
     */
    public function __construct(RabbitInterface $rabbit)
    {
        parent::__construct($rabbit, self::QUEUE_NAME, self::QUEUE_NAME_DEFERRED, self::TTL);
    }
}
