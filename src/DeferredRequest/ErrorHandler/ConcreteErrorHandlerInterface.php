<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 05.03.19
 */

namespace GepurIt\OneCClientBundle\DeferredRequest\ErrorHandler;

use GepurIt\OneCClientBundle\DeferredRequest\DeferredRequestError;

/**
 * Class ConcreteErrorHandlerInterface
 * @package GepurIt\OneCClientBundle\DeferredRequest\ErrorHandler
 */
interface ConcreteErrorHandlerInterface
{
    /**
     * @param DeferredRequestError $error
     *
     * @return bool
     */
    public function accepts(DeferredRequestError $error): bool;

    /**
     * @param DeferredRequestError $error
     *
     * @return void
     */
    public function handle(DeferredRequestError $error): void;
}
