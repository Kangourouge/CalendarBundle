<?php

namespace KRG\CalendarBundle\Form\Type;

use AppBundle\Entity\Appointment\Appointment;
use AppBundle\Entity\Appointment\Slot;
use AppBundle\Entity\User\User;
use KRG\CalendarBundle\Calendar\Calendar;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        /* @var $events array */
        $events = $this->calendar->findEvents([
            'startAt' => $options['startAt'],
        ], null, true, [
            'max_days' => $options['max_days']
        ]);

        $builder
            ->add('event', EventType::class, [
                'choices' => $events,
                'data'    => $options['user']->getAppointment()
            ])
            ->add('slot', EntityType::class, [
                'class' => Slot::class,
            ])
            ->add('startAt', HiddenType::class, [
                'mapped' => false
            ])
            ->add('endAt', HiddenType::class, [
                'mapped' => false
            ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'onPostSubmit']);
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $choiceList = $form->get('event')->getConfig()->getOption('choices');

        if (count($choiceList)) {
            $view->vars['first_event'] = $choiceList[0];
            $view->vars['last_event'] = end($choiceList);
        }
    }

    public function onPostSubmit(FormEvent $event)
    {
        /* @var $appointment Appointment */
        $appointment = $event->getData();
        $form = $event->getForm();

        $startAt = \DateTime::createFromFormat(\DateTime::ATOM, $form->get('startAt')->getData());
        $endAt = \DateTime::createFromFormat(\DateTime::ATOM, $form->get('endAt')->getData());

        if (!$startAt instanceof \DateTime || !$endAt instanceof \DateTime) {
            return;
        }

        $appointment
            ->setStartAt($startAt)
            ->setEndAt($endAt);

        $user = $form->getConfig()->getOption('user');
        $user->setAppointment($appointment);

        $event->setData($appointment);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
           'data_class'      => Appointment::class,
           'startAt'         => new \DateTime(),
           'max_days'        => null
        ]);
        $resolver->setRequired(['user']);
        $resolver->setAllowedTypes('user', User::class);
        $resolver->setAllowedTypes('startAt', \DateTime::class);
        $resolver->setAllowedTypes('max_days', ['integer', 'null']);
    }

    public function getBlockPrefix()
    {
        return 'appointment';
    }
}
