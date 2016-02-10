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
    protected $supportedPopup = array('iframe', 'popup', 'bootstrap', 'colorbox');

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('image_crop', 'array');

        $rootNode
            ->children()
                ->scalarNode('popup')
                    ->defaultValue('iframe')
                    ->validate()
                        ->ifNotInArray($this->supportedPopup)
                        ->thenInvalid('The popup %s is not supported. Please choose one of '.implode(', ', $this->supportedPopup))
                    ->end()
                ->end()
            ->end()
            ->children()
                ->integerNode('popup_width')
                    ->defaultValue(500)
                ->end()
                ->integerNode('popup_height')
                    ->defaultValue(500)
                ->end()
            ->end()
            ->children()
                ->integerNode('scale_step')
                    ->defaultValue(50)
                ->end()
            ->end()
            ->children()
                ->arrayNode('mappings')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->variableNode('uri_prefix')->cannotBeEmpty()->end()
                            ->scalarNode('liip_imagine_filter')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
