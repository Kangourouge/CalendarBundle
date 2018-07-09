<?php

namespace KRG\CalendarBundle\Calendar;

use Doctrine\ORM\EntityManager;
use KRG\CalendarBundle\Entity\AppointmentInterface;
use KRG\CalendarBundle\Entity\SlotInterface;
use KRG\CalendarBundle\Repository\CalendarRepositoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CalendarManager
{
    /** @var EntityManager */
    protected $entityManager;

    /** @var FormInterface */
    protected $form;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getSlots(array $filter, UserInterface $user = null)
    {
        $className = $this->entityManager->getClassMetadata(SlotInterface::class)->getName();
        $slotRepository = $this->entityManager->getRepository($className);
        if (!$slotRepository instanceof CalendarRepositoryInterface) {
            throw new \RuntimeException('Slot Repository must be an instance of ' . CalendarRepositoryInterface::class);
        }

        return $slotRepository->getQueryBuilder($filter, $user)->getQuery()->getResult();
    }

    public function getAppointments(array $filter, UserInterface $user = null)
    {
        $className = $this->entityManager->getClassMetadata(AppointmentInterface::class)->getName();
        $appointmentRepository = $this->entityManager->getRepository($className);
        if (!$appointmentRepository instanceof CalendarRepositoryInterface) {
            throw new \RuntimeException('Appointment Repository must be an instance of '.CalendarRepositoryInterface::class);
        }

        return $appointmentRepository->getQueryBuilder($filter, $user)->getQuery()->getResult();
    }
}
