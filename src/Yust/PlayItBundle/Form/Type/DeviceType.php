<?php

namespace Yust\PlayItBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class DeviceType extends AbstractType
{
    private $groupChoices;
    
    public function __construct(\Doctrine\ORM\EntityManager $em, $secAuthChecker, $secTokenStorage)
    {
        $groupsRep = $em->getRepository('YustPlayItBundle:GroupTable');
        $user = $secTokenStorage->getToken()->getUser();
        if(true === $secAuthChecker->isGranted('ROLE_ADMIN')) {
            $availableGroups = $groupsRep->findAll();
        } else {
            $availableGroups = $groupsRep->findBy(array('user'=> $user));
        }
        $this->groupChoices = $availableGroups;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, array('label' => 'Alias'));
        $builder->add('group', EntityType::class, array(
            'label' => 'Grupo',   
            'class' => 'YustPlayItBundle:GroupTable',
            'choice_label' => 'name',
            'choices' => $this->groupChoices
            // TODO: Ahora saldran todos los grupos de todos los usuarios. Estaria
            // bien poder filtrar por usuario (si eres admin) para poder asociar mas
            // facil al grupo correcto.
        ));   
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Yust\PlayItBundle\Entity\Device'
        ));
    }

    public function getName()
    {
        return 'device';
    }
}

