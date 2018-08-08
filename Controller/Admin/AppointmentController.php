<?php

namespace KRG\CalendarBundle\Controller\Admin;

use KRG\CalendarBundle\Calendar\CalendarFactory;
use KRG\CalendarBundle\Model\AppointmentModel;
use KRG\EasyAdminExtensionBundle\Controller\AdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/admin", name="admin_")
 */
class AppointmentController extends AdminController
{
    /** @var CalendarFactory */
    protected $calendarFactory;

    /** @var string */
    protected $calendarModel;

    /** @var array */
    protected $options;

    /** @var string */
    protected $template;

    public function __construct(CalendarFactory $calendarFactory)
    {
        $this->calendarFactory = $calendarFactory;
        $this->calendarModel = AppointmentModel::class;
        $this->options = [];
        $this->template = null;
    }

    /**
     * @Route("/appointment/show", name="appointment_show")
     */
    public function appointmentListAction(Request $request)
    {
        $calendar = $this->calendarFactory->create($this->calendarModel);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($calendar->getEvents());
        }

        return $this->render('@KRGCalendar/admin/appointment/show.html.twig', [
            'calendar' => $calendar,
            'options'  => $this->options,
            'template' => $this->template,
        ]);
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    public function setTemplate(string $template)
    {
        $this->template = $template;
    }

    public function setCalendarModel(string $calendarModel)
    {
        $this->calendarModel = $calendarModel;

        return $this;
    }
}
