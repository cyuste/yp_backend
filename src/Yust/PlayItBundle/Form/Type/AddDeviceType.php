<?php

namespace Yust\PlayItBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AddDeviceType extends AbstractType
{    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('device', DeviceType::class);
        $builder->add('Guardar', SubmitType::class );
    }

    public function getName()
    {
        return 'addDevice';
    }
}

