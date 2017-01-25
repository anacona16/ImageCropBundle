<?php

namespace Anacona16\Bundle\ImageCropBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    protected $supportedPopup = array('iframe', 'popup', 'bootstrap');

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('image_crop', 'array');

        $rootNode
            ->children()
                ->scalarNode('window')
                    ->defaultValue('iframe')
                    ->validate()
                        ->ifNotInArray($this->supportedPopup)
                        ->thenInvalid('The popup %s is not supported. Please choose one of '.implode(', ', $this->supportedPopup))
                    ->end()
                ->end()
            ->end()
            ->children()
                ->integerNode('window_width')
                    ->defaultValue(700)
                ->end()
            ->end()
            ->children()
                ->integerNode('window_height')
                    ->defaultValue(500)
                ->end()
            ->end()
            ->children()
                ->integerNode('scale_step')
                    ->defaultValue(50)
                ->end()
            ->end()
            ->children()
                ->booleanNode('scale_default')
                    ->defaultValue(false)
                ->end()
            ->end()
            ->children()
                ->arrayNode('mappings')
                    ->useAttributeAsKey('entity')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('filters')
                                ->requiresAtLeastOneElement()
                                ->useAttributeAsKey('name')
                                ->prototype('variable')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->children()
                ->integerNode('orphan_maxage')
                    ->defaultValue(3600)
                ->end()
            ->end()
            ->children()
                ->scalarNode('imagine_cache_dir')
                    ->defaultValue('media/cache')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
