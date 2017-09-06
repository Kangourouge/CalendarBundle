<?php

namespace KRG\CalendarBundle\Calendar;

use KRG\CalendarBundle\Entity\SlotInterface;

/**
 * Class for holding a calendar event's details.
 */

class Event
{
    /**
     * @var mixed Unique identifier of this event (optional).
     */
    protected $id;

    /**
     * @var string Title/label of the calendar event.
     */
    protected $title;

    /**
     * @var string URL Relative to current path.
     */
    protected $url;

    /**
     * @var string HTML color code for the bg color of the event label.
     */
    protected $bgColor;

    /**
     * @var string HTML color code for the foregorund color of the event label.
     */
    protected $fgColor;

    /**
     * @var string css class for the event label
     */
    protected $cssClass;

    /**
     * @var \DateTime DateTime object of the event start date/time.
     */
    protected $startAt;

    /**
     * @var \DateTime DateTime object of the event end date/time.
     */
    protected $endAt;

    /**
     * @var boolean Is this an all day event?
     */
    protected $allDay = false;

    /**
     * @var array Non-standard fields
     */
    protected $otherFields = array();

    /**
     * @var Slot
     */
    protected $slot;

    /**
     * @var bool
     */
    protected $available;

    public function __construct($title, \DateTime $startAt, \DateTime $endAt = null, $allDay = false)
    {
        $this->title = $title;
        $this->startAt = $startAt;
        $this->setAllDay($allDay);
        $this->available = true;

        if ($endAt === null && $this->allDay === false) {
            throw new \InvalidArgumentException("Must specify an event End DateTime if not an all day event.");
        }

        $this->endAt = $endAt;
    }

    /**
     * Convert calendar event details to an array
     *
     * @return array $event
     */
    public function toArray()
    {
        $event = array();

        if ($this->id !== null) {
            $event['id'] = $this->id;
        }

        $event['title'] = $this->title;
        $event['start'] = $this->startAt->format('Y-m-d\TH:i:sP');

        if ($this->url !== null) {
            $event['url'] = $this->url;
        }

        if ($this->bgColor !== null) {
            $event['backgroundColor'] = $this->bgColor;
            $event['borderColor'] = $this->bgColor;
        }

        if ($this->fgColor !== null) {
            $event['textColor'] = $this->fgColor;
        }

        if ($this->cssClass !== null) {
            $event['className'] = $this->cssClass;
        }

        if ($this->endAt !== null) {
            $event['end'] = $this->endAt->format('Y-m-d\TH:i:sP');
        }

        $event['allDay'] = $this->allDay;

        foreach ($this->otherFields as $field => $value) {
            $event[$field] = $value;
        }

        return $event;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setBgColor($color)
    {
        $this->bgColor = $color;

        return $this;
    }

    public function getBgColor()
    {
        return $this->bgColor;
    }

    public function setFgColor($color)
    {
        $this->fgColor = $color;
    }

    public function getFgColor()
    {
        return $this->fgColor;
    }

    public function setCssClass($class)
    {
        $this->cssClass = $class;
    }

    public function getCssClass()
    {
        return $this->cssClass;
    }

    public function setStartAt(\DateTime $start)
    {
        $this->startAt = $start;
    }

    public function getStartAt()
    {
        return $this->startAt;
    }

    public function setEndAt(\DateTime $end)
    {
        $this->endAt = $end;
    }

    public function getEndAt()
    {
        return $this->endAt;
    }

    public function setAllDay($allDay = false)
    {
        $this->allDay = (boolean)$allDay;
    }

    public function getAllDay()
    {
        return $this->allDay;
    }

    /**
     * @return boolean
     */
    public function isAvailable()
    {
        return $this->available;
    }

    /**
     * @param $available
     *
     * @return Event
     */
    public function setAvailable($available)
    {
        $this->available = $available;

        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function addField($name, $value)
    {
        $this->otherFields[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     */
    public function removeField($name)
    {
        if (!array_key_exists($name, $this->otherFields)) {
            return;
        }

        unset($this->otherFields[$name]);
    }

    /**
     * @return SlotInterface
     */
    public function getSlot()
    {
        return $this->slot;
    }

    /**
     * @param SlotInterface $slot
     */
    public function setSlot(SlotInterface $slot)
    {
        $this->slot = $slot;
    }
}
