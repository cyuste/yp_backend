<?php

namespace Yust\PlayItBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AddContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('content', new NewContentType());
        $builder->add('submit','submit', array('label' => 'Guardar'));
    }

    public function getName()
    {
        return 'newContent';
    }
    
}

