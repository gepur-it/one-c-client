<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 17.01.19
 */

namespace GepurIt\OneCClientBundle\Event;

use GepurIt\OneCClientBundle\Request\OneCRequest;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class RequestSentEvent
 * @package GepurIt\OneCClientBundle\Event
 */
class RequestSentEvent extends Event
{
    /**
     * @deprecated
     */
    const NAME = self::class;

    /**
     * @var OneCRequest
     */
    private $request;

    /**
     * RequestSentEvent constructor.
     *
     * @param OneCRequest $request
     */
    public function __construct(OneCRequest $request)
    {
        $this->request = $request;
    }
}
