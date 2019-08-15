<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 17.01.19
 */

namespace GepurIt\OneCClientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package GepurIt\OneCClientBundle\DependencyInjection
 */
class Configuration  implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('one_c_client');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->scalarNode('url')
                    ->cannotBeEmpty()
                    ->isRequired()
                    ->defaultValue('http://example.com/api/')
                ->end()
                ->scalarNode('token')
                   ->cannotBeEmpty()
                   ->isRequired()
                   ->defaultValue('1c_access_token_here')
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
