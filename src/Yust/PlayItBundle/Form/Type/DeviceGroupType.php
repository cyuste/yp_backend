<?php

namespace Yust\PlayItBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DeviceGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name','text');
        $builder->add('user', 'entity', array(
            'class' => 'YustPlayItBundle:User',
            'property' => 'username',
            // TODO 'data' o alguna manera de poner un usuario por defecto
        ));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Yust\PlayItBundle\Entity\GroupTable'
        ));
    }

    public function getName()
    {
        return 'deviceGroup';
    }
}