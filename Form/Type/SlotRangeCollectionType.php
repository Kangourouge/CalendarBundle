<?php

namespace KRG\CalendarBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SlotRangeCollectionType extends CollectionType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('label', false);
        $resolver->setDefault('entry_options', ['label' => false]);
    }

    public function getBlockPrefix()
    {
        return 'range_collection';
    }
}