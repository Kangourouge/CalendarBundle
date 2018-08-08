<?php

namespace KRG\CalendarBundle\Twig;

use KRG\CalendarBundle\Model\CalendarModelInterface;

class CalendarExtension extends \Twig_Extension
{
    protected $options = [];

    public function render(\Twig_Environment $twig, CalendarModelInterface $calendar, array $options = [], $template = null)
    {
        $this->options = $options;

        // Default options
        $this->addDefaultOption('locale', 'fr');
        $this->addDefaultOption('timeFormat', 'H(:mm)');
        $this->addDefaultOption('header', [
            'center' => 'prev,next',
            'right'  => 'agendaWeek,month'
        ]);

        $this->options['events'] = $calendar->getRequest()->getPathInfo();

        $template = $template ?: 'KRGCalendarBundle:Default:calendar.html.twig';

        return $twig->load($template)->render([
            'calendar' => $calendar,
            'options'  => $this->options,
        ]);
    }

    private function addDefaultOption($key, $value)
    {
        $this->options[$key] = (array_key_exists($key, $this->options)) ? $this->options[$key] : $value;

        return $this->options;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('calendar_render', [$this, 'render'], ['is_safe' => ['html'], 'needs_environment' => true]),
        ];
    }
}
