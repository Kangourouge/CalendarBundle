<?php

namespace KRG\CalendarBundle\Form\Type;

use AppBundle\Entity\Appointment\Appointment;
use AppBundle\Entity\Appointment\Slot;
use AppBundle\Entity\User\User;
use KRG\CalendarBundle\Calendar\Calendar;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use KRG\CalendarBundle\Model\Event as CalendarEvent;

class AppointmentType extends AbstractType
{
    /** @var Calendar */
    private $calendar;

    public function __construct(Calendar $calendar)
    {
        $this->calendar = $calendar;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $timeFormat = 'H:i';

        /* @var $events array */
        $events = $this->calendar->findEvents([
            'startAt' => $options['startAt'],
        ], null, true);

        $builder
            ->add('event', ChoiceType::class, array(
                'choices'           => $events,
                'multiple'          => false,
                'group_by'          => $this->getGroupBy(),
                'choice_label'      => function (CalendarEvent $event, $key, $index) use ($timeFormat) {
                    return $event->getStartAt()->format($timeFormat);
                },
                'choice_attr'       => function (CalendarEvent $event, $key, $index) {
                    $attr = array(
                        'data-start-at' => $event->getStartAt()->format(\DateTime::ATOM),
                        'data-end-at'   => $event->getEndAt()->format(\DateTime::ATOM),
                        'data-slot-id'  => $event->getSlot()->getId(),
                        'meridium'      => $event->getStartAt()->format('A'),
                        'full_label'    => $event->getStartAt()->format('Y-m-d H:i:s'),
                        'disabled'      => 'disabled',
                    );

                    if ($event->isAvailable()) {
                        unset($attr['disabled']);
                    }

                    return $attr;
                },
                'choices_as_values' => true,
                'required'          => true,
                'mapped'            => false,
                'label'             => false
            ))
            ->add('slot', EntityType::class, array(
                'class' => Slot::class
            ))
            ->add('startAt', HiddenType::class, array(
                'mapped' => false
            ))
            ->add('endAt', HiddenType::class, array(
                'mapped' => false
            ));

        $builder->addEventListener(FormEvents::POST_SUBMIT, array($this, 'onPostSubmit'));
    }

    function onPostSubmit(FormEvent $event)
    {
        /* @var $appointment Appointment */
        $appointment = $event->getData();
        $form = $event->getForm();

        $startAt = \DateTime::createFromFormat(\DateTime::ATOM, $form->get('startAt')->getData());
        $endAt = \DateTime::createFromFormat(\DateTime::ATOM, $form->get('endAt')->getData());

        if (!$startAt instanceof \DateTime || !$endAt instanceof \DateTime) {
            return;
        }

        $appointment->setStartAt($startAt)->setEndAt($endAt);

        $event->setData($appointment);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
           'data_class' => Appointment::class,
           'startAt'    => new \DateTime(),
        ));
        $resolver->setRequired(array('user'));
        $resolver->setAllowedTypes('user', User::class);
        $resolver->setAllowedTypes('startAt', \DateTime::class);
    }

    public function getBlockPrefix()
    {
        return 'appointment';
    }

    protected function getGroupBy()
    {
        return function (CalendarEvent $event) {
            return $event->getStartAt()->format('d/m/Y');
        };
    }
}
