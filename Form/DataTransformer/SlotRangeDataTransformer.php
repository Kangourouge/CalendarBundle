<?php

namespace KRG\CalendarBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class SlotRangeDataTransformer implements DataTransformerInterface
{
    public function transform($range)
    {
        $data = [];
        foreach (range(1, 7) as $day) {
            $data[$day] = [
                'am' => ['day' => $day, 'meridiem' => 'am', 'start' => null, 'end' => null],
                'pm' => ['day' => $day, 'meridiem' => 'pm', 'start' => null, 'end' => null],
            ];
        }

        $value = $range ? array_replace_recursive($data, $range) : $data;
        $value = array_map('array_values', $value);
        $value = call_user_func_array('array_merge', $value);

        return $value;
    }

    public function reverseTransform($value)
    {
        $range = [];
        foreach ($value as $_value) {
            $day = $_value['day'];
            $meridiem = $_value['meridiem'];
            if (!isset($range[$day])) {
                $range[$day] = [];
            }
            if (!isset($range[$day])) {
                $range[$day][$meridiem] = [];
            }
            unset($_value['day']);
            unset($_value['meridiem']);
            $range[$day][$meridiem] = $_value;
        }

        return $range;
    }
}
