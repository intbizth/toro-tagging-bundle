<?php

namespace Toro\Bundle\TaggingBundle\Form\Type;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Toro\Bundle\TaggingBundle\Provider\TagProvider;

abstract class TagChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var TagProvider
     */
    protected $provider;

    /**
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository, TagProvider $provider)
    {
        $this->repository = $repository;
        $this->provider = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // away load new from data source
            'choice_loader' => new TagChoiceLoader(function() {
                return $this->repository->findAll();
            }),
            'choice_value' => 'id',
            'choice_label' => 'name',
            'choice_translation_domain' => false,
            'label' => 'Tags',
            'placeholder' => 'Select Tags',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'toro_tag_choice';
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
