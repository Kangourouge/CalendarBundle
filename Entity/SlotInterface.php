<?php

namespace KRG\CalendarBundle\Entity;

use Doctrine\Common\Collections\Collection;

interface SlotInterface
{
    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set excluded
     *
     * @param boolean $excluded
     *
     * @return SlotInterface
     */
    public function setExcluded($excluded);

    /**
     * Get excluded
     *
     * @return boolean
     */
    public function getExcluded();

    /**
     * Set startAt
     *
     * @param \DateTime $startAt
     *
     * @return SlotInterface
     */
    public function setStartAt(\DateTime $startAt);

    /**
     * Get startAt
     *
     * @return \DateTime
     */
    public function getStartAt();

    /**
     * Set endAt
     *
     * @param \DateTime $endAt
     *
     * @return SlotInterface
     */
    public function setEndAt(\DateTime $endAt);

    /**
     * Get endAt
     *
     * @return \DateTime
     */
    public function getEndAt();

    /**
     * Set duration
     *
     * @param integer $duration
     *
     * @return SlotInterface
     */
    public function setDuration($duration);

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration();

    /**
     * Set range
     *
     * @param array $range
     *
     * @return SlotInterface
     */
    public function setRange($range);

    /**
     * Get range
     *
     * @return array
     */
    public function getRange();

    /**
     * Get range
     *
     * @return array
     */
    public function getWeek();

    /**
     * @param \DateTime $startAt
     * @param \DateTime $endAt
     *
     * @return boolean
     */
    public function contains(\DateTime $startAt, \DateTime $endAt);

    /**
     * @param AppointmentInterface $appointment
     *
     * @return SlotInterface
     */
    public function addAppointment(AppointmentInterface $appointment);

    /**
     * @return Collection
     */
    public function getAppointments();

    /**
     * Sets createdAt.
     *
     * @param  \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * Returns createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Sets updatedAt.
     *
     * @param  \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * Returns updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * Sets deletedAt.
     *
     * @param \Datetime|null $deletedAt
     *
     * @return $this
     */
    public function setDeletedAt(\DateTime $deletedAt = null);

    /**
     * Returns deletedAt.
     *
     * @return \DateTime
     */
    public function getDeletedAt();

    /**
     * Is deleted?
     *
     * @return bool
     */
    public function isDeleted();

    /**
     * @param \DateTime $startAt
     * @param \DateTime $endAt
     *
     * @return bool
     */
    public function isValid(\DateTime $startAt, \DateTime $endAt);
}
