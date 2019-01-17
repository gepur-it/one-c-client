<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 17.01.19
 */

namespace GepurIt\OneCClientBundle\Event;

use GepurIt\OneCClientBundle\Request\OneCRequest;

/**
 * Class RequestSentEvent
 * @package GepurIt\OneCClientBundle\Event
 */
class RequestSentEvent
{
    const NAME ='one.c.request_sent';

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
