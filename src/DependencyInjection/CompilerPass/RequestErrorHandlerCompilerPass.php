<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 05.03.19
 */

namespace GepurIt\OneCClientBundle\DependencyInjection\CompilerPass;

use GepurIt\OneCClientBundle\DeferredRequest\ErrorHandler\ConcreteErrorHandlerInterface;
use GepurIt\OneCClientBundle\DeferredRequest\ErrorHandler\DeferredRequestErrorHandler;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class RequestErrorHandlerCompilerPass
 * @package GepurIt\OneCClientBundle\DependencyInjection\CompilerPass
 */
class RequestErrorHandlerCompilerPass implements CompilerPassInterface
{
    const SERVICE_TAG = 'one_c.dr_error.handler';

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $baseHandler = $container->findDefinition(DeferredRequestErrorHandler::class);
        $taggedServices = $container->findTaggedServiceIds(self::SERVICE_TAG);
        foreach ($taggedServices as $key => $tags) {
            $errorHandler = $container->getDefinition($key);
            if (!in_array(ConcreteErrorHandlerInterface::class, class_implements($errorHandler->getClass()))) {
                $message = sprintf(
                    "%s should implement %s to register as ErrorHandler",
                    $errorHandler->getClass(),
                    ConcreteErrorHandlerInterface::class
                );
                throw new InvalidConfigurationException($message);
            }
            foreach ($tags as $tag) {
                $priority = $tag['priority']??0;
                $baseHandler->addMethodCall('addHandler', [$errorHandler, $priority]);
            }
        }
    }
}
