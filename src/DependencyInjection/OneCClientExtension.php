<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 17.01.19
 */

namespace GepurIt\OneCClientBundle\DependencyInjection;

use Exception;
use GepurIt\OneCClientBundle\HttpClient\ApiHttpClient;
use GepurIt\OneCClientBundle\OneCClientInterface;
use GepurIt\OneCClientBundle\Security\HashGenerator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class OneCClientExtension
 * @package GepurIt\OneCClientBundle\DependencyInjection
 */
class OneCClientExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array $configs
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
        $this->initHashGenerator($config, $container);
        $this->initClient($config, $container);
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function initHashGenerator(array $config, ContainerBuilder $container)
    {
        $client = $container->findDefinition(HashGenerator::class);
        $client->setArgument('$token', $config['token']);
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function initClient(array $config, ContainerBuilder $container)
    {
        $client = $container->findDefinition(ApiHttpClient::class);
        $client->setArgument('$resource', $config['url']);
        $client->setArgument('$login', $config['login']);
        $client->setArgument('$password', $config['password']);
        $client->setArgument('$auth', $config['auth']);
        $container->setAlias(OneCClientInterface::class, ApiHttpClient::class);
    }
}
