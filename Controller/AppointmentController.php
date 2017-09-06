<?php

namespace KRG\CalendarBundle\Controller;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use KRG\CalendarBundle\Entity\AppointmentInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AppointmentController
 * @package KRG\CalendarBundle\Controller
 */
class AppointmentController extends Controller
{
    /**
     * @param Request $request
     * @param AppointmentInterface $appointment
     */
    public function updateAction(Request $request, $id)
    {
        $startAt = \DateTime::createFromFormat('Y-m-d\TH:i:s', $request->get('startAt'));
        $endAt = \DateTime::createFromFormat('Y-m-d\TH:i:s', $request->get('endAt'));

        if (!$startAt instanceof \DateTime) {
            throw new InvalidArgumentException('$startAt is invalid');
        }
        if (!$endAt instanceof \DateTime) {
            throw new InvalidArgumentException('$endAt is invalid');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $className = $entityManager->getClassMetadata(AppointmentInterface::class)->getName();
        $appointment = $entityManager->getRepository($className)->find($id);
        assert($appointment instanceof AppointmentInterface);

        $appointment->setStartAt($startAt);
        $appointment->setEndAt($endAt);

        $validator = $this->get('validator');
        $errors = $validator->validate($appointment);
        if ($errors->count() > 0) {
            throw new \RuntimeException((string)$errors);
        }

        $entityManager->persist($appointment);
        $entityManager->flush();

        return new JsonResponse(array());
    }
}
