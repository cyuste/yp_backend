<?php

namespace Yust\PlayItBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AddGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('group', new DeviceGroupType()); 
        $builder->add('Guardar', 'submit');
    }

    public function getName()
    {
        return 'addGroup';
    }
}