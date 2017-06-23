<?php

namespace Loevgaard\DandomainImageBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class LoevgaardDandomainImageExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('loevgaard_dandomain_image.image_settings',        $config['image_settings']);
        $container->setParameter('loevgaard_dandomain_image.jpeg_quality',          $config['jpeg_quality']);
        $container->setParameter('loevgaard_dandomain_image.png_compression_level', $config['png_compression_level']);
        $container->setParameter('loevgaard_dandomain_image.resolution_x',          $config['resolution_x']);
        $container->setParameter('loevgaard_dandomain_image.resolution_y',          $config['resolution_y']);
        $container->setParameter('loevgaard_dandomain_image.tinypng',               $config['tinypng']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
