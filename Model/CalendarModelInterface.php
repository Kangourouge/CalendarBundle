<?php

namespace KRG\CalendarBundle\Model;

use KRG\CalendarBundle\Calendar\Calendar;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface CalendarModelInterface
{
    public function getId();

    /**
     * @return Request
     */
    public function getRequest();

    /**
     * @return RouterInterface
     */
    public function getRouter();

    /**
     * @param RouterInterface $router
     */
    public function setRouter(RouterInterface $router);

    /**
     * @return bool
     */
    public function isHtmlHttpRequest();

    /**
     * @param Calendar $calendar
     *
     * @return mixed
     */
    public function setCalendar(Calendar $calendar);

    /**
     * @param FormView $form
     *
     * @return mixed
     */
    public function setFilter(FormView $form);

    /**
     * @param array $colors
     *
     * @return mixed
     */
    public function setColors(array $colors);

    /**
     * @param Request $request
     * @param array|null $filter
     * @param UserInterface|null $user
     *
     * @return mixed
     */
    public function handleRequest(Request $request, array $filter = null, UserInterface $user = null);

    /**
     * @return array
     */
    public function getEvents();
}
