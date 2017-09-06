<?php

namespace KRG\CalendarBundle\Model;

use KRG\CalendarBundle\Calendar\Event;
use KRG\CalendarBundle\Entity\Appointment;
use KRG\CalendarBundle\Entity\AppointmentInterface;

class AppointmentModel extends CalendarModel
{
    public function load(array $slots, array $appointments)
    {
        /* @var $appointment Appointment */
        foreach ($appointments as $appointment) {
            $this->events[] = $this->createEvent($appointment);
        }
    }

    protected function createEvent(AppointmentInterface $appointment)
    {
        $event = new Event((string)$appointment, $appointment->getStartAt(), $appointment->getEndAt());
        $event->setBgColor($this->colors[$this->getColor($appointment) % count($this->colors)]);
        $event->addField('updateUrl', '/calendar/appointment/update/'.$appointment->getId());

        return $event;
    }

    protected function getColor(AppointmentInterface $appointment)
    {
        if (!isset($this->filter->children['professionals'])) {
            return 0;
        }

        foreach ($this->filter->children['professionals']->vars['choices'] as $idx => $formView) {
            if ($formView->getData() === $appointment->getProfessional()) {
                return $formView->vars['attr']['color'];
            }
        }

        return 0;
    }
}
