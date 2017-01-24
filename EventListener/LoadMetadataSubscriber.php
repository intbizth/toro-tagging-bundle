<?php

namespace Toro\Bundle\TaggingBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Toro\Bundle\TaggingBundle\Model\Tag;
use Toro\Bundle\TaggingBundle\ToroTaggingBundle;

class LoadMetadataSubscriber implements EventSubscriber
{
    /**
     * @var array
     */
    protected $subjects;

    /**
     * @param array $subjects
     */
    public function __construct(array $subjects)
    {
        $this->subjects = $subjects;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'loadClassMetadata',
        ];
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadata $metadata */
        $metadata = $eventArgs->getClassMetadata();
        $metadataFactory = $eventArgs->getEntityManager()->getMetadataFactory();

        foreach ($this->subjects as $subject => $class) {
            $tagEntity = $class['tag']['classes']['model'];

            // auto naming table if not extend Tag class.
            if ($metadata->getName() === Tag::class && $tagEntity === Tag::class) {
                $metadata->setPrimaryTable(array(
                    'name' => sprintf('%s_%s_tag', ToroTaggingBundle::APPLICATION_NAME, $subject)
                ));
            }

            if ($class['subject'] !== $metadata->getName()) {
                continue;
            }

            $tagEntityMetadata = $metadataFactory->getMetadataFor($tagEntity);

            $metadata->mapManyToMany(
                $this->createTagMapping($tagEntity, $subject, $tagEntityMetadata)
            );
        }
    }

    /**
     * @param string $tagEntity
     * @param string $subject
     * @param ClassMetadata $tagEntityMetadata
     *
     * @return array
     */
    private function createTagMapping($tagEntity, $subject, ClassMetadata $tagEntityMetadata)
    {
        $return = [
            'fieldName' => 'tags',
            'targetEntity' => $tagEntity,
            'joinTable' => [
                'name' => sprintf('%s_%s_tags', ToroTaggingBundle::APPLICATION_NAME, $subject),
                'joinColumns' => [[
                    'name' => $subject.'_id',
                    'referencedColumnName' => 'id',
                    'nullable' => false,
                    'unique' => false,
                    'onDelete' => 'CASCADE',
                ]],
                'inverseJoinColumns' => [[
                    'name' => 'tag_id',
                    'referencedColumnName' => $tagEntityMetadata->fieldMappings['id']['columnName'],
                    'nullable' => false,
                    'unique' => false,
                    'onDelete' => 'CASCADE',
                ]],
            ],
        ];

        if (array_key_exists('tagables', $tagEntityMetadata->associationMappings)) {
            $return['inversedBy'] = 'tagables';
        }

        return $return;
    }
}
