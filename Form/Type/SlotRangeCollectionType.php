<?php

namespace KRG\CalendarBundle\Form\Type;

use KRG\CalendarBundle\Form\DataTransformer\SlotRangeDataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SlotRangeCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        for($i=1; $i<8; $i++) {
            $builder->add($i, SlotDayType::class, ['attr' => ['class' => 'form-slot-day'], 'required' => false]);
        }
        $builder->addModelTransformer(new SlotRangeDataTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('label', false);
        $resolver->setDefault('attr', ['class' => 'form-slot-range']);
    }

    public function getBlockPrefix()
    {
        return 'range_collection';
    }
}