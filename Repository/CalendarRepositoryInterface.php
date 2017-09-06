<?php

namespace KRG\CalendarBundle\Repository;

use Symfony\Component\Security\Core\User\UserInterface;

interface CalendarRepositoryInterface
{
    public function getQueryBuilder(array $filter, UserInterface $user = null);
}
