<?php

namespace KRG\CalendarBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class SlotRangeDataTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        $data = array();
        foreach (range(1, 7) as $day) {
            $data[$day] = array(
                'am' => array('start' => null, 'end' => null),
                'pm' => array('start' => null, 'end' => null),
            );
        }

        if (is_array($value)) {
            $data = array_replace_recursive($data, $value);
        }

        return $data;
    }

    public function reverseTransform($data)
    {
        foreach ($data as &$_data) {
            foreach ($_data as $key => &$__data) {
                if ($__data['start'] === null || $__data['end'] === null) {
                    unset($_data[$key]);
                }
            }
            unset($__data);
        }
        unset($_data);

        return array_filter($data);
    }
}