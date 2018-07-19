<?php

namespace KRG\CalendarBundle\Calendar;

use KRG\CalendarBundle\Model\Event;
use KRG\CalendarBundle\Entity\SlotInterface;
use KRG\CalendarBundle\Entity\AppointmentInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Calendar
{
    /** @var CalendarManager */
    private $manager;

    public function __construct(CalendarManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Browse each slot, make a sample from range and apply it to the events array
     */
    public function getEvents(array $slots, array $appointments, $firstResult = false, $full = false, array $options = [])
    {
        $this->prepare($slots, $appointments);

        $events = [];

        /* @var $slot SlotInterface */
        foreach ($slots as $slot) {
            // Add slots
            if ($slot->getExcluded()) {
                continue;
            }

            $periods = $this->getSlotPeriods($slot, $options);
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

    public function getSlotPeriods(SlotInterface $slot, array $options = [])
    {
        $week = $slot->getWeek();

        $interval = new \DateInterval($week === null ? sprintf('PT%dM', $slot->getDuration()) : 'P1D');
        $endAt = clone $slot->getEndAt();

        $period = new \DatePeriod(max(new \DateTime(), $slot->getStartAt()), $interval, $endAt);
        $periods = [];
        if (count($week) === 0) {
            foreach ($period as $datetime) {
                $startAt = clone $datetime;
                $endAt = clone $datetime;
                $endAt->add($interval);

                $periods[] = [$startAt, $endAt];
            }
        } else {
            // Browse day by day the period
            $i = 1;
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

                        $periods[] = [$startAt, $endAt];
                    }

                    // Limit number of days available
                    if ($options['max_days'] && $i >= $options['max_days']) {
                        break;
                    }

                    $i++;
                }
            }
        }

        return $periods;
    }

    public function findEvents(array $filter, UserInterface $user = null, $full = false, array $options = [])
    {
        return $this->getEvents($this->getSlots($filter, $user), $this->getAppointments($filter, $user), false, $full, $options);
    }

    public function findOneEvent(array $filter, UserInterface $user = null)
    {
        return $this->getEvents($this->getSlots($filter, $user), $this->getAppointments($filter, $user), true);
    }

    protected function createEvent(SlotInterface $slot, \DateTime $startAt, \DateTime $endAt, array $events, array $slots, $full = false, array $options = [])
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

    protected function isValid(\DateTime $startAt, \DateTime $endAt, array $events, array $slots)
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

    protected function isFull(SlotInterface $slot, \DateTime $startAt, \DateTime $endAt, array $options = [])
    {
        return !$slot->isValid($startAt, $endAt);
    }

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

    public function getSlots(array $filter, UserInterface $user = null)
    {
        return $this->manager->getSlots($filter, $user);
    }

    public function getAppointments(array $filter, UserInterface $user = null)
    {
        return $this->manager->getAppointments($filter, $user);
    }
}
