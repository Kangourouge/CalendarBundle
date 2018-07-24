<?php

namespace KRG\CalendarBundle\Form\Type;

use KRG\CalendarBundle\Entity\Appointment;
use KRG\CalendarBundle\Model\Event;
use Symfony\Component\Form\AbstractType;
use KRG\CalendarBundle\Model\Event as CalendarEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetdata']);
    }

    public function onPreSetdata(FormEvent $event)
    {
        /** @var $appointment Appointment */
        $appointment = $event->getData();
        $form = $event->getForm();

        if ($appointment) {
            foreach ($form->getConfig()->getOption('choices') as $id => $choice) {
                if ($choice->getStartAt()->format('U')=== $appointment->getStartAt()->format('U')
                    && $choice->getEndAt()->format('U') === $appointment->getEndAt()->format('U')) {
                    $event->setData((string)$id);
                    break;
                }
            }
        }
    }

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
           'translation_domain' => 'KRGCalendarBundle',
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
