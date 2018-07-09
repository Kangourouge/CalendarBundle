<?php

namespace KRG\CalendarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use GEGM\CommonBundle\Validator\Constraints as GEGMAssert;

abstract class Appointment implements AppointmentInterface
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThan("today")
     * @var \DateTime
     */
    protected $startAt;

    /**
     * @ORM\Column(type="datetime")
     * @GEGMAssert\GreaterThan("startAt")
     * @var \DateTime
     */
    protected $endAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $attendedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $notifiedAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $comment;

    /**
     * @var SlotInterface
     */
    protected $slot;

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
     * Set startAt
     *
     * @param \DateTime $startAt
     *
     * @return AppointmentInterface
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
     * @return AppointmentInterface
     */
    public function setEndAt(\DateTime $endAt)
    {
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
     * Set attendedAt
     *
     * @param \DateTime $attendedAt
     *
     * @return Appointment
     */
    public function setAttendedAt(\DateTime $attendedAt = null)
    {
        $this->attendedAt = $attendedAt;

        return $this;
    }

    /**
     * Get attendedAt
     *
     * @return \DateTime
     */
    public function getAttendedAt()
    {
        return $this->attendedAt;
    }


    /**
     * Set notifiedAt
     *
     * @param \DateTime $notifiedAt
     *
     * @return Appointment
     */
    public function setNotifiedAt(\DateTime $notifiedAt = null)
    {
        $this->notifiedAt = $notifiedAt;

        return $this;
    }

    /**
     * Get notifiedAt
     *
     * @return \DateTime
     */
    public function getNotifiedAt()
    {
        return $this->notifiedAt;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return AppointmentInterface
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
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
     *
     * @return AppointmentInterface
     */
    public function setSlot(SlotInterface $slot)
    {
        $this->slot = $slot;

        return $this;
    }

    /**
     * @param \DateTime $startAt
     * @param \DateTime $endAt
     *
     * @return bool
     */
    public function isValid(\DateTime $startAt, \DateTime $endAt)
    {
        return $startAt >= $this->startAt && $endAt <= $this->endAt;
    }
}
