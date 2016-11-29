<?php

namespace Toro\Bundle\TaggingBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Toro\Bundle\TaggingBundle\ToroTaggingBundle;

class TagType extends AbstractResourceType
{
    /**
     * @var string
     */
    private $name = ToroTaggingBundle::APPLICATION_NAME;

    /**
     * @var string
     */
    private $subject;

    /**
     * @param string $dataClass
     * @param array $validationGroups
     * @param string $subject
     */
    public function __construct($dataClass, array $validationGroups = [], $subject)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->subject = $subject;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => sprintf('%s.%s.form.tag.name', $this->name, $this->subject),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return sprintf('%s_%s_tag', $this->name, $this->subject);
    }
}
