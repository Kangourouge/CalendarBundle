<?php

namespace KRG\CalendarBundle\Model;

class SimpleCalendarModel extends CalendarModel
{
    public function load(array $slots, array $appointments)
    {
        $this->events = array();
    }
}
