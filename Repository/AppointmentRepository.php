<?php

namespace KRG\CalendarBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;

class AppointmentRepository extends EntityRepository implements CalendarRepositoryInterface
{
	public function getQueryBuilder(array $filter, UserInterface $user = null)
    {
        $queryBuilder = $this
            ->createQueryBuilder('appointment')
            ->distinct()
            ->andWhere('appointment.startAt >= :startAt')
            ->setParameter('startAt', $filter['startAt']);

        if (isset($filter['endAt']) && $filter['endAt'] instanceof \DateTime) {
            $queryBuilder
                ->andWhere('appointment.endAt <= :endAt')
                ->setParameter('endAt', $filter['endAt']);
        }

        foreach ($filter as $key => $value) {
            if (($value instanceof Collection && $value->isEmpty())
                || (is_array($value) && count($value) === 0)
                || !$value
            ) {
                continue;
            }

            switch($key) {
                default:
            }
        }

        return $queryBuilder;
    }
}
