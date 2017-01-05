<?php

namespace Yust\PlayItBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NewContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {       
        $builder->add('name', 'text', array('label' => 'Nombre'));
        $builder->add('file', 'file', array('label' => 'Archivo'));
        $builder->add('type', 'entity', array(
            'class' => 'YustPlayItBundle:ContentType',
            'property' => 'name',
            'label' => 'Tipo'
        ));
        $builder->add('scale','checkbox', array(
            'required' => false,
            'value' => 1,
            'attr' => array('checked' => True),
            'label' => 'Expandir'
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
        return 'newContent';
    }
}

