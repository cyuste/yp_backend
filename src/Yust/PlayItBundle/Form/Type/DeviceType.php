<?php

namespace Yust\PlayItBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DeviceType extends AbstractType
{
    private $groupChoices;
    
    public function __construct(\Doctrine\ORM\EntityManager $em, $securityContext)
    {
        $groupsRep = $em->getRepository('YustPlayItBundle:GroupTable');
        $user = $securityContext->getToken()->getUser();
        if(true === $securityContext->isGranted('ROLE_ADMIN')) {
            $availableGroups = $groupsRep->findAll();
        } else {
            $availableGroups = $groupsRep->findBy(array('user'=> $user));
        }
        $this->groupChoices = $availableGroups;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name','text', array('label' => 'Alias'));
        $builder->add('group', 'entity', array(
            'label' => 'Grupo',   
            'class' => 'YustPlayItBundle:GroupTable',
            'property' => 'name',
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

