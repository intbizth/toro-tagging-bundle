<?php

namespace Toro\Bundle\TaggingBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

class ToroTaggingBundle extends AbstractResourceBundle
{
    const APPLICATION_NAME = 'toro';

    /**
     * {@inheritdoc}
     */
    public function getSupportedDrivers()
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Toro\Bundle\TaggingBundle\Model';
    }
}
