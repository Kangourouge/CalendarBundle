<?php

namespace KRG\CalendarBundle\Model;



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
