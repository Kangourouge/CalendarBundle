<?php

namespace KRG\CalendarBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use KRG\CalendarBundle\Form\DataTransformer\SlotRangeDataTransformer;

class SlotRangeCollectionType extends CollectionType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new SlotRangeDataTransformer());
        $builder->addEventListener(FormEvents::POST_SET_DATA, [$this, 'onPostSetData']);
    }

    /**
     * Must reset all fields because ModelTransformer does not add form children
     */
    public function onPostSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (count($data) === 0) {
            $data = $this->getGenericData();
        }

        foreach ($form->all() as $idx => $item) {
            $form->remove($idx);
        }

        $day = 1;
        $i = 0;
        foreach ($data as $_data) {
            foreach ($_data as $meridium => $value) {
                $form->add($i++, SlotRangeType::class, [
                    'data' => [
                        'day'      => $day,
                        'meridiem' => $meridium,
                        'start'    => $value['start'],
                        'end'      => $value['end'],
                    ],
                ]);
            }
            $day++;
        }
    }

    protected function getGenericData()
    {
        return array_fill(1, 7, [
            'am' => ['start' => '09:00', 'end' => '12:00'],
            'pm' => ['start' => '14:00', 'end' => '18:00'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'entry_options' => ['label' => false],
            'entry_type'    => SlotRangeType::class,
        ]);
    }

    public function getParent()
    {
        return CollectionType::class;
    }

    public function getBlockPrefix()
    {
        return 'range_collection';
    }
}
