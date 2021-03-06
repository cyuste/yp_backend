<?php

namespace Yust\PlayItBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text',array('label' => 'Usuario'));
        $builder->add('email', 'email');
        $builder->add('plainPassword', 'repeated', array(
            'type' => 'password',
            'invalid_message' => 'Las contraseñas introducidas no coinciden.',
            'required' => true,
            'options' => array('attr' => array('class' => 'form-group')),
            'first_options'  => array('label' => 'Nueva contraseña','attr' => array ('class' => 'form-control')),
            'second_options' => array('label' => 'Repetir contraseña','attr' => array ('class' => 'form-control')),
        ));
        $builder->add('roles', 'entity', array(
            'class' => 'YustPlayItBundle:Role',
            'property' => 'name',
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Yust\PlayItBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'user';
    }
}

