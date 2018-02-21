<?php

namespace KRG\CalendarBundle\Entity;

interface AppointmentInterface
{
    /**
     * Set startAt
     *
     * @param \DateTime $startAt
     *
     * @return AppointmentInterface
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
     * @return AppointmentInterface
     */
    public function setEndAt(\DateTime $endAt);

    /**
     * Get endAt
     *
     * @return \DateTime
     */
    public function getEndAt();

    /**
     * Set attendedAt
     *
     * @param \DateTime $attendedAt
     *
     * @return Appointment
     */
    public function setAttendedAt(\DateTime $attendedAt = null);

    /**
     * Get attendedAt
     *
     * @return \DateTime
     */
    public function getAttendedAt();

    /**
     * Set notifiedAt
     *
     * @param \DateTime $notifiedAt
     *
     * @return Appointment
     */
    public function setNotifiedAt(\DateTime $notifiedAt = null);

    /**
     * Get notifiedAt
     *
     * @return \DateTime
     */
    public function getNotifiedAt();


    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return AppointmentInterface
     */
    public function setComment($comment);

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment();


    /**
     * @return SlotInterface
     */
    public function getSlot();

    /**
     * @param SlotInterface $slot
     *
     * @return AppointmentInterface
     */
    public function setSlot(SlotInterface $slot);

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
