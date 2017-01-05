<?php

namespace Yust\PlayItBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WebType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {       
        $builder->add('name', 'text', array('label' => 'Nombre'));
        $builder->add('path', 'url', array('label' => 'URL'));
        $builder->add('type', 'entity', array(
            'class' => 'YustPlayItBundle:ContentType',
            'property' => 'name',
            'label' => 'Tipo'
        ));
        $builder->add('length','integer', array('label' => 'Duración'));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Yust\PlayItBundle\Entity\Content'
        ));
    }

    public function getName()
    {
        return 'newWeb';
    }
}

