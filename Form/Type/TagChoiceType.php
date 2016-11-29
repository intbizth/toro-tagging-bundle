<?php

namespace Toro\Bundle\TaggingBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Toro\Bundle\TaggingBundle\Provider\TagProvider;

class TagChoiceType extends ResourceChoiceType
{
    /**
     * @var string
     */
    private $subject;

    /**
     * @var TagProvider
     */
    private $provider;

    public function __construct(MetadataInterface $metadata, $subject, TagProvider $provider)
    {
        parent::__construct($metadata);

        $this->subject = $subject;
        $this->provider = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                if ($data = $event->getData()) {
                    foreach ($data as &$value) {
                        if (!is_numeric($value)) {
                            $tag = $this->provider->findOrCreate($value);
                            $value = $tag->getId();
                        }
                    }

                    $event->setData($data);
                }
            })
        ;
    }
}
