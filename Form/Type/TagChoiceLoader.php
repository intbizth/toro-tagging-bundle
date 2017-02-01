<?php

namespace Toro\Bundle\TaggingBundle\Form\Type;

use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

class TagChoiceLoader implements ChoiceLoaderInterface
{
    private $callback;

    /**
     * The loaded choice list.
     *
     * @var ArrayChoiceList
     */
    private $choiceList;

    /**
     * @param callable $callback The callable returning an array of choices
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoiceList($value = null)
    {
        return $this->choiceList = new ArrayChoiceList(call_user_func($this->callback), $value);
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoicesForValues(array $values, $value = null)
    {
        // Optimize
        if (empty($values)) {
            return array();
        }

        return $this->loadChoiceList($value)->getChoicesForValues($values);
    }

    /**
     * {@inheritdoc}
     */
    public function loadValuesForChoices(array $choices, $value = null)
    {
        // Optimize
        if (empty($choices)) {
            return array();
        }

        return $this->loadChoiceList($value)->getValuesForChoices($choices);
    }
}
