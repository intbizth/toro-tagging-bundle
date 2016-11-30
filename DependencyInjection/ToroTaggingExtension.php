<?php

namespace Toro\Bundle\TaggingBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Component\Resource\Metadata\Metadata;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Toro\Bundle\TaggingBundle\ToroTaggingBundle;

class ToroTaggingExtension extends AbstractResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($config, $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.xml');

        $appName = ToroTaggingBundle::APPLICATION_NAME;
        $this->registerResources($appName, $config['driver'], $this->resolveResources($config['resources'], $container), $container);

        foreach ($config['resources'] as $subjectName => $subjectConfig) {
            foreach ($subjectConfig as $resourceName => $resourceConfig) {
                if (!is_array($resourceConfig)) {
                    continue;
                }

                $alias = $appName.'.'.$subjectName.'_'.$resourceName;
                $metadata = Metadata::fromAliasAndConfiguration($alias, array_merge($resourceConfig, array('driver' => $config['driver'])));

                $provider = new Definition($metadata->getClass('provider'));
                $provider->setArguments(array(
                    new Reference($metadata->getServiceId('factory')),
                    new Reference($metadata->getServiceId('manager')),
                    $this->getMetadataDefinition($metadata)
                ));

                $container->setDefinition(sprintf('%s.provider.%s_%s', $appName, $subjectName, $resourceName), $provider);
            }
        }
    }

    protected function getMetadataDefinition(MetadataInterface $metadata)
    {
        $definition = new Definition(Metadata::class);
        $definition
            ->setFactory([new Reference('sylius.resource_registry'), 'get'])
            ->setArguments([$metadata->getAlias()])
        ;

        return $definition;
    }

    /**
     * @param array $resources
     * @param ContainerBuilder $container
     *
     * @return array
     */
    private function resolveResources(array $resources, ContainerBuilder $container)
    {
        $subjects = [];
        $resolvedResources = [];

        foreach ($resources as $subject => $parameters) {
            $subjects[$subject] = $parameters;
        }

        $container->setParameter(ToroTaggingBundle::APPLICATION_NAME . '.tag.subjects', $subjects);

        foreach ($resources as $subjectName => $subjectConfig) {
            foreach ($subjectConfig as $resourceName => $resourceConfig) {
                if (is_array($resourceConfig)) {
                    $resolvedResources[$subjectName.'_'.$resourceName] = $resourceConfig;
                    $resolvedResources[$subjectName.'_'.$resourceName]['subject'] = $subjectName;
                }
            }
        }

        return $resolvedResources;
    }
}
