<?php

namespace KRG\CalendarBundle\Model;

use KRG\CalendarBundle\Calendar\Event;
use KRG\CalendarBundle\Entity\AppointmentInterface;
use KRG\CalendarBundle\Entity\Slot;
use Symfony\Component\Form\FormView;

class FreeSlotModel extends CalendarModel
{
    /*
     * Slots are orderBy "excluded", inclusions first
     */
    public function load(array $slots, array $appointments)
    {
        $this->events = $this->calendar->getEvents($slots, $appointments);
    }
}
