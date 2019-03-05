<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 05.03.19
 */

namespace GepurIt\OneCClientBundle\DeferredRequest\ErrorHandler;

use GepurIt\OneCClientBundle\DeferredRequest\DeferredRequestError;

/**
 * Class DeferredRequestErrorHandler
 * @package GepurIt\OneCClientBundle\DeferredRequest\ErrorHandler
 */
class DeferredRequestErrorHandler
{
    /** @var array */
    private $handlers;

    public function addHandler(ConcreteErrorHandlerInterface $errorHandler, int $priority = 0)
    {
        $this->handlers[$priority][]=$errorHandler;
    }

    public function handle(DeferredRequestError $error)
    {
        foreach ($this->handlers as $priorities) {
            /** @var ConcreteErrorHandlerInterface $errorHandler */
            foreach ($priorities as $errorHandler) {
                if ($error->isPropagationStopped()) {
                    return;
                }
                if (!$errorHandler->accepts($error)) {
                    continue;
                }
                $errorHandler->handle($error);
            }
        }
    }
}
