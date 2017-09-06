<?php

namespace KRG\CalendarBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SlotRangeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint) {
        return;
//
//        $_data = $_form->getData();
//        if ($_data['start'] !== null) {
//            if ($_data['end'] === null) {
//                $_form->get('end')->addError(new FormError('Vous devez séléctionner une heure de fin.'));
//            } else {
//                $start = (float) preg_replace(array('/:00/', '/:30/'), array('', '.5'), $_data['start']);
//                $end = (float) preg_replace(array('/:00/', '/:30/'), array('', '.5'), $_data['end']);
//                if ($end <= $start) {
//                    $_form->get('end')->addError(new FormError('L\'heure de début doit être supérieur à l\'heure de fin.'));
//                }
//            }
//        } elseif($_data['end'] !== null) {
//            $_form->get('start')->addError(new FormError('Vous devez séléctionner une heure de début.'));
//        }
    }
}
