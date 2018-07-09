<?php

namespace KRG\CalendarBundle\Controller;

use KRG\CalendarBundle\Entity\AppointmentInterface;
use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppointmentController extends AbstractController
{
    /** @var ValidatorInterface */
    protected $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function updateAction(Request $request, $id)
    {
        $startAt = \DateTime::createFromFormat('Y-m-d\TH:i:s', $request->get('startAt'));
        $endAt = \DateTime::createFromFormat('Y-m-d\TH:i:s', $request->get('endAt'));

        if (!$startAt instanceof \DateTime) {
            throw new InvalidArgumentException($startAt.' is invalid');
        }
        if (!$endAt instanceof \DateTime) {
            throw new InvalidArgumentException($endAt.' is invalid');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $appointment = $entityManager->getRepository(AppointmentInterface::class)->find($id);
        assert($appointment instanceof AppointmentInterface);

        $appointment->setStartAt($startAt);
        $appointment->setEndAt($endAt);

        $errors = $this->validator->validate($appointment);
        if ($errors->count() > 0) {
            throw new \RuntimeException((string)$errors);
        }

        $entityManager->persist($appointment);
        $entityManager->flush();

        return new JsonResponse([]);
    }
}
