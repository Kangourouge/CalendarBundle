<?php

namespace KRG\CalendarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass(repositoryClass="KRG\CalendarBundle\Repository\SlotRepository")
 */
class Slot implements SlotInterface
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="is_excluded", type="boolean", options={"default":false})
     */
    protected $excluded;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $startAt;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThan("today")
     */
    protected $endAt;

    /**
     * @ORM\Column(type="string")
     */
    protected $duration;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(0)
     */
    protected $capacity;

    /**
     * @ORM\Column(type="json_array", name="`range`")
     * @var array
     */
    protected $range;

    /**
     * @var array
     */
    protected $week;

    /**
     * @var Collection
     */
    protected $appointments;

    public function __toString()
    {
        return (string)$this->getId();
    }

    /**
     * Slot constructor.
     */
    public function __construct()
    {
        $this->range = [];
        $this->duration = 'PT30M';
        $this->capacity = 1;
        $this->excluded = false;
        $this->appointments = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set excluded
     *
     * @param boolean $excluded
     *
     * @return Slot
     */
    public function setExcluded($excluded)
    {
        $this->excluded = $excluded;

        return $this;
    }

    /**
     * Get excluded
     *
     * @return boolean
     */
    public function getExcluded()
    {
        return $this->excluded;
    }

    /**
     * Set startAt
     *
     * @param \DateTime $startAt
     *
     * @return Slot
     */
    public function setStartAt(\DateTime $startAt)
    {
        $this->startAt = $startAt;

        return $this;
    }

    /**
     * Get startAt
     *
     * @return \DateTime
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * Set endAt
     *
     * @param \DateTime $endAt
     *
     * @return Slot
     */
    public function setEndAt(\DateTime $endAt)
    {
        $endAt->setTime(23, 59, 59);
        $this->endAt = $endAt;

        return $this;
    }

    /**
     * Get endAt
     *
     * @return \DateTime
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     *
     * @return Slot
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set capacity
     *
     * @param integer $capacity
     *
     * @return Slot
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * Get capacity
     *
     * @return integer
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * Set range
     *
     * @param array $range
     *
     * @return Slot
     */
    public function setRange($range)
    {
        $this->range = $range;

        return $this;
    }

    /**
     * Get range
     *
     * @return array
     */
    public function getRange()
    {
        return $this->range;
    }

    /*
     * Créer un échantillon d'une semaine type pour chaque slot
     * Si c'est une inclusion, chaque jour sera divisé en slots à partir des ranges et de la duration
     * Si c'est une exclusion, chaque jour sera délimité en périodes exclues
     *
     * @return array
     */
    public function getWeek()
    {
        if ($this->week !== null) {
            return $this->week;
        }

        if ($this->duration === null || $this->range === null || count($this->range) === 0) {
            return null;
        }

        $interval = new \DateInterval($this->duration);

        $week = [];
        foreach ($this->range as $day => $data) {
            foreach ($data as $_data) {
                if ($_data['start'] === null || $_data['end'] === null) {
                    continue;
                }

                $startAt = \DateTime::createFromFormat('H:i', $_data['start']);
                $endAt = \DateTime::createFromFormat('H:i', $_data['end']);

                if (!isset($week[$day % 7])) {
                    $week[$day % 7] = [];
                }

                $week[$day % 7] = array_merge($week[$day % 7], $this->getPeriod($startAt, $endAt, $interval));
            }
        }

        $this->week = $week;

        return $week;
    }

    /**
     * @param \DateTime $startAt
     * @param \DateTime $endAt
     * @param \DateInterval $interval
     *
     * @return array
     */
    public function getPeriod(\DateTime $startAt, \DateTime $endAt, \DateInterval $interval) {
        $period = new \DatePeriod($startAt, $interval, $endAt);

        $data = [];
        foreach ($period as $datetime) {
            $startAt = clone $datetime;

            $endAt = clone $datetime;
            $endAt->add($interval);

            $data[] = [$startAt, $endAt];
        }

        return $data;
    }

    /**
     * @param \DateTime $startAt
     * @param \DateTime $endAt
     *
     * @return bool
     */
    public function contains(\DateTime $startAt, \DateTime $endAt)
    {
        if ($startAt > $this->endAt || $endAt < $this->startAt) {
            return false;
        }

        $week = $this->getWeek();

        if (count($week) === 0) {
            return true;
        }

        $day = $startAt->format('w') % 7;
        if (!isset($week[$day])) {
            return false;
        }

        $data = $week[$day];
        foreach($data as $_data) {
            $_startAt = clone $startAt;
            $_startAt->setTime($_data[0]->format('H'), $_data[0]->format('i'));

            $_endAt = clone $endAt;
            $_endAt->setTime($_data[1]->format('H'), $_data[1]->format('i'));

            if ($startAt >= $_startAt && $endAt <= $_endAt) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param AppointmentInterface $appointment
     *
     * @return $this
     */
    public function addAppointment(AppointmentInterface $appointment)
    {
        $this->appointments[] = $appointment;
        $appointment->setSlot($this);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getAppointments()
    {
        if ($this->appointments === null) {
            $this->appointments = new ArrayCollection();
        }
        return $this->appointments;
    }

    public function count(\DateTime $startAt, \DateTime $endAt)
    {
        return $this->appointments->filter(function (AppointmentInterface $appointment) use ($startAt, $endAt) {
            return $appointment->isValid($startAt, $endAt);
        })->count();
    }

    public function isValid(\DateTime $startAt, \DateTime $endAt)
    {
        return $this->count($startAt, $endAt) < $this->capacity;
    }
}
