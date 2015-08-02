<?php

namespace PJM\AppBundle\Form\Type\Event;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use PJM\AppBundle\Form\Type\ImageType;
use PJM\AppBundle\Form\Type\Boquette\BoquetteByResponsableType;

class EvenementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('description', null, array(
                'required' => false,
            ))
            ->add('day', null, array(
                'label' => 'Journée(s) entière(s) ?',
                'required' => false,
            ))
            ->add('dateDebut', 'datetimePicker', array(
                'label' => 'Date de début',
                'required' => false,
            ))
            ->add('dateFin', 'datetimePicker', array(
                'label' => 'Date de fin',
                'required' => false,
                'linkedTo' => 'dateDebut',
            ))
            ->add('lieu')
            ->add('prix', 'money', array(
                'label' => 'Prix',
                'divisor' => 100,
            ))
            ->add('boquette', new BoquetteByResponsableType(array('user' => $options['user'])))
            ->add('public', null, array(
                'label' => 'Evènement public',
                'required' => false,
            ))
            ->add('image', new ImageType(), array(
                'label' => 'Image',
                'required' => false,
            ))
            ->add('save', 'submit', array(
                'label' => $options['label_submit'],
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PJM\AppBundle\Entity\Event\Evenement',
            'user' => null,
            'label_submit' => 'Créer',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pjm_appbundle_event_evenement';
    }
}
