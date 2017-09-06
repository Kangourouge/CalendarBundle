<?php

namespace KRG\CalendarBundle\Twig;

use KRG\CalendarBundle\Calendar\Event;
use KRG\CalendarBundle\Model\CalendarModelInterface;

class CalendarExtension extends \Twig_Extension
{
    protected $options = array();

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction("calendar_render", array($this, "render"), array("is_safe" => array("html"), 'needs_environment' => true)),
        );
    }

    public function render(\Twig_Environment $twig, CalendarModelInterface $calendar, array $options = array(), $template = null)
    {
        $this->options = $options;

        // Default options
        $this->addDefaultOption('locale', 'fr');
        $this->addDefaultOption('timeFormat', 'H(:mm)');

        $this->options['events'] = $calendar->getRequest()->getPathInfo();

        $template = $template ?: 'KRGCalendarBundle:Default:calendar.html.twig';

        return $twig->load($template)->render(array(
            'calendar' => $calendar,
            'options'  => $this->options,
        ));
    }

    private function addDefaultOption($key, $value)
    {
        $this->options[$key] = (array_key_exists($key, $this->options)) ? $this->options[$key] : $value;

        return $this->options;
    }
}
