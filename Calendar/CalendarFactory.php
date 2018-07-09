<?php

namespace KRG\CalendarBundle\Calendar;

use KRG\CalendarBundle\Model\CalendarModelInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CalendarFactory
{
    /** @var Calendar */
    private $calendar;

    /** @var CalendarRegistry */
    private $registry;

    /** @var FormFactory */
    private $formFactory;

    /** @var Request */
    private $request;

    /** @var RouterInterface */
    public $router;

    /** @var array */
    private $colors;

    /** @var TokenStorage */
    private $tokenStorage;

    public function __construct(Calendar $calendar, CalendarRegistry $registry, FormFactory $formFactory, RequestStack $requestStack, TokenStorage $tokenStorage, RouterInterface $router, array $colors)
    {
        $this->calendar = $calendar;
        $this->registry = $registry;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->colors = $colors;
        $this->request = $requestStack->getCurrentRequest();
        $this->tokenStorage = $tokenStorage;
    }

    public function create($model, $filter = null, array $data = [])
    {
        if (is_string($model)) {
            if (class_exists($model)) {
                $reflectionClass = new \ReflectionClass($model);
                if (!$reflectionClass->implementsInterface(CalendarModelInterface::class)) {
                    throw new \RuntimeException('$model must be an instance of '.CalendarModelInterface::class);
                }
                $model = $reflectionClass->newInstance();
            } else {
                $model = $this->registry->get($model);
            }
        } elseif (!$model instanceof CalendarModelInterface) {
            throw new \InvalidArgumentException('$calendar must be valid string|CalendarModelInterface');
        }

        if ($filter !== null) {
            if (!$filter instanceof FormInterface) {
                $filter = $this->formFactory->createNamed('calendar_filter', $filter);
            }

            $filter->handleRequest($this->request);
            $model->setFilter($filter->createView());
        }

        $model->setCalendar($this->calendar);
        $model->setRouter($this->router);
        $model->setColors($this->colors);

        $data = null;
        if ($this->request->isXmlHttpRequest()) {
            /* @var $user UserInterface */
            $user = $this->getUser();

            if ($filter !== null) {
                $data['startAt'] = new \DateTime($this->request->get('start'));
                $data['endAt'] = new \DateTime($this->request->get('end'));

                if ($filter->isValid()) {
                    $data = array_merge($filter->getData(), $data);
                } else {
                    throw new \RuntimeException('Invalid form filter');
                }
            }
        }

        $model->handleRequest($this->request, $data, $user);

        return $model;
    }

    private function getUser()
    {
        $user = $this->tokenStorage->getToken()->getUser();

        return $user instanceof UserInterface ? $user : null;
    }
}
