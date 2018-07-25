<?php

namespace KRG\CalendarBundle\Entity;

interface UserAppointmentInterface
{
    public function setAppointment(AppointmentInterface $appointment = null);

    public function getAppointment();
}
