<?php

namespace KRG\CalendarBundle\Model;

use KRG\CalendarBundle\Entity\AppointmentInterface;

class AppointmentModel extends CalendarModel
{
    public function load(array $slots, array $appointments)
    {
        /* @var $appointment AppointmentInterface */
        foreach ($appointments as $appointment) {
            $this->events[] = $this->createEvent($appointment);
        }
    }

    protected function createEvent(AppointmentInterface $appointment)
    {
        $event = new Event((string)$appointment, $appointment->getStartAt(), $appointment->getEndAt());

        $event->addField('updateUrl', '/calendar/appointment/update/'.$appointment->getId());

        return $event;
    }
}
