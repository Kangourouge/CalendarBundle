<?php

namespace KRG\CalendarBundle\Model;

use KRG\CalendarBundle\Calendar\Calendar;
use KRG\CalendarBundle\Calendar\Event;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class CalendarModel implements CalendarModelInterface
{
    /** @var string */
    protected $id;

    /** @var Calendar */
    protected $calendar;

    /** @var Request */
    protected $request;

    /** @var RouterInterface */
    protected $router;

    /** @var FormView */
    protected $filter;

    /** @var array */
    protected $events;

    /** @var array */
    protected $colors;

    public function __construct()
    {
        $this->id = sprintf('calendar_%s', uniqid());
    }

    /**
     * @param array $slots
     * @param array $appointments
     *
     * @return mixed
     */
    abstract public function load(array $slots, array $appointments);

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return RouterInterface
     */
    public function getRouter()
    {
        return $this->router;
    }

    public function isHtmlHttpRequest()
    {
        return $this->request->isXmlHttpRequest();
    }

    /**
     * @param Request $request
     * @param array|null $filter
     * @param UserInterface|null $user
     */
    public function handleRequest(Request $request, array $filter = null, UserInterface $user = null)
    {
        $this->request = $request;
        if ($filter !== null) {
            $this->load(
                $this->calendar->getSlots($filter, $user),
                $this->calendar->getAppointments($filter, $user)
            );
        }
    }

    /**
     * @param Calendar $calendar
     */
    public function setCalendar(Calendar $calendar)
    {
        $this->calendar = $calendar;

        return $this;
    }

    /**
     * @param RouterInterface $router
     */
    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;

        return $this;
    }

    /**
     * @param FormInterface $filter
     */
    public function setFilter(FormView $filter)
    {
        $this->filter = $filter;

        return $this;
    }

    public function setColors(array $colors)
    {
        $this->colors = $colors;

        return $this;
    }

    public function getEvents()
    {
        return $this->events;
    }

    public function addEvent(Event $event)
    {
        $this->events[] = $event;

        return $this;
    }

    /**
     * @return FormInterface
     */
    public function getFilter()
    {
        return $this->filter;
    }
}
