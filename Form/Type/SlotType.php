<?php

namespace KRG\CalendarBundle\Form\Type;

use KRG\CalendarBundle\Entity\SlotInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class SlotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startAt', DateType::class, [
                'required' => true,
                'widget'   => 'single_text',
                'format'   => 'dd/MM/yyyy',
            ])
            ->add('endAt', DateType::class, [
                'required' => true,
                'widget'   => 'single_text',
                'format'   => 'dd/MM/yyyy',
            ])
            ->add('range', SlotRangeCollectionType::class, [
                'label'      => false
            ])
            ->add('duration', DurationType::class, [
                'required' => true,
            ])
            ->add('capacity', IntegerType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'            => SlotInterface::class,
            'label_format'          => 'form.slot.%name%',
            'cascade_validation'    => true,
            'translation_domain'    => 'KRGCalendarBundle',
        ]);
    }

    public function getBlockPrefix()
    {
        return 'slot';
    }
}
