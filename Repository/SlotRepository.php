<?php

namespace KRG\CalendarBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;

class SlotRepository extends EntityRepository implements CalendarRepositoryInterface
{
    public function getQueryBuilder(array $filter, UserInterface $user = null)
    {
        $queryBuilder = $this
            ->createQueryBuilder('slot')
            ->distinct()
            ->andWhere('slot.startAt >= CURRENT_TIMESTAMP() OR slot.endAt >= CURRENT_TIMESTAMP()')
            ->addOrderBy('slot.excluded');

        foreach ($filter as $key => $value) {
            if (($value instanceof Collection && $value->isEmpty())
                || (is_array($value) && count($value) === 0)
                || !$value
            ) {
                continue;
            }

            switch($key) {
                default:;
            }
        }

        return $queryBuilder;
    }
}
