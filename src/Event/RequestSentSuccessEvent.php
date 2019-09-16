<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 17.01.19
 */

namespace GepurIt\OneCClientBundle\Event;

use GepurIt\OneCClientBundle\HttpClient\OneCResponse;
use GepurIt\OneCClientBundle\Request\OneCRequest;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class RequestSentEvent
 * @package GepurIt\OneCClientBundle\Event
 */
class RequestSentSuccessEvent extends Event
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
     * @var OneCResponse
     */
    private $result;

    /**
     * RequestSentEvent constructor.
     *
     * @param OneCRequest $request
     * @param OneCResponse $result
     */
    public function __construct(OneCRequest $request, OneCResponse $result)
    {
        $this->request = $request;
        $this->result = $result;
    }

    /**
     * @return OneCRequest
     */
    public function getRequest(): OneCRequest
    {
        return $this->request;
    }
}
