<?php

namespace KRG\CalendarBundle\Calendar;

use Symfony\Component\DependencyInjection\Container;

class CalendarRegistry
{
    /** @var Container */
    protected $container;

    /** @var array */
    private $calendars;

    function __construct(array $calendars = [])
    {
        $this->calendars = $calendars;
    }

    public function get($name)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException(sprintf('The field calendar "%s" is not registered with the service container.',
                $name));
        }

        $calendar = $this->container->get($this->calendars[$name]);

        if ($calendar->getName() !== $name) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The calendar name specified for the service "%s" does not match the actual name. Expected "%s", given "%s"',
                    $this->calendars[$name],
                    $name,
                    $calendar->getName()
                )
            );
        }

        return $calendar;
    }

    public function has($name)
    {
        return isset($this->calendars[$name]);
    }
}
