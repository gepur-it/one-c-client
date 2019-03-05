<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 17.01.19
 */

namespace GepurIt\OneCClientBundle;

use GepurIt\OneCClientBundle\DependencyInjection\CompilerPass\RequestErrorHandlerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class OneCClientBundle
 * @package GepurIt\OneCClientBundle
 */
class OneCClientBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RequestErrorHandlerCompilerPass());
    }
}
