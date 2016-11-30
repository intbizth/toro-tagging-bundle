<?php

namespace Toro\Bundle\TaggingBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Toro\Bundle\TaggingBundle\Doctrine\ORM\TagRepository;
use Toro\Bundle\TaggingBundle\Model\Tag;
use Toro\Bundle\TaggingBundle\Model\TagInterface;
use Toro\Bundle\TaggingBundle\Provider\TagProvider;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('toro_tagging');

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end()
        ;

        $this->addResourcesSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addResourcesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('subject')->isRequired()->cannotBeEmpty()->end()
                            ->arrayNode('tag')
                                ->isRequired()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->arrayNode('classes')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('model')->defaultValue(Tag::class)->cannotBeEmpty()->end()
                                            ->scalarNode('interface')->defaultValue(TagInterface::class)->cannotBeEmpty()->end()
                                            ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                            ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                            ->scalarNode('repository')->defaultValue(TagRepository::class)->cannotBeEmpty()->end()
                                            ->scalarNode('provider')->defaultValue(TagProvider::class)->cannotBeEmpty()->end()
                                            ->scalarNode('form')->isRequired()->cannotBeEmpty()->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
