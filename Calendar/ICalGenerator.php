<?php

namespace KRG\CalendarBundle\Calendar;

use KRG\CalendarBundle\Model\ICal;
use Symfony\Component\Templating\EngineInterface;

class ICalGenerator
{
    /** @var EngineInterface */
    protected $templating;

    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    public function generate(ICal $iCal, $template = null) {

        $tempnam = tempnam(sys_get_temp_dir(), 'ical-');

        $fd = fopen($tempnam, 'w');
        fwrite($fd, $this->templating->render($template ?: 'KRGCalendarBundle:ICalendar:event.ics.twig', ['iCal' => $iCal]));
        fclose($fd);

        return $tempnam;
    }
}
