<?php

namespace KRG\CalendarBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use KRG\CalendarBundle\Calendar\Calendar;
use KRG\CalendarBundle\Entity\AppointmentInterface;
use KRG\CalendarBundle\Entity\SlotInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\User\UserInterface;

class AppointmentType extends AbstractType
{
    /** @var Calendar */
    protected $calendar;

    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(Calendar $calendar, EntityManagerInterface $entityManager)
    {
        $this->calendar = $calendar;
        $this->entityManager = $entityManager;
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
                'class' => $this->entityManager->getClassMetadata(SlotInterface::class)
                                               ->getReflectionClass()
                                               ->getName(),
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
        /* @var $appointment AppointmentInterface */
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
           'data_class' => $this->entityManager->getClassMetadata(AppointmentInterface::class)
                                               ->getReflectionClass()
                                               ->getName(),
           'startAt'    => new \DateTime(),
           'max_days'   => null,
        ]);
        $resolver->setRequired(['user']);
        $resolver->setAllowedTypes('user', UserInterface::class);
        $resolver->setAllowedTypes('startAt', \DateTime::class);
        $resolver->setAllowedTypes('max_days', ['integer', 'null']);
    }

    public function getBlockPrefix()
    {
        return 'appointment';
    }
}
