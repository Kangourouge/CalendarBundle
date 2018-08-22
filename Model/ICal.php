<?php

namespace KRG\CalendarBundle\Model;

use KRG\CalendarBundle\Entity\AppointmentInterface;

class ICal
{
    /** @var string */
    protected $uid;

    /** @var AppointmentInterface */
    protected $appointment;

    /** @var string */
    protected $title;

    /** @var string */
    protected $description;

    /** @var string */
    protected $locale;

    /** @var string */
    protected $location;

    /** @var \DateTimeZone */
    protected $timezone;

    /** @var array */
    protected $organizers;

    public function __construct(AppointmentInterface $appointment)
    {
        $this->uid = sprintf('%s-%s', $appointment->getId(), $appointment->getCreatedAt()->format('YmdHisOT'));
        $this->organizers = [];
        $this->locale = locale_get_default();
        $this->appointment = $appointment;
    }

    function __call($name, $arguments)
    {
        if (method_exists($this->appointment, $name)) {
            return call_user_func_array([$this->appointment, $name], $arguments);
        }

        if (substr($name, 0, 3) !== 'get' && method_exists($this->appointment, sprintf('get%s', ucfirst($name)))) {
            return call_user_func_array([$this->appointment, sprintf('get%s', ucfirst($name))], $arguments);
        }

        throw new \BadMethodCallException();
    }

    public function getUid(): string
    {
        return $this->uid;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): ICal
    {
        $this->description = $description;

        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location)
    {
        $this->location = $location;

        return $this;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): ICal
    {
        $this->locale = $locale;

        return $this;
    }

    public function getAppointment(): AppointmentInterface
    {
        return $this->appointment;
    }

    public function setAppointment(AppointmentInterface $appointment)
    {
        $this->appointment = $appointment;

        return $this;
    }

    public function getOrganizers(): array
    {
        return $this->organizers;
    }

    public function addOrganizer(string $name, string $email): ICal
    {
        $this->organizers[] = ['name' => $name, 'email' => $email];

        return $this;
    }

    public function setOrganizers(array $organizers)
    {
        foreach ($organizers as $organizer) {
            $this->addOrganizer($organizer['name'], $organizer['email']);
        }

        return $this;
    }

    public function getTimezone(): \DateTimeZone
    {
        return $this->timezone;
    }

    public function setTimezone(\DateTimeZone $timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }
}
