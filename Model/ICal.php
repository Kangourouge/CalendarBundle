<?php

namespace KRG\CalendarBundle\Model;

use KRG\CalendarBundle\Entity\AppointmentInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ICal
{
    /**
     * @var string
     */
    protected $uid;

    /**
     * @var AppointmentInterface
     */
    protected $appointment;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $location;

    /**
     * @var \DateTimeZone
     */
    protected $timezone;

    /**
     * @var array
     */
    protected $organizers;

    /**
     * ICal constructor.
     *
     * @param AppointmentInterface $appointment
     */
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

    /**
     * @return string
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return ICal
     */
    public function setTitle(string $title): ICal
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return ICal
     */
    public function setDescription(string $description): ICal
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation(string $location)
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return ICal
     */
    public function setLocale(string $locale): ICal
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return AppointmentInterface
     */
    public function getAppointment(): AppointmentInterface
    {
        return $this->appointment;
    }

    /**
     * @param AppointmentInterface $appointment
     */
    public function setAppointment(AppointmentInterface $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * @return array
     */
    public function getOrganizers(): array
    {
        return $this->organizers;
    }

    /**
     * @param string $name
     * @param string $email
     *
     * @return ICal
     */
    public function addOrganizer(string $name, string $email): ICal
    {
        $this->organizers[] = ['name' => $name, 'email' => $email];

        return $this;
    }

    /**
     * @return \DateTimeZone
     */
    public function getTimezone(): \DateTimeZone
    {
        return $this->timezone;
    }

    /**
     * @param \DateTimeZone $timezone
     */
    public function setTimezone(\DateTimeZone $timezone)
    {
        $this->timezone = $timezone;
    }

}