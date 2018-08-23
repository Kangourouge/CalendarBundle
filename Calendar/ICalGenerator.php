<?php

namespace KRG\CalendarBundle\Calendar;

use KRG\CalendarBundle\Entity\AppointmentInterface;
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

    public function create(AppointmentInterface $appointment, string $title = '', string $description = '', string $locale = '', string $location = '', array $organizers = [], \DateTimeZone $timeZone = null)
    {
        $iCal = new ICal($appointment);

        if (null === $timeZone) {
            $timeZone = new \DateTimeZone('Europe/Paris');
        }

        $iCal
            ->setTitle($title)
            ->setDescription($description)
            ->setLocale($locale)
            ->setLocation($location)
            ->setOrganizers($organizers)
            ->setTimezone($timeZone);

        return $iCal;
    }

    public function getAttachment(ICal $iCal, string $filename, string $template = '@KRGCalendar/iCalendar/event.ics.twig')
    {
        $ics = \Swift_Attachment::fromPath($this->generate($iCal, $template));
        $ics->setFilename($filename);

        return $ics;
    }

    public function generate(ICal $iCal, string $template = '@KRGCalendar/iCalendar/event.ics.twig') {

        $tempnam = tempnam(sys_get_temp_dir(), 'ical-');

        $fd = fopen($tempnam, 'w');
        fwrite($fd, $this->templating->render($template, ['iCal' => $iCal]));
        fclose($fd);

        return $tempnam;
    }
}
