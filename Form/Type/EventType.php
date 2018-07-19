<?php

namespace KRG\CalendarBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use KRG\CalendarBundle\Model\Event as CalendarEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EventType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'choices'           => [],
           'multiple'          => false,
           'group_by'          => function (CalendarEvent $event) {
               return $event->getStartAt()->format('d-m-Y');
           },
           'choice_label'      => function (CalendarEvent $event, $key, $index) {
               return $event->getStartAt()->format('H:i');
           },
           'choice_attr'       => function (CalendarEvent $event, $key, $index) {
               $attr = [
                   'data-start-at' => $event->getStartAt()->format(\DateTime::ATOM),
                   'data-end-at'   => $event->getEndAt()->format(\DateTime::ATOM),
                   'data-slot-id'  => $event->getSlot()->getId(),
                   'meridium'      => $event->getStartAt()->format('A'),
                   'timestamp'     => $event->getStartAt()->format('U'),
                   'full_label'    => $event->getStartAt()->format('Y-m-d H:i:s'),
                   'disabled'      => 'disabled',
               ];

               if ($event->isAvailable()) {
                   unset($attr['disabled']);
               }

               return $attr;
           },
           'choices_as_values' => true,
           'required'          => true,
           'mapped'            => false,
           'label'             => false,
        ]);

        $resolver->setRequired(['choices']);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix()
    {
        return 'event';
    }
}
