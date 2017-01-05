<?php

namespace Yust\PlayItBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AddDeviceType extends AbstractType
{    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('device', 'device');
        $builder->add('Guardar', 'submit');
    }

    public function getName()
    {
        return 'addDevice';
    }
}

