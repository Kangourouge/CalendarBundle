<?php

namespace KRG\CalendarBundle\Calendar;

use AppBundle\Doctrine\DBAL\GenderEnum;
use KRG\CalendarBundle\Entity\AppointmentInterface;
use KRG\CalendarBundle\Entity\SlotInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Calendar
{
    /**
     * @var CalendarManager
     */
    private $manager;

    /**
     * Calendar constructor.
     *
     * @param CalendarManager $manager
     */
    public function __construct(CalendarManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param array $slots
     * @param array $appointments
     */
    protected function prepare(array $slots, array $appointments)
    {
        /* @var $slot SlotInterface */
        foreach ($slots as $slot) {
            if (!$slot->getExcluded()) {
                $slot->getAppointments()->clear();
            }
        }

        /* @var $appointment AppointmentInterface */
        foreach ($appointments as $appointment) {
            foreach ($slots as $slot) {
                if ($slot->getExcluded()) {
                    continue;
                }
                if ($slot->contains($appointment->getStartAt(), $appointment->getEndAt())) {
                    $slot->addAppointment($appointment);
                    continue 2;
                }
            }
        }
    }

    /**
     * @param array $filter
     * @param UserInterface|null $user
     *
     * @return array
     */
    public function getSlots(array $filter, UserInterface $user = null)
    {
        return $this->manager->getSlots($filter, $user);
    }

    /**
     * @param array $filter
     * @param UserInterface $user
     *
     * @return array
     */
    public function getAppointments(array $filter, UserInterface $user = null)
    {
        return $this->manager->getAppointments($filter, $user);
    }

    /**
     * Browse each slot, make a sample from range and apply it to the events array
     *
     * @param array $slots
     * @param array $appointments
     * @param bool $firstResult
     * @param bool $full
     *
     * @return array|Event|null
     */
    public function getEvents(array $slots, array $appointments, $firstResult = false, $full = false, array $options = array())
    {
        $this->prepare($slots, $appointments);

        $events = array();

        /* @var $slot SlotInterface */
        foreach ($slots as $slot) {
            // Add slots
            if ($slot->getExcluded()) {
                continue;
            }

            $periods = $this->getSlotPeriods($slot);

            foreach ($periods as $period) {
                list($startAt, $endAt) = $period;
                if (($event = $this->createEvent($slot, $startAt, $endAt, $events, $slots, $full, $options)) !== null) {
                    if ($firstResult) {
                        return $event;
                    }
                    $events[] = $event;
                }
            }
        }

        if ($firstResult) {
            return null;
        }

        return $events;
    }

    /**
     * @param SlotInterface $slot
     *
     * @return array
     */
    public function getSlotPeriods(SlotInterface $slot)
    {
        $week = $slot->getWeek();

        $interval = new \DateInterval($week === null ? sprintf('PT%dM', $slot->getDuration()) : 'P1D');
        $endAt = clone $slot->getEndAt();
        $endAt->add($interval); // Add 1 day to INCLUDE the last day

        $period = new \DatePeriod(max(new \DateTime(), $slot->getStartAt()), $interval, $endAt);
        $periods = array();
        if (count($week) === 0) {
            foreach ($period as $datetime) {
                $startAt = clone $datetime;
                $endAt = clone $datetime;
                $endAt->add($interval);

                $periods[] = array($startAt, $endAt);
            }
        } else {
            // Browse day by day the period
            foreach ($period as $datetime) {
                $dayNb = $datetime->format('w') % 7;

                if (key_exists($dayNb, $week)) {
                    $day = $week[$dayNb];
                    // Make events based on the weekly sample
                    foreach ($day as $data) {
                        $startAt = clone $datetime;
                        $startAt->setTime($data[0]->format('H'), $data[0]->format('i'));

                        $endAt = clone $datetime;
                        $endAt->setTime($data[1]->format('H'), $data[1]->format('i'));

                        $periods[] = array($startAt, $endAt);
                    }
                }
            }
        }

        return $periods;
    }

    /**
     * @param array $filter
     * @param UserInterface|null $user
     * @param bool $full
     *
     * @return array|Event|null
     */
    public function findEvents(array $filter, UserInterface $user = null, $full = false, array $options = array())
    {
        return $this->getEvents($this->getSlots($filter, $user), $this->getAppointments($filter, $user), false, $full, $options);
    }

    /**
     * @param array $filter
     * @param UserInterface|null $user
     *
     * @return array|Event|null
     */
    public function findOneEvent(array $filter, UserInterface $user = null)
    {
        return $this->getEvents($this->getSlots($filter, $user), $this->getAppointments($filter, $user), true);
    }

    /**
     * @param SlotInterface $slot
     * @param \DateTime $startAt
     * @param \DateTime $endAt
     * @param array $events
     * @param array $slots
     * @param bool $full
     *
     * @return Event|null
     */
    private function createEvent(SlotInterface $slot, \DateTime $startAt, \DateTime $endAt, array $events, array $slots, $full = false, array $options = array())
    {
        if (!$this->isValid($startAt, $endAt, $events, $slots)) {
            return null;
        }

        if (($isFull = $this->isFull($slot, $startAt, $endAt, $options)) && !$full) {
            return null;
        }

        $title = sprintf('%s - %s', $startAt->format('H:i'), $endAt->format('H:i'));
        $event = new Event($title, $startAt, $endAt);
        $event
            ->addField('slotId', $slot->getId())
            ->setAvailable(!$isFull)
            ->setSlot($slot);

        return $event;
    }

    /**
     * @param \DateTime $startAt
     * @param \DateTime $endAt
     * @param array $events
     * @param array $slots
     * @param array $appointments
     *
     * @return bool
     */
    private function isValid(\DateTime $startAt, \DateTime $endAt, array $events, array $slots)
    {
        // If it's today's slot, filter by time > now()
        if ($startAt < new \DateTime()) {
            return false;
        }

        // Check if this slot is excluded
        /* @var $slot SlotInterface */
        foreach ($slots as $slot) {
            if ($slot->getExcluded() && $slot->contains($startAt, $endAt)) {
                return false;
            }
        }

        // Check if this slot already exists
        /* @var $event Event */
        foreach ($events as $event) {
            if ($startAt >= $event->getStartAt() && $endAt <= $event->getEndAt()) {
                return false;
            }
        }

        return true;
    }

    /**
     * TODO: remove AppBundle dependency
     * @param SlotInterface $slot
     * @param \DateTime $startAt
     * @param \DateTime $endAt
     *
     * @return bool
     */
    private function isFull(SlotInterface $slot, \DateTime $startAt, \DateTime $endAt, array $options = array())
    {
        $isFull = false;
        if (isset($options['gender'])) {
            // Slot max capacity limited by gender
            // Ex: slot capacity = 8, max M = 8, max W = 4
            // OK: 8M + 0W
            // OK: 4M + 4W
            // KO: 5M + 1W

            $count[GenderEnum::MALE] = 0;
            $count[GenderEnum::FEMALE] = 0;
            /* @var $appointment \KRG\CalendarBundle\Entity\AppointmentInterface */
            foreach ($slot->getAppointments() as $appointment) {
                if ($appointment->isValid($startAt, $endAt) && null !== $appointment->getOrder()->getUser()) {
                    $count[$appointment->getOrder()->getUser()->getGender()]++;
                }
            }

            $halfCapacity = (int)floor($slot->getCapacity() / 2);
            $capacity[GenderEnum::MALE] = $count[GenderEnum::FEMALE] > 0 ? $slot->getCapacity() / 2 : $slot->getCapacity();
            $capacity[GenderEnum::FEMALE] = $count[GenderEnum::MALE] > $halfCapacity ? 0 : $halfCapacity;
            $total = $count[GenderEnum::MALE] + $count[GenderEnum::FEMALE];

            $isFull = ($total >= $slot->getCapacity() || $count[$options['gender']] >= $capacity[$options['gender']]);

            if ($isFull) {
                return $isFull;
            }
        }

        return !$slot->isValid($startAt, $endAt);
    }
}
