<?php

namespace Loevgaard\DandomainImageBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('loevgaard_dandomain_image');

        $rootNode
            ->children()
                ->arrayNode('image_settings')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->integerNode('width')->end()
                            ->integerNode('height')->defaultValue(null)->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->integerNode('jpeg_quality')->defaultValue(100)->end()
            ->integerNode('png_compression_level')->defaultValue(0)->end()
            ->integerNode('resolution_x')->defaultValue(72)->end()
            ->integerNode('resolution_y')->defaultValue(72)->end()
        ;

        return $treeBuilder;
    }
}
