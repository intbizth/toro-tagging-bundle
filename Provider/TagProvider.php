<?php

namespace Toro\Bundle\TaggingBundle\Provider;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Toro\Bundle\TaggingBundle\Model\TagInterface;

class TagProvider
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var MetadataInterface
     */
    private $metadata;
    
    /**
     * @var RepositoryInterface
     */
    private $repository;

    public function __construct(FactoryInterface $factory, ObjectManager $manager, MetadataInterface $metadata)
    {
        $this->factory = $factory;
        $this->manager = $manager;
        $this->metadata = $metadata;
        $this->repository = $manager->getRepository($metadata->getClass('model'));
    }

    public function findOrCreate($name)
    {
        $name = strtolower(trim($name));

        /** @var TagInterface $tag */
        if (!$tag = $this->repository->findOneBy(['name' => $name])) {

            $tag = $this->factory->createNew();
            $tag->setName($name);

            $this->manager->persist($tag);
            $this->manager->flush($tag);
        }

        return $tag;
    }
    
}
