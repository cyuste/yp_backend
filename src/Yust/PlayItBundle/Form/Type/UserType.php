<?php

namespace Yust\PlayItBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Yust\PlayItBundle\Entity\User;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', TextType::class, array('label' => 'Usuario'));
        $builder->add('email', EmailType::class);
        $builder->add('plainPassword', RepeatedType::class, array(
            'type' => PasswordType::class,
            'invalid_message' => 'Las contraseñas introducidas no coinciden.',
            'required' => true,
            'options' => array('attr' => array('class' => 'form-group')),
            'first_options'  => array('label' => 'Nueva contraseña','attr' => array ('class' => 'form-control')),
            'second_options' => array('label' => 'Repetir contraseña','attr' => array ('class' => 'form-control')),
        ));
        $builder->add('roles', EntityType::class, array(
            'class' => 'YustPlayItBundle:Role',
            'choice_label' => 'name',
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }

    public function getName()
    {
        return 'user';
    }
}

